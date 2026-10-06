<?php

namespace Modules\Academic\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicYearTranslation extends Model
{
    public $timestamps = false;

    protected $table = 'academic_year_translations';

    protected $fillable = [
        'name',
    ];
}
