<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\GeneralDashboardRepositoryInterface;
use Illuminate\Support\Collection;
use Modules\Academic\Models\AcademicClass;
use Modules\Academic\Models\AcademicFee;
use Modules\Academic\Models\AcademicFeePayment;
use Modules\Academic\Models\AcademicFeeType;
use Modules\Academic\Models\AcademicGuardian;
use Modules\Academic\Models\AcademicLesson;
use Modules\Academic\Models\AcademicMonthlyPlan;
use Modules\Academic\Models\AcademicStage;
use Modules\Academic\Models\AcademicStudent;
use Modules\Academic\Models\AcademicStudentFee;
use Modules\Academic\Models\AcademicSubject;
use Modules\Academic\Models\AcademicWeeklyPlan;
use Modules\Academic\Models\AcademicYear;
use Modules\Academic\Models\Event;
use Modules\Academic\Models\MeetingCalendar;
use Modules\Accounting\Models\Account;
use Modules\Accounting\Models\CashAccount;
use Modules\Accounting\Models\CostCenter;
use Modules\Accounting\Models\FiscalYear;
use Modules\Accounting\Models\Journal;
use Modules\AuditLog\Models\ActivityLog;
use Modules\HR\Models\Attendance;
use Modules\HR\Models\Department;
use Modules\HR\Models\Employee;
use Modules\HR\Models\JobOpening;
use Modules\HR\Models\JobTitle;
use Modules\HR\Models\LeaveRequest;
use Modules\HR\Models\Loan;
use Modules\HR\Models\Payroll;
use Modules\Inventory\Models\ItemCategory;
use Modules\Inventory\Models\Unit;
use Modules\Inventory\Models\Warehouse;
use Modules\Nursery\Models\Nursery;
use Modules\Nursery\Models\NurseryBranch;
use Spatie\Permission\Models\Role;

class GeneralDashboardRepository implements GeneralDashboardRepositoryInterface
{
    /**
     * Get system-wide core KPIs covering all ERP sectors.
     *
     * @return array
     */
    public function getSystemKpis(): array
    {
        // 1. Academic Sector
        $studentsCount = class_exists(AcademicStudent::class) ? AcademicStudent::count() : 0;
        $classesCount = class_exists(AcademicClass::class) ? AcademicClass::count() : 0;
        $stagesCount = class_exists(AcademicStage::class) ? AcademicStage::count() : 0;
        $subjectsCount = class_exists(AcademicSubject::class) ? AcademicSubject::count() : 0;
        $lessonsCount = class_exists(AcademicLesson::class) ? AcademicLesson::count() : 0;
        $guardiansCount = class_exists(AcademicGuardian::class) ? AcademicGuardian::count() : 0;
        $monthlyPlansCount = class_exists(AcademicMonthlyPlan::class) ? AcademicMonthlyPlan::count() : 0;
        $weeklyPlansCount = class_exists(AcademicWeeklyPlan::class) ? AcademicWeeklyPlan::count() : 0;
        $eventsCount = (class_exists(Event::class) ? Event::count() : 0) + (class_exists(MeetingCalendar::class) ? MeetingCalendar::count() : 0);

        // 2. HR & Workforce Sector
        $employeesCount = class_exists(Employee::class) ? Employee::count() : 0;
        $departmentsCount = class_exists(Department::class) ? Department::count() : 0;
        $jobTitlesCount = class_exists(JobTitle::class) ? JobTitle::count() : 0;
        $attendancesCount = class_exists(Attendance::class) ? Attendance::count() : 0;
        $leaveRequestsCount = class_exists(LeaveRequest::class) ? LeaveRequest::count() : 0;
        $loansCount = class_exists(Loan::class) ? Loan::count() : 0;
        $jobOpeningsCount = class_exists(JobOpening::class) ? JobOpening::count() : 0;
        $payrollsCount = class_exists(Payroll::class) ? Payroll::count() : 0;

        // 3. Financial & Accounting Sector
        $totalFees = class_exists(AcademicStudentFee::class) ? (float) AcademicStudentFee::sum('total_amount') : 0;
        $collectedFees = class_exists(AcademicFeePayment::class) ? (float) AcademicFeePayment::where('status', 'paid')->sum('amount') : 0;
        $collectionRate = $totalFees > 0 ? round(($collectedFees / $totalFees) * 100, 1) : 0;
        $accountsCount = class_exists(Account::class) ? Account::count() : 0;
        $journalsCount = class_exists(Journal::class) ? Journal::count() : 0;
        $cashAccountsCount = class_exists(CashAccount::class) ? CashAccount::count() : 0;
        $costCentersCount = class_exists(CostCenter::class) ? CostCenter::count() : 0;
        $fiscalYearsCount = class_exists(FiscalYear::class) ? FiscalYear::count() : 0;

        // 4. Inventory & Warehouses
        $warehousesCount = class_exists(Warehouse::class) ? Warehouse::count() : 0;
        $itemCategoriesCount = class_exists(ItemCategory::class) ? ItemCategory::count() : 0;
        $unitsCount = class_exists(Unit::class) ? Unit::count() : 0;

        // 5. Nurseries & Branches
        $branchesCount = class_exists(NurseryBranch::class) ? NurseryBranch::count() : 0;
        $nurseriesCount = class_exists(Nursery::class) ? Nursery::count() : 0;

        // 6. Security, Users & System Audit
        $usersCount = class_exists(User::class) ? User::count() : 0;
        $rolesCount = class_exists(Role::class) ? Role::count() : 0;
        $activityLogsCount = class_exists(ActivityLog::class) ? ActivityLog::count() : 0;

        return [
            // Academic
            'students' => $studentsCount,
            'classes' => $classesCount,
            'stages' => $stagesCount,
            'subjects' => $subjectsCount,
            'lessons' => $lessonsCount,
            'guardians' => $guardiansCount,
            'monthly_plans' => $monthlyPlansCount,
            'weekly_plans' => $weeklyPlansCount,
            'events' => $eventsCount,

            // HR
            'employees' => $employeesCount,
            'departments' => $departmentsCount,
            'job_titles' => $jobTitlesCount,
            'attendances' => $attendancesCount,
            'leave_requests' => $leaveRequestsCount,
            'loans' => $loansCount,
            'job_openings' => $jobOpeningsCount,
            'payrolls' => $payrollsCount,

            // Financial
            'total_fees' => $totalFees,
            'collected_fees' => $collectedFees,
            'remaining_fees' => max(0, $totalFees - $collectedFees),
            'collection_rate' => $collectionRate,
            'accounts' => $accountsCount,
            'journals' => $journalsCount,
            'cash_accounts' => $cashAccountsCount,
            'cost_centers' => $costCentersCount,
            'fiscal_years' => $fiscalYearsCount,

            // Inventory & Facilities
            'warehouses' => $warehousesCount,
            'categories' => $itemCategoriesCount,
            'units' => $unitsCount,
            'branches' => $branchesCount,
            'nurseries' => $nurseriesCount,

            // Security & Operations
            'users' => $usersCount,
            'roles' => $rolesCount,
            'activity_logs' => $activityLogsCount,
        ];
    }

