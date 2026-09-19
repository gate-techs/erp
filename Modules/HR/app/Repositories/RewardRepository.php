<?php

namespace Modules\HR\Repositories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Modules\HR\Enums\RewardStatus;
use Modules\HR\Models\Reward;
use Modules\HR\Repositories\Contracts\RewardRepositoryInterface;

class RewardRepository implements RewardRepositoryInterface
{
    /**
     * Get DataTable query with applied filters.
     */
    public function getDataTableQuery(array $filters = []): Builder
    {
        $query = Reward::with([
            'employee.translations',
            'employee.department.translations',
            'employee.jobTitle.translations',
            'rewardType.translations',
            'creator',
            'approver',
            'payer',
        ]);

        // Employee filter
        if (!empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        // Reward Type filter
        if (!empty($filters['reward_type_id'])) {
            $query->where('reward_type_id', $filters['reward_type_id']);
        }

        // Calculation Type filter
        if (!empty($filters['calculation_type'])) {
            $query->where('calculation_type', $filters['calculation_type']);
        }

        // Payment Method filter
        if (!empty($filters['payment_method'])) {
            $query->where('payment_method', $filters['payment_method']);
        }

        // Status filter
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Date Range Filters
        if (!empty($filters['date_from'])) {
            $query->whereDate('award_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('award_date', '<=', $filters['date_to']);
        }

        return $query;
    }

    /**
     * Find reward by ID.
     */
    public function findById(int $id, array $with = []): ?Reward
    {
        $query = Reward::query();

        if (!empty($with)) {
            $query->with($with);
        }

        return $query->find($id);
    }

    /**
     * Create a new reward.
     */
    public function create(array $data): Reward
    {
        return Reward::create($data);
    }

    /**
     * Update reward.
     */
    public function update(Reward $reward, array $data): bool
    {
        return $reward->update($data);
    }

    /**
     * Delete a reward.
     */
    public function delete(Reward $reward): bool
    {
        return $reward->delete();
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

        // Total Disbursed / Approved YTD
        $totalYtd = (float) Reward::whereIn('status', [RewardStatus::APPROVED->value, RewardStatus::PAID->value])
            ->whereYear('award_date', $year)
            ->sum('amount');

        // This Month Total
        $thisMonth = (float) Reward::whereIn('status', [RewardStatus::APPROVED->value, RewardStatus::PAID->value])
            ->whereBetween('award_date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        // Pending Approval Count
        $pendingCount = Reward::pending()->count();

        // Average Reward Amount
        $avgReward = (float) (Reward::whereIn('status', [RewardStatus::APPROVED->value, RewardStatus::PAID->value])
            ->avg('amount') ?? 0.0);

        return [
            'total_ytd'     => $totalYtd,
            'this_month'    => $thisMonth,
            'pending_count' => $pendingCount,
            'average_award' => round($avgReward, 2),
        ];
    }
}
