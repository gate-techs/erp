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
use Modules\Nursery\Models\NurseryBranch;

class GuardianInterview extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'guardian_interviews';

    protected $fillable = [
        'user_id',
        'academic_guardian_id',
        'institution_id',
        'branch_id',
        'guardian_name',
        'guardian_civil_id',
        'guardian_phone',
        'guardian_email',
        'interview_date',
        'start_time',
        'end_time',
        'notes',
        'status',
        'color',
    ];

    protected $casts = [
        'user_id'              => 'integer',
        'academic_guardian_id' => 'integer',
        'institution_id'       => 'integer',
        'branch_id'            => 'integer',
        'interview_date'       => 'date:Y-m-d',
    ];

    // Status Constants
    public const STATUS_PLANNED   = 'planned';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_ABSENT    = 'absent';

    public static function statuses(): array
    {
        return [
            self::STATUS_PLANNED   => __('Planned'),
            self::STATUS_COMPLETED => __('Completed'),
            self::STATUS_CANCELLED => __('Cancelled'),
            self::STATUS_ABSENT    => __('Absent'),
        ];
    }

    public static function statusColors(): array
    {
        return [
            self::STATUS_PLANNED   => '#0b3252',
            self::STATUS_COMPLETED => '#28c76f',
            self::STATUS_CANCELLED => '#ea5455',
            self::STATUS_ABSENT    => '#ff9f43',
        ];
    }

    //---------------------------------------------------------
    // Relationships
    //---------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function guardian(): BelongsTo
    {
        return $this->belongsTo(AcademicGuardian::class, 'academic_guardian_id');
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Nursery::class, 'institution_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(NurseryBranch::class, 'branch_id');
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
        return $query->whereBetween('interview_date', [$startDate, $endDate]);
    }

    public function scopeThisMonth(Builder $query): Builder
    {
        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
        $endOfMonth   = Carbon::now()->endOfMonth()->toDateString();

        return $query->whereBetween('interview_date', [$startOfMonth, $endOfMonth]);
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        $today   = Carbon::today()->toDateString();
        $nowTime = Carbon::now()->format('H:i:s');

        return $query->where(function ($q) use ($today, $nowTime) {
            $q->where('interview_date', '>', $today)
              ->orWhere(function ($sub) use ($today, $nowTime) {
                  $sub->whereDate('interview_date', $today)
                      ->where('start_time', '>=', $nowTime);
              });
        });
    }
}
