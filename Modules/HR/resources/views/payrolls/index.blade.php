@extends('layouts/contentNavbarLayout')

@section('title', __('Payroll & Wage Management') . ' - ' . config('variables.templateName'))

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <i class="icon-base bx bx-credit-card text-primary font-24"></i>
            <span>{{ __('Payroll & Wage Management') }}</span>
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('Human Resources') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Payroll & Wages') }}</li>
            </ol>
        </nav>
    </div>

    <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-primary d-flex align-items-center justify-content-center gap-2 shadow-sm" data-bs-toggle="offcanvas" data-bs-target="#offcanvasGeneratePayroll" aria-controls="offcanvasGeneratePayroll">
            <i class="icon-base bx bx-calculator font-18"></i>
            <span>{{ __('Generate New Payroll') }}</span>
        </button>
    </div>
</div>

<!-- KPI Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card kpi-card shadow-sm border-0 h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1">{{ __('Total Disbursed YTD') }}</span>
                    <h3 class="fw-bold mb-0 text-success">{{ number_format($kpis['total_paid_ytd'] ?? 0, 2) }} <small class="text-muted fs-6">{{ __('KWD') }}</small></h3>
                </div>
                <div class="avatar avatar-md bg-label-success rounded p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-wallet font-22"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card kpi-card shadow-sm border-0 h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1">{{ __('Current Month Net Payroll') }}</span>
                    <h3 class="fw-bold mb-0 text-primary">{{ number_format($kpis['this_month_net'] ?? 0, 2) }} <small class="text-muted fs-6">{{ __('KWD') }}</small></h3>
                </div>
                <div class="avatar avatar-md bg-label-primary rounded p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-credit-card font-22"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card kpi-card shadow-sm border-0 h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1">{{ __('Pending Approvals') }}</span>
                    <h3 class="fw-bold mb-0 text-warning">{{ $kpis['pending_count'] ?? 0 }}</h3>
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
                    <span class="text-muted small d-block mb-1">{{ __('Employees Processed (Month)') }}</span>
                    <h3 class="fw-bold mb-0 text-info">{{ $kpis['employees_count'] ?? 0 }}</h3>
                </div>
                <div class="avatar avatar-md bg-label-info rounded p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-user-check font-22"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Multi-Filters Card -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body py-3">
        <div class="row g-3 align-items-end">
            <!-- 1. Month Filter -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Payroll Month') }}</label>
                <input type="month" id="filter_month" class="form-control form-control-sm">
            </div>

            <!-- 2. Branch Filter -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Branch') }}</label>
                <select id="filter_branch_id" class="select2 form-select form-select-sm" data-placeholder="{{ __('All Branches') }}" data-allow-clear="true">
                    <option value="">{{ __('All Branches') }}</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 3. Status Filter -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Status') }}</label>
                <select id="filter_status" class="form-select form-select-sm">
                    <option value="">{{ __('All Statuses') }}</option>
                    @foreach ($statuses as $st)
                        <option value="{{ $st->value }}">{{ $st->label() }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 4. Reset Action -->
            <div class="col-12 col-sm-6 col-lg-3 d-flex gap-2">
                <button type="button" id="btnResetFilters" class="btn btn-outline-secondary btn-sm w-100 d-flex align-items-center justify-content-center gap-1">
                    <i class="icon-base bx bx-reset font-16"></i>
                    <span>{{ __('Reset') }}</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Data Table Card -->
<div class="card shadow-sm border-0">
    <div class="card-header border-bottom py-3 d-flex align-items-center justify-content-between">
        <h5 class="card-title mb-0 fw-bold d-flex align-items-center gap-2">
            <i class="icon-base bx bx-list-ul text-muted font-18"></i>
            <span>{{ __('Payroll Runs List') }}</span>
        </h5>
    </div>
    <div class="table-responsive text-nowrap">
        <table class="table table-hover align-middle mb-0" id="payrollsTable" style="width: 100%;">
            <thead class="table-light">
                <tr>
                    <th class="text-center" style="width: 40px;">#</th>
                    <th>{{ __('Payroll Number') }}</th>
                    <th>{{ __('Month & Title') }}</th>
                    <th>{{ __('Branch') }}</th>
                    <th>{{ __('Employees') }}</th>
                    <th>{{ __('Gross (KWD)') }}</th>
                    <th>{{ __('Deductions (KWD)') }}</th>
                    <th>{{ __('Net Total (KWD)') }}</th>
                    <th class="text-center">{{ __('Status') }}</th>
                    <th class="text-center" style="width: 110px;">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- Offcanvas: Generate New Payroll -->
<div class="offcanvas offcanvas-end shadow" tabindex="-1" id="offcanvasGeneratePayroll" aria-labelledby="offcanvasGeneratePayrollLabel" style="width: 460px;">
    <div class="offcanvas-header border-bottom">
        <h5 id="offcanvasGeneratePayrollLabel" class="offcanvas-title fw-bold d-flex align-items-center gap-2">
            <i class="icon-base bx bx-calculator text-primary font-22"></i>
            <span>{{ __('Generate Monthly Payroll') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-4">
        <div class="alert alert-primary d-flex align-items-center gap-2 mb-4 p-3">
            <i class="icon-base bx bx-info-circle text-primary font-22"></i>
            <div class="small">
                {{ __('The system will automatically calculate basic salaries, active allowances, attendance overtime and deductions, due loan installments, approved rewards, and penalties.') }}
            </div>
        </div>

        <form id="formGeneratePayroll">
            @csrf

            <!-- Payroll Month -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Payroll Month') }} <span class="text-danger">*</span></label>
                <input type="month" id="create_payroll_month" name="payroll_month" class="form-control" value="{{ date('Y-m') }}" required>
                <div class="invalid-feedback" id="err-payroll_month"></div>
            </div>

            <!-- Branch Scope -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Branch Scope') }}</label>
                <select id="create_branch_id" name="branch_id" class="select2 form-select" data-placeholder="{{ __('All Branches (Company-wide)') }}" data-allow-clear="true">
                    <option value="">{{ __('All Branches (Company-wide)') }}</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback" id="err-branch_id"></div>
            </div>

            <!-- Custom Title -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Title / Description') }}</label>
                <input type="text" id="create_title" name="title" class="form-control" placeholder="{{ __('e.g. September 2026 Main Payroll') }}">
                <div class="invalid-feedback" id="err-title"></div>
            </div>

            <!-- Administrative Notes -->
            <div class="mb-4">
                <label class="form-label fw-semibold text-heading">{{ __('Administrative Notes (Optional)') }}</label>
                <textarea id="create_notes" name="notes" rows="3" class="form-control" placeholder="{{ __('Optional notes regarding this payroll cycle...') }}"></textarea>
                <div class="invalid-feedback" id="err-notes"></div>
            </div>

            <div class="d-flex justify-content-end gap-2 border-top pt-3">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                <button type="submit" id="btnSubmitGenerate" class="btn btn-primary d-flex align-items-center gap-1">
                    <i class="icon-base bx bx-cog"></i>
                    <span>{{ __('Calculate & Generate') }}</span>
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Approve Payroll -->
<div class="modal fade" id="approvePayrollModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-check-circle text-success font-20"></i>
                    <span>{{ __('Approve Payroll') }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 text-center">
                <p class="mb-0">{{ __('Are you sure you want to approve this payroll run? This will lock employee items against modifications.') }}</p>
            </div>
            <div class="modal-footer border-top justify-content-center">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" id="btn-confirm-approve" class="btn btn-success">{{ __('Approve') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Disburse / Mark as Paid -->
<div class="modal fade" id="disbursePayrollModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-money-withdraw text-success font-20"></i>
                    <span>{{ __('Disburse Payroll') }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <span class="text-muted small d-block">{{ __('Net Total to Disburse') }}</span>
                    <h3 id="disburse_modal_amount" class="fw-bold text-success mb-0"></h3>
                </div>
                <div class="alert alert-warning p-3 mb-0 small">
                    <i class="icon-base bx bx-info-circle me-1"></i>
                    {{ __('Confirming disbursement will automatically mark loan installments as PAID, rewards as PAID, and penalties as DEDUCTED for all included employees.') }}
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" id="btn-confirm-disburse" class="btn btn-success">{{ __('Confirm Disbursement') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Cancel Payroll -->
<div class="modal fade" id="cancelPayrollModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-block text-warning font-20"></i>
                    <span>{{ __('Cancel Payroll') }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label required fw-semibold text-heading">{{ __('Cancellation Reason') }} <span class="text-danger">*</span></label>
                    <textarea id="cancel_reason" class="form-control" rows="3" placeholder="{{ __('Specify the reason for cancelling this payroll run...') }}" required></textarea>
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                <button type="button" id="btn-confirm-cancel" class="btn btn-warning">{{ __('Confirm Cancellation') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const $ = window.jQuery || window.$;
    const dataUrl = "{{ route('hr.payrolls.data') }}";
    const baseUrl = "{{ url('hr/payrolls') }}";

    // CSRF Setup
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // Initialize Select2
    if (typeof window.initSelect2 === 'function') {
        window.initSelect2();
    } else if ($.fn.select2) {
        $('.select2').each(function () {
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

    let activePayrollId = null;

    // 1. Initialize Server-Side DataTable
    const table = $('#payrollsTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        pagingType: 'simple_numbers',
        ajax: {
            url: dataUrl,
            data: function (d) {
                d.month     = $('#filter_month').val();
                d.branch_id = $('#filter_branch_id').val();
                d.status    = $('#filter_status').val();
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
            { data: 'payroll_code', name: 'payroll_number', className: 'align-middle' },
            { data: 'month_info', name: 'payroll_month', className: 'align-middle' },
            { data: 'branch_info', name: 'branch.translations.name', className: 'align-middle' },
            { data: 'employees_count_formatted', name: 'employees_count', className: 'align-middle text-center' },
            { data: 'gross_formatted', name: 'total_gross', className: 'align-middle' },
            { data: 'deductions_formatted', name: 'total_deductions', className: 'align-middle' },
            { data: 'net_formatted', name: 'total_net', className: 'align-middle' },
            { data: 'status_badge', name: 'status', className: 'text-center align-middle' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center align-middle' }
        ],
        order: [[1, 'desc']],
        drawCallback: function () {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('#payrollsTable [data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    });

    // 2. Filters Trigger Reload
    let isResetting = false;
    $('#filter_month, #filter_branch_id, #filter_status').on('change', function () {
        if (isResetting) return;
        table.ajax.reload();
    });

    $('#btnResetFilters').on('click', function () {
        isResetting = true;
        $('#filter_month').val('');
        $('#filter_branch_id').val('').trigger('change');
        $('#filter_status').val('');
        isResetting = false;
        table.ajax.reload();
    });

    // Notification helpers
    function notifySuccess(msg) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: "{{ __('Success') }}",
                text: msg,
                timer: 2000,
                showConfirmButton: false
            });
        }
    }

    function notifyError(msg) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: "{{ __('Error') }}",
                text: msg
            });
        }
    }

    // 3. Generate Form Submit
    $('#formGeneratePayroll').on('submit', function (e) {
        e.preventDefault();
        const form = $(this);
        const submitBtn = $('#btnSubmitGenerate');
        const spinner = submitBtn.find('.spinner-border');

        submitBtn.prop('disabled', true);
        spinner.removeClass('d-none');
        $('.invalid-feedback').text('');
        $('.is-invalid').removeClass('is-invalid');

        $.ajax({
            url: baseUrl,
            type: 'POST',
            data: form.serialize(),
            success: function (res) {
                submitBtn.prop('disabled', false);
                spinner.addClass('d-none');

                if (res.success) {
                    const offcanvasEl = document.getElementById('offcanvasGeneratePayroll');
                    const bsOffcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl);
                    if (bsOffcanvas) bsOffcanvas.hide();

                    form[0].reset();
                    $('#create_branch_id').val('').trigger('change');

                    table.ajax.reload();
                    notifySuccess(res.message);

                    if (res.data && res.data.url) {
                        setTimeout(() => {
                            window.location.href = res.data.url;
                        }, 1200);
                    }
                }
            },
            error: function (xhr) {
                submitBtn.prop('disabled', false);
                spinner.addClass('d-none');

                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    $.each(errors, function (field, msgs) {
                        $('#create_' + field).addClass('is-invalid');
                        $('#err-' + field).text(msgs[0]);
                    });
                } else {
                    notifyError(xhr.responseJSON?.message || "{{ __('An error occurred while generating payroll.') }}");
                }
            }
        });
    });

    // 4. Recalculate Action
    $(document).on('click', '.btn-recalculate-payroll', function () {
        const id = $(this).data('id');
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: "{{ __('Recalculate Payroll') }}",
                text: "{{ __('Are you sure you want to recalculate all items in this draft payroll? Current live attendance, rewards, and penalties will be re-synced.') }}",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: "{{ __('Yes, Recalculate') }}",
                cancelButtonText: "{{ __('Cancel') }}"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: baseUrl + '/' + id + '/recalculate',
                        type: 'POST',
                        success: function (res) {
                            table.ajax.reload();
                            notifySuccess(res.message);
                        },
                        error: function (xhr) {
                            notifyError(xhr.responseJSON?.message || "{{ __('Recalculation failed.') }}");
                        }
                    });
                }
            });
        }
    });

    // 5. Approve Action
    $(document).on('click', '.btn-approve-payroll', function () {
        activePayrollId = $(this).data('id');
        $('#approvePayrollModal').modal('show');
    });

    $('#btn-confirm-approve').on('click', function () {
        if (!activePayrollId) return;
        const btn = $(this);
        btn.prop('disabled', true);

        $.ajax({
            url: baseUrl + '/' + activePayrollId + '/approve',
            type: 'POST',
            success: function (res) {
                btn.prop('disabled', false);
                $('#approvePayrollModal').modal('hide');
                table.ajax.reload();
                notifySuccess(res.message);
            },
            error: function (xhr) {
                btn.prop('disabled', false);
                notifyError(xhr.responseJSON?.message || "{{ __('Approval failed.') }}");
            }
        });
    });

    // 6. Disburse Action
    $(document).on('click', '.btn-disburse-payroll', function () {
        activePayrollId = $(this).data('id');
        const amount = $(this).data('amount') || '';
        $('#disburse_modal_amount').text(amount);
        $('#disbursePayrollModal').modal('show');
    });

    $('#btn-confirm-disburse').on('click', function () {
        if (!activePayrollId) return;
        const btn = $(this);
        btn.prop('disabled', true);

        $.ajax({
            url: baseUrl + '/' + activePayrollId + '/disburse',
            type: 'POST',
            success: function (res) {
                btn.prop('disabled', false);
                $('#disbursePayrollModal').modal('hide');
                table.ajax.reload();
                notifySuccess(res.message);
            },
            error: function (xhr) {
                btn.prop('disabled', false);
                notifyError(xhr.responseJSON?.message || "{{ __('Disbursement failed.') }}");
            }
        });
    });

    // 7. Cancel Action
    $(document).on('click', '.btn-cancel-payroll', function () {
        activePayrollId = $(this).data('id');
        $('#cancel_reason').val('');
        $('#cancelPayrollModal').modal('show');
    });

    $('#btn-confirm-cancel').on('click', function () {
        if (!activePayrollId) return;
        const reason = $('#cancel_reason').val().trim();
        if (!reason) {
            $('#cancel_reason').addClass('is-invalid');
            return;
        }
        $('#cancel_reason').removeClass('is-invalid');

        const btn = $(this);
        btn.prop('disabled', true);

        $.ajax({
            url: baseUrl + '/' + activePayrollId + '/cancel',
            type: 'POST',
            data: { reason: reason },
            success: function (res) {
                btn.prop('disabled', false);
                $('#cancelPayrollModal').modal('hide');
                table.ajax.reload();
                notifySuccess(res.message);
            },
            error: function (xhr) {
                btn.prop('disabled', false);
                notifyError(xhr.responseJSON?.message || "{{ __('Cancellation failed.') }}");
            }
        });
    });

    // 8. Delete Action
    $(document).on('click', '.btn-delete-payroll', function () {
        const id = $(this).data('id');
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: "{{ __('Delete Payroll') }}",
                text: "{{ __('Are you sure you want to delete this payroll run? All associated employee items will be permanently removed.') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: "{{ __('Yes, Delete') }}",
                cancelButtonText: "{{ __('Cancel') }}"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: baseUrl + '/' + id,
                        type: 'DELETE',
                        success: function (res) {
                            table.ajax.reload();
                            notifySuccess(res.message);
                        },
                        error: function (xhr) {
                            notifyError(xhr.responseJSON?.message || "{{ __('Delete failed.') }}");
                        }
                    });
                }
            });
        }
    });
});
</script>
@endsection
