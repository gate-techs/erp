<?php

namespace Modules\AccessControl\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AccessControl\Models\Permission;
use Modules\AccessControl\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class SchedulesAndDashboardsPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds for Schedules & Events and Group Dashboards permissions.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $guardName = 'web';

        $permissionsByModule = [
            // ==========================================
            // 1. Group / Module Dashboards (لوحات التحكم)
            // ==========================================
            'dashboards' => [
                'dashboard.view',                  // لوحة التحكم العامة
                'academic.dashboard.view',         // لوحة تحكم الإدارة الأكاديمية
                'academic.fee-statistics.view',    // لوحة تحكم إحصائيات الرسوم والمالية
                'hr.dashboard.view',               // لوحة تحكم الموارد البشرية
                'accounting.dashboard.view',       // لوحة تحكم المحاسبة والمالية
                'inventory.dashboard.view',        // لوحة تحكم المخزون
                'crm.dashboard.view',              // لوحة تحكم وإحصائيات الـ CRM
                'crm.leads.statistics.view',       // إحصائيات طلبات الـ CRM
                'tasks.dashboard.view',            // لوحة تحكم المهام
            ],

            // ==========================================
            // 2. Events Management (إدارة الفعاليات)
            // ==========================================
            'academic_events' => [
                'academic.events.view',
                'academic.events.create',
                'academic.events.edit',
                'academic.events.delete',
            ],

            // ==========================================
            // 3. App Calendar (رزنامة التطبيق الأكاديمية)
            // ==========================================
            'academic_app_calendar' => [
                'academic.calender.view',
                'academic.calender.create',
                'academic.calender.edit',
                'academic.calender.delete',
                'academic.calendar.view',
                'academic.calendar.create',
                'academic.calendar.edit',
                'academic.calendar.delete',
            ],

            // ==========================================
            // 4. Personal Calendar (الرزنامة الشخصية)
            // ==========================================
            'academic_personal_calendar' => [
                'academic.personal-calendar.view',
                'academic.personal-calendar.create',
                'academic.personal-calendar.edit',
                'academic.personal-calendar.delete',
            ],

            // ==========================================
            // 5. Meetings Calendar (رزنامة الاجتماعات)
            // ==========================================
            'academic_meetings_calendar' => [
                'academic.meeting-calendar.view',
                'academic.meeting-calendar.create',
                'academic.meeting-calendar.edit',
                'academic.meeting-calendar.delete',
            ],

            // ==========================================
            // 6. Interviews Calendar (رزنامة مقابلات أولياء الأمور)
            // ==========================================
            'academic_interviews_calendar' => [
                'academic.interviews_calender.view',
                'academic.interviews_calender.create',
                'academic.interviews_calender.edit',
                'academic.interviews_calender.delete',
                'academic.interviews-calendar.view',
                'academic.interviews-calendar.create',
                'academic.interviews-calendar.edit',
                'academic.interviews-calendar.delete',
            ],
        ];

        $allNewPermissions = [];

        // Create or update all permissions
        foreach ($permissionsByModule as $group => $permissions) {
            foreach ($permissions as $permissionName) {
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
                $allNewPermissions[] = $permissionName;
            }
        }

        // Grant all permissions to super_admin and admin roles
        $superAdminRole = Role::findOrCreate('super_admin', $guardName);
        $adminRole = Role::findOrCreate('admin', $guardName);

        $permissionModels = Permission::where('guard_name', $guardName)
            ->whereIn('name', $allNewPermissions)
            ->get();

        $superAdminRole->givePermissionTo($permissionModels);
        $adminRole->givePermissionTo($permissionModels);

        // Also grant relevant teacher/supervisor roles personal calendar and events view permissions
        $teacherRole = Role::where('name', 'teacher')->where('guard_name', $guardName)->first();
        if ($teacherRole) {
            $teacherPerms = Permission::where('guard_name', $guardName)
                ->whereIn('name', [
                    'academic.events.view',
                    'academic.calender.view',
                    'academic.calendar.view',
                    'academic.personal-calendar.view',
                    'academic.personal-calendar.create',
                    'academic.personal-calendar.edit',
                    'academic.meeting-calendar.view',
                ])->get();
            $teacherRole->givePermissionTo($teacherPerms);
        }

        // Clear permission cache
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->command?->info('Schedules & Events and Dashboards permissions seeded successfully! (' . count($allNewPermissions) . ' permissions)');
    }
}
