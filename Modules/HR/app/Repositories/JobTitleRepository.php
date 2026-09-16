<?php

namespace Modules\HR\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Modules\HR\Models\Department;
use Modules\HR\Models\JobTitle;
use Modules\HR\Repositories\Contracts\JobTitleRepositoryInterface;

class JobTitleRepository implements JobTitleRepositoryInterface
{
    //======================
    /**
     * Get all job titles with optional filters.
     */
    public function getAll(array $filters = [], array $with = ['translations', 'department.translations']): Collection
    {
        return $this->buildQuery($filters, $with)->get();
    }

    //======================
    /**
     * Get query builder instance for server-side DataTables.
     */
    public function getDataTableQuery(array $filters = []): Builder
    {
        $query = JobTitle::with([
            'translations',
            'department.translations',
        ])
        ->select('job_titles.*')
        ->latest('job_titles.id');

        if (!empty($filters['status'])) {
            $query->where('job_titles.status', $filters['status']);
        }

        if (!empty($filters['department_id'])) {
            $query->where('job_titles.department_id', $filters['department_id']);
        }

        return $query;
    }

    //======================
    /**
     * Find a job title by ID.
     */
    public function findById(int $id, array $with = ['translations', 'department.translations']): ?JobTitle
    {
        return JobTitle::with($with)->find($id);
    }

    //======================
    /**
     * Create a new job title with translations.
     */
    public function create(array $data): JobTitle
    {
        return DB::transaction(function () use ($data) {
            return JobTitle::create($data);
        });
    }

    //======================
    /**
     * Update an existing job title.
     */
    public function update(int|JobTitle $model, array $data): JobTitle
    {
        $jobTitle = $model instanceof JobTitle ? $model : JobTitle::findOrFail($model);

        return DB::transaction(function () use ($jobTitle, $data) {
            $jobTitle->update($data);
            return $jobTitle->fresh(['translations', 'department.translations']);
        });
    }

    //======================
    /**
     * Delete a job title.
     */
    public function delete(int|JobTitle $model): bool
    {
        $jobTitle = $model instanceof JobTitle ? $model : JobTitle::findOrFail($model);

        return DB::transaction(function () use ($jobTitle) {
            return (bool) $jobTitle->delete();
        });
    }

    //======================
    /**
     * Get departments list for dropdown.
     */
    public function getDepartmentsList(): Collection
    {
        return Department::with('translations')
            ->where('status', 'active')
            ->orderBy('id', 'desc')
            ->get();
    }

    //======================
    /**
     * Build base query.
     */
    protected function buildQuery(array $filters = [], array $with = []): Builder
    {
        $query = JobTitle::query();

        if (!empty($with)) {
            $query->with($with);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        return $query->latest('id');
    }
}
