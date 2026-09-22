<?php

namespace Modules\HR\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;

interface AttendanceReportRepositoryInterface
{
    /**
     * Get Eloquent Builder for filtered attendance records.
     *
     * @param array<string, mixed> $filters
     * @return Builder
     */
    public function getFilteredQuery(array $filters = []): Builder;

    /**
     * Get all filtered attendance records without pagination.
     *
     * @param array<string, mixed> $filters
     * @return Collection
     */
    public function getFilteredRecords(array $filters = []): Collection;

    /**
     * Get paginated attendance records.
     *
     * @param array<string, mixed> $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginatedRecords(array $filters = [], int $perPage = 25): LengthAwarePaginator;

    /**
     * Compute Summary KPIs for the filtered attendance dataset.
     *
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function getSummaryKPIs(array $filters = []): array;

    /**
     * Get aggregated monthly summary (Timesheet) records grouped by employee.
     *
     * @param array<string, mixed> $filters
     * @return BaseCollection
     */
    public function getMonthlySummaryRecords(array $filters = []): BaseCollection;

    /**
     * Get paginated monthly summary (Timesheet) records.
     *
     * @param array<string, mixed> $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getMonthlySummaryPaginated(array $filters = [], int $perPage = 25): LengthAwarePaginator;
}
