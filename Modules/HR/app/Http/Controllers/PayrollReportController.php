<?php

namespace Modules\HR\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\HR\Services\PayrollReportService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PayrollReportController extends Controller
{
    public function __construct(
        protected PayrollReportService $reportService
    ) {}

    /**
     * Display the interactive Payroll & Compensation reports.
     */
    public function index(Request $request)
    {
        $this->authorizePermission('hr.reports.payrolls.view');

        $data = $this->reportService->getReportData(
            $request->all(),
            (int) $request->input('per_page', 25)
        );

        return view('hr::reports.payrolls.index', $data);
    }

    /**
     * Render the printable version of the multi-row payroll sheet / loans / rewards / EOS.
     */
    public function print(Request $request)
    {
        $this->authorizePermission('hr.reports.payrolls.print');

        $data = $this->reportService->getPrintData(
            $request->all(),
            $request->filled('template_id') ? (int) $request->template_id : null
        );

        return view('hr::reports.payrolls.print', $data);
    }

    /**
     * Render a single employee payslip voucher document.
     */
    public function payslip(Request $request, int $itemId)
    {
        $this->authorizePermission('hr.reports.payrolls.view');

        $data = $this->reportService->getSinglePayslipData(
            $itemId,
            $request->filled('template_id') ? (int) $request->template_id : null
        );

        return view('hr::reports.payrolls.single_payslip', $data);
    }

    /**
     * Export payroll report to CSV / Excel spreadsheet.
     */
    public function export(Request $request): StreamedResponse
    {
        $this->authorizePermission('hr.reports.payrolls.export');

        return $this->reportService->exportCsv(
            $request->all(),
            $request->input('columns')
        );
    }

    /**
     * Helper to verify user permission or fallback gracefully.
     */
    protected function authorizePermission(string $permission): void
    {
        $user = auth()->user();
        if (!$user) {
            abort(401);
        }

        // If user has super_admin role, allow unconditionally
        if (method_exists($user, 'hasRole') && $user->hasRole('super_admin')) {
            return;
        }

        // Check exact permission or legacy payrolls view permission as fallback
        if (method_exists($user, 'can')) {
            if ($user->can($permission) || $user->can('hr.payrolls.view') || $user->can('hr.reports.view')) {
                return;
            }
        }

        abort(403, __('You do not have permission to access this report.'));
    }
}
