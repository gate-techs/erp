<?php

namespace Modules\Accounting\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FiscalYearTranslation extends Model
{
    use HasFactory;

    protected $table = 'fiscal_year_translations';

    public $timestamps = false;

    protected $fillable = [
        'fiscal_year_id',
        'locale',
        'name',
        'description',
    ];
}
