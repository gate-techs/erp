<?php

namespace Modules\EmployeePortal\Services\Contracts;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Modules\HR\Models\Employee;

interface PortalLoanServiceInterface
{
    /**
     * Resolve employee from current user.
     */
    public function resolveCurrentEmployee(?User $user): ?Employee;

    /**
     * Get data required for submitting a new loan / salary advance request.
     */
    public function getLoanFormContext(?User $user): array;

    /**
     * AJAX real-time eligibility & installment calculation for loan request.
     */
    public function calculateLoanEligibility(?User $user, array $params): array;

    /**
     * Handle Loan / Salary Advance Request Submission.
     */
    public function submitLoanRequest(array $data, ?User $user, ?UploadedFile $attachment = null): array;

    /**
     * Get data required for loans & advances history screen.
     */
    public function getLoansHistoryContext(?User $user, ?string $status = null): array;

    /**
     * Get single loan request details with installment breakdown.
     */
    public function getLoanDetails(?User $user, int $id): ?array;
}
