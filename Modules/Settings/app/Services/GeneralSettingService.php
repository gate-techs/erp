<?php

namespace Modules\Settings\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\Settings\Enums\GeneralSettingType;
use Modules\Settings\Models\GeneralSetting;
use Modules\Settings\Repositories\Contracts\GeneralSettingRepositoryInterface;

class GeneralSettingService
{
    public function __construct(
        protected GeneralSettingRepositoryInterface $repository
    ) {}

    /**
     * Get all general settings with optional filters.
     */
    public function getAllSettings(array $filters = []): Collection
    {
        return $this->repository->getAll($filters);
    }

    /**
     * Get paginated settings for management views.
     */
    public function getPaginatedSettings(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getPaginated($filters, $perPage);
    }

    /**
     * Get settings filtered by type.
     */
    public function getByType(GeneralSettingType|string $type, ?int $parentId = null): Collection
    {
        return $this->repository->getByType($type, $parentId);
    }

    /**
     * Get setting by ID.
     */
    public function getById(int $id): ?GeneralSetting
    {
        return $this->repository->findById($id);
    }

    /**
     * Create a new setting with optional image upload.
     */
    public function createSetting(array $data, ?int $userId = null, ?\Illuminate\Http\UploadedFile $image = null): GeneralSetting
    {
        if ($userId && !isset($data['created_by'])) {
            $data['created_by'] = $userId;
        }

        if ($image) {
            $data['image'] = upload_image($image, 'general_settings');
        }

        return $this->repository->create($data);
    }

    /**
     * Update an existing setting with optional image upload.
     */
    public function updateSetting(int|GeneralSetting $setting, array $data, ?\Illuminate\Http\UploadedFile $image = null): GeneralSetting
    {
        $model = $setting instanceof GeneralSetting ? $setting : $this->repository->findById($setting);

        if ($image) {
            $data['image'] = upload_image($image, 'general_settings', $model?->image);
        }

        return $this->repository->update($model, $data);
    }

    /**
     * Delete a setting.
     */
    public function deleteSetting(int|GeneralSetting $setting): bool
    {
        return $this->repository->delete($setting);
    }

    /**
     * Get parent options (for dropdowns when choosing parent_id).
     */
    public function getParentOptions(GeneralSettingType|string|null $type = null): Collection
    {
        return $this->repository->getParents($type);
    }

    /**
     * Get all available types as an array for UI selects.
     */
    public function getAvailableTypes(): array
    {
        return GeneralSettingType::toArray();
    }
}
