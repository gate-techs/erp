<?php

namespace Modules\Academic\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Academic\Models\AcademicStage;

interface AcademicStageRepositoryInterface
{
    public function getAll(array $filters = []): Collection;

    public function getDataTableQuery(array $filters = []): Builder;

    public function findById(int $id): ?AcademicStage;

    public function create(array $data): AcademicStage;

    public function update(AcademicStage $academicStage, array $data): AcademicStage;

    public function delete(AcademicStage $academicStage): bool;
}
