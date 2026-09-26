<?php

namespace Modules\Accounting\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Accounting\Enums\BankReconciliationStatus;
use Modules\Accounting\Traits\AccountingAuditable;

class BankReconciliation extends Model
{
    use HasFactory, SoftDeletes, AccountingAuditable;

    protected $table = 'bank_reconciliations';

    protected $fillable = [
        'entity_id',
        'cash_account_id',
        'reconciliation_number',
        'statement_date',
        'statement_balance',
        'book_balance',
        'reconciled_balance',
        'difference',
        'status',
        'notes',
        'reconciled_at',
        'reconciled_by',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'entity_id' => 'integer',
        'cash_account_id' => 'integer',
        'statement_date' => 'date',
        'statement_balance' => 'decimal:4',
        'book_balance' => 'decimal:4',
        'reconciled_balance' => 'decimal:4',
        'difference' => 'decimal:4',
        'status' => BankReconciliationStatus::class,
        'reconciled_at' => 'datetime',
        'reconciled_by' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    //--------------------------------------------------
    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class, 'entity_id');
    }

    //--------------------------------------------------
    public function cashAccount(): BelongsTo
    {
        return $this->belongsTo(CashAccount::class, 'cash_account_id');
    }

    //--------------------------------------------------
    public function items(): HasMany
    {
        return $this->hasMany(BankReconciliationItem::class, 'bank_reconciliation_id');
    }

    //--------------------------------------------------
    public function reconciledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reconciled_by');
    }

    //--------------------------------------------------
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    //--------------------------------------------------
    public function isCompleted(): bool
    {
        return $this->status === BankReconciliationStatus::COMPLETED;
    }

    //--------------------------------------------------
    public function isDraft(): bool
    {
        return $this->status === BankReconciliationStatus::DRAFT;
    }
}
