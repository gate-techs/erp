<?php

namespace Modules\HR\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\HR\Models\WorkSchedule;

interface WorkScheduleRepositoryInterface
{
    //======================
    /**
     * Get all work schedules with optional filters.
     *
     * @param array $filters
     * @param array $with
     * @return Collection<int, WorkSchedule>
     */
    public function getAll(array $filters = [], array $with = ['translations', 'days.shift', 'department', 'branch']): Collection;

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
     * Find a work schedule by ID.
     *
     * @param int $id
     * @param array $with
     * @return WorkSchedule|null
     */
    public function findById(int $id, array $with = ['translations', 'days.shift', 'department', 'branch']): ?WorkSchedule;

    //======================
    /**
     * Create a new work schedule.
     *
     * @param array $data
     * @param array $daysData
     * @return WorkSchedule
     */
    public function create(array $data, array $daysData = []): WorkSchedule;

    //======================
    /**
     * Update an existing work schedule.
     *
     * @param int|WorkSchedule $model
     * @param array $data
     * @param array|null $daysData
     * @return WorkSchedule
     */
    public function update(int|WorkSchedule $model, array $data, ?array $daysData = null): WorkSchedule;

    //======================
    /**
     * Delete a work schedule.
     *
     * @param int|WorkSchedule $model
     * @return bool
     */
    public function delete(int|WorkSchedule $model): bool;
}
