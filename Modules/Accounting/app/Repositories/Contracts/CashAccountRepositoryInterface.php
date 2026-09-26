<?php

namespace Modules\Accounting\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Modules\Accounting\Models\CashAccount;

interface CashAccountRepositoryInterface
{
    //--------------------------------------------------
    public function all(array $filters = []): Collection;

    //--------------------------------------------------
    public function find(int $id, array $with = []): ?CashAccount;

    //--------------------------------------------------
    public function create(array $data): CashAccount;

    //--------------------------------------------------
    public function update(int $id, array $data): CashAccount;

    //--------------------------------------------------
    public function delete(int $id): bool;

    //--------------------------------------------------
    public function getDataTableQuery(array $filters = []): Builder;

    //--------------------------------------------------
    public function toggleStatus(int $id): CashAccount;

    //--------------------------------------------------
    public function codeExistsInEntity(string $code, int $entityId, ?int $excludeId = null): bool;
}
