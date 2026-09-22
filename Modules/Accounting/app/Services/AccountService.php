<?php

namespace Modules\Accounting\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Modules\Accounting\Enums\AccountType;
use Modules\Accounting\Models\Account;
use Modules\Accounting\Repositories\Contracts\AccountRepositoryInterface;

class AccountService
{
    public function __construct(protected AccountRepositoryInterface $repository) {}

    //--------------------------------------------------
    public function getAllAccounts(array $filters = []): Collection
    {
        return $this->repository->getAll($filters);
    }

    //--------------------------------------------------
    public function getPaginatedAccounts(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getPaginated($filters, $perPage);
    }

    //--------------------------------------------------
    public function getDataTableQuery(array $filters = []): Builder
    {
        return $this->repository->getDataTableQuery($filters);
    }

    //--------------------------------------------------
    /**
     * Get the full hierarchical tree for an entity.
     * Fetches all accounts in one query and builds the tree in memory for unlimited depth and high performance.
     */
    public function getAccountTree(int $entityId): Collection
    {
        $all = Account::query()
            ->where('entity_id', $entityId)
            ->with(['translations', 'parent.translations'])
            ->orderBy('sort_order')
            ->orderBy('code')
            ->get();

        $grouped = $all->groupBy('parent_id');

        $buildBranch = function ($parentId) use (&$buildBranch, $grouped) {
            $branch = $grouped->get($parentId, collect());
            foreach ($branch as $account) {
                $account->setRelation('children', $buildBranch($account->id));
            }
            return $branch;
        };

        return $buildBranch(null);
    }

    //--------------------------------------------------
    public function getAccountById(int $id): ?Account
    {
        return $this->repository->findById($id);
    }

    //--------------------------------------------------
    public function createAccount(array $input, ?int $userId = null): Account
    {
        return DB::transaction(function () use ($input, $userId): Account {
            $entityId = (int) ($input['entity_id'] ?? 0);
            $accountType = $this->resolveAccountType($input['account_type'] ?? null);
            $parent = $this->resolveParent($input['parent_id'] ?? null, $entityId);

            $data = $this->prepareData($input, $entityId, $parent, $accountType, $userId);

            if (empty($data['code'])) {
                $data['code'] = $this->suggestCode($entityId, $parent?->id);
            }

            return $this->repository->create($data);
        });
    }

    //--------------------------------------------------
    public function updateAccount(int|Account $account, array $input): Account
    {
        $instance = $account instanceof Account ? $account : $this->getAccountById($account);

        if (!$instance) {
            throw new InvalidArgumentException(__('Account not found.'));
        }

        $parent = $this->resolveParent($input['parent_id'] ?? $instance->parent_id, $instance->entity_id, $instance->id);
        $accountType = $this->resolveAccountType($input['account_type'] ?? $instance->account_type);

        return DB::transaction(function () use ($instance, $input, $parent, $accountType): Account {
            $data = $this->prepareData($input, $instance->entity_id, $parent, $accountType);
            if (!empty($input['code'])) {
                $data['code'] = trim((string) $input['code']);
            }

            return $this->repository->update($instance, $data);
        });
    }

    //--------------------------------------------------
    public function deleteAccount(int|Account $account): bool
    {
        $instance = $account instanceof Account ? $account : $this->getAccountById($account);

        if (!$instance) {
            return false;
        }

        if ($instance->children()->exists()) {
            throw new InvalidArgumentException(__('An account with children cannot be deleted.'));
        }

        return $this->repository->delete($instance);
    }

    //--------------------------------------------------
    public function toggleStatus(int|Account $account): Account
    {
        $instance = $account instanceof Account ? $account : $this->getAccountById($account);
        if (!$instance) {
            throw new InvalidArgumentException(__('Account not found.'));
        }

        $instance->is_active = !$instance->is_active;
        $instance->save();

        return $instance;
    }

    //--------------------------------------------------
    public function toggleDeprecated(int|Account $account): Account
    {
        $instance = $account instanceof Account ? $account : $this->getAccountById($account);
        if (!$instance) {
            throw new InvalidArgumentException(__('Account not found.'));
        }

        $instance->is_deprecated = !$instance->is_deprecated;
        $instance->save();

        return $instance;
    }

