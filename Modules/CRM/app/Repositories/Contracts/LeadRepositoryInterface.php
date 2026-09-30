<?php

namespace Modules\CRM\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\CRM\Models\Lead;

interface LeadRepositoryInterface
{
    public function getAll(array $filters = []): Collection;
    public function getDataTableQuery(array $filters = []): Builder;
    public function findById(int $id): ?Lead;
    public function create(array $data): Lead;
    public function update(int|Lead $lead, array $data): Lead;
    public function delete(int|Lead $lead): bool;
}
