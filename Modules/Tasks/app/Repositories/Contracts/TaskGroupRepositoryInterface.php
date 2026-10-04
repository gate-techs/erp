<?php

namespace Modules\Tasks\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Tasks\Models\TaskGroup;

interface TaskGroupRepositoryInterface
{
    //-----------------------------------------
    public function getAll(array $filters = []): Collection;

    //-----------------------------------------
    public function getDataTableQuery(array $filters = []): Builder;

    //-----------------------------------------
    public function findById(int $id): ?TaskGroup;

    //-----------------------------------------
    public function create(array $data): TaskGroup;

    //-----------------------------------------
    public function update(int|TaskGroup $taskGroup, array $data): TaskGroup;

    //-----------------------------------------
    public function delete(int|TaskGroup $taskGroup): bool;

    //-----------------------------------------
    public function toggleStatus(int|TaskGroup $taskGroup): bool;
}
