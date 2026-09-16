<?php

namespace Modules\Nursery\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Modules\Nursery\Enums\NurseryStatus;
use Modules\Nursery\Models\Nursery;
use Modules\Nursery\Repositories\Contracts\NurseryRepositoryInterface;

class NurseryService
{
    //======================
    public function __construct(
        protected NurseryRepositoryInterface $repository
    ) {}

    //======================
    /**
     * Get all nurseries with optional filtering.
     */
    public function getAllNurseries(array $filters = []): Collection
    {
        return $this->repository->getAll($filters);
    }

    //======================
    /**
     * Get paginated nurseries list.
     */
    public function getPaginatedNurseries(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getPaginated($filters, $perPage);
    }

    //======================
    /**
     * Get nursery details by ID.
     */
    public function getNurseryById(int $id): ?Nursery
    {
        return $this->repository->findById($id);
    }

    //======================
    /**
     * Create a new nursery with translated attributes, uploaded logo, and work days.
     */
    public function createNursery(array $data, ?UploadedFile $image = null, ?int $userId = null): Nursery
    {
        if ($userId && !isset($data['created_by'])) {
            $data['created_by'] = $userId;
        }

        if ($image) {
            $data['image'] = upload_image($image, 'nurseries');
        }

        // Prepare translations structure for Astrotomic Translatable
        $nurseryData = $this->formatTranslationData($data);

        return $this->repository->create($nurseryData);
    }

    //======================
    /**
     * Update nursery details and update logo if a new one is uploaded.
     */
    public function updateNursery(int|Nursery $nursery, array $data, ?UploadedFile $image = null): Nursery
    {
        $instance = $nursery instanceof Nursery ? $nursery : $this->getNurseryById($nursery);

        if (!$instance) {
            throw new \InvalidArgumentException(__('Nursery not found.'));
        }

        if ($image) {
            // Delete old image if exists
            if ($instance->image) {
                delete_image($instance->image);
            }
            $data['image'] = upload_image($image, 'nurseries');
        }

        // Prepare translations structure for Astrotomic Translatable
        $nurseryData = $this->formatTranslationData($data);

        return $this->repository->update($instance, $nurseryData);
    }

    //======================
    /**
     * Delete a nursery record.
     */
    public function deleteNursery(int|Nursery $nursery): bool
    {
        $instance = $nursery instanceof Nursery ? $nursery : $this->getNurseryById($nursery);

        if ($instance && $instance->image) {
            delete_image($instance->image);
        }

        return $this->repository->delete($nursery);
    }

    //======================
    /**
     * Toggle nursery active/inactive status.
     */
    public function toggleStatus(int|Nursery $nursery): Nursery
    {
        $instance = $nursery instanceof Nursery ? $nursery : $this->getNurseryById($nursery);

        $newStatus = $instance->status === NurseryStatus::ACTIVE
            ? NurseryStatus::INACTIVE->value
            : NurseryStatus::ACTIVE->value;

        return $this->repository->update($instance, ['status' => $newStatus]);
    }

    //======================
    /**
     * Get lookup form data (responsible employees and governments list).
     *
     * @return array{employees: Collection, governments: Collection}
     */
    public function getFormData(): array
    {
        return [
            'employees' => $this->repository->getEmployeesList(),
            'governments' => $this->repository->getGovernmentsList(),
        ];
    }

    //======================
    /**
     * Prepare data array supporting both Astrotomic locale nested arrays and flat name_ar/name_en keys.
     */
    protected function formatTranslationData(array $data): array
    {
        $result = $data;

        // Map Arabic fields
        if (isset($data['name_ar']) || isset($data['description_ar'])) {
            $result['ar'] = [
                'name' => $data['name_ar'] ?? ($result['ar']['name'] ?? ''),
                'description' => $data['description_ar'] ?? ($result['ar']['description'] ?? null),
            ];
            unset($result['name_ar'], $result['description_ar']);
        }

        // Map English fields
        if (isset($data['name_en']) || isset($data['description_en'])) {
            $result['en'] = [
                'name' => $data['name_en'] ?? ($result['en']['name'] ?? ''),
                'description' => $data['description_en'] ?? ($result['en']['description'] ?? null),
            ];
            unset($result['name_en'], $result['description_en']);
        }

        return $result;
    }
}
