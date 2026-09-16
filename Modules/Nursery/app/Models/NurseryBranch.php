<?php

namespace Modules\Nursery\Models;

use App\Models\User;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Nursery\Enums\NurseryStatus;
use Modules\Settings\Models\GeneralSetting;

class NurseryBranch extends Model implements TranslatableContract
{
    use HasFactory, SoftDeletes, Translatable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nursery_branches';

    /**
     * The attributes that are translatable.
     *
     * @var array<int, string>
     */
    public array $translatedAttributes = [
        'name',
        'description',
        'address',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nursery_id',
        'government_id',
        'city_id',
        'employee_id',
        'image',
        'lat',
        'lng',
        'whatsapp',
        'email',
        'status',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'lat' => 'decimal:8',
        'lng' => 'decimal:8',
        'status' => NurseryStatus::class,
    ];

    //======================
    /**
     * Get the parent nursery that owns the branch.
     */
    public function nursery(): BelongsTo
    {
        return $this->belongsTo(Nursery::class, 'nursery_id');
    }

    //======================
    /**
     * Get the government / governorate for this branch.
     */
    public function government(): BelongsTo
    {
        return $this->belongsTo(GeneralSetting::class, 'government_id');
    }

    //======================
    /**
     * Get the city / area for this branch.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(GeneralSetting::class, 'city_id');
    }

    //======================
    /**
     * Get the responsible employee assigned to this branch.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    //======================
    /**
     * Get the user who created this branch.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    //======================
    /**
     * Get work days and hours for this branch.
     */
    public function workDays(): HasMany
    {
        return $this->hasMany(NurseryBranchWorkDay::class, 'nursery_branch_id');
    }

    //======================
    /**
     * Accessor for full image URL.
     */
    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image) {
            return null;
        }

        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }

        return asset('storage/' . $this->image);
    }

    //======================
    /**
     * Accessor for Bootstrap badge class based on status.
     */
    public function getStatusBadgeAttribute(): string
    {
        return $this->status === NurseryStatus::ACTIVE
            ? 'bg-label-success'
            : 'bg-label-danger';
    }

    //======================
    /**
     * Accessor for translated status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->status instanceof NurseryStatus
            ? $this->status->label()
            : ($this->status === 'active' ? __('Active') : __('Inactive'));
    }

    //======================
    /**
     * Scope query to filter active branches.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', NurseryStatus::ACTIVE->value);
    }
}
