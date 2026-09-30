<?php

namespace Modules\CRM\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\CRM\Models\ServiceRequest;

interface ServiceRequestRepositoryInterface
{
    public function getAll(array $filters = []): Collection;

    public function getDataTableQuery(array $filters = []): Builder;

    public function findById(int $id): ?ServiceRequest;

    public function create(array $data): ServiceRequest;

    public function update(int|ServiceRequest $request, array $data): ServiceRequest;

    public function delete(int|ServiceRequest $request): bool;
}
