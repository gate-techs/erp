<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface GeneralDashboardRepositoryInterface
{
    /**
     * Get system-wide core KPIs.
     *
     * @return array
     */
    public function getSystemKpis(): array;

    /**
     * Get academic stages with classes count.
     *
     * @return Collection
     */
    public function getAcademicStagesDistribution(): Collection;

    /**
     * Get HR departments with employees count.
     *
     * @param int $limit
     * @return Collection
     */
    public function getDepartmentsDistribution(int $limit = 5): Collection;

    /**
     * Get recent system activity logs.
     *
     * @param int $limit
     * @return Collection
     */
    public function getRecentActivities(int $limit = 6): Collection;

    /**
     * Get formatted charts data.
     *
     * @return array
     */
    public function getChartData(): array;
}
