<?php

namespace Modules\EmployeePortal\Services\Contracts;

use App\Models\User;

interface PortalPushSubscriptionServiceInterface
{
    public function subscribe(User $user, array $data): void;

    public function unsubscribe(User $user, string $endpoint): void;
}
