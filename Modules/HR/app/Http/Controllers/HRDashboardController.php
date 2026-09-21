<?php

namespace Modules\HR\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Modules\HR\Services\HRDashboardService;

class HRDashboardController extends Controller
{
    public function __construct(
        protected HRDashboardService $dashboardService
    ) {}

    /**
     * Display the comprehensive master HR command dashboard.
     */
    public function index(): View
    {
        $data = $this->dashboardService->getDashboardData();

        return view('hr::dashboard', $data);
    }
}
