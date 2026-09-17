<?php

namespace Modules\HR\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\HR\Enums\WorkScheduleStatus;
use Modules\HR\Enums\WorkScheduleType;
use Modules\HR\Models\Department;
use Modules\HR\Models\Shift;
use Modules\HR\Models\WorkSchedule;
use Modules\HR\Models\WorkScheduleDay;
use Modules\HR\Repositories\Contracts\WorkScheduleRepositoryInterface;
use Modules\Nursery\Models\NurseryBranch;

class WorkScheduleService
{
    //======================
    public function __construct(
        protected WorkScheduleRepositoryInterface $workScheduleRepository
    ) {}

    //======================
    /**
     * Get query builder instance for server-side DataTables.
     */
    public function getDataTableQuery(array $filters = []): Builder
    {
        return $this->workScheduleRepository->getDataTableQuery($filters);
    }

    //======================
    /**
     * Get lookup form data needed for create/edit offcanvas.
     *
     * @return array<string, mixed>
     */
    public function getFormData(): array
    {
        return [
            'types'       => WorkScheduleType::cases(),
            'statuses'    => WorkScheduleStatus::cases(),
            'shifts'      => Shift::with('translations')->where('status', 'active')->get(),
            'departments' => Department::with('translations')->get(),
            'branches'    => class_exists(NurseryBranch::class) ? NurseryBranch::with('translations')->get() : collect(),
            'daysList'    => [
                6 => __('Saturday'),
                0 => __('Sunday'),
                1 => __('Monday'),
                2 => __('Tuesday'),
                3 => __('Wednesday'),
                4 => __('Thursday'),
                5 => __('Friday'),
            ],
        ];
    }

    //======================
    /**
     * Find schedule by ID.
     */
    public function getScheduleById(int $id): ?WorkSchedule
    {
        return $this->workScheduleRepository->findById($id);
    }

    //======================
    /**
     * Create a new work schedule.
     */
    public function createSchedule(array $validated, ?int $userId = null): WorkSchedule
    {
        $daysData = $this->prepareDaysData($validated['days'] ?? []);
        $totals = $this->calculateTotals($daysData);

        $payload = array_merge(
            $this->formatTranslationData($validated),
            [
                'code'               => !empty($validated['code']) ? $validated['code'] : null,
                'type'               => $validated['type'] ?? WorkScheduleType::FIXED->value,
                'branch_id'          => !empty($validated['branch_id']) ? (int) $validated['branch_id'] : null,
                'department_id'      => !empty($validated['department_id']) ? (int) $validated['department_id'] : null,
                'working_days_count' => $totals['working_days_count'],
                'total_weekly_hours' => $totals['total_weekly_hours'],
                'is_default'         => !empty($validated['is_default']),
                'color'              => $validated['color'] ?? '#7367f0',
                'status'             => $validated['status'] ?? WorkScheduleStatus::ACTIVE->value,
                'created_by'         => $userId,
            ]
        );

        if (!empty($payload['is_default'])) {
            WorkSchedule::where('is_default', true)->update(['is_default' => false]);
        }

        return $this->workScheduleRepository->create($payload, $daysData);
    }

    //======================
    /**
     * Update an existing work schedule.
     */
    public function updateSchedule(WorkSchedule $schedule, array $validated): WorkSchedule
    {
        $daysData = isset($validated['days']) ? $this->prepareDaysData($validated['days']) : null;
        $totals = $daysData !== null ? $this->calculateTotals($daysData) : null;

        $payload = array_merge(
            $this->formatTranslationData($validated),
            [
                'code'          => !empty($validated['code']) ? $validated['code'] : null,
                'type'          => $validated['type'] ?? $schedule->type->value,
                'branch_id'     => !empty($validated['branch_id']) ? (int) $validated['branch_id'] : null,
                'department_id' => !empty($validated['department_id']) ? (int) $validated['department_id'] : null,
                'is_default'    => !empty($validated['is_default']),
                'color'         => $validated['color'] ?? $schedule->color,
                'status'        => $validated['status'] ?? $schedule->status->value,
            ]
        );

        if ($totals !== null) {
            $payload['working_days_count'] = $totals['working_days_count'];
            $payload['total_weekly_hours'] = $totals['total_weekly_hours'];
        }

        if (!empty($payload['is_default'])) {
            WorkSchedule::where('id', '!=', $schedule->id)->where('is_default', true)->update(['is_default' => false]);
        }

        return $this->workScheduleRepository->update($schedule, $payload, $daysData);
    }

    //======================
    /**
     * Delete a work schedule.
     */
    public function deleteSchedule(WorkSchedule $schedule): bool
    {
        return $this->workScheduleRepository->delete($schedule);
    }

    //======================
    /**
     * Prepare days array, ensuring all 7 days (0 to 6) exist.
     *
     * @param array $rawDays
     * @return array
     */
    public function prepareDaysData(array $rawDays): array
    {
        $shifts = Shift::all()->keyBy('id');
        $prepared = [];

        // All 7 days in order: 6=Sat, 0=Sun, 1=Mon, 2=Tue, 3=Wed, 4=Thu, 5=Fri
        $allDayNums = [6, 0, 1, 2, 3, 4, 5];

        foreach ($allDayNums as $dayNum) {
            $dayRow = $rawDays[$dayNum] ?? [];
            $isOff = !empty($dayRow['is_off_day']);
            $shiftId = !$isOff && !empty($dayRow['shift_id']) ? (int) $dayRow['shift_id'] : null;
            $secondShiftId = !$isOff && !empty($dayRow['second_shift_id']) ? (int) $dayRow['second_shift_id'] : null;

            $hours = 0.0;
            if (!$isOff && $shiftId && isset($shifts[$shiftId])) {
                $hours += (float) ($shifts[$shiftId]->total_hours ?? 0);
            }
            if (!$isOff && $secondShiftId && isset($shifts[$secondShiftId])) {
                $hours += (float) ($shifts[$secondShiftId]->total_hours ?? 0);
            }

            $prepared[] = [
                'day_of_week'     => $dayNum,
                'is_off_day'      => $isOff,
                'shift_id'        => $shiftId,
                'second_shift_id' => $secondShiftId,
                'work_hours'      => round($hours, 2),
            ];
        }

        return $prepared;
    }

    //======================
    /**
     * Calculate working days count and total weekly hours from prepared days.
     *
     * @param array $daysData
     * @return array{working_days_count: int, total_weekly_hours: float}
     */
    public function calculateTotals(array $daysData): array
    {
        $workingDays = 0;
        $totalHours = 0.0;

        foreach ($daysData as $d) {
            if (empty($d['is_off_day'])) {
                $workingDays++;
                $totalHours += (float) ($d['work_hours'] ?? 0);
            }
        }

        return [
            'working_days_count' => $workingDays,
            'total_weekly_hours' => round($totalHours, 2),
        ];
    }

    //======================
    /**
     * Format flat inputs into Astrotomic Translatable structure.
     *
     * @param array $data
     * @return array
     */
    public function formatTranslationData(array $data): array
    {
        $formatted = [];

        if (isset($data['name_ar']) || isset($data['description_ar'])) {
            $formatted['ar'] = [
                'name'        => $data['name_ar'] ?? '',
                'description' => $data['description_ar'] ?? null,
            ];
        }

        if (isset($data['name_en']) || isset($data['description_en'])) {
            $formatted['en'] = [
                'name'        => $data['name_en'] ?? '',
                'description' => $data['description_en'] ?? null,
            ];
        }

        return $formatted;
    }
}
