@extends('layouts/contentNavbarLayout')

@section('title', __('Academic Stages') . ' - ' . config('variables.templateName'))

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
    <div class="d-flex flex-column justify-content-center">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted fw-medium small">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item text-muted fw-medium small">{{ __('Academic Management') }}</li>
                <li class="breadcrumb-item active text-primary fw-medium small" aria-current="page">{{ __('Academic Stages') }}</li>
            </ol>
        </nav>
        <!-- Title -->
        <div class="d-flex align-items-center gap-2 mt-1">
            <i class="icon-base bx bx-layer text-primary font-24"></i>
            <h4 class="mb-0 fw-bold text-heading">{{ __('Academic Stages') }}</h4>
        </div>
    </div>

    <div class="mt-3 mt-md-0">
        <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-2 shadow-sm" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddAcademicStage" aria-controls="offcanvasAddAcademicStage">
            <i class="icon-base bx bx-plus font-18"></i>
            <span>{{ __('Add Academic Stage') }}</span>
        </button>
    </div>
</div>

<!-- Filters Card -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body py-3">
        <div class="row g-3 align-items-end">
            <!-- 1. Institution Filter -->
            <div class="col-12 col-md-5">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Educational Institution / Nursery') }}</label>
                <select id="filter_institution_id" class="select2 select2-selection__rendered form-select" data-placeholder="{{ __('All Institutions') }}" data-allow-clear="true">
                    <option value="">{{ __('All Institutions') }}</option>
                    @foreach ($institutions as $inst)
                        <option value="{{ $inst->id }}">{{ $inst->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 2. Status Filter -->
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Status') }}</label>
                <select id="filter_status" class="select2 select2-selection__rendered form-select" data-placeholder="{{ __('All Statuses') }}" data-allow-clear="true">
                    <option value="">{{ __('All Statuses') }}</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}">{{ __($status === 'active' ? 'Active' : 'Inactive') }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 3. Reset Button -->
            <div class="col-12 col-md-3">
                <button type="button" id="btn_reset_filters" class="btn btn-label-secondary w-100 d-flex align-items-center justify-content-center gap-1">
                    <i class="icon-base bx bx-reset font-18"></i>
                    <span>{{ __('Reset') }}</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Alert Box for Ajax feedback -->
<div id="ajaxAlertContainer"></div>

<!-- DataTables Card -->
<div class="card shadow-sm border-0">
    <div class="card-datatable table-responsive">
        <table id="academicStagesTable" class="table table-hover border-top w-100 text-nowrap">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">#</th>
                    <th>{{ __('Academic Stage') }}</th>
                    <th>{{ __('Institution') }}</th>
                    <th class="text-center" style="width: 120px;">{{ __('Stage Code') }}</th>
                    <th class="text-center" style="width: 140px;">{{ __('Age Range') }}</th>
                    <th class="text-center" style="width: 100px;">{{ __('Status') }}</th>
                    <th class="text-center" style="width: 130px;">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 1. Offcanvas: Add Academic Stage                                          -->
<!-- ========================================================================= -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="offcanvasAddAcademicStage" aria-labelledby="offcanvasAddAcademicStageLabel" style="width: 480px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3 px-4">
        <h5 id="offcanvasAddAcademicStageLabel" class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
            <i class="icon-base bx bx-layer-plus text-primary font-22"></i>
            <span>{{ __('Add Academic Stage') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-4">
        <form id="addAcademicStageForm" action="{{ route('academic.stages.store') }}" method="POST">
            @csrf

            <!-- Institution -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Educational Institution / Nursery') }} <span class="text-danger">*</span></label>
                <select name="institution_id" id="add_institution_id" class="select2 select2-selection__rendered form-select" required data-placeholder="{{ __('Select Institution...') }}" data-allow-clear="true">
                    <option value="">{{ __('Select Institution...') }}</option>
                    @foreach ($institutions as $inst)
                        <option value="{{ $inst->id }}">{{ $inst->name }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block" id="add_institution_id_error"></div>
            </div>

            <!-- Code -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Stage Code') }} <span class="text-danger">*</span></label>
                <input type="text" name="code" id="add_code" class="form-control" placeholder="KG1, STAGE-1" required maxlength="50" />
                <div class="invalid-feedback d-block" id="add_code_error"></div>
            </div>

            <!-- Arabic Name -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Stage Name (Arabic)') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_ar" id="add_name_ar" class="form-control" required maxlength="150" dir="rtl" />
                <div class="invalid-feedback d-block" id="add_name_ar_error"></div>
            </div>

            <!-- English Name -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Stage Name (English)') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_en" id="add_name_en" class="form-control" required maxlength="150" dir="ltr" />
                <div class="invalid-feedback d-block" id="add_name_en_error"></div>
            </div>

            <!-- Age Range (Min / Max Months) -->
            <div class="row g-3 mb-3">
                <div class="col-12 col-sm-6">
                    <label class="form-label fw-semibold text-heading">{{ __('Minimum Age (Months)') }}</label>
                    <input type="number" name="min_age_months" id="add_min_age_months" class="form-control" min="0" max="1200" placeholder="24" />
                    <div class="invalid-feedback d-block" id="add_min_age_months_error"></div>
                </div>
                <div class="col-12 col-sm-6">
                    <label class="form-label fw-semibold text-heading">{{ __('Maximum Age (Months)') }}</label>
                    <input type="number" name="max_age_months" id="add_max_age_months" class="form-control" min="0" max="1200" placeholder="48" />
                    <div class="invalid-feedback d-block" id="add_max_age_months_error"></div>
                </div>
            </div>

            <!-- Status -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Status') }}</label>
                <select name="status" id="add_status" class="select2 select2-selection__rendered form-select" data-placeholder="{{ __('Select Status') }}">
                    <option value="active" selected>{{ __('Active') }}</option>
                    <option value="inactive">{{ __('Inactive') }}</option>
                </select>
            </div>

            <!-- Description (Arabic & English) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Description (Arabic)') }}</label>
                <textarea name="description_ar" id="add_description_ar" class="form-control" rows="2" dir="rtl"></textarea>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold text-heading">{{ __('Description (English)') }}</label>
                <textarea name="description_en" id="add_description_en" class="form-control" rows="2" dir="ltr"></textarea>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex gap-2">
                <button type="submit" id="btn_submit_add" class="btn btn-primary flex-grow-1 d-flex align-items-center justify-content-center gap-1 shadow-sm">
                    <i class="icon-base bx bx-save"></i>
                    <span>{{ __('Save Academic Stage') }}</span>
                </button>
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 2. Offcanvas: Edit Academic Stage                                         -->
<!-- ========================================================================= -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="offcanvasEditAcademicStage" aria-labelledby="offcanvasEditAcademicStageLabel" style="width: 480px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3 px-4">
        <h5 id="offcanvasEditAcademicStageLabel" class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
            <i class="icon-base bx bx-edit text-primary font-22"></i>
            <span>{{ __('Edit Academic Stage') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-4">
        <form id="editAcademicStageForm" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit_id" name="id" />

            <!-- Institution -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Educational Institution / Nursery') }} <span class="text-danger">*</span></label>
                <select name="institution_id" id="edit_institution_id" class="select2 select2-selection__rendered form-select" required data-placeholder="{{ __('Select Institution...') }}" data-allow-clear="true">
                    @foreach ($institutions as $inst)
                        <option value="{{ $inst->id }}">{{ $inst->name }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block" id="edit_institution_id_error"></div>
            </div>

            <!-- Code -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Stage Code') }} <span class="text-danger">*</span></label>
                <input type="text" name="code" id="edit_code" class="form-control" required maxlength="50" />
                <div class="invalid-feedback d-block" id="edit_code_error"></div>
            </div>

            <!-- Arabic Name -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Stage Name (Arabic)') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_ar" id="edit_name_ar" class="form-control" required maxlength="150" dir="rtl" />
                <div class="invalid-feedback d-block" id="edit_name_ar_error"></div>
            </div>

            <!-- English Name -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Stage Name (English)') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_en" id="edit_name_en" class="form-control" required maxlength="150" dir="ltr" />
                <div class="invalid-feedback d-block" id="edit_name_en_error"></div>
            </div>

            <!-- Age Range (Min / Max Months) -->
            <div class="row g-3 mb-3">
                <div class="col-12 col-sm-6">
                    <label class="form-label fw-semibold text-heading">{{ __('Minimum Age (Months)') }}</label>
                    <input type="number" name="min_age_months" id="edit_min_age_months" class="form-control" min="0" max="1200" />
                    <div class="invalid-feedback d-block" id="edit_min_age_months_error"></div>
                </div>
                <div class="col-12 col-sm-6">
                    <label class="form-label fw-semibold text-heading">{{ __('Maximum Age (Months)') }}</label>
                    <input type="number" name="max_age_months" id="edit_max_age_months" class="form-control" min="0" max="1200" />
                    <div class="invalid-feedback d-block" id="edit_max_age_months_error"></div>
                </div>
            </div>

            <!-- Status -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Status') }}</label>
                <select name="status" id="edit_status" class="select2 select2-selection__rendered form-select" data-placeholder="{{ __('Select Status') }}">
                    <option value="active">{{ __('Active') }}</option>
                    <option value="inactive">{{ __('Inactive') }}</option>
                </select>
            </div>

            <!-- Description (Arabic & English) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Description (Arabic)') }}</label>
                <textarea name="description_ar" id="edit_description_ar" class="form-control" rows="2" dir="rtl"></textarea>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold text-heading">{{ __('Description (English)') }}</label>
                <textarea name="description_en" id="edit_description_en" class="form-control" rows="2" dir="ltr"></textarea>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex gap-2">
                <button type="submit" id="btn_submit_edit" class="btn btn-primary flex-grow-1 d-flex align-items-center justify-content-center gap-1 shadow-sm">
                    <i class="icon-base bx bx-save"></i>
                    <span>{{ __('Save Changes') }}</span>
                </button>
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 3. Modal: Delete Confirmation                                             -->
<!-- ========================================================================= -->
<div class="modal fade" id="deleteAcademicStageModal" tabindex="-1" aria-labelledby="deleteAcademicStageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center pt-0 pb-3">
                <div class="avatar avatar-xl rounded-circle bg-label-danger mx-auto mb-3 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-trash font-32"></i>
                </div>
                <h5 class="modal-title fw-bold text-heading mb-2" id="deleteAcademicStageModalLabel">{{ __('Confirm Delete') }}</h5>
                <p class="text-muted mb-0 small">
                    {{ __('Are you sure you want to delete the academic stage') }}
                    <strong id="delete_target_name" class="text-danger d-block mt-1"></strong>
                </p>
            </div>
            <div class="modal-footer border-0 justify-content-center gap-2 pt-0 pb-4">
                <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" id="confirm_delete_btn" class="btn btn-danger btn-sm shadow-sm">{{ __('Yes, Delete') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
window.addEventListener('load', function () {
    const $ = window.jQuery || window.$;
    if (!$ || !$.fn.DataTable) return;

    const baseUrl = "{{ url('academic/stages') }}";
    const dataUrl = "{{ route('academic.stages.data') }}";

    // Setup CSRF header for jQuery AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        }
    });

    // Helper: Show Alert
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

    // Helper: Clear validation errors
    function clearFormErrors(formId) {
        $(`#${formId} .is-invalid`).removeClass('is-invalid');
        $(`#${formId} .invalid-feedback`).text('');
    }

    // Helper: Show validation errors
    function showFormErrors(formId, errors) {
        clearFormErrors(formId);
        for (const field in errors) {
            const input = $(`#${formId} [name="${field}"]`);
            input.addClass('is-invalid');
            const errorDiv = $(`#${formId}_${field}_error`) || $(`#${formId} #${formId}_${field}_error`);
            if (errorDiv.length) {
                errorDiv.text(errors[field][0]);
            } else {
                input.closest('.mb-3, .col-12, .col-sm-6').find('.invalid-feedback').text(errors[field][0]);
            }
        }
    }

    // Initialize Select2 matching standard ERP pattern
    if (typeof window.initSelect2 === 'function') {
        window.initSelect2();
    } else {
        const isRtl = $('html').attr('dir') === 'rtl' || $('html').attr('lang') === 'ar';
        $('.select2, .select2-selection__rendered').each(function () {
            const $this = $(this);
            if ($this.hasClass('select2-hidden-accessible')) return;
            const $parent = $this.closest('.offcanvas, .modal');
            $this.select2({
                theme: 'bootstrap-5',
                dropdownParent: $parent.length ? $parent : null,
                placeholder: $this.data('placeholder') || '',
                allowClear: $this.data('allow-clear') !== false,
                dir: isRtl ? 'rtl' : 'ltr',
                width: '100%'
            });
        });
    }

    // Initialize Server-Side DataTable matching the theme style
    const table = $('#academicStagesTable').DataTable({
        processing: true,
        serverSide: true,
        pagingType: 'simple_numbers',
        language: {
            emptyTable: `
                <div class="text-center py-5">
                    <div class="avatar avatar-xl bg-label-secondary mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                        <i class="icon-base bx bx-layer font-32 text-secondary"></i>
                    </div>
                    <h6 class="fw-bold mb-1">{{ __('No academic stages found') }}</h6>
                    <p class="text-muted small mb-0">{{ __('Click "Add Academic Stage" to create the first stage.') }}</p>
                </div>
            `
        },
        ajax: {
            url: dataUrl,
            data: function (d) {
                d.institution_id = $('#filter_institution_id').val();
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
            { data: 'name', name: 'translations.name', className: 'fw-semibold text-heading' },
            { data: 'institution_name', name: 'institution_id', defaultContent: '-' },
            { data: 'code', name: 'code', className: 'text-center' },
            { data: 'age_range', name: 'min_age_months', className: 'text-center' },
            { data: 'status_badge', name: 'status', className: 'text-center' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center' }
        ],
        order: [[0, 'desc']]
    });

    // Filter Change Listeners
    $('#filter_institution_id, #filter_status').on('change', function () {
        table.ajax.reload();
    });

    $('#btn_reset_filters').on('click', function () {
        $('#filter_institution_id').val('').trigger('change');
        $('#filter_status').val('').trigger('change');
        table.ajax.reload();
    });

    // 1. Submit Add Academic Stage
    $('#addAcademicStageForm').on('submit', function (e) {
        e.preventDefault();
        const form = $(this);
        const submitBtn = $('#btn_submit_add');
        const originalBtnHtml = submitBtn.html();

        clearFormErrors('addAcademicStageForm');
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> {{ __("Saving...") }}');

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function (res) {
                submitBtn.prop('disabled', false).html(originalBtnHtml);
                const offcanvasEl = document.getElementById('offcanvasAddAcademicStage');
                const offcanvasInstance = bootstrap.Offcanvas.getInstance(offcanvasEl);
                if (offcanvasInstance) offcanvasInstance.hide();

                form[0].reset();
                $('#add_institution_id').val('').trigger('change');
                $('#add_status').val('active').trigger('change');

                showAlert('success', res.message || '{{ __("Academic stage created successfully.") }}');
                table.ajax.reload(null, false);
            },
            error: function (xhr) {
                submitBtn.prop('disabled', false).html(originalBtnHtml);
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    showFormErrors('addAcademicStageForm', xhr.responseJSON.errors);
                } else {
                    const msg = xhr.responseJSON?.message || '{{ __("Error saving academic stage.") }}';
                    showAlert('danger', msg);
                }
            }
        });
    });

    // 2. Open Edit Offcanvas
    const editOffcanvasEl = document.getElementById('offcanvasEditAcademicStage');
    const editOffcanvas = new bootstrap.Offcanvas(editOffcanvasEl);

    $(document).on('click', '.btn-edit-academic-stage', function () {
        const id = $(this).data('id');
        clearFormErrors('editAcademicStageForm');

        $.ajax({
            url: `${baseUrl}/${id}`,
            type: 'GET',
            success: function (res) {
                if (res.success && res.data) {
                    const item = res.data;
                    $('#edit_id').val(item.id);
                    $('#edit_institution_id').val(item.institution_id).trigger('change');
                    $('#edit_status').val(item.status).trigger('change');
                    $('#edit_code').val(item.code);
                    $('#edit_name_ar').val(item.name_ar || (item.translations && item.translations.find(t => t.locale === 'ar')?.name) || '');
                    $('#edit_name_en').val(item.name_en || (item.translations && item.translations.find(t => t.locale === 'en')?.name) || '');
                    $('#edit_min_age_months').val(item.min_age_months !== null ? item.min_age_months : '');
                    $('#edit_max_age_months').val(item.max_age_months !== null ? item.max_age_months : '');
                    $('#edit_description_ar').val(item.description_ar || (item.translations && item.translations.find(t => t.locale === 'ar')?.description) || '');
                    $('#edit_description_en').val(item.description_en || (item.translations && item.translations.find(t => t.locale === 'en')?.description) || '');

                    $('#editAcademicStageForm').attr('action', `${baseUrl}/${item.id}`);
                    editOffcanvas.show();
                }
            },
            error: function () {
                showAlert('danger', '{{ __("Failed to load academic stage data.") }}');
            }
        });
    });

    // 3. Submit Edit Academic Stage
    $('#editAcademicStageForm').on('submit', function (e) {
        e.preventDefault();
        const form = $(this);
        const submitBtn = $('#btn_submit_edit');
        const originalBtnHtml = submitBtn.html();
        const actionUrl = form.attr('action');

        clearFormErrors('editAcademicStageForm');
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> {{ __("Updating...") }}');

        $.ajax({
            url: actionUrl,
            type: 'POST',
            data: form.serialize(),
            success: function (res) {
                submitBtn.prop('disabled', false).html(originalBtnHtml);
                editOffcanvas.hide();
                showAlert('success', res.message || '{{ __("Academic stage updated successfully.") }}');
                table.ajax.reload(null, false);
            },
            error: function (xhr) {
                submitBtn.prop('disabled', false).html(originalBtnHtml);
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    showFormErrors('editAcademicStageForm', xhr.responseJSON.errors);
                } else {
                    const msg = xhr.responseJSON?.message || '{{ __("Error updating academic stage.") }}';
                    showAlert('danger', msg);
                }
            }
        });
    });

    // 4. Toggle Status
    $(document).on('click', '.btn-toggle-academic-stage-status', function () {
        const id = $(this).data('id');
        const btn = $(this);
        btn.prop('disabled', true);

        $.ajax({
            url: `${baseUrl}/${id}/toggle-status`,
            type: 'PATCH',
            success: function (res) {
                showAlert('success', res.message || '{{ __("Academic stage status updated successfully.") }}');
                table.ajax.reload(null, false);
            },
            error: function (xhr) {
                btn.prop('disabled', false);
                const msg = xhr.responseJSON?.message || '{{ __("Failed to update status.") }}';
                showAlert('danger', msg);
            }
        });
    });

    // 5. Delete Academic Stage Modal
    const deleteModalEl = document.getElementById('deleteAcademicStageModal');
    const deleteModal = new bootstrap.Modal(deleteModalEl);
    let targetDeleteId = null;

    $(document).on('click', '.btn-delete-academic-stage', function () {
        targetDeleteId = $(this).data('id');
        const name = $(this).data('name') || '';
        $('#delete_target_name').text(name);
        deleteModal.show();
    });

    $('#confirm_delete_btn').on('click', function () {
        if (!targetDeleteId) return;
        const confirmBtn = $(this);
        confirmBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>');

        $.ajax({
            url: `${baseUrl}/${targetDeleteId}`,
            type: 'DELETE',
            success: function (res) {
                confirmBtn.prop('disabled', false).text('{{ __("Yes, Delete") }}');
                deleteModal.hide();
                showAlert('success', res.message || '{{ __("Academic stage deleted successfully.") }}');
                table.ajax.reload(null, false);
            },
            error: function (xhr) {
                confirmBtn.prop('disabled', false).text('{{ __("Yes, Delete") }}');
                deleteModal.hide();
                const msg = xhr.responseJSON?.message || '{{ __("Failed to delete academic stage.") }}';
                showAlert('danger', msg);
            }
        });
    });
});
</script>
@endsection
