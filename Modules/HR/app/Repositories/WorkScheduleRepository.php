<?php

namespace Modules\HR\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Modules\HR\Models\WorkSchedule;
use Modules\HR\Models\WorkScheduleDay;
use Modules\HR\Repositories\Contracts\WorkScheduleRepositoryInterface;

class WorkScheduleRepository implements WorkScheduleRepositoryInterface
{
    //======================
    /**
     * Get all work schedules with optional filters.
     */
    public function getAll(array $filters = [], array $with = ['translations', 'days.shift', 'department', 'branch']): Collection
    {
        $query = WorkSchedule::query()->with($with);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        if (!empty($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }

        return $query->latest('id')->get();
    }

    //======================
    /**
     * Get query builder instance for server-side DataTables.
     */
    public function getDataTableQuery(array $filters = []): Builder
    {
        $query = WorkSchedule::query()
            ->with(['translations', 'days.shift.translations', 'department.translations', 'branch.translations']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        return $query;
    }

    //======================
    /**
     * Find a work schedule by ID.
     */
    public function findById(int $id, array $with = ['translations', 'days.shift', 'department', 'branch']): ?WorkSchedule
    {
        return WorkSchedule::query()->with($with)->find($id);
    }

    //======================
    /**
     * Create a new work schedule.
     */
    public function create(array $data, array $daysData = []): WorkSchedule
    {
        return DB::transaction(function () use ($data, $daysData) {
            $schedule = WorkSchedule::create($data);

            if (!empty($daysData)) {
                $this->syncDays($schedule, $daysData);
            }

            return $schedule->load(['translations', 'days.shift', 'department', 'branch']);
        });
    }

    //======================
    /**
     * Update an existing work schedule.
     */
    public function update(int|WorkSchedule $model, array $data, ?array $daysData = null): WorkSchedule
    {
        return DB::transaction(function () use ($model, $data, $daysData) {
            $schedule = $model instanceof WorkSchedule ? $model : $this->findById($model);

            if (!$schedule) {
                throw new \InvalidArgumentException('Work schedule not found for update.');
            }

            $schedule->update($data);

            if ($daysData !== null) {
                $this->syncDays($schedule, $daysData);
            }

            return $schedule->load(['translations', 'days.shift', 'department', 'branch']);
        });
    }

    //======================
    /**
     * Delete a work schedule.
     */
    public function delete(int|WorkSchedule $model): bool
    {
        return DB::transaction(function () use ($model) {
            $schedule = $model instanceof WorkSchedule ? $model : $this->findById($model);

            if (!$schedule) {
                return false;
            }

            return (bool) $schedule->delete();
        });
    }

    //======================
    /**
     * Synchronize work schedule days (0=Sunday to 6=Saturday).
     */
    protected function syncDays(WorkSchedule $schedule, array $daysData): void
    {
        foreach ($daysData as $dayRow) {
            if (!isset($dayRow['day_of_week'])) {
                continue;
            }

            $isOff = !empty($dayRow['is_off_day']);
            $shiftId = !$isOff && !empty($dayRow['shift_id']) ? (int) $dayRow['shift_id'] : null;
            $secondShiftId = !$isOff && !empty($dayRow['second_shift_id']) ? (int) $dayRow['second_shift_id'] : null;
            $workHours = !$isOff ? (float) ($dayRow['work_hours'] ?? 0) : 0;

            WorkScheduleDay::updateOrCreate(
                [
                    'work_schedule_id' => $schedule->id,
                    'day_of_week'      => (int) $dayRow['day_of_week'],
                ],
                [
                    'is_off_day'       => $isOff,
                    'shift_id'         => $shiftId,
                    'second_shift_id'  => $secondShiftId,
                    'work_hours'       => $workHours,
                ]
            );
        }
    }
}
