<?php

namespace Modules\Accounting\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Accounting\Models\Entity;
use Modules\Accounting\Repositories\Contracts\EntityRepositoryInterface;

class EntityService
{
    //--------------------------------------------------
    public function __construct(
        protected EntityRepositoryInterface $repository,
        protected ChartOfAccountsTemplateService $coaTemplateService
    ) {}

    //--------------------------------------------------
    public function getAllEntities(array $filters = []): Collection
    {
        return $this->repository->getAll($filters);
    }

    //--------------------------------------------------
    public function getPaginatedEntities(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getPaginated($filters, $perPage);
    }

    //--------------------------------------------------
    public function getDataTableQuery(array $filters = []): Builder
    {
        return $this->repository->getDataTableQuery($filters);
    }

    //--------------------------------------------------
    public function getEntityById(int $id): ?Entity
    {
        return $this->repository->findById($id);
    }

    //--------------------------------------------------
    public function getAvailableNurseries(): Collection
    {
        return $this->repository->getAvailableNurseries();
    }

    //--------------------------------------------------
    public function createEntity(array $data, ?int $userId = null): Entity
    {
        if ($userId && !isset($data['created_by'])) {
            $data['created_by'] = $userId;
        }

        $coaAction = $data['coa_action'] ?? 'standard_template';
        $cloneEntityId = !empty($data['clone_from_entity_id']) ? (int) $data['clone_from_entity_id'] : null;

        return DB::transaction(function () use ($data, $userId, $coaAction, $cloneEntityId) {
            $entity = $this->repository->create($this->prepareData($data));

            if ($coaAction === 'standard_template') {
                $this->coaTemplateService->generateTemplateForEntity($entity, $userId);
            } elseif ($coaAction === 'clone_entity' && $cloneEntityId) {
                $this->coaTemplateService->cloneChartFromEntity($cloneEntityId, $entity, $userId);
            }

            return $entity;
        });
    }

    //--------------------------------------------------
    public function updateEntity(int|Entity $entity, array $data): Entity
    {
        return $this->repository->update($entity, $this->prepareData($data));
    }

    //--------------------------------------------------
    public function deleteEntity(int|Entity $entity): bool
    {
        return $this->repository->delete($entity);
    }

    //--------------------------------------------------
    public function generateEntityCode(): string
    {
        $lastEntity = Entity::withTrashed()->latest('id')->first();
        $nextNumber = $lastEntity ? ($lastEntity->id + 1) : 1;

        do {
            $code = 'ENT-' . str_pad((string)$nextNumber, 4, '0', STR_PAD_LEFT);
            $exists = Entity::withTrashed()->where('code', $code)->exists();
            $nextNumber++;
        } while ($exists);

        return $code;
    }

    //--------------------------------------------------
    protected function prepareData(array $input): array
    {
        $data = $input;

        if (empty($data['code'])) {
            $data['code'] = $this->generateEntityCode();
        }

        if (isset($data['type']) && $data['type'] === 'other') {
            $data['nursery_id'] = null;
        }

        foreach (['name_ar', 'name_en', 'description_ar', 'description_en', 'coa_action', 'clone_from_entity_id'] as $field) {
            unset($data[$field]);
        }

        if (array_key_exists('nursery_id', $input)) {
            $data['nursery_id'] = $input['nursery_id'] ? (int) $input['nursery_id'] : null;
        }

        $nameAr = $input['name_ar'] ?? '';
        $nameEn = !empty($input['name_en']) ? $input['name_en'] : $nameAr;

        $data['ar'] = [
            'name' => $nameAr,
            'description' => $input['description_ar'] ?? null,
        ];

        $data['en'] = [
            'name' => $nameEn,
            'description' => $input['description_en'] ?? null,
        ];

        return $data;
    }
}
