<?php

namespace Modules\HR\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\HR\Models\ExitClearance;

interface ExitClearanceRepositoryInterface
{
    public function getQuery(array $filters = []): Builder;

    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function findById(int $id): ?ExitClearance;

    public function findByCode(string $code): ?ExitClearance;

    public function create(array $data): ExitClearance;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    public function generateClearanceCode(): string;

    public function createDefaultApprovals(ExitClearance $clearance, array $customChecklist = []): Collection;

    public function updateApproval(int $approvalId, array $data): bool;

    public function getKPIs(): array;
}
