<?php

namespace Modules\CRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\CRM\Enums\ServiceRequestStatus;
use Modules\CRM\Enums\ServiceRequestType;
use Modules\HR\Models\Department;
use Modules\Nursery\Models\NurseryBranch;

class ServiceRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'crm_service_requests';

    protected $fillable = [
        'lead_id',
        'civil_id',
        'name',
        'email',
        'type',
        'branch_id',
        'department_id',
        'call_recording_number',
        'notes',
        'image',
        'status',
    ];

    protected $casts = [
        'type' => ServiceRequestType::class,
        'status' => ServiceRequestStatus::class,
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(NurseryBranch::class, 'branch_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(ServiceRequestAttachment::class, 'service_request_id');
    }
}
