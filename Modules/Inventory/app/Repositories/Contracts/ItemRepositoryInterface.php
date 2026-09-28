<?php

namespace Modules\Inventory\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Inventory\Models\Item;

interface ItemRepositoryInterface
{
    //------------------------------------------
    public function getAll(array $filters = []): Collection;

    //------------------------------------------
    public function getDataTableQuery(array $filters = []): Builder;

    //------------------------------------------
    public function findById(int $id): ?Item;

    //------------------------------------------
    public function create(array $data): Item;

    //------------------------------------------
    public function update(int|Item $item, array $data): Item;

    //------------------------------------------
    public function delete(int|Item $item): bool;

    //------------------------------------------
    public function toggleStatus(int|Item $item): Item;

    //------------------------------------------
    public function codeExists(string $code, ?int $excludeId = null): bool;

    //------------------------------------------
    public function barcodeExists(string $barcode, ?int $excludeId = null): bool;
}
