<?php

namespace Modules\Accounting\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Modules\Accounting\Models\Account;
use Modules\Accounting\Models\CashAccount;
use Modules\Accounting\Repositories\Contracts\CashAccountRepositoryInterface;

class CashAccountService
{
    //--------------------------------------------------
    public function __construct(
        protected CashAccountRepositoryInterface $repository
    ) {}

    //--------------------------------------------------
    public function getAll(array $filters = []): Collection
    {
        return $this->repository->all($filters);
    }

    //--------------------------------------------------
    public function getById(int $id): CashAccount
    {
        $cashAccount = $this->repository->find($id);

        if (!$cashAccount) {
            abort(404, __('Cash Account not found.'));
        }

        return $cashAccount;
    }

    //--------------------------------------------------
    public function getDataTableQuery(array $filters = []): Builder
    {
        return $this->repository->getDataTableQuery($filters);
    }

    //--------------------------------------------------
    public function create(array $data, ?int $userId = null): CashAccount
    {
        $entityId = (int) $data['entity_id'];
        $entity = Entity::findOrFail($entityId);
        $data['currency'] = $entity->currency ?: 'SAR';

        $this->validateUniqueCode($data['code'], $entityId);
        $this->validateAccountBelongsToEntity($data, $entityId);

        $data['created_by'] = $userId ?? auth()->id();

        return $this->repository->create($data);
    }

    //--------------------------------------------------
    public function update(int $id, array $data, ?int $userId = null): CashAccount
    {
        $cashAccount = $this->getById($id);
        $entityId = (int) ($data['entity_id'] ?? $cashAccount->entity_id);
        $entity = Entity::findOrFail($entityId);
        $data['currency'] = $entity->currency ?: 'SAR';

        if (!empty($data['code'])) {
            $this->validateUniqueCode($data['code'], $entityId, $id);
        }

        $this->validateAccountBelongsToEntity($data, $entityId);

        return $this->repository->update($id, $data);
    }

    //--------------------------------------------------
    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    //--------------------------------------------------
    public function toggleStatus(int $id): CashAccount
    {
        return $this->repository->toggleStatus($id);
    }

    //--------------------------------------------------
    protected function validateUniqueCode(string $code, int $entityId, ?int $excludeId = null): void
    {
        if ($this->repository->codeExistsInEntity($code, $entityId, $excludeId)) {
            throw ValidationException::withMessages([
                'code' => __('The short code has already been taken for the selected accounting entity.'),
            ]);
        }
    }

    //--------------------------------------------------
    protected function validateAccountBelongsToEntity(array $data, int $entityId): void
    {
        if (!empty($data['account_id'])) {
            $account = Account::find($data['account_id']);
            if ($account && (int) $account->entity_id !== $entityId) {
                throw ValidationException::withMessages([
                    'account_id' => __('Selected account does not belong to the chosen accounting entity.'),
                ]);
            }
        }
    }
}
