@extends('layouts/contentNavbarLayout')

@section('title', __('Warehouses') . ' - ' . config('variables.templateName'))

@section('content')
<!-- Header & Add Button -->
<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <i class="icon-base bx bx-package text-primary font-24"></i>
            <span>{{ __('Warehouses') }}</span>
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('Inventory') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Warehouses') }}</li>
            </ol>
        </nav>
    </div>
    <button type="button" class="btn btn-primary d-flex align-items-center justify-content-center gap-2 shadow-sm" id="btnAddWarehouse">
        <i class="icon-base bx bx-plus font-18"></i>
        <span>{{ __('Add New Warehouse') }}</span>
    </button>
</div>

<!-- Alert Notifications Container -->
<div id="alertContainer">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible d-flex align-items-center mb-4" role="alert">
            <i class="icon-base bx bx-check-circle me-2 font-20"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible d-flex align-items-center mb-4" role="alert">
            <i class="icon-base bx bx-error-circle me-2 font-20"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
</div>

<!-- Filters Card -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body py-3">
        <div class="row g-3 align-items-end">
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Accounting Entity') }}</label>
                <select id="filter_entity_id" class="form-select select2">
                    <option value="">{{ __('All Entities') }}</option>
                    @foreach($entities as $entity)
                        <option value="{{ $entity->id }}">
                            {{ $entity->translate(app()->getLocale())?->name ?? $entity->translate('ar')?->name ?? $entity->code }} ({{ $entity->code }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Warehouse Type') }}</label>
                <select id="filter_type" class="form-select select2">
                    <option value="">{{ __('All Types') }}</option>
                    <option value="main">{{ __('Main Warehouse') }}</option>
                    <option value="branch">{{ __('Branch Warehouse') }}</option>
                </select>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Status') }}</label>
                <select id="filter_status" class="form-select select2">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="active">{{ __('Active') }}</option>
                    <option value="inactive">{{ __('Inactive') }}</option>
                </select>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <button type="button" id="btnResetFilters" class="btn btn-label-secondary w-100 d-flex align-items-center justify-content-center gap-1">
                    <i class="icon-base bx bx-reset font-18"></i>
                    <span>{{ __('Reset Filters') }}</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Table Card -->
<div class="card shadow-sm border-0">
    <div class="card-datatable table-responsive">
        <table id="warehousesTable" class="table table-hover border-top w-100 text-nowrap">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px">#</th>
                    <th style="min-width: 260px">{{ __('Warehouse Name & Code') }}</th>
                    <th>{{ __('Accounting Entity') }}</th>
                    <th>{{ __('Branch') }}</th>
                    <th class="text-center">{{ __('Type') }}</th>
                    <th class="text-center">{{ __('Status') }}</th>
                    <th class="text-center">{{ __('Created Date') }}</th>
                    <th class="text-center" style="width: 120px">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- Offcanvas Form (Add / Edit) -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="warehouseOffcanvas" style="width: 520px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3 px-4">
        <h5 class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
            <i class="icon-base bx bx-package text-primary"></i>
            <span id="warehouseFormTitle">{{ __('Add New Warehouse') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-4">
        <!-- Inside Form Alert Container for validation/server errors -->
        <div id="formAlertContainer" class="d-none mb-3">
            <div class="alert alert-danger alert-dismissible mb-0 py-2" role="alert">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <i class="icon-base bx bx-error-circle font-20"></i>
                    <strong class="small">{{ __('Please fix the following errors:') }}</strong>
                </div>
                <ul id="formErrorList" class="mb-0 ps-3 small"></ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>

        <form id="warehouseForm" method="POST" action="{{ route('inventory.store') }}">
            @csrf
            <input type="hidden" name="_method" id="warehouseFormMethod" value="POST">

            <!-- Accounting Entity -->
            <div class="mb-3">
                <label class="form-label fw-semibold">{{ __('Accounting Entity') }} <span class="text-danger">*</span></label>
                <select name="entity_id" id="warehouse_entity_id" class="form-select" required>
                    <option value="">{{ __('Select Accounting Entity') }}</option>
                    @foreach($entities as $entity)
                        <option value="{{ $entity->id }}" data-nursery-id="{{ $entity->nursery_id }}">
                            {{ $entity->translate(app()->getLocale())?->name ?? $entity->translate('ar')?->name ?? $entity->code }} ({{ $entity->code }})
                        </option>
                    @endforeach
                </select>
                <div class="invalid-feedback"></div>
            </div>

            <!-- Type & Code -->
            <div class="row g-2 mb-3">
                <div class="col-12 col-sm-6">
                    <label class="form-label fw-semibold">{{ __('Warehouse Type') }} <span class="text-danger">*</span></label>
                    <select name="type" id="warehouse_type" class="form-select" required>
                        <option value="branch">{{ __('Branch Warehouse') }}</option>
                        <option value="main">{{ __('Main Warehouse') }}</option>
                    </select>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-12 col-sm-6">
                    <label class="form-label fw-semibold">{{ __('Warehouse Code') }} <span class="text-danger">*</span></label>
                    <input type="text" name="code" id="warehouse_code" class="form-control" maxlength="50" required dir="ltr" placeholder="e.g. WH-001">
                    <div class="invalid-feedback"></div>
                </div>
            </div>

            <!-- Branch (Visible only when type is 'branch') -->
            <div class="mb-3" id="warehouseBranchGroup">
                <label class="form-label fw-semibold">{{ __('Branch') }} <span class="text-danger">*</span></label>
                <select name="branch_id" id="warehouse_branch_id" class="form-select">
                    <option value="">{{ __('Select Branch') }}</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" data-nursery-id="{{ $branch->nursery_id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback"></div>
                <small class="text-muted">{{ __('Each branch can have one default warehouse.') }}</small>
            </div>

            <!-- Names -->
            <div class="mb-3">
                <label class="form-label fw-semibold">{{ __('Warehouse Name [Arabic]') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_ar" id="warehouse_name_ar" class="form-control" maxlength="255" required dir="rtl" placeholder="مخزن فرع القاهرة">
                <div class="invalid-feedback"></div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">{{ __('Warehouse Name [English]') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_en" id="warehouse_name_en" class="form-control" maxlength="255" required dir="ltr" placeholder="Cairo Branch Warehouse">
                <div class="invalid-feedback"></div>
            </div>

            <!-- Descriptions -->
            <div class="mb-3">
                <label class="form-label fw-semibold">{{ __('Description [Arabic]') }}</label>
                <textarea name="description_ar" id="warehouse_description_ar" class="form-control" rows="2" dir="rtl" placeholder="وصف إضافي للمخزن..."></textarea>
                <div class="invalid-feedback"></div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">{{ __('Description [English]') }}</label>
                <textarea name="description_en" id="warehouse_description_en" class="form-control" rows="2" dir="ltr" placeholder="Additional warehouse description..."></textarea>
                <div class="invalid-feedback"></div>
            </div>

            <!-- Status -->
            <div class="mb-4">
                <label class="form-label fw-semibold d-block">{{ __('Status') }}</label>
                <div class="d-flex align-items-center gap-3">
                    <label class="form-check form-check-inline text-success fw-semibold mb-0 cursor-pointer">
                        <input class="form-check-input" type="radio" name="status" value="active" checked>
                        <span class="form-check-label">{{ __('Active') }}</span>
                    </label>
                    <label class="form-check form-check-inline text-danger fw-semibold mb-0 cursor-pointer">
                        <input class="form-check-input" type="radio" name="status" value="inactive">
                        <span class="form-check-label">{{ __('Inactive') }}</span>
                    </label>
                </div>
            </div>

            <!-- Buttons -->
            <div class="d-flex gap-2 justify-content-end border-top pt-3">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-1" id="btnSubmitWarehouse">
                    <i class="icon-base bx bx-save"></i>
                    <span id="btnSubmitText">{{ __('Save') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteWarehouseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <form id="deleteWarehouseForm" class="modal-content">
            @csrf
            @method('DELETE')
            <div class="modal-body text-center py-4">
                <div class="avatar avatar-xl bg-label-danger mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-trash font-32 text-danger"></i>
                </div>
                <h5 class="fw-bold mb-2">{{ __('Confirm Delete') }}</h5>
                <p class="text-muted mb-0">
                    {{ __('Are you sure you want to delete:') }}<br>
                    <strong id="deleteWarehouseName" class="text-danger"></strong>
                </p>
            </div>
            <div class="modal-footer justify-content-center border-0 pt-0 pb-4">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-danger" id="btnConfirmDelete">
                    <i class="icon-base bx bx-trash me-1"></i>
                    <span>{{ __('Yes, Delete') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const $ = window.jQuery || window.$;
    const dataUrl = @json(route('inventory.data'));
    const baseUrl = @json(route('inventory.index'));
    const csrfToken = @json(csrf_token());
    const offcanvasEl = document.getElementById('warehouseOffcanvas');
    const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);
    const deleteModalEl = document.getElementById('deleteWarehouseModal');
    const deleteModal = bootstrap.Modal.getOrCreateInstance(deleteModalEl);
    const form = $('#warehouseForm');
    const btnSubmit = $('#btnSubmitWarehouse');
    const btnSubmitText = $('#btnSubmitText');

    function alertBox(type, message) {
        const icon = type === 'success' ? 'bx-check-circle' : 'bx-error-circle';
        $('#alertContainer').html(`
            <div class="alert alert-${type} alert-dismissible d-flex align-items-center mb-4" role="alert">
                <i class="icon-base bx ${icon} me-2 font-20"></i>
                <div>${message}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function clearFormErrors() {
        $('#formAlertContainer').addClass('d-none');
        $('#formErrorList').empty();
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').text('');
    }

    function showFormErrors(errors) {
        $('#formErrorList').empty();
        $.each(errors, function (field, messages) {
            const input = form.find(`[name="${field}"]`);
            if (input.length) {
                input.addClass('is-invalid');
                input.siblings('.invalid-feedback').text(messages[0]);
            }
            $('#formErrorList').append(`<li>${messages[0]}</li>`);
        });
        $('#formAlertContainer').removeClass('d-none');
    }

    function filterBranches(selected = '') {
        const nurseryId = $('#warehouse_entity_id option:selected').data('nursery-id');
        let visibleCount = 0;

        $('#warehouse_branch_id option').each(function () {
            const option = $(this);
            if (!option.val()) {
                option.show();
                return;
            }
            const match = !nurseryId || String(option.data('nursery-id')) === String(nurseryId);
            if (match) {
                option.show();
                visibleCount++;
            } else {
                option.hide();
            }
        });

        $('#warehouse_branch_id').val(selected || '');
    }

    function updateTypeFields() {
        const isMain = $('#warehouse_type').val() === 'main';
        if (isMain) {
            $('#warehouseBranchGroup').slideUp(150);
            $('#warehouse_branch_id').prop('required', false).val('');
        } else {
            $('#warehouseBranchGroup').slideDown(150);
            $('#warehouse_branch_id').prop('required', true);
        }
    }

    function resetForm() {
        clearFormErrors();
        form[0].reset();
        form.attr('action', @json(route('inventory.store')));
        $('#warehouseFormMethod').val('POST');
        $('#warehouseFormTitle').text(@json(__('Add New Warehouse')));
        $('#warehouse_type').val('branch');
        $('input[name="status"][value="active"]').prop('checked', true);
        filterBranches();
        updateTypeFields();
    }

    // Initialize DataTables
    const isRtl = document.documentElement.dir === 'rtl' || @json(app()->getLocale() === 'ar');
    const table = $('#warehousesTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 10,
        ajax: {
            url: dataUrl,
            data: function (d) {
                d.entity_id = $('#filter_entity_id').val();
                d.type = $('#filter_type').val();
                d.status = $('#filter_status').val();
            }
        },
        columns: [
            {
                data: 'DT_RowIndex',
                name: 'id',
                orderable: false,
                searchable: false,
                className: 'text-center align-middle',
                render: data => `<span class="text-muted fw-semibold">${data || '-'}</span>`
            },
            {
                data: 'name_ar',
                name: 'name_ar',
                className: 'align-middle',
                render: (data, type, row) => `
                    <div>
                        <span class="fw-bold text-heading d-block mb-1">${isRtl ? (data || '-') : (row.name_en || data || '-')}</span>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-label-dark" dir="ltr"><i class="icon-base bx bx-barcode me-1"></i>${row.code || '-'}</span>
                            <small class="text-muted" dir="ltr">${isRtl ? (row.name_en || '') : (data || '')}</small>
                        </div>
                    </div>`
            },
            {
                data: 'entity_name',
                name: 'entity_id',
                orderable: false,
                className: 'align-middle',
                render: data => `<span class="badge bg-label-secondary"><i class="icon-base bx bx-buildings me-1"></i>${data || '-'}</span>`
            },
            {
                data: 'branch_name',
                name: 'branch_id',
                orderable: false,
                className: 'align-middle'
            },
            {
                data: 'type_badge',
                name: 'type',
                className: 'text-center align-middle'
            },
            {
                data: 'status_badge',
                name: 'status',
                className: 'text-center align-middle'
            },
            {
                data: 'created_at',
                name: 'created_at',
                className: 'text-center align-middle text-muted small'
            },
            {
                data: 'actions',
                name: 'actions',
                orderable: false,
                searchable: false,
                className: 'text-center align-middle'
            }
        ],
        order: [[1, 'asc']],
        language: {
            search: '',
            searchPlaceholder: @json(__('Search...')),
            processing: '<div class="spinner-border spinner-border-sm text-primary" role="status"></div>',
            emptyTable: @json(__('No items found.')),
            zeroRecords: @json(__('No items found.')),
            paginate: {
                previous: '<i class="bx bx-chevron-' + (isRtl ? 'right' : 'left') + '"></i>',
                next: '<i class="bx bx-chevron-' + (isRtl ? 'left' : 'right') + '"></i>'
            }
        }
    });

    // Filters
    $('#filter_entity_id, #filter_type, #filter_status').on('change', function () {
        table.ajax.reload();
    });

    $('#btnResetFilters').on('click', function () {
        $('#filter_entity_id, #filter_type, #filter_status').val('').trigger('change');
    });

    // Add New Warehouse
    $('#btnAddWarehouse').on('click', function () {
        resetForm();
        offcanvas.show();
    });

    // Form Change Events
    $('#warehouse_entity_id').on('change', function () {
        filterBranches();
    });

    $('#warehouse_type').on('change', function () {
        updateTypeFields();
    });

    // Form Submit (AJAX)
    form.on('submit', function (event) {
        event.preventDefault();
        clearFormErrors();

        const url = form.attr('action');
        const method = $('#warehouseFormMethod').val() || 'POST';
        const formData = form.serialize();

        btnSubmit.prop('disabled', true);
        btnSubmitText.text(@json(__('Saving...')));

        $.ajax({
            url: url,
            type: method,
            data: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            success: function (response) {
                btnSubmit.prop('disabled', false);
                btnSubmitText.text(@json(__('Save')));
                offcanvas.hide();
                table.ajax.reload(null, false);
                alertBox('success', response.message || @json(__('Warehouse created successfully.')));
            },
            error: function (xhr) {
                btnSubmit.prop('disabled', false);
                btnSubmitText.text(@json(__('Save')));

                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    showFormErrors(xhr.responseJSON.errors);
                } else {
                    const message = (xhr.responseJSON && xhr.responseJSON.message)
                        ? xhr.responseJSON.message
                        : @json(__('Something went wrong. Please try again.'));
                    alertBox('danger', message);
                }
            }
        });
    });

    // Edit Warehouse
    $(document).on('click', '.btn-edit-warehouse', function () {
        const id = $(this).data('id');
        clearFormErrors();

        $.ajax({
            url: baseUrl + '/' + id,
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function (response) {
                const warehouse = response.data;
                const ar = (warehouse.translations || []).find(item => item.locale === 'ar') || {};
                const en = (warehouse.translations || []).find(item => item.locale === 'en') || {};

                form.attr('action', baseUrl + '/' + id);
                $('#warehouseFormMethod').val('PUT');
                $('#warehouseFormTitle').text(@json(__('Edit Warehouse')));

                $('#warehouse_entity_id').val(warehouse.entity_id);
                filterBranches(warehouse.branch_id || '');
                $('#warehouse_type').val(warehouse.type);
                $('#warehouse_code').val(warehouse.code);
                $('#warehouse_name_ar').val(ar.name || '');
                $('#warehouse_name_en').val(en.name || '');
                $('#warehouse_description_ar').val(ar.description || '');
                $('#warehouse_description_en').val(en.description || '');
                $(`input[name="status"][value="${warehouse.status}"]`).prop('checked', true);

                updateTypeFields();
                offcanvas.show();
            },
            error: function () {
                alertBox('danger', @json(__('Something went wrong. Please try again.')));
            }
        });
    });

    // Toggle Status
    $(document).on('click', '.btn-toggle-status', function () {
        const button = $(this);
        button.prop('disabled', true);

        $.ajax({
            url: baseUrl + '/' + button.data('id') + '/toggle-status',
            type: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function (response) {
                table.ajax.reload(null, false);
                alertBox('success', response.message);
            },
            error: function () {
                button.prop('disabled', false);
                alertBox('danger', @json(__('Something went wrong. Please try again.')));
            }
        });
    });

    // Delete Modal
    $(document).on('click', '.btn-delete-warehouse', function () {
        const id = $(this).data('id');
        const name = $(this).data('name');
        $('#deleteWarehouseForm').attr('action', baseUrl + '/' + id);
        $('#deleteWarehouseName').text(name);
        deleteModal.show();
    });

    // Confirm Delete (AJAX)
    $('#deleteWarehouseForm').on('submit', function (event) {
        event.preventDefault();
        const btnDelete = $('#btnConfirmDelete');
        btnDelete.prop('disabled', true);

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            success: function (response) {
                btnDelete.prop('disabled', false);
                deleteModal.hide();
                table.ajax.reload(null, false);
                alertBox('success', response.message || @json(__('Warehouse deleted successfully.')));
            },
            error: function (xhr) {
                btnDelete.prop('disabled', false);
                const message = (xhr.responseJSON && xhr.responseJSON.message)
                    ? xhr.responseJSON.message
                    : @json(__('Something went wrong. Please try again.'));
                alertBox('danger', message);
            }
        });
    });

    // Initial setups
    updateTypeFields();
});
</script>
@endsection
