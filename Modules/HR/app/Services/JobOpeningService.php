<?php

namespace Modules\HR\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Modules\HR\Enums\JobOpeningStatus;
use Modules\HR\Enums\JobRequisitionStatus;
use Modules\HR\Models\JobOpening;
use Modules\HR\Models\JobRequisition;
use Modules\HR\Repositories\Contracts\JobOpeningRepositoryInterface;
use Modules\HR\Repositories\Contracts\JobRequisitionRepositoryInterface;

class JobOpeningService
{
    public function __construct(
        protected JobOpeningRepositoryInterface $openingRepository,
        protected JobRequisitionRepositoryInterface $requisitionRepository
    ) {}

    /**
     * Get DataTable query builder.
     */
    public function getDataTableQuery(array $filters = []): Builder
    {
        return $this->openingRepository->getDataTableQuery($filters);
    }

    /**
     * Get KPI summary metrics.
     */
    public function getKPIs(): array
    {
        return $this->openingRepository->getKPIs();
    }

    /**
     * Find job opening by ID.
     */
    public function findById(int $id): ?JobOpening
    {
        return $this->openingRepository->findById($id);
    }

    /**
     * Create a new job opening.
     */
    public function createOpening(array $data, int $userId): JobOpening
    {
        return DB::transaction(function () use ($data, $userId) {
            $formatted = $this->formatTranslationData($data);
            $formatted['opening_code'] = $this->openingRepository->generateOpeningCode();
            $formatted['created_by']   = $userId;
            $formatted['status']       = $formatted['status'] ?? JobOpeningStatus::DRAFT->value;

            if ($formatted['status'] === JobOpeningStatus::PUBLISHED->value) {
                $formatted['published_by'] = $userId;
                $formatted['published_at'] = now();
            }

            $opening = $this->openingRepository->create($formatted);

            // If published and linked to an approved requisition, transition requisition to in_recruitment
            if ($opening->status === JobOpeningStatus::PUBLISHED && $opening->job_requisition_id) {
                $requisition = $this->requisitionRepository->findById($opening->job_requisition_id);
                if ($requisition && $requisition->status === JobRequisitionStatus::APPROVED) {
                    $requisition->update(['status' => JobRequisitionStatus::IN_RECRUITMENT->value]);
                }
            }

            return $opening;
        });
    }

    /**
     * Update an existing job opening.
     */
    public function updateOpening(int|JobOpening $opening, array $data, ?int $userId = null): JobOpening
    {
        return DB::transaction(function () use ($opening, $data, $userId) {
            $instance = $opening instanceof JobOpening ? $opening : $this->findById($opening);
            $formatted = $this->formatTranslationData($data);

            if (isset($formatted['status']) && $formatted['status'] === JobOpeningStatus::PUBLISHED->value && $instance->status !== JobOpeningStatus::PUBLISHED) {
                $formatted['published_by'] = $userId ?? auth()->id();
                $formatted['published_at'] = now();
            }

            $updated = $this->openingRepository->update($instance, $formatted);

            if ($updated->status === JobOpeningStatus::PUBLISHED && $updated->job_requisition_id) {
                $requisition = $this->requisitionRepository->findById($updated->job_requisition_id);
                if ($requisition && $requisition->status === JobRequisitionStatus::APPROVED) {
                    $requisition->update(['status' => JobRequisitionStatus::IN_RECRUITMENT->value]);
                }
            }

            return $updated;
        });
    }

    /**
     * Format flat inputs into Astrotomic Translatable structure.
     */
    public function formatTranslationData(array $data): array
    {
        $formatted = $data;

        $hasAr = isset($data['title_ar']) || isset($data['job_summary_ar']) || isset($data['responsibilities_ar']) || isset($data['requirements_ar']) || isset($data['benefits_ar']) || isset($data['education_level_ar']);
        $hasEn = isset($data['title_en']) || isset($data['job_summary_en']) || isset($data['responsibilities_en']) || isset($data['requirements_en']) || isset($data['benefits_en']) || isset($data['education_level_en']);

        if ($hasAr || isset($data['title'])) {
            $formatted['ar'] = [
                'title'            => $data['title_ar'] ?? ($data['title'] ?? ''),
                'job_summary'      => $data['job_summary_ar'] ?? ($data['job_summary'] ?? null),
                'responsibilities' => $data['responsibilities_ar'] ?? ($data['responsibilities'] ?? null),
                'requirements'     => $data['requirements_ar'] ?? ($data['requirements'] ?? null),
                'benefits'         => $data['benefits_ar'] ?? ($data['benefits'] ?? null),
                'education_level'  => $data['education_level_ar'] ?? ($data['education_level'] ?? null),
            ];
            unset($formatted['title_ar'], $formatted['job_summary_ar'], $formatted['responsibilities_ar'], $formatted['requirements_ar'], $formatted['benefits_ar'], $formatted['education_level_ar']);
        }

        if ($hasEn) {
            $formatted['en'] = [
                'title'            => $data['title_en'] ?? ($data['title'] ?? ''),
                'job_summary'      => $data['job_summary_en'] ?? null,
                'responsibilities' => $data['responsibilities_en'] ?? null,
                'requirements'     => $data['requirements_en'] ?? null,
                'benefits'         => $data['benefits_en'] ?? null,
                'education_level'  => $data['education_level_en'] ?? null,
            ];
            unset($formatted['title_en'], $formatted['job_summary_en'], $formatted['responsibilities_en'], $formatted['requirements_en'], $formatted['benefits_en'], $formatted['education_level_en']);
        }

        $formatted['title'] = $data['title_ar'] ?? ($data['title'] ?? ($data['title_en'] ?? ''));

        return $formatted;
    }

