<?php

namespace Modules\EmployeePortal\Services;

use App\Models\User;
use Modules\EmployeePortal\Repositories\Contracts\PortalPushSubscriptionRepositoryInterface;
use Modules\EmployeePortal\Services\Contracts\PortalPushSubscriptionServiceInterface;

class PortalPushSubscriptionService implements PortalPushSubscriptionServiceInterface
{
    public function __construct(
        private readonly PortalPushSubscriptionRepositoryInterface $repository
    ) {}

    public function subscribe(User $user, array $data): void
    {
        $this->repository->upsert($user, $data);
    }

    public function unsubscribe(User $user, string $endpoint): void
    {
        $this->repository->delete($user, hash('sha256', $endpoint));
    }
}
