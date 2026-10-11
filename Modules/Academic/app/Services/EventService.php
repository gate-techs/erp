<?php

namespace Modules\Academic\Services;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Academic\Models\Event;
use Modules\Academic\Repositories\Contracts\EventRepositoryInterface;

class EventService
{
    //------------------------------
    public function __construct(
        protected EventRepositoryInterface $repository
    ) {}

    //------------------------------
    public function getFilteredQuery(array $filters = []): Builder
    {
        return $this->repository->getFilteredQuery($filters);
    }

    //------------------------------
    public function all(array $filters = []): Collection
    {
        return $this->repository->all($filters);
    }

    //------------------------------
    public function getCalendarEvents(array $filters = []): array
    {
        $events = $this->repository->getCalendarEvents($filters);
        $locale = app()->getLocale();

        return $events->map(function (Event $event) use ($locale) {
            $arTitle = $event->translate('ar')?->title ?? '';
            $enTitle = $event->translate('en')?->title ?? '';

            // Display title like: "اسم الحدث _ English Title" as shown in user screenshot
            if (! empty($arTitle) && ! empty($enTitle) && $arTitle !== $enTitle) {
                $displayTitle = "{$arTitle} _ {$enTitle}";
            } else {
                $displayTitle = $event->translate($locale)?->title ?? $event->title ?? '';
            }

            $startDate = $event->start_date ? Carbon::parse($event->start_date)->format('Y-m-d') : null;
            $endDate = $event->end_date ? Carbon::parse($event->end_date)->format('Y-m-d') : null;

            // In FullCalendar allDay view, end date is exclusive, so add 1 day if end_date exists
            $end = null;
            if ($endDate) {
                $end = Carbon::parse($endDate)->addDay()->format('Y-m-d');
            }

            // Elegant coral / soft red badge styling matching the reference screenshot
            $bgColor = '#fee2e2';
            $borderColor = '#fca5a5';
            $textColor = '#b91c1c';

            return [
                'id'              => $event->id,
                'title'           => $displayTitle,
                'start'           => $startDate,
                'end'             => $end,
                'allDay'          => true,
                'backgroundColor' => $bgColor,
                'borderColor'     => $borderColor,
                'textColor'       => $textColor,
                'extendedProps'   => [
                    'id'               => $event->id,
                    'title_ar'         => $arTitle,
                    'title_en'         => $enTitle,
                    'description_ar'   => $event->translate('ar')?->description ?? '',
                    'description_en'   => $event->translate('en')?->description ?? '',
                    'start_date'       => $startDate,
                    'end_date'         => $endDate,
                    'status'           => $event->status,
                    'institution_id'   => $event->institution_id,
                    'institution_name' => $event->institution?->translate($locale)?->name ?? $event->institution?->name,
                ],
            ];
        })->values()->toArray();
    }

    //------------------------------
    public function find(int $id): ?Event
    {
        return $this->repository->find($id);
    }

    //------------------------------
    public function create(array $data): Event
    {
        return $this->repository->create($data);
    }

    //------------------------------
    public function update(int $id, array $data): bool
    {
        return $this->repository->update($id, $data);
    }

    //------------------------------
    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    //------------------------------
    public function toggleStatus(int $id): bool
    {
        return $this->repository->toggleStatus($id);
    }
}
