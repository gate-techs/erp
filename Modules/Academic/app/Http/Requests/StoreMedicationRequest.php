<?php
namespace Modules\Academic\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class StoreMedicationRequest extends FormRequest { public function authorize():bool{return true;} public function rules():array{return ['parent_id'=>['required','exists:users,id'],'student_id'=>['required','exists:academic_students,id'],'medicine_name'=>['required','string','max:255'],'dosage_amount'=>['required','string','max:255'],'dosage_time'=>['required','string','max:50'],'date'=>['required','date'],'nurse_id'=>['nullable','exists:users,id'],'nurse_note'=>['nullable','string'],'action_time'=>['nullable','date'],'done'=>['nullable','boolean']];} }
