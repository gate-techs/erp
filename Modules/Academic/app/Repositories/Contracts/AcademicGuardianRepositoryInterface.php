<?php

namespace Modules\Academic\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Academic\Models\AcademicGuardian;

interface AcademicGuardianRepositoryInterface
{
    public function getFilteredQuery(array $filters = []): Builder;

    public function all(): Collection;

    public function find(int $id): ?AcademicGuardian;

    public function findByUserId(int $userId): ?AcademicGuardian;

    public function findByPhone(string $phone): ?AcademicGuardian;

    public function create(array $data): AcademicGuardian;

    public function update(int $id, array $data): ?AcademicGuardian;

    public function delete(int $id): bool;

    public function toggleStatus(int $id): ?AcademicGuardian;

    public function generateGuardianCode(): string;
}
