<?php

namespace Modules\Settings\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Settings\Models\NotificationTemplate;

class NotificationTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            // ==========================================
            // 1. HR & Employee Portal (الموارد البشرية)
            // ==========================================
            [
                'code' => 'leave_submitted',
                'type' => 'leave_submitted',
                'module' => 'hr',
                'name_ar' => 'تقديم طلب إجازة',
                'name_en' => 'Leave Request Submitted',
                'description' => 'إشعار للمدير عند تقديم طلب إجازة جديد.',
                'icon' => 'bx-calendar-plus',
                'color' => 'info',
                'variables' => ['employee_name', 'leave_type', 'start_date', 'end_date', 'total_days', 'action_url'],
                'translations' => [
                    'ar' => [
                        'title' => 'طلب إجازة جديد: {employee_name}',
                        'message' => 'قام الموظف {employee_name} بتقديم طلب إجازة ({leave_type}) من {start_date} إلى {end_date} (إجمالي {total_days} يوم).',
                        'action_url' => '/portal/requests'
                    ],
                    'en' => [
                        'title' => 'New Leave Request: {employee_name}',
                        'message' => 'Employee {employee_name} submitted a leave request ({leave_type}) from {start_date} to {end_date} ({total_days} days).',
                        'action_url' => '/portal/requests'
                    ],
                ],
            ],
            [
                'code' => 'leave_approved',
                'type' => 'leave_approved',
                'module' => 'hr',
                'name_ar' => 'قبول طلب إجازة',
                'name_en' => 'Leave Request Approved',
                'description' => 'إشعار للموظف عند اعتماد طلب الإجازة.',
                'icon' => 'bx-calendar-check',
                'color' => 'success',
                'variables' => ['employee_name', 'leave_type', 'start_date', 'end_date', 'total_days', 'action_url'],
                'translations' => [
                    'ar' => [
                        'title' => 'تمت الموافقة على طلب الإجازة',
                        'message' => 'تمت الموافقة على طلب إجازتك ({leave_type}) من {start_date} إلى {end_date}.',
                        'action_url' => '/portal/requests'
                    ],
                    'en' => [
                        'title' => 'Leave Request Approved',
                        'message' => 'Your leave request ({leave_type}) from {start_date} to {end_date} has been approved.',
                        'action_url' => '/portal/requests'
                    ],
                ],
            ],
            [
                'code' => 'leave_rejected',
                'type' => 'leave_rejected',
                'module' => 'hr',
                'name_ar' => 'رفض طلب إجازة',
                'name_en' => 'Leave Request Rejected',
                'description' => 'إشعار للموظف عند رفض طلب الإجازة.',
                'icon' => 'bx-calendar-x',
                'color' => 'danger',
                'variables' => ['employee_name', 'leave_type', 'rejection_reason', 'action_url'],
                'translations' => [
                    'ar' => [
                        'title' => 'تم رفض طلب الإجازة',
                        'message' => 'نأسف لإبلاغك بأنه تم رفض طلب الإجازة ({leave_type}). السبب: {rejection_reason}',
                        'action_url' => '/portal/requests'
                    ],
                    'en' => [
                        'title' => 'Leave Request Rejected',
                        'message' => 'Your leave request ({leave_type}) was rejected. Reason: {rejection_reason}',
                        'action_url' => '/portal/requests'
                    ],
                ],
            ],
            [
                'code' => 'permission_submitted',
                'type' => 'permission_submitted',
                'module' => 'hr',
                'name_ar' => 'طلب استئذان ساعي جديد',
                'name_en' => 'Permission Request Submitted',
                'description' => 'إشعار بطلب إذن خروج أو استئذان ساعي جديد.',
                'icon' => 'bx-time',
                'color' => 'info',
                'variables' => ['employee_name', 'permission_date', 'hours_count', 'action_url'],
                'translations' => [
                    'ar' => [
                        'title' => 'طلب استئذان جديد: {employee_name}',
                        'message' => 'قدم الموظف {employee_name} طلب استئذان لتاريخ {permission_date} لمدة {hours_count} ساعة.',
                        'action_url' => '/portal/permissions'
                    ],
                    'en' => [
                        'title' => 'New Permission Request: {employee_name}',
                        'message' => 'Employee {employee_name} requested permission on {permission_date} for {hours_count} hours.',
                        'action_url' => '/portal/permissions'
                    ],
                ],
            ],
            [
                'code' => 'permission_approved',
                'type' => 'permission_approved',
                'module' => 'hr',
                'name_ar' => 'اعتماد طلب الاستئذان',
                'name_en' => 'Permission Request Approved',
                'description' => 'إشعار للموظف باعتماد طلب الاستئذان.',
                'icon' => 'bx-check-circle',
                'color' => 'success',
                'variables' => ['employee_name', 'permission_date', 'action_url'],
                'translations' => [
                    'ar' => [
                        'title' => 'تم اعتماد طلب الاستئذان',
                        'message' => 'تمت الموافقة على طلب الاستئذان الخاص بك لتاريخ {permission_date}.',
                        'action_url' => '/portal/permissions'
                    ],
                    'en' => [
                        'title' => 'Permission Request Approved',
                        'message' => 'Your permission request for {permission_date} has been approved.',
                        'action_url' => '/portal/permissions'
                    ],
                ],
            ],
            [
                'code' => 'loan_submitted',
                'type' => 'loan_submitted',
                'module' => 'hr',
                'name_ar' => 'طلب سلفة مالية جديد',
                'name_en' => 'Loan Request Submitted',
                'description' => 'إشعار للإدارة بطلب سلفة أو قرض جديد من موظف.',
                'icon' => 'bx-wallet',
                'color' => 'warning',
                'variables' => ['employee_name', 'amount', 'action_url'],
                'translations' => [
                    'ar' => [
                        'title' => 'طلب سلفة جديد: {employee_name}',
                        'message' => 'تقدم الموظف {employee_name} بطلب سلفة مالية بمبلغ {amount}.',
                        'action_url' => '/hr/loans'
                    ],
                    'en' => [
                        'title' => 'New Loan Request: {employee_name}',
                        'message' => 'Employee {employee_name} submitted a loan request for {amount}.',
                        'action_url' => '/hr/loans'
                    ],
                ],
            ],
            [
                'code' => 'loan_approved',
                'type' => 'loan_approved',
                'module' => 'hr',
                'name_ar' => 'اعتماد طلب السلفة',
                'name_en' => 'Loan Request Approved',
                'description' => 'إشعار للموظف بالموافقة على السلفة المالية.',
                'icon' => 'bx-check-double',
                'color' => 'success',
                'variables' => ['employee_name', 'amount', 'action_url'],
                'translations' => [
                    'ar' => [
                        'title' => 'تم اعتماد طلب السلفة المالية',
                        'message' => 'تمت الموافقة على طلب السلفة المالية بمبلغ {amount}.',
                        'action_url' => '/portal/loans'
                    ],
                    'en' => [
                        'title' => 'Loan Request Approved',
                        'message' => 'Your loan request for amount {amount} has been approved.',
                        'action_url' => '/portal/loans'
                    ],
                ],
            ],
            [
                'code' => 'payslip_generated',
                'type' => 'payslip_generated',
                'module' => 'hr',
                'name_ar' => 'صدور كشف الراتب',
                'name_en' => 'Payslip Generated',
                'description' => 'إشعار للموظف بصدور مسير وقسيمة الراتب الشهري.',
                'icon' => 'bx-money',
                'color' => 'primary',
                'variables' => ['employee_name', 'month_year', 'net_salary', 'action_url'],
                'translations' => [
                    'ar' => [
                        'title' => 'صدور قسيمة راتب شهر {month_year}',
                        'message' => 'تم إصدار كشف راتب شهر {month_year}. صافي الراتب المستحق: {net_salary}.',
                        'action_url' => '/portal/payroll'
                    ],
                    'en' => [
                        'title' => 'Payslip Issued for {month_year}',
                        'message' => 'Your payslip for {month_year} has been issued. Net Payable: {net_salary}.',
                        'action_url' => '/portal/payroll'
                    ],
                ],
            ],
            [
                'code' => 'penalty_issued',
                'type' => 'penalty_issued',
                'module' => 'hr',
                'name_ar' => 'إشعار جزاء إداري',
                'name_en' => 'Penalty Notice',
                'description' => 'إشعار رسمي بصدور لفت نظر أو جزاء على الموظف.',
                'icon' => 'bx-error-circle',
                'color' => 'danger',
                'variables' => ['employee_name', 'penalty_title', 'reason', 'action_url'],
                'translations' => [
                    'ar' => [
                        'title' => 'إشعار إداري: {penalty_title}',
                        'message' => 'نلفت انتباهكم لصدور قرار إداري ({penalty_title}). السبب: {reason}',
                        'action_url' => '/portal/penalties'
                    ],
                    'en' => [
                        'title' => 'Administrative Notice: {penalty_title}',
                        'message' => 'An administrative notice was issued: {penalty_title}. Reason: {reason}',
                        'action_url' => '/portal/penalties'
                    ],
                ],
            ],
            [
                'code' => 'reward_issued',
                'type' => 'reward_issued',
                'module' => 'hr',
                'name_ar' => 'إشعار مكافأة أو تكريم',
                'name_en' => 'Reward & Recognition',
                'description' => 'إشعار للموظف بمنحه مكافأة أو شكر وتقدير.',
                'icon' => 'bx-gift',
                'color' => 'success',
                'variables' => ['employee_name', 'reward_title', 'reward_amount', 'action_url'],
                'translations' => [
                    'ar' => [
                        'title' => 'مبارك! حصلت على مكافأة: {reward_title}',
                        'message' => 'تقديراً لجهودكم المتميزة، تم منحكم مكافأة: {reward_title} بقيمة {reward_amount}.',
                        'action_url' => '/portal/rewards'
                    ],
                    'en' => [
                        'title' => 'Congratulations! You received: {reward_title}',
                        'message' => 'In recognition of your performance, you were awarded {reward_title} ({reward_amount}).',
                        'action_url' => '/portal/rewards'
                    ],
                ],
            ],
            [
                'code' => 'document_expiry_warning',
                'type' => 'document_expiry_warning',
                'module' => 'hr',
                'name_ar' => 'تنبيه انتهاء إقامة أو وثيقة',
                'name_en' => 'Document Expiry Warning',
                'description' => 'إشعار بقرب انتهاء وثيقة رسمية للموظف كالإقامة أو التأمين.',
                'icon' => 'bx-file-blank',
                'color' => 'warning',
                'variables' => ['employee_name', 'document_name', 'expiry_date', 'action_url'],
                'translations' => [
                    'ar' => [
                        'title' => 'تنبيه: اقتراب انتهاء {document_name}',
                        'message' => 'نود تذكيرك بأن وثيقة {document_name} الخاصة بك تنتهي في {expiry_date}. يرجى اتخاذ الإجراء اللازم.',
                        'action_url' => '/portal/profile'
                    ],
                    'en' => [
                        'title' => 'Warning: {document_name} Expiring Soon',
                        'message' => 'Please note that your {document_name} will expire on {expiry_date}.',
                        'action_url' => '/portal/profile'
                    ],
                ],
            ],

            // ==========================================
            // 2. Accounting & Finance (المحاسبة والمالية)
            // ==========================================
            [
                'code' => 'journal_entry_pending',
                'type' => 'journal_entry_pending',
                'module' => 'accounting',
                'name_ar' => 'قيد محاسبي بانتظار الاعتماد',
                'name_en' => 'Journal Entry Pending Approval',
                'description' => 'إشعار للمدير المالي عند إنشاء قيد يومية يحتاج للمراجعة والاعتماد.',
                'icon' => 'bx-receipt',
                'color' => 'warning',
                'variables' => ['entry_number', 'amount', 'created_by', 'action_url'],
                'translations' => [
                    'ar' => [
                        'title' => 'قيد محاسبي بانتظار الاعتماد #{entry_number}',
                        'message' => 'تم إنشاء قيد يومية رقم #{entry_number} بمبلغ {amount} بواسطة {created_by} وهو بانتظار اعتمادكم.',
                        'action_url' => '/accounting/journal-entries'
                    ],
                    'en' => [
                        'title' => 'Journal Entry #{entry_number} Awaiting Approval',
                        'message' => 'Journal entry #{entry_number} for amount {amount} created by {created_by} requires approval.',
                        'action_url' => '/accounting/journal-entries'
                    ],
                ],
            ],
            [
                'code' => 'journal_entry_approved',
                'type' => 'journal_entry_approved',
                'module' => 'accounting',
                'name_ar' => 'اعتماد القيد المحاسبي',
                'name_en' => 'Journal Entry Approved',
                'description' => 'إشعار للمحاسب باعتماد القيد اليومي وترحيله.',
                'icon' => 'bx-check-shield',
                'color' => 'success',
                'variables' => ['entry_number', 'approver_name', 'action_url'],
                'translations' => [
                    'ar' => [
                        'title' => 'تم اعتماد القيد المحاسبي #{entry_number}',
                        'message' => 'تمت مراجعة واعتماد القيد المحاسبي رقم #{entry_number} بنجاح بواسطة {approver_name}.',
                        'action_url' => '/accounting/journal-entries'
                    ],
                    'en' => [
                        'title' => 'Journal Entry #{entry_number} Approved',
                        'message' => 'Journal entry #{entry_number} has been approved by {approver_name}.',
                        'action_url' => '/accounting/journal-entries'
                    ],
                ],
            ],
            [
                'code' => 'payment_received',
                'type' => 'payment_received',
                'module' => 'accounting',
                'name_ar' => 'تحصيل دفعة مالية',
                'name_en' => 'Payment Received',
                'description' => 'إشعار عند تسجيل سند قبض أو تحصيل دفعة من عميل.',
                'icon' => 'bx-credit-card',
                'color' => 'success',
                'variables' => ['client_name', 'amount', 'receipt_number', 'action_url'],
                'translations' => [
                    'ar' => [
                        'title' => 'تحصيل دفعة نقدية جديدة #{receipt_number}',
                        'message' => 'تم تسجيل استلام دفعة من {client_name} بمبلغ {amount} برقم سند #{receipt_number}.',
                        'action_url' => '/accounting/payments'
                    ],
                    'en' => [
                        'title' => 'Payment Received #{receipt_number}',
                        'message' => 'Payment received from {client_name} for amount {amount} (Receipt #{receipt_number}).',
                        'action_url' => '/accounting/payments'
                    ],
                ],
            ],
            [
                'code' => 'payment_due_alert',
                'type' => 'payment_due_alert',
                'module' => 'accounting',
                'name_ar' => 'تنبيه استحقاق دفعة أو فاتورة',
                'name_en' => 'Payment Due Alert',
                'description' => 'تنبيه للمالية باقتراب موعد استحقاق دفعة مورد أو عميل.',
                'icon' => 'bx-alarm-exclamation',
                'color' => 'danger',
                'variables' => ['entity_name', 'amount', 'due_date', 'action_url'],
                'translations' => [
                    'ar' => [
                        'title' => 'تنبيه: استحقاق دفعة مالية قادمة',
                        'message' => 'تستحق دفعة مالية لصالح/على {entity_name} بمبلغ {amount} في تاريخ {due_date}.',
                        'action_url' => '/accounting/due-payments'
                    ],
                    'en' => [
                        'title' => 'Payment Due Alert: {entity_name}',
                        'message' => 'A payment for {entity_name} of amount {amount} is due on {due_date}.',
                        'action_url' => '/accounting/due-payments'
                    ],
                ],
            ],

            // ==========================================
            // 3. CRM & Client Support (إدارة علاقات العملاء)
            // ==========================================
            [
                'code' => 'lead_assigned',
                'type' => 'lead_assigned',
                'module' => 'crm',
                'name_ar' => 'إسناد عميل محتمل جديد',
                'name_en' => 'New Lead Assigned',
                'description' => 'إشعار لموظف المبيعات فور إسناد Lead جديد له.',
                'icon' => 'bx-user-plus',
                'color' => 'info',
                'variables' => ['lead_name', 'source', 'phone', 'action_url'],
                'translations' => [
                    'ar' => [
                        'title' => 'عميل محتمل جديد: {lead_name}',
                        'message' => 'تم إسناد العميل {lead_name} (المصدر: {source}) إليك لمتابعته وإجراء الاتصال.',
                        'action_url' => '/crm/leads'
                    ],
                    'en' => [
                        'title' => 'New Lead Assigned: {lead_name}',
                        'message' => 'Lead {lead_name} (Source: {source}) has been assigned to you.',
                        'action_url' => '/crm/leads'
                    ],
                ],
            ],
            [
                'code' => 'service_request_new',
                'type' => 'service_request_new',
                'module' => 'crm',
                'name_ar' => 'طلب خدمة / تذكرة دعم جديدة',
                'name_en' => 'New Service Request',
                'description' => 'إشعار لفريق خدمة العملاء بفتح تذكرة جديدة.',
                'icon' => 'bx-support',
                'color' => 'warning',
                'variables' => ['client_name', 'ticket_number', 'subject', 'action_url'],
                'translations' => [
                    'ar' => [
                        'title' => 'تذكرة دعم جديدة #{ticket_number}',
                        'message' => 'ورد طلب خدمة جديد من العميل {client_name} بعنوان: {subject}.',
                        'action_url' => '/crm/service-requests'
                    ],
                    'en' => [
                        'title' => 'New Support Ticket #{ticket_number}',
                        'message' => 'New service request from {client_name}: {subject}.',
                        'action_url' => '/crm/service-requests'
                    ],
                ],
            ],
            [
                'code' => 'service_request_resolved',
                'type' => 'service_request_resolved',
                'module' => 'crm',
                'name_ar' => 'إغلاق أو حل تذكرة الدعم',
                'name_en' => 'Ticket Resolved',
                'description' => 'إشعار بحل طلب الدعم وإغلاقه.',
                'icon' => 'bx-check-circle',
                'color' => 'success',
                'variables' => ['client_name', 'ticket_number', 'action_url'],
                'translations' => [
                    'ar' => [
                        'title' => 'تم حل التذكرة #{ticket_number}',
                        'message' => 'تم حل طلب الخدمة رقم #{ticket_number} للعميل {client_name}.',
                        'action_url' => '/crm/service-requests'
                    ],
                    'en' => [
                        'title' => 'Ticket #{ticket_number} Resolved',
                        'message' => 'Service request #{ticket_number} for {client_name} has been resolved.',
                        'action_url' => '/crm/service-requests'
                    ],
                ],
            ],

            // ==========================================
            // 4. Tasks & Projects (المهام والمشاريع)
            // ==========================================
            [
                'code' => 'task_assigned',
                'type' => 'task_assigned',
                'module' => 'tasks',
                'name_ar' => 'إسناد مهمة جديدة',
                'name_en' => 'Task Assigned',
                'description' => 'إشعار عند إسناد مهمة إلى موظف.',
                'icon' => 'bx-task',
                'color' => 'primary',
                'variables' => ['employee_name', 'task_title', 'priority', 'due_date', 'action_url'],
                'translations' => [
                    'ar' => [
                        'title' => 'تم إسناد مهمة جديدة إليك: {task_title}',
                        'message' => 'تم إسناد المهمة {task_title} إليك. موعد التسليم: {due_date}.',
                        'action_url' => '/tasks'
                    ],
                    'en' => [
                        'title' => 'New Task Assigned: {task_title}',
                        'message' => 'Task {task_title} was assigned to you. Due date: {due_date}.',
                        'action_url' => '/tasks'
                    ],
                ],
            ],
            [
                'code' => 'task_status_updated',
                'type' => 'task_status_updated',
                'module' => 'tasks',
                'name_ar' => 'تحديث حالة المهمة',
                'name_en' => 'Task Status Updated',
                'description' => 'إشعار بتغير حالة المهمة (قيد التنفيذ / مكتملة).',
                'icon' => 'bx-refresh',
                'color' => 'info',
                'variables' => ['task_title', 'new_status', 'updated_by', 'action_url'],
                'translations' => [
                    'ar' => [
                        'title' => 'تحديث حالة المهمة: {task_title}',
                        'message' => 'تم تحديث حالة المهمة {task_title} إلى {new_status} بواسطة {updated_by}.',
                        'action_url' => '/tasks'
                    ],
                    'en' => [
                        'title' => 'Task Status Updated: {task_title}',
                        'message' => 'Task {task_title} status was updated to {new_status} by {updated_by}.',
                        'action_url' => '/tasks'
                    ],
                ],
            ],
            [
                'code' => 'task_note_added',
                'type' => 'task_note_added',
                'module' => 'tasks',
                'name_ar' => 'إضافة تعليق على مهمة',
                'name_en' => 'Task Note Added',
                'description' => 'إشعار عند إضافة تعليق جديد على مهمة.',
                'icon' => 'bx-message-rounded-detail',
                'color' => 'warning',
                'variables' => ['employee_name', 'task_title', 'author_name', 'note_content', 'action_url'],
                'translations' => [
                    'ar' => [
                        'title' => 'تعليق جديد على المهمة: {task_title}',
                        'message' => 'أضاف {author_name} تعليقًا جديدًا: "{note_content}" على المهمة {task_title}.',
                        'action_url' => '/tasks'
                    ],
                    'en' => [
                        'title' => 'New Note on Task: {task_title}',
                        'message' => '{author_name} added a note: "{note_content}" on {task_title}.',
                        'action_url' => '/tasks'
                    ],
                ],
            ],
            [
                'code' => 'task_due_soon',
                'type' => 'task_due_soon',
                'module' => 'tasks',
                'name_ar' => 'تنبيه اقتراب موعد تسليم مهمة',
                'name_en' => 'Task Due Soon',
                'description' => 'تذكير بقرب انتهاء المهلة المحددة للمهمة.',
                'icon' => 'bx-alarm',
                'color' => 'danger',
                'variables' => ['task_title', 'due_date', 'action_url'],
                'translations' => [
                    'ar' => [
                        'title' => 'تذكير: موعد تسليم المهمة {task_title}',
                        'message' => 'نود تذكيرك بأن الموعد النهائي لتسليم المهمة {task_title} هو اليوم/غداً ({due_date}).',
                        'action_url' => '/tasks'
                    ],
                    'en' => [
                        'title' => 'Reminder: Task {task_title} Due Soon',
                        'message' => 'Reminder that task {task_title} is due on {due_date}.',
                        'action_url' => '/tasks'
                    ],
                ],
            ],

            // ==========================================
            // 5. Inventory & Stock (المخزون والمستودعات)
            // ==========================================
            [
                'code' => 'inventory_low_stock',
                'type' => 'inventory_low_stock',
                'module' => 'inventory',
                'name_ar' => 'تنبيه نقص المخزون',
                'name_en' => 'Low Stock Alert',
                'description' => 'إشعار فوري عند انخفاض رصيد صنف عن حد الأمان أو إعادة الطلب.',
                'icon' => 'bx-package',
                'color' => 'danger',
                'variables' => ['item_name', 'item_code', 'current_stock', 'warehouse_name', 'action_url'],
                'translations' => [
                    'ar' => [
                        'title' => 'تنبيه مخزون: {item_name}',
                        'message' => 'وصل رصيد الصنف {item_name} في مستودع {warehouse_name} إلى {current_stock} قطعة فقط.',
                        'action_url' => '/inventory/items'
                    ],
                    'en' => [
                        'title' => 'Low Stock Alert: {item_name}',
                        'message' => 'Stock for {item_name} in {warehouse_name} dropped to {current_stock}.',
                        'action_url' => '/inventory/items'
                    ],
                ],
            ],
        ];

        foreach ($templates as $data) {
            $translations = $data['translations'];
            unset($data['translations']);

            $template = NotificationTemplate::updateOrCreate(
                ['code' => $data['code']],
                array_merge($data, [
                    'is_active' => true,
                    'is_system' => true,
                ])
            );

            foreach ($translations as $locale => $translation) {
                $template->translations()->updateOrCreate(['locale' => $locale], $translation);
            }
        }
    }
}
