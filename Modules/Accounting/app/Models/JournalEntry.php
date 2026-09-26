<?php

namespace Modules\Accounting\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Accounting\Enums\JournalEntryStatus;
use Modules\Accounting\Enums\JournalEntryType;
use Modules\Accounting\Traits\AccountingAuditable;

class JournalEntry extends Model
{
    use HasFactory, SoftDeletes, AccountingAuditable;

    protected $table = 'journal_entries';

    protected $fillable = [
        'entity_id',
        'fiscal_year_id',
        'journal_id',
        'entry_number',
        'entry_date',
        'accounting_date',
        'reference',
        'entry_type',
        'currency',
        'exchange_rate',
        'status',
        'total_debit',
        'total_credit',
        'is_balanced',
        'narration',
        'posted_at',
        'posted_by',
        'reversed_entry_id',
        'is_reversal',
        'hash',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'entity_id' => 'integer',
        'fiscal_year_id' => 'integer',
        'journal_id' => 'integer',
        'entry_date' => 'date',
        'accounting_date' => 'date',
        'entry_type' => JournalEntryType::class,
        'exchange_rate' => 'decimal:6',
        'status' => JournalEntryStatus::class,
        'total_debit' => 'decimal:4',
        'total_credit' => 'decimal:4',
        'is_balanced' => 'boolean',
        'posted_at' => 'datetime',
        'posted_by' => 'integer',
        'reversed_entry_id' => 'integer',
        'is_reversal' => 'boolean',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    //--------------------------------------------------
    // Relationships
    //--------------------------------------------------

    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class, 'entity_id');
    }

    public function fiscalYear(): BelongsTo
    {
        return $this->belongsTo(FiscalYear::class, 'fiscal_year_id');
    }

    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class, 'journal_id');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class, 'journal_entry_id')->orderBy('line_order')->orderBy('id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function postedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function reversedEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'reversed_entry_id');
    }

    public function reversalEntry(): HasMany
    {
        return $this->hasMany(JournalEntry::class, 'reversed_entry_id');
    }

    //--------------------------------------------------
    // Scopes
    //--------------------------------------------------

    public function scopeDraft($query)
    {
        return $query->where('status', JournalEntryStatus::DRAFT);
    }

    public function scopePosted($query)
    {
        return $query->where('status', JournalEntryStatus::POSTED);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', JournalEntryStatus::CANCELLED);
    }

    public function scopeByEntity($query, int $entityId)
    {
        return $query->where('entity_id', $entityId);
    }

    //--------------------------------------------------
    // Business Helper Methods
    //--------------------------------------------------

    public function isDraft(): bool
    {
        return $this->status === JournalEntryStatus::DRAFT;
    }

    public function isPosted(): bool
    {
        return $this->status === JournalEntryStatus::POSTED;
    }

    public function isCancelled(): bool
    {
        return $this->status === JournalEntryStatus::CANCELLED;
    }

    public function canBeEdited(): bool
    {
        return $this->isDraft();
    }

    public function canBePosted(): bool
    {
        return $this->isDraft() && $this->is_balanced && (float) $this->total_debit > 0 && $this->lines()->count() >= 2;
    }

    public function canBeCancelled(): bool
    {
        return $this->isDraft();
    }

    public function canBeReversed(): bool
    {
        return $this->isPosted() && !$this->is_reversal && $this->reversalEntry()->doesntExist();
    }
}
