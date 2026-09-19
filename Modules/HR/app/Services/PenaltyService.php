<?php

namespace Modules\HR\Services;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\HR\Enums\PenaltyCalculationType;
use Modules\HR\Enums\PenaltyDeductionMethod;
use Modules\HR\Enums\PenaltyStatus;
use Modules\HR\Models\Employee;
use Modules\HR\Models\Penalty;
use Modules\HR\Repositories\Contracts\PenaltyRepositoryInterface;

class PenaltyService
{
    public function __construct(
        protected PenaltyRepositoryInterface $penaltyRepo
    ) {}

    /**
     * Get DataTable query with applied filters.
     */
    public function getDataTableQuery(array $filters = [])
    {
        return $this->penaltyRepo->getDataTableQuery($filters);
    }

    /**
     * Get KPI summary metrics.
     */
    public function getKPIs(): array
    {
        return $this->penaltyRepo->getKPIs();
    }

    /**
     * Find penalty by ID.
     */
    public function getById(int $id, array $with = ['employee.translations', 'penaltyType.translations', 'creator', 'approver', 'deductor', 'canceller']): ?Penalty
    {
        return $this->penaltyRepo->findById($id, $with);
    }

    /**
     * Live calculate penalty amount based on employee salary and calculation mode.
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
            case PenaltyCalculationType::DAYS_DEDUCTION->value:
                $amount  = round($dailyRate * $calculationValue, 2);
                $formula = $calculationValue . ' ' . __('Days') . ' × ' . number_format($dailyRate, 2) . ' ' . __('KWD') . ' = ' . number_format($amount, 2) . ' ' . __('KWD');
                break;

            case PenaltyCalculationType::FIXED_AMOUNT->value:
                $amount  = round($calculationValue, 2);
                $formula = number_format($amount, 2) . ' ' . __('KWD');
                break;

            case PenaltyCalculationType::PERCENTAGE->value:
                $amount  = round(($basicSalary * ($calculationValue / 100)), 2);
                $formula = $calculationValue . '% × ' . number_format($basicSalary, 2) . ' ' . __('KWD') . ' = ' . number_format($amount, 2) . ' ' . __('KWD');
                break;

            case PenaltyCalculationType::WARNING_ONLY->value:
                $amount  = 0.0;
                $formula = __('Administrative Warning (No Financial Deduction)');
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
     * Create a new penalty entry with verified calculations.
     */
    public function createPenalty(array $data, ?UploadedFile $attachment = null, int $userId = 1): Penalty
    {
        return DB::transaction(function () use ($data, $attachment, $userId) {
            $employeeId       = (int) $data['employee_id'];
            $calcType         = $data['calculation_type'];
            $calcValue        = isset($data['calculation_value']) ? (float) $data['calculation_value'] : 0.0;

            // Calculate precise amount server-side
            $calcResult = $this->calculateAmount($employeeId, $calcType, $calcValue);
            $amount     = $calcResult['amount'];

            // Handle optional file upload
            $attachmentPath = null;
            if ($attachment) {
                $attachmentPath = $attachment->store('penalties/attachments', 'public');
            }

            // Determine payroll month if deduction method is NEXT_PAYROLL
            $payrollMonth = null;
            if (($data['deduction_method'] ?? '') === PenaltyDeductionMethod::NEXT_PAYROLL->value) {
                $payrollMonth = !empty($data['payroll_month'])
                    ? $data['payroll_month']
                    : now()->addMonth()->format('Y-m');
            }

            return $this->penaltyRepo->create([
                'employee_id'       => $employeeId,
                'penalty_type_id'   => (int) $data['penalty_type_id'],
                'calculation_type'  => $calcType,
                'calculation_value' => $calcType === PenaltyCalculationType::WARNING_ONLY->value ? null : $calcValue,
                'amount'            => $amount,
                'penalty_date'      => $data['penalty_date'],
                'deduction_method'  => $data['deduction_method'] ?? PenaltyDeductionMethod::NEXT_PAYROLL->value,
                'payroll_month'     => $payrollMonth,
                'status'            => PenaltyStatus::PENDING->value,
                'reason'            => $data['reason'],
                'notes'             => $data['notes'] ?? null,
                'attachment'        => $attachmentPath,
                'created_by'        => $userId,
            ]);
        });
    }

    /**
     * Approve penalty.
     */
    public function approvePenalty(Penalty $penalty, int $userId): bool
    {
        if ($penalty->status !== PenaltyStatus::PENDING) {
            throw new Exception(__('Only pending penalties can be approved.'));
        }

        return $this->penaltyRepo->update($penalty, [
            'status'      => PenaltyStatus::APPROVED->value,
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);
    }

    /**
     * Mark penalty as deducted.
     */
    public function deductPenalty(Penalty $penalty, int $userId, ?string $notes = null): bool
    {
        if (!in_array($penalty->status, [PenaltyStatus::PENDING, PenaltyStatus::APPROVED])) {
            throw new Exception(__('This penalty cannot be marked as deducted in its current status.'));
        }

        $updateData = [
            'status'      => PenaltyStatus::DEDUCTED->value,
            'deducted_by' => $userId,
            'deducted_at' => now(),
        ];

        if ($penalty->status === PenaltyStatus::PENDING) {
            $updateData['approved_by'] = $userId;
            $updateData['approved_at'] = now();
        }

        if ($notes) {
            $existingNotes = $penalty->notes ? $penalty->notes . "\n" : '';
            $updateData['notes'] = $existingNotes . __('Deduction Notes: ') . $notes;
        }

        return $this->penaltyRepo->update($penalty, $updateData);
    }

    /**
     * Cancel / waive penalty with reason.
     */
    public function cancelPenalty(Penalty $penalty, int $userId, string $reason): bool
    {
        if ($penalty->status === PenaltyStatus::DEDUCTED) {
            throw new Exception(__('Deducted penalties cannot be cancelled.'));
        }

        return $this->penaltyRepo->update($penalty, [
            'status'              => PenaltyStatus::CANCELLED->value,
            'cancelled_by'        => $userId,
            'cancelled_at'        => now(),
            'cancellation_reason' => $reason,
        ]);
    }

    /**
     * Delete a penalty.
     */
    public function deletePenalty(Penalty $penalty): bool
    {
        if ($penalty->status === PenaltyStatus::DEDUCTED) {
            throw new Exception(__('Cannot delete a penalty that has already been deducted.'));
        }

        if ($penalty->attachment && Storage::disk('public')->exists($penalty->attachment)) {
            Storage::disk('public')->delete($penalty->attachment);
        }

        return $this->penaltyRepo->delete($penalty);
    }
}
