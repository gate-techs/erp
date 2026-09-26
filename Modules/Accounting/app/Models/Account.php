<?php

namespace Modules\Accounting\Models;

use App\Models\User;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Accounting\Enums\AccountType;
use Modules\Accounting\Traits\AccountingAuditable;

class Account extends Model implements TranslatableContract
{
    use HasFactory, SoftDeletes, Translatable, AccountingAuditable;

    protected $table = 'accounts';

    public array $translatedAttributes = ['name', 'description'];

    protected $fillable = [
        'entity_id',
        'parent_id',
        'code',
        'account_type',
        'normal_balance',
        'level',
        'is_group',
        'allow_posting',
        'reconcile',
        'is_active',
        'is_deprecated',
        'sort_order',
        'created_by',
    ];

    protected $casts = [
        'entity_id' => 'integer',
        'parent_id' => 'integer',
        'account_type' => AccountType::class,
        'level' => 'integer',
        'is_group' => 'boolean',
        'allow_posting' => 'boolean',
        'reconcile' => 'boolean',
        'is_active' => 'boolean',
        'is_deprecated' => 'boolean',
        'sort_order' => 'integer',
        'created_by' => 'integer',
    ];

    //--------------------------------------------------
    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class, 'entity_id');
    }

    //--------------------------------------------------
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    //--------------------------------------------------
    public function children(): HasMany
    {
        return $this->hasMany(Account::class, 'parent_id')->orderBy('sort_order')->orderBy('code');
    }

    //--------------------------------------------------
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    //--------------------------------------------------
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('is_deprecated', false);
    }

    //--------------------------------------------------
    public function scopePosting($query)
    {
        return $query->where('allow_posting', true);
    }
}
