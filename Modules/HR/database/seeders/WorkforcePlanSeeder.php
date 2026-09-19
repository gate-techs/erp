<?php

namespace Modules\HR\Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Modules\HR\Enums\EmploymentType;
use Modules\HR\Enums\PlanItemPriority;
use Modules\HR\Enums\PlanQuarter;
use Modules\HR\Enums\WorkforcePlanStatus;
use Modules\HR\Models\Department;
use Modules\HR\Models\JobTitle;
use Modules\HR\Models\WorkforcePlan;
use Modules\HR\Models\WorkforcePlanItem;
use Modules\Nursery\Models\NurseryBranch;

class WorkforcePlanSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first();
        $adminId = $admin ? $admin->id : 1;

        $branches    = NurseryBranch::all();
        $departments = Department::all();
        $jobTitles   = JobTitle::all();

        if ($departments->isEmpty() || $jobTitles->isEmpty()) {
            return;
        }

        $branch1 = $branches->first()?->id;
        $branch2 = $branches->count() > 1 ? $branches->skip(1)->first()?->id : $branch1;

        $dept1 = $departments->get(0)?->id; // التعليمية
        $dept2 = $departments->get(1)?->id ?? $dept1; // رعاية الأطفال
        $dept3 = $departments->get(3)?->id ?? $dept1; // الطبية

        $jobTeacher    = $jobTitles->get(1)?->id ?? $jobTitles->first()->id; // معلمة
        $jobSupervisor = $jobTitles->get(0)?->id ?? $jobTitles->first()->id; // مشرفة
        $jobNurse      = $jobTitles->get(2)?->id ?? $jobTitles->first()->id; // ممرضة
        $jobHR         = $jobTitles->get(3)?->id ?? $jobTitles->first()->id; // شؤون موظفين

        // Plan 1: Active Annual Plan 2026
        $plan1 = WorkforcePlan::updateOrCreate(
            ['plan_code' => 'WFP-2026-001'],
            [
                'title'       => 'خطة القوى العاملة السنوية 2026 - التوسع والتشغيل',
                'year'        => 2026,
                'start_date'  => Carbon::create(2026, 1, 1),
                'end_date'    => Carbon::create(2026, 12, 31),
                'status'      => WorkforcePlanStatus::ACTIVE,
                'notes'       => 'خطة التوسع السنوية المعتمدة لزيادة الكادر التعليمي والإشرافي وتغطية الفروع الجديدة.',
                'created_by'  => $adminId,
                'approved_by' => $adminId,
                'approved_at' => Carbon::create(2026, 1, 5, 10, 0, 0),
            ]
        );

        $itemsPlan1 = [
            [
                'workforce_plan_id'        => $plan1->id,
                'department_id'            => $dept1,
                'branch_id'                => $branch1,
                'job_title_id'             => $jobTeacher,
                'employment_type'          => EmploymentType::FULL_TIME,
                'current_headcount'        => 4,
                'target_headcount'         => 8,
                'vacancies_count'          => 4,
                'fulfilled_count'          => 2,
                'estimated_monthly_salary' => 650.000,
                'priority'                 => PlanItemPriority::HIGH,
                'target_quarter'           => PlanQuarter::Q1,
                'justification'            => 'توسع في الشعب الدراسية لمرحلة الروضة الأولى والثانية.',
            ],
            [
                'workforce_plan_id'        => $plan1->id,
                'department_id'            => $dept2,
                'branch_id'                => $branch2,
                'job_title_id'             => $jobSupervisor,
                'employment_type'          => EmploymentType::FULL_TIME,
                'current_headcount'        => 1,
                'target_headcount'         => 3,
                'vacancies_count'          => 2,
                'fulfilled_count'          => 1,
                'estimated_monthly_salary' => 850.000,
                'priority'                 => PlanItemPriority::MEDIUM,
                'target_quarter'           => PlanQuarter::Q2,
                'justification'            => 'إشراف تربوي وتوجيه معلمات الحضانات.',
            ],
            [
                'workforce_plan_id'        => $plan1->id,
                'department_id'            => $dept3,
                'branch_id'                => $branch1,
                'job_title_id'             => $jobNurse,
                'employment_type'          => EmploymentType::FULL_TIME,
                'current_headcount'        => 1,
                'target_headcount'         => 2,
                'vacancies_count'          => 1,
                'fulfilled_count'          => 0,
                'estimated_monthly_salary' => 550.000,
                'priority'                 => PlanItemPriority::CRITICAL,
                'target_quarter'           => PlanQuarter::Q1,
                'justification'            => 'تغطية عيادة الفرع المستحدث وفق معايير وزارة الصحة.',
            ],
            [
                'workforce_plan_id'        => $plan1->id,
                'department_id'            => $dept1,
                'branch_id'                => null, // كل الفروع
                'job_title_id'             => $jobHR,
                'employment_type'          => EmploymentType::FULL_TIME,
                'current_headcount'        => 1,
                'target_headcount'         => 2,
                'vacancies_count'          => 1,
                'fulfilled_count'          => 0,
                'estimated_monthly_salary' => 700.000,
                'priority'                 => PlanItemPriority::MEDIUM,
                'target_quarter'           => PlanQuarter::Q3,
                'justification'            => 'دعم إدارة الموارد البشرية مع ازدياد أعداد الموظفين.',
            ],
        ];

        foreach ($itemsPlan1 as $itemData) {
            $item = WorkforcePlanItem::create($itemData);
            $item->recalculate();
        }
        $plan1->recalculateTotals();

        // Plan 2: Approved Q3/Q4 Plan
        $plan2 = WorkforcePlan::updateOrCreate(
            ['plan_code' => 'WFP-2026-002'],
            [
                'title'       => 'خطة استقطاب الكوادر التخصصية للربعين الثالث والرابع 2026',
                'year'        => 2026,
                'start_date'  => Carbon::create(2026, 7, 1),
                'end_date'    => Carbon::create(2026, 12, 31),
                'status'      => WorkforcePlanStatus::APPROVED,
                'notes'       => 'خطة الدعم الإداري والمالي وتدعيم فرق الأنشطة المدرسية.',
                'created_by'  => $adminId,
                'approved_by' => $adminId,
                'approved_at' => Carbon::create(2026, 6, 20, 11, 30, 0),
            ]
        );

        $itemsPlan2 = [
            [
                'workforce_plan_id'        => $plan2->id,
                'department_id'            => $dept1,
                'branch_id'                => $branch2,
                'job_title_id'             => $jobTeacher,
                'employment_type'          => EmploymentType::CONTRACT,
                'current_headcount'        => 2,
                'target_headcount'         => 4,
                'vacancies_count'          => 2,
                'fulfilled_count'          => 0,
                'estimated_monthly_salary' => 600.000,
                'priority'                 => PlanItemPriority::HIGH,
                'target_quarter'           => PlanQuarter::Q3,
                'justification'            => 'عقود مؤقتة لتغطية الأنشطة الصيفية وبداية العام الدراسي الجديد.',
            ],
            [
                'workforce_plan_id'        => $plan2->id,
                'department_id'            => $dept2,
                'branch_id'                => $branch1,
                'job_title_id'             => $jobSupervisor,
                'employment_type'          => EmploymentType::FULL_TIME,
                'current_headcount'        => 0,
                'target_headcount'         => 1,
                'vacancies_count'          => 1,
                'fulfilled_count'          => 0,
                'estimated_monthly_salary' => 800.000,
                'priority'                 => PlanItemPriority::MEDIUM,
                'target_quarter'           => PlanQuarter::Q4,
                'justification'            => 'مشرفة مسائية متفرغة.',
            ],
        ];

        foreach ($itemsPlan2 as $itemData) {
            $item = WorkforcePlanItem::create($itemData);
            $item->recalculate();
        }
        $plan2->recalculateTotals();

        // Plan 3: Draft Plan for 2027
        $plan3 = WorkforcePlan::updateOrCreate(
            ['plan_code' => 'WFP-2027-001'],
            [
                'title'       => 'مسودة خطة القوى العاملة للعام الأكاديمي 2027',
                'year'        => 2027,
                'start_date'  => Carbon::create(2027, 1, 1),
                'end_date'    => Carbon::create(2027, 12, 31),
                'status'      => WorkforcePlanStatus::DRAFT,
                'notes'       => 'مقترح تقديري للعام القادم قيد المراجعة وإعداد الميزانيات.',
                'created_by'  => $adminId,
            ]
        );

        $itemsPlan3 = [
            [
                'workforce_plan_id'        => $plan3->id,
                'department_id'            => $dept1,
                'branch_id'                => $branch1,
                'job_title_id'             => $jobTeacher,
                'employment_type'          => EmploymentType::FULL_TIME,
                'current_headcount'        => 6,
                'target_headcount'         => 10,
                'vacancies_count'          => 4,
                'fulfilled_count'          => 0,
                'estimated_monthly_salary' => 700.000,
                'priority'                 => PlanItemPriority::MEDIUM,
                'target_quarter'           => PlanQuarter::ANNUAL,
                'justification'            => 'توسعات متوقعة بالعام 2027.',
            ]
        ];

        foreach ($itemsPlan3 as $itemData) {
            $item = WorkforcePlanItem::create($itemData);
            $item->recalculate();
        }
        $plan3->recalculateTotals();
    }
}
