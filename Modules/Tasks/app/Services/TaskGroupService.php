<?php

namespace Modules\Tasks\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Modules\Tasks\Models\TaskGroup;
use Modules\Tasks\Repositories\Contracts\TaskGroupRepositoryInterface;

class TaskGroupService
{
    //-----------------------------------------
    public function __construct(
        protected TaskGroupRepositoryInterface $repository
    ) {}

    //-----------------------------------------
    public function getAll(array $filters = []): Collection
    {
        return $this->repository->getAll($filters);
    }

    //-----------------------------------------
    public function getDataTableQuery(array $filters = []): Builder
    {
        return $this->repository->getDataTableQuery($filters);
    }

    //-----------------------------------------
    public function getById(int $id): ?TaskGroup
    {
        return $this->repository->findById($id);
    }

    //-----------------------------------------
    public function create(array $validatedData): TaskGroup
    {
        $payload = $this->formatPayload($validatedData);
        $payload['created_by'] = Auth::id();

        return $this->repository->create($payload);
    }

    //-----------------------------------------
    public function update(int|TaskGroup $taskGroup, array $validatedData): TaskGroup
    {
        $payload = $this->formatPayload($validatedData);

        return $this->repository->update($taskGroup, $payload);
    }

    //-----------------------------------------
    public function delete(int|TaskGroup $taskGroup): bool
    {
        return $this->repository->delete($taskGroup);
    }

    //-----------------------------------------
    public function toggleStatus(int|TaskGroup $taskGroup): bool
    {
        return $this->repository->toggleStatus($taskGroup);
    }

    //-----------------------------------------
    protected function formatPayload(array $data): array
    {
        $payload = [
            'ar' => [
                'name'        => $data['name_ar'] ?? null,
                'description' => $data['description_ar'] ?? null,
            ],
            'en' => [
                'name'        => $data['name_en'] ?? null,
                'description' => $data['description_en'] ?? null,
            ],
            'department_id' => $data['department_id'] ?? null,
            'start_date'    => $data['start_date'] ?? null,
            'end_date'      => $data['end_date'] ?? null,
        ];

        if (array_key_exists('is_active', $data)) {
            $payload['is_active'] = filter_var($data['is_active'], FILTER_VALIDATE_BOOLEAN);
        }

        return $payload;
    }
}
