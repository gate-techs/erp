<?php

namespace Modules\HR\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\HR\Models\OfficialHoliday;

interface OfficialHolidayRepositoryInterface
{
    //======================
    /**
     * Get all official holidays with optional filters.
     *
     * @param array $filters
     * @param array $with
     * @return Collection<int, OfficialHoliday>
     */
    public function getAll(array $filters = [], array $with = ['translations', 'branch']): Collection;

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
     * Find an official holiday by ID.
     *
     * @param int $id
     * @param array $with
     * @return OfficialHoliday|null
     */
    public function findById(int $id, array $with = ['translations', 'branch']): ?OfficialHoliday;

    //======================
    /**
     * Create a new official holiday.
     *
     * @param array $data
     * @return OfficialHoliday
     */
    public function create(array $data): OfficialHoliday;

    //======================
    /**
     * Update an existing official holiday.
     *
     * @param int|OfficialHoliday $model
     * @param array $data
     * @return OfficialHoliday
     */
    public function update(int|OfficialHoliday $model, array $data): OfficialHoliday;

    //======================
    /**
     * Delete an official holiday.
     *
     * @param int|OfficialHoliday $model
     * @return bool
     */
    public function delete(int|OfficialHoliday $model): bool;
}
