<?php

namespace Modules\HR\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\HR\Models\ExitClearance;
use Modules\HR\Models\ExitClearanceApproval;
use Modules\HR\Models\HrWorkPolicy;
use Modules\HR\Repositories\Contracts\ExitClearanceRepositoryInterface;

class ExitClearanceRepository implements ExitClearanceRepositoryInterface
{
    public function getQuery(array $filters = []): Builder
    {
        $query = ExitClearance::query()->with([
            'employee' => function ($q) {
                $q->with('department', 'jobTitle', 'translations');
            },
            'department',
            'jobTitle',
            'approvals',
            'creator',
            'finalApprover',
        ])->latest('id');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        if (!empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        if (!empty($filters['branch_id'])) {
            $query->whereHas('employee', function ($q) use ($filters) {
                $q->where('primary_branch_id', $filters['branch_id']);
            });
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('last_working_day', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('last_working_day', '<=', $filters['date_to']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('clearance_code', 'like', "%{$search}%")
                  ->orWhereHas('employee', function ($eq) use ($search) {
                      $eq->where('employee_code', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%")
                         ->orWhereHas('translations', function ($tq) use ($search) {
                             $tq->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%");
                         });
                  });
            });
        }

        return $query;
    }

    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->getQuery($filters)->paginate($perPage);
    }

    public function findById(int $id): ?ExitClearance
    {
        return ExitClearance::with([
            'employee' => function ($q) {
                $q->with('department', 'jobTitle', 'manager');
            },
            'approvals.approver',
            'creator',
            'finalApprover',
        ])->find($id);
    }

    public function findByCode(string $code): ?ExitClearance
    {
        return ExitClearance::where('clearance_code', $code)->first();
    }

    public function create(array $data): ExitClearance
    {
        return ExitClearance::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $clearance = ExitClearance::findOrFail($id);
        return $clearance->update($data);
    }

    public function delete(int $id): bool
    {
        $clearance = ExitClearance::findOrFail($id);
        return $clearance->delete();
    }

    public function generateClearanceCode(): string
    {
        $year = date('Y');
        $prefix = "CLR-{$year}-";

        $last = ExitClearance::where('clearance_code', 'like', "{$prefix}%")
            ->orderByDesc('id')
            ->value('clearance_code');

        if ($last && preg_match('/(\d+)$/', $last, $matches)) {
            $nextNumber = str_pad((int) $matches[1] + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        return $prefix . $nextNumber;
    }

    public function createDefaultApprovals(ExitClearance $clearance, array $customChecklist = []): Collection
    {
        $workflowPolicy = HrWorkPolicy::where('key', 'clearance_approval_workflow')->value('value') ?? 'manager_it_finance_hr';

        $availableDepartments = [
            'manager' => [
                'name' => 'Direct Manager / Supervisor',
                'name_ar' => 'المدير المباشر (تسليم المهام والملفات)',
                'items' => ['Handover of projects and ongoing tasks', 'Files and documentation transferred', 'Client & partner contacts handover'],
                'sort' => 1,
            ],
            'it' => [
                'name' => 'Information Technology (IT)',
                'name_ar' => 'تقنية المعلومات (الأجهزة والبريد والحسابات)',
                'items' => ['Laptop / PC handed over', 'Company Email deactivated', 'ERP and System Accounts revoked', 'Access Cards & VPN access deleted'],
                'sort' => 2,
            ],
            'finance' => [
                'name' => 'Finance & Accounting',
                'name_ar' => 'الإدارة المالية (العهد النقدية والسلف)',
                'items' => ['Petty cash settled', 'Outstanding loan balances checked', 'Company credit card returned', 'Expense claims closed'],
                'sort' => 3,
            ],
            'facilities' => [
                'name' => 'Administration & Facilities',
                'name_ar' => 'الشؤون الإدارية والمرافق (الأصول والمفاتيح)',
                'items' => ['Office keys handed over', 'Access badge returned', 'Company vehicle / SIM card returned'],
                'sort' => 4,
            ],
            'hr' => [
                'name' => 'Human Resources (HR)',
                'name_ar' => 'الموارد البشرية (مستحقات نهاية الخدمة والوثائق)',
                'items' => ['Medical insurance card returned', 'Exit interview completed', 'End of service calculation verified', 'Official work files archived'],
                'sort' => 5,
            ],
        ];

        $selectedKeys = match ($workflowPolicy) {
            'manager_hr'         => ['manager', 'hr'],
            'manager_finance_hr' => ['manager', 'finance', 'hr'],
            'hr_only'            => ['hr'],
            default              => ['manager', 'it', 'finance', 'facilities', 'hr'],
        };

        $created = new Collection();
        foreach ($selectedKeys as $key) {
            if (isset($availableDepartments[$key])) {
                $dept = $availableDepartments[$key];
                $created->push(ExitClearanceApproval::create([
                    'exit_clearance_id' => $clearance->id,
                    'department_key'    => $key,
                    'department_name'   => app()->getLocale() === 'ar' ? $dept['name_ar'] : $dept['name'],
                    'status'            => 'pending',
                    'dues_amount'       => 0.00,
                    'items_checklist'   => $dept['items'],
                    'sort_order'        => $dept['sort'],
                ]));
            }
        }

        return $created;
    }

    public function updateApproval(int $approvalId, array $data): bool
    {
        $approval = ExitClearanceApproval::findOrFail($approvalId);
        return $approval->update($data);
    }

    public function getKPIs(): array
    {
        $total = ExitClearance::count();
        $pending = ExitClearance::where('status', 'pending')->count();
        $inProgress = ExitClearance::where('status', 'in_progress')->count();
        $cleared = ExitClearance::where('status', 'cleared')->count();

        return [
            'total'       => $total,
            'pending'     => $pending,
            'in_progress' => $inProgress,
            'cleared'     => $cleared,
        ];
    }
}
