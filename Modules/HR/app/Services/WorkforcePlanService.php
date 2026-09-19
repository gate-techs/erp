<?php

namespace Modules\HR\Services;

use Illuminate\Support\Facades\DB;
use Modules\HR\Enums\WorkforcePlanStatus;
use Modules\HR\Models\WorkforcePlan;
use Modules\HR\Models\WorkforcePlanItem;
use Modules\HR\Repositories\Contracts\WorkforcePlanRepositoryInterface;

class WorkforcePlanService
{
    public function __construct(
        protected WorkforcePlanRepositoryInterface $planRepository
    ) {}

    /**
     * Create a new workforce plan with its items.
     */
    public function createPlan(array $data, array $items, int $userId): WorkforcePlan
    {
        return DB::transaction(function () use ($data, $items, $userId) {
            $year = (int) ($data['year'] ?? now()->year);
            $data['plan_code']  = $this->planRepository->generatePlanCode($year);
            $data['created_by'] = $userId;
            $data['status']     = $data['status'] ?? WorkforcePlanStatus::DRAFT->value;

            $plan = $this->planRepository->create($data);

            foreach ($items as $itemData) {
                $this->createPlanItem($plan, $itemData);
            }

            $plan->recalculateTotals();

            return $plan->fresh(['items.department.translations', 'items.jobTitle.translations', 'items.branch']);
        });
    }

    /**
     * Update an existing workforce plan and its items.
     */
    public function updatePlan(int|WorkforcePlan $plan, array $data, ?array $items = null): WorkforcePlan
    {
        return DB::transaction(function () use ($plan, $data, $items) {
            $instance = $plan instanceof WorkforcePlan ? $plan : $this->planRepository->findById($plan);

            $this->planRepository->update($instance, $data);

            if ($items !== null) {
                // Delete existing items and recreate
                $instance->items()->delete();

                foreach ($items as $itemData) {
                    $this->createPlanItem($instance, $itemData);
                }
            }

            $instance->recalculateTotals();

            return $instance->fresh(['items.department.translations', 'items.jobTitle.translations', 'items.branch']);
        });
    }

    /**
     * Create a single plan item with live headcount calculation.
     */
    protected function createPlanItem(WorkforcePlan $plan, array $itemData): WorkforcePlanItem
    {
        $deptId   = (int) $itemData['department_id'];
        $jobId    = (int) $itemData['job_title_id'];
        $branchId = !empty($itemData['branch_id']) ? (int) $itemData['branch_id'] : null;

        $currentHeadcount = $this->planRepository->countCurrentEmployees($deptId, $jobId, $branchId);
        $targetHeadcount  = max(1, (int) ($itemData['target_headcount'] ?? 1));
        $monthlySalary    = (float) ($itemData['estimated_monthly_salary'] ?? 0);
        $vacancies        = max(0, $targetHeadcount - $currentHeadcount);
        $totalCost        = (float) ($targetHeadcount * $monthlySalary * 12);

        return $plan->items()->create([
            'department_id'            => $deptId,
            'branch_id'                => $branchId,
            'job_title_id'             => $jobId,
            'employment_type'          => $itemData['employment_type'] ?? 'full_time',
            'current_headcount'        => $currentHeadcount,
            'target_headcount'         => $targetHeadcount,
            'vacancies_count'          => $vacancies,
            'fulfilled_count'          => 0,
            'estimated_monthly_salary' => $monthlySalary,
            'estimated_total_cost'     => $totalCost,
            'priority'                 => $itemData['priority'] ?? 'medium',
            'target_quarter'           => $itemData['target_quarter'] ?? 'annual',
            'justification'            => $itemData['justification'] ?? null,
        ]);
    }

    /**
     * Submit plan for approval.
     */
    public function submitPlan(int|WorkforcePlan $plan): WorkforcePlan
    {
        $instance = $plan instanceof WorkforcePlan ? $plan : $this->planRepository->findById($plan);
        $instance->update(['status' => WorkforcePlanStatus::SUBMITTED->value]);
        return $instance;
    }

    /**
     * Approve workforce plan.
     */
    public function approvePlan(int|WorkforcePlan $plan, int $approverId): WorkforcePlan
    {
        $instance = $plan instanceof WorkforcePlan ? $plan : $this->planRepository->findById($plan);
        $instance->update([
            'status'      => WorkforcePlanStatus::APPROVED->value,
            'approved_by' => $approverId,
            'approved_at' => now(),
        ]);
        return $instance;
    }

    /**
     * Activate an approved plan.
     */
    public function activatePlan(int|WorkforcePlan $plan): WorkforcePlan
    {
        $instance = $plan instanceof WorkforcePlan ? $plan : $this->planRepository->findById($plan);
        $instance->update(['status' => WorkforcePlanStatus::ACTIVE->value]);
        return $instance;
    }

    /**
     * Close workforce plan.
     */
    public function closePlan(int|WorkforcePlan $plan): WorkforcePlan
    {
        $instance = $plan instanceof WorkforcePlan ? $plan : $this->planRepository->findById($plan);
        $instance->update(['status' => WorkforcePlanStatus::CLOSED->value]);
        return $instance;
    }

    /**
     * Cancel workforce plan.
     */
    public function cancelPlan(int|WorkforcePlan $plan): WorkforcePlan
    {
        $instance = $plan instanceof WorkforcePlan ? $plan : $this->planRepository->findById($plan);
        $instance->update(['status' => WorkforcePlanStatus::CANCELLED->value]);
        return $instance;
    }

    /**
     * Delete workforce plan.
     */
    public function deletePlan(int|WorkforcePlan $plan): bool
    {
        return $this->planRepository->delete($plan);
    }

    /**
     * Recalculate live employee headcount for all items in a plan.
     */
    public function recalculateLiveHeadcount(WorkforcePlan $plan): void
    {
        foreach ($plan->items as $item) {
            $current = $this->planRepository->countCurrentEmployees(
                $item->department_id,
                $item->job_title_id,
                $item->branch_id
            );
            $item->current_headcount = $current;
            $item->vacancies_count   = max(0, $item->target_headcount - $current);
            $item->saveQuietly();
        }
    }

    /**
     * Get live count for given parameters.
     */
    public function countCurrentEmployees(int $departmentId, int $jobTitleId, ?int $branchId = null): int
    {
        return $this->planRepository->countCurrentEmployees($departmentId, $jobTitleId, $branchId);
    }

    /**
     * Get KPI summary metrics.
     */
    public function getKPIs(): array
    {
        return $this->planRepository->getKPIs();
    }
}
