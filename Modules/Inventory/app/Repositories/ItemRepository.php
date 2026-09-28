<?php

namespace Modules\Inventory\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Inventory\Models\Item;
use Modules\Inventory\Repositories\Contracts\ItemRepositoryInterface;

class ItemRepository implements ItemRepositoryInterface
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
    public function findById(int $id): ?Item
    {
        return Item::with(['translations', 'category.translations', 'unit.translations', 'creator'])->find($id);
    }

    //------------------------------------------
    public function create(array $data): Item
    {
        $item = Item::create($data);
        return $item->fresh(['translations', 'category.translations', 'unit.translations', 'creator']);
    }

    //------------------------------------------
    public function update(int|Item $item, array $data): Item
    {
        $item = $item instanceof Item ? $item : Item::findOrFail($item);
        $item->update($data);
        return $item->fresh(['translations', 'category.translations', 'unit.translations', 'creator']);
    }

    //------------------------------------------
    public function delete(int|Item $item): bool
    {
        $item = $item instanceof Item ? $item : Item::findOrFail($item);
        return (bool) $item->delete();
    }

    //------------------------------------------
    public function toggleStatus(int|Item $item): Item
    {
        $item = $item instanceof Item ? $item : Item::findOrFail($item);
        $item->update(['status' => $item->status === 'active' ? 'inactive' : 'active']);
        return $item->fresh(['translations', 'category.translations', 'unit.translations', 'creator']);
    }

    //------------------------------------------
    public function codeExists(string $code, ?int $excludeId = null): bool
    {
        return Item::withTrashed()
            ->where('code', $code)
            ->when($excludeId, fn (Builder $q) => $q->where('id', '!=', $excludeId))
            ->exists();
    }

    //------------------------------------------
    public function barcodeExists(string $barcode, ?int $excludeId = null): bool
    {
        return Item::withTrashed()
            ->where('barcode', $barcode)
            ->when($excludeId, fn (Builder $q) => $q->where('id', '!=', $excludeId))
            ->exists();
    }

    //------------------------------------------
    protected function buildQuery(array $filters = []): Builder
    {
        return Item::with(['translations', 'category.translations', 'unit.translations', 'creator'])
            ->when(
                array_key_exists('status', $filters) && $filters['status'] !== '',
                fn (Builder $q) => $q->where('status', $filters['status'])
            )
            ->when(
                !empty($filters['category_id']),
                fn (Builder $q) => $q->where('category_id', $filters['category_id'])
            )
            ->when(
                !empty($filters['unit_id']),
                fn (Builder $q) => $q->where('unit_id', $filters['unit_id'])
            )
            ->latest('id');
    }
}
