<?php

namespace Modules\HR\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\HR\Models\Payroll;
use Modules\HR\Models\PayrollItem;

interface PayrollRepositoryInterface
{
    public function getQuery(): Builder;

    public function all(array $columns = ['*']): Collection;

    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator;

    public function find(int $id): ?Payroll;

    public function findWithDetails(int $id): ?Payroll;

    public function create(array $data): Payroll;

    public function update(Payroll $payroll, array $data): bool;

    public function delete(Payroll $payroll): bool;

    public function findItem(int $itemId): ?PayrollItem;

    public function updateItem(PayrollItem $item, array $data): bool;

    public function deleteItem(PayrollItem $item): bool;

    public function getKpis(?string $month = null, ?int $branchId = null): array;
}
