<?php

namespace Modules\Academic\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicFeeTypeTranslation extends Model
{
    protected $table = 'academic_fee_type_translations';

    protected $fillable = [
        'name',
        'description',
    ];
}
