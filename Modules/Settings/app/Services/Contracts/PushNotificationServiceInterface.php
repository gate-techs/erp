<?php

namespace Modules\Settings\Services\Contracts;

interface PushNotificationServiceInterface
{
    public function sendToUsers(array $userIds, array $payload): array;
}
