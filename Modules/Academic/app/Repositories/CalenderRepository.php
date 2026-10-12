<?php

namespace Modules\Academic\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Academic\Models\Calender;
use Modules\Academic\Repositories\Contracts\CalenderRepositoryInterface;

class CalenderRepository implements CalenderRepositoryInterface
{
    //------------------------------
    public function getFilteredQuery(array $filters = []): Builder
    {
        $query = Calender::with([
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
        $query = Calender::with([
            'translations',
            'institution.translations',
        ]);

        if (! empty($filters['institution_id'])) {
            $query->where('institution_id', $filters['institution_id']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        } else {
            $query->where('status', Calender::STATUS_ACTIVE);
        }

        // Filter by Date Range for Calendar View
        if (! empty($filters['start']) && ! empty($filters['end'])) {
            $start = date('Y-m-d', strtotime($filters['start']));
            $end   = date('Y-m-d', strtotime($filters['end']));

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
    public function find(int $id): ?Calender
    {
        return Calender::with([
            'translations',
            'institution.translations',
            'creator',
        ])->find($id);
    }

    //------------------------------
    public function create(array $data): Calender
    {
        return DB::transaction(function () use ($data) {
            $calender = Calender::create([
                'institution_id' => $data['institution_id'] ?? null,
                'start_date'     => $data['start_date'],
                'end_date'       => $data['end_date'] ?? null,
                'status'         => $data['status'] ?? Calender::STATUS_ACTIVE,
                'created_by'     => auth()->id(),
            ]);

            // Save Arabic Translation
            $arTitle = $data['title_ar'] ?? ($data['ar']['title'] ?? ($data['title'] ?? ''));
            $arDesc  = $data['description_ar'] ?? ($data['ar']['description'] ?? ($data['description'] ?? null));
            $calender->translateOrNew('ar')->title = $arTitle;
            $calender->translateOrNew('ar')->description = $arDesc;

            // Save English Translation
            $enTitle = $data['title_en'] ?? ($data['en']['title'] ?? $arTitle);
            $enDesc  = $data['description_en'] ?? ($data['en']['description'] ?? $arDesc);
            $calender->translateOrNew('en')->title = $enTitle;
            $calender->translateOrNew('en')->description = $enDesc;

            $calender->save();

            return $calender;
        });
    }

    //------------------------------
    public function update(int $id, array $data): bool
    {
        return DB::transaction(function () use ($id, $data) {
            $calender = Calender::findOrFail($id);

            $calender->update([
                'institution_id' => $data['institution_id'] ?? $calender->institution_id,
                'start_date'     => $data['start_date'] ?? $calender->start_date,
                'end_date'       => array_key_exists('end_date', $data) ? $data['end_date'] : $calender->end_date,
                'status'         => $data['status'] ?? $calender->status,
            ]);

            // Update Arabic Translation
            if (isset($data['title_ar']) || isset($data['ar']['title'])) {
                $calender->translateOrNew('ar')->title = $data['title_ar'] ?? $data['ar']['title'];
            }
            if (array_key_exists('description_ar', $data) || isset($data['ar']['description'])) {
                $calender->translateOrNew('ar')->description = $data['description_ar'] ?? ($data['ar']['description'] ?? null);
            }

            // Update English Translation
            if (isset($data['title_en']) || isset($data['en']['title'])) {
                $calender->translateOrNew('en')->title = $data['title_en'] ?? $data['en']['title'];
            }
            if (array_key_exists('description_en', $data) || isset($data['en']['description'])) {
                $calender->translateOrNew('en')->description = $data['description_en'] ?? ($data['en']['description'] ?? null);
            }

            return $calender->save();
        });
    }

    //------------------------------
    public function delete(int $id): bool
    {
        $calender = Calender::findOrFail($id);

        return (bool) $calender->delete();
    }

    //------------------------------
    public function toggleStatus(int $id): bool
    {
        $calender = Calender::findOrFail($id);
        $newStatus = $calender->status === Calender::STATUS_ACTIVE ? Calender::STATUS_INACTIVE : Calender::STATUS_ACTIVE;
        $calender->status = $newStatus;

        return (bool) $calender->save();
    }
}
