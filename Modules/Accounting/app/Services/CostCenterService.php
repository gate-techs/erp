<?php

namespace Modules\Accounting\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Modules\Accounting\Models\CostCenter;
use Modules\Accounting\Models\Entity;
use Modules\Accounting\Repositories\Contracts\CostCenterRepositoryInterface;
use Modules\Nursery\Models\NurseryBranch;

class CostCenterService
{
    //===============================
    public function __construct(
        protected CostCenterRepositoryInterface $repository
    ) {}

    //===============================
    public function getAll(array $filters = []): Collection
    {
        return $this->repository->all($filters);
    }

    //===============================
    public function getById(int $id): CostCenter
    {
        $costCenter = $this->repository->find($id);

        if (!$costCenter) {
            abort(404, __('Cost center not found.'));
        }

        return $costCenter;
    }

    //===============================
    public function getDataTableQuery(array $filters = []): Builder
    {
        return $this->repository->getDataTableQuery($filters);
    }

    //===============================
    public function create(array $data, ?int $userId = null): CostCenter
    {
        $entityId = (int) $data['entity_id'];

        $this->validateUniqueCode($data['code'], $entityId);

        if (!empty($data['parent_id'])) {
            $this->validateParent((int) $data['parent_id'], $entityId);
        }

        if (!empty($data['branch_id'])) {
            $this->validateBranch((int) $data['branch_id'], $entityId);
        }

        $data['created_by'] = $userId ?? auth()->id();

        return $this->repository->create($data);
    }

    //===============================
    public function update(int $id, array $data, ?int $userId = null): CostCenter
    {
        $costCenter = $this->getById($id);
        $entityId = (int) ($data['entity_id'] ?? $costCenter->entity_id);

        if (!empty($data['code'])) {
            $this->validateUniqueCode($data['code'], $entityId, $id);
        }

        if (array_key_exists('parent_id', $data) && !empty($data['parent_id'])) {
            $this->validateParent((int) $data['parent_id'], $entityId, $id);
        }

        if (array_key_exists('branch_id', $data) && !empty($data['branch_id'])) {
            $this->validateBranch((int) $data['branch_id'], $entityId);
        }

        return $this->repository->update($id, $data);
    }

    //===============================
    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    //===============================
    public function toggleStatus(int $id): CostCenter
    {
        return $this->repository->toggleStatus($id);
    }

    //===============================
    public function getParentsForEntity(int $entityId, ?int $excludeId = null): Collection
    {
        return $this->repository->getParentsForEntity($entityId, $excludeId);
    }

    //===============================
    public function getBranchesForEntity(int $entityId): Collection
    {
        return $this->repository->getBranchesForEntity($entityId);
    }

    //===============================
    protected function validateUniqueCode(string $code, int $entityId, ?int $excludeId = null): void
    {
        if ($this->repository->codeExistsInEntity($code, $entityId, $excludeId)) {
            throw ValidationException::withMessages([
                'code' => [__('The code has already been taken for this entity.')],
            ]);
        }
    }

    //===============================
    protected function validateParent(int $parentId, int $entityId, ?int $currentId = null): void
    {
        if ($currentId && $parentId === $currentId) {
            throw ValidationException::withMessages([
                'parent_id' => [__('A cost center cannot be its own parent.')],
            ]);
        }

        $parent = CostCenter::find($parentId);

        if (!$parent) {
            throw ValidationException::withMessages([
                'parent_id' => [__('Selected parent cost center does not exist.')],
            ]);
        }

        if ($parent->entity_id !== $entityId) {
            throw ValidationException::withMessages([
                'parent_id' => [__('Parent cost center must belong to the same accounting entity.')],
            ]);
        }

        if ($currentId && $this->isDescendant($parentId, $currentId)) {
            throw ValidationException::withMessages([
                'parent_id' => [__('Cannot set a descendant cost center as parent.')],
            ]);
        }
    }

    //===============================
    protected function validateBranch(int $branchId, int $entityId): void
    {
        $entity = Entity::find($entityId);

        if (!$entity || !$entity->nursery_id) {
            throw ValidationException::withMessages([
                'branch_id' => [__('The selected entity has no associated organization for branches.')],
            ]);
        }

        $branchExists = NurseryBranch::where('id', $branchId)
            ->where('nursery_id', $entity->nursery_id)
            ->exists();

        if (!$branchExists) {
            throw ValidationException::withMessages([
                'branch_id' => [__('The selected branch does not belong to the selected accounting entity.')],
            ]);
        }
    }

    //===============================
    protected function isDescendant(int $candidateParentId, int $currentId): bool
    {
        $current = CostCenter::find($candidateParentId);

        while ($current && $current->parent_id) {
            if ($current->parent_id === $currentId) {
                return true;
            }
            $current = CostCenter::find($current->parent_id);
        }

        return false;
    }
}
