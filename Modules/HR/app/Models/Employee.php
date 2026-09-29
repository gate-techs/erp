<?php

namespace Modules\HR\Models;

use App\Models\User;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Modules\HR\Enums\EmployeeStatus;
use Modules\HR\Enums\EmploymentType;
use Modules\HR\Enums\Gender;
use Modules\HR\Enums\MaritalStatus;
use Modules\HR\Models\Attendance;
use Modules\HR\Models\LeaveRequest;
use Modules\HR\Models\Loan;
use Modules\HR\Models\Penalty;
use Modules\HR\Models\PermissionRequest;
use Modules\HR\Models\Reward;
use Modules\AuditLog\Traits\Auditable;
use Modules\Nursery\Models\NurseryBranch;
use Modules\Settings\Models\Bank;
use Modules\Settings\Models\GeneralSetting;

class Employee extends Model implements TranslatableContract
{
    use HasFactory, SoftDeletes, Translatable, Auditable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'employees';

    /**
     * The attributes that are translatable.
     *
     * @var array<int, string>
     */
    public array $translatedAttributes = [
        'name',
        'address_1',
        'address_2',
        'notes',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_code',
        'is_teacher',
        'user_id',
        'avatar',
        'gender',
        'marital_status',
        'religion',
        'nationality',
        'date_of_birth',
        'national_id',
        'blood_type',
        'visa_type_id',
        'visa_expiry_date',
        'passport_number',
        'passport_issue_date',
        'passport_expiry_date',
        'primary_branch_id',
        'department_id',
        'job_title_id',
        'manager_id',
        'work_schedule_id',
        'shift_id',
        'employment_type',
        'hire_date',
        'probation_end_date',
        'contract_end_date',
        'status',
        'phone',
        'alternate_phone',
        'email',
        'governorate_id',
        'city',
        'emergency_contact_name',
        'emergency_contact_relation',
        'emergency_contact_phone',
        'bank_id',
        'bank_branch_id',
        'account_holder_name',
        'account_number',
        'iban',
        'basic_salary',
        'hourly_rate',
        'daily_working_hours',
        'social_insurance_number',
        'social_insurance_percentage',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_teacher'                  => 'boolean',
        'gender'                      => Gender::class,
        'employment_type'             => EmploymentType::class,
        'status'                      => EmployeeStatus::class,
        'date_of_birth'               => 'date',
        'visa_expiry_date'            => 'date',
        'passport_issue_date'         => 'date',
        'passport_expiry_date'        => 'date',
        'hire_date'                   => 'date',
        'probation_end_date'          => 'date',
        'contract_end_date'           => 'date',
        'basic_salary'                => 'decimal:2',
        'hourly_rate'                 => 'decimal:2',
        'daily_working_hours'         => 'decimal:2',
        'social_insurance_percentage' => 'decimal:2',
    ];

    //======================
    /**
     * Relationship: Linked system user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    //======================
    /**
     * Relationship: Visa Type (HR Settings).
     */
    public function visaType(): BelongsTo
    {
        return $this->belongsTo(HrSetting::class, 'visa_type_id');
    }

    //======================
    /**
     * Relationship: Primary Branch.
     */
    public function primaryBranch(): BelongsTo
    {
        return $this->belongsTo(NurseryBranch::class, 'primary_branch_id');
    }

    //======================
    /**
     * Relationship: Branch (Alias to primaryBranch).
     */
    public function branch(): BelongsTo
    {
        return $this->primaryBranch();
    }

    //======================
    /**
     * Relationship: Department.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    //======================
    /**
     * Relationship: Job Title.
     */
    public function jobTitle(): BelongsTo
    {
        return $this->belongsTo(JobTitle::class, 'job_title_id');
    }

    //======================
    /**
     * Relationship: Direct Manager.
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    //======================
    /**
     * Relationship: Direct Subordinates.
     */
    public function subordinates(): HasMany
    {
        return $this->hasMany(Employee::class, 'manager_id');
    }

