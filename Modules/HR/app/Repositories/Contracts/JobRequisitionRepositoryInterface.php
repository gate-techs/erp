<?php

namespace Modules\HR\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Modules\HR\Models\JobRequisition;

interface JobRequisitionRepositoryInterface
{
    /**
     * Get query builder instance for server-side DataTables.
     */
    public function getDataTableQuery(array $filters = []): Builder;

    /**
     * Find a job requisition by ID with relations.
     */
    public function findById(int $id, array $with = ['department.translations', 'jobTitle.translations', 'branch', 'requester', 'replacementEmployee.translations', 'approver', 'planItem.plan']): ?JobRequisition;

    /**
     * Create a new job requisition.
     */
    public function create(array $data): JobRequisition;

    /**
     * Update an existing job requisition.
     */
    public function update(int|JobRequisition $requisition, array $data): JobRequisition;

    /**
     * Delete a job requisition (soft-delete).
     */
    public function delete(int|JobRequisition $requisition): bool;

    /**
     * Generate unique requisition number for current year.
     */
    public function generateRequisitionNumber(): string;

    /**
     * Get dashboard / index stat KPI metrics.
     */
    public function getKPIs(): array;
}
