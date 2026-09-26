<?php

namespace Modules\Accounting\Traits;

use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

trait AccountingAuditable
{
    use LogsActivity;

    /**
     * Define activity log options for accounting models.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->dontLogIfAttributesChangedOnly([
                'updated_at',
            ])
            ->useLogName('accounting')
            ->setDescriptionForEvent(function (string $eventName) {
                $modelName = class_basename($this);
                $identifier = $this->entry_number 
                    ?? $this->code 
                    ?? $this->name 
                    ?? $this->reconciliation_number 
                    ?? ('#' . $this->getKey());

                $labels = [
                    'Account'            => 'الحساب المالي',
                    'JournalEntry'       => 'القيد اليومي',
                    'JournalEntryLine'   => 'سطر القيد',
                    'Journal'            => 'دفتر اليومية',
                    'CashAccount'        => 'حساب الخزينة/البنك',
                    'CostCenter'         => 'مركز التكلفة',
                    'BankReconciliation' => 'التسوية البنكية',
                    'PaymentMatching'    => 'مطابقة المدفوعات',
                    'FiscalYear'         => 'السنة المالية',
                    'LockDate'           => 'تواريخ الإقفال',
                    'LockException'      => 'استثناء فترة مقفلة',
                    'Entity'             => 'الكيان المحاسبي',
                ];

                $arModel = $labels[$modelName] ?? $modelName;

                return match ($eventName) {
                    'created'    => "تمت إضافة {$arModel}: {$identifier}",
                    'updated'    => "تم تعديل بيانات {$arModel}: {$identifier}",
                    'deleted'    => "تم حذف {$arModel}: {$identifier}",
                    'posted'     => "تم ترحيل واعتماد {$arModel}: {$identifier}",
                    'reversed'   => "تم إنشاء قيد عكسي لـ {$arModel}: {$identifier}",
                    'reconciled' => "تم اعتماد وإقفال {$arModel}: {$identifier}",
                    'reopened'   => "تمت إعادة فتح {$arModel}: {$identifier}",
                    default      => "تم إجراء ({$eventName}) على {$arModel}: {$identifier}",
                };
            });
    }
}
