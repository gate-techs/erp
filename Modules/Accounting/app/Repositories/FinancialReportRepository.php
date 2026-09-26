<?php

namespace Modules\Accounting\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Accounting\Enums\AccountType;
use Modules\Accounting\Enums\JournalEntryStatus;
use Modules\Accounting\Models\Account;
use Modules\Accounting\Models\CostCenter;
use Modules\Accounting\Models\JournalEntry;
use Modules\Accounting\Models\JournalEntryLine;
use Modules\Accounting\Repositories\Contracts\FinancialReportRepositoryInterface;

class FinancialReportRepository implements FinancialReportRepositoryInterface
{
    /**
     * Get General Ledger data with initial balances, movements, and ending balances.
     */
    public function getGeneralLedgerData(array $filters = []): array
    {
        $entityId   = !empty($filters['entity_id']) ? (int) $filters['entity_id'] : null;
        $accountId  = !empty($filters['account_id']) ? (int) $filters['account_id'] : null;
        $journalId  = !empty($filters['journal_id']) ? (int) $filters['journal_id'] : null;
        $costCenterId = !empty($filters['cost_center_id']) ? (int) $filters['cost_center_id'] : null;
        $fromDate   = $filters['from_date'] ?? now()->startOfYear()->toDateString();
        $toDate     = $filters['to_date'] ?? now()->toDateString();
        $status     = $filters['status'] ?? JournalEntryStatus::POSTED->value;

        // Base accounts query
        $accountsQuery = Account::query()
            ->with(['translations', 'parent.translations'])
            ->where('is_active', true)
            ->when($entityId, fn ($q) => $q->where('entity_id', $entityId))
            ->when($accountId, fn ($q) => $q->where('id', $accountId))
            ->orderBy('code');

        $accounts = $accountsQuery->get();

        $reportAccounts = [];
        $grandTotalInitialDebit  = 0.0;
        $grandTotalInitialCredit = 0.0;
        $grandTotalPeriodDebit   = 0.0;
        $grandTotalPeriodCredit  = 0.0;
        $grandTotalEndingDebit   = 0.0;
        $grandTotalEndingCredit  = 0.0;

        foreach ($accounts as $account) {
            $isDebitNormal = in_array($account->account_type?->value, [AccountType::ASSET->value, AccountType::EXPENSE->value]);

            // 1. Initial Balance before from_date
            $initial = $this->getAccountInitialBalance($account->id, $fromDate, $filters);
            $initialDebit  = (float) $initial['debit'];
            $initialCredit = (float) $initial['credit'];
            $initialNet    = $isDebitNormal ? ($initialDebit - $initialCredit) : ($initialCredit - $initialDebit);

            // 2. Movements in [from_date, to_date]
            $movements = $this->getAccountMovements($account->id, $fromDate, $toDate, $filters);

            // If account has no initial balance and no movement, skip unless specifically filtered
            if (!$accountId && abs($initialDebit) < 0.0001 && abs($initialCredit) < 0.0001 && $movements->isEmpty()) {
                continue;
            }

            // Calculate running balance for each movement line
            $runningBalance = $initialNet;
            $periodDebit  = 0.0;
            $periodCredit = 0.0;

            $enrichedMovements = $movements->map(function ($line) use (&$runningBalance, &$periodDebit, &$periodCredit, $isDebitNormal) {
                $debit  = (float) $line->debit;
                $credit = (float) $line->credit;

                $periodDebit  += $debit;
                $periodCredit += $credit;

                if ($isDebitNormal) {
                    $runningBalance += ($debit - $credit);
                } else {
                    $runningBalance += ($credit - $debit);
                }

                $line->running_balance = round($runningBalance, 4);
                return $line;
            });

            // 3. Ending Balance
            $totalDebitAll  = $initialDebit + $periodDebit;
            $totalCreditAll = $initialCredit + $periodCredit;
            $endingNet = $isDebitNormal ? ($totalDebitAll - $totalCreditAll) : ($totalCreditAll - $totalDebitAll);

            $endingDebit  = $endingNet > 0 ? $endingNet : 0.0;
            $endingCredit = $endingNet < 0 ? abs($endingNet) : 0.0;

            $reportAccounts[] = [
                'account'          => $account,
                'is_debit_normal'  => $isDebitNormal,
                'initial_debit'    => round($initialDebit, 2),
                'initial_credit'   => round($initialCredit, 2),
                'initial_net'      => round($initialNet, 2),
                'period_debit'     => round($periodDebit, 2),
                'period_credit'    => round($periodCredit, 2),
                'ending_debit'     => round($endingDebit, 2),
                'ending_credit'    => round($endingCredit, 2),
                'ending_net'       => round($endingNet, 2),
                'movements'        => $enrichedMovements,
                'movements_count'  => $enrichedMovements->count(),
            ];

            $grandTotalInitialDebit  += $initialDebit;
            $grandTotalInitialCredit += $initialCredit;
            $grandTotalPeriodDebit   += $periodDebit;
            $grandTotalPeriodCredit  += $periodCredit;
            $grandTotalEndingDebit   += $endingDebit;
            $grandTotalEndingCredit  += $endingCredit;
        }

        return [
            'accounts' => $reportAccounts,
            'summary'  => [
                'initial_debit'  => round($grandTotalInitialDebit, 2),
                'initial_credit' => round($grandTotalInitialCredit, 2),
                'period_debit'   => round($grandTotalPeriodDebit, 2),
                'period_credit'  => round($grandTotalPeriodCredit, 2),
                'ending_debit'   => round($grandTotalEndingDebit, 2),
                'ending_credit'  => round($grandTotalEndingCredit, 2),
                'is_balanced'    => abs($grandTotalPeriodDebit - $grandTotalPeriodCredit) < 0.01,
            ],
            'filters'  => [
                'entity_id'      => $entityId,
                'account_id'     => $accountId,
                'journal_id'     => $journalId,
                'cost_center_id' => $costCenterId,
                'from_date'      => $fromDate,
                'to_date'        => $toDate,
                'status'         => $status,
            ],
        ];
    }

