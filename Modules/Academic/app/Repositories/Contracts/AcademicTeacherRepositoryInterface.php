<?php

namespace Modules\Academic\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\HR\Models\Employee;

interface AcademicTeacherRepositoryInterface
{
    //------------------------------------------
    public function getFilteredQuery(array $filters = []): Builder;

    //------------------------------------------
    public function find(int $id): ?Employee;

    //------------------------------------------
    public function createTeacher(array $data): Employee;

    //------------------------------------------
    public function updateTeacher(int $id, array $data): bool;

    //------------------------------------------
    public function deleteTeacher(int $id): bool;

    //------------------------------------------
    public function toggleStatus(int $id): ?Employee;

    //------------------------------------------
    public function getAllocationData(int $id): array;

    //------------------------------------------
    public function saveAllocation(int $id, array $classes, array $subjects): bool;

    //------------------------------------------
    public function getInstitutions(): Collection;

    //------------------------------------------
    public function getBranches(): Collection;

    //------------------------------------------
    public function getJobTitles(): Collection;

    //------------------------------------------
    public function getManagers(): Collection;
}
