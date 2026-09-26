<?php

namespace Modules\Accounting\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Accounting\Services\FinancialReportService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FinancialReportController extends Controller
{
    /**
     * FinancialReportController constructor.
     */
    public function __construct(
        protected FinancialReportService $reportService
    ) {}

    //--------------------------------------------------

    /**
     * Display the General Ledger interactive report (Odoo-like).
     */
    public function generalLedger(Request $request): View
    {
        $filters = $request->only([
            'entity_id',
            'account_id',
            'journal_id',
            'cost_center_id',
            'from_date',
            'to_date',
            'status',
        ]);

        $data = $this->reportService->getGeneralLedgerReport($filters);

        return view('accounting::reports.general-ledger.index', $data);
    }

    //--------------------------------------------------

    /**
     * Render the official printable General Ledger using settings printable layout.
     */
    public function printGeneralLedger(Request $request): View
    {
        $filters = $request->only([
            'entity_id',
            'account_id',
            'journal_id',
            'cost_center_id',
            'from_date',
            'to_date',
            'status',
        ]);

        $templateId = $request->filled('template_id') ? (int) $request->get('template_id') : null;

        $data = $this->reportService->getGeneralLedgerPrintData($filters, $templateId);

        return view('accounting::reports.general-ledger.print', $data);
    }

    //--------------------------------------------------

    /**
     * Export the General Ledger to CSV / Excel format.
     */
    public function exportGeneralLedger(Request $request): StreamedResponse
    {
        $filters = $request->only([
            'entity_id',
            'account_id',
            'journal_id',
            'cost_center_id',
            'from_date',
            'to_date',
            'status',
        ]);

        return $this->reportService->exportGeneralLedgerCsv($filters);
    }

    //--------------------------------------------------

    /**
     * Display the Trial Balance report.
     */
    public function trialBalance(Request $request): View
    {
        $filters = $request->only([
            'entity_id',
            'cost_center_id',
            'journal_id',
            'from_date',
            'to_date',
            'status',
            'hide_zero',
        ]);

        $data = $this->reportService->getTrialBalanceReport($filters);

        return view('accounting::reports.trial-balance.index', $data);
    }

    //--------------------------------------------------

    /**
     * Render printable Trial Balance.
     */
    public function printTrialBalance(Request $request): View
    {
        $filters = $request->only([
            'entity_id',
            'cost_center_id',
            'journal_id',
            'from_date',
            'to_date',
            'status',
            'hide_zero',
        ]);

        $templateId = $request->filled('template_id') ? (int) $request->get('template_id') : null;

        $data = $this->reportService->getTrialBalancePrintData($filters, $templateId);

        return view('accounting::reports.trial-balance.print', $data);
    }

    //--------------------------------------------------

    /**
     * Export Trial Balance to CSV.
     */
    public function exportTrialBalance(Request $request): StreamedResponse
    {
        $filters = $request->only([
            'entity_id',
            'cost_center_id',
            'journal_id',
            'from_date',
            'to_date',
            'status',
            'hide_zero',
        ]);

        return $this->reportService->exportTrialBalanceCsv($filters);
    }

    //--------------------------------------------------

    /**
     * Render Income Statement (P&L) index report.
     */
    public function incomeStatement(Request $request): View
    {
        $filters = $request->only([
            'entity_id',
            'cost_center_id',
            'journal_id',
            'from_date',
            'to_date',
            'status',
            'hide_zero',
        ]);

        $data = $this->reportService->getIncomeStatementReport($filters);

        return view('accounting::reports.income-statement.index', $data);
    }

    //--------------------------------------------------

    /**
     * Render printable Income Statement.
     */
    public function printIncomeStatement(Request $request): View
    {
        $filters = $request->only([
            'entity_id',
            'cost_center_id',
            'journal_id',
            'from_date',
            'to_date',
            'status',
            'hide_zero',
        ]);

        $templateId = $request->filled('template_id') ? (int) $request->get('template_id') : null;

        $data = $this->reportService->getIncomeStatementPrintData($filters, $templateId);

        return view('accounting::reports.income-statement.print', $data);
    }

    //--------------------------------------------------

    /**
     * Export Income Statement to CSV.
     */
    public function exportIncomeStatement(Request $request): StreamedResponse
    {
        $filters = $request->only([
            'entity_id',
            'cost_center_id',
            'journal_id',
            'from_date',
            'to_date',
            'status',
            'hide_zero',
        ]);

        return $this->reportService->exportIncomeStatementCsv($filters);
    }

    //--------------------------------------------------

    /**
     * Render Balance Sheet (قائمة المركز المالي / الميزانية العمومية).
     */
    public function balanceSheet(Request $request): View
    {
        $filters = $request->only([
            'entity_id',
            'cost_center_id',
            'to_date',
            'status',
            'hide_zero',
        ]);

        $data = $this->reportService->getBalanceSheetReport($filters);

        return view('accounting::reports.balance-sheet.index', $data);
    }

