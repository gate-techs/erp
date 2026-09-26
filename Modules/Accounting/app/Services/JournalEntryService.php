<?php

namespace Modules\Accounting\Services;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Modules\Accounting\Enums\JournalEntryStatus;
use Modules\Accounting\Models\Account;
use Modules\Accounting\Models\Journal;
use Modules\Accounting\Models\JournalEntry;
use Modules\Accounting\Models\LockDate;
use Modules\Accounting\Models\LockException;
use Modules\Accounting\Repositories\Contracts\JournalEntryRepositoryInterface;

class JournalEntryService
{
    public function __construct(
        protected JournalEntryRepositoryInterface $repository
    ) {}

    public function getAll(array $filters = []): Collection
    {
        return $this->repository->all($filters);
    }

    public function getById(int $id): JournalEntry
    {
        $entry = $this->repository->findWithDetails($id);
        if (!$entry) {
            abort(404, __('Journal entry not found.'));
        }
        return $entry;
    }

    public function getDataTableQuery(array $filters = []): Builder
    {
        return $this->repository->getDataTableQuery($filters);
    }

    public function create(array $data, array $lines, ?int $userId = null): JournalEntry
    {
        $userId = $userId ?? auth()->id();
        $entityId = (int) $data['entity_id'];
        $journalId = (int) $data['journal_id'];
        $entryDate = $data['entry_date'] ?? now()->toDateString();

        // 1. Business validations
        $this->validateJournalBelongsToEntity($journalId, $entityId);
        $this->validateLockDates($entityId, $entryDate, $userId);
        $this->validateLinesIntegrity($lines, $entityId);

        $data['created_by'] = $userId;
        $data['updated_by'] = $userId;

        $entry = $this->repository->create($data, $lines);

        // Auto post if requested
        if (!empty($data['post_immediately']) && $entry->canBePosted()) {
            return $this->repository->post($entry->id, $userId);
        }

        return $entry;
    }

    public function update(int $id, array $data, array $lines, ?int $userId = null): JournalEntry
    {
        $userId = $userId ?? auth()->id();
        $entry = $this->getById($id);

        if (!$entry->canBeEdited()) {
            throw ValidationException::withMessages([
                'status' => [__('Only draft journal entries can be modified.')],
            ]);
        }

        $entityId = (int) ($data['entity_id'] ?? $entry->entity_id);
        $journalId = (int) ($data['journal_id'] ?? $entry->journal_id);
        $entryDate = $data['entry_date'] ?? $entry->entry_date->toDateString();

        // Validations
        $this->validateJournalBelongsToEntity($journalId, $entityId);
        $this->validateLockDates($entityId, $entryDate, $userId);
        $this->validateLinesIntegrity($lines, $entityId);

        $data['updated_by'] = $userId;

        $updatedEntry = $this->repository->update($id, $data, $lines);

        if (!empty($data['post_immediately']) && $updatedEntry->canBePosted()) {
            return $this->repository->post($updatedEntry->id, $userId);
        }

        return $updatedEntry;
    }

    public function delete(int $id): bool
    {
        $entry = $this->getById($id);

        if (!$entry->canBeEdited()) {
            throw ValidationException::withMessages([
                'status' => [__('Posted journal entries cannot be deleted. You must reverse them instead.')],
            ]);
        }

        return $this->repository->delete($id);
    }

    public function post(int $id, ?int $userId = null): JournalEntry
    {
        $userId = $userId ?? auth()->id();
        $entry = $this->getById($id);

        if (!$entry->isDraft()) {
            throw ValidationException::withMessages([
                'status' => [__('This journal entry has already been posted or cancelled.')],
            ]);
        }

        if (!$entry->is_balanced) {
            throw ValidationException::withMessages([
                'balance' => [__('Cannot post an unbalanced entry. Total debit must equal total credit.')],
            ]);
        }

        if ((float) $entry->total_debit <= 0) {
            throw ValidationException::withMessages([
                'total_debit' => [__('Cannot post a journal entry with zero amount.')],
            ]);
        }

        if ($entry->lines->count() < 2) {
            throw ValidationException::withMessages([
                'lines' => [__('A journal entry must have at least two balanced lines to be posted.')],
            ]);
        }

        $this->validateLockDates($entry->entity_id, $entry->entry_date->toDateString(), $userId);

        return $this->repository->post($id, $userId);
    }

    public function cancel(int $id, ?int $userId = null): JournalEntry
    {
        $userId = $userId ?? auth()->id();
        $entry = $this->getById($id);

        if (!$entry->canBeCancelled()) {
            throw ValidationException::withMessages([
                'status' => [__('Only draft entries can be cancelled.')],
            ]);
        }

        return $this->repository->cancel($id, $userId);
    }

    public function reverse(int $id, array $reversalData, ?int $userId = null): JournalEntry
    {
        $userId = $userId ?? auth()->id();
        $entry = $this->getById($id);

        if (!$entry->canBeReversed()) {
            throw ValidationException::withMessages([
                'status' => [__('This entry cannot be reversed (either not posted, already reversed, or is itself a reversal).')],
            ]);
        }

        $reversalDate = $reversalData['entry_date'] ?? now()->toDateString();
        $this->validateLockDates($entry->entity_id, $reversalDate, $userId);

        return $this->repository->reverse($id, $reversalData, $userId);
    }