    //======================
    /**
     * Relationship: Assigned Work Schedule.
     */
    public function workSchedule(): BelongsTo
    {
        return $this->belongsTo(WorkSchedule::class, 'work_schedule_id');
    }

    //======================
    /**
     * Relationship: Assigned Shift.
     */
    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }

    //======================
    /**
     * Relationship: Governorate (from General Settings).
     */
    public function governorate(): BelongsTo
    {
        return $this->belongsTo(GeneralSetting::class, 'governorate_id');
    }

    //======================
    /**
     * Relationship: Bank.
     */
    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class, 'bank_id');
    }

    //======================
    /**
     * Relationship: Bank Branch.
     */
    public function bankBranch(): BelongsTo
    {
        return $this->belongsTo(Bank::class, 'bank_branch_id');
    }

    //======================
    /**
     * Relationship: Assigned Branches Pivot entries.
     */
    public function employeeBranches(): HasMany
    {
        return $this->hasMany(EmployeeBranch::class, 'employee_id');
    }

    //======================
    /**
     * Relationship: Many-to-Many Assigned Branches.
     */
    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(NurseryBranch::class, 'employee_branches', 'employee_id', 'branch_id')
            ->withPivot('is_primary');
    }

    //======================
    /**
     * Relationship: Employee Allowances.
     */
    public function allowances(): HasMany
    {
        return $this->hasMany(EmployeeAllowance::class, 'employee_id');
    }

    //======================
    /**
     * Relationship: Payroll Items.
     */
    public function payrollItems(): HasMany
    {
        return $this->hasMany(PayrollItem::class, 'employee_id');
    }

    //======================
    /**
     * Relationship: Employee Documents.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(EmployeeDocument::class, 'employee_id');
    }

    //======================
    /**
     * Relationship: Attendances.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'employee_id');
    }

    //======================
    /**
     * Relationship: Leave Requests.
     */
    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class, 'employee_id');
    }

    //======================
    /**
     * Relationship: Permission Requests.
     */
    public function permissionRequests(): HasMany
    {
        return $this->hasMany(PermissionRequest::class, 'employee_id');
    }

    //======================
    /**
     * Relationship: Loans & Advances.
     */
    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class, 'employee_id');
    }

    //======================
    /**
     * Relationship: Rewards & Incentives.
     */
    public function rewards(): HasMany
    {
        return $this->hasMany(Reward::class, 'employee_id');
    }

    //======================
    /**
     * Relationship: Penalties & Violations.
     */
    public function penalties(): HasMany
    {
        return $this->hasMany(Penalty::class, 'employee_id');
    }

    //======================
    /**
     * Relationship: Creator user.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    //======================
    /**
     * Accessor: Avatar URL with intelligent gender-based fallback SVG.
     */
    public function getAvatarUrlAttribute(): string
    {
        if (!empty($this->avatar)) {
            if (str_starts_with($this->avatar, 'http://') || str_starts_with($this->avatar, 'https://')) {
                return $this->avatar;
            }
            if (Storage::disk('public')->exists($this->avatar)) {
                return Storage::disk('public')->url($this->avatar);
            }
            if (file_exists(public_path($this->avatar))) {
                return asset($this->avatar);
            }
            if (file_exists(public_path('storage/' . $this->avatar))) {
                return asset('storage/' . $this->avatar);
            }
        }

        if ($this->user && !empty($this->user->avatar)) {
            if (str_starts_with($this->user->avatar, 'http://') || str_starts_with($this->user->avatar, 'https://')) {
                return $this->user->avatar;
            }
            if (Storage::disk('public')->exists($this->user->avatar)) {
                return Storage::disk('public')->url($this->user->avatar);
            }
            if (file_exists(public_path($this->user->avatar))) {
                return asset($this->user->avatar);
            }
            if (file_exists(public_path('storage/' . $this->user->avatar))) {
                return asset('storage/' . $this->user->avatar);
            }
        }

        $genderVal = strtolower(trim((string) ($this->gender instanceof \BackedEnum ? $this->gender->value : ($this->gender ?? 'male'))));

        if (in_array($genderVal, ['female', 'f', 'أنثى', 'انثى'])) {
            if (file_exists(public_path('assets/portal/female-avatar.png'))) {
                return asset('assets/portal/female-avatar.png');
            }
            if (file_exists(public_path('assets/portal/teacher-avatar.png'))) {
                return asset('assets/portal/teacher-avatar.png');
            }
            return asset('assets/img/avatars/female-avatar.png');
        }

        if (file_exists(public_path('assets/portal/male-avatar.png'))) {
            return asset('assets/portal/male-avatar.png');
        }
        return asset('assets/img/avatars/male-avatar.png');
    }

    //======================
    /**
     * Accessor: Calculate profile completion percentage and details.
     *
     * @return array{percentage: int, color: string, class: string, label: string}
     */
    public function getProfileCompletionAttribute(): array
    {
        $points = 0;

        // 1. Basic Information (25 points)
        if (!empty($this->name)) $points += 5;
        if (!empty($this->civil_id) || !empty($this->passport_number)) $points += 5;
        if (!empty($this->date_of_birth)) $points += 5;
        if (!empty($this->gender)) $points += 5;
        if (!empty($this->nationality)) $points += 5;

        // 2. Contact & Address (20 points)
        if (!empty($this->phone)) $points += 10;
        if (!empty($this->email)) $points += 5;
        if (!empty($this->address) || !empty($this->emergency_contact_phone) || !empty($this->governorate_id)) $points += 5;

        // 3. Organization & Job (25 points)
        if (!empty($this->primary_branch_id)) $points += 10;
        if (!empty($this->department_id)) $points += 5;
        if (!empty($this->job_title_id)) $points += 5;
        if (!empty($this->hire_date)) $points += 5;

        // 4. Working & Payroll (20 points)
        if ($this->basic_salary > 0) $points += 10;
        if (!empty($this->work_schedule_id) || !empty($this->shift_id)) $points += 5;
        if (!empty($this->bank_id) || !empty($this->iban) || !empty($this->account_number)) $points += 5;

        // 5. Documents & Custom Avatar (10 points)
        if (!empty($this->avatar) && Storage::disk('public')->exists($this->avatar)) $points += 5;

        $hasDocs = isset($this->documents_count)
            ? ($this->documents_count > 0)
            : ($this->relationLoaded('documents') ? $this->documents->count() > 0 : $this->documents()->exists());

        if ($hasDocs) $points += 5;

        $percentage = min(100, max(0, $points));

        if ($percentage >= 80) {
            $color = '#71dd37'; // Sneat Success Green
            $class = 'bg-success';
            $label = __('Complete');
        } elseif ($percentage >= 50) {
            $color = '#ffab00'; // Sneat Warning Amber
            $class = 'bg-warning';
            $label = __('Moderate');
        } else {
            $color = '#ff3e1d'; // Sneat Danger Red
            $class = 'bg-danger';
            $label = __('Incomplete');
        }

        return [
            'percentage' => $percentage,
            'color'      => $color,
            'class'      => $class,
            'label'      => $label,
        ];
    }

    //======================
    /**
     * Scope: Filter active employees.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', EmployeeStatus::ACTIVE);
    }

    //======================
    /**
     * Scope: Filter by primary branch or assigned branches.
     */
    public function scopeByBranch(Builder $query, int $branchId): Builder
    {
        return $query->where(function (Builder $q) use ($branchId) {
            $q->where('primary_branch_id', $branchId)
              ->orWhereHas('branches', function (Builder $bq) use ($branchId) {
                  $bq->where('nursery_branches.id', $branchId);
              });
        });
    }

    //======================
    /**
     * Scope: Filter by department.
     */
    public function scopeByDepartment(Builder $query, int $departmentId): Builder
    {
        return $query->where('department_id', $departmentId);
    }

    //======================
    /**
     * Scope: Filter teachers (academic staff).
     */
    public function scopeTeachers(Builder $query): Builder
    {
        return $query->where('is_teacher', true);
    }

    //======================
    /**
     * Check if employee is an academic teacher.
     */
    public function isTeacher(): bool
    {
        return (bool) $this->is_teacher;
    }
}
