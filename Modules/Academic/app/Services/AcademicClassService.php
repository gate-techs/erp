<?php

namespace Modules\Academic\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\DatabaseManager;
use Illuminate\Validation\ValidationException;
use Modules\Academic\Models\AcademicClass;
use Modules\Academic\Repositories\Contracts\AcademicClassRepositoryInterface;

class AcademicClassService
{
    public function __construct(
        protected AcademicClassRepositoryInterface $repository,
        protected DatabaseManager $database
    ) {}

    public function getAcademicClasses(array $filters = []): Collection
    {
        return $this->repository->getAll($filters);
    }

    public function getDataTableQuery(array $filters = []): Builder
    {
        return $this->repository->getDataTableQuery($filters);
    }

    public function getAcademicClass(int $id): ?AcademicClass
    {
        return $this->repository->findById($id);
    }

    public function createAcademicClass(array $data, ?int $userId = null): AcademicClass
    {
        $this->validateDuplicateName($data);

        return $this->database->transaction(function () use ($data, $userId) {
            $data = $this->prepareCreateData($data, $userId);

            return $this->repository->create($this->prepareTranslationData($data));
        });
    }

    public function updateAcademicClass(int $id, array $data): AcademicClass
    {
        $academicClass = $this->findOrFail($id);
        $data = $this->mergeWithExistingData($academicClass, $data);

        $this->validateDuplicateName($data, $academicClass->id);

        return $this->database->transaction(function () use ($academicClass, $data) {
            return $this->repository->update(
                $academicClass,
                $this->prepareTranslationData($data)
            );
        });
    }

    public function deleteAcademicClass(int $id): bool
    {
        return $this->repository->delete($this->findOrFail($id));
    }

    public function toggleStatus(int $id): AcademicClass
    {
        $academicClass = $this->findOrFail($id);
        $nextStatus = $academicClass->isActive()
            ? AcademicClass::STATUS_INACTIVE
            : AcademicClass::STATUS_ACTIVE;

        return $this->repository->update($academicClass, [
            'status' => $nextStatus,
        ]);
    }

    private function findOrFail(int $id): AcademicClass
    {
        $academicClass = $this->repository->findById($id);

        if (!$academicClass) {
            throw ValidationException::withMessages([
                'academic_class' => __('Academic class not found.'),
            ]);
        }

        return $academicClass;
    }

    private function validateDuplicateName(array $data, ?int $ignoreId = null): void
    {
        $hasDuplicate = $this->repository->hasDuplicateName(
            (int) $data['institution_id'],
            (int) $data['branch_id'],
            (int) $data['academic_year_id'],
            (int) $data['academic_stage_id'],
            [$data['name_ar'] ?? null, $data['name_en'] ?? null],
            $ignoreId
        );

        if ($hasDuplicate) {
            throw ValidationException::withMessages([
                'name_ar' => __('This class name already exists in the selected academic scope.'),
            ]);
        }
    }

    private function prepareCreateData(array $data, ?int $userId): array
    {
        return array_merge([
            'status' => AcademicClass::STATUS_ACTIVE,
            'created_by' => $userId,
        ], $data);
    }

    private function mergeWithExistingData(AcademicClass $academicClass, array $data): array
    {
        return array_merge([
            'institution_id' => $academicClass->institution_id,
            'branch_id' => $academicClass->branch_id,
            'academic_year_id' => $academicClass->academic_year_id,
            'academic_stage_id' => $academicClass->academic_stage_id,
            'name_ar' => $academicClass->translate('ar')?->name,
            'name_en' => $academicClass->translate('en')?->name,
            'description_ar' => $academicClass->translate('ar')?->description,
            'description_en' => $academicClass->translate('en')?->description,
            'capacity' => $academicClass->capacity,
            'status' => $academicClass->status,
        ], $data);
    }

    private function prepareTranslationData(array $data): array
    {
        $arabicName = $data['name_ar'] ?? ($data['ar']['name'] ?? '');
        $englishName = $data['name_en'] ?? ($data['en']['name'] ?? $arabicName);

        $data['ar'] = [
            'name' => $arabicName,
            'description' => $data['description_ar'] ?? ($data['ar']['description'] ?? null),
        ];
        $data['en'] = [
            'name' => $englishName,
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
