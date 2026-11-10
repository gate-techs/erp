<?php

namespace Modules\HR\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\HR\Enums\ClearanceApprovalStatus;

class ExitClearanceApproval extends Model
{
    use HasFactory;

    protected $table = 'hr_exit_clearance_approvals';

    protected $fillable = [
        'exit_clearance_id',
        'department_key',
        'department_name',
        'status',
        'approved_by',
        'approved_at',
        'notes',
        'dues_amount',
        'items_checklist',
        'sort_order',
    ];

    protected $casts = [
        'status'          => ClearanceApprovalStatus::class,
        'approved_at'     => 'datetime',
        'dues_amount'     => 'decimal:2',
        'items_checklist' => 'array',
        'sort_order'      => 'integer',
    ];

    public function clearance(): BelongsTo
    {
        return $this->belongsTo(ExitClearance::class, 'exit_clearance_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}

