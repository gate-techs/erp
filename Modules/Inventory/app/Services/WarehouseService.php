<?php

namespace Modules\Inventory\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;
use Modules\Inventory\Models\Warehouse;
use Modules\Inventory\Repositories\Contracts\WarehouseRepositoryInterface;
use Modules\Accounting\Models\Entity;
use Modules\Nursery\Models\NurseryBranch;

class WarehouseService
{
    public function __construct(
        protected WarehouseRepositoryInterface $repository
    ) {}

    //------------------------------------------
    public function getAll(array $filters = []): Collection
    {
        return $this->repository->getAll($filters);
    }

    //------------------------------------------
    public function getDataTableQuery(array $filters = []): Builder
    {
        return $this->repository->getDataTableQuery($filters);
    }

    //------------------------------------------
    public function getById(int $id): Warehouse
    {
        $warehouse = $this->repository->findById($id);

        if (!$warehouse) {
            abort(404, __('Warehouse not found.'));
        }

        return $warehouse;
    }

    //------------------------------------------
    public function create(array $data, ?int $userId = null): Warehouse
    {
        if (($data['type'] ?? 'branch') === 'main' || empty($data['branch_id'])) {
            $data['branch_id'] = null;
        }

        $this->validateWarehouseData($data);
        $this->validateUniqueCode($data['code']);

        $data['created_by'] = $data['created_by'] ?? $userId ?? auth()->id();

        return $this->repository->create($this->formatTranslationData($data));
    }

    //------------------------------------------
    public function update(int $id, array $data): Warehouse
    {
        $warehouse = $this->getById($id);

        $type = $data['type'] ?? $warehouse->type;
        if ($type === 'main' || (array_key_exists('branch_id', $data) && empty($data['branch_id']))) {
            $data['branch_id'] = null;
        }

        $this->validateWarehouseData($data, $warehouse);

        if (isset($data['code'])) {
            $this->validateUniqueCode($data['code'], $id);
        }

        return $this->repository->update($warehouse, $this->formatTranslationData($data));
    }

    //------------------------------------------
    public function delete(int $id): bool
    {
        return $this->repository->delete($this->getById($id));
    }

    //------------------------------------------
    public function toggleStatus(int $id): Warehouse
    {
        return $this->repository->toggleStatus($this->getById($id));
    }

    //------------------------------------------
    protected function validateWarehouseData(array $data, ?Warehouse $warehouse = null): void
    {
        $entityId = (int) ($data['entity_id'] ?? $warehouse?->entity_id);
        $type = $data['type'] ?? $warehouse?->type ?? 'branch';
        $branchId = array_key_exists('branch_id', $data) ? $data['branch_id'] : $warehouse?->branch_id;
        $branchId = !empty($branchId) ? (int) $branchId : null;

        $entity = Entity::find($entityId);
        if (!$entity) {
            throw ValidationException::withMessages([
                'entity_id' => __('The selected accounting entity is invalid.'),
            ]);
        }

        if (!in_array($type, ['main', 'branch'], true)) {
            throw ValidationException::withMessages([
                'type' => __('The warehouse type is invalid.'),
            ]);
        }

        if ($type === 'main' && $branchId !== null) {
            throw ValidationException::withMessages([
                'branch_id' => __('The main warehouse cannot be assigned to a branch.'),
            ]);
        }

        if ($type === 'branch' && !$branchId) {
            throw ValidationException::withMessages([
                'branch_id' => __('A branch warehouse must be assigned to a branch.'),
            ]);
        }

        if ($branchId) {
            $branch = NurseryBranch::find($branchId);
            if (!$branch || (int) $branch->nursery_id !== (int) $entity->nursery_id) {
                throw ValidationException::withMessages([
                    'branch_id' => __('The selected branch does not belong to the selected accounting entity.'),
                ]);
            }

            if ($this->repository->branchHasWarehouse($entityId, (int) $branchId, $warehouse?->id)) {
                throw ValidationException::withMessages([
                    'branch_id' => __('This branch already has a warehouse for the selected accounting entity.'),
                ]);
            }
        }
    }

    //------------------------------------------
    protected function validateUniqueCode(string $code, ?int $excludeId = null): void
    {
        if ($this->repository->codeExists($code, $excludeId)) {
            throw ValidationException::withMessages([
                'code' => __('The warehouse code has already been taken.'),
            ]);
        }
    }

    //------------------------------------------
    protected function formatTranslationData(array $data): array
    {
        $result = $data;

        if (array_key_exists('name_ar', $data) || array_key_exists('description_ar', $data)) {
            $result['ar'] = [
                'name' => $data['name_ar'] ?? ($result['ar']['name'] ?? ''),
                'description' => $data['description_ar'] ?? ($result['ar']['description'] ?? null),
            ];
            unset($result['name_ar'], $result['description_ar']);
        }

        if (array_key_exists('name_en', $data) || array_key_exists('description_en', $data)) {
            $result['en'] = [
                'name' => $data['name_en'] ?? ($result['en']['name'] ?? ''),
                'description' => $data['description_en'] ?? ($result['en']['description'] ?? null),
            ];
            unset($result['name_en'], $result['description_en']);
        }

        return $result;
    }
}
