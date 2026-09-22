<?php

namespace Modules\AccessControl\Services;

use App\Models\User;
use DomainException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Modules\AccessControl\Repositories\Contracts\RoleRepositoryInterface;
use Modules\AccessControl\Repositories\Contracts\UserRepositoryInterface;

class UserService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly RoleRepositoryInterface $roleRepository,
    ) {}

    public function getUsersQuery(array $filters = []): Builder
    {
        return $this->userRepository->getQuery($filters);
    }

    public function getUserStats(): array
    {
        return $this->userRepository->getStats();
    }

    public function getRoles(array $filters = ['guard_name' => 'web']): Collection
    {
        return $this->roleRepository->getAll($filters);
    }

    public function findUser(int $id): User
    {
        $user = $this->userRepository->findById($id);
        if (!$user) {
            abort(404, __('User not found'));
        }

        return $user;
    }

    public function createUser(array $data, ?UploadedFile $avatarFile = null): User
    {
        $userData = [
            'name' => $data['name'],
            'username' => !empty($data['username']) ? $data['username'] : null,
            'email' => $data['email'],
            'phone' => !empty($data['phone']) ? $data['phone'] : null,
            'password' => Hash::make($data['password']),
            'user_type' => $data['user_type'],
            'is_active' => isset($data['is_active']) ? (bool) $data['is_active'] : true,
        ];

        if ($avatarFile) {
            $userData['avatar'] = $avatarFile->store('avatars', 'public');
        }

        return DB::transaction(function () use ($userData, $data) {
            $user = $this->userRepository->create($userData);
            if (!empty($data['role'])) {
                $this->userRepository->assignRole($user, $data['role']);
            }

            return $user;
        });
    }

    public function updateUser(int|User $user, array $data, ?UploadedFile $avatarFile = null): User
    {
        $user = $user instanceof User ? $user : $this->findUser($user);

        $userData = [
            'name' => $data['name'],
            'username' => !empty($data['username']) ? $data['username'] : null,
            'email' => $data['email'],
            'phone' => !empty($data['phone']) ? $data['phone'] : null,
            'user_type' => $data['user_type'],
            'is_active' => $user->id === auth()->id() ? true : (isset($data['is_active']) ? (bool) $data['is_active'] : false),
        ];

        if (!empty($data['password'])) {
            $userData['password'] = Hash::make($data['password']);
        }

        if ($avatarFile) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $userData['avatar'] = $avatarFile->store('avatars', 'public');
        }

        return DB::transaction(function () use ($user, $userData, $data) {
            $updatedUser = $this->userRepository->update($user, $userData);
            if (!empty($data['role'])) {
                $this->userRepository->syncRoles($updatedUser, [$data['role']]);
            }

            return $updatedUser;
        });
    }

    public function deleteUser(int|User $user): bool
    {
        $user = $user instanceof User ? $user : $this->findUser($user);

        if ($user->id === auth()->id()) {
            throw new DomainException(__('You cannot delete your own account.'));
        }

        if ($user->hasRole('super_admin') && $this->userRepository->countSuperAdmins() <= 1) {
            throw new DomainException(__('Cannot delete the only Super Administrator in the system.'));
        }

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        return $this->userRepository->delete($user);
    }

    public function toggleUserStatus(int|User $user): bool
    {
        $user = $user instanceof User ? $user : $this->findUser($user);

        if ($user->id === auth()->id()) {
            throw new DomainException(__('You cannot deactivate your own account.'));
        }

        return $this->userRepository->toggleStatus($user);
    }
}
