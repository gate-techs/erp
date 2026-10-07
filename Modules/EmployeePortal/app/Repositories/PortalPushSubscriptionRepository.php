<?php

namespace Modules\EmployeePortal\Repositories;

use App\Models\User;
use Modules\EmployeePortal\Repositories\Contracts\PortalPushSubscriptionRepositoryInterface;
use Modules\Settings\Models\PushSubscription;

class PortalPushSubscriptionRepository implements PortalPushSubscriptionRepositoryInterface
{
    public function upsert(User $user, array $data): PushSubscription
    {
        $endpointHash = hash('sha256', $data['endpoint']);

        return PushSubscription::updateOrCreate(
            ['endpoint_hash' => $endpointHash],
            [
                'user_id' => $user->id,
                'endpoint' => $data['endpoint'],
                'endpoint_hash' => $endpointHash,
                'public_key' => $data['public_key'],
                'auth_token' => $data['auth_token'],
                'content_encoding' => $data['content_encoding'] ?? 'aes128gcm',
                'last_used_at' => now(),
            ]
        );
    }

    public function delete(User $user, string $endpointHash): void
    {
        PushSubscription::query()
            ->where('user_id', $user->id)
            ->where('endpoint_hash', $endpointHash)
            ->delete();
    }
}
