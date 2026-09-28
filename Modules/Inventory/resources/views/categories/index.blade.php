@extends('layouts/contentNavbarLayout')

@section('title', __('Item Categories') . ' - ' . config('variables.templateName'))

@section('content')
<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2"><i class="icon-base bx bx-category text-primary font-24"></i><span>{{ __('Item Categories') }}</span></h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Dashboard') }}</a></li><li class="breadcrumb-item"><a href="{{ route('inventory.index') }}">{{ __('Inventory') }}</a></li><li class="breadcrumb-item active">{{ __('Item Categories') }}</li></ol></nav>
    </div>
    <button type="button" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm" id="btnAddCategory"><i class="icon-base bx bx-plus font-18"></i>{{ __('Add New Category') }}</button>
</div>

<div id="alertContainer">@if(session('success'))<div class="alert alert-success alert-dismissible d-flex align-items-center mb-4" role="alert"><i class="icon-base bx bx-check-circle me-2 font-20"></i><div>{{ session('success') }}</div><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif</div>

<div class="card shadow-sm border-0 mb-4"><div class="card-body py-3"><div class="row g-3 align-items-end">
    <div class="col-12 col-sm-6 col-lg-4"><label class="form-label fw-semibold text-heading small mb-1">{{ __('Parent Category') }}</label><select id="filter_parent_id" class="form-select select2"><option value="">{{ __('All Categories') }}</option><option value="root">{{ __('Main Categories Only') }}</option>@foreach($parentCategories as $category)<option value="{{ $category->id }}">{{ $category->name }} ({{ $category->code }})</option>@endforeach</select></div>
    <div class="col-12 col-sm-6 col-lg-4"><label class="form-label fw-semibold text-heading small mb-1">{{ __('Status') }}</label><select id="filter_status" class="form-select select2"><option value="">{{ __('All Statuses') }}</option><option value="active">{{ __('Active') }}</option><option value="inactive">{{ __('Inactive') }}</option></select></div>
    <div class="col-12 col-sm-6 col-lg-4"><button type="button" id="btnResetFilters" class="btn btn-label-secondary w-100"><i class="icon-base bx bx-reset me-1"></i>{{ __('Reset Filters') }}</button></div>
</div></div></div>

<div class="card shadow-sm border-0"><div class="card-datatable table-responsive"><table id="categoriesTable" class="table table-hover border-top w-100 text-nowrap">
    <thead><tr><th class="text-center" style="width:50px">#</th><th style="min-width:260px">{{ __('Category Name & Code') }}</th><th>{{ __('Parent Category') }}</th><th class="text-center">{{ __('Type') }}</th><th class="text-center">{{ __('Status') }}</th><th class="text-center">{{ __('Created Date') }}</th><th class="text-center" style="width:120px">{{ __('Actions') }}</th></tr></thead>
    <tbody></tbody>
</table></div></div>

<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="categoryOffcanvas" style="width:500px;max-width:95vw">
    <div class="offcanvas-header border-bottom py-3 px-4"><h5 class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0"><i class="icon-base bx bx-category text-primary"></i><span id="categoryFormTitle">{{ __('Add New Category') }}</span></h5><button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button></div>
    <div class="offcanvas-body p-4"><form id="categoryForm" method="POST" action="{{ route('inventory.categories.store') }}">
        @csrf
        <input type="hidden" name="_method" id="categoryFormMethod" value="POST">
        <div class="row g-2 mb-3"><div class="col-12 col-sm-6"><label class="form-label fw-semibold">{{ __('Category Code') }} <span class="text-danger">*</span></label><input type="text" name="code" id="category_code" class="form-control" maxlength="50" required dir="ltr" placeholder="DAIRY"></div><div class="col-12 col-sm-6"><label class="form-label fw-semibold">{{ __('Sort Order') }}</label><input type="number" name="sort_order" id="category_sort_order" class="form-control" min="0" value="0" dir="ltr"></div></div>
        <div class="mb-3"><label class="form-label fw-semibold">{{ __('Parent Category') }}</label><select name="parent_id" id="category_parent_id" class="form-select"><option value="">{{ __('Main Category (No Parent)') }}</option>@foreach($parentCategories as $category)<option value="{{ $category->id }}">{{ $category->name }} ({{ $category->code }})</option>@endforeach</select></div>
        <div class="mb-3"><label class="form-label fw-semibold">{{ __('Category Name [English]') }} <span class="text-danger">*</span></label><input type="text" name="name_en" id="category_name_en" class="form-control" maxlength="255" required dir="ltr" placeholder="Dairy"></div>
        <div class="mb-3"><label class="form-label fw-semibold">{{ __('Category Name [Arabic]') }} <span class="text-danger">*</span></label><input type="text" name="name_ar" id="category_name_ar" class="form-control" maxlength="255" required dir="rtl" placeholder="ألبان"></div>
        <div class="mb-3"><label class="form-label fw-semibold">{{ __('Description [English]') }}</label><textarea name="description_en" id="category_description_en" class="form-control" rows="3" dir="ltr"></textarea></div>
        <div class="mb-3"><label class="form-label fw-semibold">{{ __('Description [Arabic]') }}</label><textarea name="description_ar" id="category_description_ar" class="form-control" rows="3" dir="rtl"></textarea></div>
        <div class="mb-4"><label class="form-label fw-semibold d-block">{{ __('Status') }}</label><div class="d-flex gap-3"><label class="form-check text-success fw-semibold"><input class="form-check-input" type="radio" name="status" value="active" checked>{{ __('Active') }}</label><label class="form-check text-danger fw-semibold"><input class="form-check-input" type="radio" name="status" value="inactive">{{ __('Inactive') }}</label></div></div>
        <div class="d-flex gap-2 justify-content-end"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button><button type="submit" class="btn btn-primary"><i class="icon-base bx bx-save me-1"></i>{{ __('Save') }}</button></div>
    </form></div>
