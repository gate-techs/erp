<?php

namespace Modules\Academic\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Nursery\Models\Nursery;
use Modules\Nursery\Models\NurseryBranch;
use Modules\Settings\Models\GeneralSetting;

class IncidentReport extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'incident_reports';

    protected $fillable = [
        'student_id',
        'incident_type_id',
        'user_id',
        'date',
        'time',
        'location',
        'student_description',
        'procedures',
        'persons',
        'nurse_report',
        'institution_id',
        'branch_id',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'student_id'       => 'integer',
        'incident_type_id' => 'integer',
        'user_id'          => 'integer',
        'institution_id'   => 'integer',
        'branch_id'        => 'integer',
        'date'             => 'date:Y-m-d',
        'persons'          => 'array',
        'created_by'       => 'integer',
        'updated_by'       => 'integer',
        'deleted_by'       => 'integer',
    ];

    //------------------------------------------
    // Relationships
    //------------------------------------------

    /**
     * الطالب المعني بالحادث
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(AcademicStudent::class, 'student_id');
    }

    /**
     * نوع الحادث / الإصابة من جدول الإعدادات العامة
     */
    public function incidentType(): BelongsTo
    {
        return $this->belongsTo(GeneralSetting::class, 'incident_type_id');
    }

    /**
     * المسؤول / الموظف أو الممرضة المسجلة للحادث
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * الحضانة / المؤسسة
     */
    public function institution(): BelongsTo
    {
        return $this->belongsTo(Nursery::class, 'institution_id');
    }

    /**
     * الفرع
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(NurseryBranch::class, 'branch_id');
    }

    /**
     * المستخدم الذي أنشأ السجل
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    //------------------------------------------
    // Accessors & Helper Methods
    //------------------------------------------

    /**
     * جلب كائنات المستخدمين للشهود الحاضرين
     *
     * @return Collection<int, User>
     */
    public function getWitnessUsersAttribute(): Collection
    {
        $ids = is_array($this->persons) ? array_filter($this->persons) : [];
        if (empty($ids)) {
            return new Collection();
        }

        return User::whereIn('id', $ids)->get();
    }

    /**
     * أسماء الشهود مفصولة بفواصل
     */
    public function getPersonsNamesAttribute(): string
    {
        $witnesses = $this->witness_users;
        if ($witnesses->isEmpty()) {
            return '-';
        }

        return $witnesses->pluck('name')->implode(', ');
    }

    /**
     * اسم نوع الحادث المترجم
     */
    public function getIncidentTypeNameAttribute(): string
    {
        if (! $this->incidentType) {
            return '-';
        }

        return $this->incidentType->translate(app()->getLocale())?->name
            ?? $this->incidentType->translate('ar')?->name
            ?? $this->incidentType->name
            ?? '-';
    }
}
