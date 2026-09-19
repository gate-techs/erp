<?php

namespace Modules\HR\Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Modules\HR\Enums\EmploymentType;
use Modules\HR\Enums\JobRequisitionStatus;
use Modules\HR\Enums\PlanItemPriority;
use Modules\HR\Enums\RequisitionType;
use Modules\HR\Models\Employee;
use Modules\HR\Models\JobRequisition;
use Modules\HR\Models\WorkforcePlan;
use Modules\HR\Models\WorkforcePlanItem;

class JobRequisitionSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first();
        $adminId = $admin ? $admin->id : 1;

        $plan1 = WorkforcePlan::where('plan_code', 'WFP-2026-001')->first();
        $plan2 = WorkforcePlan::where('plan_code', 'WFP-2026-002')->first();

        $itemTeacher    = $plan1 ? $plan1->items()->skip(0)->first() : null;
        $itemSupervisor = $plan1 ? $plan1->items()->skip(1)->first() : null;
        $itemNurse      = $plan1 ? $plan1->items()->skip(2)->first() : null;
        $itemHR         = $plan1 ? $plan1->items()->skip(3)->first() : null;

        $anyEmployee = Employee::first();

        // 1. REQ-2026-0001: Active in recruitment (معلمة أطفال)
        if ($itemTeacher) {
            JobRequisition::updateOrCreate(
                ['requisition_number' => 'REQ-2026-0001'],
                [
                    'workforce_plan_item_id' => $itemTeacher->id,
                    'is_budgeted'            => true,
                    'department_id'          => $itemTeacher->department_id,
                    'branch_id'              => $itemTeacher->branch_id,
                    'job_title_id'           => $itemTeacher->job_title_id,
                    'requisition_type'       => RequisitionType::EXPANSION,
                    'employment_type'        => EmploymentType::FULL_TIME,
                    'headcount_needed'       => 2,
                    'hired_count'            => 1,
                    'min_salary'             => 600.000,
                    'max_salary'             => 700.000,
                    'priority'               => PlanItemPriority::HIGH,
                    'expected_start_date'    => Carbon::now()->addDays(15),
                    'justification'          => 'افتتاح فصول جديدة لمرحلة الروضة الأولى نظراً للإقبال الشديد.',
                    'job_description'        => "1. إعداد الخطط التعليمية اليومية والأنشطة الإثرائية.\n2. متابعة التطور السلوكي واللغوي للأطفال.\n3. التواصل الدوري مع أولياء الأمور.",
                    'requirements'           => "1. بكالوريوس رياض أطفال أو تربية طفولة مبكرة.\n2. خبرة لا تقل عن سنتين في الحضانات المعتمدة.\n3. إجادة اللغة الإنجليزية والعربية تحدثاً وكتابة.",
                    'status'                 => JobRequisitionStatus::IN_RECRUITMENT,
                    'created_by'             => $adminId,
                    'requested_by'           => $adminId,
                    'approved_by'            => $adminId,
                    'approved_at'            => Carbon::now()->subDays(10),
                ]
            );
        }

        // 2. REQ-2026-0002: Approved replacement (ممرضة أطفال)
        if ($itemNurse) {
            JobRequisition::updateOrCreate(
                ['requisition_number' => 'REQ-2026-0002'],
                [
                    'workforce_plan_item_id'  => $itemNurse->id,
                    'is_budgeted'             => true,
                    'department_id'           => $itemNurse->department_id,
                    'branch_id'               => $itemNurse->branch_id,
                    'job_title_id'            => $itemNurse->job_title_id,
                    'requisition_type'            => RequisitionType::REPLACEMENT,
                    'employment_type'             => EmploymentType::FULL_TIME,
                    'replacement_for_employee_id' => $anyEmployee?->id,
                    'headcount_needed'            => 1,
                    'hired_count'                 => 0,
                    'min_salary'                  => 500.000,
                    'max_salary'                  => 600.000,
                    'priority'                    => PlanItemPriority::CRITICAL,
                    'expected_start_date'         => Carbon::now()->addDays(7),
                    'justification'               => 'استقالة الممرضة الحالية والحاجة الماسة لتغطية العيادة الطبية في الفرع وفق الاشتراطات الوزارية.',
                    'job_description'             => "1. الإشراف الطبي ومتابعة الملفات الصحية للأطفال.\n2. تقديم الإسعافات الأولية وتطبيق بروتوكولات السلامة والوقاية.",
                    'requirements'                => "1. بكالوريوس أو دبلوم تمريض معتمد.\n2. ترخيص مزاولة مهنة ساري من وزارة الصحة الكويتية.",
                    'status'                      => JobRequisitionStatus::APPROVED,
                    'created_by'                  => $adminId,
                    'requested_by'                => $adminId,
                    'approved_by'                 => $adminId,
                    'approved_at'                 => Carbon::now()->subDays(3),
                ]
            );
        }

        // 3. REQ-2026-0003: Pending approval (مشرفة رعاية أطفال)
        if ($itemSupervisor) {
            JobRequisition::updateOrCreate(
                ['requisition_number' => 'REQ-2026-0003'],
                [
                    'workforce_plan_item_id' => $itemSupervisor->id,
                    'is_budgeted'            => true,
                    'department_id'          => $itemSupervisor->department_id,
                    'branch_id'              => $itemSupervisor->branch_id,
                    'job_title_id'           => $itemSupervisor->job_title_id,
                    'requisition_type'       => RequisitionType::EXPANSION,
                    'employment_type'        => EmploymentType::FULL_TIME,
                    'headcount_needed'       => 1,
                    'hired_count'            => 0,
                    'min_salary'             => 800.000,
                    'max_salary'             => 900.000,
                    'priority'               => PlanItemPriority::MEDIUM,
                    'expected_start_date'    => Carbon::now()->addMonth(),
                    'justification'          => 'دعم الكادر الإشرافي لمتابعة الجودة وتطوير معايير الرعاية والتعليم بالحضانة.',
                    'job_description'        => "1. توجيه المعلمات ومساعدات الرعاية والإشراف اليومي على تطبيق الخطط والبرامج.\n2. إعداد التقارير الدورية عن سير العمل.",
                    'requirements'           => "1. مؤهل جامعي تربوي مناسب.\n2. خبرة إشرافية لا تقل عن 4 سنوات في قطاع الحضانات.",
                    'status'                 => JobRequisitionStatus::PENDING_APPROVAL,
                    'created_by'             => $adminId,
                    'requested_by'           => $adminId,
                ]
            );
        }

        // 4. REQ-2026-0004: Fulfilled emergency / unbudgeted
        if ($itemTeacher) {
            JobRequisition::updateOrCreate(
                ['requisition_number' => 'REQ-2026-0004'],
                [
                    'workforce_plan_item_id' => null, // خارج الخطة
                    'is_budgeted'            => false,
                    'department_id'          => $itemTeacher->department_id,
                    'branch_id'              => $itemTeacher->branch_id,
                    'job_title_id'           => $itemTeacher->job_title_id,
                    'requisition_type'       => RequisitionType::TEMPORARY,
                    'employment_type'        => EmploymentType::PART_TIME,
                    'headcount_needed'       => 1,
                    'hired_count'            => 1,
                    'min_salary'             => 400.000,
                    'max_salary'             => 450.000,
                    'priority'               => PlanItemPriority::HIGH,
                    'expected_start_date'    => Carbon::now()->subMonth(),
                    'justification'          => 'حاجة طارئة لمساعدة معلمات خلال البرنامج الصيفي والفعاليات المكثفة.',
                    'job_description'        => 'مساعدة في الأنشطة الميدانية ومرافقة الأطفال.',
                    'requirements'           => 'طاقة إيجابية وخبرة في التعامل مع الأطفال.',
                    'status'                 => JobRequisitionStatus::FULFILLED,
                    'created_by'             => $adminId,
                    'requested_by'           => $adminId,
                    'approved_by'            => $adminId,
                    'approved_at'            => Carbon::now()->subMonths(2),
                ]
            );
        }

        // 5. REQ-2026-0005: Draft requisition (مسؤول شؤون موظفين)
        if ($itemHR) {
            JobRequisition::updateOrCreate(
                ['requisition_number' => 'REQ-2026-0005'],
                [
                    'workforce_plan_item_id' => $itemHR->id,
                    'is_budgeted'            => true,
                    'department_id'          => $itemHR->department_id,
                    'branch_id'              => $itemHR->branch_id,
                    'job_title_id'           => $itemHR->job_title_id,
                    'requisition_type'       => RequisitionType::EXPANSION,
                    'employment_type'        => EmploymentType::FULL_TIME,
                    'headcount_needed'       => 1,
                    'hired_count'            => 0,
                    'min_salary'             => 650.000,
                    'max_salary'             => 750.000,
                    'priority'               => PlanItemPriority::MEDIUM,
                    'expected_start_date'    => Carbon::now()->addMonths(2),
                    'justification'          => 'زيادة حجم العمل في شؤون الموظفين وتحديث ملفات التأمينات والإقامات.',
                    'job_description'        => 'إدارة ملفات الموظفين والإجازات ومتابعة إجراءات الإقامة والجوازات.',
                    'requirements'           => 'خبرة بنظام العمل الكويتي وإجراءات الشؤون وإتقان الأنظمة السحابية.',
                    'status'                 => JobRequisitionStatus::DRAFT,
                    'created_by'             => $adminId,
                    'requested_by'           => $adminId,
                ]
            );
        }
    }
}
