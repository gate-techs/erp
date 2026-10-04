<?php

namespace Modules\Tasks\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Tasks\Models\TaskGroup;
use Modules\Tasks\Repositories\Contracts\TaskGroupRepositoryInterface;

class TaskGroupRepository implements TaskGroupRepositoryInterface
{
    //-----------------------------------------
    public function getAll(array $filters = []): Collection
    {
        return $this->buildQuery($filters)->get();
    }

    //-----------------------------------------
    public function getDataTableQuery(array $filters = []): Builder
    {
        return $this->buildQuery($filters);
    }

    //-----------------------------------------
    public function findById(int $id): ?TaskGroup
    {
        return TaskGroup::query()
            ->with(['department', 'creator', 'translations'])
            ->find($id);
    }

    //-----------------------------------------
    public function create(array $data): TaskGroup
    {
        return DB::transaction(function () use ($data) {
            return TaskGroup::create($data);
        });
    }

    //-----------------------------------------
    public function update(int|TaskGroup $taskGroup, array $data): TaskGroup
    {
        $group = $taskGroup instanceof TaskGroup ? $taskGroup : TaskGroup::findOrFail($taskGroup);

        return DB::transaction(function () use ($group, $data) {
            $group->update($data);

            return $group->fresh(['department', 'creator', 'translations']);
        });
    }

    //-----------------------------------------
    public function delete(int|TaskGroup $taskGroup): bool
    {
        $group = $taskGroup instanceof TaskGroup ? $taskGroup : TaskGroup::findOrFail($taskGroup);

        return DB::transaction(function () use ($group) {
            return (bool) $group->delete();
        });
    }

    //-----------------------------------------
    public function toggleStatus(int|TaskGroup $taskGroup): bool
    {
        $group = $taskGroup instanceof TaskGroup ? $taskGroup : TaskGroup::findOrFail($taskGroup);

        return DB::transaction(function () use ($group) {
            $group->is_active = ! $group->is_active;
            return (bool) $group->save();
        });
    }

    //-----------------------------------------
    protected function buildQuery(array $filters = []): Builder
    {
        $query = TaskGroup::query()
            ->with(['department', 'creator', 'translations'])
            ->latest('id');

        if (isset($filters['is_active']) && $filters['is_active'] !== '' && $filters['is_active'] !== null) {
            $query->where('is_active', filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN));
        }

        if (!empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        if (!empty($filters['start_date'])) {
            $query->whereDate('start_date', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('end_date', '<=', $filters['end_date']);
        }

        if (!empty($filters['search'])) {
            $search = is_array($filters['search'])
                ? ($filters['search']['value'] ?? null)
                : $filters['search'];

            if (is_string($search) && $search !== '') {
                $query->where(function (Builder $builder) use ($search) {
                    $builder->whereTranslationLike('name', "%{$search}%")
                        ->orWhereTranslationLike('description', "%{$search}%")
                        ->orWhereHas('department', function (Builder $deptQuery) use ($search) {
                            $deptQuery->whereTranslationLike('name', "%{$search}%");
                        });
                });
            }
        }

        return $query;
    }
}
