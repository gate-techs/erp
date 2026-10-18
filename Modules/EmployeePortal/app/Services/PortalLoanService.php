<?php

namespace Modules\EmployeePortal\Services;

use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Modules\EmployeePortal\Repositories\Contracts\PortalEmployeeRepositoryInterface;
use Modules\EmployeePortal\Services\Contracts\PortalLoanServiceInterface;
use Modules\HR\Enums\InstallmentStatus;
use Modules\HR\Enums\LoanStatus;
use Modules\HR\Enums\LoanType;
use Modules\HR\Models\Employee;
use Modules\HR\Models\HrWorkPolicy;
use Modules\HR\Models\Loan;
use Modules\HR\Models\LoanInstallment;
use Modules\HR\Services\LoanService as HrLoanService;

class PortalLoanService implements PortalLoanServiceInterface
{
    public function __construct(
        protected PortalEmployeeRepositoryInterface $employeeRepo,
        protected HrLoanService $hrLoanService
    ) {}

    /**
     * Resolve employee from current user.
     */
    public function resolveCurrentEmployee(?User $user): ?Employee
    {
        if ($user) {
            $employee = $this->employeeRepo->findByUserId($user->id);
            if ($employee) {
                return $employee;
            }
        }

        return $this->employeeRepo->getActiveFallback();
    }

    /**
     * Get data required for submitting a new loan / salary advance request.
     */
    public function getLoanFormContext(?User $user): array
    {
        $employee = $this->resolveCurrentEmployee($user);

        $eligibility = [
            'is_eligible'          => false,
            'ineligibility_reason' => __('Employee record not found.'),
            'employee'             => [
                'basic_salary'          => 0.0,
                'max_allowed_loan'      => 0.0,
                'max_monthly_deduction' => 0.0,
                'active_loans_count'    => 0,
                'remaining_debt'        => 0.0,
                'tenure_months'         => 0,
            ],
            'policies'             => [
                'min_service_months'               => 3,
                'max_salary_multiplier'            => 3.0,
                'max_advance_multiplier'           => 1.0,
                'max_monthly_deduction_percentage' => 33.3,
                'max_installments_months'          => 24,
                'allow_multiple_loans'             => false,
            ],
        ];

        $recentLoans = collect();

        if ($employee) {
            try {
                $eligibility = $this->hrLoanService->checkEligibility($employee->id, 0.0, 1, LoanType::SALARY_ADVANCE->value);
            } catch (\Throwable $e) {
                Log::warning('Error checking eligibility for portal loan form: ' . $e->getMessage());
            }

            $recentLoans = Loan::where('employee_id', $employee->id)
                ->with('installments')
                ->latest()
                ->take(3)
                ->get();
        }

        return [
            'employee'    => $employee,
            'user'        => $user,
            'eligibility' => $eligibility,
            'recentLoans' => $recentLoans,
            'loanTypes'   => LoanType::cases(),
        ];
    }

