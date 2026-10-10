<?php

namespace Modules\Academic\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Academic\Models\TeacherGallery;

interface TeacherGalleryRepositoryInterface
{
    //------------------------------------------
    public function getFilteredQuery(array $filters = []): Builder;

    //------------------------------------------
    public function find(int $id): ?TeacherGallery;

    //------------------------------------------
    public function createGallery(array $data, array $imageFiles = []): TeacherGallery;

    //------------------------------------------
    public function updateGallery(int $id, array $data, array $imageFiles = []): bool;

    //------------------------------------------
    public function deleteGallery(int $id): bool;

    //------------------------------------------
    public function deleteImage(int $imageId): bool;

    //------------------------------------------
    public function getStudentGalleries(int $studentId): Collection;

    //------------------------------------------
    public function getClassStudents(int $classroomId): Collection;

    //------------------------------------------
    public function getTeachers(): Collection;

    //------------------------------------------
    public function getClasses(): Collection;
}
