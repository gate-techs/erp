<?php

namespace Modules\Nursery\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Nursery\Models\NurseryBranch;

interface NurseryBranchRepositoryInterface
{
    //======================
    /**
     * Get all branches with optional filters and eager loading.
     *
     * @param array $filters
     * @param array $with
     * @return Collection<int, NurseryBranch>
     */
    public function getAll(array $filters = [], array $with = ['translations', 'nursery', 'government', 'city', 'employee', 'workDays']): Collection;

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
     * Find a branch by ID.
     *
     * @param int $id
     * @param array $with
     * @return NurseryBranch|null
     */
    public function findById(int $id, array $with = ['translations', 'nursery', 'government', 'city', 'employee', 'workDays']): ?NurseryBranch;

    //======================
    /**
     * Create a new nursery branch.
     *
     * @param array $data
     * @return NurseryBranch
     */
    public function create(array $data): NurseryBranch;

    //======================
    /**
     * Update an existing nursery branch.
     *
     * @param int|NurseryBranch $model
     * @param array $data
     * @return NurseryBranch
     */
    public function update(int|NurseryBranch $model, array $data): NurseryBranch;

    //======================
    /**
     * Delete a nursery branch.
     *
     * @param int|NurseryBranch $model
     * @return bool
     */
    public function delete(int|NurseryBranch $model): bool;

    //======================
    /**
     * Sync weekly work days for a branch.
     *
     * @param NurseryBranch $branch
     * @param array $workDays
     * @return void
     */
    public function syncWorkDays(NurseryBranch $branch, array $workDays): void;

    //======================
    /**
     * Get nurseries list for dropdown selection.
     *
     * @return Collection
     */
    public function getNurseriesList(): Collection;

    //======================
    /**
     * Get governments list for dropdown selection.
     *
     * @return Collection
     */
    public function getGovernmentsList(): Collection;

    //======================
    /**
     * Get cities list (optionally filtered by government).
     *
     * @param int|null $governmentId
     * @return Collection
     */
    public function getCitiesList(?int $governmentId = null): Collection;

    //======================
    /**
     * Get employees list for dropdown selection.
     *
     * @return Collection
     */
    public function getEmployeesList(): Collection;
}
