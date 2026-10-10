<?php
namespace Modules\Academic\Services;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Academic\Models\DailyReport;
use Modules\Academic\Repositories\Contracts\DailyReportRepositoryInterface;
class DailyReportService { public function __construct(protected DailyReportRepositoryInterface $repository) {} public function query(array $filters=[]):Builder{return $this->repository->query($filters);} public function find(int $id):?DailyReport{return $this->repository->find($id);} public function formData():array{return ['institutions'=>$this->repository->institutions(),'branches'=>$this->repository->branches()];} public function branches(?int $institutionId):Collection{return $this->repository->branches($institutionId);} public function create(array $data,?int $userId):DailyReport{$data['created_by']=$userId;return $this->repository->create($data);} public function update(int $id,array $data):?DailyReport{return $this->repository->update($id,$data);} public function delete(int $id):bool{return $this->repository->delete($id);} }
