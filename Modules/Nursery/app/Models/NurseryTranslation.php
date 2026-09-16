<?php

namespace Modules\Nursery\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NurseryTranslation extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nursery_translations';

    /**
     * Disable timestamps for translation table.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nursery_id',
        'locale',
        'name',
        'description',
    ];

    //======================
    /**
     * Get parent nursery record.
     */
    public function nursery(): BelongsTo
    {
        return $this->belongsTo(Nursery::class);
    }
}
