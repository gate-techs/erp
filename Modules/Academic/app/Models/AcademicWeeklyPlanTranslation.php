<?php

namespace Modules\Academic\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicWeeklyPlanTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'academic_weekly_plan_id',
        'locale',
        'title',
        'content',
    ];
}
