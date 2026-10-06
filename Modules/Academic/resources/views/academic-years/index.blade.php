@extends('layouts/contentNavbarLayout')

@section('title', __('Academic Years') . ' - ' . config('variables.templateName'))

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
    <div class="d-flex flex-column justify-content-center">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted fw-medium small">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item text-muted fw-medium small">{{ __('Academic Management') }}</li>
                <li class="breadcrumb-item active text-primary fw-medium small" aria-current="page">{{ __('Academic Years') }}</li>
            </ol>
        </nav>
        <!-- Title -->
        <div class="d-flex align-items-center gap-2 mt-1">
            <i class="icon-base bx bx-calendar text-primary font-24"></i>
            <h4 class="mb-0 fw-bold text-heading">{{ __('Academic Years') }}</h4>
        </div>
    </div>

    <div class="mt-3 mt-md-0">
        <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-2 shadow-sm" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddAcademicYear" aria-controls="offcanvasAddAcademicYear">
            <i class="icon-base bx bx-plus font-18"></i>
            <span>{{ __('Add Academic Year') }}</span>
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
                        <option value="{{ $status }}">{{ __($status) }}</option>
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
        <table id="academicYearsTable" class="table table-hover border-top w-100 text-nowrap">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">#</th>
                    <th>{{ __('Academic Year') }}</th>
                    <th>{{ __('Institution') }}</th>
                    <th class="text-center" style="width: 110px;">{{ __('Code') }}</th>
                    <th class="text-center" style="width: 110px;">{{ __('Start Date') }}</th>
                    <th class="text-center" style="width: 110px;">{{ __('End Date') }}</th>
                    <th class="text-center" style="width: 100px;">{{ __('Current Year') }}</th>
                    <th class="text-center" style="width: 100px;">{{ __('Status') }}</th>
                    <th class="text-center" style="width: 140px;">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 1. Offcanvas: Add Academic Year                                           -->
