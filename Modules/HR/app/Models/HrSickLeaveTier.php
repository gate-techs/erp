<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrSickLeaveTier extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hr_sick_leave_tiers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name_ar',
        'name_en',
        'days_count',
        'salary_percentage',
        'sort_order',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'days_count'        => 'integer',
        'salary_percentage' => 'float',
        'sort_order'        => 'integer',
    ];

    //======================
    /**
     * Localized name accessor.
     */
    public function getNameAttribute(): string
    {
        return app()->getLocale() === 'ar' ? ($this->name_ar ?: ($this->name_en ?? '')) : ($this->name_en ?: ($this->name_ar ?? ''));
    }
}
