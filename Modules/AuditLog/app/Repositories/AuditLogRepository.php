<?php

namespace Modules\AuditLog\Repositories;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Modules\AuditLog\Models\ActivityLog;
use Modules\AuditLog\Repositories\Contracts\AuditLogRepositoryInterface;

class AuditLogRepository implements AuditLogRepositoryInterface
{
    /**
     * Get Eloquent query for DataTables.
     *
     * @param array<string, mixed> $filters
     * @return Builder
     */
    public function getDataTableQuery(array $filters = []): Builder
    {
        $query = ActivityLog::with(['causer', 'subject'])
            ->select('activity_log.*');

        // Filter by Causer / User
        if (!empty($filters['user_id'])) {
            $query->where('causer_id', $filters['user_id'])
                  ->where('causer_type', User::class);
        }

        // Filter by Event (created, updated, deleted, etc.)
        if (!empty($filters['event'])) {
            $query->where('event', $filters['event']);
        }

        // Filter by Subject Type (e.g., Employee, Payroll, Department)
        if (!empty($filters['subject_type'])) {
            $query->where('subject_type', 'LIKE', '%' . $filters['subject_type'] . '%');
        }

        // Filter by Date Range
        if (!empty($filters['start_date'])) {
            $query->whereDate('created_at', '>=', Carbon::parse($filters['start_date'])->startOfDay());
        }
        if (!empty($filters['end_date'])) {
            $query->whereDate('created_at', '<=', Carbon::parse($filters['end_date'])->endOfDay());
        }

        // Search in description or properties
        if (!empty($filters['search'])) {
            $term = is_array($filters['search']) ? ($filters['search']['value'] ?? '') : $filters['search'];
            if (!empty($term) && is_string($term)) {
                $query->where(function ($q) use ($term) {
                    $q->where('description', 'LIKE', "%{$term}%")
                      ->orWhere('event', 'LIKE', "%{$term}%")
                      ->orWhere('log_name', 'LIKE', "%{$term}%")
                      ->orWhere('subject_type', 'LIKE', "%{$term}%");
                });
            }
        }

        return $query;
    }

    /**
     * Get paginated activity logs based on filters.
     *
     * @param array<string, mixed> $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginatedLogs(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return $this->getDataTableQuery($filters)
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Find a specific activity log record by ID.
     *
     * @param int $id
     * @return ActivityLog|null
     */
    public function findById(int $id): ?ActivityLog
    {
        return ActivityLog::with(['causer', 'subject'])->find($id);
    }

    /**
     * Get summary metrics / KPIs for logs dashboard.
     *
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function getSummaryKPIs(array $filters = []): array
    {
        $today = Carbon::today();
        
        $totalLogs    = ActivityLog::count();
        $todayLogs    = ActivityLog::whereDate('created_at', $today)->count();
        $createdCount = ActivityLog::where('event', 'created')->count();
        $updatedCount = ActivityLog::where('event', 'updated')->count();
        $deletedCount = ActivityLog::where('event', 'deleted')->count();

        return compact(
            'totalLogs',
            'todayLogs',
            'createdCount',
            'updatedCount',
            'deletedCount'
        );
    }

    /**
     * Get list of unique event types and subjects for filter dropdowns.
     *
     * @return array<string, mixed>
     */
    public function getFilterLookups(): array
    {
        $events = ActivityLog::select('event')->distinct()->whereNotNull('event')->pluck('event');
        $subjects = ActivityLog::select('subject_type')->distinct()->whereNotNull('subject_type')->pluck('subject_type')->map(function ($full) {
            return [
                'full' => $full,
                'name' => class_basename($full),
            ];
        });

        $users = User::select('id', 'name', 'email')->get();

        return compact('events', 'subjects', 'users');
    }
}
