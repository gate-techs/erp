<?php

namespace Modules\HR\Database\Seeders;

use App\Enums\UserType;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\HR\Enums\EmployeeStatus;
use Modules\HR\Enums\EmploymentType;
use Modules\HR\Enums\Gender;
use Modules\HR\Enums\JobTitleStatus;
use Modules\HR\Models\Department;
use Modules\HR\Models\Employee;
use Modules\HR\Models\JobTitle;
use Modules\HR\Models\Shift;
use Modules\HR\Models\WorkSchedule;
use Modules\Nursery\Models\NurseryBranch;
use Modules\Settings\Enums\GeneralSettingType;
use Modules\Settings\Models\Bank;
use Modules\Settings\Models\GeneralSetting;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Spatie\Permission\Models\Role;

class EmployeeFromExcelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filePath = base_path('file/for upload.xlsx');

        if (!file_exists($filePath)) {
            $this->command->error("Excel file not found at: {$filePath}");
            return;
        }

        $this->command->info("Loading Excel file from {$filePath}...");
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        // Remove header row
        $header = array_shift($rows);

        // Filter valid rows having employee name
        $employeeRows = [];
        foreach ($rows as $row) {
            $name = trim($row[1] ?? '');
            if (!empty($name)) {
                $employeeRows[] = $row;
            }
        }

        $totalEmployees = count($employeeRows);
        $this->command->info("Found {$totalEmployees} employees in Excel.");

        // 1. Seed Nationalities from Excel
        $this->seedNationalities($employeeRows);

        // 2. Seed Banks from Excel
        $this->seedBanks($employeeRows);

        // 3. Seed Job Titles from Excel
        $this->seedJobTitles($employeeRows);

        // 4. Seed Employees and Users
        $this->seedEmployees($employeeRows);

        $this->command->info("Employee import completed successfully!");
    }

    /**
     * Extract and seed unique Nationalities from Excel
     */
    protected function seedNationalities(array $rows): void
    {
        $this->command->info("Seeding Nationalities from Excel...");

        $dictionary = [
            'tunis' => ['code' => 'TUN', 'ar' => 'تونسي', 'en' => 'Tunisian'],
            'pakistan' => ['code' => 'PAK', 'ar' => 'باكستاني', 'en' => 'Pakistani'],
            'jordan' => ['code' => 'JOR', 'ar' => 'أردني', 'en' => 'Jordanian'],
            'non kuwait' => ['code' => 'NON_KWT', 'ar' => 'غير كويتي (بدون)', 'en' => 'Non-Kuwaiti (Bedoon)'],
            'lebanon' => ['code' => 'LBN', 'ar' => 'لبناني', 'en' => 'Lebanese'],
            'egypt' => ['code' => 'EGY', 'ar' => 'مصري', 'en' => 'Egyptian'],
            'philippines' => ['code' => 'PHL', 'ar' => 'فلبيني', 'en' => 'Filipino'],
            'indian' => ['code' => 'IND', 'ar' => 'هندي', 'en' => 'Indian'],
            'saudi' => ['code' => 'SAU', 'ar' => 'سعودي', 'en' => 'Saudi'],
            'nigeria' => ['code' => 'NGA', 'ar' => 'نيجيري', 'en' => 'Nigerian'],
            'srilanka' => ['code' => 'LKA', 'ar' => 'سريلانكي', 'en' => 'Sri Lankan'],
            'syria' => ['code' => 'SYR', 'ar' => 'سوري', 'en' => 'Syrian'],
            'south africa' => ['code' => 'ZAF', 'ar' => 'جنوب أفريقي', 'en' => 'South African'],
            'senegal' => ['code' => 'SEN', 'ar' => 'سنغالي', 'en' => 'Senegalese'],
            'ghana' => ['code' => 'GHA', 'ar' => 'غاني', 'en' => 'Ghanaian'],
            'sudan' => ['code' => 'SDN', 'ar' => 'سوداني', 'en' => 'Sudanese'],
        ];

        $uniqueNats = [];
        foreach ($rows as $row) {
            $natRaw = trim($row[3] ?? '');
            if (!empty($natRaw)) {
                $uniqueNats[strtolower($natRaw)] = $natRaw;
            }
        }

        foreach ($uniqueNats as $key => $rawName) {
            $meta = $dictionary[$key] ?? [
                'code' => strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $rawName), 0, 3)),
                'ar' => $rawName,
                'en' => $rawName,
            ];

            $setting = GeneralSetting::where('type', GeneralSettingType::NATIONALITY)
                ->where('value', $meta['code'])
                ->first();

            if (!$setting) {
                $setting = GeneralSetting::create([
                    'type' => GeneralSettingType::NATIONALITY,
                    'value' => $meta['code'],
                    'ar' => ['name' => $meta['ar']],
                    'en' => ['name' => $meta['en']],
                ]);
            } else {
                $setting->translateOrNew('ar')->name = $meta['ar'];
                $setting->translateOrNew('en')->name = $meta['en'];
                $setting->save();
            }
        }

        $this->command->info("Seeded " . count($uniqueNats) . " nationalities.");
    }

    /**
     * Extract and seed unique Banks from Excel (ignoring CASH)
     */
    protected function seedBanks(array $rows): void
    {
        $this->command->info("Seeding Banks from Excel...");

        $dictionary = [
            'kfh' => [
                'code' => 'KFH',
                'swift_code' => 'KFHOKWKW',
                'ar' => ['name' => 'بيت التمويل الكويتي (بيتك)', 'address' => 'مدينة الكويت - برج بيتك'],
                'en' => ['name' => 'Kuwait Finance House (KFH)', 'address' => 'Kuwait City - Baitak Tower'],
            ],
            'nbk' => [
                'code' => 'NBK',
                'swift_code' => 'NBKOKWKW',
                'ar' => ['name' => 'بنك الكويت الوطني', 'address' => 'مدينة الكويت - برج الراية'],
                'en' => ['name' => 'National Bank of Kuwait (NBK)', 'address' => 'Kuwait City - Al Raya Tower'],
            ],
            'gulf' => [
                'code' => 'GBK',
                'swift_code' => 'GULFBKKW',
                'ar' => ['name' => 'بنك الخليج', 'address' => 'مدينة الكويت - مبارك الكبير'],
                'en' => ['name' => 'Gulf Bank', 'address' => 'Kuwait City - Mubarak Al Kabeer'],
            ],
            'burgan' => [
                'code' => 'BURGAN',
                'swift_code' => 'BRGNKWKW',
                'ar' => ['name' => 'بنك برقان', 'address' => 'مدينة الكويت - برج برقان'],
                'en' => ['name' => 'Burgan Bank', 'address' => 'Kuwait City - Burgan Tower'],
            ],
            'warba' => [
                'code' => 'WARBA',
                'swift_code' => 'WARBAKKW',
                'ar' => ['name' => 'بنك وربة', 'address' => 'مدينة الكويت - برج الراية'],
                'en' => ['name' => 'Warba Bank', 'address' => 'Kuwait City - Al Raya Tower'],
            ],
            'commercial' => [
                'code' => 'CBK',
                'swift_code' => 'CBKWKWKW',
                'ar' => ['name' => 'البنك التجاري الكويتي', 'address' => 'مدينة الكويت - شارع مبارك الكبير'],
                'en' => ['name' => 'Commercial Bank of Kuwait (CBK)', 'address' => 'Kuwait City - Mubarak Al Kabeer St'],
            ],
        ];

        $uniqueBanks = [];
        foreach ($rows as $row) {
            $bankRaw = trim($row[15] ?? '');
            if (!empty($bankRaw) && strtoupper($bankRaw) !== 'CASH') {
                $uniqueBanks[strtolower($bankRaw)] = $bankRaw;
            }
        }

        foreach ($uniqueBanks as $key => $rawName) {
            $data = $dictionary[$key] ?? [
                'code' => strtoupper($rawName),
                'swift_code' => null,
                'ar' => ['name' => $rawName, 'address' => 'دولة الكويت'],
                'en' => ['name' => $rawName, 'address' => 'State of Kuwait'],
            ];

            $bank = Bank::where('code', $data['code'])->first();
            if (!$bank) {
                Bank::create([
                    'code' => $data['code'],
                    'swift_code' => $data['swift_code'],
                    'status' => 'active',
                    'ar' => $data['ar'],
                    'en' => $data['en'],
                ]);
            } else {
                $bank->update([
                    'swift_code' => $data['swift_code'],
                    'status' => 'active',
                ]);
                $bank->translateOrNew('ar')->name = $data['ar']['name'];
                $bank->translateOrNew('en')->name = $data['en']['name'];
                $bank->save();
            }
        }

        $this->command->info("Seeded " . count($uniqueBanks) . " banks.");
    }

    /**
     * Extract and seed unique Job Titles from Excel
     */
    protected function seedJobTitles(array $rows): void
    {
        $this->command->info("Seeding Job Titles from Excel...");

        $dictionary = [
            'c.e.o' => [
                'code' => 'JT-CEO',
                'dept_code' => 'EDU',
                'ar' => ['name' => 'الرئيس التنفيذي', 'description' => 'الرئيس التنفيذي للأكاديمية'],
                'en' => ['name' => 'C.E.O', 'description' => 'Chief Executive Officer'],
            ],
            'human resource' => [
                'code' => 'JT-HR',
                'dept_code' => 'HR',
                'ar' => ['name' => 'مسؤول الموارد البشرية', 'description' => 'إدارة شؤون الموظفين والتوظيف'],
                'en' => ['name' => 'Human Resources', 'description' => 'HR management and personnel'],
            ],
            'photo & video grapher' => [
                'code' => 'JT-VIDEOGRAPHER',
                'dept_code' => 'EDU-ACT',
                'ar' => ['name' => 'مصور فوتوغرافي وفيديو', 'description' => 'توثيق الفعاليات والأنشطة والإنتاج المرئي'],
                'en' => ['name' => 'Photo & Video Grapher', 'description' => 'Events documentation and media production'],
            ],
            'government relations' => [
                'code' => 'JT-GOV-REL',
                'dept_code' => 'HR-PERS',
                'ar' => ['name' => 'مسؤول علاقات حكومية', 'description' => 'متابعة المعاملات الحكومية والإقامات والتراخيص'],
                'en' => ['name' => 'Government Relations', 'description' => 'Government affairs, visas and permits'],
            ],
            'branch manager' => [
                'code' => 'JT-BRANCH-MGR',
                'dept_code' => 'EDU',
                'ar' => ['name' => 'مديرة فرع', 'description' => 'إدارة وتسيير أعمال الفرع'],
                'en' => ['name' => 'Branch Manager', 'description' => 'Branch management and operations'],
            ],
            'i.t - part time' => [
                'code' => 'JT-IT-PT',
                'dept_code' => 'EDU',
                'ar' => ['name' => 'مسؤول تكنولوجيا المعلومات (دوام جزئي)', 'description' => 'الدعم الفني والتقني والشبكات'],
                'en' => ['name' => 'I.T - Part Time', 'description' => 'IT support and network management'],
            ],
            'accountant part time' => [
                'code' => 'JT-ACC-PT',
                'dept_code' => 'FIN-ACC',
                'ar' => ['name' => 'محاسب (دوام جزئي)', 'description' => 'إدارة السجلات المحاسبية والمالية'],
                'en' => ['name' => 'Accountant Part Time', 'description' => 'Accounting and financial records'],
            ],
            'maintainance' => [
                'code' => 'JT-MAINT',
                'dept_code' => 'FIN-SERV',
                'ar' => ['name' => 'فني صيانة', 'description' => 'أعمال الصيانة العامة ومتابعة المرافق'],
                'en' => ['name' => 'Maintenance', 'description' => 'General maintenance and facilities'],
            ],
            'nany / children caretaker' => [
                'code' => 'JT-NANY',
                'dept_code' => 'EDU-CARE',
                'ar' => ['name' => 'مربية / رعاية أطفال', 'description' => 'رعاية الأطفال والاهتمام اليومي بهم'],
                'en' => ['name' => 'Nanny / Children Caretaker', 'description' => 'Childcare and daily supervision'],
            ],
            'english teacher kg2' => [
                'code' => 'JT-ENG-KG2',
                'dept_code' => 'EDU-ACT',
                'ar' => ['name' => 'معلمة لغة إنجليزية (روضة 2)', 'description' => 'تدريس منهج اللغة الإنجليزية لروضة 2'],
                'en' => ['name' => 'English Teacher Kg2', 'description' => 'English teacher for KG2'],
            ],
            'administrative assistant' => [
                'code' => 'JT-ADM-ASST',
                'dept_code' => 'EDU',
                'ar' => ['name' => 'مساعد إداري', 'description' => 'مساعدة الأعمال الإدارية والمكتبية'],
                'en' => ['name' => 'Administrative Assistant', 'description' => 'Administrative and office support'],
            ],
            'arabic teacher' => [
                'code' => 'JT-AR-TEACHER',
                'dept_code' => 'EDU-ACT',
                'ar' => ['name' => 'معلمة لغة عربية', 'description' => 'تدريس منهج اللغة العربية'],
                'en' => ['name' => 'Arabic Teacher', 'description' => 'Arabic language teaching'],
            ],
            'life & social skills teacher' => [
                'code' => 'JT-SKILLS-TEACHER',
                'dept_code' => 'EDU-ACT',
                'ar' => ['name' => 'معلمة مهارات حياتية واجتماعية', 'description' => 'تنمية المهارات السلوكية والحياتية للأطفال'],
                'en' => ['name' => 'Life & Social Skills Teacher', 'description' => 'Teaching life and social skills'],
            ],
            'reception & registrar' => [
                'code' => 'JT-RECEPT-REG',
                'dept_code' => 'EDU',
                'ar' => ['name' => 'موظفة استقبال وتسجيل', 'description' => 'استقبال أولياء الأمور وتسجيل الأطفال الجدد'],
                'en' => ['name' => 'Reception & Registrar', 'description' => 'Reception and child registration'],
            ],
            'class assistant' => [
                'code' => 'JT-CLASS-ASST',
                'dept_code' => 'EDU-ACT',
                'ar' => ['name' => 'مساعدة فصل', 'description' => 'مساعدة المعلمات داخل الفصول وتنظيم الأنشطة'],
                'en' => ['name' => 'Class Assistant', 'description' => 'Classroom and activity assistance'],
            ],
            'english teacher kg1' => [
                'code' => 'JT-ENG-KG1',
                'dept_code' => 'EDU-ACT',
                'ar' => ['name' => 'معلمة لغة إنجليزية (روضة 1)', 'description' => 'تدريس منهج اللغة الإنجليزية لروضة 1'],
                'en' => ['name' => 'English Teacher Kg1', 'description' => 'English teacher for KG1'],
            ],
            'english teacher' => [
                'code' => 'JT-ENG-TEACHER',
                'dept_code' => 'EDU-ACT',
                'ar' => ['name' => 'معلمة لغة إنجليزية', 'description' => 'تدريس منهج اللغة الإنجليزية'],
                'en' => ['name' => 'English Teacher', 'description' => 'English language teacher'],
            ],
            'english teacher toddler' => [
                'code' => 'JT-ENG-TODDLER',
                'dept_code' => 'EDU-ACT',
                'ar' => ['name' => 'معلمة لغة إنجليزية (حضانة - Toddler)', 'description' => 'تعليم اللغة الإنجليزية لفئة الحضانة'],
                'en' => ['name' => 'English Teacher Toddler', 'description' => 'Toddler English teacher'],
            ],
            'teacher pre kg' => [
                'code' => 'JT-TEACHER-PREKG',
                'dept_code' => 'EDU-ACT',
                'ar' => ['name' => 'معلمة تمهيدي (Pre-KG)', 'description' => 'تدريس مرحلة ما قبل الروضة'],
                'en' => ['name' => 'Teacher Pre Kg', 'description' => 'Pre-KG classroom teacher'],
            ],
            'nurse' => [
                'code' => 'JT-NURSE',
                'dept_code' => 'MED-NURSE',
                'ar' => ['name' => 'ممرضة أطفال', 'description' => 'الرعاية الصحية والإسعافات الأولية'],
                'en' => ['name' => 'Nurse', 'description' => 'Healthcare and first aid'],
            ],
            'supervisor arabic language' => [
                'code' => 'JT-SUP-AR',
                'dept_code' => 'EDU-ACT',
                'ar' => ['name' => 'مشرفة لغة عربية', 'description' => 'الإشراف على مناهج ومعلمات اللغة العربية'],
                'en' => ['name' => 'Supervisor Arabic Language', 'description' => 'Arabic language curriculum supervisor'],
            ],
            'arabic teacher / quran' => [
                'code' => 'JT-AR-QURAN',
                'dept_code' => 'EDU-ACT',
                'ar' => ['name' => 'معلمة لغة عربية وقرآن كريم', 'description' => 'تحفيظ القرآن الكريم وتدريس اللغة العربية'],
                'en' => ['name' => 'Arabic Teacher / Quran', 'description' => 'Arabic and Quran teacher'],
            ],
            'toddler teacher' => [
                'code' => 'JT-TODDLER-TEACHER',
                'dept_code' => 'EDU-ACT',
                'ar' => ['name' => 'معلمة فئة Toddler', 'description' => 'تدريس ورعاية فئة صغار الحضانة (Toddler)'],
                'en' => ['name' => 'Toddler Teacher', 'description' => 'Teacher for Toddler stage'],
            ],
            'english language supervisor' => [
                'code' => 'JT-SUP-ENG',
                'dept_code' => 'EDU-ACT',
                'ar' => ['name' => 'مشرفة لغة إنجليزية', 'description' => 'الإشراف على مناهج ومعلمات اللغة الإنجليزية'],
                'en' => ['name' => 'English Language Supervisor', 'description' => 'English language curriculum supervisor'],
            ],
            'security guard' => [
                'code' => 'JT-SECURITY',
                'dept_code' => 'FIN-SERV',
                'ar' => ['name' => 'حارس أمن', 'description' => 'الحراسة وتأمين المداخل ومتابعة دخول وخروج الأطفال'],
                'en' => ['name' => 'Security Guard', 'description' => 'Security, safety and entrance surveillance'],
            ],
            'floor supervisor' => [
                'code' => 'JT-FLOOR-SUP',
                'dept_code' => 'EDU',
                'ar' => ['name' => 'مشرفة أدوار', 'description' => 'الإشراف الميداني على الأدوار والأجنحة وسير العمل'],
                'en' => ['name' => 'Floor Supervisor', 'description' => 'Floor supervisor and workflow coordinator'],
            ],
            'arts teacher' => [
                'code' => 'JT-ARTS-TEACHER',
                'dept_code' => 'EDU-ACT',
                'ar' => ['name' => 'معلمة فنون ورسم', 'description' => 'تدريس الأنشطة الفنية والأشغال اليدوية'],
                'en' => ['name' => 'Arts Teacher', 'description' => 'Arts, crafts and creative activities teacher'],
            ],
            'teacher kg2' => [
                'code' => 'JT-TEACHER-KG2',
                'dept_code' => 'EDU-ACT',
                'ar' => ['name' => 'معلمة روضة 2', 'description' => 'تدريس فصل روضة 2'],
                'en' => ['name' => 'Teacher Kg2', 'description' => 'Teacher for KG2 class'],
            ],
            'teacher arabic' => [
                'code' => 'JT-TEACHER-AR',
                'dept_code' => 'EDU-ACT',
                'ar' => ['name' => 'معلمة لغة عربية', 'description' => 'تدريس اللغة العربية'],
                'en' => ['name' => 'Teacher Arabic', 'description' => 'Arabic teacher'],
            ],
            'teacher mini tots' => [
                'code' => 'JT-TEACHER-MINITOTS',
                'dept_code' => 'EDU-ACT',
                'ar' => ['name' => 'معلمة فئة Mini Tots', 'description' => 'أنشطة ورعاية مرحلة Mini Tots'],
                'en' => ['name' => 'Teacher Mini Tots', 'description' => 'Teacher for Mini Tots class'],
            ],
            'teacher kg1' => [
                'code' => 'JT-TEACHER-KG1',
                'dept_code' => 'EDU-ACT',
                'ar' => ['name' => 'معلمة روضة 1', 'description' => 'تدريس فصل روضة 1'],
                'en' => ['name' => 'Teacher Kg1', 'description' => 'Teacher for KG1 class'],
            ],
        ];

        $uniqueJobs = [];
        foreach ($rows as $row) {
            $jobRaw = trim($row[8] ?? '');
            if (!empty($jobRaw)) {
                $uniqueJobs[strtolower($jobRaw)] = $jobRaw;
            }
        }

        foreach ($uniqueJobs as $key => $rawName) {
            $meta = $dictionary[$key] ?? [
                'code' => 'JT-' . strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $rawName), 0, 10)),
                'dept_code' => null,
                'ar' => ['name' => $rawName, 'description' => $rawName],
                'en' => ['name' => $rawName, 'description' => $rawName],
            ];

            $dept = null;
            if (!empty($meta['dept_code'])) {
                $dept = Department::where('code', $meta['dept_code'])->first();
            }

            $job = JobTitle::where('code', $meta['code'])->first();
            if (!$job) {
                JobTitle::create([
                    'code' => $meta['code'],
                    'department_id' => $dept?->id,
                    'status' => JobTitleStatus::ACTIVE->value,
                    'ar' => $meta['ar'],
                    'en' => $meta['en'],
                ]);
            } else {
                $job->update([
                    'department_id' => $dept?->id ?? $job->department_id,
                    'status' => JobTitleStatus::ACTIVE->value,
                ]);
                $job->translateOrNew('ar')->name = $meta['ar']['name'];
                $job->translateOrNew('ar')->description = $meta['ar']['description'];
                $job->translateOrNew('en')->name = $meta['en']['name'];
                $job->translateOrNew('en')->description = $meta['en']['description'];
                $job->save();
            }
        }

        $this->command->info("Seeded " . count($uniqueJobs) . " job titles.");
    }

    /**
     * Seed all employees and create their User accounts
     */
    protected function seedEmployees(array $rows): void
    {
        $this->command->info("Seeding Employees and Users...");

        $adminId = User::first()?->id ?? 1;

        // Fetch Nursery Branches
        $branchAbuFatira = NurseryBranch::whereTranslationLike('name', '%أبو فطيرة%')
            ->orWhereTranslationLike('name', '%ابو فطيرة%')
            ->first();
        $branchZahra = NurseryBranch::whereTranslationLike('name', '%الزهراء%')->first();

        // Default WorkSchedule and Shift
        $defaultSchedule = WorkSchedule::first();
        $defaultShift = Shift::first();

        // Spatie Employee Role
        $employeeRole = Role::where('name', 'employee')->first();

        // Cache lookup maps
        $banksMap = Bank::all()->keyBy(fn ($b) => strtoupper($b->code));
        $jobsMap = JobTitle::with('translations')->get();
        $nationalitiesMap = GeneralSetting::ofType(GeneralSettingType::NATIONALITY)->get();

        // Track first names to handle duplicates according to user rule:
        // firstname@astroleaderskw.com, and if duplicate -> firstname_lastname@astroleaderskw.com
        $firstNameCounts = [];
        foreach ($rows as $r) {
            $fullName = trim($r[1] ?? '');
            if (!$fullName) continue;
            $parts = preg_split('/\s+/', $fullName);
            $fn = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $parts[0] ?? ''));
            if ($fn) {
                $firstNameCounts[$fn] = ($firstNameCounts[$fn] ?? 0) + 1;
            }
        }

        $usedEmails = [];
        $ptCounter = 1;

        foreach ($rows as $index => $row) {
            $rawEmpId     = trim($row[0] ?? '');
            $fullName     = trim($row[1] ?? '');
            $rawGender    = trim($row[2] ?? '');
            $rawNat       = trim($row[3] ?? '');
            $rawCompany   = trim($row[4] ?? '');
            $rawCivilId   = trim($row[5] ?? '');
            $rawPassport  = trim($row[6] ?? '');
            $rawDept      = trim($row[7] ?? '');
            $rawJob       = trim($row[8] ?? '');
            $rawJoinDate  = trim($row[9] ?? '');
            $rawSalary    = trim($row[10] ?? '');
            $rawPhone     = trim($row[11] ?? '');
            $rawDob       = trim($row[12] ?? '');
            $rawArticle   = trim($row[13] ?? '');
            $rawIban      = trim($row[14] ?? '');
            $rawBank      = trim($row[15] ?? '');

            if (empty($fullName)) {
                continue;
            }

            // Employee Code
            if (empty($rawEmpId) || strtoupper($rawEmpId) === 'P.T') {
                $empCode = 'PT-' . str_pad($ptCounter++, 3, '0', STR_PAD_LEFT);
            } else {
                $empCode = $rawEmpId;
            }

            // Name parts for email
            $nameParts = preg_split('/\s+/', $fullName);
            $rawFirst = $nameParts[0] ?? '';
            $rawLast = count($nameParts) > 1 ? end($nameParts) : '';

            $cleanFirst = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $rawFirst));
            $cleanLast = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $rawLast));

            // Determine email according to duplicate rule:
            if (($firstNameCounts[$cleanFirst] ?? 0) > 1) {
                // Duplicated first name -> firstname_lastname@astroleaderskw.com
                $emailPrefix = !empty($cleanLast) ? "{$cleanFirst}_{$cleanLast}" : "{$cleanFirst}_{$empCode}";
            } else {
                // Unique first name -> firstname@astroleaderskw.com
                $emailPrefix = $cleanFirst;
            }

            $email = "{$emailPrefix}@astroleaderskw.com";
            // Extra safety in case duplicate firstname AND lastname
            if (isset($usedEmails[$email])) {
                $email = "{$cleanFirst}_{$cleanLast}_{$empCode}@astroleaderskw.com";
            }
            $usedEmails[$email] = true;

            // Teacher Flag: "يشمل معلمات Teacher وأيضاً مساعدي الفصول Class Assistant"
            $isTeacher = (stripos($rawJob, 'teacher') !== false || stripos($rawJob, 'class assistant') !== false);

            // User Type
            if ($isTeacher) {
                $userType = UserType::TEACHER;
            } elseif (stripos($rawJob, 'nurse') !== false) {
                $userType = UserType::NURSE;
            } elseif (stripos($rawJob, 'supervisor') !== false) {
                $userType = UserType::SUPERVISOR;
            } else {
                $userType = UserType::EMPLOYEE;
            }

            // Create / Update User Account
            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $fullName,
                    'password' => Hash::make('astro@123'),
                    'user_type' => $userType,
                    'email_verified_at' => now(),
                    'is_active' => true,
                    'locale' => 'ar',
                ]
            );

            if ($employeeRole && !$user->hasRole('employee')) {
                $user->assignRole($employeeRole);
            }

            // Job Title Matching
            $jobTitleId = null;
            $departmentId = null;
            if (!empty($rawJob)) {
                $matchedJob = $jobsMap->first(function ($jt) use ($rawJob) {
                    return strcasecmp($jt->translate('en')?->name ?? '', $rawJob) === 0
                        || strcasecmp($jt->code, $rawJob) === 0;
                });
                if ($matchedJob) {
                    $jobTitleId = $matchedJob->id;
                    $departmentId = $matchedJob->department_id;
                }
            }

            // Branch Assignment
            $primaryBranchId = $branchAbuFatira?->id;
            $otherBranchIds = [];

            if (stripos($rawDept, 'zahra') !== false) {
                $primaryBranchId = $branchZahra?->id ?? $primaryBranchId;
            } elseif (stripos($rawDept, 'both') !== false) {
                $primaryBranchId = $branchAbuFatira?->id;
                if ($branchZahra) {
                    $otherBranchIds[] = $branchZahra->id;
                }
            } elseif (stripos($rawDept, 'abu fatira') !== false) {
                $primaryBranchId = $branchAbuFatira?->id;
            }

            // Bank Matching (null if CASH or empty)
            $bankId = null;
            if (!empty($rawBank) && strtoupper($rawBank) !== 'CASH') {
                $bCode = strtoupper($rawBank);
                if ($bCode === 'COMMERCIAL') $bCode = 'CBK';
                if ($bCode === 'GULF') $bCode = 'GBK';
                $bankObj = $banksMap->get($bCode);
                $bankId = $bankObj?->id;
            }

            // Nationality Matching
            $nationalityVal = null;
            if (!empty($rawNat)) {
                $matchedNat = $nationalitiesMap->first(function ($n) use ($rawNat) {
                    return strcasecmp($n->translate('en')?->name ?? '', $rawNat) === 0
                        || strcasecmp($n->translate('ar')?->name ?? '', $rawNat) === 0
                        || strcasecmp($n->value, $rawNat) === 0;
                });
                $nationalityVal = $matchedNat?->value ?? strtoupper(substr($rawNat, 0, 3));
            }

            // Dates & Civil ID
            $birthDate = $this->parseDate($rawDob);
            $hireDate = $this->parseDate($rawJoinDate) ?? '2023-09-01';
            $salary = floatval(preg_replace('/[^0-9.]/', '', $rawSalary)) ?: 0.00;
            $phone = trim($rawPhone) ?: '00000000';
            $civilId = trim($rawCivilId) ?: null;
            $passport = trim($rawPassport) ?: null;
            $gender = strtolower($rawGender) === 'female' ? Gender::FEMALE->value : Gender::MALE->value;

            // Employment Type
            if (stripos($rawEmpId, 'p.t') !== false || stripos($rawJob, 'part time') !== false) {
                $employmentType = EmploymentType::PART_TIME->value;
            } elseif (stripos($rawCompany, 'contract') !== false) {
                $employmentType = EmploymentType::CONTRACT->value;
            } else {
                $employmentType = EmploymentType::FULL_TIME->value;
            }

            // Find or Create Employee
            $employee = Employee::where('employee_code', $empCode)->first() ?? new Employee();

            $employee->fill([
                'employee_code'       => $empCode,
                'is_teacher'          => $isTeacher,
                'user_id'             => $user->id,
                'gender'              => $gender,
                'job_title_id'        => $jobTitleId,
                'department_id'       => $departmentId,
                'primary_branch_id'   => $primaryBranchId,
                'work_schedule_id'    => $defaultSchedule?->id,
                'shift_id'            => $defaultShift?->id,
                'employment_type'     => $employmentType,
                'status'              => EmployeeStatus::ACTIVE->value,
                'hire_date'           => $hireDate,
                'date_of_birth'       => $birthDate,
                'phone'               => $phone,
                'email'               => $email,
                'national_id'         => $civilId,
                'passport_number'     => $passport,
                'nationality'         => $nationalityVal,
                'bank_id'             => $bankId,
                'iban'                => !empty($rawIban) ? $rawIban : null,
                'basic_salary'        => $salary,
                'hourly_rate'         => round($salary / (30 * 8), 2),
                'daily_working_hours' => 8,
                'city'                => 'مدينة الكويت',
                'created_by'          => $adminId,
            ]);

            $employee->translateOrNew('ar')->name = $fullName;
            $employee->translateOrNew('ar')->notes = "تم الاستيراد من ملف Excel ({$rawCompany}) - المسمى: {$rawJob}";
            $employee->translateOrNew('ar')->address_1 = 'دولة الكويت';

            $employee->translateOrNew('en')->name = $fullName;
            $employee->translateOrNew('en')->notes = "Imported from Excel ({$rawCompany}) - Title: {$rawJob}";
            $employee->translateOrNew('en')->address_1 = 'State of Kuwait';

            $employee->save();

            // Sync Branches
            if ($primaryBranchId) {
                $branchSync = [
                    $primaryBranchId => ['is_primary' => true],
                ];
                foreach ($otherBranchIds as $otherId) {
                    $branchSync[$otherId] = ['is_primary' => false];
                }
                $employee->branches()->sync($branchSync);
            }
        }

        $count = count($rows);
        $this->command->info("Created/Updated {$count} employee records and login accounts.");
    }

    /**
     * Parse date safely from multiple Excel formats
     */
    protected function parseDate(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        $value = trim($value);

        // Numeric Excel date serial
        if (is_numeric($value) && (int)$value > 10000) {
            try {
                return ExcelDate::excelToDateTimeObject((int)$value)->format('Y-m-d');
            } catch (\Throwable $e) {
                // continue to other parsing
            }
        }

        // Common text date formats
        $formats = ['d/m/Y', 'j/n/Y', 'Y-m-d', 'm/d/Y', 'd-m-Y'];
        foreach ($formats as $format) {
            $parsed = DateTime::createFromFormat($format, $value);
            if ($parsed && $parsed->format($format) === $value) {
                return $parsed->format('Y-m-d');
            }
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }
}
