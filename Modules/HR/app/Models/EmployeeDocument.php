<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class EmployeeDocument extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'employee_documents';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',
        'document_type_id',
        'document_number',
        'file_path',
        'file_name',
        'issue_date',
        'expiry_date',
        'notify_before_days',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'issue_date'         => 'date',
        'expiry_date'        => 'date',
        'notify_before_days' => 'integer',
    ];

    //======================
    /**
     * Relationship: Associated Employee.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    //======================
    /**
     * Relationship: Associated Document Type (from HR Settings).
     */
    public function documentType(): BelongsTo
    {
        return $this->belongsTo(HrSetting::class, 'document_type_id');
    }

    //======================
    /**
     * Accessor: Full file URL.
     */
    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path ? Storage::disk('public')->url($this->file_path) : null;
    }
}
