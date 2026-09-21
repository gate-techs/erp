<?php

namespace Modules\Settings\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrintTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'settings_print_templates';

    protected $fillable = [
        'name',
        'name_ar',
        'code',
        'type',
        'description',
        'page_size',
        'orientation',
        'margins',
        'header_settings',
        'footer_settings',
        'signature_settings',
        'custom_css',
        'is_active',
        'is_default',
    ];

    protected $casts = [
        'margins' => 'array',
        'header_settings' => 'array',
        'footer_settings' => 'array',
        'signature_settings' => 'array',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    /**
     * Get display name based on current application locale.
     */
    public function getDisplayNameAttribute(): string
    {
        if (app()->getLocale() === 'ar' && !empty($this->name_ar)) {
            return $this->name_ar;
        }

        return $this->name;
    }

    /**
     * Get CSS page size / orientation rule.
     */
    public function getPageSizeCss(): string
    {
        $size = $this->page_size ?: 'A4';
        $orient = $this->orientation ?: 'portrait';

        return "{$size} {$orient}";
    }

    /**
     * Get CSS margin string.
     */
    public function getMarginStyle(): string
    {
        $margins = $this->margins ?? [];
        $unit = $margins['unit'] ?? 'mm';
        $top = $margins['top'] ?? 15;
        $right = $margins['right'] ?? 15;
        $bottom = $margins['bottom'] ?? 15;
        $left = $margins['left'] ?? 15;

        return "{$top}{$unit} {$right}{$unit} {$bottom}{$unit} {$left}{$unit}";
    }

    /**
     * Scope for active templates.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