    /**
     * AJAX real-time eligibility & installment calculation for loan request.
     */
    public function calculateLoanEligibility(?User $user, array $params): array
    {
        $employee = $this->resolveCurrentEmployee($user);

        if (!$employee) {
            return [
                'success' => false,
                'message' => __('No employee record associated with your account.'),
            ];
        }

        $amount            = (float) ($params['amount'] ?? 0);
        $type              = $params['type'] ?? LoanType::SALARY_ADVANCE->value;
        $installmentsCount = ($type === LoanType::SALARY_ADVANCE->value) ? 1 : max(1, (int) ($params['installments_count'] ?? 1));
        $startDate         = $params['start_date'] ?? date('Y-m-01', strtotime('+1 month'));

        try {
            $eligibility = $this->hrLoanService->checkEligibility($employee->id, $amount, $installmentsCount, $type);
            $schedule    = $this->hrLoanService->generateSchedulePreview($amount, $installmentsCount, $startDate);

            $monthlyInstallment = round($amount / max(1, $installmentsCount), 2);

            return [
                'success'             => true,
                'is_eligible'         => $eligibility['is_eligible'],
                'ineligibility_reason'=> $eligibility['ineligibility_reason'],
                'monthly_installment' => $monthlyInstallment,
                'installments_count'  => $installmentsCount,
                'schedule'            => $schedule,
                'employee_data'       => $eligibility['employee'],
                'policies'            => $eligibility['policies'],
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Handle Loan / Salary Advance Request Submission.
     */
    public function submitLoanRequest(array $data, ?User $user, ?UploadedFile $attachment = null): array
    {
        $employee = $this->resolveCurrentEmployee($user);

        if (!$employee) {
            return [
                'success' => false,
                'message' => __('No employee record associated with your account.'),
            ];
        }

        try {
            $amount = (float) $data['amount'];
            $type   = $data['type'] ?? LoanType::SALARY_ADVANCE->value;
            $count  = ($type === LoanType::SALARY_ADVANCE->value) ? 1 : max(1, (int) ($data['installments_count'] ?? 1));

            // Verify eligibility before final creation
            $eligibility = $this->hrLoanService->checkEligibility($employee->id, $amount, $count, $type);

            if (!$eligibility['is_eligible']) {
                return [
                    'success' => false,
                    'message' => $eligibility['ineligibility_reason'] ?: __('Requested loan violates company loan policies.'),
                ];
            }

            $loanData = [
                'employee_id'        => $employee->id,
                'type'               => $type,
                'amount'             => $amount,
                'installments_count' => $count,
                'start_date'         => $data['start_date'] ?? date('Y-m-01', strtotime('+1 month')),
                'reason'             => $data['reason'] ?? null,
            ];

            $loan = $this->hrLoanService->createLoan($loanData, $attachment, $user?->id ?? 1);

            return [
                'success' => true,
                'message' => __('Loan / salary advance request submitted successfully and is pending administrative approval.'),
                'loan'    => $loan,
            ];
        } catch (\Throwable $e) {
            Log::error('PortalLoanService submit error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get data required for loans & advances history screen.
     */
    public function getLoansHistoryContext(?User $user, ?string $status = null): array
    {
        $employee = $this->resolveCurrentEmployee($user);

        if ($employee) {
            $baseQuery = Loan::where('employee_id', $employee->id);

            // Compute counts
            $statusCounts = [
                'all'       => (clone $baseQuery)->count(),
                'pending'   => (clone $baseQuery)->where('status', LoanStatus::PENDING->value)->count(),
                'approved'  => (clone $baseQuery)->where('status', LoanStatus::APPROVED->value)->count(),
                'disbursed' => (clone $baseQuery)->where('status', LoanStatus::DISBURSED->value)->count(),
                'paid'      => (clone $baseQuery)->where('status', LoanStatus::PAID->value)->count(),
                'rejected'  => (clone $baseQuery)->where('status', LoanStatus::REJECTED->value)->count(),
            ];

            // Filter
            $loansQuery = clone $baseQuery;
            if ($status && $status !== 'all') {
                $loansQuery->where('status', $status);
            }

            $loans = $loansQuery->with('installments')->latest('created_at')->get();

            // Total active remaining debt
            $totalRemainingDebt = (clone $baseQuery)->whereIn('status', [LoanStatus::APPROVED->value, LoanStatus::DISBURSED->value])->sum('remaining_amount');

            // Total disbursed in lifetime
            $totalDisbursed = (clone $baseQuery)->whereIn('status', [LoanStatus::DISBURSED->value, LoanStatus::PAID->value])->sum('amount');

            // Total repaid
            $totalRepaid = (clone $baseQuery)->sum('total_paid');

            // Next upcoming installment
            $nextInstallment = LoanInstallment::whereHas('loan', function ($q) use ($employee) {
                $q->where('employee_id', $employee->id)->where('status', LoanStatus::DISBURSED->value);
            })->where('status', InstallmentStatus::PENDING->value)
              ->where('due_date', '>=', Carbon::today()->startOfMonth())
              ->orderBy('due_date')
              ->first();
        } else {
            $loans = collect();
            $statusCounts = ['all' => 0, 'pending' => 0, 'approved' => 0, 'disbursed' => 0, 'paid' => 0, 'rejected' => 0];
            $totalRemainingDebt = 0.0;
            $totalDisbursed = 0.0;
            $totalRepaid = 0.0;
            $nextInstallment = null;
        }

        return [
            'employee'           => $employee,
            'user'               => $user,
            'loans'              => $loans,
            'currentFilter'      => $status ?: 'all',
            'statusCounts'       => $statusCounts,
            'totalRemainingDebt' => (float) $totalRemainingDebt,
            'totalDisbursed'     => (float) $totalDisbursed,
            'totalRepaid'        => (float) $totalRepaid,
            'nextInstallment'    => $nextInstallment,
        ];
    }

    /**
     * Get single loan request details with installment breakdown.
     */
    public function getLoanDetails(?User $user, int $id): ?array
    {
        $employee = $this->resolveCurrentEmployee($user);

        if (!$employee) {
            return null;
        }

        $loan = Loan::where('employee_id', $employee->id)
            ->with(['installments' => function ($q) {
                $q->orderBy('installment_number');
            }, 'managerApprover', 'hrApprover'])
            ->find($id);

        if (!$loan) {
            return null;
        }

        $paidInstallmentsCount    = $loan->installments->where('status', InstallmentStatus::PAID)->count();
        $pendingInstallmentsCount = $loan->installments->where('status', InstallmentStatus::PENDING)->count();

        return [
            'loan'                     => $loan,
            'employee'                 => $employee,
            'user'                     => $user,
            'progress'                 => $loan->progress_percentage,
            'paid_installments_count'  => $paidInstallmentsCount,
            'pending_installments_count'=> $pendingInstallmentsCount,
        ];
    }
}
