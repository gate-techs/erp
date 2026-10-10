@php($editing = isset($report))
<form method="POST" action="{{ $editing ? route('academic.daily-reports.update', $report) : route('academic.daily-reports.store') }}">
@csrf @if($editing) @method('PUT') @endif

<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <div class="row g-3">
            <div class="col-md-4"><label class="form-label">{{ __('Nursery') }} *</label><select name="institution_id" id="institution_id" class="form-select select2" required><option value="">{{ __('Select Nursery') }}</option>@foreach($institutions as $item)<option value="{{ $item->id }}" @selected(old('institution_id',$report->institution_id??'')==$item->id)>{{ $item->name }}</option>@endforeach</select></div>
            <div class="col-md-4"><label class="form-label">{{ __('Branch') }} *</label><select name="branch_id" id="branch_id" class="form-select select2" required><option value="">{{ __('Select Branch') }}</option>@foreach($branches as $item)<option value="{{ $item->id }}" @selected(old('branch_id',$report->branch_id??'')==$item->id)>{{ $item->name }}</option>@endforeach</select></div>
            <div class="col-md-4"><label class="form-label">{{ __('Date') }} *</label><input type="date" name="report_date" class="form-control js-flatpickr-date flatpickr-input active" value="{{ old('report_date',isset($report)?$report->report_date->format('Y-m-d'):now()->format('Y-m-d')) }}" required></div>
        </div>

        <div class="border-top pt-4 mt-4"><h6 class="fw-bold mb-3">{{ __('Children Attendance') }}</h6><div class="row g-3">
            @foreach(['children_total'=>'Total Children in Branch','children_present'=>'Children Present Today','children_absent'=>'Children Absent Today','children_withdrawn'=>'Withdrawn Children'] as $field=>$label)<div class="col-md-3"><label class="form-label">{{ __($label) }}</label><input type="number" min="0" name="{{ $field }}" class="form-control" value="{{ old($field,$report->$field??0) }}" required></div>@endforeach
            <div class="col-12"><label class="form-label">{{ __('Withdrawal Reason') }}</label><textarea name="children_withdrawal_reason" rows="3" class="form-control">{{ old('children_withdrawal_reason',$report->children_withdrawal_reason??'') }}</textarea></div>
        </div></div>

        <div class="border-top pt-4 mt-4"><h6 class="fw-bold mb-3">{{ __('Team Attendance') }}</h6><div class="row g-3">
            @foreach(['team_total'=>'Total Team','team_present'=>'Team Present Today','team_absent'=>'Team Absent Today'] as $field=>$label)<div class="col-md-4"><label class="form-label">{{ __($label) }}</label><input type="number" min="0" name="{{ $field }}" class="form-control" value="{{ old($field,$report->$field??0) }}" required></div>@endforeach
            <div class="col-12"><label class="form-label">{{ __('Absence Reason') }}</label><textarea name="team_absence_reason" rows="3" class="form-control">{{ old('team_absence_reason',$report->team_absence_reason??'') }}</textarea></div>
        </div></div>

        @foreach(['administrative'=>['Administrative and Technical Challenges','Facts / Challenge'],'incident'=>['Daily Incidents and Injuries','Incident'],'recommendation'=>['Recommendations, Suggestions and Requirements','Recommendation'],'parents'=>['Parent Challenges','Facts / Challenge']] as $category=>$texts)
        <div class="border-top pt-4 mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3"><h6 class="fw-bold mb-0">{{ __($texts[0]) }}</h6><button type="button" class="btn btn-sm btn-label-primary add-entry" data-category="{{ $category }}" data-label="{{ __($texts[1]) }}"><i class="bx bx-plus me-1"></i>{{ __('Add More') }}</button></div>
            <div class="entry-container" data-category="{{ $category }}"></div>
        </div>
        @endforeach

        <div class="d-flex justify-content-end gap-2 border-top pt-4 mt-4"><a href="{{ route('academic.daily-reports.index') }}" class="btn btn-label-secondary">{{ __('Cancel') }}</a><button class="btn btn-primary"><i class="bx bx-save me-1"></i>{{ __('Save Daily Report') }}</button></div>
    </div>
</div>
</form>

<template id="entryTemplate"><div class="entry-row border rounded p-3 mb-3 position-relative"><button type="button" class="btn btn-sm btn-icon btn-label-danger position-absolute top-0 end-0 m-2 remove-entry"><i class="bx bx-trash"></i></button><div class="row g-3"><div class="col-md-6"><label class="form-label entry-incident-label"></label><textarea class="form-control entry-incident" rows="3"></textarea></div><div class="col-md-6"><label class="form-label">{{ __('Action Taken') }}</label><textarea class="form-control entry-action" rows="3"></textarea></div></div></div></template>
@push('scripts')
<script>
(() => { const categories=['administrative','incident','recommendation','parents']; let indexes={}; const existing=@json(old('entries',$editing?$report->entries->groupBy('category')->map->values():[]));
function setup(){ if(!window.jQuery||!window.jQuery.fn.select2){setTimeout(setup,50);return;} const $=window.jQuery; $('.select2').select2({theme:'bootstrap-5',width:'100%',dir:document.documentElement.dir==='rtl'?'rtl':'ltr'}); const dateInput=document.querySelector('.js-flatpickr-date');if(window.flatpickr&&dateInput&&!dateInput._flatpickr){flatpickr(dateInput,{dateFormat:'Y-m-d',allowInput:true,disableMobile:true,locale:document.documentElement.dir==='rtl'&&flatpickr.l10ns?.ar?flatpickr.l10ns.ar:'default'});} $('#institution_id').on('change',function(){const id=this.value;const branch=$('#branch_id');branch.empty().append(new Option(@json(__('Select Branch')),''));if(!id)return;$.get(@json(url('academic/daily-reports/branches'))+'/'+id,function(rows){rows.forEach(r=>branch.append(new Option(r.name,r.id)));branch.trigger('change');});});}
function add(category,label,row={}){const container=document.querySelector(`.entry-container[data-category="${category}"]`),fragment=document.getElementById('entryTemplate').content.cloneNode(true),index=indexes[category]||0; const incident=fragment.querySelector('.entry-incident'),action=fragment.querySelector('.entry-action');fragment.querySelector('.entry-incident-label').textContent=label;incident.name=`entries[${category}][${index}][incident]`;action.name=`entries[${category}][${index}][action_taken]`;incident.value=row.incident||'';action.value=row.action_taken||'';indexes[category]=index+1;container.appendChild(fragment);}
document.querySelectorAll('.add-entry').forEach(button=>button.addEventListener('click',()=>add(button.dataset.category,button.dataset.label)));document.addEventListener('click',e=>{const button=e.target.closest('.remove-entry');if(!button)return;const container=button.closest('.entry-container');if(container.querySelectorAll('.entry-row').length>1)button.closest('.entry-row').remove();});Object.entries(existing).forEach(([category,rows])=>{const button=document.querySelector(`.add-entry[data-category="${category}"]`);if(button)rows.forEach(row=>add(category,button.dataset.label,row));});categories.forEach(category=>{const container=document.querySelector(`.entry-container[data-category="${category}"]`);if(!container.querySelector('.entry-row')){const button=document.querySelector(`.add-entry[data-category="${category}"]`);add(category,button.dataset.label);}});setup();})();
</script>
@endpush
