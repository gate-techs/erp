@extends('layouts/contentNavbarLayout')

@section('title', __('Units of Measure') . ' - ' . config('variables.templateName'))

@section('content')
<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2"><i class="icon-base bx bx-ruler text-primary font-24"></i><span>{{ __('Units of Measure') }}</span></h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Dashboard') }}</a></li><li class="breadcrumb-item"><a href="{{ route('inventory.index') }}">{{ __('Inventory') }}</a></li><li class="breadcrumb-item active">{{ __('Units of Measure') }}</li></ol></nav>
    </div>
    @can('inventory.units.create')
    <button type="button" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm" id="btnAddUnit"><i class="icon-base bx bx-plus font-18"></i>{{ __('Add New Unit') }}</button>
    @endcan
</div>

<div id="alertContainer">@if(session('success'))<div class="alert alert-success alert-dismissible d-flex align-items-center mb-4" role="alert"><i class="icon-base bx bx-check-circle me-2 font-20"></i><div>{{ session('success') }}</div><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif</div>

<div class="card shadow-sm border-0"><div class="card-datatable table-responsive"><table id="unitsTable" class="table table-hover border-top w-100 text-nowrap">
    <thead><tr>
        <th class="text-center" style="width:50px">#</th>
        <th>{{ __('Unit Name (AR)') }}</th>
        <th>{{ __('Unit Name (EN)') }}</th>
        <th class="text-center">{{ __('Symbol') }}</th>
        <th class="text-center">{{ __('Status') }}</th>
        <th class="text-center" style="width:120px">{{ __('Actions') }}</th>
    </tr></thead>
    <tbody></tbody>
</table></div></div>

{{-- Offcanvas Form --}}
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="unitOffcanvas" style="width:460px;max-width:95vw">
    <div class="offcanvas-header border-bottom py-3 px-4"><h5 class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0"><i class="icon-base bx bx-ruler text-primary"></i><span id="unitFormTitle">{{ __('Add New Unit') }}</span></h5><button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button></div>
    <div class="offcanvas-body p-4"><form id="unitForm" method="POST" action="{{ route('inventory.units.store') }}">
        @csrf
        <input type="hidden" name="_method" id="unitFormMethod" value="POST">
        <div class="mb-3"><label class="form-label fw-semibold">{{ __('Symbol') }} <span class="text-danger">*</span></label><input type="text" name="symbol" id="unit_symbol" class="form-control" maxlength="20" required dir="ltr" placeholder="kg"></div>
        <div class="mb-3"><label class="form-label fw-semibold">{{ __('Unit Name [Arabic]') }} <span class="text-danger">*</span></label><input type="text" name="name_ar" id="unit_name_ar" class="form-control" maxlength="100" required dir="rtl" placeholder="كيلوجرام"></div>
        <div class="mb-3"><label class="form-label fw-semibold">{{ __('Unit Name [English]') }} <span class="text-danger">*</span></label><input type="text" name="name_en" id="unit_name_en" class="form-control" maxlength="100" required dir="ltr" placeholder="Kilogram"></div>
        <div class="mb-4"><label class="form-label fw-semibold d-block">{{ __('Status') }}</label><div class="d-flex gap-3"><label class="form-check text-success fw-semibold"><input class="form-check-input" type="radio" name="status" value="active" checked>{{ __('Active') }}</label><label class="form-check text-danger fw-semibold"><input class="form-check-input" type="radio" name="status" value="inactive">{{ __('Inactive') }}</label></div></div>
        <div class="d-flex gap-2 justify-content-end"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button><button type="submit" class="btn btn-primary"><i class="icon-base bx bx-save me-1"></i>{{ __('Save') }}</button></div>
    </form></div>
</div>

