<?php

namespace Modules\Academic\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Modules\Academic\Models\Event;
use Modules\Academic\Repositories\Contracts\EventRepositoryInterface;

class EventService
{
    public function __construct(
        protected EventRepositoryInterface $repository
    ) {}

    public function getFilteredQuery(array $filters = []): Builder
    {
        return $this->repository->getFilteredQuery($filters);
    }

    public function find(int $id): ?Event
    {
        return $this->repository->find($id);
    }

    public function create(array $data, ?int $userId = null, ?UploadedFile $image = null): Event
    {
        if ($userId && !isset($data['created_by'])) {
            $data['created_by'] = $userId;
        }

        if ($image) {
            $data['image'] = upload_image($image, 'events');
        }

        $payload = [
            'institution_id'    => $data['institution_id'] ?? null,
            'branch_id'         => $data['branch_id'] ?? null,
            'academic_class_id' => $data['academic_class_id'] ?? null,
            'event_type_id'     => $data['event_type_id'] ?? null,
            'image'             => $data['image'] ?? null,
            'start_date'        => $data['start_date'],
            'end_date'          => $data['end_date'] ?? null,
            'start_time'        => $data['start_time'] ?? null,
            'end_time'          => $data['end_time'] ?? null,
            'price'             => $data['price'] ?? 0,
            'status'            => $data['status'] ?? Event::STATUS_ACTIVE,
            'created_by'        => $data['created_by'] ?? null,
            'ar' => [
                'name'        => $data['name_ar'],
                'description' => $data['description_ar'] ?? null,
            ],
            'en' => [
                'name'        => $data['name_en'],
                'description' => $data['description_en'] ?? null,
            ],
        ];

        return $this->repository->create($payload);
    }

    public function update(int $id, array $data, ?UploadedFile $image = null): bool
    {
        $event = $this->repository->find($id);
        if (!$event) {
            return false;
        }

        if ($image) {
            $data['image'] = upload_image($image, 'events', $event->image);
        }

        $payload = [
            'institution_id'    => $data['institution_id'] ?? null,
            'branch_id'         => $data['branch_id'] ?? null,
            'academic_class_id' => $data['academic_class_id'] ?? null,
            'event_type_id'     => $data['event_type_id'] ?? null,
            'start_date'        => $data['start_date'],
            'end_date'          => $data['end_date'] ?? null,
            'start_time'        => $data['start_time'] ?? null,
            'end_time'          => $data['end_time'] ?? null,
            'price'             => $data['price'] ?? 0,
            'status'            => $data['status'] ?? $event->status,
            'ar' => [
                'name'        => $data['name_ar'],
                'description' => $data['description_ar'] ?? null,
            ],
            'en' => [
                'name'        => $data['name_en'],
                'description' => $data['description_en'] ?? null,
            ],
        ];

        if (isset($data['image'])) {
            $payload['image'] = $data['image'];
        }

        return $this->repository->update($id, $payload);
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function toggleStatus(int $id): bool
    {
        return $this->repository->toggleStatus($id);
    }

    public function getFormData(?int $institutionId = null, ?int $branchId = null): array
    {
        return [
            'institutions' => $this->repository->getInstitutions(),
            'branches'     => $this->repository->getBranches($institutionId),
            'classes'      => $this->repository->getClasses($branchId, $institutionId),
            'eventTypes'   => $this->repository->getEventTypes(),
            'statuses'     => Event::statuses(),
        ];
    }

    public function getBranches(?int $institutionId = null): Collection
    {
        return $this->repository->getBranches($institutionId);
    }

    public function getClasses(?int $branchId = null, ?int $institutionId = null): Collection
    {
        return $this->repository->getClasses($branchId, $institutionId);
    }
}
