<?php

namespace Modules\HR\Services;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\HR\Enums\InstallmentStatus;
use Modules\HR\Enums\LoanStatus;
use Modules\HR\Enums\LoanType;
use Modules\HR\Models\Employee;
use Modules\HR\Models\HrWorkPolicy;
use Modules\HR\Models\Loan;
use Modules\HR\Models\LoanInstallment;
use Modules\HR\Repositories\Contracts\LoanRepositoryInterface;

class LoanService
{
    public function __construct(
        protected LoanRepositoryInterface $loanRepo
    ) {}

    //======================
    /**
     * Get DataTable query with applied filters.
     */
    public function getDataTableQuery(array $filters = [])
    {
        return $this->loanRepo->getDataTableQuery($filters);
    }

    //======================
    /**
     * Get KPI metrics.
     */
    public function getKPIs(): array
    {
        return $this->loanRepo->getKPIs();
    }

    //======================
    /**
     * Find loan by ID with relations.
     */
    public function getById(int $id, array $with = ['employee.translations', 'creator', 'installments', 'managerApprover', 'hrApprover']): ?Loan
    {
        return $this->loanRepo->findById($id, $with);
    }

    //======================
    /**
     * Check employee loan eligibility and return financial status metrics based on dynamic HR Work Policies.
     */
    public function checkEligibility(int $employeeId, float $amount = 0.0, int $installmentsCount = 1, ?string $loanType = null): array
    {
        $employee = Employee::with('translations')->find($employeeId);
        if (!$employee) {
            throw new Exception(__('Employee not found.'));
        }

        $basicSalary  = (float) ($employee->basic_salary ?? 0);
        $hireDate     = $employee->hire_date ? Carbon::parse($employee->hire_date) : null;
        $tenureMonths = $hireDate ? (int) $hireDate->diffInMonths(Carbon::now()) : 0;

        // Active loans & existing debt
        $activeLoansCount = $this->loanRepo->getEmployeeActiveLoansCount($employeeId);
        $remainingDebt    = $this->loanRepo->getEmployeeActiveLoansTotalRemaining($employeeId);

        // Fetch dynamic HR policies for loans
        $policies = HrWorkPolicy::getGroup('loans');

        $minTenureMonths      = (int)   ($policies['min_service_months'] ?? 3);
        $maxSalaryMultiplier  = (float) ($policies['max_salary_multiplier'] ?? 3.0);
        $maxAdvanceMultiplier = (float) ($policies['max_advance_multiplier'] ?? 1.0);
        $maxDeductionPct      = (float) ($policies['max_monthly_deduction_percentage'] ?? 33.3);
        $maxInstallmentMonths = (int)   ($policies['max_installments_months'] ?? 24);
        $allowMultipleLoans   = (bool)  ($policies['allow_multiple_loans'] ?? false);

        // Standard financial calculations based on policies:
        $isAdvance         = ($loanType === LoanType::SALARY_ADVANCE->value || $installmentsCount === 1);
        $maxMultiplier     = $isAdvance ? $maxAdvanceMultiplier : $maxSalaryMultiplier;
        $maxAllowedLoan    = round($basicSalary * $maxMultiplier, 2);

        // Max monthly deduction ceiling
        $maxMonthlyDeduction = round($basicSalary * ($maxDeductionPct / 100), 2);

        // Requested monthly installment
        $count = max(1, $installmentsCount);
        $monthlyInstallment = round($amount / $count, 2);

        // Evaluate eligibility criteria
        $isEligible = true;
        $reasons = [];

        if ($basicSalary <= 0) {
            $isEligible = false;
            $reasons[] = __('Employee does not have a basic salary configured in the system.');
        }

        if ($tenureMonths < $minTenureMonths) {
            $isEligible = false;
            $reasons[] = __('Employee has not completed the minimum service tenure requirement (:req months). Current tenure: :months months.', [
                'req'    => $minTenureMonths,
                'months' => $tenureMonths,
            ]);
        }

        if (!$allowMultipleLoans && $activeLoansCount > 0) {
            $isEligible = false;
            $reasons[] = __('Employee already has an active loan with remaining balance of :amount. Company policy prohibits multiple concurrent loans.', [
                'amount' => number_format($remainingDebt, 2),
            ]);
        }

        if ($count > $maxInstallmentMonths) {
            $isEligible = false;
            $reasons[] = __('Requested repayment duration (:req months) exceeds policy maximum limit (:max months).', [
                'req' => $count,
                'max' => $maxInstallmentMonths,
            ]);
        }

        if ($amount > 0 && $amount > $maxAllowedLoan) {
            $isEligible = false;
            $reasons[] = __('Requested loan amount (:req) exceeds maximum allowed limit (:max).', [
                'req' => number_format($amount, 2),
                'max' => number_format($maxAllowedLoan, 2),
            ]);
        }

        if ($amount > 0 && $monthlyInstallment > $maxMonthlyDeduction) {
            $isEligible = false;
            $reasons[] = __('Monthly installment (:inst) exceeds :pct% maximum monthly deduction ceiling (:max). Consider increasing number of installments.', [
                'inst' => number_format($monthlyInstallment, 2),
                'pct'  => $maxDeductionPct,
                'max'  => number_format($maxMonthlyDeduction, 2),
            ]);
        }

        return [
            'is_eligible'           => $isEligible,
            'ineligibility_reason'  => implode(' ', $reasons),
            'employee'              => [
                'id'                     => $employee->id,
                'name'                   => $employee->name,
                'code'                   => $employee->employee_code,
                'basic_salary'           => $basicSalary,
                'hire_date'              => $hireDate ? $hireDate->format('Y-m-d') : '-',
                'tenure_months'          => $tenureMonths,
                'active_loans_count'     => $activeLoansCount,
                'remaining_debt'         => $remainingDebt,
                'max_allowed_loan'       => $maxAllowedLoan,
                'max_monthly_deduction'  => $maxMonthlyDeduction,
            ],
            'requested'             => [
                'amount'              => $amount,
                'installments_count'  => $count,
                'monthly_installment' => $monthlyInstallment,
            ],
            'policies'              => [
                'min_service_months'               => $minTenureMonths,
                'max_salary_multiplier'            => $maxSalaryMultiplier,
                'max_advance_multiplier'           => $maxAdvanceMultiplier,
                'max_monthly_deduction_percentage' => $maxDeductionPct,
                'max_installments_months'          => $maxInstallmentMonths,
                'allow_multiple_loans'             => $allowMultipleLoans,
            ],
        ];
    }

