<?php

namespace Modules\Accounting\Models;

use App\Models\User;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Accounting\Enums\CashAccountType;
use Modules\Accounting\Traits\AccountingAuditable;

class CashAccount extends Model implements TranslatableContract
{
    use HasFactory, SoftDeletes, Translatable, AccountingAuditable;

    protected $table = 'cash_accounts';

    public array $translatedAttributes = ['name', 'description'];

    protected $fillable = [
        'entity_id',
        'account_id',
        'journal_id',
        'type',
        'code',
        'bank_name',
        'account_number',
        'iban',
        'swift_code',
        'currency',
        'opening_balance',
        'current_balance',
        'responsible_user_id',
        'is_active',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'entity_id' => 'integer',
        'account_id' => 'integer',
        'journal_id' => 'integer',
        'type' => CashAccountType::class,
        'opening_balance' => 'decimal:4',
        'current_balance' => 'decimal:4',
        'responsible_user_id' => 'integer',
        'is_active' => 'boolean',
        'created_by' => 'integer',
    ];

    //--------------------------------------------------
    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class, 'entity_id');
    }

    //--------------------------------------------------
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    //--------------------------------------------------
    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class, 'journal_id');
    }

    //--------------------------------------------------
    public function responsibleUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_user_id');
    }

    //--------------------------------------------------
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    //--------------------------------------------------
    public function isCash(): bool
    {
        return $this->type === CashAccountType::CASH;
    }

    //--------------------------------------------------
    public function isBank(): bool
    {
        return $this->type === CashAccountType::BANK;
    }
}
