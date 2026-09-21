<?php

namespace Modules\HR\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Modules\HR\Enums\EmployeeStatus;
use Modules\HR\Enums\JobOfferStatus;
use Modules\HR\Enums\OnboardingStatus;
use Modules\HR\Models\Attendance;
use Modules\HR\Models\Candidate;
use Modules\HR\Models\Department;
use Modules\HR\Models\Employee;
use Modules\HR\Models\Interview;
use Modules\HR\Models\JobApplication;
use Modules\HR\Models\JobOffer;
use Modules\HR\Models\JobOpening;
use Modules\HR\Models\JobRequisition;
use Modules\HR\Models\JobTitle;
use Modules\HR\Models\LeaveRequest;
use Modules\HR\Models\Loan;
use Modules\HR\Models\Onboarding;
use Modules\HR\Models\Payroll;
use Modules\HR\Models\PermissionRequest;
use Modules\HR\Repositories\Contracts\HRDashboardRepositoryInterface;

class HRDashboardRepository implements HRDashboardRepositoryInterface
{
    /**
     * Get aggregated workforce metrics (total, active, probation, leave, gender).
     */
    public function getWorkforceMetrics(): array
    {
        return [
            'total_employees'     => Employee::count(),
            'active_employees'    => Employee::where('status', EmployeeStatus::ACTIVE)->count(),
            'probation_employees' => Employee::where('status', EmployeeStatus::PROBATION)->count(),
            'on_leave_employees'  => Employee::where('status', EmployeeStatus::ON_LEAVE)->count(),
            'male_employees'      => Employee::where('gender', 'male')->count(),
            'female_employees'    => Employee::where('gender', 'female')->count(),
            'departments_count'   => Department::count(),
            'job_titles_count'    => JobTitle::count(),
        ];
    }

    /**
     * Get recruitment & candidate pipeline metrics.
     */
    public function getRecruitmentMetrics(): array
    {
        return [
            'active_openings'    => JobOpening::where('status', 'published')->count(),
            'total_openings'     => JobOpening::count(),
            'total_candidates'   => Candidate::count(),
            'total_applications' => JobApplication::count(),
        ];
    }

    /**
     * Get candidate funnel counts by stage.
     */
    public function getFunnelMetrics(): array
    {
        $rawCounts = JobApplication::groupBy('stage')
            ->selectRaw('stage, count(*) as count')
            ->pluck('count', 'stage')
            ->toArray();

        return [
            'new'       => $rawCounts['new'] ?? 0,
            'screening' => $rawCounts['screening'] ?? 0,
            'interview' => $rawCounts['interview'] ?? 0,
            'offer'     => $rawCounts['offer'] ?? 0,
            'hired'     => $rawCounts['hired'] ?? 0,
        ];
    }

    /**
     * Get job offers and onboarding journey metrics.
     */
    public function getOffersAndOnboardingMetrics(): array
    {
        return [
            'total_offers'          => JobOffer::count(),
            'pending_offers'        => JobOffer::where('status', JobOfferStatus::PENDING_APPROVAL)->count(),
            'accepted_offers'       => JobOffer::where('status', JobOfferStatus::ACCEPTED)->count(),
            'active_onboardings'    => Onboarding::whereIn('status', [OnboardingStatus::PENDING, OnboardingStatus::IN_PROGRESS])->count(),
            'completed_onboardings' => Onboarding::where('status', OnboardingStatus::COMPLETED)->count(),
        ];
    }

    /**
     * Get today's attendance and active leaves.
     */
    public function getAttendanceAndLeaveMetrics(): array
    {
        $today = Carbon::today();
        $totalEmployees = Employee::count();
        $todayPresent = Attendance::whereDate('date', $today)->where('status', 'present')->count();
        $todayLate = Attendance::whereDate('date', $today)->where('late_minutes', '>', 0)->count();
        $todayAbsent = max(0, $totalEmployees - $todayPresent);

        $onLeaveTodayCount = LeaveRequest::where('status', 'approved')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->count();

        return [
            'today_attendances'   => Attendance::whereDate('date', $today)->count(),
            'today_present'       => $todayPresent,
            'today_late'          => $todayLate,
            'today_absent'        => $todayAbsent,
            'on_leave_today_count'=> $onLeaveTodayCount,
        ];
    }

    /**
     * Get payroll and financial commitment metrics.
     */
    public function getFinancialMetrics(): array
    {
        $latestPayroll = Payroll::latest('id')->first();

        return [
            'latest_payroll'        => $latestPayroll,
            'monthly_payroll_net'   => $latestPayroll ? (float) $latestPayroll->total_net : 0,
            'active_loans_balance'  => (float) Loan::whereIn('status', ['approved', 'disbursed'])->sum('remaining_amount'),
        ];
    }