    //--------------------------------------------------

    /**
     * Get initial balance for an account before a specific date.
     */
    public function getAccountInitialBalance(int $accountId, string $fromDate, array $filters = []): array
    {
        $status = $filters['status'] ?? JournalEntryStatus::POSTED->value;
        $journalId = !empty($filters['journal_id']) ? (int) $filters['journal_id'] : null;
        $costCenterId = !empty($filters['cost_center_id']) ? (int) $filters['cost_center_id'] : null;

        $query = DB::table('journal_entry_lines')
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
            ->where('journal_entry_lines.account_id', $accountId)
            ->where('journal_entries.entry_date', '<', $fromDate);

        if ($status !== 'all') {
            $query->where('journal_entries.status', $status);
        }

        if ($journalId) {
            $query->where('journal_entries.journal_id', $journalId);
        }

        if ($costCenterId) {
            $query->where('journal_entry_lines.cost_center_id', $costCenterId);
        }

        $result = $query->selectRaw('COALESCE(SUM(journal_entry_lines.debit), 0) as total_debit, COALESCE(SUM(journal_entry_lines.credit), 0) as total_credit')->first();

        return [
            'debit'  => (float) ($result->total_debit ?? 0),
            'credit' => (float) ($result->total_credit ?? 0),
        ];
    }

    //--------------------------------------------------

    /**
     * Get movement lines for an account within a date range.
     */
    public function getAccountMovements(int $accountId, string $fromDate, string $toDate, array $filters = []): Collection
    {
        $status = $filters['status'] ?? JournalEntryStatus::POSTED->value;
        $journalId = !empty($filters['journal_id']) ? (int) $filters['journal_id'] : null;
        $costCenterId = !empty($filters['cost_center_id']) ? (int) $filters['cost_center_id'] : null;

        return JournalEntryLine::query()
            ->with([
                'journalEntry.journal.translations',
                'costCenter.translations',
            ])
            ->whereHas('journalEntry', function ($q) use ($fromDate, $toDate, $status, $journalId) {
                $q->whereBetween('entry_date', [$fromDate, $toDate]);
                if ($status !== 'all') {
                    $q->where('status', $status);
                }
                if ($journalId) {
                    $q->where('journal_id', $journalId);
                }
            })
            ->where('account_id', $accountId)
            ->when($costCenterId, fn ($q) => $q->where('cost_center_id', $costCenterId))
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
            ->select('journal_entry_lines.*')
            ->orderBy('journal_entries.entry_date', 'asc')
            ->orderBy('journal_entries.id', 'asc')
            ->orderBy('journal_entry_lines.line_order', 'asc')
            ->get();
    }

    //--------------------------------------------------

    /**
     * Get list of accounts eligible for report filtering.
     */
    public function getReportAccounts(?int $entityId = null): Collection
    {
        return Account::query()
            ->with('translations')
            ->where('is_active', true)
            ->when($entityId, fn ($q) => $q->where('entity_id', $entityId))
            ->orderBy('code')
            ->get();
    }

    //--------------------------------------------------

