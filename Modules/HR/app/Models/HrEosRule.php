<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrEosRule extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hr_eos_rules';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'name_ar',
        'name_en',
        'from_years',
        'to_years',
        'percentage',
        'sort_order',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'from_years'  => 'float',
        'to_years'    => 'float',
        'percentage'  => 'float',
        'sort_order'  => 'integer',
    ];

    //======================
    /**
     * Localized label accessor.
     */
    public function getNameAttribute(): string
    {
        return app()->getLocale() === 'ar' ? ($this->name_ar ?: ($this->name_en ?? '')) : ($this->name_en ?: ($this->name_ar ?? ''));
    }
}
