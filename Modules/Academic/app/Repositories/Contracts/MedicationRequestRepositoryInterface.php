<?php
namespace Modules\Academic\Repositories\Contracts;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Academic\Models\MedicationRequest;
interface MedicationRequestRepositoryInterface { public function query(array $filters=[]):Builder; public function find(int $id):?MedicationRequest; public function create(array $data):MedicationRequest; public function update(int $id,array $data):?MedicationRequest; public function delete(int $id,int $userId):bool; public function parents():Collection; public function nurses():Collection; public function students():Collection; }
