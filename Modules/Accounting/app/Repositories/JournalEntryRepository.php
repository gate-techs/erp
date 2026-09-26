<?php

namespace Modules\Accounting\Repositories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Accounting\Enums\JournalEntryStatus;
use Modules\Accounting\Enums\JournalEntryType;
use Modules\Accounting\Models\Entity;
use Modules\Accounting\Models\FiscalYear;
use Modules\Accounting\Models\Journal;
use Modules\Accounting\Models\JournalEntry;
use Modules\Accounting\Models\JournalEntryLine;
use Modules\Accounting\Repositories\Contracts\JournalEntryRepositoryInterface;

class JournalEntryRepository implements JournalEntryRepositoryInterface
{
    public function all(array $filters = []): Collection
    {
        return $this->buildQuery($filters)->get();
    }

    public function find(int $id): ?JournalEntry
    {
        return JournalEntry::find($id);
    }

    public function findWithDetails(int $id): ?JournalEntry
    {
        return JournalEntry::with([
            'entity.translations',
            'journal.translations',
            'fiscalYear.translations',
            'lines.account.translations',
            'lines.costCenter.translations',
            'creator',
            'updater',
            'postedBy',
            'reversedEntry',
            'reversalEntry',
        ])->find($id);
    }

    public function getDataTableQuery(array $filters = []): Builder
    {
        return $this->buildQuery($filters);
    }

    public function create(array $data, array $lines): JournalEntry
    {
        return DB::transaction(function () use ($data, $lines) {
            $journalId = (int) $data['journal_id'];
            $entityId = (int) $data['entity_id'];
            $entryDate = $data['entry_date'] ?? now()->toDateString();

            // Auto-detect fiscal year if not provided
            if (empty($data['fiscal_year_id'])) {
                $fiscalYear = FiscalYear::where('entity_id', $entityId)
                    ->where('start_date', '<=', $entryDate)
                    ->where('end_date', '>=', $entryDate)
                    ->first();
                $data['fiscal_year_id'] = $fiscalYear?->id;
            }

            // Auto-fill currency from entity if not provided
            if (empty($data['currency'])) {
                $entity = Entity::find($entityId);
                $data['currency'] = $entity?->currency ?? 'KWD';
            }

            // Default accounting date to entry date
            if (empty($data['accounting_date'])) {
                $data['accounting_date'] = $entryDate;
            }

            // Generate entry number if not supplied
            if (empty($data['entry_number'])) {
                $data['entry_number'] = $this->generateNextEntryNumber($entityId, $journalId, $entryDate);
            }

            // Calculate totals
            $totals = $this->calculateTotals($lines);
            $data['total_debit'] = $totals['debit'];
            $data['total_credit'] = $totals['credit'];
            $data['is_balanced'] = $totals['is_balanced'];
            $data['status'] = $data['status'] ?? JournalEntryStatus::DRAFT;

            $entry = JournalEntry::create($data);

            // Save lines
            $this->saveLines($entry, $lines);

            // Increment journal sequence if generated
            $this->incrementJournalSequence($journalId);

            return $this->findWithDetails($entry->id);
        });
    }

    public function update(int $id, array $data, array $lines): JournalEntry
    {
        return DB::transaction(function () use ($id, $data, $lines) {
            $entry = JournalEntry::findOrFail($id);

            // Auto-detect fiscal year if changed
            if (isset($data['entry_date']) && empty($data['fiscal_year_id'])) {
                $fiscalYear = FiscalYear::where('entity_id', $entry->entity_id)
                    ->where('start_date', '<=', $data['entry_date'])
                    ->where('end_date', '>=', $data['entry_date'])
                    ->first();
                $data['fiscal_year_id'] = $fiscalYear?->id;

                if (empty($data['accounting_date'])) {
                    $data['accounting_date'] = $data['entry_date'];
                }
            }

            // Always sync currency with the accounting entity
            $entity = Entity::find($data['entity_id'] ?? $entry->entity_id);
            if ($entity) {
                $data['currency'] = $entity->currency ?? 'KWD';
            }

            $totals = $this->calculateTotals($lines);
            $data['total_debit'] = $totals['debit'];
            $data['total_credit'] = $totals['credit'];
            $data['is_balanced'] = $totals['is_balanced'];

            $entry->update($data);

            // Replace lines
            $entry->lines()->delete();
            $this->saveLines($entry, $lines);

            return $this->findWithDetails($entry->id);
        });
    }

