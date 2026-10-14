<?php

namespace Modules\EmployeePortal\Services\Contracts;

use App\Models\User;
use Modules\HR\Models\Employee;

interface PortalPermissionServiceInterface
{
    /**
     * Resolve current active employee for the user.
     */
    public function resolveCurrentEmployee(?User $user): ?Employee;

    /**
     * Get data required for submitting a new permission request.
     */
    public function getPermissionFormContext(?User $user): array;

    /**
     * Calculate duration, remaining hours, and validate policies in real-time.
     */
    public function calculateDurationAndBalance(?User $user, array $params): array;

    /**
     * Store and submit a new permission request for the employee.
     */
    public function submitPermissionRequest(array $data, ?User $user): array;
}
