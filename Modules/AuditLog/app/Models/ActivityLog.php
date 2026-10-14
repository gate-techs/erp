<?php

namespace Modules\AuditLog\Models;

use Spatie\Activitylog\Models\Activity as SpatieActivity;

class ActivityLog extends SpatieActivity
{
    /**
     * Get dynamic connection name.
     */
    public function getConnectionName()
    {
        return config('activitylog.database_connection') ?? config('database.default');
    }

    /**
     * Create a new model instance for a related model on the main database.
     */
    public function newRelatedInstance($class)
    {
        $instance = parent::newRelatedInstance($class);
        $instance->setConnection(config('database.default', 'mysql'));
        return $instance;
    }

    /**
     * Instantiate a new MorphTo relationship supporting cross-database queries.
     */
    protected function newMorphTo(\Illuminate\Database\Eloquent\Builder $query, \Illuminate\Database\Eloquent\Model $parent, $foreignKey, $ownerKey, $type, $relation)
    {
        return new \Modules\AuditLog\Relations\CrossDatabaseMorphTo($query, $parent, $foreignKey, $ownerKey, $type, $relation);
    }

    /**
     * Get Arabic readable label for event.
     */
    public function getEventLabelAttribute(): string
    {
        return match ($this->event) {
            'created'       => __('إضافة / إنشاء'),
            'updated'       => __('تعديل'),
            'deleted'       => __('حذف'),
            'restored'      => __('استعادة'),
            'approved'      => __('اعتماد'),
            'rejected'      => __('رفض / استبعاد'),
            'disbursed'     => __('صرف مالي'),
            'posted'        => __('ترحيل معتمد'),
            'reversed'      => __('قيد عكسي'),
            'reconciled'    => __('اعتماد تسوية'),
            'reopened'      => __('إعادة فتح'),
            'locked'        => __('إقفال فترة'),
            'login'         => __('تسجيل دخول'),
            'logout'        => __('تسجيل خروج'),
            'export'        => __('تصدير بيانات'),
            default         => ucfirst($this->event ?: __('عملية مخصصة')),
        };
    }

    /**
     * Get badge color class for event.
     */
    public function getEventBadgeColorAttribute(): string
    {
        return match ($this->event) {
            'created'                                       => 'bg-label-success',
            'updated'                                       => 'bg-label-info',
            'deleted'                                       => 'bg-label-danger',
            'restored'                                      => 'bg-label-warning',
            'approved', 'disbursed', 'posted', 'reconciled' => 'bg-label-success',
            'reversed'                                      => 'bg-label-danger',
            'reopened'                                      => 'bg-label-warning',
            'locked'                                        => 'bg-label-secondary',
            'rejected'                                      => 'bg-label-danger',
            'login'                                         => 'bg-label-success',
            'logout'                                        => 'bg-label-secondary',
            'export'                                        => 'bg-label-dark',
            default                                         => 'bg-label-primary',
        };
    }

    /**
     * Get subject human-readable type label.
     */
    public function getSubjectTypeLabelAttribute(): string
    {
        if (!$this->subject_type) {
            return __('نظام / عملية عامة');
        }

        $base = class_basename($this->subject_type);

        return match ($base) {
            'Employee'          => __('موظف'),
            'Department'        => __('قسم / إدارة'),
            'JobTitle'          => __('مسمى وظيفي'),
            'Attendance'        => __('حضور وانصراف'),
            'LeaveRequest'      => __('طلب إجازة'),
            'PermissionRequest' => __('طلب إذن'),
            'Loan'              => __('سلفة / قرض'),
            'Payroll'           => __('مسير رواتب'),
            'Reward'            => __('مكافأة'),
            'Penalty'           => __('جزاء'),
            'Candidate'         => __('مرشح توظيف'),
            'JobOpening'        => __('شاغر وظيفي'),
            'User'              => __('مستخدم'),
            'Role'              => __('دور صلاحيات'),
            'Setting'           => __('إعدادات'),
            'NurseryBranch'     => __('فرع'),
            // Accounting Models
            'Account'           => __('حساب مالي'),
            'JournalEntry'      => __('قيد يومية'),
            'JournalEntryLine'  => __('سطر قيد'),
            'Journal'           => __('دفتر يومية'),
            'CashAccount'       => __('خزينة / بنك'),
            'CostCenter'        => __('مركز تكلفة'),
            'BankReconciliation' => __('تسوية بنكية'),
            'PaymentMatching'   => __('مطابقة مدفوعات'),
            'FiscalYear'        => __('سنة مالية'),
            'LockDate'          => __('تاريخ إقفال'),
            'LockException'     => __('استثناء إقفال'),
            'Entity'            => __('كيان محاسبي'),
            default             => $base,
        };
    }

