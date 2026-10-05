<?php

namespace Modules\Academic\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Academic\Models\AcademicYear;

interface AcademicYearRepositoryInterface
{
    public function getAll(array $filters = []): Collection;

    public function getDataTableQuery(array $filters = []): Builder;

    public function findById(int $id): ?AcademicYear;

    public function create(array $data): AcademicYear;

    public function update(AcademicYear $academicYear, array $data): AcademicYear;

    public function delete(AcademicYear $academicYear): bool;

    public function hasOverlap(
        int $institutionId,
        string $startDate,
        string $endDate,
        ?int $ignoreId = null
    ): bool;

    public function clearCurrentForInstitution(int $institutionId, ?int $ignoreId = null): void;
}
