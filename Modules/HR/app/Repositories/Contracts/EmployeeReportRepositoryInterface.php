<?php

namespace Modules\HR\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;

interface EmployeeReportRepositoryInterface
{
    /**
     * Get query builder for Employee Master List.
     *
     * @param array<string, mixed> $filters
     * @return Builder
     */
    public function getEmployeeMasterQuery(array $filters = []): Builder;

    /**
     * Get paginated Employee Master List.
     *
     * @param array<string, mixed> $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getEmployeeMasterPaginated(array $filters = [], int $perPage = 25): LengthAwarePaginator;

    /**
     * Get all filtered Employee Master records without pagination.
     *
     * @param array<string, mixed> $filters
     * @return Collection
     */
    public function getEmployeeMasterRecords(array $filters = []): Collection;

    /**
     * Get summary KPI totals for Employee Master list.
     *
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function getEmployeeMasterKPIs(array $filters = []): array;

    /**
     * Get all expiring and expired documents records.
     *
     * @param array<string, mixed> $filters
     * @return BaseCollection
     */
    public function getExpiringDocumentsRecords(array $filters = []): BaseCollection;

    /**
     * Get paginated expiring and expired documents records.
     *
     * @param array<string, mixed> $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getExpiringDocumentsPaginated(array $filters = [], int $perPage = 25): LengthAwarePaginator;

    /**
     * Get summary KPI totals for expiring documents.
     *
     * @param array<string, mixed> $filters
     * @return array<string, int>
     */
    public function getExpiringDocumentsKPIs(array $filters = []): array;

    /**
     * Get workforce demographics & cost aggregation breakdown.
     *
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function getWorkforceDemographics(array $filters = []): array;
}
