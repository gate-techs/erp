<?php

namespace Modules\Academic\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Academic\Repositories\Contracts\AcademicTeacherRepositoryInterface;
use Modules\HR\Models\Employee;

class AcademicTeacherService
{
    public function __construct(
        protected AcademicTeacherRepositoryInterface $repository
    ) {}

    //------------------------------------------
    public function getFilteredQuery(array $filters = []): Builder
    {
        return $this->repository->getFilteredQuery($filters);
    }

    //------------------------------------------
    public function find(int $id): ?Employee
    {
        return $this->repository->find($id);
    }

    //------------------------------------------
    public function createTeacher(array $data): Employee
    {
        return $this->repository->createTeacher($data);
    }

    //------------------------------------------
    public function updateTeacher(int $id, array $data): bool
    {
        return $this->repository->updateTeacher($id, $data);
    }

    //------------------------------------------
    public function deleteTeacher(int $id): bool
    {
        return $this->repository->deleteTeacher($id);
    }

    //------------------------------------------
    public function toggleStatus(int $id): ?Employee
    {
        return $this->repository->toggleStatus($id);
    }

    //------------------------------------------
    public function getAllocationData(int $id): array
    {
        return $this->repository->getAllocationData($id);
    }

    //------------------------------------------
    public function saveAllocation(int $id, array $classes, array $subjects): bool
    {
        return $this->repository->saveAllocation($id, $classes, $subjects);
    }

    //------------------------------------------
    public function getInstitutions(): Collection
    {
        return $this->repository->getInstitutions();
    }

    //------------------------------------------
    public function getBranches(): Collection
    {
        return $this->repository->getBranches();
    }

    //------------------------------------------
    public function getJobTitles(): Collection
    {
        return $this->repository->getJobTitles();
    }

    //------------------------------------------
    public function getManagers(): Collection
    {
        return $this->repository->getManagers();
    }

    //------------------------------------------
    /**
     * Render the HTML badges for stages, classes and subjects assigned to a teacher.
     */
    public function renderAllocationsBadgeHtml(Employee $teacher, ?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();

        // 1. Unique Stages
        $stages = $teacher->academicTeacherClasses
            ->map(fn ($tc) => $tc->stage?->translate($locale)?->name ?? $tc->stage?->translate('ar')?->name ?? $tc->stage?->name)
            ->filter()
            ->unique()
            ->values();

        // 2. Classes
        $classes = $teacher->academicTeacherClasses
            ->map(fn ($tc) => $tc->class?->translate($locale)?->name ?? $tc->class?->translate('ar')?->name ?? $tc->class?->name)
            ->filter()
            ->unique()
            ->values();

        // 3. Unique Subjects
        $subjects = $teacher->academicTeacherSubjects
            ->map(fn ($ts) => $ts->subject?->translate($locale)?->name ?? $ts->subject?->translate('ar')?->name ?? $ts->subject?->name)
            ->filter()
            ->unique()
            ->values();

        if ($stages->isEmpty() && $classes->isEmpty() && $subjects->isEmpty()) {
            return '<button type="button" class="btn btn-xs btn-label-secondary border-dashed allocate-teacher-btn" data-id="' . $teacher->id . '"><i class="icon-base bx bx-plus-circle me-1"></i>' . e(__('Not Assigned')) . '</button>';
        }

        $html = '<div class="d-flex flex-column gap-1" style="min-width: 200px; max-width: 280px; white-space: normal;">';

        // Stages Badges
        if ($stages->isNotEmpty()) {
            $html .= '<div class="d-flex flex-wrap align-items-center gap-1">';
            $html .= '<small class="text-muted fw-bold me-1" style="font-size: 11px;">' . e(__('Stages')) . ':</small>';
            foreach ($stages as $stg) {
                $html .= '<span class="badge bg-label-primary px-1 py-0" style="font-size: 11px;">' . e($stg) . '</span>';
            }
            $html .= '</div>';
        }

        // Classes Badges
        if ($classes->isNotEmpty()) {
            $html .= '<div class="d-flex flex-wrap align-items-center gap-1">';
            $html .= '<small class="text-muted fw-bold me-1" style="font-size: 11px;">' . e(__('Classes')) . ':</small>';
            foreach ($classes as $cls) {
                $html .= '<span class="badge bg-label-info px-1 py-0" style="font-size: 11px;">' . e($cls) . '</span>';
            }
            $html .= '</div>';
        }

        // Subjects Badges
        if ($subjects->isNotEmpty()) {
            $html .= '<div class="d-flex flex-wrap align-items-center gap-1">';
            $html .= '<small class="text-muted fw-bold me-1" style="font-size: 11px;">' . e(__('Subjects')) . ':</small>';
            foreach ($subjects as $sub) {
                $html .= '<span class="badge bg-label-warning px-1 py-0" style="font-size: 11px;">' . e($sub) . '</span>';
            }
            $html .= '</div>';
        }

        $html .= '</div>';

        return $html;
    }
}
