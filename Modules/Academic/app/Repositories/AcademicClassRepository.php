<?php

namespace Modules\Academic\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Academic\Models\AcademicClass;
use Modules\Academic\Repositories\Contracts\AcademicClassRepositoryInterface;

class AcademicClassRepository implements AcademicClassRepositoryInterface
{
    public function getAll(array $filters = []): Collection
    {
        return $this->getDataTableQuery($filters)
            ->with(['translations', 'academicYear.translations', 'academicStage.translations', 'creator'])
            ->get();
    }

    public function getDataTableQuery(array $filters = []): Builder
    {
        $query = AcademicClass::query()
            ->with(['translations', 'academicYear.translations', 'academicStage.translations', 'creator'])
            ->orderByDesc('id');

        $this->applyScopeFilters($query, $filters);
        $this->applyStatusFilter($query, $filters);
        $this->applySearchFilter($query, $filters);

        return $query;
    }

    public function findById(int $id): ?AcademicClass
    {
        return AcademicClass::with([
            'translations',
            'academicYear.translations',
            'academicStage.translations',
            'creator',
        ])->find($id);
    }

    public function create(array $data): AcademicClass
    {
        return AcademicClass::create($data)->load([
            'translations',
            'academicYear.translations',
            'academicStage.translations',
            'creator',
        ]);
    }

    public function update(AcademicClass $academicClass, array $data): AcademicClass
    {
        $academicClass->update($data);

        return $academicClass->fresh([
            'translations',
            'academicYear.translations',
            'academicStage.translations',
            'creator',
        ]);
    }

    public function delete(AcademicClass $academicClass): bool
    {
        return (bool) $academicClass->delete();
    }

    public function hasDuplicateName(
        int $institutionId,
        int $branchId,
        int $academicYearId,
        int $academicStageId,
        array $names,
        ?int $ignoreId = null
    ): bool {
        $names = array_values(array_filter(array_map('trim', $names)));

        if ($names === []) {
            return false;
        }

        return AcademicClass::query()
            ->where('institution_id', $institutionId)
            ->where('branch_id', $branchId)
            ->where('academic_year_id', $academicYearId)
            ->where('academic_stage_id', $academicStageId)
            ->when($ignoreId, fn (Builder $query) => $query->where('id', '!=', $ignoreId))
            ->whereHas('translations', fn (Builder $query) => $query->whereIn('name', $names))
            ->exists();
    }

    private function applyScopeFilters(Builder $query, array $filters): void
    {
        foreach (['institution_id', 'branch_id', 'academic_year_id', 'academic_stage_id'] as $field) {
            if (!empty($filters[$field])) {
                $query->where($field, (int) $filters[$field]);
            }
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
