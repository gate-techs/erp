<?php

namespace Modules\Tasks\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AccessControl\Models\Permission;
use Modules\AccessControl\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class TasksPermissionSeeder extends Seeder
{
    //-----------------------------------------
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $guardName = 'web';

        $permissions = [
            'tasks.monitoring.view',
            'tasks.task-groups.view',
            'tasks.task-groups.create',
            'tasks.task-groups.edit',
            'tasks.task-groups.delete',
            'tasks.task-groups.toggle-status',
            'tasks.view',
            'tasks.view_all',
            'tasks.create',
            'tasks.edit',
            'tasks.delete',
            'tasks.toggle-status',
        ];

        foreach ($permissions as $permissionName) {
            Permission::findOrCreate($permissionName, $guardName);
        }

        // 1. Super Admin & Admin Roles
        $superAdminRole = Role::findOrCreate('super_admin', $guardName);
        $adminRole      = Role::findOrCreate('admin', $guardName);
        $taskManagerRole= Role::findOrCreate('TaskManager', $guardName);
        $deptManagerRole= Role::findOrCreate('DeptManager', $guardName);
        $employeeRole   = Role::findOrCreate('employee', $guardName);

        $allTasksPermissions = Permission::where('guard_name', $guardName)
            ->whereIn('name', $permissions)
            ->get();

        $superAdminRole->givePermissionTo($allTasksPermissions);
        $adminRole->givePermissionTo($allTasksPermissions);
        $taskManagerRole->givePermissionTo($allTasksPermissions);
        $deptManagerRole->givePermissionTo($allTasksPermissions);

        // Employee permissions (view, update status, edit notes)
        $employeePermissions = Permission::where('guard_name', $guardName)
            ->whereIn('name', ['tasks.view', 'tasks.toggle-status', 'tasks.edit'])
            ->get();
        $employeeRole->givePermissionTo($employeePermissions);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
