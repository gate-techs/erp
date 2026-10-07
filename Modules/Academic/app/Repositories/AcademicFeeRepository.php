<?php

namespace Modules\Academic\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Academic\Models\AcademicFee;
use Modules\Academic\Repositories\Contracts\AcademicFeeRepositoryInterface;

class AcademicFeeRepository implements AcademicFeeRepositoryInterface
{
    public function getFilteredQuery(array $filters = []): Builder
    {
        $query = AcademicFee::query()
            ->with([
                'translations',
                'institution.translations',
                'branch.translations',
                'academicYear.translations',
                'stage.translations',
                'feeType.translations',
            ]);

        if (! empty($filters['institution_id'])) {
            $query->where('institution_id', $filters['institution_id']);
        }

        if (! empty($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }

        if (! empty($filters['academic_year_id'])) {
            $query->where('academic_year_id', $filters['academic_year_id']);
        }

        if (! empty($filters['academic_stage_id'])) {
            $query->where('academic_stage_id', $filters['academic_stage_id']);
        }

        if (! empty($filters['academic_fee_type_id'])) {
            $query->where('academic_fee_type_id', $filters['academic_fee_type_id']);
        }

        if (! empty($filters['billing_period'])) {
            $query->where('billing_period', $filters['billing_period']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['search'])) {
            $searchTerm = is_array($filters['search'])
                ? ($filters['search']['value'] ?? null)
                : $filters['search'];

            if (! empty($searchTerm) && is_string($searchTerm)) {
                $query->where(function ($q) use ($searchTerm) {
                    $q->whereTranslationLike('name', "%{$searchTerm}%")
                      ->orWhereTranslationLike('description', "%{$searchTerm}%")
                      ->orWhere('amount', 'like', "%{$searchTerm}%")
                      ->orWhereHas('stage.translations', function ($sq) use ($searchTerm) {
                          $sq->where('name', 'like', "%{$searchTerm}%");
                      })
                      ->orWhereHas('feeType.translations', function ($fq) use ($searchTerm) {
                          $fq->where('name', 'like', "%{$searchTerm}%");
                      });
                });
            }
        }

        return $query;
    }

    public function all(): Collection
    {
        return AcademicFee::with([
            'translations',
            'institution',
            'branch',
            'academicYear',
            'stage',
            'feeType',
        ])->get();
    }

    public function find(int $id): ?AcademicFee
    {
        return AcademicFee::with([
            'translations',
            'institution.translations',
            'branch.translations',
            'academicYear.translations',
            'stage.translations',
            'feeType.translations',
        ])->find($id);
    }

    public function create(array $data): AcademicFee
    {
        return DB::transaction(function () use ($data) {
            $fee = new AcademicFee();
            $fee->institution_id       = $data['institution_id'];
            $fee->branch_id            = $data['branch_id'] ?? null;
            $fee->academic_year_id     = $data['academic_year_id'];
            $fee->academic_stage_id    = $data['academic_stage_id'];
            $fee->academic_fee_type_id = $data['academic_fee_type_id'];
            $fee->billing_period       = $data['billing_period'] ?? AcademicFee::PERIOD_MONTHLY;
            $fee->amount               = $data['amount'] ?? 0;
            $fee->image                = $data['image'] ?? null;
            $fee->status               = $data['status'] ?? AcademicFee::STATUS_ACTIVE;
            $fee->created_by           = $data['created_by'] ?? null;

            // Translations
            foreach (['ar', 'en'] as $locale) {
                if (isset($data[$locale]) && is_array($data[$locale])) {
                    $fee->translateOrNew($locale)->name = $data[$locale]['name'] ?? '';
                    $fee->translateOrNew($locale)->description = $data[$locale]['description'] ?? null;
                }
            }

            $fee->save();

            return $fee;
        });
    }

    public function update(int $id, array $data): ?AcademicFee
    {
        return DB::transaction(function () use ($id, $data) {
            $fee = AcademicFee::find($id);
            if (! $fee) {
                return null;
            }

            $fee->institution_id       = $data['institution_id'] ?? $fee->institution_id;
            $fee->branch_id            = array_key_exists('branch_id', $data) ? $data['branch_id'] : $fee->branch_id;
            $fee->academic_year_id     = $data['academic_year_id'] ?? $fee->academic_year_id;
            $fee->academic_stage_id    = $data['academic_stage_id'] ?? $fee->academic_stage_id;
            $fee->academic_fee_type_id = $data['academic_fee_type_id'] ?? $fee->academic_fee_type_id;
            $fee->billing_period       = $data['billing_period'] ?? $fee->billing_period;
            $fee->amount               = $data['amount'] ?? $fee->amount;
            if (isset($data['image'])) {
                $fee->image = $data['image'];
            }
            if (isset($data['status'])) {
                $fee->status = $data['status'];
            }

            // Translations
            foreach (['ar', 'en'] as $locale) {
                if (isset($data[$locale]) && is_array($data[$locale])) {
                    $fee->translateOrNew($locale)->name = $data[$locale]['name'] ?? $fee->translateOrNew($locale)->name;
                    $fee->translateOrNew($locale)->description = $data[$locale]['description'] ?? $fee->translateOrNew($locale)->description;
                }
            }

            $fee->save();

            return $fee;
        });
    }

    public function delete(int $id): bool
    {
        $fee = AcademicFee::find($id);
        if ($fee) {
            return (bool) $fee->delete();
        }

        return false;
    }

    public function toggleStatus(int $id): ?AcademicFee
    {
        $fee = AcademicFee::find($id);
        if ($fee) {
            $fee->status = ($fee->status === AcademicFee::STATUS_ACTIVE)
                ? AcademicFee::STATUS_INACTIVE
                : AcademicFee::STATUS_ACTIVE;
            $fee->save();

            return $fee;
        }

        return null;
    }
}
