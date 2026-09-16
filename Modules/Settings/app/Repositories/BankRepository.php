<?php

namespace Modules\Settings\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Settings\Models\Bank;
use Modules\Settings\Repositories\Contracts\BankRepositoryInterface;

class BankRepository implements BankRepositoryInterface
{
    /**
     * Get all banks (optionally filtered).
     */
    public function getAll(array $filters = [], array $with = ['translations', 'parent', 'branches']): Collection
    {
        return $this->getDataTableQuery($filters)->with($with)->get();
    }

    /**
     * Get paginated banks.
     */
    public function getPaginated(array $filters = [], int $perPage = 15, array $with = ['translations', 'parent']): LengthAwarePaginator
    {
        return $this->getDataTableQuery($filters)->with($with)->paginate($perPage);
    }

    /**
     * Get DataTable query builder for banks or branches.
     */
    public function getDataTableQuery(array $filters = []): Builder
    {
        $query = Bank::query()->latest('id');

        // Filter by main banks vs bank branches
        if (isset($filters['type'])) {
            if ($filters['type'] === 'main') {
                $query->mainBanks();
            } elseif ($filters['type'] === 'branch') {
                $query->bankBranches();
            }
        } elseif (isset($filters['is_branch'])) {
            if ($filters['is_branch']) {
                $query->bankBranches();
            } else {
                $query->mainBanks();
            }
        }

        // Filter by parent_id (specific bank for branches)
        if (!empty($filters['parent_id'])) {
            $query->where('parent_id', $filters['parent_id']);
        }

        // Filter by status
        if (isset($filters['status']) && $filters['status'] !== '' && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        // Search
        if (!empty($filters['search'])) {
            $search = is_array($filters['search']) ? ($filters['search']['value'] ?? null) : $filters['search'];
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->whereTranslationLike('name', "%{$search}%")
                      ->orWhereTranslationLike('address', "%{$search}%")
                      ->orWhere('swift_code', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
                });
            }
        }

        return $query;
    }

    /**
     * Get all main banks list (for select dropdowns when creating branches).
     */
    public function getMainBanks(bool $onlyActive = false): Collection
    {
        $query = Bank::with('translations')->mainBanks()->orderBy('id', 'desc');

        if ($onlyActive) {
            $query->where('status', 'active');
        }

        return $query->get();
    }

    /**
     * Find bank by ID.
     */
    public function findById(int $id, array $with = ['translations', 'parent', 'branches']): ?Bank
    {
        return Bank::with($with)->find($id);
    }

    /**
     * Create a new bank or branch record.
     */
    public function create(array $data): Bank
    {
        return Bank::create($data);
    }

    /**
     * Update an existing bank or branch record.
     */
    public function update(int|Bank $model, array $data): Bank
    {
        $bank = $model instanceof Bank ? $model : Bank::findOrFail($model);
        $bank->update($data);

        return $bank->fresh(['translations', 'parent', 'branches']);
    }

    /**
     * Delete a bank or branch record.
     */
    public function delete(int|Bank $model): bool
    {
        $bank = $model instanceof Bank ? $model : Bank::findOrFail($model);

        return (bool) $bank->delete();
    }
}
