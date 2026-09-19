<?php

namespace Modules\HR\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Modules\HR\Models\Penalty;

interface PenaltyRepositoryInterface
{
    public function getDataTableQuery(array $filters = []): Builder;

    public function findById(int $id, array $with = []): ?Penalty;

    public function create(array $data): Penalty;

    public function update(Penalty $penalty, array $data): bool;

    public function delete(Penalty $penalty): bool;

    public function getKPIs(): array;
}
