<?php

namespace Modules\Accounting\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountTranslation extends Model
{
    use HasFactory;

    protected $table = 'account_translations';

    public $timestamps = false;

    protected $fillable = [
        'account_id',
        'locale',
        'name',
        'description',
    ];
}
