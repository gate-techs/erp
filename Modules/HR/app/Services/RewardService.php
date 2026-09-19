<?php

namespace Modules\HR\Services;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\HR\Enums\RewardCalculationType;
use Modules\HR\Enums\RewardPaymentMethod;
use Modules\HR\Enums\RewardStatus;
use Modules\HR\Models\Employee;
use Modules\HR\Models\HrSetting;
use Modules\HR\Models\Reward;
use Modules\HR\Repositories\Contracts\RewardRepositoryInterface;

class RewardService
{
    public function __construct(
        protected RewardRepositoryInterface $rewardRepo
    ) {}

    /**
     * Get DataTable query with applied filters.
     */
    public function getDataTableQuery(array $filters = [])
    {
        return $this->rewardRepo->getDataTableQuery($filters);
    }

    /**
     * Get KPI summary metrics.
     */
    public function getKPIs(): array
    {
        return $this->rewardRepo->getKPIs();
    }

    /**
     * Find reward by ID.
     */
    public function getById(int $id, array $with = ['employee.translations', 'rewardType.translations', 'creator', 'approver', 'payer', 'rejecter']): ?Reward
    {
        return $this->rewardRepo->findById($id, $with);
    }

    /**
     * Live calculate reward amount based on employee salary and calculation mode.
     */
    public function calculateAmount(int $employeeId, string $calculationType, float $calculationValue): array
    {
        $employee = Employee::with('translations')->find($employeeId);
        if (!$employee) {
            throw new Exception(__('Employee not found.'));
        }

        $basicSalary = (float) ($employee->basic_salary ?? 0.0);
        $dailyRate   = round($basicSalary / 30, 2);
        $amount      = 0.0;
        $formula     = '';

        switch ($calculationType) {
            case RewardCalculationType::FIXED_AMOUNT->value:
                $amount  = round($calculationValue, 2);
                $formula = number_format($amount, 2) . ' ' . __('KWD');
                break;

            case RewardCalculationType::PERCENTAGE->value:
                $amount  = round(($basicSalary * ($calculationValue / 100)), 2);
                $formula = $calculationValue . '% × ' . number_format($basicSalary, 2) . ' ' . __('KWD') . ' = ' . number_format($amount, 2) . ' ' . __('KWD');
                break;

            case RewardCalculationType::DAYS_MULTIPLIER->value:
                $amount  = round($dailyRate * $calculationValue, 2);
                $formula = $calculationValue . ' ' . __('Days') . ' × ' . number_format($dailyRate, 2) . ' ' . __('KWD') . ' = ' . number_format($amount, 2) . ' ' . __('KWD');
                break;

            default:
                $amount  = round($calculationValue, 2);
                $formula = number_format($amount, 2) . ' ' . __('KWD');
                break;
        }

        return [
            'employee_id'       => $employee->id,
            'employee_name'     => $employee->name,
            'basic_salary'      => $basicSalary,
            'daily_rate'        => $dailyRate,
            'calculation_type'  => $calculationType,
            'calculation_value' => $calculationValue,
            'amount'            => $amount,
            'amount_formatted'  => number_format($amount, 2) . ' ' . __('KWD'),
            'formula'           => $formula,
        ];
    }

    /**
     * Create a new reward entry with verified calculations.
     */
    public function createReward(array $data, ?UploadedFile $attachment = null, int $userId = 1): Reward
    {
        return DB::transaction(function () use ($data, $attachment, $userId) {
            $employeeId       = (int) $data['employee_id'];
            $calcType         = $data['calculation_type'];
            $calcValue        = (float) $data['calculation_value'];

            // Calculate precise amount server-side
            $calcResult = $this->calculateAmount($employeeId, $calcType, $calcValue);
            $amount     = $calcResult['amount'];

            // Handle optional file upload
            $attachmentPath = null;
            if ($attachment) {
                $attachmentPath = $attachment->store('rewards/attachments', 'public');
            }

            // Determine payroll month if payment method is NEXT_PAYROLL
            $payrollMonth = null;
            if (($data['payment_method'] ?? '') === RewardPaymentMethod::NEXT_PAYROLL->value) {
                $payrollMonth = !empty($data['payroll_month'])
                    ? $data['payroll_month']
                    : now()->addMonth()->format('Y-m');
            }

            return $this->rewardRepo->create([
                'employee_id'       => $employeeId,
                'reward_type_id'    => (int) $data['reward_type_id'],
                'calculation_type'  => $calcType,
                'calculation_value' => $calcValue,
                'amount'            => $amount,
                'award_date'        => $data['award_date'],
                'payment_method'    => $data['payment_method'] ?? RewardPaymentMethod::NEXT_PAYROLL->value,
                'payroll_month'     => $payrollMonth,
                'status'            => RewardStatus::PENDING->value,
                'reason'            => $data['reason'],
                'notes'             => $data['notes'] ?? null,
                'attachment'        => $attachmentPath,
                'created_by'        => $userId,
            ]);
        });
    }

    /**
     * Approve reward.
     */
    public function approveReward(Reward $reward, int $userId): bool
    {
        if ($reward->status !== RewardStatus::PENDING) {
            throw new Exception(__('Only pending rewards can be approved.'));
        }

        return $this->rewardRepo->update($reward, [
            'status'      => RewardStatus::APPROVED->value,
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);
    }

    /**
     * Mark reward as paid / disbursed.
     */
    public function payReward(Reward $reward, int $userId, ?string $notes = null): bool
    {
        if (!in_array($reward->status, [RewardStatus::PENDING, RewardStatus::APPROVED])) {
            throw new Exception(__('This reward cannot be marked as paid in its current status.'));
        }

        $updateData = [
            'status'  => RewardStatus::PAID->value,
            'paid_by' => $userId,
            'paid_at' => now(),
        ];

        if ($reward->status === RewardStatus::PENDING) {
            $updateData['approved_by'] = $userId;
            $updateData['approved_at'] = now();
        }

        if ($notes) {
            $existingNotes = $reward->notes ? $reward->notes . "\n" : '';
            $updateData['notes'] = $existingNotes . __('Payment Notes: ') . $notes;
        }

        return $this->rewardRepo->update($reward, $updateData);
    }

    /**
     * Reject reward with reason.
     */
    public function rejectReward(Reward $reward, int $userId, string $reason): bool
    {
        if ($reward->status === RewardStatus::PAID) {
            throw new Exception(__('Paid rewards cannot be rejected.'));
        }

        return $this->rewardRepo->update($reward, [
            'status'           => RewardStatus::REJECTED->value,
            'rejected_by'      => $userId,
            'rejected_at'      => now(),
            'rejection_reason' => $reason,
        ]);
    }

    /**
     * Delete a reward.
     */
    public function deleteReward(Reward $reward): bool
    {
        if ($reward->status === RewardStatus::PAID) {
            throw new Exception(__('Cannot delete a reward that has already been paid.'));
        }

        if ($reward->attachment && Storage::disk('public')->exists($reward->attachment)) {
            Storage::disk('public')->delete($reward->attachment);
        }

        return $this->rewardRepo->delete($reward);
    }
}
