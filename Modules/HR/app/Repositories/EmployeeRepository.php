<?php

namespace Modules\HR\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Modules\HR\Models\Employee;
use Modules\HR\Models\EmployeeAllowance;
use Modules\HR\Models\EmployeeBranch;
use Modules\HR\Models\EmployeeDocument;
use Modules\HR\Repositories\Contracts\EmployeeRepositoryInterface;

class EmployeeRepository implements EmployeeRepositoryInterface
{
    //======================
    /**
     * Get all employees with optional filters.
     */
    public function getAll(array $filters = [], array $with = ['translations', 'primaryBranch', 'department', 'jobTitle']): Collection
    {
        $query = Employee::query()->with($with);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['primary_branch_id'])) {
            $query->where('primary_branch_id', $filters['primary_branch_id']);
        }

        if (!empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        if (!empty($filters['job_title_id'])) {
            $query->where('job_title_id', $filters['job_title_id']);
        }

        if (!empty($filters['employment_type'])) {
            $query->where('employment_type', $filters['employment_type']);
        }

        return $query->latest('id')->get();
    }

    //======================
    /**
     * Get query builder instance for server-side DataTables.
     */
    public function getDataTableQuery(array $filters = []): Builder
    {
        $query = Employee::query()
            ->select('employees.*')
            ->with([
                'translations',
                'primaryBranch.translations',
                'department.translations',
                'jobTitle.translations',
                'manager.translations',
                'shift.translations',
                'workSchedule.translations',
            ])
            ->withCount('documents');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['branch_id'])) {
            $query->byBranch((int) $filters['branch_id']);
        }

        if (!empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        if (!empty($filters['job_title_id'])) {
            $query->where('job_title_id', $filters['job_title_id']);
        }

        if (!empty($filters['employment_type'])) {
            $query->where('employment_type', $filters['employment_type']);
        }

        if (!empty($filters['gender'])) {
            $query->where('gender', $filters['gender']);
        }

        if (!empty($filters['nationality'])) {
            $query->where('nationality', $filters['nationality']);
        }

        if (!empty($filters['work_schedule_id'])) {
            $query->where('work_schedule_id', $filters['work_schedule_id']);
        }

        if (!empty($filters['shift_id'])) {
            $query->where('shift_id', $filters['shift_id']);
        }

        if (!empty($filters['marital_status'])) {
            $query->where('marital_status', $filters['marital_status']);
        }

        if (!empty($filters['hire_date_from'])) {
            $query->whereDate('hire_date', '>=', $filters['hire_date_from']);
        }

        if (!empty($filters['hire_date_to'])) {
            $query->whereDate('hire_date', '<=', $filters['hire_date_to']);
        }

        $searchTerm = null;
        if (!empty($filters['search'])) {
            $searchTerm = is_array($filters['search']) ? ($filters['search']['value'] ?? null) : $filters['search'];
        }

        if (!empty($searchTerm) && is_string($searchTerm)) {
            $query->where(function (Builder $q) use ($searchTerm) {
                $q->whereTranslationLike('name', "%{$searchTerm}%")
                  ->orWhere('employee_code', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('phone', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('email', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('national_id', 'LIKE', "%{$searchTerm}%");
            });
        }

        return $query;
    }

    //======================
    /**
     * Find an employee by ID with related relations.
     */
    public function findById(int $id, array $with = ['translations', 'primaryBranch', 'department', 'jobTitle', 'manager', 'workSchedule', 'shift', 'branches', 'allowances.allowanceType', 'documents.documentType', 'user', 'bank', 'bankBranch', 'governorate', 'visaType']): ?Employee
    {
        return Employee::query()->with($with)->find($id);
    }

    //======================
    /**
     * Find an employee by code.
     */
    public function findByCode(string $code, array $with = ['translations']): ?Employee
    {
        return Employee::query()->with($with)->where('employee_code', $code)->first();
    }

    //======================
    /**
     * Find an employee by National ID.
     */
    public function findByNationalId(string $nationalId): ?Employee
    {
        return Employee::query()->where('national_id', $nationalId)->first();
    }

    //======================
    /**
     * Generate next sequential employee code (e.g. EMP-0001).
     */
    public function getNextEmployeeCode(): string
    {
        $lastEmployee = Employee::withTrashed()->latest('id')->first();
        $nextId = $lastEmployee ? ($lastEmployee->id + 1) : 1;

        return sprintf('EMP-%04d', $nextId);
    }

    //======================
    /**
     * Create a new employee with related branches, allowances, and documents.
     */
    public function create(array $data, array $branches = [], array $allowances = [], array $documents = []): Employee
    {
        return DB::transaction(function () use ($data, $branches, $allowances, $documents) {
            if (empty($data['employee_code'])) {
                $data['employee_code'] = $this->getNextEmployeeCode();
            }

            $employee = Employee::create($data);

            if (!empty($branches)) {
                $this->syncBranches($employee, $branches);
            }

            if (!empty($allowances)) {
                $this->syncAllowances($employee, $allowances);
            }

            if (!empty($documents)) {
                $this->syncDocuments($employee, $documents);
            }

            return $employee->load([
                'translations',
                'primaryBranch',
                'department',
                'jobTitle',
                'branches',
                'allowances.allowanceType',
                'documents.documentType',
            ]);
        });
    }

    //======================
    /**
     * Update an existing employee.
     */
    public function update(int|Employee $model, array $data, ?array $branches = null, ?array $allowances = null, ?array $documents = null): Employee
    {
        return DB::transaction(function () use ($model, $data, $branches, $allowances, $documents) {
            $employee = $model instanceof Employee ? $model : $this->findById($model);

            if (!$employee) {
                throw new \InvalidArgumentException('Employee not found for update.');
            }

            $employee->update($data);

            if ($branches !== null) {
                $this->syncBranches($employee, $branches);
            }

            if ($allowances !== null) {
                $this->syncAllowances($employee, $allowances);
            }

            if ($documents !== null) {
                $this->syncDocuments($employee, $documents);
            }

            return $employee->load([
                'translations',
                'primaryBranch',
                'department',
                'jobTitle',
                'branches',
                'allowances.allowanceType',
                'documents.documentType',
            ]);
        });
    }

    //======================
    /**
     * Delete an employee.
     */
    public function delete(int|Employee $model): bool
    {
        return DB::transaction(function () use ($model) {
            $employee = $model instanceof Employee ? $model : $this->findById($model);

            if (!$employee) {
                return false;
            }

            return (bool) $employee->delete();
        });
    }

    //======================
    /**
     * Sync assigned branches for employee.
     */
    public function syncBranches(Employee $employee, array $branches): void
    {
        EmployeeBranch::where('employee_id', $employee->id)->delete();

        $primaryBranchId = $employee->primary_branch_id;
        $seen = [];

        foreach ($branches as $branch) {
            $branchId = is_array($branch) ? ($branch['branch_id'] ?? null) : $branch;
            if (!$branchId || in_array($branchId, $seen)) {
                continue;
            }

            $seen[] = $branchId;
            $isPrimary = is_array($branch) 
                ? (!empty($branch['is_primary']) || $branchId == $primaryBranchId)
                : ($branchId == $primaryBranchId);

            EmployeeBranch::create([
                'employee_id' => $employee->id,
                'branch_id'   => (int) $branchId,
                'is_primary'  => $isPrimary,
            ]);
        }

        // Ensure primary branch is included if defined and not already added
        if ($primaryBranchId && !in_array($primaryBranchId, $seen)) {
            EmployeeBranch::create([
                'employee_id' => $employee->id,
                'branch_id'   => (int) $primaryBranchId,
                'is_primary'  => true,
            ]);
        }
    }

    //======================
    /**
     * Sync allowances for employee.
     */
    public function syncAllowances(Employee $employee, array $allowances): void
    {
        EmployeeAllowance::where('employee_id', $employee->id)->delete();

        foreach ($allowances as $item) {
            if (empty($item['allowance_type_id']) || !isset($item['amount'])) {
                continue;
            }

            EmployeeAllowance::create([
                'employee_id'       => $employee->id,
                'allowance_type_id' => (int) $item['allowance_type_id'],
                'amount'            => (float) $item['amount'],
                'is_percentage'     => !empty($item['is_percentage']),
                'is_recurring'      => !empty($item['is_recurring']),
                'effective_date'    => !empty($item['effective_date']) ? $item['effective_date'] : now()->toDateString(),
                'notes'             => $item['notes'] ?? null,
            ]);
        }
    }

    //======================
    /**
     * Sync documents for employee.
     */
    public function syncDocuments(Employee $employee, array $documents): void
    {
        EmployeeDocument::where('employee_id', $employee->id)->delete();

        foreach ($documents as $item) {
            if (empty($item['file_path'])) {
                continue;
            }

            EmployeeDocument::create([
                'employee_id'        => $employee->id,
                'document_type_id'   => !empty($item['document_type_id']) ? (int) $item['document_type_id'] : null,
                'document_number'    => $item['document_number'] ?? null,
                'file_path'          => $item['file_path'],
                'file_name'          => $item['file_name'] ?? basename($item['file_path']),
                'issue_date'         => $item['issue_date'] ?? null,
                'expiry_date'        => $item['expiry_date'] ?? null,
                'notify_before_days' => (int) ($item['notify_before_days'] ?? 30),
                'notes'              => $item['notes'] ?? null,
            ]);
        }
    }
}
