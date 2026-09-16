<?php

namespace Modules\Nursery\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\Nursery\Models\Nursery;

interface NurseryRepositoryInterface
{
    //======================
    /**
     * Get all nurseries with optional filters and eager-loaded relations.
     *
     * @param array $filters
     * @param array $with
     * @return Collection<int, Nursery>
     */
    public function getAll(array $filters = [], array $with = ['translations', 'government', 'employee', 'workDays']): Collection;

    //======================
    /**
     * Get paginated nurseries.
     *
     * @param array $filters
     * @param int $perPage
     * @param array $with
     * @return LengthAwarePaginator
     */
    public function getPaginated(array $filters = [], int $perPage = 15, array $with = ['translations', 'government', 'employee']): LengthAwarePaginator;

    //======================
    /**
     * Find a nursery by its ID.
     *
     * @param int $id
     * @param array $with
     * @return Nursery|null
     */
    public function findById(int $id, array $with = ['translations', 'government', 'employee', 'workDays']): ?Nursery;

    //======================
    /**
     * Create a new nursery record with translations.
     *
     * @param array $data
     * @return Nursery
     */
    public function create(array $data): Nursery;

    //======================
    /**
     * Update an existing nursery record with translations.
     *
     * @param int|Nursery $model
     * @param array $data
     * @return Nursery
     */
    public function update(int|Nursery $model, array $data): Nursery;

    //======================
    /**
     * Delete a nursery record.
     *
     * @param int|Nursery $model
     * @return bool
     */
    public function delete(int|Nursery $model): bool;

    //======================
    /**
     * Synchronize weekly working days for a nursery.
     *
     * @param Nursery $nursery
     * @param array<int, array{day: string, from_time: ?string, to_time: ?string, is_off: bool}> $workDays
     * @return void
     */
    public function syncWorkDays(Nursery $nursery, array $workDays): void;

    //======================
    /**
     * Get available employees list (users with user_type = 'employee').
     *
     * @return Collection
     */
    public function getEmployeesList(): Collection;

    //======================
    /**
     * Get available governments list from general_settings.
     *
     * @return Collection
     */
    public function getGovernmentsList(): Collection;
}
