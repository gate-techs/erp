<?php

namespace Modules\Accounting\Services;

use Modules\Accounting\Models\Account;
use Modules\Accounting\Models\CostCenter;
use Modules\Accounting\Models\Entity;
use Modules\Accounting\Models\Journal;
use Modules\Accounting\Repositories\Contracts\FinancialReportRepositoryInterface;
use Modules\Settings\Models\PrintTemplate;
use Modules\Settings\Models\Setting;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FinancialReportService
{
    /**
     * FinancialReportService constructor.
     */
    public function __construct(
        protected FinancialReportRepositoryInterface $reportRepository
    ) {}

    //--------------------------------------------------

    /**
     * Get data for General Ledger index view with filters and available options.
     */
    public function getGeneralLedgerReport(array $filters = []): array
    {
        $reportData = $this->reportRepository->getGeneralLedgerData($filters);
        $filterOptions = $this->getFilterOptions($filters['entity_id'] ?? null);

        $printTemplates = PrintTemplate::where('is_active', true)->get();
        $defaultTemplate = $printTemplates->where('is_default', true)->first()
            ?? $printTemplates->where('code', 'general_report')->first()
            ?? $printTemplates->first();

        $globalSetting = Setting::first();

        return array_merge($reportData, [
            'filterOptions'   => $filterOptions,
            'printTemplates'  => $printTemplates,
            'defaultTemplate' => $defaultTemplate,
            'globalSetting'   => $globalSetting,
        ]);
    }

    //--------------------------------------------------

    /**
     * Get data for printing General Ledger using Settings printable template.
     */
    public function getGeneralLedgerPrintData(array $filters = [], ?int $templateId = null): array
    {
        $reportData = $this->reportRepository->getGeneralLedgerData($filters);

        $availableTemplates = PrintTemplate::where('is_active', true)->get();
        $template = null;

        if ($templateId) {
            $template = $availableTemplates->where('id', $templateId)->first();
        }

        $template = $template
            ?? $availableTemplates->where('code', 'general_report')->first()
            ?? $availableTemplates->where('is_default', true)->first()
            ?? $availableTemplates->first();

        $globalSetting = Setting::first();

        return array_merge($reportData, [
            'template'       => $template,
            'globalSetting'  => $globalSetting,
            'printedAt'      => now(),
        ]);
    }

    //--------------------------------------------------

    /**
     * Get dropdown options for filters (entities, journals, accounts, cost centers).
     */
    public function getFilterOptions(?int $entityId = null): array
    {
        $entities = Entity::where('status', 'active')->get();

        $journalsQuery = Journal::where('is_active', true);
        if ($entityId) {
            $journalsQuery->where('entity_id', $entityId);
        }
        $journals = $journalsQuery->get();

        $accountsQuery = Account::where('is_active', true)->orderBy('code');
        if ($entityId) {
            $accountsQuery->where('entity_id', $entityId);
        }
        $accounts = $accountsQuery->get();

        $costCenters = CostCenter::where('status', 'active')->orderBy('code')->get();

        return [
            'entities'    => $entities,
            'journals'    => $journals,
            'accounts'    => $accounts,
            'costCenters' => $costCenters,
        ];
    }

    //--------------------------------------------------

    /**
     * Export General Ledger as downloadable CSV (Excel-compatible UTF-8 BOM).
     */
    public function exportGeneralLedgerCsv(array $filters = []): StreamedResponse
    {
        $reportData = $this->reportRepository->getGeneralLedgerData($filters);
        $accounts   = $reportData['accounts'];
        $summary    = $reportData['summary'];

        $filename = 'general_ledger_' . now()->format('Y_m_d_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        return response()->stream(function () use ($accounts, $summary) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM for Excel Arabic character support
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header titles
            fputcsv($handle, [
                __('Account Code'),
                __('Account Name'),
                __('Date'),
                __('Entry Number'),
                __('Journal'),
                __('Description'),
                __('Debit'),
                __('Credit'),
                __('Cumulative Balance'),
            ]);

            foreach ($accounts as $accData) {
                $account = $accData['account'];

                // Account header line
                fputcsv($handle, [
                    $account->code,
                    $account->name,
                    '',
                    '',
                    '',
                    __('Opening Balance'),
                    $accData['initial_debit'],
                    $accData['initial_credit'],
                    $accData['initial_net'],
                ]);

                // Movement lines
                foreach ($accData['movements'] as $move) {
                    fputcsv($handle, [
                        '',
                        '',
                        $move->journalEntry?->entry_date?->format('Y-m-d') ?? '',
                        $move->journalEntry?->entry_number ?? '',
                        $move->journalEntry?->journal?->name ?? '',
                        $move->description ?: ($move->journalEntry?->description ?? ''),
                        $move->debit > 0 ? number_format($move->debit, 2, '.', '') : '',
                        $move->credit > 0 ? number_format($move->credit, 2, '.', '') : '',
                        number_format($move->running_balance, 2, '.', ''),
                    ]);
                }

                // Account ending summary line
                fputcsv($handle, [
                    $account->code,
                    $account->name . ' - ' . __('Closing Balance'),
                    '',
                    '',
                    '',
                    __('Total with Closing Balance'),
                    $accData['period_debit'],
                    $accData['period_credit'],
                    $accData['ending_net'],
                ]);

                // Blank row separator
                fputcsv($handle, []);
            }

            // Grand Total row
            fputcsv($handle, [
                __('Grand Total General Ledger'),
                '',
                '',
                '',
                '',
                '',
                number_format($summary['period_debit'], 2, '.', ''),
                number_format($summary['period_credit'], 2, '.', ''),
                '',
            ]);

            fclose($handle);
        }, 200, $headers);
    }

    //--------------------------------------------------

    /**
     * Get data for Trial Balance index view with filters and available options.
     */
    public function getTrialBalanceReport(array $filters = []): array
    {
        $reportData = $this->reportRepository->getTrialBalanceData($filters);
        $filterOptions = $this->getFilterOptions($filters['entity_id'] ?? null);

        $printTemplates = PrintTemplate::where('is_active', true)->get();
        $defaultTemplate = $printTemplates->where('is_default', true)->first()
            ?? $printTemplates->where('code', 'general_report')->first()
            ?? $printTemplates->first();

        $globalSetting = Setting::first();

        return array_merge($reportData, [
            'filterOptions'   => $filterOptions,
            'printTemplates'  => $printTemplates,
            'defaultTemplate' => $defaultTemplate,
            'globalSetting'   => $globalSetting,
        ]);
    }

    //--------------------------------------------------

    /**
     * Get data for printing Trial Balance.
     */
    public function getTrialBalancePrintData(array $filters = [], ?int $templateId = null): array
    {
        $reportData = $this->reportRepository->getTrialBalanceData($filters);

        $availableTemplates = PrintTemplate::where('is_active', true)->get();
        $template = null;

        if ($templateId) {
            $template = $availableTemplates->where('id', $templateId)->first();
        }

        $template = $template
            ?? $availableTemplates->where('code', 'general_report')->first()
            ?? $availableTemplates->where('is_default', true)->first()
            ?? $availableTemplates->first();

        $globalSetting = Setting::first();

        return array_merge($reportData, [
            'template'      => $template,
            'globalSetting' => $globalSetting,
            'printedAt'     => now(),
        ]);
    }

    //--------------------------------------------------

    /**
     * Export Trial Balance as downloadable CSV.
     */
    public function exportTrialBalanceCsv(array $filters = []): StreamedResponse
    {
        $reportData = $this->reportRepository->getTrialBalanceData($filters);
        $rows       = $reportData['rows'];
        $summary    = $reportData['summary'];

        $filename = 'trial_balance_' . now()->format('Y_m_d_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        return response()->stream(function () use ($rows, $summary) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Headers
            fputcsv($handle, [
                __('Account Code'),
                __('Account Name'),
                __('Opening Balance - Debit'),
                __('Opening Balance - Credit'),
                __('Period Movements - Debit'),
                __('Period Movements - Credit'),
                __('Closing Balance - Debit'),
                __('Closing Balance - Credit'),
            ]);

            foreach ($rows as $r) {
                $acc = $r['account'];
                fputcsv($handle, [
                    $acc->code,
                    $acc->name,
                    $r['initial_debit'] > 0 ? number_format($r['initial_debit'], 2, '.', '') : '',
                    $r['initial_credit'] > 0 ? number_format($r['initial_credit'], 2, '.', '') : '',
                    $r['period_debit'] > 0 ? number_format($r['period_debit'], 2, '.', '') : '',
                    $r['period_credit'] > 0 ? number_format($r['period_credit'], 2, '.', '') : '',
                    $r['ending_debit'] > 0 ? number_format($r['ending_debit'], 2, '.', '') : '',
                    $r['ending_credit'] > 0 ? number_format($r['ending_credit'], 2, '.', '') : '',
                ]);
            }

            // Summary Totals
            fputcsv($handle, [
                __('Grand Total'),
                '',
                number_format($summary['initial_debit'], 2, '.', ''),
                number_format($summary['initial_credit'], 2, '.', ''),
                number_format($summary['period_debit'], 2, '.', ''),
                number_format($summary['period_credit'], 2, '.', ''),
                number_format($summary['ending_debit'], 2, '.', ''),
                number_format($summary['ending_credit'], 2, '.', ''),
            ]);

            fclose($handle);
        }, 200, $headers);
    }

    //--------------------------------------------------

    /**
     * Get data for Income Statement index view with filters and available options.
     */
    public function getIncomeStatementReport(array $filters = []): array
    {
        $reportData = $this->reportRepository->getIncomeStatementData($filters);
        $filterOptions = $this->getFilterOptions($filters['entity_id'] ?? null);

        $printTemplates = PrintTemplate::where('is_active', true)->get();
        $defaultTemplate = $printTemplates->where('is_default', true)->first()
            ?? $printTemplates->where('code', 'general_report')->first()
            ?? $printTemplates->first();

        $globalSetting = Setting::first();

        return array_merge($reportData, [
            'filterOptions'   => $filterOptions,
            'printTemplates'  => $printTemplates,
            'defaultTemplate' => $defaultTemplate,
            'globalSetting'   => $globalSetting,
        ]);
    }

    //--------------------------------------------------

    /**
     * Get data for printing Income Statement.
     */
    public function getIncomeStatementPrintData(array $filters = [], ?int $templateId = null): array
    {
        $reportData = $this->reportRepository->getIncomeStatementData($filters);

        $availableTemplates = PrintTemplate::where('is_active', true)->get();
        $template = null;

        if ($templateId) {
            $template = $availableTemplates->where('id', $templateId)->first();
        }

        $template = $template
            ?? $availableTemplates->where('code', 'general_report')->first()
            ?? $availableTemplates->where('is_default', true)->first()
            ?? $availableTemplates->first();

        $globalSetting = Setting::first();

        return array_merge($reportData, [
            'template'       => $template,
            'globalSetting'  => $globalSetting,
            'printedAt'      => now(),
        ]);
    }

    //--------------------------------------------------

    /**
     * Export Income Statement as downloadable CSV (Excel-compatible UTF-8 BOM).
     */
    public function exportIncomeStatementCsv(array $filters = []): StreamedResponse
    {
        $reportData = $this->reportRepository->getIncomeStatementData($filters);
        $revenues   = $reportData['revenues'];
        $expenses   = $reportData['expenses'];
        $summary    = $reportData['summary'];

        $filename = 'income_statement_' . now()->format('Y_m_d_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        return response()->stream(function () use ($revenues, $expenses, $summary) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM for Excel Arabic character support
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Section 1: Revenues
            fputcsv($handle, [__('=== Revenues ===')]);
            fputcsv($handle, [
                __('Account Code'),
                __('Account Name'),
                __('Debit'),
                __('Credit'),
                __('Net Revenue'),
                __('Percentage %'),
            ]);

            foreach ($revenues as $r) {
                $acc = $r['account'];
                fputcsv($handle, [
                    $acc->code,
                    $acc->name,
                    $r['debit'] > 0 ? number_format($r['debit'], 2, '.', '') : '',
                    $r['credit'] > 0 ? number_format($r['credit'], 2, '.', '') : '',
                    number_format($r['amount'], 2, '.', ''),
                    $r['percentage'] . '%',
                ]);
            }

            fputcsv($handle, [
                __('Total Revenues'),
                '',
                '',
                '',
                number_format($summary['total_revenues'], 2, '.', ''),
                '100.00%',
            ]);

            fputcsv($handle, []); // empty row

            // Section 2: Expenses
            fputcsv($handle, [__('=== Expenses ===')]);
            fputcsv($handle, [
                __('Account Code'),
                __('Account Name'),
                __('Debit'),
                __('Credit'),
                __('Net Expense'),
                __('Percentage of Revenue %'),
            ]);

            foreach ($expenses as $e) {
                $acc = $e['account'];
                fputcsv($handle, [
                    $acc->code,
                    $acc->name,
                    $e['debit'] > 0 ? number_format($e['debit'], 2, '.', '') : '',
                    $e['credit'] > 0 ? number_format($e['credit'], 2, '.', '') : '',
                    number_format($e['amount'], 2, '.', ''),
                    $e['percentage'] . '%',
                ]);
            }

            fputcsv($handle, [
                __('Total Expenses'),
                '',
                '',
                '',
                number_format($summary['total_expenses'], 2, '.', ''),
                '',
            ]);

            fputcsv($handle, []); // empty row

            // Net Profit / Loss
            fputcsv($handle, [
                $summary['is_profitable'] ? __('Net Profit for Period') : __('Net Loss for Period'),
                '',
                '',
                '',
                number_format($summary['net_profit'], 2, '.', ''),
                $summary['profit_margin'] . '%',
            ]);

            fclose($handle);
        }, 200, $headers);
    }

    //--------------------------------------------------

    /**
     * Get data for Balance Sheet index view with filters and available options.
     */
    public function getBalanceSheetReport(array $filters = []): array
    {
        $reportData = $this->reportRepository->getBalanceSheetData($filters);
        $filterOptions = $this->getFilterOptions($filters['entity_id'] ?? null);

        $printTemplates = PrintTemplate::where('is_active', true)->get();
        $defaultTemplate = $printTemplates->where('is_default', true)->first()
            ?? $printTemplates->where('code', 'general_report')->first()
            ?? $printTemplates->first();

        $globalSetting = Setting::first();

        return array_merge($reportData, [
            'filterOptions'   => $filterOptions,
            'printTemplates'  => $printTemplates,
            'defaultTemplate' => $defaultTemplate,
            'globalSetting'   => $globalSetting,
        ]);
    }

    //--------------------------------------------------

    /**
     * Get data for printing Balance Sheet.
     */
    public function getBalanceSheetPrintData(array $filters = [], ?int $templateId = null): array
    {
        $reportData = $this->reportRepository->getBalanceSheetData($filters);

        $availableTemplates = PrintTemplate::where('is_active', true)->get();
        $template = null;

        if ($templateId) {
            $template = $availableTemplates->where('id', $templateId)->first();
        }

        $template = $template
            ?? $availableTemplates->where('code', 'general_report')->first()
            ?? $availableTemplates->where('is_default', true)->first()
            ?? $availableTemplates->first();

        $globalSetting = Setting::first();

        return array_merge($reportData, [
            'template'       => $template,
            'globalSetting'  => $globalSetting,
            'printedAt'      => now(),
        ]);
    }

    //--------------------------------------------------

    /**
     * Export Balance Sheet as downloadable CSV (Excel-compatible UTF-8 BOM).
     */
    public function exportBalanceSheetCsv(array $filters = []): StreamedResponse
    {
        $reportData  = $this->reportRepository->getBalanceSheetData($filters);
        $assets      = $reportData['assets'];
        $liabilities = $reportData['liabilities'];
        $equity      = $reportData['equity'];
        $summary     = $reportData['summary'];

        $filename = 'balance_sheet_' . now()->format('Y_m_d_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        return response()->stream(function () use ($assets, $liabilities, $equity, $summary) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM for Excel Arabic character support
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Section 1: Assets
            fputcsv($handle, [__('=== Assets ===')]);
            fputcsv($handle, [
                __('Account Code'),
                __('Account Name'),
                __('Debit'),
                __('Credit'),
                __('Balance'),
                __('Percentage of Assets %'),
            ]);

            foreach ($assets as $r) {
                $acc = $r['account'];
                fputcsv($handle, [
                    $acc->code,
                    $acc->name,
                    $r['debit'] > 0 ? number_format($r['debit'], 2, '.', '') : '',
                    $r['credit'] > 0 ? number_format($r['credit'], 2, '.', '') : '',
                    number_format($r['balance'], 2, '.', ''),
                    $r['percentage'] . '%',
                ]);
            }

            fputcsv($handle, [
                __('Total Assets'),
                '',
                '',
                '',
                number_format($summary['total_assets'], 2, '.', ''),
                '100.00%',
            ]);

            fputcsv($handle, []); // empty row

            // Section 2: Liabilities
            fputcsv($handle, [__('=== Liabilities ===')]);
            fputcsv($handle, [
                __('Account Code'),
                __('Account Name'),
                __('Debit'),
                __('Credit'),
                __('Balance'),
                __('Percentage %'),
            ]);

            foreach ($liabilities as $r) {
                $acc = $r['account'];
                fputcsv($handle, [
                    $acc->code,
                    $acc->name,
                    $r['debit'] > 0 ? number_format($r['debit'], 2, '.', '') : '',
                    $r['credit'] > 0 ? number_format($r['credit'], 2, '.', '') : '',
                    number_format($r['balance'], 2, '.', ''),
                    $r['percentage'] . '%',
                ]);
            }

            fputcsv($handle, [
                __('Total Liabilities'),
                '',
                '',
                '',
                number_format($summary['total_liabilities'], 2, '.', ''),
                '',
            ]);

            fputcsv($handle, []); // empty row

            // Section 3: Equity
            fputcsv($handle, [__('=== Equity ===')]);
            fputcsv($handle, [
                __('Account Code'),
                __('Account Name'),
                __('Debit'),
                __('Credit'),
                __('Balance'),
                __('Percentage %'),
            ]);

            foreach ($equity as $r) {
                $acc = $r['account'];
                fputcsv($handle, [
                    $acc->code,
                    $acc->name,
                    $r['debit'] > 0 ? number_format($r['debit'], 2, '.', '') : '',
                    $r['credit'] > 0 ? number_format($r['credit'], 2, '.', '') : '',
                    number_format($r['balance'], 2, '.', ''),
                    $r['percentage'] . '%',
                ]);
            }

            // Current Period Profit/Loss row in Equity
            fputcsv($handle, [
                '',
                __('Current Period Net Profit / (Loss)'),
                '',
                '',
                number_format($summary['current_period_profit'], 2, '.', ''),
                '',
            ]);

            fputcsv($handle, [
                __('Total Equity'),
                '',
                '',
                '',
                number_format($summary['total_equity'], 2, '.', ''),
                '',
            ]);

            fputcsv($handle, []); // empty row

            // Total Liabilities & Equity vs Total Assets
            fputcsv($handle, [
                __('Total Liabilities and Equity'),
                '',
                '',
                '',
                number_format($summary['total_liabilities_equity'], 2, '.', ''),
                $summary['is_balanced'] ? __('Balanced ✔') : __('Difference:') . ' ' . $summary['difference'],
            ]);

            fclose($handle);
        }, 200, $headers);
    }

    //--------------------------------------------------

    /**
     * Get data for Account Statement index view.
     */
    public function getAccountStatementReport(array $filters = []): array
    {
        $reportData = $this->reportRepository->getAccountStatementData($filters);
        $filterOptions = $this->getFilterOptions($filters['entity_id'] ?? null);

        $printTemplates = PrintTemplate::where('is_active', true)->get();
        $defaultTemplate = $printTemplates->where('is_default', true)->first()
            ?? $printTemplates->where('code', 'general_report')->first()
            ?? $printTemplates->first();

        $globalSetting = Setting::first();

        return array_merge($reportData, [
            'filterOptions'   => $filterOptions,
            'printTemplates'  => $printTemplates,
            'defaultTemplate' => $defaultTemplate,
            'globalSetting'   => $globalSetting,
        ]);
    }

    //--------------------------------------------------

    /**
     * Get data for printing Account Statement.
     */
    public function getAccountStatementPrintData(array $filters = [], ?int $templateId = null): array
    {
        $reportData = $this->reportRepository->getAccountStatementData($filters);

        $availableTemplates = PrintTemplate::where('is_active', true)->get();
        $template = null;

        if ($templateId) {
            $template = $availableTemplates->where('id', $templateId)->first();
        }

        $template = $template
            ?? $availableTemplates->where('code', 'general_report')->first()
            ?? $availableTemplates->where('is_default', true)->first()
            ?? $availableTemplates->first();

        $globalSetting = Setting::first();

        return array_merge($reportData, [
            'template'       => $template,
            'globalSetting'  => $globalSetting,
            'printedAt'      => now(),
        ]);
    }

    //--------------------------------------------------

    /**
     * Export Account Statement to CSV.
     */
    public function exportAccountStatementCsv(array $filters = []): StreamedResponse
    {
        $reportData = $this->reportRepository->getAccountStatementData($filters);
        $account    = $reportData['account'];
        $lines      = $reportData['lines'];
        $summary    = $reportData['summary'];

        $filename = 'account_statement_' . ($account ? $account->code . '_' : '') . now()->format('Y_m_d_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        return response()->stream(function () use ($account, $lines, $summary) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, [__('Detailed Account Statement')]);
            if ($account) {
                fputcsv($handle, [__('Account:'), $account->code . ' - ' . $account->name]);
            }
            fputcsv($handle, [__('Opening Balance:'), number_format($summary['initial_balance'], 2, '.', '')]);
            fputcsv($handle, []);

            fputcsv($handle, [
                __('Date'),
                __('Entry Number'),
                __('Journal'),
                __('Description'),
                __('Reference'),
                __('Debit'),
                __('Credit'),
                __('Current Balance'),
            ]);

            foreach ($lines as $line) {
                fputcsv($handle, [
                    $line['date'],
                    $line['entry_number'],
                    $line['journal_name'],
                    $line['description'],
                    $line['reference'],
                    $line['debit'] > 0 ? number_format($line['debit'], 2, '.', '') : '',
                    $line['credit'] > 0 ? number_format($line['credit'], 2, '.', '') : '',
                    number_format($line['running_balance'], 2, '.', ''),
                ]);
            }

            fputcsv($handle, []);
            fputcsv($handle, [
                __('Total and Closing'),
                '',
                '',
                '',
                '',
                number_format($summary['total_debit'], 2, '.', ''),
                number_format($summary['total_credit'], 2, '.', ''),
                number_format($summary['ending_balance'], 2, '.', ''),
            ]);

            fclose($handle);
        }, 200, $headers);
    }

    //--------------------------------------------------

    /**
     * Get data for Cash Flow Statement index view.
     */
    public function getCashFlowReport(array $filters = []): array
    {
        $reportData = $this->reportRepository->getCashFlowData($filters);
        $filterOptions = $this->getFilterOptions($filters['entity_id'] ?? null);

        $printTemplates = PrintTemplate::where('is_active', true)->get();
        $defaultTemplate = $printTemplates->where('is_default', true)->first()
            ?? $printTemplates->where('code', 'general_report')->first()
            ?? $printTemplates->first();

        $globalSetting = Setting::first();

        return array_merge($reportData, [
            'filterOptions'   => $filterOptions,
            'printTemplates'  => $printTemplates,
            'defaultTemplate' => $defaultTemplate,
            'globalSetting'   => $globalSetting,
        ]);
    }

    //--------------------------------------------------

    /**
     * Get data for printing Cash Flow Statement.
     */
    public function getCashFlowPrintData(array $filters = [], ?int $templateId = null): array
    {
        $reportData = $this->reportRepository->getCashFlowData($filters);

        $availableTemplates = PrintTemplate::where('is_active', true)->get();
        $template = null;

        if ($templateId) {
            $template = $availableTemplates->where('id', $templateId)->first();
        }

        $template = $template
            ?? $availableTemplates->where('code', 'general_report')->first()
            ?? $availableTemplates->where('is_default', true)->first()
            ?? $availableTemplates->first();

        $globalSetting = Setting::first();

        return array_merge($reportData, [
            'template'       => $template,
            'globalSetting'  => $globalSetting,
            'printedAt'      => now(),
        ]);
    }

    //--------------------------------------------------

    /**
     * Export Cash Flow Statement to CSV.
     */
    public function exportCashFlowCsv(array $filters = []): StreamedResponse
    {
        $reportData = $this->reportRepository->getCashFlowData($filters);
        $op  = $reportData['operating'];
        $inv = $reportData['investing'];
        $fin = $reportData['financing'];
        $sum = $reportData['summary'];

        $filename = 'cash_flow_statement_' . now()->format('Y_m_d_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        return response()->stream(function () use ($op, $inv, $fin, $sum) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, [__('Statement of Cash Flows')]);
            fputcsv($handle, []);

            // Operating
            fputcsv($handle, [__('1. Operating Activities')]);
            fputcsv($handle, [__('Net Income from Operations'), number_format($op['net_income'], 2, '.', '')]);
            fputcsv($handle, [__('Change in Accounts Receivable'), number_format($op['change_ar'], 2, '.', '')]);
            fputcsv($handle, [__('Change in Accounts Payable'), number_format($op['change_ap'], 2, '.', '')]);
            fputcsv($handle, [__('Net Cash Flows from Operating Activities'), number_format($op['subtotal'], 2, '.', '')]);
            fputcsv($handle, []);

            // Investing
            fputcsv($handle, [__('2. Investing Activities')]);
            fputcsv($handle, [__('Change in Non-Current (Fixed) Assets'), number_format($inv['fixed_assets_change'], 2, '.', '')]);
            fputcsv($handle, [__('Net Cash Flows from Investing Activities'), number_format($inv['subtotal'], 2, '.', '')]);
            fputcsv($handle, []);

            // Financing
            fputcsv($handle, [__('3. Financing Activities')]);
            fputcsv($handle, [__('Change in Equity and Financing'), number_format($fin['equity_loans_change'], 2, '.', '')]);
            fputcsv($handle, [__('Net Cash Flows from Financing Activities'), number_format($fin['subtotal'], 2, '.', '')]);
            fputcsv($handle, []);

            // Summary
            fputcsv($handle, [__('Beginning Cash Balance'), number_format($sum['beginning_cash'], 2, '.', '')]);
            fputcsv($handle, [__('Net Change in Cash'), number_format($sum['net_cash_change'], 2, '.', '')]);
            fputcsv($handle, [__('Ending Cash Balance'), number_format($sum['ending_cash'], 2, '.', '')]);

            fclose($handle);
        }, 200, $headers);
    }

    //--------------------------------------------------

    /**
     * Get data for Cost Center Performance Report index view.
     */
    public function getCostCenterReport(array $filters = []): array
    {
        $reportData = $this->reportRepository->getCostCenterReportData($filters);
        $filterOptions = $this->getFilterOptions($filters['entity_id'] ?? null);

        $printTemplates = PrintTemplate::where('is_active', true)->get();
        $defaultTemplate = $printTemplates->where('is_default', true)->first()
            ?? $printTemplates->where('code', 'general_report')->first()
            ?? $printTemplates->first();

        $globalSetting = Setting::first();

        return array_merge($reportData, [
            'filterOptions'   => $filterOptions,
            'printTemplates'  => $printTemplates,
            'defaultTemplate' => $defaultTemplate,
            'globalSetting'   => $globalSetting,
        ]);
    }

    //--------------------------------------------------

    /**
     * Get data for printing Cost Center Performance Report.
     */
    public function getCostCenterReportPrintData(array $filters = [], ?int $templateId = null): array
    {
        $reportData = $this->reportRepository->getCostCenterReportData($filters);

        $availableTemplates = PrintTemplate::where('is_active', true)->get();
        $template = null;

        if ($templateId) {
            $template = $availableTemplates->where('id', $templateId)->first();
        }

        $template = $template
            ?? $availableTemplates->where('code', 'general_report')->first()
            ?? $availableTemplates->where('is_default', true)->first()
            ?? $availableTemplates->first();

        $globalSetting = Setting::first();

        return array_merge($reportData, [
            'template'       => $template,
            'globalSetting'  => $globalSetting,
            'printedAt'      => now(),
        ]);
    }

    //--------------------------------------------------

    /**
     * Export Cost Center Performance Report to CSV.
     */
    public function exportCostCenterReportCsv(array $filters = []): StreamedResponse
    {
        $reportData = $this->reportRepository->getCostCenterReportData($filters);
        $rows       = $reportData['rows'];
        $summary    = $reportData['summary'];

        $filename = 'cost_center_report_' . now()->format('Y_m_d_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        return response()->stream(function () use ($rows, $summary) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, [__('Cost Center Performance Report')]);
            fputcsv($handle, []);

            fputcsv($handle, [
                __('Cost Center Code'),
                __('Cost Center Name'),
                __('Total Revenues'),
                __('Total Expenses'),
                __('Net Profit / (Loss)'),
                __('Profit Margin %'),
            ]);

            foreach ($rows as $r) {
                $cc = $r['cost_center'];
                fputcsv($handle, [
                    $cc ? $cc->code : '—',
                    $cc ? $cc->name : __('Unassigned to Cost Center'),
                    number_format($r['revenues'], 2, '.', ''),
                    number_format($r['expenses'], 2, '.', ''),
                    number_format($r['net_profit'], 2, '.', ''),
                    $r['profit_margin'] . '%',
                ]);
            }

            fputcsv($handle, []);
            fputcsv($handle, [
                __('Grand Total'),
                '',
                number_format($summary['total_revenues'], 2, '.', ''),
                number_format($summary['total_expenses'], 2, '.', ''),
                number_format($summary['net_profit'], 2, '.', ''),
                $summary['profit_margin'] . '%',
            ]);

            fclose($handle);
        }, 200, $headers);
    }
}
