<?php

namespace Modules\Accounting\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Accounting\Models\JournalEntry;

interface JournalEntryRepositoryInterface
{
    public function all(array $filters = []): Collection;

    public function find(int $id): ?JournalEntry;

    public function findWithDetails(int $id): ?JournalEntry;

    public function getDataTableQuery(array $filters = []): Builder;

    public function create(array $data, array $lines): JournalEntry;

    public function update(int $id, array $data, array $lines): JournalEntry;

    public function delete(int $id): bool;

    public function post(int $id, int $userId): JournalEntry;

    public function cancel(int $id, int $userId): JournalEntry;

    public function reverse(int $id, array $reversalData, int $userId): JournalEntry;

    public function generateNextEntryNumber(int $entityId, int $journalId, ?string $entryDate = null): string;

    public function getSummaryStats(array $filters = []): array;
}
