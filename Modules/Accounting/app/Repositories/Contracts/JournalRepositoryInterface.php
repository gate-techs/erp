<?php

namespace Modules\Accounting\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Modules\Accounting\Models\Journal;

interface JournalRepositoryInterface
{
    public function all(array $filters = []): Collection;

    public function find(int $id, array $with = []): ?Journal;

    public function create(array $data): Journal;

    public function update(int $id, array $data): Journal;

    public function delete(int $id): bool;

    public function getDataTableQuery(array $filters = []): Builder;

    public function toggleStatus(int $id): Journal;

    public function codeExistsInEntity(string $code, int $entityId, ?int $excludeId = null): bool;
}
