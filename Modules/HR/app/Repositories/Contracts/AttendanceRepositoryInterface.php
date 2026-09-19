<?php

namespace Modules\HR\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\HR\Models\Attendance;

interface AttendanceRepositoryInterface
{
    //======================
    /**
     * Get query builder instance for server-side DataTables.
     *
     * @param array $filters
     * @return Builder
     */
    public function getDataTableQuery(array $filters = []): Builder;

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
    public function getBetweenDates(string $startDate, string $endDate, array $filters = [], array $with = ['employee.translations', 'shift.translations']): Collection;

    //======================
    /**
     * Find attendance for specific employee and date.
     *
     * @param int $employeeId
     * @param string $date
     * @return Attendance|null
     */
    public function findByEmployeeAndDate(int $employeeId, string $date): ?Attendance;

    //======================
    /**
     * Find an attendance record by ID.
     *
     * @param int $id
     * @param array $with
     * @return Attendance|null
     */
    public function findById(int $id, array $with = ['employee.translations', 'shift.translations', 'adjuster']): ?Attendance;

    //======================
    /**
     * Create or update attendance for an employee and date.
     *
     * @param array $attributes
     * @param array $values
     * @return Attendance
     */
    public function updateOrCreate(array $attributes, array $values): Attendance;

    //======================
    /**
     * Create a new attendance record.
     *
     * @param array $data
     * @return Attendance
     */
    public function create(array $data): Attendance;

    //======================
    /**
     * Update an existing attendance record.
     *
     * @param int|Attendance $model
     * @param array $data
     * @return Attendance
     */
    public function update(int|Attendance $model, array $data): Attendance;

    //======================
    /**
     * Delete an attendance record.
     *
     * @param int|Attendance $model
     * @return bool
     */
    public function delete(int|Attendance $model): bool;
}
