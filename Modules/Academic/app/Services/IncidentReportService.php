<?php

namespace Modules\Academic\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Academic\Models\IncidentReport;
use Modules\Academic\Repositories\Contracts\IncidentReportRepositoryInterface;

class IncidentReportService
{
    public function __construct(
        protected IncidentReportRepositoryInterface $repository
    ) {}

    /**
     * Get filtered query for DataTables or export.
     */
    public function query(array $filters = []): Builder
    {
        return $this->repository->query($filters);
    }

    /**
     * Find single incident report by ID.
     */
    public function find(int $id): ?IncidentReport
    {
        return $this->repository->find($id);
    }

    /**
     * Get all necessary dropdown data for forms and index filters.
     */
    public function formData(?int $branchId = null, ?int $institutionId = null): array
    {
        return [
            'students'      => $this->repository->getStudents($branchId, $institutionId),
            'incidentTypes' => $this->repository->getIncidentTypes(),
            'staffUsers'    => $this->repository->getStaffUsers(),
            'institutions'  => $this->repository->getInstitutions(),
            'branches'      => $this->repository->getBranches($institutionId),
        ];
    }

    /**
     * Create a new incident report record.
     */
    public function create(array $data, ?int $userId = null): IncidentReport
    {
        $data['created_by'] = $userId;
        // إذا لم يُحدد user_id (المسؤول) صراحة، نضعه المستخدم الحالي
        if (empty($data['user_id'])) {
            $data['user_id'] = $userId;
        }

        return $this->repository->create($data);
    }

    /**
     * Update an existing incident report.
     */
    public function update(int $id, array $data, ?int $userId = null): ?IncidentReport
    {
        $data['updated_by'] = $userId;

        return $this->repository->update($id, $data);
    }

    /**
     * Delete an incident report.
     */
    public function delete(int $id, ?int $userId = null): bool
    {
        $report = $this->repository->find($id);
        if ($report && $userId) {
            $report->update(['deleted_by' => $userId]);
        }

        return $this->repository->delete($id);
    }

    /**
     * Dynamic helper to fetch students for specific branch via AJAX.
     */
    public function getStudentsByBranch(?int $branchId = null): Collection
    {
        return $this->repository->getStudents($branchId);
    }
}
