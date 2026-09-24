<?php

namespace Modules\Accounting\Models;

use App\Models\User;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\AuditLog\Traits\Auditable as TraitsAuditable;
use Modules\Nursery\Models\Nursery;
use OwenIt\Auditing\Contracts\Auditable;

class Entity extends Model implements TranslatableContract
{
    use HasFactory, SoftDeletes, Translatable, TraitsAuditable;

    protected $table = 'entities';

    public array $translatedAttributes = ['name', 'description'];

    protected $fillable = [
        'nursery_id',
        'code',
        'type',
        'currency',
        'status',
        'created_by',
    ];

    protected $casts = [
        'nursery_id' => 'integer',
        'created_by' => 'integer',
    ];

    //--------------------------------------------------
    public function nursery(): BelongsTo
    {
        return $this->belongsTo(Nursery::class, 'nursery_id');
    }

    //--------------------------------------------------
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function lockDate(): HasOne
    {
        return $this->hasOne(LockDate::class, 'entity_id');
    }

    //===============================
    public function lockExceptions(): HasMany
    {
        return $this->hasMany(LockException::class, 'entity_id');
    }

    //===============================
    public function costCenters(): HasMany
    {
        return $this->hasMany(CostCenter::class, 'entity_id');
    }

    //===============================
    public function branches(): HasMany
    {
        return $this->hasMany(\Modules\Nursery\Models\NurseryBranch::class, 'nursery_id', 'nursery_id');
    }
}
