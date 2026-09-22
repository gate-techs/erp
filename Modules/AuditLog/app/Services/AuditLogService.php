<?php

namespace Modules\AuditLog\Services;

use Modules\AuditLog\Models\ActivityLog;
use Modules\AuditLog\Repositories\Contracts\AuditLogRepositoryInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditLogService
{
    public function __construct(
        protected AuditLogRepositoryInterface $repository
    ) {}

    /**
     * Get Eloquent query for DataTables.
     *
     * @param array<string, mixed> $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getLogsQuery(array $filters = [])
    {
        return $this->repository->getDataTableQuery($filters);
    }

    /**
     * Get filter lookups for dropdowns.
     *
     * @return array<string, mixed>
     */
    public function getFilterLookups(): array
    {
        return $this->repository->getFilterLookups();
    }

    /**
     * Get all data required for the audit log index view.
     *
     * @param array<string, mixed> $filters
     * @param int $perPage
     * @return array<string, mixed>
     */
    public function getIndexData(array $filters = [], int $perPage = 20): array
    {
        $lookups = $this->repository->getFilterLookups();

        return compact('lookups', 'filters');
    }

    /**
     * Get specific log details for modal diff display.
     *
     * @param int $id
     * @return array<string, mixed>
     */
    public function getLogDetails(int $id): array
    {
        $log = $this->repository->findById($id);

        if (!$log) {
            abort(404, __('سجل التدقيق غير موجود'));
        }

        $diffs = $log->getFieldDiffs();

        return compact('log', 'diffs');
    }

    /**
     * Helper to log custom domain activities easily from anywhere.
     *
     * @param string $description
     * @param mixed|null $performedOn
     * @param array<string, mixed> $properties
     * @param string $event
     * @return ActivityLog
     */
    public static function record(string $description, mixed $performedOn = null, array $properties = [], string $event = 'custom'): ActivityLog
    {
        $logger = activity()
            ->event($event)
            ->withProperties($properties);

        if ($performedOn) {
            $logger->performedOn($performedOn);
        }

        if (auth()->check()) {
            $logger->causedBy(auth()->user());
        }

        return $logger->log($description);
    }
}
