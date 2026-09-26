<?php

namespace Modules\Accounting\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Accounting\Services\AccountingDashboardService;

class AccountingDashboardController extends Controller
{
    public function __construct(
        protected AccountingDashboardService $service
    ) {}

    /**
     * Display the financial & accounting master command dashboard.
     */
    public function index(Request $request): View
    {
        $entityId = $request->integer('entity_id') ?: null;
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $data = $this->service->getDashboardData($entityId, $fromDate, $toDate);

        return view('accounting::dashboard', $data);
    }
}
