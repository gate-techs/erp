@extends('layouts/contentNavbarLayout')

@section('title', __('Exit Clearance Requests') . ' - ' . config('variables.templateName'))

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <i class="icon-base bx bx-user-check text-primary font-24"></i>
            <span>{{ __('Exit Clearance Requests') }}</span>
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('Human Resources') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Exit Clearances') }}</li>
            </ol>
        </nav>
    </div>

    <div>
        <button type="button" class="btn btn-primary d-flex align-items-center justify-content-center gap-2 shadow-sm" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddClearance" aria-controls="offcanvasAddClearance">
            <i class="icon-base bx bx-plus font-18"></i>
            <span>{{ __('New Clearance Request') }}</span>
        </button>
    </div>
</div>

<!-- KPI Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card kpi-card shadow-sm border-0 h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1">{{ __('Total Clearances') }}</span>
                    <h3 class="fw-bold mb-0 text-primary">{{ $kpis['total'] ?? 0 }}</h3>
                </div>
                <div class="avatar avatar-md bg-label-primary rounded p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-folder font-22"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card kpi-card shadow-sm border-0 h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1">{{ __('Pending Review') }}</span>
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
                    <span class="text-muted small d-block mb-1">{{ __('In Progress') }}</span>
                    <h3 class="fw-bold mb-0 text-info">{{ $kpis['in_progress'] ?? 0 }}</h3>
                </div>
                <div class="avatar avatar-md bg-label-info rounded p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-sync font-22"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card kpi-card shadow-sm border-0 h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1">{{ __('Fully Cleared') }}</span>
                    <h3 class="fw-bold mb-0 text-success">{{ $kpis['cleared'] ?? 0 }}</h3>
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

            <!-- 4. Status Filter -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Status') }}</label>
                <select id="filter_status" class="select2 form-select" data-placeholder="{{ __('All Statuses') }}" data-allow-clear="true">
                    <option value="">{{ __('All Statuses') }}</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}">{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 5. Date From Filter -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Last Day From') }}</label>
                <input type="date" id="filter_date_from" class="form-control js-flatpickr-date">
            </div>

            <!-- 6. Date To Filter -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Last Day To') }}</label>
                <input type="date" id="filter_date_to" class="form-control js-flatpickr-date">
            </div>

            <!-- 7. Reset Filters Button -->
            <div class="col-12 col-sm-6 col-lg-3 d-flex align-items-end">
                <button type="button" id="btnResetFilters" class="btn btn-label-secondary btn-sm w-100 d-flex align-items-center justify-content-center gap-1 shadow-xs" style="height: 38px;">
                    <i class="icon-base bx bx-reset font-18"></i>
                    <span class="fw-semibold">{{ __('Reset Filters') }}</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Clearances Server-Side DataTable Card -->
