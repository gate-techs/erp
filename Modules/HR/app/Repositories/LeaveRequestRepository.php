<?php

namespace Modules\HR\Repositories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Modules\HR\Enums\LeaveRequestStatus;
use Modules\HR\Models\LeaveRequest;
use Modules\HR\Repositories\Contracts\LeaveRequestRepositoryInterface;

class LeaveRequestRepository implements LeaveRequestRepositoryInterface
{
    //======================
    /**
     * Get query builder instance for server-side DataTables.
     */
    public function getDataTableQuery(array $filters = []): Builder
    {
        $query = LeaveRequest::with([
            'employee.translations',
            'employee.department.translations',
            'employee.primaryBranch.translations',
            'replacementEmployee.translations',
            'creator',
            'hrApprover',
            'managerApprover',
        ])
        ->select('hr_leave_requests.*')
        ->latest('hr_leave_requests.id');

        // Filter: Employee
        if (!empty($filters['employee_id'])) {
            $query->where('hr_leave_requests.employee_id', $filters['employee_id']);
        }

        // Filter: Leave Type
        if (!empty($filters['leave_type'])) {
            $query->where('hr_leave_requests.leave_type', $filters['leave_type']);
        }

        // Filter: Status
        if (!empty($filters['status'])) {
            $query->where('hr_leave_requests.status', $filters['status']);
        }

        // Filter: Branch
        if (!empty($filters['branch_id'])) {
            $query->whereHas('employee', function (Builder $q) use ($filters) {
                $q->byBranch((int) $filters['branch_id']);
            });
        }

        // Filter: Department
        if (!empty($filters['department_id'])) {
            $query->whereHas('employee', function (Builder $q) use ($filters) {
                $q->where('department_id', $filters['department_id']);
            });
        }

        // Filter: Date From
        if (!empty($filters['date_from'])) {
            $query->whereDate('hr_leave_requests.start_date', '>=', $filters['date_from']);
        }

        // Filter: Date To
        if (!empty($filters['date_to'])) {
            $query->whereDate('hr_leave_requests.end_date', '<=', $filters['date_to']);
        }

        return $query;
    }

    //======================
    /**
     * Find a leave request by ID with relations.
     */
    public function findById(int $id, array $with = ['employee.translations', 'replacementEmployee.translations', 'creator', 'hrApprover']): ?LeaveRequest
    {
        return LeaveRequest::with($with)->find($id);
    }

    //======================
    /**
     * Create a new leave request.
     */
    public function create(array $data): LeaveRequest
    {
        return LeaveRequest::create($data);
    }

    //======================
    /**
     * Update an existing leave request.
     */
    public function update(int|LeaveRequest $request, array $data): LeaveRequest
    {
        $model = $request instanceof LeaveRequest ? $request : LeaveRequest::findOrFail($request);
        $model->update($data);
        return $model->fresh(['employee.translations', 'replacementEmployee.translations']);
    }

    //======================
    /**
     * Delete a leave request (soft-delete).
     */
    public function delete(int|LeaveRequest $request): bool
    {
        $model = $request instanceof LeaveRequest ? $request : LeaveRequest::findOrFail($request);
        return (bool) $model->delete();
    }

    //======================
    /**
     * Calculate consumed days for an employee by leave type in a specific year.
     */
    public function getEmployeeConsumedDays(int $employeeId, string $leaveType, int $year): float
    {
        return (float) LeaveRequest::where('employee_id', $employeeId)
            ->where('leave_type', $leaveType)
            ->where('status', LeaveRequestStatus::APPROVED->value)
            ->whereYear('start_date', $year)
            ->sum('days_count');
    }

    //======================
    /**
     * Get dashboard / index stat KPI metrics.
     */
    public function getKPIs(): array
    {
        $today     = Carbon::today()->toDateString();
        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
        $endOfMonth   = Carbon::now()->endOfMonth()->toDateString();

        $totalMonth = LeaveRequest::whereBetween('start_date', [$startOfMonth, $endOfMonth])->count();
        $pending    = LeaveRequest::pending()->count();
        $onLeaveToday = LeaveRequest::activeOn($today)->count();
        $approvedMonth = LeaveRequest::approved()->whereBetween('start_date', [$startOfMonth, $endOfMonth])->count();

        return [
            'total_month'    => $totalMonth,
            'pending'        => $pending,
            'on_leave_today' => $onLeaveToday,
            'approved_month' => $approvedMonth,
        ];
    }

    //======================
    /**
     * Check if an employee has an overlapping approved/pending leave request.
     */
    public function hasOverlappingLeave(int $employeeId, string $startDate, string $endDate, ?int $excludeId = null): bool
    {
        $query = LeaveRequest::where('employee_id', $employeeId)
            ->whereNotIn('status', [LeaveRequestStatus::REJECTED->value, LeaveRequestStatus::CANCELLED->value])
            ->whereDate('start_date', '<=', $endDate)
            ->whereDate('end_date', '>=', $startDate);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}
