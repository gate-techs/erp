@extends('layouts/contentNavbarLayout')

@section('title', __('Loans & Advances') . ' - ' . config('variables.templateName'))

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <i class="icon-base bx bx-wallet text-primary font-24"></i>
            <span>{{ __('Loans & Advances') }}</span>
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('Human Resources') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Loans & Advances') }}</li>
            </ol>
        </nav>
    </div>

    <div>
        <button type="button" class="btn btn-primary d-flex align-items-center justify-content-center gap-2 shadow-sm" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddLoan" aria-controls="offcanvasAddLoan">
            <i class="icon-base bx bx-plus font-18"></i>
            <span>{{ __('New Loan Request') }}</span>
        </button>
    </div>
</div>

<!-- KPI Summary Cards (Using Global Component Styles) -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card kpi-card shadow-sm border-0 h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1">{{ __('Disbursed This Month') }}</span>
                    <h3 class="fw-bold mb-0 text-primary">{{ number_format($kpis['disbursed_month'] ?? 0, 2) }}</h3>
                </div>
                <div class="avatar avatar-md bg-label-primary rounded p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-dollar-circle font-22"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card kpi-card shadow-sm border-0 h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1">{{ __('Total Repaid / Recovered') }}</span>
                    <h3 class="fw-bold mb-0 text-success">{{ number_format($kpis['total_repaid'] ?? 0, 2) }}</h3>
                </div>
                <div class="avatar avatar-md bg-label-success rounded p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-check-double font-22"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card kpi-card shadow-sm border-0 h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1">{{ __('Installments Due This Month') }}</span>
                    <h3 class="fw-bold mb-0 text-info">{{ number_format($kpis['due_this_month'] ?? 0, 2) }}</h3>
                </div>
                <div class="avatar avatar-md bg-label-info rounded p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-calendar-event font-22"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card kpi-card shadow-sm border-0 h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1">{{ __('Pending Approval') }}</span>
                    <h3 class="fw-bold mb-0 text-warning">{{ $kpis['pending_count'] ?? 0 }}</h3>
                </div>
                <div class="avatar avatar-md bg-label-warning rounded p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-time-five font-22"></i>
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

            <!-- 4. Loan Type Filter -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Loan Type') }}</label>
                <select id="filter_type" class="select2 form-select" data-placeholder="{{ __('All Types') }}" data-allow-clear="true">
                    <option value="">{{ __('All Types') }}</option>
                    @foreach ($loanTypes as $type)
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
                <input type="date" id="filter_date_from" class="form-control">
            </div>

            <!-- 7. Date To Filter -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Date To') }}</label>
                <input type="date" id="filter_date_to" class="form-control">
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

