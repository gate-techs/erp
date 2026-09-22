<?php

namespace Modules\Accounting\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Accounting\Models\Entity;
use Modules\Accounting\Repositories\Contracts\EntityRepositoryInterface;
use Modules\Nursery\Models\Nursery;

class EntityRepository implements EntityRepositoryInterface
{
    //--------------------------------------------------
    public function getAll(array $filters = [], array $with = ['translations', 'nursery', 'creator']): Collection
    {
        return $this->getDataTableQuery($filters)->with($with)->get();
    }

    //--------------------------------------------------
    public function getPaginated(array $filters = [], int $perPage = 15, array $with = ['translations', 'nursery', 'creator']): LengthAwarePaginator
    {
        return $this->getDataTableQuery($filters)->with($with)->paginate($perPage);
    }

    //--------------------------------------------------
    public function getDataTableQuery(array $filters = []): Builder
    {
        $query = Entity::query()->latest('id');

        if (isset($filters['status']) && $filters['status'] !== '' && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['nursery_id'])) {
            $query->where('nursery_id', $filters['nursery_id']);
        }

        if (!empty($filters['search'])) {
            $search = is_array($filters['search']) ? ($filters['search']['value'] ?? null) : $filters['search'];

            if ($search) {
                $query->where(function (Builder $builder) use ($search) {
                    $builder->whereTranslationLike('name', "%{$search}%")
                        ->orWhereTranslationLike('description', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            }
        }

        return $query;
    }

    //--------------------------------------------------
    public function getAvailableNurseries(): Collection
    {
        $assignedNurseryIds = Entity::query()->whereNotNull('nursery_id')->pluck('nursery_id');

        return Nursery::query()
            ->with('translations')
            ->whereNotIn('id', $assignedNurseryIds)
            ->orderByDesc('id')
            ->get();
    }

    //--------------------------------------------------
    public function findById(int $id, array $with = ['translations', 'nursery', 'creator']): ?Entity
    {
        return Entity::with($with)->find($id);
    }

    //--------------------------------------------------
    public function create(array $data): Entity
    {
        return Entity::create($data);
    }

    //--------------------------------------------------
    public function update(int|Entity $model, array $data): Entity
    {
        $entity = $model instanceof Entity ? $model : Entity::findOrFail($model);
        $entity->update($data);

        return $entity->fresh(['translations', 'nursery', 'creator']);
    }

    //--------------------------------------------------
    public function delete(int|Entity $model): bool
    {
        $entity = $model instanceof Entity ? $model : Entity::findOrFail($model);

        return (bool) $entity->delete();
    }
}
