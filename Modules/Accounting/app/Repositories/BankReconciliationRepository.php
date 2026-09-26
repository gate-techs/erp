<?php

namespace Modules\Accounting\Repositories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Accounting\Enums\BankReconciliationItemType;
use Modules\Accounting\Enums\BankReconciliationStatus;
use Modules\Accounting\Enums\JournalEntryStatus;
use Modules\Accounting\Models\BankReconciliation;
use Modules\Accounting\Models\BankReconciliationItem;
use Modules\Accounting\Models\CashAccount;
use Modules\Accounting\Models\JournalEntryLine;
use Modules\Accounting\Repositories\Contracts\BankReconciliationRepositoryInterface;

class BankReconciliationRepository implements BankReconciliationRepositoryInterface
{
    //--------------------------------------------------
    public function getDataTableQuery(array $filters = []): Builder
    {
        $query = BankReconciliation::query()
            ->with([
                'entity.translations',
                'cashAccount.translations',
                'reconciledBy',
                'creator',
            ]);

        if (!empty($filters['entity_id'])) {
            $query->where('entity_id', (int) $filters['entity_id']);
        }

        if (!empty($filters['cash_account_id'])) {
            $query->where('cash_account_id', (int) $filters['cash_account_id']);
        }

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        $fromDate = $filters['from_date'] ?? $filters['date_from'] ?? null;
        if (!empty($fromDate)) {
            $query->whereDate('statement_date', '>=', $fromDate);
        }

        $toDate = $filters['to_date'] ?? $filters['date_to'] ?? null;
        if (!empty($toDate)) {
            $query->whereDate('statement_date', '<=', $toDate);
        }

        return $query->orderByDesc('statement_date')->orderByDesc('id');
    }

    //--------------------------------------------------
    public function findById(int $id): ?BankReconciliation
    {
        return BankReconciliation::query()
            ->with([
                'entity.translations',
                'cashAccount.translations',
                'items.journalEntryLine.entry.journal.translations',
                'reconciledBy',
                'creator',
            ])
            ->find($id);
    }

    //--------------------------------------------------
    public function getNextReconciliationNumber(int $entityId): string
    {
        $year = date('Y');
        $prefix = "REC-{$year}-";

        $last = BankReconciliation::where('entity_id', $entityId)
            ->where('reconciliation_number', 'like', "{$prefix}%")
            ->orderByDesc('id')
            ->value('reconciliation_number');

        if ($last) {
            $seq = (int) str_replace($prefix, '', $last) + 1;
        } else {
            $seq = 1;
        }

        return $prefix . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }

    //--------------------------------------------------
    public function calculateBookBalance(int $cashAccountId, string $asOfDate): float
    {
        $cashAccount = CashAccount::findOrFail($cashAccountId);

        if (!$cashAccount->account_id) {
            return 0.0;
        }

        $res = JournalEntryLine::query()
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
            ->where('journal_entry_lines.account_id', $cashAccount->account_id)
            ->where('journal_entries.status', JournalEntryStatus::POSTED->value)
            ->whereDate('journal_entries.entry_date', '<=', $asOfDate)
            ->selectRaw('COALESCE(SUM(journal_entry_lines.debit), 0) as total_debit, COALESCE(SUM(journal_entry_lines.credit), 0) as total_credit')
            ->first();

        $debit = (float) ($res->total_debit ?? 0);
        $credit = (float) ($res->total_credit ?? 0);

        return round($debit - $credit, 4);
    }

