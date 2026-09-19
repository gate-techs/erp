<?php

namespace Modules\HR\Repositories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\HR\Models\Attendance;
use Modules\HR\Repositories\Contracts\AttendanceRepositoryInterface;

class AttendanceRepository implements AttendanceRepositoryInterface
{
    //======================
    /**
     * Get query builder instance for server-side DataTables.
     *
     * @param array $filters
     * @return Builder
     */
    public function getDataTableQuery(array $filters = []): Builder
    {
        $query = Attendance::query()
            ->with(['employee.translations', 'employee.primaryBranch', 'employee.department.translations', 'shift.translations', 'adjuster']);

        if (!empty($filters['date'])) {
            $query->whereDate('date', $filters['date']);
        }

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->whereBetween('date', [$filters['start_date'], $filters['end_date']]);
        } elseif (!empty($filters['month']) && !empty($filters['year'])) {
            $query->whereYear('date', $filters['year'])
                  ->whereMonth('date', $filters['month']);
        }

        if (!empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        if (!empty($filters['branch_id'])) {
            $query->whereHas('employee', function ($q) use ($filters) {
                $q->where('primary_branch_id', $filters['branch_id']);
            });
        }

        if (!empty($filters['department_id'])) {
            $query->whereHas('employee', function ($q) use ($filters) {
                $q->where('department_id', $filters['department_id']);
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->latest('date');
    }

    //======================
    /**
     * Get attendance records for a specific date range.
     *
     * @param string $startDate
     * @param string $endDate
     * @param array $filters
     * @param array $with
     * @return Collection<int, Attendance>
     */
    public function getBetweenDates(string $startDate, string $endDate, array $filters = [], array $with = ['employee.translations', 'shift.translations']): Collection
    {
        $query = Attendance::query()
            ->with($with)
            ->whereBetween('date', [$startDate, $endDate]);

        if (!empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        if (!empty($filters['branch_id'])) {
            $query->whereHas('employee', function ($q) use ($filters) {
                $q->where('primary_branch_id', $filters['branch_id']);
            });
        }

        if (!empty($filters['department_id'])) {
            $query->whereHas('employee', function ($q) use ($filters) {
                $q->where('department_id', $filters['department_id']);
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->get();
    }

    //======================
    /**
     * Find attendance for specific employee and date.
     *
     * @param int $employeeId
     * @param string $date
     * @return Attendance|null
     */
    public function findByEmployeeAndDate(int $employeeId, string $date): ?Attendance
    {
        return Attendance::with(['employee.translations', 'shift.translations'])
            ->where('employee_id', $employeeId)
            ->whereDate('date', $date)
            ->first();
    }

    //======================
    /**
     * Find an attendance record by ID.
     *
     * @param int $id
     * @param array $with
     * @return Attendance|null
     */
    public function findById(int $id, array $with = ['employee.translations', 'shift.translations', 'adjuster']): ?Attendance
    {
        return Attendance::with($with)->find($id);
    }

    //======================
    /**
     * Create or update attendance for an employee and date.
     *
     * @param array $attributes
     * @param array $values
     * @return Attendance
     */
    public function updateOrCreate(array $attributes, array $values): Attendance
    {
        return Attendance::updateOrCreate($attributes, $values);
    }

    //======================
    /**
     * Create a new attendance record.
     *
     * @param array $data
     * @return Attendance
     */
    public function create(array $data): Attendance
    {
        return Attendance::create($data);
    }

    //======================
    /**
     * Update an existing attendance record.
     *
     * @param int|Attendance $model
     * @param array $data
     * @return Attendance
     */
    public function update(int|Attendance $model, array $data): Attendance
    {
        $attendance = $model instanceof Attendance ? $model : $this->findById($model);
        if ($attendance) {
            $attendance->update($data);
        }
        return $attendance;
    }

    //======================
    /**
     * Delete an attendance record.
     *
     * @param int|Attendance $model
     * @return bool
     */
    public function delete(int|Attendance $model): bool
    {
        $attendance = $model instanceof Attendance ? $model : $this->findById($model);
        return $attendance ? (bool)$attendance->delete() : false;
    }
}
