<?php

namespace Modules\Academic\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Academic\Models\PersonalCalendar;

interface PersonalCalendarRepositoryInterface
{
    //------------------------------
    public function getFilteredQuery(int $userId, array $filters = []): Builder;

    //------------------------------
    public function all(int $userId, array $filters = []): Collection;

    //------------------------------
    public function getCalendarEvents(int $userId, array $filters = []): Collection;

    //------------------------------
    public function find(int $id, ?int $userId = null): ?PersonalCalendar;

    //------------------------------
    public function create(array $data): PersonalCalendar;

    //------------------------------
    public function update(int $id, array $data, ?int $userId = null): bool;

    //------------------------------
    public function delete(int $id, ?int $userId = null): bool;

    //------------------------------
    public function updateStatus(int $id, string $status, ?int $userId = null): bool;
}