    //--------------------------------------------------
    public function getUnreconciledLines(int $cashAccountId, ?string $toDate = null, ?int $excludeReconciliationId = null): Collection
    {
        $cashAccount = CashAccount::findOrFail($cashAccountId);

        if (!$cashAccount->account_id) {
            return collect();
        }

        // Lines already in completed reconciliations (or other in-progress ones)
        $reconciledLineIdsQuery = BankReconciliationItem::query()
            ->join('bank_reconciliations', 'bank_reconciliations.id', '=', 'bank_reconciliation_items.bank_reconciliation_id')
            ->where('bank_reconciliations.cash_account_id', $cashAccountId)
            ->where('bank_reconciliations.status', '!=', BankReconciliationStatus::CANCELLED->value)
            ->whereNotNull('bank_reconciliation_items.journal_entry_line_id');

        if ($excludeReconciliationId) {
            $reconciledLineIdsQuery->where('bank_reconciliations.id', '!=', $excludeReconciliationId);
        }

        $reconciledLineIds = $reconciledLineIdsQuery->pluck('bank_reconciliation_items.journal_entry_line_id')->toArray();

        $query = JournalEntryLine::query()
            ->with(['entry.journal.translations'])
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
            ->where('journal_entry_lines.account_id', $cashAccount->account_id)
            ->where('journal_entries.status', JournalEntryStatus::POSTED->value)
            ->whereNull('journal_entries.deleted_at')
            ->select('journal_entry_lines.*');

        if ($toDate) {
            $query->whereDate('journal_entries.entry_date', '<=', $toDate);
        }

        if (!empty($reconciledLineIds)) {
            $query->whereNotIn('journal_entry_lines.id', $reconciledLineIds);
        }

        return $query->orderBy('journal_entries.entry_date')->orderBy('journal_entries.id')->get();
    }

    //--------------------------------------------------
    public function createReconciliation(array $data, array $items = []): BankReconciliation
    {
        return DB::transaction(function () use ($data, $items) {
            $reconciliation = BankReconciliation::create($data);

            foreach ($items as $item) {
                $reconciliation->items()->create([
                    'journal_entry_line_id' => $item['journal_entry_line_id'] ?? null,
                    'type'                  => $item['type'] ?? BankReconciliationItemType::MATCHED_ENTRY->value,
                    'statement_reference'   => $item['statement_reference'] ?? null,
                    'transaction_date'      => $item['transaction_date'],
                    'debit'                 => (float) ($item['debit'] ?? 0),
                    'credit'                => (float) ($item['credit'] ?? 0),
                    'is_cleared'            => (bool) ($item['is_cleared'] ?? true),
                    'notes'                 => $item['notes'] ?? null,
                    'created_by'            => auth()->id(),
                ]);
            }

            return $reconciliation->fresh(['items']);
        });
    }

    //--------------------------------------------------
    public function updateReconciliation(BankReconciliation $reconciliation, array $data, array $items = []): BankReconciliation
    {
        return DB::transaction(function () use ($reconciliation, $data, $items) {
            $reconciliation->update($data);

            // Sync items
            $reconciliation->items()->delete();

            foreach ($items as $item) {
                $reconciliation->items()->create([
                    'journal_entry_line_id' => $item['journal_entry_line_id'] ?? null,
                    'type'                  => $item['type'] ?? BankReconciliationItemType::MATCHED_ENTRY->value,
                    'statement_reference'   => $item['statement_reference'] ?? null,
                    'transaction_date'      => $item['transaction_date'],
                    'debit'                 => (float) ($item['debit'] ?? 0),
                    'credit'                => (float) ($item['credit'] ?? 0),
                    'is_cleared'            => (bool) ($item['is_cleared'] ?? true),
                    'notes'                 => $item['notes'] ?? null,
                    'created_by'            => auth()->id(),
                ]);
            }

            return $reconciliation->fresh(['items']);
        });
    }

    //--------------------------------------------------
    public function completeReconciliation(BankReconciliation $reconciliation, int $userId): BankReconciliation
    {
        $reconciliation->update([
            'status'        => BankReconciliationStatus::COMPLETED,
            'reconciled_at' => Carbon::now(),
            'reconciled_by' => $userId,
            'updated_by'    => $userId,
        ]);

        return $reconciliation->fresh();
    }

    //--------------------------------------------------
    public function reopenReconciliation(BankReconciliation $reconciliation): BankReconciliation
    {
        $reconciliation->update([
            'status'        => BankReconciliationStatus::DRAFT,
            'reconciled_at' => null,
            'reconciled_by' => null,
            'updated_by'    => auth()->id(),
        ]);

        return $reconciliation->fresh();
    }

    //--------------------------------------------------
    public function deleteReconciliation(BankReconciliation $reconciliation): bool
    {
        return DB::transaction(function () use ($reconciliation) {
            $reconciliation->items()->delete();
            return (bool) $reconciliation->delete();
        });
    }
}
