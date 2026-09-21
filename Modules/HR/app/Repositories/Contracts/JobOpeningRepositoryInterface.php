<?php

namespace Modules\HR\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Modules\HR\Models\JobOpening;

interface JobOpeningRepositoryInterface
{
    /**
     * Get query builder instance for server-side DataTables.
     */
    public function getDataTableQuery(array $filters = []): Builder;

    /**
     * Find a job opening by ID with relations.
     */
    public function findById(int $id, array $with = ['department.translations', 'jobTitle.translations', 'branch', 'requisition', 'creator', 'publisher']): ?JobOpening;

    /**
     * Create a new job opening.
     */
    public function create(array $data): JobOpening;

    /**
     * Update an existing job opening.
     */
    public function update(int|JobOpening $opening, array $data): JobOpening;

    /**
     * Delete a job opening (soft-delete).
     */
    public function delete(int|JobOpening $opening): bool;

    /**
     * Generate unique opening code for current year.
     */
    public function generateOpeningCode(): string;

    /**
     * Get dashboard / index stat KPI metrics.
     */
    public function getKPIs(): array;
}
