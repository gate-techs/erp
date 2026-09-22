<?php

namespace Modules\HR\Repositories;

use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection as BaseCollection;
use Modules\HR\Enums\LeaveRequestStatus;
use Modules\HR\Enums\LeaveType;
use Modules\HR\Enums\PermissionRequestStatus;
use Modules\HR\Models\Employee;
use Modules\HR\Models\HrWorkPolicy;
use Modules\HR\Models\LeaveRequest;
use Modules\HR\Models\PermissionRequest;
use Modules\HR\Repositories\Contracts\LeaveReportRepositoryInterface;

class LeaveReportRepository implements LeaveReportRepositoryInterface
{
    /**
     * Get Leave Requests filtered query builder.
     *
     * @param array<string, mixed> $filters
     * @return Builder
     */
    public function getLeaveRequestsQuery(array $filters = []): Builder
    {
        $query = LeaveRequest::with([
            'employee.translations',
            'employee.department.translations',
            'employee.primaryBranch.translations',
            'replacementEmployee.translations',
            'hrApprover',
            'managerApprover',
        ]);

        // 1. Date Range
        $startDate = $filters['start_date'] ?? null;
        $endDate   = $filters['end_date'] ?? null;

        if ($startDate && $endDate) {
            $query->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                  ->orWhereBetween('end_date', [$startDate, $endDate])
                  ->orWhere(function ($sub) use ($startDate, $endDate) {
                      $sub->where('start_date', '<=', $startDate)
                          ->where('end_date', '>=', $endDate);
                  });
            });
        } elseif ($startDate) {
            $query->whereDate('start_date', '>=', $startDate);
        } elseif ($endDate) {
            $query->whereDate('end_date', '<=', $endDate);
        }

        // 2. Department Filter
        if (!empty($filters['department_id'])) {
            $query->whereHas('employee', function ($q) use ($filters) {
                $q->where('department_id', $filters['department_id']);
            });
        }

        // 3. Branch Filter
        if (!empty($filters['branch_id'])) {
            $query->whereHas('employee', function ($q) use ($filters) {
                $q->where('primary_branch_id', $filters['branch_id']);
            });
        }

        // 4. Employee Filter
        if (!empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        // 5. Leave Type Filter
        if (!empty($filters['leave_type'])) {
            $query->where('leave_type', $filters['leave_type']);
        }

        // 6. Status Filter
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('start_date', 'desc')->orderBy('id', 'desc');
    }

    /**
     * Get paginated Leave Requests.
     *
     * @param array<string, mixed> $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getLeaveRequestsPaginated(array $filters = [], int $perPage = 25): LengthAwarePaginator
    {
        return $this->getLeaveRequestsQuery($filters)->paginate($perPage);
    }

    /**
     * Get all filtered Leave Requests without pagination.
     *
     * @param array<string, mixed> $filters
     * @return Collection
     */
    public function getLeaveRequestsRecords(array $filters = []): Collection
    {
        return $this->getLeaveRequestsQuery($filters)->get();
    }

    /**
     * Get Permission Requests filtered query builder.
     *
     * @param array<string, mixed> $filters
     * @return Builder
     */
    public function getPermissionsQuery(array $filters = []): Builder
    {
        $query = PermissionRequest::with([
            'employee.translations',
            'employee.department.translations',
            'employee.primaryBranch.translations',
            'hrApprover',
            'managerApprover',
        ]);

        // 1. Date Range
        $startDate = $filters['start_date'] ?? null;
        $endDate   = $filters['end_date'] ?? null;

        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        } elseif ($startDate) {
            $query->whereDate('date', '>=', $startDate);
        } elseif ($endDate) {
            $query->whereDate('date', '<=', $endDate);
        }

        // 2. Department Filter
        if (!empty($filters['department_id'])) {
            $query->whereHas('employee', function ($q) use ($filters) {
                $q->where('department_id', $filters['department_id']);
            });
        }

        // 3. Branch Filter
        if (!empty($filters['branch_id'])) {
            $query->whereHas('employee', function ($q) use ($filters) {
                $q->where('primary_branch_id', $filters['branch_id']);
            });
        }

        // 4. Employee Filter
        if (!empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        // 5. Permission Type Filter
        if (!empty($filters['permission_type'])) {
            $query->where('type', $filters['permission_type']);
        }

        // 6. Status Filter
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('date', 'desc')->orderBy('id', 'desc');
    }

    /**
     * Get paginated Permission Requests.
     *
     * @param array<string, mixed> $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPermissionsPaginated(array $filters = [], int $perPage = 25): LengthAwarePaginator
    {
        return $this->getPermissionsQuery($filters)->paginate($perPage);
    }

    /**
     * Get all filtered Permission Requests without pagination.
     *
     * @param array<string, mixed> $filters
     * @return Collection
     */
    public function getPermissionsRecords(array $filters = []): Collection
    {
        return $this->getPermissionsQuery($filters)->get();
    }

    /**
     * Get Leave Balances calculation records for employees.
     *
     * @param array<string, mixed> $filters
     * @return BaseCollection
     */
    public function getLeaveBalancesRecords(array $filters = []): BaseCollection
    {
        $employeeQuery = Employee::with([
            'translations',
            'department.translations',
            'primaryBranch.translations',
        ]);

        if (!empty($filters['department_id'])) {
            $employeeQuery->where('department_id', $filters['department_id']);
        }

        if (!empty($filters['branch_id'])) {
            $employeeQuery->where('primary_branch_id', $filters['branch_id']);
        }

        if (!empty($filters['employee_id'])) {
            $employeeQuery->where('id', $filters['employee_id']);
        }

        $employees = $employeeQuery->orderBy('id', 'asc')->get();

        // Get global policy defaults or fallback
        $policy = HrWorkPolicy::first();
        $defaultAnnualDays = (float) ($policy->annual_leave_days ?? 30);
        $defaultCasualDays = (float) ($policy->casual_leave_annual_days ?? 7);
        $defaultSickDays   = (float) ($policy->sick_leave_annual_days ?? 15);

        // Fetch all leave requests grouped by employee
        $allLeaves = LeaveRequest::all();

        return $employees->map(function ($employee) use ($allLeaves, $defaultAnnualDays, $defaultCasualDays, $defaultSickDays) {
            $empLeaves = $allLeaves->where('employee_id', $employee->id);

            // Annual Leaves
            $annualEntitled = $defaultAnnualDays;
            $annualApproved = (float) $empLeaves->filter(fn($l) => 
                $l->leave_type?->value === LeaveType::ANNUAL->value && 
                $l->status?->value === LeaveRequestStatus::APPROVED->value
            )->sum('days_count');

            $annualPending = (float) $empLeaves->filter(fn($l) => 
                $l->leave_type?->value === LeaveType::ANNUAL->value && 
                in_array($l->status?->value, [LeaveRequestStatus::PENDING->value, LeaveRequestStatus::MANAGER_APPROVED->value])
            )->sum('days_count');

            $annualRemaining = max(0, $annualEntitled - $annualApproved - $annualPending);

            // Casual Leaves
            $casualEntitled = $defaultCasualDays;
            $casualApproved = (float) $empLeaves->filter(fn($l) => 
                $l->leave_type?->value === LeaveType::CASUAL->value && 
                $l->status?->value === LeaveRequestStatus::APPROVED->value
            )->sum('days_count');
            $casualRemaining = max(0, $casualEntitled - $casualApproved);

            // Sick Leaves
            $sickEntitled = $defaultSickDays;
            $sickApproved = (float) $empLeaves->filter(fn($l) => 
                $l->leave_type?->value === LeaveType::SICK->value && 
                $l->status?->value === LeaveRequestStatus::APPROVED->value
            )->sum('days_count');
            $sickRemaining = max(0, $sickEntitled - $sickApproved);

            // Total across all types
            $totalEntitled = $annualEntitled + $casualEntitled + $sickEntitled;
            $totalConsumed = (float) $empLeaves->filter(fn($l) => $l->status?->value === LeaveRequestStatus::APPROVED->value)->sum('days_count');
            $totalPending  = (float) $empLeaves->filter(fn($l) => in_array($l->status?->value, [LeaveRequestStatus::PENDING->value, LeaveRequestStatus::MANAGER_APPROVED->value]))->sum('days_count');
            $totalRemaining = max(0, $totalEntitled - $totalConsumed - $totalPending);

            $consumptionRate = $totalEntitled > 0 ? round(($totalConsumed / $totalEntitled) * 100, 1) : 0;

            return (object) [
                'employee'          => $employee,
                'employee_id'       => $employee->id,
                'hire_date'         => $employee->hire_date ?? $employee->created_at,
                // Annual
                'annual_entitled'   => $annualEntitled,
                'annual_consumed'   => $annualApproved,
                'annual_pending'    => $annualPending,
                'annual_remaining'  => $annualRemaining,
                // Casual
                'casual_entitled'   => $casualEntitled,
                'casual_consumed'   => $casualApproved,
                'casual_remaining'  => $casualRemaining,
                // Sick
                'sick_entitled'     => $sickEntitled,
                'sick_consumed'     => $sickApproved,
                'sick_remaining'    => $sickRemaining,
                // Totals
                'total_entitled'    => $totalEntitled,
                'total_consumed'    => $totalConsumed,
                'total_pending'     => $totalPending,
                'total_remaining'   => $totalRemaining,
                'consumption_rate'  => $consumptionRate,
            ];
        });
    }

    /**
     * Get paginated Leave Balances calculation records.
     *
     * @param array<string, mixed> $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getLeaveBalancesPaginated(array $filters = [], int $perPage = 25): LengthAwarePaginator
    {
        $allBalances = $this->getLeaveBalancesRecords($filters);
        $page = Paginator::resolveCurrentPage() ?: 1;
        $sliced = $allBalances->slice(($page - 1) * $perPage, $perPage)->values();

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $sliced,
            $allBalances->count(),
            $perPage,
            $page,
            ['path' => Paginator::resolveCurrentPath(), 'query' => request()->query()]
        );
    }
}
