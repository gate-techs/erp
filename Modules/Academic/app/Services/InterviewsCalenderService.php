<?php

namespace Modules\Academic\Services;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Academic\Models\AcademicGuardian;
use Modules\Academic\Models\GuardianInterview;
use Modules\Academic\Repositories\Contracts\InterviewsCalenderRepositoryInterface;

class InterviewsCalenderService
{
    //------------------------------
    public function __construct(
        protected InterviewsCalenderRepositoryInterface $repository
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
        $statuses = GuardianInterview::statuses();
        $statusColors = GuardianInterview::statusColors();
        $locale = app()->getLocale();

        return $events->map(function (GuardianInterview $item) use ($statuses, $statusColors, $locale) {
            $dateStr = $item->interview_date ? Carbon::parse($item->interview_date)->format('Y-m-d') : null;

            $startTimeFormatted = $item->start_time ? substr($item->start_time, 0, 5) : '';
            $endTimeFormatted   = $item->end_time ? substr($item->end_time, 0, 5) : '';

            $start = "{$dateStr}T{$item->start_time}";
            $end   = $item->end_time ? "{$dateStr}T{$item->end_time}" : null;

            $color = $item->color ?: ($statusColors[$item->status] ?? '#0b3252');

            $guardianDisplayName = $item->guardian_name ?: ($item->guardian?->translate($locale)?->name ?? $item->guardian?->name ?? __('Unknown Guardian'));
            $title = $guardianDisplayName . ($item->guardian_phone ? " ({$item->guardian_phone})" : '');

            return [
                'id'              => $item->id,
                'title'           => $title,
                'start'           => $start,
                'end'             => $end,
                'allDay'          => false,
                'backgroundColor' => $color,
                'borderColor'     => $color,
                'textColor'       => '#ffffff',
                'extendedProps'   => [
                    'id'                   => $item->id,
                    'guardian_name'        => $guardianDisplayName,
                    'guardian_civil_id'    => $item->guardian_civil_id ?? '',
                    'guardian_phone'       => $item->guardian_phone ?? '',
                    'guardian_email'       => $item->guardian_email ?? '',
                    'academic_guardian_id' => $item->academic_guardian_id,
                    'institution_id'       => $item->institution_id,
                    'institution_name'     => $item->institution?->translate($locale)?->name ?? $item->institution?->name ?? '',
                    'branch_id'            => $item->branch_id,
                    'branch_name'          => $item->branch?->translate($locale)?->name ?? $item->branch?->name ?? '',
                    'interview_date'       => $dateStr,
                    'start_time'           => $startTimeFormatted,
                    'end_time'             => $endTimeFormatted,
                    'notes'                => $item->notes ?? '',
                    'status'               => $item->status,
                    'status_label'         => $statuses[$item->status] ?? $item->status,
                    'color'                => $color,
                ],
            ];
        })->values()->toArray();
    }

    //------------------------------
    public function getSidebarData(int $userId): array
    {
        $statuses = GuardianInterview::statuses();
        $locale = app()->getLocale();

        // Current Month Header (e.g. "أكتوبر 2026" or "October 2026")
        $currentMonthLabel = Carbon::now()->locale($locale)->isoFormat('MMMM YYYY');

        $formatTime12h = function (?string $time) use ($locale) {
            if (! $time) return '';
            try {
                return Carbon::parse($time)->locale($locale)->isoFormat('hh:mm a');
            } catch (\Throwable) {
                return substr($time, 0, 5);
            }
        };

        $formatInterview = function (GuardianInterview $item) use ($statuses, $locale, $formatTime12h) {
            $carbonDate = $item->interview_date ? Carbon::parse($item->interview_date)->locale($locale) : null;
            $dateStr = $carbonDate ? $carbonDate->format('Y-m-d') : '';
            $dayNum = $carbonDate ? $carbonDate->format('d') : '';
            $dateFormatted = $carbonDate ? $carbonDate->isoFormat('dddd، D MMMM') : '';

            $startTimeFormatted = $formatTime12h($item->start_time);
            $endTimeFormatted   = $formatTime12h($item->end_time);
            $timeRange = $endTimeFormatted ? "{$startTimeFormatted} - {$endTimeFormatted}" : $startTimeFormatted;

            $guardianDisplayName = $item->guardian_name ?: ($item->guardian?->translate($locale)?->name ?? $item->guardian?->name ?? __('Unknown Guardian'));

            return [
                'id'                   => $item->id,
                'guardian_name'        => $guardianDisplayName,
                'guardian_civil_id'    => $item->guardian_civil_id ?? '',
                'guardian_phone'       => $item->guardian_phone ?? '',
                'guardian_email'       => $item->guardian_email ?? '',
                'academic_guardian_id' => $item->academic_guardian_id,
                'institution_id'       => $item->institution_id,
                'institution_name'     => $item->institution?->translate($locale)?->name ?? $item->institution?->name ?? '',
                'branch_id'            => $item->branch_id,
                'branch_name'          => $item->branch?->translate($locale)?->name ?? $item->branch?->name ?? '',
                'interview_date'       => $dateStr,
                'day_num'              => $dayNum,
                'date_formatted'       => $dateFormatted,
                'start_time'           => $startTimeFormatted,
                'end_time'             => $endTimeFormatted,
                'time_range'           => $timeRange,
                'notes'                => $item->notes ?? '',
                'status'               => $item->status,
                'status_label'         => $statuses[$item->status] ?? $item->status,
                'color'                => $item->color ?? '#0b3252',
            ];
        };

        $groupByDate = function ($collection) {
            return $collection->groupBy('interview_date')->map(function ($items, $dateKey) {
                $first = $items->first();
                $count = $items->count();
                return [
                    'date'            => $dateKey,
                    'day_num'         => $first['day_num'] ?? '',
                    'date_formatted'  => $first['date_formatted'] ?? '',
                    'count'           => $count,
                    'count_label'     => $count . ' ' . (app()->getLocale() === 'ar' ? 'مواعيد' : 'Interview(s)'),
                    'interviews'      => $items->values()->toArray(),
                ];
            })->values()->toArray();
        };

        $thisMonthItems = $this->repository->getThisMonthInterviews($userId)->map($formatInterview);
        $upcomingItems  = $this->repository->getUpcomingInterviews($userId, 30)->map($formatInterview);

        return [
            'month_label'     => $currentMonthLabel,
            'this_month_count'=> $thisMonthItems->count(),
            'upcoming_count'  => $upcomingItems->count(),
            'this_month'      => $thisMonthItems->values()->toArray(),
            'this_month_groups'=> $groupByDate($thisMonthItems),
            'upcoming'        => $upcomingItems->values()->toArray(),
            'upcoming_groups' => $groupByDate($upcomingItems),
        ];
    }