    public function getSummaryStats(array $filters = []): array
    {
        return $this->repository->getSummaryStats($filters);
    }

    //--------------------------------------------------
    // Validation Rules
    //--------------------------------------------------

    protected function validateJournalBelongsToEntity(int $journalId, int $entityId): void
    {
        $journal = Journal::find($journalId);
        if (!$journal || $journal->entity_id !== $entityId) {
            throw ValidationException::withMessages([
                'journal_id' => [__('Selected journal does not belong to the chosen accounting entity.')],
            ]);
        }

        if (!$journal->is_active) {
            throw ValidationException::withMessages([
                'journal_id' => [__('The selected journal is inactive.')],
            ]);
        }
    }

    protected function validateLinesIntegrity(array $lines, int $entityId): void
    {
        if (empty($lines) || count($lines) < 2) {
            throw ValidationException::withMessages([
                'lines' => [__('A journal entry requires at least two lines (double-entry requirement).')],
            ]);
        }

        $totalDebit = 0.0000;
        $totalCredit = 0.0000;
        $accountIds = [];

        foreach ($lines as $index => $line) {
            $debit = (float) ($line['debit'] ?? 0);
            $credit = (float) ($line['credit'] ?? 0);
            $accountId = (int) ($line['account_id'] ?? 0);

            if (!$accountId) {
                throw ValidationException::withMessages([
                    "lines.{$index}.account_id" => [__('Every line must have an account selected.')],
                ]);
            }

            if ($debit < 0 || $credit < 0) {
                throw ValidationException::withMessages([
                    "lines.{$index}" => [__('Debit and credit amounts cannot be negative.')],
                ]);
            }

            if ($debit > 0 && $credit > 0) {
                throw ValidationException::withMessages([
                    "lines.{$index}" => [__('A single line cannot have both debit and credit amounts.')],
                ]);
            }

            if ($debit <= 0 && $credit <= 0) {
                throw ValidationException::withMessages([
                    "lines.{$index}" => [__('Line amount must be greater than zero in either debit or credit.')],
                ]);
            }

            $totalDebit += $debit;
            $totalCredit += $credit;
            $accountIds[] = $accountId;
        }

        // Check balance
        if (abs(round($totalDebit, 4) - round($totalCredit, 4)) >= 0.0001) {
            throw ValidationException::withMessages([
                'balance' => [__('Total Debit (:debit) must equal Total Credit (:credit).', [
                    'debit' => number_format($totalDebit, 3),
                    'credit' => number_format($totalCredit, 3),
                ])],
            ]);
        }

        // Validate all accounts exist and belong to entity
        $uniqueAccountIds = array_unique($accountIds);
        $validAccounts = Account::where('entity_id', $entityId)
            ->whereIn('id', $uniqueAccountIds)
            ->where('is_group', false)
            ->where('is_active', true)
            ->count();

        if ($validAccounts !== count($uniqueAccountIds)) {
            throw ValidationException::withMessages([
                'accounts' => [__('All accounts must be active posting accounts belonging to the selected entity.')],
            ]);
        }
    }

    protected function validateLockDates(int $entityId, string $dateString, int $userId): void
    {
        $lockDate = LockDate::where('entity_id', $entityId)->first();
        if (!$lockDate) {
            return;
        }

        $entryDate = Carbon::parse($dateString);

        // 1. Hard Lock Date (applies to all users without exception)
        if ($lockDate->hard_lock_date && $entryDate->lte($lockDate->hard_lock_date)) {
            throw ValidationException::withMessages([
                'entry_date' => [__('Accounting transactions on or before :date are permanently locked for all users.', [
                    'date' => $lockDate->hard_lock_date->format('Y-m-d'),
                ])],
            ]);
        }

        // 2. Fiscal Year Lock Date
        if ($lockDate->fiscal_year_lock_date && $entryDate->lte($lockDate->fiscal_year_lock_date)) {
            if (!$this->hasActiveLockException($entityId, $userId, 'fiscal_year', $entryDate)) {
                throw ValidationException::withMessages([
                    'entry_date' => [__('Fiscal year transactions on or before :date are locked.', [
                        'date' => $lockDate->fiscal_year_lock_date->format('Y-m-d'),
                    ])],
                ]);
            }
        }

        // 3. Lock All Date (for non-advisers)
        if ($lockDate->lock_all_date && $entryDate->lte($lockDate->lock_all_date)) {
            if (!$this->hasActiveLockException($entityId, $userId, 'lock_all', $entryDate)) {
                throw ValidationException::withMessages([
                    'entry_date' => [__('Transactions on or before :date are locked for general users.', [
                        'date' => $lockDate->lock_all_date->format('Y-m-d'),
                    ])],
                ]);
            }
        }
    }

    protected function hasActiveLockException(int $entityId, int $userId, string $lockType, Carbon $entryDate): bool
    {
        return LockException::where('entity_id', $entityId)
            ->where('user_id', $userId)
            ->where(function ($q) use ($lockType) {
                $q->where('lock_type', $lockType)
                    ->orWhere('lock_type', 'lock_all');
            })
            ->where('valid_from', '<=', now()->toDateString())
            ->where('valid_until', '>=', now()->toDateString())
            ->whereNull('revoked_at')
            ->exists();
    }
}
