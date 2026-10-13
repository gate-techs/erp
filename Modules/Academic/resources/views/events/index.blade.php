@extends('layouts/contentNavbarLayout')

@section('title', __('Events') . ' - ' . config('variables.templateName'))

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
    <div class="d-flex flex-column justify-content-center">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted fw-medium small">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item text-muted fw-medium small">{{ __('Schedules & Events') }}</li>
                <li class="breadcrumb-item active text-primary fw-medium small" aria-current="page">{{ __('Events') }}</li>
            </ol>
        </nav>
        <!-- Title -->
        <div class="d-flex align-items-center gap-2 mt-1">
            <div class="avatar avatar-sm bg-label-primary rounded p-1 d-flex align-items-center justify-content-center">
                <i class="icon-base bx bx-party text-primary font-22"></i>
            </div>
            <div>
                <h4 class="mb-0 fw-bold text-heading">{{ __('Events & Activities') }}</h4>
            </div>
        </div>
    </div>

    <div class="mt-3 mt-md-0 d-flex align-items-center gap-2">
        <a href="{{ route('settings.general.event-types.index') }}" class="btn btn-label-secondary btn-sm d-flex align-items-center gap-2 shadow-sm">
            <i class="icon-base bx bx-category font-18"></i>
            <span>{{ __('Event Types Settings') }}</span>
        </a>
        <button type="button" id="btn_open_add_event" class="btn btn-primary btn-sm d-flex align-items-center gap-2 shadow-sm">
            <i class="icon-base bx bx-plus font-18"></i>
            <span>{{ __('Add New Event') }}</span>
        </button>
    </div>
</div>

<div id="page_alert_container"></div>

