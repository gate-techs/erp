<?php

namespace Modules\Settings\Enums;

enum EmailTemplateKey: string
{
    // ==========================================
    // 1. HR & Recruitment (الموارد البشرية والتوظيف)
    // ==========================================
    case HR_EMPLOYEE_WELCOME = 'hr_employee_welcome';
    case HR_LEAVE_REQUEST_SUBMITTED = 'hr_leave_request_submitted';
    case HR_LEAVE_REQUEST_STATUS = 'hr_leave_request_status';
    case HR_PERMISSION_REQUEST_STATUS = 'hr_permission_request_status';
    case HR_LOAN_REQUEST_STATUS = 'hr_loan_request_status';
    case HR_PAYROLL_GENERATED = 'hr_payroll_generated';
    case HR_PENALTY_ISSUED = 'hr_penalty_issued';
    case HR_REWARD_ISSUED = 'hr_reward_issued';
    case HR_JOB_APPLICATION_RECEIVED = 'hr_job_application_received';
    case HR_INTERVIEW_INVITATION = 'hr_interview_invitation';
    case HR_JOB_OFFER = 'hr_job_offer';
    case HR_CANDIDATE_REJECTION = 'hr_candidate_rejection';

    // ==========================================
    // 2. Accounting & Finance (المحاسبة والمالية)
    // ==========================================
    case ACC_PAYMENT_RECEIPT = 'acc_payment_receipt';
    case ACC_PAYMENT_REMINDER = 'acc_payment_reminder';
    case ACC_JOURNAL_ENTRY_APPROVAL = 'acc_journal_entry_approval';

    // ==========================================
    // 3. CRM & Client Support (إدارة علاقات العملاء)
    // ==========================================
    case CRM_LEAD_ASSIGNED = 'crm_lead_assigned';
    case CRM_SERVICE_REQUEST_RECEIVED = 'crm_service_request_received';
    case CRM_SERVICE_REQUEST_STATUS = 'crm_service_request_status';

    // ==========================================
    // 4. Tasks & Projects (المهام والمشاريع)
    // ==========================================
    case TASK_ASSIGNED = 'task_assigned';
    case TASK_DUE_REMINDER = 'task_due_reminder';
    case TASK_STATUS_CHANGED = 'task_status_changed';

    // ==========================================
    // 5. Inventory & Warehouse (المستودعات والمخزون)
    // ==========================================
    case INVENTORY_LOW_STOCK = 'inventory_low_stock';

    // ==========================================
    // 6. System & Auth (النظام والحسابات)
    // ==========================================
    case USER_INVITATION = 'user_invitation';
    case PASSWORD_RESET = 'password_reset';

    /**
     * Get the translated readable name for display in UI.
     */
    public function label(): string
    {
        return match ($this) {
            // HR
            self::HR_EMPLOYEE_WELCOME => __('Employee Welcome & Onboarding'),
            self::HR_LEAVE_REQUEST_SUBMITTED => __('Leave Request Submitted'),
            self::HR_LEAVE_REQUEST_STATUS => __('Leave Request Status Update'),
            self::HR_PERMISSION_REQUEST_STATUS => __('Permission Request Status Update'),
            self::HR_LOAN_REQUEST_STATUS => __('Loan Request Status Update'),
            self::HR_PAYROLL_GENERATED => __('Monthly Payslip Issued'),
            self::HR_PENALTY_ISSUED => __('Employee Penalty / Warning'),
            self::HR_REWARD_ISSUED => __('Employee Reward / Appreciation'),
            self::HR_JOB_APPLICATION_RECEIVED => __('Job Application Received Confirmation'),
            self::HR_INTERVIEW_INVITATION => __('Job Interview Invitation'),
            self::HR_JOB_OFFER => __('Formal Job Offer Letter'),
            self::HR_CANDIDATE_REJECTION => __('Candidate Application Status (Regret)'),

            // Accounting
            self::ACC_PAYMENT_RECEIPT => __('Payment Receipt Issued'),
            self::ACC_PAYMENT_REMINDER => __('Payment Due Date Reminder'),
            self::ACC_JOURNAL_ENTRY_APPROVAL => __('Journal Entry Review & Approval'),

            // CRM
            self::CRM_LEAD_ASSIGNED => __('New Lead Assigned'),
            self::CRM_SERVICE_REQUEST_RECEIVED => __('Service Request / Ticket Received'),
            self::CRM_SERVICE_REQUEST_STATUS => __('Service Request Status Update'),

            // Tasks
            self::TASK_ASSIGNED => __('New Task Assigned'),
            self::TASK_DUE_REMINDER => __('Task Due Date Reminder'),
            self::TASK_STATUS_CHANGED => __('Task Status Updated'),

            // Inventory
            self::INVENTORY_LOW_STOCK => __('Inventory Low Stock Alert'),

            // System
            self::USER_INVITATION => __('New User Invitation'),
            self::PASSWORD_RESET => __('Password Reset Verification'),
        };
    }

    /**
     * Get the module category key.
     */
    public function module(): string
    {
        return match ($this) {
            self::HR_EMPLOYEE_WELCOME,
            self::HR_LEAVE_REQUEST_SUBMITTED,
            self::HR_LEAVE_REQUEST_STATUS,
            self::HR_PERMISSION_REQUEST_STATUS,
            self::HR_LOAN_REQUEST_STATUS,
            self::HR_PAYROLL_GENERATED,
            self::HR_PENALTY_ISSUED,
            self::HR_REWARD_ISSUED,
            self::HR_JOB_APPLICATION_RECEIVED,
            self::HR_INTERVIEW_INVITATION,
            self::HR_JOB_OFFER,
            self::HR_CANDIDATE_REJECTION => 'hr',

            self::ACC_PAYMENT_RECEIPT,
            self::ACC_PAYMENT_REMINDER,
            self::ACC_JOURNAL_ENTRY_APPROVAL => 'accounting',

            self::CRM_LEAD_ASSIGNED,
            self::CRM_SERVICE_REQUEST_RECEIVED,
            self::CRM_SERVICE_REQUEST_STATUS => 'crm',

            self::TASK_ASSIGNED,
            self::TASK_DUE_REMINDER,
            self::TASK_STATUS_CHANGED => 'tasks',

            self::INVENTORY_LOW_STOCK => 'inventory',

            self::USER_INVITATION,
            self::PASSWORD_RESET => 'general',
        };
    }

