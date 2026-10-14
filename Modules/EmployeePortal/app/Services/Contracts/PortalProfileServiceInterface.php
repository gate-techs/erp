<?php

namespace Modules\EmployeePortal\Services\Contracts;

use App\Models\User;
use Modules\HR\Models\Employee;

interface PortalProfileServiceInterface
{
    /**
     * Get data required for employee profile screen.
     */
    public function getProfileContext(?User $user): array;

    /**
     * Get profile and edit view data for current employee/user.
     */
    public function getProfileEditData(?User $user): array;

    /**
     * Update employee personal and contact details.
     */
    public function updateProfile(array $data, ?User $user): array;
}
