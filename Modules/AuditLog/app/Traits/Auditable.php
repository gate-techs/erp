<?php

namespace Modules\AuditLog\Traits;

use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

trait Auditable
{
    use LogsActivity;

    /**
     * Define activity log options for the model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->dontLogIfAttributesChangedOnly([
                'updated_at',
                'remember_token',
                'last_login_at',
                'password',
            ])
            ->useLogName(strtolower(class_basename($this)))
            ->setDescriptionForEvent(function (string $eventName) {
                $modelName = class_basename($this);
                $identifier = $this->name ?? $this->title ?? $this->employee_code ?? ('#' . $this->getKey());

                return match ($eventName) {
                    'created' => "تمت إضافة {$modelName}: {$identifier}",
                    'updated' => "تم تحديث بيانات {$modelName}: {$identifier}",
                    'deleted' => "تم حذف {$modelName}: {$identifier}",
                    default   => "تم تنفيذ إجراء ({$eventName}) على {$modelName}: {$identifier}",
                };
            });
    }
}
