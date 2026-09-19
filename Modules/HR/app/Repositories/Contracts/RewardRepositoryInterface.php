<?php

namespace Modules\HR\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Modules\HR\Models\Reward;

interface RewardRepositoryInterface
{
    public function getDataTableQuery(array $filters = []): Builder;

    public function findById(int $id, array $with = []): ?Reward;

    public function create(array $data): Reward;

    public function update(Reward $reward, array $data): bool;

    public function delete(Reward $reward): bool;

    public function getKPIs(): array;
}