    /**
     * Delete opening (soft delete).
     */
    public function deleteOpening(int|JobOpening $opening): bool
    {
        return $this->openingRepository->delete($opening);
    }

    /**
     * Publish a job opening.
     */
    public function publishOpening(int|JobOpening $opening, int $userId): JobOpening
    {
        return DB::transaction(function () use ($opening, $userId) {
            $instance = $opening instanceof JobOpening ? $opening : $this->findById($opening);

            $instance->update([
                'status'       => JobOpeningStatus::PUBLISHED->value,
                'published_by' => $userId,
                'published_at' => now(),
            ]);

            // Update linked requisition to in_recruitment if currently approved
            if ($instance->job_requisition_id) {
                $requisition = $this->requisitionRepository->findById($instance->job_requisition_id);
                if ($requisition && $requisition->status === JobRequisitionStatus::APPROVED) {
                    $requisition->update(['status' => JobRequisitionStatus::IN_RECRUITMENT->value]);
                }
            }

            return $instance->fresh();
        });
    }

    /**
     * Close a job opening.
     */
    public function closeOpening(int|JobOpening $opening): JobOpening
    {
        $instance = $opening instanceof JobOpening ? $opening : $this->findById($opening);

        $instance->update([
            'status'    => JobOpeningStatus::CLOSED->value,
            'closed_at' => now(),
        ]);

        return $instance->fresh();
    }

    /**
     * Put a job opening on hold.
     */
    public function holdOpening(int|JobOpening $opening): JobOpening
    {
        $instance = $opening instanceof JobOpening ? $opening : $this->findById($opening);

        $instance->update([
            'status' => JobOpeningStatus::ON_HOLD->value,
        ]);

        return $instance->fresh();
    }

    /**
     * Get approved or active requisitions available for job posting.
     */
    public function getAvailableRequisitions()
    {
        return JobRequisition::with(['jobTitle.translations', 'department.translations', 'branch'])
            ->whereIn('status', [
                JobRequisitionStatus::APPROVED->value,
                JobRequisitionStatus::IN_RECRUITMENT->value,
            ])
            ->whereColumn('hired_count', '<', 'headcount_needed')
            ->latest('id')
            ->get();
    }

    /**
     * Get pre-filled opening data from a requisition.
     */
    public function getOpeningDefaultsFromRequisition(int $requisitionId): ?array
    {
        $requisition = $this->requisitionRepository->findById($requisitionId);
        if (!$requisition) {
            return null;
        }

        $jobTitleName = $requisition->jobTitle?->translate(app()->getLocale())?->name
            ?? $requisition->jobTitle?->translate('ar')?->name
            ?? $requisition->jobTitle?->name
            ?? '';

        return [
            'job_requisition_id'     => $requisition->id,
            'title'                  => $jobTitleName,
            'job_title_id'           => $requisition->job_title_id,
            'department_id'          => $requisition->department_id,
            'branch_id'              => $requisition->branch_id,
            'employment_type'        => $requisition->employment_type->value ?? 'full_time',
            'open_positions'         => max(1, $requisition->headcount_needed - $requisition->hired_count),
            'min_experience_years'   => $requisition->required_experience_years,
            'education_level'        => $requisition->education_level,
            'min_salary'             => $requisition->min_salary,
            'max_salary'             => $requisition->max_salary,
            'currency'               => $requisition->currency ?? 'KWD',
            'job_summary'            => $requisition->job_description,
            'responsibilities'       => $requisition->responsibilities,
            'requirements'           => $requisition->requirements,
            'benefits'               => null,
            'publish_date'           => now()->toDateString(),
        ];
    }
}
