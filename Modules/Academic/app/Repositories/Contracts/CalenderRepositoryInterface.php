<?php

namespace Modules\Academic\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Academic\Models\Calender;

interface CalenderRepositoryInterface
{
    public function getFilteredQuery(array $filters = []): Builder;

    public function all(array $filters = []): Collection;

    public function getCalendarEvents(array $filters = []): Collection;

    public function find(int $id): ?Calender;

    public function create(array $data): Calender;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    public function toggleStatus(int $id): bool;
}
