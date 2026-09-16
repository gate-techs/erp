<?php

namespace Modules\Nursery\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NurseryBranchTranslation extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nursery_branch_translations';

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
        'nursery_branch_id',
        'locale',
        'name',
        'description',
        'address',
    ];

    //======================
    /**
     * Get parent branch record.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(NurseryBranch::class, 'nursery_branch_id');
    }
}
