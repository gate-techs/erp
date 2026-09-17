<?php

namespace Modules\HR\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\HR\Enums\WorkScheduleStatus;
use Modules\HR\Enums\WorkScheduleType;
use Modules\HR\Models\Department;
use Modules\HR\Models\Shift;
use Modules\HR\Models\WorkSchedule;
use Modules\HR\Models\WorkScheduleDay;

class WorkScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $morningShift  = Shift::where('code', 'SH-MORNING')->first();
        $partTimeShift = Shift::where('code', 'SH-PART-TIME')->first();
        $nightShift    = Shift::where('code', 'SH-NIGHT')->first();

        $hrDept  = Department::where('code', 'ADM-HR')->first();
        $eduDept = Department::where('code', 'EDU-CARE')->first();

        // 1. General Admin Schedule (5 Days - Fri & Sat Off)
        $adminSchedule = WorkSchedule::updateOrCreate(
            ['code' => 'SCH-ADMIN-5D'],
            [
                'type'               => WorkScheduleType::FIXED->value,
                'department_id'      => $hrDept?->id,
                'working_days_count' => 5,
                'total_weekly_hours' => 40.0,
                'is_default'         => true,
                'color'              => '#7367f0',
                'status'             => WorkScheduleStatus::ACTIVE->value,
                'ar'                 => [
                    'name'        => 'جدول دوام الإدارة العامة (5 أيام)',
                    'description' => 'الجدول المعتمد للإدارات العامة والمكاتب التنفيذية (عطلة الجمعة والسبت)',
                ],
                'en'                 => [
                    'name'        => 'General Admin Schedule (5 Days)',
                    'description' => 'Standard schedule for administrative staff (Friday & Saturday Off)',
                ],
            ]
        );

        // Days for Admin (0=Sun to 4=Thu Work, 5=Fri & 6=Sat Off)
        $adminDays = [
            6 => ['is_off_day' => true, 'shift_id' => null, 'work_hours' => 0],
            0 => ['is_off_day' => false, 'shift_id' => $morningShift?->id, 'work_hours' => 8],
            1 => ['is_off_day' => false, 'shift_id' => $morningShift?->id, 'work_hours' => 8],
            2 => ['is_off_day' => false, 'shift_id' => $morningShift?->id, 'work_hours' => 8],
            3 => ['is_off_day' => false, 'shift_id' => $morningShift?->id, 'work_hours' => 8],
            4 => ['is_off_day' => false, 'shift_id' => $morningShift?->id, 'work_hours' => 8],
            5 => ['is_off_day' => true, 'shift_id' => null, 'work_hours' => 0],
        ];

        foreach ($adminDays as $dayNum => $d) {
            WorkScheduleDay::updateOrCreate(
                [
                    'work_schedule_id' => $adminSchedule->id,
                    'day_of_week'      => $dayNum,
                ],
                $d
            );
        }

        // 2. Academic / Teachers Schedule (6 Days - Friday Off only)
        $eduSchedule = WorkSchedule::updateOrCreate(
            ['code' => 'SCH-EDU-6D'],
            [
                'type'               => WorkScheduleType::FIXED->value,
                'department_id'      => $eduDept?->id,
                'working_days_count' => 6,
                'total_weekly_hours' => 45.0,
                'is_default'         => false,
                'color'              => '#00cfe8',
                'status'             => WorkScheduleStatus::ACTIVE->value,
                'ar'                 => [
                    'name'        => 'جدول دوام الكادر التعليمي (6 أيام)',
                    'description' => 'جدول الدوام الأسبوعي المعتمد للمعلمات والكادر الأكاديمي (عطلة الجمعة فقط)',
                ],
                'en'                 => [
                    'name'        => 'Academic & Teachers Schedule (6 Days)',
                    'description' => 'Weekly schedule for teachers and childcare staff (Friday Off only)',
                ],
            ]
        );

        $eduDays = [
            6 => ['is_off_day' => false, 'shift_id' => $partTimeShift?->id ?? $morningShift?->id, 'work_hours' => 5],
            0 => ['is_off_day' => false, 'shift_id' => $morningShift?->id, 'work_hours' => 8],
            1 => ['is_off_day' => false, 'shift_id' => $morningShift?->id, 'work_hours' => 8],
            2 => ['is_off_day' => false, 'shift_id' => $morningShift?->id, 'work_hours' => 8],
            3 => ['is_off_day' => false, 'shift_id' => $morningShift?->id, 'work_hours' => 8],
            4 => ['is_off_day' => false, 'shift_id' => $morningShift?->id, 'work_hours' => 8],
            5 => ['is_off_day' => true, 'shift_id' => null, 'work_hours' => 0],
        ];

        foreach ($eduDays as $dayNum => $d) {
            WorkScheduleDay::updateOrCreate(
                [
                    'work_schedule_id' => $eduSchedule->id,
                    'day_of_week'      => $dayNum,
                ],
                $d
            );
        }

        // 3. Night Shifts / Facilities Schedule
        $nightSchedule = WorkSchedule::updateOrCreate(
            ['code' => 'SCH-NIGHT-6D'],
            [
                'type'               => WorkScheduleType::ROTATIONAL->value,
                'department_id'      => null,
                'working_days_count' => 6,
                'total_weekly_hours' => 48.0,
                'is_default'         => false,
                'color'              => '#28c76f',
                'status'             => WorkScheduleStatus::ACTIVE->value,
                'ar'                 => [
                    'name'        => 'جدول نوبات الحراسة والدعم الليلي',
                    'description' => 'جدول المناوبات والورديات الليلية للأمن والمرافق والسلامة (تمتد لليوم التالي)',
                ],
                'en'                 => [
                    'name'        => 'Security & Night Guard Schedule',
                    'description' => 'Night shift schedule for campus security and technical staff',
                ],
            ]
        );

        $nightDays = [
            6 => ['is_off_day' => false, 'shift_id' => $nightShift?->id, 'work_hours' => 8],
            0 => ['is_off_day' => false, 'shift_id' => $nightShift?->id, 'work_hours' => 8],
            1 => ['is_off_day' => false, 'shift_id' => $nightShift?->id, 'work_hours' => 8],
            2 => ['is_off_day' => false, 'shift_id' => $nightShift?->id, 'work_hours' => 8],
            3 => ['is_off_day' => false, 'shift_id' => $nightShift?->id, 'work_hours' => 8],
            4 => ['is_off_day' => false, 'shift_id' => $nightShift?->id, 'work_hours' => 8],
            5 => ['is_off_day' => true, 'shift_id' => null, 'work_hours' => 0],
        ];

        foreach ($nightDays as $dayNum => $d) {
            WorkScheduleDay::updateOrCreate(
                [
                    'work_schedule_id' => $nightSchedule->id,
                    'day_of_week'      => $dayNum,
                ],
                $d
            );
        }
    }
}
