<?php

namespace Modules\Inventory\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Inventory\Models\Unit;

interface UnitRepositoryInterface
{
    //------------------------------------------
    public function getAll(array $filters = []): Collection;

    //------------------------------------------
    public function getDataTableQuery(array $filters = []): Builder;

    //------------------------------------------
    public function findById(int $id): ?Unit;

    //------------------------------------------
    public function create(array $data): Unit;

    //------------------------------------------
    public function update(int|Unit $unit, array $data): Unit;

    //------------------------------------------
    public function delete(int|Unit $unit): bool;

    //------------------------------------------
    public function toggleStatus(int|Unit $unit): Unit;

    //------------------------------------------
    public function symbolExists(string $symbol, ?int $excludeId = null): bool;
}