    /**
     * Get Trial Balance data (initial, period movements, ending balances).
     */
    public function getTrialBalanceData(array $filters = []): array
    {
        $entityId     = !empty($filters['entity_id']) ? (int) $filters['entity_id'] : null;
        $costCenterId = !empty($filters['cost_center_id']) ? (int) $filters['cost_center_id'] : null;
        $journalId    = !empty($filters['journal_id']) ? (int) $filters['journal_id'] : null;
        $fromDate     = $filters['from_date'] ?? now()->startOfYear()->toDateString();
        $toDate       = $filters['to_date'] ?? now()->toDateString();
        $status       = $filters['status'] ?? JournalEntryStatus::POSTED->value;
        $hideZero     = true;
        if (isset($filters['hide_zero'])) {
            $val = (string) $filters['hide_zero'];
            if ($val === '0' || $val === 'false' || $val === 'no') {
                $hideZero = false;
            }
        }

        $accounts = Account::query()
            ->with(['translations', 'parent.translations'])
            ->where('is_active', true)
            ->when($entityId, fn ($q) => $q->where('entity_id', $entityId))
            ->orderBy('code')
            ->get();

        $rows = [];
        $totalInitialDebit  = 0.0;
        $totalInitialCredit = 0.0;
        $totalPeriodDebit   = 0.0;
        $totalPeriodCredit  = 0.0;
        $totalEndingDebit   = 0.0;
        $totalEndingCredit  = 0.0;

        // Fetch all period movements aggregated per account in one query for maximum performance
        $periodMovementsQuery = DB::table('journal_entry_lines')
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
            ->whereBetween('journal_entries.entry_date', [$fromDate, $toDate])
            ->groupBy('journal_entry_lines.account_id')
            ->select(
                'journal_entry_lines.account_id',
                DB::raw('COALESCE(SUM(journal_entry_lines.debit), 0) as period_debit'),
                DB::raw('COALESCE(SUM(journal_entry_lines.credit), 0) as period_credit')
            );

        if ($status !== 'all') {
            $periodMovementsQuery->where('journal_entries.status', $status);
        }
        if ($journalId) {
            $periodMovementsQuery->where('journal_entries.journal_id', $journalId);
        }
        if ($costCenterId) {
            $periodMovementsQuery->where('journal_entry_lines.cost_center_id', $costCenterId);
        }

        $periodMovementsMap = $periodMovementsQuery->get()->keyBy('account_id');

        foreach ($accounts as $account) {
            // 1. Initial balances before fromDate
            $initial = $this->getAccountInitialBalance($account->id, $fromDate, $filters);
            $rawInitDebit  = (float) $initial['debit'];
            $rawInitCredit = (float) $initial['credit'];
            $initNet = $rawInitDebit - $rawInitCredit;

            $initDebit  = $initNet > 0 ? $initNet : 0.0;
            $initCredit = $initNet < 0 ? abs($initNet) : 0.0;

            // 2. Period movement
            $period = $periodMovementsMap->get($account->id);
            $periodDebit  = $period ? (float) $period->period_debit : 0.0;
            $periodCredit = $period ? (float) $period->period_credit : 0.0;

            // 3. Ending balance
            $totalDebitCumulative  = $rawInitDebit + $periodDebit;
            $totalCreditCumulative = $rawInitCredit + $periodCredit;
            $endingNet = $totalDebitCumulative - $totalCreditCumulative;

            $endingDebit  = $endingNet > 0 ? $endingNet : 0.0;
            $endingCredit = $endingNet < 0 ? abs($endingNet) : 0.0;

            // Skip zero balance accounts if option is enabled
            if ($hideZero && abs($initDebit) < 0.0001 && abs($initCredit) < 0.0001 && abs($periodDebit) < 0.0001 && abs($periodCredit) < 0.0001) {
                continue;
            }

            $rows[] = [
                'account'        => $account,
                'initial_debit'  => round($initDebit, 2),
                'initial_credit' => round($initCredit, 2),
                'period_debit'   => round($periodDebit, 2),
                'period_credit'  => round($periodCredit, 2),
                'ending_debit'   => round($endingDebit, 2),
                'ending_credit'  => round($endingCredit, 2),
            ];

            $totalInitialDebit  += $initDebit;
            $totalInitialCredit += $initCredit;
            $totalPeriodDebit   += $periodDebit;
            $totalPeriodCredit  += $periodCredit;
            $totalEndingDebit   += $endingDebit;
            $totalEndingCredit  += $endingCredit;
        }

        return [
            'rows' => $rows,
            'summary' => [
                'initial_debit'  => round($totalInitialDebit, 2),
                'initial_credit' => round($totalInitialCredit, 2),
                'period_debit'   => round($totalPeriodDebit, 2),
                'period_credit'  => round($totalPeriodCredit, 2),
                'ending_debit'   => round($totalEndingDebit, 2),
                'ending_credit'  => round($totalEndingCredit, 2),
                'is_balanced'    => abs($totalEndingDebit - $totalEndingCredit) < 0.01,
            ],
            'filters' => [
                'entity_id'      => $entityId,
                'cost_center_id' => $costCenterId,
                'journal_id'     => $journalId,
                'from_date'      => $fromDate,
                'to_date'        => $toDate,
                'status'         => $status,
                'hide_zero'      => $hideZero,
            ],
        ];
    }

    //--------------------------------------------------

