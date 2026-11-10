<?php

namespace Modules\HR\Services;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Modules\HR\Enums\ExitClearanceStatus;
use Modules\HR\Models\Employee;
use Modules\HR\Models\ExitClearance;
use Modules\HR\Models\ExitClearanceApproval;
use Modules\HR\Models\HrWorkPolicy;
use Modules\HR\Models\Loan;
use Modules\HR\Repositories\Contracts\ExitClearanceRepositoryInterface;

class ExitClearanceService
{
    public function __construct(
        protected ExitClearanceRepositoryInterface $repository
    ) {}

    public function getDataTableQuery(array $filters = []): Builder
    {
        return $this->repository->getQuery($filters);
    }

    public function getKPIs(): array
    {
        return $this->repository->getKPIs();
    }

    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getPaginated($filters, $perPage);
    }

    public function findById(int $id): ?ExitClearance
    {
        return $this->repository->findById($id);
    }

    /**
     * Create exit clearance request with automated calculations & department checklists.
     */
    public function createClearance(array $data, ?int $userId = null): ExitClearance
    {
        return DB::transaction(function () use ($data, $userId) {
            $employee = Employee::with(['department', 'jobTitle'])->findOrFail($data['employee_id']);

            // Calculate pending outstanding loans automatically
            $outstandingLoans = Loan::where('employee_id', $employee->id)
                ->where('status', 'active')
                ->sum('remaining_amount') ?? 0.00;

            $clearanceCode = $this->repository->generateClearanceCode();

            $clearance = $this->repository->create([
                'clearance_code'           => $clearanceCode,
                'employee_id'              => $employee->id,
                'department_id'            => $employee->department_id,
                'job_title_id'             => $employee->job_title_id,
                'resignation_date'         => $data['resignation_date'] ?? null,
                'last_working_day'         => $data['last_working_day'],
                'leaving_reason'           => $data['leaving_reason'] ?? 'resignation',
                'reason_details'           => $data['reason_details'] ?? null,
                'status'                   => ExitClearanceStatus::PENDING,
                'outstanding_loans_amount' => $outstandingLoans,
                'leave_balance_days'       => $data['leave_balance_days'] ?? 0.00,
                'end_of_service_amount'    => $data['end_of_service_amount'] ?? 0.00,
                'net_settlement_amount'    => ($data['end_of_service_amount'] ?? 0.00) - $outstandingLoans,
                'created_by'               => $userId,
            ]);

            // Create approval checklist stages
            $this->repository->createDefaultApprovals($clearance);

            return $clearance;
        });
    }

    /**
     * Process department clearance sign-off (Approval / Has Dues).
     */
    public function updateDepartmentApproval(int $approvalId, array $data, int $userId): bool
    {
        return DB::transaction(function () use ($approvalId, $data, $userId) {
            $approval = ExitClearanceApproval::with('clearance')->findOrFail($approvalId);

            $updateData = [
                'status'      => $data['status'],
                'approved_by' => $userId,
                'approved_at' => now(),
                'notes'       => $data['notes'] ?? null,
                'dues_amount' => $data['dues_amount'] ?? 0.00,
            ];

            $this->repository->updateApproval($approvalId, $updateData);

            // Update main clearance status to in_progress if pending
            $clearance = $approval->clearance;
            if ($clearance->status === ExitClearanceStatus::PENDING) {
                $clearance->update(['status' => ExitClearanceStatus::IN_PROGRESS]);
            }

            return true;
        });
    }

    /**
     * Final sign-off by HR administration.
     */
    public function finalizeClearance(int $clearanceId, array $data, int $userId): bool
    {
        return DB::transaction(function () use ($clearanceId, $data, $userId) {
            $clearance = ExitClearance::with('approvals', 'employee.user')->findOrFail($clearanceId);

            // Check if any approval is still pending or has dues
            $pendingCount = $clearance->approvals()->where('status', '!=', 'cleared')->count();
            if ($pendingCount > 0 && empty($data['force_override'])) {
                throw new Exception(__('All department checklists must be fully cleared before final HR approval.'));
            }

            $clearance->update([
                'status'            => ExitClearanceStatus::CLEARED,
                'final_approved_by' => $userId,
                'final_approved_at' => now(),
                'final_notes'       => $data['final_notes'] ?? null,
                'updated_by'        => $userId,
            ]);

            // 1. Update Employee status to resigned / terminated
            $newStatus = match ($clearance->leaving_reason) {
                'termination' => 'terminated',
                default       => 'resigned',
            };
            $clearance->employee->update(['status' => $newStatus]);

            // 2. Auto-deactivate user login if enabled in policy
            $autoDeactivate = HrWorkPolicy::where('key', 'clearance_auto_deactivate_user')->value('value');
            if ($autoDeactivate == '1' && $clearance->employee->user) {
                $clearance->employee->user->update(['is_active' => false]);
            }

            return true;
        });
    }

    public function deleteClearance(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
