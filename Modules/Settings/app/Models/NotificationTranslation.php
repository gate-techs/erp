<?php

namespace Modules\Settings\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationTranslation extends Model
{
    protected $table = 'notification_translations';

    protected $fillable = [
        'notification_id', 'locale', 'title', 'message', 'action_url',
    ];

    public function notification()
    {
        return $this->belongsTo(\Illuminate\Notifications\DatabaseNotification::class, 'notification_id');
    }
}
