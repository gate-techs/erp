<?php

namespace Modules\HR\Repositories;

use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection as BaseCollection;
use Modules\HR\Enums\EmployeeStatus;
use Modules\HR\Enums\EmploymentType;
use Modules\HR\Enums\Gender;
use Modules\HR\Models\Employee;
use Modules\HR\Models\EmployeeDocument;
use Modules\HR\Repositories\Contracts\EmployeeReportRepositoryInterface;

class EmployeeReportRepository implements EmployeeReportRepositoryInterface
{
    /**
     * Get query builder for Employee Master List.
     *
     * @param array<string, mixed> $filters
     * @return Builder
     */
    public function getEmployeeMasterQuery(array $filters = []): Builder
    {
        $query = Employee::with([
            'translations',
            'department.translations',
            'primaryBranch.translations',
            'jobTitle.translations',
            'bank.translations',
            'manager.translations',
        ]);

        // 1. Status Filter
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // 2. Department Filter
        if (!empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        // 3. Branch Filter
        if (!empty($filters['branch_id'])) {
            $query->where('primary_branch_id', $filters['branch_id']);
        }

        // 4. Job Title Filter
        if (!empty($filters['job_title_id'])) {
            $query->where('job_title_id', $filters['job_title_id']);
        }

        // 5. Nationality Filter
        if (!empty($filters['nationality'])) {
            $query->where('nationality', $filters['nationality']);
        }

        // 6. Employment Type Filter
        if (!empty($filters['employment_type'])) {
            $query->where('employment_type', $filters['employment_type']);
        }

        // 7. Gender Filter
        if (!empty($filters['gender'])) {
            $query->where('gender', $filters['gender']);
        }

        // 8. Specific Employee Filter
        if (!empty($filters['employee_id'])) {
            $query->where('id', $filters['employee_id']);
        }

        // 9. Hire Date Range Filter
        $startDate = $filters['start_date'] ?? null;
        $endDate   = $filters['end_date'] ?? null;
        if ($startDate && $endDate) {
            $query->whereBetween('hire_date', [$startDate, $endDate]);
        } elseif ($startDate) {
            $query->whereDate('hire_date', '>=', $startDate);
        } elseif ($endDate) {
            $query->whereDate('hire_date', '<=', $endDate);
        }

        // 10. Keyword Search
        if (!empty($filters['search'])) {
            $term = $filters['search'];
            $query->where(function ($q) use ($term) {
                $q->where('employee_code', 'like', "%{$term}%")
                  ->orWhere('phone', 'like', "%{$term}%")
                  ->orWhere('email', 'like', "%{$term}%")
                  ->orWhere('national_id', 'like', "%{$term}%")
                  ->orWhereTranslationLike('name', "%{$term}%");
            });
        }

        return $query->orderBy('id', 'desc');
    }

    /**
     * Get paginated Employee Master List.
     *
     * @param array<string, mixed> $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getEmployeeMasterPaginated(array $filters = [], int $perPage = 25): LengthAwarePaginator
    {
        return $this->getEmployeeMasterQuery($filters)->paginate($perPage);
    }

    /**
     * Get all filtered Employee Master records without pagination.
     *
     * @param array<string, mixed> $filters
     * @return Collection
     */
    public function getEmployeeMasterRecords(array $filters = []): Collection
    {
        return $this->getEmployeeMasterQuery($filters)->get();
    }

    /**
     * Get summary KPI totals for Employee Master list.
     *
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function getEmployeeMasterKPIs(array $filters = []): array
    {
        $records = $this->getEmployeeMasterRecords($filters);

        $totalCount    = (int) $records->count();
        $activeCount   = (int) $records->filter(fn($e) => $e->status?->value === 'active')->count();
        $onLeaveCount  = (int) $records->filter(fn($e) => $e->status?->value === 'on_leave')->count();
        $suspendedCount= (int) $records->filter(fn($e) => $e->status?->value === 'suspended')->count();
        $resignedCount = (int) $records->filter(fn($e) => in_array($e->status?->value, ['resigned', 'terminated']))->count();

        $totalSalary   = (float) $records->sum('basic_salary');
        $avgSalary     = $totalCount > 0 ? round($totalSalary / $totalCount, 2) : 0;

        return compact(
            'totalCount',
            'activeCount',
            'onLeaveCount',
            'suspendedCount',
            'resignedCount',
            'totalSalary',
            'avgSalary'
        );
    }

    /**
     * Get all expiring and expired documents records.
     *
     * @param array<string, mixed> $filters
     * @return BaseCollection
     */
    public function getExpiringDocumentsRecords(array $filters = []): BaseCollection
    {
        $items = collect();
        $today = Carbon::now()->startOfDay();

        // 1. Fetch uploaded employee documents (table: employee_documents)
        $docQuery = EmployeeDocument::with([
            'employee.translations',
            'employee.department.translations',
            'employee.primaryBranch.translations',
            'documentType.translations',
        ])->whereNotNull('expiry_date');

        if (!empty($filters['department_id'])) {
            $docQuery->whereHas('employee', fn($q) => $q->where('department_id', $filters['department_id']));
        }
        if (!empty($filters['branch_id'])) {
            $docQuery->whereHas('employee', fn($q) => $q->where('primary_branch_id', $filters['branch_id']));
        }
        if (!empty($filters['employee_id'])) {
            $docQuery->where('employee_id', $filters['employee_id']);
        }
        if (!empty($filters['document_type_id'])) {
            $docQuery->where('document_type_id', $filters['document_type_id']);
        }

        $docs = $docQuery->get();
        foreach ($docs as $doc) {
            if (!$doc->employee) continue;

            $expiry = Carbon::parse($doc->expiry_date)->startOfDay();
            $daysRemaining = (int) $today->diffInDays($expiry, false);

            $statusKey = match (true) {
                $daysRemaining < 0   => 'expired',
                $daysRemaining <= 30 => 'expiring_30',
                $daysRemaining <= 60 => 'expiring_60',
                $daysRemaining <= 90 => 'expiring_90',
                default              => 'valid',
            };

            $statusLabel = match ($statusKey) {
                'expired'     => __('منتهية'),
                'expiring_30' => __('تنتهي خلال 30 يوم'),
                'expiring_60' => __('تنتهي خلال 60 يوم'),
                'expiring_90' => __('تنتهي خلال 90 يوم'),
                default       => __('سارية'),
            };

            $badgeClass = match ($statusKey) {
                'expired'     => 'bg-label-danger',
                'expiring_30' => 'bg-label-danger',
                'expiring_60' => 'bg-label-warning',
                'expiring_90' => 'bg-label-info',
                default       => 'bg-label-success',
            };

            $items->push((object) [
                'id'              => 'doc_' . $doc->id,
                'employee'        => $doc->employee,
                'employee_id'     => $doc->employee_id,
                'document_type'   => $doc->documentType?->name ?? __('مستند رسمي'),
                'document_number' => $doc->document_number ?: '-',
                'issue_date'      => $doc->issue_date ? Carbon::parse($doc->issue_date)->format('Y-m-d') : '-',
                'expiry_date'     => $expiry->format('Y-m-d'),
                'days_remaining'  => $daysRemaining,
                'status_key'      => $statusKey,
                'status_label'    => $statusLabel,
                'badge_class'     => $badgeClass,
                'file_url'        => $doc->file_url,
            ]);
        }

        // 2. Fetch Employee Passport, Visa, and Contract expiries from Employee model
        $empQuery = Employee::with([
            'translations',
            'department.translations',
            'primaryBranch.translations',
        ]);

        if (!empty($filters['department_id'])) {
            $empQuery->where('department_id', $filters['department_id']);
        }
        if (!empty($filters['branch_id'])) {
            $empQuery->where('primary_branch_id', $filters['branch_id']);
        }
        if (!empty($filters['employee_id'])) {
            $empQuery->where('id', $filters['employee_id']);
        }

        $employees = $empQuery->get();
        foreach ($employees as $emp) {
            // A. Passport
            if (!empty($emp->passport_expiry_date)) {
                $expiry = Carbon::parse($emp->passport_expiry_date)->startOfDay();
                $daysRemaining = (int) $today->diffInDays($expiry, false);
                $statusKey = $daysRemaining < 0 ? 'expired' : ($daysRemaining <= 30 ? 'expiring_30' : ($daysRemaining <= 60 ? 'expiring_60' : ($daysRemaining <= 90 ? 'expiring_90' : 'valid')));
                $items->push((object) [
                    'id'              => 'emp_passport_' . $emp->id,
                    'employee'        => $emp,
                    'employee_id'     => $emp->id,
                    'document_type'   => __('جواز السفر'),
                    'document_number' => $emp->passport_number ?: '-',
                    'issue_date'      => $emp->passport_issue_date ? Carbon::parse($emp->passport_issue_date)->format('Y-m-d') : '-',
                    'expiry_date'     => $expiry->format('Y-m-d'),
                    'days_remaining'  => $daysRemaining,
                    'status_key'      => $statusKey,
                    'status_label'    => $statusKey === 'expired' ? __('منتهية') : ($daysRemaining <= 30 ? __('تنتهي خلال 30 يوم') : ($daysRemaining <= 60 ? __('تنتهي خلال 60 يوم') : ($daysRemaining <= 90 ? __('تنتهي خلال 90 يوم') : __('سارية')))),
                    'badge_class'     => in_array($statusKey, ['expired', 'expiring_30']) ? 'bg-label-danger' : ($statusKey === 'expiring_60' ? 'bg-label-warning' : ($statusKey === 'expiring_90' ? 'bg-label-info' : 'bg-label-success')),
                    'file_url'        => null,
                ]);
            }

            // B. Visa / Residency (الإقامة)
            if (!empty($emp->visa_expiry_date)) {
                $expiry = Carbon::parse($emp->visa_expiry_date)->startOfDay();
                $daysRemaining = (int) $today->diffInDays($expiry, false);
                $statusKey = $daysRemaining < 0 ? 'expired' : ($daysRemaining <= 30 ? 'expiring_30' : ($daysRemaining <= 60 ? 'expiring_60' : ($daysRemaining <= 90 ? 'expiring_90' : 'valid')));
                $items->push((object) [
                    'id'              => 'emp_visa_' . $emp->id,
                    'employee'        => $emp,
                    'employee_id'     => $emp->id,
                    'document_type'   => __('الإقامة / التأشيرة'),
                    'document_number' => $emp->national_id ?: '-',
                    'issue_date'      => '-',
                    'expiry_date'     => $expiry->format('Y-m-d'),
                    'days_remaining'  => $daysRemaining,
                    'status_key'      => $statusKey,
                    'status_label'    => $statusKey === 'expired' ? __('منتهية') : ($daysRemaining <= 30 ? __('تنتهي خلال 30 يوم') : ($daysRemaining <= 60 ? __('تنتهي خلال 60 يوم') : ($daysRemaining <= 90 ? __('تنتهي خلال 90 يوم') : __('سارية')))),
                    'badge_class'     => in_array($statusKey, ['expired', 'expiring_30']) ? 'bg-label-danger' : ($statusKey === 'expiring_60' ? 'bg-label-warning' : ($statusKey === 'expiring_90' ? 'bg-label-info' : 'bg-label-success')),
                    'file_url'        => null,
                ]);
            }

            // C. Contract End Date (انتهاء العقد)
            if (!empty($emp->contract_end_date)) {
                $expiry = Carbon::parse($emp->contract_end_date)->startOfDay();
                $daysRemaining = (int) $today->diffInDays($expiry, false);
                $statusKey = $daysRemaining < 0 ? 'expired' : ($daysRemaining <= 30 ? 'expiring_30' : ($daysRemaining <= 60 ? 'expiring_60' : ($daysRemaining <= 90 ? 'expiring_90' : 'valid')));
                $items->push((object) [
                    'id'              => 'emp_contract_' . $emp->id,
                    'employee'        => $emp,
                    'employee_id'     => $emp->id,
                    'document_type'   => __('عقد العمل'),
                    'document_number' => $emp->employee_code ?: '-',
                    'issue_date'      => $emp->hire_date ? Carbon::parse($emp->hire_date)->format('Y-m-d') : '-',
                    'expiry_date'     => $expiry->format('Y-m-d'),
                    'days_remaining'  => $daysRemaining,
                    'status_key'      => $statusKey,
                    'status_label'    => $statusKey === 'expired' ? __('منتهية') : ($daysRemaining <= 30 ? __('تنتهي خلال 30 يوم') : ($daysRemaining <= 60 ? __('تنتهي خلال 60 يوم') : ($daysRemaining <= 90 ? __('تنتهي خلال 90 يوم') : __('سارية')))),
                    'badge_class'     => in_array($statusKey, ['expired', 'expiring_30']) ? 'bg-label-danger' : ($statusKey === 'expiring_60' ? 'bg-label-warning' : ($statusKey === 'expiring_90' ? 'bg-label-info' : 'bg-label-success')),
                    'file_url'        => null,
                ]);
            }
        }

        // 3. Apply Expiry Range Filter
        $expiryRange = $filters['expiry_range'] ?? 'all';
        if ($expiryRange === 'expired') {
            $items = $items->filter(fn($i) => $i->status_key === 'expired');
        } elseif ($expiryRange === '30_days') {
            $items = $items->filter(fn($i) => in_array($i->status_key, ['expired', 'expiring_30']));
        } elseif ($expiryRange === '60_days') {
            $items = $items->filter(fn($i) => in_array($i->status_key, ['expired', 'expiring_30', 'expiring_60']));
        } elseif ($expiryRange === '90_days') {
            $items = $items->filter(fn($i) => in_array($i->status_key, ['expired', 'expiring_30', 'expiring_60', 'expiring_90']));
        }

        return $items->sortBy('days_remaining')->values();
    }

    /**
     * Get paginated expiring and expired documents records.
     *
     * @param array<string, mixed> $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getExpiringDocumentsPaginated(array $filters = [], int $perPage = 25): LengthAwarePaginator
    {
        $all = $this->getExpiringDocumentsRecords($filters);
        $page = Paginator::resolveCurrentPage() ?: 1;
        $sliced = $all->slice(($page - 1) * $perPage, $perPage)->values();

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $sliced,
            $all->count(),
            $perPage,
            $page,
            ['path' => Paginator::resolveCurrentPath(), 'query' => request()->query()]
        );
    }

    /**
     * Get summary KPI totals for expiring documents.
     *
     * @param array<string, mixed> $filters
     * @return array<string, int>
     */
    public function getExpiringDocumentsKPIs(array $filters = []): array
    {
        // Unfiltered by range to get global counts
        $unfiltered = $this->getExpiringDocumentsRecords(array_merge($filters, ['expiry_range' => 'all']));

        return [
            'total_documents' => (int) $unfiltered->count(),
            'expired_count'   => (int) $unfiltered->filter(fn($d) => $d->status_key === 'expired')->count(),
            'expiring_30_days'=> (int) $unfiltered->filter(fn($d) => $d->status_key === 'expiring_30')->count(),
            'expiring_60_days'=> (int) $unfiltered->filter(fn($d) => $d->status_key === 'expiring_60')->count(),
            'expiring_90_days'=> (int) $unfiltered->filter(fn($d) => $d->status_key === 'expiring_90')->count(),
            'valid_count'     => (int) $unfiltered->filter(fn($d) => $d->status_key === 'valid')->count(),
        ];
    }

    /**
     * Get workforce demographics & cost aggregation breakdown.
     *
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function getWorkforceDemographics(array $filters = []): array
    {
        $employees = $this->getEmployeeMasterRecords($filters);
        $totalHeadcount = $employees->count();
        $totalBasicSalary = (float) $employees->sum('basic_salary');
        $avgSalary = $totalHeadcount > 0 ? round($totalBasicSalary / $totalHeadcount, 2) : 0;

        // 1. By Nationality
        $byNationality = $employees->groupBy(fn($e) => $e->nationality ?: __('غير محدد'))->map(function ($group, $nat) use ($totalHeadcount, $totalBasicSalary) {
            $count = $group->count();
            $salary = (float) $group->sum('basic_salary');
            return (object) [
                'name'        => $nat,
                'count'       => $count,
                'percentage'  => $totalHeadcount > 0 ? round(($count / $totalHeadcount) * 100, 1) : 0,
                'total_salary'=> $salary,
                'avg_salary'  => $count > 0 ? round($salary / $count, 2) : 0,
            ];
        })->sortByDesc('count')->values();

        // 2. By Department
        $byDepartment = $employees->groupBy(fn($e) => $e->department?->name ?? __('بدون قسم'))->map(function ($group, $dept) use ($totalHeadcount, $totalBasicSalary) {
            $count = $group->count();
            $salary = (float) $group->sum('basic_salary');
            return (object) [
                'name'        => $dept,
                'count'       => $count,
                'percentage'  => $totalHeadcount > 0 ? round(($count / $totalHeadcount) * 100, 1) : 0,
                'total_salary'=> $salary,
                'avg_salary'  => $count > 0 ? round($salary / $count, 2) : 0,
            ];
        })->sortByDesc('count')->values();

        // 3. By Branch
        $byBranch = $employees->groupBy(fn($e) => $e->primaryBranch?->name ?? __('الإدارة العامة / بدون فرع'))->map(function ($group, $branch) use ($totalHeadcount, $totalBasicSalary) {
            $count = $group->count();
            $salary = (float) $group->sum('basic_salary');
            return (object) [
                'name'        => $branch,
                'count'       => $count,
                'percentage'  => $totalHeadcount > 0 ? round(($count / $totalHeadcount) * 100, 1) : 0,
                'total_salary'=> $salary,
                'avg_salary'  => $count > 0 ? round($salary / $count, 2) : 0,
            ];
        })->sortByDesc('count')->values();

        // 4. By Job Title
        $byJobTitle = $employees->groupBy(fn($e) => $e->jobTitle?->name ?? __('غير محدد'))->map(function ($group, $title) use ($totalHeadcount, $totalBasicSalary) {
            $count = $group->count();
            $salary = (float) $group->sum('basic_salary');
            return (object) [
                'name'        => $title,
                'count'       => $count,
                'percentage'  => $totalHeadcount > 0 ? round(($count / $totalHeadcount) * 100, 1) : 0,
                'total_salary'=> $salary,
                'avg_salary'  => $count > 0 ? round($salary / $count, 2) : 0,
            ];
        })->sortByDesc('count')->values();

        // 5. By Gender
        $byGender = $employees->groupBy(fn($e) => $e->gender?->label() ?? __('غير محدد'))->map(function ($group, $gender) use ($totalHeadcount) {
            $count = $group->count();
            return (object) [
                'name'       => $gender,
                'count'      => $count,
                'percentage' => $totalHeadcount > 0 ? round(($count / $totalHeadcount) * 100, 1) : 0,
            ];
        })->values();

        return compact(
            'totalHeadcount',
            'totalBasicSalary',
            'avgSalary',
            'byNationality',
            'byDepartment',
            'byBranch',
            'byJobTitle',
            'byGender'
        );
    }
}
