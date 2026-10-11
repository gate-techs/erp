<?php

namespace Modules\Academic\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Academic\Models\Event;
use Modules\Academic\Repositories\Contracts\EventRepositoryInterface;

class EventRepository implements EventRepositoryInterface
{
    //------------------------------
    public function getFilteredQuery(array $filters = []): Builder
    {
        $query = Event::with([
            'translations',
            'institution.translations',
            'creator',
        ]);

        if (! empty($filters['institution_id'])) {
            $query->where('institution_id', $filters['institution_id']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['search'])) {
            $searchTerm = $filters['search'];
            $query->where(function ($q) use ($searchTerm) {
                $q->whereTranslationLike('title', "%{$searchTerm}%")
                  ->orWhereTranslationLike('description', "%{$searchTerm}%");
            });
        }

        return $query;
    }

    //------------------------------
    public function all(array $filters = []): Collection
    {
        return $this->getFilteredQuery($filters)
            ->orderBy('start_date', 'asc')
            ->get();
    }

    //------------------------------
    public function getCalendarEvents(array $filters = []): Collection
    {
        $query = Event::with([
            'translations',
            'institution.translations',
        ]);

        if (! empty($filters['institution_id'])) {
            $query->where('institution_id', $filters['institution_id']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        } else {
            $query->where('status', Event::STATUS_ACTIVE);
        }

        // Filter by Date Range for Calendar View
        if (! empty($filters['start']) && ! empty($filters['end'])) {
            $start = date('Y-m-d', strtotime($filters['start']));
            $end = date('Y-m-d', strtotime($filters['end']));

            $query->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_date', [$start, $end])
                  ->orWhereBetween('end_date', [$start, $end])
                  ->orWhere(function ($sub) use ($start, $end) {
                      $sub->where('start_date', '<=', $start)
                          ->where('end_date', '>=', $end);
                  });
            });
        }

        return $query->orderBy('start_date', 'asc')->get();
    }

    //------------------------------
    public function find(int $id): ?Event
    {
        return Event::with([
            'translations',
            'institution.translations',
            'creator',
        ])->find($id);
    }

    //------------------------------
    public function create(array $data): Event
    {
        return DB::transaction(function () use ($data) {
            $event = Event::create([
                'institution_id' => $data['institution_id'] ?? null,
                'start_date'     => $data['start_date'],
                'end_date'       => $data['end_date'] ?? null,
                'status'         => $data['status'] ?? Event::STATUS_ACTIVE,
                'created_by'     => auth()->id(),
            ]);

            // Save Arabic Translation
            $arTitle = $data['title_ar'] ?? ($data['ar']['title'] ?? ($data['title'] ?? ''));
            $arDesc = $data['description_ar'] ?? ($data['ar']['description'] ?? ($data['description'] ?? null));
            $event->translateOrNew('ar')->title = $arTitle;
            $event->translateOrNew('ar')->description = $arDesc;

            // Save English Translation
            $enTitle = $data['title_en'] ?? ($data['en']['title'] ?? $arTitle);
            $enDesc = $data['description_en'] ?? ($data['en']['description'] ?? $arDesc);
            $event->translateOrNew('en')->title = $enTitle;
            $event->translateOrNew('en')->description = $enDesc;

            $event->save();

            return $event;
        });
    }

    //------------------------------
    public function update(int $id, array $data): bool
    {
        return DB::transaction(function () use ($id, $data) {
            $event = Event::findOrFail($id);

            $event->update([
                'institution_id' => $data['institution_id'] ?? $event->institution_id,
                'start_date'     => $data['start_date'] ?? $event->start_date,
                'end_date'       => array_key_exists('end_date', $data) ? $data['end_date'] : $event->end_date,
                'status'         => $data['status'] ?? $event->status,
            ]);

            // Update Arabic Translation
            if (isset($data['title_ar']) || isset($data['ar']['title'])) {
                $event->translateOrNew('ar')->title = $data['title_ar'] ?? $data['ar']['title'];
            }
            if (array_key_exists('description_ar', $data) || isset($data['ar']['description'])) {
                $event->translateOrNew('ar')->description = $data['description_ar'] ?? ($data['ar']['description'] ?? null);
            }

            // Update English Translation
            if (isset($data['title_en']) || isset($data['en']['title'])) {
                $event->translateOrNew('en')->title = $data['title_en'] ?? $data['en']['title'];
            }
            if (array_key_exists('description_en', $data) || isset($data['en']['description'])) {
                $event->translateOrNew('en')->description = $data['description_en'] ?? ($data['en']['description'] ?? null);
            }

            return $event->save();
        });
    }

    //------------------------------
    public function delete(int $id): bool
    {
        $event = Event::findOrFail($id);

        return (bool) $event->delete();
    }

    //------------------------------
    public function toggleStatus(int $id): bool
    {
        $event = Event::findOrFail($id);
        $newStatus = $event->status === Event::STATUS_ACTIVE ? Event::STATUS_INACTIVE : Event::STATUS_ACTIVE;
        $event->status = $newStatus;

        return (bool) $event->save();
    }
}
