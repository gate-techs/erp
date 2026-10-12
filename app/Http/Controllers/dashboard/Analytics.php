<?php

namespace App\Http\Controllers\dashboard;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Services\GeneralDashboardService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class Analytics extends Controller
{
    protected GeneralDashboardService $dashboardService;

    public function __construct(GeneralDashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index(): View|RedirectResponse
    {
        $user = auth()->user();
        $isAdmin = $user && (
            $user->hasAnyRole(['super_admin', 'admin', 'Super Admin', 'Admin']) ||
            $user->user_type === UserType::ADMIN
        );

        if (! $isAdmin) {
            return redirect()->route('profile.show');
        }

        $dashboardData = $this->dashboardService->getDashboardData();

        return view('content.dashboard.dashboards-analytics', array_merge($dashboardData, [
            'user' => $user,
        ]));
    }
}
