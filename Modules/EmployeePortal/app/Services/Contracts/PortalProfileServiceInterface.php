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

    /**
     * Get data required for employee salary & allowances screen.
     */
    public function getSalaryContext(?User $user): array;

    /**
     * Get data required for employee awards & incentives log screen.
     */
    public function getRewardsContext(?User $user, ?string $filter = null): array;

    /**
     * Get data required for nursery policies & regulations screen.
     */
    public function getPoliciesContext(?User $user): array;

    /**
     * Get data required for employee notification settings screen.
     */
    public function getNotificationSettingsContext(?User $user): array;

    /**
     * Update employee notification settings.
     */
    public function updateNotificationSettings(?User $user, array $data): array;

    /**
     * Toggle a single notification preference dynamically via AJAX.
     */
    public function toggleSingleNotificationPreference(?User $user, string $key, bool $value): array;

    /**
     * Get data required for employee security & password settings screen.
     */
    public function getSecurityContext(?User $user): array;

    /**
     * Update user password with biometric verification.
     */
    public function updatePassword(?User $user, array $data): array;
}
