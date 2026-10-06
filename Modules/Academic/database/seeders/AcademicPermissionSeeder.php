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
