<?php

namespace Modules\Accounting\Repositories;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Accounting\Enums\JournalEntryStatus;
use Modules\Accounting\Enums\JournalEntryType;
use Modules\Accounting\Enums\JournalType;
use Modules\Accounting\Models\Account;
use Modules\Accounting\Models\Entity;
use Modules\Accounting\Models\FiscalYear;
use Modules\Accounting\Models\Journal;
use Modules\Accounting\Models\JournalEntry;
use Modules\Accounting\Models\JournalEntryLine;
use Modules\Accounting\Repositories\Contracts\OpeningBalanceRepositoryInterface;

class OpeningBalanceRepository implements OpeningBalanceRepositoryInterface
{
    //--------------------------------------------------
    public function getAccountsForEntity(int $entityId): Collection
    {
        return Account::query()
            ->with(['translations', 'parent.translations'])
            ->where('entity_id', $entityId)
            ->where('is_group', false)
            ->where('is_deprecated', false)
            ->where('is_active', true)
            ->orderBy('code')
            ->get();
    }

    //--------------------------------------------------
    public function findOpeningEntry(int $entityId, int $fiscalYearId): ?JournalEntry
    {
        return JournalEntry::query()
            ->with([
                'entity.translations',
                'fiscalYear.translations',
                'journal.translations',
                'lines.account.translations',
                'lines.costCenter.translations',
                'postedBy',
                'creator',
            ])
            ->where('entity_id', $entityId)
            ->where('fiscal_year_id', $fiscalYearId)
            ->where('entry_type', JournalEntryType::OPENING)
            ->first();
    }

    //--------------------------------------------------
    public function getGeneralJournal(int $entityId): ?Journal
    {
        return Journal::query()
            ->where('entity_id', $entityId)
            ->where('type', JournalType::GENERAL)
            ->where('is_active', true)
            ->first()
            ?? Journal::query()
                ->where('entity_id', $entityId)
                ->where('is_active', true)
                ->first();
    }

    //--------------------------------------------------
    public function saveOpeningEntry(array $headerData, array $lines, bool $post = false): JournalEntry
    {
        return DB::transaction(function () use ($headerData, $lines, $post) {
            $entityId = (int) $headerData['entity_id'];
            $fiscalYearId = (int) $headerData['fiscal_year_id'];
            $userId = auth()->id();

            $existingEntry = $this->findOpeningEntry($entityId, $fiscalYearId);

            $journal = !empty($headerData['journal_id'])
                ? Journal::find($headerData['journal_id'])
                : $this->getGeneralJournal($entityId);

            $journalId = $journal?->id;
            $entity = Entity::find($entityId);
            $currency = $entity?->currency ?? 'SAR';

            $totalDebit = 0;
            $totalCredit = 0;

            foreach ($lines as $line) {
                $totalDebit += (float) ($line['debit'] ?? 0);
                $totalCredit += (float) ($line['credit'] ?? 0);
            }

            $isBalanced = (abs($totalDebit - $totalCredit) < 0.0001) && ($totalDebit > 0);

            if ($existingEntry) {
                $entry = $existingEntry;

                // Update entry
                $entry->update([
                    'journal_id' => $journalId,
                    'entry_date' => $headerData['entry_date'] ?? $entry->entry_date,
                    'accounting_date' => $headerData['entry_date'] ?? $entry->accounting_date,
                    'narration' => $headerData['narration'] ?? __('Opening balances for fiscal year'),
                    'currency' => $currency,
                    'total_debit' => $totalDebit,
                    'total_credit' => $totalCredit,
                    'is_balanced' => $isBalanced,
                    'updated_by' => $userId,
                ]);

                // Clear existing lines to replace
                $entry->lines()->delete();
            } else {
                // Generate next sequence entry number
                $prefix = $journal?->sequence_prefix ?: 'OP';
                $nextSeq = ($journal?->sequence_number_next ?? 1);
                $entryNumber = sprintf('%s-%s-%05d', $prefix, date('Y'), $nextSeq);

                if ($journal) {
                    $journal->increment('sequence_number_next');
                }

                $entry = JournalEntry::create([
                    'entity_id' => $entityId,
                    'fiscal_year_id' => $fiscalYearId,
                    'journal_id' => $journalId,
                    'entry_number' => $entryNumber,
                    'entry_date' => $headerData['entry_date'],
                    'accounting_date' => $headerData['entry_date'],
                    'reference' => $headerData['reference'] ?? 'OPENING-BALANCE',
                    'entry_type' => JournalEntryType::OPENING,
                    'currency' => $currency,
                    'exchange_rate' => 1.0,
                    'status' => JournalEntryStatus::DRAFT,
                    'total_debit' => $totalDebit,
                    'total_credit' => $totalCredit,
                    'is_balanced' => $isBalanced,
                    'narration' => $headerData['narration'] ?? __('Opening balances for fiscal year'),
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]);
            }

            // Insert lines
            $lineIndex = 1;
            foreach ($lines as $line) {
                $debit = (float) ($line['debit'] ?? 0);
                $credit = (float) ($line['credit'] ?? 0);

                if ($debit == 0 && $credit == 0) {
                    continue;
                }

                JournalEntryLine::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => (int) $line['account_id'],
                    'partner_id' => !empty($line['partner_id']) ? (int) $line['partner_id'] : null,
                    'cost_center_id' => !empty($line['cost_center_id']) ? (int) $line['cost_center_id'] : null,
                    'currency' => $currency,
                    'exchange_rate' => 1.0,
                    'debit' => $debit,
                    'credit' => $credit,
                    'amount_currency' => $debit > 0 ? $debit : -$credit,
                    'line_description' => $line['notes'] ?? __('Opening Balance Line'),
                    'line_order' => $lineIndex++,
                ]);
            }

            // Post if requested and balanced
            if ($post && $isBalanced) {
                $entry->update([
                    'status' => JournalEntryStatus::POSTED,
                    'posted_at' => Carbon::now(),
                    'posted_by' => $userId,
                ]);
            }

            return $this->findOpeningEntry($entityId, $fiscalYearId);
        });
    }
}
