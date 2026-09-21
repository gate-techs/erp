<?php

namespace Modules\HR\Repositories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Modules\HR\Enums\ApplicationStage;
use Modules\HR\Enums\ApplicationStatus;
use Modules\HR\Models\JobApplication;
use Modules\HR\Repositories\Contracts\JobApplicationRepositoryInterface;

class JobApplicationRepository implements JobApplicationRepositoryInterface
{
    public function getKanbanData(?int $jobOpeningId = null, ?string $search = null): array
    {
        $query = JobApplication::with([
            'candidate',
            'jobOpening.translations',
            'jobOpening.jobTitle',
        ])
        ->where('status', ApplicationStatus::ACTIVE->value);

        if ($jobOpeningId) {
            $query->where('job_opening_id', $jobOpeningId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('application_code', 'like', "%{$search}%")
                  ->orWhereHas('candidate', function ($cq) use ($search) {
                      $cq->where('full_name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%")
                         ->orWhere('current_job_title', 'like', "%{$search}%");
                  });
            });
        }

        $applications = $query->orderBy('created_at', 'desc')->get();

        // Group by ordered stages
        $kanban = [];
        foreach (ApplicationStage::orderedStages() as $stage) {
            $kanban[$stage->value] = [
                'stage' => $stage,
                'items' => $applications->where('stage', $stage)->values(),
                'count' => $applications->where('stage', $stage)->count(),
            ];
        }

        return $kanban;
    }

    public function getDataTableQuery(array $filters = []): Builder
    {
        $query = JobApplication::with([
            'candidate',
            'jobOpening.translations',
            'jobOpening.jobTitle',
            'jobOpening.department',
        ]);

        if (!empty($filters['job_opening_id'])) {
            $query->where('job_opening_id', $filters['job_opening_id']);
        }

        if (!empty($filters['stage'])) {
            $query->where('stage', $filters['stage']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['rating'])) {
            $query->where('rating', '>=', $filters['rating']);
        }

        return $query;
    }

    public function getPipelineKpis(?int $jobOpeningId = null): array
    {
        $base = JobApplication::query();
        if ($jobOpeningId) {
            $base->where('job_opening_id', $jobOpeningId);
        }

        $all = (clone $base)->get();

        return [
            'total'       => $all->count(),
            'active'      => $all->where('status', ApplicationStatus::ACTIVE)->count(),
            'new'         => $all->where('stage', ApplicationStage::NEW)->where('status', ApplicationStatus::ACTIVE)->count(),
            'screening'   => $all->where('stage', ApplicationStage::SCREENING)->where('status', ApplicationStatus::ACTIVE)->count(),
            'interview'   => $all->where('stage', ApplicationStage::INTERVIEW)->where('status', ApplicationStatus::ACTIVE)->count(),
            'offer'       => $all->where('stage', ApplicationStage::OFFER)->where('status', ApplicationStatus::ACTIVE)->count(),
            'hired'       => $all->where('stage', ApplicationStage::HIRED)->count(),
            'rejected'    => $all->where('status', ApplicationStatus::REJECTED)->count(),
        ];
    }

    public function findById(int $id): ?JobApplication
    {
        return JobApplication::with([
            'candidate',
            'jobOpening.translations',
            'jobOpening.jobTitle',
            'jobOpening.department',
            'jobOpening.branch',
            'creator',
            'interviews.interviewers.translations',
            'interviews.evaluations',
        ])->find($id);
    }

    public function findByCode(string $code): ?JobApplication
    {
        return JobApplication::with([
            'candidate',
            'jobOpening.translations',
            'jobOpening.jobTitle',
        ])->where('application_code', $code)->first();
    }

    public function create(array $data): JobApplication
    {
        return JobApplication::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $app = JobApplication::find($id);
        return $app ? $app->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $app = JobApplication::find($id);
        return $app ? (bool) $app->delete() : false;
    }

    public function generateApplicationCode(): string
    {
        $year   = Carbon::now()->year;
        $prefix = "APP-{$year}-";

        $last = JobApplication::withTrashed()
            ->where('application_code', 'like', "{$prefix}%")
            ->orderByDesc('id')
            ->value('application_code');

        $nextNumber = 1;
        if ($last) {
            $lastSeq = (int) substr($last, strlen($prefix));
            $nextNumber = $lastSeq + 1;
        }

        return $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
