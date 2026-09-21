<?php

namespace Modules\HR\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Modules\HR\Enums\EvaluationRecommendation;
use Modules\HR\Enums\InterviewMode;
use Modules\HR\Enums\InterviewStatus;
use Modules\HR\Enums\InterviewType;
use Modules\HR\Models\Employee;
use Modules\HR\Models\Interview;
use Modules\HR\Models\InterviewEvaluation;
use Modules\HR\Models\JobApplication;

class InterviewSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::take(4)->get();
        if ($employees->isEmpty()) {
            return;
        }

        $emp1 = $employees->get(0);
        $emp2 = $employees->get(1) ?? $emp1;
        $emp3 = $employees->get(2) ?? $emp1;

        $apps = JobApplication::all();
        if ($apps->isEmpty()) {
            return;
        }

        $app1 = $apps->firstWhere('application_code', 'APP-2026-0001') ?? $apps->first();
        $app2 = $apps->firstWhere('application_code', 'APP-2026-0002') ?? $apps->get(1) ?? $app1;
        $app3 = $apps->firstWhere('application_code', 'APP-2026-0003') ?? $apps->get(2) ?? $app1;

        // 1. Completed Final Interview for App 1 (Hired Candidate)
        $int1 = Interview::updateOrCreate(
            ['job_application_id' => $app1->id, 'interview_round' => 1],
            [
                'job_opening_id'       => $app1->job_opening_id,
                'candidate_id'         => $app1->candidate_id,
                'title'                => 'المقابلة الختامية واعتماد العرض',
                'type'                 => InterviewType::FINAL,
                'mode'                 => InterviewMode::IN_PERSON,
                'status'               => InterviewStatus::COMPLETED,
                'scheduled_at'         => Carbon::now()->subDays(6)->setTime(11, 0),
                'duration_minutes'     => 60,
                'location'             => 'المقر الرئيسي - قاعة الاجتماعات التنفيذية A',
                'notes'                => 'المقابلة النهائية مع الإدارة لمناقشة المهام والراتب والجاهزية للبدء.',
                'overall_score'        => 5.0,
                'final_recommendation' => EvaluationRecommendation::STRONG_HIRE,
                'created_by'           => 1,
            ]
        );

        $int1->interviewers()->sync([
            $emp1->id => ['is_primary' => true],
            $emp2->id => ['is_primary' => false],
        ]);

        InterviewEvaluation::updateOrCreate(
            ['interview_id' => $int1->id, 'interviewer_id' => $emp1->id],
            [
                'technical_score'       => 5,
                'communication_score'   => 5,
                'problem_solving_score' => 5,
                'cultural_fit_score'    => 5,
                'experience_score'      => 5,
                'average_score'         => 5.0,
                'recommendation'        => EvaluationRecommendation::STRONG_HIRE,
                'strengths'             => 'خبرة استثنائية وأسلوب تربوي تفاعلي حديث وشخصية قيادية متزنة.',
                'weaknesses'            => 'لا يوجد نقاط ضعف مؤثرة.',
                'notes'                 => 'توصية مؤكدة بالتعيين الفوري مع اعتماد العرض الوظيفي.',
                'submitted_at'          => Carbon::now()->subDays(6),
            ]
        );

        // 2. Completed Technical Interview for App 2 (Fatma Mahmoud)
        $int2 = Interview::updateOrCreate(
            ['job_application_id' => $app2->id, 'interview_round' => 1],
            [
                'job_opening_id'       => $app2->job_opening_id,
                'candidate_id'         => $app2->candidate_id,
                'title'                => 'المقابلة الفنية والتأسيس اللغوي',
                'type'                 => InterviewType::TECHNICAL,
                'mode'                 => InterviewMode::ONLINE,
                'status'               => InterviewStatus::COMPLETED,
                'scheduled_at'         => Carbon::now()->subDays(2)->setTime(13, 0),
                'duration_minutes'     => 45,
                'meeting_link'         => 'https://meet.google.com/erp-hire-tech',
                'notes'                => 'اختبار طرق التأسيس اللغوي وتصميم خطط الدروس التفاعلية.',
                'overall_score'        => 4.2,
                'final_recommendation' => EvaluationRecommendation::HIRE,
                'created_by'           => 1,
            ]
        );

        $int2->interviewers()->sync([
            $emp2->id => ['is_primary' => true],
        ]);

        InterviewEvaluation::updateOrCreate(
            ['interview_id' => $int2->id, 'interviewer_id' => $emp2->id],
            [
                'technical_score'       => 4,
                'communication_score'   => 4,
                'problem_solving_score' => 4,
                'cultural_fit_score'    => 5,
                'experience_score'      => 4,
                'average_score'         => 4.2,
                'recommendation'        => EvaluationRecommendation::HIRE,
                'strengths'             => 'إلمام عميق بأساليب منتسوري في التأسيس اللغوي وروح مرحة تجذب الأطفال.',
                'weaknesses'            => 'تحتاج إلى دورة تدريبية سريعة على منظومة تقارير المتابعة الإلكترونية.',
                'notes'                 => 'مرشحة قوية ومناسبة لفريق العمل.',
                'submitted_at'          => Carbon::now()->subDays(2),
            ]
        );

        // 3. Upcoming Managerial Interview for App 2
        $int3 = Interview::updateOrCreate(
            ['job_application_id' => $app2->id, 'interview_round' => 2],
            [
                'job_opening_id'       => $app2->job_opening_id,
                'candidate_id'         => $app2->candidate_id,
                'title'                => 'المقابلة الإدارية والقيادية',
                'type'                 => InterviewType::MANAGERIAL,
                'mode'                 => InterviewMode::IN_PERSON,
                'status'               => InterviewStatus::SCHEDULED,
                'scheduled_at'         => Carbon::now()->addDays(1)->setTime(10, 30),
                'duration_minutes'     => 45,
                'location'             => 'مبنى الإدارة - مكتب مديرة فرع حولي',
                'notes'                => 'المقابلة الإدارية مع مديرة الفرع لمناقشة المرونة وضغط العمل والالتزام.',
                'created_by'           => 1,
            ]
        );

        $int3->interviewers()->sync([
            $emp3->id => ['is_primary' => true],
        ]);

        // 4. Upcoming Phone Screening for App 3 (Nour Hashemi)
        $int4 = Interview::updateOrCreate(
            ['job_application_id' => $app3->id, 'interview_round' => 1],
            [
                'job_opening_id'       => $app3->job_opening_id,
                'candidate_id'         => $app3->candidate_id,
                'title'                => 'مكالمة تصفية أولية',
                'type'                 => InterviewType::PHONE_SCREEN,
                'mode'                 => InterviewMode::PHONE,
                'status'               => InterviewStatus::SCHEDULED,
                'scheduled_at'         => Carbon::now()->addDays(2)->setTime(11, 00),
                'duration_minutes'     => 30,
                'location'             => 'هاتفياً (+965 91234567)',
                'notes'                => 'مكالمة تصفية أولية للتحقق من التفرغ وفترة الإخطار وسنوات الخبرة الفعلية.',
                'created_by'           => 1,
            ]
        );

        $int4->interviewers()->sync([
            $emp1->id => ['is_primary' => true],
        ]);
    }
}
