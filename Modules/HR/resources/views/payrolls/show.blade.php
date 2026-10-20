@extends('layouts/contentNavbarLayout')

@section('title', ($payroll->title ?: $payroll->payroll_number) . ' - ' . config('variables.templateName'))

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <a href="{{ route('hr.payrolls.index') }}" class="btn btn-icon btn-sm btn-outline-secondary rounded-circle me-1" title="{{ __('Back to Payrolls') }}">
                <i class="icon-base bx bx-arrow-back font-16"></i>
            </a>
            <h4 class="fw-bold mb-0 text-heading">{{ $payroll->payroll_number }}</h4>
            <span class="badge bg-label-primary ms-1">{{ $payroll->payroll_month }}</span>
            <span class="fw-semibold {{ $payroll->status->textColor() }} d-inline-flex align-items-center gap-1 ms-2">
                <i class="icon-base bx {{ $payroll->status->icon() }} font-16"></i>
                <span>{{ $payroll->status->label() }}</span>
            </span>
        </div>
        <nav aria-label="breadcrumb" class="ms-5">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hr.payrolls.index') }}">{{ __('Payroll & Wages') }}</a></li>
                <li class="breadcrumb-item active">{{ $payroll->payroll_number }}</li>
            </ol>
        </nav>
    </div>

    <!-- Action Toolbar -->
    <div class="d-flex flex-wrap align-items-center gap-2">
        @can('hr.payrolls.export-wps')
        <a href="{{ route('hr.payrolls.export-wps', $payroll->id) }}" class="btn btn-outline-secondary d-flex align-items-center gap-1">
            <i class="icon-base bx bx-download font-16"></i>
            <span>{{ __('Export WPS / Bank File') }}</span>
        </a>
        @endcan

        @if ($payroll->status === \Modules\HR\Enums\PayrollStatus::DRAFT)
            @can('hr.payrolls.recalculate')
            <button type="button" class="btn btn-outline-primary d-flex align-items-center gap-1 btn-recalculate-payroll" data-id="{{ $payroll->id }}">
                <i class="icon-base bx bx-refresh font-16"></i>
                <span>{{ __('Recalculate') }}</span>
            </button>
            @endcan
            @can('hr.payrolls.approve')
            <button type="button" class="btn btn-outline-success d-flex align-items-center gap-1 btn-approve-payroll" data-id="{{ $payroll->id }}">
                <i class="icon-base bx bx-check font-16"></i>
                <span>{{ __('Approve') }}</span>
            </button>
            @endcan
        @endif

        @if (in_array($payroll->status, [\Modules\HR\Enums\PayrollStatus::DRAFT, \Modules\HR\Enums\PayrollStatus::APPROVED]))
            @can('hr.payrolls.disburse')
            <button type="button" class="btn btn-success d-flex align-items-center gap-1 btn-disburse-payroll" data-id="{{ $payroll->id }}" data-amount="{{ number_format($payroll->total_net, 2) }} {{ __('KWD') }}">
                <i class="icon-base bx bx-wallet font-16"></i>
                <span>{{ __('Disburse / Mark as Paid') }}</span>
            </button>
            @endcan
        @endif
    </div>
</div>