    //--------------------------------------------------

    /**
     * Render printable Balance Sheet.
     */
    public function printBalanceSheet(Request $request): View
    {
        $filters = $request->only([
            'entity_id',
            'cost_center_id',
            'to_date',
            'status',
            'hide_zero',
        ]);

        $templateId = $request->filled('template_id') ? (int) $request->get('template_id') : null;

        $data = $this->reportService->getBalanceSheetPrintData($filters, $templateId);

        return view('accounting::reports.balance-sheet.print', $data);
    }

    //--------------------------------------------------

    /**
     * Export Balance Sheet to CSV.
     */
    public function exportBalanceSheet(Request $request): StreamedResponse
    {
        $filters = $request->only([
            'entity_id',
            'cost_center_id',
            'to_date',
            'status',
            'hide_zero',
        ]);

        return $this->reportService->exportBalanceSheetCsv($filters);
    }

    //--------------------------------------------------

    /**
     * Render Account Statement report.
     */
    public function accountStatement(Request $request): View
    {
        $filters = $request->only([
            'entity_id',
            'account_id',
            'cost_center_id',
            'journal_id',
            'from_date',
            'to_date',
            'status',
        ]);

        $data = $this->reportService->getAccountStatementReport($filters);

        return view('accounting::reports.account-statement.index', $data);
    }

    //--------------------------------------------------

    /**
     * Render printable Account Statement.
     */
    public function printAccountStatement(Request $request): View
    {
        $filters = $request->only([
            'entity_id',
            'account_id',
            'cost_center_id',
            'journal_id',
            'from_date',
            'to_date',
            'status',
        ]);

        $templateId = $request->filled('template_id') ? (int) $request->get('template_id') : null;

        $data = $this->reportService->getAccountStatementPrintData($filters, $templateId);

        return view('accounting::reports.account-statement.print', $data);
    }

    //--------------------------------------------------

    /**
     * Export Account Statement to CSV.
     */
    public function exportAccountStatement(Request $request): StreamedResponse
    {
        $filters = $request->only([
            'entity_id',
            'account_id',
            'cost_center_id',
            'journal_id',
            'from_date',
            'to_date',
            'status',
        ]);

        return $this->reportService->exportAccountStatementCsv($filters);
    }

    //--------------------------------------------------

    /**
     * Render Cash Flow Statement report.
     */
    public function cashFlow(Request $request): View
    {
        $filters = $request->only([
            'entity_id',
            'from_date',
            'to_date',
            'status',
        ]);

        $data = $this->reportService->getCashFlowReport($filters);

        return view('accounting::reports.cash-flow.index', $data);
    }

    //--------------------------------------------------

    /**
     * Render printable Cash Flow Statement.
     */
    public function printCashFlow(Request $request): View
    {
        $filters = $request->only([
            'entity_id',
            'from_date',
            'to_date',
            'status',
        ]);

        $templateId = $request->filled('template_id') ? (int) $request->get('template_id') : null;

        $data = $this->reportService->getCashFlowPrintData($filters, $templateId);

        return view('accounting::reports.cash-flow.print', $data);
    }

    //--------------------------------------------------

    /**
     * Export Cash Flow Statement to CSV.
     */
    public function exportCashFlow(Request $request): StreamedResponse
    {
        $filters = $request->only([
            'entity_id',
            'from_date',
            'to_date',
            'status',
        ]);

        return $this->reportService->exportCashFlowCsv($filters);
    }

    //--------------------------------------------------

    /**
     * Render Cost Center Performance report.
     */
    public function costCenterReport(Request $request): View
    {
        $filters = $request->only([
            'entity_id',
            'cost_center_id',
            'from_date',
            'to_date',
            'status',
            'hide_zero',
        ]);

        $data = $this->reportService->getCostCenterReport($filters);

        return view('accounting::reports.cost-center-report.index', $data);
    }

    //--------------------------------------------------

    /**
     * Render printable Cost Center Performance report.
     */
    public function printCostCenterReport(Request $request): View
    {
        $filters = $request->only([
            'entity_id',
            'cost_center_id',
            'from_date',
            'to_date',
            'status',
            'hide_zero',
        ]);

        $templateId = $request->filled('template_id') ? (int) $request->get('template_id') : null;

        $data = $this->reportService->getCostCenterReportPrintData($filters, $templateId);

        return view('accounting::reports.cost-center-report.print', $data);
    }

    //--------------------------------------------------

    /**
     * Export Cost Center Performance report to CSV.
     */
    public function exportCostCenterReport(Request $request): StreamedResponse
    {
        $filters = $request->only([
            'entity_id',
            'cost_center_id',
            'from_date',
            'to_date',
            'status',
            'hide_zero',
        ]);

        return $this->reportService->exportCostCenterReportCsv($filters);
    }
}
