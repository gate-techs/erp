<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;

class JobOpeningTranslation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hr_job_opening_translations';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'job_summary',
        'responsibilities',
        'requirements',
        'benefits',
        'education_level',
    ];
}
