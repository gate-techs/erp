<?php

namespace Modules\Accounting\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Accounting\Enums\BankReconciliationItemType;

class BankReconciliationItem extends Model
{
    use HasFactory;

    protected $table = 'bank_reconciliation_items';

    protected $fillable = [
        'bank_reconciliation_id',
        'journal_entry_line_id',
        'type',
        'statement_reference',
        'transaction_date',
        'debit',
        'credit',
        'is_cleared',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'bank_reconciliation_id' => 'integer',
        'journal_entry_line_id' => 'integer',
        'type' => BankReconciliationItemType::class,
        'transaction_date' => 'date',
        'debit' => 'decimal:4',
        'credit' => 'decimal:4',
        'is_cleared' => 'boolean',
        'created_by' => 'integer',
    ];

    //--------------------------------------------------
    public function reconciliation(): BelongsTo
    {
        return $this->belongsTo(BankReconciliation::class, 'bank_reconciliation_id');
    }

    //--------------------------------------------------
    public function journalEntryLine(): BelongsTo
    {
        return $this->belongsTo(JournalEntryLine::class, 'journal_entry_line_id');
    }

    //--------------------------------------------------
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
