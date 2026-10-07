<?php

namespace Modules\Academic\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Academic\Models\AcademicMonthlyPlan;
use Modules\Academic\Models\AcademicMonthlyPlanAttachment;
use Modules\Academic\Repositories\Contracts\AcademicMonthlyPlanRepositoryInterface;

class AcademicMonthlyPlanRepository implements AcademicMonthlyPlanRepositoryInterface
{
    public function getFilteredQuery(array $filters = []): Builder
    {
        $query = AcademicMonthlyPlan::query()
            ->with([
                'translations',
                'institution.translations',
                'branch.translations',
                'academicYear.translations',
                'academicStage.translations',
                'classes.translations',
                'attachments',
            ]);

        if (! empty($filters['institution_id'])) {
            $query->where('institution_id', $filters['institution_id']);
        }

        if (! empty($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }

        if (! empty($filters['academic_year_id'])) {
            $query->where('academic_year_id', $filters['academic_year_id']);
        }

        if (! empty($filters['academic_stage_id'])) {
            $query->where('academic_stage_id', $filters['academic_stage_id']);
        }

        if (! empty($filters['academic_class_id'])) {
            $classId = $filters['academic_class_id'];
            $query->whereHas('classes', function ($q) use ($classId) {
                $q->where('academic_classes.id', $classId);
            });
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['search'])) {
            $searchTerm = is_array($filters['search'])
                ? ($filters['search']['value'] ?? null)
                : $filters['search'];

            if (! empty($searchTerm) && is_string($searchTerm)) {
                $query->where(function ($q) use ($searchTerm) {
                    $q->whereTranslationLike('title', "%{$searchTerm}%")
                        ->orWhereTranslationLike('description', "%{$searchTerm}%")
                        ->orWhereHas('institution', function ($instQuery) use ($searchTerm) {
                            $instQuery->whereTranslationLike('name', "%{$searchTerm}%");
                        })
                        ->orWhereHas('branch', function ($brQuery) use ($searchTerm) {
                            $brQuery->whereTranslationLike('name', "%{$searchTerm}%");
                        })
                        ->orWhereHas('academicStage', function ($stageQuery) use ($searchTerm) {
                            $stageQuery->whereTranslationLike('name', "%{$searchTerm}%");
                        })
                        ->orWhereHas('classes', function ($classQuery) use ($searchTerm) {
                            $classQuery->whereTranslationLike('name', "%{$searchTerm}%");
                        });
                });
            }
        }

        return $query;
    }

    public function all(array $filters = []): Collection
    {
        return $this->getFilteredQuery($filters)->get();
    }

    public function findById(int $id, array $with = []): ?AcademicMonthlyPlan
    {
        $relations = array_merge([
            'translations',
            'institution.translations',
            'branch.translations',
            'academicYear.translations',
            'academicStage.translations',
            'classes.translations',
            'attachments',
        ], $with);

        return AcademicMonthlyPlan::with($relations)->find($id);
    }

    public function create(array $data): AcademicMonthlyPlan
    {
        return DB::transaction(function () use ($data) {
            $classIds = $data['academic_class_ids'] ?? [];
            unset($data['academic_class_ids']);

            $plan = AcademicMonthlyPlan::create($data);

            if (! empty($classIds)) {
                $plan->classes()->sync($classIds);
            }

            return $plan;
        });
    }

    public function update(int $id, array $data): AcademicMonthlyPlan
    {
        return DB::transaction(function () use ($id, $data) {
            $plan = AcademicMonthlyPlan::findOrFail($id);

            $classIds = null;
            if (array_key_exists('academic_class_ids', $data)) {
                $classIds = $data['academic_class_ids'] ?? [];
                unset($data['academic_class_ids']);
            }

            $plan->update($data);

            if ($classIds !== null) {
                $plan->classes()->sync($classIds);
            }

            return $plan;
        });
    }

    public function delete(int $id): bool
    {
        $plan = AcademicMonthlyPlan::findOrFail($id);

        return (bool) $plan->delete();
    }

    public function toggleStatus(int $id): AcademicMonthlyPlan
    {
        $plan = AcademicMonthlyPlan::findOrFail($id);
        $plan->status = ($plan->status === AcademicMonthlyPlan::STATUS_ACTIVE)
            ? AcademicMonthlyPlan::STATUS_INACTIVE
            : AcademicMonthlyPlan::STATUS_ACTIVE;
        $plan->save();

        return $plan;
    }

    public function addAttachment(int $planId, array $data): AcademicMonthlyPlanAttachment
    {
        $data['academic_monthly_plan_id'] = $planId;

        return AcademicMonthlyPlanAttachment::create($data);
    }

    public function deleteAttachment(int $attachmentId): bool
    {
        $attachment = AcademicMonthlyPlanAttachment::findOrFail($attachmentId);

        return (bool) $attachment->delete();
    }

    public function findAttachment(int $attachmentId): ?AcademicMonthlyPlanAttachment
    {
        return AcademicMonthlyPlanAttachment::find($attachmentId);
    }
}
