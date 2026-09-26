<?php

namespace Modules\Accounting\Repositories\Contracts;

use Illuminate\Support\Collection;
use Modules\Accounting\Models\JournalEntry;

interface OpeningBalanceRepositoryInterface
{
    //--------------------------------------------------
    public function getAccountsForEntity(int $entityId): Collection;

    //--------------------------------------------------
    public function findOpeningEntry(int $entityId, int $fiscalYearId): ?JournalEntry;

    //--------------------------------------------------
    public function saveOpeningEntry(array $headerData, array $lines, bool $post = false): JournalEntry;

    //--------------------------------------------------
    public function getGeneralJournal(int $entityId): ?\Modules\Accounting\Models\Journal;
}
