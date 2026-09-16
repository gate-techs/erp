<?php

namespace Modules\Nursery\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Modules\Nursery\Enums\NurseryStatus;
use Modules\Nursery\Models\NurseryBranch;
use Modules\Nursery\Repositories\Contracts\NurseryBranchRepositoryInterface;

class NurseryBranchService
{
    //======================
    public function __construct(
        protected NurseryBranchRepositoryInterface $repository
    ) {}

    //======================
    /**
     * Get all branches with optional filtering.
     */
    public function getAllBranches(array $filters = []): Collection
    {
        return $this->repository->getAll($filters);
    }

    //======================
    /**
     * Get branch details by ID.
     */
    public function getBranchById(int $id): ?NurseryBranch
    {
        return $this->repository->findById($id);
    }

    //======================
    /**
     * Create a new nursery branch with translations and image.
     */
    public function createBranch(array $data, ?UploadedFile $image = null, ?int $userId = null): NurseryBranch
    {
        if ($userId && !isset($data['created_by'])) {
            $data['created_by'] = $userId;
        }

        if ($image) {
            $data['image'] = upload_image($image, 'nursery_branches');
        }

        $branchData = $this->formatTranslationData($data);

        return $this->repository->create($branchData);
    }

    //======================
    /**
     * Update branch details and logo if a new image is provided.
     */
    public function updateBranch(int|NurseryBranch $branch, array $data, ?UploadedFile $image = null): NurseryBranch
    {
        $instance = $branch instanceof NurseryBranch ? $branch : $this->getBranchById($branch);

        if (!$instance) {
            throw new \InvalidArgumentException(__('Nursery branch not found.'));
        }

        if ($image) {
            if ($instance->image) {
                delete_image($instance->image);
            }
            $data['image'] = upload_image($image, 'nursery_branches');
        }

        $branchData = $this->formatTranslationData($data);

        return $this->repository->update($instance, $branchData);
    }

    //======================
    /**
     * Delete a nursery branch.
     */
    public function deleteBranch(int|NurseryBranch $branch): bool
    {
        $instance = $branch instanceof NurseryBranch ? $branch : $this->getBranchById($branch);

        if ($instance && $instance->image) {
            delete_image($instance->image);
        }

        return $this->repository->delete($branch);
    }

    //======================
    /**
     * Toggle branch active/inactive status.
     */
    public function toggleStatus(int|NurseryBranch $branch): NurseryBranch
    {
        $instance = $branch instanceof NurseryBranch ? $branch : $this->getBranchById($branch);

        $newStatus = $instance->status === NurseryStatus::ACTIVE
            ? NurseryStatus::INACTIVE->value
            : NurseryStatus::ACTIVE->value;

        return $this->repository->update($instance, ['status' => $newStatus]);
    }

    //======================
    /**
     * Get lookup form data for branch create/edit forms.
     */
    public function getFormData(?int $governmentId = null): array
    {
        return [
            'nurseries' => $this->repository->getNurseriesList(),
            'governments' => $this->repository->getGovernmentsList(),
            'cities' => $this->repository->getCitiesList($governmentId),
            'employees' => $this->repository->getEmployeesList(),
        ];
    }

    //======================
    /**
     * Get cities list filtered by government ID.
     */
    public function getCitiesByGovernment(int $governmentId): Collection
    {
        return $this->repository->getCitiesList($governmentId);
    }

    //======================
    /**
     * Prepare data array supporting flat name_ar/name_en, desc, and address.
     */
    protected function formatTranslationData(array $data): array
    {
        $result = $data;

        // Map Arabic fields
        if (isset($data['name_ar']) || isset($data['description_ar']) || isset($data['address_ar'])) {
            $result['ar'] = [
                'name' => $data['name_ar'] ?? ($result['ar']['name'] ?? ''),
                'description' => $data['description_ar'] ?? ($result['ar']['description'] ?? null),
                'address' => $data['address_ar'] ?? ($result['ar']['address'] ?? null),
            ];
            unset($result['name_ar'], $result['description_ar'], $result['address_ar']);
        }

        // Map English fields
        if (isset($data['name_en']) || isset($data['description_en']) || isset($data['address_en'])) {
            $result['en'] = [
                'name' => $data['name_en'] ?? ($result['en']['name'] ?? ''),
                'description' => $data['description_en'] ?? ($result['en']['description'] ?? null),
                'address' => $data['address_en'] ?? ($result['en']['address'] ?? null),
            ];
            unset($result['name_en'], $result['description_en'], $result['address_en']);
        }

        return $result;
    }
}
