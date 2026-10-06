<?php

namespace Modules\Academic\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicStageTranslation extends Model
{
    public $timestamps = false;

    protected $table = 'academic_stage_translations';

    protected $fillable = [
        'name',
        'description',
    ];
}