<!-- Quick Metrics Overview -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="avatar bg-label-primary rounded p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-party font-22"></i>
                </div>
                <div>
                    <span class="text-muted small d-block">{{ __('Total Events') }}</span>
                    <h5 class="mb-0 fw-bold text-heading" id="metric_total_events">--</h5>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="avatar bg-label-success rounded p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-check-circle font-22"></i>
                </div>
                <div>
                    <span class="text-muted small d-block">{{ __('Active') }}</span>
                    <h5 class="mb-0 fw-bold text-success" id="metric_active_events">--</h5>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="avatar bg-label-warning rounded p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-time-five font-22"></i>
                </div>
                <div>
                    <span class="text-muted small d-block">{{ __('Upcoming') }}</span>
                    <h5 class="mb-0 fw-bold text-warning" id="metric_upcoming_events">--</h5>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="avatar bg-label-info rounded p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-calendar-event font-22"></i>
                </div>
                <div>
                    <span class="text-muted small d-block">{{ __('Event Types') }}</span>
                    <h5 class="mb-0 fw-bold text-info">{{ $eventTypes->count() }}</h5>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters Card -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body py-3">
        <div class="row g-3 align-items-end">
            <!-- 1. Institution Filter -->
            <div class="col-12 col-md-3 col-lg-2">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Nursery / Institution') }}</label>
                <select id="filter_institution_id" class="select2 form-select" data-placeholder="{{ __('All Nurseries') }}">
                    <option value="">{{ __('All Nurseries') }}</option>
                    @foreach ($institutions as $inst)
                        <option value="{{ $inst->id }}">{{ $inst->translate(app()->getLocale())?->name ?? $inst->translate('ar')?->name ?? $inst->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 2. Branch Filter -->
            <div class="col-12 col-md-3 col-lg-2">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Branch') }}</label>
                <select id="filter_branch_id" class="select2 form-select" data-placeholder="{{ __('All Branches') }}">
                    <option value="">{{ __('All Branches') }}</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->translate(app()->getLocale())?->name ?? $branch->translate('ar')?->name ?? $branch->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 3. Class Filter -->
            <div class="col-12 col-md-3 col-lg-2">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Academic Class') }}</label>
                <select id="filter_class_id" class="select2 form-select" data-placeholder="{{ __('All Classes') }}">
                    <option value="">{{ __('All Classes') }}</option>
                    @foreach ($classes as $cls)
                        <option value="{{ $cls->id }}">{{ $cls->translate(app()->getLocale())?->name ?? $cls->translate('ar')?->name ?? $cls->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 4. Event Type Filter -->
            <div class="col-12 col-md-3 col-lg-2">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Event Type') }}</label>
                <select id="filter_event_type_id" class="select2 form-select" data-placeholder="{{ __('All Types') }}">
                    <option value="">{{ __('All Types') }}</option>
                    @foreach ($eventTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->translate(app()->getLocale())?->name ?? $type->translate('ar')?->name ?? $type->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 5. Status Filter -->
            <div class="col-12 col-md-2 col-lg-2">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Status') }}</label>
                <select id="filter_status" class="select2 form-select" data-placeholder="{{ __('All Statuses') }}">
                    <option value="">{{ __('All Statuses') }}</option>
                    @foreach ($statuses as $key => $lbl)
                        <option value="{{ $key }}">{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 6. Reset Button -->
            <div class="col-12 col-md-2 col-lg-2 d-flex gap-2">
                <button type="button" id="btn_reset_filters" class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center gap-1">
                    <i class="icon-base bx bx-reset font-18"></i>
                    <span>{{ __('Reset') }}</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Events Table Card -->
<div class="card shadow-sm border-0">
    <div class="card-datatable">
        <table id="eventsTable" class="table table-hover border-top w-100 text-nowrap">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th style="width: 70px;">{{ __('Image') }}</th>
                    <th>{{ __('Event Name') }}</th>
                    <th>{{ __('Nursery & Branch') }}</th>
                    <th>{{ __('Academic Class') }}</th>
                    <th>{{ __('Event Type') }}</th>
                    <th>{{ __('Date Range') }}</th>
                    <th>{{ __('Price') }}</th>
                    <th class="text-center" style="width: 90px;">{{ __('Status') }}</th>
                    <th class="text-center" style="width: 120px;">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                <!-- Loaded via DataTables Server-Side -->
            </tbody>
        </table>
    </div>
</div>

<!-- ============================================================== -->
<!-- Offcanvas Drawer: Add / Edit Event (Side Modal)                -->
<!-- ============================================================== -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="offcanvasEvent" aria-labelledby="offcanvasEventLabel" style="width: 540px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3 px-4">
        <h5 class="offcanvas-title fw-bold d-flex align-items-center gap-2 mb-0" id="offcanvasEventLabel">
            <i class="icon-base bx bx-party text-primary font-22"></i>
            <span id="offcanvasEventTitleText">{{ __('Add New Event') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-4">
        <div id="offcanvas_alert_error" class="alert alert-danger d-none mb-3" role="alert">
            <ul class="mb-0 ps-3" id="offcanvas_error_list"></ul>
        </div>

        <form id="eventForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="event_method" name="_method" value="POST">
            <input type="hidden" id="event_id" value="">

            <div class="row g-3">
                <!-- Cover Image Upload with Preview -->
                <div class="col-12 text-center">
                    <label class="form-label d-block text-start fw-semibold mb-2">{{ __('Event Cover / Image') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                    <div id="drawer_image_preview_box" class="position-relative d-inline-block rounded border overflow-hidden bg-light w-100 mb-2" style="height: 160px; cursor: pointer;" onclick="document.getElementById('drawer_image_input').click();">
                        <img id="drawer_image_preview" src="" alt="Preview" class="w-100 h-100 object-fit-cover d-none">
                        <div id="drawer_placeholder_box" class="h-100 d-flex flex-column align-items-center justify-content-center text-muted p-3">
                            <i class="icon-base bx bx-cloud-upload font-32 text-primary mb-1"></i>
                            <span class="small fw-medium">{{ __('Click to upload event image') }}</span>
                            <small class="text-muted font-11">PNG, JPG, WEBP ({{ __('Max: 2MB') }})</small>
                        </div>
                    </div>
                    <input type="file" name="image" id="drawer_image_input" class="d-none" accept="image/*">
                </div>

                <!-- Event Name Arabic -->
                <div class="col-12">
                    <label class="form-label required">{{ __('Event Name (Arabic)') }} <span class="text-danger">*</span></label>
                    <input type="text" name="name_ar" id="drawer_name_ar" class="form-control" placeholder="مثال: رحلة علمية إلى حديقة الشهيد" required>
                </div>

                <!-- Event Name English -->
                <div class="col-12">
                    <label class="form-label required">{{ __('Event Name (English)') }} <span class="text-danger">*</span></label>
                    <input type="text" name="name_en" id="drawer_name_en" class="form-control" placeholder="Example: Science Trip to Al Shaheed Park" required dir="ltr">
                </div>

                <!-- Nursery / Institution -->
                <div class="col-12">
                    <label class="form-label required">{{ __('Nursery') }} <span class="text-danger">*</span></label>
                    <select id="drawer_institution_id" name="institution_id" class="form-select select2-drawer" required>
                        <option value="">{{ __('Select Nursery') }}</option>
                        @foreach ($institutions as $inst)
                            <option value="{{ $inst->id }}">{{ $inst->translate(app()->getLocale())?->name ?? $inst->translate('ar')?->name ?? $inst->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Branch -->
                <div class="col-12">
                    <label class="form-label required">{{ __('Branch') }} <span class="text-danger">*</span></label>
                    <select id="drawer_branch_id" name="branch_id" class="form-select select2-drawer" required>
                        <option value="">{{ __('Select Branch') }}</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->translate(app()->getLocale())?->name ?? $branch->translate('ar')?->name ?? $branch->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Academic Class -->
                <div class="col-12">
                    <label class="form-label">{{ __('Academic Class') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                    <select id="drawer_academic_class_id" name="academic_class_id" class="form-select select2-drawer">
                        <option value="">{{ __('All Classes in Branch') }}</option>
                        @foreach ($classes as $cls)
                            <option value="{{ $cls->id }}">{{ $cls->translate(app()->getLocale())?->name ?? $cls->translate('ar')?->name ?? $cls->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Event Type -->
                <div class="col-12">
                    <label class="form-label required">{{ __('Event Type') }} <span class="text-danger">*</span></label>
                    <select id="drawer_event_type_id" name="event_type_id" class="form-select select2-drawer" required>
                        <option value="">{{ __('Select Event Type') }}</option>
                        @foreach ($eventTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->translate(app()->getLocale())?->name ?? $type->translate('ar')?->name ?? $type->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Start Date -->
                <div class="col-12">
                    <label class="form-label required">{{ __('Start Date') }} <span class="text-danger">*</span></label>
                    <input type="text" name="start_date" id="drawer_start_date" class="form-control flatpickr-date" placeholder="YYYY-MM-DD" required>
                </div>

                <!-- End Date -->
                <div class="col-12">
                    <label class="form-label">{{ __('End Date') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                    <input type="text" name="end_date" id="drawer_end_date" class="form-control flatpickr-date" placeholder="YYYY-MM-DD">
                </div>

                <!-- Start Time -->
                <div class="col-12">
                    <label class="form-label">{{ __('Start Time') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                    <input type="text" name="start_time" id="drawer_start_time" class="form-control flatpickr-time" placeholder="HH:MM">
                </div>

                <!-- End Time -->
                <div class="col-12">
                    <label class="form-label">{{ __('End Time') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                    <input type="text" name="end_time" id="drawer_end_time" class="form-control flatpickr-time" placeholder="HH:MM">
                </div>

                <!-- Price -->
                <div class="col-12">
                    <label class="form-label">{{ __('Price / Fees (KWD)') }}</label>
                    <div class="input-group">
                        <input type="number" step="0.001" min="0" name="price" id="drawer_price" class="form-control" value="0.000">
                        <span class="input-group-text">{{ __('KWD') }}</span>
                    </div>
                </div>

                <!-- Status -->
                <div class="col-12">
                    <label class="form-label">{{ __('Status') }}</label>
                    <select name="status" id="drawer_status" class="form-select">
                        @foreach ($statuses as $key => $lbl)
                            <option value="{{ $key }}">{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Details Arabic -->
                <div class="col-12">
                    <label class="form-label">{{ __('Event Details (Arabic)') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                    <textarea name="description_ar" id="drawer_description_ar" rows="3" class="form-control" placeholder="تفاصيل الفاعلية والتعليمات لأولياء الأمور..."></textarea>
                </div>

                <!-- Details English -->
                <div class="col-12">
                    <label class="form-label">{{ __('Event Details (English)') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                    <textarea name="description_en" id="drawer_description_en" rows="3" class="form-control" placeholder="Event details and instructions in English..." dir="ltr"></textarea>
                </div>
            </div>

            <div class="mt-4 pt-3 border-top d-flex align-items-center justify-content-end gap-2">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                <button type="submit" id="drawer_submit_btn" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm">
                    <i class="icon-base bx bx-save font-18"></i>
                    <span id="drawer_submit_text">{{ __('Save Event') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Delete Event -->
<div class="modal fade" id="deleteEventModal" tabindex="-1" aria-labelledby="deleteEventModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title d-flex align-items-center gap-2 text-danger" id="deleteEventModalLabel">
                    <i class="icon-base bx bx-trash"></i>
                    {{ __('Confirm Deletion') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="icon-base bx bx-error-circle text-danger mb-3" style="font-size: 54px;"></i>
                <h5 class="fw-bold mb-2">{{ __('Are you sure you want to delete this event?') }}</h5>
                <p class="text-muted mb-0">
                    {{ __('You are about to delete:') }} <strong id="delete_target_name" class="text-heading"></strong>
                </p>
                <small class="text-danger d-block mt-2">{{ __('This action cannot be undone.') }}</small>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" id="btn_confirm_delete" class="btn btn-danger d-flex align-items-center gap-1">
                    <i class="icon-base bx bx-trash"></i>
                    <span>{{ __('Yes, Delete') }}</span>
                </button>
            </div>
        </div>
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

    const dataUrl = "{{ route('academic.events.data') }}";
    const storeUrl = "{{ route('academic.events.store') }}";
    const baseUrl = "{{ url('academic/events') }}";
    const branchesUrl = "{{ url('academic/events/branches') }}";
    const classesUrl = "{{ url('academic/events/classes') }}";
    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    // Offcanvas Instance
    const offcanvasEl = document.getElementById('offcanvasEvent');
    const offcanvasInstance = offcanvasEl ? bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl) : null;

    // Initialize Filter Select2
    $('.select2').each(function () {
        const placeholder = $(this).data('placeholder') || '';
        $(this).select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: placeholder,
            allowClear: true,
            dir: document.documentElement.dir === 'rtl' ? 'rtl' : 'ltr'
        });
    });

    // Initialize Drawer Select2
    function initDrawerSelect2() {
        $('.select2-drawer').each(function () {
            $(this).select2({
                theme: 'bootstrap-5',
                width: '100%',
                dropdownParent: $('#offcanvasEvent'),
                dir: document.documentElement.dir === 'rtl' ? 'rtl' : 'ltr'
            });
        });
    }
    initDrawerSelect2();

    // Flatpickr
    if (window.flatpickr) {
        flatpickr('.flatpickr-date', {
            dateFormat: 'Y-m-d',
            allowInput: true,
            disableMobile: true,
            locale: document.documentElement.dir === 'rtl' && flatpickr.l10ns?.ar ? flatpickr.l10ns.ar : 'default'
        });

        flatpickr('.flatpickr-time', {
            enableTime: true,
            noCalendar: true,
            dateFormat: 'H:i',
            allowInput: true,
            disableMobile: true
        });
    }

    // Dynamic Cascade (Filter): Institution -> Branches
    $('#filter_institution_id').on('change', function () {
        const instId = $(this).val();
        const branchSelect = $('#filter_branch_id');
        branchSelect.empty().append(new Option("{{ __('All Branches') }}", ""));

        if (!instId) {
            branchSelect.trigger('change');
            return;
        }

        $.getJSON(branchesUrl + '/' + instId, function (data) {
            data.forEach(function (b) {
                branchSelect.append(new Option(b.name, b.id));
            });
            branchSelect.trigger('change');
        });
    });

    // Dynamic Cascade (Filter): Branch -> Classes
    $('#filter_branch_id').on('change', function () {
        const branchId = $(this).val();
        const classSelect = $('#filter_class_id');
        classSelect.empty().append(new Option("{{ __('All Classes') }}", ""));

        if (!branchId) {
            classSelect.trigger('change');
            return;
        }

        $.getJSON(classesUrl + '/' + branchId, function (data) {
            data.forEach(function (c) {
                classSelect.append(new Option(c.name, c.id));
            });
            classSelect.trigger('change');
        });
    });

    // Dynamic Cascade (Drawer): Institution -> Branches
    $('#drawer_institution_id').on('change', function () {
        const instId = $(this).val();
        const branchSelect = $('#drawer_branch_id');
        const currentBranch = branchSelect.data('selected');
        branchSelect.empty().append(new Option("{{ __('Select Branch') }}", ""));

        if (!instId) {
            branchSelect.trigger('change');
            return;
        }

        $.getJSON(branchesUrl + '/' + instId, function (data) {
            data.forEach(function (b) {
                const opt = new Option(b.name, b.id, false, b.id == currentBranch);
                branchSelect.append(opt);
            });
            branchSelect.trigger('change');
        });
    });

    // Dynamic Cascade (Drawer): Branch -> Classes
    $('#drawer_branch_id').on('change', function () {
        const branchId = $(this).val();
        const classSelect = $('#drawer_academic_class_id');
        const currentClass = classSelect.data('selected');
        classSelect.empty().append(new Option("{{ __('All Classes in Branch') }}", ""));

        if (!branchId) {
            classSelect.trigger('change');
            return;
        }

        $.getJSON(classesUrl + '/' + branchId, function (data) {
            data.forEach(function (c) {
                const opt = new Option(c.name, c.id, false, c.id == currentClass);
                classSelect.append(opt);
            });
            classSelect.trigger('change');
        });
    });

    // Initialize DataTable
    const table = $('#eventsTable').DataTable({
        processing: true,
        serverSide: true,
        pagingType: 'simple_numbers',
        ajax: {
            url: dataUrl,
            data: function (d) {
                d.institution_id = $('#filter_institution_id').val();
                d.branch_id = $('#filter_branch_id').val();
                d.academic_class_id = $('#filter_class_id').val();
                d.event_type_id = $('#filter_event_type_id').val();
                d.status = $('#filter_status').val();
            }
        },
        drawCallback: function (settings) {
            const json = settings.json;
            if (json) {
                $('#metric_total_events').text(json.recordsTotal || 0);
            }
        },
        columns: [
            {
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false,
                render: (data, type, row, meta) => `<strong class="text-muted">#${data || (meta.row + 1 + (meta.settings._iDisplayStart || 0))}</strong>`
            },
            {
                data: 'image_url',
                name: 'image',
                orderable: false,
                searchable: false,
                render: data => data
                    ? `<img src="${data}" alt="Cover" class="rounded object-fit-cover border shadow-xs" width="42" height="42">`
                    : `<div class="avatar avatar-sm bg-label-primary rounded d-inline-flex align-items-center justify-content-center" style="width: 42px; height: 42px;"><i class="icon-base bx bx-party font-20"></i></div>`
            },
            {
                data: 'name_display',
                name: 'name',
                orderable: false,
                render: (data, type, row) => `
                    <div>
                        <a href="javascript:void(0);" class="fw-bold text-heading text-hover-primary d-block btn-edit-event" data-id="${row.id}">${data}</a>
                        ${row.name_en && row.name_en !== data ? `<small class="text-muted d-block font-sans">${row.name_en}</small>` : ''}
                    </div>`
            },
            {
                data: 'institution_name',
                name: 'institution_id',
                orderable: false,
                render: (data, type, row) => `
                    <div>
                        <span class="fw-semibold text-heading small">${data}</span>
                        <small class="text-muted d-block"><i class="bx bx-map-pin font-12 me-1"></i>${row.branch_name || '-'}</small>
                    </div>`
            },
            {
                data: 'class_name',
                name: 'academic_class_id',
                orderable: false,
                render: data => (data === "{{ __('All Classes') }}")
                    ? `<span class="badge bg-label-secondary font-monospace"><i class="bx bx-layer me-1 font-12"></i>${data}</span>`
                    : `<span class="badge bg-label-primary"><i class="bx bx-chalkboard me-1 font-12"></i>${data}</span>`
            },
            {
                data: 'event_type_name',
                name: 'event_type_id',
                orderable: false,
                render: data => `<span class="badge bg-label-info fw-semibold"><i class="bx bx-tag-alt me-1 font-12"></i>${data}</span>`
            },
            {
                data: 'dates_formatted',
                name: 'start_date',
                render: data => `
                    <div class="small">
                        <span class="fw-medium text-heading">${data?.full || '-'}</span>
                    </div>`
            },
            {
                data: 'price',
                name: 'price',
                render: data => (parseFloat(data) > 0)
                    ? `<span class="badge bg-label-success fw-bold">${data} {{ __('KWD') }}</span>`
                    : `<span class="badge bg-label-secondary">{{ __('Free') }}</span>`
            },
            {
                data: 'status',
                name: 'status',
                className: 'text-center',
                render: (data, type, row) => {
                    const isChecked = (data === 'active') ? 'checked' : '';
                    let badgeClass = 'bg-label-success';
                    let label = "{{ __('Active') }}";

                    if (data === 'inactive') {
                        badgeClass = 'bg-label-danger';
                        label = "{{ __('Inactive') }}";
                    } else if (data === 'upcoming') {
                        badgeClass = 'bg-label-warning';
                        label = "{{ __('Upcoming') }}";
                    } else if (data === 'completed') {
                        badgeClass = 'bg-label-secondary';
                        label = "{{ __('Completed') }}";
                    }

                    return `
                        <div class="d-flex flex-column align-items-center gap-1">
                            <span class="badge ${badgeClass} mb-1">${label}</span>
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input btn-toggle-event-status" type="checkbox" role="switch" data-id="${row.id}" ${isChecked}>
                            </div>
                        </div>`;
                }
            },
            {
                data: 'actions',
                orderable: false,
                searchable: false,
                className: 'text-center',
                render: (actions, type, row) => `
                    <div class="d-inline-flex gap-1">
                        <a href="${actions.show_url}" class="btn btn-icon btn-sm btn-outline-info" title="{{ __('View') }}">
                            <i class="icon-base bx bx-show"></i>
                        </a>
                        <button type="button" class="btn btn-icon btn-sm btn-outline-primary btn-edit-event"
                                title="{{ __('Edit') }}"
                                data-id="${row.id}">
                            <i class="icon-base bx bx-edit-alt"></i>
                        </button>
                        <button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-delete-event"
                                title="{{ __('Delete') }}"
                                data-id="${row.id}"
                                data-url="${actions.delete_url}"
                                data-name="${row.name_display}">
                            <i class="icon-base bx bx-trash"></i>
                        </button>
                    </div>`
            }
        ],
        order: [[6, 'desc']]
    });

    // Filter changes reload
    $('#filter_institution_id, #filter_branch_id, #filter_class_id, #filter_event_type_id, #filter_status').on('change', function () {
        table.ajax.reload();
    });

    // Reset Filters
    $('#btn_reset_filters').on('click', function () {
        $('#filter_institution_id').val('').trigger('change');
        $('#filter_branch_id').val('').trigger('change');
        $('#filter_class_id').val('').trigger('change');
        $('#filter_event_type_id').val('').trigger('change');
        $('#filter_status').val('').trigger('change');
        table.ajax.reload();
    });

    // Image Preview Handling in Drawer
    const drawerImageInput = document.getElementById('drawer_image_input');
    const drawerImagePreview = document.getElementById('drawer_image_preview');
    const drawerPlaceholderBox = document.getElementById('drawer_placeholder_box');

    if (drawerImageInput) {
        drawerImageInput.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    drawerImagePreview.src = e.target.result;
                    drawerImagePreview.classList.remove('d-none');
                    drawerPlaceholderBox.classList.add('d-none');
                };
                reader.readAsDataURL(file);
            }
        });
    }

    function resetDrawerForm() {
        $('#eventForm')[0].reset();
        $('#event_id').val('');
        $('#event_method').val('POST');
        $('#offcanvas_alert_error').addClass('d-none');
        $('#offcanvas_error_list').empty();

        drawerImagePreview.src = '';
        drawerImagePreview.classList.add('d-none');
        drawerPlaceholderBox.classList.remove('d-none');

        $('#drawer_institution_id').val('').data('selected', '').trigger('change');
        $('#drawer_branch_id').val('').data('selected', '').trigger('change');
        $('#drawer_academic_class_id').val('').data('selected', '').trigger('change');
        $('#drawer_event_type_id').val('').trigger('change');
        $('#drawer_status').val('active');
        $('#drawer_price').val('0.000');
    }

    // Open Drawer for Add Event
    $('#btn_open_add_event').on('click', function () {
        resetDrawerForm();
        $('#offcanvasEventTitleText').text("{{ __('Add New Event') }}");
        $('#drawer_submit_text').text("{{ __('Save Event') }}");
        if (offcanvasInstance) offcanvasInstance.show();
    });

    // Open Drawer for Edit Event
    $(document).on('click', '.btn-edit-event', function () {
        const id = $(this).data('id');
        resetDrawerForm();
        $('#offcanvasEventTitleText').text("{{ __('Edit Event') }}");
        $('#drawer_submit_text').text("{{ __('Update Event') }}");
        $('#event_id').val(id);
        $('#event_method').val('PUT');

        $.ajax({
            url: baseUrl + '/' + id + '/edit',
            type: 'GET',
            headers: { 'Accept': 'application/json' },
            success: function (res) {
                $('#drawer_name_ar').val(res.name_ar || '');
                $('#drawer_name_en').val(res.name_en || '');
                $('#drawer_description_ar').val(res.description_ar || '');
                $('#drawer_description_en').val(res.description_en || '');
                $('#drawer_start_date').val(res.start_date || '');
                $('#drawer_end_date').val(res.end_date || '');
                $('#drawer_start_time').val(res.start_time || '');
                $('#drawer_end_time').val(res.end_time || '');
                $('#drawer_price').val(res.price || '0.000');
                $('#drawer_status').val(res.status || 'active');

                if (res.image_url) {
                    drawerImagePreview.src = res.image_url;
                    drawerImagePreview.classList.remove('d-none');
                    drawerPlaceholderBox.classList.add('d-none');
                }

                $('#drawer_event_type_id').val(res.event_type_id).trigger('change');

                // Cascade setting with preselected values
                $('#drawer_branch_id').data('selected', res.branch_id);
                $('#drawer_academic_class_id').data('selected', res.academic_class_id);
                $('#drawer_institution_id').val(res.institution_id).trigger('change');

                if (offcanvasInstance) offcanvasInstance.show();
            },
            error: function () {
                alert("{{ __('Failed to load event data.') }}");
            }
        });
    });

    // Handle Event Form Submission (AJAX via FormData)
    $('#eventForm').on('submit', function (e) {
        e.preventDefault();
        const form = this;
        const id = $('#event_id').val();
        const isUpdate = (id !== '');
        const submitBtn = $('#drawer_submit_btn');
        const errorAlert = $('#offcanvas_alert_error');
        const errorList = $('#offcanvas_error_list');

        errorAlert.addClass('d-none');
        errorList.empty();
        submitBtn.prop('disabled', true);

        const formData = new FormData(form);
        const targetUrl = isUpdate ? (baseUrl + '/' + id) : storeUrl;

        $.ajax({
            url: targetUrl,
            type: 'POST', // with _method=PUT inside formData for update
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            success: function (res) {
                if (offcanvasInstance) offcanvasInstance.hide();
                table.ajax.reload(null, false);

                $('#page_alert_container').html(`
                    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
                        <i class="bx bx-check-circle me-2 font-20"></i>
                        <div>${res.message || "{{ __('Saved successfully.') }}"}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `);
            },
            error: function (xhr) {
                errorAlert.removeClass('d-none');
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    const errs = xhr.responseJSON.errors;
                    Object.keys(errs).forEach(function (key) {
                        errs[key].forEach(function (msg) {
                            errorList.append(`<li>${msg}</li>`);
                        });
                    });
                } else {
                    errorList.append(`<li>{{ __('Error saving event. Please check inputs and try again.') }}</li>`);
                }
            },
            complete: function () {
                submitBtn.prop('disabled', false);
            }
        });
    });

    // Delete Event Handling
    let deleteTargetUrl = '';
    const deleteModalEl = document.getElementById('deleteEventModal');
    const deleteModal = deleteModalEl ? new bootstrap.Modal(deleteModalEl) : null;
    const deleteTargetName = document.getElementById('delete_target_name');

    $(document).on('click', '.btn-delete-event', function () {
        deleteTargetUrl = $(this).data('url');
        deleteTargetName.textContent = $(this).data('name') || '';
        if (deleteModal) deleteModal.show();
    });

    $('#btn_confirm_delete').on('click', function () {
        if (!deleteTargetUrl) return;
        const btn = $(this);
        btn.prop('disabled', true);

        $.ajax({
            url: deleteTargetUrl,
            type: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            success: function (res) {
                if (deleteModal) deleteModal.hide();
                table.ajax.reload();
            },
            error: function () {
                alert("{{ __('Failed to delete event. Please try again.') }}");
            },
            complete: function () {
                btn.prop('disabled', false);
            }
        });
    });

    // Toggle Status Handling
    $(document).on('change', '.btn-toggle-event-status', function () {
        const id = $(this).data('id');
        const checkbox = $(this);
        $.ajax({
            url: baseUrl + '/' + id + '/toggle-status',
            type: 'PATCH',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            success: function () {
                table.ajax.reload(null, false);
            },
            error: function () {
                checkbox.prop('checked', !checkbox.prop('checked'));
                alert("{{ __('Failed to update status. Please try again.') }}");
            }
        });
    });
});
</script>
@endsection
