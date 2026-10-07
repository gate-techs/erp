<?php

namespace Modules\EmployeePortal\Repositories\Contracts;

use App\Models\User;
use Modules\Settings\Models\PushSubscription;

interface PortalPushSubscriptionRepositoryInterface
{
    public function upsert(User $user, array $data): PushSubscription;

    public function delete(User $user, string $endpointHash): void;
}