    //======================
    /**
     * Generate live preview of installment schedule.
     */
    public function generateSchedulePreview(float $amount, int $installmentsCount, string $startDate): array
    {
        $count = max(1, $installmentsCount);
        $baseInstallment = round($amount / $count, 2);
        $accumulated = $baseInstallment * ($count - 1);
        $finalInstallment = round($amount - $accumulated, 2);

        $start = Carbon::parse($startDate);
        $schedule = [];

        for ($i = 1; $i <= $count; $i++) {
            $dueDate = $start->copy()->addMonths($i - 1);
            $instAmount = ($i === $count) ? $finalInstallment : $baseInstallment;

            $schedule[] = [
                'installment_number' => $i,
                'due_date'           => $dueDate->format('Y-m-d'),
                'due_date_formatted' => $dueDate->isoFormat('MMMM YYYY'),
                'amount'             => $instAmount,
            ];
        }

        return $schedule;
    }

    //======================
    /**
     * Create a new loan and generate its installment schedule.
     */
    public function createLoan(array $data, ?UploadedFile $attachment = null, int $userId = 1): Loan
    {
        return DB::transaction(function () use ($data, $attachment, $userId) {
            $amount            = (float) $data['amount'];
            $installmentsCount = (int) ($data['installments_count'] ?? 1);
            $startDate         = $data['start_date'];
            $type              = $data['type'] ?? LoanType::INSTALLMENT_LOAN->value;

            // Compute monthly installment
            $monthlyInstallment = round($amount / max(1, $installmentsCount), 2);

            // Handle attachment
            $attachmentPath = null;
            if ($attachment) {
                $attachmentPath = $attachment->store('loans/attachments', 'public');
            }

            $loan = $this->loanRepo->create([
                'employee_id'         => $data['employee_id'],
                'created_by'          => $userId,
                'type'                => $type,
                'amount'              => $amount,
                'installments_count'  => $installmentsCount,
                'monthly_installment' => $monthlyInstallment,
                'start_date'          => $startDate,
                'total_paid'          => 0.00,
                'remaining_amount'    => $amount,
                'status'              => LoanStatus::PENDING->value,
                'reason'              => $data['reason'] ?? null,
                'attachment'          => $attachmentPath,
            ]);

            // Generate Installments
            $schedule = $this->generateSchedulePreview($amount, $installmentsCount, $startDate);
            foreach ($schedule as $item) {
                LoanInstallment::create([
                    'loan_id'            => $loan->id,
                    'installment_number' => $item['installment_number'],
                    'due_date'           => $item['due_date'],
                    'amount'             => $item['amount'],
                    'paid_amount'        => 0.00,
                    'status'             => InstallmentStatus::PENDING->value,
                ]);
            }

            return $loan;
        });
    }

    //======================
    /**
     * Approve a loan request.
     */
    public function approveLoan(int $id, int $userId): Loan
    {
        $loan = $this->loanRepo->findById($id);
        if (!$loan) {
            throw new Exception(__('Loan request not found.'));
        }

        if ($loan->status !== LoanStatus::PENDING) {
            throw new Exception(__('Only pending loan requests can be approved.'));
        }

        $this->loanRepo->update($loan, [
            'status'              => LoanStatus::APPROVED->value,
            'hr_approved_by'      => $userId,
            'hr_approved_at'      => Carbon::now(),
            'manager_approved_by' => $userId,
            'manager_approved_at' => Carbon::now(),
        ]);

        return $loan->fresh();
    }

