<?php

namespace Modules\HR\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\HR\Models\Employee;

interface EmployeeRepositoryInterface
{
    //======================
    /**
     * Get all employees with optional filters.
     *
     * @param array $filters
     * @param array $with
     * @return Collection<int, Employee>
     */
    public function getAll(array $filters = [], array $with = ['translations', 'primaryBranch', 'department', 'jobTitle']): Collection;

    //======================
    /**
     * Get query builder instance for server-side DataTables.
     *
     * @param array $filters
     * @return Builder
     */
    public function getDataTableQuery(array $filters = []): Builder;

    //======================
    /**
     * Find an employee by ID with related relations.
     *
     * @param int $id
     * @param array $with
     * @return Employee|null
     */
    public function findById(int $id, array $with = ['translations', 'primaryBranch', 'department', 'jobTitle', 'manager', 'workSchedule', 'shift', 'branches', 'allowances.allowanceType', 'documents.documentType', 'user', 'bank', 'bankBranch', 'governorate', 'visaType']): ?Employee;

    //======================
    /**
     * Find an employee by code.
     *
     * @param string $code
     * @param array $with
     * @return Employee|null
     */
    public function findByCode(string $code, array $with = ['translations']): ?Employee;

    //======================
    /**
     * Find an employee by National ID.
     *
     * @param string $nationalId
     * @return Employee|null
     */
    public function findByNationalId(string $nationalId): ?Employee;

    //======================
    /**
     * Generate next sequential employee code (e.g. EMP-0001).
     *
     * @return string
     */
    public function getNextEmployeeCode(): string;

    //======================
    /**
     * Create a new employee with related branches, allowances, and documents.
     *
     * @param array $data
     * @param array $branches
     * @param array $allowances
     * @param array $documents
     * @return Employee
     */
    public function create(array $data, array $branches = [], array $allowances = [], array $documents = []): Employee;

    //======================
    /**
     * Update an existing employee.
     *
     * @param int|Employee $model
     * @param array $data
     * @param array|null $branches
     * @param array|null $allowances
     * @param array|null $documents
     * @return Employee
     */
    public function update(int|Employee $model, array $data, ?array $branches = null, ?array $allowances = null, ?array $documents = null): Employee;

    //======================
    /**
     * Delete an employee.
     *
     * @param int|Employee $model
     * @return bool
     */
    public function delete(int|Employee $model): bool;

    //======================
    /**
     * Sync assigned branches for employee.
     *
     * @param Employee $employee
     * @param array $branches
     * @return void
     */
    public function syncBranches(Employee $employee, array $branches): void;

    //======================
    /**
     * Sync allowances for employee.
     *
     * @param Employee $employee
     * @param array $allowances
     * @return void
     */
    public function syncAllowances(Employee $employee, array $allowances): void;

    //======================
    /**
     * Sync documents for employee.
     *
     * @param Employee $employee
     * @param array $documents
     * @return void
     */
    public function syncDocuments(Employee $employee, array $documents): void;
}
