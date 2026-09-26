<?php

namespace Modules\Accounting\Services;

use Modules\Accounting\Models\Entity;
use Modules\Accounting\Repositories\Contracts\AccountingDashboardRepositoryInterface;
use Modules\Settings\Models\Setting;

class AccountingDashboardService
{
    public function __construct(
        protected AccountingDashboardRepositoryInterface $repository
    ) {}

    /**
     * Compile and format all dashboard data for presentation.
     */
    public function getDashboardData(?int $entityId = null, ?string $fromDate = null, ?string $toDate = null): array
    {
        $entities = Entity::with('translations')->orderBy('id')->get();

        $kpis = $this->repository->getFinancialKPIs($entityId, $fromDate, $toDate);
        $pending = $this->repository->getPendingActionsSummary($entityId);
        $trend = $this->repository->getMonthlyRevenueExpenseTrend($entityId, 6);
        $cashAccounts = $this->repository->getCashAndBankBalances($entityId);
        $expenseBreakdown = $this->repository->getExpenseBreakdown($entityId);
        $cashFlowTrend = $this->repository->getCashFlowMonthlyTrend($entityId, 6);
        $liquidityDistribution = $this->repository->getLiquidityDistribution($entityId);
        $journalActivity = $this->repository->getJournalActivityBreakdown($entityId);
        $recentEntries = $this->repository->getRecentJournalEntries($entityId, 6);

        // Pending Action alerts cards
        $pendingActions = [
            'draft_entries' => [
                'count' => $pending['draft_entries'],
                'label' => __('Draft journal entries needing posting'),
                'icon'  => 'bx-receipt',
                'route' => route('accounting.journal-entries.index', ['status' => 'draft']),
                'badge' => 'bg-label-warning',
            ],
            'draft_recs' => [
                'count' => $pending['draft_recs'],
                'label' => __('Draft bank reconciliations pending matching'),
                'icon'  => 'bx-check-shield',
                'route' => route('accounting.bank-reconciliations.index', ['status' => 'draft']),
                'badge' => 'bg-label-info',
            ],
        ];

        // Currency symbol from settings or default
        $systemSetting = Setting::instance();
        $currencySymbol = $systemSetting?->currency_symbol ?: ($systemSetting?->currency ? __($systemSetting->currency) : 'د.ك');

        return [
            'entities'              => $entities,
            'selectedEntityId'      => $entityId,
            'kpis'                  => $kpis,
            'pending'               => $pending,
            'pendingActions'        => $pendingActions,
            'trend'                 => $trend,
            'cashFlowTrend'         => $cashFlowTrend,
            'liquidityDistribution' => $liquidityDistribution,
            'journalActivity'       => $journalActivity,
            'cashAccounts'          => $cashAccounts,
            'expenseBreakdown'      => $expenseBreakdown,
            'recentEntries'         => $recentEntries,
            'currencySymbol'        => $currencySymbol,
        ];
    }
}
