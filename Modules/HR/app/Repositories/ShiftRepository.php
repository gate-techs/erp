<?php

namespace Modules\HR\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Modules\HR\Models\Shift;
use Modules\HR\Repositories\Contracts\ShiftRepositoryInterface;

class ShiftRepository implements ShiftRepositoryInterface
{
    //======================
    /**
     * Get all shifts with optional filters.
     */
    public function getAll(array $filters = [], array $with = ['translations']): Collection
    {
        return $this->buildQuery($filters, $with)->get();
    }

    //======================
    /**
     * Get query builder instance for server-side DataTables.
     */
    public function getDataTableQuery(array $filters = []): Builder
    {
        $query = Shift::with(['translations'])
            ->select('shifts.*')
            ->latest('shifts.id');

        if (!empty($filters['status'])) {
            $query->where('shifts.status', $filters['status']);
        }

        if (isset($filters['is_overnight']) && $filters['is_overnight'] !== '') {
            $query->where('shifts.is_overnight', (bool) $filters['is_overnight']);
        }

        return $query;
    }

    //======================
    /**
     * Find a shift by ID.
     */
    public function findById(int $id, array $with = ['translations']): ?Shift
    {
        return Shift::with($with)->find($id);
    }

    //======================
    /**
     * Create a new shift with translations.
     */
    public function create(array $data): Shift
    {
        return DB::transaction(function () use ($data) {
            return Shift::create($data);
        });
    }

    //======================
    /**
     * Update an existing shift.
     */
    public function update(int|Shift $model, array $data): Shift
    {
        $shift = $model instanceof Shift ? $model : Shift::findOrFail($model);

        return DB::transaction(function () use ($shift, $data) {
            $shift->update($data);
            return $shift->fresh(['translations']);
        });
    }

    //======================
    /**
     * Delete a shift.
     */
    public function delete(int|Shift $model): bool
    {
        $shift = $model instanceof Shift ? $model : Shift::findOrFail($model);

        return DB::transaction(function () use ($shift) {
            return (bool) $shift->delete();
        });
    }

    //======================
    /**
     * Build base query.
     */
    protected function buildQuery(array $filters = [], array $with = []): Builder
    {
        $query = Shift::query();

        if (!empty($with)) {
            $query->with($with);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['is_overnight']) && $filters['is_overnight'] !== '') {
            $query->where('is_overnight', (bool) $filters['is_overnight']);
        }

        return $query->latest('id');
    }
}