    public function delete(int $id): bool
    {
        $entry = JournalEntry::findOrFail($id);
        return (bool) $entry->delete();
    }

    public function post(int $id, int $userId): JournalEntry
    {
        return DB::transaction(function () use ($id, $userId) {
            $entry = JournalEntry::findOrFail($id);

            $entry->update([
                'status' => JournalEntryStatus::POSTED,
                'posted_at' => now(),
                'posted_by' => $userId,
                'updated_by' => $userId,
            ]);

            return $this->findWithDetails($entry->id);
        });
    }

    public function cancel(int $id, int $userId): JournalEntry
    {
        $entry = JournalEntry::findOrFail($id);
        $entry->update([
            'status' => JournalEntryStatus::CANCELLED,
            'updated_by' => $userId,
        ]);

        return $this->findWithDetails($entry->id);
    }

    public function reverse(int $id, array $reversalData, int $userId): JournalEntry
    {
        return DB::transaction(function () use ($id, $reversalData, $userId) {
            $original = $this->findWithDetails($id);

            $reversalDate = $reversalData['entry_date'] ?? now()->toDateString();
            $journalId = $reversalData['journal_id'] ?? $original->journal_id;
            $entityId = $original->entity_id;

            // Generate next entry number for reversal
            $entryNumber = $this->generateNextEntryNumber($entityId, $journalId, $reversalDate);

            // Invert debit and credit lines
            $invertedLines = [];
            foreach ($original->lines as $idx => $line) {
                $invertedLines[] = [
                    'account_id' => $line->account_id,
                    'cost_center_id' => $line->cost_center_id,
                    'partner_id' => $line->partner_id,
                    'partner_type' => $line->partner_type,
                    'partner_name' => $line->partner_name,
                    'description' => __('Reversal: ') . ($line->description ?? $original->entry_number),
                    'debit' => $line->credit,  // swapped
                    'credit' => $line->debit,  // swapped
                    'currency' => $line->currency,
                    'line_order' => $idx,
                ];
            }

            $narration = $reversalData['narration'] ?? (__('Reversal entry for ') . $original->entry_number . ($reversalData['reason'] ? ' - ' . $reversalData['reason'] : ''));

            $reversalEntryData = [
                'entity_id' => $entityId,
                'fiscal_year_id' => $reversalData['fiscal_year_id'] ?? null,
                'journal_id' => $journalId,
                'entry_number' => $entryNumber,
                'entry_date' => $reversalDate,
                'accounting_date' => $reversalDate,
                'reference' => 'REV/' . $original->entry_number,
                'entry_type' => JournalEntryType::REVERSAL,
                'currency' => $original->currency,
                'exchange_rate' => $original->exchange_rate,
                'status' => JournalEntryStatus::POSTED,
                'narration' => $narration,
                'posted_at' => now(),
                'posted_by' => $userId,
                'reversed_entry_id' => $original->id,
                'is_reversal' => true,
                'created_by' => $userId,
                'updated_by' => $userId,
            ];

            $reversalEntry = $this->create($reversalEntryData, $invertedLines);

            return $reversalEntry;
        });
    }

    public function generateNextEntryNumber(int $entityId, int $journalId, ?string $entryDate = null): string
    {
        $journal = Journal::find($journalId);
        $date = $entryDate ? Carbon::parse($entryDate) : now();

        $prefix = $journal?->sequence_prefix ?: ($journal?->code ? $journal->code . '/%Y/' : 'JE/%Y/');
        $prefix = str_replace(['%Y', '%y', '%m', '%d'], [$date->format('Y'), $date->format('y'), $date->format('m'), $date->format('d')], $prefix);

        $nextSeq = $journal?->sequence_number_next ?: 1;

        // Ensure uniqueness
        do {
            $formattedNumber = $prefix . str_pad((string) $nextSeq, 5, '0', STR_PAD_LEFT);
            $exists = JournalEntry::where('entity_id', $entityId)->where('entry_number', $formattedNumber)->exists();
            if ($exists) {
                $nextSeq++;
            }
        } while ($exists);

        return $formattedNumber;
    }

