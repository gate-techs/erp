<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\HR\Enums\OnboardingTaskCategory;

class OnboardingTask extends Model
{
    use HasFactory;

    protected $table = 'hr_onboarding_tasks';

    protected $fillable = [
        'onboarding_id',
        'title',
        'category',
        'is_completed',
        'completed_at',
        'assigned_to',
        'notes',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function getCategoryAttribute(?string $value): OnboardingTaskCategory
    {
        return OnboardingTaskCategory::tryFrom((string) $value) ?? OnboardingTaskCategory::DOCUMENTATION;
    }

    public function setCategoryAttribute(mixed $value): void
    {
        $this->attributes['category'] = $value instanceof OnboardingTaskCategory ? $value->value : $value;
    }

    public function onboarding(): BelongsTo
    {
        return $this->belongsTo(Onboarding::class, 'onboarding_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_to');
    }
}
