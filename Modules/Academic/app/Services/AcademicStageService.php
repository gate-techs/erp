<?php

namespace Modules\Academic\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\DatabaseManager;
use Illuminate\Validation\ValidationException;
use Modules\Academic\Models\AcademicStage;
use Modules\Academic\Repositories\Contracts\AcademicStageRepositoryInterface;

class AcademicStageService
{
    public function __construct(
        protected AcademicStageRepositoryInterface $repository,
        protected DatabaseManager $database
    ) {}

    public function getAcademicStages(array $filters = []): Collection
    {
        return $this->repository->getAll($filters);
    }

    public function getDataTableQuery(array $filters = []): Builder
    {
        return $this->repository->getDataTableQuery($filters);
    }

    public function getAcademicStage(int $id): ?AcademicStage
    {
        return $this->repository->findById($id);
    }

    public function createAcademicStage(array $data, ?int $userId = null): AcademicStage
    {
        $this->validateAgeRange($data);

        return $this->database->transaction(function () use ($data, $userId) {
            $data = $this->prepareCreateData($data, $userId);

            return $this->repository->create($this->prepareTranslationData($data));
        });
    }

    public function updateAcademicStage(int $id, array $data): AcademicStage
    {
        $academicStage = $this->findOrFail($id);
        $data = $this->mergeWithExistingData($academicStage, $data);

        $this->validateAgeRange($data);

        return $this->database->transaction(function () use ($academicStage, $data) {
            return $this->repository->update(
                $academicStage,
                $this->prepareTranslationData($data)
            );
        });
    }

    public function deleteAcademicStage(int $id): bool
    {
        return $this->repository->delete($this->findOrFail($id));
    }

    public function toggleStatus(int $id): AcademicStage
    {
        $academicStage = $this->findOrFail($id);
        $nextStatus = $academicStage->isActive()
            ? AcademicStage::STATUS_INACTIVE
            : AcademicStage::STATUS_ACTIVE;

        return $this->repository->update($academicStage, [
            'status' => $nextStatus,
        ]);
    }

    private function findOrFail(int $id): AcademicStage
    {
        $academicStage = $this->repository->findById($id);

        if (!$academicStage) {
            throw ValidationException::withMessages([
                'academic_stage' => __('Academic stage not found.'),
            ]);
        }

        return $academicStage;
    }

    private function validateAgeRange(array $data): void
    {
        $minAge = $data['min_age_months'] ?? null;
        $maxAge = $data['max_age_months'] ?? null;

        if ($minAge !== null && $maxAge !== null && (int) $maxAge < (int) $minAge) {
            throw ValidationException::withMessages([
                'max_age_months' => __('The maximum age must be greater than or equal to the minimum age.'),
            ]);
        }
    }

    private function prepareCreateData(array $data, ?int $userId): array
    {
        return array_merge([
            'status' => AcademicStage::STATUS_ACTIVE,
            'created_by' => $userId,
        ], $data);
    }

    private function mergeWithExistingData(AcademicStage $academicStage, array $data): array
    {
        return array_merge([
            'institution_id' => $academicStage->institution_id,
            'code' => $academicStage->code,
            'name_ar' => $academicStage->translate('ar')?->name,
            'name_en' => $academicStage->translate('en')?->name,
            'description_ar' => $academicStage->translate('ar')?->description,
            'description_en' => $academicStage->translate('en')?->description,
            'min_age_months' => $academicStage->min_age_months,
            'max_age_months' => $academicStage->max_age_months,
            'status' => $academicStage->status,
        ], $data);
    }

    private function prepareTranslationData(array $data): array
    {
        $data['ar'] = [
            'name' => $data['name_ar'] ?? ($data['ar']['name'] ?? ''),
            'description' => $data['description_ar'] ?? ($data['ar']['description'] ?? null),
        ];
        $data['en'] = [
            'name' => $data['name_en'] ?? ($data['en']['name'] ?? $data['ar']['name']),
            'description' => $data['description_en'] ?? ($data['en']['description'] ?? null),
        ];

        unset(
            $data['name_ar'],
            $data['name_en'],
            $data['description_ar'],
            $data['description_en']
        );

        return $data;
    }
}
