<?php

namespace Modules\HR\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\HR\Enums\JobTitleStatus;
use Modules\HR\Models\JobTitle;
use Modules\HR\Repositories\Contracts\JobTitleRepositoryInterface;

class JobTitleService
{
    //======================
    public function __construct(
        protected JobTitleRepositoryInterface $repository
    ) {}

    //======================
    /**
     * Get all job titles with optional filtering.
     */
    public function getAllJobTitles(array $filters = []): Collection
    {
        return $this->repository->getAll($filters);
    }

    //======================
    /**
     * Get query builder for DataTables.
     */
    public function getDataTableQuery(array $filters = []): Builder
    {
        return $this->repository->getDataTableQuery($filters);
    }

    //======================
    /**
     * Get job title details by ID.
     */
    public function getJobTitleById(int $id): ?JobTitle
    {
        return $this->repository->findById($id);
    }

    //======================
    /**
     * Create a new job title with translations.
     */
    public function createJobTitle(array $data, ?int $userId = null): JobTitle
    {
        if ($userId && !isset($data['created_by'])) {
            $data['created_by'] = $userId;
        }

        $formattedData = $this->formatTranslationData($data);

        return $this->repository->create($formattedData);
    }

    //======================
    /**
     * Update job title details.
     */
    public function updateJobTitle(int|JobTitle $jobTitle, array $data): JobTitle
    {
        $formattedData = $this->formatTranslationData($data);

        return $this->repository->update($jobTitle, $formattedData);
    }

    //======================
    /**
     * Delete job title.
     */
    public function deleteJobTitle(int|JobTitle $jobTitle): bool
    {
        return $this->repository->delete($jobTitle);
    }

    //======================
    /**
     * Get lookups needed for forms and filters.
     */
    public function getFormData(): array
    {
        return [
            'departments' => $this->repository->getDepartmentsList(),
            'statuses'    => JobTitleStatus::cases(),
        ];
    }

    //======================
    /**
     * Format flat inputs into Astrotomic Translatable structure.
     */
    protected function formatTranslationData(array $data): array
    {
        $formatted = $data;

        if (isset($data['name_ar']) || isset($data['description_ar'])) {
            $formatted['ar'] = [
                'name'        => $data['name_ar'] ?? null,
                'description' => $data['description_ar'] ?? null,
            ];
            unset($formatted['name_ar'], $formatted['description_ar']);
        }

        if (isset($data['name_en']) || isset($data['description_en'])) {
            $formatted['en'] = [
                'name'        => $data['name_en'] ?? null,
                'description' => $data['description_en'] ?? null,
            ];
            unset($formatted['name_en'], $formatted['description_en']);
        }

        return $formatted;
    }
}
