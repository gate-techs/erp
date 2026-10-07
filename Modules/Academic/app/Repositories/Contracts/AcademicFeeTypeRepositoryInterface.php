<?php

namespace Modules\Academic\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Academic\Models\AcademicFeeType;

interface AcademicFeeTypeRepositoryInterface
{
    public function getFilteredQuery(array $filters = []): Builder;

    public function all(): Collection;

    public function find(int $id): ?AcademicFeeType;

    public function create(array $data): AcademicFeeType;

    public function update(int $id, array $data): ?AcademicFeeType;

    public function delete(int $id): bool;

    public function toggleStatus(int $id): ?AcademicFeeType;
}