    //======================
    /**
     * Disburse an approved loan (finance payout).
     */
    public function disburseLoan(int $id, array $disbursementData, int $userId): Loan
    {
        $loan = $this->loanRepo->findById($id);
        if (!$loan) {
            throw new Exception(__('Loan request not found.'));
        }

        if (!in_array($loan->status, [LoanStatus::PENDING, LoanStatus::APPROVED])) {
            throw new Exception(__('Only pending or approved loans can be disbursed.'));
        }

        $this->loanRepo->update($loan, [
            'status'                 => LoanStatus::DISBURSED->value,
            'disbursed_at'           => $disbursementData['disbursed_at'] ?? Carbon::now(),
            'disbursement_method'    => $disbursementData['disbursement_method'] ?? 'bank_transfer',
            'disbursement_reference' => $disbursementData['disbursement_reference'] ?? null,
            'hr_approved_by'         => $loan->hr_approved_by ?? $userId,
            'hr_approved_at'         => $loan->hr_approved_at ?? Carbon::now(),
        ]);

        return $loan->fresh();
    }

    //======================
    /**
     * Reject a loan request.
     */
    public function rejectLoan(int $id, string $reason, int $userId): Loan
    {
        $loan = $this->loanRepo->findById($id);
        if (!$loan) {
            throw new Exception(__('Loan request not found.'));
        }

        if ($loan->status === LoanStatus::DISBURSED || $loan->status === LoanStatus::PAID) {
            throw new Exception(__('Disbursed or paid loans cannot be rejected.'));
        }

        $this->loanRepo->update($loan, [
            'status'           => LoanStatus::REJECTED->value,
            'rejection_reason' => $reason,
        ]);

        return $loan->fresh();
    }

    //======================
    /**
     * Postpone an installment to a new due date.
     */
    public function postponeInstallment(int $installmentId, string $newDueDate, ?string $reason = null): LoanInstallment
    {
        $installment = LoanInstallment::with('loan')->find($installmentId);
        if (!$installment) {
            throw new Exception(__('Installment not found.'));
        }

        if ($installment->status === InstallmentStatus::PAID) {
            throw new Exception(__('Paid installments cannot be postponed.'));
        }

        $originalDueDate = $installment->due_date;

        $installment->update([
            'postponed_from' => $installment->postponed_from ?? $originalDueDate,
            'due_date'       => $newDueDate,
            'status'         => InstallmentStatus::POSTPONED->value,
            'notes'          => trim(($installment->notes ? $installment->notes . ' | ' : '') . ($reason ?? __('Postponed'))),
        ]);

        return $installment->fresh();
    }

    //======================
    /**
     * Record payment for an installment (manual or payroll).
     */
    public function payInstallment(int $installmentId, ?float $paidAmount = null, ?string $notes = null): LoanInstallment
    {
        return DB::transaction(function () use ($installmentId, $paidAmount, $notes) {
            $installment = LoanInstallment::with('loan')->find($installmentId);
            if (!$installment) {
                throw new Exception(__('Installment not found.'));
            }

            if ($installment->status === InstallmentStatus::PAID) {
                throw new Exception(__('Installment is already marked as paid.'));
            }

            $amountToPay = $paidAmount !== null ? min($installment->amount, $paidAmount) : $installment->amount;

            $installment->update([
                'paid_amount' => $amountToPay,
                'status'      => InstallmentStatus::PAID->value,
                'paid_at'     => Carbon::now()->toDateString(),
                'notes'       => $notes ?? $installment->notes,
            ]);

            // Recalculate Loan total_paid and remaining_amount
            $loan = $installment->loan;
            $totalPaid = (float) LoanInstallment::where('loan_id', $loan->id)->where('status', InstallmentStatus::PAID->value)->sum('paid_amount');
            $remaining = max(0.00, round($loan->amount - $totalPaid, 2));

            $loanUpdate = [
                'total_paid'       => $totalPaid,
                'remaining_amount' => $remaining,
            ];

            if ($remaining <= 0.00) {
                $loanUpdate['status'] = LoanStatus::PAID->value;
            }

            $loan->update($loanUpdate);

            return $installment->fresh();
        });
    }

    //======================
    /**
     * Delete a loan (only if pending or rejected).
     */
    public function deleteLoan(int $id): bool
    {
        $loan = $this->loanRepo->findById($id);
        if (!$loan) {
            throw new Exception(__('Loan request not found.'));
        }

        if (in_array($loan->status, [LoanStatus::DISBURSED, LoanStatus::PAID])) {
            throw new Exception(__('Active disbursed or paid loans cannot be deleted.'));
        }

        if ($loan->attachment) {
            Storage::disk('public')->delete($loan->attachment);
        }

        return $this->loanRepo->delete($loan);
    }
}
