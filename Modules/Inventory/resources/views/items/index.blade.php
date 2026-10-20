@extends('layouts/contentNavbarLayout')

@section('title', __('Items') . ' - ' . config('variables.templateName'))

@section('content')
<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2"><i class="icon-base bx bx-box text-primary font-24"></i><span>{{ __('Items') }}</span></h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Dashboard') }}</a></li><li class="breadcrumb-item"><a href="{{ route('inventory.index') }}">{{ __('Inventory') }}</a></li><li class="breadcrumb-item active">{{ __('Items') }}</li></ol></nav>
    </div>
    @can('inventory.items.create')
    <button type="button" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm" id="btnAddItem"><i class="icon-base bx bx-plus font-18"></i>{{ __('Add New Item') }}</button>
    @endcan
</div>

<div id="alertContainer">@if(session('success'))<div class="alert alert-success alert-dismissible d-flex align-items-center mb-4" role="alert"><i class="icon-base bx bx-check-circle me-2 font-20"></i><div>{{ session('success') }}</div><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif</div>

{{-- Filters --}}
<div class="card shadow-sm border-0 mb-4"><div class="card-body py-3"><div class="row g-3 align-items-end">
    <div class="col-12 col-sm-6 col-lg-3"><label class="form-label fw-semibold text-heading small mb-1">{{ __('Category') }}</label>
        <select id="filter_category_id" class="form-select select2"><option value="">{{ __('All Categories') }}</option>@foreach($categories as $cat)<option value="{{ $cat->id }}">{{ $cat->name }}</option>@endforeach</select></div>
    <div class="col-12 col-sm-6 col-lg-3"><label class="form-label fw-semibold text-heading small mb-1">{{ __('Unit') }}</label>
        <select id="filter_unit_id" class="form-select select2"><option value="">{{ __('All Units') }}</option>@foreach($units as $unit)<option value="{{ $unit->id }}">{{ $unit->name }}</option>@endforeach</select></div>
    <div class="col-12 col-sm-6 col-lg-3"><label class="form-label fw-semibold text-heading small mb-1">{{ __('Status') }}</label>
        <select id="filter_status" class="form-select select2"><option value="">{{ __('All Statuses') }}</option><option value="active">{{ __('Active') }}</option><option value="inactive">{{ __('Inactive') }}</option></select></div>
    <div class="col-12 col-sm-6 col-lg-3"><button type="button" id="btnResetFilters" class="btn btn-label-secondary w-100"><i class="icon-base bx bx-reset me-1"></i>{{ __('Reset Filters') }}</button></div>
</div></div></div>

{{-- Table --}}
<div class="card shadow-sm border-0"><div class="card-datatable table-responsive"><table id="itemsTable" class="table table-hover border-top w-100 text-nowrap">
    <thead><tr>
        <th class="text-center" style="width:50px">#</th>
        <th style="width:60px">{{ __('Image') }}</th>
        <th style="min-width:260px">{{ __('Item Name & Code') }}</th>
        <th>{{ __('Category') }}</th>
        <th class="text-center">{{ __('Unit') }}</th>
        <th class="text-center">{{ __('Cost Price') }}</th>
        <th class="text-center">{{ __('Selling Price') }}</th>
        <th class="text-center">{{ __('Total Stock') }}</th>
        <th class="text-center">{{ __('Status') }}</th>
        <th class="text-center" style="width:120px">{{ __('Actions') }}</th>
    </tr></thead>
    <tbody></tbody>
</table></div></div>

