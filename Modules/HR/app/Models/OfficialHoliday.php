<?php

namespace Modules\HR\Models;

use App\Models\User;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\HR\Enums\OfficialHolidayStatus;
use Modules\Nursery\Models\NurseryBranch;

class OfficialHoliday extends Model implements TranslatableContract
{
    use HasFactory, SoftDeletes, Translatable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'official_holidays';

    /**
     * The attributes that are translatable.
     *
     * @var array<int, string>
     */
    public array $translatedAttributes = [
        'name',
        'description',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'start_date',
        'end_date',
        'days_count',
        'year',
        'is_recurring',
        'branch_id',
        'color',
        'status',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date'   => 'date:Y-m-d',
        'end_date'     => 'date:Y-m-d',
        'days_count'   => 'integer',
        'year'         => 'integer',
        'is_recurring' => 'boolean',
        'status'       => OfficialHolidayStatus::class,
    ];

    //======================
    /**
     * Relationship: Optional specific branch.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(NurseryBranch::class, 'branch_id');
    }

    //======================
    /**
     * Relationship: User who created the record.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
