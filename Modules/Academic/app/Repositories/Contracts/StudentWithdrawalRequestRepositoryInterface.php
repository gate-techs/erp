<?php
namespace Modules\Academic\Repositories\Contracts;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Academic\Models\StudentWithdrawalRequest;
interface StudentWithdrawalRequestRepositoryInterface { public function query(array $filters=[]):Builder; public function find(int $id):?StudentWithdrawalRequest; public function create(array $data):StudentWithdrawalRequest; public function update(int $id,array $data):?StudentWithdrawalRequest; public function delete(int $id):bool; public function institutions():Collection; public function branches(?int $institutionId=null):Collection; public function students(?int $institutionId=null,?int $branchId=null):Collection; }
