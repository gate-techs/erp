<?php

namespace Modules\EmployeePortal\Services\Contracts;

use App\Models\User;
use Modules\HR\Models\Employee;

interface PortalLeaveServiceInterface
{
    /**
     * Get data required for submitting a new leave request.
     */
    public function getLeaveFormContext(?User $user): array;

    /**
     * Calculate days and check balance in real-time.
     */
    public function calculateDaysAndBalance(?User $user, string $leaveType, string $startDate, string $endDate): array;

    /**
     * Store and submit a new leave request for the employee.
     */
    public function submitLeaveRequest(array $data, ?User $user): array;
}
