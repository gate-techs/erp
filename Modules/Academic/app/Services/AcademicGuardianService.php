<?php

namespace Modules\Academic\Services;

use App\Enums\UserType;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\Academic\Models\AcademicGuardian;
use Modules\Academic\Repositories\Contracts\AcademicGuardianRepositoryInterface;
use Modules\HR\Models\Employee;

class AcademicGuardianService
{
    public function __construct(
        protected AcademicGuardianRepositoryInterface $repository
    ) {}

    //------------------------------------------
    public function getFilteredQuery(array $filters = []): Builder
    {
        return $this->repository->getFilteredQuery($filters);
    }

    //------------------------------------------
    public function all(): Collection
    {
        return $this->repository->all();
    }

    //------------------------------------------
    public function find(int $id): ?AcademicGuardian
    {
        return $this->repository->find($id);
    }

    //------------------------------------------
    public function findByUserId(int $userId): ?AcademicGuardian
    {
        return $this->repository->findByUserId($userId);
    }

    //------------------------------------------
    /**
     * Create a new guardian record and link/create user account.
     */
    public function create(array $data, ?UploadedFile $photo = null): AcademicGuardian
    {
        return DB::transaction(function () use ($data, $photo) {
            $photoPath = null;
            if ($photo) {
                $photoPath = upload_image($photo, 'academic/guardians');
            }

            $user = null;
            $existingUserId = $data['existing_user_id'] ?? null;

            if (! empty($existingUserId)) {
                // Scenario 1: Linking an existing user (e.g. Teacher or Employee)
                $user = User::find($existingUserId);
                if ($user) {
                    if (! $user->hasRole('guardian')) {
                        $user->assignRole('guardian');
                    }
                    if ($photoPath && empty($user->avatar)) {
                        $user->update(['avatar' => $photoPath]);
                    }
                }
            } else {
                // Scenario 2: Creating a new user account for the guardian
                $name = $data['name_ar'] ?? $data['name_en'] ?? __('Guardian');
                $user = User::create([
                    'name'      => $name,
                    'phone'     => $data['phone'] ?? null,
                    'email'     => ! empty($data['email']) ? $data['email'] : null,
                    'password'  => Hash::make($data['password'] ?? '12345678'),
                    'user_type' => UserType::GUARDIAN,
                    'is_active' => ($data['status'] ?? AcademicGuardian::STATUS_ACTIVE) === AcademicGuardian::STATUS_ACTIVE,
                    'avatar'    => $photoPath,
                ]);

                $user->assignRole('guardian');
            }

            $payload = [
                'user_id'        => $user?->id,
                'civil_id'       => $data['civil_id'] ?? null,
                'institution_id' => $data['institution_id'],
                'branch_id'      => $data['branch_id'] ?? null,
                'phone_code'     => $data['phone_code'] ?? '+966',
                'phone'          => $data['phone'],
                'email'          => $data['email'] ?? null,
                'photo'          => $photoPath ?? $user?->avatar,
                'status'         => $data['status'] ?? AcademicGuardian::STATUS_ACTIVE,
                'created_by'     => auth()->id(),
                'ar'             => [
                    'name' => $data['name_ar'] ?? $user?->name ?? '',
                ],
                'en'             => [
                    'name' => $data['name_en'] ?? $data['name_ar'] ?? $user?->name ?? '',
                ],
            ];

            return $this->repository->create($payload);
        });
    }

    //------------------------------------------
    /**
     * Update an existing guardian record and sync user account.
     */
    public function update(int $id, array $data, ?UploadedFile $photo = null): ?AcademicGuardian
    {
        return DB::transaction(function () use ($id, $data, $photo) {
            $guardian = $this->find($id);
            if (! $guardian) {
                return null;
            }

            $photoPath = $guardian->photo;
            if ($photo) {
                $photoPath = upload_image($photo, 'academic/guardians', $guardian->photo);
                $data['photo'] = $photoPath;
            }

            // Sync linked user
            if ($guardian->user) {
                $user = $guardian->user;
                $userUpdates = [
                    'name'      => $data['name_ar'] ?? $user->name,
                    'phone'     => $data['phone'] ?? $user->phone,
                    'is_active' => ($data['status'] ?? $guardian->status) === AcademicGuardian::STATUS_ACTIVE,
                ];

                if (! empty($data['email'])) {
                    $userUpdates['email'] = $data['email'];
                }

                if (! empty($data['password'])) {
                    $userUpdates['password'] = Hash::make($data['password']);
                }

                if ($photoPath) {
                    $userUpdates['avatar'] = $photoPath;
                }

                $user->update($userUpdates);
            }

            $payload = [
                'civil_id'       => array_key_exists('civil_id', $data) ? $data['civil_id'] : $guardian->civil_id,
                'institution_id' => $data['institution_id'] ?? $guardian->institution_id,
                'branch_id'      => $data['branch_id'] ?? $guardian->branch_id,
                'phone_code'     => $data['phone_code'] ?? $guardian->phone_code,
                'phone'          => $data['phone'] ?? $guardian->phone,
                'email'          => $data['email'] ?? $guardian->email,
                'photo'          => $photoPath,
                'status'         => $data['status'] ?? $guardian->status,
                'ar'             => [
                    'name' => $data['name_ar'] ?? $guardian->translate('ar')?->name ?? '',
                ],
                'en'             => [
                    'name' => $data['name_en'] ?? $data['name_ar'] ?? $guardian->translate('en')?->name ?? '',
                ],
            ];

            return $this->repository->update($id, $payload);
        });
    }

    //------------------------------------------
    /**
     * Delete a guardian record.
     */
    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    //------------------------------------------
    /**
     * Toggle guardian active status and sync user account.
     */
    public function toggleStatus(int $id): ?AcademicGuardian
    {
        return DB::transaction(function () use ($id) {
            $guardian = $this->repository->toggleStatus($id);
            if ($guardian && $guardian->user) {
                $guardian->user->update([
                    'is_active' => $guardian->status === AcademicGuardian::STATUS_ACTIVE,
                ]);
            }

            return $guardian;
        });
    }

    //------------------------------------------
    /**
     * Retrieve available employees/teachers that can be linked as guardians.
     */
    public function getAvailableEmployees(?int $branchId = null): Collection
    {
        $query = Employee::query()
            ->with(['translations', 'user', 'jobTitle.translations'])
            ->whereNotNull('user_id')
            ->where('status', 'active');

        if ($branchId) {
            $query->where('primary_branch_id', $branchId);
        }

        return $query->get();
    }
}
