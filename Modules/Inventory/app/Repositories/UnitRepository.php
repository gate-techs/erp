<?php

namespace Modules\Inventory\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Inventory\Models\Unit;
use Modules\Inventory\Repositories\Contracts\UnitRepositoryInterface;

class UnitRepository implements UnitRepositoryInterface
{
    //------------------------------------------
    public function getAll(array $filters = []): Collection
    {
        return $this->buildQuery($filters)->get();
    }

    //------------------------------------------
    public function getDataTableQuery(array $filters = []): Builder
    {
        return $this->buildQuery($filters);
    }

    //------------------------------------------
    public function findById(int $id): ?Unit
    {
        return Unit::with(['translations', 'creator'])->find($id);
    }

    //------------------------------------------
    public function create(array $data): Unit
    {
        $unit = Unit::create($data);
        return $unit->fresh(['translations', 'creator']);
    }

    //------------------------------------------
    public function update(int|Unit $unit, array $data): Unit
    {
        $unit = $unit instanceof Unit ? $unit : Unit::findOrFail($unit);
        $unit->update($data);
        return $unit->fresh(['translations', 'creator']);
    }

    //------------------------------------------
    public function delete(int|Unit $unit): bool
    {
        $unit = $unit instanceof Unit ? $unit : Unit::findOrFail($unit);
        return (bool) $unit->delete();
    }

    //------------------------------------------
    public function toggleStatus(int|Unit $unit): Unit
    {
        $unit = $unit instanceof Unit ? $unit : Unit::findOrFail($unit);
        $unit->update(['status' => $unit->status === 'active' ? 'inactive' : 'active']);
        return $unit->fresh(['translations', 'creator']);
    }

    //------------------------------------------
    public function symbolExists(string $symbol, ?int $excludeId = null): bool
    {
        return Unit::withTrashed()
            ->where('symbol', $symbol)
            ->when($excludeId, fn (Builder $q) => $q->where('id', '!=', $excludeId))
            ->exists();
    }

    //------------------------------------------
    protected function buildQuery(array $filters = []): Builder
    {
        return Unit::with(['translations', 'creator'])
            ->when(
                array_key_exists('status', $filters) && $filters['status'] !== '',
                fn (Builder $q) => $q->where('status', $filters['status'])
            )
            ->latest('id');
    }
}