<!-- ========================================================================= -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="offcanvasAddAcademicYear" aria-labelledby="offcanvasAddAcademicYearLabel" style="width: 480px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3 px-4">
        <h5 id="offcanvasAddAcademicYearLabel" class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
            <i class="icon-base bx bx-calendar-plus text-primary font-22"></i>
            <span>{{ __('Add Academic Year') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-4">
        <form id="addAcademicYearForm" action="{{ route('academic.academic-years.store') }}" method="POST">
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
                <label class="form-label required fw-semibold text-heading">{{ __('Academic Year Code') }} <span class="text-danger">*</span></label>
                <input type="text" name="code" id="add_code" class="form-control" placeholder="{{ __('Example: 2026-2027 or AY-2026') }}" required maxlength="30" />
                <div class="invalid-feedback d-block" id="add_code_error"></div>
            </div>

            <!-- Arabic Name -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Academic Year Name (Arabic)') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_ar" id="add_name_ar" class="form-control" placeholder="{{ __('Example: Academic Year 2026 - 2027') }}" required maxlength="100" dir="rtl" />
                <div class="invalid-feedback d-block" id="add_name_ar_error"></div>
            </div>

            <!-- English Name -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Academic Year Name (English)') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_en" id="add_name_en" class="form-control" placeholder="e.g. Academic Year 2026 - 2027" required maxlength="100" dir="ltr" />
                <div class="invalid-feedback d-block" id="add_name_en_error"></div>
            </div>

            <!-- Start Date & End Date -->
            <div class="row g-3 mb-3">
                <div class="col-12 col-sm-6">
                    <label class="form-label required fw-semibold text-heading">{{ __('Start Date') }} <span class="text-danger">*</span></label>
                    <input type="date" name="start_date" id="add_start_date" class="form-control js-flatpickr-date" value="{{ date('Y-m-d') }}" placeholder="YYYY-MM-DD" required />
                    <div class="invalid-feedback d-block" id="add_start_date_error"></div>
                </div>
                <div class="col-12 col-sm-6">
                    <label class="form-label required fw-semibold text-heading">{{ __('End Date') }} <span class="text-danger">*</span></label>
                    <input type="date" name="end_date" id="add_end_date" class="form-control js-flatpickr-date" value="{{ date('Y-m-d', strtotime('+1 year')) }}" placeholder="YYYY-MM-DD" required />
                    <div class="invalid-feedback d-block" id="add_end_date_error"></div>
                </div>
            </div>

            <!-- Status & Is Current -->
            <div class="row g-3 mb-3 align-items-center">
                <div class="col-12 col-sm-6">
                    <label class="form-label fw-semibold text-heading">{{ __('Status') }}</label>
                    <select name="status" id="add_status" class="select2 select2-selection__rendered form-select" data-placeholder="{{ __('Select Status') }}">
                        <option value="draft">{{ __('Draft') }}</option>
                        <option value="active" selected>{{ __('Active') }}</option>
                        <option value="closed">{{ __('Closed') }}</option>
                        <option value="archived">{{ __('Archived') }}</option>
                    </select>
                </div>
                <div class="col-12 col-sm-6 pt-3">
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" name="is_current" value="1" id="add_is_current">
                        <label class="form-check-label fw-semibold text-heading" for="add_is_current">
                            {{ __('Set as Current Academic Year') }}
                        </label>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div class="mb-4">
                <label class="form-label fw-semibold text-heading">{{ __('Notes') }}</label>
                <textarea name="notes" id="add_notes" class="form-control" rows="3" placeholder="{{ __('Any administrative notes...') }}"></textarea>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex gap-2">
                <button type="submit" id="btn_submit_add" class="btn btn-primary flex-grow-1 d-flex align-items-center justify-content-center gap-1 shadow-sm">
                    <i class="icon-base bx bx-save"></i>
                    <span>{{ __('Save Academic Year') }}</span>
                </button>
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 2. Offcanvas: Edit Academic Year                                          -->
<!-- ========================================================================= -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="offcanvasEditAcademicYear" aria-labelledby="offcanvasEditAcademicYearLabel" style="width: 480px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3 px-4">
        <h5 id="offcanvasEditAcademicYearLabel" class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
            <i class="icon-base bx bx-edit text-primary font-22"></i>
            <span>{{ __('Edit Academic Year') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-4">
        <form id="editAcademicYearForm" method="POST">
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
                <label class="form-label required fw-semibold text-heading">{{ __('Academic Year Code') }} <span class="text-danger">*</span></label>
                <input type="text" name="code" id="edit_code" class="form-control" required maxlength="30" />
                <div class="invalid-feedback d-block" id="edit_code_error"></div>
            </div>

            <!-- Arabic Name -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Academic Year Name (Arabic)') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_ar" id="edit_name_ar" class="form-control" required maxlength="100" dir="rtl" />
                <div class="invalid-feedback d-block" id="edit_name_ar_error"></div>
            </div>

            <!-- English Name -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Academic Year Name (English)') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_en" id="edit_name_en" class="form-control" required maxlength="100" dir="ltr" />
                <div class="invalid-feedback d-block" id="edit_name_en_error"></div>
            </div>

            <!-- Start Date & End Date -->
            <div class="row g-3 mb-3">
                <div class="col-12 col-sm-6">
                    <label class="form-label required fw-semibold text-heading">{{ __('Start Date') }} <span class="text-danger">*</span></label>
                    <input type="date" name="start_date" id="edit_start_date" class="form-control js-flatpickr-date" placeholder="YYYY-MM-DD" required />
                    <div class="invalid-feedback d-block" id="edit_start_date_error"></div>
                </div>
                <div class="col-12 col-sm-6">
                    <label class="form-label required fw-semibold text-heading">{{ __('End Date') }} <span class="text-danger">*</span></label>
                    <input type="date" name="end_date" id="edit_end_date" class="form-control js-flatpickr-date" placeholder="YYYY-MM-DD" required />
                    <div class="invalid-feedback d-block" id="edit_end_date_error"></div>
                </div>
            </div>

            <!-- Status & Is Current -->
            <div class="row g-3 mb-3 align-items-center">
                <div class="col-12 col-sm-6">
                    <label class="form-label fw-semibold text-heading">{{ __('Status') }}</label>
                    <select name="status" id="edit_status" class="select2 select2-selection__rendered form-select" data-placeholder="{{ __('Select Status') }}">
                        <option value="draft">{{ __('Draft') }}</option>
                        <option value="active">{{ __('Active') }}</option>
                        <option value="closed">{{ __('Closed') }}</option>
                        <option value="archived">{{ __('Archived') }}</option>
                    </select>
                </div>
                <div class="col-12 col-sm-6 pt-3">
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" name="is_current" value="1" id="edit_is_current">
                        <label class="form-check-label fw-semibold text-heading" for="edit_is_current">
                            {{ __('Set as Current Academic Year') }}
                        </label>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div class="mb-4">
                <label class="form-label fw-semibold text-heading">{{ __('Notes') }}</label>
                <textarea name="notes" id="edit_notes" class="form-control" rows="3"></textarea>
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
<div class="modal fade" id="deleteAcademicYearModal" tabindex="-1" aria-labelledby="deleteAcademicYearModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center pt-0 pb-3">
                <div class="avatar avatar-xl rounded-circle bg-label-danger mx-auto mb-3 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-trash font-32"></i>
                </div>
                <h5 class="modal-title fw-bold text-heading mb-2" id="deleteAcademicYearModalLabel">{{ __('Confirm Delete') }}</h5>
                <p class="text-muted mb-0 small">
                    {{ __('Are you sure you want to delete the academic year') }}
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

    const baseUrl = "{{ url('academic/academic-years') }}";
    const dataUrl = "{{ route('academic.academic-years.data') }}";

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

    // Initialize Select2 matching standard ERP pattern (nursery-branches pattern)
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
    const table = $('#academicYearsTable').DataTable({
        processing: true,
        serverSide: true,
        pagingType: 'simple_numbers',
        language: {
            emptyTable: `
                <div class="text-center py-5">
                    <div class="avatar avatar-xl bg-label-secondary mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                        <i class="icon-base bx bx-calendar font-32 text-secondary"></i>
                    </div>
                    <h6 class="fw-bold mb-1">{{ __('No academic years found') }}</h6>
                    <p class="text-muted small mb-0">{{ __('Click "Add Academic Year" to create the first period.') }}</p>
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
            { data: 'start_date', name: 'start_date', className: 'text-center' },
            { data: 'end_date', name: 'end_date', className: 'text-center' },
            { data: 'is_current_badge', name: 'is_current', className: 'text-center' },
            { data: 'status_badge', name: 'status', className: 'text-center' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center' }
        ],
        order: [[4, 'desc']]
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

    // 1. Submit Add Academic Year
    $('#addAcademicYearForm').on('submit', function (e) {
        e.preventDefault();
        const form = $(this);
        const submitBtn = $('#btn_submit_add');
        const originalBtnHtml = submitBtn.html();

        clearFormErrors('addAcademicYearForm');
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> {{ __("Saving...") }}');

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function (res) {
                submitBtn.prop('disabled', false).html(originalBtnHtml);
                const offcanvasEl = document.getElementById('offcanvasAddAcademicYear');
                const offcanvasInstance = bootstrap.Offcanvas.getInstance(offcanvasEl);
                if (offcanvasInstance) offcanvasInstance.hide();

                form[0].reset();
                $('#add_institution_id').val('').trigger('change');
                $('#add_status').val('active').trigger('change');

                showAlert('success', res.message || '{{ __("Academic year created successfully.") }}');
                table.ajax.reload(null, false);
            },
            error: function (xhr) {
                submitBtn.prop('disabled', false).html(originalBtnHtml);
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    showFormErrors('addAcademicYearForm', xhr.responseJSON.errors);
                } else {
                    const msg = xhr.responseJSON?.message || '{{ __("Error saving academic year.") }}';
                    showAlert('danger', msg);
                }
            }
        });
    });

    // 2. Open Edit Offcanvas
    const editOffcanvasEl = document.getElementById('offcanvasEditAcademicYear');
    const editOffcanvas = new bootstrap.Offcanvas(editOffcanvasEl);

    $(document).on('click', '.btn-edit-academic-year', function () {
        const id = $(this).data('id');
        clearFormErrors('editAcademicYearForm');

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
                    
                    if (item.start_date) {
                        const sDate = item.start_date.substring(0, 10);
                        const sInput = document.getElementById('edit_start_date');
                        if (sInput._flatpickr) {
                            sInput._flatpickr.setDate(sDate, true);
                        } else {
                            $('#edit_start_date').val(sDate);
                        }
                    }
                    if (item.end_date) {
                        const eDate = item.end_date.substring(0, 10);
                        const eInput = document.getElementById('edit_end_date');
                        if (eInput._flatpickr) {
                            eInput._flatpickr.setDate(eDate, true);
                        } else {
                            $('#edit_end_date').val(eDate);
                        }
                    }

                    $('#edit_is_current').prop('checked', !!item.is_current);
                    $('#edit_notes').val(item.notes || '');

                    $('#editAcademicYearForm').attr('action', `${baseUrl}/${item.id}`);
                    editOffcanvas.show();
                }
            },
            error: function () {
                showAlert('danger', '{{ __("Failed to load academic year data.") }}');
            }
        });
    });

    // 3. Submit Edit Academic Year
    $('#editAcademicYearForm').on('submit', function (e) {
        e.preventDefault();
        const form = $(this);
        const submitBtn = $('#btn_submit_edit');
        const originalBtnHtml = submitBtn.html();
        const actionUrl = form.attr('action');

        clearFormErrors('editAcademicYearForm');
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> {{ __("Updating...") }}');

        $.ajax({
            url: actionUrl,
            type: 'POST',
            data: form.serialize(),
            success: function (res) {
                submitBtn.prop('disabled', false).html(originalBtnHtml);
                editOffcanvas.hide();
                showAlert('success', res.message || '{{ __("Academic year updated successfully.") }}');
                table.ajax.reload(null, false);
            },
            error: function (xhr) {
                submitBtn.prop('disabled', false).html(originalBtnHtml);
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    showFormErrors('editAcademicYearForm', xhr.responseJSON.errors);
                } else {
                    const msg = xhr.responseJSON?.message || '{{ __("Error updating academic year.") }}';
                    showAlert('danger', msg);
                }
            }
        });
    });

    // 4. Toggle Status
    $(document).on('click', '.btn-toggle-academic-year-status', function () {
        const id = $(this).data('id');
        const btn = $(this);
        btn.prop('disabled', true);

        $.ajax({
            url: `${baseUrl}/${id}/toggle-status`,
            type: 'PATCH',
            success: function (res) {
                showAlert('success', res.message || '{{ __("Academic year status updated successfully.") }}');
                table.ajax.reload(null, false);
            },
            error: function (xhr) {
                btn.prop('disabled', false);
                const msg = xhr.responseJSON?.message || '{{ __("Failed to update status.") }}';
                showAlert('danger', msg);
            }
        });
    });

    // 5. Set as Current Year
    $(document).on('click', '.btn-set-current-academic-year', function () {
        const id = $(this).data('id');
        const btn = $(this);
        btn.prop('disabled', true);

        $.ajax({
            url: `${baseUrl}/${id}/set-current`,
            type: 'PATCH',
            success: function (res) {
                showAlert('success', res.message || '{{ __("Academic year set as current successfully.") }}');
                table.ajax.reload(null, false);
            },
            error: function (xhr) {
                btn.prop('disabled', false);
                const msg = xhr.responseJSON?.message || '{{ __("Failed to set current academic year.") }}';
                showAlert('danger', msg);
            }
        });
    });

    // 6. Delete Academic Year Modal
    const deleteModalEl = document.getElementById('deleteAcademicYearModal');
    const deleteModal = new bootstrap.Modal(deleteModalEl);
    let targetDeleteId = null;

    $(document).on('click', '.btn-delete-academic-year', function () {
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
                showAlert('success', res.message || '{{ __("Academic year deleted successfully.") }}');
                table.ajax.reload(null, false);
            },
            error: function (xhr) {
                confirmBtn.prop('disabled', false).text('{{ __("Yes, Delete") }}');
                deleteModal.hide();
                const msg = xhr.responseJSON?.message || '{{ __("Failed to delete academic year.") }}';
                showAlert('danger', msg);
            }
        });
    });
});
</script>
@endsection
