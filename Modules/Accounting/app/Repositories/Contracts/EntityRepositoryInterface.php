<?php

namespace Modules\Accounting\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Accounting\Models\Entity;

interface EntityRepositoryInterface
{
    //--------------------------------------------------
    public function getAll(array $filters = [], array $with = ['translations', 'nursery', 'creator']): Collection;

    //--------------------------------------------------
    public function getPaginated(array $filters = [], int $perPage = 15, array $with = ['translations', 'nursery', 'creator']): LengthAwarePaginator;

    //--------------------------------------------------
    public function getDataTableQuery(array $filters = []): Builder;

    //--------------------------------------------------
    public function getAvailableNurseries(): Collection;

    //--------------------------------------------------
    public function findById(int $id, array $with = ['translations', 'nursery', 'creator']): ?Entity;

    //--------------------------------------------------
    public function create(array $data): Entity;

    //--------------------------------------------------
    public function update(int|Entity $model, array $data): Entity;

    //--------------------------------------------------
    public function delete(int|Entity $model): bool;
}