    /**
     * Get Income Statement (P&L) data (revenues, expenses, gross & net profit).
     */
    public function getIncomeStatementData(array $filters = []): array
    {
        $entityId     = !empty($filters['entity_id']) ? (int) $filters['entity_id'] : null;
        $costCenterId = !empty($filters['cost_center_id']) ? (int) $filters['cost_center_id'] : null;
        $journalId    = !empty($filters['journal_id']) ? (int) $filters['journal_id'] : null;
        $fromDate     = $filters['from_date'] ?? now()->startOfYear()->toDateString();
        $toDate       = $filters['to_date'] ?? now()->toDateString();
        $status       = $filters['status'] ?? JournalEntryStatus::POSTED->value;
        $hideZero     = true;
        if (isset($filters['hide_zero'])) {
            $val = (string) $filters['hide_zero'];
            if ($val === '0' || $val === 'false' || $val === 'no') {
                $hideZero = false;
            }
        }

        // Fetch income and expense accounts
        $accounts = Account::query()
            ->with(['translations', 'parent.translations'])
            ->where('is_active', true)
            ->whereIn('account_type', [AccountType::INCOME->value, AccountType::EXPENSE->value])
            ->when($entityId, fn ($q) => $q->where('entity_id', $entityId))
            ->orderBy('code')
            ->get();

        // Aggregate period movements per account
        $movementsQuery = DB::table('journal_entry_lines')
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
            ->whereBetween('journal_entries.entry_date', [$fromDate, $toDate])
            ->groupBy('journal_entry_lines.account_id')
            ->select(
                'journal_entry_lines.account_id',
                DB::raw('COALESCE(SUM(journal_entry_lines.debit), 0) as period_debit'),
                DB::raw('COALESCE(SUM(journal_entry_lines.credit), 0) as period_credit')
            );

        if ($status !== 'all') {
            $movementsQuery->where('journal_entries.status', $status);
        }
        if ($journalId) {
            $movementsQuery->where('journal_entries.journal_id', $journalId);
        }
        if ($costCenterId) {
            $movementsQuery->where('journal_entry_lines.cost_center_id', $costCenterId);
        }

        $movementsMap = $movementsQuery->get()->keyBy('account_id');

        $revenues = [];
        $expenses = [];
        $totalRevenues = 0.0;
        $totalExpenses = 0.0;

        foreach ($accounts as $account) {
            $mov = $movementsMap->get($account->id);
            $debit  = $mov ? (float) $mov->period_debit : 0.0;
            $credit = $mov ? (float) $mov->period_credit : 0.0;

            if ($account->account_type === AccountType::INCOME) {
                // Income normal balance is Credit
                $amount = $credit - $debit;
                if ($hideZero && abs($amount) < 0.0001 && abs($debit) < 0.0001 && abs($credit) < 0.0001) {
                    continue;
                }
                $revenues[] = [
                    'account' => $account,
                    'debit'   => round($debit, 2),
                    'credit'  => round($credit, 2),
                    'amount'  => round($amount, 2),
                ];
                $totalRevenues += $amount;
            } else {
                // Expense normal balance is Debit
                $amount = $debit - $credit;
                if ($hideZero && abs($amount) < 0.0001 && abs($debit) < 0.0001 && abs($credit) < 0.0001) {
                    continue;
                }
                $expenses[] = [
                    'account' => $account,
                    'debit'   => round($debit, 2),
                    'credit'  => round($credit, 2),
                    'amount'  => round($amount, 2),
                ];
                $totalExpenses += $amount;
            }
        }

        // Calculate percentages
        foreach ($revenues as &$item) {
            $item['percentage'] = $totalRevenues > 0 ? round(($item['amount'] / $totalRevenues) * 100, 2) : 0.0;
        }
        unset($item);

        foreach ($expenses as &$item) {
            $item['percentage'] = $totalRevenues > 0 ? round(($item['amount'] / $totalRevenues) * 100, 2) : 0.0;
        }
        unset($item);

        $netProfit = $totalRevenues - $totalExpenses;
        $profitMargin = $totalRevenues > 0 ? round(($netProfit / $totalRevenues) * 100, 2) : 0.0;

        return [
            'revenues' => $revenues,
            'expenses' => $expenses,
            'summary'  => [
                'total_revenues' => round($totalRevenues, 2),
                'total_expenses' => round($totalExpenses, 2),
                'net_profit'     => round($netProfit, 2),
                'is_profitable'  => $netProfit >= 0,
                'profit_margin'  => $profitMargin,
            ],
            'filters'  => [
                'entity_id'      => $entityId,
                'cost_center_id' => $costCenterId,
                'journal_id'     => $journalId,
                'from_date'      => $fromDate,
                'to_date'        => $toDate,
                'status'         => $status,
                'hide_zero'      => $hideZero,
            ],
        ];
    }

    //--------------------------------------------------

