<?php

namespace Modules\HR\Services;

use Illuminate\Support\Facades\DB;
use Modules\HR\Enums\ApplicationStage;
use Modules\HR\Enums\JobOfferStatus;
use Modules\HR\Models\JobApplication;
use Modules\HR\Models\JobOffer;
use Modules\HR\Models\Onboarding;

class JobOfferService
{
    public function createOffer(array $data, int $userId): JobOffer
    {
        return DB::transaction(function () use ($data, $userId) {
            // Generate Code
            $year = date('Y');
            $latestId = (JobOffer::max('id') ?? 0) + 1;
            $data['offer_code'] = 'OFF-' . $year . '-' . str_pad((string)$latestId, 4, '0', STR_PAD_LEFT);

            // Compute Total Salary
            $basic = (float)($data['basic_salary'] ?? 0);
            $housing = (float)($data['housing_allowance'] ?? 0);
            $transport = (float)($data['transportation_allowance'] ?? 0);
            $other = (float)($data['other_allowances'] ?? 0);
            $data['total_salary'] = $basic + $housing + $transport + $other;

            $data['currency'] = $data['currency'] ?? (\Modules\Settings\Models\Setting::instance()->currency ?? 'KWD');
            $data['created_by'] = $userId;
            $data['status'] = $data['status'] ?? JobOfferStatus::DRAFT->value;

            $offer = JobOffer::create($data);

            // Move application stage to OFFER
            if ($offer->job_application_id) {
                JobApplication::where('id', $offer->job_application_id)
                    ->update(['stage' => ApplicationStage::OFFER->value]);
            }

            return $offer;
        });
    }

    public function updateOffer(JobOffer $offer, array $data): JobOffer
    {
        return DB::transaction(function () use ($offer, $data) {
            $basic = (float)($data['basic_salary'] ?? $offer->basic_salary);
            $housing = (float)($data['housing_allowance'] ?? $offer->housing_allowance);
            $transport = (float)($data['transportation_allowance'] ?? $offer->transportation_allowance);
            $other = (float)($data['other_allowances'] ?? $offer->other_allowances);
            $data['total_salary'] = $basic + $housing + $transport + $other;

            $offer->update($data);
            return $offer->fresh();
        });
    }

    public function submitForApproval(JobOffer $offer): JobOffer
    {
        $offer->update(['status' => JobOfferStatus::PENDING_APPROVAL->value]);
        return $offer->fresh();
    }

    public function approveOffer(JobOffer $offer, int $userId): JobOffer
    {
        $offer->update([
            'status'      => JobOfferStatus::APPROVED->value,
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);
        return $offer->fresh();
    }

    public function sendOffer(JobOffer $offer): JobOffer
    {
        $offer->update([
            'status'  => JobOfferStatus::SENT->value,
            'sent_at' => now(),
        ]);
        return $offer->fresh();
    }

    public function acceptOffer(JobOffer $offer): JobOffer
    {
        return DB::transaction(function () use ($offer) {
            $offer->update([
                'status'       => JobOfferStatus::ACCEPTED->value,
                'responded_at' => now(),
            ]);

            // Automatically create Onboarding record if one doesn't exist
            $onboarding = Onboarding::firstOrCreate(
                ['job_offer_id' => $offer->id],
                [
                    'onboarding_code'    => 'ONB-' . date('Y') . '-' . str_pad((string)((Onboarding::max('id') ?? 0) + 1), 4, '0', STR_PAD_LEFT),
                    'candidate_id'       => $offer->candidate_id,
                    'job_application_id' => $offer->job_application_id,
                    'department_id'      => $offer->department_id,
                    'job_title_id'       => $offer->job_title_id,
                    'start_date'         => $offer->joining_date ?? now()->addDays(7)->toDateString(),
                    'status'             => 'pending',
                    'created_by'         => $offer->created_by,
                ]
            );

            // Populate default checklist tasks if new
            if ($onboarding->wasRecentlyCreated) {
                $defaultTasks = [
                    ['title' => __('Collect required official onboarding documents'), 'category' => 'documentation'],
                    ['title' => __('Sign official employment contract'), 'category' => 'documentation'],
                    ['title' => __('Create corporate email & system account'), 'category' => 'it_access'],
                    ['title' => __('Assign laptop & access peripherals'), 'category' => 'it_access'],
                    ['title' => __('Office orientation & department team introduction'), 'category' => 'orientation'],
                    ['title' => __('Review company work policy & code of conduct'), 'category' => 'orientation'],
                    ['title' => __('Setup office desk & workstation'), 'category' => 'facilities'],
                ];

                foreach ($defaultTasks as $task) {
                    $onboarding->tasks()->create($task);
                }
            }

            return $offer->fresh();
        });
    }

    public function declineOffer(JobOffer $offer, ?string $reason): JobOffer
    {
        $offer->update([
            'status'           => JobOfferStatus::DECLINED->value,
            'rejection_reason' => $reason,
            'responded_at'     => now(),
        ]);
        return $offer->fresh();
    }

    public function cancelOffer(JobOffer $offer): JobOffer
    {
        $offer->update(['status' => JobOfferStatus::CANCELLED->value]);
        return $offer->fresh();
    }
}
