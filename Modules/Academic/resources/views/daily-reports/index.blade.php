@extends('layouts/contentNavbarLayout')
@section('title', __('Daily Reports'))
@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
    <div class="d-flex flex-column justify-content-center">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted fw-medium small">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item text-muted fw-medium small">{{ __('Student Affairs') }}</li>
                <li class="breadcrumb-item active text-primary fw-medium small" aria-current="page">{{ __('Daily Reports') }}</li>
            </ol>
        </nav>
        <!-- Title -->
        <div class="d-flex align-items-center gap-2 mt-1">
            <i class="icon-base bx bx-clipboard text-primary font-24"></i>
            <h4 class="mb-0 fw-bold text-heading">{{ __('Daily Reports') }}</h4>
        </div>
    </div>

    @can('academic.daily-reports.create')
    <div class="mt-3 mt-md-0 d-flex align-items-center gap-2">
        <a class="btn btn-primary btn-sm d-flex align-items-center gap-2 shadow-sm" href="{{ route('academic.daily-reports.create') }}">
            <i class="icon-base bx bx-plus font-18"></i>
            <span>{{ __('Add Daily Report') }}</span>
        </a>
    </div>
    @endcan
</div>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
<div class="card border-0 shadow-sm"><div class="card-body border-bottom"><div class="row g-2"><div class="col-md-4"><select id="filter_institution" class="form-select select2"><option value="">{{ __('All Nurseries') }}</option>@foreach($institutions as $n)<option value="{{ $n->id }}">{{ $n->name }}</option>@endforeach</select></div><div class="col-md-4"><select id="filter_branch" class="form-select select2"><option value="">{{ __('All Branches') }}</option>@foreach($branches as $b)<option value="{{ $b->id }}">{{ $b->name }}</option>@endforeach</select></div><div class="col-md-4"><input id="filter_date" type="date" class="form-control js-flatpickr-date flatpickr-input active"></div></div></div><div class="card-datatable table-responsive"><table id="dailyReportsTable" class="table table-hover border-top w-100 text-nowrap"><thead><tr><th>#</th><th>{{ __('Date') }}</th><th>{{ __('Nursery') }}</th><th>{{ __('Branch') }}</th><th>{{ __('Children Present Today') }}</th><th>{{ __('Team Present Today') }}</th><th>{{ __('Actions') }}</th></tr></thead></table></div></div>
@endsection
@section('page-script')<script>(()=>{function init(){if(!window.jQuery||!window.jQuery.fn.DataTable){setTimeout(init,50);return;}const $=window.jQuery,csrf=document.querySelector('meta[name="csrf-token"]').content;$('.select2').select2({theme:'bootstrap-5',width:'100%',dir:document.documentElement.dir==='rtl'?'rtl':'ltr'});const dateInput=document.getElementById('filter_date');if(window.flatpickr&&dateInput&&!dateInput._flatpickr){flatpickr(dateInput,{dateFormat:'Y-m-d',allowInput:true,disableMobile:true,locale:document.documentElement.dir==='rtl'&&flatpickr.l10ns?.ar?flatpickr.l10ns.ar:'default'});}const table=$('#dailyReportsTable').DataTable({processing:true,serverSide:true,ajax:{url:@json(route('academic.daily-reports.data')),data:d=>{d.institution_id=$('#filter_institution').val();d.branch_id=$('#filter_branch').val();d.report_date=$('#filter_date').val();}},columns:[{data:'DT_RowIndex',orderable:false,searchable:false},{data:'report_date'},{data:'institution_name'},{data:'branch_name'},{data:'children_present'},{data:'team_present'},{data:'actions',orderable:false,searchable:false}]});$('#filter_institution,#filter_branch,#filter_date').on('change',()=>table.ajax.reload());$(document).on('click','.delete-report',function(){if(!confirm(@json(__('Delete this daily report?'))))return;$.ajax({url:@json(url('academic/daily-reports'))+'/'+$(this).data('id'),type:'DELETE',headers:{'X-CSRF-TOKEN':csrf},success:()=>table.ajax.reload()});});}init();})();</script>@endsection
