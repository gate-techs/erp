<?php

namespace Modules\HR\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\HR\Models\AttendanceRawLog;
use Modules\HR\Repositories\Contracts\AttendanceRawLogRepositoryInterface;

class AttendanceRawLogRepository implements AttendanceRawLogRepositoryInterface
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
        $query = AttendanceRawLog::query()
            ->with(['employee.translations', 'device', 'creator']);

        if (!empty($filters['date'])) {
            $query->whereDate('punch_time', $filters['date']);
        }

        if (!empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        if (!empty($filters['source'])) {
            $query->where('source', $filters['source']);
        }

        return $query->latest('punch_time');
    }

    //======================
    /**
     * Get punches for an employee on a given date.
     *
     * @param int $employeeId
     * @param string $date
     * @return Collection<int, AttendanceRawLog>
     */
    public function getEmployeeDailyLogs(int $employeeId, string $date): Collection
    {
        return AttendanceRawLog::where('employee_id', $employeeId)
            ->whereDate('punch_time', $date)
            ->orderBy('punch_time', 'asc')
            ->get();
    }

    //======================
    /**
     * Create a raw punch log.
     *
     * @param array $data
     * @return AttendanceRawLog
     */
    public function create(array $data): AttendanceRawLog
    {
        return AttendanceRawLog::create($data);
    }

    //======================
    /**
     * Insert batch raw logs.
     *
     * @param array $records
     * @return bool
     */
    public function insertBatch(array $records): bool
    {
        return AttendanceRawLog::insert($records);
    }
}
