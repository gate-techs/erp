<?php

namespace Modules\Accounting\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Accounting\Traits\AccountingAuditable;

class LockDate extends Model
{
    use HasFactory, AccountingAuditable;

    protected $table = 'accounting_lock_dates';

    protected $fillable = [
        'entity_id',
        'lock_all_date',
        'tax_lock_date',
        'fiscal_year_lock_date',
        'hard_lock_date',
        'updated_by',
    ];

    protected $casts = [
        'entity_id' => 'integer',
        'lock_all_date' => 'date',
        'tax_lock_date' => 'date',
        'fiscal_year_lock_date' => 'date',
        'hard_lock_date' => 'date',
        'updated_by' => 'integer',
    ];

    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class, 'entity_id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function exceptions(): HasMany
    {
        return $this->hasMany(LockException::class, 'entity_id', 'entity_id');
    }
}
