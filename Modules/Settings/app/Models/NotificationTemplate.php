<?php

namespace Modules\Settings\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotificationTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'settings_notification_templates';

    protected $fillable = [
        'code', 'type', 'module', 'name_ar', 'name_en', 'description',
        'icon', 'color', 'variables', 'is_active', 'is_system',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
        'is_system' => 'boolean',
    ];

    public function translations()
    {
        return $this->hasMany(NotificationTemplateTranslation::class, 'template_id');
    }

    public function translation(?string $locale = null): ?NotificationTemplateTranslation
    {
        $locale ??= app()->getLocale();

        return $this->translations->firstWhere('locale', $locale)
            ?? $this->translations->firstWhere('locale', 'en')
            ?? $this->translations->first();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
