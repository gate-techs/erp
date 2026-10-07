<?php

namespace Modules\Academic\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Modules\Academic\Models\AcademicMonthlyPlan;
use Modules\Academic\Models\AcademicMonthlyPlanAttachment;

interface AcademicMonthlyPlanRepositoryInterface
{
    public function getFilteredQuery(array $filters = []): Builder;

    public function all(array $filters = []): Collection;

    public function findById(int $id, array $with = []): ?AcademicMonthlyPlan;

    public function create(array $data): AcademicMonthlyPlan;

    public function update(int $id, array $data): AcademicMonthlyPlan;

    public function delete(int $id): bool;

    public function toggleStatus(int $id): AcademicMonthlyPlan;

    public function addAttachment(int $planId, array $data): AcademicMonthlyPlanAttachment;

    public function deleteAttachment(int $attachmentId): bool;

    public function findAttachment(int $attachmentId): ?AcademicMonthlyPlanAttachment;
}
