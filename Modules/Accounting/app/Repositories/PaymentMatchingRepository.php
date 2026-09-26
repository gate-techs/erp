<?php

namespace Modules\Accounting\Repositories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Accounting\Enums\JournalEntryStatus;
use Modules\Accounting\Models\JournalEntryLine;
use Modules\Accounting\Models\PaymentMatching;
use Modules\Accounting\Repositories\Contracts\PaymentMatchingRepositoryInterface;

class PaymentMatchingRepository implements PaymentMatchingRepositoryInterface
{
    //--------------------------------------------------
    public function getDataTableQuery(array $filters = []): Builder
    {
        $query = PaymentMatching::query()
            ->with([
                'entity.translations',
                'debitLine.account.translations',
                'debitLine.entry.journal.translations',
                'creditLine.account.translations',
                'creditLine.entry.journal.translations',
                'matchedBy',
            ]);

        if (!empty($filters['entity_id'])) {
            $query->where('entity_id', (int) $filters['entity_id']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('matched_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('matched_at', '<=', $filters['date_to']);
        }

        return $query->orderByDesc('matched_at')->orderByDesc('id');
    }

    //--------------------------------------------------
    public function getUnmatchedDebitLines(int $entityId, ?int $accountId = null, ?int $partnerId = null): Collection
    {
        $query = JournalEntryLine::query()
            ->with(['account.translations', 'entry.journal.translations'])
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
            ->where('journal_entries.entity_id', $entityId)
            ->where('journal_entries.status', JournalEntryStatus::POSTED->value)
            ->whereNull('journal_entries.deleted_at')
            ->where('journal_entry_lines.debit', '>', 0);

        if ($accountId) {
            $query->where('journal_entry_lines.account_id', $accountId);
        }

        if ($partnerId) {
            $query->where('journal_entry_lines.partner_id', $partnerId);
        }

        // Calculate matched amount so far
        $lines = $query->select('journal_entry_lines.*')->orderBy('journal_entries.entry_date')->get();

        return $lines->map(function ($line) {
            $matchedAmount = (float) PaymentMatching::where('debit_line_id', $line->id)->sum('matched_amount');
            $remaining = round((float) $line->debit - $matchedAmount, 4);

            $line->matched_amount = $matchedAmount;
            $line->remaining_amount = $remaining;

            return $line;
        })->filter(function ($line) {
            return $line->remaining_amount > 0.0001;
        })->values();
    }

    //--------------------------------------------------
    public function getUnmatchedCreditLines(int $entityId, ?int $accountId = null, ?int $partnerId = null): Collection
    {
        $query = JournalEntryLine::query()
            ->with(['account.translations', 'entry.journal.translations'])
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
            ->where('journal_entries.entity_id', $entityId)
            ->where('journal_entries.status', JournalEntryStatus::POSTED->value)
            ->whereNull('journal_entries.deleted_at')
            ->where('journal_entry_lines.credit', '>', 0);

        if ($accountId) {
            $query->where('journal_entry_lines.account_id', $accountId);
        }

        if ($partnerId) {
            $query->where('journal_entry_lines.partner_id', $partnerId);
        }

        $lines = $query->select('journal_entry_lines.*')->orderBy('journal_entries.entry_date')->get();

        return $lines->map(function ($line) {
            $matchedAmount = (float) PaymentMatching::where('credit_line_id', $line->id)->sum('matched_amount');
            $remaining = round((float) $line->credit - $matchedAmount, 4);

            $line->matched_amount = $matchedAmount;
            $line->remaining_amount = $remaining;

            return $line;
        })->filter(function ($line) {
            return $line->remaining_amount > 0.0001;
        })->values();
    }

    //--------------------------------------------------
    public function matchLines(int $entityId, int $debitLineId, int $creditLineId, float $amount, ?string $notes = null, ?int $userId = null): PaymentMatching
    {
        return PaymentMatching::create([
            'entity_id'      => $entityId,
            'debit_line_id'  => $debitLineId,
            'credit_line_id' => $creditLineId,
            'matched_amount' => $amount,
            'notes'          => $notes,
            'matched_by'     => $userId ?: auth()->id(),
            'matched_at'     => Carbon::now(),
        ]);
    }

    //--------------------------------------------------
    public function unmatch(int $matchingId): bool
    {
        $matching = PaymentMatching::findOrFail($matchingId);
        return (bool) $matching->delete();
    }
}
