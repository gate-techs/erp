<?php

namespace Modules\HR\Repositories;

use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\HR\Enums\AttendanceStatus;
use Modules\HR\Models\Attendance;
use Modules\HR\Repositories\Contracts\AttendanceReportRepositoryInterface;

class AttendanceReportRepository implements AttendanceReportRepositoryInterface
{
    /**
     * Get Eloquent Builder for filtered attendance records.
     *
     * @param array<string, mixed> $filters
     * @return Builder
     */
    public function getFilteredQuery(array $filters = []): Builder
    {
        $query = Attendance::with([
            'employee.translations',
            'employee.department.translations',
            'employee.primaryBranch.translations',
            'shift.translations',
        ]);

        $reportType = $filters['report_type'] ?? 'daily';

        // 1. Date Range
        $startDate = $filters['start_date'] ?? null;
        $endDate   = $filters['end_date'] ?? null;

        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        } elseif ($startDate) {
            $query->whereDate('date', '>=', $startDate);
        } elseif ($endDate) {
            $query->whereDate('date', '<=', $endDate);
        } else {
            if ($reportType === 'daily' && empty($filters['start_date']) && empty($filters['end_date'])) {
                // Default for daily: Today
                $query->whereDate('date', Carbon::today()->toDateString());
            } else {
                // Default: Current Month
                $query->whereBetween('date', [
                    Carbon::now()->startOfMonth()->toDateString(),
                    Carbon::now()->toDateString()
                ]);
            }
        }

        // 2. Department Filter
        if (!empty($filters['department_id'])) {
            $query->whereHas('employee', function ($q) use ($filters) {
                $q->where('department_id', $filters['department_id']);
            });
        }

        // 3. Branch Filter
        if (!empty($filters['branch_id'])) {
            $query->whereHas('employee', function ($q) use ($filters) {
                $q->where('primary_branch_id', $filters['branch_id']);
            });
        }

        // 4. Shift Filter
        if (!empty($filters['shift_id'])) {
            $query->where('shift_id', $filters['shift_id']);
        }

        // 5. Employee Filter
        if (!empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        // 6. Status Filter
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // 7. Sub-Report specific conditions
        if ($reportType === 'lateness') {
            $query->where(function ($q) {
                $q->where('late_minutes', '>', 0)
                  ->orWhere('early_leave_minutes', '>', 0);
            });
        } elseif ($reportType === 'absenteeism') {
            $query->where('status', AttendanceStatus::ABSENT->value);
        }

        // 8. Special Flags
        if (!empty($filters['only_late'])) {
            $query->where('late_minutes', '>', 0);
        }

        if (!empty($filters['only_overtime'])) {
            $query->where('overtime_minutes', '>', 0);
        }

        if (!empty($filters['only_early_leave'])) {
            $query->where('early_leave_minutes', '>', 0);
        }

        return $query->orderBy('date', 'desc')->orderBy('employee_id', 'asc');
    }

    /**
     * Get all filtered attendance records without pagination.
     *
     * @param array<string, mixed> $filters
     * @return Collection
     */
    public function getFilteredRecords(array $filters = []): Collection
    {
        return $this->getFilteredQuery($filters)->get();
    }

