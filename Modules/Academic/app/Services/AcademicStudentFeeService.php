<?php

namespace Modules\Academic\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Modules\Academic\Models\AcademicFeeInstallment;
use Modules\Academic\Models\AcademicFeePayment;
use Modules\Academic\Models\AcademicStudentFee;
use Modules\Academic\Repositories\Contracts\AcademicStudentFeeRepositoryInterface;

class AcademicStudentFeeService
{
    public function __construct(
        protected AcademicStudentFeeRepositoryInterface $repository
    ) {}

    public function all(array $filters = []): Collection
    {
        return $this->repository->all($filters);
    }

    public function getFilteredQuery(array $filters = []): Builder
    {
        return $this->repository->getFilteredQuery($filters);
    }

    public function getPaymentsQuery(array $filters = []): Builder
    {
        return $this->repository->getPaymentsQuery($filters);
    }

    public function find(int $id): ?AcademicStudentFee
    {
        return $this->repository->find($id);
    }

    public function update(int $id, array $data): AcademicStudentFee
    {
        return DB::transaction(function () use ($id, $data) {
            $studentFee = $this->repository->find($id);
            abort_unless($studentFee, 404, __('Student fee assignment not found.'));

            $hasPayments = $studentFee->payments->isNotEmpty();
            if ($hasPayments && (
                (int) $data['installments_count'] !== (int) $studentFee->installments_count
                || (string) $data['start_date'] !== optional($studentFee->start_date)->format('Y-m-d')
            )) {
                abort(422, __('Installment count and start date cannot be changed after recording payments.'));
            }

            $payload = [
                'academic_student_id' => $data['academic_student_id'],
                'academic_fee_id' => $data['academic_fee_id'],
                'total_amount' => (float) $data['total_amount'],
                'installments_count' => (int) $data['installments_count'],
                'installment_amount' => round((float) $data['total_amount'] / (int) $data['installments_count'], 2),
                'start_date' => $data['start_date'],
            ];
            $updated = $this->repository->update($studentFee, $payload);

            if (! $hasPayments) {
                $this->repository->syncInstallments(
                    $updated,
                    (float) $payload['total_amount'],
                    (int) $payload['installments_count'],
                    $payload['start_date']
                );
            }

            return $this->repository->find($id);
        });
    }

    public function delete(int $id): bool
    {
        $studentFee = $this->repository->find($id);
        abort_unless($studentFee, 404, __('Student fee assignment not found.'));
        return $this->repository->delete($studentFee);
    }

    public function create(array $data): AcademicStudentFee
    {
        return DB::transaction(function () use ($data) {
            $fee = $this->repository->findFee($data['academic_fee_id']);
            abort_unless($fee, 404, __('Academic fee not found.'));
            $total = array_key_exists('total_amount', $data) && $data['total_amount'] !== null
                ? (float) $data['total_amount']
                : (float) $fee->amount;
            $count = (int) $data['installments_count'];
            $baseAmount = round($total / $count, 2);

            $studentFee = $this->repository->create([
                'academic_student_id' => $data['academic_student_id'],
                'academic_fee_id' => $data['academic_fee_id'],
                'total_amount' => $total,
                'installments_count' => $count,
                'installment_amount' => $baseAmount,
                'start_date' => $data['start_date'],
                'status' => AcademicStudentFee::STATUS_ACTIVE,
                'created_by' => auth()->id(),
            ]);

            $startDate = \Carbon\Carbon::parse($data['start_date']);
            for ($number = 1; $number <= $count; $number++) {
                $amount = $number === $count
                    ? round($total - ($baseAmount * ($count - 1)), 2)
                    : $baseAmount;

                $installment = $this->repository->createInstallment($studentFee, [
                    'installment_number' => $number,
                    'due_date' => $startDate->copy()->addMonths($number - 1)->toDateString(),
                    'amount' => $amount,
                    'paid_amount' => 0,
                    'status' => AcademicFeeInstallment::STATUS_PENDING,
                ]);

                $paymentSequence = $this->repository->nextPaymentId();
                $this->repository->createPayment($studentFee, [
                    'academic_fee_installment_id' => $installment->id,
                    'amount' => $amount,
                    'payment_date' => $installment->due_date->toDateString(),
                    'invoice_number' => null,
                    'payment_number' => 'PAY-' . now()->format('Y') . '-' . str_pad((string) $paymentSequence, 6, '0', STR_PAD_LEFT),
                    'transaction_number' => null,
                    'reference_number' => null,
                    'tracking_number' => null,
                    'status' => AcademicFeePayment::STATUS_UNPAID,
                    'created_by' => auth()->id(),
                ]);
            }

            return $this->repository->find($studentFee->id);
        });
    }

