<?php

namespace Modules\Accounting\Repositories;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Accounting\Enums\AccountType;
use Modules\Accounting\Enums\BankReconciliationStatus;
use Modules\Accounting\Enums\CashAccountType;
use Modules\Accounting\Enums\JournalEntryStatus;
use Modules\Accounting\Models\Account;
use Modules\Accounting\Models\BankReconciliation;
use Modules\Accounting\Models\CashAccount;
use Modules\Accounting\Models\Entity;
use Modules\Accounting\Models\FiscalYear;
use Modules\Accounting\Models\JournalEntry;
use Modules\Accounting\Models\JournalEntryLine;
use Modules\Accounting\Models\LockDate;
use Modules\Accounting\Models\PaymentMatching;
use Modules\Accounting\Repositories\Contracts\AccountingDashboardRepositoryInterface;

class AccountingDashboardRepository implements AccountingDashboardRepositoryInterface
{
    //--------------------------------------------------
    public function getFinancialKPIs(?int $entityId = null, ?string $fromDate = null, ?string $toDate = null): array
    {
        $fromDate = $fromDate ?: Carbon::now()->startOfYear()->toDateString();
        $toDate = $toDate ?: Carbon::now()->toDateString();

        // 1. Total Revenues (Income Accounts)
        $revenueQuery = JournalEntryLine::query()
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
            ->join('accounts', 'accounts.id', '=', 'journal_entry_lines.account_id')
            ->where('journal_entries.status', JournalEntryStatus::POSTED->value)
            ->whereNull('journal_entries.deleted_at')
            ->where('accounts.account_type', AccountType::INCOME->value)
            ->whereBetween('journal_entries.entry_date', [$fromDate, $toDate]);

        if ($entityId) {
            $revenueQuery->where('journal_entries.entity_id', $entityId);
        }

        $revenue = (float) $revenueQuery->selectRaw('COALESCE(SUM(credit - debit), 0) as total')->value('total');

        // 2. Total Expenses (Expense Accounts)
        $expenseQuery = JournalEntryLine::query()
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
            ->join('accounts', 'accounts.id', '=', 'journal_entry_lines.account_id')
            ->where('journal_entries.status', JournalEntryStatus::POSTED->value)
            ->whereNull('journal_entries.deleted_at')
            ->where('accounts.account_type', AccountType::EXPENSE->value)
            ->whereBetween('journal_entries.entry_date', [$fromDate, $toDate]);

        if ($entityId) {
            $expenseQuery->where('journal_entries.entity_id', $entityId);
        }

        $expenses = (float) $expenseQuery->selectRaw('COALESCE(SUM(debit - credit), 0) as total')->value('total');

        $netProfit = $revenue - $expenses;
        $profitMargin = $revenue > 0 ? round(($netProfit / $revenue) * 100, 1) : 0;

        // 3. Total Treasury & Bank Liquidity
        $liquidityQuery = JournalEntryLine::query()
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
            ->join('cash_accounts', 'cash_accounts.account_id', '=', 'journal_entry_lines.account_id')
            ->where('journal_entries.status', JournalEntryStatus::POSTED->value)
            ->whereNull('journal_entries.deleted_at');

        if ($entityId) {
            $liquidityQuery->where('journal_entries.entity_id', $entityId);
        }

        $totalLiquidity = (float) $liquidityQuery->selectRaw('COALESCE(SUM(journal_entry_lines.debit - journal_entry_lines.credit), 0) as total')->value('total');

        return [
            'revenue'        => max(0, $revenue),
            'expenses'       => max(0, $expenses),
            'net_profit'     => $netProfit,
            'profit_margin'  => $profitMargin,
            'total_liquidity'=> $totalLiquidity,
            'from_date'      => $fromDate,
            'to_date'        => $toDate,
        ];
    }

    //--------------------------------------------------
    public function getPendingActionsSummary(?int $entityId = null): array
    {
        // 1. Draft Journal Entries
        $draftEntriesQuery = JournalEntry::where('status', JournalEntryStatus::DRAFT->value);
        if ($entityId) {
            $draftEntriesQuery->where('entity_id', $entityId);
        }
        $draftEntriesCount = $draftEntriesQuery->count();

        // 2. Draft Bank Reconciliations
        $draftRecsQuery = BankReconciliation::where('status', BankReconciliationStatus::DRAFT->value);
        if ($entityId) {
            $draftRecsQuery->whereHas('cashAccount', fn($q) => $q->where('entity_id', $entityId));
        }
        $draftRecsCount = $draftRecsQuery->count();

        // 3. Unmatched Open Operations
        $unmatchedLinesQuery = JournalEntryLine::query()
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
            ->where('journal_entries.status', JournalEntryStatus::POSTED->value)
            ->whereNull('journal_entries.deleted_at')
            ->where('journal_entry_lines.reconciled', false)
            ->where(function ($q) {
                $q->where('journal_entry_lines.debit', '>', 0)
                  ->orWhere('journal_entry_lines.credit', '>', 0);
            });

        if ($entityId) {
            $unmatchedLinesQuery->where('journal_entries.entity_id', $entityId);
        }

        // 4. Current Fiscal Year and Lock Dates
        $activeFiscalYearQuery = FiscalYear::whereNull('closed_at')->orderByDesc('start_date');
        if ($entityId) {
            $activeFiscalYearQuery->where('entity_id', $entityId);
        }
        $activeFiscalYear = $activeFiscalYearQuery->first();

        $activeLockDate = null;
        if ($entityId) {
            $activeLockDate = LockDate::where('entity_id', $entityId)->first();
        }

        return [
            'draft_entries'     => $draftEntriesCount,
            'draft_recs'        => $draftRecsCount,
            'active_fiscal_year'=> $activeFiscalYear,
            'lock_date'         => $activeLockDate,
        ];
    }

