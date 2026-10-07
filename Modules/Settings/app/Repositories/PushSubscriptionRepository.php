<?php

namespace Modules\Settings\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Modules\Settings\Models\PushSubscription;
use Modules\Settings\Repositories\Contracts\PushSubscriptionRepositoryInterface;

class PushSubscriptionRepository implements PushSubscriptionRepositoryInterface
{
    public function forUsers(array $userIds): Collection
    {
        return PushSubscription::query()->whereIn('user_id', $userIds)->get();
    }

    public function deleteByEndpointHash(string $endpointHash): void
    {
        PushSubscription::query()->where('endpoint_hash', $endpointHash)->delete();
    }
}
