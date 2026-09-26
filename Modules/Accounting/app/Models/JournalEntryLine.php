<?php

namespace Modules\Accounting\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JournalEntryLine extends Model
{
    use HasFactory;

    protected $table = 'journal_entry_lines';

    protected $fillable = [
        'journal_entry_id',
        'account_id',
        'cost_center_id',
        'partner_id',
        'partner_type',
        'partner_name',
        'description',
        'debit',
        'credit',
        'currency',
        'amount_currency',
        'reconciled',
        'matching_number',
        'line_order',
    ];

    protected $casts = [
        'journal_entry_id' => 'integer',
        'account_id' => 'integer',
        'cost_center_id' => 'integer',
        'partner_id' => 'integer',
        'debit' => 'decimal:4',
        'credit' => 'decimal:4',
        'amount_currency' => 'decimal:4',
        'reconciled' => 'boolean',
        'line_order' => 'integer',
    ];

    //--------------------------------------------------
    // Relationships
    //--------------------------------------------------

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }

    public function entry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function costCenter(): BelongsTo
    {
        return $this->belongsTo(CostCenter::class, 'cost_center_id');
    }

    //--------------------------------------------------
    // Helpers
    //--------------------------------------------------

    public function isDebit(): bool
    {
        return (float) $this->debit > 0;
    }

    public function isCredit(): bool
    {
        return (float) $this->credit > 0;
    }

    public function getBalanceAttribute(): float
    {
        return (float) $this->debit - (float) $this->credit;
    }
}
