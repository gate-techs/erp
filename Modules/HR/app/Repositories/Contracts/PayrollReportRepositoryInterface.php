<?php

namespace Modules\HR\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;

interface PayrollReportRepositoryInterface
{
    /**
     * Get query builder for Payroll Items (Monthly Payroll Sheet).
     *
     * @param array<string, mixed> $filters
     * @return Builder
     */
    public function getPayrollSheetQuery(array $filters = []): Builder;

    /**
     * Get paginated Payroll Items for Monthly Payroll Sheet.
     *
     * @param array<string, mixed> $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPayrollSheetPaginated(array $filters = [], int $perPage = 25): LengthAwarePaginator;

    /**
     * Get all filtered Payroll Items without pagination.
     *
     * @param array<string, mixed> $filters
     * @return Collection
     */
    public function getPayrollSheetRecords(array $filters = []): Collection;

    /**
     * Get summary KPI totals for the monthly payroll sheet.
     *
     * @param array<string, mixed> $filters
     * @return array<string, float>
     */
    public function getPayrollSheetKPIs(array $filters = []): array;

    /**
     * Get query builder for Loans & Installments.
     *
     * @param array<string, mixed> $filters
     * @return Builder
     */
    public function getLoansQuery(array $filters = []): Builder;

    /**
     * Get paginated Loans.
     *
     * @param array<string, mixed> $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getLoansPaginated(array $filters = [], int $perPage = 25): LengthAwarePaginator;

    /**
     * Get all filtered Loans without pagination.
     *
     * @param array<string, mixed> $filters
     * @return Collection
     */
    public function getLoansRecords(array $filters = []): Collection;

    /**
     * Get summary KPI totals for Loans.
     *
     * @param array<string, mixed> $filters
     * @return array<string, float>
     */
    public function getLoansKPIs(array $filters = []): array;

    /**
     * Get paginated Rewards & Penalties combined log.
     *
     * @param array<string, mixed> $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getRewardsPenaltiesPaginated(array $filters = [], int $perPage = 25): LengthAwarePaginator;

    /**
     * Get all filtered Rewards & Penalties records without pagination.
     *
     * @param array<string, mixed> $filters
     * @return BaseCollection
     */
    public function getRewardsPenaltiesRecords(array $filters = []): BaseCollection;

    /**
     * Get summary KPI totals for Rewards & Penalties.
     *
     * @param array<string, mixed> $filters
     * @return array<string, float>
     */
    public function getRewardsPenaltiesKPIs(array $filters = []): array;

    /**
     * Get paginated End of Service (EOS) provisions calculation records.
     *
     * @param array<string, mixed> $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getEosProvisionsPaginated(array $filters = [], int $perPage = 25): LengthAwarePaginator;

    /**
     * Get all End of Service (EOS) provisions calculation records.
     *
     * @param array<string, mixed> $filters
     * @return BaseCollection
     */
    public function getEosProvisionsRecords(array $filters = []): BaseCollection;

    /**
     * Get summary KPI totals for End of Service (EOS) provisions.
     *
     * @param array<string, mixed> $filters
     * @return array<string, float>
     */
    public function getEosProvisionsKPIs(array $filters = []): array;

    /**
     * Find a single PayrollItem by ID with its parent payroll and employee relations for payslip.
     *
     * @param int $itemId
     * @return mixed
     */
    public function findPayrollItemWithDetails(int $itemId): mixed;
}
