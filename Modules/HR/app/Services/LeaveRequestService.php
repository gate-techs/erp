<?php

namespace Modules\HR\Services;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Modules\HR\Enums\AttendanceSource;
use Modules\HR\Enums\AttendanceStatus;
use Modules\HR\Enums\LeaveRequestStatus;
use Modules\HR\Enums\LeaveType;
use Modules\HR\Models\Attendance;
use Modules\HR\Models\Employee;
use Modules\HR\Models\HrSickLeaveTier;
use Modules\HR\Models\HrWorkPolicy;
use Modules\HR\Models\LeaveRequest;
use Modules\HR\Models\OfficialHoliday;
use Modules\HR\Models\WorkScheduleDay;
use Modules\HR\Repositories\Contracts\LeaveRequestRepositoryInterface;
use Modules\Settings\Services\Contracts\NotificationServiceInterface;

class LeaveRequestService
{
    //======================
    public function __construct(
        protected LeaveRequestRepositoryInterface $leaveRepo,
        protected ?NotificationServiceInterface $notificationService = null
    ) {
        $this->notificationService = $notificationService ?? app(NotificationServiceInterface::class);
    }

    //======================
    /**
     * Get DataTable query builder with applied filters.
     */
    public function getDataTableQuery(array $filters = [])
    {
        return $this->leaveRepo->getDataTableQuery($filters);
    }

    //======================
    /**
     * Get KPI stats for index view.
     */
    public function getKPIs(): array
    {
        return $this->leaveRepo->getKPIs();
    }

    //======================
    /**
     * Find leave request by ID.
     */
    public function getById(int $id, array $with = ['employee.translations', 'replacementEmployee.translations', 'creator', 'hrApprover', 'managerApprover']): ?LeaveRequest
    {
        return $this->leaveRepo->findById($id, $with);
    }

