<?php

namespace Modules\EmployeePortal\Services\Contracts;

use App\Models\User;
use Modules\HR\Models\Employee;

interface PortalAttendanceServiceInterface
{
    /**
     * Resolve the employee associated with the given user.
     *
     * @param User|null $user
     * @return Employee|null
     */
    public function resolveCurrentEmployee(?User $user = null): ?Employee;

    /**
     * Get data required to render the post-login punch dashboard.
     *
     * @param User|null $user
     * @return array
     */
    public function getDashboardData(?User $user = null): array;

    /**
     * Record a mobile punch (check-in, check-out, or auto) with GPS coordinates.
     *
     * @param array $data
     * @param User|null $user
     * @return array
     */
    public function recordPunch(array $data, ?User $user = null): array;
}
