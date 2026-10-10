<?php

namespace Modules\Nursery\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Nursery\Models\Package;

interface PackageRepositoryInterface
{
    public function getFilteredQuery(array $filters = []): Builder;

    public function all(): Collection;

    public function find(int $id): ?Package;

    public function create(array $data): Package;

    public function update(int $id, array $data): ?Package;

    public function delete(int $id): bool;

    public function toggleStatus(int $id): ?Package;

    public function getNurseries(): Collection;
}