    /**
     * Get Balance Sheet data (assets, liabilities, equity, balance check).
     */
    public function getBalanceSheetData(array $filters = []): array
    {
        $entityId     = !empty($filters['entity_id']) ? (int) $filters['entity_id'] : null;
        $costCenterId = !empty($filters['cost_center_id']) ? (int) $filters['cost_center_id'] : null;
        $toDate       = $filters['to_date'] ?? now()->toDateString();
        $status       = $filters['status'] ?? JournalEntryStatus::POSTED->value;
        $hideZero     = true;
        if (isset($filters['hide_zero'])) {
            $val = (string) $filters['hide_zero'];
            if ($val === '0' || $val === 'false' || $val === 'no') {
                $hideZero = false;
            }
        }

        // 1. Balance sheet accounts (Asset, Liability, Equity)
        $accounts = Account::query()
            ->with(['translations', 'parent.translations'])
            ->where('is_active', true)
            ->whereIn('account_type', [
                AccountType::ASSET->value,
                AccountType::LIABILITY->value,
                AccountType::EQUITY->value,
            ])
            ->when($entityId, fn ($q) => $q->where('entity_id', $entityId))
            ->orderBy('code')
            ->get();

        // 2. Cumulative movements per account up to $toDate
        $movementsQuery = DB::table('journal_entry_lines')
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
            ->where('journal_entries.entry_date', '<=', $toDate)
            ->groupBy('journal_entry_lines.account_id')
            ->select(
                'journal_entry_lines.account_id',
                DB::raw('COALESCE(SUM(journal_entry_lines.debit), 0) as cum_debit'),
                DB::raw('COALESCE(SUM(journal_entry_lines.credit), 0) as cum_credit')
            );

        if ($status !== 'all') {
            $movementsQuery->where('journal_entries.status', $status);
        }
        if ($costCenterId) {
            $movementsQuery->where('journal_entry_lines.cost_center_id', $costCenterId);
        }

        $movementsMap = $movementsQuery->get()->keyBy('account_id');

        $assets = [];
        $liabilities = [];
        $equity = [];
        $totalAssets = 0.0;
        $totalLiabilities = 0.0;
        $totalEquityAccounts = 0.0;

        foreach ($accounts as $account) {
            $mov = $movementsMap->get($account->id);
            $debit  = $mov ? (float) $mov->cum_debit : 0.0;
            $credit = $mov ? (float) $mov->cum_credit : 0.0;

            if ($account->account_type === AccountType::ASSET) {
                // Assets: normal balance is Debit
                $balance = $debit - $credit;
                if ($hideZero && abs($balance) < 0.0001 && abs($debit) < 0.0001 && abs($credit) < 0.0001) {
                    continue;
                }
                $assets[] = [
                    'account' => $account,
                    'debit'   => round($debit, 2),
                    'credit'  => round($credit, 2),
                    'balance' => round($balance, 2),
                ];
                $totalAssets += $balance;
            } elseif ($account->account_type === AccountType::LIABILITY) {
                // Liabilities: normal balance is Credit
                $balance = $credit - $debit;
                if ($hideZero && abs($balance) < 0.0001 && abs($debit) < 0.0001 && abs($credit) < 0.0001) {
                    continue;
                }
                $liabilities[] = [
                    'account' => $account,
                    'debit'   => round($debit, 2),
                    'credit'  => round($credit, 2),
                    'balance' => round($balance, 2),
                ];
                $totalLiabilities += $balance;
            } else {
                // Equity: normal balance is Credit
                $balance = $credit - $debit;
                if ($hideZero && abs($balance) < 0.0001 && abs($debit) < 0.0001 && abs($credit) < 0.0001) {
                    continue;
                }
                $equity[] = [
                    'account' => $account,
                    'debit'   => round($debit, 2),
                    'credit'  => round($credit, 2),
                    'balance' => round($balance, 2),
                ];
                $totalEquityAccounts += $balance;
            }
        }

        // 3. Current period net profit/loss up to $toDate (Revenue - Expenses)
        $plMovements = DB::table('journal_entry_lines')
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
            ->join('accounts', 'journal_entry_lines.account_id', '=', 'accounts.id')
            ->where('journal_entries.entry_date', '<=', $toDate)
            ->whereIn('accounts.account_type', [AccountType::INCOME->value, AccountType::EXPENSE->value])
            ->when($entityId, fn ($q) => $q->where('accounts.entity_id', $entityId))
            ->when($status !== 'all', fn ($q) => $q->where('journal_entries.status', $status))
            ->when($costCenterId, fn ($q) => $q->where('journal_entry_lines.cost_center_id', $costCenterId))
            ->select(
                'accounts.account_type',
                DB::raw('COALESCE(SUM(journal_entry_lines.debit), 0) as total_debit'),
                DB::raw('COALESCE(SUM(journal_entry_lines.credit), 0) as total_credit')
            )
            ->groupBy('accounts.account_type')
            ->get()
            ->keyBy('account_type');

        $incomeMov  = $plMovements->get(AccountType::INCOME->value);
        $expenseMov = $plMovements->get(AccountType::EXPENSE->value);

        $totalIncome   = $incomeMov ? ((float) $incomeMov->total_credit - (float) $incomeMov->total_debit) : 0.0;
        $totalExpense  = $expenseMov ? ((float) $expenseMov->total_debit - (float) $expenseMov->total_credit) : 0.0;
        $currentPeriodProfit = round($totalIncome - $totalExpense, 2);

        // Total Equity including current period profit
        $totalEquity = round($totalEquityAccounts + $currentPeriodProfit, 2);
        $totalLiabilitiesEquity = round($totalLiabilities + $totalEquity, 2);
        $diff = round($totalAssets - $totalLiabilitiesEquity, 2);
        $isBalanced = abs($diff) < 0.01;

        // Calculate percentages of total assets
        foreach ($assets as &$item) {
            $item['percentage'] = $totalAssets > 0 ? round(($item['balance'] / $totalAssets) * 100, 2) : 0.0;
        }
        unset($item);

        foreach ($liabilities as &$item) {
            $item['percentage'] = $totalAssets > 0 ? round(($item['balance'] / $totalAssets) * 100, 2) : 0.0;
        }
        unset($item);

        foreach ($equity as &$item) {
            $item['percentage'] = $totalAssets > 0 ? round(($item['balance'] / $totalAssets) * 100, 2) : 0.0;
        }
        unset($item);

        return [
            'assets'      => $assets,
            'liabilities' => $liabilities,
            'equity'      => $equity,
            'summary'     => [
                'total_assets'              => round($totalAssets, 2),
                'total_liabilities'         => round($totalLiabilities, 2),
                'total_equity_accounts'     => round($totalEquityAccounts, 2),
                'current_period_profit'     => $currentPeriodProfit,
                'total_equity'              => $totalEquity,
                'total_liabilities_equity'  => $totalLiabilitiesEquity,
                'difference'                => $diff,
                'is_balanced'               => $isBalanced,
            ],
            'filters'     => [
                'entity_id'      => $entityId,
                'cost_center_id' => $costCenterId,
                'to_date'        => $toDate,
                'status'         => $status,
                'hide_zero'      => $hideZero,
            ],
        ];
    }

    //--------------------------------------------------

