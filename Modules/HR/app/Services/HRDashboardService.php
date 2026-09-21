<?php

namespace Modules\HR\Services;

use Modules\HR\Repositories\Contracts\HRDashboardRepositoryInterface;
use Modules\Settings\Models\Setting;

class HRDashboardService
{
    public function __construct(
        protected HRDashboardRepositoryInterface $repository
    ) {}

    /**
     * Compile and format all dashboard data for presentation.
     */
    public function getDashboardData(): array
    {
        $systemSetting = Setting::instance();
        $settingCurrency = $systemSetting?->currency_symbol ?: ($systemSetting?->currency ? __($systemSetting->currency) : 'د.ك');

        // 1. Workforce
        $workforce = $this->repository->getWorkforceMetrics();
        $totalEmployees = $workforce['total_employees'];
        $maleRatio = $totalEmployees > 0 ? round(($workforce['male_employees'] / $totalEmployees) * 100) : 50;
        $femaleRatio = $totalEmployees > 0 ? (100 - $maleRatio) : 50;

        // 2. Recruitment & Funnel
        $recruitment = $this->repository->getRecruitmentMetrics();
        $funnel = $this->repository->getFunnelMetrics();
        $funnelData = [
            'labels' => [
                __('New Applications'),
                __('Screening'),
                __('Interviews'),
                __('Job Offers'),
                __('Hired'),
            ],
            'series' => array_values($funnel),
        ];

        // 3. Offers & Onboarding
        $offersAndOnboarding = $this->repository->getOffersAndOnboardingMetrics();

        // 4. Attendance & Leaves
        $attendance = $this->repository->getAttendanceAndLeaveMetrics();
        $attendanceRate = $totalEmployees > 0
            ? round(($attendance['today_present'] / $totalEmployees) * 100, 1)
            : 0;

        // 5. Financials
        $financials = $this->repository->getFinancialMetrics();

        // 6. Pending Actions Center
        $pendingCounts = $this->repository->getPendingActionsSummary();
        $totalPendingActions = array_sum($pendingCounts);

        $pendingActions = [
            'requisitions' => [
                'count' => $pendingCounts['pending_requisitions'],
                'label' => __('Pending Job Requisitions'),
                'icon'  => 'bx-briefcase',
                'route' => route('hr.job-requisitions.index'),
                'badge' => 'bg-label-warning',
            ],
            'offers' => [
                'count' => $pendingCounts['pending_offers'],
                'label' => __('Offers Awaiting Approval'),
                'icon'  => 'bx-gift',
                'route' => route('hr.job-offers.index'),
                'badge' => 'bg-label-info',
            ],
            'leaves' => [
                'count' => $pendingCounts['pending_leaves'],
                'label' => __('Pending Leave Requests'),
                'icon'  => 'bx-calendar-exclamation',
                'route' => route('hr.leaves.index'),
                'badge' => 'bg-label-primary',
            ],
            'loans' => [
                'count' => $pendingCounts['pending_loans'],
                'label' => __('Pending Loan Applications'),
                'icon'  => 'bx-wallet',
                'route' => route('hr.loans.index'),
                'badge' => 'bg-label-success',
            ],
            'permissions' => [
                'count' => $pendingCounts['pending_permissions'],
                'label' => __('Pending Permission Requests'),
                'icon'  => 'bx-time-five',
                'route' => route('hr.permissions.index'),
                'badge' => 'bg-label-secondary',
            ],
        ];

        // 7. Department Distribution Chart Data
        $deptDistribution = $this->repository->getDepartmentDistribution();

        // 8. Requisition Metrics & Recent Requisitions
        $requisitionMetrics = $this->repository->getJobRequisitionMetrics();
        $recentRequisitions = $this->repository->getRecentJobRequisitions(5);

        // 9. Operational Lists & Collections
        $upcomingInterviews    = $this->repository->getUpcomingInterviews(5);
        $recentOnboardings     = $this->repository->getRecentOnboardings(5);
        $topOpenings           = $this->repository->getTopJobOpenings(5);
        $employeesOnLeaveToday = $this->repository->getEmployeesOnLeaveToday();
        $recentAttendanceLogs  = $this->repository->getRecentAttendanceLogs(5);
        $pendingLeaves         = $this->repository->getPendingLeaveRequests(5);
        $pendingPermissions    = $this->repository->getPendingPermissionRequests(5);
        $pendingLoans          = $this->repository->getPendingLoanRequests(5);

        return compact(
            'settingCurrency',
            'workforce',
            'maleRatio',
            'femaleRatio',
            'recruitment',
            'funnel',
            'funnelData',
            'offersAndOnboarding',
            'attendance',
            'attendanceRate',
            'financials',
            'totalPendingActions',
            'pendingActions',
            'deptDistribution',
            'requisitionMetrics',
            'recentRequisitions',
            'upcomingInterviews',
            'recentOnboardings',
            'topOpenings',
            'employeesOnLeaveToday',
            'recentAttendanceLogs',
            'pendingLeaves',
            'pendingPermissions',
            'pendingLoans'
        );
    }
}
