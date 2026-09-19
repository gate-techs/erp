<?php

namespace Modules\HR\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Modules\HR\Enums\EmployeeStatus;
use Modules\HR\Enums\WorkforcePlanStatus;
use Modules\HR\Models\Employee;
use Modules\HR\Models\WorkforcePlan;
use Modules\HR\Repositories\Contracts\WorkforcePlanRepositoryInterface;

class WorkforcePlanRepository implements WorkforcePlanRepositoryInterface
{
    /**
     * Get query builder instance for server-side DataTables.
     */
    public function getDataTableQuery(array $filters = []): Builder
    {
        $query = WorkforcePlan::query()
            ->with(['creator', 'approver'])
            ->withCount('items');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['year'])) {
            $query->where('year', $filters['year']);
        }

        $search = is_array($filters['search'] ?? null)
            ? ($filters['search']['value'] ?? null)
            : ($filters['search'] ?? null);

        if (!empty($search)) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('plan_code', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%");
            });
        }

        return $query->latest('id');
    }

    /**
     * Find a workforce plan by ID with relations.
     */
    public function findById(int $id, array $with = ['items.department.translations', 'items.jobTitle.translations', 'items.branch', 'creator', 'approver']): ?WorkforcePlan
    {
        return WorkforcePlan::with($with)->find($id);
    }

    /**
     * Create a new workforce plan.
     */
    public function create(array $data): WorkforcePlan
    {
        return WorkforcePlan::create($data);
    }

    /**
     * Update an existing workforce plan.
     */
    public function update(int|WorkforcePlan $plan, array $data): WorkforcePlan
    {
        $instance = $plan instanceof WorkforcePlan ? $plan : $this->findById($plan);
        $instance->update($data);
        return $instance;
    }

    /**
     * Delete a workforce plan (soft-delete).
     */
    public function delete(int|WorkforcePlan $plan): bool
    {
        $instance = $plan instanceof WorkforcePlan ? $plan : $this->findById($plan);
        return (bool) $instance->delete();
    }

    /**
     * Generate unique plan code for a given year.
     */
    public function generatePlanCode(int $year): string
    {
        $count = WorkforcePlan::withTrashed()->where('year', $year)->count() + 1;
        return sprintf('WFP-%d-%03d', $year, $count);
    }

    /**
     * Get dashboard / index stat KPI metrics.
     */
    public function getKPIs(): array
    {
        $currentYear = now()->year;

        $totalPlans        = WorkforcePlan::count();
        $activePlans       = WorkforcePlan::whereIn('status', [WorkforcePlanStatus::APPROVED->value, WorkforcePlanStatus::ACTIVE->value])->count();
        $totalPlannedHead  = (int) WorkforcePlan::where('year', $currentYear)->sum('total_target_headcount');
        $totalBudgetedCost = (float) WorkforcePlan::where('year', $currentYear)->sum('total_budgeted_cost');

        return [
            'total_plans'         => $totalPlans,
            'active_plans'        => $activePlans,
            'total_planned_head'  => $totalPlannedHead,
            'total_budgeted_cost' => $totalBudgetedCost,
        ];
    }

    /**
     * Count live employees for a given department, branch, and job title.
     */
    public function countCurrentEmployees(int $departmentId, int $jobTitleId, ?int $branchId = null): int
    {
        $query = Employee::where('department_id', $departmentId)
            ->where('job_title_id', $jobTitleId)
            ->whereIn('status', [
                EmployeeStatus::ACTIVE->value,
                EmployeeStatus::PROBATION->value,
                EmployeeStatus::ON_LEAVE->value,
            ]);

        if ($branchId) {
            $query->where(function ($q) use ($branchId) {
                $q->where('primary_branch_id', $branchId)
                  ->orWhereHas('branches', function ($b) use ($branchId) {
                      $b->where('branch_id', $branchId);
                  });
            });
        }

        return $query->count();
    }
}
