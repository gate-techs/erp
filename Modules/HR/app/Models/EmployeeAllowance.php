<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeAllowance extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'employee_allowances';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',
        'allowance_type_id',
        'amount',
        'is_percentage',
        'is_recurring',
        'effective_date',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount'         => 'decimal:2',
        'is_percentage'  => 'boolean',
        'is_recurring'   => 'boolean',
        'effective_date' => 'date',
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
     * Relationship: Associated Allowance Type (from HR Settings).
     */
    public function allowanceType(): BelongsTo
    {
        return $this->belongsTo(HrSetting::class, 'allowance_type_id');
    }
}
