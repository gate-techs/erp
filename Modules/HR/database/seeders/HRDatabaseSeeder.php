<?php

namespace Modules\HR\Database\Seeders;

use Illuminate\Database\Seeder;

class HRDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            DepartmentSeeder::class,
            // JobTitleSeeder::class, // [موقوف: يتم توليد المسميات الوظيفية من ملف الإكسيل عبر EmployeeFromExcelSeeder]
            ShiftSeeder::class,
            WorkScheduleSeeder::class,
            OfficialHolidaySeeder::class,
            HrSettingSeeder::class,
            EmployeeFromExcelSeeder::class, // [جديد: استيراد الموظفين والوظائف والجنسيات والبنوك من Excel]
            // EmployeeSeeder::class, // [موقوف: تم استبداله بـ EmployeeFromExcelSeeder]
            LeaveRequestSeeder::class,
            PermissionRequestSeeder::class,
            LoanSeeder::class,
            RewardSeeder::class,
            PenaltySeeder::class,
            PayrollSeeder::class,
            WorkforcePlanSeeder::class,
            JobRequisitionSeeder::class,
            JobOpeningSeeder::class,
            JobApplicationSeeder::class,
            InterviewSeeder::class,
            AttendanceSeeder::class,
        ]);
    }
}
