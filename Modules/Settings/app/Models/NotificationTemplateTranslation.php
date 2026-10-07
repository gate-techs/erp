<?php

namespace Modules\Settings\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationTemplateTranslation extends Model
{
    protected $table = 'settings_notification_template_translations';

    protected $fillable = ['template_id', 'locale', 'title', 'message', 'action_url'];

    public function template()
    {
        return $this->belongsTo(NotificationTemplate::class, 'template_id');
    }
}