    /**
     * Get detailed Account Statement data for a specific account.
     */
    public function getAccountStatementData(array $filters = []): array
    {
        $entityId     = !empty($filters['entity_id']) ? (int) $filters['entity_id'] : null;
        $accountId    = !empty($filters['account_id']) ? (int) $filters['account_id'] : null;
        $costCenterId = !empty($filters['cost_center_id']) ? (int) $filters['cost_center_id'] : null;
        $journalId    = !empty($filters['journal_id']) ? (int) $filters['journal_id'] : null;
        $fromDate     = $filters['from_date'] ?? now()->startOfYear()->toDateString();
        $toDate       = $filters['to_date'] ?? now()->toDateString();
        $status       = $filters['status'] ?? JournalEntryStatus::POSTED->value;

        // Find selected account or fallback to first active postable account
        $account = null;
        if ($accountId) {
            $account = Account::with(['translations', 'parent.translations'])->find($accountId);
        }
        if (!$account) {
            $account = Account::with(['translations', 'parent.translations'])
                ->where('is_active', true)
                ->when($entityId, fn ($q) => $q->where('entity_id', $entityId))
                ->orderBy('code')
                ->first();
        }

        if (!$account) {
            return [
                'account'   => null,
                'lines'     => [],
                'summary'   => [
                    'initial_balance' => 0.0,
                    'total_debit'     => 0.0,
                    'total_credit'    => 0.0,
                    'net_movement'    => 0.0,
                    'ending_balance'  => 0.0,
                ],
                'filters'   => [
                    'entity_id'      => $entityId,
                    'account_id'     => null,
                    'cost_center_id' => $costCenterId,
                    'journal_id'     => $journalId,
                    'from_date'      => $fromDate,
                    'to_date'        => $toDate,
                    'status'         => $status,
                ],
            ];
        }

        $isDebitNormal = $account->account_type->normalBalance() === 'debit';

        // 1. Initial Balance before fromDate
        $initial = $this->getAccountInitialBalance($account->id, $fromDate, $filters);
        $initDebit  = (float) $initial['debit'];
        $initCredit = (float) $initial['credit'];
        $initialBalance = $isDebitNormal ? ($initDebit - $initCredit) : ($initCredit - $initDebit);

        // 2. Period Movements
        $movements = $this->getAccountMovements($account->id, $fromDate, $toDate, $filters);

        $lines = [];
        $runningBalance = $initialBalance;
        $totalDebit  = 0.0;
        $totalCredit = 0.0;

        foreach ($movements as $m) {
            $deb = (float) $m->debit;
            $crd = (float) $m->credit;

            $totalDebit  += $deb;
            $totalCredit += $crd;

            $impact = $isDebitNormal ? ($deb - $crd) : ($crd - $deb);
            $runningBalance += $impact;

            $lines[] = [
                'id'              => $m->id,
                'date'            => $m->entry_date,
                'entry_number'    => $m->entry_number,
                'entry_id'        => $m->journal_entry_id,
                'journal_name'    => $m->journal_name,
                'description'     => $m->line_description ?: $m->entry_narration,
                'reference'       => $m->reference_number,
                'partner_name'    => $m->partner_name,
                'debit'           => round($deb, 2),
                'credit'          => round($crd, 2),
                'running_balance' => round($runningBalance, 2),
            ];
        }

        $netMovement   = $isDebitNormal ? ($totalDebit - $totalCredit) : ($totalCredit - $totalDebit);
        $endingBalance = $initialBalance + $netMovement;

        return [
            'account'   => $account,
            'lines'     => $lines,
            'summary'   => [
                'initial_balance' => round($initialBalance, 2),
                'total_debit'     => round($totalDebit, 2),
                'total_credit'    => round($totalCredit, 2),
                'net_movement'    => round($netMovement, 2),
                'ending_balance'  => round($endingBalance, 2),
                'is_debit_normal' => $isDebitNormal,
            ],
            'filters'   => [
                'entity_id'      => $entityId,
                'account_id'     => $account->id,
                'cost_center_id' => $costCenterId,
                'journal_id'     => $journalId,
                'from_date'      => $fromDate,
                'to_date'        => $toDate,
                'status'         => $status,
            ],
        ];
    }

    //--------------------------------------------------

