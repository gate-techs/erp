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
            'created'              => 'bg-label-success',
            'updated'              => 'bg-label-info',
            'deleted'              => 'bg-label-danger',
            'restored'             => 'bg-label-warning',
            'approved', 'disbursed'=> 'bg-label-primary',
            'rejected'             => 'bg-label-danger',
            'login'                => 'bg-label-success',
            'logout'               => 'bg-label-secondary',
            'export'               => 'bg-label-dark',
            default                => 'bg-label-primary',
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
            'Employee'        => __('موظف'),
            'Department'      => __('قسم / إدارة'),
            'JobTitle'        => __('مسمى وظيفي'),
            'Attendance'      => __('حضور وانصراف'),
            'LeaveRequest'    => __('طلب إجازة'),
            'PermissionRequest' => __('طلب إذن'),
            'Loan'            => __('سلفة / قرض'),
            'Payroll'         => __('مسير رواتب'),
            'Reward'          => __('مكافأة'),
            'Penalty'         => __('جزاء'),
            'Candidate'       => __('مرشح توظيف'),
            'JobOpening'      => __('شاغر وظيفي'),
            'User'            => __('مستخدم'),
            'Role'            => __('دور صلاحيات'),
            'Setting'         => __('إعدادات'),
            'NurseryBranch'   => __('فرع'),
            default           => $base,
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
            default            => ucfirst(str_replace('_', ' ', $field)),
        };
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

        return (string) $val;
    }
}
