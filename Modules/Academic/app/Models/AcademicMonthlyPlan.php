<?php

namespace Modules\Academic\Models;

use App\Models\User;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Nursery\Models\Nursery;
use Modules\Nursery\Models\NurseryBranch;
use Modules\Settings\Models\Setting;

class AcademicMonthlyPlan extends Model implements TranslatableContract
{
    use HasFactory, SoftDeletes, Translatable;

    public array $translatedAttributes = [
        'title',
        'description',
    ];

    protected $table = 'academic_monthly_plans';

    protected $fillable = [
        'image',
        'institution_id',
        'branch_id',
        'academic_year_id',
        'academic_stage_id',
        'start_date',
        'end_date',
        'status',
        'created_by',
    ];

    protected $casts = [
        'institution_id'    => 'integer',
        'branch_id'         => 'integer',
        'academic_year_id'  => 'integer',
        'academic_stage_id' => 'integer',
        'start_date'        => 'date:Y-m-d',
        'end_date'          => 'date:Y-m-d',
    ];

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    public static function statuses(): array
    {
        return [
            self::STATUS_ACTIVE,
            self::STATUS_INACTIVE,
        ];
    }

    protected $appends = [
        'title_ar',
        'title_en',
        'description_ar',
        'description_en',
        'image_url',
        'dates_formatted',
    ];

    public function getImageUrlAttribute(): string
    {
        if ($this->image) {
            if (filter_var($this->image, FILTER_VALIDATE_URL)) {
                return $this->image;
            }
            if (str_starts_with($this->image, 'assets/')) {
                return asset($this->image);
            }
            return asset('storage/' . $this->image);
        }

        // Fallback 1: Institution / Nursery image
        if ($this->institution && $this->institution->image) {
            if (filter_var($this->institution->image, FILTER_VALIDATE_URL)) {
                return $this->institution->image;
            }
            return asset('storage/' . $this->institution->image);
        }

        // Fallback 2: General settings logo
        try {
            $setting = Setting::first();
            if ($setting && $setting->logo) {
                return asset('storage/' . $setting->logo);
            }
        } catch (\Throwable $e) {
            // Ignore if settings table not present
        }

        return asset('assets/img/favicon/favicon.ico');
    }

    public function getTitleArAttribute(): ?string
    {
        return $this->translate('ar')?->title;
    }

    public function getTitleEnAttribute(): ?string
    {
        return $this->translate('en')?->title;
    }

    public function getDescriptionArAttribute(): ?string
    {
        return $this->translate('ar')?->description;
    }

    public function getDescriptionEnAttribute(): ?string
    {
        return $this->translate('en')?->description;
    }

    public function getDatesFormattedAttribute(): string
    {
        $start = $this->start_date ? $this->start_date->format('Y-m-d') : '';
        $end = $this->end_date ? $this->end_date->format('Y-m-d') : '';

        if ($start && $end) {
            return "{$start} &rarr; {$end}";
        }

        return $start ?: $end ?: '-';
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

    public function academicStage(): BelongsTo
    {
        return $this->belongsTo(AcademicStage::class, 'academic_stage_id');
    }

    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(
            AcademicClass::class,
            'academic_monthly_plan_classes',
            'academic_monthly_plan_id',
            'academic_class_id'
        );
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(AcademicMonthlyPlanAttachment::class, 'academic_monthly_plan_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
