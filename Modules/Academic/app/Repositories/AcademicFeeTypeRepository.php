<?php

namespace Modules\Academic\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Academic\Models\AcademicFeeType;
use Modules\Academic\Repositories\Contracts\AcademicFeeTypeRepositoryInterface;

class AcademicFeeTypeRepository implements AcademicFeeTypeRepositoryInterface
{
    public function getFilteredQuery(array $filters = []): Builder
    {
        $query = AcademicFeeType::query()
            ->with([
                'translations',
                'institution.translations',
                'branch.translations',
            ]);

        if (! empty($filters['institution_id'])) {
            $query->where('institution_id', $filters['institution_id']);
        }

        if (! empty($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
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
                      ->orWhere('code', 'like', "%{$searchTerm}%");
                });
            }
        }

        return $query;
    }

    public function all(): Collection
    {
        return AcademicFeeType::with([
            'translations',
            'institution',
            'branch',
        ])->orderBy('order')->get();
    }

    public function find(int $id): ?AcademicFeeType
    {
        return AcademicFeeType::with([
            'translations',
            'institution.translations',
            'branch.translations',
        ])->find($id);
    }

    public function create(array $data): AcademicFeeType
    {
        return DB::transaction(function () use ($data) {
            $type = new AcademicFeeType();
            $type->institution_id = $data['institution_id'] ?? null;
            $type->branch_id      = $data['branch_id'] ?? null;
            $type->code           = $data['code'] ?? null;
            $type->order          = $data['order'] ?? 0;
            $type->status         = $data['status'] ?? AcademicFeeType::STATUS_ACTIVE;
            $type->created_by     = $data['created_by'] ?? null;

            foreach (['ar', 'en'] as $locale) {
                if (isset($data[$locale]) && is_array($data[$locale])) {
                    $type->translateOrNew($locale)->name = $data[$locale]['name'] ?? '';
                    $type->translateOrNew($locale)->description = $data[$locale]['description'] ?? null;
                }
            }

            $type->save();

            return $type;
        });
    }

    public function update(int $id, array $data): ?AcademicFeeType
    {
        return DB::transaction(function () use ($id, $data) {
            $type = AcademicFeeType::find($id);
            if (! $type) {
                return null;
            }

            if (array_key_exists('institution_id', $data)) {
                $type->institution_id = $data['institution_id'];
            }
            if (array_key_exists('branch_id', $data)) {
                $type->branch_id = $data['branch_id'];
            }
            if (isset($data['code'])) {
                $type->code = $data['code'];
            }
            if (isset($data['order'])) {
                $type->order = $data['order'];
            }
            if (isset($data['status'])) {
                $type->status = $data['status'];
            }

            foreach (['ar', 'en'] as $locale) {
                if (isset($data[$locale]) && is_array($data[$locale])) {
                    $type->translateOrNew($locale)->name = $data[$locale]['name'] ?? $type->translateOrNew($locale)->name;
                    $type->translateOrNew($locale)->description = $data[$locale]['description'] ?? $type->translateOrNew($locale)->description;
                }
            }

            $type->save();

            return $type;
        });
    }

    public function delete(int $id): bool
    {
        $type = AcademicFeeType::find($id);
        if ($type) {
            return (bool) $type->delete();
        }

        return false;
    }

    public function toggleStatus(int $id): ?AcademicFeeType
    {
        $type = AcademicFeeType::find($id);
        if ($type) {
            $type->status = ($type->status === AcademicFeeType::STATUS_ACTIVE)
                ? AcademicFeeType::STATUS_INACTIVE
                : AcademicFeeType::STATUS_ACTIVE;
            $type->save();

            return $type;
        }

        return null;
    }
}
