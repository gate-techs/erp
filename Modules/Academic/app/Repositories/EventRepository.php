<?php

namespace Modules\Academic\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Academic\Models\AcademicClass;
use Modules\Academic\Models\Event;
use Modules\Academic\Repositories\Contracts\EventRepositoryInterface;
use Modules\Nursery\Models\Nursery;
use Modules\Nursery\Models\NurseryBranch;
use Modules\Settings\Enums\GeneralSettingType;
use Modules\Settings\Models\GeneralSetting;

class EventRepository implements EventRepositoryInterface
{
    public function getFilteredQuery(array $filters = []): Builder
    {
        $query = Event::with([
            'translations',
            'institution.translations',
            'branch.translations',
            'classroom.translations',
            'eventType.translations',
        ])->latest('id');

        if (!empty($filters['institution_id'])) {
            $query->where('institution_id', $filters['institution_id']);
        }

        if (!empty($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }

        if (!empty($filters['academic_class_id'])) {
            $query->where('academic_class_id', $filters['academic_class_id']);
        }

        if (!empty($filters['event_type_id'])) {
            $query->where('event_type_id', $filters['event_type_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['start_date'])) {
            $query->whereDate('start_date', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('end_date', '<=', $filters['end_date']);
        }

        $search = is_array($filters['search'] ?? null)
            ? ($filters['search']['value'] ?? null)
            : ($filters['search'] ?? null);

        if (!empty($search) && is_string($search)) {
            $query->where(function (Builder $q) use ($search) {
                $q->whereTranslationLike('name', "%{$search}%")
                  ->orWhereTranslationLike('description', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%")
                  ->orWhere('price', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    public function find(int $id): ?Event
    {
        return Event::with([
            'translations',
            'institution.translations',
            'branch.translations',
            'classroom.translations',
            'eventType.translations',
        ])->find($id);
    }

    public function create(array $data): Event
    {
        return Event::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $event = $this->find($id);
        if (!$event) {
            return false;
        }

        return $event->update($data);
    }

    public function delete(int $id): bool
    {
        $event = $this->find($id);
        if (!$event) {
            return false;
        }

        return (bool) $event->delete();
    }

    public function toggleStatus(int $id): bool
    {
        $event = $this->find($id);
        if (!$event) {
            return false;
        }

        $event->status = ($event->status === Event::STATUS_ACTIVE)
            ? Event::STATUS_INACTIVE
            : Event::STATUS_ACTIVE;

        return $event->save();
    }

    public function getInstitutions(): Collection
    {
        return Nursery::with('translations')->orderBy('id')->get();
    }

    public function getBranches(?int $institutionId = null): Collection
    {
        return NurseryBranch::with('translations')
            ->when($institutionId, fn ($q) => $q->where('nursery_id', $institutionId))
            ->orderBy('id')
            ->get();
    }

    public function getClasses(?int $branchId = null, ?int $institutionId = null): Collection
    {
        return AcademicClass::with('translations')
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->when($institutionId, fn ($q) => $q->where('institution_id', $institutionId))
            ->orderBy('id')
            ->get();
    }

    public function getEventTypes(): Collection
    {
        return GeneralSetting::ofType(GeneralSettingType::EVENT_TYPE)
            ->with('translations')
            ->where('status', true)
            ->orderBy('id')
            ->get();
    }
}
