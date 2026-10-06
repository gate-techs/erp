<?php

namespace Modules\Academic\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Academic\Models\AcademicStage;
use Modules\Academic\Repositories\Contracts\AcademicStageRepositoryInterface;

class AcademicStageRepository implements AcademicStageRepositoryInterface
{
    public function getAll(array $filters = []): Collection
    {
        return $this->getDataTableQuery($filters)
            ->with('creator')
            ->get();
    }

    public function getDataTableQuery(array $filters = []): Builder
    {
        $query = AcademicStage::query()
            ->with(['translations', 'creator', 'institution'])
            ->orderByDesc('id');

        $this->applyInstitutionFilter($query, $filters);
        $this->applyStatusFilter($query, $filters);
        $this->applySearchFilter($query, $filters);

        return $query;
    }

    public function findById(int $id): ?AcademicStage
    {
        return AcademicStage::with(['translations', 'creator', 'institution'])->find($id);
    }

    public function create(array $data): AcademicStage
    {
        return AcademicStage::create($data)->load(['translations', 'creator', 'institution']);
    }

    public function update(AcademicStage $academicStage, array $data): AcademicStage
    {
        $academicStage->update($data);

        return $academicStage->fresh(['translations', 'creator', 'institution']);
    }

    public function delete(AcademicStage $academicStage): bool
    {
        return (bool) $academicStage->delete();
    }

    private function applyInstitutionFilter(Builder $query, array $filters): void
    {
        if (!empty($filters['institution_id'])) {
            $query->where('institution_id', (int) $filters['institution_id']);
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
                ->where('code', 'like', "%{$search}%")
                ->orWhereTranslationLike('name', "%{$search}%")
                ->orWhereTranslationLike('description', "%{$search}%");
        });
    }
}
