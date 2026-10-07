<?php

namespace Modules\Academic\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Academic\Models\AcademicFeeType;
use Modules\Academic\Repositories\Contracts\AcademicFeeTypeRepositoryInterface;

class AcademicFeeTypeService
{
    public function __construct(
        protected AcademicFeeTypeRepositoryInterface $repository
    ) {}

    public function getFilteredQuery(array $filters = []): Builder
    {
        return $this->repository->getFilteredQuery($filters);
    }

    public function all(): Collection
    {
        return $this->repository->all();
    }

    public function find(int $id): ?AcademicFeeType
    {
        return $this->repository->find($id);
    }

    public function create(array $data): AcademicFeeType
    {
        return DB::transaction(function () use ($data) {
            $payload = $this->formatTranslationData($data);

            return $this->repository->create($payload);
        });
    }

    public function update(int $id, array $data): AcademicFeeType
    {
        return DB::transaction(function () use ($id, $data) {
            $payload = $this->formatTranslationData($data);

            return $this->repository->update($id, $payload);
        });
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function toggleStatus(int $id): ?AcademicFeeType
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
