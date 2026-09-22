<?php

namespace Modules\HR\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\HR\Services\EmployeeReportService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EmployeeReportController extends Controller
{
    public function __construct(
        protected EmployeeReportService $reportService
    ) {}

    /**
     * Display the interactive Employee Master, Expiring Documents, and Demographics reports.
     */
    public function index(Request $request)
    {
        $this->authorizePermission('hr.reports.employees.view');

        $data = $this->reportService->getReportData(
            $request->all(),
            (int) $request->input('per_page', 25)
        );

        return view('hr::reports.employees.index', $data);
    }

    /**
     * Render the printable version of the employee master / expiring documents / demographics.
     */
    public function print(Request $request)
    {
        $this->authorizePermission('hr.reports.employees.print');

        $data = $this->reportService->getPrintData(
            $request->all(),
            $request->filled('template_id') ? (int) $request->template_id : null
        );

        return view('hr::reports.employees.print', $data);
    }

    /**
     * Export employee master or documents report to CSV / Excel spreadsheet.
     */
    public function export(Request $request): StreamedResponse
    {
        $this->authorizePermission('hr.reports.employees.export');

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

        // Check exact permission or legacy employees view permission as fallback
        if (method_exists($user, 'can')) {
            if ($user->can($permission) || $user->can('hr.employees.view') || $user->can('hr.reports.view')) {
                return;
            }
        }

        abort(403, __('You do not have permission to access this report.'));
    }
}
