<?php

namespace Modules\Inventory\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;
use Modules\Inventory\Models\Unit;
use Modules\Inventory\Repositories\Contracts\UnitRepositoryInterface;

class UnitService
{
    public function __construct(
        protected UnitRepositoryInterface $repository
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
    public function getById(int $id): Unit
    {
        $unit = $this->repository->findById($id);

        if (!$unit) {
            abort(404, __('Unit not found.'));
        }

        return $unit;
    }

    //------------------------------------------
    public function create(array $data, ?int $userId = null): Unit
    {
        $this->validateUniqueSymbol($data['symbol']);
        $data['created_by'] = $data['created_by'] ?? $userId ?? auth()->id();

        return $this->repository->create($this->formatTranslationData($data));
    }

    //------------------------------------------
    public function update(int $id, array $data): Unit
    {
        $unit = $this->getById($id);

        if (isset($data['symbol'])) {
            $this->validateUniqueSymbol($data['symbol'], $id);
        }

        return $this->repository->update($unit, $this->formatTranslationData($data));
    }

    //------------------------------------------
    public function delete(int $id): bool
    {
        $unit = $this->getById($id);

        if ($unit->items()->exists()) {
            throw ValidationException::withMessages([
                'unit' => __('This unit is used by items and cannot be deleted.'),
            ]);
        }

        return $this->repository->delete($unit);
    }

    //------------------------------------------
    public function toggleStatus(int $id): Unit
    {
        return $this->repository->toggleStatus($this->getById($id));
    }

    //------------------------------------------
    protected function validateUniqueSymbol(string $symbol, ?int $excludeId = null): void
    {
        if ($this->repository->symbolExists($symbol, $excludeId)) {
            throw ValidationException::withMessages([
                'symbol' => __('The unit symbol has already been taken.'),
            ]);
        }
    }

    //------------------------------------------
    protected function formatTranslationData(array $data): array
    {
        $result = $data;

        if (array_key_exists('name_ar', $data)) {
            $result['ar'] = ['name' => $data['name_ar'] ?? ($result['ar']['name'] ?? '')];
            unset($result['name_ar']);
        }

        if (array_key_exists('name_en', $data)) {
            $result['en'] = ['name' => $data['name_en'] ?? ($result['en']['name'] ?? '')];
            unset($result['name_en']);
        }

        return $result;
    }
}
