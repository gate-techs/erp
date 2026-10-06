<?php

namespace Modules\Academic\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Academic\Models\AcademicYear;
use Modules\Academic\Repositories\Contracts\AcademicYearRepositoryInterface;

class AcademicYearRepository implements AcademicYearRepositoryInterface
{
    public function getAll(array $filters = []): Collection
    {
        return $this->getDataTableQuery($filters)
            ->with(['creator', 'translations', 'institution'])
            ->get();
    }

    public function getDataTableQuery(array $filters = []): Builder
    {
        $query = AcademicYear::query()
            ->with(['creator', 'translations', 'institution'])
            ->orderByDesc('start_date')
            ->orderByDesc('id');

        $this->applyInstitutionFilter($query, $filters);
        $this->applyStatusFilter($query, $filters);
        $this->applySearchFilter($query, $filters);

        return $query;
    }

    public function findById(int $id): ?AcademicYear
    {
        return AcademicYear::with(['creator', 'translations', 'institution'])->find($id);
    }

    public function create(array $data): AcademicYear
    {
        return AcademicYear::create($data)->load('creator');
    }

    public function update(AcademicYear $academicYear, array $data): AcademicYear
    {
        $academicYear->update($data);

        return $academicYear->fresh(['creator']);
    }

    public function delete(AcademicYear $academicYear): bool
    {
        return (bool) $academicYear->delete();
    }

    public function hasOverlap(
        int $institutionId,
        string $startDate,
        string $endDate,
        ?int $ignoreId = null
    ): bool {
        return AcademicYear::query()
            ->where('institution_id', $institutionId)
            ->when($ignoreId, fn (Builder $query) => $query->where('id', '!=', $ignoreId))
            ->whereDate('start_date', '<=', $endDate)
            ->whereDate('end_date', '>=', $startDate)
            ->exists();
    }

    public function clearCurrentForInstitution(int $institutionId, ?int $ignoreId = null): void
    {
        AcademicYear::query()
            ->where('institution_id', $institutionId)
            ->when($ignoreId, fn (Builder $query) => $query->where('id', '!=', $ignoreId))
            ->where('is_current', true)
            ->update(['is_current' => false]);
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
                ->orWhere('notes', 'like', "%{$search}%")
                ->orWhereYear('start_date', $search)
                ->orWhereYear('end_date', $search);
        });
    }
}
