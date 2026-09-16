<?php

namespace Modules\Settings\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\Settings\Enums\GeneralSettingType;
use Modules\Settings\Models\GeneralSetting;

interface GeneralSettingRepositoryInterface
{
    /**
     * Get all general settings based on filters.
     *
     * @param array $filters
     * @param array $with
     * @return Collection<int, GeneralSetting>
     */
    public function getAll(array $filters = [], array $with = ['translations', 'parent', 'children']): Collection;

    /**
     * Get paginated general settings.
     *
     * @param array $filters
     * @param int $perPage
     * @param array $with
     * @return LengthAwarePaginator
     */
    public function getPaginated(array $filters = [], int $perPage = 15, array $with = ['translations', 'parent']): LengthAwarePaginator;

    /**
     * Get settings by specific type.
     *
     * @param GeneralSettingType|string $type
     * @param int|null $parentId
     * @return Collection<int, GeneralSetting>
     */
    public function getByType(GeneralSettingType|string $type, ?int $parentId = null): Collection;

    /**
     * Find a general setting by ID.
     *
     * @param int $id
     * @param array $with
     * @return GeneralSetting|null
     */
    public function findById(int $id, array $with = ['translations', 'parent', 'children']): ?GeneralSetting;

    /**
     * Create a new general setting record with translations.
     *
     * @param array $data
     * @return GeneralSetting
     */
    public function create(array $data): GeneralSetting;

    /**
     * Update an existing general setting record with translations.
     *
     * @param int|GeneralSetting $model
     * @param array $data
     * @return GeneralSetting
     */
    public function update(int|GeneralSetting $model, array $data): GeneralSetting;

    /**
     * Delete a general setting record.
     *
     * @param int|GeneralSetting $model
     * @return bool
     */
    public function delete(int|GeneralSetting $model): bool;

    /**
     * Get available parent options for a given type or all.
     *
     * @param GeneralSettingType|string|null $type
     * @return Collection<int, GeneralSetting>
     */
    public function getParents(GeneralSettingType|string|null $type = null): Collection;
}
