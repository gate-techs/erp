<?php

namespace Modules\HR\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Modules\HR\Models\PermissionRequest;

interface PermissionRequestRepositoryInterface
{
    /**
     * Get query builder instance for server-side DataTables with filters.
     */
    public function getDataTableQuery(array $filters = []): Builder;

    /**
     * Find a permission request by ID with relations.
     */
    public function findById(int $id, array $with = []): ?PermissionRequest;

    /**
     * Create a new permission request.
     */
    public function create(array $data): PermissionRequest;

    /**
     * Update an existing permission request.
     */
    public function update(int|PermissionRequest $request, array $data): PermissionRequest;

    /**
     * Delete a permission request (soft-delete).
     */
    public function delete(int|PermissionRequest $request): bool;

    /**
     * Get total consumed permission hours for an employee in a given month.
     */
    public function getEmployeeConsumedHoursInMonth(int $employeeId, int $year, int $month): float;

    /**
     * Get total permission requests count for an employee in a given month.
     */
    public function getEmployeePermissionCountInMonth(int $employeeId, int $year, int $month): int;

    /**
     * Get dashboard / index stat KPI metrics.
     */
    public function getKPIs(): array;

    /**
     * Check if an employee already has an overlapping permission on the same date.
     */
    public function hasOverlappingPermission(int $employeeId, string $date, string $fromTime, string $toTime, ?int $excludeId = null): bool;
}
