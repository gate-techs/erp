<?php

namespace Modules\CRM\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Modules\CRM\Models\Lead;
use Modules\CRM\Repositories\Contracts\LeadRepositoryInterface;

class LeadRepository implements LeadRepositoryInterface
{
    public function getAll(array $filters = []): Collection
    {
        return $this->buildQuery($filters)->get();
    }

    public function getDataTableQuery(array $filters = []): Builder
    {
        return $this->buildQuery($filters);
    }

    public function findById(int $id): ?Lead
    {
        return Lead::query()->find($id);
    }

    public function create(array $data): Lead
    {
        return DB::transaction(fn () => Lead::create($data));
    }

    public function update(int|Lead $lead, array $data): Lead
    {
        $lead = $lead instanceof Lead ? $lead : Lead::findOrFail($lead);

        return DB::transaction(function () use ($lead, $data) {
            $lead->update($data);

            return $lead->fresh();
        });
    }

    public function delete(int|Lead $lead): bool
    {
        $lead = $lead instanceof Lead ? $lead : Lead::findOrFail($lead);

        return DB::transaction(fn () => (bool) $lead->delete());
    }

    protected function buildQuery(array $filters = []): Builder
    {
        $query = Lead::query()->latest('id');

        if (!empty($filters['search'])) {
            $search = is_array($filters['search'])
                ? ($filters['search']['value'] ?? null)
                : $filters['search'];

            if (is_string($search) && $search !== '') {
                $query->where(function (Builder $builder) use ($search) {
                    $builder->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('civil_id', 'like', "%{$search}%")
                        ->orWhere('job_title', 'like', "%{$search}%");
                });
            }
        }

        return $query;
    }
}
