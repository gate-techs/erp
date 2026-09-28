<?php

namespace Modules\EmployeePortal\Repositories;

use Modules\EmployeePortal\Repositories\Contracts\PortalAttendanceRepositoryInterface;
use Modules\HR\Models\Attendance;

class PortalAttendanceRepository implements PortalAttendanceRepositoryInterface
{
    /**
     * Find attendance record for an employee on a given date.
     *
     * @param int $employeeId
     * @param string $date
     * @return Attendance|null
     */
    public function findTodayAttendance(int $employeeId, string $date): ?Attendance
    {
        return Attendance::where('employee_id', $employeeId)
            ->where('date', $date)
            ->first();
    }

    /**
     * Find or create attendance instance for an employee on a date.
     *
     * @param int $employeeId
     * @param string $date
     * @return Attendance
     */
    public function findOrNew(int $employeeId, string $date): Attendance
    {
        return Attendance::firstOrNew([
            'employee_id' => $employeeId,
            'date'        => $date,
        ]);
    }

    /**
     * Save attendance model.
     *
     * @param Attendance $attendance
     * @return bool
     */
    public function save(Attendance $attendance): bool
    {
        return $attendance->save();
    }
}
