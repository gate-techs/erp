<?php

namespace Modules\Accounting\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Modules\Accounting\Models\Account;
use Modules\Accounting\Models\Journal;
use Modules\Accounting\Repositories\Contracts\JournalRepositoryInterface;

class JournalService
{
    public function __construct(
        protected JournalRepositoryInterface $repository
    ) {}

    public function getAll(array $filters = []): Collection
    {
        return $this->repository->all($filters);
    }

    public function getById(int $id): Journal
    {
        $journal = $this->repository->find($id);

        if (!$journal) {
            abort(404, __('Journal not found.'));
        }

        return $journal;
    }

    public function getDataTableQuery(array $filters = []): Builder
    {
        return $this->repository->getDataTableQuery($filters);
    }

    public function create(array $data, ?int $userId = null): Journal
    {
        $entityId = (int) $data['entity_id'];
        $this->validateUniqueCode($data['code'], $entityId);
        $this->validateAccountsBelongToEntity($data, $entityId);

        $data['created_by'] = $userId ?? auth()->id();

        return $this->repository->create($data);
    }

    public function update(int $id, array $data, ?int $userId = null): Journal
    {
        $journal = $this->getById($id);
        $entityId = (int) ($data['entity_id'] ?? $journal->entity_id);

        if (!empty($data['code'])) {
            $this->validateUniqueCode($data['code'], $entityId, $id);
        }

        $this->validateAccountsBelongToEntity($data, $entityId);

        return $this->repository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function toggleStatus(int $id): Journal
    {
        return $this->repository->toggleStatus($id);
    }

    protected function validateUniqueCode(string $code, int $entityId, ?int $excludeId = null): void
    {
        if ($this->repository->codeExistsInEntity($code, $entityId, $excludeId)) {
            throw ValidationException::withMessages([
                'code' => [__('The journal code has already been taken for this entity.')],
            ]);
        }
    }

    protected function validateAccountsBelongToEntity(array $data, int $entityId): void
    {
        $accountIds = collect([
            $data['default_account_id'] ?? null,
            $data['suspense_account_id'] ?? null,
            $data['profit_account_id'] ?? null,
            $data['loss_account_id'] ?? null,
        ])->filter()->map(fn ($id) => (int) $id)->unique()->values();

        if ($accountIds->isEmpty()) {
            return;
        }

        $validCount = Account::query()
            ->where('entity_id', $entityId)
            ->whereIn('id', $accountIds)
            ->count();

        if ($validCount !== $accountIds->count()) {
            throw ValidationException::withMessages([
                'default_account_id' => [__('All selected accounts must belong to the selected accounting entity.')],
            ]);
        }
    }
}
