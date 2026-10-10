<?php
namespace Modules\Academic\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class StoreStudentWithdrawalRequest extends FormRequest { public function authorize():bool{return true;} public function rules():array{return ['institution_id'=>['required','exists:nurseries,id'],'branch_id'=>['required','exists:nursery_branches,id'],'academic_student_id'=>['required','exists:academic_students,id'],'request_date'=>['required','date'],'effective_withdrawal_date'=>['nullable','date'],'reason'=>['required','string'],'parent_signature'=>['nullable','string'],'branch_manager_signature'=>['nullable','string'],'parent_financial_obligations'=>['nullable','string'],'management_report'=>['nullable','string'],'status'=>['required',Rule::in(['pending','approved','rejected','cancelled'])],'rejection_reason'=>['nullable','string']];} }
