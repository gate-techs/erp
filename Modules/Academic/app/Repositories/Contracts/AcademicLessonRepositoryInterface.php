<?php

namespace Modules\Academic\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Academic\Models\AcademicLesson;

interface AcademicLessonRepositoryInterface
{
    public function getAll(array $filters = []): Collection;

    public function getDataTableQuery(array $filters = []): Builder;

    public function findById(int $id): ?AcademicLesson;

    public function create(array $data): AcademicLesson;

    public function update(AcademicLesson $academicLesson, array $data): AcademicLesson;

    public function delete(AcademicLesson $academicLesson): bool;
}
