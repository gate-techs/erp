<?php

namespace Modules\EmployeePortal\Services\Contracts;

use App\Models\User;
use Modules\HR\Models\Employee;

interface PortalDocumentServiceInterface
{
    /**
     * Resolve current active employee for the user.
     */
    public function resolveCurrentEmployee(?User $user): ?Employee;

    /**
     * Get data required for employee official documents screen.
     */
    public function getDocumentsContext(?User $user): array;

    /**
     * Get data required for create / update document form screen.
     */
    public function getCreateDocumentContext(?User $user, ?int $documentId = null): array;

    /**
     * Handle document upload / update by employee.
     */
    public function uploadDocument(array $data, ?User $user): array;
}
