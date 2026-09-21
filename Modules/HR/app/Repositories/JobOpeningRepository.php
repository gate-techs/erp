<?php

namespace Modules\HR\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Modules\HR\Enums\JobOpeningStatus;
use Modules\HR\Models\JobOpening;
use Modules\HR\Repositories\Contracts\JobOpeningRepositoryInterface;

class JobOpeningRepository implements JobOpeningRepositoryInterface
{
    /**
     * Get query builder instance for server-side DataTables.
     */
    public function getDataTableQuery(array $filters = []): Builder
    {
        $query = JobOpening::query()
            ->with([
                'department.translations',
                'jobTitle.translations',
                'branch',
                'requisition',
            ]);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        if (!empty($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }

        if (!empty($filters['employment_type'])) {
            $query->where('employment_type', $filters['employment_type']);
        }

        if (!empty($filters['workplace_type'])) {
            $query->where('workplace_type', $filters['workplace_type']);
        }

        if (!empty($filters['posting_type'])) {
            $query->where('posting_type', $filters['posting_type']);
        }

        $search = is_array($filters['search'] ?? null)
            ? ($filters['search']['value'] ?? null)
            : ($filters['search'] ?? null);

        if (!empty($search)) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('opening_code', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhereHas('jobTitle.translations', function ($t) use ($search) {
                      $t->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('department.translations', function ($t) use ($search) {
                      $t->where('name', 'like', "%{$search}%");
                  });
            });
        }

        return $query->latest('id');
    }

    /**
     * Find a job opening by ID with relations.
     */
    public function findById(int $id, array $with = ['department.translations', 'jobTitle.translations', 'branch', 'requisition', 'creator', 'publisher']): ?JobOpening
    {
        return JobOpening::with($with)->find($id);
    }

    /**
     * Create a new job opening.
     */
    public function create(array $data): JobOpening
    {
        return JobOpening::create($data);
    }

    /**
     * Update an existing job opening.
     */
    public function update(int|JobOpening $opening, array $data): JobOpening
    {
        $instance = $opening instanceof JobOpening ? $opening : $this->findById($opening);
        $instance->update($data);
        return $instance;
    }

    /**
     * Delete a job opening (soft-delete).
     */
    public function delete(int|JobOpening $opening): bool
    {
        $instance = $opening instanceof JobOpening ? $opening : $this->findById($opening);
        return (bool) $instance?->delete();
    }

    /**
     * Generate unique opening code for current year (e.g. JOB-2026-0001).
     */
    public function generateOpeningCode(): string
    {
        $year   = date('Y');
        $prefix = "JOB-{$year}-";

        $last = JobOpening::withTrashed()
            ->where('opening_code', 'like', "{$prefix}%")
            ->orderByDesc('id')
            ->value('opening_code');

        $nextSeq = 1;
        if ($last && preg_match('/-(\d+)$/', $last, $m)) {
            $nextSeq = ((int) $m[1]) + 1;
        }

        return sprintf('%s%04d', $prefix, $nextSeq);
    }

    /**
     * Get dashboard / index stat KPI metrics.
     */
    public function getKPIs(): array
    {
        $total           = JobOpening::count();
        $published       = JobOpening::where('status', JobOpeningStatus::PUBLISHED->value)->count();
        $draft           = JobOpening::where('status', JobOpeningStatus::DRAFT->value)->count();
        $closed          = JobOpening::where('status', JobOpeningStatus::CLOSED->value)->count();
        $openPositions   = (int) JobOpening::whereIn('status', [JobOpeningStatus::PUBLISHED->value, JobOpeningStatus::DRAFT->value])->sum('open_positions');
        $filledPositions = (int) JobOpening::sum('filled_positions');

        return [
            'total'            => $total,
            'published'        => $published,
            'draft'            => $draft,
            'closed'           => $closed,
            'open_positions'   => $openPositions,
            'filled_positions' => $filledPositions,
        ];
    }
}
