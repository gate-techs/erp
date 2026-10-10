<?php

namespace Modules\Academic\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Academic\Models\IncidentReport;

interface IncidentReportRepositoryInterface
{
    /**
     * Get query builder with filters applied.
     */
    public function query(array $filters = []): Builder;

    /**
     * Find a single report by ID.
     */
    public function find(int $id): ?IncidentReport;

    /**
     * Create a new incident report.
     */
    public function create(array $data): IncidentReport;

    /**
     * Update an existing incident report.
     */
    public function update(int $id, array $data): ?IncidentReport;

    /**
     * Delete an incident report by ID.
     */
    public function delete(int $id): bool;

    /**
     * Get active students for dropdown selection.
     */
    public function getStudents(?int $branchId = null, ?int $institutionId = null): Collection;

    /**
     * Get incident types from general_settings.
     */
    public function getIncidentTypes(): Collection;

    /**
     * Get staff/users for responsible officer and witnesses.
     */
    public function getStaffUsers(): Collection;

    /**
     * Get institutions/nurseries list.
     */
    public function getInstitutions(): Collection;

    /**
     * Get branches list.
     */
    public function getBranches(?int $institutionId = null): Collection;
}
