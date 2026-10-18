<?php

namespace Modules\EmployeePortal\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\HR\Models\Employee;

class EmployeeNotificationPreference extends Model
{
    use HasFactory;

    protected $table = 'employee_notification_preferences';

    protected $fillable = [
        'employee_id',
        'user_id',
        'morning_reminder_enabled',
        'morning_reminder_mins',
        'checkout_reminder_enabled',
        'punch_success_alert',
        'payroll_alert_enabled',
        'loans_alert_enabled',
        'rewards_alert_enabled',
        'leave_requests_alert',
        'permission_requests_alert',
        'admin_memos_alert',
        'policy_updates_alert',
        'push_enabled',
        'whatsapp_enabled',
        'email_enabled',
        'quiet_hours_enabled',
        'quiet_hours_start',
        'quiet_hours_end',
    ];

    protected $casts = [
        'morning_reminder_enabled'  => 'boolean',
        'morning_reminder_mins'     => 'integer',
        'checkout_reminder_enabled' => 'boolean',
        'punch_success_alert'       => 'boolean',
        'payroll_alert_enabled'     => 'boolean',
        'loans_alert_enabled'       => 'boolean',
        'rewards_alert_enabled'     => 'boolean',
        'leave_requests_alert'      => 'boolean',
        'permission_requests_alert' => 'boolean',
        'admin_memos_alert'         => 'boolean',
        'policy_updates_alert'      => 'boolean',
        'push_enabled'              => 'boolean',
        'whatsapp_enabled'          => 'boolean',
        'email_enabled'             => 'boolean',
        'quiet_hours_enabled'       => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
