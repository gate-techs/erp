<?php

namespace Modules\HR\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\HR\Enums\HrSettingStatus;
use Modules\HR\Enums\HrSettingType;
use Modules\HR\Models\HrSetting;
use Modules\HR\Repositories\Contracts\HrSettingRepositoryInterface;

class HrSettingService
{
    //======================
    public function __construct(
        protected HrSettingRepositoryInterface $repository
    ) {}

    //======================
    /**
     * Get all HR settings with optional filtering.
     */
    public function getAllSettings(array $filters = []): Collection
    {
        return $this->repository->getAll($filters);
    }

    //======================
    /**
     * Get query builder for DataTables (pre-eager loaded to avoid N+1).
     */
    public function getDataTableQuery(array $filters = []): Builder
    {
        return $this->repository->getDataTableQuery($filters);
    }

    //======================
    /**
     * Get setting details by ID.
     */
    public function getSettingById(int $id): ?HrSetting
    {
        return $this->repository->findById($id);
    }

    //======================
    /**
     * Create a new HR setting with translations.
     */
    public function createSetting(array $data, ?int $userId = null): HrSetting
    {
        if ($userId && !isset($data['created_by'])) {
            $data['created_by'] = $userId;
        }

        $formattedData = $this->formatTranslationData($data);

        return $this->repository->create($formattedData);
    }

    //======================
    /**
     * Update an HR setting with translations.
     */
    public function updateSetting(int|HrSetting $setting, array $data): HrSetting
    {
        $formattedData = $this->formatTranslationData($data);

        return $this->repository->update($setting, $formattedData);
    }

    //======================
    /**
     * Delete an HR setting.
     */
    public function deleteSetting(int|HrSetting $setting): bool
    {
        return $this->repository->delete($setting);
    }

    //======================
    /**
     * Get parent options list for dropdown.
     */
    public function getParentsList(?string $type = null, ?int $excludeId = null): Collection
    {
        return $this->repository->getParentsList($type, $excludeId);
    }

    //======================
    /**
     * Get lookups needed for forms and filters.
     */
    public function getFormData(?string $type = null, ?int $excludeId = null): array
    {
        return [
            'types'    => HrSettingType::cases(),
            'statuses' => HrSettingStatus::cases(),
            'parents'  => $this->repository->getParentsList($type, $excludeId),
            'counts'   => $this->repository->getCountsByType(),
        ];
    }

    //======================
    /**
     * Get counts aggregated by setting type.
     */
    public function getCountsByType(): array
    {
        return $this->repository->getCountsByType();
    }

    //======================
    /**
     * Format flat form inputs into Astrotomic Translatable structure.
     */
    protected function formatTranslationData(array $data): array
    {
        $formatted = $data;

        if (isset($data['name_ar']) || isset($data['description_ar'])) {
            $formatted['ar'] = [
                'name'        => $data['name_ar'] ?? null,
                'description' => $data['description_ar'] ?? null,
            ];
            unset($formatted['name_ar'], $formatted['description_ar']);
        }

        if (isset($data['name_en']) || isset($data['description_en'])) {
            $formatted['en'] = [
                'name'        => $data['name_en'] ?? null,
                'description' => $data['description_en'] ?? null,
            ];
            unset($formatted['name_en'], $formatted['description_en']);
        }

        // Clean empty parent_id
        if (isset($formatted['parent_id']) && $formatted['parent_id'] === '') {
            $formatted['parent_id'] = null;
        }

        return $formatted;
    }
}
