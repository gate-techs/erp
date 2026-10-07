<?php

namespace Modules\Academic\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Academic\Models\AcademicClass;
use Modules\Academic\Models\AcademicStage;
use Modules\Academic\Models\AcademicWeeklyPlan;
use Modules\Nursery\Models\Nursery;
use Modules\Nursery\Models\NurseryBranch;

class AcademicWeeklyPlanDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds for Academic Weekly Plans.
     */
    public function run(): void
    {
        $adminUser = User::first();
        $adminId = $adminUser?->id ?? 1;

        $nurseries = Nursery::all();

        if ($nurseries->isEmpty()) {
            return;
        }

        foreach ($nurseries as $nursery) {
            $branches = NurseryBranch::where('nursery_id', $nursery->id)->get();
            $stages = AcademicStage::where('institution_id', $nursery->id)->get();
            $classes = AcademicClass::where('institution_id', $nursery->id)->get();

            if ($stages->isEmpty() || $classes->isEmpty()) {
                continue;
            }

            $firstStage = $stages->first();
            $firstClass = $classes->first();
            $branchId = $branches->first()?->id;

            $plans = [
                [
                    'title_ar'   => 'الخطة الأسبوعية - الأسبوع الأول: الترحيب والتعارف',
                    'title_en'   => 'Weekly Plan - Week 1: Welcome & Orientation',
                    'content_ar' => "الأهداف الأسبوعية:\n- أنشطة كسر الجليد والتعرف على المعلمة والزملاء.\n- تعليم الروتين اليومي وأركان الفصل.\n- مهارات حسية وحركية ممتعة وقراءة قصة ترحيبية.",
                    'content_en' => "Weekly Objectives:\n- Ice-breaker activities and classroom orientation.\n- Daily routines and classroom centers introduction.\n- Sensory games and interactive storytelling.",
                    'start_date' => now()->startOfWeek()->toDateString(),
                    'end_date'   => now()->endOfWeek()->toDateString(),
                    'status'     => AcademicWeeklyPlan::STATUS_ACTIVE,
                ],
                [
                    'title_ar'   => 'الخطة الأسبوعية - الأسبوع الثاني: استكشاف الحواس الخمس',
                    'title_en'   => 'Weekly Plan - Week 2: Five Senses Discovery',
                    'content_ar' => "الأهداف الأسبوعية:\n- تعريف الأطفال بالحواس الخمس واستخداماتها اليومية.\n- تجارب استكشافية للألوان والأصوات والملمس.\n- تدريبات لغوية وأناشيد تعليمية.",
                    'content_en' => "Weekly Objectives:\n- Introducing the five senses and their daily functions.\n- Sensory experiments for colors, textures, and sounds.\n- Language activities and educational rhymes.",
                    'start_date' => now()->addWeek()->startOfWeek()->toDateString(),
                    'end_date'   => now()->addWeek()->endOfWeek()->toDateString(),
                    'status'     => AcademicWeeklyPlan::STATUS_ACTIVE,
                ],
            ];

            foreach ($plans as $planData) {
                $plan = AcademicWeeklyPlan::where('institution_id', $nursery->id)
                    ->where('academic_stage_id', $firstStage->id)
                    ->where('academic_class_id', $firstClass->id)
                    ->whereHas('translations', function ($q) use ($planData) {
                        $q->where('title', $planData['title_ar']);
                    })
                    ->first();

                if (!$plan) {
                    $plan = new AcademicWeeklyPlan();
                    $plan->institution_id    = $nursery->id;
                    $plan->branch_id         = $branchId;
                    $plan->academic_stage_id = $firstStage->id;
                    $plan->academic_class_id = $firstClass->id;
                    $plan->start_date        = $planData['start_date'];
                    $plan->end_date          = $planData['end_date'];
                    $plan->status            = $planData['status'];
                    $plan->created_by        = $adminId;

                    $plan->translateOrNew('ar')->title   = $planData['title_ar'];
                    $plan->translateOrNew('ar')->content = $planData['content_ar'];

                    $plan->translateOrNew('en')->title   = $planData['title_en'];
                    $plan->translateOrNew('en')->content = $planData['content_en'];

                    $plan->save();
                }
            }
        }
    }
}
