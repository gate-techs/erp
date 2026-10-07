<?php

namespace Modules\Academic\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Academic\Models\AcademicFee;

interface AcademicFeeRepositoryInterface
{
    public function getFilteredQuery(array $filters = []): Builder;

    public function all(): Collection;

    public function find(int $id): ?AcademicFee;

    public function create(array $data): AcademicFee;

    public function update(int $id, array $data): ?AcademicFee;

    public function delete(int $id): bool;

    public function toggleStatus(int $id): ?AcademicFee;
}
