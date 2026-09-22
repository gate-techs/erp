<?php

namespace Modules\AuditLog\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AuditLogPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'audit.view'   => 'عرض سجل الأنشطة والتدقيق',
            'audit.export' => 'تصدير سجل الأنشطة والتدقيق',
        ];

        foreach ($permissions as $name => $label) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        // Assign to Super Admin and Admin roles
        $adminRoles = Role::whereIn('name', ['Super Admin', 'super-admin', 'Admin', 'admin'])->get();
        foreach ($adminRoles as $role) {
            $role->givePermissionTo(array_keys($permissions));
        }
    }
}
