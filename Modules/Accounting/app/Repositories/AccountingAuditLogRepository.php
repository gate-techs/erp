<?php

namespace Modules\Accounting\Repositories;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Accounting\Models\Account;
use Modules\Accounting\Models\BankReconciliation;
use Modules\Accounting\Models\CashAccount;
use Modules\Accounting\Models\CostCenter;
use Modules\Accounting\Models\Entity;
use Modules\Accounting\Models\FiscalYear;
use Modules\Accounting\Models\Journal;
use Modules\Accounting\Models\JournalEntry;
use Modules\Accounting\Models\JournalEntryLine;
use Modules\Accounting\Models\LockDate;
use Modules\Accounting\Models\LockException;
use Modules\Accounting\Models\PaymentMatching;
use Modules\Accounting\Repositories\Contracts\AccountingAuditLogRepositoryInterface;
use Modules\AuditLog\Models\ActivityLog;

class AccountingAuditLogRepository implements AccountingAuditLogRepositoryInterface
{
    /**
     * Recognized accounting models.
     */
    protected array $accountingModels = [
        JournalEntry::class,
        JournalEntryLine::class,
        Account::class,
        Journal::class,
        CashAccount::class,
        CostCenter::class,
        BankReconciliation::class,
        PaymentMatching::class,
        FiscalYear::class,
        LockDate::class,
        LockException::class,
        Entity::class,
    ];

    /**
     * Base query for accounting activity logs.
     */
    protected function baseQuery()
    {
        return ActivityLog::query()
            ->with(['causer'])
            ->where(function ($q) {
                $q->where('log_name', 'accounting')
                  ->orWhereIn('subject_type', $this->accountingModels);
            });
    }

    /**
     * Get paginated accounting activity logs with filters.
     */
    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->baseQuery();

        // 1. Search keyword
        if (!empty($filters['search'])) {
            $s = trim($filters['search']);
            $query->where(function ($q) use ($s) {
                $q->where('description', 'like', "%{$s}%")
                  ->orWhere('id', $s);
            });
        }

        // 2. User filter
        if (!empty($filters['user_id'])) {
            $query->where('causer_id', $filters['user_id']);
        }

        // 3. Event filter
        if (!empty($filters['event'])) {
            $query->where('event', $filters['event']);
        }

        // 4. Subject Type filter
        if (!empty($filters['subject_type'])) {
            $query->where('subject_type', $filters['subject_type']);
        }

        // 5. Date From
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', Carbon::parse($filters['date_from']));
        }

        // 6. Date To
        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', Carbon::parse($filters['date_to']));
        }

        return $query->latest('id')->paginate($perPage);
    }

    /**
     * Get lookups for filter dropdowns.
     */
    public function getLookups(): array
    {
        $users = User::query()
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();

        $entities = Entity::query()
            ->select('id')
            ->withTranslation()
            ->get();

        $subjectTypes = [
            JournalEntry::class       => __('Journal Entries'),
            Account::class            => __('Chart of Accounts'),
            BankReconciliation::class => __('Bank Reconciliations'),
            PaymentMatching::class    => __('Payment Matching'),
            CashAccount::class        => __('Cash & Bank Accounts'),
            CostCenter::class         => __('Cost Centers'),
            Journal::class            => __('Journals'),
            FiscalYear::class         => __('Fiscal Years'),
            LockDate::class           => __('Lock Dates'),
            Entity::class             => __('Accounting Entities'),
        ];

        $events = [
            'created'    => __('Created / Added'),
            'updated'    => __('Updated'),
            'deleted'    => __('Deleted'),
            'posted'     => __('Posted'),
            'reversed'   => __('Reversed'),
            'reconciled' => __('Reconciled'),
            'reopened'   => __('Reopened for Edit'),
            'locked'     => __('Period Locked'),
        ];

        return [
            'users'        => $users,
            'entities'     => $entities,
            'subjectTypes' => $subjectTypes,
            'events'       => $events,
        ];
    }

    /**
     * Find single log by ID.
     */
    public function findById(int $id): ?ActivityLog
    {
        return $this->baseQuery()->with(['causer', 'subject'])->find($id);
    }

    /**
     * Get KPI summary metrics.
     */
    public function getKpiMetrics(): array
    {
        $totalActivities = (clone $this->baseQuery())->count();
        $todayActivities = (clone $this->baseQuery())->whereDate('created_at', Carbon::today())->count();
        $postedEntries   = (clone $this->baseQuery())->whereIn('event', ['posted', 'approved'])->count();
        $modifications   = (clone $this->baseQuery())->where('event', 'updated')->count();

        return [
            'total'         => $totalActivities,
            'today'         => $todayActivities,
            'posted'        => $postedEntries,
            'modifications' => $modifications,
        ];
    }
}
