<?php
namespace Modules\Academic\Repositories;
use App\Enums\UserType;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Academic\Models\AcademicStudent;
use Modules\Academic\Models\MedicationRequest;
use Modules\Academic\Repositories\Contracts\MedicationRequestRepositoryInterface;
class MedicationRequestRepository implements MedicationRequestRepositoryInterface
{
    public function query(array $filters=[]):Builder{$q=MedicationRequest::with(['parent','student.translations','nurse'])->latest('date')->latest('id');foreach(['parent_id','student_id','nurse_id','done','date'] as $f)if(isset($filters[$f])&&$filters[$f]!=='')$q->where($f,$filters[$f]);return $q;}
    public function find(int $id):?MedicationRequest{return MedicationRequest::with(['parent','student.translations','nurse'])->find($id);}
    public function create(array $data):MedicationRequest{return MedicationRequest::create($data);}
    public function update(int $id,array $data):?MedicationRequest{$r=MedicationRequest::find($id);if(!$r)return null;$r->update($data);return $this->find($id);}
    public function delete(int $id,int $userId):bool{$r=MedicationRequest::find($id);if(!$r)return false;$r->deleted_by=$userId;$r->save();return (bool)$r->delete();}
    public function parents():Collection{return User::where('user_type',UserType::GUARDIAN->value)->where('is_active',true)->orderBy('name')->get();}
    public function nurses():Collection{return User::where('user_type',UserType::NURSE->value)->where('is_active',true)->orderBy('name')->get();}
    public function students():Collection{return AcademicStudent::with('translations')->where('status',AcademicStudent::STATUS_ACTIVE)->orderBy('id')->get();}
}
