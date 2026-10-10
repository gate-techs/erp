<?php
namespace Modules\Academic\Repositories;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Academic\Models\AcademicStudent;
use Modules\Academic\Models\StudentWithdrawalRequest;
use Modules\Academic\Repositories\Contracts\StudentWithdrawalRequestRepositoryInterface;
use Modules\Nursery\Models\Nursery;
use Modules\Nursery\Models\NurseryBranch;
class StudentWithdrawalRequestRepository implements StudentWithdrawalRequestRepositoryInterface
{
    public function query(array $filters=[]):Builder { $q=StudentWithdrawalRequest::with(['institution.translations','branch.translations','student.translations','guardian.translations'])->latest('request_date'); foreach(['institution_id','branch_id','status'] as $field) if(!empty($filters[$field]))$q->where($field,$filters[$field]); return $q; }
    public function find(int $id):?StudentWithdrawalRequest{return StudentWithdrawalRequest::with(['institution.translations','branch.translations','student.translations','academicClass.translations','guardian.translations','reviewer'])->find($id);}
    public function create(array $data):StudentWithdrawalRequest{return StudentWithdrawalRequest::create($data);}
    public function update(int $id,array $data):?StudentWithdrawalRequest{$r=StudentWithdrawalRequest::find($id);if(!$r)return null;$r->update($data);return $this->find($id);}
    public function delete(int $id):bool{$r=StudentWithdrawalRequest::find($id);return $r?(bool)$r->delete():false;}
    public function institutions():Collection{return Nursery::with('translations')->orderBy('id')->get();}
    public function branches(?int $institutionId=null):Collection{return NurseryBranch::with('translations')->when($institutionId,fn($q)=>$q->where('nursery_id',$institutionId))->orderBy('id')->get();}
    public function students(?int $institutionId=null,?int $branchId=null):Collection{return AcademicStudent::with(['translations','class.translations','guardian.translations'])->where('status',AcademicStudent::STATUS_ACTIVE)->when($institutionId,fn($q)=>$q->where('institution_id',$institutionId))->when($branchId,fn($q)=>$q->where('branch_id',$branchId))->orderBy('id')->get();}
}