    /**
     * Get the translated module badge label.
     */
    public function moduleLabel(): string
    {
        return match ($this->module()) {
            'hr' => __('Human Resources & Recruitment'),
            'accounting' => __('Accounting & Finance'),
            'crm' => __('CRM & Support'),
            'tasks' => __('Tasks & Projects'),
            'inventory' => __('Inventory & Stock'),
            default => __('General System'),
        };
    }

    /**
     * Badge CSS color classes for UI representation.
     */
    public function moduleBadgeClass(): string
    {
        return match ($this->module()) {
            'hr' => 'bg-label-primary text-primary',
            'accounting' => 'bg-label-success text-success',
            'crm' => 'bg-label-info text-info',
            'tasks' => 'bg-label-warning text-warning',
            'inventory' => 'bg-label-danger text-danger',
            default => 'bg-label-secondary text-secondary',
        };
    }

    /**
     * Allowed variables / placeholders with human-readable descriptions.
     *
     * @return array<string, array{desc_ar: string, desc_en: string}>
     */
    public function variables(): array
    {
        $common = [
            '{app_name}'     => ['desc_ar' => 'اسم النظام', 'desc_en' => 'Application Name'],
            '{company_name}' => ['desc_ar' => 'اسم الشركة / المؤسسة', 'desc_en' => 'Company Name'],
            '{app_url}'      => ['desc_ar' => 'رابط النظام', 'desc_en' => 'Application URL'],
            '{current_year}' => ['desc_ar' => 'السنة الحالية', 'desc_en' => 'Current Year'],
        ];

        $specific = match ($this) {
            // HR & Recruitment
            self::HR_EMPLOYEE_WELCOME => [
                '{employee_name}' => ['desc_ar' => 'اسم الموظف', 'desc_en' => 'Employee Name'],
                '{job_title}'     => ['desc_ar' => 'المسمى الوظيفي', 'desc_en' => 'Job Title'],
                '{department}'    => ['desc_ar' => 'القسم', 'desc_en' => 'Department'],
                '{login_email}'   => ['desc_ar' => 'البريد الإلكتروني للدخول', 'desc_en' => 'Login Email'],
                '{login_url}'     => ['desc_ar' => 'رابط بوابة الموظف', 'desc_en' => 'Employee Portal URL'],
            ],
            self::HR_LEAVE_REQUEST_SUBMITTED => [
                '{manager_name}'  => ['desc_ar' => 'اسم المدير المسؤول', 'desc_en' => 'Manager Name'],
                '{employee_name}' => ['desc_ar' => 'اسم الموظف مقدم الطلب', 'desc_en' => 'Employee Name'],
                '{leave_type}'    => ['desc_ar' => 'نوع الإجازة', 'desc_en' => 'Leave Type'],
                '{start_date}'    => ['desc_ar' => 'تاريخ البداية', 'desc_en' => 'Start Date'],
                '{end_date}'      => ['desc_ar' => 'تاريخ النهاية', 'desc_en' => 'End Date'],
                '{days_count}'    => ['desc_ar' => 'عدد الأيام', 'desc_en' => 'Total Days'],
                '{action_url}'    => ['desc_ar' => 'رابط مراجعة الطلب', 'desc_en' => 'Review Request URL'],
            ],
            self::HR_LEAVE_REQUEST_STATUS => [
                '{employee_name}'   => ['desc_ar' => 'اسم الموظف', 'desc_en' => 'Employee Name'],
                '{leave_type}'      => ['desc_ar' => 'نوع الإجازة', 'desc_en' => 'Leave Type'],
                '{status}'          => ['desc_ar' => 'حالة الطلب (مقبول / مرفوض)', 'desc_en' => 'Request Status'],
                '{rejection_reason}'=> ['desc_ar' => 'سبب الرفض (إن وجد)', 'desc_en' => 'Rejection Reason'],
                '{action_url}'      => ['desc_ar' => 'رابط الطلب', 'desc_en' => 'Request URL'],
            ],
            self::HR_PERMISSION_REQUEST_STATUS => [
                '{employee_name}'   => ['desc_ar' => 'اسم الموظف', 'desc_en' => 'Employee Name'],
                '{permission_date}' => ['desc_ar' => 'تاريخ الاستئذان', 'desc_en' => 'Permission Date'],
                '{hours_count}'     => ['desc_ar' => 'عدد الساعات', 'desc_en' => 'Hours Count'],
                '{status}'          => ['desc_ar' => 'حالة الطلب', 'desc_en' => 'Status'],
            ],
            self::HR_LOAN_REQUEST_STATUS => [
                '{employee_name}' => ['desc_ar' => 'اسم الموظف', 'desc_en' => 'Employee Name'],
                '{amount}'        => ['desc_ar' => 'مبلغ السلفة / القرض', 'desc_en' => 'Loan Amount'],
                '{status}'        => ['desc_ar' => 'حالة الطلب', 'desc_en' => 'Status'],
            ],
            self::HR_PAYROLL_GENERATED => [
                '{employee_name}' => ['desc_ar' => 'اسم الموظف', 'desc_en' => 'Employee Name'],
                '{month_year}'    => ['desc_ar' => 'الشهر والسنة', 'desc_en' => 'Month / Year'],
                '{basic_salary}'  => ['desc_ar' => 'الراتب الأساسي', 'desc_en' => 'Basic Salary'],
                '{allowances}'    => ['desc_ar' => 'إجمالي البدلات', 'desc_en' => 'Allowances'],
                '{deductions}'    => ['desc_ar' => 'إجمالي الخصومات', 'desc_en' => 'Deductions'],
                '{net_salary}'    => ['desc_ar' => 'صافي الراتب المستحق', 'desc_en' => 'Net Salary'],
                '{payslip_url}'   => ['desc_ar' => 'رابط قسيمة الراتب', 'desc_en' => 'Payslip URL'],
            ],
            self::HR_PENALTY_ISSUED => [
                '{employee_name}'  => ['desc_ar' => 'اسم الموظف', 'desc_en' => 'Employee Name'],
                '{penalty_title}'  => ['desc_ar' => 'نوع الجزاء / الإنذار', 'desc_en' => 'Penalty Title'],
                '{penalty_reason}' => ['desc_ar' => 'سبب الجزاء', 'desc_en' => 'Reason'],
                '{deduction_days}' => ['desc_ar' => 'أيام الخصم أو القيمة', 'desc_en' => 'Deduction Days/Value'],
            ],
            self::HR_REWARD_ISSUED => [
                '{employee_name}' => ['desc_ar' => 'اسم الموظف', 'desc_en' => 'Employee Name'],
                '{reward_title}'  => ['desc_ar' => 'عنوان المكافأة / التكريم', 'desc_en' => 'Reward Title'],
                '{reward_amount}' => ['desc_ar' => 'قيمة المكافأة (إن وجدت)', 'desc_en' => 'Reward Amount'],
            ],
            self::HR_JOB_APPLICATION_RECEIVED => [
                '{candidate_name}' => ['desc_ar' => 'اسم المتقدم للوظيفة', 'desc_en' => 'Candidate Name'],
                '{job_title}'      => ['desc_ar' => 'المسمى الوظيفي الشاغر', 'desc_en' => 'Job Title'],
            ],
            self::HR_INTERVIEW_INVITATION => [
                '{candidate_name}' => ['desc_ar' => 'اسم المرشح', 'desc_en' => 'Candidate Name'],
                '{job_title}'      => ['desc_ar' => 'المسمى الوظيفي', 'desc_en' => 'Job Title'],
                '{interview_date}' => ['desc_ar' => 'تاريخ المقابلة', 'desc_en' => 'Interview Date'],
                '{interview_time}' => ['desc_ar' => 'وقت المقابلة', 'desc_en' => 'Interview Time'],
                '{interview_type}' => ['desc_ar' => 'نوع المقابلة (حضوري / أونلاين)', 'desc_en' => 'Interview Type'],
                '{location_link}'  => ['desc_ar' => 'العنوان أو رابط الاجتماع', 'desc_en' => 'Location / Meeting Link'],
            ],
            self::HR_JOB_OFFER => [
                '{candidate_name}' => ['desc_ar' => 'اسم المرشح', 'desc_en' => 'Candidate Name'],
                '{job_title}'      => ['desc_ar' => 'المسمى الوظيفي المعروض', 'desc_en' => 'Offered Job Title'],
                '{salary}'         => ['desc_ar' => 'الراتب المعروض', 'desc_en' => 'Offered Salary'],
                '{expiry_date}'    => ['desc_ar' => 'آخر موعد لقبول العرض', 'desc_en' => 'Offer Expiration Date'],
                '{offer_url}'      => ['desc_ar' => 'رابط استعراض وتوقيع العرض', 'desc_en' => 'Offer URL'],
            ],
            self::HR_CANDIDATE_REJECTION => [
                '{candidate_name}' => ['desc_ar' => 'اسم المتقدم', 'desc_en' => 'Candidate Name'],
                '{job_title}'      => ['desc_ar' => 'المسمى الوظيفي', 'desc_en' => 'Job Title'],
            ],

            // Accounting & Finance
            self::ACC_PAYMENT_RECEIPT => [
                '{entity_name}'    => ['desc_ar' => 'اسم العميل / المورد', 'desc_en' => 'Entity / Client Name'],
                '{receipt_number}' => ['desc_ar' => 'رقم سند القبض / الدفعة', 'desc_en' => 'Receipt Number'],
                '{amount}'         => ['desc_ar' => 'المبلغ المدفوع', 'desc_en' => 'Paid Amount'],
                '{payment_method}' => ['desc_ar' => 'طريقة الدفع', 'desc_en' => 'Payment Method'],
                '{payment_date}'   => ['desc_ar' => 'تاريخ الدفع', 'desc_en' => 'Payment Date'],
                '{receipt_url}'    => ['desc_ar' => 'رابط معاينة السند', 'desc_en' => 'Receipt URL'],
            ],
            self::ACC_PAYMENT_REMINDER => [
                '{entity_name}' => ['desc_ar' => 'اسم العميل', 'desc_en' => 'Client Name'],
                '{balance_due}' => ['desc_ar' => 'المبلغ المستحق', 'desc_en' => 'Due Balance'],
                '{due_date}'    => ['desc_ar' => 'تاريخ الاستحقاق', 'desc_en' => 'Due Date'],
                '{pay_url}'     => ['desc_ar' => 'رابط السداد الإلكتروني', 'desc_en' => 'Payment URL'],
            ],
            self::ACC_JOURNAL_ENTRY_APPROVAL => [
                '{approver_name}' => ['desc_ar' => 'اسم المدير المالي / المعتمد', 'desc_en' => 'Approver Name'],
                '{entry_number}'  => ['desc_ar' => 'رقم القيد المحاسبي', 'desc_en' => 'Entry Number'],
                '{entry_amount}'  => ['desc_ar' => 'إجمالي القيد', 'desc_en' => 'Entry Amount'],
                '{created_by}'    => ['desc_ar' => 'المستخدم منشئ القيد', 'desc_en' => 'Created By'],
                '{entry_url}'     => ['desc_ar' => 'رابط مراجعة القيد', 'desc_en' => 'Review Entry URL'],
            ],

            // CRM
            self::CRM_LEAD_ASSIGNED => [
                '{agent_name}'  => ['desc_ar' => 'اسم مسؤول المبيعات', 'desc_en' => 'Sales Agent Name'],
                '{lead_name}'   => ['desc_ar' => 'اسم العميل المحتمل', 'desc_en' => 'Lead Name'],
                '{lead_phone}'  => ['desc_ar' => 'رقم هاتف العميل', 'desc_en' => 'Lead Phone'],
                '{lead_source}' => ['desc_ar' => 'مصدر العميل', 'desc_en' => 'Lead Source'],
                '{lead_url}'    => ['desc_ar' => 'رابط العميل في الـ CRM', 'desc_en' => 'Lead CRM URL'],
            ],
            self::CRM_SERVICE_REQUEST_RECEIVED => [
                '{client_name}'   => ['desc_ar' => 'اسم العميل', 'desc_en' => 'Client Name'],
                '{ticket_number}' => ['desc_ar' => 'رقم التذكرة / الطلب', 'desc_en' => 'Ticket Number'],
                '{ticket_title}'  => ['desc_ar' => 'عنوان الطلب', 'desc_en' => 'Ticket Title'],
                '{ticket_url}'    => ['desc_ar' => 'رابط متابعة الطلب', 'desc_en' => 'Ticket Tracking URL'],
            ],
            self::CRM_SERVICE_REQUEST_STATUS => [
                '{client_name}'   => ['desc_ar' => 'اسم العميل', 'desc_en' => 'Client Name'],
                '{ticket_number}' => ['desc_ar' => 'رقم التذكرة', 'desc_en' => 'Ticket Number'],
                '{status}'        => ['desc_ar' => 'الحالة الجديدة للطلب', 'desc_en' => 'New Status'],
                '{ticket_url}'    => ['desc_ar' => 'رابط الطلب', 'desc_en' => 'Ticket URL'],
            ],

            // Tasks
            self::TASK_ASSIGNED => [
                '{employee_name}' => ['desc_ar' => 'اسم الموظف المكلف', 'desc_en' => 'Employee Name'],
                '{task_title}'    => ['desc_ar' => 'عنوان المهمة', 'desc_en' => 'Task Title'],
                '{task_code}'     => ['desc_ar' => 'كود المهمة', 'desc_en' => 'Task Code'],
                '{priority}'      => ['desc_ar' => 'أولوية المهمة', 'desc_en' => 'Priority'],
                '{due_date}'      => ['desc_ar' => 'تاريخ الاستحقاق', 'desc_en' => 'Due Date'],
                '{assigned_by}'   => ['desc_ar' => 'مسند المهمة', 'desc_en' => 'Assigned By'],
                '{task_url}'      => ['desc_ar' => 'رابط المهمة', 'desc_en' => 'Task URL'],
            ],
            self::TASK_DUE_REMINDER => [
                '{employee_name}' => ['desc_ar' => 'اسم الموظف', 'desc_en' => 'Employee Name'],
                '{task_title}'    => ['desc_ar' => 'عنوان المهمة', 'desc_en' => 'Task Title'],
                '{due_date}'      => ['desc_ar' => 'الموعد النهائي', 'desc_en' => 'Due Date'],
                '{task_url}'      => ['desc_ar' => 'رابط المهمة', 'desc_en' => 'Task URL'],
            ],
            self::TASK_STATUS_CHANGED => [
                '{employee_name}' => ['desc_ar' => 'اسم الموظف', 'desc_en' => 'Employee Name'],
                '{task_title}'    => ['desc_ar' => 'عنوان المهمة', 'desc_en' => 'Task Title'],
                '{status}'        => ['desc_ar' => 'الحالة الجديدة', 'desc_en' => 'New Status'],
                '{assigned_by}'   => ['desc_ar' => 'المسؤول عن التحديث', 'desc_en' => 'Updated By'],
                '{task_url}'      => ['desc_ar' => 'رابط المهمة', 'desc_en' => 'Task URL'],
            ],

            // Inventory
            self::INVENTORY_LOW_STOCK => [
                '{item_name}'      => ['desc_ar' => 'اسم الصنف', 'desc_en' => 'Item Name'],
                '{item_code}'      => ['desc_ar' => 'كود الصنف / SKU', 'desc_en' => 'Item Code'],
                '{warehouse_name}' => ['desc_ar' => 'اسم المستودع', 'desc_en' => 'Warehouse Name'],
                '{current_stock}'  => ['desc_ar' => 'الرصيد المتبقي الحالي', 'desc_en' => 'Current Stock'],
                '{reorder_level}'  => ['desc_ar' => 'حد إعادة الطلب', 'desc_en' => 'Reorder Level'],
                '{item_url}'       => ['desc_ar' => 'رابط الصنف', 'desc_en' => 'Item URL'],
            ],

            // System
            self::USER_INVITATION => [
                '{user_name}'      => ['desc_ar' => 'اسم المستخدم', 'desc_en' => 'User Name'],
                '{activation_url}' => ['desc_ar' => 'رابط تفعيل الحساب وتعيين كلمة المرور', 'desc_en' => 'Activation URL'],
            ],
            self::PASSWORD_RESET => [
                '{user_name}' => ['desc_ar' => 'اسم المستخدم', 'desc_en' => 'User Name'],
                '{reset_url}' => ['desc_ar' => 'رابط إعادة تعيين كلمة المرور', 'desc_en' => 'Password Reset URL'],
            ],
        };

        return array_merge($common, $specific);
    }

