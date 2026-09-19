<?php

namespace Modules\HR\Services;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\HR\Enums\AttendanceStatus;
use Modules\HR\Enums\PermissionRequestStatus;
use Modules\HR\Enums\PermissionType;
use Modules\HR\Models\Attendance;
use Modules\HR\Models\Employee;
use Modules\HR\Models\HrWorkPolicy;
use Modules\HR\Models\PermissionRequest;
use Modules\HR\Repositories\Contracts\PermissionRequestRepositoryInterface;

class PermissionRequestService
{
    public function __construct(
        protected PermissionRequestRepositoryInterface $permissionRepo
    ) {}

    //======================
    /**
     * Get DataTable query with applied filters.
     */
    public function getDataTableQuery(array $filters = [])
    {
        return $this->permissionRepo->getDataTableQuery($filters);
    }

    //======================
    /**
     * Get KPI metrics.
     */
    public function getKPIs(): array
    {
        return $this->permissionRepo->getKPIs();
    }

    //======================
    /**
     * Find permission request by ID.
     */
    public function getById(int $id, array $with = ['employee.translations', 'creator', 'managerApprover', 'hrApprover']): ?PermissionRequest
    {
        return $this->permissionRepo->findById($id, $with);
    }

    //======================
    /**
     * Get employee monthly balance, limits, and consumed hours according to HrWorkPolicy.
     */
    public function getEmployeeMonthlyBalance(int $employeeId, ?string $date = null): array
    {
        $employee = Employee::find($employeeId);
        if (!$employee) {
            throw new Exception(__('Employee not found.'));
        }

        $parsedDate = $date ? Carbon::parse($date) : Carbon::today();
        $year       = $parsedDate->year;
        $month      = $parsedDate->month;

        // Fetch company policies for 'permissions'
        $policies = HrWorkPolicy::getGroup('permissions');

        $maxHoursPerMonth   = (float) ($policies['max_monthly_permission_hours'] ?? 4.0);
        $maxCountPerMonth   = (int)   ($policies['max_monthly_permission_count'] ?? 3);
        $maxSingleHours     = (float) ($policies['max_single_permission_hours'] ?? 2.0);
        $minAdvanceHours    = (int)   ($policies['min_advance_notice_hours'] ?? 24);
        $financialPolicy    = $policies['permission_financial_policy'] ?? 'paid_with_excess_deduction';
        $approvalWorkflow   = $policies['approval_workflow'] ?? 'manager_and_hr';
        $maxEmergencyCount  = (int)   ($policies['max_monthly_emergency_permissions'] ?? 1);

        // Calculate consumed approved hours and total requests in this month
        $consumedHours = $this->permissionRepo->getEmployeeConsumedHoursInMonth($employeeId, $year, $month);
        $consumedCount = $this->permissionRepo->getEmployeePermissionCountInMonth($employeeId, $year, $month);

        $remainingHours = max(0.0, round($maxHoursPerMonth - $consumedHours, 2));
        $remainingCount = max(0, $maxCountPerMonth - $consumedCount);

        return [
            'month'                  => $parsedDate->format('Y-m'),
            'month_name'             => $parsedDate->translatedFormat('F Y'),
            'max_monthly_hours'      => $maxHoursPerMonth,
            'consumed_hours'         => $consumedHours,
            'remaining_hours'        => $remainingHours,
            'max_monthly_count'      => $maxCountPerMonth,
            'consumed_count'         => $consumedCount,
            'remaining_count'        => $remainingCount,
            'max_single_hours'       => $maxSingleHours,
            'min_advance_hours'      => $minAdvanceHours,
            'financial_policy'       => $financialPolicy,
            'approval_workflow'      => $approvalWorkflow,
            'max_emergency_count'    => $maxEmergencyCount,
            'can_request'            => $remainingCount > 0 && ($remainingHours > 0 || $financialPolicy !== 'always_deducted'),
        ];
    }

