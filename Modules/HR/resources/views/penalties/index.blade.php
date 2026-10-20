@extends('layouts/contentNavbarLayout')

@section('title', __('Penalties & Disciplinary Actions Management') . ' - ' . config('variables.templateName'))

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <i class="icon-base bx bx-error-circle text-danger font-24"></i>
            <span>{{ __('Penalties & Disciplinary Actions Management') }}</span>
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('Human Resources') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Penalties & Deductions') }}</li>
            </ol>
        </nav>
    </div>

    <div class="d-flex align-items-center gap-2">
        @can('hr.settings.penalty-types.view')
        <a href="{{ route('hr.settings.penalty-types.index') }}" class="btn btn-label-secondary d-flex align-items-center gap-1 shadow-xs">
            <i class="icon-base bx bx-slider font-18"></i>
            <span>{{ __('Penalty Types') }}</span>
        </a>
        @endcan
        @can('hr.penalties.create')
        <button type="button" class="btn btn-primary d-flex align-items-center justify-content-center gap-2 shadow-sm" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddPenalty" aria-controls="offcanvasAddPenalty">
            <i class="icon-base bx bx-plus font-18"></i>
            <span>{{ __('Add New Penalty') }}</span>
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
                    <span class="text-muted small d-block mb-1">{{ __('Total Deductions YTD') }}</span>
                    <h3 class="fw-bold mb-0 text-danger">{{ number_format($kpis['total_ytd'] ?? 0, 2) }}</h3>
                </div>
                <div class="avatar avatar-md bg-label-danger rounded p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-money-withdraw font-22"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card kpi-card shadow-sm border-0 h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1">{{ __('Current Month Deductions') }}</span>
                    <h3 class="fw-bold mb-0 text-warning">{{ number_format($kpis['this_month'] ?? 0, 2) }}</h3>
                </div>
                <div class="avatar avatar-md bg-label-warning rounded p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-calendar-exclamation font-22"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card kpi-card shadow-sm border-0 h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1">{{ __('Pending Approval') }}</span>
                    <h3 class="fw-bold mb-0 text-info">{{ $kpis['pending_count'] ?? 0 }}</h3>
                </div>
                <div class="avatar avatar-md bg-label-info rounded p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-time-five font-22"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card kpi-card shadow-sm border-0 h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1">{{ __('Total Penalties YTD') }}</span>
                    <h3 class="fw-bold mb-0 text-primary">{{ $kpis['total_count_ytd'] ?? 0 }}</h3>
                </div>
                <div class="avatar avatar-md bg-label-primary rounded p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-error-circle font-22"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Standard Filters Card -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body py-3">
        <div class="row g-3 align-items-end">
            <!-- 1. Employee Filter -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Employee') }}</label>
                <select id="filter_employee_id" class="select2 form-select" data-placeholder="{{ __('Select Employee...') }}" data-allow-clear="true">
                    <option value="">{{ __('Select Employee...') }}</option>
                    @foreach ($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->name }} ({{ $emp->employee_code }})</option>
                    @endforeach
                </select>
            </div>

            <!-- 2. Penalty Type Filter -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Penalty Type') }}</label>
                <select id="filter_penalty_type_id" class="select2 form-select" data-placeholder="{{ __('All Types') }}" data-allow-clear="true">
                    <option value="">{{ __('All Types') }}</option>
                    @foreach ($penaltyTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 3. Status Filter -->
            <div class="col-12 col-sm-6 col-lg-2">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Status') }}</label>
                <select id="filter_status" class="select2 form-select" data-placeholder="{{ __('All Statuses') }}" data-allow-clear="true">
                    <option value="">{{ __('All Statuses') }}</option>
                    @foreach ($statuses as $st)
                        <option value="{{ $st->value }}">{{ $st->label() }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 4. Deduction Method Filter -->
            <div class="col-12 col-sm-6 col-lg-2">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Deduction Method') }}</label>
                <select id="filter_deduction_method" class="select2 form-select" data-placeholder="{{ __('All') }}" data-allow-clear="true">
                    <option value="">{{ __('All') }}</option>
                    @foreach ($deductionMethods as $dm)
                        <option value="{{ $dm->value }}">{{ $dm->label() }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 5. Reset Filters Button -->
            <div class="col-12 col-sm-6 col-lg-2 d-flex align-items-end">
                <button type="button" id="btnResetFilters" class="btn btn-label-secondary btn-sm w-100 d-flex align-items-center justify-content-center gap-1 shadow-xs" style="height: 38px;">
                    <i class="icon-base bx bx-reset font-18"></i>
                    <span class="fw-semibold">{{ __('Reset Filters') }}</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Penalties DataTable Card -->
<div class="card shadow-sm border-0">
    <div class="card-datatable table-responsive">
        <table id="penaltiesTable" class="table table-hover border-top w-100 text-nowrap">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">#</th>
                    <th>{{ __('Penalty #') }}</th>
                    <th>{{ __('Employee') }}</th>
                    <th>{{ __('Penalty Type') }}</th>
                    <th>{{ __('Calculation Mode') }}</th>
                    <th>{{ __('Amount') }}</th>
                    <th>{{ __('Deduction') }}</th>
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
<!-- Offcanvas: Add New Penalty                                                -->
<!-- ========================================================================= -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="offcanvasAddPenalty" aria-labelledby="offcanvasAddPenaltyLabel" style="width: 560px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3 px-4">
        <h5 id="offcanvasAddPenaltyLabel" class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
            <i class="icon-base bx bx-error-circle text-danger font-22"></i>
            <span>{{ __('Add New Penalty') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-4">
        <form id="formAddPenalty" enctype="multipart/form-data">
            @csrf
            <!-- Employee Selection -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Employee') }} <span class="text-danger">*</span></label>
                <select id="create_employee_id" name="employee_id" class="form-select select2" required data-placeholder="{{ __('Select Employee...') }}">
                    <option value="">{{ __('Select Employee...') }}</option>
                    @foreach ($employees as $emp)
                        <option value="{{ $emp->id }}" data-salary="{{ $emp->basic_salary }}">
                            {{ $emp->name }} ({{ $emp->employee_code }})
                        </option>
                    @endforeach
                </select>
                <div class="invalid-feedback" id="err-employee_id"></div>
            </div>

            <!-- Penalty Type (From hr_settings) -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Penalty Type') }} <span class="text-danger">*</span></label>
                <select id="create_penalty_type_id" name="penalty_type_id" class="form-select select2" required data-placeholder="{{ __('Select Penalty Type...') }}">
                    <option value="">{{ __('Select Penalty Type...') }}</option>
                    @foreach ($penaltyTypes as $pt)
                        <option value="{{ $pt->id }}">{{ $pt->name }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback" id="err-penalty_type_id"></div>
            </div>

            <!-- Penalty Date -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Penalty Date') }} <span class="text-danger">*</span></label>
                <input type="date" id="create_penalty_date" name="penalty_date" class="form-control js-flatpickr-date" value="{{ date('Y-m-d') }}" required>
                <div class="invalid-feedback" id="err-penalty_date"></div>
            </div>

            <!-- Calculation Mode -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Calculation Mode') }} <span class="text-danger">*</span></label>
                <select id="create_calculation_type" name="calculation_type" class="form-select" required>
                    @foreach ($calculationTypes as $ct)
                        <option value="{{ $ct->value }}">{{ $ct->label() }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback" id="err-calculation_type"></div>
            </div>

            <!-- Calculation Value Input (Dynamic based on Mode) -->
            <div class="mb-3" id="calculationValueGroup">
                <label id="lbl-calculation_value" class="form-label required fw-semibold text-heading">{{ __('Number of Days') }} <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="number" step="0.5" min="0.1" id="create_calculation_value" name="calculation_value" class="form-control" placeholder="1" required>
                    <span id="unit-calculation_value" class="input-group-text">{{ __('Days') }}</span>
                </div>
                <div class="invalid-feedback" id="err-calculation_value"></div>
            </div>

            <!-- Live Calculation Breakdown Card -->
            <div id="liveCalcBox" class="alert alert-secondary p-3 mb-3 d-none">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="small fw-semibold text-heading d-flex align-items-center gap-1">
                        <i class="icon-base bx bx-calculator text-danger font-18"></i>
                        <span>{{ __('Live Preview') }}</span>
                    </span>
                    <span id="calcFormulaBadge" class="badge bg-label-danger small">-</span>
                </div>
                <div class="d-flex justify-content-between align-items-center border-top pt-2">
                    <span class="text-muted small">{{ __('Calculated Final Total:') }}</span>
                    <strong id="calcFinalAmount" class="text-danger fs-6">0.00 {{ __('KWD') }}</strong>
                </div>
            </div>

            <!-- Warning Notice for Non-financial penalty -->
            <div id="warningNoticeBox" class="alert alert-info p-3 mb-3 d-none">
                <div class="d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-info-circle text-info font-20"></i>
                    <span class="small">{{ __('This penalty will be registered in the employee disciplinary profile without salary deduction.') }}</span>
                </div>
            </div>

            <!-- Deduction Method -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Deduction Method') }} <span class="text-danger">*</span></label>
                <select id="create_deduction_method" name="deduction_method" class="form-select" required>
                    @foreach ($deductionMethods as $dm)
                        <option value="{{ $dm->value }}">{{ $dm->label() }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback" id="err-deduction_method"></div>
            </div>

            <!-- Payroll Month Selection (Conditional) -->
            <div class="mb-3" id="payrollMonthContainer">
                <label class="form-label fw-semibold text-heading">{{ __('Payroll Month') }}</label>
                <input type="month" id="create_payroll_month" name="payroll_month" class="form-control" value="{{ date('Y-m') }}">
                <small class="text-muted">{{ __('Leave empty to automatically apply with next payroll run.') }}</small>
                <div class="invalid-feedback" id="err-payroll_month"></div>
            </div>

            <!-- Reason & Justification -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Reason & Justification') }} <span class="text-danger">*</span></label>
                <textarea id="create_reason" name="reason" rows="3" class="form-control" placeholder="{{ __('Explain the violation or disciplinary reason behind this penalty...') }}" required></textarea>
                <div class="invalid-feedback" id="err-reason"></div>
            </div>

            <!-- Notes -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Notes') }}</label>
                <textarea id="create_notes" name="notes" rows="2" class="form-control" placeholder="{{ __('Optional administrative notes...') }}"></textarea>
                <div class="invalid-feedback" id="err-notes"></div>
            </div>

            <!-- Supporting Document -->
            <div class="mb-4">
                <label class="form-label fw-semibold text-heading">{{ __('Supporting Document') }}</label>
                <input type="file" id="create_attachment" name="attachment" class="form-control" accept=".pdf,.png,.jpg,.jpeg,.doc,.docx">
                <small class="text-muted">{{ __('Supported formats: PDF, JPG, PNG, DOC (Max 10MB)') }}</small>
                <div class="invalid-feedback" id="err-attachment"></div>
            </div>

            <div class="d-flex justify-content-end gap-2 border-top pt-3">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                <button type="submit" id="btnSubmitPenalty" class="btn btn-primary d-flex align-items-center gap-1">
                    <i class="icon-base bx bx-save"></i>
                    <span>{{ __('Submit Penalty') }}</span>
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: View Penalty Details -->
<div class="modal fade" id="viewPenaltyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-error-circle text-danger font-20"></i>
                    <span>{{ __('Penalty Details') }}</span>
                    <span id="view_penalty_number" class="badge bg-label-danger ms-2"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Employee Summary Header -->
                <div class="d-flex align-items-center justify-content-between p-3 rounded bg-label-secondary mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar avatar-md">
                            <img id="view_emp_avatar" src="" alt="Avatar" class="rounded-circle">
                        </div>
                        <div>
                            <h6 id="view_emp_name" class="fw-bold mb-0"></h6>
                            <small class="text-muted"><span id="view_emp_code"></span> • <span id="view_emp_dept"></span></small>
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="small text-muted d-block">{{ __('Amount') }}</span>
                        <h4 id="view_amount" class="text-danger fw-bold mb-0"></h4>
                    </div>
                </div>

                <!-- Detail Grid -->
                <div class="row g-3 mb-4">
                    <div class="col-sm-6 col-md-3">
                        <span class="text-muted small d-block">{{ __('Penalty Type') }}</span>
                        <span id="view_penalty_type" class="fw-semibold text-heading"></span>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <span class="text-muted small d-block">{{ __('Calculation Mode') }}</span>
                        <span id="view_calculation_type" class="fw-semibold text-heading"></span>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <span class="text-muted small d-block">{{ __('Penalty Date') }}</span>
                        <span id="view_penalty_date" class="fw-semibold text-heading"></span>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <span class="text-muted small d-block">{{ __('Status') }}</span>
                        <span id="view_status_badge"></span>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <span class="text-muted small d-block">{{ __('Deduction') }}</span>
                        <span id="view_deduction_method" class="fw-semibold text-heading"></span>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <span class="text-muted small d-block">{{ __('Payroll Month') }}</span>
                        <span id="view_payroll_month" class="fw-semibold text-heading"></span>
                    </div>
                    <div class="col-sm-6 col-md-6">
                        <span class="text-muted small d-block">{{ __('Created By') }}</span>
                        <span id="view_created_by" class="fw-semibold text-heading"></span> <small class="text-muted">(<span id="view_created_at"></span>)</small>
                    </div>
                </div>

                <!-- Reason Section -->
                <div class="card bg-light border-0 mb-3">
                    <div class="card-body p-3">
                        <span class="fw-semibold text-heading small d-block mb-1">{{ __('Reason & Justification') }}:</span>
                        <p id="view_reason" class="mb-0 text-muted small"></p>
                    </div>
                </div>

                <!-- Cancellation Notice (if cancelled) -->
                <div id="view_cancellation_container" class="alert alert-warning p-3 mb-3 d-none">
                    <span class="fw-bold small d-block mb-1">{{ __('Cancellation Reason') }}:</span>
                    <p id="view_cancellation_reason" class="mb-0 small"></p>
                    <small class="text-muted d-block mt-1">{{ __('Cancelled By') }}: <span id="view_cancelled_by"></span> • <span id="view_cancelled_at"></span></small>
                </div>

                <!-- Attachment button -->
                <div id="view_attachment_container" class="d-none">
                    <a id="view_attachment_link" href="#" target="_blank" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1">
                        <i class="icon-base bx bx-paperclip"></i>
                        <span>{{ __('View Attachment') }}</span>
                    </a>
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Approve Penalty -->
<div class="modal fade" id="approvePenaltyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-check text-success font-20"></i>
                    <span>{{ __('Approve Penalty') }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 text-center">
                <p class="mb-0">{{ __('Are you sure you want to approve this penalty?') }}</p>
            </div>
            <div class="modal-footer border-top justify-content-center">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" id="btn-confirm-approve" class="btn btn-success">{{ __('Approve') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Deduct Penalty -->
<div class="modal fade" id="deductPenaltyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-minus-circle text-danger font-20"></i>
                    <span>{{ __('Deduct Penalty') }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <span class="text-muted small d-block">{{ __('Amount to Deduct') }}</span>
                    <h3 id="deduct_modal_amount" class="fw-bold text-danger mb-0"></h3>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold text-heading">{{ __('Deduction Notes (Optional)') }}</label>
                    <textarea id="deduct_notes" class="form-control" rows="2" placeholder="{{ __('e.g. Applied manually against pending balance...') }}"></textarea>
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" id="btn-confirm-deduct" class="btn btn-danger">{{ __('Confirm Deduction') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Cancel / Waive Penalty -->
<div class="modal fade" id="cancelPenaltyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-block text-warning font-20"></i>
                    <span>{{ __('Cancel / Waive Penalty') }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label required fw-semibold text-heading">{{ __('Cancellation Reason') }} <span class="text-danger">*</span></label>
                    <textarea id="cancel_reason" class="form-control" rows="3" placeholder="{{ __('Specify the reason for cancellation / grievance acceptance...') }}" required></textarea>
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
    const dataUrl = "{{ route('hr.penalties.data') }}";
    const baseUrl = "{{ url('hr/penalties') }}";

    // Global CSRF Setup
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

    let activePenaltyId = null;

    // 1. Initialize Server-Side DataTable
    const table = $('#penaltiesTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        pagingType: 'simple_numbers',
        ajax: {
            url: dataUrl,
            data: function (d) {
                d.employee_id      = $('#filter_employee_id').val();
                d.penalty_type_id  = $('#filter_penalty_type_id').val();
                d.status           = $('#filter_status').val();
                d.deduction_method = $('#filter_deduction_method').val();
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
            { data: 'penalty_number_badge', name: 'penalty_number', className: 'align-middle' },
            { data: 'employee_info', name: 'employee.translations.name', className: 'align-middle' },
            { data: 'penalty_type_badge', name: 'penaltyType.translations.name', className: 'align-middle' },
            { data: 'calculation_info', name: 'calculation_type', className: 'align-middle' },
            { data: 'amount_formatted', name: 'amount', className: 'align-middle' },
            { data: 'deduction_info', name: 'deduction_method', className: 'align-middle' },
            { data: 'status_badge', name: 'status', className: 'text-center align-middle' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center align-middle' }
        ],
        order: [[1, 'desc']],
        drawCallback: function () {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('#penaltiesTable [data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    });

    // 2. Filters Trigger Reload
    let isResetting = false;

    $('#filter_employee_id, #filter_penalty_type_id, #filter_status, #filter_deduction_method').on('change', function () {
        if (isResetting) return;
        table.ajax.reload();
    });

    $('#btnResetFilters').on('click', function () {
        isResetting = true;
        $('#filter_employee_id, #filter_penalty_type_id, #filter_status, #filter_deduction_method').val('').trigger('change');
        isResetting = false;
        table.ajax.reload();
    });

    // 3. Calculation Mode UI Toggle & Live Preview
    function updateCalculationModeUI() {
        const mode = $('#create_calculation_type').val();
        const lbl  = $('#lbl-calculation_value');
        const unit = $('#unit-calculation_value');
        const valGroup = $('#calculationValueGroup');
        const warnBox = $('#warningNoticeBox');

        if (mode === 'days_deduction') {
            valGroup.removeClass('d-none');
            warnBox.addClass('d-none');
            lbl.html("{{ __('Number of Days') }} <span class='text-danger'>*</span>");
            unit.text("{{ __('Days') }}");
            $('#create_calculation_value').prop('required', true).attr('step', '0.5');
        } else if (mode === 'fixed_amount') {
            valGroup.removeClass('d-none');
            warnBox.addClass('d-none');
            lbl.html("{{ __('Amount (KWD)') }} <span class='text-danger'>*</span>");
            unit.text("{{ __('KWD') }}");
            $('#create_calculation_value').prop('required', true).attr('step', '0.01');
        } else if (mode === 'percentage') {
            valGroup.removeClass('d-none');
            warnBox.addClass('d-none');
            lbl.html("{{ __('Percentage of Basic Salary (%)') }} <span class='text-danger'>*</span>");
            unit.text("%");
            $('#create_calculation_value').prop('required', true).attr('step', '1');
        } else if (mode === 'warning_only') {
            valGroup.addClass('d-none');
            warnBox.removeClass('d-none');
            $('#liveCalcBox').addClass('d-none');
            $('#create_calculation_value').prop('required', false).val('');
            return;
        }

        triggerLiveCalc();
    }

    $('#create_calculation_type').on('change', updateCalculationModeUI);

    // Deduction Method UI Toggle
    $('#create_deduction_method').on('change', function () {
        if ($(this).val() === 'next_payroll') {
            $('#payrollMonthContainer').removeClass('d-none');
        } else {
            $('#payrollMonthContainer').addClass('d-none');
        }
    });

    // Live Calculation Trigger
    function triggerLiveCalc() {
        const empId = $('#create_employee_id').val();
        const mode  = $('#create_calculation_type').val();
        const val   = parseFloat($('#create_calculation_value').val()) || 0;

        if (mode === 'warning_only') {
            $('#liveCalcBox').addClass('d-none');
            return;
        }

        if (!empId || val <= 0) {
            $('#liveCalcBox').addClass('d-none');
            return;
        }

        $.ajax({
            url: "{{ route('hr.penalties.calculate') }}",
            type: 'GET',
            data: {
                employee_id: empId,
                calculation_type: mode,
                calculation_value: val
            },
            success: function (res) {
                if (res.success) {
                    $('#liveCalcBox').removeClass('d-none');
                    $('#calcFormulaBadge').text(res.data.formula);
                    $('#calcFinalAmount').text(res.data.amount_formatted);
                }
            },
            error: function () {
                $('#liveCalcBox').addClass('d-none');
            }
        });
    }

    $('#create_employee_id, #create_calculation_value').on('input change', triggerLiveCalc);

    // SweetAlert notification helpers
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

    // 4. Submit Add Penalty Form
    $('#formAddPenalty').on('submit', function (e) {
        e.preventDefault();
        const form = $(this);
        const submitBtn = $('#btnSubmitPenalty');
        const spinner = submitBtn.find('.spinner-border');

        submitBtn.prop('disabled', true);
        spinner.removeClass('d-none');
        $('.invalid-feedback').text('');
        $('.is-invalid').removeClass('is-invalid');

        const formData = new FormData(this);

        $.ajax({
            url: baseUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                submitBtn.prop('disabled', false);
                spinner.addClass('d-none');

                if (res.success) {
                    const offcanvasEl = document.getElementById('offcanvasAddPenalty');
                    const bsOffcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl);
                    if (bsOffcanvas) bsOffcanvas.hide();

                    $('#formAddPenalty')[0].reset();
                    $('#create_employee_id').val('').trigger('change');
                    $('#create_penalty_type_id').val('').trigger('change');
                    $('#liveCalcBox').addClass('d-none');

                    table.ajax.reload();
                    notifySuccess(res.message);
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
                    notifyError(xhr.responseJSON?.message || "{{ __('An error occurred while submitting the request.') }}");
                }
            }
        });
    });

    // 5. View Details Click
    $(document).on('click', '.btn-view-penalty', function () {
        const id = $(this).data('id');
        $.ajax({
            url: baseUrl + '/' + id,
            type: 'GET',
            success: function (res) {
                if (res.success) {
                    const d = res.data;
                    $('#view_penalty_number').text(d.penalty_number);
                    $('#view_emp_avatar').attr('src', d.employee.avatar);
                    $('#view_emp_name').text(d.employee.name);
                    $('#view_emp_code').text(d.employee.code);
                    $('#view_emp_dept').text(d.employee.department);
                    $('#view_amount').text(d.amount + ' ' + "{{ __('KWD') }}");

                    $('#view_penalty_type').text(d.penalty_type);
                    $('#view_calculation_type').text(d.calculation_type);
                    $('#view_penalty_date').text(d.penalty_date);
                    $('#view_deduction_method').text(d.deduction_method);
                    $('#view_payroll_month').text(d.payroll_month);
                    $('#view_created_by').text(d.created_by);
                    $('#view_created_at').text(d.created_at);

                    $('#view_status_badge').html('<span class="fw-semibold ' + (d.status_color || 'text-secondary') + ' d-inline-flex align-items-center gap-1"><i class="icon-base ' + d.status_icon + ' font-14"></i>' + d.status_label + '</span>');
                    $('#view_reason').text(d.reason);

                    if (d.cancellation_reason) {
                        $('#view_cancellation_container').removeClass('d-none');
                        $('#view_cancellation_reason').text(d.cancellation_reason);
                        $('#view_cancelled_by').text(d.cancelled_by);
                        $('#view_cancelled_at').text(d.cancelled_at);
                    } else {
                        $('#view_cancellation_container').addClass('d-none');
                    }

                    if (d.attachment_url) {
                        $('#view_attachment_container').removeClass('d-none');
                        $('#view_attachment_link').attr('href', d.attachment_url);
                    } else {
                        $('#view_attachment_container').addClass('d-none');
                    }

                    $('#viewPenaltyModal').modal('show');
                }
            },
            error: function () {
                notifyError("{{ __('Failed to load penalty details.') }}");
            }
        });
    });

    // 6. Approve Action
    $(document).on('click', '.btn-approve-penalty', function () {
        activePenaltyId = $(this).data('id');
        $('#approvePenaltyModal').modal('show');
    });

    $('#btn-confirm-approve').on('click', function () {
        if (!activePenaltyId) return;
        const btn = $(this);
        btn.prop('disabled', true);

        $.ajax({
            url: baseUrl + '/' + activePenaltyId + '/approve',
            type: 'POST',
            success: function (res) {
                btn.prop('disabled', false);
                $('#approvePenaltyModal').modal('hide');
                table.ajax.reload();
                notifySuccess(res.message);
            },
            error: function (xhr) {
                btn.prop('disabled', false);
                notifyError(xhr.responseJSON?.message || "{{ __('Approval failed.') }}");
            }
        });
    });

    // 7. Deduct Action
    $(document).on('click', '.btn-deduct-penalty', function () {
        activePenaltyId = $(this).data('id');
        const amount = $(this).data('amount') || '';
        $('#deduct_modal_amount').text(amount);
        $('#deduct_notes').val('');
        $('#deductPenaltyModal').modal('show');
    });

    $('#btn-confirm-deduct').on('click', function () {
        if (!activePenaltyId) return;
        const btn = $(this);
        btn.prop('disabled', true);

        $.ajax({
            url: baseUrl + '/' + activePenaltyId + '/deduct',
            type: 'POST',
            data: {
                notes: $('#deduct_notes').val()
            },
            success: function (res) {
                btn.prop('disabled', false);
                $('#deductPenaltyModal').modal('hide');
                table.ajax.reload();
                notifySuccess(res.message);
            },
            error: function (xhr) {
                btn.prop('disabled', false);
                notifyError(xhr.responseJSON?.message || "{{ __('Deduction failed.') }}");
            }
        });
    });

    // 8. Cancel Action
    $(document).on('click', '.btn-cancel-penalty', function () {
        activePenaltyId = $(this).data('id');
        $('#cancel_reason').val('');
        $('#cancelPenaltyModal').modal('show');
    });

    $('#btn-confirm-cancel').on('click', function () {
        if (!activePenaltyId) return;
        const reason = $('#cancel_reason').val().trim();
        if (!reason) {
            $('#cancel_reason').addClass('is-invalid');
            return;
        }
        $('#cancel_reason').removeClass('is-invalid');

        const btn = $(this);
        btn.prop('disabled', true);

        $.ajax({
            url: baseUrl + '/' + activePenaltyId + '/cancel',
            type: 'POST',
            data: { reason: reason },
            success: function (res) {
                btn.prop('disabled', false);
                $('#cancelPenaltyModal').modal('hide');
                table.ajax.reload();
                notifySuccess(res.message);
            },
            error: function (xhr) {
                btn.prop('disabled', false);
                notifyError(xhr.responseJSON?.message || "{{ __('Cancellation failed.') }}");
            }
        });
    });

    // 9. Delete Action
    $(document).on('click', '.btn-delete-penalty', function () {
        const id = $(this).data('id');

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: "{{ __('Delete Penalty') }}",
                text: "{{ __('Are you sure you want to delete this penalty record?') }}",
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
