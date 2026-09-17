@extends('layouts/contentNavbarLayout')

@section('title', __('Shifts') . ' - ' . config('variables.templateName'))

@section('content')
<!-- Header -->
<div class="mb-4">
    <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
        <i class="icon-base bx bx-slider-alt text-primary font-22"></i>
        {{ __('HR Settings') }}
    </h4>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-3" role="alert">
        <i class="icon-base bx bx-check-circle me-2 font-20"></i>
        <div class="flex-grow-1">{{ session('success') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-3" role="alert">
        <i class="icon-base bx bx-error me-2 font-20"></i>
        <div class="flex-grow-1">{{ session('error') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- 2-Column Layout with Sub-Sidebar -->
<div class="row g-4">
    <!-- Sub-Sidebar Column -->
    <div class="col-12 col-md-4 col-lg-3">
        @include('hr::settings.partials.sidebar')
    </div>

    <!-- Main Content Column -->
    <div class="col-12 col-md-8 col-lg-9">
        <!-- Action Bar: Title & Add Button -->
        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-3">
            <div>
                <h5 class="fw-bold mb-1 d-flex align-items-center gap-2 text-heading">
                    <i class="icon-base bx bx-time-five text-primary"></i>
                    {{ __('Shifts') }}
                </h5>
                <p class="text-muted mb-0 small">{{ __('Manage company working shifts, hours, grace periods, and attendance windows.') }}</p>
            </div>

            <div>
                <button type="button" class="btn btn-primary d-flex align-items-center justify-content-center gap-2 shadow-sm w-100 w-sm-auto" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddShift" aria-controls="offcanvasAddShift">
                    <i class="icon-base bx bx-plus font-18"></i>
                    <span>{{ __('Add New Shift') }}</span>
                </button>
            </div>
        </div>

        <!-- Shifts Server-Side DataTable Card -->
        <div class="card shadow-sm border-0">
            <div class="card-datatable">
                <table id="shiftsTable" class="table table-hover border-top w-100 text-nowrap">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>{{ __('Code') }}</th>
                            <th>{{ __('Arabic Name') }}</th>
                            <th>{{ __('English Name') }}</th>
                            <th>{{ __('Working Hours') }}</th>
                            <th>{{ __('Grace Periods') }}</th>
                            <th>{{ __('Overnight') }}</th>
                            <th>{{ __('Color') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Created Date') }}</th>
                            <th class="text-center" style="width: 100px;">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Loaded via DataTables Server-Side -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 1. Offcanvas: Add New Shift                                               -->
<!-- ========================================================================= -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="offcanvasAddShift" aria-labelledby="offcanvasAddShiftLabel" style="width: 520px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3 px-4">
        <h5 id="offcanvasAddShiftLabel" class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
            <i class="icon-base bx bx-plus text-primary"></i>
            <span>{{ __('Add New Shift') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-4">
        <form action="{{ route('hr.settings.shifts.store') }}" method="POST" id="addShiftForm">
            @csrf

            <!-- Shift Code & Color -->
            <div class="row g-3 mb-3">
                <div class="col-sm-7">
                    <label class="form-label fw-semibold">{{ __('Shift Code') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                    <input type="text" name="code" class="form-control" placeholder="{{ __('e.g. MORNING / SH-01') }}">
                </div>
                <div class="col-sm-5">
                    <label class="form-label fw-semibold">{{ __('Badge Color') }}</label>
                    <div class="input-group">
                        <input type="color" name="color" class="form-control form-control-color w-100" value="#7367f0" title="{{ __('Choose shift color') }}">
                    </div>
                </div>
            </div>

            <!-- Arabic Name -->
            <div class="mb-3">
                <label class="form-label required fw-semibold">{{ __('Shift Name [Arabic]') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_ar" class="form-control" placeholder="{{ __('e.g. الوردية الصباحية / الوردية المسائية') }}" required dir="rtl">
            </div>

            <!-- English Name -->
            <div class="mb-3">
                <label class="form-label required fw-semibold">{{ __('Shift Name [English]') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_en" class="form-control" placeholder="{{ __('e.g. Morning Shift / Evening Shift') }}" required dir="ltr">
            </div>

            <!-- Shift Working Hours -->
            <div class="p-3 bg-label-secondary rounded mb-3">
                <h6 class="fw-bold mb-2 text-heading d-flex align-items-center gap-1">
                    <i class="icon-base bx bx-time text-primary"></i>
                    <span>{{ __('Core Working Hours') }}</span>
                </h6>
                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label required fw-semibold">{{ __('Start Time') }} <span class="text-danger">*</span></label>
                        <input type="time" name="start_time" class="form-control" value="08:00" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label required fw-semibold">{{ __('End Time') }} <span class="text-danger">*</span></label>
                        <input type="time" name="end_time" class="form-control" value="16:00" required>
                    </div>
                </div>

                <div class="form-check form-switch mt-3">
                    <input class="form-check-input" type="checkbox" name="is_overnight" value="1" id="add_is_overnight">
                    <label class="form-check-label fw-semibold" for="add_is_overnight">
                        {{ __('Overnight Shift (Ends Next Day)') }}
                    </label>
                </div>
            </div>

            <!-- Grace Periods -->
            <div class="row g-3 mb-3">
                <div class="col-6">
                    <label class="form-label fw-semibold">{{ __('Grace In (Minutes)') }}</label>
                    <input type="number" name="grace_period_in" class="form-control" value="15" min="0" max="180">
                    <small class="text-muted">{{ __('Allowed late arrival') }}</small>
                </div>
                <div class="col-6">
                    <label class="form-label fw-semibold">{{ __('Grace Out (Minutes)') }}</label>
                    <input type="number" name="grace_period_out" class="form-control" value="0" min="0" max="180">
                    <small class="text-muted">{{ __('Allowed early departure') }}</small>
                </div>
            </div>

            <!-- Attendance Windows (Check-in & Check-out range) Collapsible -->
            <div class="card card-border-shadow-primary mb-3">
                <div class="card-header p-3 cursor-pointer d-flex justify-content-between align-items-center" data-bs-toggle="collapse" data-bs-target="#addAttendanceWindowsCollapse" aria-expanded="false">
                    <span class="fw-semibold text-heading d-flex align-items-center gap-1">
                        <i class="icon-base bx bx-expand-vertical text-muted"></i>
                        {{ __('Attendance Windows (Optional)') }}
                    </span>
                    <i class="icon-base bx bx-chevron-down text-muted"></i>
                </div>
                <div class="collapse" id="addAttendanceWindowsCollapse">
                    <div class="card-body p-3 pt-0">
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label class="form-label small fw-semibold">{{ __('Check-in Starts') }}</label>
                                <input type="time" name="checkin_start" class="form-control form-control-sm">
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold">{{ __('Check-in Ends') }}</label>
                                <input type="time" name="checkin_end" class="form-control form-control-sm">
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label small fw-semibold">{{ __('Check-out Starts') }}</label>
                                <input type="time" name="checkout_start" class="form-control form-control-sm">
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold">{{ __('Check-out Ends') }}</label>
                                <input type="time" name="checkout_end" class="form-control form-control-sm">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status -->
            <div class="mb-3">
                <label class="form-label required fw-semibold">{{ __('Status') }} <span class="text-danger">*</span></label>
                <select class="form-select select2" name="status" data-placeholder="{{ __('Select Status') }}" required>
                    @foreach ($statuses as $statusCase)
                        <option value="{{ $statusCase->value }}" {{ $statusCase->value === 'active' ? 'selected' : '' }}>
                            {{ $statusCase->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Description Arabic -->
            <div class="mb-3">
                <label class="form-label fw-semibold">{{ __('Description [Arabic]') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                <textarea name="description_ar" class="form-control" rows="2" placeholder="{{ __('وصف الوردية أو ملاحظات...') }}" dir="rtl"></textarea>
            </div>

            <!-- Description English -->
            <div class="mb-3">
                <label class="form-label fw-semibold">{{ __('Description [English]') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                <textarea name="description_en" class="form-control" rows="2" placeholder="{{ __('Shift description or notes...') }}" dir="ltr"></textarea>
            </div>

            <!-- Form Actions -->
            <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-1">
                    <i class="icon-base bx bx-check"></i>
                    <span>{{ __('Save Shift') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 2. Offcanvas: Edit Shift                                                  -->
<!-- ========================================================================= -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="offcanvasEditShift" aria-labelledby="offcanvasEditShiftLabel" style="width: 520px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3 px-4">
        <h5 id="offcanvasEditShiftLabel" class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
            <i class="icon-base bx bx-edit-alt text-primary"></i>
            <span>{{ __('Edit Shift') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-4">
        <form action="" method="POST" id="editShiftForm">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" id="edit_id">

            <!-- Shift Code & Color -->
            <div class="row g-3 mb-3">
                <div class="col-sm-7">
                    <label class="form-label fw-semibold">{{ __('Shift Code') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                    <input type="text" name="code" id="edit_code" class="form-control" placeholder="{{ __('e.g. MORNING / SH-01') }}">
                </div>
                <div class="col-sm-5">
                    <label class="form-label fw-semibold">{{ __('Badge Color') }}</label>
                    <div class="input-group">
                        <input type="color" name="color" id="edit_color" class="form-control form-control-color w-100">
                    </div>
                </div>
            </div>

            <!-- Arabic Name -->
            <div class="mb-3">
                <label class="form-label required fw-semibold">{{ __('Shift Name [Arabic]') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_ar" id="edit_name_ar" class="form-control" required dir="rtl">
            </div>

            <!-- English Name -->
            <div class="mb-3">
                <label class="form-label required fw-semibold">{{ __('Shift Name [English]') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_en" id="edit_name_en" class="form-control" required dir="ltr">
            </div>

            <!-- Shift Working Hours -->
            <div class="p-3 bg-label-secondary rounded mb-3">
                <h6 class="fw-bold mb-2 text-heading d-flex align-items-center gap-1">
                    <i class="icon-base bx bx-time text-primary"></i>
                    <span>{{ __('Core Working Hours') }}</span>
                </h6>
                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label required fw-semibold">{{ __('Start Time') }} <span class="text-danger">*</span></label>
                        <input type="time" name="start_time" id="edit_start_time" class="form-control" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label required fw-semibold">{{ __('End Time') }} <span class="text-danger">*</span></label>
                        <input type="time" name="end_time" id="edit_end_time" class="form-control" required>
                    </div>
                </div>

                <div class="form-check form-switch mt-3">
                    <input class="form-check-input" type="checkbox" name="is_overnight" value="1" id="edit_is_overnight">
                    <label class="form-check-label fw-semibold" for="edit_is_overnight">
                        {{ __('Overnight Shift (Ends Next Day)') }}
                    </label>
                </div>
            </div>

            <!-- Grace Periods -->
            <div class="row g-3 mb-3">
                <div class="col-6">
                    <label class="form-label fw-semibold">{{ __('Grace In (Minutes)') }}</label>
                    <input type="number" name="grace_period_in" id="edit_grace_period_in" class="form-control" min="0" max="180">
                    <small class="text-muted">{{ __('Allowed late arrival') }}</small>
                </div>
                <div class="col-6">
                    <label class="form-label fw-semibold">{{ __('Grace Out (Minutes)') }}</label>
                    <input type="number" name="grace_period_out" id="edit_grace_period_out" class="form-control" min="0" max="180">
                    <small class="text-muted">{{ __('Allowed early departure') }}</small>
                </div>
            </div>

            <!-- Attendance Windows Collapsible -->
            <div class="card card-border-shadow-primary mb-3">
                <div class="card-header p-3 cursor-pointer d-flex justify-content-between align-items-center" data-bs-toggle="collapse" data-bs-target="#editAttendanceWindowsCollapse" aria-expanded="false">
                    <span class="fw-semibold text-heading d-flex align-items-center gap-1">
                        <i class="icon-base bx bx-expand-vertical text-muted"></i>
                        {{ __('Attendance Windows (Optional)') }}
                    </span>
                    <i class="icon-base bx bx-chevron-down text-muted"></i>
                </div>
                <div class="collapse" id="editAttendanceWindowsCollapse">
                    <div class="card-body p-3 pt-0">
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label class="form-label small fw-semibold">{{ __('Check-in Starts') }}</label>
                                <input type="time" name="checkin_start" id="edit_checkin_start" class="form-control form-control-sm">
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold">{{ __('Check-in Ends') }}</label>
                                <input type="time" name="checkin_end" id="edit_checkin_end" class="form-control form-control-sm">
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label small fw-semibold">{{ __('Check-out Starts') }}</label>
                                <input type="time" name="checkout_start" id="edit_checkout_start" class="form-control form-control-sm">
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold">{{ __('Check-out Ends') }}</label>
                                <input type="time" name="checkout_end" id="edit_checkout_end" class="form-control form-control-sm">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status -->
            <div class="mb-3">
                <label class="form-label required fw-semibold">{{ __('Status') }} <span class="text-danger">*</span></label>
                <select class="form-select select2" name="status" id="edit_status" data-placeholder="{{ __('Select Status') }}" required>
                    @foreach ($statuses as $statusCase)
                        <option value="{{ $statusCase->value }}">{{ $statusCase->label() }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Description Arabic -->
            <div class="mb-3">
                <label class="form-label fw-semibold">{{ __('Description [Arabic]') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                <textarea name="description_ar" id="edit_description_ar" class="form-control" rows="2" dir="rtl"></textarea>
            </div>

            <!-- Description English -->
            <div class="mb-3">
                <label class="form-label fw-semibold">{{ __('Description [English]') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                <textarea name="description_en" id="edit_description_en" class="form-control" rows="2" dir="ltr"></textarea>
            </div>

            <!-- Form Actions -->
            <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-1">
                    <i class="icon-base bx bx-check"></i>
                    <span>{{ __('Save Changes') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 3. Modal: Delete Confirmation                                             -->
<!-- ========================================================================= -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="" method="POST" id="deleteShiftForm" class="modal-content border-0 shadow">
            @csrf
            @method('DELETE')
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title text-danger fw-bold d-flex align-items-center gap-2" id="deleteConfirmModalLabel">
                    <i class="icon-base bx bx-trash font-20"></i>
                    {{ __('Delete Shift') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="avatar avatar-lg bg-label-danger mx-auto mb-3">
                    <i class="icon-base bx bx-error-circle fs-2"></i>
                </div>
                <h5 class="mb-2 fw-semibold">{{ __('Are you sure you want to delete this shift?') }}</h5>
                <p class="text-muted mb-0">
                    <span id="deleteItemName" class="fw-bold text-heading"></span>
                </p>
                <p class="text-muted small mt-1">{{ __('This action will remove the shift and its timetable definitions.') }}</p>
            </div>
            <div class="modal-footer border-top justify-content-center gap-2">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-danger">{{ __('Yes, Delete') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('page-script')
<script>
window.addEventListener('load', function () {
    const $ = window.jQuery || window.$;
    if (!$ || !$.fn.DataTable) {
        console.error('jQuery or DataTables not loaded');
        return;
    }

    const dataUrl = "{{ route('hr.settings.shifts.data') }}";
    const baseUrl = "{{ url('hr/settings/shifts') }}";

    // 1. Initialize Server-Side DataTable
    const table = $('#shiftsTable').DataTable({
        processing: true,
        serverSide: true,
        pagingType: 'simple_numbers',
        ajax: dataUrl,
        columns: [
            { 
                data: 'DT_RowIndex', 
                name: 'DT_RowIndex',
                orderable: false, 
                searchable: false,
                render: (data, type, row, meta) => `<strong class="text-muted">#${data || (meta.row + 1 + (meta.settings._iDisplayStart || 0))}</strong>`
            },
            { 
                data: 'code', 
                name: 'code',
                render: data => `<span class="badge bg-label-info font-monospace">${data || '-'}</span>`
            },
            { 
                data: 'name_ar', 
                name: 'name_ar',
                render: data => `<span class="fw-semibold text-heading">${data || '-'}</span>`
            },
            { 
                data: 'name_en', 
                name: 'name_en',
                render: data => `<span class="text-body">${data || '-'}</span>`
            },
            { 
                data: 'time_range', 
                name: 'start_time',
                orderable: true,
                searchable: false
            },
            { 
                data: 'grace_periods', 
                name: 'grace_period_in',
                orderable: false,
                searchable: false
            },
            { 
                data: 'is_overnight', 
                name: 'is_overnight',
                className: 'text-center'
            },
            { 
                data: 'color_badge', 
                name: 'color',
                orderable: false,
                searchable: false
            },
            { 
                data: 'status', 
                name: 'status',
                className: 'text-center'
            },
            { 
                data: 'created_at', 
                name: 'created_at',
                render: data => `<small class="text-muted">${data || '-'}</small>`
            },
            {
                data: 'actions',
                orderable: false,
                searchable: false,
                className: 'text-center'
            }
        ],
        order: [[9, 'desc']],
        language: {
            search: "{{ __('Search:') }}",
            searchPlaceholder: "{{ __('Search shifts...') }}",
            lengthMenu: "{{ __('Show _MENU_ entries') }}",
            info: "{{ __('Showing _START_ to _END_ of _TOTAL_ entries') }}",
            infoEmpty: "{{ __('Showing 0 to 0 of 0 entries') }}",
            emptyTable: "{{ __('No shifts available in table') }}",
            zeroRecords: "{{ __('No matching shifts found') }}",
            processing: "{{ __('Processing...') }}",
            paginate: {
                first: "{{ __('First') }}",
                last: "{{ __('Last') }}",
                next: "{{ __('Next') }}",
                previous: "{{ __('Previous') }}"
            }
        }
    });

    // 2. Edit Action: Populate & Open Offcanvas
    const editOffcanvasEl = document.getElementById('offcanvasEditShift');
    const editForm = document.getElementById('editShiftForm');

    $(document).on('click', '.btn-edit-shift', function () {
        const raw = $(this).attr('data-row');
        if (!raw) return;

        const row = JSON.parse(raw);

        editForm.action = baseUrl + '/' + row.id;
        $('#edit_id').val(row.id);
        $('#edit_code').val(row.code || '');
        $('#edit_color').val(row.color || '#7367f0');
        $('#edit_name_ar').val(row.name_ar || '');
        $('#edit_name_en').val(row.name_en || '');
        $('#edit_start_time').val(row.start_time || '');
        $('#edit_end_time').val(row.end_time || '');
        $('#edit_is_overnight').prop('checked', !!row.is_overnight);
        $('#edit_grace_period_in').val(row.grace_period_in !== undefined ? row.grace_period_in : 15);
        $('#edit_grace_period_out').val(row.grace_period_out !== undefined ? row.grace_period_out : 0);
        $('#edit_checkin_start').val(row.checkin_start || '');
        $('#edit_checkin_end').val(row.checkin_end || '');
        $('#edit_checkout_start').val(row.checkout_start || '');
        $('#edit_checkout_end').val(row.checkout_end || '');
        $('#edit_description_ar').val(row.description_ar || '');
        $('#edit_description_en').val(row.description_en || '');

        if ($('#edit_status').length) {
            $('#edit_status').val(row.status || 'active').trigger('change');
        }

        if (editOffcanvasEl) {
            const editOffcanvas = bootstrap.Offcanvas.getOrCreateInstance(editOffcanvasEl);
            editOffcanvas.show();
        }
    });

    // 3. Delete Action: Confirmation Modal
    const deleteModalEl = document.getElementById('deleteConfirmModal');
    const deleteModal = deleteModalEl ? new bootstrap.Modal(deleteModalEl) : null;
    const deleteForm = document.getElementById('deleteShiftForm');
    const deleteTargetName = document.getElementById('deleteItemName');

    $(document).on('click', '.btn-delete-shift', function () {
        const id = $(this).data('id');
        const name = $(this).data('name');

        deleteForm.action = baseUrl + '/' + id;
        deleteTargetName.textContent = name || '';

        if (deleteModal) {
            deleteModal.show();
        }
    });

    // Initialize Select2 in offcanvases
    function setupSelect2(container = document) {
        if (typeof window.initSelect2 === 'function') {
            window.initSelect2(container);
        } else if ($.fn.select2) {
            $(container).find('.select2').each(function () {
                if (!$(this).hasClass('select2-hidden-accessible')) {
                    $(this).select2({
                        placeholder: $(this).data('placeholder') || '',
                        allowClear: $(this).data('allow-clear') !== false,
                        width: '100%',
                        dropdownParent: $(this).closest('.offcanvas').length ? $(this).closest('.offcanvas') : $(document.body)
                    });
                }
            });
        }
    }

    setupSelect2();

    $(document).on('shown.bs.offcanvas', function (e) {
        setupSelect2(e.target);
    });
});
</script>
@endsection
