<?php

namespace Modules\HR\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\HR\Models\HrSetting;

interface HrSettingRepositoryInterface
{
    //======================
    /**
     * Get all HR settings with optional filters.
     *
     * @param array $filters
     * @param array $with
     * @return Collection<int, HrSetting>
     */
    public function getAll(array $filters = [], array $with = ['translations', 'parent.translations']): Collection;

    //======================
    /**
     * Get query builder instance for server-side DataTables.
     *
     * @param array $filters
     * @return Builder
     */
    public function getDataTableQuery(array $filters = []): Builder;

    //======================
    /**
     * Find an HR setting by ID.
     *
     * @param int $id
     * @param array $with
     * @return HrSetting|null
     */
    public function findById(int $id, array $with = ['translations', 'parent.translations']): ?HrSetting;

    //======================
    /**
     * Create a new HR setting.
     *
     * @param array $data
     * @return HrSetting
     */
    public function create(array $data): HrSetting;

    //======================
    /**
     * Update an existing HR setting.
     *
     * @param int|HrSetting $model
     * @param array $data
     * @return HrSetting
     */
    public function update(int|HrSetting $model, array $data): HrSetting;

    //======================
    /**
     * Delete an HR setting.
     *
     * @param int|HrSetting $model
     * @return bool
     */
    public function delete(int|HrSetting $model): bool;

    //======================
    /**
     * Get parent settings list for dropdown selection (avoiding N+1).
     *
     * @param string|null $type
     * @param int|null $excludeId
     * @return Collection
     */
    public function getParentsList(?string $type = null, ?int $excludeId = null): Collection;

    //======================
    /**
     * Get counts aggregated by setting type.
     *
     * @return array<string, int>
     */
    public function getCountsByType(): array;
}
