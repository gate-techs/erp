<?php

namespace Modules\Academic\Models;

use Illuminate\Database\Eloquent\Model;

class EventTranslation extends Model
{
    public $timestamps = false;

    protected $table = 'event_translations';

    protected $fillable = [
        'event_id',
        'locale',
        'title',
        'description',
    ];
}
