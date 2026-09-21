<?php

namespace Modules\AccessControl\Repositories\Contracts;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{
    public function getAll(array $filters = []): Collection;

    public function getQuery(array $filters = []): Builder;

    public function findById(int $id): ?User;

    public function create(array $data): User;

    public function update(int|User $user, array $data): User;

    public function delete(int|User $user): bool;

    public function toggleStatus(int|User $user): bool;

    public function assignRole(int|User $user, string $role): User;

    public function syncRoles(int|User $user, array|string $roles): User;

    public function getStats(): array;

    public function countSuperAdmins(): int;
}
