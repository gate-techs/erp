<?php

namespace Modules\Tasks\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Tasks\Enums\TaskStatus;

class TaskStatusHistory extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'task_status_histories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'task_id',
        'old_status',
        'new_status',
        'changed_by',
        'comment',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'old_status' => TaskStatus::class,
        'new_status' => TaskStatus::class,
    ];

    //-----------------------------------------
    /**
     * Relationship: The task associated with this history entry.
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    //-----------------------------------------
    /**
     * Relationship: The user who changed the status.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    //-----------------------------------------
    /**
     * Get the changer's name safely.
     */
    public function getChangerNameAttribute(): string
    {
        return $this->user?->name ?? __('System');
    }
}
