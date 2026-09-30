<?php

namespace Modules\CRM\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\CRM\Models\Lead;
use Modules\CRM\Repositories\Contracts\LeadRepositoryInterface;

class LeadService
{
    public function __construct(
        protected LeadRepositoryInterface $repository
    ) {}

    public function getAllLeads(array $filters = []): Collection
    {
        return $this->repository->getAll($filters);
    }

    public function getDataTableQuery(array $filters = []): Builder
    {
        return $this->repository->getDataTableQuery($filters);
    }

    public function getLeadById(int $id): ?Lead
    {
        return $this->repository->findById($id);
    }

    public function createLead(array $data): Lead
    {
        return $this->repository->create($data);
    }

    public function updateLead(int|Lead $lead, array $data): Lead
    {
        return $this->repository->update($lead, $data);
    }

    public function deleteLead(int|Lead $lead): bool
    {
        return $this->repository->delete($lead);
    }
}
