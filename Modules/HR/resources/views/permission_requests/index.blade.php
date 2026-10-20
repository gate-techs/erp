@extends('layouts/contentNavbarLayout')

@section('title', __('Permissions & Short Leaves') . ' - ' . config('variables.templateName'))

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <i class="icon-base bx bx-time-five text-primary font-24"></i>
            <span>{{ __('Permissions & Short Leaves') }}</span>
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('Human Resources') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Permissions & Short Leaves') }}</li>
            </ol>
        </nav>
    </div>

    <div>
        @can('hr.permissions.create')
        <button type="button" class="btn btn-primary d-flex align-items-center justify-content-center gap-2 shadow-sm" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddPermission" aria-controls="offcanvasAddPermission">
            <i class="icon-base bx bx-plus font-18"></i>
            <span>{{ __('New Permission Request') }}</span>
        </button>
        @endcan
    </div>
</div>

<!-- KPI Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card kpi-card shadow-sm border-0 h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1">{{ __('Total Requests This Month') }}</span>
                    <h3 class="fw-bold mb-0 text-primary">{{ $kpis['total_month'] ?? 0 }}</h3>
                </div>
                <div class="avatar avatar-md bg-label-primary rounded p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-calendar font-22"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card kpi-card shadow-sm border-0 h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1">{{ __('Pending Approval') }}</span>
                    <h3 class="fw-bold mb-0 text-warning">{{ $kpis['pending'] ?? 0 }}</h3>
                </div>
                <div class="avatar avatar-md bg-label-warning rounded p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-hourglass font-22"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card kpi-card shadow-sm border-0 h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1">{{ __('Hours Consumed This Month') }}</span>
                    <h3 class="fw-bold mb-0 text-info">{{ number_format($kpis['consumed_hours_month'] ?? $kpis['hours_month'] ?? 0, 1) }} <span class="fs-6 fw-normal text-muted">{{ __('Hours') }}</span></h3>
                </div>
                <div class="avatar avatar-md bg-label-info rounded p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-stopwatch font-22"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card kpi-card shadow-sm border-0 h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1">{{ __('Approved This Month') }}</span>
                    <h3 class="fw-bold mb-0 text-success">{{ $kpis['approved_month'] ?? 0 }}</h3>
                </div>
                <div class="avatar avatar-md bg-label-success rounded p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-check-double font-22"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Standard Filters Card -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body py-3">
        <div class="row g-3 align-items-end">
            <!-- 1. Branch Filter -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Branch') }}</label>
                <select id="filter_branch_id" class="select2 form-select" data-placeholder="{{ __('All Branches') }}" data-allow-clear="true">
                    <option value="">{{ __('All Branches') }}</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->translate(app()->getLocale())?->name ?? $branch->translate('ar')?->name ?? $branch->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 2. Department Filter -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Department') }}</label>
                <select id="filter_department_id" class="select2 form-select" data-placeholder="{{ __('All Departments') }}" data-allow-clear="true">
                    <option value="">{{ __('All Departments') }}</option>
                    @foreach ($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->translate(app()->getLocale())?->name ?? $dept->translate('ar')?->name ?? $dept->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 3. Employee Filter -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Employee') }}</label>
                <select id="filter_employee_id" class="select2 form-select" data-placeholder="{{ __('All Employees') }}" data-allow-clear="true">
                    <option value="">{{ __('All Employees') }}</option>
                    @foreach ($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->name }} ({{ $emp->employee_code }})</option>
                    @endforeach
                </select>
            </div>

            <!-- 4. Permission Type Filter -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Permission Type') }}</label>
                <select id="filter_type" class="select2 form-select" data-placeholder="{{ __('All Types') }}" data-allow-clear="true">
                    <option value="">{{ __('All Types') }}</option>
                    @foreach ($permissionTypes as $type)
                        <option value="{{ $type->value }}">{{ $type->label() }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 5. Status Filter -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Status') }}</label>
                <select id="filter_status" class="select2 form-select" data-placeholder="{{ __('All Statuses') }}" data-allow-clear="true">
                    <option value="">{{ __('All Statuses') }}</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}">{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 6. Date From Filter -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Date From') }}</label>
                <input type="date" id="filter_date_from" class="form-control js-flatpickr-date">
            </div>

            <!-- 7. Date To Filter -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Date To') }}</label>
                <input type="date" id="filter_date_to" class="form-control js-flatpickr-date">
            </div>

            <!-- 8. Reset Filters Action Button -->
            <div class="col-12 col-sm-6 col-lg-3 d-flex align-items-end">
                <button type="button" id="btnResetFilters" class="btn btn-label-secondary btn-sm w-100 d-flex align-items-center justify-content-center gap-1 shadow-xs" style="height: 38px;">
                    <i class="icon-base bx bx-reset font-18"></i>
                    <span class="fw-semibold">{{ __('Reset Filters') }}</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Permission Requests DataTable Card -->
