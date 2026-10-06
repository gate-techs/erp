<?php

namespace Modules\EmployeePortal\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Modules\EmployeePortal\Repositories\Contracts\PortalEmployeeRepositoryInterface;
use Modules\EmployeePortal\Services\Contracts\PortalProfileServiceInterface;
use Modules\HR\Models\Employee;

class PortalProfileService implements PortalProfileServiceInterface
{
    public function __construct(
        protected PortalEmployeeRepositoryInterface $employeeRepo
    ) {}

    /**
     * Resolve employee model from user or fallback.
     */
    public function resolveCurrentEmployee(?User $user): ?Employee
    {
        if ($user) {
            $employee = $this->employeeRepo->findByUserId($user->id);
            if ($employee) {
                return $employee;
            }
        }

        return $this->employeeRepo->getActiveFallback();
    }

    /**
     * Get profile and edit view data for current employee/user.
     */
    public function getProfileEditData(?User $user): array
    {
        $employee = $this->resolveCurrentEmployee($user);
        $branches = \Modules\Nursery\Models\NurseryBranch::with('translations')->get();

        return [
            'employee' => $employee,
            'user'     => $user,
            'branches' => $branches,
        ];
    }

    /**
     * Update employee personal and contact details.
     */
    public function updateProfile(array $data, ?User $user): array
    {
        $employee = $this->resolveCurrentEmployee($user);

        if (!$employee) {
            return [
                'success' => false,
                'message' => __('لم يتم العثور على سجل الموظف الخاص بك.'),
            ];
        }

        try {
            DB::beginTransaction();

            $updatePayload = [];

            // Handle Avatar upload if provided
            if (isset($data['avatar']) && $data['avatar'] instanceof \Illuminate\Http\UploadedFile) {
                $file = $data['avatar'];
                $path = $file->store('employees/avatars', 'public');
                $updatePayload['avatar'] = $path;

                // Sync with user avatar if exists
                if ($user) {
                    $user->update(['avatar' => $path]);
                }
            }

            // Translatable and Core attributes
            if (!empty($data['name_ar'])) {
                $updatePayload['ar'] = array_merge($updatePayload['ar'] ?? [], [
                    'name' => $data['name_ar'],
                ]);
            }

            if (!empty($data['name_en'])) {
                $updatePayload['en'] = array_merge($updatePayload['en'] ?? [], [
                    'name' => $data['name_en'],
                ]);
            }

            // Personal & Branch attributes
            if (isset($data['date_of_birth'])) {
                $updatePayload['date_of_birth'] = $data['date_of_birth'];
            }
            if (isset($data['nationality'])) {
                $updatePayload['nationality'] = $data['nationality'];
            }
            if (isset($data['marital_status'])) {
                $updatePayload['marital_status'] = $data['marital_status'];
            }
            if (isset($data['primary_branch_id'])) {
                $updatePayload['primary_branch_id'] = $data['primary_branch_id'];
            }

            // Contact attributes
            if (isset($data['phone'])) {
                $updatePayload['phone'] = $data['phone'];
            }
            if (isset($data['alternate_phone'])) {
                $updatePayload['alternate_phone'] = $data['alternate_phone'];
            }
            if (isset($data['email'])) {
                $updatePayload['email'] = $data['email'];
                if ($user && $user->email !== $data['email']) {
                    $user->update(['email' => $data['email']]);
                }
            }
            if (isset($data['emergency_contact_name'])) {
                $updatePayload['emergency_contact_name'] = $data['emergency_contact_name'];
            }
            if (isset($data['emergency_contact_relation'])) {
                $updatePayload['emergency_contact_relation'] = $data['emergency_contact_relation'];
            }
            if (isset($data['emergency_contact_phone'])) {
                $updatePayload['emergency_contact_phone'] = $data['emergency_contact_phone'];
            }

            // Password update
            if (!empty($data['password']) && $user) {
                $user->update([
                    'password' => \Illuminate\Support\Facades\Hash::make($data['password']),
                ]);
            }

            // Update Employee via Repository
            $updatedEmployee = $this->employeeRepo->update($employee, $updatePayload);

            DB::commit();

            return [
                'success'  => true,
                'message'  => __('تم حفظ وتحديث البيانات بنجاح.'),
                'employee' => $updatedEmployee,
            ];
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('PortalProfileService update error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => __('حدث خطأ أثناء حفظ البيانات: ') . $e->getMessage(),
            ];
        }
    }
}
