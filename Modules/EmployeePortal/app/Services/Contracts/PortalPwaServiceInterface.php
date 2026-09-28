<?php

namespace Modules\EmployeePortal\Services\Contracts;

interface PortalPwaServiceInterface
{
    /**
     * Get web app manifest array.
     *
     * @return array
     */
    public function getManifest(): array;

    /**
     * Get service worker javascript string.
     *
     * @return string
     */
    public function getServiceWorkerScript(): string;
}
