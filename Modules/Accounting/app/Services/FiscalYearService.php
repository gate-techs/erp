<?php

namespace Modules\Accounting\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\Accounting\Models\FiscalYear;
use Modules\Accounting\Repositories\Contracts\FiscalYearRepositoryInterface;

class FiscalYearService
{
    //===============================
    public function __construct(
        protected FiscalYearRepositoryInterface $repository
    ) {}

    //===============================
    public function getFiscalYears(array $filters = []): Collection
    {
        return $this->repository->getAll($filters);
    }

    //===============================
    public function getDataTableQuery(array $filters = []): \Illuminate\Database\Eloquent\Builder
    {
        return $this->repository->getDataTableQuery($filters);
    }

    //===============================
    public function getFiscalYear(int $id): ?FiscalYear
    {
        return $this->repository->findById($id);
    }

    //===============================
    public function createFiscalYear(array $data, ?int $userId = null): FiscalYear
    {
        $this->validateDatesAndOverlap($data);

        return DB::transaction(function () use ($data, $userId) {
            $data['created_by'] = $userId;
            $data['status'] = 'open';

            return $this->repository->create($this->prepareData($data));
        });
    }

    //===============================
    public function updateFiscalYear(int|FiscalYear $fiscalYear, array $data): FiscalYear
    {
        $model = $fiscalYear instanceof FiscalYear ? $fiscalYear : $this->repository->findById($fiscalYear);

        if (!$model) {
            throw ValidationException::withMessages(['fiscal_year' => __('Fiscal year not found.')]);
        }

        $this->validateDatesAndOverlap($data, $model->id);

        return $this->repository->update($model, $this->prepareData($data));
    }

    //===============================
    public function deleteFiscalYear(int|FiscalYear $fiscalYear): bool
    {
        $model = $fiscalYear instanceof FiscalYear ? $fiscalYear : $this->repository->findById($fiscalYear);

        if (!$model) {
            return false;
        }

        if ($model->status !== 'open') {
            throw ValidationException::withMessages(['fiscal_year' => __('Only open fiscal years can be deleted.')]);
        }

        return $this->repository->delete($model);
    }

    //===============================
    public function changeStatus(int|FiscalYear $fiscalYear, ?int $userId = null): FiscalYear
    {
        $model = $fiscalYear instanceof FiscalYear ? $fiscalYear : $this->repository->findById($fiscalYear);

        if (!$model) {
            throw ValidationException::withMessages(['fiscal_year' => __('Fiscal year not found.')]);
        }

        $isClosing = $model->status === 'open';

        return $this->repository->update($model, [
            'status' => $isClosing ? 'closed' : 'open',
            'closed_by' => $isClosing ? $userId : null,
            'closed_at' => $isClosing ? now() : null,
        ]);
    }

    //===============================
    protected function validateDatesAndOverlap(array $data, ?int $ignoreId = null): void
    {
        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);

        if ($startDate->greaterThan($endDate)) {
            throw ValidationException::withMessages([
                'end_date' => __('The end date must be after or equal to the start date.'),
            ]);
        }

        if ($this->repository->hasOverlap((int) $data['entity_id'], $startDate->toDateString(), $endDate->toDateString(), $ignoreId)) {
            throw ValidationException::withMessages([
                'start_date' => __('This fiscal year overlaps another fiscal year for the selected entity.'),
            ]);
        }
    }

    //===============================
    protected function prepareData(array $input): array
    {
        $data = $input;

        $nameAr = trim((string) ($input['name_ar'] ?? ''));
        $nameEn = trim((string) ($input['name_en'] ?? '')) ?: $nameAr;

        $data['ar'] = [
            'name' => $nameAr,
            'description' => $input['description_ar'] ?? null,
        ];
        $data['en'] = [
            'name' => $nameEn,
            'description' => $input['description_en'] ?? null,
        ];

        foreach (['name_ar', 'name_en', 'description_ar', 'description_en'] as $field) {
            unset($data[$field]);
        }

        return $data;
    }
}
