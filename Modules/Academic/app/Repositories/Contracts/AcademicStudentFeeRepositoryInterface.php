<?php

namespace Modules\Academic\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Academic\Models\AcademicFee;
use Modules\Academic\Models\AcademicFeeInstallment;
use Modules\Academic\Models\AcademicFeePayment;
use Modules\Academic\Models\AcademicStudentFee;

interface AcademicStudentFeeRepositoryInterface
{
    public function getFilteredQuery(array $filters = []): Builder;

    public function all(array $filters = []): Collection;

    public function find(int $id): ?AcademicStudentFee;

    public function findFee(int $id): ?AcademicFee;

    public function create(array $data): AcademicStudentFee;

    public function update(AcademicStudentFee $studentFee, array $data): AcademicStudentFee;

    public function delete(AcademicStudentFee $studentFee): bool;

    public function syncInstallments(AcademicStudentFee $studentFee, float $total, int $count, string $startDate): void;

    public function createInstallment(AcademicStudentFee $studentFee, array $data): AcademicFeeInstallment;

    public function nextUnpaidInstallment(AcademicStudentFee $studentFee): ?AcademicFeeInstallment;

    public function findInstallment(AcademicStudentFee $studentFee, int $id): ?AcademicFeeInstallment;

    public function paidAmount(AcademicStudentFee $studentFee, array $statuses): float;

    public function installmentPaidAmount(AcademicFeeInstallment $installment, string $status): float;

    public function nextPaymentId(): int;

    public function createPayment(AcademicStudentFee $studentFee, array $data): AcademicFeePayment;

    public function findPayment(int $id): ?AcademicFeePayment;

    public function updatePayment(AcademicFeePayment $payment, array $data): AcademicFeePayment;

    public function deletePayment(AcademicFeePayment $payment): bool;

    public function getPaymentsQuery(array $filters = []): Builder;

    public function updateInstallment(AcademicFeeInstallment $installment, array $data): AcademicFeeInstallment;

    public function updateStatus(AcademicStudentFee $studentFee, string $status): AcademicStudentFee;
}
