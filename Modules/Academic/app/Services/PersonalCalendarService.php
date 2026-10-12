<?php

namespace Modules\Academic\Services;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Academic\Models\PersonalCalendar;
use Modules\Academic\Repositories\Contracts\PersonalCalendarRepositoryInterface;

class PersonalCalendarService
{
    //------------------------------
    public function __construct(
        protected PersonalCalendarRepositoryInterface $repository
    ) {}

    //------------------------------
    public function getFilteredQuery(int $userId, array $filters = []): Builder
    {
        return $this->repository->getFilteredQuery($userId, $filters);
    }

    //------------------------------
    public function all(int $userId, array $filters = []): Collection
    {
        return $this->repository->all($userId, $filters);
    }

    //------------------------------
    public function getCalendarEvents(int $userId, array $filters = []): array
    {
        $events = $this->repository->getCalendarEvents($userId, $filters);
        $statuses = PersonalCalendar::statuses();
        $statusColors = PersonalCalendar::statusColors();
        $locale = app()->getLocale();

        return $events->map(function (PersonalCalendar $item) use ($statuses, $statusColors, $locale) {
            $dateStr = $item->event_date ? Carbon::parse($item->event_date)->format('Y-m-d') : null;

            $start = $dateStr;
            $end = null;
            $allDay = (bool) $item->is_all_day;

            if (! $allDay && $item->start_time) {
                $start = "{$dateStr}T{$item->start_time}";

                if ($item->end_time) {
                    $end = "{$dateStr}T{$item->end_time}";
                }
            } else {
                $allDay = true;
            }

            $color = $item->color ?: ($statusColors[$item->status] ?? '#0b3252');

            return [
                'id'              => $item->id,
                'title'           => $item->title,
                'start'           => $start,
                'end'             => $end,
                'allDay'          => $allDay,
                'backgroundColor' => $color,
                'borderColor'     => $color,
                'textColor'       => '#ffffff',
                'extendedProps'   => [
                    'id'               => $item->id,
                    'title'            => $item->title,
                    'description'      => $item->description ?? '',
                    'event_date'       => $dateStr,
                    'start_time'       => $item->start_time ? substr($item->start_time, 0, 5) : '',
                    'end_time'         => $item->end_time ? substr($item->end_time, 0, 5) : '',
                    'is_all_day'       => $item->is_all_day,
                    'status'           => $item->status,
                    'status_label'     => $statuses[$item->status] ?? $item->status,
                    'color'            => $color,
                    'institution_id'   => $item->institution_id,
                    'institution_name' => $item->institution?->translate($locale)?->name ?? $item->institution?->name,
                ],
            ];
        })->values()->toArray();
    }

    //------------------------------
    public function find(int $id, ?int $userId = null): ?PersonalCalendar
    {
        return $this->repository->find($id, $userId);
    }

    //------------------------------
    public function create(array $data): PersonalCalendar
    {
        return $this->repository->create($data);
    }

    //------------------------------
    public function update(int $id, array $data, ?int $userId = null): bool
    {
        return $this->repository->update($id, $data, $userId);
    }

    //------------------------------
    public function delete(int $id, ?int $userId = null): bool
    {
        return $this->repository->delete($id, $userId);
    }

    //------------------------------
    public function updateStatus(int $id, string $status, ?int $userId = null): bool
    {
        return $this->repository->updateStatus($id, $status, $userId);
    }
}
