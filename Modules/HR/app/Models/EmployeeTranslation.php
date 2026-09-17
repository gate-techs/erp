<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeTranslation extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'employee_translations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'address_1',
        'address_2',
        'notes',
    ];
}
