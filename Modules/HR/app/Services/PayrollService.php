<?php

namespace Modules\HR\Services;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Modules\HR\Enums\EmployeeStatus;
use Modules\HR\Enums\InstallmentStatus;
use Modules\HR\Enums\LoanStatus;
use Modules\HR\Enums\PayrollPaymentStatus;
use Modules\HR\Enums\PayrollStatus;
use Modules\HR\Enums\PenaltyDeductionMethod;
use Modules\HR\Enums\PenaltyStatus;
use Modules\HR\Enums\RewardPaymentMethod;
use Modules\HR\Enums\RewardStatus;
use Modules\HR\Models\Attendance;
use Modules\HR\Models\Employee;
use Modules\HR\Models\HrWorkPolicy;
use Modules\HR\Models\LoanInstallment;
use Modules\HR\Models\Payroll;
use Modules\HR\Models\PayrollItem;
use Modules\HR\Models\Penalty;
use Modules\HR\Models\Reward;
use Modules\HR\Repositories\Contracts\PayrollRepositoryInterface;

class PayrollService
{
    public function __construct(
        protected PayrollRepositoryInterface $payrollRepo
    ) {}

    /**
     * Generate a new monthly payroll run with automated 1-click calculations.
     *
     * @throws Exception
     */
    public function generatePayroll(string $month, ?int $branchId, ?string $title, ?string $notes, int $userId): Payroll
    {
        // 1. Check for duplicate active payroll run for the same month & branch
        $existsQuery = Payroll::where('payroll_month', $month)
            ->where('status', '!=', PayrollStatus::CANCELLED->value);

        if ($branchId) {
            $existsQuery->where('branch_id', $branchId);
        } else {
            $existsQuery->whereNull('branch_id');
        }

        if ($existsQuery->exists()) {
            throw new Exception(__('A payroll run for this month and branch scope already exists and is active.'));
        }

        return DB::transaction(function () use ($month, $branchId, $title, $notes, $userId) {
            // 2. Determine date range
            $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth()->toDateString();
            $endDate   = Carbon::createFromFormat('Y-m', $month)->endOfMonth()->toDateString();

            // 3. Create Draft Master Payroll
            $defaultTitle = $title ?: __('Monthly Payroll - :month', ['month' => $month]);
            $payroll = $this->payrollRepo->create([
                'payroll_month' => $month,
                'title'         => $defaultTitle,
                'branch_id'     => $branchId,
                'status'        => PayrollStatus::DRAFT->value,
                'notes'         => $notes,
                'created_by'    => $userId,
            ]);

            // 4. Fetch eligible active employees
            $empQuery = Employee::with(['allowances.allowanceType', 'bank', 'jobTitle', 'department', 'primaryBranch'])
                ->where('status', EmployeeStatus::ACTIVE->value);

            if ($branchId) {
                $empQuery->where('primary_branch_id', $branchId);
            }

            $employees = $empQuery->get();

            if ($employees->isEmpty()) {
                throw new Exception(__('No active employees found to generate payroll for this scope.'));
            }

            // Work policy constants
            $monthDays = (float) HrWorkPolicy::getVal('payroll_month_days', 26);
            $dailyHours = 8.0;

            $itemsData = [];

            foreach ($employees as $emp) {
                $basicSalary = (float) ($emp->basic_salary ?? 0);
                $dailyRate = $monthDays > 0 ? ($basicSalary / $monthDays) : ($basicSalary / 26);
                $hourlyRate = (float) ($emp->hourly_rate > 0 ? $emp->hourly_rate : ($dailyRate / $dailyHours));

                // A. Allowances
                $allowancesAmount = 0.0;
                $allowancesBreakdown = [];
                if ($emp->allowances) {
                    foreach ($emp->allowances as $allowance) {
                        $amt = (float) $allowance->amount;
                        if ($allowance->is_percentage) {
                            $amt = ($basicSalary * $amt) / 100.0;
                        }
                        $allowancesAmount += $amt;
                        $allowancesBreakdown[] = [
                            'name'   => $allowance->allowanceType?->name ?? __('Allowance'),
                            'amount' => round($amt, 2),
                        ];
                    }
                }

                // B. Overtime (from Attendances)
                $overtimeMinutes = (int) Attendance::where('employee_id', $emp->id)
                    ->whereBetween('date', [$startDate, $endDate])
                    ->sum('overtime_minutes');

                $overtimeHours = round($overtimeMinutes / 60, 2);
                $overtimeAmount = round($overtimeHours * $hourlyRate * 1.25, 2);

                // C. Rewards (approved & scheduled for this month or general)
                $rewardsAmount = (float) Reward::where('employee_id', $emp->id)
                    ->where('status', RewardStatus::APPROVED->value)
                    ->where('payment_method', RewardPaymentMethod::NEXT_PAYROLL->value)
                    ->where(function ($q) use ($month) {
                        $q->where('payroll_month', $month)->orWhereNull('payroll_month');
                    })
                    ->sum('amount');

                $grossSalary = round($basicSalary + $allowancesAmount + $overtimeAmount + $rewardsAmount, 2);

                // D. Absence Deductions
                $absenceDays = (float) Attendance::where('employee_id', $emp->id)
                    ->whereBetween('date', [$startDate, $endDate])
                    ->where('status', 'absent')
                    ->count();

                $absenceDeduction = round($absenceDays * $dailyRate, 2);

                // E. Lateness Deductions
                $lateMinutes = (int) Attendance::where('employee_id', $emp->id)
                    ->whereBetween('date', [$startDate, $endDate])
                    ->sum('late_minutes');

                $lateDeduction = round(($lateMinutes / 60) * $hourlyRate, 2);

                // F. Loan Installments (Pending & due this month)
                $loansDeduction = (float) LoanInstallment::whereHas('loan', function ($q) use ($emp) {
                    $q->where('employee_id', $emp->id)->where('status', LoanStatus::DISBURSED->value);
                })
                ->where('status', InstallmentStatus::PENDING->value)
                ->whereBetween('due_date', [$startDate, $endDate])
                ->sum('amount');

                // G. Penalties (approved & scheduled for this month)
                $penaltiesDeduction = (float) Penalty::where('employee_id', $emp->id)
                    ->where('status', PenaltyStatus::APPROVED->value)
                    ->where('deduction_method', PenaltyDeductionMethod::NEXT_PAYROLL->value)
                    ->where(function ($q) use ($month) {
                        $q->where('payroll_month', $month)->orWhereNull('payroll_month');
                    })
                    ->sum('amount');

                // H. Social Insurance (if percentage configured)
                $insurancePercentage = (float) ($emp->social_insurance_percentage ?? 0);
                $insuranceDeduction = $insurancePercentage > 0 ? round(($basicSalary * $insurancePercentage) / 100, 2) : 0.0;

                $totalDeductions = round($absenceDeduction + $lateDeduction + $loansDeduction + $penaltiesDeduction + $insuranceDeduction, 2);
                $netSalary = max(0.00, round($grossSalary - $totalDeductions, 2));

                $bankName = $emp->bank ? $emp->bank->name : null;

                $itemsData[] = [
                    'payroll_id'          => $payroll->id,
                    'employee_id'         => $emp->id,
                    'basic_salary'        => $basicSalary,
                    'allowances_amount'   => $allowancesAmount,
                    'allowances_breakdown'=> json_encode($allowancesBreakdown),
                    'overtime_hours'      => $overtimeHours,
                    'overtime_amount'     => $overtimeAmount,
                    'rewards_amount'      => $rewardsAmount,
                    'other_earnings'      => 0.00,
                    'gross_salary'        => $grossSalary,
                    'absence_days'        => $absenceDays,
                    'absence_deduction'   => $absenceDeduction,
                    'late_minutes'        => $lateMinutes,
                    'late_deduction'      => $lateDeduction,
                    'loans_deduction'     => $loansDeduction,
                    'penalties_deduction' => $penaltiesDeduction,
                    'insurance_deduction' => $insuranceDeduction,
                    'other_deductions'    => 0.00,
                    'total_deductions'    => $totalDeductions,
                    'net_salary'          => $netSalary,
                    'bank_name'           => $bankName,
                    'account_number'      => $emp->account_number,
                    'iban'                => $emp->iban,
                    'payment_status'      => PayrollPaymentStatus::PENDING->value,
                    'created_at'          => now(),
                    'updated_at'          => now(),
                ];
            }

            // Insert all payroll items in bulk
            PayrollItem::insert($itemsData);

            // Re-aggregate and update master payroll totals
            $this->syncPayrollTotals($payroll);

            return $payroll->fresh(['items', 'branch']);
        });
    }