    public function addPayment(int $studentFeeId, array $data): AcademicFeePayment
    {
        return DB::transaction(function () use ($studentFeeId, $data) {
            $studentFee = $this->repository->find($studentFeeId);
            abort_unless($studentFee, 404, __('Student fee assignment not found.'));
            $amount = (float) $data['amount'];
            $paidStatuses = [AcademicFeePayment::STATUS_PAID];
            $paidTotal = $this->repository->paidAmount($studentFee, $paidStatuses);
            $remaining = round((float) $studentFee->total_amount - $paidTotal, 2);

            if ($amount > $remaining) {
                abort(422, __('Payment amount exceeds the remaining balance.'));
            }

            $nextPaymentId = $this->repository->nextPaymentId();

            $installment = null;
            if (! empty($data['academic_fee_installment_id'])) {
                $installment = $this->repository->findInstallment($studentFee, (int) $data['academic_fee_installment_id']);
                abort_unless($installment, 404, __('Installment not found.'));
            } else {
                $installment = $this->repository->nextUnpaidInstallment($studentFee);
            }

            $payment = $this->repository->createPayment($studentFee, [
                'academic_fee_installment_id' => $installment?->id,
                'amount' => $amount,
                'payment_date' => $data['payment_date'],
                'invoice_number' => $data['invoice_number'] ?? ('INV-' . now()->format('Y') . '-' . str_pad((string) $nextPaymentId, 6, '0', STR_PAD_LEFT)),
                'payment_number' => $data['payment_number'] ?? ('PAY-' . now()->format('Y') . '-' . str_pad((string) $nextPaymentId, 6, '0', STR_PAD_LEFT)),
                'transaction_number' => $data['transaction_number'] ?? null,
                'reference_number' => $data['reference_number'] ?? null,
                'tracking_number' => $data['tracking_number'] ?? null,
                'status' => $data['status'],
                'created_by' => auth()->id(),
            ]);

            if ($installment && $data['status'] === AcademicFeePayment::STATUS_PAID) {
                $installmentPaid = $this->repository->installmentPaidAmount($installment, AcademicFeePayment::STATUS_PAID);
                $this->repository->updateInstallment($installment, [
                    'paid_amount' => $installmentPaid,
                    'status' => $installmentPaid >= (float) $installment->amount
                        ? AcademicFeeInstallment::STATUS_PAID
                        : AcademicFeeInstallment::STATUS_PARTIAL,
                ]);
            }

            $freshStudentFee = $this->repository->find($studentFee->id);
            if ($freshStudentFee) {
                $this->refreshStudentFeeStatus($freshStudentFee);
            }

            return $payment->fresh(['installment', 'creator']);
        });
    }

