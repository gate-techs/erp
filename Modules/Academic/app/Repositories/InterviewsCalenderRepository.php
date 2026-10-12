<?php

namespace Modules\Academic\Repositories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Academic\Models\AcademicGuardian;
use Modules\Academic\Models\GuardianInterview;
use Modules\Academic\Repositories\Contracts\InterviewsCalenderRepositoryInterface;

class InterviewsCalenderRepository implements InterviewsCalenderRepositoryInterface
{
    //------------------------------
    public function getFilteredQuery(int $userId, array $filters = []): Builder
    {
        $query = GuardianInterview::with(['guardian.translations', 'institution.translations', 'branch.translations'])
            ->where('user_id', $userId);

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['institution_id'])) {
            $query->where('institution_id', $filters['institution_id']);
        }

        if (! empty($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }

        if (! empty($filters['search'])) {
            $searchTerm = $filters['search'];
            $query->where(function ($q) use ($searchTerm) {
                $q->where('guardian_name', 'like', "%{$searchTerm}%")
                  ->orWhere('guardian_civil_id', 'like', "%{$searchTerm}%")
                  ->orWhere('guardian_phone', 'like', "%{$searchTerm}%")
                  ->orWhere('notes', 'like', "%{$searchTerm}%");
            });
        }

        return $query;
    }

    //------------------------------
    public function all(int $userId, array $filters = []): Collection
    {
        return $this->getFilteredQuery($userId, $filters)
            ->orderBy('interview_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();
    }

    //------------------------------
    public function getCalendarEvents(int $userId, array $filters = []): Collection
    {
        $query = GuardianInterview::with(['guardian.translations', 'institution.translations', 'branch.translations'])
            ->where('user_id', $userId);

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['institution_id'])) {
            $query->where('institution_id', $filters['institution_id']);
        }

        if (! empty($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }

        if (! empty($filters['start']) && ! empty($filters['end'])) {
            $start = Carbon::parse($filters['start'])->toDateString();
            $end   = Carbon::parse($filters['end'])->toDateString();
            $query->whereBetween('interview_date', [$start, $end]);
        }

        return $query->orderBy('interview_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();
    }

    //------------------------------
    public function getThisMonthInterviews(int $userId): Collection
    {
        return GuardianInterview::with(['guardian.translations', 'institution.translations', 'branch.translations'])
            ->where('user_id', $userId)
            ->thisMonth()
            ->orderBy('interview_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();
    }

    //------------------------------
    public function getUpcomingInterviews(int $userId, int $limit = 30): Collection
    {
        return GuardianInterview::with(['guardian.translations', 'institution.translations', 'branch.translations'])
            ->where('user_id', $userId)
            ->upcoming()
            ->orderBy('interview_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->limit($limit)
            ->get();
    }

    //------------------------------
    public function find(int $id, ?int $userId = null): ?GuardianInterview
    {
        $query = GuardianInterview::with(['guardian.translations', 'institution.translations', 'branch.translations']);

        if ($userId !== null) {
            $query->where('user_id', $userId);
        }

        return $query->find($id);
    }

    //------------------------------
    public function findByCivilId(string $civilId): ?AcademicGuardian
    {
        return AcademicGuardian::with(['translations', 'institution.translations', 'branch.translations'])
            ->where('civil_id', trim($civilId))
            ->first();
    }

    //------------------------------
    public function create(array $data): GuardianInterview
    {
        return GuardianInterview::create($data);
    }

    //------------------------------
    public function update(int $id, array $data, ?int $userId = null): bool
    {
        $interview = $this->find($id, $userId);
        if (! $interview) {
            return false;
        }

        return (bool) $interview->update($data);
    }

    //------------------------------
    public function delete(int $id, ?int $userId = null): bool
    {
        $interview = $this->find($id, $userId);
        if (! $interview) {
            return false;
        }

        return (bool) $interview->delete();
    }

    //------------------------------
    public function updateStatus(int $id, string $status, ?int $userId = null): bool
    {
        $interview = $this->find($id, $userId);
        if (! $interview) {
            return false;
        }

        return (bool) $interview->update(['status' => $status]);
    }
}
