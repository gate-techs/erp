<?php

namespace Modules\Accounting\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Accounting\Models\FiscalYear;
use Modules\Accounting\Repositories\Contracts\FiscalYearRepositoryInterface;

class FiscalYearRepository implements FiscalYearRepositoryInterface
{
    //===============================
    public function getAll(array $filters = []): Collection
    {
        return $this->getDataTableQuery($filters)
            ->with(['translations', 'entity.translations', 'creator', 'closer'])
            ->get();
    }

    //===============================
    public function getDataTableQuery(array $filters = []): Builder
    {
        $query = FiscalYear::query()->orderByDesc('start_date');

        if (!empty($filters['entity_id'])) {
            $query->where('entity_id', $filters['entity_id']);
        }

        if (isset($filters['status']) && $filters['status'] !== '' && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['start_year'])) {
            $query->whereYear('start_date', $filters['start_year']);
        }

        if (!empty($filters['search'])) {
            $search = is_array($filters['search']) ? ($filters['search']['value'] ?? null) : $filters['search'];

            if ($search) {
                $query->where(function (Builder $builder) use ($search) {
                    $builder->whereTranslationLike('name', "%{$search}%")
                        ->orWhereTranslationLike('description', "%{$search}%")
                        ->orWhereHas('entity', function (Builder $q) use ($search) {
                            $q->whereTranslationLike('name', "%{$search}%")
                                ->orWhere('code', 'like', "%{$search}%");
                        })
                        ->orWhereYear('start_date', 'like', "%{$search}%")
                        ->orWhereYear('end_date', 'like', "%{$search}%");
                });
            }
        }

        return $query;
    }

    //===============================
    public function findById(int $id, array $with = ['translations', 'entity.translations', 'creator', 'closer']): ?FiscalYear
    {
        return FiscalYear::with($with)->find($id);
    }

    //===============================
    public function create(array $data): FiscalYear
    {
        return FiscalYear::create($data);
    }

    //===============================
    public function update(int|FiscalYear $model, array $data): FiscalYear
    {
        $fiscalYear = $model instanceof FiscalYear ? $model : FiscalYear::findOrFail($model);
        $fiscalYear->update($data);

        return $fiscalYear->fresh(['translations', 'entity.translations', 'creator', 'closer']);
    }

    //===============================
    public function delete(int|FiscalYear $model): bool
    {
        $fiscalYear = $model instanceof FiscalYear ? $model : FiscalYear::findOrFail($model);

        return (bool) $fiscalYear->delete();
    }

    //===============================
    public function hasOverlap(int $entityId, string $startDate, string $endDate, ?int $ignoreId = null): bool
    {
        return FiscalYear::query()
            ->where('entity_id', $entityId)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->whereDate('start_date', '<=', $endDate)
            ->whereDate('end_date', '>=', $startDate)
            ->exists();
    }
}
