<?php
namespace Modules\Academic\Services;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;
use Modules\Academic\Models\AcademicStudent;
use Modules\Academic\Models\StudentWithdrawalRequest;
use Modules\Academic\Repositories\Contracts\StudentWithdrawalRequestRepositoryInterface;
class StudentWithdrawalRequestService
{
    public function __construct(protected StudentWithdrawalRequestRepositoryInterface $repository){}
    public function query(array $filters=[]):Builder{return $this->repository->query($filters);}
    public function find(int $id):?StudentWithdrawalRequest{return $this->repository->find($id);}
    public function formData():array{return ['institutions'=>$this->repository->institutions(),'branches'=>$this->repository->branches(),'students'=>$this->repository->students()];}
    public function branches(?int $institutionId):Collection{return $this->repository->branches($institutionId);}
    public function students(?int $institutionId,?int $branchId):Collection{return $this->repository->students($institutionId,$branchId);}
    public function create(array $data,?int $userId):StudentWithdrawalRequest { $this->ensureNoPendingRequest($data['academic_student_id']); return $this->repository->create(array_merge($this->snapshotStudent($data),['created_by'=>$userId])); }
    public function update(int $id,array $data):?StudentWithdrawalRequest{return $this->repository->update($id,$this->snapshotStudent($data));}
    public function delete(int $id):bool{return $this->repository->delete($id);}
    private function ensureNoPendingRequest(int $studentId):void { if(StudentWithdrawalRequest::where('academic_student_id',$studentId)->where('status',StudentWithdrawalRequest::STATUS_PENDING)->exists())throw ValidationException::withMessages(['academic_student_id'=>__('This student already has a pending withdrawal request.')]); }
    private function snapshotStudent(array $data):array { $student=AcademicStudent::with('guardian')->findOrFail($data['academic_student_id']); $data['institution_id']=$student->institution_id;$data['branch_id']=$student->branch_id;$data['academic_class_id']=$student->academic_class_id;$data['academic_guardian_id']=$student->academic_guardian_id;$data['civil_id_snapshot']=$student->civil_id;return $data; }
}
