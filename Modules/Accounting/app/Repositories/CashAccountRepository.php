<?php

namespace Modules\Accounting\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Modules\Accounting\Enums\CashAccountType;
use Modules\Accounting\Models\CashAccount;
use Modules\Accounting\Repositories\Contracts\CashAccountRepositoryInterface;

class CashAccountRepository implements CashAccountRepositoryInterface
{
    //--------------------------------------------------
    public function all(array $filters = []): Collection
    {
        return $this->buildFilterQuery($filters)
            ->with(['translations', 'entity.translations', 'account.translations', 'journal.translations', 'responsibleUser'])
            ->orderBy('code')
            ->get();
    }

    //--------------------------------------------------
    public function find(int $id, array $with = ['translations', 'entity.translations', 'account.translations', 'journal.translations', 'responsibleUser']): ?CashAccount
    {
        return CashAccount::with($with)->find($id);
    }

    //--------------------------------------------------
    public function create(array $data): CashAccount
    {
        $cashAccount = CashAccount::create($this->attributes($data));
        $this->saveTranslations($cashAccount, $data);

        return $cashAccount->fresh(['translations', 'entity.translations', 'account.translations', 'journal.translations', 'responsibleUser']);
    }

    //--------------------------------------------------
    public function update(int $id, array $data): CashAccount
    {
        $cashAccount = CashAccount::findOrFail($id);
        $cashAccount->update($this->attributes($data, $cashAccount));
        $this->saveTranslations($cashAccount, $data);

        return $cashAccount->fresh(['translations', 'entity.translations', 'account.translations', 'journal.translations', 'responsibleUser']);
    }

    //--------------------------------------------------
    public function delete(int $id): bool
    {
        return (bool) CashAccount::findOrFail($id)->delete();
    }

    //--------------------------------------------------
    public function getDataTableQuery(array $filters = []): Builder
    {
        return $this->buildFilterQuery($filters);
    }

    //--------------------------------------------------
    public function toggleStatus(int $id): CashAccount
    {
        $cashAccount = CashAccount::findOrFail($id);
        $cashAccount->update(['is_active' => !$cashAccount->is_active]);

        return $cashAccount->fresh();
    }

    //--------------------------------------------------
    public function codeExistsInEntity(string $code, int $entityId, ?int $excludeId = null): bool
    {
        $query = CashAccount::query()
            ->where('entity_id', $entityId)
            ->where('code', $code);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    //--------------------------------------------------
    protected function buildFilterQuery(array $filters): Builder
    {
        $query = CashAccount::query()->select('cash_accounts.*');

        if (!empty($filters['entity_id'])) {
            $query->where('cash_accounts.entity_id', $filters['entity_id']);
        }

        if (!empty($filters['type'])) {
            $query->where('cash_accounts.type', $filters['type']);
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('cash_accounts.is_active', (bool) $filters['is_active']);
        }

        return $query;
    }

    //--------------------------------------------------
    protected function attributes(array $data, ?CashAccount $cashAccount = null): array
    {
        return [
            'entity_id' => $data['entity_id'] ?? $cashAccount?->entity_id,
            'account_id' => array_key_exists('account_id', $data) ? ($data['account_id'] ?: null) : $cashAccount?->account_id,
            'journal_id' => array_key_exists('journal_id', $data) ? ($data['journal_id'] ?: null) : $cashAccount?->journal_id,
            'type' => $data['type'] ?? $cashAccount?->type?->value ?? CashAccountType::CASH->value,
            'code' => $data['code'] ?? $cashAccount?->code,
            'bank_name' => array_key_exists('bank_name', $data) ? ($data['bank_name'] ?: null) : $cashAccount?->bank_name,
            'account_number' => array_key_exists('account_number', $data) ? ($data['account_number'] ?: null) : $cashAccount?->account_number,
            'iban' => array_key_exists('iban', $data) ? ($data['iban'] ?: null) : $cashAccount?->iban,
            'swift_code' => array_key_exists('swift_code', $data) ? ($data['swift_code'] ?: null) : $cashAccount?->swift_code,
            'currency' => array_key_exists('currency', $data) ? ($data['currency'] ?: 'SAR') : ($cashAccount?->currency ?? 'SAR'),
            'opening_balance' => array_key_exists('opening_balance', $data) ? (float) ($data['opening_balance'] ?? 0) : ($cashAccount?->opening_balance ?? 0),
            'responsible_user_id' => array_key_exists('responsible_user_id', $data) ? ($data['responsible_user_id'] ?: null) : $cashAccount?->responsible_user_id,
            'is_active' => $data['is_active'] ?? $cashAccount?->is_active ?? true,
            'notes' => array_key_exists('notes', $data) ? ($data['notes'] ?: null) : $cashAccount?->notes,
            'created_by' => $data['created_by'] ?? $cashAccount?->created_by,
        ];
    }

    //--------------------------------------------------
    protected function saveTranslations(CashAccount $cashAccount, array $data): void
    {
        foreach (['ar', 'en'] as $locale) {
            $nameKey = "name_{$locale}";
            $descriptionKey = "description_{$locale}";

            if (array_key_exists($nameKey, $data) || array_key_exists($descriptionKey, $data)) {
                $translation = $cashAccount->translateOrNew($locale);

                if (array_key_exists($nameKey, $data)) {
                    $translation->name = $data[$nameKey];
                }

                if (array_key_exists($descriptionKey, $data)) {
                    $translation->description = $data[$descriptionKey];
                }
            }
        }

        $cashAccount->save();
    }
}
