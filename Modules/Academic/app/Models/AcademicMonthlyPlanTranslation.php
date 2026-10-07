<?php

namespace Modules\Academic\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicMonthlyPlanTranslation extends Model
{
    public $timestamps = false;

    protected $table = 'academic_monthly_plan_translations';

    protected $fillable = [
        'academic_monthly_plan_id',
        'locale',
        'title',
        'description',
    ];
}
