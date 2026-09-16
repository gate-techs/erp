<?php

namespace Modules\HR\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\HR\Models\Department;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. الإدارة التعليمية والتربوية
        $edu = Department::updateOrCreate(
            ['code' => 'EDU'],
            [
                'parent_id' => null,
                'status'    => 'active',
                'ar'        => ['name' => 'الإدارة التعليمية والتربوية'],
                'en'        => ['name' => 'Educational & Care Administration'],
            ]
        );

        Department::updateOrCreate(
            ['code' => 'EDU-CARE'],
            [
                'parent_id' => $edu->id,
                'status'    => 'active',
                'ar'        => ['name' => 'قسم رعاية الأطفال'],
                'en'        => ['name' => 'Child Care Department'],
            ]
        );

        Department::updateOrCreate(
            ['code' => 'EDU-ACT'],
            [
                'parent_id' => $edu->id,
                'status'    => 'active',
                'ar'        => ['name' => 'قسم الأنشطة والتعليم'],
                'en'        => ['name' => 'Activities & Learning Department'],
            ]
        );

        // 2. الإدارة الطبية والصحية
        $med = Department::updateOrCreate(
            ['code' => 'MED'],
            [
                'parent_id' => null,
                'status'    => 'active',
                'ar'        => ['name' => 'الإدارة الطبية والصحية'],
                'en'        => ['name' => 'Medical & Health Administration'],
            ]
        );

        Department::updateOrCreate(
            ['code' => 'MED-NURSE'],
            [
                'parent_id' => $med->id,
                'status'    => 'active',
                'ar'        => ['name' => 'قسم التمريض والرعاية'],
                'en'        => ['name' => 'Nursing & Healthcare Department'],
            ]
        );

        Department::updateOrCreate(
            ['code' => 'MED-NUTR'],
            [
                'parent_id' => $med->id,
                'status'    => 'active',
                'ar'        => ['name' => 'قسم التغذية'],
                'en'        => ['name' => 'Nutrition & Meals Department'],
            ]
        );

        // 3. إدارة الموارد البشرية
        $hr = Department::updateOrCreate(
            ['code' => 'HR'],
            [
                'parent_id' => null,
                'status'    => 'active',
                'ar'        => ['name' => 'إدارة الموارد البشرية'],
                'en'        => ['name' => 'Human Resources Administration'],
            ]
        );

        Department::updateOrCreate(
            ['code' => 'HR-PERS'],
            [
                'parent_id' => $hr->id,
                'status'    => 'active',
                'ar'        => ['name' => 'قسم شؤون الموظفين'],
                'en'        => ['name' => 'Personnel Affairs Department'],
            ]
        );

        // 4. الإدارة المالية والإدارية
        $fin = Department::updateOrCreate(
            ['code' => 'FIN'],
            [
                'parent_id' => null,
                'status'    => 'active',
                'ar'        => ['name' => 'الإدارة المالية والإدارية'],
                'en'        => ['name' => 'Financial & Administrative Administration'],
            ]
        );

        Department::updateOrCreate(
            ['code' => 'FIN-ACC'],
            [
                'parent_id' => $fin->id,
                'status'    => 'active',
                'ar'        => ['name' => 'قسم الحسابات والرسوم'],
                'en'        => ['name' => 'Accounts & Fees Department'],
            ]
        );

        Department::updateOrCreate(
            ['code' => 'FIN-SERV'],
            [
                'parent_id' => $fin->id,
                'status'    => 'active',
                'ar'        => ['name' => 'قسم الخدمات والصيانة'],
                'en'        => ['name' => 'Services & Maintenance Department'],
            ]
        );
    }
}