    /**
     * Recalculate all items in a draft payroll.
     *
     * @throws Exception
     */
    public function recalculate(Payroll $payroll): Payroll
    {
        if ($payroll->status !== PayrollStatus::DRAFT) {
            throw new Exception(__('Only draft payrolls can be recalculated.'));
        }

        return DB::transaction(function () use ($payroll) {
            // Remove old items
            $payroll->items()->delete();

            $month = $payroll->payroll_month;
            $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth()->toDateString();
            $endDate   = Carbon::createFromFormat('Y-m', $month)->endOfMonth()->toDateString();

            $empQuery = Employee::with(['allowances.allowanceType', 'bank'])
                ->where('status', EmployeeStatus::ACTIVE->value);

            if ($payroll->branch_id) {
                $empQuery->where('primary_branch_id', $payroll->branch_id);
            }

            $employees = $empQuery->get();
            $monthDays = (float) HrWorkPolicy::getVal('payroll_month_days', 26);
            $dailyHours = 8.0;

            $itemsData = [];
            foreach ($employees as $emp) {
                $basicSalary = (float) ($emp->basic_salary ?? 0);
                $dailyRate = $monthDays > 0 ? ($basicSalary / $monthDays) : ($basicSalary / 26);
                $hourlyRate = (float) ($emp->hourly_rate > 0 ? $emp->hourly_rate : ($dailyRate / $dailyHours));

                $allowancesAmount = 0.0;
                $allowancesBreakdown = [];
                if ($emp->allowances) {
                    foreach ($emp->allowances as $allowance) {
                        $amt = (float) $allowance->amount;
                        if ($allowance->is_percentage) {
                            $amt = ($basicSalary * $amt) / 100.0;
                        }
                        $allowancesAmount += $amt;
                        $allowancesBreakdown[] = [
                            'name'   => $allowance->allowanceType?->name ?? __('Allowance'),
                            'amount' => round($amt, 2),
                        ];
                    }
                }

                $overtimeMinutes = (int) Attendance::where('employee_id', $emp->id)
                    ->whereBetween('date', [$startDate, $endDate])
                    ->sum('overtime_minutes');

                $overtimeHours = round($overtimeMinutes / 60, 2);
                $overtimeAmount = round($overtimeHours * $hourlyRate * 1.25, 2);

                $rewardsAmount = (float) Reward::where('employee_id', $emp->id)
                    ->where('status', RewardStatus::APPROVED->value)
                    ->where('payment_method', RewardPaymentMethod::NEXT_PAYROLL->value)
                    ->where(function ($q) use ($month) {
                        $q->where('payroll_month', $month)->orWhereNull('payroll_month');
                    })
                    ->sum('amount');

                $grossSalary = round($basicSalary + $allowancesAmount + $overtimeAmount + $rewardsAmount, 2);

                $absenceDays = (float) Attendance::where('employee_id', $emp->id)
                    ->whereBetween('date', [$startDate, $endDate])
                    ->where('status', 'absent')
                    ->count();

                $absenceDeduction = round($absenceDays * $dailyRate, 2);

                $lateMinutes = (int) Attendance::where('employee_id', $emp->id)
                    ->whereBetween('date', [$startDate, $endDate])
                    ->sum('late_minutes');

                $lateDeduction = round(($lateMinutes / 60) * $hourlyRate, 2);

                $loansDeduction = (float) LoanInstallment::whereHas('loan', function ($q) use ($emp) {
                    $q->where('employee_id', $emp->id)->where('status', LoanStatus::DISBURSED->value);
                })
                ->where('status', InstallmentStatus::PENDING->value)
                ->whereBetween('due_date', [$startDate, $endDate])
                ->sum('amount');

                $penaltiesDeduction = (float) Penalty::where('employee_id', $emp->id)
                    ->where('status', PenaltyStatus::APPROVED->value)
                    ->where('deduction_method', PenaltyDeductionMethod::NEXT_PAYROLL->value)
                    ->where(function ($q) use ($month) {
                        $q->where('payroll_month', $month)->orWhereNull('payroll_month');
                    })
                    ->sum('amount');

                $insurancePercentage = (float) ($emp->social_insurance_percentage ?? 0);
                $insuranceDeduction = $insurancePercentage > 0 ? round(($basicSalary * $insurancePercentage) / 100, 2) : 0.0;

                $totalDeductions = round($absenceDeduction + $lateDeduction + $loansDeduction + $penaltiesDeduction + $insuranceDeduction, 2);
                $netSalary = max(0.00, round($grossSalary - $totalDeductions, 2));

                $bankName = $emp->bank ? $emp->bank->name : null;

                $itemsData[] = [
                    'payroll_id'          => $payroll->id,
                    'employee_id'         => $emp->id,
                    'basic_salary'        => $basicSalary,
                    'allowances_amount'   => $allowancesAmount,
                    'allowances_breakdown'=> json_encode($allowancesBreakdown),
                    'overtime_hours'      => $overtimeHours,
                    'overtime_amount'     => $overtimeAmount,
                    'rewards_amount'      => $rewardsAmount,
                    'other_earnings'      => 0.00,
                    'gross_salary'        => $grossSalary,
                    'absence_days'        => $absenceDays,
                    'absence_deduction'   => $absenceDeduction,
                    'late_minutes'        => $lateMinutes,
                    'late_deduction'      => $lateDeduction,
                    'loans_deduction'     => $loansDeduction,
                    'penalties_deduction' => $penaltiesDeduction,
                    'insurance_deduction' => $insuranceDeduction,
                    'other_deductions'    => 0.00,
                    'total_deductions'    => $totalDeductions,
                    'net_salary'          => $netSalary,
                    'bank_name'           => $bankName,
                    'account_number'      => $emp->account_number,
                    'iban'                => $emp->iban,
                    'payment_status'      => PayrollPaymentStatus::PENDING->value,
                    'created_at'          => now(),
                    'updated_at'          => now(),
                ];
            }

            PayrollItem::insert($itemsData);
            $this->syncPayrollTotals($payroll);

            return $payroll->fresh(['items', 'branch']);
        });
    }

