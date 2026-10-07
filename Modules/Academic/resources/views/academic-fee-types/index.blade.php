@extends('layouts/contentNavbarLayout')

@section('title', __('Fee Types') . ' - ' . config('variables.templateName'))

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
    <div class="d-flex flex-column justify-content-center">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted fw-medium small">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item text-muted fw-medium small">{{ __('Fees & Packages') }}</li>
                <li class="breadcrumb-item active text-primary fw-medium small" aria-current="page">{{ __('Fee Types') }}</li>
            </ol>
        </nav>
        <!-- Title -->
        <div class="d-flex align-items-center gap-2 mt-1">
            <i class="icon-base bx bx-category text-primary font-24"></i>
            <h4 class="mb-0 fw-bold text-heading">{{ __('Fee Types') }}</h4>
        </div>
    </div>

    <div class="mt-3 mt-md-0 d-flex align-items-center gap-2">
        <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-2 shadow-sm" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddFeeType" aria-controls="offcanvasAddFeeType">
            <i class="icon-base bx bx-plus font-18"></i>
            <span>{{ __('Add Fee Type') }}</span>
        </button>
    </div>
</div>

<!-- Filters Card (3 per row) -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body py-3">
        <div class="row g-3 align-items-end">
            <!-- 1. Institution Filter -->
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Educational Institution / Nursery') }}</label>
                <select id="filter_institution_id" class="select2 select2-selection__rendered form-select" data-allow-clear="true">
                    <option value="">{{ __('All Institutions') }}</option>
                    @foreach ($institutions as $inst)
                        <option value="{{ $inst->id }}">{{ $inst->translate(app()->getLocale())?->name ?? $inst->translate('ar')?->name ?? $inst->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 2. Branch Filter -->
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Branch') }}</label>
                <select id="filter_branch_id" class="select2 select2-selection__rendered form-select" data-allow-clear="true">
                    <option value="">{{ __('All Branches') }}</option>
                    @foreach ($branches as $br)
                        <option value="{{ $br->id }}">{{ $br->translate(app()->getLocale())?->name ?? $br->translate('ar')?->name ?? $br->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 3. Status Filter & Reset Button -->
            <div class="col-12 col-md-4">
                <div class="row g-2">
                    <div class="col-8">
                        <label class="form-label fw-semibold text-heading small mb-1">{{ __('Status') }}</label>
                        <select id="filter_status" class="select2 select2-selection__rendered form-select" data-allow-clear="true">
                            <option value="">{{ __('All Statuses') }}</option>
                            <option value="active">{{ __('Active') }}</option>
                            <option value="inactive">{{ __('Inactive') }}</option>
                        </select>
                    </div>
                    <div class="col-4">
                        <label class="form-label d-none d-md-block">&nbsp;</label>
                        <button type="button" id="btn_reset_filters" class="btn btn-label-secondary btn-sm w-100 d-flex align-items-center justify-content-center gap-1">
                            <i class="icon-base bx bx-reset font-18"></i>
                            <span>{{ __('Reset') }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Dynamic Alert Container -->
<div id="ajaxAlertContainer"></div>

<!-- Fee Types DataTable Card -->
<div class="card shadow-sm border-0">
    <div class="card-datatable table-responsive">
        <table id="feeTypesTable" class="table table-hover border-top w-100 text-nowrap">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">#</th>
                    <th>{{ __('Fee Type Name') }}</th>
                    <th>{{ __('Institution') }}</th>
                    <th>{{ __('Branch') }}</th>
                    <th class="text-center" style="width: 80px;">{{ __('Order') }}</th>
                    <th class="text-center" style="width: 100px;">{{ __('Status') }}</th>
                    <th class="text-center" style="width: 120px;">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 1. Offcanvas: Add Fee Type                                                -->
<!-- ========================================================================= -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="offcanvasAddFeeType" aria-labelledby="offcanvasAddFeeTypeLabel" style="width: 460px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3">
        <h5 id="offcanvasAddFeeTypeLabel" class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
            <i class="icon-base bx bx-plus-circle text-primary font-22"></i>
            <span>{{ __('Add Fee Type') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-4">
        <form id="formAddFeeType">
            @csrf

            <!-- 1. Institution (Single row) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Educational Institution / Nursery') }}</label>
                <select name="institution_id" id="add_institution_id" class="select2 select2-selection__rendered form-select">
                    <option value="">{{ __('All Institutions') }}</option>
                    @foreach ($institutions as $inst)
                        <option value="{{ $inst->id }}">{{ $inst->translate(app()->getLocale())?->name ?? $inst->translate('ar')?->name ?? $inst->name }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block" id="formAddFeeType_institution_id_error"></div>
            </div>

            <!-- 2. Branch (Single row) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Branch') }}</label>
                <select name="branch_id" id="add_branch_id" class="select2 select2-selection__rendered form-select">
                    <option value="">{{ __('All Branches') }}</option>
                </select>
                <div class="invalid-feedback d-block" id="formAddFeeType_branch_id_error"></div>
            </div>

            <!-- 3. Name Arabic (Single row) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Fee Type Name [Arabic]') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_ar" id="add_name_ar" class="form-control" required dir="rtl">
                <div class="invalid-feedback d-block" id="formAddFeeType_name_ar_error"></div>
            </div>

            <!-- 4. Name English (Single row) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Fee Type Name [English]') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_en" id="add_name_en" class="form-control" required dir="ltr">
                <div class="invalid-feedback d-block" id="formAddFeeType_name_en_error"></div>
            </div>

            <!-- 5. Order (Single row) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Order') }}</label>
                <input type="number" name="order" id="add_order" class="form-control" value="0" min="0">
                <div class="invalid-feedback d-block" id="formAddFeeType_order_error"></div>
            </div>

            <!-- 6. Status (Single row) -->
            <div class="mb-4">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Status') }} <span class="text-danger">*</span></label>
                <select name="status" id="add_status" class="select2 select2-selection__rendered form-select" required>
                    <option value="active">{{ __('Active') }}</option>
                    <option value="inactive">{{ __('Inactive') }}</option>
                </select>
                <div class="invalid-feedback d-block" id="formAddFeeType_status_error"></div>
            </div>

            <!-- Form Actions -->
            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                <button type="submit" id="btn_submit_add" class="btn btn-primary d-flex align-items-center gap-1">
                    <i class="icon-base bx bx-check font-18"></i>
                    <span>{{ __('Save') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 2. Offcanvas: Edit Fee Type                                               -->
<!-- ========================================================================= -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="offcanvasEditFeeType" aria-labelledby="offcanvasEditFeeTypeLabel" style="width: 460px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3">
        <h5 id="offcanvasEditFeeTypeLabel" class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
            <i class="icon-base bx bx-edit text-primary font-22"></i>
            <span>{{ __('Edit Fee Type') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-4">
        <form id="formEditFeeType">
            @csrf
            @method('PUT')
            <input type="hidden" name="type_id" id="edit_type_id">

            <!-- 1. Institution (Single row) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Educational Institution / Nursery') }}</label>
                <select name="institution_id" id="edit_institution_id" class="select2 select2-selection__rendered form-select">
                    <option value="">{{ __('All Institutions') }}</option>
                    @foreach ($institutions as $inst)
                        <option value="{{ $inst->id }}">{{ $inst->translate(app()->getLocale())?->name ?? $inst->translate('ar')?->name ?? $inst->name }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block" id="formEditFeeType_institution_id_error"></div>
            </div>

            <!-- 2. Branch (Single row) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Branch') }}</label>
                <select name="branch_id" id="edit_branch_id" class="select2 select2-selection__rendered form-select">
                    <option value="">{{ __('All Branches') }}</option>
                    @foreach ($branches as $br)
                        <option value="{{ $br->id }}">{{ $br->translate(app()->getLocale())?->name ?? $br->translate('ar')?->name ?? $br->name }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block" id="formEditFeeType_branch_id_error"></div>
            </div>

            <!-- 3. Name Arabic (Single row) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Fee Type Name [Arabic]') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_ar" id="edit_name_ar" class="form-control" required dir="rtl">
                <div class="invalid-feedback d-block" id="formEditFeeType_name_ar_error"></div>
            </div>

            <!-- 4. Name English (Single row) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Fee Type Name [English]') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_en" id="edit_name_en" class="form-control" required dir="ltr">
                <div class="invalid-feedback d-block" id="formEditFeeType_name_en_error"></div>
            </div>

            <!-- 5. Order (Single row) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Order') }}</label>
                <input type="number" name="order" id="edit_order" class="form-control" min="0">
                <div class="invalid-feedback d-block" id="formEditFeeType_order_error"></div>
            </div>

            <!-- 6. Status (Single row) -->
            <div class="mb-4">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Status') }} <span class="text-danger">*</span></label>
                <select name="status" id="edit_status" class="select2 select2-selection__rendered form-select" required>
                    <option value="active">{{ __('Active') }}</option>
                    <option value="inactive">{{ __('Inactive') }}</option>
                </select>
                <div class="invalid-feedback d-block" id="formEditFeeType_status_error"></div>
            </div>

            <!-- Form Actions -->
            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                <button type="submit" id="btn_submit_edit" class="btn btn-primary d-flex align-items-center gap-1">
                    <i class="icon-base bx bx-check font-18"></i>
                    <span>{{ __('Save Changes') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 3. Modal: View Fee Type Details                                           -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalViewFeeType" tabindex="-1" aria-labelledby="modalViewFeeTypeLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title fw-bold text-heading d-flex align-items-center gap-2 mb-0" id="modalViewFeeTypeLabel">
                    <i class="icon-base bx bx-category text-primary font-22"></i>
                    <span id="view_type_title">{{ __('Fee Type Details') }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <h5 class="fw-bold text-primary mb-1" id="view_name_ar">-</h5>
                    <h6 class="text-muted mb-0" id="view_name_en">-</h6>
                </div>

                <div class="p-3 bg-light rounded">
                    <div class="row g-2">
                        <div class="col-6"><small class="text-muted d-block">{{ __('Institution') }}:</small> <span id="view_inst_name" class="fw-semibold text-heading small">-</span></div>
                        <div class="col-6"><small class="text-muted d-block">{{ __('Branch') }}:</small> <span id="view_branch_name" class="fw-semibold text-heading small">-</span></div>
                        <div class="col-6"><small class="text-muted d-block">{{ __('Order') }}:</small> <span id="view_order" class="badge bg-label-secondary">-</span></div>
                        <div class="col-6"><small class="text-muted d-block">{{ __('Status') }}:</small> <span id="view_type_status" class="badge bg-label-success">-</span></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top py-2">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 4. Modal: Delete Fee Type Confirmation                                    -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalDeleteFeeType" tabindex="-1" aria-labelledby="modalDeleteFeeTypeLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-body text-center p-4">
                <div class="avatar avatar-xl bg-label-danger mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                    <i class="icon-base bx bx-trash font-28 text-danger"></i>
                </div>
                <h5 class="fw-bold mb-2">{{ __('Delete Fee Type') }}</h5>
                <p class="text-muted small mb-4">{{ __('Are you sure you want to delete') }} <strong id="delete_type_name" class="text-heading"></strong>? {{ __('This action cannot be undone.') }}</p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="button" id="btn_confirm_delete" class="btn btn-danger">{{ __('Delete') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
(function () {
    function initFeeTypes() {
        const $ = window.jQuery || window.$;
        if (!$ || !$.fn.DataTable) {
            setTimeout(initFeeTypes, 50);
            return;
        }

        const baseUrl = "{{ url('academic/fee-types') }}";
        const dataUrl = "{{ route('academic.fee-types.data') }}";

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            }
        });

        function showAlert(type, message) {
            const icon = type === 'success' ? 'bx-check-circle' : 'bx-error-circle';
            const html = `
                <div class="alert alert-${type} alert-dismissible d-flex align-items-center mb-4 fade show" role="alert">
                    <i class="icon-base bx ${icon} me-2 font-20"></i>
                    <div>${message}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            $('#ajaxAlertContainer').html(html);
            window.scrollTo({ top: 0, behavior: 'smooth' });
            setTimeout(() => {
                $('#ajaxAlertContainer .alert').alert('close');
            }, 5000);
        }

        function clearFormErrors(formId) {
            $(`#${formId} .is-invalid`).removeClass('is-invalid');
            $(`#${formId} .invalid-feedback`).text('');
        }

        function showFormErrors(formId, errors) {
            clearFormErrors(formId);
            for (const field in errors) {
                const cleanField = field.replace(/\.\d+/g, '');
                const input = $(`#${formId} [name="${field}"], #${formId} [name="${cleanField}"]`);
                input.addClass('is-invalid');
                const errorDiv = $(`#${formId}_${cleanField}_error`);
                if (errorDiv.length) {
                    errorDiv.text(errors[field][0]);
                } else {
                    input.closest('.mb-3, .mb-4').find('.invalid-feedback').text(errors[field][0]);
                }
            }
        }

        function applySelect2(context) {
            const isRtl = $('html').attr('dir') === 'rtl' || $('html').attr('lang') === 'ar';
            const target = context ? $(context).find('select.select2, select.select2-selection__rendered') : $('select.select2, select.select2-selection__rendered');
            target.each(function () {
                const $this = $(this);
                if ($this.hasClass('select2-hidden-accessible')) {
                    $this.select2('destroy');
                }
                const $parent = $this.closest('.offcanvas, .modal');
                $this.select2({
                    theme: 'bootstrap-5',
                    dropdownParent: $parent.length ? $parent : null,
                    placeholder: '',
                    allowClear: $this.data('allow-clear') !== false,
                    dir: isRtl ? 'rtl' : 'ltr',
                    width: '100%'
                });
            });
        }

        applySelect2();

        $('#offcanvasAddFeeType, #offcanvasEditFeeType').on('shown.bs.offcanvas', function () {
            applySelect2(this);
        });

        const table = $('#feeTypesTable').DataTable({
            processing: true,
            serverSide: true,
            pagingType: 'simple_numbers',
            language: {
                emptyTable: `
                    <div class="text-center py-5">
                        <div class="avatar avatar-xl bg-label-secondary mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                            <i class="icon-base bx bx-category font-32 text-secondary"></i>
                        </div>
                        <h6 class="fw-bold mb-1">{{ __('No fee types found') }}</h6>
                        <p class="text-muted small mb-0">{{ __('Click "Add Fee Type" to create the first type.') }}</p>
                    </div>
                `
            },
            ajax: {
                url: dataUrl,
                data: function (d) {
                    d.institution_id = $('#filter_institution_id').val();
                    d.branch_id = $('#filter_branch_id').val();
                    d.status = $('#filter_status').val();
                }
            },
            columns: [
                { 
                    data: 'DT_RowIndex', 
                    name: 'DT_RowIndex', 
                    orderable: false, 
                    searchable: false, 
                    className: 'text-center',
                    render: (data, type, row, meta) => `<strong class="text-muted">#${data || (meta.row + 1 + (meta.settings._iDisplayStart || 0))}</strong>`
                },
                { data: 'type_info', name: 'translations.name', className: 'fw-semibold text-heading' },
                { data: 'institution_name', name: 'institution_id', defaultContent: '-' },
                { data: 'branch_name', name: 'branch_id', defaultContent: '-' },
                { data: 'order_badge', name: 'order', className: 'text-center' },
                { data: 'status_badge', name: 'status', className: 'text-center' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center' }
            ],
            order: [[4, 'asc']]
        });

        $('#filter_institution_id, #filter_branch_id, #filter_status').on('change', function () {
            table.ajax.reload();
        });

        $('#btn_reset_filters').on('click', function () {
            $('#filter_institution_id').val('').trigger('change');
            $('#filter_branch_id').val('').trigger('change');
            $('#filter_status').val('').trigger('change');
            table.ajax.reload();
        });

        // Submit Add Form
        $('#formAddFeeType').on('submit', function (e) {
            e.preventDefault();
            clearFormErrors('formAddFeeType');

            const btn = $('#btn_submit_add');
            btn.prop('disabled', true).addClass('disabled');

            $.ajax({
                url: baseUrl,
                type: 'POST',
                data: $(this).serialize(),
                success: function (res) {
                    btn.prop('disabled', false).removeClass('disabled');
                    const offcanvasEl = document.getElementById('offcanvasAddFeeType');
                    const offcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl) || new bootstrap.Offcanvas(offcanvasEl);
                    offcanvas.hide();

                    $('#formAddFeeType')[0].reset();
                    $('#formAddFeeType select').val('').trigger('change');
                    $('#add_status').val('active').trigger('change');

                    table.ajax.reload(null, false);
                    showAlert('success', res.message || "{{ __('Fee type created successfully.') }}");
                },
                error: function (xhr) {
                    btn.prop('disabled', false).removeClass('disabled');
                    if (xhr.status === 422) {
                        showFormErrors('formAddFeeType', xhr.responseJSON.errors);
                    } else {
                        showAlert('danger', xhr.responseJSON?.message || "{{ __('An error occurred while creating fee type.') }}");
                    }
                }
            });
        });

        // View Details Modal
        $(document).on('click', '.btn-view-academic-fee-type', function () {
            const id = $(this).data('id');
            $.get(`${baseUrl}/${id}`, function (res) {
                if (res.success) {
                    const data = res.data;
                    $('#view_type_title').text(data.name_ar || data.name_en || "{{ __('Fee Type Details') }}");
                    $('#view_name_ar').text(data.name_ar || '-');
                    $('#view_name_en').text(data.name_en || '-');
                    $('#view_order').text(data.order);
                    $('#view_inst_name').text(data.institution_name);
                    $('#view_branch_name').text(data.branch_name);

                    const statusBadge = data.status === 'active'
                        ? '<span class="badge bg-label-success">{{ __("Active") }}</span>'
                        : '<span class="badge bg-label-danger">{{ __("Inactive") }}</span>';
                    $('#view_type_status').html(statusBadge);

                    $('#modalViewFeeType').modal('show');
                }
            }).fail(function () {
                showAlert('danger', "{{ __('Failed to load fee type details.') }}");
            });
        });

        // Open Edit Offcanvas
        $(document).on('click', '.btn-edit-academic-fee-type', function () {
            const id = $(this).data('id');
            clearFormErrors('formEditFeeType');

            $.get(`${baseUrl}/${id}`, function (res) {
                if (res.success) {
                    const data = res.data;
                    $('#edit_type_id').val(data.id);
                    $('#edit_name_ar').val(data.name_ar);
                    $('#edit_name_en').val(data.name_en);
                    $('#edit_order').val(data.order);
                    $('#edit_status').val(data.status).trigger('change');
                    $('#edit_institution_id').val(data.institution_id).trigger('change');
                    $('#edit_branch_id').val(data.branch_id).trigger('change');

                    const offcanvasEl = document.getElementById('offcanvasEditFeeType');
                    const offcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl) || new bootstrap.Offcanvas(offcanvasEl);
                    offcanvas.show();
                }
            }).fail(function () {
                showAlert('danger', "{{ __('Failed to load fee type details for editing.') }}");
            });
        });

        // Submit Edit Form
        $('#formEditFeeType').on('submit', function (e) {
            e.preventDefault();
            clearFormErrors('formEditFeeType');

            const id = $('#edit_type_id').val();
            const btn = $('#btn_submit_edit');
            btn.prop('disabled', true).addClass('disabled');

            $.ajax({
                url: `${baseUrl}/${id}`,
                type: 'POST',
                data: $(this).serialize(),
                success: function (res) {
                    btn.prop('disabled', false).removeClass('disabled');
                    const offcanvasEl = document.getElementById('offcanvasEditFeeType');
                    const offcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl) || new bootstrap.Offcanvas(offcanvasEl);
                    offcanvas.hide();

                    table.ajax.reload(null, false);
                    showAlert('success', res.message || "{{ __('Fee type updated successfully.') }}");
                },
                error: function (xhr) {
                    btn.prop('disabled', false).removeClass('disabled');
                    if (xhr.status === 422) {
                        showFormErrors('formEditFeeType', xhr.responseJSON.errors);
                    } else {
                        showAlert('danger', xhr.responseJSON?.message || "{{ __('An error occurred while updating fee type.') }}");
                    }
                }
            });
        });

        // Toggle Status
        $(document).on('click', '.btn-toggle-academic-fee-type-status', function () {
            const id = $(this).data('id');
            const $btn = $(this);
            $btn.prop('disabled', true);

            $.ajax({
                url: `${baseUrl}/${id}/toggle-status`,
                type: 'PATCH',
                success: function (res) {
                    table.ajax.reload(null, false);
                    showAlert('success', res.message || "{{ __('Status updated successfully.') }}");
                },
                error: function () {
                    $btn.prop('disabled', false);
                    showAlert('danger', "{{ __('Failed to update status.') }}");
                }
            });
        });

        // Delete Trigger
        let typeIdToDelete = null;
        $(document).on('click', '.btn-delete-academic-fee-type', function () {
            typeIdToDelete = $(this).data('id');
            const typeName = $(this).data('name');
            $('#delete_type_name').text(typeName);
            $('#modalDeleteFeeType').modal('show');
        });

        // Confirm Delete
        $('#btn_confirm_delete').on('click', function () {
            if (!typeIdToDelete) return;

            const btn = $(this);
            btn.prop('disabled', true).addClass('disabled');

            $.ajax({
                url: `${baseUrl}/${typeIdToDelete}`,
                type: 'DELETE',
                success: function (res) {
                    btn.prop('disabled', false).removeClass('disabled');
                    $('#modalDeleteFeeType').modal('hide');
                    table.ajax.reload(null, false);
                    showAlert('success', res.message || "{{ __('Fee type deleted successfully.') }}");
                    typeIdToDelete = null;
                },
                error: function () {
                    btn.prop('disabled', false).removeClass('disabled');
                    showAlert('danger', "{{ __('Failed to delete fee type.') }}");
                }
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initFeeTypes);
    } else {
        initFeeTypes();
    }
})();
</script>
@endsection
