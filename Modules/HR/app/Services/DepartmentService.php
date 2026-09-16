<?php

namespace Modules\HR\Services;

use Illuminate\Database\Eloquent\Collection;
use Modules\HR\Enums\DepartmentStatus;
use Modules\HR\Models\Department;
use Modules\HR\Repositories\Contracts\DepartmentRepositoryInterface;

class DepartmentService
{
    //======================
    public function __construct(
        protected DepartmentRepositoryInterface $repository
    ) {}

    //======================
    /**
     * Get all departments with optional filtering.
     */
    public function getAllDepartments(array $filters = []): Collection
    {
        return $this->repository->getAll($filters);
    }

    //======================
    /**
     * Get department details by ID.
     */
    public function getDepartmentById(int $id): ?Department
    {
        return $this->repository->findById($id);
    }

    //======================
    /**
     * Create a new department with translations.
     */
    public function createDepartment(array $data, ?int $userId = null): Department
    {
        if ($userId && !isset($data['created_by'])) {
            $data['created_by'] = $userId;
        }

        $formattedData = $this->formatTranslationData($data);

        return $this->repository->create($formattedData);
    }

    //======================
    /**
     * Update department details.
     */
    public function updateDepartment(int|Department $department, array $data): Department
    {
        $formattedData = $this->formatTranslationData($data);

        return $this->repository->update($department, $formattedData);
    }

    //======================
    /**
     * Delete a department.
     */
    public function deleteDepartment(int|Department $department): bool
    {
        return $this->repository->delete($department);
    }

    //======================
    /**
     * Toggle status between active and inactive.
     */
    public function toggleStatus(int|Department $department): Department
    {
        $model = $department instanceof Department ? $department : $this->repository->findById($department);

        if (!$model) {
            throw new \InvalidArgumentException('Department not found.');
        }

        $newStatus = $model->status === DepartmentStatus::ACTIVE
            ? DepartmentStatus::INACTIVE
            : DepartmentStatus::ACTIVE;

        return $this->repository->update($model, ['status' => $newStatus->value]);
    }

    //======================
    /**
     * Get data required for creation and edit forms.
     */
    public function getFormData(?int $excludeId = null): array
    {
        return [
            'parentDepartments' => $this->repository->getMainDepartmentsList($excludeId),
            'nurseries'         => $this->repository->getNurseriesList(),
            'branches'          => $this->repository->getBranchesList(),
            'managers'          => $this->repository->getManagersList(),
            'statuses'          => DepartmentStatus::cases(),
        ];
    }

    //======================
    /**
     * Get branches list filtered by nursery ID.
     */
    public function getBranchesByNursery(?int $nurseryId): Collection
    {
        return $this->repository->getBranchesList($nurseryId);
    }

    //======================
    /**
     * Prepare data array supporting flat name_ar/name_en and description.
     */
    protected function formatTranslationData(array $data): array
    {
        $result = $data;

        // Map Arabic fields
        if (isset($data['name_ar']) || isset($data['description_ar'])) {
            $result['ar'] = [
                'name'        => $data['name_ar'] ?? ($result['ar']['name'] ?? ''),
                'description' => $data['description_ar'] ?? ($result['ar']['description'] ?? null),
            ];
            unset($result['name_ar'], $result['description_ar']);
        }

        // Map English fields
        if (isset($data['name_en']) || isset($data['description_en'])) {
            $result['en'] = [
                'name'        => $data['name_en'] ?? ($result['en']['name'] ?? ''),
                'description' => $data['description_en'] ?? ($result['en']['description'] ?? null),
            ];
            unset($result['name_en'], $result['description_en']);
        }

        return $result;
    }
}