    /**
     * Update an individual payroll item (e.g. manual adjustments).
     *
     * @throws Exception
     */
    public function updateItem(PayrollItem $item, array $data): PayrollItem
    {
        $payroll = $item->payroll;
        if ($payroll->status !== PayrollStatus::DRAFT) {
            throw new Exception(__('Only items in a draft payroll can be edited.'));
        }

        return DB::transaction(function () use ($item, $data, $payroll) {
            $basicSalary       = isset($data['basic_salary']) ? (float) $data['basic_salary'] : (float) $item->basic_salary;
            $allowancesAmount  = isset($data['allowances_amount']) ? (float) $data['allowances_amount'] : (float) $item->allowances_amount;
            $overtimeAmount    = isset($data['overtime_amount']) ? (float) $data['overtime_amount'] : (float) $item->overtime_amount;
            $rewardsAmount     = isset($data['rewards_amount']) ? (float) $data['rewards_amount'] : (float) $item->rewards_amount;
            $otherEarnings     = isset($data['other_earnings']) ? (float) $data['other_earnings'] : (float) $item->other_earnings;

            $grossSalary = round($basicSalary + $allowancesAmount + $overtimeAmount + $rewardsAmount + $otherEarnings, 2);

            $absenceDeduction   = isset($data['absence_deduction']) ? (float) $data['absence_deduction'] : (float) $item->absence_deduction;
            $lateDeduction      = isset($data['late_deduction']) ? (float) $data['late_deduction'] : (float) $item->late_deduction;
            $loansDeduction     = isset($data['loans_deduction']) ? (float) $data['loans_deduction'] : (float) $item->loans_deduction;
            $penaltiesDeduction = isset($data['penalties_deduction']) ? (float) $data['penalties_deduction'] : (float) $item->penalties_deduction;
            $insuranceDeduction = isset($data['insurance_deduction']) ? (float) $data['insurance_deduction'] : (float) $item->insurance_deduction;
            $otherDeductions    = isset($data['other_deductions']) ? (float) $data['other_deductions'] : (float) $item->other_deductions;

            $totalDeductions = round($absenceDeduction + $lateDeduction + $loansDeduction + $penaltiesDeduction + $insuranceDeduction + $otherDeductions, 2);
            $netSalary = max(0.00, round($grossSalary - $totalDeductions, 2));

            $item->update(array_merge($data, [
                'basic_salary'        => $basicSalary,
                'allowances_amount'   => $allowancesAmount,
                'overtime_amount'     => $overtimeAmount,
                'rewards_amount'      => $rewardsAmount,
                'other_earnings'      => $otherEarnings,
                'gross_salary'        => $grossSalary,
                'absence_deduction'   => $absenceDeduction,
                'late_deduction'      => $lateDeduction,
                'loans_deduction'     => $loansDeduction,
                'penalties_deduction' => $penaltiesDeduction,
                'insurance_deduction' => $insuranceDeduction,
                'other_deductions'    => $otherDeductions,
                'total_deductions'    => $totalDeductions,
                'net_salary'          => $netSalary,
            ]));

            $this->syncPayrollTotals($payroll);

            return $item->fresh();
        });
    }

