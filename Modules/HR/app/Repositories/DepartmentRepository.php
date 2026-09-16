<?php

namespace Modules\HR\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Modules\HR\Enums\DepartmentStatus;
use Modules\HR\Models\Department;
use Modules\HR\Repositories\Contracts\DepartmentRepositoryInterface;
use Modules\Nursery\Models\Nursery;
use Modules\Nursery\Models\NurseryBranch;

class DepartmentRepository implements DepartmentRepositoryInterface
{
    //======================
    /**
     * Get all departments with optional filters.
     */
    public function getAll(array $filters = [], array $with = ['translations', 'parent.translations', 'nursery.translations', 'branch.translations', 'manager']): Collection
    {
        return $this->buildQuery($filters, $with)->get();
    }

    //======================
    /**
     * Get query builder instance for server-side DataTables.
     */
    public function getDataTableQuery(array $filters = []): Builder
    {
        $query = Department::with([
            'translations',
            'parent.translations',
            'nursery.translations',
            'branch.translations',
            'manager',
        ])
        ->select('departments.*')
        ->latest('departments.id');

        // Apply filters
        if (!empty($filters['status'])) {
            $query->where('departments.status', $filters['status']);
        }

        if (!empty($filters['nursery_id'])) {
            $query->where('departments.nursery_id', $filters['nursery_id']);
        }

        if (!empty($filters['branch_id'])) {
            $query->where('departments.branch_id', $filters['branch_id']);
        }

        if (isset($filters['type'])) {
            if ($filters['type'] === 'main') {
                $query->whereNull('departments.parent_id');
            } elseif ($filters['type'] === 'sub') {
                $query->whereNotNull('departments.parent_id');
            }
        }

        if (!empty($filters['parent_id'])) {
            $query->where('departments.parent_id', $filters['parent_id']);
        }

        return $query;
    }

    //======================
    /**
     * Find a department by ID.
     */
    public function findById(int $id, array $with = ['translations', 'parent.translations', 'nursery.translations', 'branch.translations', 'manager']): ?Department
    {
        return Department::with($with)->find($id);
    }

    //======================
    /**
     * Create a new department.
     */
    public function create(array $data): Department
    {
        return DB::transaction(function () use ($data) {
            return Department::create($data);
        });
    }

    //======================
    /**
     * Update an existing department.
     */
    public function update(int|Department $model, array $data): Department
    {
        return DB::transaction(function () use ($model, $data) {
            $department = $model instanceof Department ? $model : $this->findById($model);

            if (!$department) {
                throw new \InvalidArgumentException("Department not found with ID: {$model}");
            }

            $department->update($data);

            return $department->fresh(['translations', 'parent.translations', 'nursery.translations', 'branch.translations', 'manager']);
        });
    }

    //======================
    /**
     * Delete a department.
     */
    public function delete(int|Department $model): bool
    {
        return DB::transaction(function () use ($model) {
            $department = $model instanceof Department ? $model : $this->findById($model);

            if (!$department) {
                return false;
            }

            return (bool) $department->delete();
        });
    }

    //======================
    /**
     * Get main parent departments for parent selection dropdown.
     */
    public function getMainDepartmentsList(?int $excludeId = null): Collection
    {
        $query = Department::with('translations')
            ->whereNull('parent_id')
            ->where('status', DepartmentStatus::ACTIVE);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->get();
    }

    //======================
    /**
     * Get nurseries list for dropdown selection.
     */
    public function getNurseriesList(): Collection
    {
        return Nursery::with('translations')->latest('id')->get();
    }

    //======================
    /**
     * Get branches list (optionally filtered by nursery).
     */
    public function getBranchesList(?int $nurseryId = null): Collection
    {
        $query = NurseryBranch::with('translations')->latest('id');

        if ($nurseryId) {
            $query->where('nursery_id', $nurseryId);
        }

        return $query->get();
    }

    //======================
    /**
     * Get managers/users list for dropdown selection.
     */
    public function getManagersList(): Collection
    {
        return User::select('id', 'name', 'email')->latest('id')->get();
    }

    //======================
    /**
     * Build base query.
     */
    protected function buildQuery(array $filters = [], array $with = []): Builder
    {
        $query = Department::with($with);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['parent_id'])) {
            $query->where('parent_id', $filters['parent_id']);
        }

        if (!empty($filters['nursery_id'])) {
            $query->where('nursery_id', $filters['nursery_id']);
        }

        if (!empty($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }

        return $query;
    }
}
