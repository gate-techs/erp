@extends('layouts/contentNavbarLayout')

@section('title', __('Leave Requests') . ' - ' . config('variables.templateName'))

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <i class="icon-base bx bx-calendar-star text-primary font-24"></i>
            <span>{{ __('Leave Requests') }}</span>
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('Human Resources') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Leave Requests') }}</li>
            </ol>
        </nav>
    </div>

    <div>
        <button type="button" class="btn btn-primary d-flex align-items-center justify-content-center gap-2 shadow-sm" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddLeave" aria-controls="offcanvasAddLeave">
            <i class="icon-base bx bx-plus font-18"></i>
            <span>{{ __('New Leave Request') }}</span>
        </button>
    </div>
</div>

<!-- KPI Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card kpi-card shadow-sm border-0 h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1">{{ __('Total This Month') }}</span>
                    <h3 class="fw-bold mb-0 text-primary">{{ $kpis['total_this_month'] ?? $kpis['total_month'] ?? 0 }}</h3>
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
                    <i class="icon-base bx bx-time-five font-22"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card kpi-card shadow-sm border-0 h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1">{{ __('On Leave Today') }}</span>
                    <h3 class="fw-bold mb-0 text-info">{{ $kpis['on_leave_today'] ?? 0 }}</h3>
                </div>
                <div class="avatar avatar-md bg-label-info rounded p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-user-check font-22"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card kpi-card shadow-sm border-0 h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1">{{ __('Approved This Month') }}</span>
                    <h3 class="fw-bold mb-0 text-success">{{ $kpis['approved_this_month'] ?? $kpis['approved_month'] ?? 0 }}</h3>
                </div>
                <div class="avatar avatar-md bg-label-success rounded p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-check-double font-22"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Standard Filters Card (Exact Match with employees/index.blade.php) -->
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

            <!-- 4. Leave Type Filter -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Leave Type') }}</label>
                <select id="filter_leave_type" class="select2 form-select" data-placeholder="{{ __('All Types') }}" data-allow-clear="true">
                    <option value="">{{ __('All Types') }}</option>
                    @foreach ($leaveTypes as $type)
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
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Start Date From') }}</label>
                <input type="date" id="filter_date_from" class="form-control js-flatpickr-date">
            </div>

            <!-- 7. Date To Filter -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Start Date To') }}</label>
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

<!-- Leave Requests DataTable Card (Matching employees & banks layout) -->
<div class="card shadow-sm border-0">
    <div class="card-datatable table-responsive">
        <table id="leaveRequestsTable" class="table table-hover border-top w-100 text-nowrap">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">#</th>
                    <th>{{ __('Employee') }}</th>
                    <th>{{ __('Leave Type') }}</th>
                    <th>{{ __('Period & Duration') }}</th>
                    <th>{{ __('Replacement') }}</th>
                    <th class="text-center">{{ __('File') }}</th>
                    <th class="text-center">{{ __('Status') }}</th>
                    <th class="text-center" style="width: 110px;">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                <!-- Loaded via Server-Side DataTables -->
            </tbody>
        </table>
    </div>
</div>

