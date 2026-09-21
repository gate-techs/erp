<?php

namespace Modules\HR\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Modules\HR\Enums\ApplicationStage;
use Modules\HR\Enums\ApplicationStatus;
use Modules\HR\Enums\RejectionReason;
use Modules\HR\Models\Candidate;
use Modules\HR\Models\JobApplication;
use Modules\HR\Models\JobOpening;

class JobApplicationSeeder extends Seeder
{
    public function run(): void
    {
        $openings = JobOpening::all();
        if ($openings->isEmpty()) {
            return;
        }

        $op1 = $openings->firstWhere('opening_code', 'JOB-2026-0001') ?? $openings->first();
        $op2 = $openings->firstWhere('opening_code', 'JOB-2026-0002') ?? $openings->get(1) ?? $op1;
        $op3 = $openings->firstWhere('opening_code', 'JOB-2026-0003') ?? $openings->get(2) ?? $op1;

        $candidatesData = [
            // Candidate 1: Hired Kindergarten Teacher
            [
                'candidate' => [
                    'first_name'             => 'مريم',
                    'last_name'              => 'الأنصاري',
                    'full_name'              => 'مريم الأنصاري',
                    'email'                  => 'mariam.alansari@example.com',
                    'phone'                  => '+965 99887766',
                    'nationality'            => 'كويتية',
                    'city'                   => 'الكويت العاصمة',
                    'current_job_title'      => 'معلمة رياض أطفال أولى',
                    'current_company'        => 'روضة البراعم النموذجية',
                    'total_experience_years' => 4.5,
                    'notes'                  => 'مرشحة ممتازة ذات سجل تدريسي متميز واجتازت المقابلة بكفاءة.',
                ],
                'application' => [
                    'application_code'   => 'APP-2026-0001',
                    'job_opening_id'     => $op1->id,
                    'stage'              => ApplicationStage::HIRED,
                    'status'             => ApplicationStatus::ACTIVE,
                    'rating'             => 5,
                    'expected_salary'    => 550.00,
                    'currency'           => 'KWD',
                    'notice_period_days' => 15,
                    'source'             => 'LinkedIn',
                    'hired_at'           => Carbon::now()->subDays(3),
                    'created_at'         => Carbon::now()->subDays(20),
                ],
            ],

            // Candidate 2: Interview Stage Teacher
            [
                'candidate' => [
                    'first_name'             => 'فاطمة',
                    'last_name'              => 'محمود السعيد',
                    'full_name'              => 'فاطمة محمود السعيد',
                    'email'                  => 'fatma.mahmoud@example.com',
                    'phone'                  => '+965 66554433',
                    'nationality'            => 'مصرية',
                    'city'                   => 'حولي',
                    'current_job_title'      => 'معلمة تربية مبكرة وتأسيس لغوي',
                    'current_company'        => 'أكاديمية المستقبل',
                    'total_experience_years' => 3.0,
                    'notes'                  => 'تم تحديد مقابلة فنية حضورية يوم الخميس القادم.',
                ],
                'application' => [
                    'application_code'   => 'APP-2026-0002',
                    'job_opening_id'     => $op1->id,
                    'stage'              => ApplicationStage::INTERVIEW,
                    'status'             => ApplicationStatus::ACTIVE,
                    'rating'             => 4,
                    'expected_salary'    => 500.00,
                    'currency'           => 'KWD',
                    'notice_period_days' => 30,
                    'source'             => 'Career Portal',
                    'created_at'         => Carbon::now()->subDays(8),
                ],
            ],

            // Candidate 3: Screening Stage Teacher
            [
                'candidate' => [
                    'first_name'             => 'نور',
                    'last_name'              => 'خليل الهاشمي',
                    'full_name'              => 'نور خليل الهاشمي',
                    'email'                  => 'nour.hashemi@example.com',
                    'phone'                  => '+965 91234567',
                    'nationality'            => 'أردنية',
                    'city'                   => 'السالمية',
                    'current_job_title'      => 'مساعدة معلمة رياض أطفال',
                    'current_company'        => 'حضانة النجوم',
                    'total_experience_years' => 2.0,
                    'notes'                  => 'الملف مكتمل ويجري مراجعة شهادة البكالوريوس.',
                ],
                'application' => [
                    'application_code'   => 'APP-2026-0003',
                    'job_opening_id'     => $op1->id,
                    'stage'              => ApplicationStage::SCREENING,
                    'status'             => ApplicationStatus::ACTIVE,
                    'rating'             => 3,
                    'expected_salary'    => 480.00,
                    'currency'           => 'KWD',
                    'notice_period_days' => 15,
                    'source'             => 'Direct',
                    'created_at'         => Carbon::now()->subDays(4),
                ],
            ],

            // Candidate 4: New / Applied Teacher
            [
                'candidate' => [
                    'first_name'             => 'ريم',
                    'last_name'              => 'سالم الدوسري',
                    'full_name'              => 'ريم سالم الدوسري',
                    'email'                  => 'reem.aldosari@example.com',
                    'phone'                  => '+965 50112233',
                    'nationality'            => 'كويتية',
                    'city'                   => 'الفروانية',
                    'current_job_title'      => 'خريجة حديثة - تربية طفولة',
                    'current_company'        => null,
                    'total_experience_years' => 1.0,
                    'notes'                  => 'طلب وارد حديثاً من الموقع الإلكتروني.',
                ],
                'application' => [
                    'application_code'   => 'APP-2026-0004',
                    'job_opening_id'     => $op1->id,
                    'stage'              => ApplicationStage::NEW,
                    'status'             => ApplicationStatus::ACTIVE,
                    'rating'             => null,
                    'expected_salary'    => 450.00,
                    'currency'           => 'KWD',
                    'notice_period_days' => 0,
                    'source'             => 'Career Portal',
                    'created_at'         => Carbon::now()->subDay(),
                ],
            ],

            // Candidate 5: Offer Stage Nurse
            [
                'candidate' => [
                    'first_name'             => 'هيلين',
                    'last_name'              => 'سانتوس (Helen Santos)',
                    'full_name'              => 'هيلين سانتوس',
                    'email'                  => 'helen.santos@example.com',
                    'phone'                  => '+965 97788990',
                    'nationality'            => 'فلبينية',
                    'city'                   => 'المهبولة',
                    'current_job_title'      => 'Staff Pediatric Nurse',
                    'current_company'        => 'مستشفى الشفاء الدولي',
                    'total_experience_years' => 5.0,
                    'notes'                  => 'تم تقديم العرض الوظيفي الرسمي وجاري انتظار التوقيع.',
                ],
                'application' => [
                    'application_code'   => 'APP-2026-0005',
                    'job_opening_id'     => $op2->id,
                    'stage'              => ApplicationStage::OFFER,
                    'status'             => ApplicationStatus::ACTIVE,
                    'rating'             => 5,
                    'expected_salary'    => 520.00,
                    'currency'           => 'KWD',
                    'notice_period_days' => 30,
                    'source'             => 'Recruitment Agency',
                    'created_at'         => Carbon::now()->subDays(12),
                ],
            ],

            // Candidate 6: Disqualified / Rejected Candidate
            [
                'candidate' => [
                    'first_name'             => 'أحمد',
                    'last_name'              => 'عصام كمال',
                    'full_name'              => 'أحمد عصام كمال',
                    'email'                  => 'ahmed.kamal@example.com',
                    'phone'                  => '+965 60123456',
                    'nationality'            => 'مصري',
                    'city'                   => 'الفحيحيل',
                    'current_job_title'      => 'مندوب مبيعات وتسويق',
                    'current_company'        => 'شركة الخليج للتجارة',
                    'total_experience_years' => 1.5,
                    'notes'                  => 'لا يمتلك الخبرة أو المؤهل المطلوب للشاغر.',
                ],
                'application' => [
                    'application_code'   => 'APP-2026-0006',
                    'job_opening_id'     => $op3->id,
                    'stage'              => ApplicationStage::SCREENING,
                    'status'             => ApplicationStatus::REJECTED,
                    'rating'             => 1,
                    'expected_salary'    => 700.00,
                    'currency'           => 'KWD',
                    'notice_period_days' => 60,
                    'source'             => 'Direct',
                    'rejection_reason'   => RejectionReason::EXPERIENCE_MISMATCH,
                    'rejection_notes'    => 'المؤهل والخبرة العملية غير متطابقين مع متطلبات الوظيفة المعلنة.',
                    'rejected_at'        => Carbon::now()->subDays(2),
                    'created_at'         => Carbon::now()->subDays(14),
                ],
            ],
        ];

        foreach ($candidatesData as $item) {
            $candidate = Candidate::updateOrCreate(
                ['email' => $item['candidate']['email']],
                $item['candidate']
            );

            $appData = $item['application'];
            $appData['candidate_id'] = $candidate->id;

            JobApplication::updateOrCreate(
                ['application_code' => $appData['application_code']],
                $appData
            );
        }
    }
}
