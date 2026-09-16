<?php

namespace Modules\Nursery\Repositories;

use App\Enums\UserType;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Nursery\Enums\DayOfWeek;
use Modules\Nursery\Models\Nursery;
use Modules\Nursery\Models\NurseryBranch;
use Modules\Nursery\Repositories\Contracts\NurseryBranchRepositoryInterface;
use Modules\Settings\Enums\GeneralSettingType;
use Modules\Settings\Models\GeneralSetting;

class NurseryBranchRepository implements NurseryBranchRepositoryInterface
{
    //======================
    /**
     * Get all branches with optional filters and eager loading.
     */
    public function getAll(array $filters = [], array $with = ['translations', 'nursery', 'government', 'city', 'employee', 'workDays']): Collection
    {
        return $this->buildQuery($filters, $with)->get();
    }

    //======================
    /**
     * Get query builder instance for server-side DataTables.
     */
    public function getDataTableQuery(array $filters = []): Builder
    {
        return NurseryBranch::with(['translations', 'nursery.translations', 'government.translations', 'city.translations', 'employee'])
            ->select('nursery_branches.*')
            ->latest('nursery_branches.id');
    }

    //======================
    /**
     * Find a branch by ID.
     */
    public function findById(int $id, array $with = ['translations', 'nursery', 'government', 'city', 'employee', 'workDays']): ?NurseryBranch
    {
        return NurseryBranch::with($with)->find($id);
    }

    //======================
    /**
     * Create a new nursery branch.
     */
    public function create(array $data): NurseryBranch
    {
        return DB::transaction(function () use ($data) {
            $workDays = $data['work_days'] ?? null;
            unset($data['work_days']);

            $branch = NurseryBranch::create($data);

            if (!empty($workDays) && is_array($workDays)) {
                $this->syncWorkDays($branch, $workDays);
            } else {
                $this->syncWorkDays($branch, DayOfWeek::defaultSchedule());
            }

            return $branch->fresh(['translations', 'nursery', 'government', 'city', 'employee', 'workDays']);
        });
    }

    //======================
    /**
     * Update an existing nursery branch.
     */
    public function update(int|NurseryBranch $model, array $data): NurseryBranch
    {
        $branch = $model instanceof NurseryBranch ? $model : NurseryBranch::findOrFail($model);

        return DB::transaction(function () use ($branch, $data) {
            $workDays = $data['work_days'] ?? null;
            unset($data['work_days']);

            $branch->update($data);

            if ($workDays !== null && is_array($workDays)) {
                $this->syncWorkDays($branch, $workDays);
            }

            return $branch->fresh(['translations', 'nursery', 'government', 'city', 'employee', 'workDays']);
        });
    }

    //======================
    /**
     * Delete a nursery branch.
     */
    public function delete(int|NurseryBranch $model): bool
    {
        $branch = $model instanceof NurseryBranch ? $model : NurseryBranch::findOrFail($model);

        return (bool) $branch->delete();
    }

    //======================
    /**
     * Sync weekly work days for a branch.
     */
    public function syncWorkDays(NurseryBranch $branch, array $workDays): void
    {
        foreach ($workDays as $dayItem) {
            $dayName = $dayItem['day'] ?? ($dayItem['day_name'] ?? null);
            if (empty($dayName)) {
                continue;
            }

            $dayVal = $dayName instanceof DayOfWeek ? $dayName->value : (string) $dayName;
            $isOff = !empty($dayItem['is_off']);

            $branch->workDays()->updateOrCreate(
                ['day_name' => $dayVal],
                [
                    'from_time' => $isOff ? null : ($dayItem['from_time'] ?? null),
                    'to_time' => $isOff ? null : ($dayItem['to_time'] ?? null),
                    'is_off' => $isOff,
                ]
            );
        }
    }

    //======================
    /**
     * Get nurseries list for dropdown selection.
     */
    public function getNurseriesList(): Collection
    {
        return Nursery::with('translations')
            ->where('status', 'active')
            ->get();
    }

    //======================
    /**
     * Get governments list for dropdown selection.
     */
    public function getGovernmentsList(): Collection
    {
        return GeneralSetting::ofType(GeneralSettingType::GOVERNMENT)
            ->with('translations')
            ->get();
    }

    //======================
    /**
     * Get cities list (optionally filtered by government).
     */
    public function getCitiesList(?int $governmentId = null): Collection
    {
        $query = GeneralSetting::ofType(GeneralSettingType::CITY)
            ->with('translations');

        if ($governmentId) {
            $query->where('parent_id', $governmentId);
        }

        return $query->get();
    }

    //======================
    /**
     * Get employees list for dropdown selection.
     */
    public function getEmployeesList(): Collection
    {
        return User::where('user_type', UserType::EMPLOYEE)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    //======================
    /**
     * Build reusable Eloquent query based on filters.
     */
    protected function buildQuery(array $filters = [], array $with = []): Builder
    {
        $query = NurseryBranch::with($with)->latest('id');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['nursery_id'])) {
            $query->where('nursery_id', $filters['nursery_id']);
        }

        if (!empty($filters['government_id'])) {
            $query->where('government_id', $filters['government_id']);
        }

        if (!empty($filters['city_id'])) {
            $query->where('city_id', $filters['city_id']);
        }

        if (!empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $q) use ($search) {
                $q->whereTranslationLike('name', "%{$search}%")
                  ->orWhereTranslationLike('description', "%{$search}%")
                  ->orWhereTranslationLike('address', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('whatsapp', 'like', "%{$search}%");
            });
        }

        return $query;
    }
}