    /**
     * Get academic stages with classes count.
     *
     * @return Collection
     */
    public function getAcademicStagesDistribution(): Collection
    {
        if (! class_exists(AcademicStage::class)) {
            return collect();
        }

        return AcademicStage::withCount('classes')->get();
    }

    /**
     * Get HR departments with employees count.
     *
     * @param int $limit
     * @return Collection
     */
    public function getDepartmentsDistribution(int $limit = 6): Collection
    {
        if (! class_exists(Department::class)) {
            return collect();
        }

        return Department::with('translations')
            ->withCount('employees')
            ->orderByDesc('employees_count')
            ->take($limit)
            ->get();
    }

    /**
     * Get recent system activity logs.
     *
     * @param int $limit
     * @return Collection
     */
    public function getRecentActivities(int $limit = 6): Collection
    {
        if (! class_exists(ActivityLog::class)) {
            return collect();
        }

        try {
            return ActivityLog::with('causer')
                ->latest()
                ->take($limit)
                ->get();
        } catch (\Throwable $e) {
            return collect();
        }
    }

    /**
     * Get formatted charts data.
     *
     * @return array
     */
    public function getChartData(): array
    {
        $stages = $this->getAcademicStagesDistribution();
        $departments = $this->getDepartmentsDistribution(6);

        // Fee types distribution
        $feeTypes = class_exists(AcademicFeeType::class)
            ? AcademicFeeType::withCount('fees')->take(6)->get()
            : collect();

        // System core entities distribution for Operational Pulse chart
        $systemDistribution = [
            'labels' => [
                __('Chart of Accounts'),
                __('Staff & Employees'),
                __('System Users'),
                __('Classes'),
                __('Categories'),
                __('Departments')
            ],
            'series' => [
                class_exists(Account::class) ? Account::count() : 0,
                class_exists(Employee::class) ? Employee::count() : 0,
                class_exists(User::class) ? User::count() : 0,
                class_exists(AcademicClass::class) ? AcademicClass::count() : 0,
                class_exists(ItemCategory::class) ? ItemCategory::count() : 0,
                class_exists(Department::class) ? Department::count() : 0,
            ]
        ];

        return [
            'stages_chart' => [
                'labels' => $stages->map(fn($s) => $s->name ?? __('Stage'))->toArray(),
                'series' => $stages->pluck('classes_count')->toArray(),
            ],
            'departments_chart' => [
                'labels' => $departments->map(fn($d) => $d->name ?? __('Department'))->toArray(),
                'series' => $departments->pluck('employees_count')->toArray(),
            ],
            'fee_types_chart' => [
                'labels' => $feeTypes->map(fn($f) => $f->name ?? __('Fee Type'))->toArray(),
                'series' => $feeTypes->pluck('fees_count')->toArray(),
            ],
            'system_distribution_chart' => $systemDistribution,
        ];
    }
}
