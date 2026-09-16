<?php

namespace Modules\Nursery\Repositories;

use App\Enums\UserType;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Nursery\Enums\DayOfWeek;
use Modules\Nursery\Models\Nursery;
use Modules\Nursery\Repositories\Contracts\NurseryRepositoryInterface;
use Modules\Settings\Enums\GeneralSettingType;
use Modules\Settings\Models\GeneralSetting;

class NurseryRepository implements NurseryRepositoryInterface
{
    //======================
    /**
     * Get all nurseries based on optional filters.
     */
    public function getAll(array $filters = [], array $with = ['translations', 'government', 'employee', 'workDays']): Collection
    {
        return $this->buildQuery($filters, $with)->get();
    }

    //======================
    /**
     * Get paginated nurseries.
     */
    public function getPaginated(array $filters = [], int $perPage = 15, array $with = ['translations', 'government', 'employee']): LengthAwarePaginator
    {
        return $this->buildQuery($filters, $with)->paginate($perPage);
    }

    //======================
    /**
     * Find a nursery by its ID.
     */
    public function findById(int $id, array $with = ['translations', 'government', 'employee', 'workDays']): ?Nursery
    {
        return Nursery::with($with)->find($id);
    }

    //======================
    /**
     * Create a new nursery record with translations.
     */
    public function create(array $data): Nursery
    {
        return DB::transaction(function () use ($data) {
            $workDays = $data['work_days'] ?? null;
            unset($data['work_days']);

            $nursery = Nursery::create($data);

            if (!empty($workDays) && is_array($workDays)) {
                $this->syncWorkDays($nursery, $workDays);
            } else {
                // Initialize with standard Gulf default work schedule
                $this->syncWorkDays($nursery, DayOfWeek::defaultSchedule());
            }

            return $nursery->fresh(['translations', 'government', 'employee', 'workDays']);
        });
    }

    //======================
    /**
     * Update an existing nursery record with translations.
     */
    public function update(int|Nursery $model, array $data): Nursery
    {
        $nursery = $model instanceof Nursery ? $model : Nursery::findOrFail($model);

        return DB::transaction(function () use ($nursery, $data) {
            $workDays = $data['work_days'] ?? null;
            unset($data['work_days']);

            $nursery->update($data);

            if ($workDays !== null && is_array($workDays)) {
                $this->syncWorkDays($nursery, $workDays);
            }

            return $nursery->fresh(['translations', 'government', 'employee', 'workDays']);
        });
    }

    //======================
    /**
     * Delete a nursery record.
     */
    public function delete(int|Nursery $model): bool
    {
        $nursery = $model instanceof Nursery ? $model : Nursery::findOrFail($model);

        return (bool) $nursery->delete();
    }

    //======================
    /**
     * Synchronize weekly working days for a nursery.
     */
    public function syncWorkDays(Nursery $nursery, array $workDays): void
    {
        foreach ($workDays as $dayItem) {
            if (empty($dayItem['day'])) {
                continue;
            }

            $dayValue = $dayItem['day'] instanceof DayOfWeek ? $dayItem['day']->value : (string) $dayItem['day'];
            $isOff = !empty($dayItem['is_off']);

            $nursery->workDays()->updateOrCreate(
                ['day' => $dayValue],
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
     * Get available employees list (users with user_type = 'employee').
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
     * Get available governments list from general_settings.
     */
    public function getGovernmentsList(): Collection
    {
        return GeneralSetting::ofType(GeneralSettingType::GOVERNMENT)
            ->with('translations')
            ->get();
    }

    //======================
    /**
     * Build reusable Eloquent query based on filters.
     */
    protected function buildQuery(array $filters = [], array $with = []): Builder
    {
        $query = Nursery::with($with)->latest('id');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['government_id'])) {
            $query->where('government_id', $filters['government_id']);
        }

        if (!empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $q) use ($search) {
                $q->whereTranslationLike('name', "%{$search}%")
                  ->orWhereTranslationLike('description', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('whatsapp', 'like', "%{$search}%");
            });
        }

        return $query;
    }
}
