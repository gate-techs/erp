<?php

namespace Modules\AuditLog\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Modules\AuditLog\Models\ActivityLog;

interface AuditLogRepositoryInterface
{
    /**
     * Get Eloquent query for DataTables.
     *
     * @param array<string, mixed> $filters
     * @return Builder
     */
    public function getDataTableQuery(array $filters = []): Builder;

    /**
     * Get paginated activity logs based on filters.
     *
     * @param array<string, mixed> $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginatedLogs(array $filters = [], int $perPage = 20): LengthAwarePaginator;

    /**
     * Find a specific activity log record by ID.
     *
     * @param int $id
     * @return ActivityLog|null
     */
    public function findById(int $id): ?ActivityLog;

    /**
     * Get summary metrics / KPIs for logs dashboard.
     *
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function getSummaryKPIs(array $filters = []): array;

    /**
     * Get list of unique event types and subjects for filter dropdowns.
     *
     * @return array<string, mixed>
     */
    public function getFilterLookups(): array;
}
