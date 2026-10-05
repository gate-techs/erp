<?php

namespace Modules\Academic\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Academic\Models\AcademicClass;

interface AcademicClassRepositoryInterface
{
    public function getAll(array $filters = []): Collection;

    public function getDataTableQuery(array $filters = []): Builder;

    public function findById(int $id): ?AcademicClass;

    public function create(array $data): AcademicClass;

    public function update(AcademicClass $academicClass, array $data): AcademicClass;

    public function delete(AcademicClass $academicClass): bool;

    public function hasDuplicateName(
        int $institutionId,
        int $branchId,
        int $academicYearId,
        int $academicStageId,
        array $names,
        ?int $ignoreId = null
    ): bool;
}
