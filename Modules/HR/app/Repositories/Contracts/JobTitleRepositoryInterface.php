<?php

namespace Modules\HR\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\HR\Models\JobTitle;

interface JobTitleRepositoryInterface
{
    //======================
    /**
     * Get all job titles with optional filters.
     *
     * @param array $filters
     * @param array $with
     * @return Collection<int, JobTitle>
     */
    public function getAll(array $filters = [], array $with = ['translations', 'department.translations']): Collection;

    //======================
    /**
     * Get query builder instance for server-side DataTables.
     *
     * @param array $filters
     * @return Builder
     */
    public function getDataTableQuery(array $filters = []): Builder;

    //======================
    /**
     * Find a job title by ID.
     *
     * @param int $id
     * @param array $with
     * @return JobTitle|null
     */
    public function findById(int $id, array $with = ['translations', 'department.translations']): ?JobTitle;

    //======================
    /**
     * Create a new job title.
     *
     * @param array $data
     * @return JobTitle
     */
    public function create(array $data): JobTitle;

    //======================
    /**
     * Update an existing job title.
     *
     * @param int|JobTitle $model
     * @param array $data
     * @return JobTitle
     */
    public function update(int|JobTitle $model, array $data): JobTitle;

    //======================
    /**
     * Delete a job title.
     *
     * @param int|JobTitle $model
     * @return bool
     */
    public function delete(int|JobTitle $model): bool;

    //======================
    /**
     * Get departments list for dropdown selection.
     *
     * @return Collection
     */
    public function getDepartmentsList(): Collection;
}