    /**
     * Get paginated attendance records.
     *
     * @param array<string, mixed> $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginatedRecords(array $filters = [], int $perPage = 25): LengthAwarePaginator
    {
        return $this->getFilteredQuery($filters)->paginate($perPage);
    }

    /**
     * Compute Summary KPIs (total records, present, late, absent, minutes, etc.).
     *
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function getSummaryKPIs(array $filters = []): array
    {
        $records = $this->getFilteredRecords($filters);

        $totalRecords = $records->count();
        $presentCount = $records->whereIn('status.value', [
            AttendanceStatus::PRESENT->value,
            AttendanceStatus::LATE->value,
            AttendanceStatus::HALF_DAY->value,
        ])->count();
        $lateCount    = $records->where('late_minutes', '>', 0)->count();
        $absentCount  = $records->where('status.value', AttendanceStatus::ABSENT->value)->count();
        $leaveCount   = $records->where('status.value', AttendanceStatus::ON_LEAVE->value)->count();

        $totalLateMinutes       = (int) $records->sum('late_minutes');
        $totalEarlyLeaveMinutes = (int) $records->sum('early_leave_minutes');
        $totalOvertimeMinutes   = (int) $records->sum('overtime_minutes');
        $totalWorkingHours      = (float) $records->sum('total_working_hours');

        return [
            'total_records'              => $totalRecords,
            'present_count'              => $presentCount,
            'late_count'                 => $lateCount,
            'absent_count'               => $absentCount,
            'leave_count'                => $leaveCount,
            'total_late_minutes'         => $totalLateMinutes,
            'total_early_leave_minutes'  => $totalEarlyLeaveMinutes,
            'total_overtime_minutes'     => $totalOvertimeMinutes,
            'total_working_hours'        => round($totalWorkingHours, 2),
            'attendance_rate'            => $totalRecords > 0 ? round(($presentCount / $totalRecords) * 100, 1) : 0,
        ];
    }

    /**
     * Get aggregated monthly summary (Timesheet) records grouped by employee.
     *
     * @param array<string, mixed> $filters
     * @return \Illuminate\Support\Collection
     */
    public function getMonthlySummaryRecords(array $filters = []): \Illuminate\Support\Collection
    {
        $records = $this->getFilteredRecords($filters);

        return $records->groupBy('employee_id')->map(function ($empRecords) {
            /** @var \Illuminate\Database\Eloquent\Collection $empRecords */
            $firstRecord = $empRecords->first();
            $employee = $firstRecord ? $firstRecord->employee : null;

            $scheduledDays = $empRecords->count();
            $presentDays = $empRecords->filter(fn($r) => in_array($r->status?->value, [
                AttendanceStatus::PRESENT->value,
                AttendanceStatus::LATE->value,
                AttendanceStatus::HALF_DAY->value,
            ]))->count();

            $absentDays = $empRecords->filter(fn($r) => $r->status?->value === AttendanceStatus::ABSENT->value)->count();
            $leaveDays = $empRecords->filter(fn($r) => $r->status?->value === AttendanceStatus::ON_LEAVE->value)->count();
            $restDays = $empRecords->filter(fn($r) => in_array($r->status?->value, [
                AttendanceStatus::REST_DAY->value,
                AttendanceStatus::HOLIDAY->value,
            ]))->count();

            $lateMinutes = (int) $empRecords->sum('late_minutes');
            $earlyLeaveMinutes = (int) $empRecords->sum('early_leave_minutes');
            $overtimeMinutes = (int) $empRecords->sum('overtime_minutes');
            $workingHours = (float) $empRecords->sum('total_working_hours');

            $attendanceRate = $scheduledDays > 0 ? round(($presentDays / $scheduledDays) * 100, 1) : 0;

            return (object) [
                'employee'            => $employee,
                'employee_id'         => $employee?->id,
                'scheduled_days'      => $scheduledDays,
                'present_days'        => $presentDays,
                'absent_days'         => $absentDays,
                'leave_days'          => $leaveDays,
                'rest_days'           => $restDays,
                'late_minutes'        => $lateMinutes,
                'early_leave_minutes' => $earlyLeaveMinutes,
                'overtime_minutes'    => $overtimeMinutes,
                'total_working_hours' => round($workingHours, 2),
                'attendance_rate'     => $attendanceRate,
            ];
        })->values();
    }

    /**
     * Get paginated monthly summary (Timesheet) records.
     *
     * @param array<string, mixed> $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getMonthlySummaryPaginated(array $filters = [], int $perPage = 25): LengthAwarePaginator
    {
        $allSummary = $this->getMonthlySummaryRecords($filters);
        $page = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;
        $sliced = $allSummary->slice(($page - 1) * $perPage, $perPage)->values();

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $sliced,
            $allSummary->count(),
            $perPage,
            $page,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(), 'query' => request()->query()]
        );
    }
}
