<?php

namespace Modules\Academic\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\Academic\Models\AcademicFee;
use Modules\Academic\Repositories\Contracts\AcademicFeeRepositoryInterface;

class AcademicFeeService
{
    public function __construct(
        protected AcademicFeeRepositoryInterface $repository
    ) {}

    public function getFilteredQuery(array $filters = []): Builder
    {
        return $this->repository->getFilteredQuery($filters);
    }

    public function all(): Collection
    {
        return $this->repository->all();
    }

    public function find(int $id): ?AcademicFee
    {
        return $this->repository->find($id);
    }

    public function create(array $data, ?UploadedFile $image = null): AcademicFee
    {
        return DB::transaction(function () use ($data, $image) {
            if ($image) {
                $data['image'] = $image->store('academic/fees/images', 'public');
            }

            $payload = $this->formatTranslationData($data);

            return $this->repository->create($payload);
        });
    }

    public function update(int $id, array $data, ?UploadedFile $image = null): AcademicFee
    {
        return DB::transaction(function () use ($id, $data, $image) {
            $fee = $this->find($id);

            if ($image) {
                if ($fee && $fee->image && Storage::disk('public')->exists($fee->image)) {
                    Storage::disk('public')->delete($fee->image);
                }
                $data['image'] = $image->store('academic/fees/images', 'public');
            }

            $payload = $this->formatTranslationData($data);

            return $this->repository->update($id, $payload);
        });
    }

    public function delete(int $id): bool
    {
        $fee = $this->find($id);
        if ($fee && $fee->image && Storage::disk('public')->exists($fee->image)) {
            Storage::disk('public')->delete($fee->image);
        }

        return $this->repository->delete($id);
    }

    public function toggleStatus(int $id): ?AcademicFee
    {
        return $this->repository->toggleStatus($id);
    }

    protected function formatTranslationData(array $data): array
    {
        $payload = $data;

        $payload['ar'] = [
            'name'        => $data['name_ar'] ?? ($data['ar']['name'] ?? ''),
            'description' => $data['description_ar'] ?? ($data['ar']['description'] ?? null),
        ];

        $payload['en'] = [
            'name'        => $data['name_en'] ?? ($data['en']['name'] ?? ''),
            'description' => $data['description_en'] ?? ($data['en']['description'] ?? null),
        ];

        return $payload;
    }
}
