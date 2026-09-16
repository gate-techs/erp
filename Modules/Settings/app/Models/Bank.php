<?php

namespace Modules\Settings\Models;

use App\Models\User;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bank extends Model implements TranslatableContract
{
    use HasFactory, SoftDeletes, Translatable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'banks';

    /**
     * The attributes that are translatable.
     *
     * @var array<int, string>
     */
    public array $translatedAttributes = [
        'name',
        'address',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'parent_id',
        'code',
        'swift_code',
        'status',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'string',
    ];

    //======================
    /**
     * Get the parent bank (for branches).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Bank::class, 'parent_id');
    }

    //======================
    /**
     * Get the branches of this bank.
     */
    public function branches(): HasMany
    {
        return $this->hasMany(Bank::class, 'parent_id');
    }

    //======================
    /**
     * User who created this record.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    //======================
    /**
     * Scope for Main Banks only (parent_id is null).
     */
    public function scopeMainBanks($query)
    {
        return $query->whereNull('parent_id');
    }

    //======================
    /**
     * Scope for Bank Branches only (parent_id is not null).
     */
    public function scopeBankBranches($query)
    {
        return $query->whereNotNull('parent_id');
    }
}
