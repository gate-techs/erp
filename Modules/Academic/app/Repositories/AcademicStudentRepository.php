<?php

namespace Modules\Academic\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Academic\Models\AcademicStudent;
use Modules\Academic\Repositories\Contracts\AcademicStudentRepositoryInterface;

class AcademicStudentRepository implements AcademicStudentRepositoryInterface
{
    //------------------------------------------
    public function getFilteredQuery(array $filters = []): Builder
    {
        $query = AcademicStudent::query()
            ->with([
                'translations',
                'institution.translations',
                'branch.translations',
                'academicYear.translations',
                'stage.translations',
                'class.translations',
                'guardian.translations',
                'creator',
                'father',
                'mother',
            ]);

        if (! empty($filters['institution_id'])) {
            $query->where('institution_id', (int) $filters['institution_id']);
        }

        if (! empty($filters['branch_id'])) {
            $query->where('branch_id', (int) $filters['branch_id']);
        }

        if (! empty($filters['academic_year_id'])) {
            $query->where('academic_year_id', (int) $filters['academic_year_id']);
        }

        if (! empty($filters['academic_stage_id'])) {
            $query->where('academic_stage_id', (int) $filters['academic_stage_id']);
        }

        if (! empty($filters['academic_class_id'])) {
            $query->where('academic_class_id', (int) $filters['academic_class_id']);
        }

        if (! empty($filters['gender'])) {
            $query->where('gender', (int) $filters['gender']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['guardian_id'])) {
            $query->where('academic_guardian_id', (int) $filters['guardian_id']);
        }

        if (! empty($filters['search'])) {
            $term = is_array($filters['search'])
                ? ($filters['search']['value'] ?? null)
                : $filters['search'];
            $term = is_string($term) ? trim($term) : '';

            if ($term === '') {
                return $query->latest('id');
            }

            $query->where(function (Builder $q) use ($term) {
                $q->whereTranslationLike('name', "%{$term}%")
                    ->orWhere('student_code', 'like', "%{$term}%")
                    ->orWhere('civil_id', 'like', "%{$term}%")
                    ->orWhereHas('father', function (Builder $fq) use ($term) {
                        $fq->where('name', 'like', "%{$term}%")
                            ->orWhere('phone', 'like', "%{$term}%");
                    })
                    ->orWhereHas('mother', function (Builder $mq) use ($term) {
                        $mq->where('name', 'like', "%{$term}%")
                            ->orWhere('phone', 'like', "%{$term}%");
                    });
            });
        }

        return $query->latest('id');
    }

    //------------------------------------------
    public function all(): Collection
    {
        return AcademicStudent::with([
            'translations',
            'institution.translations',
            'branch.translations',
            'stage.translations',
            'class.translations',
        ])->get();
    }

    //------------------------------------------
    public function find(int $id): ?AcademicStudent
    {
        return AcademicStudent::with([
            'translations',
            'institution',
            'branch',
            'academicYear',
            'stage',
            'class',
            'nationality',
            'guardian',
            'father',
            'mother',
        ])->find($id);
    }

    //------------------------------------------
    public function findWithAllRelations(int $id): ?AcademicStudent
    {
        return AcademicStudent::with([
            'translations',
            'institution.translations',
            'branch.translations',
            'academicYear.translations',
            'stage.translations',
            'class.translations',
            'nationality',
            'guardian.translations',
            'parents',
            'father',
            'mother',
            'siblings.academicClass',
            'siblings.siblingStudent.translations',
            'diseases',
            'medicalResponses',
            'emergencyContacts',
            'authorizedPickups',
            'informationResponses',
        ])->find($id);
    }

    //------------------------------------------
    public function findByCode(string $code): ?AcademicStudent
    {
        return AcademicStudent::where('student_code', $code)->first();
    }

    //------------------------------------------
    public function findByCivilId(string $civilId): ?AcademicStudent
    {
        return AcademicStudent::where('civil_id', $civilId)->first();
    }

    //------------------------------------------
    public function create(array $data): AcademicStudent
    {
        return AcademicStudent::create($data);
    }

    //------------------------------------------
    public function update(int $id, array $data): ?AcademicStudent
    {
        $student = $this->find($id);
        if (! $student) {
            return null;
        }

        $student->update($data);

        return $student->fresh();
    }

    //------------------------------------------
    public function delete(int $id): bool
    {
        $student = $this->find($id);
        if (! $student) {
            return false;
        }

        return (bool) $student->delete();
    }

    //------------------------------------------
    public function toggleStatus(int $id): ?AcademicStudent
    {
        $student = $this->find($id);
        if (! $student) {
            return null;
        }

        $student->status = $student->status === AcademicStudent::STATUS_ACTIVE
            ? AcademicStudent::STATUS_INACTIVE
            : AcademicStudent::STATUS_ACTIVE;

        $student->save();

        return $student;
    }

    //------------------------------------------
    public function generateStudentCode(): string
    {
        $prefix = 'STD-' . date('Y') . '-';
        $lastStudent = AcademicStudent::withTrashed()
            ->where('student_code', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = 1;
        if ($lastStudent && preg_match('/' . preg_quote($prefix, '/') . '(\d+)/', $lastStudent->student_code, $matches)) {
            $nextNumber = (int) $matches[1] + 1;
        }

        return $prefix . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
