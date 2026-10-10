<?php

namespace Modules\Academic\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Academic\Models\AcademicGuardian;
use Modules\Academic\Repositories\Contracts\AcademicGuardianRepositoryInterface;

class AcademicGuardianRepository implements AcademicGuardianRepositoryInterface
{
    //------------------------------------------
    public function getFilteredQuery(array $filters = []): Builder
    {
        $query = AcademicGuardian::query()
            ->withCount('students')
            ->with([
                'translations',
                'institution.translations',
                'branch.translations',
                'user',
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
                        ->orWhere('phone', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('email', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('guardian_code', 'LIKE', "%{$searchTerm}%")
                        ->orWhereHas('user', function ($uq) use ($searchTerm) {
                            $uq->where('name', 'LIKE', "%{$searchTerm}%")
                                ->orWhere('phone', 'LIKE', "%{$searchTerm}%")
                                ->orWhere('email', 'LIKE', "%{$searchTerm}%");
                        });
                });
            }
        }

        return $query->latest('id');
    }

    //------------------------------------------
    public function all(): Collection
    {
        return AcademicGuardian::with(['translations', 'institution', 'branch', 'user'])
            ->latest('id')
            ->get();
    }

    //------------------------------------------
    public function find(int $id): ?AcademicGuardian
    {
        return AcademicGuardian::with(['translations', 'institution.translations', 'branch.translations', 'user'])
            ->find($id);
    }

    //------------------------------------------
    public function findByUserId(int $userId): ?AcademicGuardian
    {
        return AcademicGuardian::with(['translations', 'institution.translations', 'branch.translations', 'user'])
            ->where('user_id', $userId)
            ->first();
    }

    //------------------------------------------
    public function findByPhone(string $phone): ?AcademicGuardian
    {
        return AcademicGuardian::with(['translations', 'user'])
            ->where('phone', $phone)
            ->first();
    }

    //------------------------------------------
    public function create(array $data): AcademicGuardian
    {
        if (empty($data['guardian_code'])) {
            $data['guardian_code'] = $this->generateGuardianCode();
        }

        return AcademicGuardian::create($data);
    }

    //------------------------------------------
    public function update(int $id, array $data): ?AcademicGuardian
    {
        $guardian = $this->find($id);
        if (! $guardian) {
            return null;
        }

        $guardian->update($data);

        return $guardian->fresh(['translations', 'institution', 'branch', 'user']);
    }

    //------------------------------------------
    public function delete(int $id): bool
    {
        $guardian = $this->find($id);
        if (! $guardian) {
            return false;
        }

        return (bool) $guardian->delete();
    }

    //------------------------------------------
    public function toggleStatus(int $id): ?AcademicGuardian
    {
        $guardian = $this->find($id);
        if (! $guardian) {
            return null;
        }

        $newStatus = $guardian->status === AcademicGuardian::STATUS_ACTIVE
            ? AcademicGuardian::STATUS_INACTIVE
            : AcademicGuardian::STATUS_ACTIVE;

        $guardian->update(['status' => $newStatus]);

        return $guardian->fresh();
    }

    //------------------------------------------
    public function generateGuardianCode(): string
    {
        $lastId = DB::table('academic_guardians')->max('id') ?? 0;
        $nextNumber = $lastId + 1;

        return sprintf('GRD-%04d', $nextNumber);
    }
}
