<?php

namespace Modules\Accounting\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Accounting\Services\AccountingAuditLogService;

class AccountingAuditLogController extends Controller
{
    public function __construct(
        protected AccountingAuditLogService $service
    ) {}

    /**
     * Display the Accounting Audit Trail & Activity Log dashboard.
     */
    public function index(): View
    {
        $viewData = $this->service->getViewData();

        return view('accounting::audit-logs.index', $viewData);
    }

    /**
     * Get DataTables JSON records.
     */
    public function data(Request $request): JsonResponse
    {
        return $this->service->getDataTableResponse($request);
    }

    /**
     * Get single log diff details for modal.
     */
    public function show(int $id): JsonResponse
    {
        $details = $this->service->getLogDetails($id);

        return response()->json([
            'status' => true,
            'data'   => $details,
        ]);
    }
}
