<?php

namespace Modules\Inventory\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Inventory\Models\Warehouse;
use Modules\Inventory\Repositories\Contracts\WarehouseRepositoryInterface;

class WarehouseRepository implements WarehouseRepositoryInterface
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
    public function findById(int $id): ?Warehouse
    {
        return Warehouse::with(['translations', 'entity.translations', 'branch.translations', 'creator'])->find($id);
    }

    //------------------------------------------
    public function create(array $data): Warehouse
    {
        $warehouse = Warehouse::create($data);

        return $warehouse->fresh(['translations', 'entity.translations', 'branch.translations', 'creator']);
    }

    //------------------------------------------
    public function update(int|Warehouse $warehouse, array $data): Warehouse
    {
        $warehouse = $warehouse instanceof Warehouse
            ? $warehouse
            : Warehouse::findOrFail($warehouse);

        $warehouse->update($data);

        return $warehouse->fresh(['translations', 'entity.translations', 'branch.translations', 'creator']);
    }

    //------------------------------------------
    public function delete(int|Warehouse $warehouse): bool
    {
        $warehouse = $warehouse instanceof Warehouse
            ? $warehouse
            : Warehouse::findOrFail($warehouse);

        return (bool) $warehouse->delete();
    }

    //------------------------------------------
    public function toggleStatus(int|Warehouse $warehouse): Warehouse
    {
        $warehouse = $warehouse instanceof Warehouse
            ? $warehouse
            : Warehouse::findOrFail($warehouse);

        $warehouse->update([
            'status' => $warehouse->status === 'active' ? 'inactive' : 'active',
        ]);

        return $warehouse->fresh(['translations', 'entity.translations', 'branch.translations', 'creator']);
    }

    //------------------------------------------
    public function codeExists(string $code, ?int $excludeId = null): bool
    {
        return Warehouse::query()
            ->where('code', $code)
            ->when($excludeId, fn (Builder $query) => $query->where('id', '!=', $excludeId))
            ->exists();
    }

    //------------------------------------------
    public function branchHasWarehouse(int $entityId, int $branchId, ?int $excludeId = null): bool
    {
        return Warehouse::query()
            ->where('entity_id', $entityId)
            ->where('branch_id', $branchId)
            ->when($excludeId, fn (Builder $query) => $query->where('id', '!=', $excludeId))
            ->exists();
    }

    //------------------------------------------
    protected function buildQuery(array $filters = []): Builder
    {
        $searchTerm = null;
        if (isset($filters['search'])) {
            $searchTerm = is_array($filters['search']) ? ($filters['search']['value'] ?? null) : $filters['search'];
        }

        return Warehouse::with(['translations', 'entity.translations', 'branch.translations', 'creator'])
            ->when($filters['entity_id'] ?? null, fn (Builder $query, $entityId) => $query->where('entity_id', $entityId))
            ->when($filters['branch_id'] ?? null, fn (Builder $query, $branchId) => $query->where('branch_id', $branchId))
            ->when($filters['type'] ?? null, fn (Builder $query, $type) => $query->where('type', $type))
            ->when(array_key_exists('status', $filters) && $filters['status'] !== '' && $filters['status'] !== null, fn (Builder $query) => $query->where('status', $filters['status']))
            ->when(!empty($searchTerm), function (Builder $query) use ($searchTerm) {
                $query->where(function (Builder $nestedQuery) use ($searchTerm) {
                    $nestedQuery
                        ->where('warehouses.code', 'like', "%{$searchTerm}%")
                        ->orWhereTranslationLike('name', "%{$searchTerm}%")
                        ->orWhereTranslationLike('description', "%{$searchTerm}%");
                });
            })
            ->latest('warehouses.id');
    }
}