    /**
     * Get Cash Flow Statement data (operating, investing, financing, net change).
     */
    public function getCashFlowData(array $filters = []): array
    {
        $entityId  = !empty($filters['entity_id']) ? (int) $filters['entity_id'] : null;
        $fromDate  = $filters['from_date'] ?? now()->startOfYear()->toDateString();
        $toDate    = $filters['to_date'] ?? now()->toDateString();
        $status    = $filters['status'] ?? JournalEntryStatus::POSTED->value;

        // 1. Cash Accounts (Code starts with 1101 or reconcile under current assets)
        $cashAccounts = Account::query()
            ->where('is_active', true)
            ->where(function ($q) {
                $q->where('code', 'like', '1101%')
                  ->orWhere(fn ($sq) => $sq->where('account_type', AccountType::ASSET->value)->where('reconcile', true));
            })
            ->when($entityId, fn ($q) => $q->where('entity_id', $entityId))
            ->pluck('id');

        // Beginning cash balance (< fromDate)
        $beginningCash = 0.0;
        if ($cashAccounts->isNotEmpty()) {
            $initQuery = DB::table('journal_entry_lines')
                ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
                ->whereIn('journal_entry_lines.account_id', $cashAccounts)
                ->where('journal_entries.entry_date', '<', $fromDate);
            if ($status !== 'all') {
                $initQuery->where('journal_entries.status', $status);
            }
            $initRes = $initQuery->select(
                DB::raw('COALESCE(SUM(journal_entry_lines.debit), 0) as deb'),
                DB::raw('COALESCE(SUM(journal_entry_lines.credit), 0) as crd')
            )->first();

            $beginningCash = (float) $initRes->deb - (float) $initRes->crd;
        }

        // 2. Net Income for the period (Revenue - Expenses)
        $plMovements = DB::table('journal_entry_lines')
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
            ->join('accounts', 'journal_entry_lines.account_id', '=', 'accounts.id')
            ->whereBetween('journal_entries.entry_date', [$fromDate, $toDate])
            ->whereIn('accounts.account_type', [AccountType::INCOME->value, AccountType::EXPENSE->value])
            ->when($entityId, fn ($q) => $q->where('accounts.entity_id', $entityId))
            ->when($status !== 'all', fn ($q) => $q->where('journal_entries.status', $status))
            ->select(
                'accounts.account_type',
                DB::raw('COALESCE(SUM(journal_entry_lines.debit), 0) as deb'),
                DB::raw('COALESCE(SUM(journal_entry_lines.credit), 0) as crd')
            )
            ->groupBy('accounts.account_type')
            ->get()
            ->keyBy('account_type');

        $inc = $plMovements->get(AccountType::INCOME->value);
        $exp = $plMovements->get(AccountType::EXPENSE->value);
        $netIncome = ($inc ? ((float) $inc->crd - (float) $inc->deb) : 0.0)
                   - ($exp ? ((float) $exp->deb - (float) $exp->crd) : 0.0);

        // 3. Operating Working Capital Adjustments
        // Accounts Receivable (Code 1102) -> increase in A/R decreases cash
        $arQuery = DB::table('journal_entry_lines')
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
            ->join('accounts', 'journal_entry_lines.account_id', '=', 'accounts.id')
            ->whereBetween('journal_entries.entry_date', [$fromDate, $toDate])
            ->where('accounts.code', 'like', '1102%')
            ->when($entityId, fn ($q) => $q->where('accounts.entity_id', $entityId))
            ->when($status !== 'all', fn ($q) => $q->where('journal_entries.status', $status))
            ->select(DB::raw('COALESCE(SUM(debit - credit), 0) as change_ar'))
            ->first();
        $changeAr = -1 * (float) ($arQuery->change_ar ?? 0.0);

        // Accounts Payable (Code 2101) -> increase in A/P increases cash
        $apQuery = DB::table('journal_entry_lines')
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
            ->join('accounts', 'journal_entry_lines.account_id', '=', 'accounts.id')
            ->whereBetween('journal_entries.entry_date', [$fromDate, $toDate])
            ->where('accounts.code', 'like', '2101%')
            ->when($entityId, fn ($q) => $q->where('accounts.entity_id', $entityId))
            ->when($status !== 'all', fn ($q) => $q->where('journal_entries.status', $status))
            ->select(DB::raw('COALESCE(SUM(credit - debit), 0) as change_ap'))
            ->first();
        $changeAp = (float) ($apQuery->change_ap ?? 0.0);

        // Other working capital items
        $otherWorkingCapital = 0.0;
        $netOperatingCash = $netIncome + $changeAr + $changeAp + $otherWorkingCapital;

        // 4. Investing Activities (Fixed assets changes - Code 12)
        $invQuery = DB::table('journal_entry_lines')
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
            ->join('accounts', 'journal_entry_lines.account_id', '=', 'accounts.id')
            ->whereBetween('journal_entries.entry_date', [$fromDate, $toDate])
            ->where('accounts.code', 'like', '12%')
            ->when($entityId, fn ($q) => $q->where('accounts.entity_id', $entityId))
            ->when($status !== 'all', fn ($q) => $q->where('journal_entries.status', $status))
            ->select(DB::raw('COALESCE(SUM(credit - debit), 0) as net_inv'))
            ->first();
        $netInvestingCash = (float) ($invQuery->net_inv ?? 0.0);

        // 5. Financing Activities (Equity and Long-Term Liabilities changes - Code 3, 22)
        $finQuery = DB::table('journal_entry_lines')
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
            ->join('accounts', 'journal_entry_lines.account_id', '=', 'accounts.id')
            ->whereBetween('journal_entries.entry_date', [$fromDate, $toDate])
            ->where('accounts.code', 'like', '3%')
            ->when($entityId, fn ($q) => $q->where('accounts.entity_id', $entityId))
            ->when($status !== 'all', fn ($q) => $q->where('journal_entries.status', $status))
            ->select(DB::raw('COALESCE(SUM(credit - debit), 0) as net_fin'))
            ->first();
        $netFinancingCash = (float) ($finQuery->net_fin ?? 0.0);

        $netCashChange = $netOperatingCash + $netInvestingCash + $netFinancingCash;
        $endingCash = $beginningCash + $netCashChange;

        return [
            'operating' => [
                'net_income'           => round($netIncome, 2),
                'change_ar'            => round($changeAr, 2),
                'change_ap'            => round($changeAp, 2),
                'other_working_capital'=> round($otherWorkingCapital, 2),
                'subtotal'             => round($netOperatingCash, 2),
            ],
            'investing' => [
                'fixed_assets_change'  => round($netInvestingCash, 2),
                'subtotal'             => round($netInvestingCash, 2),
            ],
            'financing' => [
                'equity_loans_change'  => round($netFinancingCash, 2),
                'subtotal'             => round($netFinancingCash, 2),
            ],
            'summary'   => [
                'beginning_cash'       => round($beginningCash, 2),
                'net_cash_change'      => round($netCashChange, 2),
                'ending_cash'          => round($endingCash, 2),
            ],
            'filters'   => [
                'entity_id' => $entityId,
                'from_date' => $fromDate,
                'to_date'   => $toDate,
                'status'    => $status,
            ],
        ];
    }

