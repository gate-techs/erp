<?php

namespace Modules\AccessControl\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Modules\AccessControl\Models\Permission;
use Modules\AccessControl\Repositories\Contracts\PermissionRepositoryInterface;

class PermissionRepository implements PermissionRepositoryInterface
{
    public function getAll(array $filters = []): Collection
    {
        return $this->query($filters)->get();
    }

    public function findById(int $id): ?Permission
    {
        return Permission::find($id);
    }

    public function create(array $data): Permission
    {
        return Permission::create($data);
    }

    public function update(int|Permission $permission, array $data): Permission
    {
        $permission = $permission instanceof Permission ? $permission : Permission::findOrFail($permission);
        $permission->update($data);

        return $permission->fresh();
    }

    public function delete(int|Permission $permission): bool
    {
        $permission = $permission instanceof Permission ? $permission : Permission::findOrFail($permission);

        return (bool) $permission->delete();
    }

    private function query(array $filters)
    {
        return Permission::query()
            ->when($filters['guard_name'] ?? null, fn ($query, $guard) => $query->where('guard_name', $guard))
            ->when($filters['search'] ?? null, fn ($query, $search) => $query->where('name', 'like', "%{$search}%"))
            ->orderBy('name');
    }
}
