<?php

namespace Modules\EmployeePortal\Repositories;

use Modules\EmployeePortal\Repositories\Contracts\PortalEmployeeRepositoryInterface;
use Modules\HR\Models\Employee;

class PortalEmployeeRepository implements PortalEmployeeRepositoryInterface
{
    /**
     * Default relationships to eager load for portal employee profile.
     */
    protected array $relations = [
        'translations',
        'department.translations',
        'jobTitle.translations',
        'primaryBranch.translations',
        'shift.translations',
        'workSchedule.days',
    ];

    /**
     * Find employee associated with a user ID.
     *
     * @param int $userId
     * @return Employee|null
     */
    public function findByUserId(int $userId): ?Employee
    {
        return Employee::with($this->relations)
            ->where('user_id', $userId)
            ->first();
    }

    /**
     * Find active employee fallback for portal testing / preview.
     *
     * @return Employee|null
     */
    public function getActiveFallback(): ?Employee
    {
        return Employee::with($this->relations)
            ->where('status', 'active')
            ->first();
    }

    /**
     * Update employee record with translations.
     *
     * @param Employee $employee
     * @param array $data
     * @return Employee
     */
    public function update(Employee $employee, array $data): Employee
    {
        $employee->update($data);
        return $employee->fresh($this->relations);
    }
}
