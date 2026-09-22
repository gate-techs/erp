<?php

namespace Modules\HR\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\HR\Services\LeaveReportService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LeaveReportController extends Controller
{
    public function __construct(
        protected LeaveReportService $reportService
    ) {}

    /**
     * Display the interactive Leave & Permission reports.
     */
    public function index(Request $request)
    {
        $this->authorizePermission('hr.reports.leaves.view');

        $data = $this->reportService->getReportData(
            $request->all(),
            (int) $request->input('per_page', 25)
        );

        return view('hr::reports.leaves.index', $data);
    }

    /**
     * Render the printable version using the selected PrintTemplate.
     */
    public function print(Request $request)
    {
        $this->authorizePermission('hr.reports.leaves.print');

        $data = $this->reportService->getPrintData(
            $request->all(),
            $request->filled('template_id') ? (int) $request->template_id : null
        );

        return view('hr::reports.leaves.print', $data);
    }

    /**
     * Export leave & permission report to CSV / Excel spreadsheet.
     */
    public function export(Request $request): StreamedResponse
    {
        $this->authorizePermission('hr.reports.leaves.export');

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

        // Check exact permission or legacy leaves view permission as fallback
        if (method_exists($user, 'can')) {
            if ($user->can($permission) || $user->can('hr.leaves.view')) {
                return;
            }
        }

        abort(403, __('You do not have permission to access this report.'));
    }
}