    //--------------------------------------------------
    public function getMonthlyRevenueExpenseTrend(?int $entityId = null, int $months = 6): array
    {
        $labels = [];
        $revenueSeries = [];
        $expenseSeries = [];

        $now = Carbon::now()->startOfMonth();

        for ($i = $months - 1; $i >= 0; $i--) {
            $monthStart = (clone $now)->subMonths($i)->startOfMonth();
            $monthEnd = (clone $monthStart)->endOfMonth();

            $monthLabel = $monthStart->translatedFormat('M Y');
            $labels[] = $monthLabel;

            // Revenue for month
            $revQ = JournalEntryLine::query()
                ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
                ->join('accounts', 'accounts.id', '=', 'journal_entry_lines.account_id')
                ->where('journal_entries.status', JournalEntryStatus::POSTED->value)
                ->whereNull('journal_entries.deleted_at')
                ->where('accounts.account_type', AccountType::INCOME->value)
                ->whereBetween('journal_entries.entry_date', [$monthStart->toDateString(), $monthEnd->toDateString()]);

            if ($entityId) {
                $revQ->where('journal_entries.entity_id', $entityId);
            }

            $monthRev = (float) $revQ->selectRaw('COALESCE(SUM(credit - debit), 0) as total')->value('total');
            $revenueSeries[] = round(max(0, $monthRev), 2);

            // Expense for month
            $expQ = JournalEntryLine::query()
                ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
                ->join('accounts', 'accounts.id', '=', 'journal_entry_lines.account_id')
                ->where('journal_entries.status', JournalEntryStatus::POSTED->value)
                ->whereNull('journal_entries.deleted_at')
                ->where('accounts.account_type', AccountType::EXPENSE->value)
                ->whereBetween('journal_entries.entry_date', [$monthStart->toDateString(), $monthEnd->toDateString()]);

            if ($entityId) {
                $expQ->where('journal_entries.entity_id', $entityId);
            }

            $monthExp = (float) $expQ->selectRaw('COALESCE(SUM(debit - credit), 0) as total')->value('total');
            $expenseSeries[] = round(max(0, $monthExp), 2);
        }

        return [
            'labels'   => $labels,
            'revenues' => $revenueSeries,
            'expenses' => $expenseSeries,
        ];
    }

    //--------------------------------------------------
    public function getCashAndBankBalances(?int $entityId = null): array
    {
        $query = CashAccount::with(['translations', 'entity.translations', 'account.translations']);

        if ($entityId) {
            $query->where('entity_id', $entityId);
        }

        $accounts = $query->orderBy('type')->orderBy('id')->get();
        $locale = app()->getLocale();

        $result = [];
        foreach ($accounts as $ca) {
            $balance = 0;
            if ($ca->account_id) {
                $balance = (float) JournalEntryLine::query()
                    ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
                    ->where('journal_entries.status', JournalEntryStatus::POSTED->value)
                    ->whereNull('journal_entries.deleted_at')
                    ->where('journal_entry_lines.account_id', $ca->account_id)
                    ->selectRaw('COALESCE(SUM(debit - credit), 0) as total')
                    ->value('total');
            }

            $result[] = [
                'id'       => $ca->id,
                'name'     => $ca->translate($locale)?->name ?? $ca->code,
                'code'     => $ca->code,
                'type'     => $ca->type instanceof CashAccountType ? $ca->type->value : (string) $ca->type,
                'bank_name'=> $ca->bank_name,
                'balance'  => round($balance, 2),
            ];
        }

        return $result;
    }

