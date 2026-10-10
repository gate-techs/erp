<?php

namespace Modules\Academic\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Academic\Models\AcademicClass;
use Modules\Academic\Models\AcademicStage;
use Modules\Academic\Models\AcademicSubject;
use Modules\Academic\Models\AcademicTeacherClass;
use Modules\Academic\Models\AcademicTeacherSubject;
use Modules\Academic\Repositories\Contracts\AcademicTeacherRepositoryInterface;
use Modules\HR\Models\Employee;
use Modules\HR\Models\JobTitle;
use Modules\Nursery\Models\Nursery;
use Modules\Nursery\Models\NurseryBranch;

class AcademicTeacherRepository implements AcademicTeacherRepositoryInterface
{
    //------------------------------------------
    public function getFilteredQuery(array $filters = []): Builder
    {
        $query = Employee::query()
            ->where('is_teacher', true)
            ->with(['branch.translations', 'jobTitle.translations', 'manager.translations', 'user.roles', 'translations']);

        if (! empty($filters['branch_id'])) {
            $query->where('primary_branch_id', (int) $filters['branch_id']);
        }

        if (! empty($filters['job_title_id'])) {
            $query->where('job_title_id', (int) $filters['job_title_id']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['search'])) {
            $term = is_array($filters['search'])
                ? ($filters['search']['value'] ?? null)
                : $filters['search'];
            $term = is_string($term) ? trim($term) : '';

            if ($term !== '') {
                $query->where(function (Builder $q) use ($term) {
                    $q->whereTranslationLike('name', "%{$term}%")
                        ->orWhere('employee_code', 'like', "%{$term}%")
                        ->orWhere('phone', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%");
                });
            }
        }

        return $query->latest('id');
    }

    //------------------------------------------
    public function find(int $id): ?Employee
    {
        return Employee::where('is_teacher', true)
            ->with(['branch.translations', 'jobTitle.translations', 'manager.translations', 'user.roles', 'translations'])
            ->find($id);
    }

    //------------------------------------------
    public function createTeacher(array $data): Employee
    {
        return DB::transaction(function () use ($data) {
            $teacher = new Employee();
            $teacher->is_teacher         = true;
            $teacher->employee_code      = $data['employee_code'] ?? ('TCH-' . rand(1000, 9999));
            $teacher->primary_branch_id  = $data['primary_branch_id'] ?? null;
            $teacher->job_title_id       = $data['job_title_id'] ?? null;
            $teacher->manager_id         = $data['manager_id'] ?? null;
            $teacher->phone              = $data['phone'] ?? null;
            $teacher->email              = $data['email'] ?? null;
            $teacher->status             = $data['status'] ?? 'active';

            $locale = app()->getLocale();
            $teacher->translateOrNew($locale)->name = $data['name'] ?? '';
            if ($locale !== 'ar' && ! empty($data['name_ar'])) {
                $teacher->translateOrNew('ar')->name = $data['name_ar'];
            }

            $teacher->save();

            return $teacher;
        });
    }

    //------------------------------------------
    public function updateTeacher(int $id, array $data): bool
    {
        $teacher = $this->find($id);
        if (! $teacher) {
            return false;
        }

        return DB::transaction(function () use ($teacher, $data) {
            if (isset($data['primary_branch_id'])) {
                $teacher->primary_branch_id = $data['primary_branch_id'];
            }
            if (isset($data['job_title_id'])) {
                $teacher->job_title_id = $data['job_title_id'];
            }
            if (isset($data['manager_id'])) {
                $teacher->manager_id = $data['manager_id'];
            }
            if (isset($data['phone'])) {
                $teacher->phone = $data['phone'];
            }
            if (isset($data['email'])) {
                $teacher->email = $data['email'];
            }
            if (isset($data['status'])) {
                $teacher->status = $data['status'];
            }

            if (! empty($data['name'])) {
                $teacher->translateOrNew(app()->getLocale())->name = $data['name'];
            }
            if (! empty($data['name_ar'])) {
                $teacher->translateOrNew('ar')->name = $data['name_ar'];
            }

            return $teacher->save();
        });
    }

    //------------------------------------------
    public function deleteTeacher(int $id): bool
    {
        $teacher = $this->find($id);
        if (! $teacher) {
            return false;
        }

        return DB::transaction(function () use ($teacher) {
            AcademicTeacherClass::where('employee_id', $teacher->id)->delete();
            AcademicTeacherSubject::where('employee_id', $teacher->id)->delete();
            return (bool) $teacher->delete();
        });
    }

    //------------------------------------------
    public function toggleStatus(int $id): ?Employee
    {
        $teacher = $this->find($id);
        if (! $teacher) {
            return null;
        }

        $teacher->status = ($teacher->status === 'active') ? 'inactive' : 'active';
        $teacher->save();

        return $teacher;
    }

    //------------------------------------------
    public function getAllocationData(int $id): array
    {
        $teacher = $this->find($id);
        if (! $teacher) {
            return [];
        }

        $branchId = $teacher->primary_branch_id;
        $branchName = $teacher->branch?->translate(app()->getLocale())?->name
            ?? $teacher->branch?->translate('ar')?->name
            ?? $teacher->branch?->name
            ?? '-';

        $jobTitleName = $teacher->jobTitle?->translate(app()->getLocale())?->name
            ?? $teacher->jobTitle?->translate('ar')?->name
            ?? $teacher->jobTitle?->name
            ?? '-';

        // Load all active stages with their classes and subjects
        $stages = AcademicStage::active()
            ->with(['translations'])
            ->get();

        // Get all classes in branch (or all classes if branch not specified)
        $branchClassesQuery = AcademicClass::active()->with(['translations', 'branch.translations']);
        if ($branchId) {
            $branchClassesQuery->where('branch_id', $branchId);
        }
        $branchClasses = $branchClassesQuery->get();

        // Total available branch classes count
        $totalBranchClassesCount = $branchClasses->count();

        // All active subjects
        $allSubjects = AcademicSubject::active()
            ->with(['translations', 'academicStages'])
            ->get();

        // Teacher's current assigned classes and subjects
        $assignedClasses = AcademicTeacherClass::where('employee_id', $teacher->id)->get();
        $assignedSubjects = AcademicTeacherSubject::where('employee_id', $teacher->id)->get();

        $selectedClassIdsByStage = [];
        foreach ($assignedClasses as $ac) {
            $selectedClassIdsByStage[$ac->academic_stage_id][] = $ac->academic_class_id;
        }

        $selectedSubjectIdsByStage = [];
        foreach ($assignedSubjects as $as) {
            $selectedSubjectIdsByStage[$as->academic_stage_id][] = $as->academic_subject_id;
        }

        $formattedStages = [];
        foreach ($stages as $stage) {
            $stageId = $stage->id;
            $stageName = $stage->translate(app()->getLocale())?->name
                ?? $stage->translate('ar')?->name
                ?? $stage->name
                ?? '-';

            // Classes for this stage
            $stageClasses = $branchClasses->where('academic_stage_id', $stageId)->values();
            $formattedClasses = $stageClasses->map(function ($c) {
                return [
                    'id'          => $c->id,
                    'name'        => $c->translate(app()->getLocale())?->name ?? $c->translate('ar')?->name ?? $c->name,
                    'branch_id'   => $c->branch_id,
                    'branch_name' => $c->branch?->translate(app()->getLocale())?->name ?? $c->branch?->translate('ar')?->name ?? $c->branch?->name ?? '',
                ];
            });

            // Subjects for this stage
            $stageSubjects = $allSubjects->filter(function ($sub) use ($stageId) {
                if ($sub->academicStages->isEmpty()) {
                    return true;
                }
                return $sub->academicStages->contains('id', $stageId);
            })->values();

            $formattedSubjects = $stageSubjects->map(function ($s) {
                return [
                    'id'   => $s->id,
                    'name' => $s->translate(app()->getLocale())?->name ?? $s->translate('ar')?->name ?? $s->name,
                ];
            });

            $selectedClassesForStage = $selectedClassIdsByStage[$stageId] ?? [];
            $selectedSubjectsForStage = $selectedSubjectIdsByStage[$stageId] ?? [];

            $formattedStages[] = [
                'id'                   => $stageId,
                'name'                 => $stageName,
                'classes'              => $formattedClasses,
                'total_classes'        => $formattedClasses->count(),
                'subjects'             => $formattedSubjects,
                'total_subjects'       => $formattedSubjects->count(),
                'selected_class_ids'   => array_values(array_unique($selectedClassesForStage)),
                'selected_subject_ids' => array_values(array_unique($selectedSubjectsForStage)),
            ];
        }

        return [
            'teacher' => [
                'id'            => $teacher->id,
                'employee_code' => $teacher->employee_code,
                'name'          => $teacher->translate(app()->getLocale())?->name ?? $teacher->translate('ar')?->name ?? $teacher->name,
                'branch_id'     => $branchId,
                'branch_name'   => $branchName,
                'job_title'     => $jobTitleName,
            ],
            'branch_classes_count' => $totalBranchClassesCount,
            'stages'               => $formattedStages,
        ];
    }

    //------------------------------------------
    public function saveAllocation(int $id, array $classes, array $subjects): bool
    {
        $teacher = $this->find($id);
        if (! $teacher) {
            return false;
        }

        return DB::transaction(function () use ($teacher, $classes, $subjects) {
            // Delete existing
            AcademicTeacherClass::where('employee_id', $teacher->id)->delete();
            AcademicTeacherSubject::where('employee_id', $teacher->id)->delete();

            // Insert classes
            foreach ($classes as $item) {
                if (! empty($item['stage_id']) && ! empty($item['class_id'])) {
                    AcademicTeacherClass::updateOrCreate([
                        'employee_id'       => $teacher->id,
                        'academic_class_id' => (int) $item['class_id'],
                    ], [
                        'academic_stage_id' => (int) $item['stage_id'],
                    ]);
                }
            }

            // Insert subjects
            foreach ($subjects as $item) {
                if (! empty($item['stage_id']) && ! empty($item['subject_id'])) {
                    AcademicTeacherSubject::updateOrCreate([
                        'employee_id'         => $teacher->id,
                        'academic_stage_id'   => (int) $item['stage_id'],
                        'academic_subject_id' => (int) $item['subject_id'],
                    ]);
                }
            }

            return true;
        });
    }

    //------------------------------------------
    public function getInstitutions(): Collection
    {
        return Nursery::active()->with('translations')->get();
    }

    //------------------------------------------
    public function getBranches(): Collection
    {
        return NurseryBranch::active()->with('translations')->get();
    }

    //------------------------------------------
    public function getJobTitles(): Collection
    {
        return JobTitle::with('translations')->get();
    }

    //------------------------------------------
    public function getManagers(): Collection
    {
        return Employee::where('status', 'active')->with('translations')->get();
    }
}
