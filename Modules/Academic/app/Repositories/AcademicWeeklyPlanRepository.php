<?php

namespace Modules\Academic\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Academic\Models\AcademicWeeklyPlan;
use Modules\Academic\Repositories\Contracts\AcademicWeeklyPlanRepositoryInterface;

class AcademicWeeklyPlanRepository implements AcademicWeeklyPlanRepositoryInterface
{
    public function getQuery(array $filters = []): Builder
    {
        $query = AcademicWeeklyPlan::query()
            ->with([
                'translations',
                'institution.translations',
                'branch.translations',
                'academicStage.translations',
                'academicClass.translations',
                'attachments',
            ])
            ->withCount('attachments')
            ->latest('id');

        if (!empty($filters['institution_id'])) {
            $query->where('institution_id', (int) $filters['institution_id']);
        }

        if (!empty($filters['branch_id'])) {
            $query->where('branch_id', (int) $filters['branch_id']);
        }

        if (!empty($filters['academic_stage_id'])) {
            $query->where('academic_stage_id', (int) $filters['academic_stage_id']);
        }

        if (!empty($filters['academic_class_id'])) {
            $query->where('academic_class_id', (int) $filters['academic_class_id']);
        }

        if (isset($filters['status']) && $filters['status'] !== '' && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        $search = is_array($filters['search'] ?? null)
            ? ($filters['search']['value'] ?? null)
            : ($filters['search'] ?? null);

        if (!empty($search) && is_string($search)) {
            $query->where(function ($q) use ($search) {
                $q->whereTranslationLike('title', "%{$search}%")
                  ->orWhereTranslationLike('content', "%{$search}%");
            });
        }

        return $query;
    }

    public function all(array $filters = []): Collection
    {
        return $this->getQuery($filters)->get();
    }

    public function findById(int $id): ?AcademicWeeklyPlan
    {
        return AcademicWeeklyPlan::with([
            'translations',
            'institution.translations',
            'branch.translations',
            'academicStage.translations',
            'academicClass.translations',
            'attachments.creator',
            'creator',
        ])->find($id);
    }

    public function create(array $data): AcademicWeeklyPlan
    {
        return AcademicWeeklyPlan::create($data);
    }

    public function update(int $id, array $data): AcademicWeeklyPlan
    {
        $plan = $this->findById($id);
        if (!$plan) {
            throw new \RuntimeException("Academic weekly plan #{$id} not found.");
        }
        $plan->update($data);
        return $plan->fresh(['translations', 'attachments']);
    }

    public function delete(int $id): bool
    {
        $plan = $this->findById($id);
        return $plan ? (bool) $plan->delete() : false;
    }

    public function toggleStatus(int $id): AcademicWeeklyPlan
    {
        $plan = $this->findById($id);
        if (!$plan) {
            throw new \RuntimeException("Academic weekly plan #{$id} not found.");
        }
        $plan->status = $plan->status === AcademicWeeklyPlan::STATUS_ACTIVE
            ? AcademicWeeklyPlan::STATUS_INACTIVE
            : AcademicWeeklyPlan::STATUS_ACTIVE;
        $plan->save();

        return $plan;
    }
}
