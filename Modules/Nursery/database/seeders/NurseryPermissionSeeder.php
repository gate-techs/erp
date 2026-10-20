<?php

namespace Modules\Nursery\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class NurseryPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds for Nursery and Branch action permissions.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $guardName = 'web';

        $screens = [
            // 1. الحضانات (Nurseries)
            'nurseries' => [
                'nursery.view'          => 'عرض قائمة وتفاصيل الحضانات',
                'nursery.create'        => 'إضافة حضانة جديدة',
                'nursery.edit'          => 'تعديل بيانات الحضانة',
                'nursery.delete'        => 'حذف الحضانة',
                'nursery.toggle-status' => 'تغيير حالة الحضانة (تفعيل/تعطيل)',
            ],

            // 2. فروع الحضانات (Nursery Branches)
            'nursery_branches' => [
                'nursery.branches.view'          => 'عرض قائمة وتفاصيل الفروع',
                'nursery.branches.create'        => 'إضافة فرع جديد',
                'nursery.branches.edit'          => 'تعديل بيانات الفرع',
                'nursery.branches.delete'        => 'حذف الفرع',
                'nursery.branches.toggle-status' => 'تغيير حالة الفرع (تفعيل/تعطيل)',
            ],
        ];

        $allPermissionNames = [];

        // Insert or update all permissions safely
        foreach ($screens as $screenKey => $permissions) {
            foreach ($permissions as $permName => $permLabel) {
                Permission::firstOrCreate(
                    [
                        'name' => $permName,
                        'guard_name' => $guardName,
                    ]
                );
                $allPermissionNames[] = $permName;
            }
        }

        // Assign all permissions to Super Admin and Admin roles automatically
        $adminRoles = Role::whereIn('name', ['Super Admin', 'super-admin', 'super_admin', 'Admin', 'admin'])
            ->where('guard_name', $guardName)
            ->get();

        foreach ($adminRoles as $role) {
            $role->givePermissionTo($allPermissionNames);
        }

        // Clear permission cache
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
