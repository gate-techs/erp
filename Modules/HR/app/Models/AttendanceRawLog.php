<?php

namespace Modules\HR\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\HR\Enums\AttendanceSource;
use Modules\HR\Enums\PunchType;

class AttendanceRawLog extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'attendance_raw_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',
        'device_user_id',
        'punch_time',
        'punch_type',
        'source',
        'device_id',
        'latitude',
        'longitude',
        'location_address',
        'ip_address',
        'device_info',
        'snapshot',
        'is_processed',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'punch_time'   => 'datetime',
        'punch_type'   => PunchType::class,
        'source'       => AttendanceSource::class,
        'latitude'     => 'decimal:8',
        'longitude'    => 'decimal:8',
        'is_processed' => 'boolean',
    ];

    //======================
    /**
     * Get the employee this punch belongs to.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    //======================
    /**
     * Get the device if punched via biometric device.
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(BiometricDevice::class, 'device_id');
    }

    //======================
    /**
     * Get the user who recorded this punch if manual.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
