<?php

namespace Modules\Academic\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Academic\Models\AcademicWeeklyPlan;

interface AcademicWeeklyPlanRepositoryInterface
{
    public function getQuery(array $filters = []): Builder;

    public function all(array $filters = []): Collection;

    public function findById(int $id): ?AcademicWeeklyPlan;

    public function create(array $data): AcademicWeeklyPlan;

    public function update(int $id, array $data): AcademicWeeklyPlan;

    public function delete(int $id): bool;

    public function toggleStatus(int $id): AcademicWeeklyPlan;
}
