<?php

namespace Modules\Accounting\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Modules\Accounting\Models\CostCenter;

interface CostCenterRepositoryInterface
{
    //===============================
    public function all(array $filters = []): Collection;

    //===============================
    public function find(int $id, array $with = []): ?CostCenter;

    //===============================
    public function create(array $data): CostCenter;

    //===============================
    public function update(int $id, array $data): CostCenter;

    //===============================
    public function delete(int $id): bool;

    //===============================
    public function getDataTableQuery(array $filters = []): Builder;

    //===============================
    public function getParentsForEntity(int $entityId, ?int $excludeId = null): Collection;

    //===============================
    public function getBranchesForEntity(int $entityId): Collection;

    //===============================
    public function toggleStatus(int $id): CostCenter;

    //===============================
    public function codeExistsInEntity(string $code, int $entityId, ?int $excludeId = null): bool;
}
