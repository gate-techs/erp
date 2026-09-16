<?php

namespace Modules\Settings\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\Settings\Enums\GeneralSettingType;
use Modules\Settings\Models\GeneralSetting;
use Modules\Settings\Repositories\Contracts\GeneralSettingRepositoryInterface;

class GeneralSettingRepository implements GeneralSettingRepositoryInterface
{
    /**
     * Get all general settings based on filters.
     */
    public function getAll(array $filters = [], array $with = ['translations', 'parent', 'children']): Collection
    {
        return $this->buildQuery($filters, $with)->get();
    }

    /**
     * Get paginated general settings.
     */
    public function getPaginated(array $filters = [], int $perPage = 15, array $with = ['translations', 'parent']): LengthAwarePaginator
    {
        return $this->buildQuery($filters, $with)->paginate($perPage);
    }

    /**
     * Get settings by specific type.
     */
    public function getByType(GeneralSettingType|string $type, ?int $parentId = null): Collection
    {
        $typeValue = $type instanceof GeneralSettingType ? $type->value : $type;

        $query = GeneralSetting::with(['translations', 'children'])
            ->where('type', $typeValue);

        if ($parentId !== null) {
            $query->where('parent_id', $parentId);
        }

        return $query->get();
    }

    /**
     * Find a general setting by ID.
     */
    public function findById(int $id, array $with = ['translations', 'parent', 'children']): ?GeneralSetting
    {
        return GeneralSetting::with($with)->find($id);
    }

    /**
     * Create a new general setting record with translations.
     */
    public function create(array $data): GeneralSetting
    {
        return GeneralSetting::create($data);
    }

    /**
     * Update an existing general setting record with translations.
     */
    public function update(int|GeneralSetting $model, array $data): GeneralSetting
    {
        $setting = $model instanceof GeneralSetting ? $model : GeneralSetting::findOrFail($model);
        $setting->update($data);

        return $setting->fresh(['translations', 'parent', 'children']);
    }

    /**
     * Delete a general setting record.
     */
    public function delete(int|GeneralSetting $model): bool
    {
        $setting = $model instanceof GeneralSetting ? $model : GeneralSetting::findOrFail($model);

        return (bool) $setting->delete();
    }

    /**
     * Get available parent options.
     */
    public function getParents(GeneralSettingType|string|null $type = null): Collection
    {
        $query = GeneralSetting::with('translations')->parentsOnly();

        if ($type !== null) {
            $typeValue = $type instanceof GeneralSettingType ? $type->value : $type;
            $query->where('type', $typeValue);
        }

        return $query->get();
    }

    /**
     * Helper to build filtered query.
     */
    protected function buildQuery(array $filters, array $with = [])
    {
        $query = GeneralSetting::with($with)->latest('id');

        if (!empty($filters['type'])) {
            $typeValue = $filters['type'] instanceof GeneralSettingType ? $filters['type']->value : $filters['type'];
            $query->where('type', $typeValue);
        }

        if (isset($filters['parent_id'])) {
            if ($filters['parent_id'] === 'root' || $filters['parent_id'] === null) {
                $query->whereNull('parent_id');
            } else {
                $query->where('parent_id', $filters['parent_id']);
            }
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->whereTranslationLike('name', "%{$search}%")
                  ->orWhere('value', 'like', "%{$search}%");
            });
        }

        return $query;
    }
}
