<?php

namespace Modules\Academic\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Academic\Models\AcademicFee;
use Modules\Academic\Models\AcademicFeeInstallment;
use Modules\Academic\Models\AcademicFeePayment;
use Modules\Academic\Models\AcademicStudentFee;
use Modules\Academic\Repositories\Contracts\AcademicStudentFeeRepositoryInterface;

class AcademicStudentFeeRepository implements AcademicStudentFeeRepositoryInterface
{
    public function getFilteredQuery(array $filters = []): Builder
    {
        $query = AcademicStudentFee::query()->with([
            'student.translations',
            'fee.translations',
            'installments',
            'payments',
        ]);

        foreach (['academic_student_id', 'academic_fee_id', 'status'] as $field) {
            if (! empty($filters[$field])) {
                $query->where($field, $filters[$field]);
            }
        }

        return $query->latest('id');
    }

    public function all(array $filters = []): Collection
    {
        return $this->getFilteredQuery($filters)->get();
    }

    public function find(int $id): ?AcademicStudentFee
    {
        return AcademicStudentFee::with([
            'student.translations',
            'fee.translations',
            'installments' => fn ($query) => $query->orderBy('installment_number'),
            'payments' => fn ($query) => $query->with('installment')->latest('payment_date'),
            'payments.creator',
        ])->find($id);
    }

    public function findFee(int $id): ?AcademicFee
    {
        return AcademicFee::find($id);
    }

    public function create(array $data): AcademicStudentFee
    {
        return AcademicStudentFee::create($data);
    }

    public function update(AcademicStudentFee $studentFee, array $data): AcademicStudentFee
    {
        $studentFee->update($data);
        return $studentFee->fresh(['student.translations', 'fee.translations', 'installments', 'payments']);
    }

    public function delete(AcademicStudentFee $studentFee): bool
    {
        return (bool) $studentFee->delete();
    }

    public function syncInstallments(AcademicStudentFee $studentFee, float $total, int $count, string $startDate): void
    {
        $studentFee->installments()->delete();

        $baseAmount = round($total / $count, 2);
        $date = \Carbon\Carbon::parse($startDate);
        for ($number = 1; $number <= $count; $number++) {
            $amount = $number === $count
                ? round($total - ($baseAmount * ($count - 1)), 2)
                : $baseAmount;

            $studentFee->installments()->create([
                'installment_number' => $number,
                'due_date' => $date->copy()->addMonths($number - 1)->toDateString(),
                'amount' => $amount,
                'paid_amount' => 0,
                'status' => AcademicFeeInstallment::STATUS_PENDING,
            ]);
        }
    }

    public function createInstallment(AcademicStudentFee $studentFee, array $data): AcademicFeeInstallment
    {
        return $studentFee->installments()->create($data);
    }

    public function nextUnpaidInstallment(AcademicStudentFee $studentFee): ?AcademicFeeInstallment
    {
        return $studentFee->installments()
            ->whereColumn('paid_amount', '<', 'amount')
            ->orderBy('installment_number')
            ->first();
    }

    public function findInstallment(AcademicStudentFee $studentFee, int $id): ?AcademicFeeInstallment
    {
        return $studentFee->installments()->find($id);
    }

    public function paidAmount(AcademicStudentFee $studentFee, array $statuses): float
    {
        return (float) $studentFee->payments()->whereIn('status', $statuses)->sum('amount');
    }

    public function installmentPaidAmount(AcademicFeeInstallment $installment, string $status): float
    {
        return (float) $installment->payments()->where('status', $status)->sum('amount');
    }

    public function nextPaymentId(): int
    {
        return (int) AcademicFeePayment::withTrashed()->max('id') + 1;
    }

    public function createPayment(AcademicStudentFee $studentFee, array $data): AcademicFeePayment
    {
        return $studentFee->payments()->create($data);
    }

    public function findPayment(int $id): ?AcademicFeePayment
    {
        return AcademicFeePayment::with(['studentFee', 'installment', 'creator'])->find($id);
    }

    public function updatePayment(AcademicFeePayment $payment, array $data): AcademicFeePayment
    {
        $payment->update($data);
        return $payment->fresh(['installment', 'creator']);
    }

    public function deletePayment(AcademicFeePayment $payment): bool
    {
        return (bool) $payment->delete();
    }

    public function getPaymentsQuery(array $filters = []): Builder
    {
        $query = AcademicFeePayment::query()->with([
            'studentFee.student.translations',
            'studentFee.fee.translations',
            'installment',
            'creator',
        ]);

        if (! empty($filters['academic_student_fee_id'])) {
            $query->where('academic_student_fee_id', $filters['academic_student_fee_id']);
        }
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->latest('payment_date')->latest('id');
    }

    public function updateInstallment(AcademicFeeInstallment $installment, array $data): AcademicFeeInstallment
    {
        $installment->update($data);
        return $installment->fresh();
    }

    public function updateStatus(AcademicStudentFee $studentFee, string $status): AcademicStudentFee
    {
        $studentFee->update(['status' => $status]);
        return $studentFee->fresh();
    }
}
