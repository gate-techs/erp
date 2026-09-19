<?php

namespace Modules\HR\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\HR\Enums\AttendanceSource;
use Modules\HR\Enums\AttendanceStatus;

class Attendance extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'attendances';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',
        'shift_id',
        'date',
        'check_in',
        'check_out',
        'total_working_hours',
        'required_hours',
        'late_minutes',
        'early_leave_minutes',
        'overtime_minutes',
        'status',
        'check_in_source',
        'check_out_source',
        'is_manual_adjusted',
        'adjustment_reason',
        'adjusted_by',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date'                => 'date:Y-m-d',
        'status'              => AttendanceStatus::class,
        'check_in_source'     => AttendanceSource::class,
        'check_out_source'    => AttendanceSource::class,
        'total_working_hours' => 'decimal:2',
        'required_hours'      => 'decimal:2',
        'late_minutes'        => 'integer',
        'early_leave_minutes' => 'integer',
        'overtime_minutes'    => 'integer',
        'is_manual_adjusted'  => 'boolean',
    ];

    //======================
    /**
     * Get the employee this attendance belongs to.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    //======================
    /**
     * Get the shift assigned for this attendance record.
     */
    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }

    //======================
    /**
     * Get user who adjusted the attendance manually.
     */
    public function adjuster(): BelongsTo
    {
        return $this->belongsTo(User::class, 'adjusted_by');
    }

    //======================
    /**
     * Get user who created this record.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
