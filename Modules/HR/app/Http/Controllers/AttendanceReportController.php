<?php

namespace Modules\HR\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\HR\Services\AttendanceReportService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttendanceReportController extends Controller
{
    public function __construct(
        protected AttendanceReportService $reportService
    ) {}

    /**
     * Display the interactive Attendance & Time Tracking report.
     */
    public function index(Request $request)
    {
        $this->authorizePermission('hr.reports.attendance.view');

        $data = $this->reportService->getReportData(
            $request->all(),
            (int) $request->input('per_page', 25)
        );

        return view('hr::reports.attendance.index', $data);
    }

    /**
     * Render the printable version using the selected PrintTemplate.
     */
    public function print(Request $request)
    {
        $this->authorizePermission('hr.reports.attendance.print');

        $data = $this->reportService->getPrintData(
            $request->all(),
            $request->filled('template_id') ? (int) $request->template_id : null
        );

        return view('hr::reports.attendance.print', $data);
    }

    /**
     * Export attendance report to CSV / Excel spreadsheet.
     */
    public function export(Request $request): StreamedResponse
    {
        $this->authorizePermission('hr.reports.attendance.export');

        return $this->reportService->exportCsv(
            $request->all(),
            $request->input('columns')
        );
    }

    /**
     * Helper to verify user permission or fallback gracefully for super_admin / hr.attendances.view.
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

        // Check exact permission or legacy attendances view permission as fallback
        if (method_exists($user, 'can')) {
            if ($user->can($permission) || $user->can('hr.attendances.view')) {
                return;
            }
        }

        abort(403, __('You do not have permission to access this report.'));
    }
}