<!-- ========================================================================= -->
<!-- Offcanvas: Submit New Leave Request (Matching attendances & banks layout) -->
<!-- ========================================================================= -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="offcanvasAddLeave" aria-labelledby="offcanvasAddLeaveLabel" style="width: 540px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3 px-4">
        <h5 id="offcanvasAddLeaveLabel" class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
            <i class="icon-base bx bx-calendar-plus text-primary font-22"></i>
            <span>{{ __('Submit New Leave Request') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-4">
        <form id="leaveRequestForm" action="{{ route('hr.leaves.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Employee Selection -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading" for="modal_employee_id">{{ __('Employee') }} <span class="text-danger">*</span></label>
                <select id="modal_employee_id" name="employee_id" class="form-select select2" data-placeholder="{{ __('Select Employee...') }}" required>
                    <option value="">{{ __('Select Employee...') }}</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" data-gender="{{ $emp->gender?->value ?? '' }}">
                            {{ $emp->name }} ({{ $emp->employee_code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Real-Time Balance Info Box -->
            <div class="mb-3">
                <div id="liveBalanceContainer" class="live-balance-card p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fw-semibold text-heading small d-flex align-items-center gap-1">
                            <i class="icon-base bx bx-wallet text-primary"></i>
                            <span id="balanceLabel">{{ __('Leave Balance & Eligibility') }}</span>
                        </span>
                        <span id="balanceSubtext" class="badge bg-label-secondary font-11">
                            {{ __('Select an employee to verify available leave balance.') }}
                        </span>
                    </div>
                    <div class="row g-2 text-center" id="balanceNumbers" style="display: none !important;">
                        <div class="col-4">
                            <div class="bg-white rounded p-1 border">
                                <span class="text-muted d-block font-11">{{ __('Total Entitled') }}</span>
                                <strong id="valTotalEntitled" class="text-primary font-14">0</strong>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="bg-white rounded p-1 border">
                                <span class="text-muted d-block font-11">{{ __('Consumed') }}</span>
                                <strong id="valConsumed" class="text-warning font-14">0</strong>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="bg-white rounded p-1 border">
                                <span class="text-muted d-block font-11">{{ __('Remaining') }}</span>
                                <strong id="valRemaining" class="text-success font-14">0</strong>
                            </div>
                        </div>
                    </div>
                    <div id="balanceAlertBox" class="alert alert-danger py-1 px-2 mt-2 mb-0 small d-none"></div>
                </div>
            </div>

            <!-- Leave Type -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading" for="modal_leave_type">{{ __('Leave Type') }} <span class="text-danger">*</span></label>
                <select id="modal_leave_type" name="leave_type" class="form-select select2" data-placeholder="{{ __('Select Leave Type...') }}" required>
                    @foreach($leaveTypes as $type)
                        <option value="{{ $type->value }}" data-requires-attachment="{{ $type->requiresAttachment() ? '1' : '0' }}">
                            {{ $type->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Date Range -->
            <div class="row g-3 mb-3">
                <div class="col-6">
                    <label class="form-label required fw-semibold text-heading">{{ __('Start Date') }} <span class="text-danger">*</span></label>
                    <input type="date" id="modal_start_date" name="start_date" class="form-control js-flatpickr-date" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-6">
                    <label class="form-label required fw-semibold text-heading">{{ __('End Date') }} <span class="text-danger">*</span></label>
                    <input type="date" id="modal_end_date" name="end_date" class="form-control js-flatpickr-date" value="{{ date('Y-m-d') }}" required>
                </div>
            </div>

            <!-- Live Net Working Days Calculation Result -->
            <div class="mb-3" id="daysCalcWrapper" style="display: none;">
                <div class="p-2 px-3 rounded bg-light border d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <span class="small text-muted">
                        <i class="bx bx-calculator text-primary me-1"></i>
                        <span>{{ __('Calendar Days:') }} <strong id="valCalendarDays">0</strong></span>
                        <span class="mx-2">•</span>
                        <span>{{ __('Weekends/Holidays Excluded:') }} <strong id="valExcludedDays">0</strong></span>
                    </span>
                    <span class="badge bg-primary font-12 px-2 py-1">
                        {{ __('Net Deducted Days:') }} <span id="valNetWorkingDays" class="fw-bold">0</span> {{ __('Days') }}
                    </span>
                </div>
            </div>

            <!-- Replacement Employee -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading" for="modal_replacement_employee_id">{{ __('Replacement / Handover Employee') }}</label>
                <select id="modal_replacement_employee_id" name="replacement_employee_id" class="form-select select2" data-placeholder="{{ __('None / Optional') }}" data-allow-clear="true">
                    <option value="">{{ __('None / Optional') }}</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Emergency Contact Phone -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Emergency Contact Phone') }}</label>
                <input type="text" id="modal_emergency_contact_phone" name="emergency_contact_phone" class="form-control" placeholder="+965 ...">
            </div>

            <!-- Attachment Upload -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading d-flex align-items-center justify-content-between">
                    <span>{{ __('Official Attachment / Medical Certificate') }} <span id="attachmentRequiredAsterisk" class="text-danger d-none">*</span></span>
                    <small class="text-muted">{{ __('PDF, JPG, PNG up to 5MB') }}</small>
                </label>
                <input type="file" id="modal_attachment" name="attachment" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
            </div>

            <!-- Reason / Notes -->
            <div class="mb-4">
                <label class="form-label fw-semibold text-heading">{{ __('Reason / Notes') }}</label>
                <textarea id="modal_reason" name="reason" rows="2" class="form-control" placeholder="{{ __('Optional reason or handover notes...') }}"></textarea>
            </div>

            <!-- Fixed Footer Actions -->
            <div class="d-flex align-items-center justify-content-end gap-2 pt-3 border-top">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-1" id="btnSubmitLeave">
                    <i class="icon-base bx bx-check font-18"></i>
                    <span>{{ __('Submit Leave Request') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- Modal: View Leave Details                                                 -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalViewLeaveDetails" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title fw-bold d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-file text-primary font-22"></i>
                    <span>{{ __('Leave Request Details') }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="viewDetailsBody">
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
<!-- Modal: Reject Leave Request                                               -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalRejectLeave" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <form id="rejectLeaveForm" class="modal-content">
            @csrf
            <input type="hidden" id="reject_leave_id">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title fw-bold text-danger d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-x-circle font-22"></i>
                    <span>{{ __('Reject Leave Request') }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted small mb-3">
                    {{ __('Please provide the formal reason for rejecting this leave request. The employee will be notified.') }}
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
    const dataUrl = "{{ route('hr.leaves.data') }}";
    const baseUrl = "{{ url('hr/leaves') }}";

    // 1. Initialize Server-Side DataTable (Exact match with employees & banks layout)
    const table = $('#leaveRequestsTable').DataTable({
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
                d.leave_type    = $('#filter_leave_type').val();
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
            { data: 'leave_type_badge', name: 'leave_type', className: 'align-middle' },
            { data: 'period', name: 'start_date', className: 'align-middle' },
            { data: 'replacement_info', name: 'replacementEmployee.translations.first_name', orderable: false, className: 'align-middle' },
            { data: 'attachment_btn', name: 'attachment', orderable: false, searchable: false, className: 'text-center align-middle' },
            { data: 'status_badge', name: 'status', className: 'text-center align-middle' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center align-middle' }
        ],
        order: [[3, 'desc']],
        drawCallback: function () {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('#leaveRequestsTable [data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    });

    // 3. Custom Filters Trigger Reload with debounce / reset guard
    let isResetting = false;

    $('#filter_branch_id, #filter_department_id, #filter_employee_id, #filter_leave_type, #filter_status, #filter_date_from, #filter_date_to').on('change', function () {
        if (isResetting) return;
        table.ajax.reload();
    });

    // Ensure global select2 initializer is executed if present
    if (typeof window.initSelect2 === 'function') {
        window.initSelect2();
    }

    // Reset Filters Handler
    $('#btnResetFilters').on('click', function () {
        isResetting = true;
        $('#filter_branch_id, #filter_department_id, #filter_employee_id, #filter_leave_type, #filter_status').val('').trigger('change');
        $('#filter_date_from, #filter_date_to').val('');
        isResetting = false;
        table.ajax.reload();
    });

    // 4. Real-Time Balance & Working Days Calculation
    function checkLiveBalance() {
        const empId   = $('#modal_employee_id').val();
        const type    = $('#modal_leave_type').val();
        const start   = $('#modal_start_date').val();
        const end     = $('#modal_end_date').val();

        // Update attachment requirement indicator
        const reqAtt = $('#modal_leave_type option:selected').data('requires-attachment') == '1';
        if (reqAtt) {
            $('#attachmentRequiredAsterisk').removeClass('d-none');
        } else {
            $('#attachmentRequiredAsterisk').addClass('d-none');
        }

        if (!empId) {
            $('#balanceNumbers').attr('style', 'display: none !important;');
            $('#balanceLabel').text("{{ __('Leave Balance & Eligibility') }}");
            $('#balanceSubtext').text("{{ __('Select an employee to verify available leave balance.') }}");
            $('#balanceAlertBox').addClass('d-none');
            $('#daysCalcWrapper').hide();
            return;
        }

        $.ajax({
            url: "{{ route('hr.leaves.check-balance') }}",
            type: 'GET',
            data: {
                employee_id: empId,
                leave_type: type,
                start_date: start,
                end_date: end
            },
            success: function(res) {
                if (res.success && res.balance) {
                    const b = res.balance;
                    $('#balanceNumbers').attr('style', 'display: flex !important;');
                    $('#valTotalEntitled').text(b.total_entitlement);
                    $('#valConsumed').text(b.consumed_days);
                    $('#valRemaining').text(b.remaining_days);

                    $('#balanceLabel').text("{{ __('Remaining Available Balance:') }} " + b.remaining_days + " {{ __('Days') }}");
                    $('#balanceSubtext').text("{{ __('Total Entitled:') }} " + b.total_entitlement + " | {{ __('Consumed:') }} " + b.consumed_days);

                    // Check eligibility alert
                    if (!b.is_eligible) {
                        $('#balanceAlertBox').removeClass('d-none').text(b.ineligibility_reason);
                        $('#liveBalanceContainer').removeClass('active').addClass('danger');
                    } else {
                        $('#balanceAlertBox').addClass('d-none');
                        $('#liveBalanceContainer').removeClass('danger').addClass('active');
                    }
                }

                // Days calculation
                if (res.success && res.days_calc) {
                    const d = res.days_calc;
                    $('#valCalendarDays').text(d.calendar_days);
                    $('#valExcludedDays').text(d.weekend_days + d.holiday_days);
                    $('#valNetWorkingDays').text(d.working_days);
                    $('#daysCalcWrapper').show();
                } else {
                    $('#daysCalcWrapper').hide();
                }
            },
            error: function() {
                $('#daysCalcWrapper').hide();
            }
        });
    }

    $('#modal_employee_id, #modal_leave_type').on('change', checkLiveBalance);
    $('#modal_start_date, #modal_end_date').on('change', checkLiveBalance);

    // 5. Submit Leave Request Form
    $('#leaveRequestForm').on('submit', function(e) {
        e.preventDefault();
        const form = this;
        const btn  = $('#btnSubmitLeave');
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
                    const offcanvasEl = document.getElementById('offcanvasAddLeave');
                    const offcanvasInstance = bootstrap.Offcanvas.getInstance(offcanvasEl);
                    if (offcanvasInstance) {
                        offcanvasInstance.hide();
                    }
                    form.reset();
                    $('#modal_employee_id, #modal_replacement_employee_id').val('').trigger('change');
                    $('#balanceNumbers').attr('style', 'display: none !important;');
                    $('#daysCalcWrapper').hide();
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
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html(originalText);
                const msg = xhr.responseJSON?.message || '{{ __("An error occurred while submitting the request.") }}';
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: '{{ __("Error") }}',
                        text: msg,
                        icon: 'error'
                    });
                } else {
                    alert(msg);
                }
            }
        });
    });

    // 6. View Details Action
    $(document).on('click', '.btn-view-leave', function() {
        const id = $(this).data('id');
        const modal = new bootstrap.Modal(document.getElementById('modalViewLeaveDetails'));
        $('#viewDetailsBody').html(`
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">{{ __('Loading...') }}</span>
                </div>
            </div>
        `);
        modal.show();

        $.ajax({
            url: baseUrl + '/' + id,
            type: 'GET',
            success: function(res) {
                const l = res.data || res.leave;
                if (res.success && l) {
                    let daysTableHtml = '';
                    const daysDetails = res.days_details || (res.calculation && res.calculation.details) || [];
                    if (daysDetails && daysDetails.length > 0) {
                        daysTableHtml = `
                            <div class="mt-3">
                                <h6 class="fw-bold mb-2 small text-muted text-uppercase d-flex align-items-center gap-1">
                                    <i class="icon-base bx bx-calendar-event text-primary"></i>
                                    <span>{{ __('Dates Breakdown') }}</span>
                                </h6>
                                <div class="table-responsive border rounded" style="max-height: 220px; overflow-y: auto;">
                                    <table class="table table-sm table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>{{ __('Date') }}</th>
                                                <th>{{ __('Day Classification') }}</th>
                                                <th>{{ __('Deduction Impact') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${daysDetails.map(item => {
                                                let typeBadge = '';
                                                let impactHtml = '';

                                                if (item.type === 'working_day') {
                                                    typeBadge = '<span class="badge bg-label-success d-inline-flex align-items-center gap-1"><i class="icon-base bx bx-briefcase font-12"></i>{{ __("Working Day") }}</span>';
                                                    impactHtml = '<span class="text-success fw-semibold small d-inline-flex align-items-center gap-1"><i class="icon-base bx bx-check"></i>{{ __("Deducted from balance (1 Day)") }}</span>';
                                                } else if (item.type === 'holiday') {
                                                    typeBadge = '<span class="badge bg-label-warning d-inline-flex align-items-center gap-1"><i class="icon-base bx bx-gift font-12"></i>{{ __("Official Holiday") }}</span>';
                                                    impactHtml = `<span class="text-warning fw-semibold small d-inline-flex align-items-center gap-1"><i class="icon-base bx bx-x"></i>${item.reason || ''} ({{ __("Not Deducted") }})</span>`;
                                                } else {
                                                    typeBadge = '<span class="badge bg-label-secondary d-inline-flex align-items-center gap-1"><i class="icon-base bx bx-coffee font-12"></i>{{ __("Weekly Rest Day") }}</span>';
                                                    impactHtml = '<span class="text-muted small d-inline-flex align-items-center gap-1"><i class="icon-base bx bx-minus"></i>{{ __("Rest Day (Not Deducted)") }}</span>';
                                                }

                                                return `
                                                    <tr>
                                                        <td class="fw-semibold text-heading">${item.date} (${item.day})</td>
                                                        <td>${typeBadge}</td>
                                                        <td>${impactHtml}</td>
                                                    </tr>
                                                `;
                                            }).join('')}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        `;
                    }

                    const empName = (l.employee && l.employee.name) || l.employee_name || '-';
                    const repName = (l.replacement_employee && l.replacement_employee.name) || l.replacement_employee_name || (typeof l.replacement_employee === 'string' ? l.replacement_employee : '{{ __("None") }}');
                    const leaveType = l.leave_type_label || l.leave_type || '-';

                    $('#viewDetailsBody').html(`
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
                                        <div class="small text-muted">{{ __('Leave Type') }}</div>
                                        <strong class="text-heading">${leaveType}</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="icon-base bx bx-calendar text-primary font-20"></i>
                                    <div>
                                        <div class="small text-muted">{{ __('Period & Duration') }}</div>
                                        <strong>${l.start_date} &rarr; ${l.end_date}</strong>
                                        <span class="badge bg-primary ms-1">${l.days_count} {{ __('Days') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="icon-base bx bx-user-check text-primary font-20"></i>
                                    <div>
                                        <div class="small text-muted">{{ __('Replacement') }}</div>
                                        <strong>${repName}</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="small text-muted mb-1">{{ __('Status') }}</div>
                                <div>${l.status_badge}</div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="small text-muted mb-1">{{ __('Emergency Contact Phone') }}</div>
                                <strong>${l.emergency_contact_phone || '-'}</strong>
                            </div>
                            <div class="col-12">
                                <div class="small text-muted mb-1">{{ __('Reason / Notes') }}</div>
                                <p class="p-2 rounded bg-light mb-0">${l.reason || '{{ __("No reason specified") }}'}</p>
                            </div>
                            ${l.rejection_reason ? `
                                <div class="col-12">
                                    <div class="small text-danger fw-semibold mb-1">{{ __('Rejection Reason') }}</div>
                                    <p class="p-2 rounded bg-label-danger text-danger mb-0">${l.rejection_reason}</p>
                                </div>
                            ` : ''}
                            ${l.attachment_url ? `
                                <div class="col-12">
                                    <a href="${l.attachment_url}" target="_blank" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1">
                                        <i class="icon-base bx bx-paperclip"></i> {{ __('View Attachment') }}
                                    </a>
                                </div>
                            ` : ''}
                        </div>
                        ${daysTableHtml}
                    `);
                } else {
                    $('#viewDetailsBody').html(`
                        <div class="alert alert-danger mb-0">
                            ${res.message || '{{ __("Failed to load details.") }}'}
                        </div>
                    `);
                }
            },
            error: function(xhr) {
                const msg = xhr.responseJSON?.message || '{{ __("Failed to load details.") }}';
                $('#viewDetailsBody').html(`
                    <div class="alert alert-danger mb-0">
                        ${msg}
                    </div>
                `);
            }
        });
    });

    // 7. Approve Action
    $(document).on('click', '.btn-approve-leave', function() {
        const id = $(this).data('id');
        const confirmMsg = "{{ __('Approve Leave Request?') }}";
        const subMsg = "{{ __('This will confirm the leave, deduct from employee balance, and synchronize with attendance records.') }}";

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
                    alert(msg);
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
    $(document).on('click', '.btn-reject-leave', function() {
        const id = $(this).data('id');
        $('#reject_leave_id').val(id);
        $('#reject_reason_text').val('');
        const modal = new bootstrap.Modal(document.getElementById('modalRejectLeave'));
        modal.show();
    });

    $('#rejectLeaveForm').on('submit', function(e) {
        e.preventDefault();
        const id = $('#reject_leave_id').val();
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
                const modalEl = document.getElementById('modalRejectLeave');
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
                alert(msg);
            }
        });
    });

    // 9. Delete Action
    $(document).on('click', '.btn-delete-leave', function() {
        const id = $(this).data('id');
        const confirmMsg = "{{ __('Are you sure you want to delete this pending leave request?') }}";

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
                    alert(msg);
                }
            });
        };

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: '{{ __("Confirm Delete") }}',
                text: confirmMsg,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ea5455',
                cancelButtonColor: '#82868b',
                confirmButtonText: "{{ __('Yes, Delete') }}",
                cancelButtonText: "{{ __('Cancel') }}"
            }).then((result) => {
                if (result.isConfirmed) doDelete();
            });
        } else {
            if (confirm(confirmMsg)) doDelete();
        }
    });
});
</script>
@endsection
