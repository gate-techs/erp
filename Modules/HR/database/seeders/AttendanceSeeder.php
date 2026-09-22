<?php

namespace Modules\HR\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Modules\HR\Enums\AttendanceSource;
use Modules\HR\Enums\AttendanceStatus;
use Modules\HR\Models\Attendance;
use Modules\HR\Models\Employee;
use Modules\HR\Models\Shift;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = Employee::all();
        if ($employees->isEmpty()) {
            return;
        }

        $defaultShift = Shift::first() ?? Shift::create([
            'name'                => 'الوردية الصباحية الرسمية',
            'start_time'          => '08:00:00',
            'end_time'            => '16:00:00',
            'is_night_shift'      => false,
            'is_flexible'         => false,
            'grace_period_in'     => 15,
            'grace_period_out'    => 15,
            'is_active'           => true,
        ]);

        $startDate = Carbon::now()->subDays(29)->startOfDay();
        $endDate   = Carbon::now()->startOfDay();

        $attendances = [];

        foreach ($employees as $employee) {
            $shift = $defaultShift;
            $currentDate = $startDate->copy();

            while ($currentDate->lte($endDate)) {
                $isWeekend = in_array($currentDate->dayOfWeek, [Carbon::FRIDAY, Carbon::SATURDAY]);
                $dateStr = $currentDate->toDateString();

                if ($isWeekend) {
                    $attendances[] = [
                        'employee_id'         => $employee->id,
                        'shift_id'            => $shift->id,
                        'date'                => $dateStr,
                        'check_in'            => null,
                        'check_out'           => null,
                        'total_working_hours' => 0.00,
                        'required_hours'      => 8.00,
                        'late_minutes'        => 0,
                        'early_leave_minutes' => 0,
                        'overtime_minutes'    => 0,
                        'status'              => AttendanceStatus::REST_DAY->value,
                        'check_in_source'     => null,
                        'check_out_source'    => null,
                        'is_manual_adjusted'  => false,
                        'adjustment_reason'   => null,
                        'created_at'          => now(),
                        'updated_at'          => now(),
                    ];
                } else {
                    // Random pattern based on employee ID & date
                    $seed = ($employee->id * 31 + $currentDate->day) % 100;

                    if ($seed < 70) {
                        // Regular Present (on-time)
                        $inMinute = rand(0, 10);
                        $outMinute = rand(0, 20);
                        $checkIn = Carbon::parse($dateStr . ' 07:5' . rand(0, 9) . ':00');
                        $checkOut = Carbon::parse($dateStr . ' 16:' . str_pad((string)$outMinute, 2, '0', STR_PAD_LEFT) . ':00');
                        $overtime = $outMinute > 15 ? ($outMinute - 15) : 0;

                        $attendances[] = [
                            'employee_id'         => $employee->id,
                            'shift_id'            => $shift->id,
                            'date'                => $dateStr,
                            'check_in'            => $checkIn->format('Y-m-d H:i:s'),
                            'check_out'           => $checkOut->format('Y-m-d H:i:s'),
                            'total_working_hours' => round(8.00 + ($overtime / 60), 2),
                            'required_hours'      => 8.00,
                            'late_minutes'        => 0,
                            'early_leave_minutes' => 0,
                            'overtime_minutes'    => $overtime,
                            'status'              => AttendanceStatus::PRESENT->value,
                            'check_in_source'     => AttendanceSource::BIOMETRIC_DEVICE->value,
                            'check_out_source'    => AttendanceSource::BIOMETRIC_DEVICE->value,
                            'is_manual_adjusted'  => false,
                            'adjustment_reason'   => null,
                            'created_at'          => now(),
                            'updated_at'          => now(),
                        ];
                    } elseif ($seed < 85) {
                        // Late Arrival or Early Leave
                        $lateMins = rand(20, 65);
                        $earlyLeaveMins = ($seed % 2 === 0) ? rand(15, 45) : 0;

                        $checkIn = Carbon::parse($dateStr . ' 08:00:00')->addMinutes($lateMins);
                        $checkOut = Carbon::parse($dateStr . ' 16:00:00')->subMinutes($earlyLeaveMins);

                        $workHours = max(0, 8.0 - (($lateMins + $earlyLeaveMins) / 60));

                        $attendances[] = [
                            'employee_id'         => $employee->id,
                            'shift_id'            => $shift->id,
                            'date'                => $dateStr,
                            'check_in'            => $checkIn->format('Y-m-d H:i:s'),
                            'check_out'           => $checkOut->format('Y-m-d H:i:s'),
                            'total_working_hours' => round($workHours, 2),
                            'required_hours'      => 8.00,
                            'late_minutes'        => $lateMins,
                            'early_leave_minutes' => $earlyLeaveMins,
                            'overtime_minutes'    => 0,
                            'status'              => AttendanceStatus::LATE->value,
                            'check_in_source'     => AttendanceSource::BIOMETRIC_DEVICE->value,
                            'check_out_source'    => AttendanceSource::BIOMETRIC_DEVICE->value,
                            'is_manual_adjusted'  => false,
                            'adjustment_reason'   => null,
                            'created_at'          => now(),
                            'updated_at'          => now(),
                        ];
                    } elseif ($seed < 93) {
                        // Absent without leave (for Absenteeism report)
                        $attendances[] = [
                            'employee_id'         => $employee->id,
                            'shift_id'            => $shift->id,
                            'date'                => $dateStr,
                            'check_in'            => null,
                            'check_out'           => null,
                            'total_working_hours' => 0.00,
                            'required_hours'      => 8.00,
                            'late_minutes'        => 0,
                            'early_leave_minutes' => 0,
                            'overtime_minutes'    => 0,
                            'status'              => AttendanceStatus::ABSENT->value,
                            'check_in_source'     => null,
                            'check_out_source'    => null,
                            'is_manual_adjusted'  => false,
                            'adjustment_reason'   => 'غياب غير مبرر دون إخطار مسبق',
                            'created_at'          => now(),
                            'updated_at'          => now(),
                        ];
                    } else {
                        // Approved Leave (for Monthly summary)
                        $attendances[] = [
                            'employee_id'         => $employee->id,
                            'shift_id'            => $shift->id,
                            'date'                => $dateStr,
                            'check_in'            => null,
                            'check_out'           => null,
                            'total_working_hours' => 0.00,
                            'required_hours'      => 8.00,
                            'late_minutes'        => 0,
                            'early_leave_minutes' => 0,
                            'overtime_minutes'    => 0,
                            'status'              => AttendanceStatus::ON_LEAVE->value,
                            'check_in_source'     => null,
                            'check_out_source'    => null,
                            'is_manual_adjusted'  => true,
                            'adjustment_reason'   => 'إجازة سنوية معتمدة',
                            'created_at'          => now(),
                            'updated_at'          => now(),
                        ];
                    }
                }

                $currentDate->addDay();
            }
        }

        // Insert in chunks
        foreach (array_chunk($attendances, 100) as $chunk) {
            Attendance::insert($chunk);
        }
    }
}
