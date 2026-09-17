<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;

class ShiftTranslation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shift_translations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
    ];
}
