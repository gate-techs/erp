<?php

namespace App\Services;

use App\Repositories\Contracts\GeneralDashboardRepositoryInterface;

class GeneralDashboardService
{
    protected GeneralDashboardRepositoryInterface $dashboardRepository;

    public function __construct(GeneralDashboardRepositoryInterface $dashboardRepository)
    {
        $this->dashboardRepository = $dashboardRepository;
    }

    /**
     * Get all aggregated dashboard data for the general view.
     *
     * @return array
     */
    public function getDashboardData(): array
    {
        return [
            'stats' => $this->dashboardRepository->getSystemKpis(),
            'stagesList' => $this->dashboardRepository->getAcademicStagesDistribution(),
            'departmentsList' => $this->dashboardRepository->getDepartmentsDistribution(),
            'recentActivities' => $this->dashboardRepository->getRecentActivities(6),
            'chartData' => $this->dashboardRepository->getChartData(),
        ];
    }
}
