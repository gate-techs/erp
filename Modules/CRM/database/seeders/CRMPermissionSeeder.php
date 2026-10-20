<?php

namespace Modules\CRM\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class CRMPermissionSeeder extends Seeder
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
            // 0. إحصائيات الطلبات (Dashboard)
            'crm_dashboard' => [
                'crm.dashboard.view' => 'عرض إحصائيات ولوحة تحكم علاقات العملاء',
            ],

            // 1. العملاء المحتملين (Leads)
            'crm_leads' => [
                'crm.leads.view'   => 'عرض قائمة العملاء المحتملين',
                'crm.leads.create' => 'إضافة عميل محتمل جديد',
                'crm.leads.edit'   => 'تعديل بيانات العميل المحتمل',
                'crm.leads.delete' => 'حذف عميل محتمل',
                'crm.leads.export' => 'تصدير العملاء المحتملين',
            ],

            // 2. طلبات الخدمة والشكاوى (Service Requests)
            'crm_service_requests' => [
                'crm.service-requests.view'   => 'عرض واستعراض طلبات الخدمة والشكاوى',
                'crm.service-requests.create' => 'إضافة طلب خدمة / شكوى جديدة',
                'crm.service-requests.edit'   => 'تعديل طلب الخدمة وتحديث الحالة',
                'crm.service-requests.delete' => 'حذف طلب الخدمة',
                'crm.service-requests.export' => 'تصدير طلبات الخدمة والشكاوى',
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