    //------------------------------
    public function checkGuardianByCivilId(string $civilId): array
    {
        $civilId = trim($civilId);
        if (empty($civilId)) {
            return ['found' => false];
        }

        $guardian = $this->repository->findByCivilId($civilId);

        if (! $guardian) {
            return ['found' => false];
        }

        $locale = app()->getLocale();
        $nameAr = $guardian->translate('ar')?->name ?? $guardian->name ?? '';
        $nameEn = $guardian->translate('en')?->name ?? $guardian->name ?? '';
        $displayName = $guardian->translate($locale)?->name ?? $nameAr ?: $nameEn;

        return [
            'found'    => true,
            'guardian' => [
                'id'             => $guardian->id,
                'guardian_code'  => $guardian->guardian_code,
                'civil_id'       => $guardian->civil_id,
                'name'           => $displayName,
                'name_ar'        => $nameAr,
                'name_en'        => $nameEn,
                'phone'          => $guardian->phone,
                'phone_code'     => $guardian->phone_code,
                'email'          => $guardian->email,
                'institution_id' => $guardian->institution_id,
                'branch_id'      => $guardian->branch_id,
            ],
        ];
    }

    //------------------------------
    public function find(int $id, ?int $userId = null): ?GuardianInterview
    {
        return $this->repository->find($id, $userId);
    }

    //------------------------------
    public function create(array $data): GuardianInterview
    {
        return DB::transaction(function () use ($data) {
            $guardianId = $data['academic_guardian_id'] ?? null;
            $civilId = ! empty($data['guardian_civil_id']) ? trim($data['guardian_civil_id']) : null;

            // If guardian not linked, check by civil ID or create new AcademicGuardian
            if (! $guardianId && $civilId) {
                $existingGuardian = $this->repository->findByCivilId($civilId);
                if ($existingGuardian) {
                    $guardianId = $existingGuardian->id;
                } else {
                    // Create new AcademicGuardian automatically
                    $guardianId = $this->createGuardianRecord($data);
                }
            } elseif (! $guardianId && ! empty($data['guardian_name'])) {
                $guardianId = $this->createGuardianRecord($data);
            }

            $data['academic_guardian_id'] = $guardianId;

            return $this->repository->create($data);
        });
    }

    //------------------------------
    public function update(int $id, array $data, ?int $userId = null): bool
    {
        return DB::transaction(function () use ($id, $data, $userId) {
            $civilId = ! empty($data['guardian_civil_id']) ? trim($data['guardian_civil_id']) : null;
            $guardianId = $data['academic_guardian_id'] ?? null;

            if (! $guardianId && $civilId) {
                $existingGuardian = $this->repository->findByCivilId($civilId);
                if ($existingGuardian) {
                    $guardianId = $existingGuardian->id;
                } else {
                    $guardianId = $this->createGuardianRecord($data);
                }
                $data['academic_guardian_id'] = $guardianId;
            }

            return $this->repository->update($id, $data, $userId);
        });
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

    //------------------------------
    protected function createGuardianRecord(array $data): int
    {
        $lastId = DB::table('academic_guardians')->max('id') ?? 0;
        $guardianCode = sprintf('GRD-%04d', $lastId + 1);

        $guardianData = [
            'guardian_code'  => $guardianCode,
            'civil_id'       => ! empty($data['guardian_civil_id']) ? trim($data['guardian_civil_id']) : null,
            'institution_id' => $data['institution_id'] ?? null,
            'branch_id'      => $data['branch_id'] ?? null,
            'phone'          => $data['guardian_phone'] ?? '',
            'email'          => $data['guardian_email'] ?? null,
            'status'         => AcademicGuardian::STATUS_ACTIVE,
            'created_by'     => auth()->id(),
            'ar' => [
                'name' => $data['guardian_name'] ?? '',
            ],
            'en' => [
                'name' => $data['guardian_name'] ?? '',
            ],
        ];

        $guardian = AcademicGuardian::create($guardianData);

        return $guardian->id;
    }
}