    /**
     * Get pending items counts requiring management action.
     */
    public function getPendingActionsSummary(): array
    {
        return [
            'pending_requisitions' => JobRequisition::where('status', 'pending_approval')->count(),
            'pending_offers'       => JobOffer::where('status', JobOfferStatus::PENDING_APPROVAL)->count(),
            'pending_leaves'       => LeaveRequest::where('status', 'pending')->count(),
            'pending_loans'        => Loan::where('status', 'pending')->count(),
            'pending_permissions'  => PermissionRequest::where('status', 'pending')->count(),
        ];
    }

    /**
     * Get employee distribution across departments.
     */
    public function getDepartmentDistribution(): array
    {
        $departments = Department::withCount('employees')
            ->get()
            ->filter(fn($d) => $d->employees_count > 0)
            ->values();

        if ($departments->isEmpty()) {
            return [
                'labels' => [__('General Administration'), __('Human Resources'), __('Operations')],
                'series' => [4, 3, 2],
            ];
        }

        return [
            'labels' => $departments->pluck('name')->toArray(),
            'series' => $departments->pluck('employees_count')->toArray(),
        ];
    }

    /**
     * Get job requisitions metrics by status.
     */
    public function getJobRequisitionMetrics(): array
    {
        return [
            'total'             => JobRequisition::count(),
            'pending_approval'  => JobRequisition::where('status', 'pending_approval')->count(),
            'approved'          => JobRequisition::where('status', 'approved')->count(),
            'in_recruitment'    => JobRequisition::where('status', 'in_recruitment')->count(),
            'fulfilled'         => JobRequisition::where('status', 'fulfilled')->count(),
            'total_headcount'   => (int) JobRequisition::whereIn('status', ['approved', 'in_recruitment'])->sum('headcount_needed'),
            'total_hired'       => (int) JobRequisition::whereIn('status', ['approved', 'in_recruitment', 'fulfilled'])->sum('hired_count'),
        ];
    }

    /**
     * Get recent job requisitions with department and position.
     */
    public function getRecentJobRequisitions(int $limit = 5): Collection
    {
        return JobRequisition::with([
                'department.translations',
                'jobTitle.translations',
                'requester'
            ])
            ->latest('id')
            ->limit($limit)
            ->get();
    }

    /**
     * Get upcoming or recent scheduled interviews.
     */
    public function getUpcomingInterviews(int $limit = 5): Collection
    {
        return Interview::with(['candidate', 'jobOpening.translations'])
            ->latest('scheduled_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get active onboarding journeys.
     */
    public function getRecentOnboardings(int $limit = 5): Collection
    {
        return Onboarding::with(['candidate', 'jobTitle.translations', 'tasks'])
            ->latest('id')
            ->limit($limit)
            ->get();
    }

    /**
     * Get top active job openings with applicant counts.
     */
    public function getTopJobOpenings(int $limit = 5): Collection
    {
        return JobOpening::with(['department.translations'])
            ->withCount('applications')
            ->latest('id')
            ->limit($limit)
            ->get();
    }

    /**
     * Get employees currently on leave today.
     */
    public function getEmployeesOnLeaveToday(): Collection
    {
        $today = Carbon::today();
        return LeaveRequest::with(['employee.translations', 'employee.department.translations'])
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->limit(5)
            ->get();
    }

    /**
     * Get recent attendance records.
     */
    public function getRecentAttendanceLogs(int $limit = 5): Collection
    {
        return Attendance::with(['employee.translations', 'employee.department.translations', 'shift'])
            ->latest('date')
            ->latest('id')
            ->limit($limit)
            ->get();
    }

    /**
     * Get pending leave requests awaiting approval.
     */
    public function getPendingLeaveRequests(int $limit = 5): Collection
    {
        return LeaveRequest::with(['employee.translations', 'employee.department.translations'])
            ->where('status', 'pending')
            ->latest('id')
            ->limit($limit)
            ->get();
    }

    /**
     * Get pending permission requests awaiting approval.
     */
    public function getPendingPermissionRequests(int $limit = 5): Collection
    {
        return PermissionRequest::with(['employee.translations', 'employee.department.translations'])
            ->where('status', 'pending')
            ->latest('id')
            ->limit($limit)
            ->get();
    }

    /**
     * Get pending loan requests awaiting approval.
     */
    public function getPendingLoanRequests(int $limit = 5): Collection
    {
        return Loan::with(['employee.translations', 'employee.department.translations'])
            ->where('status', 'pending')
            ->latest('id')
            ->limit($limit)
            ->get();
    }
}
