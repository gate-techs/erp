@extends('layouts/contentNavbarLayout')

@section('title', __('Packages') . ' - ' . config('variables.templateName'))

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-1">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item text-muted">{{ __('Fees & Packages') }}</li>
            <li class="breadcrumb-item active text-primary">{{ __('Packages') }}</li>
        </ol></nav>
        <h4 class="mb-0 fw-bold text-heading"><i class="bx bx-package text-primary me-1"></i>{{ __('Packages') }}</h4>
    </div>
    @can('nursery.packages.create')
    <a href="{{ route('packages.create') }}" class="btn btn-primary btn-sm mt-3 mt-md-0"><i class="bx bx-plus me-1"></i>{{ __('Add Package') }}</a>
    @endcan
</div>

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
<div id="packageAlert"></div>

<div class="card shadow-sm border-0">
    <div class="card-body border-bottom py-3"><div class="row g-2">
        <div class="col-md-4"><select id="filter_nursery_id" class="form-select select2"><option value="">{{ __('All Nurseries') }}</option>@foreach($nurseries as $nursery)<option value="{{ $nursery->id }}">{{ $nursery->name }}</option>@endforeach</select></div>
        <div class="col-md-3"><select id="filter_status" class="form-select select2"><option value="">{{ __('All Statuses') }}</option><option value="active">{{ __('Active') }}</option><option value="inactive">{{ __('Inactive') }}</option></select></div>
    </div></div>
    <div class="card-datatable table-responsive"><table id="packagesTable" class="table table-hover border-top w-100 text-nowrap"><thead><tr>
        <th>#</th><th>{{ __('Image') }}</th><th>{{ __('Package Name') }}</th><th>{{ __('Nursery') }}</th><th>{{ __('Price') }}</th><th>{{ __('Discount') }}</th><th>{{ __('Order') }}</th><th>{{ __('Status') }}</th><th>{{ __('Actions') }}</th>
    </tr></thead><tbody></tbody></table></div>
</div>

<div class="modal fade" id="packageViewModal" tabindex="-1"><div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="viewPackageTitle">{{ __('Package Details') }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body" id="viewPackageBody"></div></div></div></div>
<div class="modal fade" id="packageDeleteModal" tabindex="-1"><div class="modal-dialog modal-sm modal-dialog-centered"><div class="modal-content"><div class="modal-body text-center p-4"><h5>{{ __('Delete Package') }}</h5><p>{{ __('Are you sure you want to delete') }} <strong id="deletePackageName"></strong>?</p><div class="d-flex justify-content-center gap-2"><button class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button><button class="btn btn-danger" id="confirmDelete">{{ __('Delete') }}</button></div></div></div></div></div>
@endsection

@section('page-script')
<script>
(() => {
    function startPackagesPage() {
        if (!window.jQuery || !window.jQuery.fn.DataTable) { setTimeout(startPackagesPage, 50); return; }
        const $ = window.jQuery;
        const baseUrl = @json(url('packages'));
        const csrf = document.querySelector('meta[name="csrf-token"]').content;
        let deleteId = null;
        const esc = value => $('<div>').text(value ?? '').html();
        const alertBox = (type, message) => $('#packageAlert').html(`<div class="alert alert-${type} alert-dismissible fade show">${esc(message)}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`);
        $('.select2').select2({
            theme: 'bootstrap-5',
            allowClear: true,
            width: '100%',
            dir: document.documentElement.dir === 'rtl' ? 'rtl' : 'ltr'
        });
        const table = $('#packagesTable').DataTable({
            processing: true, serverSide: true,
            ajax: { url: @json(route('packages.data')), data: d => { d.nursery_id = $('#filter_nursery_id').val(); d.status = $('#filter_status').val(); } },
            columns: [
                {data:'DT_RowIndex',orderable:false,searchable:false},
                {data:'image_url',orderable:false,searchable:false,render:d => d ? `<img src="${d}" class="rounded border" width="42" height="42" style="object-fit:cover">` : '-'},
                {data:'name_ar',name:'translations.name'}, {data:'nursery_name',name:'nursery_id'}, {data:'price'}, {data:'discount_display',name:'discount_value'}, {data:'sort_order'}, {data:'status_badge',name:'status',orderable:false}, {data:'actions',orderable:false,searchable:false}
            ], order:[[6,'asc']]
        });
        $('#filter_nursery_id,#filter_status').on('change', () => table.ajax.reload());
        $(document).on('click','.btn-toggle-package-status',function(){ $.ajax({url:`${baseUrl}/${$(this).data('id')}/toggle-status`,type:'PATCH',headers:{'X-CSRF-TOKEN':csrf,Accept:'application/json'},success:r=>{table.ajax.reload(null,false);alertBox('success',r.message);},error:()=>alertBox('danger',@json(__('Unable to update status.')))}); });
        $(document).on('click','.btn-delete-package',function(){ deleteId=$(this).data('id'); $('#deletePackageName').text($(this).data('name')); bootstrap.Modal.getOrCreateInstance(document.getElementById('packageDeleteModal')).show(); });
        $('#confirmDelete').on('click',function(){ if(!deleteId) return; $.ajax({url:`${baseUrl}/${deleteId}`,type:'DELETE',headers:{'X-CSRF-TOKEN':csrf,Accept:'application/json'},success:r=>{bootstrap.Modal.getInstance(document.getElementById('packageDeleteModal')).hide();table.ajax.reload(null,false);alertBox('success',r.message);},error:x=>alertBox('danger',x.responseJSON?.message || @json(__('Delete failed.')))}) });
        $(document).on('click','.btn-view-package',function(){ $.get(`${baseUrl}/${$(this).data('id')}`,r=>{const d=r.data; $('#viewPackageTitle').text(d.name_ar || d.name_en); $('#viewPackageBody').html(`<div class="row g-3"><div class="col-md-6"><b>{{ __('Arabic Name') }}:</b> ${esc(d.name_ar)}</div><div class="col-md-6"><b>{{ __('English Name') }}:</b> ${esc(d.name_en)}</div><div class="col-md-6"><b>{{ __('Price') }}:</b> ${esc(d.price || '-')}</div><div class="col-md-6"><b>{{ __('Discount') }}:</b> ${esc(d.discount_value)} ${d.discount_type === 'percentage' ? '%' : ''}</div><div class="col-12"><b>{{ __('Features') }}:</b><ul>${(d.features||[]).map(f=>`<li>${esc(f.name_ar)} / ${esc(f.name_en)}</li>`).join('')}</ul></div></div>`); bootstrap.Modal.getOrCreateInstance(document.getElementById('packageViewModal')).show();}); });
    }
    startPackagesPage();
})();
</script>
@endsection
