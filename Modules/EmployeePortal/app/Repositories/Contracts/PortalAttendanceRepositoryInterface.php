<?php

namespace Modules\EmployeePortal\Repositories\Contracts;

use Modules\HR\Models\Attendance;

interface PortalAttendanceRepositoryInterface
{
    /**
     * Find attendance record for an employee on a given date.
     *
     * @param int $employeeId
     * @param string $date
     * @return Attendance|null
     */
    public function findTodayAttendance(int $employeeId, string $date): ?Attendance;

    /**
     * Find or create attendance instance for an employee on a date.
     *
     * @param int $employeeId
     * @param string $date
     * @return Attendance
     */
    public function findOrNew(int $employeeId, string $date): Attendance;

    /**
     * Save attendance model.
     *
     * @param Attendance $attendance
     * @return bool
     */
    public function save(Attendance $attendance): bool;
}
