<?php

namespace Modules\Tasks\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\HR\Models\Department;
use Modules\Tasks\Models\TaskGroup;

class TaskGroupSeeder extends Seeder
{
    //-----------------------------------------
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::first();
        $adminId = $adminUser?->id;

        $departments = Department::all();
        $deptIds = $departments->pluck('id')->toArray();

        $groups = [
            [
                'ar' => [
                    'name'        => 'تطوير البوابة الإلكترونية والنظام الإداري',
                    'description' => 'مجموعة العمل الخاصة بتطوير واجهات وتطبيقات الـ ERP وربط البوابة مع الخدمات الخارجية.',
                ],
                'en' => [
                    'name'        => 'Portal & ERP System Development',
                    'description' => 'Task group dedicated to ERP enhancements, portal modules, and external API integrations.',
                ],
                'department_id' => !empty($deptIds) ? $deptIds[0] : null,
                'start_date'    => now()->startOfMonth()->format('Y-m-d'),
                'end_date'      => now()->addMonths(3)->endOfMonth()->format('Y-m-d'),
                'is_active'     => true,
                'created_by'    => $adminId,
            ],
            [
                'ar' => [
                    'name'        => 'حملة التسويق والتسجيل للعام الجديد',
                    'description' => 'إدارة الحملات الإعلانية ومتابعة استفسارات أولياء الأمور والعملاء المحتملين.',
                ],
                'en' => [
                    'name'        => 'New Academic Year Marketing Campaign',
                    'description' => 'Managing social media ads, lead follow-ups, and student enrollment processes.',
                ],
                'department_id' => count($deptIds) > 1 ? $deptIds[1] : (!empty($deptIds) ? $deptIds[0] : null),
                'start_date'    => now()->subDays(10)->format('Y-m-d'),
                'end_date'      => now()->addMonths(1)->format('Y-m-d'),
                'is_active'     => true,
                'created_by'    => $adminId,
            ],
            [
                'ar' => [
                    'name'        => 'صيانة وتجهيز الفروع والمرافق',
                    'description' => 'متابعة أعمال الفحص الدوري للأجهزة ومعدات الأمان والسلامة في الفروع.',
                ],
                'en' => [
                    'name'        => 'Facilities & Branches Maintenance',
                    'description' => 'Overseeing regular safety inspections, hardware upgrades, and branch preparations.',
                ],
                'department_id' => count($deptIds) > 2 ? $deptIds[2] : (!empty($deptIds) ? $deptIds[0] : null),
                'start_date'    => now()->format('Y-m-d'),
                'end_date'      => now()->addDays(45)->format('Y-m-d'),
                'is_active'     => true,
                'created_by'    => $adminId,
            ],
            [
                'ar' => [
                    'name'        => 'برنامج تدريب الكادر الأكاديمي والإداري',
                    'description' => 'تنظيم ورش العمل والدورات التدريبية للموظفين الجدد لتطوير المهارات.',
                ],
                'en' => [
                    'name'        => 'Staff Training & Professional Development',
                    'description' => 'Organizing onboarding workshops and skills development sessions for staff members.',
                ],
                'department_id' => !empty($deptIds) ? $deptIds[0] : null,
                'start_date'    => now()->addDays(5)->format('Y-m-d'),
                'end_date'      => now()->addMonths(2)->format('Y-m-d'),
                'is_active'     => true,
                'created_by'    => $adminId,
            ],
            [
                'ar' => [
                    'name'        => 'إقفال ومراجعة الميزانية المالية السنوية',
                    'description' => 'مراجعة التقارير المحاسبية السنوية ومطابقة الحسابات البنكية وإعداد الموازنة التقديرية.',
                ],
                'en' => [
                    'name'        => 'Annual Financial Review & Budget Planning',
                    'description' => 'Reviewing end-of-year accounting reports, bank reconciliations, and next year forecasting.',
                ],
                'department_id' => count($deptIds) > 1 ? $deptIds[1] : (!empty($deptIds) ? $deptIds[0] : null),
                'start_date'    => now()->startOfYear()->format('Y-m-d'),
                'end_date'      => now()->endOfYear()->format('Y-m-d'),
                'is_active'     => false,
                'created_by'    => $adminId,
            ],
        ];

        foreach ($groups as $groupData) {
            TaskGroup::create($groupData);
        }
    }
}