<div class="card shadow-sm border-0">
    <div class="card-datatable table-responsive">
        <table id="exitClearancesTable" class="table table-hover border-top w-100 text-nowrap">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">#</th>
                    <th>{{ __('Code') }}</th>
                    <th>{{ __('Employee') }}</th>
                    <th>{{ __('Departure Dates') }}</th>
                    <th>{{ __('Reason') }}</th>
                    <th>{{ __('Clearance Progress') }}</th>
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
<!-- Offcanvas Modal Side: Submit New Exit Clearance Request                   -->
<!-- ========================================================================= -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="offcanvasAddClearance" aria-labelledby="offcanvasAddClearanceLabel" style="width: 540px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3 px-4">
        <h5 id="offcanvasAddClearanceLabel" class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
            <i class="icon-base bx bx-user-plus text-primary font-22"></i>
            <span>{{ __('New Clearance Request') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-4">
        <form id="clearanceRequestForm" action="{{ route('hr.exit-clearances.store') }}" method="POST">
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

            <!-- Leaving Reason -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading" for="modal_leaving_reason">{{ __('Leaving Reason') }} <span class="text-danger">*</span></label>
                <select id="modal_leaving_reason" name="leaving_reason" class="form-select" required>
                    <option value="resignation">{{ __('Resignation') }}</option>
                    <option value="contract_expiry">{{ __('Contract Expiry') }}</option>
                    <option value="termination">{{ __('Employer Termination') }}</option>
                    <option value="retirement">{{ __('Retirement') }}</option>
                    <option value="other">{{ __('Other') }}</option>
                </select>
            </div>

            <!-- Dates Row -->
            <div class="row g-2 mb-3">
                <div class="col-12 col-sm-6">
                    <label class="form-label fw-semibold text-heading" for="modal_resignation_date">{{ __('Resignation / Notice Date') }}</label>
                    <input type="date" id="modal_resignation_date" name="resignation_date" class="form-control js-flatpickr-date" value="{{ date('Y-m-d') }}">
                </div>
                <div class="col-12 col-sm-6">
                    <label class="form-label required fw-semibold text-heading" for="modal_last_working_day">{{ __('Last Working Day') }} <span class="text-danger">*</span></label>
                    <input type="date" id="modal_last_working_day" name="last_working_day" class="form-control js-flatpickr-date" value="{{ date('Y-m-d', strtotime('+30 days')) }}" required>
                </div>
            </div>

            <!-- Financial Estimates -->
            <div class="row g-2 mb-3">
                <div class="col-12 col-sm-6">
                    <label class="form-label fw-semibold text-heading" for="modal_leave_balance">{{ __('Remaining Leave Balance (Days)') }}</label>
                    <input type="number" step="0.5" min="0" id="modal_leave_balance" name="leave_balance_days" class="form-control" value="0">
                </div>
                <div class="col-12 col-sm-6">
                    <label class="form-label fw-semibold text-heading" for="modal_eos_amount">{{ __('Estimated End of Service Pay') }}</label>
                    <input type="number" step="0.01" min="0" id="modal_eos_amount" name="end_of_service_amount" class="form-control" value="0.00">
                </div>
            </div>

            <!-- Reason Details & Administrative Notes -->
            <div class="mb-4">
                <label class="form-label fw-semibold text-heading" for="modal_reason_details">{{ __('Reason Details & Administrative Notes') }}</label>
                <textarea id="modal_reason_details" name="reason_details" class="form-control" rows="3" placeholder="{{ __('Optional notes or background information...') }}"></textarea>
            </div>

            <!-- Form Actions -->
            <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                <button type="submit" id="btnSubmitClearance" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm">
                    <i class="icon-base bx bx-check font-18"></i>
                    <span>{{ __('Create Clearance Journey') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tableId = '#exitClearancesTable';
    const formId  = '#clearanceRequestForm';

    // 1. Initialize DataTable Server-Side
    const table = $(tableId).DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('hr.exit-clearances.data') }}",
            data: function (d) {
                d.branch_id     = $('#filter_branch_id').val();
                d.department_id = $('#filter_department_id').val();
                d.employee_id   = $('#filter_employee_id').val();
                d.status        = $('#filter_status').val();
                d.date_from     = $('#filter_date_from').val();
                d.date_to       = $('#filter_date_to').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
            { data: 'clearance_code_badge', name: 'clearance_code' },
            { data: 'employee_info', name: 'employee_info' },
            { data: 'dates_info', name: 'last_working_day' },
            { data: 'reason_badge', name: 'leaving_reason' },
            { data: 'progress_bar', name: 'progress_bar', orderable: false, searchable: false },
            { data: 'status_badge', name: 'status', className: 'text-center' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center' }
        ],
        order: [[1, 'desc']],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        drawCallback: function () {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('#exitClearancesTable [data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    });

    // 2. Filter Triggers
    $('#filter_branch_id, #filter_department_id, #filter_employee_id, #filter_status, #filter_date_from, #filter_date_to').on('change', function () {
        table.draw();
    });

    $('#btnResetFilters').on('click', function () {
        $('#filter_branch_id').val('').trigger('change');
        $('#filter_department_id').val('').trigger('change');
        $('#filter_employee_id').val('').trigger('change');
        $('#filter_status').val('').trigger('change');
        $('#filter_date_from').val('');
        $('#filter_date_to').val('');
        table.draw();
    });

    // 3. Form Submit with AJAX
    $(formId).on('submit', function (e) {
        e.preventDefault();
        const $form = $(this);
        const $btn = $('#btnSubmitClearance');
        const originalText = $btn.html();

        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> {{ __("Saving...") }}');

        $.ajax({
            url: $form.attr('action'),
            method: 'POST',
            data: $form.serialize(),
            success: function (res) {
                if (typeof toastr !== 'undefined') {
                    toastr.success(res.message || "{{ __('Exit clearance request created successfully.') }}");
                } else if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'success', title: "{{ __('Success') }}", text: res.message, timer: 2000, showConfirmButton: false });
                }

                const offcanvasEl = document.getElementById('offcanvasAddClearance');
                const offcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl);
                if (offcanvas) offcanvas.hide();

                $form[0].reset();
                $('#modal_employee_id').val('').trigger('change');
                table.draw();
            },
            error: function (xhr) {
                const msg = xhr.responseJSON?.message || "{{ __('Failed to create clearance request.') }}";
                if (typeof toastr !== 'undefined') {
                    toastr.error(msg);
                } else if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'error', title: "{{ __('Error') }}", text: msg });
                }
            },
            complete: function () {
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });

    // 4. Delete Handler
    $(document).on('click', '.btn-delete-clearance', function () {
        const id = $(this).data('id');
        const url = "{{ url('hr/exit-clearances') }}/" + id;

        const performDelete = () => {
            $.ajax({
                url: url,
                type: 'DELETE',
                data: { _token: "{{ csrf_token() }}" },
                success: function (res) {
                    if (typeof toastr !== 'undefined') toastr.success(res.message);
                    table.draw(false);
                },
                error: function (xhr) {
                    const msg = xhr.responseJSON?.message || "{{ __('Error deleting request') }}";
                    if (typeof toastr !== 'undefined') toastr.error(msg);
                }
            });
        };

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: "{{ __('Are you sure?') }}",
                text: "{{ __('This will delete the clearance request and all its checklist records.') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#8592a3',
                confirmButtonText: "{{ __('Yes, Delete') }}",
                cancelButtonText: "{{ __('Cancel') }}"
            }).then((result) => {
                if (result.isConfirmed) performDelete();
            });
        } else {
            if (confirm("{{ __('Are you sure you want to delete this clearance request?') }}")) {
                performDelete();
            }
        }
    });
});
</script>
@endsection
