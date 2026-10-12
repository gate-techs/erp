<?php

namespace Modules\Academic\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Academic\Models\MeetingCalendar;

interface MeetingCalendarRepositoryInterface
{
    public function getFilteredQuery(int $userId, array $filters = []): Builder;

    public function all(int $userId, array $filters = []): Collection;

    public function getCalendarEvents(int $userId, array $filters = []): Collection;

    public function getTodayMeetings(int $userId): Collection;

    public function getUpcomingMeetings(int $userId, int $limit = 10): Collection;

    public function find(int $id, ?int $userId = null): ?MeetingCalendar;

    public function create(array $data): MeetingCalendar;

    public function update(int $id, array $data, ?int $userId = null): bool;

    public function delete(int $id, ?int $userId = null): bool;

    public function updateStatus(int $id, string $status, ?int $userId = null): bool;
}
