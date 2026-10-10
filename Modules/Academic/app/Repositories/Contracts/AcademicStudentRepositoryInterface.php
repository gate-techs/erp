<?php

namespace Modules\Academic\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Academic\Models\AcademicStudent;

interface AcademicStudentRepositoryInterface
{
    //------------------------------------------
    public function getFilteredQuery(array $filters = []): Builder;

    //------------------------------------------
    public function all(): Collection;

    //------------------------------------------
    public function find(int $id): ?AcademicStudent;

    //------------------------------------------
    public function findWithAllRelations(int $id): ?AcademicStudent;

    //------------------------------------------
    public function findByCode(string $code): ?AcademicStudent;

    //------------------------------------------
    public function findByCivilId(string $civilId): ?AcademicStudent;

    //------------------------------------------
    public function create(array $data): AcademicStudent;

    //------------------------------------------
    public function update(int $id, array $data): ?AcademicStudent;

    //------------------------------------------
    public function delete(int $id): bool;

    //------------------------------------------
    public function toggleStatus(int $id): ?AcademicStudent;

    //------------------------------------------
    public function generateStudentCode(): string;
}
