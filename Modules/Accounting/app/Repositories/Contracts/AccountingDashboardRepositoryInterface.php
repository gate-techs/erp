<?php

namespace Modules\Accounting\Repositories\Contracts;

interface AccountingDashboardRepositoryInterface
{
    /**
     * Get executive financial KPI summary for an entity (or all entities).
     */
    public function getFinancialKPIs(?int $entityId = null, ?string $fromDate = null, ?string $toDate = null): array;

    /**
     * Get pending operations summary (draft entries, unreconciled lines, unmatched allocations, lock dates).
     */
    public function getPendingActionsSummary(?int $entityId = null): array;

    /**
     * Get monthly revenue and expense trends for the last 6 or 12 months.
     */
    public function getMonthlyRevenueExpenseTrend(?int $entityId = null, int $months = 6): array;

    /**
     * Get liquidity distribution across bank and cash accounts.
     */
    public function getCashAndBankBalances(?int $entityId = null): array;

    /**
     * Get expense breakdown by major categories or cost centers.
     */
    public function getExpenseBreakdown(?int $entityId = null): array;

    /**
     * Get monthly cash flow trend (cash/bank inflows vs outflows) for the last N months.
     */
    public function getCashFlowMonthlyTrend(?int $entityId = null, int $months = 6): array;

    /**
     * Get liquidity distribution breakdown for donut chart.
     */
    public function getLiquidityDistribution(?int $entityId = null): array;

    /**
     * Get activity breakdown by journal type or name.
     */
    public function getJournalActivityBreakdown(?int $entityId = null): array;

    /**
     * Get recent journal entries.
     */
    public function getRecentJournalEntries(?int $entityId = null, int $limit = 6): array;
}
