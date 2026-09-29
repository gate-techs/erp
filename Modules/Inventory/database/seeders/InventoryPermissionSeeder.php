<?php

namespace Modules\Inventory\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class InventoryPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $guardName = 'web';

        $screens = [
            // 1. الأصناف والمنتجات
            'inventory_items' => [
                'inventory.items.view'          => 'عرض قائمة الأصناف والمنتجات',
                'inventory.items.create'        => 'إضافة صنف جديد',
                'inventory.items.edit'          => 'تعديل بيانات الصنف',
                'inventory.items.delete'        => 'حذف الصنف',
                'inventory.items.toggle-status' => 'تفعيل/تعطيل الصنف',
            ],

            // 2. تصنيفات الأصناف
            'inventory_categories' => [
                'inventory.categories.view'          => 'عرض تصنيفات الأصناف',
                'inventory.categories.create'        => 'إضافة تصنيف أصناف',
                'inventory.categories.edit'          => 'تعديل تصنيف أصناف',
                'inventory.categories.delete'        => 'حذف تصنيف أصناف',
                'inventory.categories.toggle-status' => 'تفعيل/تعطيل تصنيف أصناف',
            ],

            // 3. وحدات القياس
            'inventory_units' => [
                'inventory.units.view'          => 'عرض وحدات القياس',
                'inventory.units.create'        => 'إضافة وحدة قياس',
                'inventory.units.edit'          => 'تعديل وحدة قياس',
                'inventory.units.delete'        => 'حذف وحدة قياس',
                'inventory.units.toggle-status' => 'تفعيل/تعطيل وحدة القياس',
            ],

            // 4. المستودعات والمخازن
            'inventory_warehouses' => [
                'inventory.warehouses.view'          => 'عرض قائمة المستودعات',
                'inventory.warehouses.create'        => 'إضافة مستودع جديد',
                'inventory.warehouses.edit'          => 'تعديل مستودع',
                'inventory.warehouses.delete'        => 'حذف مستودع',
                'inventory.warehouses.toggle-status' => 'تفعيل/تعطيل المستودع',
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

        // Assign to Super Admin and Admin roles automatically
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