    //======================
    /**
     * Calculate duration in decimal hours between two times (e.g. 08:30 and 10:00 => 1.50).
     */
    public function calculateHours(string $fromTime, string $toTime): float
    {
        $start = Carbon::createFromTimeString($fromTime);
        $end   = Carbon::createFromTimeString($toTime);

        if ($end->lte($start)) {
            throw new Exception(__('End time must be after start time.'));
        }

        $minutes = $start->diffInMinutes($end);
        return round($minutes / 60, 2);
    }

    //======================
    /**
     * Create a new permission request with policy validations.
     */
    public function createPermissionRequest(array $data, int $userId): PermissionRequest
    {
        return DB::transaction(function () use ($data, $userId) {
            $employeeId = (int) $data['employee_id'];
            $date       = $data['date'];
            $fromTime   = $data['from_time'];
            $toTime     = $data['to_time'];
            $type       = $data['type'] ?? PermissionType::DURING_SHIFT->value;
            $isEmergency= !empty($data['is_emergency']) || $type === PermissionType::EMERGENCY->value;

            // 1. Calculate requested hours
            $hours = $this->calculateHours($fromTime, $toTime);

            // 2. Policy & Balance validation
            $balance = $this->getEmployeeMonthlyBalance($employeeId, $date);

            if ($hours > $balance['max_single_hours']) {
                throw new Exception(
                    __('Requested permission duration (:hours hrs) exceeds maximum single permission limit (:max hrs).', [
                        'hours' => $hours,
                        'max'   => $balance['max_single_hours'],
                    ])
                );
            }

            if ($balance['remaining_count'] <= 0) {
                throw new Exception(
                    __('Employee has reached the maximum permission requests limit (:max) for this month.', [
                        'max' => $balance['max_monthly_count'],
                    ])
                );
            }

            // 3. Check advance notice unless emergency
            if (!$isEmergency && $balance['min_advance_hours'] > 0) {
                $permissionDateTime = Carbon::parse($date . ' ' . $fromTime);
                $minNoticeDateTime  = Carbon::now()->addHours($balance['min_advance_hours']);
                if ($permissionDateTime->lt($minNoticeDateTime)) {
                    throw new Exception(
                        __('Permission requests require at least :hours hours advance notice.', [
                            'hours' => $balance['min_advance_hours'],
                        ])
                    );
                }
            }

            // 4. Overlap check
            if ($this->permissionRepo->hasOverlappingPermission($employeeId, $date, $fromTime, $toTime)) {
                throw new Exception(__('Employee already has a permission request registered during this time range.'));
            }

            // 5. Handle file attachment
            $attachmentPath = null;
            if (!empty($data['attachment']) && $data['attachment'] instanceof UploadedFile) {
                $attachmentPath = $data['attachment']->store('hr/permissions/' . date('Y/m'), 'public');
            }

            // 6. Set initial status based on approval workflow policy
            $initialStatus = PermissionRequestStatus::PENDING->value;
            if ($balance['approval_workflow'] === 'auto_approve') {
                $initialStatus = PermissionRequestStatus::APPROVED->value;
            }

            $permission = $this->permissionRepo->create([
                'employee_id'      => $employeeId,
                'date'             => $date,
                'type'             => $type,
                'from_time'        => $fromTime,
                'to_time'          => $toTime,
                'hours_count'      => $hours,
                'status'           => $initialStatus,
                'is_emergency'     => $isEmergency,
                'reason'           => $data['reason'] ?? null,
                'attachment'       => $attachmentPath,
                'financial_policy' => $balance['financial_policy'],
                'created_by'       => $userId,
            ]);

            // If auto approved, synchronize with attendance
            if ($initialStatus === PermissionRequestStatus::APPROVED->value) {
                $this->syncWithAttendance($permission);
            }

            return $permission;
        });
    }

