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

class FiscalYear extends Model implements TranslatableContract
{
    use HasFactory, SoftDeletes, Translatable;

    protected $table = 'fiscal_years';

    public array $translatedAttributes = ['name', 'description'];

    protected $fillable = [
        'entity_id',
        'start_date',
        'end_date',
        'status',
        'created_by',
        'closed_by',
        'closed_at',
    ];

    protected $casts = [
        'entity_id' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'closed_at' => 'datetime',
        'created_by' => 'integer',
        'closed_by' => 'integer',
    ];

    //===============================
    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class, 'entity_id');
    }

    //===============================
    public function translations(): HasMany
    {
        return $this->hasMany(FiscalYearTranslation::class, 'fiscal_year_id');
    }

    //===============================
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    //===============================
    public function closer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    //===============================
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }
}
