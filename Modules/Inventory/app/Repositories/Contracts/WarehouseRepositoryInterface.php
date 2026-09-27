<?php

namespace Modules\Inventory\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Inventory\Models\Warehouse;

interface WarehouseRepositoryInterface
{
    //------------------------------------------
    public function getAll(array $filters = []): Collection;

    //------------------------------------------
    public function getDataTableQuery(array $filters = []): Builder;

    //------------------------------------------
    public function findById(int $id): ?Warehouse;

    //------------------------------------------
    public function create(array $data): Warehouse;

    //------------------------------------------
    public function update(int|Warehouse $warehouse, array $data): Warehouse;

    //------------------------------------------
    public function delete(int|Warehouse $warehouse): bool;

    //------------------------------------------
    public function toggleStatus(int|Warehouse $warehouse): Warehouse;

    //------------------------------------------
    public function codeExists(string $code, ?int $excludeId = null): bool;

    //------------------------------------------
    public function branchHasWarehouse(int $entityId, int $branchId, ?int $excludeId = null): bool;
}
