<?php

namespace Modules\AccessControl\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Modules\AccessControl\Models\Permission;

interface PermissionRepositoryInterface
{
    public function getAll(array $filters = []): Collection;
    public function findById(int $id): ?Permission;
    public function create(array $data): Permission;
    public function update(int|Permission $permission, array $data): Permission;
    public function delete(int|Permission $permission): bool;
}