{{-- Offcanvas Form --}}
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="itemOffcanvas" style="width:540px;max-width:95vw">
    <div class="offcanvas-header border-bottom py-3 px-4"><h5 class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0"><i class="icon-base bx bx-box text-primary"></i><span id="itemFormTitle">{{ __('Add New Item') }}</span></h5><button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button></div>
    <div class="offcanvas-body p-4">
        <form id="itemForm" method="POST" action="{{ route('inventory.items.store') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="_method" id="itemFormMethod" value="POST">

        {{-- Image --}}
        <div class="mb-3 text-center">
            <div id="imagePreviewWrapper" class="mb-2 d-flex justify-content-center">
                <div id="imagePlaceholder" class="avatar avatar-xl bg-label-secondary rounded-3 d-flex align-items-center justify-content-center" style="width:80px;height:80px;cursor:pointer">
                    <i class="bx bx-image-add font-28 text-muted"></i>
                </div>
                <img id="imagePreview" src="" class="rounded-3 d-none" style="width:80px;height:80px;object-fit:cover;cursor:pointer" alt="">
            </div>
            <input type="file" name="image" id="item_image" class="d-none" accept="image/*">
            <label for="item_image" class="btn btn-sm btn-outline-secondary"><i class="bx bx-upload me-1"></i>{{ __('Upload Image') }}</label>
            <button type="button" id="btnRemoveImage" class="btn btn-sm btn-outline-danger d-none ms-1"><i class="bx bx-x me-1"></i>{{ __('Remove') }}</button>
        </div>

        <div class="row g-2 mb-3">
            <div class="col-12 col-sm-6"><label class="form-label fw-semibold">{{ __('Item Code') }} <span class="text-danger">*</span></label><input type="text" name="code" id="item_code" class="form-control" maxlength="50" required dir="ltr" placeholder="PRD-001"></div>
            <div class="col-12 col-sm-6"><label class="form-label fw-semibold">{{ __('Barcode') }}</label><input type="text" name="barcode" id="item_barcode" class="form-control" maxlength="100" dir="ltr" placeholder="6281234567890"></div>
        </div>
        <div class="mb-3"><label class="form-label fw-semibold">{{ __('Category') }} <span class="text-danger">*</span></label>
            <select name="category_id" id="item_category_id" class="form-select" required><option value="">{{ __('Select Category') }}</option>@foreach($categories as $cat)<option value="{{ $cat->id }}">{{ $cat->name }}</option>@endforeach</select></div>
        <div class="mb-3"><label class="form-label fw-semibold">{{ __('Unit of Measure') }}</label>
            <select name="unit_id" id="item_unit_id" class="form-select"><option value="">{{ __('No Unit') }}</option>@foreach($units as $unit)<option value="{{ $unit->id }}">{{ $unit->name }} ({{ $unit->symbol }})</option>@endforeach</select></div>
        <div class="mb-3"><label class="form-label fw-semibold">{{ __('Item Name [Arabic]') }} <span class="text-danger">*</span></label><input type="text" name="name_ar" id="item_name_ar" class="form-control" maxlength="255" required dir="rtl" placeholder="مثال: حليب كامل الدسم"></div>
        <div class="mb-3"><label class="form-label fw-semibold">{{ __('Item Name [English]') }} <span class="text-danger">*</span></label><input type="text" name="name_en" id="item_name_en" class="form-control" maxlength="255" required dir="ltr" placeholder="Example: Full-Fat Milk"></div>
        <div class="mb-3"><label class="form-label fw-semibold">{{ __('Description [Arabic]') }}</label><textarea name="description_ar" id="item_description_ar" class="form-control" rows="2" dir="rtl"></textarea></div>
        <div class="mb-3"><label class="form-label fw-semibold">{{ __('Description [English]') }}</label><textarea name="description_en" id="item_description_en" class="form-control" rows="2" dir="ltr"></textarea></div>
        <div class="row g-2 mb-3">
            <div class="col-12 col-sm-6"><label class="form-label fw-semibold">{{ __('Cost Price') }}</label><input type="number" name="cost_price" id="item_cost_price" class="form-control" min="0" step="0.0001" value="0" dir="ltr"></div>
            <div class="col-12 col-sm-6"><label class="form-label fw-semibold">{{ __('Selling Price') }}</label><input type="number" name="selling_price" id="item_selling_price" class="form-control" min="0" step="0.0001" value="0" dir="ltr"></div>
        </div>
        <div class="mb-4"><label class="form-label fw-semibold d-block">{{ __('Status') }}</label><div class="d-flex gap-3"><label class="form-check text-success fw-semibold"><input class="form-check-input" type="radio" name="status" value="active" checked>{{ __('Active') }}</label><label class="form-check text-danger fw-semibold"><input class="form-check-input" type="radio" name="status" value="inactive">{{ __('Inactive') }}</label></div></div>
        <div class="d-flex gap-2 justify-content-end"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button><button type="submit" class="btn btn-primary"><i class="icon-base bx bx-save me-1"></i>{{ __('Save') }}</button></div>
    </form></div>