<!-- Financial Summary KPI Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-3">
                <span class="text-muted small d-block mb-1">{{ __('Basic Salary') }}</span>
                <h5 class="fw-bold mb-0 text-heading">{{ number_format($payroll->total_basic_salary, 2) }}</h5>
                <small class="text-muted">{{ __('KWD') }}</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-3">
                <span class="text-muted small d-block mb-1">{{ __('Allowances') }}</span>
                <h5 class="fw-bold mb-0 text-heading">{{ number_format($payroll->total_allowances, 2) }}</h5>
                <small class="text-muted">{{ __('KWD') }}</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-3">
                <span class="text-muted small d-block mb-1">{{ __('Overtime') }}</span>
                <h5 class="fw-bold mb-0 text-heading">{{ number_format($payroll->total_overtime, 2) }}</h5>
                <small class="text-muted">{{ __('KWD') }}</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-3">
                <span class="text-muted small d-block mb-1">{{ __('Rewards') }}</span>
                <h5 class="fw-bold mb-0 text-success">+{{ number_format($payroll->total_rewards, 2) }}</h5>
                <small class="text-muted">{{ __('KWD') }}</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-3">
                <span class="text-muted small d-block mb-1">{{ __('Total Deductions') }}</span>
                <h5 class="fw-bold mb-0 text-danger">-{{ number_format($payroll->total_deductions, 2) }}</h5>
                <small class="text-muted">{{ __('KWD') }}</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card shadow-sm border-0 h-100 bg-label-success border border-success">
            <div class="card-body p-3">
                <span class="text-success fw-semibold small d-block mb-1">{{ __('Net Total') }}</span>
                <h4 class="fw-bold mb-0 text-success">{{ number_format($payroll->total_net, 2) }}</h4>
                <small class="text-success">{{ __('KWD') }}</small>
            </div>
        </div>
    </div>
</div>

<!-- Scope & Notes Alert if present -->
@if ($payroll->notes || $payroll->branch)
    <div class="alert alert-light border d-flex align-items-center justify-content-between p-3 mb-4">
        <div>
            @if ($payroll->branch)
                <span class="badge bg-label-secondary me-2"><i class="icon-base bx bx-building font-14"></i> {{ $payroll->branch->name }}</span>
            @endif
            @if ($payroll->notes)
                <span class="text-muted small"><i class="icon-base bx bx-note font-14 me-1"></i>{{ $payroll->notes }}</span>
            @endif
        </div>
        <span class="text-muted small">{{ __('Total Employees Processed:') }} <strong>{{ $payroll->employees_count }}</strong></span>
    </div>
@endif

<!-- Employees Items Table Card -->
<div class="card shadow-sm border-0">
    <div class="card-header border-bottom py-3 d-flex align-items-center justify-content-between">
        <h5 class="card-title mb-0 fw-bold d-flex align-items-center gap-2">
            <i class="icon-base bx bx-user-check text-primary font-20"></i>
            <span>{{ __('Employees Payroll Details') }}</span>
            <span class="badge bg-label-primary ms-1">{{ $payroll->employees_count }}</span>
        </h5>
    </div>
    <div class="table-responsive text-nowrap">
        <table class="table table-hover align-middle mb-0" id="payrollItemsTable" style="width: 100%;">
            <thead class="table-light">
                <tr>
                    <th class="text-center" style="width: 40px;">#</th>
                    <th>{{ __('Employee') }}</th>
                    <th>{{ __('Basic (KWD)') }}</th>
                    <th>{{ __('Allowances (KWD)') }}</th>
                    <th>{{ __('Overtime') }}</th>
                    <th>{{ __('Rewards') }}</th>
                    <th>{{ __('Gross (KWD)') }}</th>
                    <th>{{ __('Deductions Breakdown') }}</th>
                    <th>{{ __('Net Salary (KWD)') }}</th>
                    <th class="text-center">{{ __('Status') }}</th>
                    <th class="text-center" style="width: 80px;">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- Modal: View Payslip -->
