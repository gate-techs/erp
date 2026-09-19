<?php

namespace Modules\HR\Repositories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Modules\HR\Enums\PermissionRequestStatus;
use Modules\HR\Models\PermissionRequest;
use Modules\HR\Repositories\Contracts\PermissionRequestRepositoryInterface;

class PermissionRequestRepository implements PermissionRequestRepositoryInterface
{
    //======================
    /**
     * Get query builder instance for server-side DataTables with applied filters.
     */
    public function getDataTableQuery(array $filters = []): Builder
    {
        $query = PermissionRequest::with([
            'employee.translations',
            'employee.department.translations',
            'employee.primaryBranch.translations',
            'creator',
            'managerApprover',
            'hrApprover',
        ])
        ->select('hr_permission_requests.*')
        ->latest('hr_permission_requests.id');

        // Filter: Employee
        if (!empty($filters['employee_id'])) {
            $query->where('hr_permission_requests.employee_id', $filters['employee_id']);
        }

        // Filter: Type
        if (!empty($filters['type'])) {
            $query->where('hr_permission_requests.type', $filters['type']);
        }

        // Filter: Status
        if (!empty($filters['status'])) {
            $query->where('hr_permission_requests.status', $filters['status']);
        }

        // Filter: Branch (via Employee Scope)
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
            $query->whereDate('hr_permission_requests.date', '>=', $filters['date_from']);
        }

        // Filter: Date To
        if (!empty($filters['date_to'])) {
            $query->whereDate('hr_permission_requests.date', '<=', $filters['date_to']);
        }

        return $query;
    }

    //======================
    /**
     * Find a permission request by ID with relations.
     */
    public function findById(int $id, array $with = ['employee.translations', 'creator', 'managerApprover', 'hrApprover']): ?PermissionRequest
    {
        return PermissionRequest::with($with)->find($id);
    }

    //======================
    /**
     * Create a new permission request.
     */
    public function create(array $data): PermissionRequest
    {
        return PermissionRequest::create($data);
    }

    //======================
    /**
     * Update an existing permission request.
     */
    public function update(int|PermissionRequest $request, array $data): PermissionRequest
    {
        $model = $request instanceof PermissionRequest ? $request : PermissionRequest::findOrFail($request);
        $model->update($data);
        return $model->fresh(['employee.translations', 'creator', 'managerApprover', 'hrApprover']);
    }

    //======================
    /**
     * Delete a permission request (soft-delete).
     */
    public function delete(int|PermissionRequest $request): bool
    {
        $model = $request instanceof PermissionRequest ? $request : PermissionRequest::findOrFail($request);
        return (bool) $model->delete();
    }

    //======================
    /**
     * Get total consumed permission hours for an employee in a given month.
     */
    public function getEmployeeConsumedHoursInMonth(int $employeeId, int $year, int $month): float
    {
        return (float) PermissionRequest::where('employee_id', $employeeId)
            ->where('status', PermissionRequestStatus::APPROVED->value)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->sum('hours_count');
    }

    //======================
    /**
     * Get total permission requests count for an employee in a given month.
     */
    public function getEmployeePermissionCountInMonth(int $employeeId, int $year, int $month): int
    {
        return PermissionRequest::where('employee_id', $employeeId)
            ->whereNotIn('status', [PermissionRequestStatus::REJECTED->value, PermissionRequestStatus::CANCELLED->value])
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->count();
    }

    //======================
    /**
     * Get dashboard / index stat KPI metrics.
     */
    public function getKPIs(): array
    {
        $today        = Carbon::today()->toDateString();
        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
        $endOfMonth   = Carbon::now()->endOfMonth()->toDateString();

        $totalMonth = PermissionRequest::whereBetween('date', [$startOfMonth, $endOfMonth])->count();
        $pending    = PermissionRequest::pending()->count();
        $hoursMonth = (float) PermissionRequest::approved()->whereBetween('date', [$startOfMonth, $endOfMonth])->sum('hours_count');
        $approvedMonth = PermissionRequest::approved()->whereBetween('date', [$startOfMonth, $endOfMonth])->count();

        return [
            'total_month'    => $totalMonth,
            'pending'        => $pending,
            'hours_month'    => $hoursMonth,
            'approved_month' => $approvedMonth,
        ];
    }

    //======================
    /**
     * Check if an employee already has an overlapping permission on the same date.
     */
    public function hasOverlappingPermission(int $employeeId, string $date, string $fromTime, string $toTime, ?int $excludeId = null): bool
    {
        $query = PermissionRequest::where('employee_id', $employeeId)
            ->whereDate('date', $date)
            ->whereNotIn('status', [PermissionRequestStatus::REJECTED->value, PermissionRequestStatus::CANCELLED->value])
            ->where(function ($q) use ($fromTime, $toTime) {
                $q->where(function ($sub) use ($fromTime, $toTime) {
                    $sub->where('from_time', '<', $toTime)
                        ->where('to_time', '>', $fromTime);
                });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}
