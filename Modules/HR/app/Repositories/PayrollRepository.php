<?php

namespace Modules\HR\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\HR\Enums\PayrollStatus;
use Modules\HR\Models\Payroll;
use Modules\HR\Models\PayrollItem;
use Modules\HR\Repositories\Contracts\PayrollRepositoryInterface;

class PayrollRepository implements PayrollRepositoryInterface
{
    public function getQuery(): Builder
    {
        return Payroll::query()->with(['branch', 'creator', 'approver', 'payer']);
    }

    public function all(array $columns = ['*']): Collection
    {
        return Payroll::all($columns);
    }

    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->getQuery()->latest('id')->paginate($perPage, $columns);
    }

    public function find(int $id): ?Payroll
    {
        return Payroll::find($id);
    }

    public function findWithDetails(int $id): ?Payroll
    {
        return Payroll::with([
            'branch',
            'creator',
            'approver',
            'payer',
            'canceller',
            'items.employee.jobTitle',
            'items.employee.department',
            'items.employee.primaryBranch',
        ])->find($id);
    }

    public function create(array $data): Payroll
    {
        return Payroll::create($data);
    }

    public function update(Payroll $payroll, array $data): bool
    {
        return $payroll->update($data);
    }

    public function delete(Payroll $payroll): bool
    {
        return $payroll->delete();
    }

    public function findItem(int $itemId): ?PayrollItem
    {
        return PayrollItem::with(['employee.jobTitle', 'employee.department', 'employee.primaryBranch', 'payroll'])->find($itemId);
    }

    public function updateItem(PayrollItem $item, array $data): bool
    {
        return $item->update($data);
    }

    public function deleteItem(PayrollItem $item): bool
    {
        return $item->delete();
    }

    public function getKpis(?string $month = null, ?int $branchId = null): array
    {
        $currentMonth = $month ?: now()->format('Y-m');
        $currentYear = substr($currentMonth, 0, 4);

        $baseQuery = Payroll::query();
        if ($branchId) {
            $baseQuery->where('branch_id', $branchId);
        }

        // Total Disbursed (Paid) YTD
        $totalPaidYtd = (clone $baseQuery)
            ->where('payroll_month', 'like', "{$currentYear}-%")
            ->where('status', PayrollStatus::PAID->value)
            ->sum('total_net');

        // Current Month Net Total (across all statuses except cancelled)
        $thisMonthNet = (clone $baseQuery)
            ->where('payroll_month', $currentMonth)
            ->where('status', '!=', PayrollStatus::CANCELLED->value)
            ->sum('total_net');

        // Pending Approval Count
        $pendingCount = (clone $baseQuery)
            ->where('status', PayrollStatus::DRAFT->value)
            ->count();

        // Total Employees Processed in this month
        $employeesCount = (clone $baseQuery)
            ->where('payroll_month', $currentMonth)
            ->where('status', '!=', PayrollStatus::CANCELLED->value)
            ->sum('employees_count');

        return [
            'total_paid_ytd'  => (float) $totalPaidYtd,
            'this_month_net'  => (float) $thisMonthNet,
            'pending_count'   => (int) $pendingCount,
            'employees_count' => (int) $employeesCount,
        ];
    }
}
