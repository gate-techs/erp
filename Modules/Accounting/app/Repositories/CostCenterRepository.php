<?php

namespace Modules\Accounting\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Modules\Accounting\Enums\CostCenterStatus;
use Modules\Accounting\Models\CostCenter;
use Modules\Accounting\Models\Entity;
use Modules\Accounting\Repositories\Contracts\CostCenterRepositoryInterface;
use Modules\Nursery\Models\NurseryBranch;

class CostCenterRepository implements CostCenterRepositoryInterface
{
    //===============================
    public function all(array $filters = []): Collection
    {
        return $this->buildFilterQuery($filters)
            ->with(['translations', 'entity.translations', 'parent.translations', 'branch.translations'])
            ->orderBy('code')
            ->get();
    }

    //===============================
    public function find(int $id, array $with = ['translations', 'entity.translations', 'parent.translations', 'branch.translations']): ?CostCenter
    {
        return CostCenter::with($with)->find($id);
    }

    //===============================
    public function create(array $data): CostCenter
    {
        $costCenter = CostCenter::create([
            'entity_id'  => $data['entity_id'],
            'parent_id'  => $data['parent_id'] ?? null,
            'branch_id'  => $data['branch_id'] ?? null,
            'code'       => $data['code'],
            'type'       => $data['type'],
            'status'     => $data['status'] ?? CostCenterStatus::ACTIVE->value,
            'created_by' => $data['created_by'] ?? null,
        ]);

        $this->saveTranslations($costCenter, $data);

        return $costCenter->fresh(['translations', 'entity.translations', 'parent.translations', 'branch.translations']);
    }

    //===============================
    public function update(int $id, array $data): CostCenter
    {
        $costCenter = CostCenter::findOrFail($id);

        $costCenter->update([
            'entity_id' => $data['entity_id'] ?? $costCenter->entity_id,
            'parent_id' => array_key_exists('parent_id', $data) ? $data['parent_id'] : $costCenter->parent_id,
            'branch_id' => array_key_exists('branch_id', $data) ? $data['branch_id'] : $costCenter->branch_id,
            'code'      => $data['code'] ?? $costCenter->code,
            'type'      => $data['type'] ?? $costCenter->type,
            'status'    => $data['status'] ?? $costCenter->status,
        ]);

        $this->saveTranslations($costCenter, $data);

        return $costCenter->fresh(['translations', 'entity.translations', 'parent.translations', 'branch.translations']);
    }

    //===============================
    public function delete(int $id): bool
    {
        $costCenter = CostCenter::findOrFail($id);

        // Nullify parent_id of any direct children before deleting
        CostCenter::where('parent_id', $id)->update(['parent_id' => $costCenter->parent_id]);

        return (bool) $costCenter->delete();
    }

    //===============================
    public function getDataTableQuery(array $filters = []): Builder
    {
        return $this->buildFilterQuery($filters);
    }

    //===============================
    public function getParentsForEntity(int $entityId, ?int $excludeId = null): Collection
    {
        $query = CostCenter::query()
            ->where('entity_id', $entityId)
            ->where('status', CostCenterStatus::ACTIVE->value)
            ->with('translations');

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->orderBy('code')->get();
    }

    //===============================
    public function getBranchesForEntity(int $entityId): Collection
    {
        $entity = Entity::find($entityId);

        if (!$entity || !$entity->nursery_id) {
            return collect();
        }

        return NurseryBranch::query()
            ->where('nursery_id', $entity->nursery_id)
            ->with('translations')
            ->orderBy('id')
            ->get();
    }

    //===============================
    public function toggleStatus(int $id): CostCenter
    {
        $costCenter = CostCenter::findOrFail($id);
        $newStatus = $costCenter->status === CostCenterStatus::ACTIVE
            ? CostCenterStatus::INACTIVE
            : CostCenterStatus::ACTIVE;

        $costCenter->update(['status' => $newStatus]);

        return $costCenter;
    }

    //===============================
    public function codeExistsInEntity(string $code, int $entityId, ?int $excludeId = null): bool
    {
        $query = CostCenter::query()
            ->where('entity_id', $entityId)
            ->where('code', $code);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    //===============================
    protected function buildFilterQuery(array $filters): Builder
    {
        $query = CostCenter::query()->select('accounting_cost_centers.*');

        if (!empty($filters['entity_id'])) {
            $query->where('accounting_cost_centers.entity_id', $filters['entity_id']);
        }

        if (!empty($filters['type'])) {
            $query->where('accounting_cost_centers.type', $filters['type']);
        }

        if (!empty($filters['status'])) {
            $query->where('accounting_cost_centers.status', $filters['status']);
        }

        if (!empty($filters['parent_id'])) {
            $query->where('accounting_cost_centers.parent_id', $filters['parent_id']);
        }

        if (!empty($filters['branch_id'])) {
            $query->where('accounting_cost_centers.branch_id', $filters['branch_id']);
        }

        return $query;
    }

    //===============================
    protected function saveTranslations(CostCenter $costCenter, array $data): void
    {
        if (isset($data['name_ar']) || isset($data['description_ar'])) {
            $ar = $costCenter->translateOrNew('ar');
            if (isset($data['name_ar'])) {
                $ar->name = $data['name_ar'];
            }
            if (isset($data['description_ar'])) {
                $ar->description = $data['description_ar'];
            }
        }

        if (isset($data['name_en']) || isset($data['description_en'])) {
            $en = $costCenter->translateOrNew('en');
            if (isset($data['name_en'])) {
                $en->name = $data['name_en'];
            }
            if (isset($data['description_en'])) {
                $en->description = $data['description_en'];
            }
        }

        $costCenter->save();
    }
}
