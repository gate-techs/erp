<?php

namespace Modules\Academic\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Academic\Models\AcademicSubject;

interface AcademicSubjectRepositoryInterface
{
    public function getAll(array $filters = []): Collection;

    public function getDataTableQuery(array $filters = []): Builder;

    public function findById(int $id): ?AcademicSubject;

    public function create(array $data): AcademicSubject;

    public function update(AcademicSubject $academicSubject, array $data): AcademicSubject;

    public function delete(AcademicSubject $academicSubject): bool;

    public function syncStages(AcademicSubject $academicSubject, array $stageIds): void;
}