    //--------------------------------------------------
    /**
     * Suggest next logical account code based on parent or entity.
     */
    public function suggestCode(int $entityId, ?int $parentId = null): string
    {
        if ($parentId) {
            $parent = Account::find($parentId);
            if ($parent) {
                $latestChild = Account::where('entity_id', $entityId)
                    ->where('parent_id', $parentId)
                    ->orderByRaw('LENGTH(code) DESC, code DESC')
                    ->first();

                if ($latestChild && is_numeric($latestChild->code)) {
                    return (string) ((int) $latestChild->code + 1);
                }

                return $parent->code . '01';
            }
        }

        // Top level account: find max root code or default
        $latestRoot = Account::where('entity_id', $entityId)
            ->whereNull('parent_id')
            ->orderByRaw('LENGTH(code) DESC, code DESC')
            ->first();

        if ($latestRoot && is_numeric($latestRoot->code)) {
            return (string) ((int) $latestRoot->code + 1);
        }

        return '1';
    }

    //--------------------------------------------------
    /**
     * Get group accounts eligible to be parents in the specified entity.
     */
    public function getPotentialParents(int $entityId, ?int $excludeId = null): Collection
    {
        $query = Account::where('entity_id', $entityId)
            ->where('is_group', true)
            ->with('translations')
            ->orderBy('code');

        if ($excludeId) {
            $query->where('id', '!=', $excludeId)
                ->where(function ($q) use ($excludeId) {
                    $q->whereNull('parent_id')->orWhere('parent_id', '!=', $excludeId);
                });
        }

        return $query->get();
    }

    //--------------------------------------------------
    protected function prepareData(array $input, int $entityId, ?Account $parent, AccountType $accountType, ?int $userId = null): array
    {
        $isGroup = filter_var($input['is_group'] ?? false, FILTER_VALIDATE_BOOLEAN);

        $data = [
            'entity_id'      => $entityId,
            'parent_id'      => $parent?->id,
            'account_type'   => $accountType->value,
            'normal_balance' => $accountType->normalBalance(),
            'level'          => $parent ? ($parent->level + 1) : 1,
            'is_group'       => $isGroup,
            'allow_posting'  => $isGroup ? false : true,
            'reconcile'      => filter_var($input['reconcile'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'is_active'      => filter_var($input['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'is_deprecated'  => filter_var($input['is_deprecated'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'sort_order'     => (int) ($input['sort_order'] ?? 0),
        ];

        if (!empty($input['code'])) {
            $data['code'] = trim((string) $input['code']);
        }

        if ($userId) {
            $data['created_by'] = $userId;
        }

        foreach (['ar', 'en'] as $locale) {
            $nameKey = "name_{$locale}";
            $descriptionKey = "description_{$locale}";

            if (array_key_exists($nameKey, $input) || array_key_exists($descriptionKey, $input)) {
                $data[$locale] = [
                    'name'        => $input[$nameKey] ?? '',
                    'description' => $input[$descriptionKey] ?? null,
                ];
            }
        }

        return $data;
    }

    //--------------------------------------------------
    protected function resolveParent(?int $parentId, int $entityId, ?int $accountId = null): ?Account
    {
        if (!$parentId) {
            return null;
        }

        $parent = Account::find($parentId);

        if (!$parent || $parent->entity_id !== $entityId) {
            throw new InvalidArgumentException(__('The selected parent account is invalid.'));
        }

        if ($accountId && ($parent->id === $accountId || $parent->parent_id === $accountId)) {
            throw new InvalidArgumentException(__('An account cannot be its own parent.'));
        }

        return $parent;
    }

    //--------------------------------------------------
    protected function resolveAccountType(AccountType|string|null $accountType): AccountType
    {
        $resolved = $accountType instanceof AccountType
            ? $accountType
            : AccountType::tryFrom((string) $accountType);

        if (!$resolved) {
            throw new InvalidArgumentException(__('The selected account type is invalid.'));
        }

        return $resolved;
    }
}
