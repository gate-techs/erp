<?php

namespace Modules\Accounting\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LockException extends Model
{
    use HasFactory;

    public const TYPE_LOCK_ALL = 'lock_all';
    public const TYPE_TAX = 'tax';
    public const TYPE_FISCAL_YEAR = 'fiscal_year';

    protected $table = 'accounting_lock_exceptions';

    protected $fillable = [
        'entity_id',
        'user_id',
        'lock_type',
        'valid_from',
        'valid_until',
        'reason',
        'created_by',
        'revoked_at',
        'revoked_by',
    ];

    protected $casts = [
        'entity_id' => 'integer',
        'user_id' => 'integer',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'created_by' => 'integer',
        'revoked_at' => 'datetime',
        'revoked_by' => 'integer',
    ];

    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class, 'entity_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function revoker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revoked_by');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->whereNull('revoked_at')
            ->where('valid_from', '<=', now())
            ->where('valid_until', '>=', now());
    }

    public function scopeForType(Builder $query, string $lockType): Builder
    {
        return $query->where('lock_type', $lockType);
    }
}
