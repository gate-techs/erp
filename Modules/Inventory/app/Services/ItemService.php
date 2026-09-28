<?php

namespace Modules\Inventory\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Modules\Inventory\Models\Item;
use Modules\Inventory\Repositories\Contracts\ItemRepositoryInterface;

class ItemService
{
    public function __construct(
        protected ItemRepositoryInterface $repository
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
    public function getById(int $id): Item
    {
        $item = $this->repository->findById($id);

        if (!$item) {
            abort(404, __('Item not found.'));
        }

        return $item;
    }

    //------------------------------------------
    public function create(array $data, ?int $userId = null): Item
    {
        $this->validateUniqueCode($data['code']);

        if (!empty($data['barcode'])) {
            $this->validateUniqueBarcode($data['barcode']);
        }

        if (!empty($data['image'])) {
            $data['image_path'] = $data['image']->store('items', 'public');
            unset($data['image']);
        }

        $data['created_by'] = $data['created_by'] ?? $userId ?? auth()->id();

        return $this->repository->create($this->formatTranslationData($data));
    }

    //------------------------------------------
    public function update(int $id, array $data): Item
    {
        $item = $this->getById($id);

        if (isset($data['code'])) {
            $this->validateUniqueCode($data['code'], $id);
        }

        if (!empty($data['barcode'])) {
            $this->validateUniqueBarcode($data['barcode'], $id);
        }

        if (!empty($data['image'])) {
            if ($item->image_path) {
                Storage::disk('public')->delete($item->image_path);
            }
            $data['image_path'] = $data['image']->store('items', 'public');
            unset($data['image']);
        }

        return $this->repository->update($item, $this->formatTranslationData($data));
    }

    //------------------------------------------
    public function delete(int $id): bool
    {
        $item = $this->getById($id);

        if ($item->stock()->where('quantity', '>', 0)->exists()) {
            throw ValidationException::withMessages([
                'item' => __('Cannot delete an item that has stock in warehouses.'),
            ]);
        }

        if ($item->image_path) {
            Storage::disk('public')->delete($item->image_path);
        }

        return $this->repository->delete($item);
    }

    //------------------------------------------
    public function toggleStatus(int $id): Item
    {
        return $this->repository->toggleStatus($this->getById($id));
    }

    //------------------------------------------
    protected function validateUniqueCode(string $code, ?int $excludeId = null): void
    {
        if ($this->repository->codeExists($code, $excludeId)) {
            throw ValidationException::withMessages([
                'code' => __('The item code has already been taken.'),
            ]);
        }
    }

    //------------------------------------------
    protected function validateUniqueBarcode(string $barcode, ?int $excludeId = null): void
    {
        if ($this->repository->barcodeExists($barcode, $excludeId)) {
            throw ValidationException::withMessages([
                'barcode' => __('The barcode has already been taken.'),
            ]);
        }
    }

    //------------------------------------------
    protected function formatTranslationData(array $data): array
    {
        $result = $data;

        if (array_key_exists('name_ar', $data) || array_key_exists('description_ar', $data)) {
            $result['ar'] = [
                'name'        => $data['name_ar'] ?? ($result['ar']['name'] ?? ''),
                'description' => $data['description_ar'] ?? ($result['ar']['description'] ?? null),
            ];
            unset($result['name_ar'], $result['description_ar']);
        }

        if (array_key_exists('name_en', $data) || array_key_exists('description_en', $data)) {
            $result['en'] = [
                'name'        => $data['name_en'] ?? ($result['en']['name'] ?? ''),
                'description' => $data['description_en'] ?? ($result['en']['description'] ?? null),
            ];
            unset($result['name_en'], $result['description_en']);
        }

        return $result;
    }
}
