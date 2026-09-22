<?php

namespace Modules\AccessControl\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\AccessControl\Models\Role;
use Modules\AccessControl\Repositories\Contracts\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function getAll(array $filters = []): Collection
    {
        return $this->getQuery($filters)->get();
    }

    public function getQuery(array $filters = []): Builder
    {
        return User::with('roles')
            ->select('users.*')
            ->when(!empty($filters['user_type']), fn ($q) => $q->where('user_type', $filters['user_type']))
            ->when(isset($filters['status']) && $filters['status'] !== '', fn ($q) => $q->where('is_active', (bool) $filters['status']))
            ->when(!empty($filters['role']), fn ($q) => $q->whereHas('roles', fn ($r) => $r->where('name', $filters['role'])))
            ->when(!empty($filters['search']), function ($q) use ($filters) {
                $search = $filters['search'];
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            });
    }

    public function findById(int $id): ?User
    {
        return User::with('roles')->find($id);
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(int|User $user, array $data): User
    {
        $user = $user instanceof User ? $user : User::findOrFail($user);
        $user->update($data);

        return $user->fresh('roles');
    }

    public function delete(int|User $user): bool
    {
        $user = $user instanceof User ? $user : User::findOrFail($user);

        return (bool) $user->delete();
    }

    public function toggleStatus(int|User $user): bool
    {
        $user = $user instanceof User ? $user : User::findOrFail($user);
        $user->is_active = !$user->is_active;
        $user->save();

        return (bool) $user->is_active;
    }

    public function assignRole(int|User $user, string $role): User
    {
        $user = $user instanceof User ? $user : User::findOrFail($user);
        $user->assignRole($role);

        return $user->fresh('roles');
    }

    public function syncRoles(int|User $user, array|string $roles): User
    {
        $user = $user instanceof User ? $user : User::findOrFail($user);
        $user->syncRoles((array) $roles);

        return $user->fresh('roles');
    }

    public function getStats(): array
    {
        return [
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'inactive' => User::where('is_active', false)->count(),
            'roles_count' => Role::where('guard_name', 'web')->count(),
        ];
    }

    public function countSuperAdmins(): int
    {
        return User::role('super_admin')->count();
    }
}
