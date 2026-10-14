<?php

namespace Modules\EmployeePortal\Services;

use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\EmployeePortal\Repositories\Contracts\PortalEmployeeRepositoryInterface;
use Modules\EmployeePortal\Services\Contracts\PortalPermissionServiceInterface;
use Modules\HR\Enums\PermissionRequestStatus;
use Modules\HR\Enums\PermissionType;
use Modules\HR\Models\Employee;
use Modules\HR\Models\HrWorkPolicy;
use Modules\HR\Models\PermissionRequest;
use Modules\HR\Services\PermissionRequestService;

class PortalPermissionService implements PortalPermissionServiceInterface
{
    public function __construct(
        protected PortalEmployeeRepositoryInterface $employeeRepo,
        protected PermissionRequestService $hrPermissionService
    ) {}

    /**
     * Resolve employee from current user.
     */
    public function resolveCurrentEmployee(?User $user): ?Employee
    {
        if ($user) {
            $employee = $this->employeeRepo->findByUserId($user->id);
            if ($employee) {
                return $employee;
            }
        }

        return $this->employeeRepo->getActiveFallback();
    }

    /**
     * Get data required for submitting a new permission request.
     */
    public function getPermissionFormContext(?User $user): array
    {
        $employee = $this->resolveCurrentEmployee($user);

        if ($employee) {
            // Live calculated balance & limits from HrWorkPolicy and actual employee usage
            $balance = $this->hrPermissionService->getEmployeeMonthlyBalance($employee->id);
            $recentPermissions = PermissionRequest::where('employee_id', $employee->id)
                ->latest('date')
                ->latest('id')
                ->take(5)
                ->get();
        } else {
            // Read dynamic company policies directly from hr_work_policies settings table
            $policies = HrWorkPolicy::getGroup('permissions');
            $today = Carbon::today();

            $maxHours = (float) ($policies['max_monthly_permission_hours'] ?? 0);
            $maxCount = (int) ($policies['max_monthly_permission_count'] ?? 0);

            $balance = [
                'month'               => $today->format('Y-m'),
                'month_name'          => $today->translatedFormat('F Y'),
                'max_monthly_hours'   => $maxHours,
                'consumed_hours'      => 0.0,
                'remaining_hours'     => $maxHours,
                'max_monthly_count'   => $maxCount,
                'consumed_count'      => 0,
                'remaining_count'     => $maxCount,
                'max_single_hours'    => (float) ($policies['max_single_permission_hours'] ?? 0),
                'min_advance_hours'   => (int) ($policies['min_advance_notice_hours'] ?? 0),
                'financial_policy'    => $policies['permission_financial_policy'] ?? 'paid_with_excess_deduction',
                'approval_workflow'   => $policies['approval_workflow'] ?? 'manager_and_hr',
                'max_emergency_count' => (int) ($policies['max_monthly_emergency_permissions'] ?? 0),
                'can_request'         => true,
            ];
            $recentPermissions = collect();
        }

        $permissionTypes = PermissionType::cases();

        return [
            'employee'          => $employee,
            'user'              => $user,
            'balance'           => $balance,
            'permissionTypes'   => $permissionTypes,
            'recentPermissions' => $recentPermissions,
        ];
    }

