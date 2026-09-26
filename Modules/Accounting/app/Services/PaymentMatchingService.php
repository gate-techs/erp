<?php

namespace Modules\Accounting\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;
use Modules\Accounting\Models\JournalEntryLine;
use Modules\Accounting\Models\PaymentMatching;
use Modules\Accounting\Repositories\Contracts\PaymentMatchingRepositoryInterface;

class PaymentMatchingService
{
    //--------------------------------------------------
    public function __construct(
        protected PaymentMatchingRepositoryInterface $repository
    ) {}

    //--------------------------------------------------
    public function getDataTableQuery(array $filters = []): Builder
    {
        return $this->repository->getDataTableQuery($filters);
    }

    //--------------------------------------------------
    public function getUnmatchedLines(int $entityId, ?int $accountId = null, ?int $partnerId = null): array
    {
        $debitLines = $this->repository->getUnmatchedDebitLines($entityId, $accountId, $partnerId);
        $creditLines = $this->repository->getUnmatchedCreditLines($entityId, $accountId, $partnerId);

        return [
            'debitLines'  => $debitLines,
            'creditLines' => $creditLines,
        ];
    }

    //--------------------------------------------------
    public function match(int $entityId, int $debitLineId, int $creditLineId, float $amount, ?string $notes = null): PaymentMatching
    {
        if ($amount <= 0) {
            throw ValidationException::withMessages([
                'amount' => __('Matched amount must be greater than zero.'),
            ]);
        }

        $debitLine = JournalEntryLine::findOrFail($debitLineId);
        $creditLine = JournalEntryLine::findOrFail($creditLineId);

        // Check remaining debit
        $matchedDebit = (float) PaymentMatching::where('debit_line_id', $debitLineId)->sum('matched_amount');
        $remainingDebit = round((float) $debitLine->debit - $matchedDebit, 4);

        if ($amount > ($remainingDebit + 0.0001)) {
            throw ValidationException::withMessages([
                'amount' => __('Matched amount exceeds remaining debit amount of: ') . number_format($remainingDebit, 4),
            ]);
        }

        // Check remaining credit
        $matchedCredit = (float) PaymentMatching::where('credit_line_id', $creditLineId)->sum('matched_amount');
        $remainingCredit = round((float) $creditLine->credit - $matchedCredit, 4);

        if ($amount > ($remainingCredit + 0.0001)) {
            throw ValidationException::withMessages([
                'amount' => __('Matched amount exceeds remaining credit amount of: ') . number_format($remainingCredit, 4),
            ]);
        }

        return $this->repository->matchLines($entityId, $debitLineId, $creditLineId, $amount, $notes, auth()->id());
    }

    //--------------------------------------------------
    public function unmatch(int $matchingId): bool
    {
        return $this->repository->unmatch($matchingId);
    }
}