    /**
     * Get subject title / identifier.
     */
    public function getSubjectTitleAttribute(): string
    {
        if (!$this->subject) {
            return ($this->subject_type ? class_basename($this->subject_type) . ' #' . $this->subject_id : '-');
        }

        $s = $this->subject;

        if (isset($s->entry_number)) {
            return __('قيد رقم:') . ' ' . $s->entry_number;
        }
        if (isset($s->reconciliation_number)) {
            return __('تسوية رقم:') . ' ' . $s->reconciliation_number;
        }
        if (isset($s->code) && isset($s->name)) {
            return $s->code . ' - ' . $s->name;
        }
        if (isset($s->name)) {
            return $s->name;
        }
        if (isset($s->title)) {
            return $s->title;
        }
        if (isset($s->employee_code)) {
            return ($s->name ?? '') . ' (' . $s->employee_code . ')';
        }
        if (isset($s->month) && isset($s->year)) {
            return $s->month . '/' . $s->year;
        }

        return '#' . $this->subject_id;
    }

    /**
     * Get structured field diff comparison array.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getFieldDiffs(): array
    {
        $old = $this->properties['old'] ?? [];
        $attributes = $this->properties['attributes'] ?? [];

        $allKeys = array_unique(array_merge(array_keys($old), array_keys($attributes)));
        $diffs = [];

        foreach ($allKeys as $key) {
            // Ignore internal technical keys
            if (in_array($key, ['updated_at', 'created_at', 'remember_token', 'password'])) {
                continue;
            }

            $oldVal = $old[$key] ?? null;
            $newVal = $attributes[$key] ?? null;

            // If it's an update and both are strictly equal, skip
            if ($this->event === 'updated' && $oldVal === $newVal) {
                continue;
            }

            $diffs[] = [
                'field'       => $key,
                'field_label' => $this->formatFieldLabel($key),
                'old'         => $this->formatValue($oldVal),
                'new'         => $this->formatValue($newVal),
            ];
        }

        return $diffs;
    }

    /**
     * Human readable field translation.
     */
    protected function formatFieldLabel(string $field): string
    {
        return match ($field) {
            'name'             => __('الاسم'),
            'employee_code'    => __('الرقم الوظيفي'),
            'email'            => __('البريد الإلكتروني'),
            'phone'            => __('رقم الهاتف'),
            'status'           => __('الحالة'),
            'basic_salary'     => __('الراتب الأساسي'),
            'department_id'    => __('القسم'),
            'job_title_id'     => __('المسمى الوظيفي'),
            'branch_id', 'primary_branch_id' => __('الفرع'),
            'hire_date'        => __('تاريخ التعيين'),
            'start_date'       => __('تاريخ البدء'),
            'end_date'         => __('تاريخ الانتهاء'),
            'amount'           => __('المبلغ'),
            'reason'           => __('السبب / المبرر'),
            'notes'            => __('ملاحظات'),
            // Accounting fields
            'entry_number'     => __('رقم القيد'),
            'entry_date'       => __('تاريخ القيد'),
            'account_id'       => __('الحساب المالي'),
            'debit'            => __('مدين'),
            'credit'           => __('دائن'),
            'code'             => __('كود / رمز الحساب'),
            'account_type'     => __('نوع الحساب'),
            'statement_balance'=> __('رصيد كشف الحساب'),
            'statement_date'   => __('تاريخ كشف الحساب'),
            'lock_date'        => __('تاريخ الإقفال'),
            'fiscal_year_id'   => __('السنة المالية'),
            'entity_id'        => __('الكيان المحاسبي'),
            // System / meta fields
            'id'               => __('المعرّف'),
            'attributes'       => __('البيانات'),
            'updated_at'       => __('تاريخ التحديث'),
            'updated_by'       => __('آخر تحديث بواسطة'),
            'is_active'        => __('نشط'),
            'is_group'         => __('حساب تجميعي'),
            'is_deprecated'    => __('موقوف'),
            'is_teacher'       => __('معلم'),
            'allow_posting'    => __('يسمح بالترحيل'),
            'attachment'       => __('المرفق'),
            'parent_id'        => __('العنصر / الحساب الرئيسي'),
            'user_id'          => __('المستخدم'),
            'employee_id'      => __('الموظف'),
            'bank_id'          => __('البنك'),
            'bank_branch_id'   => __('فرع البنك'),
            'emergency_contact_phone' => __('هاتف الطوارئ'),
            // HR fields
            'staff_type'       => __('نوع الموظف'),
            'national_id'      => __('الرقم المدني'),
            'iban'             => __('رقم الآيبان (IBAN)'),
            'date_of_birth'    => __('تاريخ الميلاد'),
            'manager_approved_by' => __('اعتماد المدير بواسطة'),
            'manager_approved_at' => __('تاريخ اعتماد المدير'),
            'hr_approved_by'   => __('اعتماد الموارد البشرية بواسطة'),
            'hr_approved_at'   => __('تاريخ اعتماد الموارد البشرية'),
            // Accounting journal fields
            'posted_at'        => __('تاريخ الترحيل'),
            'closed_by'        => __('أُغلق بواسطة'),
            'closed_at'        => __('تاريخ الإغلاق'),
            'sequence_number_next' => __('رقم التسلسل التالي'),
            'refund_sequence'  => __('تسلسل المرتجعات'),
            'payment_sequence' => __('تسلسل المدفوعات'),
            'restrict_mode_hash_table' => __('قفل التعديل (Hash)'),
            default            => $this->translatedFieldFallback($field),
        };
    }

