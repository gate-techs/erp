<?php

namespace Modules\Academic\Services;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Academic\Models\Calender;
use Modules\Academic\Repositories\Contracts\CalenderRepositoryInterface;

class CalenderService
{
    //------------------------------
    public function __construct(
        protected CalenderRepositoryInterface $repository
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

        return $events->map(function (Calender $calender) use ($locale) {
            $arTitle = $calender->translate('ar')?->title ?? '';
            $enTitle = $calender->translate('en')?->title ?? '';

            // Display title like: "اسم الحدث _ English Title"
            if (! empty($arTitle) && ! empty($enTitle) && $arTitle !== $enTitle) {
                $displayTitle = "{$arTitle} _ {$enTitle}";
            } else {
                $displayTitle = $calender->translate($locale)?->title ?? $calender->title ?? '';
            }

            $startDate = $calender->start_date ? Carbon::parse($calender->start_date)->format('Y-m-d') : null;
            $endDate   = $calender->end_date ? Carbon::parse($calender->end_date)->format('Y-m-d') : null;

            // In FullCalendar allDay view, end date is exclusive, so add 1 day if end_date exists
            $end = null;
            if ($endDate) {
                $end = Carbon::parse($endDate)->addDay()->format('Y-m-d');
            }

            // Elegant coral / soft red badge styling
            $bgColor     = '#fee2e2';
            $borderColor = '#fca5a5';
            $textColor   = '#b91c1c';

            return [
                'id'              => $calender->id,
                'title'           => $displayTitle,
                'start'           => $startDate,
                'end'             => $end,
                'allDay'          => true,
                'backgroundColor' => $bgColor,
                'borderColor'     => $borderColor,
                'textColor'       => $textColor,
                'extendedProps'   => [
                    'id'               => $calender->id,
                    'title_ar'         => $arTitle,
                    'title_en'         => $enTitle,
                    'description_ar'   => $calender->translate('ar')?->description ?? '',
                    'description_en'   => $calender->translate('en')?->description ?? '',
                    'start_date'       => $startDate,
                    'end_date'         => $endDate,
                    'status'           => $calender->status,
                    'institution_id'   => $calender->institution_id,
                    'institution_name' => $calender->institution?->translate($locale)?->name ?? $calender->institution?->name,
                ],
            ];
        })->values()->toArray();
    }

    //------------------------------
    public function find(int $id): ?Calender
    {
        return $this->repository->find($id);
    }

    //------------------------------
    public function create(array $data): Calender
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
