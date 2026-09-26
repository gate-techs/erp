<?php

namespace Modules\Accounting\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Accounting\Traits\AccountingAuditable;

class PaymentMatching extends Model
{
    use HasFactory, SoftDeletes, AccountingAuditable;

    protected $table = 'accounting_payment_matchings';

    protected $fillable = [
        'entity_id',
        'debit_line_id',
        'credit_line_id',
        'matched_amount',
        'notes',
        'matched_by',
        'matched_at',
    ];

    protected $casts = [
        'entity_id' => 'integer',
        'debit_line_id' => 'integer',
        'credit_line_id' => 'integer',
        'matched_amount' => 'decimal:4',
        'matched_by' => 'integer',
        'matched_at' => 'datetime',
    ];

    //--------------------------------------------------
    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class, 'entity_id');
    }

    //--------------------------------------------------
    public function debitLine(): BelongsTo
    {
        return $this->belongsTo(JournalEntryLine::class, 'debit_line_id');
    }

    //--------------------------------------------------
    public function creditLine(): BelongsTo
    {
        return $this->belongsTo(JournalEntryLine::class, 'credit_line_id');
    }

    //--------------------------------------------------
    public function matchedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'matched_by');
    }
}
