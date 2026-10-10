<?php

namespace Modules\AccessControl\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AccessControl\Models\Permission;
use Modules\AccessControl\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class StudentAndFeesPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            // Fees & Packages
            'academic.fee-types.view',
            'academic.fee-types.create',
            'academic.fee-types.edit',
            'academic.fee-types.delete',
            'academic.fee-types.toggle-status',
            'academic.fees.view',
            'academic.fees.create',
            'academic.fees.edit',
            'academic.fees.delete',
            'academic.fees.toggle-status',
            'academic.student-fees.view',
            'academic.student-fees.create',
            'academic.student-fees.payments.create',
            'nursery.packages.view',
            'nursery.packages.create',
            'nursery.packages.edit',
            'nursery.packages.delete',
            'nursery.packages.toggle-status',

            // Student Services
            'academic.students.view',
            'academic.students.create',
            'academic.students.edit',
            'academic.students.delete',
            'academic.students.toggle-status',
            'academic.guardians.view',
            'academic.guardians.create',
            'academic.guardians.edit',
            'academic.guardians.delete',
            'academic.guardians.toggle-status',
            'academic.teachers.view',
            'academic.teachers.create',
            'academic.teachers.edit',
            'academic.teachers.delete',
            'academic.teachers.toggle-status',
            'academic.attendance.view',
            'academic.attendance.create',
            'academic.attendance.edit',
            'academic.attendance.delete',
            'academic.galleries.view',
            'academic.galleries.create',
            'academic.galleries.edit',
            'academic.galleries.delete',

            // Student Affairs
            'academic.daily-reports.view',
            'academic.daily-reports.create',
            'academic.daily-reports.edit',
            'academic.daily-reports.delete',
            'academic.incident-reports.view',
            'academic.incident-reports.create',
            'academic.incident-reports.edit',
            'academic.incident-reports.delete',
            'academic.medication-requests.view',
            'academic.medication-requests.create',
            'academic.medication-requests.edit',
            'academic.medication-requests.delete',
            'academic.medication-requests.done',
            'academic.withdrawal-requests.view',
            'academic.withdrawal-requests.create',
            'academic.withdrawal-requests.edit',
            'academic.withdrawal-requests.delete',
            'academic.withdrawal-requests.approve',
            'academic.withdrawal-requests.reject',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $roles = [
            Role::findOrCreate('super_admin', 'web'),
            Role::findOrCreate('admin', 'web'),
        ];

        $permissionModels = Permission::where('guard_name', 'web')
            ->whereIn('name', $permissions)
            ->get();

        foreach ($roles as $role) {
            $role->givePermissionTo($permissionModels);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
