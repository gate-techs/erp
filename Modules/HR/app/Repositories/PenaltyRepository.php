<?php

namespace Modules\HR\Repositories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Modules\HR\Enums\PenaltyStatus;
use Modules\HR\Models\Penalty;
use Modules\HR\Repositories\Contracts\PenaltyRepositoryInterface;

class PenaltyRepository implements PenaltyRepositoryInterface
{
    /**
     * Get DataTable query with applied filters.
     */
    public function getDataTableQuery(array $filters = []): Builder
    {
        $query = Penalty::with([
            'employee.translations',
            'employee.department.translations',
            'employee.jobTitle.translations',
            'penaltyType.translations',
            'creator',
            'approver',
            'deductor',
            'canceller',
        ]);

        // Employee filter
        if (!empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        // Penalty Type filter
        if (!empty($filters['penalty_type_id'])) {
            $query->where('penalty_type_id', $filters['penalty_type_id']);
        }

        // Calculation Type filter
        if (!empty($filters['calculation_type'])) {
            $query->where('calculation_type', $filters['calculation_type']);
        }

        // Deduction Method filter
        if (!empty($filters['deduction_method'])) {
            $query->where('deduction_method', $filters['deduction_method']);
        }

        // Status filter
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Date Range Filters
        if (!empty($filters['date_from'])) {
            $query->whereDate('penalty_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('penalty_date', '<=', $filters['date_to']);
        }

        return $query;
    }

    /**
     * Find penalty by ID.
     */
    public function findById(int $id, array $with = []): ?Penalty
    {
        $query = Penalty::query();

        if (!empty($with)) {
            $query->with($with);
        }

        return $query->find($id);
    }

    /**
     * Create a new penalty.
     */
    public function create(array $data): Penalty
    {
        return Penalty::create($data);
    }

    /**
     * Update penalty.
     */
    public function update(Penalty $penalty, array $data): bool
    {
        return $penalty->update($data);
    }

    /**
     * Delete a penalty.
     */
    public function delete(Penalty $penalty): bool
    {
        return $penalty->delete();
    }

    /**
     * Get KPI summary metrics.
     */
    public function getKPIs(): array
    {
        $now          = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth()->toDateString();
        $endOfMonth   = $now->copy()->endOfMonth()->toDateString();
        $year         = $now->year;

        // Total Deductions YTD
        $totalYtd = (float) Penalty::whereIn('status', [PenaltyStatus::APPROVED->value, PenaltyStatus::DEDUCTED->value])
            ->whereYear('penalty_date', $year)
            ->sum('amount');

        // This Month Total Deductions
        $thisMonth = (float) Penalty::whereIn('status', [PenaltyStatus::APPROVED->value, PenaltyStatus::DEDUCTED->value])
            ->whereBetween('penalty_date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        // Pending Approval Count
        $pendingCount = Penalty::pending()->count();

        // Total Penalties Count YTD
        $totalCountYtd = Penalty::whereYear('penalty_date', $year)->count();

        return [
            'total_ytd'       => $totalYtd,
            'this_month'      => $thisMonth,
            'pending_count'   => $pendingCount,
            'total_count_ytd' => $totalCountYtd,
        ];
    }
}
