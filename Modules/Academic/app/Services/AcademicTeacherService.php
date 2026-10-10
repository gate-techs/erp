<?php

namespace Modules\Academic\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Academic\Repositories\Contracts\AcademicTeacherRepositoryInterface;
use Modules\HR\Models\Employee;

class AcademicTeacherService
{
    public function __construct(
        protected AcademicTeacherRepositoryInterface $repository
    ) {}

    //------------------------------------------
    public function getFilteredQuery(array $filters = []): Builder
    {
        return $this->repository->getFilteredQuery($filters);
    }

    //------------------------------------------
    public function find(int $id): ?Employee
    {
        return $this->repository->find($id);
    }

    //------------------------------------------
    public function createTeacher(array $data): Employee
    {
        return $this->repository->createTeacher($data);
    }

    //------------------------------------------
    public function updateTeacher(int $id, array $data): bool
    {
        return $this->repository->updateTeacher($id, $data);
    }

    //------------------------------------------
    public function deleteTeacher(int $id): bool
    {
        return $this->repository->deleteTeacher($id);
    }

    //------------------------------------------
    public function toggleStatus(int $id): ?Employee
    {
        return $this->repository->toggleStatus($id);
    }

    //------------------------------------------
    public function getAllocationData(int $id): array
    {
        return $this->repository->getAllocationData($id);
    }

    //------------------------------------------
    public function saveAllocation(int $id, array $classes, array $subjects): bool
    {
        return $this->repository->saveAllocation($id, $classes, $subjects);
    }

    //------------------------------------------
    public function getInstitutions(): Collection
    {
        return $this->repository->getInstitutions();
    }

    //------------------------------------------
    public function getBranches(): Collection
    {
        return $this->repository->getBranches();
    }

    //------------------------------------------
    public function getJobTitles(): Collection
    {
        return $this->repository->getJobTitles();
    }

    //------------------------------------------
    public function getManagers(): Collection
    {
        return $this->repository->getManagers();
    }
}
