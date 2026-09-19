<?php

namespace Modules\HR\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Modules\HR\Models\LeaveRequest;

interface LeaveRequestRepositoryInterface
{
    /**
     * Get query builder instance for server-side DataTables.
     */
    public function getDataTableQuery(array $filters = []): Builder;

    /**
     * Find a leave request by ID with relations.
     */
    public function findById(int $id, array $with = ['employee.translations', 'replacementEmployee.translations', 'creator', 'hrApprover']): ?LeaveRequest;

    /**
     * Create a new leave request.
     */
    public function create(array $data): LeaveRequest;

    /**
     * Update an existing leave request.
     */
    public function update(int|LeaveRequest $request, array $data): LeaveRequest;

    /**
     * Delete a leave request (soft-delete).
     */
    public function delete(int|LeaveRequest $request): bool;

    /**
     * Calculate consumed days for an employee by leave type in a specific year.
     */
    public function getEmployeeConsumedDays(int $employeeId, string $leaveType, int $year): float;

    /**
     * Get dashboard / index stat KPI metrics.
     */
    public function getKPIs(): array;

    /**
     * Check if an employee has an overlapping approved/pending leave request.
     */
    public function hasOverlappingLeave(int $employeeId, string $startDate, string $endDate, ?int $excludeId = null): bool;
}
