<?php

namespace Modules\Academic\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicSubjectTranslation extends Model
{
    public $timestamps = false;

    protected $table = 'academic_subject_translations';

    protected $fillable = [
        'name',
        'description',
    ];
}
