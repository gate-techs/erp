<?php

namespace Modules\Accounting\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Modules\Accounting\Enums\JournalEntryStatus;
use Modules\Accounting\Models\FiscalYear;
use Modules\Accounting\Models\JournalEntry;
use Modules\Accounting\Models\LockDate;
use Modules\Accounting\Models\LockException;
use Modules\Accounting\Repositories\Contracts\OpeningBalanceRepositoryInterface;

class OpeningBalanceService
{
    //--------------------------------------------------
    public function __construct(
        protected OpeningBalanceRepositoryInterface $repository
    ) {}

    //--------------------------------------------------
    public function getOpeningBalanceData(int $entityId, int $fiscalYearId): array
    {
        $accounts = $this->repository->getAccountsForEntity($entityId);
        $openingEntry = $this->repository->findOpeningEntry($entityId, $fiscalYearId);

        $linesByAccount = [];
        $totalDebit = 0;
        $totalCredit = 0;

        if ($openingEntry) {
            foreach ($openingEntry->lines as $line) {
                $linesByAccount[$line->account_id] = [
                    'debit' => (float) $line->debit,
                    'credit' => (float) $line->credit,
                    'notes' => $line->line_description,
                    'cost_center_id' => $line->cost_center_id,
                ];
                $totalDebit += (float) $line->debit;
                $totalCredit += (float) $line->credit;
            }
        }

        $difference = round($totalDebit - $totalCredit, 4);
        $isBalanced = abs($difference) < 0.0001 && $totalDebit > 0;

        return [
            'accounts' => $accounts,
            'openingEntry' => $openingEntry,
            'linesByAccount' => $linesByAccount,
            'totals' => [
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'difference' => $difference,
                'is_balanced' => $isBalanced,
                'status' => $openingEntry?->status->value ?? 'not_created',
                'entry_number' => $openingEntry?->entry_number,
                'entry_date' => $openingEntry?->entry_date?->format('Y-m-d'),
                'is_posted' => $openingEntry?->status === JournalEntryStatus::POSTED,
            ],
        ];
    }

    //--------------------------------------------------
    public function save(array $headerData, array $lines, bool $post = false): JournalEntry
    {
        $entityId = (int) $headerData['entity_id'];
        $fiscalYearId = (int) $headerData['fiscal_year_id'];
        $entryDate = $headerData['entry_date'] ?? now()->toDateString();
        $userId = auth()->id();

        // 1. Check Fiscal Year
        $fiscalYear = FiscalYear::find($fiscalYearId);
        if (!$fiscalYear || $fiscalYear->entity_id !== $entityId) {
            throw ValidationException::withMessages([
                'fiscal_year_id' => __('Selected fiscal year does not belong to the selected accounting entity.'),
            ]);
        }

        // 2. Validate Lock Dates
        $this->validateLockDates($entityId, $entryDate, $userId);

        // 3. Calculate Totals
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($lines as $line) {
            $totalDebit += (float) ($line['debit'] ?? 0);
            $totalCredit += (float) ($line['credit'] ?? 0);
        }

        $difference = round($totalDebit - $totalCredit, 4);
        $isBalanced = abs($difference) < 0.0001 && $totalDebit > 0;

        // If posting, balance is mandatory
        if ($post) {
            if ($totalDebit <= 0 && $totalCredit <= 0) {
                throw ValidationException::withMessages([
                    'balance' => __('Cannot post an empty opening entry. Please enter valid opening amounts.'),
                ]);
            }

            if (!$isBalanced) {
                throw ValidationException::withMessages([
                    'balance' => __('Opening entry is unbalanced. Total Debit must strictly equal Total Credit before posting. Difference: ') . number_format($difference, 4),
                ]);
            }
        }

        return $this->repository->saveOpeningEntry($headerData, $lines, $post);
    }

    //--------------------------------------------------
    public function reopen(int $entityId, int $fiscalYearId): JournalEntry
    {
        $entry = $this->repository->findOpeningEntry($entityId, $fiscalYearId);

        if (!$entry) {
            abort(404, __('Opening entry not found.'));
        }

        if ($entry->status !== JournalEntryStatus::POSTED) {
            throw ValidationException::withMessages([
                'entry' => __('Only posted opening entries can be reopened.'),
            ]);
        }

        $userId = auth()->id();
        $this->validateLockDates($entityId, $entry->entry_date->toDateString(), $userId);

        $entry->update([
            'status' => JournalEntryStatus::DRAFT,
            'posted_at' => null,
            'posted_by' => null,
            'updated_by' => $userId,
        ]);

        return $entry->fresh();
    }

    //--------------------------------------------------
    protected function validateLockDates(int $entityId, string $date, int $userId): void
    {
        $lockDate = LockDate::where('entity_id', $entityId)->first();
        if (!$lockDate) {
            return;
        }

        $targetDate = Carbon::parse($date)->startOfDay();

        // 1. Hard lock
        if ($lockDate->hard_lock_date) {
            $hardLock = Carbon::parse($lockDate->hard_lock_date)->startOfDay();
            if ($targetDate->lte($hardLock)) {
                throw ValidationException::withMessages([
                    'entry_date' => __('Cannot modify opening balances on or before the hard lock date: :date', ['date' => $hardLock->toDateString()]),
                ]);
            }
        }

        // 2. Active user exception check
        $hasException = LockException::where('entity_id', $entityId)
            ->where('user_id', $userId)
            ->where('is_revoked', false)
            ->where('expires_at', '>', Carbon::now())
            ->exists();

        if ($hasException) {
            return;
        }

        // 3. Fiscal year lock
        if ($lockDate->fiscal_year_lock_date) {
            $fyLock = Carbon::parse($lockDate->fiscal_year_lock_date)->startOfDay();
            if ($targetDate->lte($fyLock)) {
                throw ValidationException::withMessages([
                    'entry_date' => __('Cannot modify opening balances in a locked fiscal year period: :date', ['date' => $fyLock->toDateString()]),
                ]);
            }
        }
    }
}
