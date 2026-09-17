<?php

namespace Modules\HR\Models;

use App\Models\User;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\HR\Enums\ShiftStatus;

class Shift extends Model implements TranslatableContract
{
    use HasFactory, SoftDeletes, Translatable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shifts';

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
        'code',
        'start_time',
        'end_time',
        'checkin_start',
        'checkin_end',
        'checkout_start',
        'checkout_end',
        'grace_period_in',
        'grace_period_out',
        'total_hours',
        'is_overnight',
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
        'status'           => ShiftStatus::class,
        'is_overnight'     => 'boolean',
        'grace_period_in'  => 'integer',
        'grace_period_out' => 'integer',
        'total_hours'      => 'decimal:2',
    ];

    //======================
    /**
     * Get the user that created the shift.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
