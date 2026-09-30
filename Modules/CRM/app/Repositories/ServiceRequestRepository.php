<?php

namespace Modules\CRM\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Modules\CRM\Models\ServiceRequest;
use Modules\CRM\Repositories\Contracts\ServiceRequestRepositoryInterface;

class ServiceRequestRepository implements ServiceRequestRepositoryInterface
{
    public function getAll(array $filters = []): Collection
    {
        return $this->buildQuery($filters)->get();
    }

    public function getDataTableQuery(array $filters = []): Builder
    {
        return $this->buildQuery($filters);
    }

    public function findById(int $id): ?ServiceRequest
    {
        return ServiceRequest::query()
            ->with(['lead', 'branch', 'department', 'attachments'])
            ->find($id);
    }

    public function create(array $data): ServiceRequest
    {
        return DB::transaction(fn () => ServiceRequest::create($data));
    }

    public function update(int|ServiceRequest $request, array $data): ServiceRequest
    {
        $serviceRequest = $request instanceof ServiceRequest
            ? $request
            : ServiceRequest::findOrFail($request);

        return DB::transaction(function () use ($serviceRequest, $data) {
            $serviceRequest->update($data);

            return $serviceRequest->fresh(['lead', 'branch', 'department', 'attachments']);
        });
    }

    public function delete(int|ServiceRequest $request): bool
    {
        $serviceRequest = $request instanceof ServiceRequest
            ? $request
            : ServiceRequest::findOrFail($request);

        return DB::transaction(fn () => (bool) $serviceRequest->delete());
    }

    protected function buildQuery(array $filters = []): Builder
    {
        $query = ServiceRequest::query()
            ->with(['lead', 'branch', 'department'])
            ->latest('id');

        foreach (['type', 'status', 'branch_id', 'department_id'] as $field) {
            if (isset($filters[$field]) && $filters[$field] !== '') {
                $query->where($field, $filters[$field]);
            }
        }

        if (!empty($filters['search'])) {
            $search = is_array($filters['search'])
                ? ($filters['search']['value'] ?? null)
                : $filters['search'];

            if (is_string($search) && $search !== '') {
                $query->where(function (Builder $builder) use ($search) {
                    $builder->where('civil_id', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('call_recording_number', 'like', "%{$search}%");
                });
            }
        }

        return $query;
    }
}
