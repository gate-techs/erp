<?php

namespace Modules\Academic\Models;

use App\Models\User;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Modules\Nursery\Models\Nursery;
use Modules\Nursery\Models\NurseryBranch;

class AcademicFee extends Model
{
    use HasFactory, SoftDeletes, Translatable;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    public const PERIOD_ONE_TIME = 'one_time';
    public const PERIOD_MONTHLY  = 'monthly';
    public const PERIOD_TERM     = 'term';
    public const PERIOD_ANNUALLY = 'annually';

    public array $translatedAttributes = [
        'name',
        'description',
    ];

    protected $table = 'academic_fees';

    protected $fillable = [
        'institution_id',
        'branch_id',
        'academic_year_id',
        'academic_stage_id',
        'academic_fee_type_id',
        'billing_period',
        'amount',
        'image',
        'status',
        'created_by',
    ];

    protected $casts = [
        'institution_id'       => 'integer',
        'branch_id'            => 'integer',
        'academic_year_id'     => 'integer',
        'academic_stage_id'    => 'integer',
        'academic_fee_type_id' => 'integer',
        'amount'               => 'decimal:2',
        'created_by'           => 'integer',
    ];

    protected $appends = [
        'image_url',
    ];

    public static function statuses(): array
    {
        return [
            self::STATUS_ACTIVE,
            self::STATUS_INACTIVE,
        ];
    }

    public static function billingPeriods(): array
    {
        return [
            self::PERIOD_ONE_TIME,
            self::PERIOD_MONTHLY,
            self::PERIOD_TERM,
            self::PERIOD_ANNUALLY,
        ];
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Nursery::class, 'institution_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(NurseryBranch::class, 'branch_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(AcademicStage::class, 'academic_stage_id');
    }

    public function feeType(): BelongsTo
    {
        return $this->belongsTo(AcademicFeeType::class, 'academic_fee_type_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->image && Storage::disk('public')->exists($this->image)) {
            return Storage::disk('public')->url($this->image);
        }

        if ($this->relationLoaded('institution') && $this->institution && $this->institution->logo) {
            return $this->institution->logo_url;
        }

        return asset('assets/img/favicon/favicon.ico');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }
}
