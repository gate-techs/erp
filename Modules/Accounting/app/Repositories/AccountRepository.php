<?php

namespace Modules\Accounting\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Accounting\Models\Account;
use Modules\Accounting\Repositories\Contracts\AccountRepositoryInterface;

class AccountRepository implements AccountRepositoryInterface
{
    //--------------------------------------------------
    public function getAll(array $filters = [], array $with = ['translations', 'parent', 'children']): Collection
    {
        return $this->getDataTableQuery($filters)->with($with)->get();
    }

    //--------------------------------------------------
    public function getPaginated(array $filters = [], int $perPage = 15, array $with = ['translations', 'parent']): LengthAwarePaginator
    {
        return $this->getDataTableQuery($filters)->with($with)->paginate($perPage);
    }

    //--------------------------------------------------
    public function getDataTableQuery(array $filters = []): Builder
    {
        $query = Account::query()->latest('id');

        if (!empty($filters['entity_id'])) {
            $query->where('entity_id', $filters['entity_id']);
        }

        if (array_key_exists('parent_id', $filters)) {
            $filters['parent_id'] === null
                ? $query->whereNull('parent_id')
                : $query->where('parent_id', $filters['parent_id']);
        }

        if (!empty($filters['account_type'])) {
            $query->where('account_type', $filters['account_type']);
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '' && $filters['is_active'] !== 'all') {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        if (!empty($filters['search'])) {
            $search = is_array($filters['search']) ? ($filters['search']['value'] ?? null) : $filters['search'];

            if ($search) {
                $query->where(function (Builder $builder) use ($search) {
                    $builder->whereTranslationLike('name', "%{$search}%")
                        ->orWhereTranslationLike('description', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            }
        }

        return $query->orderBy('sort_order')->orderBy('code');
    }

    //--------------------------------------------------
    public function getTree(int $entityId, array $with = ['translations', 'children.translations']): Collection
    {
        return Account::query()
            ->where('entity_id', $entityId)
            ->whereNull('parent_id')
            ->with($with)
            ->orderBy('sort_order')
            ->orderBy('code')
            ->get();
    }

    //--------------------------------------------------
    public function findById(int $id, array $with = ['translations', 'parent', 'children']): ?Account
    {
        return Account::with($with)->find($id);
    }

    //--------------------------------------------------
    public function create(array $data): Account
    {
        return Account::create($data);
    }

    //--------------------------------------------------
    public function update(int|Account $model, array $data): Account
    {
        $account = $model instanceof Account ? $model : Account::findOrFail($model);
        $account->update($data);

        return $account->fresh(['translations', 'parent', 'children']);
    }

    //--------------------------------------------------
    public function delete(int|Account $model): bool
    {
        $account = $model instanceof Account ? $model : Account::findOrFail($model);

        return (bool) $account->delete();
    }
}
