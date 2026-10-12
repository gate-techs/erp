<?php

namespace Modules\Academic\Services;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Academic\Models\MeetingCalendar;
use Modules\Academic\Repositories\Contracts\MeetingCalendarRepositoryInterface;

class MeetingCalendarService
{
    //------------------------------
    public function __construct(
        protected MeetingCalendarRepositoryInterface $repository
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
        $statuses = MeetingCalendar::statuses();
        $statusColors = MeetingCalendar::statusColors();
        $locale = app()->getLocale();

        return $events->map(function (MeetingCalendar $item) use ($statuses, $statusColors, $locale) {
            $dateStr = $item->event_date ? Carbon::parse($item->event_date)->format('Y-m-d') : null;

            $startTimeFormatted = $item->start_time ? substr($item->start_time, 0, 5) : '';
            $endTimeFormatted   = $item->end_time ? substr($item->end_time, 0, 5) : '';

            $start = "{$dateStr}T{$item->start_time}";
            $end   = $item->end_time ? "{$dateStr}T{$item->end_time}" : null;

            $color = $item->color ?: ($statusColors[$item->status] ?? '#0b3252');

            return [
                'id'              => $item->id,
                'title'           => $item->title,
                'start'           => $start,
                'end'             => $end,
                'allDay'          => false,
                'backgroundColor' => $color,
                'borderColor'     => $color,
                'textColor'       => '#ffffff',
                'extendedProps'   => [
                    'id'               => $item->id,
                    'title'            => $item->title,
                    'description'      => $item->description ?? '',
                    'event_date'       => $dateStr,
                    'start_time'       => $startTimeFormatted,
                    'end_time'         => $endTimeFormatted,
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
    public function getSidebarData(int $userId): array
    {
        $statuses = MeetingCalendar::statuses();
        $locale = app()->getLocale();

        $formatTime12h = function (?string $time) use ($locale) {
            if (! $time) return '';
            try {
                return Carbon::parse($time)->locale($locale)->isoFormat('hh:mm a');
            } catch (\Throwable) {
                return substr($time, 0, 5);
            }
        };

        $formatMeeting = function (MeetingCalendar $item) use ($statuses, $locale, $formatTime12h) {
            $carbonDate = $item->event_date ? Carbon::parse($item->event_date)->locale($locale) : null;
            $dateStr = $carbonDate ? $carbonDate->format('Y-m-d') : '';
            $dayNum = $carbonDate ? $carbonDate->format('d') : '';
            $dateFormatted = $carbonDate ? $carbonDate->isoFormat('dddd، D MMMM') : '';

            $startTimeFormatted = $formatTime12h($item->start_time);
            $endTimeFormatted   = $formatTime12h($item->end_time);
            $timeRange = $endTimeFormatted ? "{$startTimeFormatted} - {$endTimeFormatted}" : $startTimeFormatted;

            return [
                'id'               => $item->id,
                'title'            => $item->title,
                'description'      => $item->description ?? '',
                'event_date'       => $dateStr,
                'day_num'          => $dayNum,
                'date_formatted'   => $dateFormatted,
                'start_time'       => $startTimeFormatted,
                'end_time'         => $endTimeFormatted,
                'time_range'       => $timeRange,
                'status'           => $item->status,
                'status_label'     => $statuses[$item->status] ?? $item->status,
                'color'            => $item->color ?? '#0b3252',
            ];
        };

        $groupByDate = function ($collection) {
            return $collection->groupBy('event_date')->map(function ($items, $dateKey) {
                $first = $items->first();
                $count = $items->count();
                return [
                    'date'            => $dateKey,
                    'day_num'         => $first['day_num'] ?? '',
                    'date_formatted'  => $first['date_formatted'] ?? '',
                    'count'           => $count,
                    'count_label'     => $count . ' ' . (app()->getLocale() === 'ar' ? 'اجتماع' : 'Meeting(s)'),
                    'meetings'        => $items->values()->toArray(),
                ];
            })->values()->toArray();
        };

        $todayItems = $this->repository->getTodayMeetings($userId)->map($formatMeeting);
        $upcomingItems = $this->repository->getUpcomingMeetings($userId, 30)->map($formatMeeting);

        return [
            'today_count'     => $todayItems->count(),
            'upcoming_count'  => $upcomingItems->count(),
            'today'           => $todayItems->values()->toArray(),
            'today_groups'    => $groupByDate($todayItems),
            'upcoming'        => $upcomingItems->values()->toArray(),
            'upcoming_groups' => $groupByDate($upcomingItems),
        ];
    }

    //------------------------------
    public function find(int $id, ?int $userId = null): ?MeetingCalendar
    {
        return $this->repository->find($id, $userId);
    }

    //------------------------------
    public function create(array $data): MeetingCalendar
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
