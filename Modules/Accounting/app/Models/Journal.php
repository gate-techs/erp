<?php

namespace Modules\Accounting\Models;

use App\Models\User;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Accounting\Enums\JournalType;
use Modules\Accounting\Traits\AccountingAuditable;

class Journal extends Model implements TranslatableContract
{
    use HasFactory, SoftDeletes, Translatable, AccountingAuditable;

    protected $table = 'journals';

    public array $translatedAttributes = ['name', 'description'];

    protected $fillable = [
        'entity_id',
        'code',
        'type',
        'currency',
        'sequence_prefix',
        'sequence_number_next',
        'refund_sequence',
        'payment_sequence',
        'default_account_id',
        'suspense_account_id',
        'profit_account_id',
        'loss_account_id',
        'allow_manual_entries',
        'reconcile',
        'restrict_mode_hash_table',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'entity_id' => 'integer',
        'type' => JournalType::class,
        'sequence_number_next' => 'integer',
        'refund_sequence' => 'boolean',
        'payment_sequence' => 'boolean',
        'default_account_id' => 'integer',
        'suspense_account_id' => 'integer',
        'profit_account_id' => 'integer',
        'loss_account_id' => 'integer',
        'allow_manual_entries' => 'boolean',
        'reconcile' => 'boolean',
        'restrict_mode_hash_table' => 'boolean',
        'is_active' => 'boolean',
        'created_by' => 'integer',
    ];

    //--------------------------------------------------
    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class, 'entity_id');
    }

    //--------------------------------------------------
    public function translations(): HasMany
    {
        return $this->hasMany(JournalTranslation::class, 'journal_id');
    }

    //--------------------------------------------------
    public function defaultAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'default_account_id');
    }

    //--------------------------------------------------
    public function suspenseAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'suspense_account_id');
    }

    //--------------------------------------------------
    public function profitAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'profit_account_id');
    }

    //--------------------------------------------------
    public function lossAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'loss_account_id');
    }

    //--------------------------------------------------
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    //--------------------------------------------------
    public function entries(): HasMany
    {
        return $this->hasMany(JournalEntry::class, 'journal_id');
    }

    //--------------------------------------------------
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
