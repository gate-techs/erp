<?php

namespace Modules\AI\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AIUsageLog extends Model
{
    protected $table = 'ai_usage_logs';

    protected $fillable = [
        'provider_code',
        'driver',
        'model',
        'task_code',
        'prompt_tokens',
        'completion_tokens',
        'total_tokens',
        'characters_count',
        'user_id',
        'status',
        'error_message',
    ];

    protected function casts(): array
    {
        return [
            'prompt_tokens' => 'integer',
            'completion_tokens' => 'integer',
            'total_tokens' => 'integer',
            'characters_count' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function record(array $data): ?self
    {
        if (!config('ai.log_usage', false)) {
            return null;
        }

        try {
            return self::create([
                'provider_code' => $data['provider_code'] ?? 'unknown',
                'driver' => $data['driver'] ?? 'unknown',
                'model' => $data['model'] ?? null,
                'task_code' => $data['task_code'] ?? null,
                'prompt_tokens' => (int) ($data['prompt_tokens'] ?? 0),
                'completion_tokens' => (int) ($data['completion_tokens'] ?? 0),
                'total_tokens' => (int) ($data['total_tokens'] ?? 0),
                'characters_count' => (int) ($data['characters_count'] ?? 0),
                'user_id' => auth()->id(),
                'status' => $data['status'] ?? 'success',
                'error_message' => $data['error_message'] ?? null,
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to record AI usage log: ' . $e->getMessage());
            return null;
        }
    }
}
