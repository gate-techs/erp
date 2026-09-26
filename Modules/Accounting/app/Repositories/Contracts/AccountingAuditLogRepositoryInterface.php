<?php

namespace Modules\Accounting\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\AuditLog\Models\ActivityLog;

interface AccountingAuditLogRepositoryInterface
{
    /**
     * Get paginated accounting activity logs with filters.
     */
    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Get lookups for filter dropdowns.
     */
    public function getLookups(): array;

    /**
     * Find single log by ID.
     */
    public function findById(int $id): ?ActivityLog;

    /**
     * Get KPI summary metrics.
     */
    public function getKpiMetrics(): array;
}