    public function updatePayment(int $paymentId, array $data): AcademicFeePayment
    {
        return DB::transaction(function () use ($paymentId, $data) {
            $payment = $this->repository->findPayment($paymentId);
            abort_unless($payment, 404, __('Payment not found.'));
            abort_if($payment->status === AcademicFeePayment::STATUS_PAID, 422, __('A paid payment cannot be edited.'));

            $studentFee = $this->repository->find($payment->academic_student_fee_id);
            abort_unless($studentFee, 404, __('Student fee assignment not found.'));
            $newAmount = (float) $data['amount'];
            $paidTotal = $this->repository->paidAmount($studentFee, [AcademicFeePayment::STATUS_PAID]);
            $remainingAfterEditedPayment = round((float) $studentFee->total_amount - $paidTotal - $newAmount, 2);
            $otherUnpaidPayments = $studentFee->payments
                ->filter(fn (AcademicFeePayment $item) => $item->status !== AcademicFeePayment::STATUS_PAID && $item->id !== $payment->id)
                ->values();

            abort_if($remainingAfterEditedPayment < 0, 422, __('Payment amount exceeds the remaining balance.'));
            abort_if($otherUnpaidPayments->isEmpty() && abs($remainingAfterEditedPayment) > 0.01, 422, __('The final unpaid payment must equal the remaining balance.'));

            $updated = $this->repository->updatePayment($payment, [
                'amount' => (float) $data['amount'],
                'payment_date' => $data['payment_date'],
            ]);
            if ($payment->academic_fee_installment_id) {
                $installment = $this->repository->findInstallment($studentFee, $payment->academic_fee_installment_id);
                if ($installment) {
                    $this->repository->updateInstallment($installment, [
                        'amount' => $newAmount,
                        'due_date' => $data['payment_date'],
                    ]);
                }
            }

            $baseAmount = $otherUnpaidPayments->isNotEmpty()
                ? round($remainingAfterEditedPayment / $otherUnpaidPayments->count(), 2)
                : 0;
            foreach ($otherUnpaidPayments as $index => $otherPayment) {
                $amount = $index === $otherUnpaidPayments->count() - 1
                    ? round($remainingAfterEditedPayment - ($baseAmount * $index), 2)
                    : $baseAmount;
                $this->repository->updatePayment($otherPayment, ['amount' => $amount]);
                if ($otherPayment->academic_fee_installment_id) {
                    $installment = $this->repository->findInstallment($studentFee, $otherPayment->academic_fee_installment_id);
                    if ($installment) {
                        $this->repository->updateInstallment($installment, ['amount' => $amount]);
                    }
                }
            }

            $this->refreshStudentFeeStatus($this->repository->find($studentFee->id));

            return $updated;
        });
    }

    public function deletePayment(int $paymentId): bool
    {
        return DB::transaction(function () use ($paymentId) {
            $payment = $this->repository->findPayment($paymentId);
            abort_unless($payment, 404, __('Payment not found.'));
            $studentFee = $payment->studentFee;
            $installmentId = $payment->academic_fee_installment_id;
            $deleted = $this->repository->deletePayment($payment);
            if ($installmentId) {
                $this->refreshInstallment($studentFee, $installmentId);
            }
            $this->refreshStudentFeeStatus($this->repository->find($studentFee->id));
            return $deleted;
        });
    }

    public function togglePaymentStatus(int $paymentId): AcademicFeePayment
    {
        return DB::transaction(function () use ($paymentId) {
            $payment = $this->repository->findPayment($paymentId);
            abort_unless($payment, 404, __('Payment not found.'));
            $studentFee = $this->repository->find($payment->academic_student_fee_id);
            abort_unless($studentFee, 404, __('Student fee assignment not found.'));

            $updated = $this->repository->updatePayment($payment, [
                'status' => $payment->status === AcademicFeePayment::STATUS_PAID
                    ? AcademicFeePayment::STATUS_UNPAID
                    : AcademicFeePayment::STATUS_PAID,
            ]);

            if ($payment->academic_fee_installment_id) {
                $this->refreshInstallment($studentFee, $payment->academic_fee_installment_id);
            }
            $this->refreshStudentFeeStatus($this->repository->find($studentFee->id));

            return $updated;
        });
    }

    protected function refreshInstallment(AcademicStudentFee $studentFee, int $installmentId): void
    {
        $installment = $this->repository->findInstallment($studentFee, $installmentId);
        if (! $installment) {
            return;
        }
        $paid = $this->repository->installmentPaidAmount($installment, AcademicFeePayment::STATUS_PAID);
        $this->repository->updateInstallment($installment, [
            'paid_amount' => $paid,
            'status' => $paid >= (float) $installment->amount
                ? AcademicFeeInstallment::STATUS_PAID
                : ($paid > 0 ? AcademicFeeInstallment::STATUS_PARTIAL : AcademicFeeInstallment::STATUS_PENDING),
        ]);
    }

    protected function refreshStudentFeeStatus(?AcademicStudentFee $studentFee): void
    {
        if (! $studentFee) {
            return;
        }

        $isCompleted = $studentFee->installments->every(
            fn (AcademicFeeInstallment $installment) => $installment->status === AcademicFeeInstallment::STATUS_PAID
        );

        if ($isCompleted) {
            $this->repository->updateStatus($studentFee, AcademicStudentFee::STATUS_COMPLETED);
        } elseif ($studentFee->status === AcademicStudentFee::STATUS_COMPLETED) {
            $this->repository->updateStatus($studentFee, AcademicStudentFee::STATUS_ACTIVE);
        }
    }
}
