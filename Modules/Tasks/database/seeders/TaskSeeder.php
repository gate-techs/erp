<?php

namespace Modules\Tasks\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\HR\Models\Department;
use Modules\HR\Models\Employee;
use Modules\Tasks\Enums\TaskPriority;
use Modules\Tasks\Enums\TaskStatus;
use Modules\Tasks\Models\Task;
use Modules\Tasks\Models\TaskGroup;
use Modules\Tasks\Models\TaskNote;
use Modules\Tasks\Models\TaskStatusHistory;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::first() ?? User::factory()->create([
            'name'     => 'System Admin',
            'username' => 'admin',
            'email'    => 'admin@gate.test',
        ]);

        $employees = Employee::query()->take(15)->get();
        $departments = Department::query()->take(6)->get();

        // 1. Ensure Task Groups exist
        $groupsData = [
            [
                'ar' => [
                    'name' => 'مشروع تطوير المنصة الرقمية وبوابة الموظفين',
                    'description' => 'مجموعة عمل لمتابعة وتطوير البوابة الإلكترونية وتطبيقات الجوال والربط مع أنظمة الـ ERP.',
                ],
                'en' => [
                    'name' => 'Digital Platform & Employee Portal Project',
                    'description' => 'Working group for web portal and mobile app development and ERP integration.',
                ],
                'department_id' => $departments->first()?->id,
                'start_date' => now()->subDays(20)->format('Y-m-d'),
                'end_date' => now()->addDays(70)->format('Y-m-d'),
                'is_active' => true,
                'created_by' => $admin->id,
            ],
            [
                'ar' => [
                    'name' => 'إعداد الميزانية السنوية والتقارير المالية 2026',
                    'description' => 'إعداد الموازنة التقديرية ومراجعة الدفاتر المحاسبية والتدقيق المالي السنوي.',
                ],
                'en' => [
                    'name' => 'Annual Budget & Financial Reports 2026',
                    'description' => 'Preparing financial budget, closing books, and yearly financial audit.',
                ],
                'department_id' => $departments->skip(1)->first()?->id,
                'start_date' => now()->subDays(15)->format('Y-m-d'),
                'end_date' => now()->addDays(40)->format('Y-m-d'),
                'is_active' => true,
                'created_by' => $admin->id,
            ],
            [
                'ar' => [
                    'name' => 'حملة التسويق والفعاليات الربع سنوية Q4',
                    'description' => 'تجهيز وتنسيق الفعاليات والمعارض التسويقية والحملات الإعلانية الممولة.',
                ],
                'en' => [
                    'name' => 'Marketing Campaign & Events Q4',
                    'description' => 'Organizing campaigns, marketing events, and sponsored advertising ads.',
                ],
                'department_id' => $departments->skip(2)->first()?->id,
                'start_date' => now()->subDays(10)->format('Y-m-d'),
                'end_date' => now()->addDays(50)->format('Y-m-d'),
                'is_active' => true,
                'created_by' => $admin->id,
            ],
            [
                'ar' => [
                    'name' => 'تطوير الهيكل التنظيمي واللوائح الداخلية HR',
                    'description' => 'تحديث بطاقات الوصف الوظيفي، سلم الرواتب، ونظام تقييم الأداء الدوري.',
                ],
                'en' => [
                    'name' => 'Organizational Restructuring & HR Policies',
                    'description' => 'Updating job descriptions, grading scale, and performance appraisal policy.',
                ],
                'department_id' => $departments->skip(3)->first()?->id,
                'start_date' => now()->subDays(5)->format('Y-m-d'),
                'end_date' => now()->addDays(35)->format('Y-m-d'),
                'is_active' => true,
                'created_by' => $admin->id,
            ],
        ];

        foreach ($groupsData as $gData) {
            $existing = TaskGroup::whereTranslation('name', $gData['ar']['name'])->first();
            if (!$existing) {
                TaskGroup::create($gData);
            }
        }

        $allGroups = TaskGroup::all();

        // 2. Comprehensive Realistic Tasks Data
        $tasksData = [
            // --- Group 1: IT / Platform ---
            [
                'ar' => [
                    'title' => 'تصميم واجهات تجربة المستخدم (UI/UX) لداشبورد المهام',
                    'body'  => 'المطلوب تصميم الشاشات التفاعلية لجدول المهام ولوحة Kanban مع تسليم ملفات Figma ومكتبة الألوان المعتمدة.',
                ],
                'en' => [
                    'title' => 'UI/UX Design for Tasks Dashboard & Kanban Board',
                    'body'  => 'Design modern interactive UI screens for task tables and Kanban boards with Figma assets.',
                ],
                'priority'        => TaskPriority::HIGH,
                'task_status'     => TaskStatus::IN_PROGRESS,
                'start_date'      => now()->subDays(10)->format('Y-m-d'),
                'completion_date' => now()->addDays(4)->format('Y-m-d'),
                'group_index'     => 0,
                'history'         => [
                    ['old' => null, 'new' => TaskStatus::PENDING, 'comment' => 'تم إنشاء المهمة وإسنادها لفريق التصميم'],
                    ['old' => TaskStatus::PENDING, 'new' => TaskStatus::IN_PROGRESS, 'comment' => 'بدء رسم النماذج الأولية Wireframes واختبار تدفق المستخدم'],
                ],
                'notes'           => [
                    'يرجى مراعاة التوافق التام مع اتجاهات RTL للغة العربية و LTR للإنجليزية.',
                    'تم رفع النماذج الأولية على فيجما للمراجعة مع مدير المنتج.',
                ],
            ],
            [
                'ar' => [
                    'title' => 'تطوير واجهات برمجة التطبيقات (API Endpoints) لتطبيق الجوال',
                    'body'  => 'بناء REST APIs الآمنة لمصادقة المستخدمين وعرض المهام والملاحظات وتحديث الحالات مع التوثيق عبر Swagger.',
                ],
                'en' => [
                    'title' => 'Develop Mobile API Endpoints with Swagger Docs',
                    'body'  => 'Build secure REST APIs for user auth, task listing, notes, and status updates.',
                ],
                'priority'        => TaskPriority::URGENT,
                'task_status'     => TaskStatus::IN_PROGRESS,
                'start_date'      => now()->subDays(6)->format('Y-m-d'),
                'completion_date' => now()->addDays(6)->format('Y-m-d'),
                'group_index'     => 0,
                'history'         => [
                    ['old' => null, 'new' => TaskStatus::PENDING, 'comment' => 'تجهيز مسودات متطلبات الـ API'],
                    ['old' => TaskStatus::PENDING, 'new' => TaskStatus::IN_PROGRESS, 'comment' => 'تم الانتهاء من الـ Authentication ومسارات المهام'],
                ],
                'notes'           => [
                    'تم استخدام Sanctum للمصادقة وتشفير التوكن.',
                ],
            ],
            [
                'ar' => [
                    'title' => 'فحص الأمان والحماية واختبار الاختراق للسيرفرات',
                    'body'  => 'إجراء فحص أمني شامل للثغرات البرمجية ومنافذ السيرفر وإعداد تقرير الحماية الدوري.',
                ],
                'en' => [
                    'title' => 'Security Audit & Server Vulnerability Assessment',
                    'body'  => 'Perform penetration testing and vulnerability scan across all cloud server instances.',
                ],
                'priority'        => TaskPriority::HIGH,
                'task_status'     => TaskStatus::ON_HOLD,
                'start_date'      => now()->subDays(8)->format('Y-m-d'),
                'completion_date' => now()->addDays(10)->format('Y-m-d'),
                'group_index'     => 0,
                'history'         => [
                    ['old' => null, 'new' => TaskStatus::PENDING, 'comment' => 'إنشاء المهمة وجدولة وقت الفحص'],
                    ['old' => TaskStatus::PENDING, 'new' => TaskStatus::IN_PROGRESS, 'comment' => 'جاري مسح البورتات والمنافذ الخارجية'],
                    ['old' => TaskStatus::IN_PROGRESS, 'new' => TaskStatus::ON_HOLD, 'comment' => 'معلقة مؤقتاً بانتظار نافذة الصيانة الرسمية المعتمدة'],
                ],
                'notes'           => [
                    'تم تحديد موعد الصيانة فجر السبت لاستكمال الفحص.',
                ],
            ],
            [
                'ar' => [
                    'title' => 'إعداد وتكوين النسخ الاحتياطي التلقائي لقواعد البيانات',
                    'body'  => 'ضبط سكربتات النسخ الاحتياطي اليومي والتخزين السحابي المشفر واختبار استعادة البيانات.',
                ],
                'en' => [
                    'title' => 'Configure Automated Database Backup & Recovery',
                    'body'  => 'Setup daily encrypted cloud backups and test point-in-time recovery procedures.',
                ],
                'priority'        => TaskPriority::MEDIUM,
                'task_status'     => TaskStatus::COMPLETED,
                'start_date'      => now()->subDays(18)->format('Y-m-d'),
                'completion_date' => now()->subDays(5)->format('Y-m-d'),
                'completed_at'    => now()->subDays(5),
                'group_index'     => 0,
                'history'         => [
                    ['old' => null, 'new' => TaskStatus::PENDING, 'comment' => 'بدء تجهيز التخزين السحابي'],
                    ['old' => TaskStatus::PENDING, 'new' => TaskStatus::IN_PROGRESS, 'comment' => 'تثبيت واختبار كرون النسخ الاحتياطي'],
                    ['old' => TaskStatus::IN_PROGRESS, 'new' => TaskStatus::COMPLETED, 'comment' => 'تم الاختبار واستعادة نسخة تجريبية بنجاح 100%'],
                ],
                'notes'           => [
                    'النسخ الاحتياطي يعمل يومياً عند الساعة 2:00 صباحاً.',
                ],
            ],

            // --- Group 2: Financial & Budget ---
            [
                'ar' => [
                    'title' => 'إعداد ومراجعة مسير الرواتب والبدلات لشهر أكتوبر',
                    'body'  => 'تدقيق ساعات العمل الإضافي والخصومات والبدلات وتوليد مسيرات الرواتب للتحويل البنكي.',
                ],
                'en' => [
                    'title' => 'Prepare & Audit October Payroll & Allowances',
                    'body'  => 'Audit overtime hours, deductions, bonuses, and prepare bank transfer files.',
                ],
                'priority'        => TaskPriority::URGENT,
                'task_status'     => TaskStatus::COMPLETED,
                'start_date'      => now()->subDays(14)->format('Y-m-d'),
                'completion_date' => now()->subDays(1)->format('Y-m-d'),
                'completed_at'    => now()->subDays(1),
                'group_index'     => 1,
                'history'         => [
                    ['old' => null, 'new' => TaskStatus::PENDING, 'comment' => 'سحب بيانات البصمة والحضور والغياب'],
                    ['old' => TaskStatus::PENDING, 'new' => TaskStatus::IN_PROGRESS, 'comment' => 'احتساب البدلات والمكافآت والخصومات'],
                    ['old' => TaskStatus::IN_PROGRESS, 'new' => TaskStatus::COMPLETED, 'comment' => 'تم اعتماد المسيرات وإرسال ملفات الـ WPS للبنك'],
                ],
                'notes'           => [
                    'تمت مطابقة الرواتب لجميع الفروع بنجاح.',
                ],
            ],
            [
                'ar' => [
                    'title' => 'إعداد مسودة الميزانية التقديرية للعام المالي الجديد 2027',
                    'body'  => 'تجميع تقديرات النفقات والإيرادات من كافة الأقسام وإعداد الجداول التحليلية.',
                ],
                'en' => [
                    'title' => 'Draft Estimated Budget for Fiscal Year 2027',
                    'body'  => 'Collect department expenditures and forecast revenue projections for 2027.',
                ],
                'priority'        => TaskPriority::HIGH,
                'task_status'     => TaskStatus::IN_PROGRESS,
                'start_date'      => now()->subDays(5)->format('Y-m-d'),
                'completion_date' => now()->addDays(20)->format('Y-m-d'),
                'group_index'     => 1,
                'history'         => [
                    ['old' => null, 'new' => TaskStatus::PENDING, 'comment' => 'إرسال نماذج التقديرات لمدراء الإدارات'],
                    ['old' => TaskStatus::PENDING, 'new' => TaskStatus::IN_PROGRESS, 'comment' => 'استلام نماذج 4 أقسام وجاري تجميع الأرقام'],
                ],
                'notes'           => [
                    'ننتظر رد قسم التسويق والمبيعات خلال هذا الأسبوع.',
                ],
            ],
            [
                'ar' => [
                    'title' => 'جرد العهد والأصول الثابتة السنوي للفرع الرئيسي',
                    'body'  => 'مطابقة الأجهزة والمعدات والأصول مع سجلات النظام وإصدار تقرير الفروقات.',
                ],
                'en' => [
                    'title' => 'Fixed Assets & Equipment Inventory Audit',
                    'body'  => 'Match equipment and asset tags with ERP ledger and compile variance report.',
                ],
                'priority'        => TaskPriority::LOW,
                'task_status'     => TaskStatus::PENDING,
                'start_date'      => now()->addDays(3)->format('Y-m-d'),
                'completion_date' => now()->addDays(25)->format('Y-m-d'),
                'group_index'     => 1,
                'history'         => [
                    ['old' => null, 'new' => TaskStatus::PENDING, 'comment' => 'تم جدولة موعد الجرد بعد موافقة الإدارة المالية'],
                ],
                'notes'           => [],
            ],

            // --- Group 3: Marketing ---
            [
                'ar' => [
                    'title' => 'تصميم وإنتاج الفيديو الترويجي للحملة التسويقية الجديدة',
                    'body'  => 'إنتاج فيديو موشن جرافيك ترويجي عالي الجودة مدته 30 ثانية مع التعليق الصوتي الاحترافي.',
                ],
                'en' => [
                    'title' => 'Motion Graphic Video Production for Promo Campaign',
                    'body'  => 'Produce high quality 30-second motion graphic promo video with voiceover.',
                ],
                'priority'        => TaskPriority::HIGH,
                'task_status'     => TaskStatus::IN_PROGRESS,
                'start_date'      => now()->subDays(4)->format('Y-m-d'),
                'completion_date' => now()->addDays(7)->format('Y-m-d'),
                'group_index'     => 2,
                'history'         => [
                    ['old' => null, 'new' => TaskStatus::PENDING, 'comment' => 'كتابة السيناريو والـ Storyboard'],
                    ['old' => TaskStatus::PENDING, 'new' => TaskStatus::IN_PROGRESS, 'comment' => 'بدء التحريك والتسجيل الصوتي'],
                ],
                'notes'           => [
                    'تم اعتماد النص الإعلاني من قبل الإدارة العامة.',
                ],
            ],
            [
                'ar' => [
                    'title' => 'إطلاق وإدارة الحملات الإعلانية الممولة على منصات التواصل',
                    'body'  => 'ضبط الحملات الممولة على Google Ads و Instagram واستهداف الجمهور المحدد وتحسين تكلفة النقرة.',
                ],
                'en' => [
                    'title' => 'Launch & Manage Paid Ads on Social & Google',
                    'body'  => 'Setup and optimize paid campaigns on Instagram, TikTok, and Google Ads.',
                ],
                'priority'        => TaskPriority::MEDIUM,
                'task_status'     => TaskStatus::PENDING,
                'start_date'      => now()->format('Y-m-d'),
                'completion_date' => now()->addDays(15)->format('Y-m-d'),
                'group_index'     => 2,
                'history'         => [
                    ['old' => null, 'new' => TaskStatus::PENDING, 'comment' => 'تجهيز الميزانية الإعلانية وحسابات الدفع'],
                ],
                'notes'           => [],
            ],

            // --- Group 4: HR & Organization ---
            [
                'ar' => [
                    'title' => 'تحديث بطاقات الوصف الوظيفي لجميع مسميات الشركة',
                    'body'  => 'مراجعة المهام والمسؤوليات والمؤهلات المطلوبة وتحديث وثائق الوصف الوظيفي.',
                ],
                'en' => [
                    'title' => 'Update Job Description Cards & Competencies',
                    'body'  => 'Review duties, responsibilities, and qualifications for all company job titles.',
                ],
                'priority'        => TaskPriority::MEDIUM,
                'task_status'     => TaskStatus::IN_PROGRESS,
                'start_date'      => now()->subDays(8)->format('Y-m-d'),
                'completion_date' => now()->addDays(12)->format('Y-m-d'),
                'group_index'     => 3,
                'history'         => [
                    ['old' => null, 'new' => TaskStatus::PENDING, 'comment' => 'إنشاء المهمة وتوزيع النماذج'],
                    ['old' => TaskStatus::PENDING, 'new' => TaskStatus::IN_PROGRESS, 'comment' => 'تم إنجاز وتدقيق 12 مسمى وظيفي حتى الآن'],
                ],
                'notes'           => [
                    'جاري مراجعة متطلبات وظائف تقنية المعلومات والتسويق.',
                ],
            ],
            [
                'ar' => [
                    'title' => 'إجراء مقابلات المرشحين لوظائف المبيعات والتسويق',
                    'body'  => 'عقد مقابلات تقييم المرشحين وتعبئة بطاقات التقييم واختيار القائمة النهائية.',
                ],
                'en' => [
                    'title' => 'Interview Shortlisted Candidates for Sales Roles',
                    'body'  => 'Conduct structured interviews and evaluate candidate scorecards.',
                ],
                'priority'        => TaskPriority::HIGH,
                'task_status'     => TaskStatus::COMPLETED,
                'start_date'      => now()->subDays(16)->format('Y-m-d'),
                'completion_date' => now()->subDays(3)->format('Y-m-d'),
                'completed_at'    => now()->subDays(3),
                'group_index'     => 3,
                'history'         => [
                    ['old' => null, 'new' => TaskStatus::PENDING, 'comment' => 'فرز السير الذاتية وتحديد المواعيد'],
                    ['old' => TaskStatus::PENDING, 'new' => TaskStatus::IN_PROGRESS, 'comment' => 'مقابلة 6 مرشحين مؤهلين'],
                    ['old' => TaskStatus::IN_PROGRESS, 'new' => TaskStatus::COMPLETED, 'comment' => 'تم اختيار المرشح النهائي وتقديم العرض الوظيفي'],
                ],
                'notes'           => [
                    'تم توقيع العرض الوظيفي وسيبدأ العمل مطلع الشهر القادم.',
                ],
            ],
            [
                'ar' => [
                    'title' => 'تنظيم الدورة التدريبية لمهارات خدمة العملاء والتواصل',
                    'body'  => 'التنسيق مع المدرب وتجهيز قاعة التدريب والمواد العلمية وإصدار الشهادات للموظفين.',
                ],
                'en' => [
                    'title' => 'Organize Customer Service & Communication Training',
                    'body'  => 'Coordinate with the trainer, prepare training room, materials, and certificates.',
                ],
                'priority'        => TaskPriority::LOW,
                'task_status'     => TaskStatus::PENDING,
                'start_date'      => now()->addDays(5)->format('Y-m-d'),
                'completion_date' => now()->addDays(18)->format('Y-m-d'),
                'group_index'     => 3,
                'history'         => [
                    ['old' => null, 'new' => TaskStatus::PENDING, 'comment' => 'تم اعتماد الحقيبة التدريبية وقائمة المتدربين'],
                ],
                'notes'           => [],
            ],
        ];

        foreach ($tasksData as $index => $item) {
            $group = $allGroups->get($item['group_index']) ?? $allGroups->first();
            $employee = $employees->get($index % max(1, $employees->count()));

            $task = Task::create([
                'ar' => $item['ar'],
                'en' => $item['en'],
                'task_group_id'   => $group?->id,
                'employee_id'     => $employee?->id,
                'priority'        => $item['priority'],
                'task_status'     => $item['task_status'],
                'start_date'      => $item['start_date'],
                'completion_date' => $item['completion_date'],
                'completed_at'    => $item['completed_at'] ?? null,
                'created_by'      => $admin->id,
            ]);

            // Status Histories
            foreach ($item['history'] as $h) {
                TaskStatusHistory::create([
                    'task_id'    => $task->id,
                    'old_status' => $h['old'],
                    'new_status' => $h['new'],
                    'changed_by' => $admin->id,
                    'comment'    => $h['comment'],
                    'created_at' => now()->subDays(rand(1, 15)),
                ]);
            }

            // Notes
            foreach ($item['notes'] as $noteText) {
                TaskNote::create([
                    'task_id'    => $task->id,
                    'user_id'    => $admin->id,
                    'note'       => $noteText,
                    'created_at' => now()->subDays(rand(1, 10)),
                ]);
            }
        }
    }
}
