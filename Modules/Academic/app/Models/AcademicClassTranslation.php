<?php

namespace Modules\Academic\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicClassTranslation extends Model
{
    public $timestamps = false;

    protected $table = 'academic_class_translations';

    protected $fillable = [
        'name',
    ];
}
