<?php

namespace Modules\Academic\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Academic\Models\AcademicSubject;
use Modules\Academic\Repositories\Contracts\AcademicSubjectRepositoryInterface;

class AcademicSubjectRepository implements AcademicSubjectRepositoryInterface
{
    public function getAll(array $filters = []): Collection
    {
        return $this->getDataTableQuery($filters)
            ->with(['translations', 'institution.translations', 'academicYear.translations', 'academicStages.translations', 'attachments', 'creator'])
            ->get();
    }

    public function getDataTableQuery(array $filters = []): Builder
    {
        $query = AcademicSubject::query()
            ->with(['translations', 'institution.translations', 'academicYear.translations', 'academicStages.translations', 'attachments', 'creator'])
            ->withCount('attachments')
            ->orderByDesc('id');

        $this->applyScopeFilters($query, $filters);
        $this->applyStatusFilter($query, $filters);
        $this->applySearchFilter($query, $filters);

        return $query;
    }

    public function findById(int $id): ?AcademicSubject
    {
        return AcademicSubject::with([
            'translations',
            'institution.translations',
            'academicYear.translations',
            'academicStages.translations',
            'attachments.creator',
            'creator',
        ])->find($id);
    }

    public function create(array $data): AcademicSubject
    {
        return AcademicSubject::create($data)->load([
            'translations',
            'institution',
            'academicYear',
            'academicStages',
            'attachments',
            'creator',
        ]);
    }

    public function update(AcademicSubject $academicSubject, array $data): AcademicSubject
    {
        $academicSubject->update($data);

        return $academicSubject->fresh([
            'translations',
            'institution',
            'academicYear',
            'academicStages',
            'attachments',
            'creator',
        ]);
    }

    public function delete(AcademicSubject $academicSubject): bool
    {
        return (bool) $academicSubject->delete();
    }

    public function syncStages(AcademicSubject $academicSubject, array $stageIds): void
    {
        $academicSubject->academicStages()->sync($stageIds);
    }

    private function applyScopeFilters(Builder $query, array $filters): void
    {
        if (!empty($filters['institution_id'])) {
            $query->where('institution_id', (int) $filters['institution_id']);
        }

        if (!empty($filters['academic_year_id'])) {
            $query->where('academic_year_id', (int) $filters['academic_year_id']);
        }

        if (!empty($filters['academic_stage_id'])) {
            $query->whereHas('academicStages', function (Builder $q) use ($filters) {
                $q->where('academic_stages.id', (int) $filters['academic_stage_id']);
            });
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
                ->whereTranslationLike('name', "%{$search}%")
                ->orWhereTranslationLike('description', "%{$search}%");
        });
    }
}
