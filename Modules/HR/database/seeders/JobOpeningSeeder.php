<?php

namespace Modules\HR\Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Modules\HR\Enums\EmploymentType;
use Modules\HR\Enums\JobOpeningStatus;
use Modules\HR\Enums\PostingType;
use Modules\HR\Enums\WorkplaceType;
use Modules\HR\Models\JobOpening;
use Modules\HR\Models\JobRequisition;
use Modules\HR\Models\JobTitle;
use Modules\HR\Models\Department;
use Modules\Nursery\Models\NurseryBranch;

class JobOpeningSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first();
        $adminId = $admin ? $admin->id : 1;

        $req1 = JobRequisition::where('requisition_number', 'REQ-2026-0001')->first();
        $req2 = JobRequisition::where('requisition_number', 'REQ-2026-0002')->first();

        $deptTeaching = Department::first();
        $jobTeacher   = JobTitle::first();
        $branch       = NurseryBranch::first();

        // 1. JOB-2026-0001: Active Published Opening
        JobOpening::updateOrCreate(
            ['opening_code' => 'JOB-2026-0001'],
            [
                'job_requisition_id'   => $req1?->id,
                'title'                => 'معلمة رياض أطفال معتمدة (Early Childhood Educator)',
                'job_title_id'         => $req1?->job_title_id ?? $jobTeacher?->id,
                'department_id'        => $req1?->department_id ?? $deptTeaching?->id,
                'branch_id'            => $req1?->branch_id ?? $branch?->id,
                'employment_type'      => EmploymentType::FULL_TIME,
                'workplace_type'       => WorkplaceType::ON_SITE,
                'posting_type'         => PostingType::BOTH,
                'open_positions'       => 2,
                'filled_positions'     => 1,
                'min_experience_years' => 2,
                'max_experience_years' => 5,
                'min_salary'           => 450.00,
                'max_salary'           => 650.00,
                'currency'             => 'KWD',
                'hide_salary'          => false,
                'publish_date'         => Carbon::now()->subDays(10),
                'closing_date'         => Carbon::now()->addDays(20),
                'status'               => JobOpeningStatus::PUBLISHED,
                'created_by'           => $adminId,
                'published_by'         => $adminId,
                'ar' => [
                    'title'            => 'معلمة رياض أطفال معتمدة',
                    'education_level'  => 'بكالوريوس رياض أطفال أو تربية طفولة مبكرة',
                    'job_summary'      => 'نبحث عن معلمات متميزات ومتحمسات في مجال رياض الأطفال للانضمام إلى فريقنا التعليمي في بيئة محفزة وآمنة للأطفال.',
                    'responsibilities' => "1. إعداد وتطبيق خطط الدروس والأنشطة التعليمية التفاعلية وفق المنهج المعتمد.\n2. تقييم ومتابعة التطور السلوكي واللغوي والمهاري لكل طفل.\n3. الحفاظ على بيئة صفية آمنة ومحفزة على الإبداع والاكتشاف.\n4. التواصل الدوري والفعال مع أولياء الأمور بشأن تقدم أطفالهم.",
                    'requirements'     => "1. مؤهل جامعي متخصص (تربية طفولة مبكرة / رياض أطفال).\n2. خبرة مثبتة لا تقل عن سنتين في مؤسسات تعليمية أو حضانات دولية.\n3. مهارات ممتازة في إدارة الصف والتواصل الفعال.\n4. إجادة اللغة الإنجليزية والعربية بطلاقة.",
                    'benefits'         => "1. راتب مجزي وبدلات سنوية.\n2. تأمين صحي شامل واشتراك نوادي.\n3. دورات تدريبية وتطوير مهني مستمر.\n4. خصم خاص لأبناء العاملين.",
                ],
                'en' => [
                    'title'            => 'Certified Early Childhood Educator',
                    'education_level'  => "Bachelor's Degree in Early Childhood Education",
                    'job_summary'      => 'We are seeking passionate kindergarten educators to join our teaching staff in an inspiring and caring learning atmosphere.',
                    'responsibilities' => "1. Plan and deliver engaging daily lesson plans in line with our approved international curriculum.\n2. Monitor and evaluate children's developmental, behavioral, and language milestones.\n3. Ensure a secure, nurturing, and creative classroom setting.\n4. Foster active and regular communications with parents.",
                    'requirements'     => "1. University degree in Early Childhood Education or related field.\n2. Minimum 2 years experience in certified international nurseries or preschools.\n3. Fluent spoken and written English and Arabic.",
                    'benefits'         => "1. Competitive salary package and annual bonuses.\n2. Comprehensive medical health insurance.\n3. Continuous professional development workshops.\n4. Generous employee tuition discount for children.",
                ]
            ]
        );

        // 2. JOB-2026-0002: Active Published External Opening
        JobOpening::updateOrCreate(
            ['opening_code' => 'JOB-2026-0002'],
            [
                'job_requisition_id'   => $req2?->id,
                'title'                => 'ممرضة أطفال مقيمة (Pediatric Nurse)',
                'job_title_id'         => $req2?->job_title_id ?? $jobTeacher?->id,
                'department_id'        => $req2?->department_id ?? $deptTeaching?->id,
                'branch_id'            => $req2?->branch_id ?? $branch?->id,
                'employment_type'      => EmploymentType::FULL_TIME,
                'workplace_type'       => WorkplaceType::ON_SITE,
                'posting_type'         => PostingType::EXTERNAL,
                'open_positions'       => 1,
                'filled_positions'     => 0,
                'min_experience_years' => 3,
                'max_experience_years' => 6,
                'min_salary'           => 400.00,
                'max_salary'           => 550.00,
                'currency'             => 'KWD',
                'hide_salary'          => false,
                'publish_date'         => Carbon::now()->subDays(5),
                'closing_date'         => Carbon::now()->addDays(25),
                'status'               => JobOpeningStatus::PUBLISHED,
                'created_by'           => $adminId,
                'published_by'         => $adminId,
                'ar' => [
                    'title'            => 'ممرضة أطفال مقيمة',
                    'education_level'  => 'بكالوريوس تمريض مع ترخيص مزاولة المهنة ساري',
                    'job_summary'      => 'مطلوب ممرضة أطفال متمرسة لمتابعة الحالة الصحية العامة للأطفال وتطبيق أعلى معايير السلامة والرعاية الصحية.',
                    'responsibilities' => "1. الفحص اليومي والمتابعة الصحية الدورية للأطفال في الحضانة.\n2. التعامل مع الحالات الطارئة والإسعافات الأولية بكفاءة عالية.\n3. إدارة السجلات الطبية للأطفال والتواصل مع أولياء الأمور عند اللزوم.",
                    'requirements'         => "1. شهادة تمريض معتمدة وترخيص مزاولة مهنة ساري.\n2. خبرة عملية سابقة في طب الأطفال أو الحضانات.\n3. شهادة إسعافات أولية وإنعاش قلبي رئوي (BLS / CPR).",
                    'benefits'             => "1. تأمين طبي واجتماعي كامل.\n2. بيئة عمل مهنية داعمة.\n3. مكافآت سنوية ترتبط بالأداء.",
                ],
                'en' => [
                    'title'            => 'Resident Pediatric Nurse',
                    'education_level'  => "Bachelor of Science in Nursing (BSN) with valid license",
                    'job_summary'      => 'Experienced pediatric nurse required to oversee daily child health, safety compliance, and medical wellness.',
                    'responsibilities' => "1. Perform daily health assessments and morning check-ins for nursery children.\n2. Handle first aid procedures and emergency protocols effectively.\n3. Maintain confidential medical records and communicate with parents as needed.",
                    'requirements'     => "1. Valid nursing license with certified credentials.\n2. Minimum 3 years nursing experience in pediatrics or daycare settings.\n3. Certified in Basic Life Support (BLS) and CPR.",
                    'benefits'         => "1. Comprehensive medical insurance coverage.\n2. Supportive and professional working environment.\n3. Annual performance-based incentives.",
                ]
            ]
        );

        // 3. JOB-2026-0003: Draft Hybrid Opening
        JobOpening::updateOrCreate(
            ['opening_code' => 'JOB-2026-0003'],
            [
                'job_requisition_id'   => null,
                'title'                => 'أخصائي استقطاب مواهب وموارد بشرية',
                'job_title_id'         => $jobTeacher?->id,
                'department_id'        => $deptTeaching?->id,
                'branch_id'            => $branch?->id,
                'employment_type'      => EmploymentType::FULL_TIME,
                'workplace_type'       => WorkplaceType::HYBRID,
                'posting_type'         => PostingType::BOTH,
                'open_positions'       => 1,
                'filled_positions'     => 0,
                'min_experience_years' => 2,
                'max_experience_years' => 4,
                'min_salary'           => 500.00,
                'max_salary'           => 750.00,
                'currency'             => 'KWD',
                'hide_salary'          => true,
                'publish_date'         => null,
                'closing_date'         => null,
                'status'               => JobOpeningStatus::DRAFT,
                'created_by'           => $adminId,
                'published_by'         => null,
                'ar' => [
                    'title'            => 'أخصائي استقطاب مواهب وموارد بشرية',
                    'education_level'  => 'مؤهل جامعي مناسب في إدارة الأعمال أو الموارد البشرية',
                    'job_summary'      => 'إدارة خطة التوظيف ومتابعة إعلانات الشواغر واستقطاب أفضل الكفاءات التعليمية والإدارية.',
                    'responsibilities' => "1. نشر الإعلانات الوظيفية ومتابعة طلبات المتقدمين عبر المنصات المختلفة.\n2. فرز السير الذاتية وإجراء المقابلات الأولية.\n3. تنسيق مقابلات الأقسام وإعداد عروض العمل.",
                    'requirements'     => "1. خبرة لا تقل عن سنتين في توظيف الكوادر التعليمية والإدارية.\n2. إتقان استخدام نظم إدارة المتقدمين (ATS).\n3. مهارات تفاوض وتواصل استثنائية.",
                    'benefits'         => "1. مرونة العمل بنظام الدوام الهجين (يومان عن بعد أسبوعياً).\n2. بدلات اتصالات وتطوير وظيفي.",
                ],
                'en' => [
                    'title'            => 'Talent Acquisition & HR Specialist',
                    'education_level'  => "Bachelor's degree in Business Administration, HR, or related field",
                    'job_summary'      => 'Manage talent recruitment pipeline and recruit high-caliber educational and administrative personnel.',
                    'responsibilities' => "1. Create job postings and track applicant sources across job boards.\n2. Screen resumes and conduct preliminary screening interviews.\n3. Coordinate departmental interview panels and extend job offers.",
                    'requirements'     => "1. Minimum 2 years recruiting experience in education or service sector.\n2. Proficiency with modern Applicant Tracking Systems (ATS).\n3. Strong interpersonal and negotiation skills.",
                    'benefits'         => "1. Flexible hybrid work arrangement (2 days remote per week).\n2. Communication allowance and professional training budget.",
                ]
            ]
        );

        // 4. JOB-2026-0004: Closed Opening
        JobOpening::updateOrCreate(
            ['opening_code' => 'JOB-2026-0004'],
            [
                'job_requisition_id'   => null,
                'title'                => 'مشرفة تربوية وأكاديمية (Academic Coordinator)',
                'job_title_id'         => $jobTeacher?->id,
                'department_id'        => $deptTeaching?->id,
                'branch_id'            => $branch?->id,
                'employment_type'      => EmploymentType::FULL_TIME,
                'workplace_type'       => WorkplaceType::ON_SITE,
                'posting_type'         => PostingType::INTERNAL,
                'open_positions'       => 1,
                'filled_positions'     => 1,
                'min_experience_years' => 4,
                'max_experience_years' => 8,
                'min_salary'           => 700.00,
                'max_salary'           => 950.00,
                'currency'             => 'KWD',
                'hide_salary'          => false,
                'publish_date'         => Carbon::now()->subMonths(2),
                'closing_date'         => Carbon::now()->subDays(15),
                'status'               => JobOpeningStatus::CLOSED,
                'created_by'           => $adminId,
                'published_by'         => $adminId,
                'ar' => [
                    'title'            => 'مشرفة تربوية وأكاديمية',
                    'education_level'  => 'ماجستير في التربية أو إدارة المؤسسات التعليمية',
                    'job_summary'      => 'تم الانتهاء من شغل الشاغر بنجاح وترقية إحدى كفاءاتنا الداخلية.',
                    'responsibilities' => 'متابعة المناهج الدراسية وتقييم أداء المعلمات.',
                    'requirements'     => 'خبرة إشرافية سابقة في رياض الأطفال.',
                    'benefits'         => 'مزايا إشرافية وتأمينية كاملة.',
                ],
                'en' => [
                    'title'            => 'Academic Coordinator',
                    'education_level'  => "Master's in Education or Educational Leadership",
                    'job_summary'      => 'Successfully fulfilled via internal promotion.',
                    'responsibilities' => 'Oversee curriculum implementation and monitor educator performance.',
                    'requirements'     => 'Prior supervisory experience in early childhood education.',
                    'benefits'         => 'Comprehensive leadership and health benefits.',
                ]
            ]
        );
    }
}
