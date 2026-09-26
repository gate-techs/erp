<?php

namespace Modules\AI\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AITask extends Model
{
    protected $table = 'ai_tasks';

    protected $fillable = ['code', 'name', 'description', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function providers(): BelongsToMany
    {
        return $this->belongsToMany(AIProvider::class, 'ai_task_providers', 'task_id', 'provider_id')
            ->withPivot(['priority', 'is_default', 'is_active'])
            ->withTimestamps();
    }

    public function getIconAttribute(): string
    {
        return match ($this->code) {
            'translation' => 'bx bx-globe',
            'text_generation' => 'bx bx-edit-alt',
            'report_summary' => 'bx bx-file-blank',
            'ocr_invoice' => 'bx bx-receipt',
            'forecasting' => 'bx bx-trending-up',
            'financial_assistant' => 'bx bx-bot',
            default => 'bx bx-bot',
        };
    }

    public function getColorAttribute(): string
    {
        return match ($this->code) {
            'translation' => 'primary',
            'text_generation' => 'success',
            'report_summary' => 'info',
            'ocr_invoice' => 'warning',
            'forecasting' => 'danger',
            'financial_assistant' => 'primary',
            default => 'secondary',
        };
    }
}
