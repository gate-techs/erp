<?php

namespace Modules\AccessControl\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Modules\AccessControl\Models\Role;
use Modules\AccessControl\Repositories\Contracts\RoleRepositoryInterface;

class RoleRepository implements RoleRepositoryInterface
{
    public function getAll(array $filters = []): Collection
    {
        return $this->query($filters)->with('permissions')->get();
    }

    public function getQuery(array $filters = []): Builder
    {
        return $this->query($filters)->with('permissions');
    }

    public function findById(int $id): ?Role
    {
        return Role::with('permissions')->find($id);
    }

    public function create(array $data): Role
    {
        return Role::create($data);
    }

    public function update(int|Role $role, array $data): Role
    {
        $role = $role instanceof Role ? $role : Role::findOrFail($role);
        $role->update($data);

        return $role->fresh('permissions');
    }

    public function delete(int|Role $role): bool
    {
        $role = $role instanceof Role ? $role : Role::findOrFail($role);

        return (bool) $role->delete();
    }

    public function syncPermissions(int|Role $role, array $permissions): Role
    {
        $role = $role instanceof Role ? $role : Role::findOrFail($role);

        $guardName = $role->guard_name ?? config('auth.defaults.guard', 'web');
        foreach ($permissions as $permName) {
            if (!empty($permName)) {
                \Spatie\Permission\Models\Permission::findOrCreate($permName, $guardName);
            }
        }

        $role->syncPermissions($permissions);

        return $role->fresh('permissions');
    }

    private function query(array $filters)
    {
        return Role::query()
            ->when($filters['guard_name'] ?? null, fn ($query, $guard) => $query->where('guard_name', $guard))
            ->when($filters['search'] ?? null, fn ($query, $search) => $query->where('name', 'like', "%{$search}%"))
            ->orderBy('name');
    }
}
