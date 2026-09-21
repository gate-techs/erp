<?php

namespace Modules\HR\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\HR\Models\JobApplication;

interface JobApplicationRepositoryInterface
{
    public function getKanbanData(?int $jobOpeningId = null, ?string $search = null): array;

    public function getDataTableQuery(array $filters = []): Builder;

    public function getPipelineKpis(?int $jobOpeningId = null): array;

    public function findById(int $id): ?JobApplication;

    public function findByCode(string $code): ?JobApplication;

    public function create(array $data): JobApplication;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    public function generateApplicationCode(): string;
}