    //--------------------------------------------------
    public function getExpenseBreakdown(?int $entityId = null): array
    {
        $fromDate = Carbon::now()->startOfYear()->toDateString();
        $toDate = Carbon::now()->toDateString();

        $query = JournalEntryLine::query()
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
            ->join('accounts', 'accounts.id', '=', 'journal_entry_lines.account_id')
            ->leftJoin('account_translations', function ($join) {
                $join->on('account_translations.account_id', '=', 'accounts.id')
                     ->where('account_translations.locale', '=', app()->getLocale());
            })
            ->where('journal_entries.status', JournalEntryStatus::POSTED->value)
            ->whereNull('journal_entries.deleted_at')
            ->where('accounts.account_type', AccountType::EXPENSE->value)
            ->whereBetween('journal_entries.entry_date', [$fromDate, $toDate]);

        if ($entityId) {
            $query->where('journal_entries.entity_id', $entityId);
        }

        $results = $query->groupBy('accounts.id', 'accounts.code', 'account_translations.name')
            ->selectRaw('accounts.code, COALESCE(account_translations.name, accounts.code) as account_name, COALESCE(SUM(journal_entry_lines.debit - journal_entry_lines.credit), 0) as total')
            ->havingRaw('total > 0')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        $labels = [];
        $series = [];

        foreach ($results as $row) {
            $labels[] = $row->account_name;
            $series[] = round((float) $row->total, 2);
        }

        return [
            'labels' => $labels,
            'series' => $series,
        ];
    }

    //--------------------------------------------------
    public function getCashFlowMonthlyTrend(?int $entityId = null, int $months = 6): array
    {
        $labels = [];
        $inflows = [];
        $outflows = [];

        $now = Carbon::now()->startOfMonth();

        for ($i = $months - 1; $i >= 0; $i--) {
            $monthStart = (clone $now)->subMonths($i)->startOfMonth();
            $monthEnd = (clone $monthStart)->endOfMonth();

            $labels[] = $monthStart->translatedFormat('M Y');

            // Cash & Bank Inflows (Debits)
            $inQ = JournalEntryLine::query()
                ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
                ->join('cash_accounts', 'cash_accounts.account_id', '=', 'journal_entry_lines.account_id')
                ->where('journal_entries.status', JournalEntryStatus::POSTED->value)
                ->whereNull('journal_entries.deleted_at')
                ->whereBetween('journal_entries.entry_date', [$monthStart->toDateString(), $monthEnd->toDateString()]);

            if ($entityId) {
                $inQ->where('journal_entries.entity_id', $entityId);
            }

            $inVal = (float) $inQ->selectRaw('COALESCE(SUM(journal_entry_lines.debit), 0) as total')->value('total');
            $inflows[] = round($inVal, 2);

            // Cash & Bank Outflows (Credits)
            $outQ = JournalEntryLine::query()
                ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
                ->join('cash_accounts', 'cash_accounts.account_id', '=', 'journal_entry_lines.account_id')
                ->where('journal_entries.status', JournalEntryStatus::POSTED->value)
                ->whereNull('journal_entries.deleted_at')
                ->whereBetween('journal_entries.entry_date', [$monthStart->toDateString(), $monthEnd->toDateString()]);

            if ($entityId) {
                $outQ->where('journal_entries.entity_id', $entityId);
            }

            $outVal = (float) $outQ->selectRaw('COALESCE(SUM(journal_entry_lines.credit), 0) as total')->value('total');
            $outflows[] = round($outVal, 2);
        }

        return [
            'labels'   => $labels,
            'inflows'  => $inflows,
            'outflows' => $outflows,
        ];
    }

    //--------------------------------------------------
    public function getLiquidityDistribution(?int $entityId = null): array
    {
        $accounts = $this->getCashAndBankBalances($entityId);

        $labels = [];
        $series = [];

        foreach ($accounts as $acc) {
            if ($acc['balance'] > 0) {
                $labels[] = $acc['name'];
                $series[] = round($acc['balance'], 2);
            }
        }

        return [
            'labels' => $labels,
            'series' => $series,
        ];
    }

    //--------------------------------------------------
    public function getJournalActivityBreakdown(?int $entityId = null): array
    {
        $query = JournalEntry::query()
            ->join('journals', 'journals.id', '=', 'journal_entries.journal_id')
            ->leftJoin('journal_translations', function ($join) {
                $join->on('journal_translations.journal_id', '=', 'journals.id')
                     ->where('journal_translations.locale', '=', app()->getLocale());
            })
            ->where('journal_entries.status', JournalEntryStatus::POSTED->value)
            ->whereNull('journal_entries.deleted_at');

        if ($entityId) {
            $query->where('journal_entries.entity_id', $entityId);
        }

        $results = $query->groupBy('journals.id', 'journals.code', 'journal_translations.name')
            ->selectRaw('journals.code, COALESCE(journal_translations.name, journals.code) as journal_name, COUNT(journal_entries.id) as entry_count')
            ->orderByDesc('entry_count')
            ->limit(6)
            ->get();

        $labels = [];
        $series = [];

        foreach ($results as $row) {
            $labels[] = $row->journal_name;
            $series[] = (int) $row->entry_count;
        }

        return [
            'labels' => $labels,
            'series' => $series,
        ];
    }

    //--------------------------------------------------
    public function getRecentJournalEntries(?int $entityId = null, int $limit = 6): array
    {
        $query = JournalEntry::with(['journal.translations', 'entity.translations', 'creator'])
            ->orderByDesc('entry_date')
            ->orderByDesc('id')
            ->limit($limit);

        if ($entityId) {
            $query->where('entity_id', $entityId);
        }

        return $query->get()->all();
    }
}
