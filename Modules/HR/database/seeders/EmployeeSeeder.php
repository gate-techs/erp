<?php

namespace Modules\HR\Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Modules\HR\Enums\EmployeeStatus;
use Modules\HR\Enums\EmploymentType;
use Modules\HR\Enums\Gender;
use Modules\HR\Models\Employee;
use Modules\HR\Models\EmployeeAllowance;
use Modules\HR\Models\EmployeeDocument;
use Modules\HR\Models\HrSetting;
use Modules\Nursery\Models\NurseryBranch;
use Modules\Settings\Enums\GeneralSettingType;
use Modules\Settings\Models\GeneralSetting;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds for Employees of Astro Nursery.
     */
    public function run(): void
    {
        $adminUser = User::first();
        $adminId = $adminUser?->id ?? 1;

        // Fetch Branches
        $branchAbuFatira = NurseryBranch::whereTranslationLike('name', '%أبو فطيرة%')
            ->orWhereTranslationLike('name', '%ابو فطيرة%')
            ->first();

        $branchZahraa = NurseryBranch::whereTranslationLike('name', '%الزهراء%')
            ->first();

        if (!$branchAbuFatira || !$branchZahraa) {
            $this->command->warn('Branches not found. Please run NurseryDatabaseSeeder first.');
            return;
        }

        // Lookups
        $govHawalli = GeneralSetting::ofType(GeneralSettingType::GOVERNMENT)->whereTranslationLike('name', '%حولي%')->first();
        $govMubarak = GeneralSetting::ofType(GeneralSettingType::GOVERNMENT)->whereTranslationLike('name', '%مبارك الكبير%')->first();

        $natKuwaiti = GeneralSetting::ofType(GeneralSettingType::NATIONALITY)->whereTranslationLike('name', '%كويتي%')->first();
        $natSaudi = GeneralSetting::ofType(GeneralSettingType::NATIONALITY)->whereTranslationLike('name', '%سعودي%')->first();
        $natEgyptian = GeneralSetting::ofType(GeneralSettingType::NATIONALITY)->whereTranslationLike('name', '%مصري%')->first();
        $natBahraini = GeneralSetting::ofType(GeneralSettingType::NATIONALITY)->whereTranslationLike('name', '%بحريني%')->first();

        $statusSingle = GeneralSetting::ofType(GeneralSettingType::MARITAL_STATUS)->whereTranslationLike('name', '%أعزب%')->first();
        $statusMarried = GeneralSetting::ofType(GeneralSettingType::MARITAL_STATUS)->whereTranslationLike('name', '%متزوج%')->first();

        $relMuslim = GeneralSetting::ofType(GeneralSettingType::RELIGION)->whereTranslationLike('name', '%مسلم%')->first();

        // HR Allowance & Document Settings
        $housingAllowance = HrSetting::where('type', 'allowance-types')->whereTranslationLike('name', '%سكن%')->first();
        $transportAllowance = HrSetting::where('type', 'allowance-types')->whereTranslationLike('name', '%انتقال%')->orWhereTranslationLike('name', '%مواصلات%')->first();
        $phoneAllowance = HrSetting::where('type', 'allowance-types')->whereTranslationLike('name', '%هاتف%')->first();
        $natureAllowance = HrSetting::where('type', 'allowance-types')->whereTranslationLike('name', '%طبيعة عمل%')->first();

        $civilIdDoc = HrSetting::where('type', 'document-types')->whereTranslationLike('name', '%هوية%')->orWhereTranslationLike('name', '%إقامة%')->first();
        $passportDoc = HrSetting::where('type', 'document-types')->whereTranslationLike('name', '%جواز%')->first();
        $contractDoc = HrSetting::where('type', 'document-types')->whereTranslationLike('name', '%عقد%')->first();
        $degreeDoc = HrSetting::where('type', 'document-types')->whereTranslationLike('name', '%شهادة%')->orWhereTranslationLike('name', '%مؤهل%')->first();

        $employeesData = [
            [
                'code'           => 'EMP-0001',
                'name_ar'        => 'د. منى خالد العتيبي',
                'name_en'        => 'Dr. Mona Khaled Al-Otaibi',
                'gender'         => Gender::FEMALE->value,
                'job_title_id'   => 1, // مشرفة رعاية أطفال / إدارة
                'department_id'  => 1, // الإدارة التعليمية والتربوية
                'primary_branch' => $branchZahraa->id,
                'other_branches' => [$branchAbuFatira->id],
                'schedule_id'    => 1,
                'shift_id'       => 1,
                'nationality_id' => $natKuwaiti?->id ?? 75,
                'marital_id'     => $statusMarried?->id ?? 133,
                'gov_id'         => $govHawalli?->id ?? 16,
                'city'           => 'منطقة الزهراء',
                'hire_date'      => '2023-01-15',
                'birth_date'     => '1985-04-12',
                'phone'          => '+96599112233',
                'email'          => 'mona.otaibi@astro-nursery.com',
                'national_id'    => '285041200111',
                'basic_salary'   => 1250.00,
                'notes_ar'       => 'مديرة الحضانة والمشرفة العامة على المناهج التعليمية والأنشطة التربوية',
                'allowances'     => [
                    ['type_id' => $housingAllowance?->id ?? 1, 'amount' => 250.00, 'notes' => 'بدل سكن شهري'],
                    ['type_id' => $phoneAllowance?->id ?? 10, 'amount' => 30.00, 'notes' => 'بدل هاتف واتصال إداري'],
                ],
                'documents'      => [
                    ['type_id' => $civilIdDoc?->id ?? 2, 'number' => '285041200111', 'issue' => '2022-01-01', 'expiry' => '2027-01-01'],
                    ['type_id' => $degreeDoc?->id ?? 14, 'number' => 'PHD-99812', 'issue' => '2015-06-20', 'expiry' => null],
                ],
            ],
            [
                'code'           => 'EMP-0002',
                'name_ar'        => 'سارة أحمد الشمري',
                'name_en'        => 'Sara Ahmed Al-Shammari',
                'gender'         => Gender::FEMALE->value,
                'job_title_id'   => 2, // معلمة رياض أطفال
                'department_id'  => 3, // قسم الأنشطة والتعليم
                'primary_branch' => $branchAbuFatira->id,
                'other_branches' => [],
                'schedule_id'    => 2,
                'shift_id'       => 1,
                'nationality_id' => $natKuwaiti?->id ?? 75,
                'marital_id'     => $statusSingle?->id ?? 132,
                'gov_id'         => $govMubarak?->id ?? 54,
                'city'           => 'أبو فطيرة',
                'hire_date'      => '2023-09-01',
                'birth_date'     => '1994-08-20',
                'phone'          => '+96599223344',
                'email'          => 'sara.shammari@astro-nursery.com',
                'national_id'    => '294082000222',
                'basic_salary'   => 800.00,
                'notes_ar'       => 'معلمة فصل مرحلة التمهيدي - متخصصة في الأنشطة الإبداعية',
                'allowances'     => [
                    ['type_id' => $natureAllowance?->id ?? 11, 'amount' => 75.00, 'notes' => 'بدل تخصص وأنشطة تفاعلية'],
                ],
                'documents'      => [
                    ['type_id' => $civilIdDoc?->id ?? 2, 'number' => '294082000222', 'issue' => '2023-01-10', 'expiry' => '2028-01-10'],
                ],
            ],
            [
                'code'           => 'EMP-0003',
                'name_ar'        => 'مروة مصطفى النجار',
                'name_en'        => 'Marwa Moustafa El-Naggar',
                'gender'         => Gender::FEMALE->value,
                'job_title_id'   => 2, // معلمة رياض أطفال
                'department_id'  => 3, // قسم الأنشطة والتعليم
                'primary_branch' => $branchZahraa->id,
                'other_branches' => [],
                'schedule_id'    => 2,
                'shift_id'       => 1,
                'nationality_id' => $natEgyptian?->id ?? 81,
                'marital_id'     => $statusMarried?->id ?? 133,
                'gov_id'         => $govHawalli?->id ?? 16,
                'city'           => 'حولي',
                'hire_date'      => '2022-08-20',
                'birth_date'     => '1990-11-15',
                'phone'          => '+96599334455',
                'email'          => 'marwa.naggar@astro-nursery.com',
                'national_id'    => '290111500333',
                'basic_salary'   => 550.00,
                'notes_ar'       => 'معلمة لغة إنجليزية وصوتيات للأطفال',
                'allowances'     => [
                    ['type_id' => $housingAllowance?->id ?? 1, 'amount' => 120.00, 'notes' => 'بدل سكن'],
                    ['type_id' => $transportAllowance?->id ?? 9, 'amount' => 40.00, 'notes' => 'بدل مواصلات'],
                ],
                'documents'      => [
                    ['type_id' => $passportDoc?->id ?? 13, 'number' => 'A18293841', 'issue' => '2021-05-01', 'expiry' => '2028-04-30'],
                    ['type_id' => $civilIdDoc?->id ?? 2, 'number' => '290111500333', 'issue' => '2022-09-01', 'expiry' => '2026-09-01'],
                ],
            ],
            [
                'code'           => 'EMP-0004',
                'name_ar'        => 'زينب حسن الموسوي',
                'name_en'        => 'Zainab Hassan Al-Mousawi',
                'gender'         => Gender::FEMALE->value,
                'job_title_id'   => 3, // ممرضة أطفال
                'department_id'  => 5, // قسم التمريض والرعاية
                'primary_branch' => $branchAbuFatira->id,
                'other_branches' => [],
                'schedule_id'    => 1,
                'shift_id'       => 1,
                'nationality_id' => $natBahraini?->id ?? 79,
                'marital_id'     => $statusSingle?->id ?? 132,
                'gov_id'         => $govMubarak?->id ?? 54,
                'city'           => 'صباح السالم',
                'hire_date'      => '2023-04-10',
                'birth_date'     => '1992-03-05',
                'phone'          => '+96599445566',
                'email'          => 'zainab.mousawi@astro-nursery.com',
                'national_id'    => '292030500444',
                'basic_salary'   => 650.00,
                'notes_ar'       => 'المسؤولة الصحية ومتابعة النظافة والفحص اليومي للأطفال',
                'allowances'     => [
                    ['type_id' => $natureAllowance?->id ?? 11, 'amount' => 60.00, 'notes' => 'بدل طبيعة عمل تمريضي'],
                ],
                'documents'      => [
                    ['type_id' => $degreeDoc?->id ?? 14, 'number' => 'NURSE-4432', 'issue' => '2016-07-15', 'expiry' => null],
                ],
            ],
            [
                'code'           => 'EMP-0005',
                'name_ar'        => 'فهد سليمان الدوسري',
                'name_en'        => 'Fahad Sulaiman Al-Dosari',
                'gender'         => Gender::MALE->value,
                'job_title_id'   => 4, // مسؤول شؤون موظفين
                'department_id'  => 8, // قسم شؤون الموظفين
                'primary_branch' => $branchZahraa->id,
                'other_branches' => [$branchAbuFatira->id],
                'schedule_id'    => 1,
                'shift_id'       => 1,
                'nationality_id' => $natSaudi?->id ?? 76,
                'marital_id'     => $statusMarried?->id ?? 133,
                'gov_id'         => $govHawalli?->id ?? 16,
                'city'           => 'السالمية',
                'hire_date'      => '2023-02-01',
                'birth_date'     => '1988-07-19',
                'phone'          => '+96599556677',
                'email'          => 'fahad.dosari@astro-nursery.com',
                'national_id'    => '288071900555',
                'basic_salary'   => 850.00,
                'notes_ar'       => 'مسؤول شؤون الموظفين والعقود والإقامات لفرعي الحضانة',
                'allowances'     => [
                    ['type_id' => $housingAllowance?->id ?? 1, 'amount' => 150.00, 'notes' => 'بدل سكن شهري'],
                    ['type_id' => $phoneAllowance?->id ?? 10, 'amount' => 25.00, 'notes' => 'بدل اتصالات'],
                ],
                'documents'      => [
                    ['type_id' => $civilIdDoc?->id ?? 2, 'number' => '288071900555', 'issue' => '2023-02-01', 'expiry' => '2028-02-01'],
                    ['type_id' => $contractDoc?->id ?? 12, 'number' => 'CNT-2023-05', 'issue' => '2023-02-01', 'expiry' => '2027-02-01'],
                ],
            ],
            [
                'code'           => 'EMP-0006',
                'name_ar'        => 'محمود عبد الرحيم علي',
                'name_en'        => 'Mahmoud Abdel-Rahim Ali',
                'gender'         => Gender::MALE->value,
                'job_title_id'   => 5, // محاسب مالي
                'department_id'  => 10, // قسم الحسابات والرسوم
                'primary_branch' => $branchAbuFatira->id,
                'other_branches' => [$branchZahraa->id],
                'schedule_id'    => 1,
                'shift_id'       => 1,
                'nationality_id' => $natEgyptian?->id ?? 81,
                'marital_id'     => $statusMarried?->id ?? 133,
                'gov_id'         => $govMubarak?->id ?? 54,
                'city'           => 'القرين',
                'hire_date'      => '2022-10-15',
                'birth_date'     => '1987-12-08',
                'phone'          => '+96599667788',
                'email'          => 'mahmoud.ali@astro-nursery.com',
                'national_id'    => '287120800666',
                'basic_salary'   => 650.00,
                'notes_ar'       => 'المحاسب المالي المسؤول عن تحصيل اشتراكات ورسوم الحضانة وإعداد كشوف الرواتب',
                'allowances'     => [
                    ['type_id' => $housingAllowance?->id ?? 1, 'amount' => 120.00, 'notes' => 'بدل سكن'],
                    ['type_id' => $transportAllowance?->id ?? 9, 'amount' => 40.00, 'notes' => 'بدل مواصلات بين الفروع'],
                ],
                'documents'      => [
                    ['type_id' => $civilIdDoc?->id ?? 2, 'number' => '287120800666', 'issue' => '2022-10-01', 'expiry' => '2026-10-01'],
                    ['type_id' => $passportDoc?->id ?? 13, 'number' => 'P99281726', 'issue' => '2020-03-10', 'expiry' => '2027-03-09'],
                ],
            ],
            [
                'code'           => 'EMP-0007',
                'name_ar'        => 'مها راشد الفضلي',
                'name_en'        => 'Maha Rashed Al-Fadhli',
                'gender'         => Gender::FEMALE->value,
                'job_title_id'   => 6, // منسق إداري عام
                'department_id'  => 1, // الإدارة التعليمية والتربوية
                'primary_branch' => $branchAbuFatira->id,
                'other_branches' => [],
                'schedule_id'    => 1,
                'shift_id'       => 1,
                'nationality_id' => $natKuwaiti?->id ?? 75,
                'marital_id'     => $statusSingle?->id ?? 132,
                'gov_id'         => $govMubarak?->id ?? 54,
                'city'           => 'أبو فطيرة',
                'hire_date'      => '2024-01-02',
                'birth_date'     => '1996-05-14',
                'phone'          => '+96599778899',
                'email'          => 'maha.fadhli@astro-nursery.com',
                'national_id'    => '296051400777',
                'basic_salary'   => 700.00,
                'notes_ar'       => 'منسقة العلاقات العامة واستقبال أولياء الأمور وتسجيل الأطفال الجدد',
                'allowances'     => [],
                'documents'      => [
                    ['type_id' => $civilIdDoc?->id ?? 2, 'number' => '296051400777', 'issue' => '2023-05-01', 'expiry' => '2028-05-01'],
                ],
            ],
            [
                'code'           => 'EMP-0008',
                'name_ar'        => 'عبد الله ناصر المطيري',
                'name_en'        => 'Abdullah Nasser Al-Mutairi',
                'gender'         => Gender::MALE->value,
                'job_title_id'   => 7, // مساعد دعم لوجستي
                'department_id'  => 11, // قسم الخدمات والصيانة
                'primary_branch' => $branchZahraa->id,
                'other_branches' => [$branchAbuFatira->id],
                'schedule_id'    => 1,
                'shift_id'       => 1,
                'nationality_id' => $natKuwaiti?->id ?? 75,
                'marital_id'     => $statusMarried?->id ?? 133,
                'gov_id'         => $govHawalli?->id ?? 16,
                'city'           => 'الزهراء',
                'hire_date'      => '2023-06-01',
                'birth_date'     => '1991-09-25',
                'phone'          => '+96599889900',
                'email'          => 'abdullah.mutairi@astro-nursery.com',
                'national_id'    => '291092500888',
                'basic_salary'   => 500.00,
                'notes_ar'       => 'مسؤول الصيانة العامة والمشتريات ومتابعة أمن وسلامة مرافق الحضانة',
                'allowances'     => [
                    ['type_id' => $transportAllowance?->id ?? 9, 'amount' => 50.00, 'notes' => 'بدل حركة وانتقال'],
                ],
                'documents'      => [
                    ['type_id' => $civilIdDoc?->id ?? 2, 'number' => '291092500888', 'issue' => '2023-06-01', 'expiry' => '2028-06-01'],
                ],
            ],
        ];

        foreach ($employeesData as $data) {
            $employee = Employee::where('employee_code', $data['code'])->first();

            if (!$employee) {
                $employee = new Employee();
            }

            $employee->fill([
                'employee_code'      => $data['code'],
                'gender'             => $data['gender'],
                'job_title_id'       => $data['job_title_id'],
                'department_id'      => $data['department_id'],
                'primary_branch_id'  => $data['primary_branch'],
                'work_schedule_id'   => $data['schedule_id'],
                'shift_id'           => $data['shift_id'],
                'employment_type'    => EmploymentType::FULL_TIME->value,
                'status'             => EmployeeStatus::ACTIVE->value,
                'hire_date'          => $data['hire_date'],
                'date_of_birth'      => $data['birth_date'],
                'phone'              => $data['phone'],
                'email'              => $data['email'],
                'national_id'        => $data['national_id'],
                'nationality'        => $data['nationality_id'],
                'marital_status'     => $data['marital_id'],
                'religion'           => $relMuslim?->id ?? 100,
                'governorate_id'     => $data['gov_id'],
                'city'               => $data['city'],
                'basic_salary'       => $data['basic_salary'],
                'hourly_rate'        => round($data['basic_salary'] / (30 * 8), 2),
                'daily_working_hours'=> 8,
                'created_by'         => $adminId,
            ]);

            $employee->translateOrNew('ar')->name = $data['name_ar'];
            $employee->translateOrNew('ar')->notes = $data['notes_ar'];
            $employee->translateOrNew('ar')->address_1 = $data['city'] . ' - دولة الكويت';

            $employee->translateOrNew('en')->name = $data['name_en'];
            $employee->translateOrNew('en')->notes = $data['notes_ar'];
            $employee->translateOrNew('en')->address_1 = $data['city'] . ' - State of Kuwait';

            $employee->save();

            // Sync Branches
            $branchSync = [
                $data['primary_branch'] => ['is_primary' => true],
            ];
            foreach ($data['other_branches'] as $otherB) {
                $branchSync[$otherB] = ['is_primary' => false];
            }
            $employee->branches()->sync($branchSync);

            // Allowances
            $employee->allowances()->delete();
            foreach ($data['allowances'] as $alw) {
                EmployeeAllowance::create([
                    'employee_id'       => $employee->id,
                    'allowance_type_id' => $alw['type_id'],
                    'amount'            => $alw['amount'],
                    'is_percentage'     => false,
                    'is_recurring'      => true,
                    'effective_date'    => $data['hire_date'],
                    'notes'             => $alw['notes'],
                ]);
            }

            // Documents
            $employee->documents()->delete();
            foreach ($data['documents'] as $doc) {
                EmployeeDocument::create([
                    'employee_id'        => $employee->id,
                    'document_type_id'   => $doc['type_id'],
                    'document_number'    => $doc['number'],
                    'file_path'          => 'uploads/documents/sample_document.pdf',
                    'file_name'          => 'official_document.pdf',
                    'issue_date'         => $doc['issue'],
                    'expiry_date'        => $doc['expiry'],
                    'notify_before_days' => 30,
                    'notes'              => 'مستند رسمي معتمد',
                ]);
            }
        }
    }
}
