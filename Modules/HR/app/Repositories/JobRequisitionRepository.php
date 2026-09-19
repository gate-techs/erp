<?php

namespace Modules\HR\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Modules\HR\Enums\JobRequisitionStatus;
use Modules\HR\Models\JobRequisition;
use Modules\HR\Repositories\Contracts\JobRequisitionRepositoryInterface;

class JobRequisitionRepository implements JobRequisitionRepositoryInterface
{
    /**
     * Get query builder instance for server-side DataTables.
     */
    public function getDataTableQuery(array $filters = []): Builder
    {
        $query = JobRequisition::query()
            ->with([
                'department.translations',
                'jobTitle.translations',
                'branch',
                'requester',
                'planItem',
            ]);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        if (!empty($filters['requisition_type'])) {
            $query->where('requisition_type', $filters['requisition_type']);
        }

        if (!empty($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }

        if (!empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (isset($filters['is_budgeted']) && $filters['is_budgeted'] !== '') {
            $query->where('is_budgeted', (bool) $filters['is_budgeted']);
        }

        $search = is_array($filters['search'] ?? null)
            ? ($filters['search']['value'] ?? null)
            : ($filters['search'] ?? null);

        if (!empty($search)) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('requisition_number', 'like', "%{$search}%")
                  ->orWhereHas('jobTitle.translations', function ($t) use ($search) {
                      $t->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('department.translations', function ($t) use ($search) {
                      $t->where('name', 'like', "%{$search}%");
                  });
            });
        }

        return $query->latest('id');
    }

    /**
     * Find a job requisition by ID with relations.
     */
    public function findById(int $id, array $with = ['department.translations', 'jobTitle.translations', 'branch', 'requester', 'replacementEmployee.translations', 'approver', 'planItem.plan']): ?JobRequisition
    {
        return JobRequisition::with($with)->find($id);
    }

    /**
     * Create a new job requisition.
     */
    public function create(array $data): JobRequisition
    {
        return JobRequisition::create($data);
    }

    /**
     * Update an existing job requisition.
     */
    public function update(int|JobRequisition $requisition, array $data): JobRequisition
    {
        $instance = $requisition instanceof JobRequisition ? $requisition : $this->findById($requisition);
        $instance->update($data);
        return $instance;
    }

    /**
     * Delete a job requisition (soft-delete).
     */
    public function delete(int|JobRequisition $requisition): bool
    {
        $instance = $requisition instanceof JobRequisition ? $requisition : $this->findById($requisition);
        return (bool) $instance->delete();
    }

    /**
     * Generate unique requisition number for current year.
     */
    public function generateRequisitionNumber(): string
    {
        $year = now()->year;
        $count = JobRequisition::withTrashed()
            ->whereYear('created_at', $year)
            ->count() + 1;
        return sprintf('REQ-%d-%04d', $year, $count);
    }

    /**
     * Get dashboard / index stat KPI metrics.
     */
    public function getKPIs(): array
    {
        $totalRequisitions = JobRequisition::count();
        $pendingApprovals  = JobRequisition::where('status', JobRequisitionStatus::PENDING_APPROVAL->value)->count();
        $inRecruitment     = JobRequisition::where('status', JobRequisitionStatus::IN_RECRUITMENT->value)->count();
        $fulfilled         = JobRequisition::where('status', JobRequisitionStatus::FULFILLED->value)->count();

        return [
            'total_requisitions' => $totalRequisitions,
            'pending_approvals'  => $pendingApprovals,
            'in_recruitment'     => $inRecruitment,
            'fulfilled'          => $fulfilled,
        ];
    }
}
