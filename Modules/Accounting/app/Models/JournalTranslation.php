<?php

namespace Modules\Accounting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JournalTranslation extends Model
{
    protected $table = 'journal_translations';

    public $timestamps = false;

    protected $fillable = [
        'journal_id',
        'locale',
        'name',
        'description',
    ];

    //--------------------------------------------------
    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class, 'journal_id');
    }
}
