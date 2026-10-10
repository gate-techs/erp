<?php

namespace Modules\Academic\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Academic\Models\AcademicStudent;
use Modules\Academic\Models\IncidentReport;
use Modules\Academic\Repositories\Contracts\IncidentReportRepositoryInterface;
use Modules\Nursery\Models\Nursery;
use Modules\Nursery\Models\NurseryBranch;
use Modules\Settings\Enums\GeneralSettingType;
use Modules\Settings\Models\GeneralSetting;

class IncidentReportRepository implements IncidentReportRepositoryInterface
{
    /**
     * Get query builder with filters applied.
     */
    public function query(array $filters = []): Builder
    {
        $query = IncidentReport::query()
            ->with([
                'student.translations',
                'student.class.translations',
                'student.stage.translations',
                'student.branch.translations',
                'incidentType.translations',
                'user',
                'institution.translations',
                'branch.translations',
            ]);

        // تصفية حسب الطالب
        if (! empty($filters['student_id'])) {
            $query->where('student_id', $filters['student_id']);
        }

        // تصفية حسب نوع الحادث
        if (! empty($filters['incident_type_id'])) {
            $query->where('incident_type_id', $filters['incident_type_id']);
        }

        // تصفية حسب المسؤول / المستخدم المسجل
        if (! empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        // تصفية حسب المؤسسة
        if (! empty($filters['institution_id'])) {
            $query->where('institution_id', $filters['institution_id']);
        }

        // تصفية حسب الفرع
        if (! empty($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }

        // تصفية حسب التاريخ الدقيق
        if (! empty($filters['date'])) {
            $query->whereDate('date', $filters['date']);
        }

        // تصفية حسب نطاق التاريخ
        if (! empty($filters['date_from'])) {
            $query->whereDate('date', '>=', $filters['date_from']);
        }
        if (! empty($filters['date_to'])) {
            $query->whereDate('date', '<=', $filters['date_to']);
        }

        // تصفية بالبحث النصي
        $searchTerm = $filters['search'] ?? null;
        if (is_array($searchTerm)) {
            $searchTerm = $searchTerm['value'] ?? null;
        }

        if (! empty($searchTerm) && is_string($searchTerm)) {
            $query->where(function (Builder $q) use ($searchTerm) {
                $q->whereHas('student', function (Builder $sq) use ($searchTerm) {
                    $sq->whereHas('translations', function (Builder $tq) use ($searchTerm) {
                        $tq->where('name', 'like', "%{$searchTerm}%");
                    })->orWhere('student_code', 'like', "%{$searchTerm}%");
                })
                ->orWhere('location', 'like', "%{$searchTerm}%")
                ->orWhere('student_description', 'like', "%{$searchTerm}%")
                ->orWhere('procedures', 'like', "%{$searchTerm}%")
                ->orWhere('nurse_report', 'like', "%{$searchTerm}%");
            });
        }

        return $query->latest('date')->latest('id');
    }

    /**
     * Find single record by ID.
     */
    public function find(int $id): ?IncidentReport
    {
        return IncidentReport::with([
            'student.translations',
            'student.class.translations',
            'student.stage.translations',
            'student.guardian.translations',
            'student.branch.translations',
            'student.institution.translations',
            'incidentType.translations',
            'user',
            'creator',
            'institution.translations',
            'branch.translations',
        ])->find($id);
    }

    /**
     * Create a new incident report.
     */
    public function create(array $data): IncidentReport
    {
        return DB::transaction(function () use ($data) {
            // ملء بيانات الحضانة والفرع تلقائياً من الطالب إذا لم تُرسل
            if (! empty($data['student_id']) && (empty($data['institution_id']) || empty($data['branch_id']))) {
                $student = AcademicStudent::find($data['student_id']);
                if ($student) {
                    $data['institution_id'] = $data['institution_id'] ?? $student->institution_id;
                    $data['branch_id']      = $data['branch_id'] ?? $student->branch_id;
                }
            }

            // التأكد من حفظ persons كمصفوفة أرقام
            if (isset($data['persons']) && is_array($data['persons'])) {
                $data['persons'] = array_values(array_filter(array_map('intval', $data['persons'])));
            }

            return IncidentReport::create($data);
        });
    }

    /**
     * Update an existing report.
     */
    public function update(int $id, array $data): ?IncidentReport
    {
        return DB::transaction(function () use ($id, $data) {
            $report = IncidentReport::find($id);
            if (! $report) {
                return null;
            }

            if (isset($data['persons']) && is_array($data['persons'])) {
                $data['persons'] = array_values(array_filter(array_map('intval', $data['persons'])));
            }

            $report->update($data);

            return $this->find($report->id);
        });
    }

    /**
     * Delete a report.
     */
    public function delete(int $id): bool
    {
        $report = IncidentReport::find($id);

        return $report ? (bool) $report->delete() : false;
    }

    /**
     * Get students list.
     */
    public function getStudents(?int $branchId = null, ?int $institutionId = null): Collection
    {
        $query = AcademicStudent::with(['translations', 'class.translations', 'branch.translations'])
            ->whereIn('status', [AcademicStudent::STATUS_ACTIVE, AcademicStudent::STATUS_PENDING]);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if ($institutionId) {
            $query->where('institution_id', $institutionId);
        }

        return $query->orderBy('id')->get();
    }

    /**
     * Get incident types from general_settings.
     */
    public function getIncidentTypes(): Collection
    {
        return GeneralSetting::ofType(GeneralSettingType::INCIDENT_TYPE)
            ->with('translations')
            ->orderBy('id')
            ->get();
    }

    /**
     * Get staff users.
     */
    public function getStaffUsers(): Collection
    {
        return User::where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    /**
     * Get institutions.
     */
    public function getInstitutions(): Collection
    {
        return Nursery::with('translations')->orderBy('id')->get();
    }

    /**
     * Get branches.
     */
    public function getBranches(?int $institutionId = null): Collection
    {
        return NurseryBranch::with('translations')
            ->when($institutionId, fn ($q) => $q->where('nursery_id', $institutionId))
            ->orderBy('id')
            ->get();
    }
}
