<?php

namespace Modules\HR\Repositories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Modules\HR\Enums\InterviewStatus;
use Modules\HR\Models\Interview;
use Modules\HR\Repositories\Contracts\InterviewRepositoryInterface;

class InterviewRepository implements InterviewRepositoryInterface
{
    public function getDataTableQuery(array $filters = []): Builder
    {
        $query = Interview::with([
            'jobApplication.candidate',
            'jobOpening.translations',
            'candidate',
            'interviewers.translations',
            'evaluations',
        ]);

        if (!empty($filters['job_opening_id'])) {
            $query->where('job_opening_id', $filters['job_opening_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['mode'])) {
            $query->where('mode', $filters['mode']);
        }

        if (!empty($filters['date'])) {
            $query->whereDate('scheduled_at', $filters['date']);
        }

        return $query;
    }

    public function getUpcomingInterviews(int $limit = 5): array|\Illuminate\Support\Collection
    {
        return Interview::with([
            'candidate',
            'jobOpening.translations',
            'interviewers.translations',
        ])
        ->where('status', InterviewStatus::SCHEDULED)
        ->where('scheduled_at', '>=', now()->startOfDay())
        ->orderBy('scheduled_at', 'asc')
        ->limit($limit)
        ->get();
    }

    public function getInterviewKpis(?int $jobOpeningId = null): array
    {
        $base = Interview::query();
        if ($jobOpeningId) {
            $base->where('job_opening_id', $jobOpeningId);
        }

        $all = (clone $base)->get();

        return [
            'total'       => $all->count(),
            'scheduled'   => $all->where('status', InterviewStatus::SCHEDULED)->count(),
            'completed'   => $all->where('status', InterviewStatus::COMPLETED)->count(),
            'today'       => $all->filter(fn ($item) => $item->scheduled_at && $item->scheduled_at->isToday())->count(),
            'cancelled'   => $all->where('status', InterviewStatus::CANCELLED)->count(),
        ];
    }

    public function findWithRelations(int $id): ?Interview
    {
        return Interview::with([
            'jobApplication.candidate',
            'jobOpening.translations',
            'candidate',
            'interviewers.translations',
            'evaluations.interviewer.translations',
            'creator',
        ])->find($id);
    }
}
