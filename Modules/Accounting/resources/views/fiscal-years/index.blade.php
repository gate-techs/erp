@extends('layouts/contentNavbarLayout')

@section('title', __('Fiscal Years') . ' - ' . config('variables.templateName'))

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <i class="icon-base bx bx-calendar text-primary font-24"></i>
            <span>{{ __('Fiscal Years') }}</span>
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('Accounting') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Fiscal Years') }}</li>
            </ol>
        </nav>
    </div>

    <div>
        <!-- Button that opens the Side Offcanvas matching the theme design -->
        <button type="button" class="btn btn-primary d-flex align-items-center justify-content-center gap-2 shadow-sm" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddFiscalYear" aria-controls="offcanvasAddFiscalYear">
            <i class="icon-base bx bx-plus font-18"></i>
            <span>{{ __('Add New Fiscal Year') }}</span>
        </button>
    </div>
</div>

<!-- Alert Notifications Container -->
<div id="alertContainer"></div>

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

<!-- Standard Filters Card -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body py-3">
        <div class="row g-3 align-items-end">
            <!-- 1. Entity Filter -->
            <div class="col-12 col-sm-6 col-lg-4">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Accounting Entity') }}</label>
                <select id="filter_entity_id" class="select2 form-select" data-placeholder="{{ __('All Entities') }}" data-allow-clear="true">
                    <option value="">{{ __('All Entities') }}</option>
                    @foreach ($entities as $entity)
                        <option value="{{ $entity->id }}">
                            {{ $entity->translate(app()->getLocale())?->name ?? $entity->translate('ar')?->name ?? $entity->code }} ({{ $entity->code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- 2. Status Filter -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Status') }}</label>
                <select id="filter_status" class="select2 form-select" data-placeholder="{{ __('All Statuses') }}" data-allow-clear="true">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="open">{{ __('Open') }}</option>
                    <option value="closed">{{ __('Closed') }}</option>
                </select>
            </div>

            <!-- 3. Year Filter -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Year') }}</label>
                <select id="filter_year" class="select2 form-select" data-placeholder="{{ __('All Years') }}" data-allow-clear="true">
                    <option value="">{{ __('All Years') }}</option>
                    @for($y = (int) date('Y') + 2; $y >= (int) date('Y') - 5; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <!-- 4. Reset Filters Action Button -->
            <div class="col-12 col-sm-6 col-lg-2 d-flex align-items-end">
                <button type="button" id="btnResetFilters" class="btn btn-label-secondary btn-sm w-100 d-flex align-items-center justify-content-center gap-1 shadow-xs" style="height: 38px;">
                    <i class="icon-base bx bx-reset font-18"></i>
                    <span class="fw-semibold">{{ __('Reset Filters') }}</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Fiscal Years DataTable Card -->
<div class="card shadow-sm border-0">
    <div class="card-datatable table-responsive">
        <table id="fiscalYearsTable" class="table table-hover border-top w-100 text-nowrap">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">#</th>
                    <th>{{ __('Fiscal Year') }}</th>
                    <th>{{ __('Accounting Entity') }}</th>
                    <th class="text-center">{{ __('Start Date') }}</th>
                    <th class="text-center">{{ __('End Date') }}</th>
                    <th class="text-center" style="width: 100px;">{{ __('Duration') }}</th>
                    <th class="text-center" style="width: 110px;">{{ __('Status') }}</th>
                    <th class="text-center" style="width: 120px;">{{ __('Created Date') }}</th>
                    <th class="text-center" style="width: 120px;">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                <!-- Loaded via Server-Side DataTables -->
            </tbody>
        </table>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 1. Offcanvas: Add New Fiscal Year                                         -->
<!-- ========================================================================= -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="offcanvasAddFiscalYear" aria-labelledby="offcanvasAddFiscalYearLabel" style="width: 500px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3 px-4">
        <h5 id="offcanvasAddFiscalYearLabel" class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
            <i class="icon-base bx bx-calendar-plus text-primary font-22"></i>
            <span>{{ __('Add New Fiscal Year') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-4">
        <!-- Quick Preset helper buttons -->
        <div class="mb-3 p-3 bg-label-primary rounded border border-primary border-opacity-25">
            <label class="form-label fw-bold text-heading d-flex align-items-center gap-1 mb-2">
                <i class="icon-base bx bx-bulb text-warning font-18"></i>
                <span>{{ __('Quick Presets') }} ({{ __('Full Calendar Year') }})</span>
            </label>
            <div class="d-flex flex-wrap gap-2">
                @php $curY = (int) date('Y'); @endphp
                <button type="button" class="btn btn-xs btn-outline-primary btn-preset-year" data-year="{{ $curY - 1 }}">{{ $curY - 1 }}</button>
                <button type="button" class="btn btn-xs btn-primary btn-preset-year" data-year="{{ $curY }}">{{ $curY }} ({{ __('Current') }})</button>
                <button type="button" class="btn btn-xs btn-outline-primary btn-preset-year" data-year="{{ $curY + 1 }}">{{ $curY + 1 }}</button>
                <button type="button" class="btn btn-xs btn-outline-primary btn-preset-year" data-year="{{ $curY + 2 }}">{{ $curY + 2 }}</button>
            </div>
        </div>

        <form action="{{ route('accounting.fiscal-years.store') }}" method="POST" id="addFiscalYearForm">
            @csrf

            <!-- Entity Selection -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Accounting Entity') }} <span class="text-danger">*</span></label>
                <select name="entity_id" id="add_entity_id" class="select2 form-select" data-placeholder="{{ __('Select Entity') }}" required>
                    <option value="">{{ __('Select Entity') }}</option>
                    @foreach ($entities as $entity)
                        <option value="{{ $entity->id }}">
                            {{ $entity->translate(app()->getLocale())?->name ?? $entity->translate('ar')?->name ?? $entity->code }} ({{ $entity->code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Fiscal Year Name (Arabic) -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Fiscal Year Name (Arabic)') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_ar" id="add_name_ar" class="form-control" placeholder="{{ __('e.g. Fiscal Year 2026') }}" required />
            </div>

            <!-- Fiscal Year Name (English) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Fiscal Year Name (English)') }}</label>
                <input type="text" name="name_en" id="add_name_en" class="form-control" placeholder="e.g. Fiscal Year 2026" />
            </div>

            <!-- Date Range (Start & End Date) -->
            <div class="row g-2 mb-3">
                <div class="col-6">
                    <label class="form-label required fw-semibold text-heading">{{ __('Start Date') }} <span class="text-danger">*</span></label>
                    <input type="date" name="start_date" id="add_start_date" class="form-control" required />
                </div>
                <div class="col-6">
                    <label class="form-label required fw-semibold text-heading">{{ __('End Date') }} <span class="text-danger">*</span></label>
                    <input type="date" name="end_date" id="add_end_date" class="form-control" required />
                </div>
            </div>

            <!-- Description (Arabic) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Description (Arabic)') }}</label>
                <textarea name="description_ar" id="add_description_ar" class="form-control" rows="2" placeholder="{{ __('Description (Arabic)') }}"></textarea>
            </div>

            <!-- Description (English) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Description (English)') }}</label>
                <textarea name="description_en" id="add_description_en" class="form-control" rows="2" placeholder="{{ __('Description (English)') }}"></textarea>
            </div>

            <!-- Footer Buttons -->
            <div class="d-flex align-items-center justify-content-end gap-2 pt-3 border-top">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm px-4" id="btnSubmitAdd">
                    <i class="icon-base bx bx-save"></i>
                    <span>{{ __('Save') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 2. Offcanvas: Edit Fiscal Year                                            -->
<!-- ========================================================================= -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="offcanvasEditFiscalYear" aria-labelledby="offcanvasEditFiscalYearLabel" style="width: 500px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3 px-4">
        <h5 id="offcanvasEditFiscalYearLabel" class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
            <i class="icon-base bx bx-edit text-info font-22"></i>
            <span>{{ __('Edit Fiscal Year') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-4">
        <form action="" method="POST" id="editFiscalYearForm">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit_fiscal_year_id" name="id" />

            <!-- Entity Selection -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Accounting Entity') }} <span class="text-danger">*</span></label>
                <select name="entity_id" id="edit_entity_id" class="select2 form-select" data-placeholder="{{ __('Select Entity') }}" required>
                    <option value="">{{ __('Select Entity') }}</option>
                    @foreach ($entities as $entity)
                        <option value="{{ $entity->id }}">
                            {{ $entity->translate(app()->getLocale())?->name ?? $entity->translate('ar')?->name ?? $entity->code }} ({{ $entity->code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Fiscal Year Name (Arabic) -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Fiscal Year Name (Arabic)') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_ar" id="edit_name_ar" class="form-control" required />
            </div>

            <!-- Fiscal Year Name (English) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Fiscal Year Name (English)') }}</label>
                <input type="text" name="name_en" id="edit_name_en" class="form-control" />
            </div>

            <!-- Date Range (Start & End Date) -->
            <div class="row g-2 mb-3">
                <div class="col-6">
                    <label class="form-label required fw-semibold text-heading">{{ __('Start Date') }} <span class="text-danger">*</span></label>
                    <input type="date" name="start_date" id="edit_start_date" class="form-control" required />
                </div>
                <div class="col-6">
                    <label class="form-label required fw-semibold text-heading">{{ __('End Date') }} <span class="text-danger">*</span></label>
                    <input type="date" name="end_date" id="edit_end_date" class="form-control" required />
                </div>
            </div>

            <!-- Description (Arabic) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Description (Arabic)') }}</label>
                <textarea name="description_ar" id="edit_description_ar" class="form-control" rows="2"></textarea>
            </div>

            <!-- Description (English) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Description (English)') }}</label>
                <textarea name="description_en" id="edit_description_en" class="form-control" rows="2"></textarea>
            </div>

            <!-- Footer Buttons -->
            <div class="d-flex align-items-center justify-content-end gap-2 pt-3 border-top">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm px-4" id="btnSubmitEdit">
                    <i class="icon-base bx bx-save"></i>
                    <span>{{ __('Save Changes') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 3. Modal: Delete Fiscal Year Confirmation                                 -->
<!-- ========================================================================= -->
<div class="modal fade" id="deleteFiscalYearModal" tabindex="-1" aria-labelledby="deleteFiscalYearModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <form id="deleteFiscalYearForm" method="POST" class="modal-content">
            @csrf
            @method('DELETE')
            <div class="modal-body text-center py-4">
                <div class="avatar avatar-xl bg-label-danger mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-trash font-32 text-danger"></i>
                </div>
                <h5 class="fw-bold mb-2">{{ __('Confirm Delete') }}</h5>
                <p class="text-muted mb-0">
                    {{ __('Are you sure you want to delete:') }}
                    <br>
                    <strong id="delete_target_name" class="text-danger"></strong>؟
                </p>
            </div>
            <div class="modal-footer justify-content-center border-0 pt-0 pb-4">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-danger" id="btnConfirmDelete">{{ __('Yes, Delete') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const $ = window.jQuery || window.$;
    const dataUrl = "{{ route('accounting.fiscal-years.data') }}";
    const baseUrl = "{{ url('accounting/fiscal-years') }}";

    // 1. Initialize Server-Side DataTable
    const table = $('#fiscalYearsTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        pagingType: 'simple_numbers',
        ajax: {
            url: dataUrl,
            data: function (d) {
                d.entity_id = $('#filter_entity_id').val();
                d.status = $('#filter_status').val();
                d.start_year = $('#filter_year').val();
            }
        },
        columns: [
            {
                data: 'DT_RowIndex',
                name: 'id',
                orderable: false,
                searchable: false,
                className: 'text-center align-middle',
                render: (data, type, row, meta) => `<strong class="text-muted">#${data || (meta.row + 1 + (meta.settings._iDisplayStart || 0))}</strong>`
            },
            {
                data: 'fiscal_year_info',
                name: 'start_date',
                className: 'align-middle',
                render: (data, type, row) => data
            },
            {
                data: 'entity_info',
                name: 'entity_id',
                className: 'align-middle',
                render: (data, type, row) => data
            },
            {
                data: 'start_date',
                name: 'start_date',
                className: 'text-center align-middle',
                render: data => `<span class="fw-semibold">${data || '-'}</span>`
            },
            {
                data: 'end_date',
                name: 'end_date',
                className: 'text-center align-middle',
                render: data => `<span class="fw-semibold">${data || '-'}</span>`
            },
            {
                data: 'duration_info',
                name: 'duration_info',
                orderable: false,
                searchable: false,
                className: 'text-center align-middle',
                render: (data, type, row) => data
            },
            {
                data: 'status_badge',
                name: 'status',
                className: 'text-center align-middle',
                render: (data, type, row) => data
            },
            {
                data: 'created_at',
                name: 'created_at',
                className: 'text-center align-middle',
                render: data => `<span class="text-muted">${data || '-'}</span>`
            },
            {
                data: 'actions',
                name: 'actions',
                orderable: false,
                searchable: false,
                className: 'text-center align-middle',
                render: (data, type, row) => data
            }
        ],
        order: [[3, 'desc']],
        drawCallback: function () {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('#fiscalYearsTable [data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    });

    // 2. Custom Filters Trigger Reload
    let isResetting = false;
    $('#filter_entity_id, #filter_status, #filter_year').on('change', function () {
        if (isResetting) return;
        table.ajax.reload();
    });

    // Initialize Select2 with universal Sneat Bootstrap-5 theme matching project standards
    function setupSelect2(container = document) {
        const isRtl = $('html').attr('dir') === 'rtl' || $('html').attr('lang') === 'ar';
        const noResultsText = isRtl ? 'لا توجد نتائج مطابقة' : 'No results found';
        const searchingText = isRtl ? 'جاري البحث...' : 'Searching...';

        if (typeof window.initSelect2 === 'function') {
            window.initSelect2(container);
        } else if ($.fn.select2) {
            $(container).find('.select2, [data-control="select2"]').each(function () {
                if (!$(this).hasClass('select2-hidden-accessible')) {
                    $(this).select2({
                        theme: 'bootstrap-5',
                        placeholder: $(this).data('placeholder') || '',
                        allowClear: $(this).data('allow-clear') !== false,
                        dir: isRtl ? 'rtl' : 'ltr',
                        width: '100%',
                        dropdownParent: $(this).closest('.modal, .offcanvas').length ? $(this).closest('.modal, .offcanvas') : $(document.body),
                        language: {
                            noResults: () => noResultsText,
                            searching: () => searchingText
                        }
                    });
                }
            });
        }
    }

    setupSelect2();

    // 3. Reset Filters Handler
    $('#btnResetFilters').on('click', function () {
        isResetting = true;
        $('#filter_entity_id, #filter_status, #filter_year').val('').trigger('change');
        isResetting = false;
        table.ajax.reload();
    });

    // Helper for displaying alert message
    function showAlert(type, message) {
        const icon = type === 'success' ? 'bx-check-circle' : 'bx-error-circle';
        const html = `
            <div class="alert alert-${type} alert-dismissible d-flex align-items-center mb-4" role="alert">
                <i class="icon-base bx ${icon} me-2 font-20"></i>
                <div>${message}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        $('#alertContainer').html(html);
        $('html, body').animate({ scrollTop: $('#alertContainer').offset().top - 80 }, 300);
    }

    // 4. Quick Preset Year Click
    $(document).on('click', '.btn-preset-year', function () {
        const year = $(this).data('year');
        $('#add_start_date').val(`${year}-01-01`);
        $('#add_end_date').val(`${year}-12-31`);
        if (!$('#add_name_ar').val() || $('#add_name_ar').val().startsWith('السنة المالية')) {
            $('#add_name_ar').val(`السنة المالية ${year}`);
        }
        if (!$('#add_name_en').val() || $('#add_name_en').val().startsWith('Fiscal Year')) {
            $('#add_name_en').val(`Fiscal Year ${year}`);
        }
    });

    // 5. AJAX Submission: Add Fiscal Year
    const addOffcanvasEl = document.getElementById('offcanvasAddFiscalYear');
    const addOffcanvas = addOffcanvasEl ? bootstrap.Offcanvas.getOrCreateInstance(addOffcanvasEl) : null;
    const addForm = $('#addFiscalYearForm');

    addForm.on('submit', function (e) {
        e.preventDefault();
        const submitBtn = $('#btnSubmitAdd');
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> ' + "{{ __('Saving...') }}");

        $.ajax({
            url: addForm.attr('action'),
            method: 'POST',
            data: addForm.serialize(),
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function (res) {
                if (addOffcanvas) addOffcanvas.hide();
                addForm[0].reset();
                $('#add_entity_id').val('').trigger('change');
                table.ajax.reload();
                showAlert('success', res.message || "{{ __('Fiscal year created successfully.') }}");
            },
            error: function (xhr) {
                let errorMsg = "{{ __('An error occurred while saving.') }}";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const firstKey = Object.keys(xhr.responseJSON.errors)[0];
                    errorMsg = xhr.responseJSON.errors[firstKey][0];
                }
                alert(errorMsg);
            },
            complete: function () {
                submitBtn.prop('disabled', false).html('<i class="icon-base bx bx-save me-1"></i> ' + "{{ __('Save') }}");
            }
        });
    });

    // 6. Open Edit Offcanvas & Load Data
    const editOffcanvasEl = document.getElementById('offcanvasEditFiscalYear');
    const editOffcanvas = editOffcanvasEl ? bootstrap.Offcanvas.getOrCreateInstance(editOffcanvasEl) : null;

    $(document).on('click', '.btn-edit-fiscal-year', function () {
        const id = $(this).data('id');
        const fetchUrl = baseUrl + '/' + id;

        $.ajax({
            url: fetchUrl,
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function (res) {
                if (!res.success) return;
                const d = res.data;

                $('#edit_fiscal_year_id').val(d.id);
                $('#editFiscalYearForm').attr('action', baseUrl + '/' + d.id);
                $('#edit_name_ar').val(d.name_ar);
                $('#edit_name_en').val(d.name_en);
                $('#edit_start_date').val(d.start_date);
                $('#edit_end_date').val(d.end_date);
                $('#edit_description_ar').val(d.description_ar);
                $('#edit_description_en').val(d.description_en);
                $('#edit_entity_id').val(d.entity_id).trigger('change');

                if (editOffcanvas) editOffcanvas.show();
            },
            error: function () {
                alert("{{ __('Failed to load fiscal year details.') }}");
            }
        });
    });

    // 7. AJAX Submission: Edit Fiscal Year
    const editForm = $('#editFiscalYearForm');
    editForm.on('submit', function (e) {
        e.preventDefault();
        const submitBtn = $('#btnSubmitEdit');
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> ' + "{{ __('Saving...') }}");

        $.ajax({
            url: editForm.attr('action'),
            method: 'POST',
            data: editForm.serialize(),
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function (res) {
                if (editOffcanvas) editOffcanvas.hide();
                table.ajax.reload();
                showAlert('success', res.message || "{{ __('Fiscal year updated successfully.') }}");
            },
            error: function (xhr) {
                let errorMsg = "{{ __('An error occurred while saving.') }}";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const firstKey = Object.keys(xhr.responseJSON.errors)[0];
                    errorMsg = xhr.responseJSON.errors[firstKey][0];
                }
                alert(errorMsg);
            },
            complete: function () {
                submitBtn.prop('disabled', false).html('<i class="icon-base bx bx-save me-1"></i> ' + "{{ __('Save Changes') }}");
            }
        });
    });

    // 8. Toggle Status (Lock/Unlock)
    $(document).on('click', '.btn-toggle-status', function () {
        const id = $(this).data('id');
        const status = $(this).data('status');
        const actionText = status === 'open' ? "{{ __('Are you sure you want to close this fiscal year?') }}" : "{{ __('Are you sure you want to reopen this fiscal year?') }}";

        if (!confirm(actionText)) return;

        $.ajax({
            url: baseUrl + '/' + id + '/toggle-status',
            method: 'POST',
            data: {
                _token: "{{ csrf_token() }}"
            },
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function (res) {
                table.ajax.reload();
                showAlert('success', res.message || "{{ __('Status updated successfully.') }}");
            },
            error: function (xhr) {
                let errorMsg = "{{ __('Failed to change status.') }}";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                alert(errorMsg);
            }
        });
    });

    // 9. Delete Confirmation Modal
    const deleteModalEl = document.getElementById('deleteFiscalYearModal');
    const deleteModal = deleteModalEl ? bootstrap.Modal.getOrCreateInstance(deleteModalEl) : null;
    const deleteForm = $('#deleteFiscalYearForm');

    $(document).on('click', '.btn-delete-fiscal-year', function () {
        const id = $(this).data('id');
        const name = $(this).data('name');

        deleteForm.attr('action', baseUrl + '/' + id);
        $('#delete_target_name').text(name);

        if (deleteModal) deleteModal.show();
    });

    deleteForm.on('submit', function (e) {
        e.preventDefault();
        const submitBtn = $('#btnConfirmDelete');
        submitBtn.prop('disabled', true);

        $.ajax({
            url: deleteForm.attr('action'),
            method: 'POST',
            data: deleteForm.serialize(),
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function (res) {
                if (deleteModal) deleteModal.hide();
                table.ajax.reload();
                showAlert('success', res.message || "{{ __('Fiscal year deleted successfully.') }}");
            },
            error: function (xhr) {
                let errorMsg = "{{ __('Failed to delete fiscal year.') }}";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                alert(errorMsg);
            },
            complete: function () {
                submitBtn.prop('disabled', false);
            }
        });
    });
});
</script>
@endsection
