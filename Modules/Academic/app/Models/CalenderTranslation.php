<?php

namespace Modules\Academic\Models;

use Illuminate\Database\Eloquent\Model;

class CalenderTranslation extends Model
{
    public $timestamps = false;

    protected $table = 'calender_translations';

    protected $fillable = [
        'calender_id',
        'locale',
        'title',
        'description',
    ];
}
