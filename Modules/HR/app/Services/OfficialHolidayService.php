<?php

namespace Modules\HR\Services;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Modules\HR\Enums\OfficialHolidayStatus;
use Modules\HR\Models\OfficialHoliday;
use Modules\HR\Repositories\Contracts\OfficialHolidayRepositoryInterface;
use Modules\Nursery\Models\NurseryBranch;

class OfficialHolidayService
{
    //======================
    public function __construct(
        protected OfficialHolidayRepositoryInterface $officialHolidayRepository
    ) {}

    //======================
    /**
     * Get query builder instance for server-side DataTables.
     */
    public function getDataTableQuery(array $filters = []): Builder
    {
        return $this->officialHolidayRepository->getDataTableQuery($filters);
    }

    //======================
    /**
     * Lookup form data needed for offcanvases and filters.
     *
     * @return array<string, mixed>
     */
    public function getFormData(): array
    {
        $currentYear = (int) date('Y');
        $years = range($currentYear - 2, $currentYear + 3);

        return [
            'statuses' => OfficialHolidayStatus::cases(),
            'branches' => class_exists(NurseryBranch::class) ? NurseryBranch::with('translations')->get() : collect(),
            'years'    => $years,
        ];
    }

    //======================
    /**
     * Find holiday by ID.
     */
    public function getHolidayById(int $id): ?OfficialHoliday
    {
        return $this->officialHolidayRepository->findById($id);
    }

    //======================
    /**
     * Create a new official holiday.
     */
    public function createHoliday(array $validated, ?int $userId = null): OfficialHoliday
    {
        $startDate = Carbon::parse($validated['start_date']);
        $endDate   = Carbon::parse($validated['end_date']);
        $daysCount = $startDate->diffInDays($endDate) + 1;
        $year      = (int) $startDate->format('Y');

        $payload = array_merge(
            $this->formatTranslationData($validated),
            [
                'start_date'   => $startDate->format('Y-m-d'),
                'end_date'     => $endDate->format('Y-m-d'),
                'days_count'   => $daysCount,
                'year'         => $year,
                'is_recurring' => !empty($validated['is_recurring']),
                'branch_id'    => !empty($validated['branch_id']) ? (int) $validated['branch_id'] : null,
                'color'        => $validated['color'] ?? '#e83e8c',
                'status'       => $validated['status'] ?? OfficialHolidayStatus::ACTIVE->value,
                'created_by'   => $userId,
            ]
        );

        return $this->officialHolidayRepository->create($payload);
    }

    //======================
    /**
     * Update an existing official holiday.
     */
    public function updateHoliday(OfficialHoliday $holiday, array $validated): OfficialHoliday
    {
        $startDate = Carbon::parse($validated['start_date']);
        $endDate   = Carbon::parse($validated['end_date']);
        $daysCount = $startDate->diffInDays($endDate) + 1;
        $year      = (int) $startDate->format('Y');

        $payload = array_merge(
            $this->formatTranslationData($validated),
            [
                'start_date'   => $startDate->format('Y-m-d'),
                'end_date'     => $endDate->format('Y-m-d'),
                'days_count'   => $daysCount,
                'year'         => $year,
                'is_recurring' => !empty($validated['is_recurring']),
                'branch_id'    => !empty($validated['branch_id']) ? (int) $validated['branch_id'] : null,
                'color'        => $validated['color'] ?? $holiday->color,
                'status'       => $validated['status'] ?? $holiday->status->value,
            ]
        );

        return $this->officialHolidayRepository->update($holiday, $payload);
    }

    //======================
    /**
     * Delete an official holiday.
     */
    public function deleteHoliday(OfficialHoliday $holiday): bool
    {
        return $this->officialHolidayRepository->delete($holiday);
    }

    //======================
    /**
     * Format flat inputs into Astrotomic Translatable structure.
     *
     * @param array $data
     * @return array
     */
    public function formatTranslationData(array $data): array
    {
        $formatted = [];

        if (isset($data['name_ar']) || isset($data['description_ar'])) {
            $formatted['ar'] = [
                'name'        => $data['name_ar'] ?? '',
                'description' => $data['description_ar'] ?? null,
            ];
        }

        if (isset($data['name_en']) || isset($data['description_en'])) {
            $formatted['en'] = [
                'name'        => $data['name_en'] ?? '',
                'description' => $data['description_en'] ?? null,
            ];
        }

        return $formatted;
    }
}
