<?php

namespace Modules\Inventory\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Inventory\Models\ItemCategory;

interface ItemCategoryRepositoryInterface
{
    //------------------------------------------
    public function getAll(array $filters = []): Collection;

    //------------------------------------------
    public function getDataTableQuery(array $filters = []): Builder;

    //------------------------------------------
    public function findById(int $id): ?ItemCategory;

    //------------------------------------------
    public function create(array $data): ItemCategory;

    //------------------------------------------
    public function update(int|ItemCategory $category, array $data): ItemCategory;

    //------------------------------------------
    public function delete(int|ItemCategory $category): bool;

    //------------------------------------------
    public function toggleStatus(int|ItemCategory $category): ItemCategory;

    //------------------------------------------
    public function codeExists(string $code, ?int $excludeId = null): bool;
}
