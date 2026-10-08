<?php

namespace Modules\Settings\Services\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface NotificationServiceInterface
{
    public function getUserNotifications(int $userId): LengthAwarePaginator;

    public function getBroadcastData(array $filters): array;

    public function sendBroadcast(array $data, int $createdBy): array;

    public function sendTemplateNotification(
        string $templateCode,
        array $variables = [],
        mixed $recipients = [],
        ?string $actionUrl = null
    ): array;
}

