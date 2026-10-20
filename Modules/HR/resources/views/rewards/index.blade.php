@extends('layouts/contentNavbarLayout')

@section('title', __('Rewards & Incentives Management') . ' - ' . config('variables.templateName'))

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <i class="icon-base bx bx-gift text-primary font-24"></i>
            <span>{{ __('Rewards & Incentives Management') }}</span>
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('Human Resources') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Rewards & Incentives') }}</li>
            </ol>
        </nav>
    </div>

    <div class="d-flex align-items-center gap-2">
        @can('hr.settings.reward-types.view')
        <a href="{{ route('hr.settings.reward-types.index') }}" class="btn btn-label-secondary d-flex align-items-center gap-1 shadow-xs">
            <i class="icon-base bx bx-slider font-18"></i>
            <span>{{ __('Reward Types') }}</span>
        </a>
        @endcan
        @can('hr.rewards.create')
        <button type="button" class="btn btn-primary d-flex align-items-center justify-content-center gap-2 shadow-sm" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddReward" aria-controls="offcanvasAddReward">
            <i class="icon-base bx bx-plus font-18"></i>
            <span>{{ __('Add New Reward') }}</span>
        </button>
        @endcan
    </div>
</div>

<!-- KPI Summary Cards (Using Global Component Styles) -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card kpi-card shadow-sm border-0 h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1">{{ __('Total Rewards YTD') }}</span>
                    <h3 class="fw-bold mb-0 text-primary">{{ number_format($kpis['total_ytd'] ?? 0, 2) }}</h3>
                </div>
                <div class="avatar avatar-md bg-label-primary rounded p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-trophy font-22"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card kpi-card shadow-sm border-0 h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1">{{ __('Current Month Rewards') }}</span>
                    <h3 class="fw-bold mb-0 text-success">{{ number_format($kpis['this_month'] ?? 0, 2) }}</h3>
                </div>
                <div class="avatar avatar-md bg-label-success rounded p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-calendar-star font-22"></i>
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
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card kpi-card shadow-sm border-0 h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1">{{ __('Average Reward') }}</span>
                    <h3 class="fw-bold mb-0 text-info">{{ number_format($kpis['average_award'] ?? 0, 2) }}</h3>
                </div>
                <div class="avatar avatar-md bg-label-info rounded p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-chart font-22"></i>
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
                <select id="filter_employee_id" class="select2 form-select" data-placeholder="{{ __('All Employees') }}" data-allow-clear="true">
                    <option value="">{{ __('All Employees') }}</option>
                    @foreach ($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->name }} ({{ $emp->employee_code }})</option>
                    @endforeach
                </select>
            </div>

            <!-- 2. Reward Type Filter -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Reward Type') }}</label>
                <select id="filter_reward_type_id" class="select2 form-select" data-placeholder="{{ __('All Types') }}" data-allow-clear="true">
                    <option value="">{{ __('All Types') }}</option>
                    @foreach ($rewardTypes as $type)
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

            <!-- 4. Payment Method Filter -->
            <div class="col-12 col-sm-6 col-lg-2">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Payment Method') }}</label>
                <select id="filter_payment_method" class="select2 form-select" data-placeholder="{{ __('All') }}" data-allow-clear="true">
                    <option value="">{{ __('All') }}</option>
                    @foreach ($paymentMethods as $pm)
                        <option value="{{ $pm->value }}">{{ $pm->label() }}</option>
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

