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

        return [
            'employee'      => $employee,
            'user'          => $user,
            'balance'       => $balance,
            'statusCounts'  => $statusCounts,
            'permissions'   => $permissions,
            'currentFilter' => $filter,
        ];
    }
}