    /**
     * Calculate duration, remaining balance, and validate policies in real-time.
     */
    public function calculateDurationAndBalance(?User $user, array $params): array
    {
        $employee = $this->resolveCurrentEmployee($user);
        if (!$employee) {
            return [
                'success' => false,
                'message' => __('Employee record not found.'),
            ];
        }

        try {
            $fromTime    = $params['from_time'];
            $toTime      = $params['to_time'];
            $date        = $params['date'] ?? Carbon::today()->toDateString();
            $type        = $params['type'] ?? PermissionType::DURING_SHIFT->value;
            $isEmergency = !empty($params['is_emergency']) || $type === PermissionType::EMERGENCY->value;

            // 1. Calculate hours duration
            $hours = $this->hrPermissionService->calculateHours($fromTime, $toTime);

            // 2. Fetch monthly balance for the requested month
            $balance = $this->hrPermissionService->getEmployeeMonthlyBalance($employee->id, $date);

            // 3. Human duration format using standard localization keys
            $totalMinutes = (int) round($hours * 60);
            $h = intdiv($totalMinutes, 60);
            $m = $totalMinutes % 60;

            $parts = [];
            if ($h > 0) {
                $parts[] = $h . ' ' . ($h === 1 ? __('hr') : __('hrs'));
            }
            if ($m > 0) {
                $parts[] = $m . ' ' . __('min');
            }
            $durationFormatted = !empty($parts) ? implode(' ', $parts) : ('0 ' . __('min'));

            // 4. Policy validations & warnings using standard localized keys
            $warnings = [];
            $isValid = true;

            if ($hours > $balance['max_single_hours']) {
                $warnings[] = __('Requested permission duration (:hours hrs) exceeds maximum single permission limit (:max hrs).', [
                    'hours' => $hours,
                    'max'   => $balance['max_single_hours'],
                ]);
                $isValid = false;
            }

            if ($balance['remaining_count'] <= 0) {
                $warnings[] = __('Employee has reached the maximum permission requests limit (:max) for this month.', [
                    'max' => $balance['max_monthly_count'],
                ]);
                $isValid = false;
            }

            if (!$isEmergency && $balance['min_advance_hours'] > 0) {
                try {
                    $permissionDateTime = Carbon::parse($date . ' ' . $fromTime);
                    $minNoticeDateTime  = Carbon::now()->addHours($balance['min_advance_hours']);
                    if ($permissionDateTime->lt($minNoticeDateTime)) {
                        $warnings[] = __('Permission requests require at least :hours hours advance notice.', [
                            'hours' => $balance['min_advance_hours'],
                        ]);
                        $isValid = false;
                    }
                } catch (\Throwable $e) {}
            }

            // Projected remaining balance
            $projectedRemainingHours = max(0.0, round($balance['remaining_hours'] - $hours, 2));

            return [
                'success'                   => true,
                'hours_count'               => $hours,
                'duration_formatted'        => $durationFormatted,
                'max_monthly_hours'         => $balance['max_monthly_hours'],
                'consumed_hours'            => $balance['consumed_hours'],
                'remaining_hours'           => $balance['remaining_hours'],
                'projected_remaining_hours' => $projectedRemainingHours,
                'remaining_count'           => $balance['remaining_count'],
                'max_single_hours'          => $balance['max_single_hours'],
                'is_valid'                  => $isValid,
                'warning'                   => !empty($warnings) ? implode(' | ', $warnings) : null,
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Store and submit a new permission request for the employee.
     */
    public function submitPermissionRequest(array $data, ?User $user): array
    {
        $employee = $this->resolveCurrentEmployee($user);

        if (!$employee) {
            return [
                'success' => false,
                'message' => __('No employee record associated with your account.'),
            ];
        }

        try {
            $data['employee_id'] = $employee->id;

            $permission = $this->hrPermissionService->createPermissionRequest($data, $user?->id ?? 1);

            return [
                'success' => true,
                'message' => __('Permission request submitted successfully and is pending approval!'),
                'data'    => $permission,
            ];
        } catch (\Throwable $e) {
            Log::error('PortalPermissionService submit error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get data required for permission requests history screen.
     */
    public function getPermissionsHistoryContext(?User $user, ?string $status = null): array
    {
        $employee = $this->resolveCurrentEmployee($user);
        $today = Carbon::today();

        if ($employee) {
            $balance = $this->hrPermissionService->getEmployeeMonthlyBalance($employee->id);
            $query = PermissionRequest::where('employee_id', $employee->id);
        } else {
            $policies = HrWorkPolicy::getGroup('permissions');
            $maxHours = (float) ($policies['max_monthly_permission_hours'] ?? 4.0);
            $maxCount = (int) ($policies['max_monthly_permission_count'] ?? 3);

            $balance = [
                'month'               => $today->format('Y-m'),
                'month_name'          => $today->translatedFormat('F Y'),
                'max_monthly_hours'   => $maxHours,
                'consumed_hours'      => 0.0,
                'remaining_hours'     => $maxHours,
                'max_monthly_count'   => $maxCount,
                'consumed_count'      => 0,
                'remaining_count'     => $maxCount,
                'max_single_hours'    => (float) ($policies['max_single_permission_hours'] ?? 2.0),
                'min_advance_hours'   => (int) ($policies['min_advance_notice_hours'] ?? 24),
                'financial_policy'    => $policies['permission_financial_policy'] ?? 'paid_with_excess_deduction',
                'approval_workflow'   => $policies['approval_workflow'] ?? 'manager_and_hr',
                'max_emergency_count' => (int) ($policies['max_monthly_emergency_permissions'] ?? 1),
                'can_request'         => true,
            ];
            $query = PermissionRequest::query();
        }

        // Calculate progress bar percentage and today date
        $maxHours = (float) ($balance['max_monthly_hours'] ?? 4.0);
        $consumed = (float) ($balance['consumed_hours'] ?? 0.0);
        $percentage = $maxHours > 0 ? min(100, round(($consumed / $maxHours) * 100)) : 0;
        $balance['consumption_percentage'] = $percentage;
        $balance['today_formatted'] = $today->translatedFormat('j F Y');

        // Calculate counts for each status tab
        $baseQuery = clone $query;
        $statusCounts = [
            'all'       => (clone $baseQuery)->count(),
            'pending'   => (clone $baseQuery)->whereIn('status', [
                PermissionRequestStatus::PENDING->value,
                PermissionRequestStatus::MANAGER_APPROVED->value
            ])->count(),
            'approved'  => (clone $baseQuery)->where('status', PermissionRequestStatus::APPROVED->value)->count(),
            'rejected'  => (clone $baseQuery)->where('status', PermissionRequestStatus::REJECTED->value)->count(),
            'emergency' => (clone $baseQuery)->where(function($q) {
                $q->where('is_emergency', true)->orWhere('type', PermissionType::EMERGENCY->value);
            })->count(),
        ];

        // Apply tab filter if provided
        $filter = $status ?: 'all';
        if ($filter === 'pending') {
            $query->whereIn('status', [
                PermissionRequestStatus::PENDING->value,
                PermissionRequestStatus::MANAGER_APPROVED->value
            ]);
        } elseif ($filter === 'approved') {
            $query->where('status', PermissionRequestStatus::APPROVED->value);
        } elseif ($filter === 'rejected') {
            $query->where('status', PermissionRequestStatus::REJECTED->value);
        } elseif ($filter === 'emergency') {
            $query->where(function($q) {
                $q->where('is_emergency', true)->orWhere('type', PermissionType::EMERGENCY->value);
            });
        }

        $permissions = $query->latest('date')->latest('id')->paginate(15);
        $permissions->getCollection()->transform(function ($perm) {
            $isPending = in_array($perm->status->value, [
                PermissionRequestStatus::PENDING->value,
                PermissionRequestStatus::MANAGER_APPROVED->value,
            ]);
            $isApproved = $perm->status->value === PermissionRequestStatus::APPROVED->value;
            $isRejected = $perm->status->value === PermissionRequestStatus::REJECTED->value;

            $iconClass = 'amber';
            $iconBx = 'bx-bell';
            if ($isApproved) {
                $iconClass = 'green';
                $iconBx = 'bx-check';
            } elseif ($isRejected) {
                $iconClass = 'red';
                $iconBx = 'bx-x';
            }

            $permTitle = __('Personal Permission');
            if ($perm->is_emergency || $perm->type->value === 'emergency') {
                $permTitle = __('Personal Emergency Permission');
            } elseif ($perm->type->value === 'early_departure') {
                $permTitle = __('Early Departure');
            } elseif ($perm->type->value === 'late_arrival') {
                $permTitle = __('Late Arrival');
            } elseif ($perm->type->value === 'during_shift') {
                $permTitle = __('Personal Permission');
            } else {
                $permTitle = $perm->type->label();
            }

            $requestCode = 'PER-' . ($perm->date ? $perm->date->format('Y') : date('Y')) . '-' . str_pad($perm->id, 4, '0', STR_PAD_LEFT);
            $formattedFrom = Carbon::parse($perm->from_time)->translatedFormat('h:i A');
            $formattedTo = Carbon::parse($perm->to_time)->translatedFormat('h:i A');
            $formattedDate = $perm->date ? $perm->date->translatedFormat('l j F Y') : '-';

            $perm->is_pending_status = $isPending;
            $perm->is_approved_status = $isApproved;
            $perm->is_rejected_status = $isRejected;
            $perm->icon_class = $iconClass;
            $perm->icon_bx = $iconBx;
            $perm->display_title = $permTitle;
            $perm->request_code = $requestCode;
            $perm->formatted_from = $formattedFrom;
            $perm->formatted_to = $formattedTo;
            $perm->formatted_date = $formattedDate;

            return $perm;
        });

        return [
            'employee'      => $employee,
            'user'          => $user,
            'balance'       => $balance,
            'statusCounts'  => $statusCounts,
            'permissions'   => $permissions,
            'currentFilter' => $filter,
        ];
    }

    /**
     * Get data required for permission request details screen.
     */
    public function getPermissionDetails(?User $user, int $id): ?array
    {
        $employee = $this->resolveCurrentEmployee($user);
        if (!$employee) {
            return null;
        }

        $query = PermissionRequest::with([
            'employee.translations',
            'employee.department',
            'creator',
            'managerApprover',
            'hrApprover',
        ])->where('id', $id);

        // Security check: restrict to own employee requests if mapped
        if ($user && $this->employeeRepo->findByUserId($user->id)) {
            $query->where('employee_id', $employee->id);
        }

        $permission = $query->first();

        if (!$permission) {
            return null;
        }

        $balance = $this->hrPermissionService->getEmployeeMonthlyBalance(
            $permission->employee_id ?? $employee->id,
            $permission->date?->toDateString()
        );

        $status = $permission->status;
        $isPending = in_array($status->value, [
            PermissionRequestStatus::PENDING->value,
            PermissionRequestStatus::MANAGER_APPROVED->value,
        ]);
        $isApproved = $status === PermissionRequestStatus::APPROVED;
        $isRejected = $status === PermissionRequestStatus::REJECTED;
        $isCancelled = $status === PermissionRequestStatus::CANCELLED;

        // Permission Title
        $title = __('Personal Permission');
        if ($permission->is_emergency || $permission->type->value === 'emergency') {
            $title = __('Emergency Permission');
        } elseif ($permission->type->value === 'early_departure') {
            $title = __('Early Departure');
        } elseif ($permission->type->value === 'late_arrival') {
            $title = __('Late Arrival');
        } elseif ($permission->type->value === 'during_shift') {
            $title = __('Personal Permission During Shift');
        } else {
            $title = $permission->type->label();
        }

        // Hero state theme and icons
        $heroClass = 'pending';
        $statusIcon = 'bx-time-five';
        $statusText = __('Under Review & Approval');

        if ($isApproved) {
            $heroClass = 'approved';
            $statusIcon = 'bx-check-double';
            $statusText = __('Approved');
        } elseif ($isRejected) {
            $heroClass = 'rejected';
            $statusIcon = 'bx-x-circle';
            $statusText = __('Request Rejected');
        } elseif ($isCancelled) {
            $heroClass = 'cancelled';
            $statusIcon = 'bx-block';
            $statusText = __('Request Cancelled');
        }

        $requestCode = 'PER-' . ($permission->date ? $permission->date->format('Y') : date('Y')) . '-' . str_pad($permission->id, 4, '0', STR_PAD_LEFT);
        $formattedDate = $permission->date ? $permission->date->translatedFormat('l, j F Y') : '-';
        $formattedFrom = Carbon::parse($permission->from_time)->translatedFormat('h:i A');
        $formattedTo = Carbon::parse($permission->to_time)->translatedFormat('h:i A');
        $createdAt = $permission->created_at ? $permission->created_at->translatedFormat('j F Y - h:i A') : '-';

        // Direct Manager Approval step
        $mgrApproved = !empty($permission->manager_approved_at);
        $mgrDate = $permission->manager_approved_at ? $permission->manager_approved_at->translatedFormat('j F Y - h:i A') : null;

        // HR Approval step
        $hrApproved = !empty($permission->hr_approved_at) && $isApproved;
        $hrClass = $hrApproved ? 'completed' : ($isRejected ? 'rejected' : ($isApproved ? 'completed' : ''));
        $hrDate = $permission->hr_approved_at ? $permission->hr_approved_at->translatedFormat('j F Y - h:i A') : null;

        // Calculate Arabic duration & combined time string dynamically
        $hours = (float) $permission->hours_count;
        $durationText = $this->formatArabicHours($hours);
        $timeAndDuration = $durationText . ' (' . $formattedFrom . ' - ' . $formattedTo . ')';

        // Reason text (only if provided in database)
        $hasReason = !empty(trim((string)$permission->reason));
        $reasonText = $hasReason ? trim($permission->reason) : null;

        // Attachment info (only if file actually exists on record)
        $hasAttachment = !empty($permission->attachment);
        $attachmentName = $hasAttachment ? basename($permission->attachment) : null;
        $attachmentUrl = $hasAttachment ? asset('storage/' . $permission->attachment) : null;

        // Emergency flag & Type label
        $isEmergency = !empty($permission->is_emergency) || $permission->type->value === 'emergency';
        $permTypeLabel = $permission->type->label();

        // Dynamic Workflow based on actual company policy in hr_work_policies
        $workflow = $balance['approval_workflow'] ?? 'manager_and_hr';

        $steps = [];

        // 1. Step: Submission (Always exists)
        $steps[] = [
            'title'       => 'تقديم الطلب وتوثيق البيانات',
            'subtitle'    => $createdAt . ' • مسجل إلكترونياً',
            'state'       => 'completed',
            'line_active' => $isApproved || ($workflow === 'manager_and_hr' && $mgrApproved),
            'icon'        => 'bx-check',
        ];

        // 2. Manager Step (Only if workflow involves direct manager)
        if (in_array($workflow, ['manager_and_hr', 'manager_only'])) {
            if ($mgrApproved) {
                $mgrSubtitle = ($permission->managerApprover?->name ?? __('Direct Manager')) . ($mgrDate ? ' • ' . $mgrDate : '');
                $mgrState = 'completed';
                $mgrIcon = 'bx-check';
                $mgrLineActive = $isApproved;
            } elseif ($isRejected && empty($permission->manager_approved_at)) {
                $mgrSubtitle = 'لم تتم الموافقة';
                $mgrState = 'rejected';
                $mgrIcon = 'bx-x';
                $mgrLineActive = false;
            } elseif ($isCancelled) {
                $mgrSubtitle = 'تم إلغاء الطلب من قبل الموظف';
                $mgrState = 'rejected';
                $mgrIcon = 'bx-block';
                $mgrLineActive = false;
            } else {
                $mgrSubtitle = 'بانتظار مراجعة واعتماد الإدارة المباشرة';
                $mgrState = 'current';
                $mgrIcon = 'bx-refresh';
                $mgrLineActive = false;
            }

            $steps[] = [
                'title'       => 'موافقة مديرة الروضة / الفرع',
                'subtitle'    => $mgrSubtitle,
                'state'       => $mgrState,
                'line_active' => $mgrLineActive,
                'icon'        => $mgrIcon,
            ];
        }

        // 3. HR Step (Only if workflow involves HR)
        if (in_array($workflow, ['manager_and_hr', 'hr_only'])) {
            if ($hrApproved) {
                $hrSubtitle = ($permission->hrApprover?->name ?? __('HR Management')) . ($hrDate ? ' • ' . $hrDate : '');
                $hrState = 'completed';
                $hrIcon = 'bx-check';
                $hrLineActive = true;
            } elseif ($isRejected) {
                $hrSubtitle = 'تم رفض الطلب' . (!empty($permission->rejection_reason) ? ' (' . $permission->rejection_reason . ')' : '');
                $hrState = 'rejected';
                $hrIcon = 'bx-x';
                $hrLineActive = false;
            } elseif ($isCancelled) {
                $hrSubtitle = 'تم إلغاء الطلب من قبل الموظف';
                $hrState = 'rejected';
                $hrIcon = 'bx-block';
                $hrLineActive = false;
            } elseif ($workflow === 'manager_and_hr' && !$mgrApproved) {
                $hrSubtitle = 'بانتظار اعتماد الإدارة المباشرة أولاً';
                $hrState = 'locked';
                $hrIcon = 'bx-lock-alt';
                $hrLineActive = false;
            } else {
                // For hr_only or when manager already approved
                $hrSubtitle = 'جاري مراجعة الموارد البشرية والاعتماد';
                $hrState = 'current';
                $hrIcon = 'bx-refresh';
                $hrLineActive = false;
            }

            $steps[] = [
                'title'       => 'اعتماد الإدارة المالية وشؤون الموظفين (HR)',
                'subtitle'    => $hrSubtitle,
                'state'       => $hrState,
                'line_active' => $hrLineActive,
                'icon'        => $hrIcon,
            ];
        }

        // 4. Execution / Attendance sync Step (Final)
        $steps[] = [
            'title'       => 'تسليم الطلب والتوثيق بسجل الحضور',
            'subtitle'    => $isApproved 
                ? 'تم الاعتماد وتوثيق ساعات الاستئذان في سجل الحضور' 
                : 'يتم التحويل والتوثيق تلقائياً بعد الاعتماد النهائي',
            'state'       => $isApproved ? 'completed' : 'locked',
            'line_active' => false,
            'icon'        => $isApproved ? 'bx-check' : 'bx-lock-alt',
        ];

        // Total count of stages dynamically
        $totalStages = count($steps);

        // Stage text calculation
        if ($isApproved) {
            $stageText = 'المرحلة ' . $totalStages . ' من ' . $totalStages . ' (معتمد)';
        } elseif ($isRejected) {
            $stageText = 'تم رفض الطلب';
        } elseif ($isCancelled) {
            $stageText = 'تم إلغاء الطلب';
        } else {
            $currentStageNum = 2;
            foreach ($steps as $idx => $st) {
                if ($st['state'] === 'current') {
                    $currentStageNum = $idx + 1;
                    break;
                }
            }
            $stageText = 'المرحلة ' . $currentStageNum . ' من ' . $totalStages;
        }

        return [
            'employee'              => $employee,
            'user'                  => $user,
            'permission'            => $permission,
            'balance'               => $balance,
            'requestCode'           => $requestCode,
            'permTitle'             => $title,
            'permTypeLabel'         => $permTypeLabel,
            'isEmergency'           => $isEmergency,
            'heroClass'             => $heroClass,
            'statusIcon'            => $statusIcon,
            'statusText'            => $statusText,
            'isPending'             => $isPending,
            'isApproved'            => $isApproved,
            'isRejected'            => $isRejected,
            'isCancelled'           => $isCancelled,
            'formattedDate'         => $formattedDate,
            'formattedFrom'         => $formattedFrom,
            'formattedTo'           => $formattedTo,
            'createdAt'             => $createdAt,
            'timeAndDuration'       => $timeAndDuration,
            'hasReason'             => $hasReason,
            'reasonText'            => $reasonText,
            'hasAttachment'         => $hasAttachment,
            'attachmentName'        => $attachmentName,
            'attachmentUrl'         => $attachmentUrl,
            'stageText'             => $stageText,
            'steps'                 => $steps,
        ];
    }

    /**
     * Helper to format duration in natural Arabic text.
     */
    protected function formatArabicHours(float $hours): string
    {
        if ($hours == 1.0) {
            return 'ساعة واحدة';
        } elseif ($hours == 2.0) {
            return 'ساعتان';
        } elseif ($hours == 1.5) {
            return 'ساعة ونصف';
        } elseif ($hours == 2.5) {
            return 'ساعتان ونصف';
        } elseif ($hours >= 3 && $hours <= 10) {
            return $hours . ' ساعات';
        } else {
            return $hours . ' ساعة';
        }
    }

    /**
     * Cancel an existing permission request if it is still pending.
     */
    public function cancelPermissionRequest(?User $user, int $id): array
    {
        $employee = $this->resolveCurrentEmployee($user);
        if (!$employee) {
            return [
                'success' => false,
                'message' => __('Employee record not found.'),
            ];
        }

        $query = PermissionRequest::where('id', $id);
        if ($user && $this->employeeRepo->findByUserId($user->id)) {
            $query->where('employee_id', $employee->id);
        }

        $permission = $query->first();

        if (!$permission) {
            return [
                'success' => false,
                'message' => __('Permission request not found.'),
            ];
        }

        if (!in_array($permission->status->value, [
            PermissionRequestStatus::PENDING->value,
            PermissionRequestStatus::MANAGER_APPROVED->value,
        ])) {
            return [
                'success' => false,
                'message' => __('Cannot cancel a request that has already been approved, rejected, or cancelled.'),
            ];
        }

        $permission->status = PermissionRequestStatus::CANCELLED;
        $permission->save();

        return [
            'success' => true,
            'message' => __('Permission request has been cancelled successfully.'),
            'data'    => $permission,
        ];
    }
}

