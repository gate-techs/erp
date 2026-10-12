<?php

namespace Modules\Academic\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Academic\Models\AcademicGuardian;
use Modules\Academic\Models\GuardianInterview;

interface InterviewsCalenderRepositoryInterface
{
    public function getFilteredQuery(int $userId, array $filters = []): Builder;

    public function all(int $userId, array $filters = []): Collection;

    public function getCalendarEvents(int $userId, array $filters = []): Collection;

    public function getThisMonthInterviews(int $userId): Collection;

    public function getUpcomingInterviews(int $userId, int $limit = 30): Collection;

    public function find(int $id, ?int $userId = null): ?GuardianInterview;

    public function findByCivilId(string $civilId): ?AcademicGuardian;

    public function create(array $data): GuardianInterview;

    public function update(int $id, array $data, ?int $userId = null): bool;

    public function delete(int $id, ?int $userId = null): bool;

    public function updateStatus(int $id, string $status, ?int $userId = null): bool;
}
