<?php

namespace Modules\EmployeePortal\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\EmployeePortal\Repositories\Contracts\PortalEmployeeRepositoryInterface;
use Modules\EmployeePortal\Services\Contracts\PortalLeaveServiceInterface;
use Modules\HR\Enums\LeaveRequestStatus;
use Modules\HR\Enums\LeaveType;
use Modules\HR\Models\Employee;
use Modules\HR\Models\LeaveRequest;
use Modules\HR\Services\LeaveRequestService;

class PortalLeaveService implements PortalLeaveServiceInterface
{
    public function __construct(
        protected PortalEmployeeRepositoryInterface $employeeRepo,
        protected LeaveRequestService $hrLeaveService
    ) {}

    /**
     * Resolve employee from user.
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
     * Get data required for submitting a new leave request.
     */
    public function getLeaveFormContext(?User $user): array
    {
        $employee = $this->resolveCurrentEmployee($user);

        // Fetch annual leave balance
        $annualBalance = 18;
        if ($employee) {
            try {
                $balanceData = $this->hrLeaveService->checkBalance($employee->id, LeaveType::ANNUAL->value);
                $annualBalance = $balanceData['remaining_balance'] ?? 18;
            } catch (\Throwable $e) {
                $annualBalance = 18;
            }
        }

        $leaveTypes = LeaveType::cases();

        return [
            'employee'      => $employee,
            'user'          => $user,
            'annualBalance' => $annualBalance,
            'leaveTypes'    => $leaveTypes,
        ];
    }

    /**
     * Calculate days and check balance in real-time.
     */
    public function calculateDaysAndBalance(?User $user, string $leaveType, string $startDate, string $endDate): array
    {
        $employee = $this->resolveCurrentEmployee($user);
        if (!$employee) {
            return [
                'success' => false,
                'message' => __('Employee record not found.'),
            ];
        }

        try {
            $daysData = $this->hrLeaveService->calculateLeaveDays($employee->id, $startDate, $endDate);
            $balanceData = $this->hrLeaveService->checkBalance($employee->id, $leaveType);

            // Compute return date: 1 day after end date
            $returnDate = Carbon::parse($endDate)->addDay()->format('Y-m-d');

            return [
                'success'           => true,
                'working_days'      => $daysData['working_days'] ?? 1,
                'calendar_days'     => $daysData['calendar_days'] ?? 1,
                'return_date'       => $returnDate,
                'return_date_human' => Carbon::parse($returnDate)->format('d/m/Y'),
                'remaining_balance' => $balanceData['remaining_balance'] ?? 0,
                'is_eligible'       => $balanceData['is_eligible'] ?? true,
                'ineligibility_reason' => $balanceData['ineligibility_reason'] ?? null,
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Store and submit a new leave request for the employee.
     */
    public function submitLeaveRequest(array $data, ?User $user): array
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

            $attachmentPath = null;
            if (isset($data['attachment']) && $data['attachment'] instanceof \Illuminate\Http\UploadedFile) {
                $attachmentPath = $data['attachment']->store('leaves/attachments', 'public');
            }

            // Calculate days
            $daysResult = $this->hrLeaveService->calculateLeaveDays($employee->id, $data['start_date'], $data['end_date']);
            $workingDays = $daysResult['working_days'] ?? 1;

            $leaveRequest = LeaveRequest::create([
                'employee_id'             => $employee->id,
                'leave_type'              => $data['leave_type'],
                'start_date'              => $data['start_date'],
                'end_date'                => $data['end_date'],
                'days_count'              => $workingDays,
                'emergency_contact_phone' => $data['emergency_contact_phone'] ?? null,
                'replacement_employee_id' => $data['replacement_employee_id'] ?? null,
                'reason'                  => $data['reason'] ?? null,
                'attachment'              => $attachmentPath,
                'status'                  => LeaveRequestStatus::PENDING->value,
                'created_by'              => $user?->id,
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => __('تم تقديم طلب الإجازة بنجاح وهو قيد الاعتماد!'),
                'data'    => $leaveRequest,
            ];
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('PortalLeaveService submit error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => __('حدث خطأ أثناء تقديم الطلب: ') . $e->getMessage(),
            ];
        }
    }
}