<!-- Rewards DataTable Card (Matching loans & leaves exact structure) -->
<div class="card shadow-sm border-0">
    <div class="card-datatable table-responsive">
        <table id="rewardsTable" class="table table-hover border-top w-100 text-nowrap">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">#</th>
                    <th>{{ __('Reward #') }}</th>
                    <th>{{ __('Employee') }}</th>
                    <th>{{ __('Reward Type') }}</th>
                    <th>{{ __('Calculation Mode') }}</th>
                    <th>{{ __('Amount') }}</th>
                    <th>{{ __('Disbursement') }}</th>
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
<!-- Offcanvas: Add New Reward                                                 -->
<!-- ========================================================================= -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="offcanvasAddReward" aria-labelledby="offcanvasAddRewardLabel" style="width: 560px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3 px-4">
        <h5 id="offcanvasAddRewardLabel" class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
            <i class="icon-base bx bx-gift text-primary font-22"></i>
            <span>{{ __('Add New Reward') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-4">
        <form id="formAddReward" enctype="multipart/form-data">
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

            <!-- Reward Type (From hr_settings) -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Reward Type') }} <span class="text-danger">*</span></label>
                <select id="create_reward_type_id" name="reward_type_id" class="form-select select2" required data-placeholder="{{ __('Select Reward Type...') }}">
                    <option value="">{{ __('Select Reward Type...') }}</option>
                    @foreach ($rewardTypes as $rt)
                        <option value="{{ $rt->id }}">{{ $rt->name }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback" id="err-reward_type_id"></div>
            </div>

            <!-- Award Date -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Award Date') }} <span class="text-danger">*</span></label>
                <input type="date" id="create_award_date" name="award_date" class="form-control js-flatpickr-date" value="{{ date('Y-m-d') }}" required>
                <div class="invalid-feedback" id="err-award_date"></div>
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

            <!-- Calculation Value Input -->
            <div class="mb-3">
                <label id="lbl-calculation_value" class="form-label required fw-semibold text-heading">{{ __('Amount (KWD)') }} <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="number" step="0.01" min="0.01" id="create_calculation_value" name="calculation_value" class="form-control" placeholder="0.00" required>
                    <span id="unit-calculation_value" class="input-group-text">{{ __('KWD') }}</span>
                </div>
                <div class="invalid-feedback" id="err-calculation_value"></div>
            </div>

            <!-- Live Calculation Breakdown Card -->
            <div id="liveCalcBox" class="alert alert-secondary p-3 mb-3 d-none">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="small fw-semibold text-heading d-flex align-items-center gap-1">
                        <i class="icon-base bx bx-calculator text-primary font-18"></i>
                        <span>{{ __('Live Preview') }}</span>
                    </span>
                    <span id="calcFormulaBadge" class="badge bg-label-primary small">-</span>
                </div>
                <div class="d-flex justify-content-between align-items-center border-top pt-2">
                    <span class="small text-muted">{{ __('Calculated Final Total:') }}</span>
                    <span id="calcFinalAmount" class="fw-bold text-success fs-5">0.00 KWD</span>
                </div>
            </div>

            <!-- Payment Method & Schedule -->
            <div class="row g-2 mb-3">
                <div class="col-12 col-sm-6">
                    <label class="form-label required fw-semibold text-heading">{{ __('Payment Method') }} <span class="text-danger">*</span></label>
                    <select id="create_payment_method" name="payment_method" class="form-select" required>
                        @foreach ($paymentMethods as $pm)
                            <option value="{{ $pm->value }}">{{ $pm->label() }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback" id="err-payment_method"></div>
                </div>
                <div class="col-12 col-sm-6" id="payrollMonthContainer">
                    <label class="form-label fw-semibold text-heading">{{ __('Payroll Month') }}</label>
                    <input type="month" id="create_payroll_month" name="payroll_month" class="form-control" value="{{ date('Y-m') }}">
                    <div class="invalid-feedback" id="err-payroll_month"></div>
                </div>
            </div>

            <!-- Reason & Justification -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Reason & Justification') }} <span class="text-danger">*</span></label>
                <textarea id="create_reason" name="reason" class="form-control" rows="3" placeholder="{{ __('Explain the reason or achievement behind this reward...') }}" required></textarea>
                <div class="invalid-feedback" id="err-reason"></div>
            </div>

            <!-- Supporting Document -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Supporting Document') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                <input type="file" id="create_attachment" name="attachment" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                <div class="form-text small">{{ __('PDF, JPG, PNG up to 10MB') }}</div>
                <div class="invalid-feedback" id="err-attachment"></div>
            </div>

            <!-- Internal Notes -->
            <div class="mb-4">
                <label class="form-label fw-semibold text-heading">{{ __('Notes') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                <textarea id="create_notes" name="notes" class="form-control" rows="2" placeholder="{{ __('Notes...') }}"></textarea>
            </div>

            <div class="d-flex justify-content-end gap-2 border-top pt-3">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                <button type="submit" id="btn-submit-reward" class="btn btn-primary d-flex align-items-center gap-1">
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    <span>{{ __('Submit Reward') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: View Reward Details -->
<div class="modal fade" id="viewRewardModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-gift text-primary font-20"></i>
                    <span>{{ __('Reward Details') }}</span>
                    <span id="view_reward_number" class="badge bg-label-primary ms-2"></span>
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
                        <h4 id="view_amount" class="text-success fw-bold mb-0"></h4>
                    </div>
                </div>

                <!-- Detail Grid -->
                <div class="row g-3 mb-4">
                    <div class="col-sm-6 col-md-3">
                        <span class="text-muted small d-block">{{ __('Reward Type') }}</span>
                        <span id="view_reward_type" class="fw-semibold text-heading"></span>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <span class="text-muted small d-block">{{ __('Calculation Mode') }}</span>
                        <span id="view_calculation_type" class="fw-semibold text-heading"></span>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <span class="text-muted small d-block">{{ __('Award Date') }}</span>
                        <span id="view_award_date" class="fw-semibold text-heading"></span>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <span class="text-muted small d-block">{{ __('Status') }}</span>
                        <span id="view_status_badge"></span>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <span class="text-muted small d-block">{{ __('Disbursement') }}</span>
                        <span id="view_payment_method" class="fw-semibold text-heading"></span>
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

                <!-- Rejection Notice (if rejected) -->
                <div id="view_rejection_container" class="alert alert-danger p-3 mb-3 d-none">
                    <span class="fw-bold small d-block mb-1">{{ __('Rejection Reason') }}:</span>
                    <p id="view_rejection_reason" class="mb-0 small"></p>
                    <small class="text-muted d-block mt-1">{{ __('Rejected By') }}: <span id="view_rejected_by"></span> • <span id="view_rejected_at"></span></small>
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

<!-- Modal: Approve Reward -->
<div class="modal fade" id="approveRewardModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-check text-success font-20"></i>
                    <span>{{ __('Approve Reward') }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 text-center">
                <p class="mb-0">{{ __('Are you sure you want to approve this reward?') }}</p>
            </div>
            <div class="modal-footer border-top justify-content-center">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" id="btn-confirm-approve" class="btn btn-success">{{ __('Approve') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Pay Reward -->
<div class="modal fade" id="payRewardModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-credit-card text-primary font-20"></i>
                    <span>{{ __('Pay Reward') }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <span class="text-muted small d-block">{{ __('Amount to Disburse') }}</span>
                    <h3 id="pay_modal_amount" class="fw-bold text-success mb-0"></h3>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold text-heading">{{ __('Payment Notes (Optional)') }}</label>
                    <textarea id="pay_notes" class="form-control" rows="2" placeholder="{{ __('e.g. Disbursed via direct bank transfer #TRX-98231...') }}"></textarea>
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" id="btn-confirm-pay" class="btn btn-primary">{{ __('Confirm Payment') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Reject Reward -->
<div class="modal fade" id="rejectRewardModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-x-circle text-danger font-20"></i>
                    <span>{{ __('Reject Reward') }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label required fw-semibold text-heading">{{ __('Rejection Reason') }} <span class="text-danger">*</span></label>
                    <textarea id="reject_reason" class="form-control" rows="3" placeholder="{{ __('Specify the reason for rejection...') }}" required></textarea>
                    <div class="invalid-feedback" id="err-reject-reason">{{ __('Specify the reason for rejection...') }}</div>
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" id="btn-confirm-reject" class="btn btn-danger">{{ __('Reject Reward') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Delete Reward -->
<div class="modal fade" id="deleteRewardModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title text-danger d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-trash font-20"></i>
                    <span>{{ __('Delete Reward') }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 text-center">
                <p class="mb-0">{{ __('Are you sure you want to delete this reward record?') }}</p>
                <strong id="delete_target_name" class="text-heading d-block mt-1"></strong>
            </div>
            <div class="modal-footer border-top justify-content-center">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" id="btn-confirm-delete" class="btn btn-danger">{{ __('Yes, Delete') }}</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const $ = window.jQuery || window.$;
    const dataUrl = "{{ route('hr.rewards.data') }}";
    const baseUrl = "{{ url('hr/rewards') }}";

    // Global CSRF Setup
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // Initialize Select2 matching other HR screens
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

    let activeRewardId = null;

    // 1. Initialize Server-Side DataTable (Exact match with loans & leaves layout)
    const table = $('#rewardsTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        pagingType: 'simple_numbers',
        ajax: {
            url: dataUrl,
            data: function (d) {
                d.employee_id      = $('#filter_employee_id').val();
                d.reward_type_id   = $('#filter_reward_type_id').val();
                d.status           = $('#filter_status').val();
                d.payment_method   = $('#filter_payment_method').val();
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
            { data: 'reward_number_badge', name: 'reward_number', className: 'align-middle' },
            { data: 'employee_info', name: 'employee.translations.name', className: 'align-middle' },
            { data: 'reward_type_badge', name: 'rewardType.translations.name', className: 'align-middle' },
            { data: 'calculation_info', name: 'calculation_type', className: 'align-middle' },
            { data: 'amount_formatted', name: 'amount', className: 'align-middle' },
            { data: 'payment_info', name: 'payment_method', className: 'align-middle' },
            { data: 'status_badge', name: 'status', className: 'text-center align-middle' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center align-middle' }
        ],
        order: [[1, 'desc']],
        drawCallback: function () {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('#rewardsTable [data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    });

    // 2. Custom Filters Trigger Reload with debounce & reset guard
    let isResetting = false;

    $('#filter_employee_id, #filter_reward_type_id, #filter_status, #filter_payment_method').on('change', function () {
        if (isResetting) return;
        table.ajax.reload();
    });

    // Reset Filters Handler
    $('#btnResetFilters').on('click', function () {
        isResetting = true;
        $('#filter_employee_id, #filter_reward_type_id, #filter_status, #filter_payment_method').val('').trigger('change');
        isResetting = false;
        table.ajax.reload();
    });

    // 3. Calculation Mode UI Toggle & Live Preview
    function updateCalculationModeUI() {
        const mode = $('#create_calculation_type').val();
        const lbl  = $('#lbl-calculation_value');
        const unit = $('#unit-calculation_value');

        if (mode === 'fixed_amount') {
            lbl.html("{{ __('Amount (KWD)') }} <span class='text-danger'>*</span>");
            unit.text("{{ __('KWD') }}");
        } else if (mode === 'percentage') {
            lbl.html("{{ __('Percentage of Basic Salary (%)') }} <span class='text-danger'>*</span>");
            unit.text("%");
        } else if (mode === 'days_multiplier') {
            lbl.html("{{ __('Number of Days') }} <span class='text-danger'>*</span>");
            unit.text("{{ __('Days') }}");
        }

        triggerLiveCalc();
    }

    $('#create_calculation_type').on('change', updateCalculationModeUI);

    // Payment Method UI Toggle
    $('#create_payment_method').on('change', function () {
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

        if (!empId || val <= 0) {
            $('#liveCalcBox').addClass('d-none');
            return;
        }

        $.ajax({
            url: "{{ route('hr.rewards.calculate') }}",
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

    $('#create_employee_id').on('change', triggerLiveCalc);
    $('#create_calculation_value').on('input keyup change', triggerLiveCalc);

    // Helper notification functions
    function notifySuccess(message) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: "{{ __('Success') }}",
                text: message,
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
        } else {
            alert(message);
        }
    }

    function notifyError(message) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: "{{ __('Error') }}",
                text: message,
                icon: 'error'
            });
        } else {
            alert(message);
        }
    }

    // 4. Add Reward Form Submission
    $('#formAddReward').on('submit', function (e) {
        e.preventDefault();
        $('.invalid-feedback').text('');
        $('.form-control, .form-select').removeClass('is-invalid');

        const submitBtn = $('#btn-submit-reward');
        const spinner   = submitBtn.find('.spinner-border');

        submitBtn.prop('disabled', true);
        spinner.removeClass('d-none');

        const formData = new FormData(this);

        $.ajax({
            url: "{{ route('hr.rewards.store') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                submitBtn.prop('disabled', false);
                spinner.addClass('d-none');

                if (res.success) {
                    const offcanvasEl = document.getElementById('offcanvasAddReward');
                    const bsOffcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl);
                    if (bsOffcanvas) bsOffcanvas.hide();

                    $('#formAddReward')[0].reset();
                    $('#create_employee_id').val('').trigger('change');
                    $('#create_reward_type_id').val('').trigger('change');
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
    $(document).on('click', '.btn-view-reward', function () {
        const id = $(this).data('id');
        $.ajax({
            url: baseUrl + '/' + id,
            type: 'GET',
            success: function (res) {
                if (res.success) {
                    const d = res.data;
                    $('#view_reward_number').text(d.reward_number);
                    $('#view_emp_avatar').attr('src', d.employee.avatar);
                    $('#view_emp_name').text(d.employee.name);
                    $('#view_emp_code').text(d.employee.code);
                    $('#view_emp_dept').text(d.employee.department);
                    $('#view_amount').text(d.amount + ' ' + "{{ __('KWD') }}");

                    $('#view_reward_type').text(d.reward_type);
                    $('#view_calculation_type').text(d.calculation_type);
                    $('#view_award_date').text(d.award_date);
                    $('#view_payment_method').text(d.payment_method);
                    $('#view_payroll_month').text(d.payroll_month);
                    $('#view_created_by').text(d.created_by);
                    $('#view_created_at').text(d.created_at);

                    $('#view_status_badge').html('<span class="fw-semibold ' + (d.status_color || 'text-secondary') + ' d-inline-flex align-items-center gap-1"><i class="icon-base ' + d.status_icon + ' font-14"></i>' + d.status_label + '</span>');
                    $('#view_reason').text(d.reason);

                    if (d.rejection_reason) {
                        $('#view_rejection_container').removeClass('d-none');
                        $('#view_rejection_reason').text(d.rejection_reason);
                        $('#view_rejected_by').text(d.rejected_by);
                        $('#view_rejected_at').text(d.rejected_at);
                    } else {
                        $('#view_rejection_container').addClass('d-none');
                    }

                    if (d.attachment_url) {
                        $('#view_attachment_container').removeClass('d-none');
                        $('#view_attachment_link').attr('href', d.attachment_url);
                    } else {
                        $('#view_attachment_container').addClass('d-none');
                    }

                    $('#viewRewardModal').modal('show');
                }
            }
        });
    });

    // 6. Approve Click
    $(document).on('click', '.btn-approve-reward', function () {
        activeRewardId = $(this).data('id');
        $('#approveRewardModal').modal('show');
    });

    $('#btn-confirm-approve').on('click', function () {
        if (!activeRewardId) return;
        $.ajax({
            url: baseUrl + '/' + activeRewardId + '/approve',
            type: 'POST',
            success: function (res) {
                $('#approveRewardModal').modal('hide');
                table.ajax.reload();
                notifySuccess(res.message);
            },
            error: function (xhr) {
                notifyError(xhr.responseJSON?.message || "{{ __('Approval failed.') }}");
            }
        });
    });

    // 7. Pay Click
    $(document).on('click', '.btn-pay-reward', function () {
        activeRewardId = $(this).data('id');
        const amt = $(this).data('amount');
        $('#pay_modal_amount').text(parseFloat(amt).toFixed(2) + ' ' + "{{ __('KWD') }}");
        $('#pay_notes').val('');
        $('#payRewardModal').modal('show');
    });

    $('#btn-confirm-pay').on('click', function () {
        if (!activeRewardId) return;
        $.ajax({
            url: baseUrl + '/' + activeRewardId + '/pay',
            type: 'POST',
            data: { notes: $('#pay_notes').val() },
            success: function (res) {
                $('#payRewardModal').modal('hide');
                table.ajax.reload();
                notifySuccess(res.message);
            },
            error: function (xhr) {
                notifyError(xhr.responseJSON?.message || "{{ __('Payment recording failed.') }}");
            }
        });
    });

    // 8. Reject Click
    $(document).on('click', '.btn-reject-reward', function () {
        activeRewardId = $(this).data('id');
        $('#reject_reason').val('');
        $('#rejectRewardModal').modal('show');
    });

    $('#btn-confirm-reject').on('click', function () {
        if (!activeRewardId) return;
        const reason = $('#reject_reason').val().trim();
        if (!reason) {
            $('#reject_reason').addClass('is-invalid');
            return;
        }

        $.ajax({
            url: baseUrl + '/' + activeRewardId + '/reject',
            type: 'POST',
            data: { reason: reason },
            success: function (res) {
                $('#rejectRewardModal').modal('hide');
                table.ajax.reload();
                notifySuccess(res.message);
            },
            error: function (xhr) {
                notifyError(xhr.responseJSON?.message || "{{ __('Rejection failed.') }}");
            }
        });
    });

    // 9. Delete Click
    $(document).on('click', '.btn-delete-reward', function () {
        activeRewardId = $(this).data('id');
        $('#delete_target_name').text($(this).data('name'));
        $('#deleteRewardModal').modal('show');
    });

    $('#btn-confirm-delete').on('click', function () {
        if (!activeRewardId) return;
        $.ajax({
            url: baseUrl + '/' + activeRewardId,
            type: 'DELETE',
            success: function (res) {
                $('#deleteRewardModal').modal('hide');
                table.ajax.reload();
                notifySuccess(res.message);
            },
            error: function (xhr) {
                notifyError(xhr.responseJSON?.message || "{{ __('Deletion failed.') }}");
            }
        });
    });
});
</script>
@endsection
