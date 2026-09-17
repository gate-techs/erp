<?php

namespace Modules\HR\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Modules\HR\Enums\HrSettingStatus;
use Modules\HR\Enums\HrSettingType;
use Modules\HR\Models\HrSetting;
use Modules\HR\Repositories\Contracts\HrSettingRepositoryInterface;

class HrSettingRepository implements HrSettingRepositoryInterface
{
    //======================
    /**
     * Get all HR settings with optional filters.
     */
    public function getAll(array $filters = [], array $with = ['translations', 'parent.translations']): Collection
    {
        return $this->buildQuery($filters, $with)->get();
    }

    //======================
    /**
     * Get query builder instance for server-side DataTables with eager loading to prevent N+1.
     */
    public function getDataTableQuery(array $filters = []): Builder
    {
        $query = HrSetting::with([
            'translations',
            'parent.translations',
        ])
        ->select('hr_settings.*')
        ->latest('hr_settings.id');

        if (!empty($filters['type'])) {
            $query->where('hr_settings.type', $filters['type']);
        }

        if (!empty($filters['status'])) {
            $query->where('hr_settings.status', $filters['status']);
        }

        if (isset($filters['parent_id']) && $filters['parent_id'] !== '') {
            if ($filters['parent_id'] === 'null') {
                $query->whereNull('hr_settings.parent_id');
            } else {
                $query->where('hr_settings.parent_id', $filters['parent_id']);
            }
        }

        return $query;
    }

    //======================
    /**
     * Find an HR setting by ID with translations.
     */
    public function findById(int $id, array $with = ['translations', 'parent.translations']): ?HrSetting
    {
        return HrSetting::with($with)->find($id);
    }

    //======================
    /**
     * Create a new HR setting within a transaction.
     */
    public function create(array $data): HrSetting
    {
        return DB::transaction(function () use ($data) {
            return HrSetting::create($data);
        });
    }

    //======================
    /**
     * Update an existing HR setting within a transaction.
     */
    public function update(int|HrSetting $model, array $data): HrSetting
    {
        $setting = $model instanceof HrSetting ? $model : HrSetting::findOrFail($model);

        return DB::transaction(function () use ($setting, $data) {
            $setting->update($data);

            return $setting->fresh(['translations', 'parent.translations']);
        });
    }

    //======================
    /**
     * Delete an HR setting within a transaction.
     */
    public function delete(int|HrSetting $model): bool
    {
        $setting = $model instanceof HrSetting ? $model : HrSetting::findOrFail($model);

        return DB::transaction(function () use ($setting) {
            return (bool) $setting->delete();
        });
    }

    //======================
    /**
     * Get parent settings list for dropdown selection (eager loaded to prevent N+1).
     */
    public function getParentsList(?string $type = null, ?int $excludeId = null): Collection
    {
        $query = HrSetting::with('translations')
            ->whereNull('parent_id')
            ->where('status', HrSettingStatus::ACTIVE->value);

        if ($type) {
            $query->where('type', $type);
        }

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->latest('id')->get();
    }

    //======================
    /**
     * Get counts aggregated by setting type.
     */
    public function getCountsByType(): array
    {
        $counts = HrSetting::select('type', DB::raw('count(*) as total'))
            ->groupBy('type')
            ->pluck('total', 'type')
            ->toArray();

        $result = [
            'total' => HrSetting::count(),
        ];

        foreach (HrSettingType::cases() as $typeCase) {
            $result[$typeCase->value] = $counts[$typeCase->value] ?? 0;
        }

        return $result;
    }

    //======================
    /**
     * Build base query with eager loads and filters.
     */
    protected function buildQuery(array $filters = [], array $with = []): Builder
    {
        $query = HrSetting::query();

        if (!empty($with)) {
            $query->with($with);
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['parent_id']) && $filters['parent_id'] !== '') {
            if ($filters['parent_id'] === 'null') {
                $query->whereNull('parent_id');
            } else {
                $query->where('parent_id', $filters['parent_id']);
            }
        }

        return $query->latest('id');
    }
}
