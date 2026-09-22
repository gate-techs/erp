<?php

namespace Modules\HR\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;

interface LeaveReportRepositoryInterface
{
    /**
     * Get Leave Requests filtered query builder.
     *
     * @param array<string, mixed> $filters
     * @return Builder
     */
    public function getLeaveRequestsQuery(array $filters = []): Builder;

    /**
     * Get paginated Leave Requests.
     *
     * @param array<string, mixed> $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getLeaveRequestsPaginated(array $filters = [], int $perPage = 25): LengthAwarePaginator;

    /**
     * Get all filtered Leave Requests without pagination.
     *
     * @param array<string, mixed> $filters
     * @return Collection
     */
    public function getLeaveRequestsRecords(array $filters = []): Collection;

    /**
     * Get Permission Requests filtered query builder.
     *
     * @param array<string, mixed> $filters
     * @return Builder
     */
    public function getPermissionsQuery(array $filters = []): Builder;

    /**
     * Get paginated Permission Requests.
     *
     * @param array<string, mixed> $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPermissionsPaginated(array $filters = [], int $perPage = 25): LengthAwarePaginator;

    /**
     * Get all filtered Permission Requests without pagination.
     *
     * @param array<string, mixed> $filters
     * @return Collection
     */
    public function getPermissionsRecords(array $filters = []): Collection;

    /**
     * Get Leave Balances calculation records for employees.
     *
     * @param array<string, mixed> $filters
     * @return BaseCollection
     */
    public function getLeaveBalancesRecords(array $filters = []): BaseCollection;

    /**
     * Get paginated Leave Balances calculation records.
     *
     * @param array<string, mixed> $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getLeaveBalancesPaginated(array $filters = [], int $perPage = 25): LengthAwarePaginator;
}
