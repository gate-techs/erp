<?php

namespace Modules\EmployeePortal\Services\Contracts;

use App\Models\User;
use Illuminate\Http\Request;
use Modules\HR\Models\Employee;
use Modules\HR\Models\ExitClearance;

interface PortalExitClearanceServiceInterface
{
    public function resolveCurrentEmployee(?User $user): ?Employee;

    public function getFormContext(?User $user): array;

    public function storeClearanceRequest(?User $user, array $data): array;

    public function getEmployeeClearanceHistory(?User $user, array $filters = []): array;

    public function getClearanceDetails(?User $user, int $id): ?ExitClearance;
}

