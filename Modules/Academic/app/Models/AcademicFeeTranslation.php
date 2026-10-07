<?php

namespace Modules\Academic\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicFeeTranslation extends Model
{
    protected $table = 'academic_fee_translations';

    protected $fillable = [
        'name',
        'description',
    ];
}