    /**
     * Approve payroll run.
     *
     * @throws Exception
     */
    public function approve(Payroll $payroll, int $userId): Payroll
    {
        if ($payroll->status !== PayrollStatus::DRAFT) {
            throw new Exception(__('Only draft payrolls can be approved.'));
        }

        $payroll->update([
            'status'      => PayrollStatus::APPROVED->value,
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);

        return $payroll->fresh();
    }

    /**
     * Disburse / Mark as Paid and synchronize all connected modules.
     *
     * @throws Exception
     */
    public function disburse(Payroll $payroll, int $userId): Payroll
    {
        if (!in_array($payroll->status, [PayrollStatus::DRAFT, PayrollStatus::APPROVED])) {
            throw new Exception(__('This payroll cannot be disbursed.'));
        }

        return DB::transaction(function () use ($payroll, $userId) {
            $month = $payroll->payroll_month;
            $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth()->toDateString();
            $endDate   = Carbon::createFromFormat('Y-m', $month)->endOfMonth()->toDateString();

            // 1. Mark payroll as Paid
            $payroll->update([
                'status'  => PayrollStatus::PAID->value,
                'paid_by' => $userId,
                'paid_at' => now(),
            ]);

            // 2. Mark all items as Paid
            $payroll->items()->update(['payment_status' => PayrollPaymentStatus::PAID->value]);

            // 3. Mark connected loan installments as Paid
            $empIds = $payroll->items()->pluck('employee_id')->toArray();
            LoanInstallment::whereHas('loan', function ($q) use ($empIds) {
                $q->whereIn('employee_id', $empIds);
            })
            ->where('status', InstallmentStatus::PENDING->value)
            ->whereBetween('due_date', [$startDate, $endDate])
            ->update([
                'status'      => InstallmentStatus::PAID->value,
                'paid_at'     => now(),
                'paid_amount' => DB::raw('amount'),
                'payroll_id'  => $payroll->id,
            ]);

            // 4. Mark connected rewards as Paid
            Reward::whereIn('employee_id', $empIds)
                ->where('status', RewardStatus::APPROVED->value)
                ->where('payment_method', RewardPaymentMethod::NEXT_PAYROLL->value)
                ->where(function ($q) use ($month) {
                    $q->where('payroll_month', $month)->orWhereNull('payroll_month');
                })
                ->update([
                    'status'  => RewardStatus::PAID->value,
                    'paid_by' => $userId,
                    'paid_at' => now(),
                ]);

            // 5. Mark connected penalties as Deducted
            Penalty::whereIn('employee_id', $empIds)
                ->where('status', PenaltyStatus::APPROVED->value)
                ->where('deduction_method', PenaltyDeductionMethod::NEXT_PAYROLL->value)
                ->where(function ($q) use ($month) {
                    $q->where('payroll_month', $month)->orWhereNull('payroll_month');
                })
                ->update([
                    'status'      => PenaltyStatus::DEDUCTED->value,
                    'deducted_by' => $userId,
                    'deducted_at' => now(),
                ]);

            return $payroll->fresh();
        });
    }

    /**
     * Cancel payroll run.
     *
     * @throws Exception
     */
    public function cancel(Payroll $payroll, string $reason, int $userId): Payroll
    {
        if ($payroll->status === PayrollStatus::PAID) {
            throw new Exception(__('Paid/disbursed payrolls cannot be cancelled.'));
        }

        $payroll->update([
            'status'              => PayrollStatus::CANCELLED->value,
            'cancellation_reason' => $reason,
            'cancelled_by'        => $userId,
            'cancelled_at'        => now(),
        ]);

        return $payroll->fresh();
    }

    /**
     * Delete payroll run.
     *
     * @throws Exception
     */
    public function delete(Payroll $payroll): bool
    {
        if ($payroll->status === PayrollStatus::PAID) {
            throw new Exception(__('Paid payrolls cannot be deleted.'));
        }

        return DB::transaction(function () use ($payroll) {
            $payroll->items()->delete();
            return $this->payrollRepo->delete($payroll);
        });
    }

    /**
     * Synchronize and recalculate master payroll totals from its items.
     */
    protected function syncPayrollTotals(Payroll $payroll): void
    {
        $totals = $payroll->items()
            ->selectRaw('
                COUNT(*) as count,
                COALESCE(SUM(basic_salary), 0) as total_basic,
                COALESCE(SUM(allowances_amount), 0) as total_allowances,
                COALESCE(SUM(overtime_amount), 0) as total_overtime,
                COALESCE(SUM(rewards_amount), 0) as total_rewards,
                COALESCE(SUM(gross_salary), 0) as total_gross,
                COALESCE(SUM(absence_deduction), 0) as total_absence,
                COALESCE(SUM(loans_deduction), 0) as total_loans,
                COALESCE(SUM(penalties_deduction), 0) as total_penalties,
                COALESCE(SUM(total_deductions), 0) as total_deductions,
                COALESCE(SUM(net_salary), 0) as total_net
            ')
            ->first();

        $payroll->update([
            'employees_count'           => $totals->count ?? 0,
            'total_basic_salary'        => $totals->total_basic ?? 0,
            'total_allowances'          => $totals->total_allowances ?? 0,
            'total_overtime'            => $totals->total_overtime ?? 0,
            'total_rewards'             => $totals->total_rewards ?? 0,
            'total_gross'               => $totals->total_gross ?? 0,
            'total_absence_deduction'   => $totals->total_absence ?? 0,
            'total_loans_deduction'     => $totals->total_loans ?? 0,
            'total_penalties_deduction' => $totals->total_penalties ?? 0,
            'total_deductions'          => $totals->total_deductions ?? 0,
            'total_net'                 => $totals->total_net ?? 0,
        ]);
    }
}
