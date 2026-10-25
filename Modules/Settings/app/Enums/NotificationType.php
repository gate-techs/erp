<?php

namespace Modules\Settings\Enums;

enum NotificationType: string
{
    // ==========================================
    // 1. HR & Employee Portal (الموارد البشرية)
    // ==========================================
    case LEAVE_SUBMITTED = 'leave_submitted';
    case LEAVE_APPROVED = 'leave_approved';
    case LEAVE_REJECTED = 'leave_rejected';
    case PERMISSION_SUBMITTED = 'permission_submitted';
    case PERMISSION_APPROVED = 'permission_approved';
    case PERMISSION_REJECTED = 'permission_rejected';
    case LOAN_SUBMITTED = 'loan_submitted';
    case LOAN_APPROVED = 'loan_approved';
    case LOAN_REJECTED = 'loan_rejected';
    case PAYSLIP_GENERATED = 'payslip_generated';
    case PENALTY_ISSUED = 'penalty_issued';
    case REWARD_ISSUED = 'reward_issued';
    case CONTRACT_EXPIRY_WARNING = 'contract_expiry_warning';
    case DOCUMENT_EXPIRY_WARNING = 'document_expiry_warning';

    // ==========================================
    // 2. Accounting & Finance (المحاسبة والمالية)
    // ==========================================
    case JOURNAL_ENTRY_PENDING = 'journal_entry_pending';
    case JOURNAL_ENTRY_APPROVED = 'journal_entry_approved';
    case PAYMENT_RECEIVED = 'payment_received';
    case PAYMENT_DUE_ALERT = 'payment_due_alert';
    case FISCAL_YEAR_CLOSING = 'fiscal_year_closing';

    // ==========================================
    // 3. CRM & Client Support (إدارة العملاء والخدمات)
    // ==========================================
    case LEAD_ASSIGNED = 'lead_assigned';
    case SERVICE_REQUEST_NEW = 'service_request_new';
    case SERVICE_REQUEST_RESOLVED = 'service_request_resolved';

    // ==========================================
    // 4. Tasks & Projects (المهام والمشاريع)
    // ==========================================
    case TASK_ASSIGNED = 'task_assigned';
    case TASK_STATUS_UPDATED = 'task_status_updated';
    case TASK_NOTE_ADDED = 'task_note_added';
    case TASK_DUE_SOON = 'task_due_soon';
    case TASK_OVERDUE = 'task_overdue';

    // ==========================================
    // 5. Inventory & Stock (المخزون والمستودعات)
    // ==========================================
    case INVENTORY_LOW_STOCK = 'inventory_low_stock';

    // ==========================================
    // 6. System & General (إشعارات النظام العامة)
    // ==========================================
    case SYSTEM_BROADCAST = 'system_broadcast';

    /**
     * Translated display label.
     */
    public function label(): string
    {
        return match ($this) {
            // HR
            self::LEAVE_SUBMITTED => __('تقديم طلب إجازة جديد'),
            self::LEAVE_APPROVED => __('الموافقة على طلب الإجازة'),
            self::LEAVE_REJECTED => __('رفض طلب الإجازة'),
            self::PERMISSION_SUBMITTED => __('تقديم طلب استئذان ساعي'),
            self::PERMISSION_APPROVED => __('الموافقة على طلب الاستئذان'),
            self::PERMISSION_REJECTED => __('رفض طلب الاستئذان'),
            self::LOAN_SUBMITTED => __('تقديم طلب سلفة مالية'),
            self::LOAN_APPROVED => __('الموافقة على طلب السلفة'),
            self::LOAN_REJECTED => __('رفض طلب السلفة'),
            self::PAYSLIP_GENERATED => __('صدور كشف الراتب الشهري'),
            self::PENALTY_ISSUED => __('إشعار لفت نظر أو جزاء إداري'),
            self::REWARD_ISSUED => __('إشعار مكافأة أو شكر وتقدير'),
            self::CONTRACT_EXPIRY_WARNING => __('تنبيه قرب انتهاء عقد عمل موظف'),
            self::DOCUMENT_EXPIRY_WARNING => __('تنبيه قرب انتهاء وثيقة / إقامة موظف'),

            // Accounting
            self::JOURNAL_ENTRY_PENDING => __('قيد محاسبي جديد بانتظار الاعتماد'),
            self::JOURNAL_ENTRY_APPROVED => __('اعتماد القيد المحاسبي'),
            self::PAYMENT_RECEIVED => __('إشعار تحصيل / استلام دفعة مالية'),
            self::PAYMENT_DUE_ALERT => __('تنبيه استحقاق دفعة مالية'),
            self::FISCAL_YEAR_CLOSING => __('تنبيه إقفال الفترة / السنة المالية'),

            // CRM
            self::LEAD_ASSIGNED => __('إسناد عميل محتمل جديد (Lead)'),
            self::SERVICE_REQUEST_NEW => __('تذكرة أو طلب خدمة جديد من عميل'),
            self::SERVICE_REQUEST_RESOLVED => __('إغلاق أو حل طلب الخدمة'),

            // Tasks
            self::TASK_ASSIGNED => __('إسناد مهمة جديدة إلى موظف'),
            self::TASK_STATUS_UPDATED => __('تحديث حالة المهمة'),
            self::TASK_NOTE_ADDED => __('إضافة تعليق جديد على المهمة'),
            self::TASK_DUE_SOON => __('تنبيه باقتراب الموعد النهائي للمهمة'),
            self::TASK_OVERDUE => __('تنبيه بتأخر تسليم المهمة عن موعدها'),

            // Inventory
            self::INVENTORY_LOW_STOCK => __('تنبيه وصول صنف لحد إعادة الطلب'),

            // System
            self::SYSTEM_BROADCAST => __('إشعار وتعميم إداري عام'),
        };
    }

    /**
     * Get module classification.
     */
    public function module(): string
    {
        return match ($this) {
            self::LEAVE_SUBMITTED,
            self::LEAVE_APPROVED,
            self::LEAVE_REJECTED,
            self::PERMISSION_SUBMITTED,
            self::PERMISSION_APPROVED,
            self::PERMISSION_REJECTED,
            self::LOAN_SUBMITTED,
            self::LOAN_APPROVED,
            self::LOAN_REJECTED,
            self::PAYSLIP_GENERATED,
            self::PENALTY_ISSUED,
            self::REWARD_ISSUED,
            self::CONTRACT_EXPIRY_WARNING,
            self::DOCUMENT_EXPIRY_WARNING => 'hr',

            self::JOURNAL_ENTRY_PENDING,
            self::JOURNAL_ENTRY_APPROVED,
            self::PAYMENT_RECEIVED,
            self::PAYMENT_DUE_ALERT,
            self::FISCAL_YEAR_CLOSING => 'accounting',

            self::LEAD_ASSIGNED,
            self::SERVICE_REQUEST_NEW,
            self::SERVICE_REQUEST_RESOLVED => 'crm',

            self::TASK_ASSIGNED,
            self::TASK_STATUS_UPDATED,
            self::TASK_NOTE_ADDED,
            self::TASK_DUE_SOON,
            self::TASK_OVERDUE => 'tasks',

            self::INVENTORY_LOW_STOCK => 'inventory',

            self::SYSTEM_BROADCAST => 'general',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function toArray(): array
    {
        $result = [];
        foreach (self::cases() as $case) {
            $result[$case->value] = $case->label();
        }

        return $result;
    }
}
