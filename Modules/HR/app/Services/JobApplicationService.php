<?php

namespace Modules\HR\Services;

use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\HR\Enums\ApplicationStage;
use Modules\HR\Enums\ApplicationStatus;
use Modules\HR\Enums\RejectionReason;
use Modules\HR\Models\Candidate;
use Modules\HR\Models\JobApplication;
use Modules\HR\Repositories\Contracts\JobApplicationRepositoryInterface;

class JobApplicationService
{
    public function __construct(
        protected JobApplicationRepositoryInterface $repository
    ) {}

    /**
     * Create candidate and submit application in a single transaction.
     */
    public function submitApplication(array $data, ?UploadedFile $resumeFile = null): JobApplication
    {
        return DB::transaction(function () use ($data, $resumeFile) {
            $resumePath = null;
            if ($resumeFile && $resumeFile->isValid()) {
                $resumePath = $resumeFile->store('resumes', 'public');
            }

            $fullName = trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''));

            // Find or create candidate by email
            $candidate = Candidate::firstOrNew(['email' => $data['email']]);
            $candidate->fill([
                'first_name'             => $data['first_name'],
                'last_name'              => $data['last_name'] ?? null,
                'full_name'              => $fullName,
                'phone'                  => $data['phone'] ?? null,
                'nationality'            => $data['nationality'] ?? null,
                'city'                   => $data['city'] ?? null,
                'current_job_title'      => $data['current_job_title'] ?? null,
                'current_company'        => $data['current_company'] ?? null,
                'total_experience_years' => $data['total_experience_years'] ?? null,
                'linkedin_url'           => $data['linkedin_url'] ?? null,
                'portfolio_url'          => $data['portfolio_url'] ?? null,
                'notes'                  => $data['notes'] ?? null,
                'created_by'             => auth()->id(),
            ]);

            if ($resumePath) {
                $candidate->resume_path = $resumePath;
            }
            $candidate->save();

            // Create Application
            $appCode = $this->repository->generateApplicationCode();
            $application = $this->repository->create([
                'application_code'   => $appCode,
                'candidate_id'       => $candidate->id,
                'job_opening_id'     => $data['job_opening_id'],
                'stage'              => $data['stage'] ?? ApplicationStage::NEW->value,
                'status'             => ApplicationStatus::ACTIVE->value,
                'rating'             => $data['rating'] ?? null,
                'expected_salary'    => $data['expected_salary'] ?? null,
                'currency'           => $data['currency'] ?? 'KWD',
                'notice_period_days' => $data['notice_period_days'] ?? null,
                'source'             => $data['source'] ?? 'Direct',
                'cover_letter'       => $data['cover_letter'] ?? null,
                'created_by'         => auth()->id(),
            ]);

            return $application;
        });
    }

    /**
     * Update application stage (e.g. via Kanban Drag & Drop)
     */
    public function updateStage(int $applicationId, string $newStage): JobApplication
    {
        return DB::transaction(function () use ($applicationId, $newStage) {
            $app = $this->repository->findById($applicationId);
            if (!$app) {
                throw new \InvalidArgumentException(__('Application not found.'));
            }

            $stageEnum = ApplicationStage::tryFrom($newStage);
            if (!$stageEnum) {
                throw new \InvalidArgumentException(__('Invalid recruitment stage.'));
            }

            $oldStage = $app->stage;

            $updateData = [
                'stage'  => $stageEnum->value,
                'status' => ApplicationStatus::ACTIVE->value,
            ];

            // If moving to HIRED
            if ($stageEnum === ApplicationStage::HIRED && $oldStage !== ApplicationStage::HIRED) {
                $updateData['hired_at'] = Carbon::now();
                if ($app->jobOpening) {
                    $app->jobOpening->increment('filled_positions');
                }
            }
            // If moving away from HIRED
            elseif ($oldStage === ApplicationStage::HIRED && $stageEnum !== ApplicationStage::HIRED) {
                $updateData['hired_at'] = null;
                if ($app->jobOpening && $app->jobOpening->filled_positions > 0) {
                    $app->jobOpening->decrement('filled_positions');
                }
            }

            $app->update($updateData);

            return $app->fresh(['candidate', 'jobOpening']);
        });
    }

    /**
     * Reject / Disqualify candidate
     */
    public function rejectApplication(int $applicationId, string $reason, ?string $notes = null): JobApplication
    {
        return DB::transaction(function () use ($applicationId, $reason, $notes) {
            $app = $this->repository->findById($applicationId);
            if (!$app) {
                throw new \InvalidArgumentException(__('Application not found.'));
            }

            // If it was hired, revert filled headcount
            if ($app->stage === ApplicationStage::HIRED && $app->jobOpening && $app->jobOpening->filled_positions > 0) {
                $app->jobOpening->decrement('filled_positions');
            }

            $app->update([
                'status'           => ApplicationStatus::REJECTED->value,
                'rejection_reason' => $reason,
                'rejection_notes'  => $notes,
                'rejected_at'      => Carbon::now(),
            ]);

            return $app->fresh();
        });
    }

    /**
     * Reopen / Restore candidate to active pool
     */
    public function reopenApplication(int $applicationId): JobApplication
    {
        return DB::transaction(function () use ($applicationId) {
            $app = $this->repository->findById($applicationId);
            if (!$app) {
                throw new \InvalidArgumentException(__('Application not found.'));
            }

            $app->update([
                'status'           => ApplicationStatus::ACTIVE->value,
                'rejection_reason' => null,
                'rejection_notes'  => null,
                'rejected_at'      => null,
            ]);

            return $app->fresh();
        });
    }

    /**
     * Update rating score (1-5)
     */
    public function updateRating(int $applicationId, int $rating): JobApplication
    {
        $app = $this->repository->findById($applicationId);
        if (!$app) {
            throw new \InvalidArgumentException(__('Application not found.'));
        }

        $app->update(['rating' => min(5, max(1, $rating))]);
        return $app->fresh();
    }
}
