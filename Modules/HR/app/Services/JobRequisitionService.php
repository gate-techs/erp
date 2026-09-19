<?php

namespace Modules\HR\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\HR\Enums\JobRequisitionStatus;
use Modules\HR\Enums\WorkforcePlanStatus;
use Modules\HR\Models\JobRequisition;
use Modules\HR\Models\WorkforcePlanItem;
use Modules\HR\Repositories\Contracts\JobRequisitionRepositoryInterface;

class JobRequisitionService
{
    public function __construct(
        protected JobRequisitionRepositoryInterface $requisitionRepository
    ) {}

    /**
     * Create a new job requisition.
     */
    public function createRequisition(array $data, int $userId): JobRequisition
    {
        return DB::transaction(function () use ($data, $userId) {
            $data['requisition_number'] = $this->requisitionRepository->generateRequisitionNumber();
            $data['created_by']         = $userId;
            $data['requested_by']       = $data['requested_by'] ?? $userId;
            $data['status']             = $data['status'] ?? JobRequisitionStatus::DRAFT->value;

            // Check if linked to a workforce plan item
            if (!empty($data['workforce_plan_item_id'])) {
                $planItem = WorkforcePlanItem::find($data['workforce_plan_item_id']);
                if ($planItem) {
                    $data['is_budgeted']    = true;
                    $data['department_id']  = $planItem->department_id;
                    $data['job_title_id']   = $planItem->job_title_id;
                    $data['branch_id']      = $data['branch_id'] ?? $planItem->branch_id;
                    $data['employment_type']= $data['employment_type'] ?? $planItem->employment_type->value;
                }
            } else {
                $data['is_budgeted'] = false;
            }

            return $this->requisitionRepository->create($data);
        });
    }

    /**
     * Update an existing job requisition.
     */
    public function updateRequisition(int|JobRequisition $requisition, array $data): JobRequisition
    {
        return DB::transaction(function () use ($requisition, $data) {
            $instance = $requisition instanceof JobRequisition ? $requisition : $this->requisitionRepository->findById($requisition);

            if (!empty($data['workforce_plan_item_id'])) {
                $planItem = WorkforcePlanItem::find($data['workforce_plan_item_id']);
                if ($planItem) {
                    $data['is_budgeted']   = true;
                    $data['department_id'] = $planItem->department_id;
                    $data['job_title_id']  = $planItem->job_title_id;
                }
            }

            return $this->requisitionRepository->update($instance, $data);
        });
    }

    /**
     * Submit requisition for approval.
     */
    public function submitRequisition(int|JobRequisition $requisition): JobRequisition
    {
        $instance = $requisition instanceof JobRequisition ? $requisition : $this->requisitionRepository->findById($requisition);
        $instance->update(['status' => JobRequisitionStatus::PENDING_APPROVAL->value]);
        return $instance;
    }

    /**
     * Approve requisition.
     */
    public function approveRequisition(int|JobRequisition $requisition, int $approverId): JobRequisition
    {
        $instance = $requisition instanceof JobRequisition ? $requisition : $this->requisitionRepository->findById($requisition);
        $instance->update([
            'status'      => JobRequisitionStatus::APPROVED->value,
            'approved_by' => $approverId,
            'approved_at' => now(),
        ]);
        return $instance;
    }

    /**
     * Reject requisition with reason.
     */
    public function rejectRequisition(int|JobRequisition $requisition, string $reason, int $approverId): JobRequisition
    {
        $instance = $requisition instanceof JobRequisition ? $requisition : $this->requisitionRepository->findById($requisition);
        $instance->update([
            'status'           => JobRequisitionStatus::REJECTED->value,
            'rejection_reason' => $reason,
            'approved_by'      => $approverId,
            'approved_at'      => now(),
        ]);
        return $instance;
    }

    /**
     * Start recruitment / sourcing for an approved requisition.
     */
    public function startRecruitment(int|JobRequisition $requisition): JobRequisition
    {
        $instance = $requisition instanceof JobRequisition ? $requisition : $this->requisitionRepository->findById($requisition);
        $instance->update(['status' => JobRequisitionStatus::IN_RECRUITMENT->value]);
        return $instance;
    }

    /**
     * Record a new hire against this requisition and update workforce plan.
     */
    public function recordHire(int|JobRequisition $requisition, int $count = 1): JobRequisition
    {
        return DB::transaction(function () use ($requisition, $count) {
            $instance = $requisition instanceof JobRequisition ? $requisition : $this->requisitionRepository->findById($requisition);

            $instance->hired_count += $count;

            if ($instance->hired_count >= $instance->headcount_needed) {
                $instance->status = JobRequisitionStatus::FULFILLED->value;
            }

            $instance->save();

            // Increment plan item fulfilled count if linked
            if ($instance->planItem) {
                $instance->planItem->fulfilled_count += $count;
                $instance->planItem->recalculate();
            }

            return $instance;
        });
    }

    /**
     * Cancel requisition.
     */
    public function cancelRequisition(int|JobRequisition $requisition): JobRequisition
    {
        $instance = $requisition instanceof JobRequisition ? $requisition : $this->requisitionRepository->findById($requisition);
        $instance->update(['status' => JobRequisitionStatus::CANCELLED->value]);
        return $instance;
    }

    /**
     * Delete requisition.
     */
    public function deleteRequisition(int|JobRequisition $requisition): bool
    {
        return $this->requisitionRepository->delete($requisition);
    }

    /**
     * Get approved workforce plan items available for recruitment.
     */
    public function getAvailablePlanItems(?int $departmentId = null): Collection
    {
        $query = WorkforcePlanItem::whereHas('plan', function ($q) {
            $q->whereIn('status', [
                WorkforcePlanStatus::APPROVED->value,
                WorkforcePlanStatus::ACTIVE->value,
            ]);
        })->with(['department.translations', 'jobTitle.translations', 'branch', 'plan']);

        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        return $query->get();
    }

    /**
     * Get KPI summary metrics.
     */
    public function getKPIs(): array
    {
        return $this->requisitionRepository->getKPIs();
    }
}
