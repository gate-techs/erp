<?php
namespace Modules\Academic\Services;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Academic\Models\MedicationRequest;
use Modules\Academic\Repositories\Contracts\MedicationRequestRepositoryInterface;
class MedicationRequestService
{
    public function __construct(protected MedicationRequestRepositoryInterface $repository){}
    public function query(array $filters=[]):Builder{return $this->repository->query($filters);}
    public function find(int $id):?MedicationRequest{return $this->repository->find($id);}
    public function formData():array{return ['parents'=>$this->repository->parents(),'nurses'=>$this->repository->nurses(),'students'=>$this->repository->students()];}
    public function create(array $data,int $userId):MedicationRequest{$data['created_by']=$userId;return $this->repository->create($data);}
    public function update(int $id,array $data,int $userId):?MedicationRequest{$data['updated_by']=$userId;return $this->repository->update($id,$data);}
    public function delete(int $id,int $userId):bool{return $this->repository->delete($id,$userId);}
    public function markDone(int $id,array $data,int $userId):?MedicationRequest{$data['done']=true;$data['nurse_id']=$data['nurse_id']??$userId;$data['action_time']=now();$data['updated_by']=$userId;return $this->repository->update($id,$data);}
}
