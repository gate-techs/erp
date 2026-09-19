<?php

namespace Modules\HR\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Nursery\Models\NurseryBranch;

class BiometricDevice extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'biometric_devices';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'branch_id',
        'name',
        'ip_address',
        'port',
        'device_serial_number',
        'model',
        'protocol',
        'status',
        'last_sync_at',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'port'         => 'integer',
        'last_sync_at' => 'datetime',
    ];

    //======================
    /**
     * Get the branch the device belongs to.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(NurseryBranch::class, 'branch_id');
    }

    //======================
    /**
     * Get the raw logs from this device.
     */
    public function rawLogs(): HasMany
    {
        return $this->hasMany(AttendanceRawLog::class, 'device_id');
    }

    //======================
    /**
     * Get the user who registered this device.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
