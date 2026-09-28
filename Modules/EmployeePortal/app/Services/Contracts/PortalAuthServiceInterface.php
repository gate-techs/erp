<?php

namespace Modules\EmployeePortal\Services\Contracts;

interface PortalAuthServiceInterface
{
    /**
     * Attempt to log in with email and password credentials.
     *
     * @param array $credentials
     * @param bool $remember
     * @return array
     */
    public function attemptLogin(array $credentials, bool $remember = false): array;

    /**
     * Invalidate current session and log out.
     *
     * @return void
     */
    public function logout(): void;
}