{{-- Delete Modal --}}
<div class="modal fade" id="deleteUnitModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered modal-sm"><form id="deleteUnitForm" class="modal-content">@csrf @method('DELETE')<div class="modal-body text-center py-4"><div class="avatar avatar-xl bg-label-danger mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center"><i class="icon-base bx bx-trash font-32 text-danger"></i></div><h5 class="fw-bold mb-2">{{ __('Confirm Delete') }}</h5><p class="text-muted mb-0">{{ __('Are you sure you want to delete:') }}<br><strong id="deleteUnitName" class="text-danger"></strong></p></div><div class="modal-footer justify-content-center border-0 pt-0 pb-4"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button><button type="submit" class="btn btn-danger">{{ __('Yes, Delete') }}</button></div></form></div></div>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const $ = window.jQuery || window.$;
    const dataUrl  = @json(route('inventory.units.data'));
    const baseUrl  = @json(route('inventory.units.index'));
    const csrfToken = @json(csrf_token());
    const offcanvas   = bootstrap.Offcanvas.getOrCreateInstance(document.getElementById('unitOffcanvas'));
    const deleteModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('deleteUnitModal'));
    const form = $('#unitForm');

    function alertBox(type, message) { $('#alertContainer').html(`<div class="alert alert-${type} alert-dismissible d-flex align-items-center mb-4" role="alert"><i class="icon-base bx ${type === 'success' ? 'bx-check-circle' : 'bx-error-circle'} me-2 font-20"></i><div>${message}</div><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`); }
    function resetForm() { form[0].reset(); form.attr('action', @json(route('inventory.units.store'))); $('#unitFormMethod').val('POST'); $('#unitFormTitle').text(@json(__('Add New Unit'))); $('input[name="status"][value="active"]').prop('checked', true); }

    const table = $('#unitsTable').DataTable({
        processing: true, serverSide: true, pageLength: 15,
        ajax: { url: dataUrl },
        columns: [
            { data: 'DT_RowIndex', name: 'id', orderable: false, searchable: false, className: 'text-center align-middle' },
            { data: 'name_ar', name: 'name_ar', className: 'align-middle fw-semibold' },
            { data: 'name_en', name: 'name_en', className: 'align-middle text-muted' },
            { data: 'symbol', name: 'symbol', className: 'text-center align-middle', render: d => `<span class="badge bg-label-primary" dir="ltr">${d}</span>` },
            { data: 'status_badge', name: 'status', className: 'text-center align-middle' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center align-middle' }
        ],
        order: [[1, 'asc']],
        language: { search: '', searchPlaceholder: @json(__('Search...')), paginate: { previous: '<i class="bx bx-chevron-left"></i>', next: '<i class="bx bx-chevron-right"></i>' } }
    });

    $('#btnAddUnit').on('click', function () { resetForm(); offcanvas.show(); });

    $(document).on('click', '.btn-edit-unit', function () {
        $.ajax({ url: baseUrl + '/' + $(this).data('id'), headers: { Accept: 'application/json' }, success: function (response) {
            const unit = response.data;
            const ar = unit.translations.find(t => t.locale === 'ar') || {};
            const en = unit.translations.find(t => t.locale === 'en') || {};
            form.attr('action', baseUrl + '/' + unit.id); $('#unitFormMethod').val('PUT'); $('#unitFormTitle').text(@json(__('Edit Unit')));
            $('#unit_symbol').val(unit.symbol); $('#unit_name_ar').val(ar.name || ''); $('#unit_name_en').val(en.name || '');
            $(`input[name="status"][value="${unit.status}"]`).prop('checked', true); offcanvas.show();
        }});
    });

    $(document).on('click', '.btn-toggle-unit-status', function () {
        const btn = $(this);
        $.ajax({ url: baseUrl + '/' + btn.data('id') + '/toggle-status', type: 'PATCH', headers: { 'X-CSRF-TOKEN': csrfToken }, success: function (r) { table.ajax.reload(null, false); alertBox('success', r.message); } });
    });

    $(document).on('click', '.btn-delete-unit', function () {
        $('#deleteUnitForm').attr('action', baseUrl + '/' + $(this).data('id'));
        $('#deleteUnitName').text($(this).data('name')); deleteModal.show();
    });

    $('#deleteUnitForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({ url: $(this).attr('action'), type: 'POST', data: $(this).serialize(), headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function (r) { deleteModal.hide(); table.ajax.reload(null, false); alertBox('success', r.message); },
            error: function (xhr) { alertBox('danger', xhr.responseJSON?.message || @json(__('Unable to delete this unit.'))); }
        });
    });

    form.on('submit', function (e) {
        e.preventDefault();
        const action = $(this).attr('action');
        const method = $('#unitFormMethod').val();
        $.ajax({
            url: action, type: 'POST', data: $(this).serialize(), headers: { 'Accept': 'application/json' },
            success: function (r) { offcanvas.hide(); resetForm(); table.ajax.reload(null, false); alertBox('success', r.message); },
            error: function (xhr) {
                const errors = xhr.responseJSON?.errors;
                const msg = errors ? Object.values(errors).flat().join('<br>') : (xhr.responseJSON?.message || @json(__('Something went wrong. Please try again.')));
                alertBox('danger', msg);
            }
        });
    });
});
</script>
@endsection
