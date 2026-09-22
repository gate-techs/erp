<?php

namespace Modules\Accounting\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Accounting\Models\Account;

interface AccountRepositoryInterface
{
    //--------------------------------------------------
    public function getAll(array $filters = [], array $with = ['translations', 'parent', 'children']): Collection;

    //--------------------------------------------------
    public function getPaginated(array $filters = [], int $perPage = 15, array $with = ['translations', 'parent']): LengthAwarePaginator;

    //--------------------------------------------------
    public function getDataTableQuery(array $filters = []): Builder;

    //--------------------------------------------------
    public function getTree(int $entityId, array $with = ['translations', 'children.translations']): Collection;

    //--------------------------------------------------
    public function findById(int $id, array $with = ['translations', 'parent', 'children']): ?Account;

    //--------------------------------------------------
    public function create(array $data): Account;

    //--------------------------------------------------
    public function update(int|Account $model, array $data): Account;

    //--------------------------------------------------
    public function delete(int|Account $model): bool;
}
