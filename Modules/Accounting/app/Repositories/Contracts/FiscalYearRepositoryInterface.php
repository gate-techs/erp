<?php

namespace Modules\Accounting\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Accounting\Models\FiscalYear;

interface FiscalYearRepositoryInterface
{
    //===============================
    public function getAll(array $filters = []): Collection;

    //===============================
    public function getDataTableQuery(array $filters = []): Builder;

    //===============================
    public function findById(int $id, array $with = ['translations', 'entity.translations', 'creator', 'closer']): ?FiscalYear;

    //===============================
    public function create(array $data): FiscalYear;

    //===============================
    public function update(int|FiscalYear $model, array $data): FiscalYear;

    //===============================
    public function delete(int|FiscalYear $model): bool;

    //===============================
    public function hasOverlap(int $entityId, string $startDate, string $endDate, ?int $ignoreId = null): bool;
}
