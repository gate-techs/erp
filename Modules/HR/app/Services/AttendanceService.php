<?php

namespace Modules\HR\Services;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\HR\Enums\AttendanceSource;
use Modules\HR\Enums\AttendanceStatus;
use Modules\HR\Enums\PunchType;
use Modules\HR\Models\Attendance;
use Modules\HR\Models\AttendanceRawLog;
use Modules\HR\Models\Employee;
use Modules\HR\Models\OfficialHoliday;
use Modules\HR\Models\Shift;
use Modules\HR\Repositories\Contracts\AttendanceRawLogRepositoryInterface;
use Modules\HR\Repositories\Contracts\AttendanceRepositoryInterface;
use Modules\HR\Repositories\Contracts\EmployeeRepositoryInterface;

class AttendanceService
{
    /**
     * Constructor injection for repositories.
     */
    public function __construct(
        protected AttendanceRepositoryInterface $attendanceRepo,
        protected AttendanceRawLogRepositoryInterface $rawLogRepo,
        protected EmployeeRepositoryInterface $employeeRepo
    ) {}

    //==========================================================================
    // 1. Matrix Data for Monthly Grid
    //==========================================================================
    /**
     * Prepare complete matrix data for the Monthly Grid View.
     *
     * @param array $filters
     * @return array
     */
    public function getMonthlyMatrixData(array $filters = []): array
    {
        $year = (int)($filters['year'] ?? now()->year);
        $month = (int)($filters['month'] ?? now()->month);

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        $today = now()->startOfDay();

        // Generate day headers
        $period = CarbonPeriod::create($startDate, $endDate);
        $days = [];
        $arabicDayNames = [
            0 => 'أحد',
            1 => 'إثنين',
            2 => 'ثلاثاء',
            3 => 'أربعاء',
            4 => 'خميس',
            5 => 'جمعة',
            6 => 'سبت',
        ];

        foreach ($period as $date) {
            $days[] = [
                'day'          => $date->day,
                'date'         => $date->format('Y-m-d'),
                'day_of_week'  => $date->dayOfWeek,
                'day_name_ar'  => $arabicDayNames[$date->dayOfWeek],
                'day_name_en'  => $date->format('D'),
                'is_today'     => $date->isToday(),
                'is_future'    => $date->greaterThan($today),
            ];
        }

        // Fetch Official Holidays for this month
        $holidays = OfficialHoliday::where('status', 'active')
            ->where(function ($q) use ($startDate, $endDate, $year) {
                $q->whereBetween('start_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                  ->orWhereBetween('end_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                  ->orWhere(function ($sub) use ($startDate, $endDate) {
                      $sub->where('start_date', '<=', $startDate->format('Y-m-d'))
                          ->where('end_date', '>=', $endDate->format('Y-m-d'));
                  });
            })
            ->get();

        // Get Employees Query
        $empFilters = array_filter([
            'id'                 => $filters['employee_id'] ?? null,
            'primary_branch_id'  => $filters['branch_id'] ?? null,
            'department_id'      => $filters['department_id'] ?? null,
            'status'             => 'active',
        ]);

        $employees = $this->employeeRepo->getAll($empFilters, [
            'translations',
            'department.translations',
            'primaryBranch',
            'shift.translations',
            'workSchedule.days',
        ]);

        // Get all attendances in this range
        $attendances = $this->attendanceRepo->getBetweenDates(
            $startDate->format('Y-m-d'),
            $endDate->format('Y-m-d'),
            $filters,
            ['employee.translations', 'shift.translations']
        )->groupBy(['employee_id', fn($item) => Carbon::parse($item->date)->format('Y-m-d')]);

        // Build Employee Matrix Rows
        $matrixRows = [];
        foreach ($employees as $employee) {
            $rowDays = [];
            $scheduleDays = $employee->workSchedule?->days?->keyBy('day_of_week');

            foreach ($days as $dayInfo) {
                $dateStr = $dayInfo['date'];
                $isFuture = $dayInfo['is_future'];
                $dayOfWeek = $dayInfo['day_of_week'];

                // 1. Check if Attendance record already exists
                $attendance = $attendances[$employee->id][$dateStr][0] ?? null;

                if ($attendance) {
                    $rowDays[$dayInfo['day']] = [
                        'date'          => $dateStr,
                        'status'        => $attendance->status->value,
                        'status_label'  => $attendance->status->label(),
                        'icon'          => $attendance->status->icon(),
                        'badge_class'   => $attendance->status->badgeClass(),
                        'check_in'      => $attendance->check_in ? Carbon::parse($attendance->check_in)->format('h:i A') : null,
                        'check_out'     => $attendance->check_out ? Carbon::parse($attendance->check_out)->format('h:i A') : null,
                        'work_hours'    => $attendance->total_working_hours,
                        'late_minutes'  => $attendance->late_minutes,
                        'attendance_id' => $attendance->id,
                        'has_record'    => true,
                    ];
                    continue;
                }

                // 2. If Future date
                if ($isFuture) {
                    $rowDays[$dayInfo['day']] = [
                        'date'          => $dateStr,
                        'status'        => 'future',
                        'status_label'  => __('Future'),
                        'icon'          => '-',
                        'badge_class'   => 'bg-light text-muted',
                        'check_in'      => null,
                        'check_out'     => null,
                        'work_hours'    => 0,
                        'late_minutes'  => 0,
                        'attendance_id' => null,
                        'has_record'    => false,
                    ];
                    continue;
                }

                // 3. Check for Holiday
                $holiday = $holidays->first(function ($h) use ($dateStr, $employee) {
                    $isDateInRange = $dateStr >= $h->start_date->format('Y-m-d') && $dateStr <= $h->end_date->format('Y-m-d');
                    $isBranchMatch = empty($h->branch_id) || $h->branch_id == $employee->primary_branch_id;
                    return $isDateInRange && $isBranchMatch;
                });

                if ($holiday) {
                    $rowDays[$dayInfo['day']] = [
                        'date'          => $dateStr,
                        'status'        => 'holiday',
                        'status_label'  => $holiday->name ?? __('Official Holiday'),
                        'icon'          => '⭐',
                        'badge_class'   => 'bg-label-secondary text-secondary',
                        'check_in'      => null,
                        'check_out'     => null,
                        'work_hours'    => 0,
                        'late_minutes'  => 0,
                        'attendance_id' => null,
                        'has_record'    => false,
                    ];
                    continue;
                }

                // 4. Check Work Schedule Off Day (Rest day / Weekend)
                $scheduleDay = $scheduleDays[$dayOfWeek] ?? null;
                $isOffDay = $scheduleDay ? (bool)$scheduleDay->is_off_day : in_array($dayOfWeek, [5, 6]); // default Fri, Sat

                if ($isOffDay) {
                    $rowDays[$dayInfo['day']] = [
                        'date'          => $dateStr,
                        'status'        => 'rest_day',
                        'status_label'  => __('Rest Day'),
                        'icon'          => '⃠',
                        'sub_label'     => __('يوم عطلة'),
                        'badge_class'   => 'bg-label-dark text-muted',
                        'check_in'      => null,
                        'check_out'     => null,
                        'work_hours'    => 0,
                        'late_minutes'  => 0,
                        'attendance_id' => null,
                        'has_record'    => false,
                    ];
                    continue;
                }

                // 5. Default past day without record = Absent
                $rowDays[$dayInfo['day']] = [
                    'date'          => $dateStr,
                    'status'        => 'absent',
                    'status_label'  => __('Absent'),
                    'icon'          => '✕',
                    'badge_class'   => 'bg-label-danger text-danger',
                    'check_in'      => null,
                    'check_out'     => null,
                    'work_hours'    => 0,
                    'late_minutes'  => 0,
                    'attendance_id' => null,
                    'has_record'    => false,
                ];
            }

            // Summary stats for employee row
            $presents = collect($rowDays)->where('status', 'present')->count();
            $lates    = collect($rowDays)->where('status', 'late')->count();
            $absents  = collect($rowDays)->where('status', 'absent')->count();
            $leaves   = collect($rowDays)->where('status', 'on_leave')->count();

            $matrixRows[] = [
                'employee_id'    => $employee->id,
                'employee_name'  => $employee->name,
                'employee_code'  => $employee->employee_code,
                'avatar'         => $employee->avatar,
                'department'     => $employee->department?->name,
                'branch'         => $employee->primaryBranch?->name,
                'days'           => $rowDays,
                'stats'          => [
                    'present' => $presents,
                    'late'    => $lates,
                    'absent'  => $absents,
                    'leave'   => $leaves,
                ],
            ];
        }

        $monthNames = [
            1  => __('January'),
            2  => __('February'),
            3  => __('March'),
            4  => __('April'),
            5  => __('May'),
            6  => __('June'),
            7  => __('July'),
            8  => __('August'),
            9  => __('September'),
            10 => __('October'),
            11 => __('November'),
            12 => __('December'),
        ];

        return [
            'year'        => $year,
            'month'       => $month,
            'month_name'  => $monthNames[$month] ?? Carbon::createFromDate($year, $month, 1)->translatedFormat('F'),
            'days'        => $days,
            'matrix_rows' => $matrixRows,
            'total_count' => count($matrixRows),
        ];
    }

    //==========================================================================
    // 2. Record Punch (Check In / Check Out from Device, Mobile, or Manual Quick)
    //==========================================================================
    /**
     * Record a raw punch and process daily attendance summary.
     *
     * @param array $data
     * @return Attendance
     */
    public function recordPunch(array $data): Attendance
    {
        return DB::transaction(function () use ($data) {
            $employeeId = $data['employee_id'];
            $punchTime = Carbon::parse($data['punch_time'] ?? now());
            $dateStr = $punchTime->format('Y-m-d');
            $timeStr = $punchTime->format('H:i:s');
            $punchType = $data['punch_type'] ?? PunchType::AUTO->value;
            $source = $data['source'] ?? AttendanceSource::MANUAL->value;

            // 1. Create Raw Log
            $rawLog = $this->rawLogRepo->create([
                'employee_id'      => $employeeId,
                'device_user_id'   => $data['device_user_id'] ?? null,
                'punch_time'       => $punchTime,
                'punch_type'       => $punchType,
                'source'           => $source,
                'device_id'        => $data['device_id'] ?? null,
                'latitude'         => $data['latitude'] ?? null,
                'longitude'        => $data['longitude'] ?? null,
                'location_address' => $data['location_address'] ?? null,
                'ip_address'       => $data['ip_address'] ?? request()->ip(),
                'device_info'      => $data['device_info'] ?? request()->userAgent(),
                'snapshot'         => $data['snapshot'] ?? null,
                'is_processed'     => true,
                'created_by'       => Auth::id(),
            ]);

            // 2. Fetch Employee with Shift
            $employee = Employee::with('shift')->findOrFail($employeeId);
            $shift = $employee->shift;

            // 3. Get or initialize today's Attendance
            $attendance = $this->attendanceRepo->findByEmployeeAndDate($employeeId, $dateStr);

            if (!$attendance) {
                $attendance = new Attendance([
                    'employee_id'     => $employeeId,
                    'shift_id'        => $shift?->id,
                    'date'            => $dateStr,
                    'required_hours'  => $shift?->total_hours ?? 8.0,
                    'created_by'      => Auth::id(),
                ]);
            }

            // Determine In / Out times
            if ($punchType === PunchType::CHECK_IN->value || (!$attendance->check_in && $punchType === PunchType::AUTO->value)) {
                $attendance->check_in = $timeStr;
                $attendance->check_in_source = $source;
            } elseif ($punchType === PunchType::CHECK_OUT->value || ($attendance->check_in && $punchType === PunchType::AUTO->value)) {
                $attendance->check_out = $timeStr;
                $attendance->check_out_source = $source;
            }

            // Recalculate working hours & late/early leave
            $this->calculateAttendanceMetrics($attendance, $shift);

            $attendance->save();

            return $attendance;
        });
    }

    //==========================================================================
    // 3. Manual Entry / Adjustment by HR
    //==========================================================================
    /**
     * Save manual attendance directly from Dashboard.
     *
     * @param array $data
     * @return Attendance
     */
    public function saveManualAttendance(array $data): Attendance
    {
        return DB::transaction(function () use ($data) {
            $employee = Employee::with('shift')->findOrFail($data['employee_id']);
            $shift = $employee->shift;
            $date = $data['date'];

            $attendance = $this->attendanceRepo->findByEmployeeAndDate($employee->id, $date);

            if (!$attendance) {
                $attendance = new Attendance([
                    'employee_id'    => $employee->id,
                    'date'           => $date,
                    'shift_id'       => $shift?->id,
                    'required_hours' => $shift?->total_hours ?? 8.0,
                    'created_by'     => Auth::id(),
                ]);
            }

            $attendance->check_in = !empty($data['check_in']) ? Carbon::parse($data['check_in'])->format('H:i:s') : null;
            $attendance->check_out = !empty($data['check_out']) ? Carbon::parse($data['check_out'])->format('H:i:s') : null;
            $attendance->status = $data['status'] ?? AttendanceStatus::PRESENT->value;
            $attendance->check_in_source = AttendanceSource::MANUAL->value;
            $attendance->check_out_source = AttendanceSource::MANUAL->value;
            $attendance->is_manual_adjusted = true;
            $attendance->adjustment_reason = $data['adjustment_reason'] ?? $data['notes'] ?? null;
            $attendance->adjusted_by = Auth::id();

            // Auto calculate if both check in & out exist
            if ($attendance->check_in && $attendance->check_out) {
                $this->calculateAttendanceMetrics($attendance, $shift);
            } else {
                $attendance->total_working_hours = (float)($data['total_working_hours'] ?? 0);
                $attendance->late_minutes = (int)($data['late_minutes'] ?? 0);
                $attendance->early_leave_minutes = (int)($data['early_leave_minutes'] ?? 0);
                $attendance->overtime_minutes = (int)($data['overtime_minutes'] ?? 0);
            }

            $attendance->save();

            return $attendance;
        });
    }

    //==========================================================================
    // 4. Update Existing Attendance Record
    //==========================================================================
    /**
     * Update an attendance record with HR adjustment audit.
     *
     * @param int $id
     * @param array $data
     * @return Attendance
     */
    public function updateAttendance(int $id, array $data): Attendance
    {
        return DB::transaction(function () use ($id, $data) {
            $attendance = Attendance::with(['employee.shift'])->findOrFail($id);
            $shift = $attendance->employee?->shift;

            $attendance->check_in = !empty($data['check_in']) ? Carbon::parse($data['check_in'])->format('H:i:s') : null;
            $attendance->check_out = !empty($data['check_out']) ? Carbon::parse($data['check_out'])->format('H:i:s') : null;
            $attendance->status = $data['status'];
            $attendance->is_manual_adjusted = true;
            $attendance->adjustment_reason = $data['adjustment_reason'] ?? $data['notes'] ?? null;
            $attendance->adjusted_by = Auth::id();

            if ($attendance->check_in && $attendance->check_out) {
                $this->calculateAttendanceMetrics($attendance, $shift);
            } else {
                $attendance->total_working_hours = (float)($data['total_working_hours'] ?? 0);
                $attendance->late_minutes = (int)($data['late_minutes'] ?? 0);
                $attendance->early_leave_minutes = (int)($data['early_leave_minutes'] ?? 0);
                $attendance->overtime_minutes = (int)($data['overtime_minutes'] ?? 0);
            }

            $attendance->save();

            return $attendance;
        });
    }

    //==========================================================================
    // 5. Delete Attendance
    //==========================================================================
    /**
     * Delete an attendance record.
     *
     * @param int $id
     * @return bool
     */
    public function deleteAttendance(int $id): bool
    {
        return $this->attendanceRepo->delete($id);
    }

    //==========================================================================
    // 6. Internal Calculator
    //==========================================================================
    /**
     * Calculate total hours, late minutes, early leave, overtime, and status.
     *
     * @param Attendance $attendance
     * @param Shift|null $shift
     * @return void
     */
    protected function calculateAttendanceMetrics(Attendance &$attendance, ?Shift $shift): void
    {
        if (!$attendance->check_in) {
            return;
        }

        $dateStr = Carbon::parse($attendance->date)->format('Y-m-d');
        $checkInDateTime = Carbon::parse($dateStr . ' ' . $attendance->check_in);

        // 1. Calculate Late Minutes if Shift exists
        if ($shift && $shift->start_time) {
            $shiftStart = Carbon::parse($dateStr . ' ' . $shift->start_time);
            $graceIn = $shift->grace_period_in ?? 15;
            $shiftStartWithGrace = $shiftStart->copy()->addMinutes($graceIn);

            if ($checkInDateTime->greaterThan($shiftStartWithGrace)) {
                $attendance->late_minutes = $shiftStart->diffInMinutes($checkInDateTime);
                $attendance->status = AttendanceStatus::LATE->value;
            } else {
                $attendance->late_minutes = 0;
                $attendance->status = AttendanceStatus::PRESENT->value;
            }
        } else {
            $attendance->late_minutes = 0;
            $attendance->status = AttendanceStatus::PRESENT->value;
        }

        // 2. If Check-out exists, calculate total hours and early leave / overtime
        if ($attendance->check_out) {
            $checkOutDateTime = Carbon::parse($dateStr . ' ' . $attendance->check_out);

            // Overnight shift check
            if ($checkOutDateTime->lessThan($checkInDateTime)) {
                $checkOutDateTime->addDay();
            }

            $diffMinutes = $checkInDateTime->diffInMinutes($checkOutDateTime);
            $attendance->total_working_hours = round($diffMinutes / 60, 2);

            if ($shift && $shift->end_time) {
                $shiftEnd = Carbon::parse($dateStr . ' ' . $shift->end_time);
                if ($shift->is_overnight) {
                    $shiftEnd->addDay();
                }

                $graceOut = $shift->grace_period_out ?? 0;
                $shiftEndWithGrace = $shiftEnd->copy()->subMinutes($graceOut);

                if ($checkOutDateTime->lessThan($shiftEndWithGrace)) {
                    $attendance->early_leave_minutes = $checkOutDateTime->diffInMinutes($shiftEnd);
                } else {
                    $attendance->early_leave_minutes = 0;
                }

                // Overtime calculation
                if ($checkOutDateTime->greaterThan($shiftEnd)) {
                    $attendance->overtime_minutes = $shiftEnd->diffInMinutes($checkOutDateTime);
                } else {
                    $attendance->overtime_minutes = 0;
                }
            }
        }
    }

    //==========================================================================
    // 7. Excel Import Handler
    //==========================================================================
    /**
     * Import attendance records from CSV / Excel file.
     *
     * @param UploadedFile $file
     * @return array
     */
    public function importExcel(UploadedFile $file): array
    {
        $rows = array_map('str_getcsv', file($file->getRealPath()));
        if (empty($rows)) {
            throw new Exception(__('The uploaded file is empty.'));
        }

        // Remove header row
        $header = array_shift($rows);
        $importedCount = 0;
        $errors = [];

        foreach ($rows as $index => $row) {
            if (empty(array_filter($row))) {
                continue;
            }

            // Expected columns: employee_code, date, check_in, check_out, status
            $code = trim($row[0] ?? '');
            $date = trim($row[1] ?? '');
            $in   = trim($row[2] ?? '');
            $out  = trim($row[3] ?? '');
            $stat = trim($row[4] ?? AttendanceStatus::PRESENT->value);

            if (!$code || !$date) {
                $errors[] = __('Row :row: Missing employee code or date.', ['row' => $index + 2]);
                continue;
            }

            $employee = Employee::where('employee_code', $code)->first();
            if (!$employee) {
                $errors[] = __('Row :row: Employee code ":code" not found.', ['row' => $index + 2, 'code' => $code]);
                continue;
            }

            try {
                $this->saveManualAttendance([
                    'employee_id' => $employee->id,
                    'date'        => Carbon::parse($date)->format('Y-m-d'),
                    'check_in'    => $in ? Carbon::parse($in)->format('H:i:s') : null,
                    'check_out'   => $out ? Carbon::parse($out)->format('H:i:s') : null,
                    'status'      => $stat ?: AttendanceStatus::PRESENT->value,
                    'notes'       => 'Imported from Excel',
                ]);
                $importedCount++;
            } catch (Exception $e) {
                $errors[] = __('Row :row: Error processing data - :msg', ['row' => $index + 2, 'msg' => $e->getMessage()]);
            }
        }

        return [
            'imported_count' => $importedCount,
            'errors'         => $errors,
        ];
    }

    //==========================================================================
    // 8. Employee Day Attendance Timeline
    //==========================================================================
    /**
     * Get real-time/day timeline progression for an employee.
     *
     * @param int $employeeId
     * @param string|null $date
     * @return array
     */
    public function getEmployeeDayTimeline(int $employeeId, ?string $date = null): array
    {
        $dateStr = $date ? Carbon::parse($date)->format('Y-m-d') : now()->format('Y-m-d');
        $targetDate = Carbon::parse($dateStr);
        $isToday = $targetDate->isToday();

        $employee = Employee::with(['shift.translations', 'department.translations', 'primaryBranch'])
            ->find($employeeId);

        if (!$employee) {
            throw new Exception(__('Employee not found.'));
        }

        $attendance = Attendance::where('employee_id', $employeeId)
            ->whereDate('date', $dateStr)
            ->first();

        $shift = $employee->shift;
        $shiftStart = $shift?->start_time ? Carbon::parse($dateStr . ' ' . $shift->start_time) : null;
        $shiftEnd = $shift?->end_time ? Carbon::parse($dateStr . ' ' . $shift->end_time) : null;
        if ($shift && $shift->is_overnight && $shiftEnd && $shiftStart && $shiftEnd->lessThan($shiftStart)) {
            $shiftEnd->addDay();
        }

        $checkInTime = $attendance?->check_in ? Carbon::parse($dateStr . ' ' . $attendance->check_in) : null;
        $checkOutTime = $attendance?->check_out ? Carbon::parse($dateStr . ' ' . $attendance->check_out) : null;

        $hasCheckIn = !empty($checkInTime);
        $hasCheckOut = !empty($checkOutTime);

        // Progress status calculation
        // States: 'not_started', 'in_progress', 'completed'
        $progressState = 'not_started';
        $statusBadge = __('لم يبدأ بعد');
        $statusBadgeColor = 'secondary';
        $progressPercent = 0;

        if ($hasCheckIn && $hasCheckOut) {
            $progressState = 'completed';
            $statusBadge = __('مكتملة ✓');
            $statusBadgeColor = 'success';
            $progressPercent = 100;
        } elseif ($hasCheckIn) {
            $progressState = 'in_progress';
            $statusBadge = __('الدوام جاري حالياً');
            $statusBadgeColor = 'primary';
            $progressPercent = 50;

            // Calculate dynamic progress if today and shift exists
            if ($isToday && $shiftStart && $shiftEnd) {
                $now = now();
                if ($now->greaterThanOrEqualTo($shiftEnd)) {
                    $progressPercent = 90;
                } elseif ($now->greaterThan($shiftStart)) {
                    $totalShiftMinutes = max(1, $shiftStart->diffInMinutes($shiftEnd));
                    $elapsedMinutes = $shiftStart->diffInMinutes($now);
                    $calcPct = round(($elapsedMinutes / $totalShiftMinutes) * 100);
                    $progressPercent = min(90, max(20, $calcPct));
                } else {
                    $progressPercent = 25;
                }
            }
        }

        return [
            'employee' => [
                'id'          => $employee->id,
                'name'        => $employee->name,
                'code'        => $employee->employee_code,
                'avatar_url'  => $employee->avatar ? asset('storage/' . $employee->avatar) : null,
                'department'  => $employee->department?->name ?? '-',
                'branch'      => $employee->primaryBranch?->name ?? '-',
                'shift_name'  => $shift?->name ?? __('وردية قياسية'),
                'shift_start' => $shiftStart ? $shiftStart->format('h:i A') : null,
                'shift_end'   => $shiftEnd ? $shiftEnd->format('h:i A') : null,
            ],
            'date'                  => $dateStr,
            'is_today'              => $isToday,
            'has_attendance'        => !empty($attendance),
            'has_check_in'          => $hasCheckIn,
            'has_check_out'         => $hasCheckOut,
            'check_in_formatted'    => $checkInTime ? $checkInTime->format('h:i A') : null,
            'check_out_formatted'   => $checkOutTime ? $checkOutTime->format('h:i A') : null,
            'progress_state'        => $progressState,
            'progress_percent'      => $progressPercent,
            'status_badge'          => $statusBadge,
            'status_badge_color'    => $statusBadgeColor,
            'attendance_status'     => $attendance?->status?->value ?? ($hasCheckIn ? 'present' : 'absent'),
            'attendance_status_lbl' => $attendance?->status?->label() ?? ($hasCheckIn ? __('حاضر') : __('غائب / لم يسجل')),
            'total_working_hours'   => $attendance?->total_working_hours ?? 0,
            'late_minutes'          => $attendance?->late_minutes ?? 0,
            'early_leave_minutes'   => $attendance?->early_leave_minutes ?? 0,
        ];
    }
}

