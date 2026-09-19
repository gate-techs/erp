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
            JobTitleSeeder::class,
            ShiftSeeder::class,
            WorkScheduleSeeder::class,
            OfficialHolidaySeeder::class,
            HrSettingSeeder::class,
            EmployeeSeeder::class,
            LeaveRequestSeeder::class,
            PermissionRequestSeeder::class,
            LoanSeeder::class,
            RewardSeeder::class,
            PenaltySeeder::class,
            PayrollSeeder::class,
            WorkforcePlanSeeder::class,
            JobRequisitionSeeder::class,
        ]);
    }
}
