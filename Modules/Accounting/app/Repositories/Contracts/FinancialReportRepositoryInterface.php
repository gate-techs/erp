<?php

namespace Modules\Accounting\Repositories\Contracts;

use Illuminate\Support\Collection;

interface FinancialReportRepositoryInterface
{
    /**
     * Get General Ledger data with initial balances, movements, and ending balances.
     */
    public function getGeneralLedgerData(array $filters = []): array;

    //--------------------------------------------------

    /**
     * Get initial balance for an account before a specific date.
     */
    public function getAccountInitialBalance(int $accountId, string $fromDate, array $filters = []): array;

    //--------------------------------------------------

    /**
     * Get movement lines for an account within a date range.
     */
    public function getAccountMovements(int $accountId, string $fromDate, string $toDate, array $filters = []): Collection;

    //--------------------------------------------------

    /**
     * Get list of accounts eligible for report filtering.
     */
    public function getReportAccounts(?int $entityId = null): Collection;

    //--------------------------------------------------

    /**
     * Get Trial Balance data (initial, period movements, ending balances).
     */
    public function getTrialBalanceData(array $filters = []): array;

    //--------------------------------------------------

    /**
     * Get Income Statement (P&L) data (revenues, expenses, gross & net profit).
     */
    public function getIncomeStatementData(array $filters = []): array;

    //--------------------------------------------------

    /**
     * Get Balance Sheet data (assets, liabilities, equity, balance check).
     */
    public function getBalanceSheetData(array $filters = []): array;

    //--------------------------------------------------

    /**
     * Get detailed Account Statement data for a specific account.
     */
    public function getAccountStatementData(array $filters = []): array;

    //--------------------------------------------------

    /**
     * Get Cash Flow Statement data (operating, investing, financing, net change).
     */
    public function getCashFlowData(array $filters = []): array;

    //--------------------------------------------------

    /**
     * Get Cost Center Performance Report data (revenues, expenses, net profit per center).
     */
    public function getCostCenterReportData(array $filters = []): array;
}
