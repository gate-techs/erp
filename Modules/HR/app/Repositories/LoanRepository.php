<?php

namespace Modules\HR\Repositories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Modules\HR\Enums\InstallmentStatus;
use Modules\HR\Enums\LoanStatus;
use Modules\HR\Models\Loan;
use Modules\HR\Models\LoanInstallment;
use Modules\HR\Repositories\Contracts\LoanRepositoryInterface;

class LoanRepository implements LoanRepositoryInterface
{
    /**
     * Get DataTable query with applied filters.
     */
    public function getDataTableQuery(array $filters = []): Builder
    {
        $query = Loan::with([
            'employee.translations',
            'employee.department.translations',
            'creator',
            'installments',
        ]);

        // Branch Filter
        if (!empty($filters['branch_id'])) {
            $query->whereHas('employee', function ($q) use ($filters) {
                $q->where('primary_branch_id', $filters['branch_id']);
            });
        }

        // Department Filter
        if (!empty($filters['department_id'])) {
            $query->whereHas('employee', function ($q) use ($filters) {
                $q->where('department_id', $filters['department_id']);
            });
        }

        // Employee Filter
        if (!empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        // Loan Type Filter
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        // Status Filter
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Date Range Filters (start_date)
        if (!empty($filters['date_from'])) {
            $query->whereDate('start_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('start_date', '<=', $filters['date_to']);
        }

        return $query;
    }

    /**
     * Find loan by ID.
     */
    public function findById(int $id, array $with = []): ?Loan
    {
        $query = Loan::query();

        if (!empty($with)) {
            $query->with($with);
        }

        return $query->find($id);
    }

    /**
     * Create a new loan.
     */
    public function create(array $data): Loan
    {
        return Loan::create($data);
    }

    /**
     * Update loan attributes.
     */
    public function update(Loan $loan, array $data): bool
    {
        return $loan->update($data);
    }

    /**
     * Delete a loan.
     */
    public function delete(Loan $loan): bool
    {
        return $loan->delete();
    }

    /**
     * Get count of active (disbursed or approved) loans for an employee.
     */
    public function getEmployeeActiveLoansCount(int $employeeId): int
    {
        return Loan::where('employee_id', $employeeId)
            ->whereIn('status', [LoanStatus::APPROVED->value, LoanStatus::DISBURSED->value])
            ->where('remaining_amount', '>', 0)
            ->count();
    }

    /**
     * Get total remaining debt for an employee across active loans.
     */
    public function getEmployeeActiveLoansTotalRemaining(int $employeeId): float
    {
        return (float) Loan::where('employee_id', $employeeId)
            ->whereIn('status', [LoanStatus::APPROVED->value, LoanStatus::DISBURSED->value])
            ->sum('remaining_amount');
    }

    /**
     * Get KPI metrics for index dashboard.
     */
    public function getKPIs(): array
    {
        $now          = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth()->toDateString();
        $endOfMonth   = $now->copy()->endOfMonth()->toDateString();
        $year         = $now->year;
        $month        = $now->month;

        // 1. Total Disbursed Loans this month
        $disbursedMonth = (float) Loan::whereIn('status', [LoanStatus::DISBURSED->value, LoanStatus::PAID->value])
            ->whereBetween('disbursed_at', [$startOfMonth . ' 00:00:00', $endOfMonth . ' 23:59:59'])
            ->sum('amount');

        // 2. Total Recovered / Repaid overall
        $totalRepaid = (float) Loan::sum('total_paid');

        // 3. Installments Due This Month
        $dueThisMonth = (float) LoanInstallment::where('status', InstallmentStatus::PENDING->value)
            ->whereYear('due_date', $year)
            ->whereMonth('due_date', $month)
            ->sum('amount');

        // 4. Pending Requests Count
        $pendingCount = Loan::pending()->count();

        return [
            'disbursed_month' => $disbursedMonth,
            'total_repaid'    => $totalRepaid,
            'due_this_month'  => $dueThisMonth,
            'pending_count'   => $pendingCount,
        ];
    }
}