</div>

<div class="modal fade" id="deleteCategoryModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered modal-sm"><form id="deleteCategoryForm" class="modal-content">@csrf @method('DELETE')<div class="modal-body text-center py-4"><div class="avatar avatar-xl bg-label-danger mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center"><i class="icon-base bx bx-trash font-32 text-danger"></i></div><h5 class="fw-bold mb-2">{{ __('Confirm Delete') }}</h5><p class="text-muted mb-0">{{ __('Are you sure you want to delete:') }}<br><strong id="deleteCategoryName" class="text-danger"></strong></p></div><div class="modal-footer justify-content-center border-0 pt-0 pb-4"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button><button type="submit" class="btn btn-danger">{{ __('Yes, Delete') }}</button></div></form></div></div>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const $ = window.jQuery || window.$;
    const dataUrl = @json(route('inventory.categories.data'));
    const baseUrl = @json(route('inventory.categories.index'));
    const csrfToken = @json(csrf_token());
    const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(document.getElementById('categoryOffcanvas'));
    const deleteModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('deleteCategoryModal'));
    const form = $('#categoryForm');

    function alertBox(type, message) { $('#alertContainer').html(`<div class="alert alert-${type} alert-dismissible d-flex align-items-center mb-4" role="alert"><i class="icon-base bx ${type === 'success' ? 'bx-check-circle' : 'bx-error-circle'} me-2 font-20"></i><div>${message}</div><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`); }
    function resetForm() { form[0].reset(); form.attr('action', @json(route('inventory.categories.store'))); $('#categoryFormMethod').val('POST'); $('#categoryFormTitle').text(@json(__('Add New Category'))); $('#category_sort_order').val(0); $('input[name="status"][value="active"]').prop('checked', true); }

    const table = $('#categoriesTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 10,
        ajax: { url: dataUrl, data: function (d) { d.parent_id = $('#filter_parent_id').val(); d.status = $('#filter_status').val(); } },
        columns: [
            { data: 'DT_RowIndex', name: 'id', orderable: false, searchable: false, className: 'text-center align-middle' },
            { data: 'name_ar', name: 'name_ar', className: 'align-middle', render: (data, type, row) => `<div><span class="fw-bold text-heading d-block">${data || '-'}</span><small class="text-muted" dir="ltr">${row.name_en || ''} · ${row.code || ''}</small></div>` },
            { data: 'parent_name', name: 'parent_id', orderable: false, className: 'align-middle' },
            { data: 'type_badge', name: 'parent_id', orderable: false, className: 'text-center align-middle' },
            { data: 'status_badge', name: 'status', className: 'text-center align-middle' },
            { data: 'created_at', name: 'created_at', className: 'text-center align-middle' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center align-middle' }
        ],
        order: [[1, 'asc']],
        language: { search: '', searchPlaceholder: @json(__('Search...')), paginate: { previous: '<i class="bx bx-chevron-left"></i>', next: '<i class="bx bx-chevron-right"></i>' } }
    });

    $('#filter_parent_id, #filter_status').on('change', () => table.ajax.reload());
    $('#btnResetFilters').on('click', function () { $('#filter_parent_id, #filter_status').val('').trigger('change'); table.ajax.reload(); });
    $('#btnAddCategory').on('click', function () { resetForm(); offcanvas.show(); });

    $(document).on('click', '.btn-edit-category', function () {
        $.ajax({ url: baseUrl + '/' + $(this).data('id'), headers: { Accept: 'application/json' }, success: function (response) {
            const category = response.data;
            const ar = category.translations.find(item => item.locale === 'ar') || {};
            const en = category.translations.find(item => item.locale === 'en') || {};
            form.attr('action', baseUrl + '/' + category.id); $('#categoryFormMethod').val('PUT'); $('#categoryFormTitle').text(@json(__('Edit Category')));
            $('#category_code').val(category.code); $('#category_sort_order').val(category.sort_order); $('#category_parent_id').val(category.parent_id || '');
            $('#category_name_ar').val(ar.name || ''); $('#category_name_en').val(en.name || ''); $('#category_description_ar').val(ar.description || ''); $('#category_description_en').val(en.description || '');
            $(`input[name="status"][value="${category.status}"]`).prop('checked', true); offcanvas.show();
        }});
    });

    $(document).on('click', '.btn-toggle-category-status', function () { const button = $(this); $.ajax({ url: baseUrl + '/' + button.data('id') + '/toggle-status', type: 'PATCH', headers: { 'X-CSRF-TOKEN': csrfToken }, success: function (response) { table.ajax.reload(null, false); alertBox('success', response.message); } }); });
    $(document).on('click', '.btn-delete-category', function () { $('#deleteCategoryForm').attr('action', baseUrl + '/' + $(this).data('id')); $('#deleteCategoryName').text($(this).data('name')); deleteModal.show(); });
    $('#deleteCategoryForm').on('submit', function (event) { event.preventDefault(); $.ajax({ url: $(this).attr('action'), type: 'POST', data: $(this).serialize(), headers: { 'X-Requested-With': 'XMLHttpRequest' }, success: function (response) { deleteModal.hide(); table.ajax.reload(null, false); alertBox('success', response.message); }, error: function (xhr) { alertBox('danger', xhr.responseJSON?.message || @json(__('Unable to delete this category.'))); } }); });
});
</script>
@endsection
