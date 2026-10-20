<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/** Single entry point for all application roles and permissions. */
class PermissionDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            \Modules\AccessControl\Database\Seeders\RoleSeeder::class,
            \Modules\AccessControl\Database\Seeders\PermissionSeeder::class,
            \Modules\AccessControl\Database\Seeders\StudentAndFeesPermissionSeeder::class,
            \Modules\AccessControl\Database\Seeders\SchedulesAndDashboardsPermissionSeeder::class,
            \Modules\Academic\Database\Seeders\AcademicPermissionSeeder::class,
            \Modules\Accounting\Database\Seeders\AccountingPermissionSeeder::class,
            \Modules\AuditLog\Database\Seeders\AuditLogPermissionSeeder::class,
            \Modules\CRM\Database\Seeders\CRMPermissionSeeder::class,
            \Modules\Inventory\Database\Seeders\InventoryPermissionSeeder::class,
            \Modules\Nursery\Database\Seeders\NurseryPermissionSeeder::class,
            \Modules\Tasks\Database\Seeders\TasksPermissionSeeder::class,
        ]);
    }
}
