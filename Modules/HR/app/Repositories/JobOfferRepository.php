<?php

namespace Modules\HR\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Modules\HR\Enums\JobOfferStatus;
use Modules\HR\Models\JobOffer;
use Modules\HR\Repositories\Contracts\JobOfferRepositoryInterface;

class JobOfferRepository implements JobOfferRepositoryInterface
{
    public function getDataTableQuery(array $filters = []): Builder
    {
        $query = JobOffer::with([
            'candidate',
            'jobApplication',
            'jobOpening.translations',
            'department.translations',
            'jobTitle.translations',
            'approver',
        ]);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        if (!empty($filters['job_opening_id'])) {
            $query->where('job_opening_id', $filters['job_opening_id']);
        }

        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $query->where(function ($q) use ($s) {
                $q->where('offer_code', 'like', "%{$s}%")
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
        $all = JobOffer::all();

        return [
            'total'            => $all->count(),
            'pending_approval' => $all->where('status', JobOfferStatus::PENDING_APPROVAL)->count(),
            'sent'             => $all->where('status', JobOfferStatus::SENT)->count(),
            'accepted'         => $all->where('status', JobOfferStatus::ACCEPTED)->count(),
            'declined'         => $all->where('status', JobOfferStatus::DECLINED)->count(),
        ];
    }

    public function findById(int $id): ?JobOffer
    {
        return JobOffer::find($id);
    }

    public function findWithRelations(int $id): ?JobOffer
    {
        return JobOffer::with([
            'candidate',
            'jobApplication',
            'jobOpening.translations',
            'department.translations',
            'jobTitle.translations',
            'approver',
            'creator',
            'onboarding',
        ])->find($id);
    }

    public function create(array $data): JobOffer
    {
        return JobOffer::create($data);
    }

    public function update(JobOffer $offer, array $data): bool
    {
        return $offer->update($data);
    }

    public function delete(JobOffer $offer): bool
    {
        return (bool) $offer->delete();
    }
}
