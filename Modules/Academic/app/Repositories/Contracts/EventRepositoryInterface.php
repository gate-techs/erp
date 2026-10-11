<?php

namespace Modules\Academic\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Academic\Models\Event;

interface EventRepositoryInterface
{
    //------------------------------
    public function getFilteredQuery(array $filters = []): Builder;

    //------------------------------
    public function all(array $filters = []): Collection;

    //------------------------------
    public function getCalendarEvents(array $filters = []): Collection;

    //------------------------------
    public function find(int $id): ?Event;

    //------------------------------
    public function create(array $data): Event;

    //------------------------------
    public function update(int $id, array $data): bool;

    //------------------------------
    public function delete(int $id): bool;

    //------------------------------
    public function toggleStatus(int $id): bool;
}
