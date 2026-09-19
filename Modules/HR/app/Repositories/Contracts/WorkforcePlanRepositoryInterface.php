<?php

namespace Modules\HR\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Modules\HR\Models\WorkforcePlan;

interface WorkforcePlanRepositoryInterface
{
    /**
     * Get query builder instance for server-side DataTables.
     */
    public function getDataTableQuery(array $filters = []): Builder;

    /**
     * Find a workforce plan by ID with relations.
     */
    public function findById(int $id, array $with = ['items.department.translations', 'items.jobTitle.translations', 'items.branch', 'creator', 'approver']): ?WorkforcePlan;

    /**
     * Create a new workforce plan.
     */
    public function create(array $data): WorkforcePlan;

    /**
     * Update an existing workforce plan.
     */
    public function update(int|WorkforcePlan $plan, array $data): WorkforcePlan;

    /**
     * Delete a workforce plan (soft-delete).
     */
    public function delete(int|WorkforcePlan $plan): bool;

    /**
     * Generate unique plan code for a given year.
     */
    public function generatePlanCode(int $year): string;

    /**
     * Get dashboard / index stat KPI metrics.
     */
    public function getKPIs(): array;

    /**
     * Count live employees for a given department, branch, and job title.
     */
    public function countCurrentEmployees(int $departmentId, int $jobTitleId, ?int $branchId = null): int;
}
