<?php

namespace Modules\HR\Repositories;

use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection as BaseCollection;
use Modules\HR\Enums\PayrollPaymentStatus;
use Modules\HR\Models\Employee;
use Modules\HR\Models\HrWorkPolicy;
use Modules\HR\Models\Loan;
use Modules\HR\Models\Payroll;
use Modules\HR\Models\PayrollItem;
use Modules\HR\Models\Penalty;
use Modules\HR\Models\Reward;
use Modules\HR\Repositories\Contracts\PayrollReportRepositoryInterface;

class PayrollReportRepository implements PayrollReportRepositoryInterface
{
    /**
     * Get query builder for Payroll Items (Monthly Payroll Sheet).
     *
     * @param array<string, mixed> $filters
     * @return Builder
     */
    public function getPayrollSheetQuery(array $filters = []): Builder
    {
        $query = PayrollItem::with([
            'payroll',
            'employee.translations',
            'employee.department.translations',
            'employee.primaryBranch.translations',
            'employee.jobTitle.translations',
        ]);

        // 1. Filter by specific payroll run or payroll month
        if (!empty($filters['payroll_id'])) {
            $query->where('payroll_id', $filters['payroll_id']);
        } elseif (!empty($filters['payroll_month'])) {
            $query->whereHas('payroll', function ($q) use ($filters) {
                $q->where('payroll_month', $filters['payroll_month']);
            });
        }

        // 2. Department Filter
        if (!empty($filters['department_id'])) {
            $query->whereHas('employee', function ($q) use ($filters) {
                $q->where('department_id', $filters['department_id']);
            });
        }

        // 3. Branch Filter
        if (!empty($filters['branch_id'])) {
            $query->whereHas('employee', function ($q) use ($filters) {
                $q->where('primary_branch_id', $filters['branch_id']);
            });
        }

        // 4. Employee Filter
        if (!empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        // 5. Payment Status Filter
        if (!empty($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        return $query->orderBy('payroll_id', 'desc')->orderBy('id', 'asc');
    }

    /**
     * Get paginated Payroll Items for Monthly Payroll Sheet.
     *
     * @param array<string, mixed> $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPayrollSheetPaginated(array $filters = [], int $perPage = 25): LengthAwarePaginator
    {
        return $this->getPayrollSheetQuery($filters)->paginate($perPage);
    }

    /**
     * Get all filtered Payroll Items without pagination.
     *
     * @param array<string, mixed> $filters
     * @return Collection
     */
    public function getPayrollSheetRecords(array $filters = []): Collection
    {
        return $this->getPayrollSheetQuery($filters)->get();
    }

    /**
     * Get summary KPI totals for the monthly payroll sheet.
     *
     * @param array<string, mixed> $filters
     * @return array<string, float>
     */
    public function getPayrollSheetKPIs(array $filters = []): array
    {
        $items = $this->getPayrollSheetRecords($filters);

        return [
            'total_employees'        => (int) $items->count(),
            'total_basic_salary'     => (float) $items->sum('basic_salary'),
            'total_allowances'       => (float) $items->sum('allowances_amount'),
            'total_overtime'         => (float) $items->sum('overtime_amount'),
            'total_rewards'          => (float) $items->sum('rewards_amount'),
            'total_gross_salary'     => (float) $items->sum('gross_salary'),
            'total_late_deduction'   => (float) $items->sum('late_deduction'),
            'total_absence_deduction'=> (float) $items->sum('absence_deduction'),
            'total_loans_deduction'  => (float) $items->sum('loans_deduction'),
            'total_penalties_deduct' => (float) $items->sum('penalties_deduction'),
            'total_insurance_deduct' => (float) $items->sum('insurance_deduction'),
            'total_deductions'       => (float) $items->sum('total_deductions'),
            'total_net_salary'       => (float) $items->sum('net_salary'),
        ];
    }

    /**
     * Get query builder for Loans & Installments.
     *
     * @param array<string, mixed> $filters
     * @return Builder
     */
    public function getLoansQuery(array $filters = []): Builder
    {
        $query = Loan::with([
            'employee.translations',
            'employee.department.translations',
            'employee.primaryBranch.translations',
            'installments',
        ]);

        // 1. Status Filter
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // 2. Type Filter
        if (!empty($filters['loan_type'])) {
            $query->where('type', $filters['loan_type']);
        }

        // 3. Department Filter
        if (!empty($filters['department_id'])) {
            $query->whereHas('employee', function ($q) use ($filters) {
                $q->where('department_id', $filters['department_id']);
            });
        }

        // 4. Branch Filter
        if (!empty($filters['branch_id'])) {
            $query->whereHas('employee', function ($q) use ($filters) {
                $q->where('primary_branch_id', $filters['branch_id']);
            });
        }

        // 5. Employee Filter
        if (!empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        // 6. Date Range Filter
        $startDate = $filters['start_date'] ?? null;
        $endDate   = $filters['end_date'] ?? null;
        if ($startDate && $endDate) {
            $query->whereBetween('start_date', [$startDate, $endDate]);
        } elseif ($startDate) {
            $query->whereDate('start_date', '>=', $startDate);
        } elseif ($endDate) {
            $query->whereDate('start_date', '<=', $endDate);
        }

        return $query->orderBy('start_date', 'desc')->orderBy('id', 'desc');
    }

    /**
     * Get paginated Loans.
     *
     * @param array<string, mixed> $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getLoansPaginated(array $filters = [], int $perPage = 25): LengthAwarePaginator
    {
        return $this->getLoansQuery($filters)->paginate($perPage);
    }

    /**
     * Get all filtered Loans without pagination.
     *
     * @param array<string, mixed> $filters
     * @return Collection
     */
    public function getLoansRecords(array $filters = []): Collection
    {
        return $this->getLoansQuery($filters)->get();
    }

    /**
     * Get summary KPI totals for Loans.
     *
     * @param array<string, mixed> $filters
     * @return array<string, float>
     */
    public function getLoansKPIs(array $filters = []): array
    {
        $loans = $this->getLoansRecords($filters);

        return [
            'total_loans_count'     => (int) $loans->count(),
            'total_amount'          => (float) $loans->sum('amount'),
            'total_paid'            => (float) $loans->sum('total_paid'),
            'total_remaining'       => (float) $loans->sum('remaining_amount'),
            'total_monthly_burden'  => (float) $loans->where('status.value', 'active')->sum('monthly_installment'),
        ];
    }

    /**
     * Get all filtered Rewards & Penalties records without pagination.
     *
     * @param array<string, mixed> $filters
     * @return BaseCollection
     */
    public function getRewardsPenaltiesRecords(array $filters = []): BaseCollection
    {
        $itemType = $filters['item_type'] ?? 'all';
        $items = collect();

        // 1. Fetch Rewards if not filtered to penalties only
        if ($itemType === 'all' || $itemType === 'reward') {
            $rewardQuery = Reward::with([
                'employee.translations',
                'employee.department.translations',
                'employee.primaryBranch.translations',
                'rewardType.translations',
                'approver',
            ]);

            if (!empty($filters['department_id'])) {
                $rewardQuery->whereHas('employee', fn($q) => $q->where('department_id', $filters['department_id']));
            }
            if (!empty($filters['branch_id'])) {
                $rewardQuery->whereHas('employee', fn($q) => $q->where('primary_branch_id', $filters['branch_id']));
            }
            if (!empty($filters['employee_id'])) {
                $rewardQuery->where('employee_id', $filters['employee_id']);
            }
            if (!empty($filters['status'])) {
                $rewardQuery->where('status', $filters['status']);
            }
            if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
                $rewardQuery->whereBetween('award_date', [$filters['start_date'], $filters['end_date']]);
            } elseif (!empty($filters['start_date'])) {
                $rewardQuery->whereDate('award_date', '>=', $filters['start_date']);
            } elseif (!empty($filters['end_date'])) {
                $rewardQuery->whereDate('award_date', '<=', $filters['end_date']);
            }

            $rewards = $rewardQuery->get()->map(function ($r) {
                return (object) [
                    'id'               => $r->id,
                    'reference_number' => $r->reward_number,
                    'item_kind'        => 'reward',
                    'item_kind_label'  => __('مكافأة / حافز'),
                    'employee'         => $r->employee,
                    'date'             => $r->award_date,
                    'amount'           => (float) $r->amount,
                    'type_name'        => $r->rewardType?->name ?? __('مكافأة عامة'),
                    'reason'           => $r->reason,
                    'status'           => $r->status,
                    'approved_by'      => $r->approver?->name ?? '-',
                    'created_at'       => $r->created_at,
                ];
            });

            $items = $items->concat($rewards);
        }

        // 2. Fetch Penalties if not filtered to rewards only
        if ($itemType === 'all' || $itemType === 'penalty') {
            $penaltyQuery = Penalty::with([
                'employee.translations',
                'employee.department.translations',
                'employee.primaryBranch.translations',
                'penaltyType.translations',
                'approver',
            ]);

            if (!empty($filters['department_id'])) {
                $penaltyQuery->whereHas('employee', fn($q) => $q->where('department_id', $filters['department_id']));
            }
            if (!empty($filters['branch_id'])) {
                $penaltyQuery->whereHas('employee', fn($q) => $q->where('primary_branch_id', $filters['branch_id']));
            }
            if (!empty($filters['employee_id'])) {
                $penaltyQuery->where('employee_id', $filters['employee_id']);
            }
            if (!empty($filters['status'])) {
                $penaltyQuery->where('status', $filters['status']);
            }
            if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
                $penaltyQuery->whereBetween('penalty_date', [$filters['start_date'], $filters['end_date']]);
            } elseif (!empty($filters['start_date'])) {
                $penaltyQuery->whereDate('penalty_date', '>=', $filters['start_date']);
            } elseif (!empty($filters['end_date'])) {
                $penaltyQuery->whereDate('penalty_date', '<=', $filters['end_date']);
            }

            $penalties = $penaltyQuery->get()->map(function ($p) {
                return (object) [
                    'id'               => $p->id,
                    'reference_number' => $p->penalty_number,
                    'item_kind'        => 'penalty',
                    'item_kind_label'  => __('جزاء / مخالفة'),
                    'employee'         => $p->employee,
                    'date'             => $p->penalty_date,
                    'amount'           => (float) $p->amount,
                    'type_name'        => $p->penaltyType?->name ?? __('جزاء إداري'),
                    'reason'           => $p->reason,
                    'status'           => $p->status,
                    'approved_by'      => $p->approver?->name ?? '-',
                    'created_at'       => $p->created_at,
                ];
            });

            $items = $items->concat($penalties);
        }

        return $items->sortByDesc(fn($i) => Carbon::parse($i->date ?? $i->created_at)->timestamp)->values();
    }

    /**
     * Get paginated Rewards & Penalties combined log.
     *
     * @param array<string, mixed> $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getRewardsPenaltiesPaginated(array $filters = [], int $perPage = 25): LengthAwarePaginator
    {
        $all = $this->getRewardsPenaltiesRecords($filters);
        $page = Paginator::resolveCurrentPage() ?: 1;
        $sliced = $all->slice(($page - 1) * $perPage, $perPage)->values();

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $sliced,
            $all->count(),
            $perPage,
            $page,
            ['path' => Paginator::resolveCurrentPath(), 'query' => request()->query()]
        );
    }

    /**
     * Get summary KPI totals for Rewards & Penalties.
     *
     * @param array<string, mixed> $filters
     * @return array<string, float>
     */
    public function getRewardsPenaltiesKPIs(array $filters = []): array
    {
        $all = $this->getRewardsPenaltiesRecords($filters);

        $rewards = $all->filter(fn($i) => $i->item_kind === 'reward');
        $penalties = $all->filter(fn($i) => $i->item_kind === 'penalty');

        return [
            'total_items_count'      => (int) $all->count(),
            'total_rewards_count'    => (int) $rewards->count(),
            'total_rewards_amount'   => (float) $rewards->sum('amount'),
            'total_penalties_count'  => (int) $penalties->count(),
            'total_penalties_amount' => (float) $penalties->sum('amount'),
            'net_impact'             => (float) ($rewards->sum('amount') - $penalties->sum('amount')),
        ];
    }

    /**
     * Get all End of Service (EOS) provisions calculation records.
     *
     * @param array<string, mixed> $filters
     * @return BaseCollection
     */
    public function getEosProvisionsRecords(array $filters = []): BaseCollection
    {
        $empQuery = Employee::with([
            'translations',
            'department.translations',
            'primaryBranch.translations',
            'jobTitle.translations',
        ]);

        if (!empty($filters['department_id'])) {
            $empQuery->where('department_id', $filters['department_id']);
        }
        if (!empty($filters['branch_id'])) {
            $empQuery->where('primary_branch_id', $filters['branch_id']);
        }
        if (!empty($filters['employee_id'])) {
            $empQuery->where('id', $filters['employee_id']);
        }

        $employees = $empQuery->orderBy('id', 'asc')->get();
        $asOfDate = !empty($filters['as_of_date']) ? Carbon::parse($filters['as_of_date'])->endOfDay() : Carbon::now()->endOfDay();

        return $employees->map(function ($emp) use ($asOfDate) {
            $hireDate = $emp->hire_date ? Carbon::parse($emp->hire_date) : Carbon::parse($emp->created_at);

            // Ensure hire date is not after calculation date
            if ($hireDate->isAfter($asOfDate)) {
                $serviceYearsDecimal = 0.0;
                $years = 0;
                $months = 0;
                $days = 0;
            } else {
                $diff = $hireDate->diff($asOfDate);
                $years = $diff->y;
                $months = $diff->m;
                $days = $diff->d;
                $serviceYearsDecimal = round($years + ($months / 12) + ($days / 365.25), 2);
            }

            $monthlySalary = (float) ($emp->basic_salary ?? 0);

            // Labor Law Standard Provision Formula:
            // 1. First 5 years: Half a month's wage per year
            $tier1Years = min(5.0, max(0.0, $serviceYearsDecimal));
            $tier1Provision = round($tier1Years * ($monthlySalary * 0.5), 2);

            // 2. Beyond 5 years: Full month's wage per year
            $tier2Years = max(0.0, $serviceYearsDecimal - 5.0);
            $tier2Provision = round($tier2Years * $monthlySalary, 2);

            // Total Accrued Liability
            $totalEosProvision = round($tier1Provision + $tier2Provision, 2);

            return (object) [
                'employee'            => $emp,
                'employee_id'         => $emp->id,
                'hire_date'           => $hireDate->format('Y-m-d'),
                'as_of_date'          => $asOfDate->format('Y-m-d'),
                'service_years'       => $years,
                'service_months'      => $months,
                'service_days'        => $days,
                'service_duration_text' => "{$years} س / {$months} ش / {$days} ي",
                'service_years_decimal'=> $serviceYearsDecimal,
                'monthly_salary'      => $monthlySalary,
                'tier1_years'         => $tier1Years,
                'tier1_provision'     => $tier1Provision,
                'tier2_years'         => $tier2Years,
                'tier2_provision'     => $tier2Provision,
                'total_eos_provision' => $totalEosProvision,
            ];
        });
    }

    /**
     * Get paginated End of Service (EOS) provisions calculation records.
     *
     * @param array<string, mixed> $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getEosProvisionsPaginated(array $filters = [], int $perPage = 25): LengthAwarePaginator
    {
        $all = $this->getEosProvisionsRecords($filters);
        $page = Paginator::resolveCurrentPage() ?: 1;
        $sliced = $all->slice(($page - 1) * $perPage, $perPage)->values();

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $sliced,
            $all->count(),
            $perPage,
            $page,
            ['path' => Paginator::resolveCurrentPath(), 'query' => request()->query()]
        );
    }

    /**
     * Get summary KPI totals for End of Service (EOS) provisions.
     *
     * @param array<string, mixed> $filters
     * @return array<string, float>
     */
    public function getEosProvisionsKPIs(array $filters = []): array
    {
        $records = $this->getEosProvisionsRecords($filters);

        return [
            'total_employees'            => (int) $records->count(),
            'total_monthly_salaries'     => (float) $records->sum('monthly_salary'),
            'total_tier1_provisions'     => (float) $records->sum('tier1_provision'),
            'total_tier2_provisions'     => (float) $records->sum('tier2_provision'),
            'total_cumulative_provisions'=> (float) $records->sum('total_eos_provision'),
        ];
    }

    /**
     * Find a single PayrollItem by ID with its parent payroll and employee relations for payslip.
     *
     * @param int $itemId
     * @return mixed
     */
    public function findPayrollItemWithDetails(int $itemId): mixed
    {
        return PayrollItem::with([
            'payroll',
            'employee.translations',
            'employee.department.translations',
            'employee.primaryBranch.translations',
            'employee.jobTitle.translations',
            'employee.bank.translations',
        ])->find($itemId);
    }
}