    /**
     * Fallback label for fields not listed above: reuse an existing translation of the
     * field's English name (e.g. "Hire Date", "Leave Type"), else the humanized name.
     */
    protected function translatedFieldFallback(string $field): string
    {
        $base = preg_replace('/_id$/', '', $field);
        $candidates = array_unique([
            \Illuminate\Support\Str::headline($field),
            \Illuminate\Support\Str::headline($base),
            ucfirst(str_replace('_', ' ', $field)),
            ucfirst(str_replace('_', ' ', $base)),
        ]);

        foreach ($candidates as $candidate) {
            $translated = __($candidate);
            if ($translated !== $candidate) {
                return $translated;
            }
        }

        return ucfirst(str_replace('_', ' ', $field));
    }

    /**
     * Format value for display.
     */
    protected function formatValue(mixed $val): string
    {
        if (is_null($val)) {
            return '-';
        }
        if (is_bool($val)) {
            return $val ? __('نعم') : __('لا');
        }
        if (is_array($val)) {
            return json_encode($val, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }
        // ISO-8601 timestamps from model casts (e.g. 2026-10-01T00:00:00.000000Z) -> readable date / date-time
        if (is_string($val) && preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(\.\d+)?Z$/', $val)) {
            $dt = \Illuminate\Support\Carbon::parse($val);

            // Date-only casts serialize as midnight UTC: show the date as-is (no timezone shift)
            return $dt->format('H:i:s') === '00:00:00'
                ? $dt->format('Y-m-d')
                : $dt->timezone(config('app.timezone'))->format('Y-m-d H:i');
        }

        return (string) $val;
    }
}
