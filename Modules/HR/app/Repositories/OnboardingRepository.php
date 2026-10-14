<?php

namespace Modules\HR\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Modules\HR\Enums\OnboardingStatus;
use Modules\HR\Models\Onboarding;
use Modules\HR\Repositories\Contracts\OnboardingRepositoryInterface;

class OnboardingRepository implements OnboardingRepositoryInterface
{
    public function getDataTableQuery(array $filters = []): Builder
    {
        $query = Onboarding::with([
            'candidate',
            'jobApplication',
            'jobOffer',
            'employee.translations',
            'department.translations',
            'jobTitle.translations',
            'buddy.translations',
            'tasks',
        ]);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $query->where(function ($q) use ($s) {
                $q->where('onboarding_code', 'like', "%{$s}%")
                  ->orWhereHas('candidate', function ($cq) use ($s) {
                      $cq->where('full_name', 'like', "%{$s}%")
                         ->orWhere('email', 'like', "%{$s}%")
                         ->orWhere('phone', 'like', "%{$s}%");
                  });
            });
        }

        return $query;
    }

    public function getKpis(): array
    {
        $all = Onboarding::all();

        return [
            'total'       => $all->count(),
            'in_progress' => $all->where('status', OnboardingStatus::IN_PROGRESS)->count(),
            'pending'     => $all->where('status', OnboardingStatus::PENDING)->count(),
            'completed'   => $all->where('status', OnboardingStatus::COMPLETED)->count(),
            'converted'   => $all->whereNotNull('employee_id')->count(),
        ];
    }

    public function findById(int $id): ?Onboarding
    {
        return Onboarding::find($id);
    }

    public function findWithRelations(int $id): ?Onboarding
    {
        return Onboarding::with([
            'candidate',
            'jobApplication',
            'jobOffer',
            'employee.translations',
            'department.translations',
            'jobTitle.translations',
            'buddy.translations',
            'creator',
            'tasks.assignee.translations',
        ])->find($id);
    }

    public function create(array $data): Onboarding
    {
        return Onboarding::create($data);
    }

    public function update(Onboarding $onboarding, array $data): bool
    {
        return $onboarding->update($data);
    }

    public function delete(Onboarding $onboarding): bool
    {
        return (bool) $onboarding->delete();
    }
}
