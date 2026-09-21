<?php

namespace Modules\HR\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface HRDashboardRepositoryInterface
{
    /**
     * Get aggregated workforce metrics (total, active, probation, leave, gender).
     */
    public function getWorkforceMetrics(): array;

    /**
     * Get recruitment & candidate pipeline metrics.
     */
    public function getRecruitmentMetrics(): array;

    /**
     * Get candidate funnel counts by stage.
     */
    public function getFunnelMetrics(): array;

    /**
     * Get job offers and onboarding journey metrics.
     */
    public function getOffersAndOnboardingMetrics(): array;

    /**
     * Get today's attendance and active leaves.
     */
    public function getAttendanceAndLeaveMetrics(): array;

    /**
     * Get payroll and financial commitment metrics.
     */
    public function getFinancialMetrics(): array;

    /**
     * Get pending items counts requiring management action.
     */
    public function getPendingActionsSummary(): array;

    /**
     * Get employee distribution across departments.
     */
    public function getDepartmentDistribution(): array;

    /**
     * Get job requisitions metrics by status.
     */
    public function getJobRequisitionMetrics(): array;

    /**
     * Get recent job requisitions with department and position.
     */
    public function getRecentJobRequisitions(int $limit = 5): Collection;

    /**
     * Get upcoming or recent scheduled interviews.
     */
    public function getUpcomingInterviews(int $limit = 5): Collection;

    /**
     * Get active onboarding journeys.
     */
    public function getRecentOnboardings(int $limit = 5): Collection;

    /**
     * Get top active job openings with applicant counts.
     */
    public function getTopJobOpenings(int $limit = 5): Collection;

    /**
     * Get employees currently on leave today.
     */
    public function getEmployeesOnLeaveToday(): Collection;

    /**
     * Get recent attendance records.
     */
    public function getRecentAttendanceLogs(int $limit = 5): Collection;

    /**
     * Get pending leave requests awaiting approval.
     */
    public function getPendingLeaveRequests(int $limit = 5): Collection;

    /**
     * Get pending permission requests awaiting approval.
     */
    public function getPendingPermissionRequests(int $limit = 5): Collection;

    /**
     * Get pending loan requests awaiting approval.
     */
    public function getPendingLoanRequests(int $limit = 5): Collection;
}
