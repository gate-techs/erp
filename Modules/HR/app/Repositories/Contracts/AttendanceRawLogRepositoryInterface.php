<?php

namespace Modules\HR\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\HR\Models\AttendanceRawLog;

interface AttendanceRawLogRepositoryInterface
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
     * Get punches for an employee on a given date.
     *
     * @param int $employeeId
     * @param string $date
     * @return Collection<int, AttendanceRawLog>
     */
    public function getEmployeeDailyLogs(int $employeeId, string $date): Collection;

    //======================
    /**
     * Create a raw punch log.
     *
     * @param array $data
     * @return AttendanceRawLog
     */
    public function create(array $data): AttendanceRawLog;

    //======================
    /**
     * Insert batch raw logs.
     *
     * @param array $records
     * @return bool
     */
    public function insertBatch(array $records): bool;
}
