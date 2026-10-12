<?php

namespace Modules\Academic\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Academic\Models\PersonalCalendar;
use Modules\Academic\Repositories\Contracts\PersonalCalendarRepositoryInterface;

class PersonalCalendarRepository implements PersonalCalendarRepositoryInterface
{
    //------------------------------
    public function getFilteredQuery(int $userId, array $filters = []): Builder
    {
        $query = PersonalCalendar::with(['institution.translations'])
            ->where('user_id', $userId);

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['institution_id'])) {
            $query->where('institution_id', $filters['institution_id']);
        }

        if (! empty($filters['search'])) {
            $searchTerm = $filters['search'];
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        return $query;
    }

    //------------------------------
    public function all(int $userId, array $filters = []): Collection
    {
        return $this->getFilteredQuery($userId, $filters)
            ->orderBy('event_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();
    }

    //------------------------------
    public function getCalendarEvents(int $userId, array $filters = []): Collection
    {
        $query = PersonalCalendar::with(['institution.translations'])
            ->where('user_id', $userId);

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['institution_id'])) {
            $query->where('institution_id', $filters['institution_id']);
        }

        if (! empty($filters['start']) && ! empty($filters['end'])) {
            $start = date('Y-m-d', strtotime($filters['start']));
            $end   = date('Y-m-d', strtotime($filters['end']));
            $query->whereBetween('event_date', [$start, $end]);
        }

        return $query->orderBy('event_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();
    }

    //------------------------------
    public function find(int $id, ?int $userId = null): ?PersonalCalendar
    {
        $query = PersonalCalendar::with(['institution.translations']);

        if ($userId !== null) {
            $query->where('user_id', $userId);
        }

        return $query->find($id);
    }

    //------------------------------
    public function create(array $data): PersonalCalendar
    {
        return PersonalCalendar::create([
            'user_id'        => $data['user_id'] ?? auth()->id(),
            'institution_id' => $data['institution_id'] ?? null,
            'title'          => $data['title'],
            'description'    => $data['description'] ?? null,
            'event_date'     => $data['event_date'],
            'start_time'     => $data['start_time'] ?? null,
            'end_time'       => $data['end_time'] ?? null,
            'is_all_day'     => (bool) ($data['is_all_day'] ?? false),
            'status'         => $data['status'] ?? PersonalCalendar::STATUS_PLANNED,
            'color'          => $data['color'] ?? '#0b3252',
        ]);
    }

    //------------------------------
    public function update(int $id, array $data, ?int $userId = null): bool
    {
        $item = $this->find($id, $userId);

        if (! $item) {
            return false;
        }

        return (bool) $item->update([
            'institution_id' => array_key_exists('institution_id', $data) ? $data['institution_id'] : $item->institution_id,
            'title'          => $data['title'] ?? $item->title,
            'description'    => array_key_exists('description', $data) ? $data['description'] : $item->description,
            'event_date'     => $data['event_date'] ?? $item->event_date,
            'start_time'     => array_key_exists('start_time', $data) ? $data['start_time'] : $item->start_time,
            'end_time'       => array_key_exists('end_time', $data) ? $data['end_time'] : $item->end_time,
            'is_all_day'     => array_key_exists('is_all_day', $data) ? (bool) $data['is_all_day'] : $item->is_all_day,
            'status'         => $data['status'] ?? $item->status,
            'color'          => $data['color'] ?? $item->color,
        ]);
    }

    //------------------------------
    public function delete(int $id, ?int $userId = null): bool
    {
        $item = $this->find($id, $userId);

        if (! $item) {
            return false;
        }

        return (bool) $item->delete();
    }

    //------------------------------
    public function updateStatus(int $id, string $status, ?int $userId = null): bool
    {
        $item = $this->find($id, $userId);

        if (! $item) {
            return false;
        }

        $item->status = $status;

        return (bool) $item->save();
    }
}
