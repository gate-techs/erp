<?php

namespace Modules\HR\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\HR\Models\Shift;

interface ShiftRepositoryInterface
{
    //======================
    /**
     * Get all shifts with optional filters.
     *
     * @param array $filters
     * @param array $with
     * @return Collection<int, Shift>
     */
    public function getAll(array $filters = [], array $with = ['translations']): Collection;

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
     * Find a shift by ID.
     *
     * @param int $id
     * @param array $with
     * @return Shift|null
     */
    public function findById(int $id, array $with = ['translations']): ?Shift;

    //======================
    /**
     * Create a new shift.
     *
     * @param array $data
     * @return Shift
     */
    public function create(array $data): Shift;

    //======================
    /**
     * Update an existing shift.
     *
     * @param int|Shift $model
     * @param array $data
     * @return Shift
     */
    public function update(int|Shift $model, array $data): Shift;

    //======================
    /**
     * Delete a shift.
     *
     * @param int|Shift $model
     * @return bool
     */
    public function delete(int|Shift $model): bool;
}
