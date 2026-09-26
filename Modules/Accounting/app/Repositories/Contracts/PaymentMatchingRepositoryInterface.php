<?php

namespace Modules\Accounting\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Modules\Accounting\Models\PaymentMatching;

interface PaymentMatchingRepositoryInterface
{
    //--------------------------------------------------
    public function getDataTableQuery(array $filters = []): Builder;

    //--------------------------------------------------
    public function getUnmatchedDebitLines(int $entityId, ?int $accountId = null, ?int $partnerId = null): Collection;

    //--------------------------------------------------
    public function getUnmatchedCreditLines(int $entityId, ?int $accountId = null, ?int $partnerId = null): Collection;

    //--------------------------------------------------
    public function matchLines(int $entityId, int $debitLineId, int $creditLineId, float $amount, ?string $notes = null, ?int $userId = null): PaymentMatching;

    //--------------------------------------------------
    public function unmatch(int $matchingId): bool;
}