<div class="card shadow-sm border-0">
    <div class="card-datatable table-responsive">
        <table id="permissionRequestsTable" class="table table-hover border-top w-100 text-nowrap">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">#</th>
                    <th>{{ __('Employee') }}</th>
                    <th>{{ __('Permission Type') }}</th>
                    <th>{{ __('Date & Time') }}</th>
                    <th>{{ __('Hours') }}</th>
                    <th class="text-center">{{ __('Attachment') }}</th>
                    <th class="text-center">{{ __('Status') }}</th>
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
<!-- Offcanvas: Submit New Permission Request                                  -->
<!-- ========================================================================= -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="offcanvasAddPermission" aria-labelledby="offcanvasAddPermissionLabel" style="width: 540px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3 px-4">
        <h5 id="offcanvasAddPermissionLabel" class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
            <i class="icon-base bx bx-time-five text-primary font-22"></i>
            <span>{{ __('New Permission Request') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-4">
        <form id="permissionRequestForm" action="{{ route('hr.permissions.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Employee Selection -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading" for="modal_employee_id">{{ __('Employee') }} <span class="text-danger">*</span></label>
                <select id="modal_employee_id" name="employee_id" class="form-select select2" data-placeholder="{{ __('Select Employee...') }}" required>
                    <option value="">{{ __('Select Employee...') }}</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}">
                            {{ $emp->name }} ({{ $emp->employee_code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Date -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading" for="modal_date">{{ __('Date') }} <span class="text-danger">*</span></label>
                <input type="date" id="modal_date" name="date" class="form-control js-flatpickr-date" value="{{ date('Y-m-d') }}" required>
            </div>

            <!-- Permission Type & Emergency Flag -->
            <div class="row g-2 mb-3">
                <div class="col-12 col-sm-8">
                    <label class="form-label required fw-semibold text-heading" for="modal_type">{{ __('Permission Type') }} <span class="text-danger">*</span></label>
                    <select id="modal_type" name="type" class="form-select" required>
                        @foreach ($permissionTypes as $type)
                            <option value="{{ $type->value }}">{{ $type->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-sm-4 d-flex align-items-end pb-1">
                    <div class="form-check form-switch mb-1">
                        <input class="form-check-input" type="checkbox" id="modal_is_emergency" name="is_emergency" value="1">
                        <label class="form-check-label fw-semibold text-danger small" for="modal_is_emergency">
                            {{ __('Emergency Permission') }}
                        </label>
                    </div>
                </div>
            </div>

            <!-- Time Range (From Time & To Time) -->
            <div class="row g-2 mb-3">
                <div class="col-6">
                    <label class="form-label required fw-semibold text-heading" for="modal_from_time">{{ __('From Time') }} <span class="text-danger">*</span></label>
                    <input type="time" id="modal_from_time" name="from_time" class="form-control" value="08:00" required>
                </div>
                <div class="col-6">
                    <label class="form-label required fw-semibold text-heading" for="modal_to_time">{{ __('To Time') }} <span class="text-danger">*</span></label>
                    <input type="time" id="modal_to_time" name="to_time" class="form-control" value="10:00" required>
                </div>
            </div>

            <!-- Duration Indicator -->
            <div class="d-flex align-items-center justify-content-between p-2 rounded bg-label-primary mb-3">
                <span class="small fw-semibold text-heading">{{ __('Calculated Duration:') }}</span>
                <span id="calculatedDurationBadge" class="badge bg-primary fs-6">2.0 {{ __('Hours') }}</span>
            </div>

            <!-- Live Policy & Balance Container -->
            <div class="mb-3">
                <div id="liveBalanceContainer" class="live-balance-card p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <i class="icon-base bx bx-info-circle text-primary font-20"></i>
                            <div>
                                <h6 class="mb-0 fw-bold small" id="balanceLabel">{{ __('Permission Monthly Balance') }}</h6>
                                <small class="text-muted" id="balanceSubtext">{{ __('Select an employee to verify available monthly quota.') }}</small>
                            </div>
                        </div>
                    </div>

                    <div id="balanceNumbers" class="row g-2 text-center mt-1" style="display: none !important;">
                        <div class="col-4">
                            <div class="p-2 border rounded bg-white">
                                <small class="text-muted d-block">{{ __('Max Hours') }}</small>
                                <strong class="text-heading" id="valMaxHours">0</strong> <small>{{ __('h') }}</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 border rounded bg-white">
                                <small class="text-muted d-block">{{ __('Consumed') }}</small>
                                <strong class="text-warning" id="valConsumedHours">0</strong> <small>{{ __('h') }}</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 border rounded bg-white">
                                <small class="text-muted d-block">{{ __('Remaining') }}</small>
                                <strong class="text-success" id="valRemainingHours">0</strong> <small>{{ __('h') }}</small>
                            </div>
                        </div>
                    </div>

                    <div id="balanceCountInfo" class="small text-muted mt-2 text-center" style="display: none;">
                        <span>{{ __('Monthly Permissions Count:') }}</span> <strong id="valCountUsed">0</strong> / <strong id="valMaxCount">0</strong>
                    </div>

                    <div id="balanceAlertBox" class="alert alert-danger py-2 px-3 mt-2 mb-0 small d-none" role="alert"></div>
                </div>
            </div>

            <!-- Reason -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading" for="modal_reason">{{ __('Reason') }} <span class="text-danger">*</span></label>
                <textarea id="modal_reason" name="reason" class="form-control" rows="2" placeholder="{{ __('State the reason for this permission request...') }}" required></textarea>
            </div>

            <!-- Attachment File -->
            <div class="mb-4">
                <label class="form-label fw-semibold text-heading" for="modal_attachment">
                    {{ __('Attachment (Optional)') }}
                </label>
                <input type="file" id="modal_attachment" name="attachment" class="form-control" accept=".pdf,.png,.jpg,.jpeg">
                <div class="form-text small text-muted">{{ __('Supported formats: PDF, JPG, PNG (Max 5MB)') }}</div>
            </div>

            <!-- Form Action Buttons -->
            <div class="d-flex align-items-center justify-content-end gap-2 pt-2 border-top">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm" id="btnSubmitPermission">
                    <i class="icon-base bx bx-send font-18"></i>
                    <span>{{ __('Submit Request') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- Modal: View Permission Details                                            -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalViewPermissionDetails" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title fw-bold d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-file text-primary font-22"></i>
                    <span>{{ __('Permission Request Details') }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="viewPermissionDetailsBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">{{ __('Loading...') }}</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top py-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- Modal: Reject Permission Request                                          -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalRejectPermission" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <form id="rejectPermissionForm" class="modal-content">
            @csrf
            <input type="hidden" id="reject_permission_id">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title fw-bold text-danger d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-x-circle font-22"></i>
                    <span>{{ __('Reject Permission Request') }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted small mb-3">
                    {{ __('Please provide the formal reason for rejecting this permission request. The employee will be notified.') }}
                </p>
                <div class="mb-3">
                    <label class="form-label fw-semibold text-heading">{{ __('Rejection Reason') }} <span class="text-danger">*</span></label>
                    <textarea id="reject_reason_text" name="rejection_reason" class="form-control" rows="3" placeholder="{{ __('Specify rejection reason...') }}" required></textarea>
                </div>
            </div>
            <div class="modal-footer border-top py-2">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-danger d-flex align-items-center gap-1" id="btnConfirmReject">
                    <i class="icon-base bx bx-x font-18"></i>
                    <span>{{ __('Confirm Rejection') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const $ = window.jQuery || window.$;
    const dataUrl = "{{ route('hr.permissions.data') }}";
    const baseUrl = "{{ url('hr/permissions') }}";

    // 1. Initialize Server-Side DataTable
    const table = $('#permissionRequestsTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        pagingType: 'simple_numbers',
        ajax: {
            url: dataUrl,
            data: function(d) {
                d.branch_id     = $('#filter_branch_id').val();
                d.department_id = $('#filter_department_id').val();
                d.employee_id   = $('#filter_employee_id').val();
                d.type          = $('#filter_type').val();
                d.status        = $('#filter_status').val();
                d.date_from     = $('#filter_date_from').val();
                d.date_to       = $('#filter_date_to').val();
            }
        },
        columns: [
            { 
                data: 'DT_RowIndex', 
                name: 'DT_RowIndex', 
                orderable: false, 
                searchable: false,
                className: 'text-center align-middle',
                render: (data, type, row, meta) => `<strong class="text-muted">#${data || (meta.row + 1 + (meta.settings._iDisplayStart || 0))}</strong>`
            },
            { data: 'employee_info', name: 'employee.translations.first_name', className: 'align-middle' },
            { data: 'type_badge', name: 'type', className: 'align-middle' },
            { data: 'date_time', name: 'date', className: 'align-middle' },
            { data: 'hours_count', name: 'hours_count', className: 'align-middle' },
            { data: 'attachment_btn', name: 'attachment', orderable: false, searchable: false, className: 'text-center align-middle' },
            { data: 'status_badge', name: 'status', className: 'text-center align-middle' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center align-middle' }
        ],
        order: [[3, 'desc']],
        drawCallback: function () {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('#permissionRequestsTable [data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    });

    // 2. Custom Filters Trigger Reload with debounce & reset guard
    let isResetting = false;

    $('#filter_branch_id, #filter_department_id, #filter_employee_id, #filter_type, #filter_status, #filter_date_from, #filter_date_to').on('change', function () {
        if (isResetting) return;
        table.ajax.reload();
    });

    // Ensure global select2 initializer is executed if present
    if (typeof window.initSelect2 === 'function') {
        window.initSelect2();
    } else if ($.fn.select2) {
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%',
            dropdownParent: $('#offcanvasAddPermission').is(':visible') ? $('#offcanvasAddPermission') : null
        });
    }

    // Reset Filters Handler
    $('#btnResetFilters').on('click', function () {
        isResetting = true;
        $('#filter_branch_id, #filter_department_id, #filter_employee_id, #filter_type, #filter_status').val('').trigger('change');
        $('#filter_date_from, #filter_date_to').val('');
        isResetting = false;
        table.ajax.reload();
    });

    // 3. Real-Time Calculation of Hours Duration
    function calculateHoursDuration() {
        const fromTime = $('#modal_from_time').val();
        const toTime   = $('#modal_to_time').val();

        if (fromTime && toTime) {
            const [fromH, fromM] = fromTime.split(':').map(Number);
            const [toH, toM]     = toTime.split(':').map(Number);

            const fromTotalMinutes = fromH * 60 + fromM;
            const toTotalMinutes   = toH * 60 + toM;

            let diffMinutes = toTotalMinutes - fromTotalMinutes;
            if (diffMinutes < 0) {
                diffMinutes += 24 * 60; // Crosses midnight
            }

            const hours = (diffMinutes / 60).toFixed(2);
            $('#calculatedDurationBadge').text(hours + " {{ __('Hours') }}");
            return parseFloat(hours);
        }
        return 0;
    }

    $('#modal_from_time, #modal_to_time').on('change', function() {
        calculateHoursDuration();
        checkLiveBalance();
    });

    // 4. Real-Time Balance & Eligibility Live Checking
    function checkLiveBalance() {
        const empId       = $('#modal_employee_id').val();
        const date        = $('#modal_date').val();
        const fromTime    = $('#modal_from_time').val();
        const toTime      = $('#modal_to_time').val();
        const isEmergency = $('#modal_is_emergency').is(':checked') ? 1 : 0;

        if (!empId) {
            $('#balanceNumbers').attr('style', 'display: none !important;');
            $('#balanceCountInfo').hide();
            $('#balanceLabel').text("{{ __('Permission Monthly Balance') }}");
            $('#balanceSubtext').text("{{ __('Select an employee to verify available monthly quota.') }}");
            $('#balanceAlertBox').addClass('d-none');
            $('#liveBalanceContainer').removeClass('danger warning-state active');
            return;
        }

        $.ajax({
            url: "{{ route('hr.permissions.check-balance') }}",
            type: 'GET',
            data: {
                employee_id: empId,
                date: date,
                from_time: fromTime,
                to_time: toTime,
                is_emergency: isEmergency
            },
            success: function(res) {
                if (res.success && res.balance) {
                    const b = res.balance;
                    $('#balanceNumbers').attr('style', 'display: flex !important;');
                    $('#valMaxHours').text(b.max_monthly_hours);
                    $('#valConsumedHours').text(b.consumed_hours);
                    $('#valRemainingHours').text(b.remaining_hours);

                    $('#balanceCountInfo').show();
                    $('#valCountUsed').text(b.consumed_count);
                    $('#valMaxCount').text(b.max_monthly_count);

                    $('#balanceLabel').text("{{ __('Remaining Available Quota:') }} " + b.remaining_hours + " {{ __('Hours') }}");
                    $('#balanceSubtext').text("{{ __('Max:') }} " + b.max_monthly_hours + " {{ __('Hours') }} | {{ __('Requests Used:') }} " + b.consumed_count + " / " + b.max_monthly_count);

                    // Check eligibility alert
                    if (!res.is_eligible) {
                        $('#balanceAlertBox').removeClass('d-none').text(res.message);
                        $('#liveBalanceContainer').removeClass('active warning-state').addClass('danger');
                    } else {
                        $('#balanceAlertBox').addClass('d-none');
                        $('#liveBalanceContainer').removeClass('danger warning-state').addClass('active');
                    }
                }
            },
            error: function() {
                // Silently keep current state
            }
        });
    }

    $('#modal_employee_id, #modal_date, #modal_type, #modal_is_emergency').on('change', checkLiveBalance);

    // 5. Submit Permission Request Form via AJAX
    $('#permissionRequestForm').on('submit', function(e) {
        e.preventDefault();
        const form = this;
        const btn  = $('#btnSubmitPermission');
        const originalText = btn.html();

        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status"></span> {{ __("Submitting...") }}');

        const formData = new FormData(form);

        $.ajax({
            url: form.action,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                btn.prop('disabled', false).html(originalText);
                if (res.success) {
                    // Close offcanvas
                    const offcanvasEl = document.getElementById('offcanvasAddPermission');
                    const offcanvasInstance = bootstrap.Offcanvas.getInstance(offcanvasEl);
                    if (offcanvasInstance) {
                        offcanvasInstance.hide();
                    }
                    form.reset();
                    $('#modal_employee_id').val('').trigger('change');
                    $('#modal_date').val("{{ date('Y-m-d') }}");
                    $('#modal_from_time').val('08:00');
                    $('#modal_to_time').val('10:00');
                    calculateHoursDuration();

                    table.ajax.reload();

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: '{{ __("Success") }}',
                            text: res.message,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        alert(res.message);
                    }
                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('{{ __("Error") }}', res.message || '{{ __("Failed to submit permission request.") }}', 'error');
                    } else {
                        alert(res.message || '{{ __("Failed to submit permission request.") }}');
                    }
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html(originalText);
                let msg = '{{ __("Validation Error.") }}';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                }

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: '{{ __("Unable to Submit") }}',
                        html: msg,
                        icon: 'error'
                    });
                } else {
                    alert(msg);
                }
            }
        });
    });

    // 6. View Permission Details Modal
    $(document).on('click', '.btn-view-permission', function() {
        const id = $(this).data('id');
        const modal = new bootstrap.Modal(document.getElementById('modalViewPermissionDetails'));
        modal.show();

        $('#viewPermissionDetailsBody').html(`
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">{{ __('Loading...') }}</span>
                </div>
            </div>
        `);

        $.ajax({
            url: baseUrl + '/' + id,
            type: 'GET',
            success: function(res) {
                const p = res.data || res.permission;
                if (res.success && p) {
                    const empName = (p.employee && p.employee.name) || p.employee_name || '-';
                    const permType = p.type_label || p.type || '-';
                    const creatorName = p.creator_name || (p.creator && p.creator.name) || '-';

                    $('#viewPermissionDetailsBody').html(`
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="icon-base bx bx-user text-primary font-20"></i>
                                    <div>
                                        <div class="small text-muted">{{ __('Employee') }}</div>
                                        <strong class="text-heading">${empName}</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="icon-base bx bx-category text-primary font-20"></i>
                                    <div>
                                        <div class="small text-muted">{{ __('Permission Type') }}</div>
                                        <div>${p.type_badge || permType}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="icon-base bx bx-calendar text-primary font-20"></i>
                                    <div>
                                        <div class="small text-muted">{{ __('Date') }}</div>
                                        <strong>${p.date}</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="icon-base bx bx-time text-primary font-20"></i>
                                    <div>
                                        <div class="small text-muted">{{ __('Time & Duration') }}</div>
                                        <strong>${p.from_time} &rarr; ${p.to_time}</strong>
                                        <span class="badge bg-primary ms-1">${p.hours_count} {{ __('Hours') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="small text-muted mb-1">{{ __('Status') }}</div>
                                <div>${p.status_badge}</div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="small text-muted mb-1">{{ __('Created By') }}</div>
                                <strong>${creatorName}</strong>
                            </div>
                            <div class="col-12">
                                <div class="small text-muted mb-1">{{ __('Reason / Notes') }}</div>
                                <p class="p-2 rounded bg-light mb-0">${p.reason || '{{ __("No reason specified") }}'}</p>
                            </div>
                            ${p.rejection_reason ? `
                                <div class="col-12">
                                    <div class="small text-danger fw-semibold mb-1">{{ __('Rejection Reason') }}</div>
                                    <p class="p-2 rounded bg-label-danger text-danger mb-0">${p.rejection_reason}</p>
                                </div>
                            ` : ''}
                            ${p.attachment_url ? `
                                <div class="col-12">
                                    <a href="${p.attachment_url}" target="_blank" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1">
                                        <i class="icon-base bx bx-paperclip"></i> {{ __('View Attachment') }}
                                    </a>
                                </div>
                            ` : ''}
                        </div>
                    `);
                } else {
                    $('#viewPermissionDetailsBody').html(`
                        <div class="alert alert-danger mb-0">
                            ${res.message || '{{ __("Failed to load details.") }}'}
                        </div>
                    `);
                }
            },
            error: function(xhr) {
                const msg = xhr.responseJSON?.message || '{{ __("Failed to load details.") }}';
                $('#viewPermissionDetailsBody').html(`
                    <div class="alert alert-danger mb-0">
                        ${msg}
                    </div>
                `);
            }
        });
    });

    // 7. Approve Action
    $(document).on('click', '.btn-approve-permission', function() {
        const id = $(this).data('id');
        const confirmMsg = "{{ __('Approve Permission Request?') }}";
        const subMsg = "{{ __('This will confirm the permission, deduct hours from monthly allowance, and synchronize with attendance records.') }}";

        const doApprove = function() {
            $.ajax({
                url: baseUrl + '/' + id + '/approve',
                type: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function(res) {
                    table.ajax.reload();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: '{{ __("Approved") }}',
                            text: res.message,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        alert(res.message);
                    }
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON?.message || '{{ __("Approval failed.") }}';
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('{{ __("Error") }}', msg, 'error');
                    } else {
                        alert(msg);
                    }
                }
            });
        };

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: confirmMsg,
                text: subMsg,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28c76f',
                cancelButtonColor: '#82868b',
                confirmButtonText: "{{ __('Yes, Approve') }}",
                cancelButtonText: "{{ __('Cancel') }}"
            }).then((result) => {
                if (result.isConfirmed) doApprove();
            });
        } else {
            if (confirm(confirmMsg + "\n" + subMsg)) doApprove();
        }
    });

    // 8. Reject Action
    $(document).on('click', '.btn-reject-permission', function() {
        const id = $(this).data('id');
        $('#reject_permission_id').val(id);
        $('#reject_reason_text').val('');
        const modal = new bootstrap.Modal(document.getElementById('modalRejectPermission'));
        modal.show();
    });

    $('#rejectPermissionForm').on('submit', function(e) {
        e.preventDefault();
        const id = $('#reject_permission_id').val();
        const reason = $('#reject_reason_text').val();
        const btn = $('#btnConfirmReject');
        btn.prop('disabled', true);

        $.ajax({
            url: baseUrl + '/' + id + '/reject',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                rejection_reason: reason
            },
            success: function(res) {
                btn.prop('disabled', false);
                const modalEl = document.getElementById('modalRejectPermission');
                const modalInstance = bootstrap.Modal.getInstance(modalEl);
                if (modalInstance) modalInstance.hide();
                table.ajax.reload();

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: '{{ __("Rejected") }}',
                        text: res.message,
                        icon: 'info',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    alert(res.message);
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false);
                const msg = xhr.responseJSON?.message || '{{ __("Rejection failed.") }}';
                if (typeof Swal !== 'undefined') {
                    Swal.fire('{{ __("Error") }}', msg, 'error');
                } else {
                    alert(msg);
                }
            }
        });
    });

    // 9. Delete Action
    $(document).on('click', '.btn-delete-permission', function() {
        const id = $(this).data('id');
        const confirmMsg = "{{ __('Delete Permission Request?') }}";
        const subMsg = "{{ __('Are you sure you want to delete this permission request? This action cannot be undone.') }}";

        const doDelete = function() {
            $.ajax({
                url: baseUrl + '/' + id,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function(res) {
                    table.ajax.reload();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: '{{ __("Deleted") }}',
                            text: res.message,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        alert(res.message);
                    }
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON?.message || '{{ __("Deletion failed.") }}';
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('{{ __("Error") }}', msg, 'error');
                    } else {
                        alert(msg);
                    }
                }
            });
        };

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: confirmMsg,
                text: subMsg,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#82868b',
                confirmButtonText: "{{ __('Yes, Delete') }}",
                cancelButtonText: "{{ __('Cancel') }}"
            }).then((result) => {
                if (result.isConfirmed) doDelete();
            });
        } else {
            if (confirm(confirmMsg + "\n" + subMsg)) doDelete();
        }
    });

    // Initial calculation for duration
    calculateHoursDuration();
});
</script>
@endsection
