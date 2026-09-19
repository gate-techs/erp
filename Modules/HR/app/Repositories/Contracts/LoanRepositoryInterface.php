<?php

namespace Modules\HR\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Modules\HR\Models\Loan;

interface LoanRepositoryInterface
{
    public function getDataTableQuery(array $filters = []): Builder;

    public function findById(int $id, array $with = []): ?Loan;

    public function create(array $data): Loan;

    public function update(Loan $loan, array $data): bool;

    public function delete(Loan $loan): bool;

    public function getEmployeeActiveLoansCount(int $employeeId): int;

    public function getEmployeeActiveLoansTotalRemaining(int $employeeId): float;

    public function getKPIs(): array;
}
