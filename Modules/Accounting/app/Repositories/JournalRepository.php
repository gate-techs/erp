<?php

namespace Modules\Accounting\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Modules\Accounting\Enums\JournalType;
use Modules\Accounting\Models\Journal;
use Modules\Accounting\Repositories\Contracts\JournalRepositoryInterface;

class JournalRepository implements JournalRepositoryInterface
{
    public function all(array $filters = []): Collection
    {
        return $this->buildFilterQuery($filters)
            ->with(['translations', 'entity.translations'])
            ->orderBy('code')
            ->get();
    }

    public function find(int $id, array $with = ['translations', 'entity.translations', 'defaultAccount.translations', 'suspenseAccount.translations', 'profitAccount.translations', 'lossAccount.translations']): ?Journal
    {
        return Journal::with($with)->find($id);
    }

    public function create(array $data): Journal
    {
        $journal = Journal::create($this->attributes($data));
        $this->saveTranslations($journal, $data);

        return $journal->fresh(['translations', 'entity.translations']);
    }

    public function update(int $id, array $data): Journal
    {
        $journal = Journal::findOrFail($id);
        $journal->update($this->attributes($data, $journal));
        $this->saveTranslations($journal, $data);

        return $journal->fresh(['translations', 'entity.translations']);
    }

    public function delete(int $id): bool
    {
        return (bool) Journal::findOrFail($id)->delete();
    }

    public function getDataTableQuery(array $filters = []): Builder
    {
        return $this->buildFilterQuery($filters);
    }

    public function toggleStatus(int $id): Journal
    {
        $journal = Journal::findOrFail($id);
        $journal->update(['is_active' => !$journal->is_active]);

        return $journal->fresh();
    }

    public function codeExistsInEntity(string $code, int $entityId, ?int $excludeId = null): bool
    {
        $query = Journal::query()
            ->where('entity_id', $entityId)
            ->where('code', $code);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    protected function buildFilterQuery(array $filters): Builder
    {
        $query = Journal::query()->select('journals.*');

        if (!empty($filters['entity_id'])) {
            $query->where('journals.entity_id', $filters['entity_id']);
        }

        if (!empty($filters['type'])) {
            $query->where('journals.type', $filters['type']);
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('journals.is_active', (bool) $filters['is_active']);
        }

        return $query;
    }

    protected function attributes(array $data, ?Journal $journal = null): array
    {
        return [
            'entity_id' => $data['entity_id'] ?? $journal?->entity_id,
            'code' => $data['code'] ?? $journal?->code,
            'type' => $data['type'] ?? $journal?->type?->value ?? JournalType::GENERAL->value,
            'currency' => array_key_exists('currency', $data) ? ($data['currency'] ?: null) : $journal?->currency,
            'sequence_prefix' => array_key_exists('sequence_prefix', $data) ? ($data['sequence_prefix'] ?: null) : $journal?->sequence_prefix,
            'sequence_number_next' => $data['sequence_number_next'] ?? $journal?->sequence_number_next ?? 1,
            'refund_sequence' => $data['refund_sequence'] ?? $journal?->refund_sequence ?? false,
            'payment_sequence' => $data['payment_sequence'] ?? $journal?->payment_sequence ?? false,
            'default_account_id' => array_key_exists('default_account_id', $data) ? ($data['default_account_id'] ?: null) : $journal?->default_account_id,
            'suspense_account_id' => array_key_exists('suspense_account_id', $data) ? ($data['suspense_account_id'] ?: null) : $journal?->suspense_account_id,
            'profit_account_id' => array_key_exists('profit_account_id', $data) ? ($data['profit_account_id'] ?: null) : $journal?->profit_account_id,
            'loss_account_id' => array_key_exists('loss_account_id', $data) ? ($data['loss_account_id'] ?: null) : $journal?->loss_account_id,
            'allow_manual_entries' => $data['allow_manual_entries'] ?? $journal?->allow_manual_entries ?? true,
            'reconcile' => $data['reconcile'] ?? $journal?->reconcile ?? false,
            'restrict_mode_hash_table' => $data['restrict_mode_hash_table'] ?? $journal?->restrict_mode_hash_table ?? false,
            'is_active' => $data['is_active'] ?? $journal?->is_active ?? true,
            'created_by' => $data['created_by'] ?? $journal?->created_by,
        ];
    }

    protected function saveTranslations(Journal $journal, array $data): void
    {
        foreach (['ar', 'en'] as $locale) {
            $nameKey = "name_{$locale}";
            $descriptionKey = "description_{$locale}";

            if (array_key_exists($nameKey, $data) || array_key_exists($descriptionKey, $data)) {
                $translation = $journal->translateOrNew($locale);

                if (array_key_exists($nameKey, $data)) {
                    $translation->name = $data[$nameKey];
                }

                if (array_key_exists($descriptionKey, $data)) {
                    $translation->description = $data[$descriptionKey];
                }
            }
        }

        $journal->save();
    }
}
