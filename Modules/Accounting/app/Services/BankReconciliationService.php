<?php

namespace Modules\Accounting\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;
use Modules\Accounting\Enums\BankReconciliationItemType;
use Modules\Accounting\Enums\BankReconciliationStatus;
use Modules\Accounting\Models\BankReconciliation;
use Modules\Accounting\Models\CashAccount;
use Modules\Accounting\Repositories\Contracts\BankReconciliationRepositoryInterface;

class BankReconciliationService
{
    //--------------------------------------------------
    public function __construct(
        protected BankReconciliationRepositoryInterface $repository
    ) {}

    //--------------------------------------------------
    public function getDataTableQuery(array $filters = []): Builder
    {
        return $this->repository->getDataTableQuery($filters);
    }

    //--------------------------------------------------
    public function findById(int $id): ?BankReconciliation
    {
        return $this->repository->findById($id);
    }

    //--------------------------------------------------
    public function getWorkspaceData(int $cashAccountId, string $statementDate, float $statementBalance, ?int $reconciliationId = null): array
    {
        $cashAccount = CashAccount::with(['entity.translations', 'account.translations', 'journal.translations'])->findOrFail($cashAccountId);

        $bookBalance = $this->repository->calculateBookBalance($cashAccountId, $statementDate);
        $unreconciledLines = $this->repository->getUnreconciledLines($cashAccountId, $statementDate, $reconciliationId);

        $existingReconciliation = $reconciliationId ? $this->repository->findById($reconciliationId) : null;

        $clearedLineIds = [];
        $manualItems = [];

        if ($existingReconciliation) {
            foreach ($existingReconciliation->items as $item) {
                if ($item->journal_entry_line_id) {
                    $clearedLineIds[] = $item->journal_entry_line_id;
                } else {
                    $manualItems[] = $item;
                }
            }
        }

        return [
            'cashAccount'            => $cashAccount,
            'bookBalance'            => $bookBalance,
            'statementDate'          => $statementDate,
            'statementBalance'       => $statementBalance,
            'unreconciledLines'      => $unreconciledLines,
            'existingReconciliation' => $existingReconciliation,
            'clearedLineIds'         => $clearedLineIds,
            'manualItems'            => $manualItems,
        ];
    }

    //--------------------------------------------------
    public function saveReconciliation(array $data, bool $complete = false): BankReconciliation
    {
        $cashAccountId = (int) $data['cash_account_id'];
        $cashAccount = CashAccount::findOrFail($cashAccountId);
        $entityId = $cashAccount->entity_id;
        $statementDate = $data['statement_date'];
        $statementBalance = (float) $data['statement_balance'];
        $bookBalance = $this->repository->calculateBookBalance($cashAccountId, $statementDate);

        $items = $data['items'] ?? [];
        $totalClearedDebit = 0.0;
        $totalClearedCredit = 0.0;

        foreach ($items as $item) {
            $totalClearedDebit += (float) ($item['debit'] ?? 0);
            $totalClearedCredit += (float) ($item['credit'] ?? 0);
        }

        // Reconciled balance = Book balance + adjustments / or cleared transactions
        $reconciledBalance = round($totalClearedDebit - $totalClearedCredit, 4);
        $difference = round($statementBalance - $reconciledBalance, 4);

        if ($complete && abs($difference) > 0.0001) {
            throw ValidationException::withMessages([
                'difference' => __('Cannot complete bank reconciliation when difference is not zero. Current difference: ') . number_format($difference, 4),
            ]);
        }

        $userId = auth()->id();
        $reconciliationId = $data['id'] ?? null;

        if ($reconciliationId) {
            $reconciliation = $this->repository->findById($reconciliationId);
            if (!$reconciliation) {
                abort(404, __('Bank reconciliation not found.'));
            }

            if ($reconciliation->isCompleted()) {
                throw ValidationException::withMessages([
                    'status' => __('Completed bank reconciliations cannot be modified.'),
                ]);
            }

            $updateData = [
                'statement_date'     => $statementDate,
                'statement_balance'  => $statementBalance,
                'book_balance'       => $bookBalance,
                'reconciled_balance' => $reconciledBalance,
                'difference'         => $difference,
                'notes'              => $data['notes'] ?? null,
                'updated_by'         => $userId,
            ];

            $reconciliation = $this->repository->updateReconciliation($reconciliation, $updateData, $items);
        } else {
            $reconciliationNumber = $this->repository->getNextReconciliationNumber($entityId);

            $createData = [
                'entity_id'             => $entityId,
                'cash_account_id'       => $cashAccountId,
                'reconciliation_number' => $reconciliationNumber,
                'statement_date'        => $statementDate,
                'statement_balance'     => $statementBalance,
                'book_balance'          => $bookBalance,
                'reconciled_balance'    => $reconciledBalance,
                'difference'            => $difference,
                'status'                => BankReconciliationStatus::DRAFT,
                'notes'                 => $data['notes'] ?? null,
                'created_by'            => $userId,
                'updated_by'            => $userId,
            ];

            $reconciliation = $this->repository->createReconciliation($createData, $items);
        }

        if ($complete) {
            $reconciliation = $this->repository->completeReconciliation($reconciliation, $userId);
        }

        return $reconciliation;
    }

    //--------------------------------------------------
    public function reopenReconciliation(int $id): BankReconciliation
    {
        $reconciliation = $this->repository->findById($id);

        if (!$reconciliation) {
            abort(404, __('Bank reconciliation not found.'));
        }

        return $this->repository->reopenReconciliation($reconciliation);
    }

    //--------------------------------------------------
    public function deleteReconciliation(int $id): bool
    {
        $reconciliation = $this->repository->findById($id);

        if (!$reconciliation) {
            abort(404, __('Bank reconciliation not found.'));
        }

        if ($reconciliation->isCompleted()) {
            throw ValidationException::withMessages([
                'status' => __('Completed bank reconciliations cannot be deleted.'),
            ]);
        }

        return $this->repository->deleteReconciliation($reconciliation);
    }
}