<div class="modal fade" id="viewPayslipModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom d-flex align-items-center justify-content-between">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-receipt text-primary font-22"></i>
                    <span>{{ __('Employee Salary Payslip') }}</span>
                </h5>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1" onclick="window.printPayslip()">
                        <i class="icon-base bx bx-printer font-16"></i>
                        <span>{{ __('Print') }}</span>
                    </button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body p-4" id="payslipPrintArea">
                <!-- Company / Header -->
                <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-4">
                    <div>
                        <h4 class="fw-bold mb-1 text-primary">{{ config('variables.templateName', 'ERP System') }}</h4>
                        <span class="text-muted small">{{ __('Salary Payslip for the period:') }} <strong id="ps_payroll_month"></strong></span>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-label-secondary" id="ps_payroll_number"></span>
                        <div class="mt-1" id="ps_payment_status"></div>
                    </div>
                </div>

                <!-- Employee Header Info -->
                <div class="row g-3 p-3 rounded bg-label-secondary mb-4">
                    <div class="col-sm-6">
                        <span class="text-muted small d-block">{{ __('Employee Name') }}:</span>
                        <strong class="text-heading fs-6" id="ps_emp_name"></strong>
                    </div>
                    <div class="col-sm-3">
                        <span class="text-muted small d-block">{{ __('Employee Code') }}:</span>
                        <strong class="text-heading" id="ps_emp_code"></strong>
                    </div>
                    <div class="col-sm-3">
                        <span class="text-muted small d-block">{{ __('Civil ID') }}:</span>
                        <strong class="text-heading" id="ps_emp_civil_id"></strong>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted small d-block">{{ __('Job Title & Department') }}:</span>
                        <span class="text-heading" id="ps_emp_job"></span>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted small d-block">{{ __('Bank & IBAN') }}:</span>
                        <span class="text-heading small" id="ps_emp_bank"></span>
                    </div>
                </div>

                <!-- Earnings vs Deductions Breakdown -->
                <div class="row g-4 mb-4">
                    <!-- Column 1: Earnings -->
                    <div class="col-md-6 border-end">
                        <h6 class="fw-bold text-success border-bottom pb-2 mb-3 d-flex align-items-center justify-content-between">
                            <span><i class="icon-base bx bx-plus-circle me-1"></i>{{ __('Earnings & Allowances') }}</span>
                            <span class="small">{{ __('Amount (KWD)') }}</span>
                        </h6>
                        <table class="table table-sm table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <td class="text-muted">{{ __('Basic Salary') }}</td>
                                    <td class="text-end fw-semibold" id="ps_basic_salary">0.00</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">{{ __('Allowances') }}</td>
                                    <td class="text-end fw-semibold" id="ps_allowances">0.00</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">{{ __('Overtime Pay') }} <small id="ps_overtime_hrs" class="text-muted"></small></td>
                                    <td class="text-end fw-semibold" id="ps_overtime">0.00</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">{{ __('Rewards & Bonuses') }}</td>
                                    <td class="text-end fw-semibold text-success" id="ps_rewards">0.00</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">{{ __('Other Earnings') }}</td>
                                    <td class="text-end fw-semibold" id="ps_other_earnings">0.00</td>
                                </tr>
                                <tr class="border-top">
                                    <td class="fw-bold text-heading pt-2">{{ __('Gross Total Earnings') }}</td>
                                    <td class="text-end fw-bold text-heading pt-2" id="ps_gross_salary">0.00</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Column 2: Deductions -->
                    <div class="col-md-6">
                        <h6 class="fw-bold text-danger border-bottom pb-2 mb-3 d-flex align-items-center justify-content-between">
                            <span><i class="icon-base bx bx-minus-circle me-1"></i>{{ __('Deductions & Recoveries') }}</span>
                            <span class="small">{{ __('Amount (KWD)') }}</span>
                        </h6>
                        <table class="table table-sm table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <td class="text-muted">{{ __('Absence Deduction') }}</td>
                                    <td class="text-end fw-semibold text-danger" id="ps_absence_deduction">0.00</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">{{ __('Late Penalty') }}</td>
                                    <td class="text-end fw-semibold text-danger" id="ps_late_deduction">0.00</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">{{ __('Loan Installment') }}</td>
                                    <td class="text-end fw-semibold text-warning" id="ps_loans_deduction">0.00</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">{{ __('Disciplinary Penalties') }}</td>
                                    <td class="text-end fw-semibold text-danger" id="ps_penalties_deduction">0.00</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">{{ __('Social Insurance (PIFSS)') }}</td>
                                    <td class="text-end fw-semibold" id="ps_insurance_deduction">0.00</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">{{ __('Other Deductions') }}</td>
                                    <td class="text-end fw-semibold" id="ps_other_deductions">0.00</td>
                                </tr>
                                <tr class="border-top">
                                    <td class="fw-bold text-danger pt-2">{{ __('Total Deductions') }}</td>
                                    <td class="text-end fw-bold text-danger pt-2" id="ps_total_deductions">0.00</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Net Total Banner -->
                <div class="d-flex align-items-center justify-content-between p-3 rounded bg-label-success border border-success mb-4">
                    <div>
                        <span class="fw-semibold text-success d-block mb-1">{{ __('Net Salary Disbursed') }}</span>
                        <small class="text-muted">{{ __('Direct bank transfer into employee IBAN account') }}</small>
                    </div>
                    <div class="text-end">
                        <h3 class="fw-bold text-success mb-0" id="ps_net_salary">0.00</h3>
                        <small class="fw-semibold text-success">{{ __('KWD (Kuwaiti Dinar)') }}</small>
                    </div>
                </div>

                <!-- Signatures -->
                <div class="row pt-4 text-center border-top text-muted small">
                    <div class="col-4">
                        <p class="mb-5">{{ __('Prepared By (HR)') }}</p>
                        <span>____________________</span>
                    </div>
                    <div class="col-4">
                        <p class="mb-5">{{ __('Approved By (Finance)') }}</p>
                        <span>____________________</span>
                    </div>
                    <div class="col-4">
                        <p class="mb-5">{{ __('Employee Signature') }}</p>
                        <span>____________________</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Adjust Payroll Item -->
