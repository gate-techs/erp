<?php

namespace Modules\EmployeePortal\Services;

use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Modules\EmployeePortal\Repositories\Contracts\PortalEmployeeRepositoryInterface;
use Modules\EmployeePortal\Services\Contracts\PortalExitClearanceServiceInterface;
use Modules\HR\Enums\ExitClearanceStatus;
use Modules\HR\Models\Employee;
use Modules\HR\Models\ExitClearance;
use Modules\HR\Models\Loan;
use Modules\HR\Services\ExitClearanceService;
use Modules\Settings\Services\Contracts\NotificationServiceInterface;

class PortalExitClearanceService implements PortalExitClearanceServiceInterface
{
    public function __construct(
        protected PortalEmployeeRepositoryInterface $employeeRepo,
        protected ExitClearanceService $hrClearanceService,
        protected NotificationServiceInterface $notificationService
    ) {}

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

    public function getFormContext(?User $user): array
    {
        $employee = $this->resolveCurrentEmployee($user);

        $pendingClearance = null;
        $outstandingLoans = 0.00;
        $leaveBalance = 0.00;

        if ($employee) {
            $pendingClearance = ExitClearance::where('employee_id', $employee->id)
                ->whereIn('status', [ExitClearanceStatus::PENDING, ExitClearanceStatus::IN_PROGRESS])
                ->latest('id')
                ->first();

            $outstandingLoans = (float) Loan::where('employee_id', $employee->id)
                ->where('status', 'active')
                ->sum('remaining_amount');

            // Remaining leave balance if available on employee model
            $leaveBalance = (float) ($employee->remaining_leaves_count ?? $employee->annual_leaves_balance ?? 0);
        }

        return [
            'employee'          => $employee,
            'pendingClearance'  => $pendingClearance,
            'outstandingLoans'  => $outstandingLoans,
            'leaveBalance'      => $leaveBalance,
            'reasons'           => [
                'resignation'     => __('Resignation'),
                'contract_expiry' => __('Contract Expiry'),
                'retirement'      => __('Retirement'),
                'other'           => __('Other'),
            ],
        ];
    }

    public function storeClearanceRequest(?User $user, array $data): array
    {
        $employee = $this->resolveCurrentEmployee($user);

        if (!$employee) {
            return [
                'success' => false,
                'message' => __('Employee record not found.'),
            ];
        }

        // Check if there is already an active/pending clearance
        $existing = ExitClearance::where('employee_id', $employee->id)
            ->whereIn('status', [ExitClearanceStatus::PENDING, ExitClearanceStatus::IN_PROGRESS])
            ->first();

        if ($existing) {
            return [
                'success' => false,
                'message' => __('You already have an active exit clearance request under processing (:code).', ['code' => $existing->clearance_code]),
            ];
        }

        try {
            $payload = [
                'employee_id'           => $employee->id,
                'resignation_date'      => $data['resignation_date'] ?? date('Y-m-d'),
                'last_working_day'      => $data['last_working_day'],
                'leaving_reason'        => $data['leaving_reason'] ?? 'resignation',
                'reason_details'        => $data['reason_details'] ?? null,
                'leave_balance_days'    => $data['leave_balance_days'] ?? 0.0,
                'end_of_service_amount' => 0.0,
            ];

            $clearance = $this->hrClearanceService->createClearance($payload, $user?->id);

            // Send notification to HR Manager / HR team
            $this->notifyHrOnClearanceSubmission($clearance, $employee);

            return [
                'success'   => true,
                'message'   => __('Exit clearance request submitted successfully. Checklists assigned to relevant departments.'),
                'clearance' => $clearance,
            ];
        } catch (Exception $e) {
            Log::error('Portal Exit Clearance Store Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return [
                'success' => false,
                'message' => __('Failed to submit exit clearance request: :err', ['err' => $e->getMessage()]),
            ];
        }
    }

    public function getEmployeeClearanceHistory(?User $user, array $filters = []): array
    {
        $employee = $this->resolveCurrentEmployee($user);

        if (!$employee) {
            return [
                'clearances'   => collect(),
                'statusCounts' => ['all' => 0, 'pending' => 0, 'in_progress' => 0, 'cleared' => 0],
                'filter'       => 'all',
            ];
        }

        $query = ExitClearance::with(['approvals', 'department', 'jobTitle'])
            ->where('employee_id', $employee->id)
            ->latest('id');

        $allRecords = (clone $query)->get();

        $statusCounts = [
            'all'         => $allRecords->count(),
            'pending'     => $allRecords->where('status', ExitClearanceStatus::PENDING)->count(),
            'in_progress' => $allRecords->where('status', ExitClearanceStatus::IN_PROGRESS)->count(),
            'cleared'     => $allRecords->where('status', ExitClearanceStatus::CLEARED)->count(),
        ];

        $filter = $filters['status'] ?? 'all';
        if ($filter !== 'all') {
            $statusEnum = ExitClearanceStatus::tryFrom($filter);
            if ($statusEnum) {
                $query->where('status', $statusEnum);
            }
        }

        $clearances = $query->paginate(15);

        return [
            'clearances'   => $clearances,
            'statusCounts' => $statusCounts,
            'filter'       => $filter,
            'employee'     => $employee,
        ];
    }

    public function getClearanceDetails(?User $user, int $id): ?ExitClearance
    {
        $employee = $this->resolveCurrentEmployee($user);
        if (!$employee) {
            return null;
        }

        return ExitClearance::with(['approvals.approver', 'department', 'jobTitle'])
            ->where('employee_id', $employee->id)
            ->where('id', $id)
            ->first();
    }

    /**
     * Send notification to HR Manager / HR Administration when exit clearance is requested.
     */
    protected function notifyHrOnClearanceSubmission(ExitClearance $clearance, Employee $employee): void
    {
        try {
            $recipients = collect();

            // HR Manager, Administrative HR, Super Admin, Admin roles
            $targetRoleNames = ['hr_manager', 'Administrative hr', 'HR Manager', 'super_admin', 'admin'];

            $roleUsers = User::role($targetRoleNames)->where('is_active', true)->get();
            $recipients = $recipients->merge($roleUsers);

            // Also direct manager if assigned and active
            $managerUser = $employee->manager?->user;
            if ($managerUser && $managerUser->is_active) {
                $recipients->push($managerUser);
            }

            $recipients = $recipients->unique('id');

            if ($recipients->isEmpty()) {
                Log::warning("No HR managers found to notify for exit clearance #{$clearance->id}");
                return;
            }

            $actionUrl = route('hr.exit-clearances.index');

            $variables = [
                'employee_name'    => $employee->name ?? __('Employee'),
                'employee_code'    => $employee->employee_code ?? '',
                'clearance_code'   => $clearance->clearance_code,
                'last_working_day' => Carbon::parse($clearance->last_working_day)->format('Y-m-d'),
                'resignation_date' => Carbon::parse($clearance->resignation_date)->format('Y-m-d'),
                'action_url'       => $actionUrl,
            ];

            $this->notificationService->sendTemplateNotification(
                'exit_clearance_submitted',
                $variables,
                $recipients,
                $actionUrl
            );

            Log::info("Exit clearance notification sent to HR users: [" . $recipients->pluck('id')->implode(', ') . "] for clearance #{$clearance->id}");
        } catch (\Throwable $e) {
            Log::warning('Exit clearance submission notification failed: ' . $e->getMessage(), [
                'clearance_id' => $clearance->id,
            ]);
        }
    }
}