<!-- Loans DataTable Card -->
<div class="card shadow-sm border-0">
    <div class="card-datatable table-responsive">
        <table id="loansTable" class="table table-hover border-top w-100 text-nowrap">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">#</th>
                    <th>{{ __('Employee') }}</th>
                    <th>{{ __('Loan Type') }}</th>
                    <th>{{ __('Amount') }}</th>
                    <th>{{ __('Installments') }}</th>
                    <th>{{ __('Repayment Progress') }}</th>
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
<!-- Offcanvas: Submit New Loan Request                                        -->
<!-- ========================================================================= -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="offcanvasAddLoan" aria-labelledby="offcanvasAddLoanLabel" style="width: 580px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3 px-4">
        <h5 id="offcanvasAddLoanLabel" class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
            <i class="icon-base bx bx-wallet text-primary font-22"></i>
            <span>{{ __('New Loan Request') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-4">
        <form id="loanRequestForm" action="{{ route('hr.loans.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Employee Selection -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading" for="modal_employee_id">{{ __('Employee') }} <span class="text-danger">*</span></label>
                <select id="modal_employee_id" name="employee_id" class="form-select select2" data-placeholder="{{ __('Select Employee...') }}" required>
                    <option value="">{{ __('Select Employee...') }}</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" data-salary="{{ $emp->basic_salary ?? 0 }}">
                            {{ $emp->name }} ({{ $emp->employee_code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Employee Eligibility Summary Box -->
            <div class="mb-3">
                <div id="liveEligibilityContainer" class="live-balance-card p-3">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="icon-base bx bx-shield-quarter text-primary font-20"></i>
                        <div>
                            <h6 class="mb-0 fw-bold small" id="eligibilityTitle">{{ __('Employee Financial Eligibility') }}</h6>
                            <small class="text-muted" id="eligibilitySubtext">{{ __('Select an employee to verify loan eligibility and salary caps.') }}</small>
                        </div>
                    </div>

                    <div id="eligibilityNumbers" class="row g-2 text-center mt-1" style="display: none !important;">
                        <div class="col-4">
                            <div class="p-2 border rounded bg-white">
                                <small class="text-muted d-block">{{ __('Basic Salary') }}</small>
                                <strong class="text-heading" id="valBasicSalary">0.00</strong>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 border rounded bg-white">
                                <small class="text-muted d-block">{{ __('Max Loan Cap') }}</small>
                                <strong class="text-primary" id="valMaxLoan">0.00</strong>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 border rounded bg-white">
                                <small class="text-muted d-block">{{ __('Max Monthly Cut') }}</small>
                                <strong class="text-warning" id="valMaxDeduction">0.00</strong>
                            </div>
                        </div>
                    </div>

                    <div id="eligibilityAlertBox" class="alert alert-danger py-2 px-3 mt-2 mb-0 small d-none" role="alert"></div>
                </div>
            </div>

            <!-- Loan Type -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading" for="modal_type">{{ __('Loan Type') }} <span class="text-danger">*</span></label>
                <select id="modal_type" name="type" class="form-select" required>
                    @foreach ($loanTypes as $type)
                        <option value="{{ $type->value }}">{{ $type->label() }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Amount & Installments Count -->
            <div class="row g-2 mb-3">
                <div class="col-12 col-sm-6">
                    <label class="form-label required fw-semibold text-heading" for="modal_amount">{{ __('Loan Amount') }} <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="1" id="modal_amount" name="amount" class="form-control" placeholder="0.00" required>
                </div>
                <div class="col-12 col-sm-6">
                    <label class="form-label required fw-semibold text-heading" for="modal_installments_count">{{ __('Installments Count (Months)') }} <span class="text-danger">*</span></label>
                    <input type="number" min="1" max="60" id="modal_installments_count" name="installments_count" class="form-control" value="12" required>
                </div>
            </div>

            <!-- Start Date (First Installment) -->
            <div class="row g-2 mb-3">
                <div class="col-12 col-sm-6">
                    <label class="form-label required fw-semibold text-heading" for="modal_start_date">{{ __('First Installment Due Date') }} <span class="text-danger">*</span></label>
                    <input type="date" id="modal_start_date" name="start_date" class="form-control" value="{{ date('Y-m-01', strtotime('+1 month')) }}" required>
                </div>
                <div class="col-12 col-sm-6 d-flex flex-column justify-content-end pb-1">
                    <div class="p-2 rounded bg-label-primary d-flex align-items-center justify-content-between">
                        <span class="small fw-semibold text-heading">{{ __('Monthly Deduction:') }}</span>
                        <span id="calculatedMonthlyBadge" class="badge bg-primary fs-6">0.00</span>
                    </div>
                </div>
            </div>

            <!-- Live Installments Schedule Preview Accordion -->
            <div class="mb-3">
                <div class="border rounded p-3 bg-light">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fw-bold small text-heading d-flex align-items-center gap-1">
                            <i class="icon-base bx bx-calendar-star text-primary"></i>
                            {{ __('Installment Schedule Preview') }}
                        </span>
                        <span class="badge bg-label-info small" id="scheduleCountBadge">0 {{ __('Installments') }}</span>
                    </div>
                    <div class="table-responsive border rounded bg-white" style="max-height: 180px; overflow-y: auto;">
                        <table class="table table-sm table-hover mb-0 text-nowrap" id="schedulePreviewTable">
                            <thead class="table-light">
                                <tr>
                                    <th class="py-1">#</th>
                                    <th class="py-1">{{ __('Due Date') }}</th>
                                    <th class="py-1 text-end">{{ __('Amount') }}</th>
                                </tr>
                            </thead>
                            <tbody id="schedulePreviewBody">
                                <tr>
                                    <td colspan="3" class="text-center text-muted small py-3">{{ __('Enter amount and installments to preview schedule.') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Reason -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading" for="modal_reason">{{ __('Reason') }} <span class="text-danger">*</span></label>
                <textarea id="modal_reason" name="reason" class="form-control" rows="2" placeholder="{{ __('State the reason for this loan request...') }}" required></textarea>
            </div>

            <!-- Attachment File -->
            <div class="mb-4">
                <label class="form-label fw-semibold text-heading" for="modal_attachment">
                    {{ __('Attachment (Optional)') }}
                </label>
                <input type="file" id="modal_attachment" name="attachment" class="form-control" accept=".pdf,.png,.jpg,.jpeg">
                <div class="form-text small text-muted">{{ __('Supported formats: PDF, JPG, PNG (Max 5MB)') }}</div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex align-items-center justify-content-end gap-2 pt-2 border-top">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm" id="btnSubmitLoan">
                    <i class="icon-base bx bx-send font-18"></i>
                    <span>{{ __('Submit Loan Request') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- Modal: View Loan Details & Installments Breakdown                         -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalViewLoanDetails" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title fw-bold d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-file text-primary font-22"></i>
                    <span>{{ __('Loan Details & Repayment Schedule') }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="viewLoanDetailsBody">
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
<!-- Modal: Disburse Loan Funds                                                -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalDisburseLoan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <form id="disburseLoanForm" class="modal-content">
            @csrf
            <input type="hidden" id="disburse_loan_id">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title fw-bold text-primary d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-dollar-circle font-22"></i>
                    <span>{{ __('Disburse Loan Funds') }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-primary py-2 px-3 mb-3 small">
                    {{ __('Disbursing this loan will activate the monthly repayment schedule and update status to Active / Disbursed.') }}
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold text-heading">{{ __('Disbursement Method') }} <span class="text-danger">*</span></label>
                    <select name="disbursement_method" id="disburse_method" class="form-select" required>
                        <option value="bank_transfer">{{ __('Bank Transfer') }}</option>
                        <option value="cash">{{ __('Cash') }}</option>
                        <option value="cheque">{{ __('Cheque') }}</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold text-heading">{{ __('Reference / Transaction Number') }}</label>
                    <input type="text" name="disbursement_reference" id="disburse_reference" class="form-control" placeholder="{{ __('e.g. TR-9988234 or Cheque #') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold text-heading">{{ __('Disbursement Date') }} <span class="text-danger">*</span></label>
                    <input type="date" name="disbursed_at" id="disburse_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
            </div>
            <div class="modal-footer border-top py-2">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-1" id="btnConfirmDisburse">
                    <i class="icon-base bx bx-check font-18"></i>
                    <span>{{ __('Confirm Disbursement') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- Modal: Reject Loan Request                                                -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalRejectLoan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <form id="rejectLoanForm" class="modal-content">
            @csrf
            <input type="hidden" id="reject_loan_id">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title fw-bold text-danger d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-x-circle font-22"></i>
                    <span>{{ __('Reject Loan Request') }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted small mb-3">
                    {{ __('Please provide the formal reason for rejecting this loan request. The employee will be notified.') }}
                </p>
                <div class="mb-3">
                    <label class="form-label fw-semibold text-heading">{{ __('Rejection Reason') }} <span class="text-danger">*</span></label>
                    <textarea id="reject_loan_reason_text" name="rejection_reason" class="form-control" rows="3" placeholder="{{ __('Specify rejection reason...') }}" required></textarea>
                </div>
            </div>
            <div class="modal-footer border-top py-2">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-danger d-flex align-items-center gap-1" id="btnConfirmRejectLoan">
                    <i class="icon-base bx bx-x font-18"></i>
                    <span>{{ __('Confirm Rejection') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- Modal: Postpone Installment                                               -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalPostponeInstallment" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <form id="postponeInstallmentForm" class="modal-content">
            @csrf
            <input type="hidden" id="postpone_installment_id">
            <input type="hidden" id="postpone_parent_loan_id">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title fw-bold text-warning d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-calendar-event font-22"></i>
                    <span>{{ __('Postpone Installment') }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted small mb-3">
                    {{ __('Select a new due date for this installment and provide the justification.') }}
                </p>
                <div class="mb-3">
                    <label class="form-label fw-semibold text-heading">{{ __('New Due Date') }} <span class="text-danger">*</span></label>
                    <input type="date" id="postpone_new_due_date" name="new_due_date" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold text-heading">{{ __('Postponement Reason') }}</label>
                    <textarea id="postpone_reason" name="reason" class="form-control" rows="2" placeholder="{{ __('e.g. Employee request due to emergency circumstances...') }}"></textarea>
                </div>
            </div>
            <div class="modal-footer border-top py-2">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-warning d-flex align-items-center gap-1" id="btnConfirmPostpone">
                    <i class="icon-base bx bx-check font-18"></i>
                    <span>{{ __('Confirm Postponement') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- Modal: Pay Installment                                                    -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalPayInstallment" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <form id="payInstallmentForm" class="modal-content">
            @csrf
            <input type="hidden" id="pay_installment_id">
            <input type="hidden" id="pay_parent_loan_id">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title fw-bold text-success d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-check-circle font-22"></i>
                    <span>{{ __('Record Installment Payment') }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-semibold text-heading">{{ __('Payment Amount') }} <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0.01" id="pay_amount" name="paid_amount" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold text-heading">{{ __('Notes / Receipt #') }}</label>
                    <input type="text" id="pay_notes" name="notes" class="form-control" placeholder="{{ __('e.g. Cash payment receipt #442') }}">
                </div>
            </div>
            <div class="modal-footer border-top py-2">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-success d-flex align-items-center gap-1" id="btnConfirmPay">
                    <i class="icon-base bx bx-check font-18"></i>
                    <span>{{ __('Save Payment') }}</span>
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
    const dataUrl = "{{ route('hr.loans.data') }}";
    const baseUrl = "{{ url('hr/loans') }}";

    // 1. Initialize Server-Side DataTable
    const table = $('#loansTable').DataTable({
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
            { data: 'amount_formatted', name: 'amount', className: 'align-middle' },
            { data: 'installments_info', name: 'installments_count', className: 'align-middle' },
            { data: 'repayment_progress', name: 'total_paid', className: 'align-middle' },
            { data: 'status_badge', name: 'status', className: 'text-center align-middle' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center align-middle' }
        ],
        order: [[3, 'desc']],
        drawCallback: function () {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('#loansTable [data-bs-toggle="tooltip"]'));
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
            dropdownParent: $('#offcanvasAddLoan').is(':visible') ? $('#offcanvasAddLoan') : null
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

    // 3. Live Employee Eligibility Check
    function checkLiveEligibility() {
        const empId  = $('#modal_employee_id').val();
        const amount = parseFloat($('#modal_amount').val()) || 0;
        const count  = parseInt($('#modal_installments_count').val()) || 1;
        const type   = $('#modal_type').val();

        // If salary advance, lock installments to 1
        if (type === 'salary_advance') {
            $('#modal_installments_count').val(1).prop('readonly', true);
        } else {
            $('#modal_installments_count').prop('readonly', false);
        }

        if (!empId) {
            $('#eligibilityNumbers').attr('style', 'display: none !important;');
            $('#eligibilityTitle').text("{{ __('Employee Financial Eligibility') }}");
            $('#eligibilitySubtext').text("{{ __('Select an employee to verify loan eligibility and salary caps.') }}");
            $('#eligibilityAlertBox').addClass('d-none');
            $('#liveEligibilityContainer').removeClass('danger warning-state active');
            return;
        }

        $.ajax({
            url: "{{ route('hr.loans.check-eligibility') }}",
            type: 'GET',
            data: {
                employee_id: empId,
                amount: amount,
                installments_count: count,
                type: type
            },
            success: function(res) {
                if (res.success && res.data) {
                    const d = res.data;
                    const emp = d.employee;

                    $('#eligibilityNumbers').attr('style', 'display: flex !important;');
                    $('#valBasicSalary').text(parseFloat(emp.basic_salary).toFixed(2));
                    $('#valMaxLoan').text(parseFloat(emp.max_allowed_loan).toFixed(2));
                    $('#valMaxDeduction').text(parseFloat(emp.max_monthly_deduction).toFixed(2));

                    $('#eligibilityTitle').text("{{ __('Eligible Loan Cap:') }} " + parseFloat(emp.max_allowed_loan).toFixed(2));
                    $('#eligibilitySubtext').text("{{ __('Service Tenure:') }} " + emp.tenure_months + " {{ __('Months') }} | {{ __('Active Loans:') }} " + emp.active_loans_count);

                    if (!d.is_eligible) {
                        $('#eligibilityAlertBox').removeClass('d-none').text(d.ineligibility_reason);
                        $('#liveEligibilityContainer').removeClass('active warning-state').addClass('danger');
                    } else {
                        $('#eligibilityAlertBox').addClass('d-none');
                        $('#liveEligibilityContainer').removeClass('danger warning-state').addClass('active');
                    }
                }
            }
        });
    }

    // 4. Live Schedule Preview Calculation
    function updateSchedulePreview() {
        const amount = parseFloat($('#modal_amount').val()) || 0;
        const count  = parseInt($('#modal_installments_count').val()) || 1;
        const start  = $('#modal_start_date').val();

        if (amount > 0 && count > 0) {
            const monthly = (amount / count).toFixed(2);
            $('#calculatedMonthlyBadge').text(monthly);
        } else {
            $('#calculatedMonthlyBadge').text('0.00');
        }

        if (amount <= 0 || count <= 0 || !start) {
            $('#schedulePreviewBody').html(`
                <tr><td colspan="3" class="text-center text-muted small py-3">{{ __('Enter amount and installments to preview schedule.') }}</td></tr>
            `);
            $('#scheduleCountBadge').text('0 {{ __("Installments") }}');
            return;
        }

        $.ajax({
            url: "{{ route('hr.loans.preview-schedule') }}",
            type: 'GET',
            data: {
                amount: amount,
                installments_count: count,
                start_date: start
            },
            success: function(res) {
                if (res.success && res.schedule) {
                    $('#scheduleCountBadge').text(res.schedule.length + ' {{ __("Installments") }}');
                    const rows = res.schedule.map(item => `
                        <tr>
                            <td class="fw-semibold text-muted py-1">#${item.installment_number}</td>
                            <td class="py-1">${item.due_date} <small class="text-muted">(${item.due_date_formatted})</small></td>
                            <td class="text-end fw-bold text-heading py-1">${parseFloat(item.amount).toFixed(2)}</td>
                        </tr>
                    `).join('');
                    $('#schedulePreviewBody').html(rows);
                }
            }
        });
    }

    $('#modal_employee_id, #modal_type').on('change', function() {
        checkLiveEligibility();
        updateSchedulePreview();
    });

    $('#modal_amount, #modal_installments_count, #modal_start_date').on('input change', function() {
        checkLiveEligibility();
        updateSchedulePreview();
    });

    // 5. Submit Loan Request Form
    $('#loanRequestForm').on('submit', function(e) {
        e.preventDefault();
        const form = this;
        const btn  = $('#btnSubmitLoan');
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
                    const offcanvasEl = document.getElementById('offcanvasAddLoan');
                    const offcanvasInstance = bootstrap.Offcanvas.getInstance(offcanvasEl);
                    if (offcanvasInstance) offcanvasInstance.hide();

                    form.reset();
                    $('#modal_employee_id').val('').trigger('change');
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
                        Swal.fire('{{ __("Error") }}', res.message || '{{ __("Failed to submit loan request.") }}', 'error');
                    } else {
                        alert(res.message);
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

    // 6. View Loan Details Modal & Installments Breakdown
    function loadLoanDetails(id) {
        const modal = new bootstrap.Modal(document.getElementById('modalViewLoanDetails'));
        modal.show();

        $('#viewLoanDetailsBody').html(`
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
                const l = res.data;
                if (res.success && l) {
                    const progress = l.progress_percentage || 0;
                    const barColor = progress >= 100 ? 'bg-success' : (progress >= 50 ? 'bg-primary' : 'bg-warning');

                    let installmentsHtml = '';
                    if (l.installments && l.installments.length > 0) {
                        installmentsHtml = `
                            <div class="mt-4">
                                <h6 class="fw-bold mb-2 text-heading d-flex align-items-center justify-content-between">
                                    <span class="d-flex align-items-center gap-1">
                                        <i class="icon-base bx bx-list-ol text-primary"></i>
                                        {{ __('Installments Breakdown') }} (${l.installments.length})
                                    </span>
                                </h6>
                                <div class="table-responsive border rounded">
                                    <table class="table table-hover table-sm mb-0 text-nowrap">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>{{ __('Due Date') }}</th>
                                                <th>{{ __('Installment Amount') }}</th>
                                                <th>{{ __('Paid Amount') }}</th>
                                                <th class="text-center">{{ __('Status') }}</th>
                                                <th class="text-center">{{ __('Actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${l.installments.map(inst => `
                                                <tr>
                                                    <td class="fw-semibold text-muted">#${inst.number}</td>
                                                    <td>
                                                        <span class="text-heading fw-semibold">${inst.due_date}</span>
                                                        ${inst.postponed_from ? `<small class="text-warning d-block">({{ __('Postponed from') }}: ${inst.postponed_from})</small>` : ''}
                                                    </td>
                                                    <td class="fw-bold text-heading">${inst.amount}</td>
                                                    <td class="text-success fw-semibold">${inst.paid_amount}</td>
                                                    <td class="text-center">${inst.status_badge}</td>
                                                    <td class="text-center">
                                                        ${inst.status_value !== 'paid' ? `
                                                            <div class="d-inline-flex gap-1">
                                                                <button type="button" class="btn btn-xs btn-outline-success btn-pay-inst" 
                                                                        data-id="${inst.id}" data-amount="${inst.amount}" data-loan-id="${l.id}" title="{{ __('Record Payment') }}">
                                                                    <i class="icon-base bx bx-check"></i> {{ __('Pay') }}
                                                                </button>
                                                                <button type="button" class="btn btn-xs btn-outline-warning btn-postpone-inst" 
                                                                        data-id="${inst.id}" data-loan-id="${l.id}" title="{{ __('Postpone') }}">
                                                                    <i class="icon-base bx bx-calendar-event"></i> {{ __('Postpone') }}
                                                                </button>
                                                            </div>
                                                        ` : `<span class="text-success small fw-semibold"><i class="icon-base bx bx-check-circle"></i> {{ __('Settled') }}</span>`}
                                                    </td>
                                                </tr>
                                            `).join('')}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        `;
                    }

                    $('#viewLoanDetailsBody').html(`
                        <!-- Top Progress Bar -->
                        <div class="card bg-label-secondary border-0 mb-3 p-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold text-heading">{{ __('Repayment Progress') }}</span>
                                <span class="fw-bold fs-6 text-primary">${progress}%</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar ${barColor}" role="progressbar" style="width: ${progress}%" aria-valuenow="${progress}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="d-flex justify-content-between small text-muted mt-2">
                                <span>{{ __('Total Paid:') }} <strong class="text-success">${l.total_paid}</strong></span>
                                <span>{{ __('Remaining:') }} <strong class="text-danger">${l.remaining_amount}</strong></span>
                                <span>{{ __('Total Loan:') }} <strong class="text-heading">${l.amount}</strong></span>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="icon-base bx bx-user text-primary font-20"></i>
                                    <div>
                                        <div class="small text-muted">{{ __('Employee') }}</div>
                                        <strong class="text-heading">${l.employee_name} (${l.employee_code})</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="icon-base bx bx-category text-primary font-20"></i>
                                    <div>
                                        <div class="small text-muted">{{ __('Loan Type') }}</div>
                                        <div>${l.type_badge}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="icon-base bx bx-calendar text-primary font-20"></i>
                                    <div>
                                        <div class="small text-muted">{{ __('Start Date / First Due') }}</div>
                                        <strong>${l.start_date}</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="icon-base bx bx-money text-primary font-20"></i>
                                    <div>
                                        <div class="small text-muted">{{ __('Installments Plan') }}</div>
                                        <strong>${l.installments_count} {{ __('Months') }} &bull; ${l.monthly_installment} / {{ __('mo') }}</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="small text-muted mb-1">{{ __('Status') }}</div>
                                <div>${l.status_badge}</div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="small text-muted mb-1">{{ __('Created By') }}</div>
                                <strong>${l.creator_name}</strong>
                            </div>
                            ${l.disbursed_at ? `
                                <div class="col-12 col-md-6">
                                    <div class="small text-muted mb-1">{{ __('Disbursement Details') }}</div>
                                    <span class="badge bg-label-success">${l.disbursed_at} &bull; ${l.disbursement_method || '-'}</span>
                                    ${l.disbursement_reference ? `<small class="d-block text-muted">{{ __('Ref:') }} ${l.disbursement_reference}</small>` : ''}
                                </div>
                            ` : ''}
                            <div class="col-12">
                                <div class="small text-muted mb-1">{{ __('Reason / Justification') }}</div>
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
                        ${installmentsHtml}
                    `);
                }
            }
        });
    }

    $(document).on('click', '.btn-view-loan', function() {
        const id = $(this).data('id');
        loadLoanDetails(id);
    });

    // 7. Approve Loan Action
    $(document).on('click', '.btn-approve-loan', function() {
        const id = $(this).data('id');
        const confirmMsg = "{{ __('Approve Loan Request?') }}";
        const subMsg = "{{ __('This will confirm administrative approval. Finance can then disburse the funds.') }}";

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

    // 8. Disburse Loan Modal Trigger
    $(document).on('click', '.btn-disburse-loan', function() {
        const id = $(this).data('id');
        $('#disburse_loan_id').val(id);
        const modal = new bootstrap.Modal(document.getElementById('modalDisburseLoan'));
        modal.show();
    });

    $('#disburseLoanForm').on('submit', function(e) {
        e.preventDefault();
        const id = $('#disburse_loan_id').val();
        const btn = $('#btnConfirmDisburse');
        btn.prop('disabled', true);

        $.ajax({
            url: baseUrl + '/' + id + '/disburse',
            type: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                btn.prop('disabled', false);
                const modalEl = document.getElementById('modalDisburseLoan');
                const modalInstance = bootstrap.Modal.getInstance(modalEl);
                if (modalInstance) modalInstance.hide();
                table.ajax.reload();

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: '{{ __("Disbursed") }}',
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
                btn.prop('disabled', false);
                const msg = xhr.responseJSON?.message || '{{ __("Disbursement failed.") }}';
                alert(msg);
            }
        });
    });

    // 9. Reject Loan Modal Trigger
    $(document).on('click', '.btn-reject-loan', function() {
        const id = $(this).data('id');
        $('#reject_loan_id').val(id);
        $('#reject_loan_reason_text').val('');
        const modal = new bootstrap.Modal(document.getElementById('modalRejectLoan'));
        modal.show();
    });

    $('#rejectLoanForm').on('submit', function(e) {
        e.preventDefault();
        const id = $('#reject_loan_id').val();
        const btn = $('#btnConfirmRejectLoan');
        btn.prop('disabled', true);

        $.ajax({
            url: baseUrl + '/' + id + '/reject',
            type: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                btn.prop('disabled', false);
                const modalEl = document.getElementById('modalRejectLoan');
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

    // 10. Postpone Installment
    $(document).on('click', '.btn-postpone-inst', function() {
        const instId = $(this).data('id');
        const loanId = $(this).data('loan-id');
        $('#postpone_installment_id').val(instId);
        $('#postpone_parent_loan_id').val(loanId);
        $('#postpone_new_due_date').val('');
        $('#postpone_reason').val('');

        const modal = new bootstrap.Modal(document.getElementById('modalPostponeInstallment'));
        modal.show();
    });

    $('#postponeInstallmentForm').on('submit', function(e) {
        e.preventDefault();
        const instId = $('#postpone_installment_id').val();
        const loanId = $('#postpone_parent_loan_id').val();
        const btn = $('#btnConfirmPostpone');
        btn.prop('disabled', true);

        $.ajax({
            url: baseUrl + '/installments/' + instId + '/postpone',
            type: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                btn.prop('disabled', false);
                const modalEl = document.getElementById('modalPostponeInstallment');
                const modalInstance = bootstrap.Modal.getInstance(modalEl);
                if (modalInstance) modalInstance.hide();

                table.ajax.reload();
                loadLoanDetails(loanId);
            },
            error: function(xhr) {
                btn.prop('disabled', false);
                alert(xhr.responseJSON?.message || '{{ __("Failed to postpone installment.") }}');
            }
        });
    });

    // 11. Pay Installment
    $(document).on('click', '.btn-pay-inst', function() {
        const instId = $(this).data('id');
        const amount = $(this).data('amount');
        const loanId = $(this).data('loan-id');
        $('#pay_installment_id').val(instId);
        $('#pay_parent_loan_id').val(loanId);
        $('#pay_amount').val(amount);
        $('#pay_notes').val('');

        const modal = new bootstrap.Modal(document.getElementById('modalPayInstallment'));
        modal.show();
    });

    $('#payInstallmentForm').on('submit', function(e) {
        e.preventDefault();
        const instId = $('#pay_installment_id').val();
        const loanId = $('#pay_parent_loan_id').val();
        const btn = $('#btnConfirmPay');
        btn.prop('disabled', true);

        $.ajax({
            url: baseUrl + '/installments/' + instId + '/pay',
            type: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                btn.prop('disabled', false);
                const modalEl = document.getElementById('modalPayInstallment');
                const modalInstance = bootstrap.Modal.getInstance(modalEl);
                if (modalInstance) modalInstance.hide();

                table.ajax.reload();
                loadLoanDetails(loanId);
            },
            error: function(xhr) {
                btn.prop('disabled', false);
                alert(xhr.responseJSON?.message || '{{ __("Failed to record payment.") }}');
            }
        });
    });

    // 12. Delete Loan
    $(document).on('click', '.btn-delete-loan', function() {
        const id = $(this).data('id');
        const confirmMsg = "{{ __('Delete Loan Request?') }}";
        const subMsg = "{{ __('Are you sure you want to delete this loan request? This action cannot be undone.') }}";

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
});
</script>
@endsection