    //======================
    /**
     * Calculate actual net working days between two dates for an employee,
     * excluding their schedule weekend rest days and official public holidays.
     */
    public function calculateLeaveDays(int $employeeId, string $startDate, string $endDate): array
    {
        $employee = Employee::with('workSchedule.days')->find($employeeId);
        if (!$employee) {
            throw new Exception(__('Employee not found.'));
        }

        $start  = Carbon::parse($startDate);
        $end    = Carbon::parse($endDate);

        if ($end->lt($start)) {
            throw new Exception(__('End date must be greater than or equal to start date.'));
        }

        // 1. Get employee non-working days of the week (e.g. Friday = 5, Saturday = 6)
        $restDayIndices = [];
        if ($employee->workSchedule && $employee->workSchedule->days && $employee->workSchedule->days->isNotEmpty()) {
            $restDayIndices = $employee->workSchedule->days
                ->where('is_off_day', true)
                ->pluck('day_of_week') // integer 0..6
                ->toArray();
        } else {
            // Default rest days: Friday (5) & Saturday (6)
            $restDayIndices = [5, 6];
        }

        // 2. Get official holidays in this date range
        $holidays = OfficialHoliday::where('status', 'active')
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_date', [$start->toDateString(), $end->toDateString()])
                  ->orWhereBetween('end_date', [$start->toDateString(), $end->toDateString()])
                  ->orWhere(function ($sub) use ($start, $end) {
                      $sub->where('start_date', '<=', $start->toDateString())
                          ->where('end_date', '>=', $end->toDateString());
                  });
            })->get();

        $holidayDates = [];
        foreach ($holidays as $h) {
            $hPeriod = CarbonPeriod::create($h->start_date, $h->end_date);
            foreach ($hPeriod as $d) {
                $holidayDates[$d->format('Y-m-d')] = $h->name;
            }
        }

        // 3. Iterate through requested period
        $period        = CarbonPeriod::create($start, $end);
        $calendarDays  = 0;
        $workingDays   = 0;
        $weekendDays   = 0;
        $holidayDays   = 0;
        $datesDetails  = [];

        foreach ($period as $date) {
            $calendarDays++;
            $dateStr     = $date->format('Y-m-d');
            $dayOfWeek   = $date->dayOfWeek; // 0=Sunday ... 6=Saturday
            $isWeekend   = in_array($dayOfWeek, $restDayIndices);
            $isHoliday   = isset($holidayDates[$dateStr]);

            if ($isHoliday) {
                $holidayDays++;
                $datesDetails[] = [
                    'date'   => $dateStr,
                    'day'    => $date->translatedFormat('l'),
                    'type'   => 'holiday',
                    'reason' => $holidayDates[$dateStr],
                ];
            } elseif ($isWeekend) {
                $weekendDays++;
                $datesDetails[] = [
                    'date'   => $dateStr,
                    'day'    => $date->translatedFormat('l'),
                    'type'   => 'rest_day',
                    'reason' => __('Weekly Rest Day'),
                ];
            } else {
                $workingDays++;
                $datesDetails[] = [
                    'date'   => $dateStr,
                    'day'    => $date->translatedFormat('l'),
                    'type'   => 'working_day',
                    'reason' => __('Working Day (Deducted)'),
                ];
            }
        }

        return [
            'calendar_days' => $calendarDays,
            'working_days'  => (float) $workingDays,
            'weekend_days'  => $weekendDays,
            'holiday_days'  => $holidayDays,
            'details'       => $datesDetails,
        ];
    }

    //======================
    /**
     * Get live entitlement, consumed, and remaining balance for an employee.
     */
    public function getEmployeeLeaveBalance(int $employeeId, string $leaveType, ?string $requestDate = null): array
    {
        $employee = Employee::find($employeeId);
        if (!$employee) {
            throw new Exception(__('Employee not found.'));
        }

        $year    = $requestDate ? Carbon::parse($requestDate)->year : Carbon::now()->year;
        $type    = LeaveType::tryFrom($leaveType);
        $typeVal = $type ? $type->value : $leaveType;

        $totalEntitlement = 0.0;
        $consumedDays     = $this->leaveRepo->getEmployeeConsumedDays($employeeId, $typeVal, $year);
        $isEligible       = true;
        $ineligibilityReason = null;
        $extraInfo        = [];

        switch ($typeVal) {
            case LeaveType::ANNUAL->value:
                $totalEntitlement = (float) HrWorkPolicy::getVal('annual_leave_days', 21);

                // First year eligibility check
                if ($employee->hire_date) {
                    $hireDate = Carbon::parse($employee->hire_date);
                    $monthsWorked = $hireDate->diffInMonths(Carbon::now());
                    $requiredMonths = (int) HrWorkPolicy::getVal('first_year_eligibility_months', 3);

                    if ($monthsWorked < $requiredMonths) {
                        $isEligible = false;
                        $ineligibilityReason = __('Employee is in their first year of service and has not yet completed the :months months waiting period.', ['months' => $requiredMonths]);
                    }
                }
                break;

            case LeaveType::CASUAL->value:
                $enabled = (bool) HrWorkPolicy::getVal('enable_casual_leave', true);
                if (!$enabled) {
                    $isEligible = false;
                    $ineligibilityReason = __('Casual leave is currently disabled in company work policies.');
                }
                $totalEntitlement = (float) HrWorkPolicy::getVal('casual_leave_annual_days', 6);
                $extraInfo['allow_consecutive'] = (bool) HrWorkPolicy::getVal('allow_consecutive_casual_days', false);
                break;

            case LeaveType::SICK->value:
                // Sick leave is governed by progressive tiers
                $tiers = HrSickLeaveTier::where('status', 'active')->orderBy('sort_order')->get();
                $totalDaysInTiers = $tiers->sum('days_count');
                $totalEntitlement = (float) $totalDaysInTiers;
                $extraInfo['sick_tiers'] = $tiers->map(fn($t) => [
                    'name'       => $t->name,
                    'days'       => $t->days_count,
                    'percentage' => $t->salary_percentage,
                ])->toArray();
                break;

            case LeaveType::MATERNITY->value:
                if ($employee->gender && $employee->gender->value === 'male') {
                    $isEligible = false;
                    $ineligibilityReason = __('Maternity leave is strictly reserved for female employees.');
                }
                $totalEntitlement = (float) HrWorkPolicy::getVal('maternity_leave_days', 70);
                break;

            case LeaveType::HAJJ->value:
                $totalEntitlement = (float) HrWorkPolicy::getVal('hajj_leave_days', 10);
                $minServiceYears  = (int) HrWorkPolicy::getVal('hajj_min_service_years', 2);
                $maxTimes         = (int) HrWorkPolicy::getVal('hajj_max_times', 1);

                if ($employee->hire_date) {
                    $yearsWorked = Carbon::parse($employee->hire_date)->diffInYears(Carbon::now());
                    if ($yearsWorked < $minServiceYears) {
                        $isEligible = false;
                        $ineligibilityReason = __('Hajj leave requires at least :years years of completed service.', ['years' => $minServiceYears]);
                    }
                }

                // Check lifetime usage
                $lifetimeHajjCount = LeaveRequest::where('employee_id', $employeeId)
                    ->where('leave_type', LeaveType::HAJJ->value)
                    ->where('status', LeaveRequestStatus::APPROVED->value)
                    ->count();

                if ($lifetimeHajjCount >= $maxTimes) {
                    $isEligible = false;
                    $ineligibilityReason = __('Employee has already exhausted the maximum allowed Hajj leaves (:times time).', ['times' => $maxTimes]);
                }

                $extraInfo['require_official_permit'] = (bool) HrWorkPolicy::getVal('hajj_require_official_permit', true);
                break;

            case LeaveType::MARRIAGE->value:
                $totalEntitlement = (float) HrWorkPolicy::getVal('marriage_leave_days', 5);
                break;

            case LeaveType::BEREAVEMENT->value:
                $totalEntitlement = (float) HrWorkPolicy::getVal('bereavement_leave_days', 5);
                break;

            case LeaveType::PATERNITY->value:
                $totalEntitlement = (float) HrWorkPolicy::getVal('paternity_leave_days', 3);
                break;

            case LeaveType::UNPAID->value:
                $totalEntitlement = 365.0; // Unlimited or subjective
                break;

            default:
                $totalEntitlement = 30.0;
                break;
        }

        $remainingDays = max(0.0, $totalEntitlement - $consumedDays);

        return [
            'leave_type'           => $typeVal,
            'total_entitlement'    => $totalEntitlement,
            'consumed_days'        => $consumedDays,
            'remaining_days'       => $remainingDays,
            'is_eligible'          => $isEligible,
            'ineligibility_reason' => $ineligibilityReason,
            'extra_info'           => $extraInfo,
        ];
    }

    //======================
    /**
     * Store new leave request.
     */
    public function createLeaveRequest(array $data, ?int $userId = null): LeaveRequest
    {
        return DB::transaction(function () use ($data, $userId) {
            $employeeId = (int) $data['employee_id'];
            $startDate  = $data['start_date'];
            $endDate    = $data['end_date'];
            $leaveType  = $data['leave_type'];

            // 1. Check date overlap
            if ($this->leaveRepo->hasOverlappingLeave($employeeId, $startDate, $endDate)) {
                throw new Exception(__('Employee already has a pending or approved leave request during this date period.'));
            }

            // 2. Check balance and eligibility
            $balance = $this->getEmployeeLeaveBalance($employeeId, $leaveType, $startDate);
            if (!$balance['is_eligible']) {
                throw new Exception($balance['ineligibility_reason'] ?? __('Employee is not eligible for this leave type.'));
            }

            // 3. Calculate actual working days
            $daysCalc = $this->calculateLeaveDays($employeeId, $startDate, $endDate);
            $workingDays = $daysCalc['working_days'];

            if ($workingDays <= 0) {
                throw new Exception(__('The selected dates contain only weekends or official holidays; no working days to deduct.'));
            }

            // Check if sufficient balance
            if ($leaveType !== LeaveType::UNPAID->value && $workingDays > $balance['remaining_days']) {
                throw new Exception(__('Requested working days (:requested days) exceed the remaining balance (:remaining days).', [
                    'requested' => $workingDays,
                    'remaining' => $balance['remaining_days'],
                ]));
            }

            // 4. Handle attachment upload
            $attachmentPath = null;
            if (!empty($data['attachment']) && $data['attachment'] instanceof UploadedFile) {
                $attachmentPath = $data['attachment']->store('hr/leave_attachments', 'public');
            }

            // 5. Prepare record data
            $payload = [
                'employee_id'             => $employeeId,
                'leave_type'              => $leaveType,
                'start_date'              => $startDate,
                'end_date'                => $endDate,
                'days_count'              => $workingDays,
                'replacement_employee_id' => !empty($data['replacement_employee_id']) ? $data['replacement_employee_id'] : null,
                'status'                  => LeaveRequestStatus::PENDING->value,
                'reason'                  => $data['reason'] ?? null,
                'attachment'              => $attachmentPath,
                'emergency_contact_phone' => $data['emergency_contact_phone'] ?? null,
                'created_by'              => $userId,
            ];

            $created = $this->leaveRepo->create($payload);

            $this->notifyManagerOnLeaveSubmission($created);

            return $created;
        });
    }


    //======================
    /**
     * Update leave request.
     */
    public function updateLeaveRequest(int|LeaveRequest $request, array $data, ?int $userId = null): LeaveRequest
    {
        return DB::transaction(function () use ($request, $data, $userId) {
            $model = $request instanceof LeaveRequest ? $request : $this->leaveRepo->findById($request);
            if (!$model) {
                throw new Exception(__('Leave request not found.'));
            }

            if (!in_array($model->status, [LeaveRequestStatus::PENDING, LeaveRequestStatus::MANAGER_APPROVED])) {
                throw new Exception(__('Only pending requests can be modified.'));
            }

            $employeeId = (int) ($data['employee_id'] ?? $model->employee_id);
            $startDate  = $data['start_date'] ?? $model->start_date->format('Y-m-d');
            $endDate    = $data['end_date'] ?? $model->end_date->format('Y-m-d');
            $leaveType  = $data['leave_type'] ?? $model->leave_type->value;

            // Check overlap
            if ($this->leaveRepo->hasOverlappingLeave($employeeId, $startDate, $endDate, $model->id)) {
                throw new Exception(__('Employee already has a pending or approved leave request during this date period.'));
            }

            // Calculate working days
            $daysCalc    = $this->calculateLeaveDays($employeeId, $startDate, $endDate);
            $workingDays = $daysCalc['working_days'];

            // Handle attachment
            $attachmentPath = $model->attachment;
            if (!empty($data['attachment']) && $data['attachment'] instanceof UploadedFile) {
                if ($model->attachment) {
                    Storage::disk('public')->delete($model->attachment);
                }
                $attachmentPath = $data['attachment']->store('hr/leave_attachments', 'public');
            }

            $payload = [
                'employee_id'             => $employeeId,
                'leave_type'              => $leaveType,
                'start_date'              => $startDate,
                'end_date'                => $endDate,
                'days_count'              => $workingDays,
                'replacement_employee_id' => !empty($data['replacement_employee_id']) ? $data['replacement_employee_id'] : null,
                'reason'                  => $data['reason'] ?? $model->reason,
                'attachment'              => $attachmentPath,
                'emergency_contact_phone' => $data['emergency_contact_phone'] ?? $model->emergency_contact_phone,
                'updated_by'              => $userId,
            ];

            return $this->leaveRepo->update($model, $payload);
        });
    }

    //======================
    /**
     * Delete leave request.
     */
    public function deleteLeaveRequest(int|LeaveRequest $request): bool
    {
        $model = $request instanceof LeaveRequest ? $request : $this->leaveRepo->findById($request);
        if (!$model) {
            throw new Exception(__('Leave request not found.'));
        }

        if ($model->status === LeaveRequestStatus::APPROVED) {
            throw new Exception(__('Approved leave requests cannot be deleted directly; cancel them instead.'));
        }

        if ($model->attachment) {
            Storage::disk('public')->delete($model->attachment);
        }

        return $this->leaveRepo->delete($model);
    }

    //======================
    /**
     * Approve leave request (Manager or HR).
     */
    public function approveLeaveRequest(int|LeaveRequest $request, int $approverId, string $role = 'hr'): LeaveRequest
    {
        return DB::transaction(function () use ($request, $approverId, $role) {
            $model = $request instanceof LeaveRequest ? $request : $this->leaveRepo->findById($request);
            if (!$model) {
                throw new Exception(__('Leave request not found.'));
            }

            $workflow = HrWorkPolicy::getVal('approval_workflow', 'manager_and_hr');

            if ($role === 'manager' && $workflow === 'manager_and_hr') {
                $model->update([
                    'status'              => LeaveRequestStatus::MANAGER_APPROVED->value,
                    'manager_approved_by' => $approverId,
                    'manager_approved_at' => Carbon::now(),
                ]);
            } else {
                // Final HR Approval
                $model->update([
                    'status'         => LeaveRequestStatus::APPROVED->value,
                    'hr_approved_by' => $approverId,
                    'hr_approved_at' => Carbon::now(),
                ]);

                // Sync with Attendance table
                $this->syncApprovedLeaveWithAttendance($model);
            }

            return $model->fresh(['employee.translations', 'hrApprover', 'managerApprover']);
        });
    }

    //======================
    /**
     * Reject leave request with reason.
     */
    public function rejectLeaveRequest(int|LeaveRequest $request, int $approverId, string $rejectionReason): LeaveRequest
    {
        return DB::transaction(function () use ($request, $approverId, $rejectionReason) {
            $model = $request instanceof LeaveRequest ? $request : $this->leaveRepo->findById($request);
            if (!$model) {
                throw new Exception(__('Leave request not found.'));
            }

            $model->update([
                'status'           => LeaveRequestStatus::REJECTED->value,
                'rejection_reason' => $rejectionReason,
                'hr_approved_by'   => $approverId,
                'hr_approved_at'   => Carbon::now(),
            ]);

            return $model;
        });
    }

    //======================
    /**
     * Cancel leave request.
     */
    public function cancelLeaveRequest(int|LeaveRequest $request, int $userId): LeaveRequest
    {
        return DB::transaction(function () use ($request, $userId) {
            $model = $request instanceof LeaveRequest ? $request : $this->leaveRepo->findById($request);
            if (!$model) {
                throw new Exception(__('Leave request not found.'));
            }

            if ($model->status === LeaveRequestStatus::APPROVED && Carbon::parse($model->start_date)->isPast()) {
                throw new Exception(__('Cannot cancel a leave that has already started or elapsed.'));
            }

            $model->update([
                'status'     => LeaveRequestStatus::CANCELLED->value,
                'updated_by' => $userId,
            ]);

            // If it was already synced to attendances, reset them
            Attendance::where('employee_id', $model->employee_id)
                ->whereBetween('date', [$model->start_date, $model->end_date])
                ->where('status', AttendanceStatus::ON_LEAVE->value)
                ->delete();

            return $model;
        });
    }

    // =========================================================================
    // Helper: Sync Approved Leave with Attendances Table
    // =========================================================================

    protected function syncApprovedLeaveWithAttendance(LeaveRequest $leave): void
    {
        $daysCalc = $this->calculateLeaveDays($leave->employee_id, $leave->start_date->format('Y-m-d'), $leave->end_date->format('Y-m-d'));

        foreach ($daysCalc['details'] as $item) {
            if ($item['type'] === 'working_day') {
                Attendance::updateOrCreate(
                    [
                        'employee_id' => $leave->employee_id,
                        'date'        => $item['date'],
                    ],
                    [
                        'status'              => AttendanceStatus::ON_LEAVE->value,
                        'total_working_hours' => 0,
                        'late_minutes'        => 0,
                        'early_leave_minutes' => 0,
                        'overtime_minutes'    => 0,
                        'is_manual_adjusted'  => true,
                        'adjustment_reason'   => __('Approved Leave: :type', ['type' => $leave->leave_type->label()]),
                        'created_by'          => $leave->hr_approved_by,
                    ]
                );
            }
        }
    }

    //======================
    /**
     * Send notification when a leave request is submitted:
     * - To the direct manager of the employee
     * - To users with the supervisor role ('supervisor')
     * - To users with the HR manager role ('hr_manager')
     */
    public function notifyManagerOnLeaveSubmission(LeaveRequest $leaveRequest): array
    {
        try {
            $employee = Employee::with(['translations', 'manager.user'])->find($leaveRequest->employee_id);
            if (! $employee) {
                return ['sent' => 0, 'recipients' => 0];
            }

            $recipients = collect();

            // 1. Direct manager's linked user account
            $managerUser = $employee->manager?->user;
            if ($managerUser && $managerUser->is_active) {
                $recipients->push($managerUser);
            }

            // 2. Users with 'supervisor' role (or user_type = 'supervisor')
            try {
                $supervisors = \App\Models\User::role('supervisor')->where('is_active', true)->get();
                $recipients = $recipients->merge($supervisors);
            } catch (\Throwable $e) {
                // Role might not exist in database yet
            }
            $supervisorsByType = \App\Models\User::where('user_type', 'supervisor')->where('is_active', true)->get();
            $recipients = $recipients->merge($supervisorsByType);

            // 3. Users with 'hr_manager' role
            try {
                $hrManagers = \App\Models\User::role('hr_manager')->where('is_active', true)->get();
                $recipients = $recipients->merge($hrManagers);
            } catch (\Throwable $e) {
                // Role might not exist in database yet
            }

            // Fallback: If no recipients found, notify super_admin / admin users
            if ($recipients->isEmpty()) {
                try {
                    $admins = \App\Models\User::role(['super_admin', 'admin'])->where('is_active', true)->get();
                    $recipients = $recipients->merge($admins);
                } catch (\Throwable $e) {
                    $admins = \App\Models\User::where('user_type', 'admin')->where('is_active', true)->get();
                    $recipients = $recipients->merge($admins);
                }
            }

            // Deduplicate and filter active users
            $recipients = $recipients->unique('id')->filter(fn ($u) => (bool) $u->is_active);

            // Exclude the applicant themselves if other recipients are available
            $applicantUserId = $employee->user_id ?? $leaveRequest->created_by;
            if ($applicantUserId && $recipients->count() > 1) {
                $recipients = $recipients->reject(fn ($u) => (int) $u->id === (int) $applicantUserId);
            }

            if ($recipients->isEmpty()) {
                return ['sent' => 0, 'recipients' => 0];
            }


            $leaveTypeLabel = $leaveRequest->leave_type instanceof LeaveType
                ? $leaveRequest->leave_type->label()
                : (LeaveType::tryFrom((string) $leaveRequest->leave_type)?->label() ?? (string) $leaveRequest->leave_type);

            $actionUrl = route('hr.leaves.index');

            $variables = [
                'employee_name' => $employee->name ?? __('Employee'),
                'leave_type'    => $leaveTypeLabel,
                'start_date'    => Carbon::parse($leaveRequest->start_date)->format('Y-m-d'),
                'end_date'      => Carbon::parse($leaveRequest->end_date)->format('Y-m-d'),
                'total_days'    => $leaveRequest->days_count,
                'action_url'    => $actionUrl,
            ];

            return $this->notificationService->sendTemplateNotification(
                'leave_submitted',
                $variables,
                $recipients,
                $actionUrl
            );
        } catch (\Throwable $e) {
            Log::warning('Leave submission notification failed: ' . $e->getMessage(), [
                'leave_request_id' => $leaveRequest->id ?? null,
            ]);

            return ['sent' => 0, 'recipients' => 0, 'error' => $e->getMessage()];
        }
    }
}

