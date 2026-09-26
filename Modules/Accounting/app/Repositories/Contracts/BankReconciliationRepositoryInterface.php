<?php

namespace Modules\Accounting\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Modules\Accounting\Models\BankReconciliation;

interface BankReconciliationRepositoryInterface
{
    //--------------------------------------------------
    public function getDataTableQuery(array $filters = []): Builder;

    //--------------------------------------------------
    public function findById(int $id): ?BankReconciliation;

    //--------------------------------------------------
    public function getNextReconciliationNumber(int $entityId): string;

    //--------------------------------------------------
    public function calculateBookBalance(int $cashAccountId, string $asOfDate): float;

    //--------------------------------------------------
    public function getUnreconciledLines(int $cashAccountId, ?string $toDate = null, ?int $excludeReconciliationId = null): Collection;

    //--------------------------------------------------
    public function createReconciliation(array $data, array $items = []): BankReconciliation;

    //--------------------------------------------------
    public function updateReconciliation(BankReconciliation $reconciliation, array $data, array $items = []): BankReconciliation;

    //--------------------------------------------------
    public function completeReconciliation(BankReconciliation $reconciliation, int $userId): BankReconciliation;

    //--------------------------------------------------
    public function reopenReconciliation(BankReconciliation $reconciliation): BankReconciliation;

    //--------------------------------------------------
    public function deleteReconciliation(BankReconciliation $reconciliation): bool;
}
