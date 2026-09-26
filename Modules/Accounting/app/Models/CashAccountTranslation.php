<?php

namespace Modules\Accounting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashAccountTranslation extends Model
{
    protected $table = 'cash_account_translations';

    public $timestamps = false;

    protected $fillable = [
        'cash_account_id',
        'locale',
        'name',
        'description',
    ];

    //--------------------------------------------------
    public function cashAccount(): BelongsTo
    {
        return $this->belongsTo(CashAccount::class, 'cash_account_id');
    }
}
