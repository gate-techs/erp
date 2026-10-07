<?php

namespace Modules\Settings\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface PushSubscriptionRepositoryInterface
{
    public function forUsers(array $userIds): Collection;

    public function deleteByEndpointHash(string $endpointHash): void;
}
