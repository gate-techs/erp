<?php
namespace Modules\Academic\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class StoreDailyReportRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array { return [
        'institution_id'=>['required','exists:nurseries,id'], 'branch_id'=>['required','exists:nursery_branches,id'], 'report_date'=>['required','date'],
        'children_total'=>['required','integer','min:0'], 'children_present'=>['required','integer','min:0'], 'children_absent'=>['required','integer','min:0'], 'children_withdrawn'=>['required','integer','min:0'], 'children_withdrawal_reason'=>['nullable','string'],
        'team_total'=>['required','integer','min:0'], 'team_present'=>['required','integer','min:0'], 'team_absent'=>['required','integer','min:0'], 'team_absence_reason'=>['nullable','string'],
        'entries'=>['nullable','array'], 'entries.*'=>['array'], 'entries.*.*.incident'=>['nullable','string'], 'entries.*.*.action_taken'=>['nullable','string'],
    ]; }
}
