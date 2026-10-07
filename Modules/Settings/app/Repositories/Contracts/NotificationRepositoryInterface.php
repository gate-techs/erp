<?php

namespace Modules\Settings\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\DatabaseNotification;
use Modules\Settings\Models\NotificationBatch;

interface NotificationRepositoryInterface
{
    public function getForUser(int $userId, int $perPage = 20): LengthAwarePaginator;

    public function countBatches(): int;

    public function searchBatches(?string $search, int $start, int $length): array;

    public function getUsersByAudience(string $audience): Collection;

    public function createBatch(array $data): NotificationBatch;

    public function createNotification(array $data): DatabaseNotification;

    public function createTranslations(array $translations): bool;
}
