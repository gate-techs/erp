<?php

namespace Modules\EmployeePortal\Repositories\Contracts;

use Modules\HR\Models\Employee;

interface PortalEmployeeRepositoryInterface
{
    /**
     * Find employee associated with a user ID.
     *
     * @param int $userId
     * @return Employee|null
     */
    public function findByUserId(int $userId): ?Employee;

    /**
     * Find active employee fallback for portal testing / preview.
     *
     * @return Employee|null
     */
    public function getActiveFallback(): ?Employee;

    /**
     * Update employee record with translations.
     *
     * @param Employee $employee
     * @param array $data
     * @return Employee
     */
    public function update(Employee $employee, array $data): Employee;
}
