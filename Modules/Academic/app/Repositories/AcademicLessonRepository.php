<?php

namespace Modules\Academic\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Academic\Models\AcademicLesson;
use Modules\Academic\Repositories\Contracts\AcademicLessonRepositoryInterface;

class AcademicLessonRepository implements AcademicLessonRepositoryInterface
{
    public function getAll(array $filters = []): Collection
    {
        return $this->getDataTableQuery($filters)
            ->with(['translations', 'institution.translations', 'academicStage.translations', 'academicSubject.translations', 'attachments', 'creator'])
            ->get();
    }

    public function getDataTableQuery(array $filters = []): Builder
    {
        $query = AcademicLesson::query()
            ->with(['translations', 'institution.translations', 'academicStage.translations', 'academicSubject.translations', 'attachments', 'creator'])
            ->withCount('attachments')
            ->orderBy('order', 'asc')
            ->orderByDesc('id');

        $this->applyScopeFilters($query, $filters);
        $this->applyStatusFilter($query, $filters);
        $this->applySearchFilter($query, $filters);

        return $query;
    }

    public function findById(int $id): ?AcademicLesson
    {
        return AcademicLesson::with([
            'translations',
            'institution.translations',
            'academicStage.translations',
            'academicSubject.translations',
            'attachments.creator',
            'creator',
        ])->find($id);
    }

    public function create(array $data): AcademicLesson
    {
        return AcademicLesson::create($data)->load([
            'translations',
            'institution',
            'academicStage',
            'academicSubject',
            'attachments',
            'creator',
        ]);
    }

    public function update(AcademicLesson $academicLesson, array $data): AcademicLesson
    {
        $academicLesson->update($data);

        return $academicLesson->fresh([
            'translations',
            'institution',
            'academicStage',
            'academicSubject',
            'attachments',
            'creator',
        ]);
    }

    public function delete(AcademicLesson $academicLesson): bool
    {
        return (bool) $academicLesson->delete();
    }

    private function applyScopeFilters(Builder $query, array $filters): void
    {
        if (!empty($filters['institution_id'])) {
            $query->where('institution_id', (int) $filters['institution_id']);
        }

        if (!empty($filters['academic_stage_id'])) {
            $query->where('academic_stage_id', (int) $filters['academic_stage_id']);
        }

        if (!empty($filters['academic_subject_id'])) {
            $query->where('academic_subject_id', (int) $filters['academic_subject_id']);
        }

        if (!empty($filters['start_date'])) {
            $query->whereDate('start_date', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('end_date', '<=', $filters['end_date']);
        }
    }

    private function applyStatusFilter(Builder $query, array $filters): void
    {
        if (isset($filters['status']) && $filters['status'] !== '' && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }
    }

    private function applySearchFilter(Builder $query, array $filters): void
    {
        $search = is_array($filters['search'] ?? null)
            ? ($filters['search']['value'] ?? null)
            : ($filters['search'] ?? null);

        if (!$search) {
            return;
        }

        $query->where(function (Builder $builder) use ($search) {
            $builder
                ->whereTranslationLike('title', "%{$search}%")
                ->orWhereTranslationLike('content', "%{$search}%");
        });
    }
}
