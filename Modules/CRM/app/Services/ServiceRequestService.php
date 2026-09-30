<?php

namespace Modules\CRM\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\CRM\Enums\ServiceRequestStatus;
use Modules\CRM\Models\Lead;
use Modules\CRM\Models\ServiceRequest;
use Modules\CRM\Repositories\Contracts\ServiceRequestRepositoryInterface;

class ServiceRequestService
{
    public function __construct(
        protected ServiceRequestRepositoryInterface $repository
    ) {}

    public function getAll(array $filters = []): Collection
    {
        return $this->repository->getAll($filters);
    }

    public function getDataTableQuery(array $filters = []): Builder
    {
        return $this->repository->getDataTableQuery($filters);
    }

    public function findById(int $id): ?ServiceRequest
    {
        return $this->repository->findById($id);
    }

    public function create(array $data, ?UploadedFile $image = null, array $attachments = []): ServiceRequest
    {
        return DB::transaction(function () use ($data, $image, $attachments) {
            $lead = $this->findOrCreateLead($data);
            $data['lead_id'] = $lead->id;
            $data['status'] ??= ServiceRequestStatus::PENDING->value;

            if ($image?->isValid()) {
                $data['image'] = $image->store('crm/service-requests/images', 'public');
            }

            $request = $this->repository->create($data);
            $this->storeAttachments($request, $attachments);

            return $request->fresh(['lead', 'branch', 'department', 'attachments']);
        });
    }

    public function update(ServiceRequest $request, array $data, ?UploadedFile $image = null, array $attachments = []): ServiceRequest
    {
        return DB::transaction(function () use ($request, $data, $image, $attachments) {
            $lead = $this->findOrCreateLead($data);
            $data['lead_id'] = $lead->id;

            if ($image?->isValid()) {
                if ($request->image) {
                    Storage::disk('public')->delete($request->image);
                }
                $data['image'] = $image->store('crm/service-requests/images', 'public');
            }

            $updated = $this->repository->update($request, $data);
            $this->storeAttachments($updated, $attachments);

            return $updated->fresh(['lead', 'branch', 'department', 'attachments']);
        });
    }

    public function delete(ServiceRequest $request): bool
    {
        return DB::transaction(function () use ($request) {
            if ($request->image) {
                Storage::disk('public')->delete($request->image);
            }

            foreach ($request->attachments as $attachment) {
                Storage::disk('public')->delete($attachment->file_path);
            }

            return $this->repository->delete($request);
        });
    }

    protected function findOrCreateLead(array $data): Lead
    {
        $lead = Lead::query()->where('civil_id', $data['civil_id'])->first();

        if ($lead) {
            $lead->update([
                'name' => $data['name'],
                'email' => $data['email'] ?? $lead->email,
            ]);

            return $lead;
        }

        return Lead::create([
            'civil_id' => $data['civil_id'],
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => null,
        ]);
    }

    protected function storeAttachments(ServiceRequest $request, array $attachments): void
    {
        foreach ($attachments as $attachment) {
            if (!$attachment instanceof UploadedFile || !$attachment->isValid()) {
                continue;
            }

            $request->attachments()->create([
                'file_path' => $attachment->store('crm/service-requests/attachments', 'public'),
                'original_name' => $attachment->getClientOriginalName(),
                'mime_type' => $attachment->getClientMimeType(),
                'file_size' => $attachment->getSize(),
            ]);
        }
    }
}
