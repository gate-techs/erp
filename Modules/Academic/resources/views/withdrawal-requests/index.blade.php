@extends('layouts/contentNavbarLayout')
@section('title', __('Student Withdrawal Requests'))
@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
    <div class="d-flex flex-column justify-content-center">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted fw-medium small">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item text-muted fw-medium small">{{ __('Student Affairs') }}</li>
                <li class="breadcrumb-item active text-primary fw-medium small" aria-current="page">{{ __('Student Withdrawal Requests') }}</li>
            </ol>
        </nav>
        <!-- Title -->
        <div class="d-flex align-items-center gap-2 mt-1">
            <i class="icon-base bx bx-log-out-circle text-primary font-24"></i>
            <h4 class="mb-0 fw-bold text-heading">{{ __('Student Withdrawal Requests') }}</h4>
        </div>
    </div>

    <div class="mt-3 mt-md-0 d-flex align-items-center gap-2">
        <a href="{{ route('academic.withdrawal-requests.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2 shadow-sm">
            <i class="icon-base bx bx-plus font-18"></i>
            <span>{{ __('Add Withdrawal Request') }}</span>
        </a>
    </div>
</div>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
<div class="card shadow-sm border-0"><div class="card-body border-bottom"><div class="row g-2"><div class="col-md-4"><select id="filter_institution" class="form-select select2"><option value="">{{ __('All Nurseries') }}</option>@foreach($institutions as $item)<option value="{{ $item->id }}">{{ $item->name }}</option>@endforeach</select></div><div class="col-md-4"><select id="filter_branch" class="form-select select2"><option value="">{{ __('All Branches') }}</option>@foreach($branches as $item)<option value="{{ $item->id }}">{{ $item->name }}</option>@endforeach</select></div><div class="col-md-4"><select id="filter_status" class="form-select select2"><option value="">{{ __('All Statuses') }}</option>@foreach(['pending','approved','rejected','cancelled'] as $status)<option value="{{ $status }}">{{ __($status) }}</option>@endforeach</select></div></div></div><div class="card-datatable table-responsive"><table id="withdrawalTable" class="table table-hover border-top w-100 text-nowrap"><thead><tr><th>#</th><th>{{ __('Request Date') }}</th><th>{{ __('Student Name') }}</th><th>{{ __('Nursery') }}</th><th>{{ __('Branch') }}</th><th>{{ __('Status') }}</th><th>{{ __('Actions') }}</th></tr></thead></table></div></div>
@endsection
@section('page-script')<script>(()=>{function init(){if(!window.jQuery||!window.jQuery.fn.DataTable){setTimeout(init,50);return;}const $=window.jQuery,csrf=document.querySelector('meta[name="csrf-token"]').content;$('.select2').select2({theme:'bootstrap-5',width:'100%',dir:document.documentElement.dir==='rtl'?'rtl':'ltr'});const table=$('#withdrawalTable').DataTable({processing:true,serverSide:true,ajax:{url:@json(route('academic.withdrawal-requests.data')),data:d=>{d.institution_id=$('#filter_institution').val();d.branch_id=$('#filter_branch').val();d.status=$('#filter_status').val();}},columns:[{data:'DT_RowIndex',orderable:false,searchable:false},{data:'request_date'},{data:'student_name'},{data:'institution_name'},{data:'branch_name'},{data:'status_badge',orderable:false},{data:'actions',orderable:false,searchable:false}]});$('#filter_institution,#filter_branch,#filter_status').on('change',()=>table.ajax.reload());$(document).on('click','.delete-withdrawal',function(){if(!confirm(@json(__('Delete this withdrawal request?'))))return;$.ajax({url:@json(url('academic/withdrawal-requests'))+'/'+$(this).data('id'),type:'DELETE',headers:{'X-CSRF-TOKEN':csrf},success:()=>table.ajax.reload()});});}init();})();</script>@endsection
