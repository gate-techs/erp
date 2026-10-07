<?php

namespace Modules\Settings\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class NotificationBatch extends Model
{
    protected $table = 'notification_batches';

    protected $fillable = [
        'template_id', 'created_by', 'audience_type', 'audience_filter',
        'payload', 'status', 'recipients_count', 'scheduled_at', 'sent_at',
    ];

    protected $casts = [
        'audience_filter' => 'array',
        'payload' => 'array',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    public function template()
    {
        return $this->belongsTo(NotificationTemplate::class, 'template_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
