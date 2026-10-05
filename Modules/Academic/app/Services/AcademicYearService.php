<?php

namespace Modules\Academic\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\Academic\Models\AcademicYear;
use Modules\Academic\Repositories\Contracts\AcademicYearRepositoryInterface;

class AcademicYearService
{
    public function __construct(
        protected AcademicYearRepositoryInterface $repository
    ) {}

    public function getAcademicYears(array $filters = []): Collection
    {
        return $this->repository->getAll($filters);
    }

    public function getDataTableQuery(array $filters = []): Builder
    {
        return $this->repository->getDataTableQuery($filters);
    }

    public function getAcademicYear(int $id): ?AcademicYear
    {
        return $this->repository->findById($id);
    }

    public function createAcademicYear(array $data, ?int $userId = null): AcademicYear
    {
        $this->validateDateRange($data);
        $this->validateNoOverlap($data);

        return DB::transaction(function () use ($data, $userId) {
            $data = $this->prepareCreateData($data, $userId);

            if ($data['is_current']) {
                $this->repository->clearCurrentForInstitution((int) $data['institution_id']);
            }

            return $this->repository->create($this->prepareTranslationData($data));
        });
    }

    public function updateAcademicYear(int $id, array $data): AcademicYear
    {
        $academicYear = $this->findOrFail($id);
        $data = $this->mergeWithExistingData($academicYear, $data);

        $this->validateDateRange($data);
        $this->validateNoOverlap($data, $academicYear->id);

        return DB::transaction(function () use ($academicYear, $data) {
            $data = $this->prepareUpdateData($data, $academicYear);

            if ($data['is_current']) {
                $this->repository->clearCurrentForInstitution(
                    (int) $data['institution_id'],
                    $academicYear->id
                );
            }

            return $this->repository->update(
                $academicYear,
                $this->prepareTranslationData($data)
            );
        });
    }

    public function deleteAcademicYear(int $id): bool
    {
        $academicYear = $this->findOrFail($id);

        if ($academicYear->is_current) {
            throw ValidationException::withMessages([
                'academic_year' => __('The current academic year cannot be deleted.'),
            ]);
        }

        return $this->repository->delete($academicYear);
    }

    public function toggleStatus(int $id): AcademicYear
    {
        $academicYear = $this->findOrFail($id);

        $nextStatus = match ($academicYear->status) {
            AcademicYear::STATUS_ACTIVE => AcademicYear::STATUS_CLOSED,
            AcademicYear::STATUS_CLOSED,
            AcademicYear::STATUS_DRAFT => AcademicYear::STATUS_ACTIVE,
            default => throw ValidationException::withMessages([
                'status' => __('Archived academic years cannot be activated.'),
            ]),
        };

        return $this->repository->update($academicYear, [
            'status' => $nextStatus,
        ]);
    }

    public function setCurrent(int $id): AcademicYear
    {
        $academicYear = $this->findOrFail($id);

        if ($academicYear->status === AcademicYear::STATUS_ARCHIVED) {
            throw ValidationException::withMessages([
                'academic_year' => __('An archived academic year cannot be current.'),
            ]);
        }

        return DB::transaction(function () use ($academicYear) {
            $this->repository->clearCurrentForInstitution(
                (int) $academicYear->institution_id,
                $academicYear->id
            );

            return $this->repository->update($academicYear, [
                'is_current' => true,
                'status' => AcademicYear::STATUS_ACTIVE,
            ]);
        });
    }

    private function findOrFail(int $id): AcademicYear
    {
        $academicYear = $this->repository->findById($id);

        if (!$academicYear) {
            throw ValidationException::withMessages([
                'academic_year' => __('Academic year not found.'),
            ]);
        }

        return $academicYear;
    }

    private function validateDateRange(array $data): void
    {
        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);

        if ($startDate->greaterThan($endDate)) {
            throw ValidationException::withMessages([
                'end_date' => __('The end date must be after or equal to the start date.'),
            ]);
        }
    }

    private function validateNoOverlap(array $data, ?int $ignoreId = null): void
    {
        $hasOverlap = $this->repository->hasOverlap(
            (int) $data['institution_id'],
            Carbon::parse($data['start_date'])->toDateString(),
            Carbon::parse($data['end_date'])->toDateString(),
            $ignoreId
        );

        if ($hasOverlap) {
            throw ValidationException::withMessages([
                'start_date' => __('This academic year overlaps another year for the selected institution.'),
            ]);
        }
    }

    private function prepareCreateData(array $data, ?int $userId): array
    {
        $prepared = array_merge([
            'status' => AcademicYear::STATUS_DRAFT,
            'is_current' => false,
            'created_by' => $userId,
        ], $data);

        $prepared['status'] = $prepared['status'] ?: AcademicYear::STATUS_DRAFT;
        $prepared['is_current'] = (bool) $prepared['is_current'];

        if ($prepared['is_current']) {
            $prepared['status'] = AcademicYear::STATUS_ACTIVE;
        }

        return $prepared;
    }

    private function prepareUpdateData(array $data, AcademicYear $academicYear): array
    {
        $prepared = array_merge([
            'institution_id' => $academicYear->institution_id,
            'is_current' => $academicYear->is_current,
        ], $data);

        $prepared['is_current'] = (bool) $prepared['is_current'];

        if ($prepared['is_current']) {
            $prepared['status'] = AcademicYear::STATUS_ACTIVE;
        }

        return $prepared;
    }

    private function mergeWithExistingData(AcademicYear $academicYear, array $data): array
    {
        return array_merge([
            'institution_id' => $academicYear->institution_id,
            'code' => $academicYear->code,
            'name_ar' => $academicYear->translate('ar')?->name,
            'name_en' => $academicYear->translate('en')?->name,
            'start_date' => $academicYear->start_date?->toDateString(),
            'end_date' => $academicYear->end_date?->toDateString(),
            'status' => $academicYear->status,
            'is_current' => $academicYear->is_current,
            'notes' => $academicYear->notes,
        ], $data);
    }

    private function prepareTranslationData(array $data): array
    {
        $arabicName = $data['name_ar'] ?? ($data['ar']['name'] ?? '');
        $englishName = $data['name_en'] ?? ($data['en']['name'] ?? $arabicName);

        $data['ar'] = [
            'name' => $arabicName,
        ];
        $data['en'] = [
            'name' => $englishName,
        ];

        unset($data['name_ar'], $data['name_en']);

        return $data;
    }
}
