<?php

namespace Modules\Inventory\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Inventory\Models\ItemCategory;
use Modules\Inventory\Repositories\Contracts\ItemCategoryRepositoryInterface;

class ItemCategoryRepository implements ItemCategoryRepositoryInterface
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
    public function findById(int $id): ?ItemCategory
    {
        return ItemCategory::with(['translations', 'parent.translations', 'children.translations', 'creator'])->find($id);
    }

    //------------------------------------------
    public function create(array $data): ItemCategory
    {
        $category = ItemCategory::create($data);

        return $category->fresh(['translations', 'parent.translations', 'children.translations', 'creator']);
    }

    //------------------------------------------
    public function update(int|ItemCategory $category, array $data): ItemCategory
    {
        $category = $category instanceof ItemCategory ? $category : ItemCategory::findOrFail($category);
        $category->update($data);

        return $category->fresh(['translations', 'parent.translations', 'children.translations', 'creator']);
    }

    //------------------------------------------
    public function delete(int|ItemCategory $category): bool
    {
        $category = $category instanceof ItemCategory ? $category : ItemCategory::findOrFail($category);

        return (bool) $category->delete();
    }

    //------------------------------------------
    public function toggleStatus(int|ItemCategory $category): ItemCategory
    {
        $category = $category instanceof ItemCategory ? $category : ItemCategory::findOrFail($category);
        $category->update(['status' => $category->status === 'active' ? 'inactive' : 'active']);

        return $category->fresh(['translations', 'parent.translations', 'children.translations', 'creator']);
    }

    //------------------------------------------
    public function codeExists(string $code, ?int $excludeId = null): bool
    {
        return ItemCategory::query()
            ->where('code', $code)
            ->when($excludeId, fn (Builder $query) => $query->where('id', '!=', $excludeId))
            ->exists();
    }

    //------------------------------------------
    protected function buildQuery(array $filters = []): Builder
    {
        return ItemCategory::with(['translations', 'parent.translations', 'creator'])
            ->when(array_key_exists('status', $filters) && $filters['status'] !== '', fn (Builder $query) => $query->where('status', $filters['status']))
            ->when(($filters['parent_id'] ?? '') === 'root', fn (Builder $query) => $query->whereNull('parent_id'))
            ->when(isset($filters['parent_id']) && $filters['parent_id'] !== '' && $filters['parent_id'] !== 'root', fn (Builder $query) => $query->where('parent_id', $filters['parent_id']))
            ->latest('id');
    }
}