    //======================
    /**
     * Approve a permission request (Manager or HR approval).
     */
    public function approvePermissionRequest(int $id, int $userId, string $role = 'hr'): PermissionRequest
    {
        return DB::transaction(function () use ($id, $userId, $role) {
            $permission = $this->permissionRepo->findById($id);
            if (!$permission) {
                throw new Exception(__('Permission request not found.'));
            }

            if ($permission->status === PermissionRequestStatus::APPROVED) {
                throw new Exception(__('This permission request is already approved.'));
            }

            $policies = HrWorkPolicy::getGroup('permissions');
            $workflow = $policies['approval_workflow'] ?? 'manager_and_hr';

            $updates = [];

            if ($workflow === 'manager_only' || $role === 'manager') {
                $updates['manager_approved_by'] = $userId;
                $updates['manager_approved_at'] = Carbon::now();

                if ($workflow === 'manager_only') {
                    $updates['status'] = PermissionRequestStatus::APPROVED->value;
                } else {
                    $updates['status'] = PermissionRequestStatus::MANAGER_APPROVED->value;
                }
            } else {
                // HR Approval (Final)
                $updates['hr_approved_by'] = $userId;
                $updates['hr_approved_at'] = Carbon::now();
                $updates['status']         = PermissionRequestStatus::APPROVED->value;
            }

            $updated = $this->permissionRepo->update($permission, $updates);

            if ($updated->status === PermissionRequestStatus::APPROVED) {
                $this->syncWithAttendance($updated);
            }

            return $updated;
        });
    }

    //======================
    /**
     * Reject a permission request with mandatory reason.
     */
    public function rejectPermissionRequest(int $id, string $reason, int $userId): PermissionRequest
    {
        return DB::transaction(function () use ($id, $reason, $userId) {
            $permission = $this->permissionRepo->findById($id);
            if (!$permission) {
                throw new Exception(__('Permission request not found.'));
            }

            return $this->permissionRepo->update($permission, [
                'status'           => PermissionRequestStatus::REJECTED->value,
                'rejection_reason' => $reason,
                'hr_approved_by'   => $userId,
                'hr_approved_at'   => Carbon::now(),
            ]);
        });
    }

    //======================
    /**
     * Delete a permission request (only if pending or rejected).
     */
    public function deletePermissionRequest(int $id): bool
    {
        $permission = $this->permissionRepo->findById($id);
        if (!$permission) {
            throw new Exception(__('Permission request not found.'));
        }

        if ($permission->status === PermissionRequestStatus::APPROVED) {
            throw new Exception(__('Approved permission requests cannot be deleted directly.'));
        }

        if ($permission->attachment) {
            Storage::disk('public')->delete($permission->attachment);
        }

        return $this->permissionRepo->delete($permission);
    }

    //======================
    /**
     * Synchronize approved permission with Attendance table for the day.
     */
    protected function syncWithAttendance(PermissionRequest $permission): void
    {
        $attendance = Attendance::where('employee_id', $permission->employee_id)
            ->whereDate('date', $permission->date->toDateString())
            ->first();

        if ($attendance) {
            // Deduct late or early minutes excused by the permission
            $excusedMinutes = (int) round($permission->hours_count * 60);

            if ($permission->type === PermissionType::LATE_ARRIVAL) {
                $attendance->late_minutes = max(0, $attendance->late_minutes - $excusedMinutes);
            } elseif ($permission->type === PermissionType::EARLY_DEPARTURE) {
                $attendance->early_leave_minutes = max(0, $attendance->early_leave_minutes - $excusedMinutes);
            }

            $note = __('Excused by approved permission #:id (:hours hrs)', [
                'id'    => $permission->id,
                'hours' => $permission->hours_count,
            ]);

            $attendance->adjustment_reason = trim(($attendance->adjustment_reason ? $attendance->adjustment_reason . ' | ' : '') . $note);
            $attendance->save();
        }
    }
}