<div class="modal fade" id="editItemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-edit text-warning font-20"></i>
                    <span>{{ __('Adjust Employee Payroll Item') }}</span>
                    <span id="edit_emp_name_badge" class="badge bg-label-primary ms-2"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditItem">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_item_id" name="item_id">

                <div class="modal-body p-4">
                    <div class="row g-3">
                        <!-- Earnings Column -->
                        <div class="col-md-6 border-end pe-md-3">
                            <h6 class="fw-bold text-success mb-3">{{ __('Earnings (KWD)') }}</h6>

                            <div class="mb-3">
                                <label class="form-label required fw-semibold text-heading small">{{ __('Basic Salary') }}</label>
                                <input type="number" step="0.01" id="edit_basic_salary" name="basic_salary" class="form-control calc-trigger" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-heading small">{{ __('Allowances Amount') }}</label>
                                <input type="number" step="0.01" id="edit_allowances_amount" name="allowances_amount" class="form-control calc-trigger">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-heading small">{{ __('Overtime Amount') }}</label>
                                <input type="number" step="0.01" id="edit_overtime_amount" name="overtime_amount" class="form-control calc-trigger">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-heading small">{{ __('Rewards Amount') }}</label>
                                <input type="number" step="0.01" id="edit_rewards_amount" name="rewards_amount" class="form-control calc-trigger">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-heading small">{{ __('Other Earnings') }}</label>
                                <input type="number" step="0.01" id="edit_other_earnings" name="other_earnings" class="form-control calc-trigger">
                            </div>
                        </div>

                        <!-- Deductions Column -->
                        <div class="col-md-6 ps-md-3">
                            <h6 class="fw-bold text-danger mb-3">{{ __('Deductions (KWD)') }}</h6>

                            <div class="mb-3">
                                <label class="form-label fw-semibold text-heading small">{{ __('Absence Deduction') }}</label>
                                <input type="number" step="0.01" id="edit_absence_deduction" name="absence_deduction" class="form-control calc-trigger">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-heading small">{{ __('Late Deduction') }}</label>
                                <input type="number" step="0.01" id="edit_late_deduction" name="late_deduction" class="form-control calc-trigger">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-heading small">{{ __('Loans Deduction') }}</label>
                                <input type="number" step="0.01" id="edit_loans_deduction" name="loans_deduction" class="form-control calc-trigger">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-heading small">{{ __('Penalties Deduction') }}</label>
                                <input type="number" step="0.01" id="edit_penalties_deduction" name="penalties_deduction" class="form-control calc-trigger">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-heading small">{{ __('Social Insurance (PIFSS)') }}</label>
                                <input type="number" step="0.01" id="edit_insurance_deduction" name="insurance_deduction" class="form-control calc-trigger">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-heading small">{{ __('Other Deductions') }}</label>
                                <input type="number" step="0.01" id="edit_other_deductions" name="other_deductions" class="form-control calc-trigger">
                            </div>
                        </div>
                    </div>

                    <!-- Live Calculation Preview -->
                    <div class="d-flex justify-content-between align-items-center p-3 rounded bg-light border mt-3">
                        <div>
                            <span class="text-muted small d-block">{{ __('Gross Earnings:') }} <strong id="preview_gross" class="text-heading">0.00</strong></span>
                            <span class="text-muted small d-block">{{ __('Total Deductions:') }} <strong id="preview_deductions" class="text-danger">0.00</strong></span>
                        </div>
                        <div class="text-end">
                            <span class="text-muted small d-block">{{ __('Calculated Net Salary:') }}</span>
                            <h4 id="preview_net" class="fw-bold text-success mb-0">0.00 {{ __('KWD') }}</h4>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="mt-3">
                        <label class="form-label fw-semibold text-heading small">{{ __('Notes / Adjustment Justification') }}</label>
                        <textarea id="edit_notes" name="notes" rows="2" class="form-control" placeholder="{{ __('e.g. Approved exception for special travel allowance...') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" id="btnSubmitEditItem" class="btn btn-primary d-flex align-items-center gap-1">
                        <i class="icon-base bx bx-save"></i>
                        <span>{{ __('Save Adjustments') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const $ = window.jQuery || window.$;
    const payrollId = "{{ $payroll->id }}";
    const itemsUrl = "{{ route('hr.payrolls.items-data', $payroll->id) }}";
    const baseUrl = "{{ url('hr/payrolls') }}";

    // CSRF Setup
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // 1. Initialize Items DataTable
    const itemsTable = $('#payrollItemsTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 15,
        lengthMenu: [[10, 15, 25, 50, 100], [10, 15, 25, 50, 100]],
        pagingType: 'simple_numbers',
        ajax: {
            url: itemsUrl
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
            { data: 'employee_info', name: 'employee_id', orderable: false, className: 'align-middle' },
            { data: 'basic_salary_formatted', name: 'basic_salary', className: 'align-middle' },
            { data: 'allowances_formatted', name: 'allowances_amount', className: 'align-middle' },
            { data: 'overtime_formatted', name: 'overtime_amount', className: 'align-middle' },
            { data: 'rewards_formatted', name: 'rewards_amount', className: 'align-middle' },
            { data: 'gross_formatted', name: 'gross_salary', className: 'align-middle' },
            { data: 'deductions_breakdown', name: 'total_deductions', className: 'align-middle' },
            { data: 'net_salary_formatted', name: 'net_salary', className: 'align-middle' },
            { data: 'payment_status_badge', name: 'payment_status', className: 'text-center align-middle' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center align-middle' }
        ],
        order: [[8, 'desc']],
        drawCallback: function () {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('#payrollItemsTable [data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    });

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

    // 2. View Payslip Modal
    $(document).on('click', '.btn-view-payslip', function () {
        const itemId = $(this).data('item-id');
        $.ajax({
            url: baseUrl + '/' + payrollId + '/items/' + itemId + '/payslip',
            type: 'GET',
            success: function (res) {
                if (res.success) {
                    const d = res.data;
                    $('#ps_payroll_number').text(d.payroll_number);
                    $('#ps_payroll_month').text(d.payroll_month);
                    $('#ps_payment_status').html('<span class="fw-semibold ' + d.payment_color + '">' + d.payment_status + '</span>');

                    $('#ps_emp_name').text(d.employee.name);
                    $('#ps_emp_code').text(d.employee.code);
                    $('#ps_emp_civil_id').text(d.employee.civil_id || '-');
                    $('#ps_emp_job').text(d.employee.job_title + ' (' + d.employee.department + ' - ' + d.employee.branch + ')');
                    $('#ps_emp_bank').text(d.employee.bank_name + ' / ' + (d.employee.iban || '-'));

                    // Earnings
                    $('#ps_basic_salary').text(d.earnings.basic_salary);
                    $('#ps_allowances').text(d.earnings.allowances);
                    $('#ps_overtime').text(d.earnings.overtime_amount);
                    $('#ps_overtime_hrs').text('(' + d.earnings.overtime_hours + ' ' + "{{ __('hrs') }}" + ')');
                    $('#ps_rewards').text(d.earnings.rewards_amount);
                    $('#ps_other_earnings').text(d.earnings.other_earnings);
                    $('#ps_gross_salary').text(d.earnings.gross_salary);

                    // Deductions
                    $('#ps_absence_deduction').text(d.deductions.absence_deduction);
                    $('#ps_late_deduction').text(d.deductions.late_deduction);
                    $('#ps_loans_deduction').text(d.deductions.loans_deduction);
                    $('#ps_penalties_deduction').text(d.deductions.penalties_deduction);
                    $('#ps_insurance_deduction').text(d.deductions.insurance_deduction);
                    $('#ps_other_deductions').text(d.deductions.other_deductions);
                    $('#ps_total_deductions').text(d.deductions.total_deductions);

                    // Net
                    $('#ps_net_salary').text(d.net_salary);

                    $('#viewPayslipModal').modal('show');
                }
            },
            error: function () {
                notifyError("{{ __('Failed to load payslip data.') }}");
            }
        });
    });

    // Print Payslip Helper
    window.printPayslip = function () {
        const printContent = document.getElementById('payslipPrintArea').innerHTML;
        const originalContent = document.body.innerHTML;
        document.body.innerHTML = printContent;
        window.print();
        document.body.innerHTML = originalContent;
        window.location.reload();
    };

    // 3. Edit Item Modal & Live Calculation
    function calculateModalPreview() {
        const basic      = parseFloat($('#edit_basic_salary').val()) || 0;
        const allowances = parseFloat($('#edit_allowances_amount').val()) || 0;
        const overtime   = parseFloat($('#edit_overtime_amount').val()) || 0;
        const rewards    = parseFloat($('#edit_rewards_amount').val()) || 0;
        const otherE     = parseFloat($('#edit_other_earnings').val()) || 0;

        const gross = basic + allowances + overtime + rewards + otherE;

        const absence   = parseFloat($('#edit_absence_deduction').val()) || 0;
        const late      = parseFloat($('#edit_late_deduction').val()) || 0;
        const loans     = parseFloat($('#edit_loans_deduction').val()) || 0;
        const penalties = parseFloat($('#edit_penalties_deduction').val()) || 0;
        const insurance = parseFloat($('#edit_insurance_deduction').val()) || 0;
        const otherD    = parseFloat($('#edit_other_deductions').val()) || 0;

        const deductions = absence + late + loans + penalties + insurance + otherD;
        const net = Math.max(0, gross - deductions);

        $('#preview_gross').text(gross.toFixed(2) + ' ' + "{{ __('KWD') }}");
        $('#preview_deductions').text('-' + deductions.toFixed(2) + ' ' + "{{ __('KWD') }}");
        $('#preview_net').text(net.toFixed(2) + ' ' + "{{ __('KWD') }}");
    }

    $('.calc-trigger').on('input change', calculateModalPreview);

    $(document).on('click', '.btn-edit-item', function () {
        const itemId = $(this).data('item-id');
        $('#edit_item_id').val(itemId);

        $.ajax({
            url: baseUrl + '/' + payrollId + '/items/' + itemId,
            type: 'GET',
            success: function (res) {
                if (res.success) {
                    const d = res.data;
                    $('#edit_emp_name_badge').text(d.employee_name);
                    $('#edit_basic_salary').val(d.basic_salary);
                    $('#edit_allowances_amount').val(d.allowances_amount);
                    $('#edit_overtime_amount').val(d.overtime_amount);
                    $('#edit_rewards_amount').val(d.rewards_amount);
                    $('#edit_other_earnings').val(d.other_earnings);

                    $('#edit_absence_deduction').val(d.absence_deduction);
                    $('#edit_late_deduction').val(d.late_deduction);
                    $('#edit_loans_deduction').val(d.loans_deduction);
                    $('#edit_penalties_deduction').val(d.penalties_deduction);
                    $('#edit_insurance_deduction').val(d.insurance_deduction);
                    $('#edit_other_deductions').val(d.other_deductions);

                    $('#edit_notes').val(d.notes || '');

                    calculateModalPreview();
                    $('#editItemModal').modal('show');
                }
            },
            error: function () {
                notifyError("{{ __('Failed to load item details.') }}");
            }
        });
    });

    $('#formEditItem').on('submit', function (e) {
        e.preventDefault();
        const itemId = $('#edit_item_id').val();
        const btn = $('#btnSubmitEditItem');
        btn.prop('disabled', true);

        $.ajax({
            url: baseUrl + '/' + payrollId + '/items/' + itemId,
            type: 'PUT',
            data: $(this).serialize(),
            success: function (res) {
                btn.prop('disabled', false);
                $('#editItemModal').modal('hide');
                notifySuccess(res.message);
                itemsTable.ajax.reload();
                setTimeout(() => window.location.reload(), 1200);
            },
            error: function (xhr) {
                btn.prop('disabled', false);
                notifyError(xhr.responseJSON?.message || "{{ __('Failed to update item.') }}");
            }
        });
    });

    // 4. Recalculate Action
    $('.btn-recalculate-payroll').on('click', function () {
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
                        url: baseUrl + '/' + payrollId + '/recalculate',
                        type: 'POST',
                        success: function (res) {
                            notifySuccess(res.message);
                            setTimeout(() => window.location.reload(), 1000);
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
    $('.btn-approve-payroll').on('click', function () {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: "{{ __('Approve Payroll') }}",
                text: "{{ __('Are you sure you want to approve this payroll run? This will lock employee items against modifications.') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: "{{ __('Approve') }}",
                cancelButtonText: "{{ __('Cancel') }}"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: baseUrl + '/' + payrollId + '/approve',
                        type: 'POST',
                        success: function (res) {
                            notifySuccess(res.message);
                            setTimeout(() => window.location.reload(), 1000);
                        },
                        error: function (xhr) {
                            notifyError(xhr.responseJSON?.message || "{{ __('Approval failed.') }}");
                        }
                    });
                }
            });
        }
    });

    // 6. Disburse Action
    $('.btn-disburse-payroll').on('click', function () {
        const amount = $(this).data('amount') || '';
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: "{{ __('Disburse Payroll') }}",
                text: "{{ __('Confirming disbursement of :amount will automatically mark loan installments as PAID, rewards as PAID, and penalties as DEDUCTED.') }}".replace(':amount', amount),
                icon: 'success',
                showCancelButton: true,
                confirmButtonColor: '#28c76f',
                confirmButtonText: "{{ __('Confirm Disbursement') }}",
                cancelButtonText: "{{ __('Cancel') }}"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: baseUrl + '/' + payrollId + '/disburse',
                        type: 'POST',
                        success: function (res) {
                            notifySuccess(res.message);
                            setTimeout(() => window.location.reload(), 1200);
                        },
                        error: function (xhr) {
                            notifyError(xhr.responseJSON?.message || "{{ __('Disbursement failed.') }}");
                        }
                    });
                }
            });
        }
    });
});
</script>
@endsection
