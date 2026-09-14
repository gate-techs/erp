<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class UserRepository implements UserRepositoryInterface
{
    protected User $model;

    //==================================
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    //==================================
    /**
     * Find a user by email address.
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User
    {
        return $this->model->newQuery()->where('email', $email)->first();
    }

    //==================================
    /**
     * Find a user by ID.
     *
     * @param int $id
     * @return User|null
     */
    public function findById(int $id): ?User
    {
        return $this->model->newQuery()->find($id);
    }

    //==================================
    /**
     * Get all users.
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        return $this->model->newQuery()->latest()->get();
    }

    //==================================
    /**
     * Create a new user record.
     *
     * @param array $data
     * @return User
     */
    public function create(array $data): User
    {
        return $this->model->newQuery()->create($data);
    }

    //==================================
    /**
     * Update an existing user.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $user = $this->findById($id);

        if (!$user) {
            return false;
        }

        return $user->update($data);
    }

    //==================================
    /**
     * Delete a user by ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $user = $this->findById($id);

        if (!$user) {
            return false;
        }

        return (bool) $user->delete();
    }
}
