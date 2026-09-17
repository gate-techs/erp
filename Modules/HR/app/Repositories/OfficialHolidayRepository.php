<?php

namespace Modules\HR\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Modules\HR\Models\OfficialHoliday;
use Modules\HR\Repositories\Contracts\OfficialHolidayRepositoryInterface;

class OfficialHolidayRepository implements OfficialHolidayRepositoryInterface
{
    //======================
    /**
     * Get all official holidays with optional filters.
     */
    public function getAll(array $filters = [], array $with = ['translations', 'branch']): Collection
    {
        $query = OfficialHoliday::query()->with($with);

        if (!empty($filters['year'])) {
            $query->where('year', (int) $filters['year']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }

        return $query->orderBy('start_date', 'desc')->get();
    }

    //======================
    /**
     * Get query builder instance for server-side DataTables.
     */
    public function getDataTableQuery(array $filters = []): Builder
    {
        $query = OfficialHoliday::query()
            ->with(['translations', 'branch.translations']);

        if (!empty($filters['year'])) {
            $query->where('year', (int) $filters['year']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }

        return $query;
    }

    //======================
    /**
     * Find an official holiday by ID.
     */
    public function findById(int $id, array $with = ['translations', 'branch']): ?OfficialHoliday
    {
        return OfficialHoliday::query()->with($with)->find($id);
    }

    //======================
    /**
     * Create a new official holiday.
     */
    public function create(array $data): OfficialHoliday
    {
        return DB::transaction(function () use ($data) {
            return OfficialHoliday::create($data);
        });
    }

    //======================
    /**
     * Update an existing official holiday.
     */
    public function update(int|OfficialHoliday $model, array $data): OfficialHoliday
    {
        return DB::transaction(function () use ($model, $data) {
            $holiday = $model instanceof OfficialHoliday ? $model : $this->findById($model);

            if (!$holiday) {
                throw new \InvalidArgumentException('Official holiday not found for update.');
            }

            $holiday->update($data);

            return $holiday->fresh(['translations', 'branch']);
        });
    }

    //======================
    /**
     * Delete an official holiday.
     */
    public function delete(int|OfficialHoliday $model): bool
    {
        return DB::transaction(function () use ($model) {
            $holiday = $model instanceof OfficialHoliday ? $model : $this->findById($model);

            if (!$holiday) {
                return false;
            }

            return (bool) $holiday->delete();
        });
    }
}
