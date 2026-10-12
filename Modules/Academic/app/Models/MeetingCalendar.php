<?php

namespace Modules\Academic\Models;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Nursery\Models\Nursery;

class MeetingCalendar extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'meeting_calendars';

    protected $fillable = [
        'user_id',
        'institution_id',
        'title',
        'description',
        'event_date',
        'start_time',
        'end_time',
        'status',
        'color',
    ];

    protected $casts = [
        'user_id'        => 'integer',
        'institution_id' => 'integer',
        'event_date'     => 'date:Y-m-d',
    ];

    // Status Constants
    public const STATUS_PLANNED     = 'planned';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED   = 'completed';
    public const STATUS_CANCELLED   = 'cancelled';

    public static function statuses(): array
    {
        return [
            self::STATUS_PLANNED     => __('Planned'),
            self::STATUS_IN_PROGRESS => __('In Progress'),
            self::STATUS_COMPLETED   => __('Completed'),
            self::STATUS_CANCELLED   => __('Cancelled'),
        ];
    }

    // Default status colors
    public static function statusColors(): array
    {
        return [
            self::STATUS_PLANNED     => '#0b3252',
            self::STATUS_IN_PROGRESS => '#ff9f43',
            self::STATUS_COMPLETED   => '#28c76f',
            self::STATUS_CANCELLED   => '#ea5455',
        ];
    }

    //---------------------------------------------------------
    // Relationships
    //---------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Nursery::class, 'institution_id');
    }

    //---------------------------------------------------------
    // Query Scopes
    //---------------------------------------------------------

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeForDateRange(Builder $query, string $startDate, string $endDate): Builder
    {
        return $query->whereBetween('event_date', [$startDate, $endDate]);
    }

    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('event_date', Carbon::today());
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        $today = Carbon::today()->format('Y-m-d');
        $nowTime = Carbon::now()->format('H:i:s');

        return $query->where(function ($q) use ($today, $nowTime) {
            $q->where('event_date', '>', $today)
              ->orWhere(function ($sub) use ($today, $nowTime) {
                  $sub->whereDate('event_date', $today)
                      ->where('start_time', '>=', $nowTime);
              });
        });
    }
}