    /**
     * Get default subject and HTML content for seeding.
     *
     * @return array{subject_ar: string, subject_en: string, body_ar: string, body_en: string}
     */
    public function defaultContent(): array
    {
        return match ($this) {
            self::HR_EMPLOYEE_WELCOME => [
                'subject_ar' => 'مرحباً بك في فريق عمل {company_name}',
                'subject_en' => 'Welcome to {company_name} Team',
                'body_ar' => '<p>مرحباً <strong>{employee_name}</strong>،</p><p>يسعدنا انضمامك إلى فريق العمل في {company_name} بمسمى: <strong>{job_title}</strong> في قسم <strong>{department}</strong>.</p><p>تم إعداد حسابك في النظام، ويمكنك تسجيل الدخول بالبريد الإلكتروني: <code>{login_email}</code>.</p><p>نتمنى لك رحلة مهنية ملهمة وحافلة بالإنجازات والنجاح معنا.</p>',
                'body_en' => '<p>Dear <strong>{employee_name}</strong>,</p><p>We are delighted to welcome you to {company_name} as <strong>{job_title}</strong> in the <strong>{department}</strong> department.</p><p>Your account is ready. You can log in using: <code>{login_email}</code>.</p><p>We wish you a wonderful and successful journey with us.</p>',
            ],
            self::HR_LEAVE_REQUEST_SUBMITTED => [
                'subject_ar' => 'طلب إجازة جديد للمراجعة: {employee_name}',
                'subject_en' => 'New Leave Request for Review: {employee_name}',
                'body_ar' => '<p>عزيزي <strong>{manager_name}</strong>،</p><p>قام الموظف <strong>{employee_name}</strong> بتقديم طلب إجازة جديد ({leave_type}) للفترة من <strong>{start_date}</strong> إلى <strong>{end_date}</strong> (إجمالي {days_count} يوم).</p><p>يرجى النقر على الرابط لمراجعة الطلب والموافقة أو الرفض.</p>',
                'body_en' => '<p>Dear <strong>{manager_name}</strong>,</p><p>Employee <strong>{employee_name}</strong> has submitted a new leave request ({leave_type}) from <strong>{start_date}</strong> to <strong>{end_date}</strong> ({days_count} days).</p><p>Please click below to review and take action.</p>',
            ],
            self::HR_LEAVE_REQUEST_STATUS => [
                'subject_ar' => 'تحديث حالة طلب الإجازة ({leave_type})',
                'subject_en' => 'Leave Request Status Update ({leave_type})',
                'body_ar' => '<p>مرحباً <strong>{employee_name}</strong>،</p><p>نود إفادتك بأن حالة طلب إجازتك ({leave_type}) أصبحت: <strong>{status}</strong>.</p><p>{rejection_reason}</p>',
                'body_en' => '<p>Hello <strong>{employee_name}</strong>,</p><p>Your leave request ({leave_type}) status is now: <strong>{status}</strong>.</p><p>{rejection_reason}</p>',
            ],
            self::HR_PERMISSION_REQUEST_STATUS => [
                'subject_ar' => 'تحديث حالة طلب الاستئذان الساعي',
                'subject_en' => 'Permission Request Status Update',
                'body_ar' => '<p>مرحباً <strong>{employee_name}</strong>،</p><p>نود إشعارك بأن طلب الاستئذان لتاريخ <strong>{permission_date}</strong> لمدة <strong>{hours_count} ساعة</strong> تمت معالجته وحالته: <strong>{status}</strong>.</p>',
                'body_en' => '<p>Hello <strong>{employee_name}</strong>,</p><p>Your permission request for date <strong>{permission_date}</strong> ({hours_count} hours) status is: <strong>{status}</strong>.</p>',
            ],
            self::HR_LOAN_REQUEST_STATUS => [
                'subject_ar' => 'تحديث حالة طلب السلفة / القرض',
                'subject_en' => 'Loan Request Status Update',
                'body_ar' => '<p>مرحباً <strong>{employee_name}</strong>،</p><p>طلب السلفة المقدم بمبلغ <strong>{amount}</strong> أصبحت حالته: <strong>{status}</strong>.</p>',
                'body_en' => '<p>Hello <strong>{employee_name}</strong>,</p><p>Your loan request for amount <strong>{amount}</strong> status is now: <strong>{status}</strong>.</p>',
            ],
            self::HR_PAYROLL_GENERATED => [
                'subject_ar' => 'قسيمة الراتب لشهر {month_year}',
                'subject_en' => 'Payslip Issued for {month_year}',
                'body_ar' => '<p>مرحباً <strong>{employee_name}</strong>،</p><p>يسرنا إبلاغك بصدور كشف راتب شهر <strong>{month_year}</strong>.</p><div style="background:#f8fafc;padding:15px;border-radius:8px;margin:15px 0;"><p><strong>الراتب الأساسي:</strong> {basic_salary}</p><p><strong>البدلات:</strong> {allowances}</p><p><strong>الخصومات:</strong> {deductions}</p><p style="font-size:16px;color:#0f2d59;"><strong>صافي الراتب المستحق:</strong> {net_salary}</p></div><p>يمكنك استعراض وتحميل قسيمة الراتب بالتفصيل من خلال الرابط أدناه.</p>',
                'body_en' => '<p>Dear <strong>{employee_name}</strong>,</p><p>Your payslip for <strong>{month_year}</strong> has been issued.</p><div style="background:#f8fafc;padding:15px;border-radius:8px;margin:15px 0;"><p><strong>Basic:</strong> {basic_salary}</p><p><strong>Allowances:</strong> {allowances}</p><p><strong>Deductions:</strong> {deductions}</p><p style="font-size:16px;color:#0f2d59;"><strong>Net Payable:</strong> {net_salary}</p></div><p>Click below to view full details.</p>',
            ],
            self::HR_PENALTY_ISSUED => [
                'subject_ar' => 'إشعار إداري: {penalty_title}',
                'subject_en' => 'Administrative Notice: {penalty_title}',
                'body_ar' => '<p>الموظف <strong>{employee_name}</strong>،</p><p>يؤسفنا إشعاركم بصدور قرار إداري ({penalty_title}).</p><p><strong>السبب:</strong> {penalty_reason}</p><p><strong>الأثر:</strong> {deduction_days}</p>',
                'body_en' => '<p>Dear <strong>{employee_name}</strong>,</p><p>This is an administrative notice regarding ({penalty_title}).</p><p><strong>Reason:</strong> {penalty_reason}</p><p><strong>Impact:</strong> {deduction_days}</p>',
            ],
            self::HR_REWARD_ISSUED => [
                'subject_ar' => 'شكر وتقدير ومكافأة: {reward_title}',
                'subject_en' => 'Recognition & Reward: {reward_title}',
                'body_ar' => '<p>الموظف المتميز <strong>{employee_name}</strong>،</p><p>تقديراً لجهودكم المتميزة والملموسة، يسر الإدارة منحكم: <strong>{reward_title}</strong> (القيمة: {reward_amount}).</p><p>شاكرين لكم عطائكم وتفانيكم المستمر.</p>',
                'body_en' => '<p>Dear <strong>{employee_name}</strong>,</p><p>In recognition of your exceptional efforts, we are pleased to award you: <strong>{reward_title}</strong> (Value: {reward_amount}).</p><p>Thank you for your dedication and commitment.</p>',
            ],
            self::HR_JOB_APPLICATION_RECEIVED => [
                'subject_ar' => 'تأكيد استلام طلب التقديم على وظيفة: {job_title}',
                'subject_en' => 'Job Application Received: {job_title}',
                'body_ar' => '<p>عزيزنا <strong>{candidate_name}</strong>،</p><p>نشكرك على اهتمامك بالانضمام إلى {company_name}. نؤكد لك استلام طلب تقديمك على وظيفة <strong>{job_title}</strong> بنجاح.</p><p>سيقوم فريق الموارد البشرية بدراسة سيرتك الذاتية والتواصل معك في أقرب وقت في حال توافق مؤهلاتك مع متطلبات الوظيفة.</p>',
                'body_en' => '<p>Dear <strong>{candidate_name}</strong>,</p><p>Thank you for your interest in joining {company_name}. We have successfully received your application for <strong>{job_title}</strong>.</p><p>Our talent acquisition team will review your profile and reach out if your background matches our requirements.</p>',
            ],
            self::HR_INTERVIEW_INVITATION => [
                'subject_ar' => 'دعوة لمقابلة عمل: {job_title} لدى {company_name}',
                'subject_en' => 'Interview Invitation: {job_title} at {company_name}',
                'body_ar' => '<p>مرحباً <strong>{candidate_name}</strong>،</p><p>بناءً على طلب التوظيف المقدم منكم لوظيفة <strong>{job_title}</strong>، يسعدنا دعوتكم لإجراء مقابلة عمل:</p><div style="background:#f8fafc;padding:15px;border-radius:8px;margin:15px 0;"><p><strong>تاريخ المقابلة:</strong> {interview_date}</p><p><strong>الوقت:</strong> {interview_time}</p><p><strong>نوع المقابلة:</strong> {interview_type}</p><p><strong>المكان / الرابط:</strong> {location_link}</p></div><p>يرجى تأكيد الحضور بالرد على هذا البريد الإلكتروني.</p>',
                'body_en' => '<p>Dear <strong>{candidate_name}</strong>,</p><p>We are pleased to invite you for an interview for the <strong>{job_title}</strong> role at {company_name}:</p><div style="background:#f8fafc;padding:15px;border-radius:8px;margin:15px 0;"><p><strong>Date:</strong> {interview_date}</p><p><strong>Time:</strong> {interview_time}</p><p><strong>Type:</strong> {interview_type}</p><p><strong>Location / Link:</strong> {location_link}</p></div><p>Please confirm your availability by replying to this email.</p>',
            ],
            self::HR_JOB_OFFER => [
                'subject_ar' => 'عرض وظيفي رسمي من {company_name}: {job_title}',
                'subject_en' => 'Official Job Offer from {company_name}: {job_title}',
                'body_ar' => '<p>عزيزنا <strong>{candidate_name}</strong>،</p><p>يسعدنا أن نقدم لك هذا العرض الوظيفي الرسمي لشغل منصب <strong>{job_title}</strong> براتب شهري قدره <strong>{salary}</strong>.</p><p>العرض سارٍ حتى تاريخ <strong>{expiry_date}</strong>. يرجى النقر على الرابط التالي لمراجعة كافة بنود العرض وقبوله إلكترونياً.</p>',
                'body_en' => '<p>Dear <strong>{candidate_name}</strong>,</p><p>We are delighted to extend an official job offer for the position of <strong>{job_title}</strong> with a monthly salary of <strong>{salary}</strong>.</p><p>This offer is valid until <strong>{expiry_date}</strong>. Click below to review and sign.</p>',
            ],
            self::HR_CANDIDATE_REJECTION => [
                'subject_ar' => 'بخصوص طلب التقديم على وظيفة: {job_title}',
                'subject_en' => 'Regarding your application for: {job_title}',
                'body_ar' => '<p>عزيزنا <strong>{candidate_name}</strong>،</p><p>نشكرك على وقتك واهتمامك بالانضمام إلى فريق {company_name} والتقديم على وظيفة <strong>{job_title}</strong>.</p><p>بعد مراجعة متأنية، نود إفادتك بأننا قررنا المضي قدماً مع مرشحين آخرين تتطابق خبراتهم بشكل أكبر مع احتياجات المرحلة الحالية. وسنحتفظ بسيرتك الذاتية لفرصنا المستقبلية.</p>',
                'body_en' => '<p>Dear <strong>{candidate_name}</strong>,</p><p>Thank you for taking the time to apply for <strong>{job_title}</strong> at {company_name}.</p><p>After careful consideration, we have decided to pursue candidates whose background more closely aligns with our current needs. We will keep your resume for future opportunities.</p>',
            ],

            // Accounting & Finance
            self::ACC_PAYMENT_RECEIPT => [
                'subject_ar' => 'إيصال استلام دفعة رقم #{receipt_number}',
                'subject_en' => 'Payment Receipt #{receipt_number}',
                'body_ar' => '<p>السادة / <strong>{entity_name}</strong>،</p><p>نشكركم على تعاملكم. تم استلام دفعتكم بنجاح:</p><div style="background:#f8fafc;padding:15px;border-radius:8px;margin:15px 0;"><p><strong>رقم السند:</strong> #{receipt_number}</p><p><strong>المبلغ المستلم:</strong> {amount}</p><p><strong>طريقة الدفع:</strong> {payment_method}</p><p><strong>التاريخ:</strong> {payment_date}</p></div><p>يمكنكم استعراض وطباعة سند القبض من خلال الرابط أدناه.</p>',
                'body_en' => '<p>Dear <strong>{entity_name}</strong>,</p><p>Thank you for your payment. We have successfully received:</p><div style="background:#f8fafc;padding:15px;border-radius:8px;margin:15px 0;"><p><strong>Receipt #:</strong> #{receipt_number}</p><p><strong>Amount:</strong> {amount}</p><p><strong>Method:</strong> {payment_method}</p><p><strong>Date:</strong> {payment_date}</p></div><p>Click below to view your official receipt.</p>',
            ],
            self::ACC_PAYMENT_REMINDER => [
                'subject_ar' => 'تذكير بموعد استحقاق دفعة مالية',
                'subject_en' => 'Payment Due Reminder',
                'body_ar' => '<p>السادة / <strong>{entity_name}</strong>،</p><p>نود تذكيركم بلطف بأن لديكم دفعة مالية مستحقة بمبلغ <strong>{balance_due}</strong> تستحق في تاريخ <strong>{due_date}</strong>.</p><p>يرجى ترتيب السداد لضمان استمرارية الخدمات والعمليات دون انقطاع.</p>',
                'body_en' => '<p>Dear <strong>{entity_name}</strong>,</p><p>This is a gentle reminder that an outstanding payment of <strong>{balance_due}</strong> is due on <strong>{due_date}</strong>.</p><p>Please arrange payment to avoid any service disruptions.</p>',
            ],
            self::ACC_JOURNAL_ENTRY_APPROVAL => [
                'subject_ar' => 'طلب اعتماد قيد محاسبي رقم #{entry_number}',
                'subject_en' => 'Journal Entry Approval Request #{entry_number}',
                'body_ar' => '<p>عزيزي <strong>{approver_name}</strong>،</p><p>تم إنشاء قيد محاسبي جديد رقم <strong>#{entry_number}</strong> بمبلغ <strong>{entry_amount}</strong> بواسطة المستخدم <strong>{created_by}</strong> وهو بانتظار مراجعتكم واعتمادكم.</p>',
                'body_en' => '<p>Dear <strong>{approver_name}</strong>,</p><p>A new journal entry #{entry_number} for total amount {entry_amount} created by {created_by} is awaiting your approval.</p>',
            ],

            // CRM
            self::CRM_LEAD_ASSIGNED => [
                'subject_ar' => 'إسناد عميل محتمل جديد: {lead_name}',
                'subject_en' => 'New Lead Assigned: {lead_name}',
                'body_ar' => '<p>مرحباً <strong>{agent_name}</strong>،</p><p>تم إسناد عميل محتمل جديد لك لمتابعته:</p><div style="background:#f8fafc;padding:15px;border-radius:8px;margin:15px 0;"><p><strong>اسم العميل:</strong> {lead_name}</p><p><strong>رقم الهاتف:</strong> {lead_phone}</p><p><strong>المصدر:</strong> {lead_source}</p></div><p>يرجى سرعة التواصل مع العميل وتحديث مراحل الـ CRM.</p>',
                'body_en' => '<p>Hello <strong>{agent_name}</strong>,</p><p>A new lead has been assigned to you:</p><div style="background:#f8fafc;padding:15px;border-radius:8px;margin:15px 0;"><p><strong>Lead Name:</strong> {lead_name}</p><p><strong>Phone:</strong> {lead_phone}</p><p><strong>Source:</strong> {lead_source}</p></div><p>Please reach out promptly.</p>',
            ],
            self::CRM_SERVICE_REQUEST_RECEIVED => [
                'subject_ar' => 'تم استلام طلبكم بنجاح: تذكرة #{ticket_number}',
                'subject_en' => 'Service Request Received: Ticket #{ticket_number}',
                'body_ar' => '<p>مرحباً <strong>{client_name}</strong>،</p><p>نشكركم على تواصلكم. تم استلام طلبكم بعنوان: <strong>{ticket_title}</strong> برقم تذكرة <strong>#{ticket_number}</strong>.</p><p>يعمل فريق الدعم على معالجة طلبكم والرد بأسرع وقت ممكن.</p>',
                'body_en' => '<p>Dear <strong>{client_name}</strong>,</p><p>Thank you for contacting us. We have received your request <strong>{ticket_title}</strong> with Ticket #{ticket_number}.</p><p>Our team is reviewing it and will respond shortly.</p>',
            ],
            self::CRM_SERVICE_REQUEST_STATUS => [
                'subject_ar' => 'تحديث على تذكرة الدعم #{ticket_number}',
                'subject_en' => 'Update on Support Ticket #{ticket_number}',
                'body_ar' => '<p>مرحباً <strong>{client_name}</strong>،</p><p>نود إفادتكم بوجود تحديث جديد على طلبكم رقم <strong>#{ticket_number}</strong>، وحالته الحالية هي: <strong>{status}</strong>.</p><p>يمكنكم استعراض التفاصيل والرد عبر الرابط أدناه.</p>',
                'body_en' => '<p>Dear <strong>{client_name}</strong>,</p><p>There is an update on your support ticket #{ticket_number}. The status is now: <strong>{status}</strong>.</p><p>Click below to view details and respond.</p>',
            ],

            // Tasks
            self::TASK_ASSIGNED => [
                'subject_ar' => 'إسناد مهمة جديدة: {task_title}',
                'subject_en' => 'New Task Assigned: {task_title}',
                'body_ar' => '<p>مرحباً <strong>{employee_name}</strong>،</p><p>تم إسناد مهمة جديدة لك بعنوان: <strong>{task_title}</strong> (كود: <code>{task_code}</code>).</p><div style="background:#f8fafc;padding:15px;border-radius:8px;margin:15px 0;"><p><strong>درجة الأولوية:</strong> {priority}</p><p><strong>تاريخ الاستحقاق:</strong> {due_date}</p><p><strong>المسند بواسطة:</strong> {assigned_by}</p></div><p>يرجى النقر لمراجعة كافة التفاصيل والبدء في الإنجاز.</p>',
                'body_en' => '<p>Hello <strong>{employee_name}</strong>,</p><p>You have been assigned a new task: <strong>{task_title}</strong> (Code: <code>{task_code}</code>).</p><div style="background:#f8fafc;padding:15px;border-radius:8px;margin:15px 0;"><p><strong>Priority:</strong> {priority}</p><p><strong>Due Date:</strong> {due_date}</p><p><strong>Assigned By:</strong> {assigned_by}</p></div><p>Click below to view details.</p>',
            ],
            self::TASK_DUE_REMINDER => [
                'subject_ar' => 'تذكير باقتراب الموعد النهائي للمهمة: {task_title}',
                'subject_en' => 'Task Due Reminder: {task_title}',
                'body_ar' => '<p>مرحباً <strong>{employee_name}</strong>،</p><p>نود تذكيرك بأن موعد تسليم المهمة <strong>{task_title}</strong> هو <strong>{due_date}</strong>. يرجى التأكد من إنهاء المتطلبات في الموعد المحدد.</p>',
                'body_en' => '<p>Hello <strong>{employee_name}</strong>,</p><p>This is a reminder that task <strong>{task_title}</strong> is due on <strong>{due_date}</strong>. Please ensure completion on time.</p>',
            ],
            self::TASK_STATUS_CHANGED => [
                'subject_ar' => 'تحديث حالة المهمة: {task_title}',
                'subject_en' => 'Task Status Updated: {task_title}',
                'body_ar' => '<p>مرحباً <strong>{employee_name}</strong>،</p><p>تم تغيير حالة المهمة <strong>{task_title}</strong> إلى: <strong>{status}</strong> بواسطة <strong>{assigned_by}</strong>.</p>',
                'body_en' => '<p>Hello <strong>{employee_name}</strong>,</p><p>The status of task <strong>{task_title}</strong> was updated to <strong>{status}</strong> by <strong>{assigned_by}</strong>.</p>',
            ],

            // Inventory
            self::INVENTORY_LOW_STOCK => [
                'subject_ar' => 'تنبيه انخفاض مخزون: {item_name}',
                'subject_en' => 'Low Stock Alert: {item_name}',
                'body_ar' => '<p>إلى مسؤولي المستودعات والمشتريات،</p><p>نلفت انتباهكم إلى وصول رصيد الصنف <strong>{item_name}</strong> (كود: <code>{item_code}</code>) في مستودع <strong>{warehouse_name}</strong> إلى مستوى حرج:</p><div style="background:#f8fafc;padding:15px;border-radius:8px;margin:15px 0;"><p><strong>الرصيد المتبقي:</strong> <span style="color:#dc2626;font-weight:bold;">{current_stock}</span></p><p><strong>حد إعادة الطلب:</strong> {reorder_level}</p></div><p>يرجى المبادرة بإصدار أمر شراء لتعويض النقص في المخزون.</p>',
                'body_en' => '<p>Warehouse & Procurement Team,</p><p>Stock for item <strong>{item_name}</strong> (Code: <code>{item_code}</code>) in warehouse <strong>{warehouse_name}</strong> has reached reorder level:</p><div style="background:#f8fafc;padding:15px;border-radius:8px;margin:15px 0;"><p><strong>Current Stock:</strong> <span style="color:#dc2626;font-weight:bold;">{current_stock}</span></p><p><strong>Reorder Level:</strong> {reorder_level}</p></div><p>Please initiate a purchase order.</p>',
            ],

            // System
            self::USER_INVITATION => [
                'subject_ar' => 'دعوة للانضمام إلى منصة {company_name}',
                'subject_en' => 'Invitation to join {company_name} Platform',
                'body_ar' => '<p>مرحباً <strong>{user_name}</strong>،</p><p>تمت دعوتك لإنشاء وتفعيل حسابك على نظام <strong>{company_name}</strong>.</p><p>يرجى النقر على الرابط التالي لتعيين كلمة المرور الخاصة بك وتفعيل الحساب للبدء في استخدام النظام.</p>',
                'body_en' => '<p>Hello <strong>{user_name}</strong>,</p><p>You have been invited to join the <strong>{company_name}</strong> system.</p><p>Click below to set your password and activate your account.</p>',
            ],
            self::PASSWORD_RESET => [
                'subject_ar' => 'رابط إعادة تعيين كلمة المرور',
                'subject_en' => 'Password Reset Request',
                'body_ar' => '<p>مرحباً <strong>{user_name}</strong>،</p><p>تلقينا طلباً لإعادة تعيين كلمة المرور الخاصة بحسابكم. إذا كنت أنت من قام بالطلب، يرجى النقر على الزر أدناه لاختيار كلمة مرور جديدة.</p><p>إذا لم تطلب ذلك، يمكنك تجاهل هذا البريد بأمان.</p>',
                'body_en' => '<p>Hello <strong>{user_name}</strong>,</p><p>We received a request to reset your password. If you requested this, click below to set a new password.</p><p>If you did not request this, please ignore this email.</p>',
            ],
        };
    }
}