</div>

{{-- Delete Modal --}}
<div class="modal fade" id="deleteItemModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered modal-sm"><form id="deleteItemForm" class="modal-content">@csrf @method('DELETE')<div class="modal-body text-center py-4"><div class="avatar avatar-xl bg-label-danger mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center"><i class="icon-base bx bx-trash font-32 text-danger"></i></div><h5 class="fw-bold mb-2">{{ __('Confirm Delete') }}</h5><p class="text-muted mb-0">{{ __('Are you sure you want to delete:') }}<br><strong id="deleteItemName" class="text-danger"></strong></p></div><div class="modal-footer justify-content-center border-0 pt-0 pb-4"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button><button type="submit" class="btn btn-danger">{{ __('Yes, Delete') }}</button></div></form></div></div>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const $ = window.jQuery || window.$;
    const dataUrl   = @json(route('inventory.items.data'));
    const baseUrl   = @json(route('inventory.items.index'));
    const csrfToken = @json(csrf_token());
    const offcanvas   = bootstrap.Offcanvas.getOrCreateInstance(document.getElementById('itemOffcanvas'));
    const deleteModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('deleteItemModal'));
    const form = $('#itemForm');

    function alertBox(type, message) { $('#alertContainer').html(`<div class="alert alert-${type} alert-dismissible d-flex align-items-center mb-4" role="alert"><i class="icon-base bx ${type === 'success' ? 'bx-check-circle' : 'bx-error-circle'} me-2 font-20"></i><div>${message}</div><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`); }

    function resetForm() {
        form[0].reset();
        form.attr('action', @json(route('inventory.items.store')));
        $('#itemFormMethod').val('POST');
        $('#itemFormTitle').text(@json(__('Add New Item')));
        $('input[name="status"][value="active"]').prop('checked', true);
        $('#item_cost_price, #item_selling_price').val(0);
        resetImagePreview(null);
    }

    function resetImagePreview(src) {
        if (src) {
            $('#imagePreview').attr('src', src).removeClass('d-none');
            $('#imagePlaceholder').addClass('d-none');
            $('#btnRemoveImage').removeClass('d-none');
        } else {
            $('#imagePreview').addClass('d-none').attr('src', '');
            $('#imagePlaceholder').removeClass('d-none');
            $('#btnRemoveImage').addClass('d-none');
        }
    }

    $('#item_image').on('change', function () {
        const file = this.files[0];
        if (file) { resetImagePreview(URL.createObjectURL(file)); }
    });
    $('#imagePlaceholder, #imagePreview').on('click', function () { $('#item_image').trigger('click'); });
    $('#btnRemoveImage').on('click', function () { $('#item_image').val(''); resetImagePreview(null); });

    const table = $('#itemsTable').DataTable({
        processing: true, serverSide: true, pageLength: 15,
        ajax: { url: dataUrl, data: function (d) { d.category_id = $('#filter_category_id').val(); d.unit_id = $('#filter_unit_id').val(); d.status = $('#filter_status').val(); } },
        columns: [
            { data: 'DT_RowIndex', name: 'id', orderable: false, searchable: false, className: 'text-center align-middle' },
            { data: 'image_thumb', name: 'image_path', orderable: false, searchable: false, className: 'align-middle' },
            { data: 'name_ar', name: 'name_ar', className: 'align-middle', render: (data, type, row) => `<div><span class="fw-bold text-heading d-block">${data || '-'}</span><small class="text-muted" dir="ltr">${row.name_en || ''} · ${row.code || ''}</small></div>` },
            { data: 'category_name', name: 'category_id', orderable: false, className: 'align-middle' },
            { data: 'unit_name', name: 'unit_id', orderable: false, className: 'text-center align-middle' },
            { data: 'cost_price', name: 'cost_price', className: 'text-center align-middle', render: d => Number(d).toLocaleString('ar-SA', {minimumFractionDigits: 2}) },
            { data: 'selling_price', name: 'selling_price', className: 'text-center align-middle', render: d => Number(d).toLocaleString('ar-SA', {minimumFractionDigits: 2}) },
            { data: 'total_quantity', name: 'total_quantity', orderable: false, searchable: false, className: 'text-center align-middle', render: d => `<span class="badge bg-label-info">${d}</span>` },
            { data: 'status_badge', name: 'status', className: 'text-center align-middle' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center align-middle' }
        ],
        order: [[2, 'asc']],
        language: { search: '', searchPlaceholder: @json(__('Search...')), paginate: { previous: '<i class="bx bx-chevron-left"></i>', next: '<i class="bx bx-chevron-right"></i>' } }
    });

    $('#filter_category_id, #filter_unit_id, #filter_status').on('change', () => table.ajax.reload());
    $('#btnResetFilters').on('click', function () { $('#filter_category_id, #filter_unit_id, #filter_status').val('').trigger('change'); table.ajax.reload(); });
    $('#btnAddItem').on('click', function () { resetForm(); offcanvas.show(); });

    $(document).on('click', '.btn-edit-item', function () {
        $.ajax({ url: baseUrl + '/' + $(this).data('id'), headers: { Accept: 'application/json' }, success: function (response) {
            const item = response.data;
            const ar = item.translations.find(t => t.locale === 'ar') || {};
            const en = item.translations.find(t => t.locale === 'en') || {};
            form.attr('action', baseUrl + '/' + item.id); $('#itemFormMethod').val('PUT'); $('#itemFormTitle').text(@json(__('Edit Item')));
            $('#item_code').val(item.code); $('#item_barcode').val(item.barcode || '');
            $('#item_category_id').val(item.category_id); $('#item_unit_id').val(item.unit_id || '');
            $('#item_name_ar').val(ar.name || ''); $('#item_name_en').val(en.name || '');
            $('#item_description_ar').val(ar.description || ''); $('#item_description_en').val(en.description || '');
            $('#item_cost_price').val(item.cost_price); $('#item_selling_price').val(item.selling_price);
            $(`input[name="status"][value="${item.status}"]`).prop('checked', true);
            if (item.image_path) { resetImagePreview('/storage/' + item.image_path); } else { resetImagePreview(null); }
            offcanvas.show();
        }});
    });

    $(document).on('click', '.btn-toggle-item-status', function () {
        const btn = $(this);
        $.ajax({ url: baseUrl + '/' + btn.data('id') + '/toggle-status', type: 'PATCH', headers: { 'X-CSRF-TOKEN': csrfToken }, success: function (r) { table.ajax.reload(null, false); alertBox('success', r.message); } });
    });

    $(document).on('click', '.btn-delete-item', function () {
        $('#deleteItemForm').attr('action', baseUrl + '/' + $(this).data('id'));
        $('#deleteItemName').text($(this).data('name')); deleteModal.show();
    });

    $('#deleteItemForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({ url: $(this).attr('action'), type: 'POST', data: $(this).serialize(), headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function (r) { deleteModal.hide(); table.ajax.reload(null, false); alertBox('success', r.message); },
            error: function (xhr) { alertBox('danger', xhr.responseJSON?.message || @json(__('Unable to delete this item.'))); }
        });
    });

    form.on('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        $.ajax({
            url: $(this).attr('action'), type: 'POST', data: formData,
            processData: false, contentType: false,
            headers: { 'Accept': 'application/json' },
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