    public function getSummaryStats(array $filters = []): array
    {
        $query = $this->buildQuery($filters);

        $allQuery = clone $query;
        $draftQuery = clone $query;
        $postedQuery = clone $query;

        return [
            'total_count' => $allQuery->count(),
            'draft_count' => $draftQuery->where('journal_entries.status', JournalEntryStatus::DRAFT)->count(),
            'posted_count' => $postedQuery->where('journal_entries.status', JournalEntryStatus::POSTED)->count(),
            'total_amount' => (float) (clone $query)->where('journal_entries.status', JournalEntryStatus::POSTED)->sum('journal_entries.total_debit'),
        ];
    }

    //--------------------------------------------------
    // Protected Helpers
    //--------------------------------------------------

    protected function buildQuery(array $filters = []): Builder
    {
        return JournalEntry::query()
            ->with([
                'entity.translations',
                'journal.translations',
                'creator',
                'postedBy',
            ])
            ->when(!empty($filters['entity_id']), fn ($q) => $q->where('journal_entries.entity_id', $filters['entity_id']))
            ->when(!empty($filters['journal_id']), fn ($q) => $q->where('journal_entries.journal_id', $filters['journal_id']))
            ->when(!empty($filters['status']), fn ($q) => $q->where('journal_entries.status', $filters['status']))
            ->when(!empty($filters['entry_type']), fn ($q) => $q->where('journal_entries.entry_type', $filters['entry_type']))
            ->when(!empty($filters['from_date']), fn ($q) => $q->where('journal_entries.entry_date', '>=', $filters['from_date']))
            ->when(!empty($filters['to_date']), fn ($q) => $q->where('journal_entries.entry_date', '<=', $filters['to_date']))
            ->when(isset($filters['search']), function ($q) use ($filters) {
                $keyword = is_array($filters['search']) ? ($filters['search']['value'] ?? null) : $filters['search'];
                if (!empty($keyword) && is_string($keyword)) {
                    $q->where(function ($sub) use ($keyword) {
                        $sub->where('journal_entries.entry_number', 'like', "%{$keyword}%")
                            ->orWhere('journal_entries.reference', 'like', "%{$keyword}%")
                            ->orWhere('journal_entries.narration', 'like', "%{$keyword}%");
                    });
                }
            });
    }

    protected function calculateTotals(array $lines): array
    {
        $totalDebit = 0.0000;
        $totalCredit = 0.0000;

        foreach ($lines as $line) {
            $totalDebit += (float) ($line['debit'] ?? 0);
            $totalCredit += (float) ($line['credit'] ?? 0);
        }

        // Round to 4 decimal places
        $totalDebit = round($totalDebit, 4);
        $totalCredit = round($totalCredit, 4);
        $isBalanced = abs($totalDebit - $totalCredit) < 0.0001;

        return [
            'debit' => $totalDebit,
            'credit' => $totalCredit,
            'is_balanced' => $isBalanced,
        ];
    }

    protected function saveLines(JournalEntry $entry, array $lines): void
    {
        foreach ($lines as $order => $lineData) {
            $debit = (float) ($lineData['debit'] ?? 0);
            $credit = (float) ($lineData['credit'] ?? 0);

            // Skip invalid empty lines
            if ($debit <= 0 && $credit <= 0 && empty($lineData['account_id'])) {
                continue;
            }

            $entry->lines()->create([
                'account_id' => (int) $lineData['account_id'],
                'cost_center_id' => !empty($lineData['cost_center_id']) ? (int) $lineData['cost_center_id'] : null,
                'partner_id' => !empty($lineData['partner_id']) ? (int) $lineData['partner_id'] : null,
                'partner_type' => $lineData['partner_type'] ?? null,
                'partner_name' => $lineData['partner_name'] ?? null,
                'description' => $lineData['description'] ?? $entry->narration,
                'debit' => $debit,
                'credit' => $credit,
                'currency' => $lineData['currency'] ?? $entry->currency,
                'amount_currency' => isset($lineData['amount_currency']) ? (float) $lineData['amount_currency'] : null,
                'line_order' => $order,
            ]);
        }
    }

    protected function incrementJournalSequence(int $journalId): void
    {
        $journal = Journal::find($journalId);
        if ($journal) {
            $journal->increment('sequence_number_next');
        }
    }
}
