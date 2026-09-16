<?php

namespace Modules\HR\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\HR\Models\Department;

interface DepartmentRepositoryInterface
{
    //======================
    /**
     * Get all departments with optional filters.
     *
     * @param array $filters
     * @param array $with
     * @return Collection<int, Department>
     */
    public function getAll(array $filters = [], array $with = ['translations', 'parent.translations', 'nursery.translations', 'branch.translations', 'manager']): Collection;

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
     * Find a department by ID.
     *
     * @param int $id
     * @param array $with
     * @return Department|null
     */
    public function findById(int $id, array $with = ['translations', 'parent.translations', 'nursery.translations', 'branch.translations', 'manager']): ?Department;

    //======================
    /**
     * Create a new department.
     *
     * @param array $data
     * @return Department
     */
    public function create(array $data): Department;

    //======================
    /**
     * Update an existing department.
     *
     * @param int|Department $model
     * @param array $data
     * @return Department
     */
    public function update(int|Department $model, array $data): Department;

    //======================
    /**
     * Delete a department.
     *
     * @param int|Department $model
     * @return bool
     */
    public function delete(int|Department $model): bool;

    //======================
    /**
     * Get main parent departments (الإدارات الرئيسية) for parent selection dropdown.
     *
     * @param int|null $excludeId
     * @return Collection
     */
    public function getMainDepartmentsList(?int $excludeId = null): Collection;

    //======================
    /**
     * Get nurseries list for dropdown selection.
     *
     * @return Collection
     */
    public function getNurseriesList(): Collection;

    //======================
    /**
     * Get branches list (optionally filtered by nursery).
     *
     * @param int|null $nurseryId
     * @return Collection
     */
    public function getBranchesList(?int $nurseryId = null): Collection;

    //======================
    /**
     * Get managers/users list for dropdown selection.
     *
     * @return Collection
     */
    public function getManagersList(): Collection;
}
