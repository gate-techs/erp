<?php

namespace Modules\HR\Services;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\HR\Enums\ShiftStatus;
use Modules\HR\Models\Shift;
use Modules\HR\Repositories\Contracts\ShiftRepositoryInterface;

class ShiftService
{
    //======================
    public function __construct(
        protected ShiftRepositoryInterface $repository
    ) {}

    //======================
    /**
     * Get all shifts with optional filtering.
     */
    public function getAllShifts(array $filters = []): Collection
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
     * Get shift details by ID.
     */
    public function getShiftById(int $id): ?Shift
    {
        return $this->repository->findById($id);
    }

    //======================
    /**
     * Create a new shift with translations.
     */
    public function createShift(array $data, ?int $userId = null): Shift
    {
        if ($userId && !isset($data['created_by'])) {
            $data['created_by'] = $userId;
        }

        $data = $this->processShiftData($data);
        $formattedData = $this->formatTranslationData($data);

        return $this->repository->create($formattedData);
    }

    //======================
    /**
     * Update shift details.
     */
    public function updateShift(int|Shift $shift, array $data): Shift
    {
        $data = $this->processShiftData($data);
        $formattedData = $this->formatTranslationData($data);

        return $this->repository->update($shift, $formattedData);
    }

    //======================
    /**
     * Delete shift.
     */
    public function deleteShift(int|Shift $shift): bool
    {
        return $this->repository->delete($shift);
    }

    //======================
    /**
     * Get lookups needed for forms and filters.
     */
    public function getFormData(): array
    {
        return [
            'statuses' => ShiftStatus::cases(),
        ];
    }

    //======================
    /**
     * Calculate and validate shift hours and overnight detection.
     */
    protected function processShiftData(array $data): array
    {
        if (!empty($data['start_time']) && !empty($data['end_time'])) {
            $start = Carbon::parse($data['start_time']);
            $end = Carbon::parse($data['end_time']);

            // If end is before start, or is_overnight is explicitly checked
            $isOvernight = !empty($data['is_overnight']) || ($end->lessThan($start));
            $data['is_overnight'] = (bool) $isOvernight;

            if ($isOvernight && $end->lessThan($start)) {
                $end->addDay();
            }

            // Calculate total hours
            $minutes = $start->diffInMinutes($end);
            $data['total_hours'] = round($minutes / 60, 2);
        }

        // Set default color if empty
        if (empty($data['color'])) {
            $data['color'] = '#7367f0';
        }

        return $data;
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
