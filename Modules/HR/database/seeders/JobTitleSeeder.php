<?php

namespace Modules\HR\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\HR\Enums\JobTitleStatus;
use Modules\HR\Models\Department;
use Modules\HR\Models\JobTitle;

class JobTitleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $careDept   = Department::where('code', 'EDU-CARE')->first();
        $actDept    = Department::where('code', 'EDU-ACT')->first();
        $medDept    = Department::where('code', 'MED-NURSE')->first();
        $hrDept     = Department::where('code', 'ADM-HR')->first();
        $finDept    = Department::where('code', 'ADM-FIN')->first();

        $titles = [
            [
                'code'          => 'JT-MGR-CARE',
                'department_id' => $careDept?->id,
                'status'        => JobTitleStatus::ACTIVE->value,
                'ar'            => ['name' => 'مشرفة رعاية أطفال', 'description' => 'الإشراف على رعاية الأطفال واحتياجاتهم اليومية'],
                'en'            => ['name' => 'Child Care Supervisor', 'description' => 'Supervises daily childcare and essentials'],
            ],
            [
                'code'          => 'JT-TEACHER',
                'department_id' => $actDept?->id,
                'status'        => JobTitleStatus::ACTIVE->value,
                'ar'            => ['name' => 'معلمة رياض أطفال', 'description' => 'تقديم الأنشطة التعليمية وتنمية المهارات'],
                'en'            => ['name' => 'Kindergarten Teacher', 'description' => 'Conducting educational activities and skills development'],
            ],
            [
                'code'          => 'JT-NURSE',
                'department_id' => $medDept?->id,
                'status'        => JobTitleStatus::ACTIVE->value,
                'ar'            => ['name' => 'ممرضة أطفال', 'description' => 'متابعة الحالة الصحية وتطبيق الإسعافات الأولية'],
                'en'            => ['name' => 'Pediatric Nurse', 'description' => 'Health checkups and first aid management'],
            ],
            [
                'code'          => 'JT-HR-OFFICER',
                'department_id' => $hrDept?->id,
                'status'        => JobTitleStatus::ACTIVE->value,
                'ar'            => ['name' => 'مسؤول شؤون موظفين', 'description' => 'متابعة سجلات الموظفين والوثائق والإجازات'],
                'en'            => ['name' => 'HR Specialist', 'description' => 'Personnel records, documents and leaves tracking'],
            ],
            [
                'code'          => 'JT-ACCOUNTANT',
                'department_id' => $finDept?->id,
                'status'        => JobTitleStatus::ACTIVE->value,
                'ar'            => ['name' => 'محاسب مالي', 'description' => 'إدارة الحسابات اليومية والاشتراكات وسندات القبض'],
                'en'            => ['name' => 'Accountant', 'description' => 'Handling daily accounts, tuition fees and vouchers'],
            ],
            [
                'code'          => 'JT-GEN-ADMIN',
                'department_id' => null,
                'status'        => JobTitleStatus::ACTIVE->value,
                'ar'            => ['name' => 'منسق إداري عام', 'description' => 'تنسيق العمليات الإدارية اليومية واستقبال أولياء الأمور'],
                'en'            => ['name' => 'Administrative Coordinator', 'description' => 'Operations coordination and parent receptions'],
            ],
            [
                'code'          => 'JT-OLD-ROLE',
                'department_id' => null,
                'status'        => JobTitleStatus::INACTIVE->value,
                'ar'            => ['name' => 'مساعد دعم لوجستي', 'description' => 'مسمى وظيفي سابق موقوف حالياً'],
                'en'            => ['name' => 'Logistics Support Assistant', 'description' => 'Archived former position'],
            ],
        ];

        foreach ($titles as $titleData) {
            JobTitle::updateOrCreate(
                ['code' => $titleData['code']],
                $titleData
            );
        }
    }
}
