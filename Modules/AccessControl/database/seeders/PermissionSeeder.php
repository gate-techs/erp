<?php

namespace Modules\AccessControl\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\AccessControl\Models\Permission;
use Modules\AccessControl\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $guardName = 'web';

        // All system screens and their permissions
        $permissionsByModule = [
            // ==========================================
            // 1. Nurseries Module (الحضانات)
            // ==========================================
            'nurseries' => [
                'nursery.view',
                'nursery.create',
                'nursery.edit',
                'nursery.delete',
                'nursery.toggle-status',
            ],
            'nursery_branches' => [
                'nursery.branches.view',
                'nursery.branches.create',
                'nursery.branches.edit',
                'nursery.branches.delete',
                'nursery.branches.toggle-status',
            ],

            // ==========================================
            // 2. Human Resources Module (الموارد البشرية)
            // ==========================================
            'hr_dashboard' => [
                'hr.dashboard.view',
            ],
            'hr_employees' => [
                'hr.employees.view',
                'hr.employees.create',
                'hr.employees.edit',
                'hr.employees.delete',
                'hr.employees.export',
            ],
            'hr_attendances' => [
                'hr.attendances.view',
                'hr.attendances.create',
                'hr.attendances.edit',
                'hr.attendances.delete',
                'hr.attendances.import',
                'hr.attendances.export',
            ],
            'hr_leaves' => [
                'hr.leaves.view',
                'hr.leaves.create',
                'hr.leaves.edit',
                'hr.leaves.delete',
                'hr.leaves.approve',
                'hr.leaves.reject',
            ],
            'hr_permissions' => [
                'hr.permissions.view',
                'hr.permissions.create',
                'hr.permissions.edit',
                'hr.permissions.delete',
                'hr.permissions.approve',
                'hr.permissions.reject',
            ],
            'hr_payrolls' => [
                'hr.payrolls.view',
                'hr.payrolls.create',
                'hr.payrolls.edit',
                'hr.payrolls.delete',
                'hr.payrolls.recalculate',
                'hr.payrolls.approve',
                'hr.payrolls.disburse',
                'hr.payrolls.cancel',
                'hr.payrolls.export-wps',
            ],
            'hr_loans' => [
                'hr.loans.view',
                'hr.loans.create',
                'hr.loans.edit',
                'hr.loans.delete',
                'hr.loans.approve',
                'hr.loans.reject',
                'hr.loans.disburse',
                'hr.loans.postpone',
                'hr.loans.pay-installment',
            ],
            'hr_rewards' => [
                'hr.rewards.view',
                'hr.rewards.create',
                'hr.rewards.edit',
                'hr.rewards.delete',
                'hr.rewards.approve',
                'hr.rewards.reject',
                'hr.rewards.pay',
            ],
            'hr_penalties' => [
                'hr.penalties.view',
                'hr.penalties.create',
                'hr.penalties.edit',
                'hr.penalties.delete',
                'hr.penalties.approve',
                'hr.penalties.deduct',
                'hr.penalties.cancel',
            ],
            'hr_workforce_plans' => [
                'hr.workforce-plans.view',
                'hr.workforce-plans.create',
                'hr.workforce-plans.edit',
                'hr.workforce-plans.delete',
                'hr.workforce-plans.submit',
                'hr.workforce-plans.approve',
                'hr.workforce-plans.activate',
                'hr.workforce-plans.close',
                'hr.workforce-plans.recalculate',
            ],
            'hr_job_requisitions' => [
                'hr.job-requisitions.view',
                'hr.job-requisitions.create',
                'hr.job-requisitions.edit',
                'hr.job-requisitions.delete',
                'hr.job-requisitions.submit',
                'hr.job-requisitions.approve',
                'hr.job-requisitions.reject',
                'hr.job-requisitions.start-recruitment',
                'hr.job-requisitions.cancel',
            ],
            'hr_job_openings' => [
                'hr.job-openings.view',
                'hr.job-openings.create',
                'hr.job-openings.edit',
                'hr.job-openings.delete',
                'hr.job-openings.publish',
                'hr.job-openings.close',
            ],
            'hr_job_applications' => [
                'hr.job-applications.view',
                'hr.job-applications.create',
                'hr.job-applications.edit',
                'hr.job-applications.delete',
                'hr.job-applications.update-stage',
                'hr.job-applications.reject',
                'hr.job-applications.reopen',
                'hr.job-applications.download-resume',
            ],
            'hr_interviews' => [
                'hr.interviews.view',
                'hr.interviews.create',
                'hr.interviews.edit',
                'hr.interviews.delete',
                'hr.interviews.evaluate',
                'hr.interviews.cancel',
            ],
            'hr_job_offers' => [
                'hr.job-offers.view',
                'hr.job-offers.create',
                'hr.job-offers.edit',
                'hr.job-offers.delete',
                'hr.job-offers.submit',
                'hr.job-offers.approve',
                'hr.job-offers.send',
                'hr.job-offers.accept',
                'hr.job-offers.decline',
                'hr.job-offers.cancel',
            ],
            'hr_onboardings' => [
                'hr.onboardings.view',
                'hr.onboardings.create',
                'hr.onboardings.edit',
                'hr.onboardings.delete',
                'hr.onboardings.tasks',
                'hr.onboardings.convert',
            ],
            'hr_reports' => [
                'hr.reports.view',
                'hr.reports.attendance.view',
                'hr.reports.attendance.export',
                'hr.reports.attendance.print',
                'hr.reports.leaves.view',
                'hr.reports.payrolls.view',
                'hr.reports.employees.view',
            ],

            // ==========================================
            // 3. HR Settings (إعدادات الموارد البشرية)
            // ==========================================
            'hr_settings_administrations' => [
                'hr.settings.administrations.view',
                'hr.settings.administrations.create',
                'hr.settings.administrations.edit',
                'hr.settings.administrations.delete',
            ],
            'hr_settings_departments' => [
                'hr.settings.departments.view',
                'hr.settings.departments.create',
                'hr.settings.departments.edit',
                'hr.settings.departments.delete',
            ],
            'hr_settings_job_titles' => [
                'hr.settings.job-titles.view',
                'hr.settings.job-titles.create',
                'hr.settings.job-titles.edit',
                'hr.settings.job-titles.delete',
            ],
            'hr_settings_allowance_types' => [
                'hr.settings.allowance-types.view',
                'hr.settings.allowance-types.create',
                'hr.settings.allowance-types.edit',
                'hr.settings.allowance-types.delete',
            ],
            'hr_settings_document_types' => [
                'hr.settings.document-types.view',
                'hr.settings.document-types.create',
                'hr.settings.document-types.edit',
                'hr.settings.document-types.delete',
            ],
            'hr_settings_reward_types' => [
                'hr.settings.reward-types.view',
                'hr.settings.reward-types.create',
                'hr.settings.reward-types.edit',
                'hr.settings.reward-types.delete',
            ],
            'hr_settings_penalty_types' => [
                'hr.settings.penalty-types.view',
                'hr.settings.penalty-types.create',
                'hr.settings.penalty-types.edit',
                'hr.settings.penalty-types.delete',
            ],
            'hr_settings_shifts' => [
                'hr.settings.shifts.view',
                'hr.settings.shifts.create',
                'hr.settings.shifts.edit',
                'hr.settings.shifts.delete',
            ],
            'hr_settings_work_schedules' => [
                'hr.settings.work-schedules.view',
                'hr.settings.work-schedules.create',
                'hr.settings.work-schedules.edit',
                'hr.settings.work-schedules.delete',
            ],
            'hr_settings_official_holidays' => [
                'hr.settings.official-holidays.view',
                'hr.settings.official-holidays.create',
                'hr.settings.official-holidays.edit',
                'hr.settings.official-holidays.delete',
            ],
            'hr_settings_policies' => [
                'hr.settings.policies.view',
                'hr.settings.policies.edit',
                'hr.settings.policies.reset',
            ],

            // ==========================================
            // 4. System Settings & Access Control (إعدادات النظام وإدارة الوصول)
            // ==========================================
            'users' => [
                'users.view',
                'users.create',
                'users.edit',
                'users.delete',
                'users.toggle-status',
            ],
            'profile' => [
                'profile.view',
                'profile.edit',
            ],
            'settings_company' => [
                'settings.company.view',
                'settings.company.edit',
            ],
            'settings_roles' => [
                'settings.roles.view',
                'settings.roles.create',
                'settings.roles.edit',
                'settings.roles.delete',
                'settings.roles.assign-permissions',
            ],
            'settings_translations' => [
                'settings.translations.view',
                'settings.translations.create',
                'settings.translations.edit',
                'settings.translations.delete',
            ],
            'settings_print_templates' => [
                'settings.print-templates.view',
                'settings.print-templates.create',
                'settings.print-templates.edit',
                'settings.print-templates.delete',
            ],
            'settings_general_governments' => [
                'settings.general.governments.view',
                'settings.general.governments.create',
                'settings.general.governments.edit',
                'settings.general.governments.delete',
            ],
            'settings_general_nationalities' => [
                'settings.general.nationalities.view',
                'settings.general.nationalities.create',
                'settings.general.nationalities.edit',
                'settings.general.nationalities.delete',
            ],
            'settings_general_religions' => [
                'settings.general.religions.view',
                'settings.general.religions.create',
                'settings.general.religions.edit',
                'settings.general.religions.delete',
            ],
            'settings_general_marital_statuses' => [
                'settings.general.marital-statuses.view',
                'settings.general.marital-statuses.create',
                'settings.general.marital-statuses.edit',
                'settings.general.marital-statuses.delete',
            ],
            'settings_general_banks' => [
                'settings.general.banks.view',
                'settings.general.banks.create',
                'settings.general.banks.edit',
                'settings.general.banks.delete',
            ],
            'settings_general_bank_branches' => [
                'settings.general.bank-branches.view',
                'settings.general.bank-branches.create',
                'settings.general.bank-branches.edit',
                'settings.general.bank-branches.delete',
            ],
            // ==========================================
            // 5. Audit & Activity Logs (سجل الأنشطة والتدقيق)
            // ==========================================
            'audit_logs' => [
                'audit.view',
                'audit.export',
            ],

            // ==========================================
            // 6. Accounting Module (المحاسبة والمالية)
            // ==========================================
            'accounting_entities' => [
                'accounting.entities.view',
                'accounting.entities.create',
                'accounting.entities.edit',
                'accounting.entities.delete',
            ],
        ];

        // Safely update or create each permission
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
            }
        }

        // Ensure default roles exist using updateOrCreate / findOrCreate
        $superAdminRole = Role::findOrCreate('super_admin', $guardName);
        $adminRole = Role::findOrCreate('admin', $guardName);
        $employeeRole = Role::findOrCreate('employee', $guardName);
        $guardianRole = Role::findOrCreate('guardian', $guardName);
        $studentRole = Role::findOrCreate('student', $guardName);

        // Super Admin gets all permissions
        $allPermissions = Permission::where('guard_name', $guardName)->get();
        $superAdminRole->syncPermissions($allPermissions);

        // Admin role also gets all permissions initially
        $adminRole->syncPermissions($allPermissions);

        // All roles can view and edit their own profile
        $profilePermissions = Permission::where('guard_name', $guardName)->whereIn('name', ['profile.view', 'profile.edit'])->get();
        $employeeRole->givePermissionTo($profilePermissions);
        $guardianRole->givePermissionTo($profilePermissions);
        $studentRole->givePermissionTo($profilePermissions);

        // Assign super_admin role to default admin user if exists
        $adminUser = User::where('email', 'admin@gatetech.com')->first();
        if ($adminUser && method_exists($adminUser, 'assignRole')) {
            $adminUser->assignRole($superAdminRole);
        }

        // Clear permission cache
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
