<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{
    //==================================
    /**
     * Find a user by email address.
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User;

    //==================================
    /**
     * Find a user by ID.
     *
     * @param int $id
     * @return User|null
     */
    public function findById(int $id): ?User;

    //==================================
    /**
     * Get all users.
     *
     * @return Collection
     */
    public function getAll(): Collection;

    //==================================
    /**
     * Create a new user record.
     *
     * @param array $data
     * @return User
     */
    public function create(array $data): User;

    //==================================
    /**
     * Update an existing user.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool;

    //==================================
    /**
     * Delete a user by ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;
}
