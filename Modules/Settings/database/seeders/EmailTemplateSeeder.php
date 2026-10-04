<?php

namespace Modules\Settings\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Settings\Models\EmailTemplate;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'code'                  => 'task_assigned',
                'module'                => 'tasks',
                'name'                  => 'New Task Assigned',
                'name_ar'               => 'إسناد مهمة جديدة',
                'description'           => 'يتم إرساله للموظف فور إسناد مهمة جديدة له.',
                'subject_ar'            => 'إسناد مهمة جديدة: {task_title}',
                'subject_en'            => 'New Task Assigned: {task_title}',
                'body_ar'               => '<p>مرحباً <strong>{employee_name}</strong>،</p><p>تم إسناد مهمة جديدة لك بعنوان: <strong>{task_title}</strong> (كود المهمة: <code>{task_code}</code>).</p><div class="highlight-box"><p><strong>درجة الأولوية:</strong> {priority}</p><p><strong>تاريخ الاستحقاق:</strong> {due_date}</p><p><strong>المسند بواسطة:</strong> {assigned_by}</p></div><p>يرجى النقر على الزر أدناه لمراجعة كافة تفاصيل المهمة والبدء في تنفيذها.</p>',
                'body_en'               => '<p>Hello <strong>{employee_name}</strong>,</p><p>You have been assigned a new task: <strong>{task_title}</strong> (Task Code: <code>{task_code}</code>).</p><div class="highlight-box"><p><strong>Priority:</strong> {priority}</p><p><strong>Due Date:</strong> {due_date}</p><p><strong>Assigned By:</strong> {assigned_by}</p></div><p>Please click the button below to review the task details and start execution.</p>',
                'variables'             => [
                    ['key' => '{employee_name}', 'desc_ar' => 'اسم الموظف', 'desc_en' => 'Employee Name'],
                    ['key' => '{task_title}', 'desc_ar' => 'عنوان المهمة', 'desc_en' => 'Task Title'],
                    ['key' => '{task_code}', 'desc_ar' => 'كود المهمة', 'desc_en' => 'Task Code'],
                    ['key' => '{priority}', 'desc_ar' => 'أولوية المهمة', 'desc_en' => 'Priority'],
                    ['key' => '{due_date}', 'desc_ar' => 'تاريخ الاستحقاق', 'desc_en' => 'Due Date'],
                    ['key' => '{assigned_by}', 'desc_ar' => 'اسم مسند المهمة', 'desc_en' => 'Assigned By'],
                    ['key' => '{task_url}', 'desc_ar' => 'رابط المهمة', 'desc_en' => 'Task URL'],
                ],
                'action_button_text_ar' => 'عرض تفاصيل المهمة',
                'action_button_text_en' => 'View Task Details',
                'action_url_variable'   => '{task_url}',
                'is_active'             => true,
                'is_system'             => true,
            ],
            [
                'code'                  => 'task_status_changed',
                'module'                => 'tasks',
                'name'                  => 'Task Status Updated',
                'name_ar'               => 'تحديث حالة المهمة',
                'description'           => 'يتم إرساله عند تغيير حالة المهمة من مرحلة إلى أخرى.',
                'subject_ar'            => 'تحديث حالة المهمة: {task_title}',
                'subject_en'            => 'Task Status Updated: {task_title}',
                'body_ar'               => '<p>مرحباً <strong>{employee_name}</strong>،</p><p>نود إفادتكم بأنه قد تم تحديث حالة المهمة <strong>{task_title}</strong> إلى: <span style="background-color: #e0f2fe; color: #0284c7; padding: 4px 10px; border-radius: 4px; font-weight: bold;">{status}</span>.</p><p><strong>المسؤول عن التحديث:</strong> {assigned_by}</p><p>يمكنكم النقر على الزر أدناه لمتابعة سجل التغييرات على المهمة.</p>',
                'body_en'               => '<p>Hello <strong>{employee_name}</strong>,</p><p>Please note that the status of task <strong>{task_title}</strong> has been updated to: <span style="background-color: #e0f2fe; color: #0284c7; padding: 4px 10px; border-radius: 4px; font-weight: bold;">{status}</span>.</p><p><strong>Updated by:</strong> {assigned_by}</p><p>Click the button below to view the updated task history.</p>',
                'variables'             => [
                    ['key' => '{employee_name}', 'desc_ar' => 'اسم الموظف', 'desc_en' => 'Employee Name'],
                    ['key' => '{task_title}', 'desc_ar' => 'عنوان المهمة', 'desc_en' => 'Task Title'],
                    ['key' => '{status}', 'desc_ar' => 'الحالة الجديدة', 'desc_en' => 'New Status'],
                    ['key' => '{assigned_by}', 'desc_ar' => 'اسم المسؤول', 'desc_en' => 'Updated By'],
                    ['key' => '{task_url}', 'desc_ar' => 'رابط المهمة', 'desc_en' => 'Task URL'],
                ],
                'action_button_text_ar' => 'متابعة المهمة',
                'action_button_text_en' => 'View Task',
                'action_url_variable'   => '{task_url}',
                'is_active'             => true,
                'is_system'             => true,
            ],
            [
                'code'                  => 'task_note_added',
                'module'                => 'tasks',
                'name'                  => 'New Note Added on Task',
                'name_ar'               => 'إضافة ملاحظة جديدة على المهمة',
                'description'           => 'يتم إرساله عند إضافة تعليق أو ملاحظة جديدة على المهمة.',
                'subject_ar'            => 'ملاحظة جديدة على المهمة: {task_title}',
                'subject_en'            => 'New Note on Task: {task_title}',
                'body_ar'               => '<p>مرحباً <strong>{employee_name}</strong>،</p><p>تمت إضافة ملاحظة جديدة على المهمة <strong>{task_title}</strong> بواسطة <strong>{note_author}</strong>:</p><div class="highlight-box"><p>{note_content}</p></div><p>يمكنكم الرد على الملاحظة أو المتابعة عبر النظام.</p>',
                'body_en'               => '<p>Hello <strong>{employee_name}</strong>,</p><p>A new note has been added to task <strong>{task_title}</strong> by <strong>{note_author}</strong>:</p><div class="highlight-box"><p>{note_content}</p></div><p>You can respond to the note or follow up in the system.</p>',
                'variables'             => [
                    ['key' => '{employee_name}', 'desc_ar' => 'اسم الموظف', 'desc_en' => 'Employee Name'],
                    ['key' => '{task_title}', 'desc_ar' => 'عنوان المهمة', 'desc_en' => 'Task Title'],
                    ['key' => '{note_author}', 'desc_ar' => 'كاتب الملاحظة', 'desc_en' => 'Note Author'],
                    ['key' => '{note_content}', 'desc_ar' => 'نص الملاحظة', 'desc_en' => 'Note Content'],
                    ['key' => '{task_url}', 'desc_ar' => 'رابط المهمة', 'desc_en' => 'Task URL'],
                ],
                'action_button_text_ar' => 'عرض الملاحظة والرد',
                'action_button_text_en' => 'View Note & Reply',
                'action_url_variable'   => '{task_url}',
                'is_active'             => true,
                'is_system'             => true,
            ],
            [
                'code'                  => 'task_overdue',
                'module'                => 'tasks',
                'name'                  => 'Task Overdue Reminder',
                'name_ar'               => 'تنبيه بمهمة متأخرة',
                'description'           => 'تنبيه يتم إرساله عند تأخر المهمة عن موعد استحقاقها.',
                'subject_ar'            => 'تنبيه عاجل: المهمة متأخرة ({task_title})',
                'subject_en'            => 'Urgent Reminder: Task is Overdue ({task_title})',
                'body_ar'               => '<p>مرحباً <strong>{employee_name}</strong>،</p><p>نود تنبيهكم بأن المهمة <strong>{task_title}</strong> قد تجاوزت موعد استحقاقها المحدد بتاريخ (<strong>{due_date}</strong>) بمقدار <strong>{days_overdue} أيام</strong>.</p><div class="highlight-box"><p style="color: #dc2626; font-weight: bold; margin: 0;">يرجى مراجعة المهمة وتحديث حالتها أو إتمامها في أقرب وقت.</p></div>',
                'body_en'               => '<p>Hello <strong>{employee_name}</strong>,</p><p>Please note that task <strong>{task_title}</strong> is overdue by <strong>{days_overdue} days</strong> past its deadline (<strong>{due_date}</strong>).</p><div class="highlight-box"><p style="color: #dc2626; font-weight: bold; margin: 0;">Please take immediate action and update the task status.</p></div>',
                'variables'             => [
                    ['key' => '{employee_name}', 'desc_ar' => 'اسم الموظف', 'desc_en' => 'Employee Name'],
                    ['key' => '{task_title}', 'desc_ar' => 'عنوان المهمة', 'desc_en' => 'Task Title'],
                    ['key' => '{due_date}', 'desc_ar' => 'تاريخ الاستحقاق', 'desc_en' => 'Due Date'],
                    ['key' => '{days_overdue}', 'desc_ar' => 'عدد أيام التأخير', 'desc_en' => 'Days Overdue'],
                    ['key' => '{task_url}', 'desc_ar' => 'رابط المهمة', 'desc_en' => 'Task URL'],
                ],
                'action_button_text_ar' => 'إنجاز المهمة الآن',
                'action_button_text_en' => 'Complete Task Now',
                'action_url_variable'   => '{task_url}',
                'is_active'             => true,
                'is_system'             => true,
            ],
            [
                'code'                  => 'payslip_issued',
                'module'                => 'hr',
                'name'                  => 'Monthly Payslip Notification',
                'name_ar'               => 'إشعار بإصدار كشف الراتب',
                'description'           => 'يتم إرساله للموظفين عند اعتماد وإصدار مسير الرواتب الشهري.',
                'subject_ar'            => 'إشعار بإصدار مسير راتب شهر {month_year}',
                'subject_en'            => 'Payslip Issued for {month_year}',
                'body_ar'               => '<p>عزيزي/عزيزتي <strong>{employee_name}</strong>،</p><p>يسرنا إشعاركم بأنه قد تم إصدار كشف الراتب لشهر <strong>{month_year}</strong> وجاهز للاطلاع.</p><div class="highlight-box"><p><strong>الراتب الأساسي:</strong> {basic_salary}</p><p><strong>البدلات:</strong> {allowances}</p><p><strong>الخصومات:</strong> {deductions}</p><p style="font-size: 16px; color: #15803d; font-weight: bold; margin-top: 8px;"><strong>صافي الراتب المحول:</strong> {net_salary}</p></div><p>يمكنكم الاطلاع على كشف الراتب التفصيلي وتحميله من خلال الرابط أدناه.</p>',
                'body_en'               => '<p>Dear <strong>{employee_name}</strong>,</p><p>We are pleased to inform you that your payslip for <strong>{month_year}</strong> has been issued and is available for review.</p><div class="highlight-box"><p><strong>Basic Salary:</strong> {basic_salary}</p><p><strong>Allowances:</strong> {allowances}</p><p><strong>Deductions:</strong> {deductions}</p><p style="font-size: 16px; color: #15803d; font-weight: bold; margin-top: 8px;"><strong>Net Transferred Salary:</strong> {net_salary}</p></div><p>You can view and download your full payslip via the button below.</p>',
                'variables'             => [
                    ['key' => '{employee_name}', 'desc_ar' => 'اسم الموظف', 'desc_en' => 'Employee Name'],
                    ['key' => '{month_year}', 'desc_ar' => 'الشهر والسنة', 'desc_en' => 'Month & Year'],
                    ['key' => '{basic_salary}', 'desc_ar' => 'الراتب الأساسي', 'desc_en' => 'Basic Salary'],
                    ['key' => '{allowances}', 'desc_ar' => 'إجمالي البدلات', 'desc_en' => 'Total Allowances'],
                    ['key' => '{deductions}', 'desc_ar' => 'إجمالي الخصومات', 'desc_en' => 'Total Deductions'],
                    ['key' => '{net_salary}', 'desc_ar' => 'صافي الراتب', 'desc_en' => 'Net Salary'],
                    ['key' => '{payslip_url}', 'desc_ar' => 'رابط كشف الراتب', 'desc_en' => 'Payslip URL'],
                ],
                'action_button_text_ar' => 'عرض وتحميل كشف الراتب',
                'action_button_text_en' => 'View & Download Payslip',
                'action_url_variable'   => '{payslip_url}',
                'is_active'             => true,
                'is_system'             => true,
            ],
            [
                'code'                  => 'leave_approved',
                'module'                => 'hr',
                'name'                  => 'Leave Request Approved',
                'name_ar'               => 'اعتماد طلب الإجازة',
                'description'           => 'يتم إرساله للموظف عند قبول واعتماد طلب الإجازة.',
                'subject_ar'            => 'تم اعتماد طلب إجازتكم ({leave_type})',
                'subject_en'            => 'Your Leave Request has been Approved ({leave_type})',
                'body_ar'               => '<p>مرحباً <strong>{employee_name}</strong>،</p><p>يسعدنا إبلاغكم بأنه قد تمت الموافقة رسمياً على طلب <strong>{leave_type}</strong> المقدم من قبلكم.</p><div class="highlight-box"><p><strong>تاريخ البدء:</strong> {start_date}</p><p><strong>تاريخ الانتهاء:</strong> {end_date}</p><p><strong>إجمالي الأيام:</strong> {total_days} يوم</p></div><p>نتمنى لكم إجازة سعيدة وموفقة.</p>',
                'body_en'               => '<p>Hello <strong>{employee_name}</strong>,</p><p>We are glad to inform you that your request for <strong>{leave_type}</strong> has been officially approved.</p><div class="highlight-box"><p><strong>Start Date:</strong> {start_date}</p><p><strong>End Date:</strong> {end_date}</p><p><strong>Total Duration:</strong> {total_days} days</p></div><p>We wish you a pleasant and restful leave.</p>',
                'variables'             => [
                    ['key' => '{employee_name}', 'desc_ar' => 'اسم الموظف', 'desc_en' => 'Employee Name'],
                    ['key' => '{leave_type}', 'desc_ar' => 'نوع الإجازة', 'desc_en' => 'Leave Type'],
                    ['key' => '{start_date}', 'desc_ar' => 'تاريخ البداية', 'desc_en' => 'Start Date'],
                    ['key' => '{end_date}', 'desc_ar' => 'تاريخ النهاية', 'desc_en' => 'End Date'],
                    ['key' => '{total_days}', 'desc_ar' => 'عدد الأيام', 'desc_en' => 'Total Days'],
                ],
                'action_button_text_ar' => null,
                'action_button_text_en' => null,
                'action_url_variable'   => null,
                'is_active'             => true,
                'is_system'             => true,
            ],
            [
                'code'                  => 'employee_welcome',
                'module'                => 'hr',
                'name'                  => 'Welcome New Employee',
                'name_ar'               => 'ترحيب بالموظف الجديد',
                'description'           => 'رسالة ترحيبية بالموظف الجديد فور إنشائه على النظام.',
                'subject_ar'            => 'أهلاً وسهلاً بك في {company_name}',
                'subject_en'            => 'Welcome to {company_name}',
                'body_ar'               => '<p>مرحباً <strong>{employee_name}</strong>،</p><p>يسعدنا جداً انضمامكم إلى فريق عمل <strong>{company_name}</strong>. نتمنى لكم مسيرة مهنية مليئة بالنجاح والتميز والإنجازات معنا.</p><div class="highlight-box"><p>تم تفعيل حسابكم في بوابة الموظفين بنجاح. يمكنكم تسجيل الدخول للبدء في استخدام النظام والاستفادة من الخدمات المتاحة.</p></div>',
                'body_en'               => '<p>Hello <strong>{employee_name}</strong>,</p><p>We are thrilled to welcome you to the <strong>{company_name}</strong> team. We look forward to a successful and fruitful journey together.</p><div class="highlight-box"><p>Your employee portal account has been activated. You can now log in and explore available features and services.</p></div>',
                'variables'             => [
                    ['key' => '{employee_name}', 'desc_ar' => 'اسم الموظف', 'desc_en' => 'Employee Name'],
                    ['key' => '{company_name}', 'desc_ar' => 'اسم المؤسسة', 'desc_en' => 'Company Name'],
                    ['key' => '{system_url}', 'desc_ar' => 'رابط النظام', 'desc_en' => 'System URL'],
                ],
                'action_button_text_ar' => 'تسجيل الدخول للنظام',
                'action_button_text_en' => 'Log in to System',
                'action_url_variable'   => '{system_url}',
                'is_active'             => true,
                'is_system'             => true,
            ],
        ];

        foreach ($templates as $data) {
            EmailTemplate::updateOrCreate(
                ['code' => $data['code']],
                $data
            );
        }
    }
}
