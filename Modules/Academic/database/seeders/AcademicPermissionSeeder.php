<?php

namespace Modules\Academic\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AccessControl\Models\Permission;
use Modules\AccessControl\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class AcademicPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds for Academic module permissions.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $guardName = 'web';

        $academicPermissions = [
            // 1. Academic Years / Periods
            'academic.years.view',
            'academic.years.create',
            'academic.years.edit',
            'academic.years.delete',
            'academic.years.toggle-status',
            'academic.years.set-current',

            // 2. Academic Stages
            'academic.stages.view',
            'academic.stages.create',
            'academic.stages.edit',
            'academic.stages.delete',
            'academic.stages.toggle-status',

            // 3. Academic Classes
            'academic.classes.view',
            'academic.classes.create',
            'academic.classes.edit',
            'academic.classes.delete',
            'academic.classes.toggle-status',

            // 4. Academic Subjects
            'academic.subjects.view',
            'academic.subjects.create',
            'academic.subjects.edit',
            'academic.subjects.delete',
            'academic.subjects.toggle-status',
            'academic.subjects.clone',
            'academic.subjects.board',

            // 5. Academic Lessons
            'academic.lessons.view',
            'academic.lessons.create',
            'academic.lessons.edit',
            'academic.lessons.delete',
            'academic.lessons.toggle-status',

            // 6. Academic Weekly Plans
            'academic.weekly-plans.view',
            'academic.weekly-plans.create',
            'academic.weekly-plans.edit',
            'academic.weekly-plans.delete',
            'academic.weekly-plans.toggle-status',

            // 7. Academic Monthly Plans
            'academic.monthly-plans.view',
            'academic.monthly-plans.create',
            'academic.monthly-plans.edit',
            'academic.monthly-plans.delete',
            'academic.monthly-plans.toggle-status',

            // 8. Academic Fee Types
            'academic.fee-types.view',
            'academic.fee-types.create',
            'academic.fee-types.edit',
            'academic.fee-types.delete',
            'academic.fee-types.toggle-status',

            // 9. Academic Fees
            'academic.fees.view',
            'academic.fees.create',
            'academic.fees.edit',
            'academic.fees.delete',
            'academic.fees.toggle-status',

            // 9.1 Student Fees, Installments & Payments
            'academic.student-fees.view',
            'academic.student-fees.create',
            'academic.student-fees.payments.create',

            // 10. Academic Guardians
            'academic.guardians.view',
            'academic.guardians.create',
            'academic.guardians.edit',
            'academic.guardians.delete',
            'academic.guardians.toggle-status',

            // 11. Dashboards
            'academic.dashboard.view',
            'academic.fee-statistics.view',

            // 12. Academic Events
            'academic.events.view',
            'academic.events.create',
            'academic.events.edit',
            'academic.events.delete',

            // 13. App Calendar
            'academic.calender.view',
            'academic.calender.create',
            'academic.calender.edit',
            'academic.calender.delete',
            'academic.calendar.view',
            'academic.calendar.create',
            'academic.calendar.edit',
            'academic.calendar.delete',

            // 14. Personal Calendar
            'academic.personal-calendar.view',
            'academic.personal-calendar.create',
            'academic.personal-calendar.edit',
            'academic.personal-calendar.delete',

            // 15. Meetings Calendar
            'academic.meeting-calendar.view',
            'academic.meeting-calendar.create',
            'academic.meeting-calendar.edit',
            'academic.meeting-calendar.delete',

            // 16. Interviews Calendar
            'academic.interviews_calender.view',
            'academic.interviews_calender.create',
            'academic.interviews_calender.edit',
            'academic.interviews_calender.delete',
            'academic.interviews-calendar.view',
            'academic.interviews-calendar.create',
            'academic.interviews-calendar.edit',
            'academic.interviews-calendar.delete',
        ];

        // Create or update permissions
        foreach ($academicPermissions as $permissionName) {
            Permission::updateOrCreate(
                [
                    'name' => $permissionName,
                    'guard_name' => $guardName,
                ],
                [
                    'name' => $permissionName,
                    'guard_name' => $guardName,
                ]
            );
        }

        // Grant permissions to super_admin and admin
        $superAdminRole = Role::findOrCreate('super_admin', $guardName);
        $adminRole = Role::findOrCreate('admin', $guardName);

        $createdPermissions = Permission::where('guard_name', $guardName)
            ->whereIn('name', $academicPermissions)
            ->get();

        $superAdminRole->givePermissionTo($createdPermissions);
        $adminRole->givePermissionTo($createdPermissions);

        // Clear permission cache
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
