<?php

namespace Modules\Inventory\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;
use Modules\Inventory\Models\ItemCategory;
use Modules\Inventory\Repositories\Contracts\ItemCategoryRepositoryInterface;

class ItemCategoryService
{
    public function __construct(
        protected ItemCategoryRepositoryInterface $repository
    ) {}

    //------------------------------------------
    public function getAll(array $filters = []): Collection
    {
        return $this->repository->getAll($filters);
    }

    //------------------------------------------
    public function getDataTableQuery(array $filters = []): Builder
    {
        return $this->repository->getDataTableQuery($filters);
    }

    //------------------------------------------
    public function getById(int $id): ItemCategory
    {
        $category = $this->repository->findById($id);

        if (!$category) {
            abort(404, __('Item category not found.'));
        }

        return $category;
    }

    //------------------------------------------
    public function create(array $data, ?int $userId = null): ItemCategory
    {
        $this->validateCategoryData($data);
        $this->validateUniqueCode($data['code']);

        $data['created_by'] = $data['created_by'] ?? $userId ?? auth()->id();

        return $this->repository->create($this->formatTranslationData($data));
    }

    //------------------------------------------
    public function update(int $id, array $data): ItemCategory
    {
        $category = $this->getById($id);
        $this->validateCategoryData($data, $category);

        if (isset($data['code'])) {
            $this->validateUniqueCode($data['code'], $id);
        }

        return $this->repository->update($category, $this->formatTranslationData($data));
    }

    //------------------------------------------
    public function delete(int $id): bool
    {
        $category = $this->getById($id);

        if ($category->children()->exists()) {
            throw ValidationException::withMessages([
                'category' => __('A category with subcategories cannot be deleted.'),
            ]);
        }

        return $this->repository->delete($category);
    }

    //------------------------------------------
    public function toggleStatus(int $id): ItemCategory
    {
        return $this->repository->toggleStatus($this->getById($id));
    }

    //------------------------------------------
    protected function validateCategoryData(array $data, ?ItemCategory $category = null): void
    {
        $parentId = array_key_exists('parent_id', $data) ? $data['parent_id'] : $category?->parent_id;

        if (!$parentId) {
            return;
        }

        if ($category && (int) $parentId === (int) $category->id) {
            throw ValidationException::withMessages([
                'parent_id' => __('A category cannot be its own parent.'),
            ]);
        }

        $parent = ItemCategory::find($parentId);
        if (!$parent) {
            throw ValidationException::withMessages([
                'parent_id' => __('The selected parent category does not exist.'),
            ]);
        }

        $visited = [];
        while ($parent) {
            if (in_array($parent->id, $visited, true)) {
                break;
            }

            $visited[] = $parent->id;

            if ($category && (int) $parent->id === (int) $category->id) {
                throw ValidationException::withMessages([
                    'parent_id' => __('A category cannot be moved below one of its descendants.'),
                ]);
            }

            $parent = $parent->parent_id ? ItemCategory::find($parent->parent_id) : null;
        }
    }

    //------------------------------------------
    protected function validateUniqueCode(string $code, ?int $excludeId = null): void
    {
        if ($this->repository->codeExists($code, $excludeId)) {
            throw ValidationException::withMessages([
                'code' => __('The category code has already been taken.'),
            ]);
        }
    }

    //------------------------------------------
    protected function formatTranslationData(array $data): array
    {
        $result = $data;

        if (array_key_exists('name_ar', $data) || array_key_exists('description_ar', $data)) {
            $result['ar'] = [
                'name' => $data['name_ar'] ?? ($result['ar']['name'] ?? ''),
                'description' => $data['description_ar'] ?? ($result['ar']['description'] ?? null),
            ];
            unset($result['name_ar'], $result['description_ar']);
        }

        if (array_key_exists('name_en', $data) || array_key_exists('description_en', $data)) {
            $result['en'] = [
                'name' => $data['name_en'] ?? ($result['en']['name'] ?? ''),
                'description' => $data['description_en'] ?? ($result['en']['description'] ?? null),
            ];
            unset($result['name_en'], $result['description_en']);
        }

        return $result;
    }
}
