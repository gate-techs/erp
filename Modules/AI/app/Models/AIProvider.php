<?php

namespace Modules\AI\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;

class AIProvider extends Model
{
    protected $table = 'ai_providers';

    protected $fillable = ['code', 'name', 'driver', 'api_key', 'model', 'base_url', 'settings', 'is_active'];

    protected function casts(): array
    {
        return ['api_key' => 'encrypted', 'settings' => 'array', 'is_active' => 'boolean'];
    }

    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(AITask::class, 'ai_task_providers', 'provider_id', 'task_id')
            ->withPivot(['priority', 'is_default', 'is_active'])
            ->withTimestamps();
    }

    public function maskedApiKey(): string
    {
        if (empty($this->api_key)) {
            return __('Not configured');
        }

        $length = strlen($this->api_key);
        if ($length > 8) {
            return str_repeat('•', 8) . substr($this->api_key, -4);
        }

        return str_repeat('•', 8);
    }

    public function getDriverLabelAttribute(): string
    {
        return match (strtolower($this->driver)) {
            'gemini' => 'Google Gemini',
            'openai' => 'OpenAI (ChatGPT)',
            'deepl' => 'DeepL Translator',
            'ollama' => 'Ollama (Local)',
            'claude' => 'Anthropic Claude',
            default => ucfirst($this->driver),
        };
    }

    public function getDriverIconAttribute(): string
    {
        return match (strtolower($this->driver)) {
            'gemini' => 'bx bxs-sparkles',
            'openai' => 'bx bx-bot',
            'deepl' => 'bx bx-transfer',
            default => 'bx bx-chip',
        };
    }

    public function getDriverColorAttribute(): string
    {
        return match (strtolower($this->driver)) {
            'gemini' => 'info',
            'openai' => 'success',
            'deepl' => 'primary',
            default => 'dark',
        };
    }
}
