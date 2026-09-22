<?php

namespace Modules\AccessControl\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Modules\AccessControl\Models\Role;

interface RoleRepositoryInterface
{
    public function getAll(array $filters = []): Collection;

    public function getQuery(array $filters = []): Builder;
    public function findById(int $id): ?Role;
    public function create(array $data): Role;
    public function update(int|Role $role, array $data): Role;
    public function delete(int|Role $role): bool;
    public function syncPermissions(int|Role $role, array $permissions): Role;
}
