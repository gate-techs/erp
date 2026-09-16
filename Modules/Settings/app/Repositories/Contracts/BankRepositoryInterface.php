<?php

namespace Modules\Settings\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Settings\Models\Bank;

interface BankRepositoryInterface
{
    /**
     * Get all banks (optionally filtered by type: main banks or branches).
     */
    public function getAll(array $filters = [], array $with = ['translations', 'parent', 'branches']): Collection;

    /**
     * Get paginated banks.
     */
    public function getPaginated(array $filters = [], int $perPage = 15, array $with = ['translations', 'parent']): LengthAwarePaginator;

    /**
     * Get DataTable query builder for banks or branches.
     */
    public function getDataTableQuery(array $filters = []): Builder;

    /**
     * Get all main banks list (for select dropdowns when creating branches).
     */
    public function getMainBanks(bool $onlyActive = false): Collection;

    /**
     * Find bank by ID.
     */
    public function findById(int $id, array $with = ['translations', 'parent', 'branches']): ?Bank;

    /**
     * Create a new bank or branch record.
     */
    public function create(array $data): Bank;

    /**
     * Update an existing bank or branch record.
     */
    public function update(int|Bank $model, array $data): Bank;

    /**
     * Delete a bank or branch record.
     */
    public function delete(int|Bank $model): bool;
}
