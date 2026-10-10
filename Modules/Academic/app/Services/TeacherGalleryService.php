<?php

namespace Modules\Academic\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Academic\Models\TeacherGallery;
use Modules\Academic\Repositories\Contracts\TeacherGalleryRepositoryInterface;

class TeacherGalleryService
{
    //------------------------------------------
    public function __construct(
        protected TeacherGalleryRepositoryInterface $repository
    ) {}

    //------------------------------------------
    public function getFilteredQuery(array $filters = []): Builder
    {
        return $this->repository->getFilteredQuery($filters);
    }

    //------------------------------------------
    public function find(int $id): ?TeacherGallery
    {
        return $this->repository->find($id);
    }

    //------------------------------------------
    public function createGallery(array $data, array $imageFiles = []): TeacherGallery
    {
        return $this->repository->createGallery($data, $imageFiles);
    }

    //------------------------------------------
    public function updateGallery(int $id, array $data, array $imageFiles = []): bool
    {
        return $this->repository->updateGallery($id, $data, $imageFiles);
    }

    //------------------------------------------
    public function deleteGallery(int $id): bool
    {
        return $this->repository->deleteGallery($id);
    }

    //------------------------------------------
    public function deleteImage(int $imageId): bool
    {
        return $this->repository->deleteImage($imageId);
    }

    //------------------------------------------
    public function getStudentGalleries(int $studentId): Collection
    {
        return $this->repository->getStudentGalleries($studentId);
    }

    //------------------------------------------
    public function getClassStudents(int $classroomId): Collection
    {
        return $this->repository->getClassStudents($classroomId);
    }

    //------------------------------------------
    public function getTeachers(): Collection
    {
        return $this->repository->getTeachers();
    }

    //------------------------------------------
    public function getClasses(): Collection
    {
        return $this->repository->getClasses();
    }
}