    //--------------------------------------------------

    /**
     * Get Cost Center Performance Report data (revenues, expenses, net profit per center).
     */
    public function getCostCenterReportData(array $filters = []): array
    {
        $entityId     = !empty($filters['entity_id']) ? (int) $filters['entity_id'] : null;
        $costCenterId = !empty($filters['cost_center_id']) ? (int) $filters['cost_center_id'] : null;
        $fromDate     = $filters['from_date'] ?? now()->startOfYear()->toDateString();
        $toDate       = $filters['to_date'] ?? now()->toDateString();
        $status       = $filters['status'] ?? JournalEntryStatus::POSTED->value;
        $hideZero     = true;
        if (isset($filters['hide_zero'])) {
            $val = (string) $filters['hide_zero'];
            if ($val === '0' || $val === 'false' || $val === 'no') {
                $hideZero = false;
            }
        }

        // Query Cost Centers
        $costCentersQuery = CostCenter::query()
            ->with('translations')
            ->where('status', 'active')
            ->when($entityId, fn ($q) => $q->where('entity_id', $entityId))
            ->when($costCenterId, fn ($q) => $q->where('id', $costCenterId))
            ->orderBy('code');

        $costCenters = $costCentersQuery->get();

        // Query aggregations grouped by cost_center_id and account_type
        $movementsQuery = DB::table('journal_entry_lines')
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
            ->join('accounts', 'journal_entry_lines.account_id', '=', 'accounts.id')
            ->whereBetween('journal_entries.entry_date', [$fromDate, $toDate])
            ->whereIn('accounts.account_type', [AccountType::INCOME->value, AccountType::EXPENSE->value])
            ->when($entityId, fn ($q) => $q->where('accounts.entity_id', $entityId))
            ->when($costCenterId, fn ($q) => $q->where('journal_entry_lines.cost_center_id', $costCenterId))
            ->groupBy('journal_entry_lines.cost_center_id', 'accounts.account_type')
            ->select(
                'journal_entry_lines.cost_center_id',
                'accounts.account_type',
                DB::raw('COALESCE(SUM(journal_entry_lines.debit), 0) as deb'),
                DB::raw('COALESCE(SUM(journal_entry_lines.credit), 0) as crd')
            );

        if ($status !== 'all') {
            $movementsQuery->where('journal_entries.status', $status);
        }

        $movements = $movementsQuery->get();

        // Organize map: [cost_center_id => ['income' => net, 'expense' => net]]
        $map = [];
        foreach ($movements as $m) {
            $cid = $m->cost_center_id ?? 0;
            if (!isset($map[$cid])) {
                $map[$cid] = ['income' => 0.0, 'expense' => 0.0];
            }
            if ($m->account_type === AccountType::INCOME->value) {
                $map[$cid]['income'] += ((float) $m->crd - (float) $m->deb);
            } else {
                $map[$cid]['expense'] += ((float) $m->deb - (float) $m->crd);
            }
        }

        $rows = [];
        $totalRevenues = 0.0;
        $totalExpenses = 0.0;

        foreach ($costCenters as $cc) {
            $data = $map[$cc->id] ?? ['income' => 0.0, 'expense' => 0.0];
            $rev = round($data['income'], 2);
            $exp = round($data['expense'], 2);
            $net = round($rev - $exp, 2);

            if ($hideZero && abs($rev) < 0.0001 && abs($exp) < 0.0001) {
                continue;
            }

            $rows[] = [
                'cost_center'    => $cc,
                'revenues'       => $rev,
                'expenses'       => $exp,
                'net_profit'     => $net,
                'profit_margin'  => $rev > 0 ? round(($net / $rev) * 100, 2) : 0.0,
            ];

            $totalRevenues += $rev;
            $totalExpenses += $exp;
        }

        // Check if there are unassigned movements (cost_center_id = null)
        if (isset($map[0])) {
            $unassignedRev = round($map[0]['income'], 2);
            $unassignedExp = round($map[0]['expense'], 2);
            $unassignedNet = round($unassignedRev - $unassignedExp, 2);

            if (!$hideZero || abs($unassignedRev) > 0.0001 || abs($unassignedExp) > 0.0001) {
                $rows[] = [
                    'cost_center'    => null,
                    'revenues'       => $unassignedRev,
                    'expenses'       => $unassignedExp,
                    'net_profit'     => $unassignedNet,
                    'profit_margin'  => $unassignedRev > 0 ? round(($unassignedNet / $unassignedRev) * 100, 2) : 0.0,
                ];
                $totalRevenues += $unassignedRev;
                $totalExpenses += $unassignedExp;
            }
        }

        $totalNetProfit = round($totalRevenues - $totalExpenses, 2);
        $totalMargin = $totalRevenues > 0 ? round(($totalNetProfit / $totalRevenues) * 100, 2) : 0.0;

        return [
            'rows'    => $rows,
            'summary' => [
                'total_revenues' => round($totalRevenues, 2),
                'total_expenses' => round($totalExpenses, 2),
                'net_profit'     => $totalNetProfit,
                'profit_margin'  => $totalMargin,
            ],
            'filters' => [
                'entity_id'      => $entityId,
                'cost_center_id' => $costCenterId,
                'from_date'      => $fromDate,
                'to_date'        => $toDate,
                'status'         => $status,
                'hide_zero'      => $hideZero,
            ],
        ];
    }
}
