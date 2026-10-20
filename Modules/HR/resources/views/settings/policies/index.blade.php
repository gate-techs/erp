@extends('layouts/contentNavbarLayout')

@section('title', __('Work Policies & Rules') . ' - ' . config('variables.templateName'))

@section('page-style')
<style>
    .policy-card {
        transition: all 0.2s ease-in-out;
        border: 1px solid #e7e7e8;
    }
    .policy-card:hover {
        border-color: #696cff;
        box-shadow: 0 4px 12px rgba(105, 108, 255, 0.08);
    }
    .table-tiers th {
        background-color: #f5f5f9;
        font-weight: 600;
        font-size: 0.82rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .nav-pills-custom .nav-link {
        font-weight: 600;
        padding: 0.65rem 1.15rem;
        border-radius: 8px;
        color: #566a7f;
        transition: all 0.2s ease;
    }
    .nav-pills-custom .nav-link.active {
        background-color: #696cff;
        color: #fff !important;
        box-shadow: 0 4px 10px rgba(105, 108, 255, 0.3);
    }
    .nav-pills-custom .nav-link.active i {
        color: #fff !important;
    }
    .section-title {
        font-size: 0.92rem;
        font-weight: 700;
        color: #566a7f;
        border-bottom: 2px solid #eceef1;
        padding-bottom: 0.45rem;
        margin-bottom: 1.15rem;
    }
    .unit-badge {
        position: absolute;
        inset-inline-end: 12px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 0.78rem;
        color: #a1acb8;
        pointer-events: none;
        z-index: 5;
    }
    .input-with-unit {
        padding-inline-end: 55px !important;
    }
    .info-tooltip {
        cursor: help;
        color: #a1acb8;
        font-size: 0.92rem;
        transition: color 0.15s ease-in-out;
    }
    .info-tooltip:hover {
        color: #696cff;
    }
</style>
@endsection

@section('content')
<!-- Header -->
<div class="mb-4">
    <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
        <i class="icon-base bx bx-slider-alt text-primary font-22"></i>
        {{ __('HR Settings') }}
    </h4>
</div>


<!-- 2-Column Layout with Sub-Sidebar -->
<div class="row g-4">
    <!-- Sub-Sidebar Column -->
    <div class="col-12 col-md-4 col-lg-3">
        @include('hr::settings.partials.sidebar')
    </div>

    <!-- Main Content Column -->
    <div class="col-12 col-md-8 col-lg-9">
        <form id="policiesForm" action="{{ route('hr.settings.policies.update') }}" method="POST">
            @csrf

            <!-- Header Card & Actions -->
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body py-3">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                        <div>
                            <h5 class="fw-bold mb-1 d-flex align-items-center gap-2 text-heading">
                                <i class="icon-base bx bx-book-content text-primary font-22"></i>
                                <span>{{ __('Work Policies & Regulations') }}</span>
                            </h5>
                            <p class="text-muted mb-0 small">
                                {{ __('Configure dynamic HR rules, leave balances, sick tiers, absence limits, permissions, and end of service criteria.') }}
                            </p>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            @can('hr.settings.policies.reset')
                            <button type="button" id="btnResetDefaults" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1 shadow-sm">
                                <i class="icon-base bx bx-reset font-16"></i>
                                <span>{{ __('Reset Defaults') }}</span>
                            </button>
                            @endcan

                            @can('hr.settings.policies.edit')
                            <button type="submit" class="btn btn-primary btn-sm d-flex align-items-center gap-1 shadow-sm px-3">
                                <i class="icon-base bx bx-save font-16"></i>
                                <span>{{ __('Save All Changes') }}</span>
                            </button>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs Navigation -->
            <ul class="nav nav-pills nav-pills-custom mb-3 gap-2 flex-nowrap overflow-auto pb-2" id="policiesTabs" role="tablist" style="scrollbar-width: thin;">
                <li class="nav-item text-nowrap" role="presentation">
                    <button class="nav-link active d-flex align-items-center gap-2" id="leaves-tab" data-bs-toggle="pill" data-bs-target="#tab-leaves" type="button" role="tab" aria-controls="tab-leaves" aria-selected="true">
                        <i class="bx bx-calendar-star font-18"></i>
                        <span>{{ __('Leave Policies') }}</span>
                    </button>
                </li>
                <li class="nav-item text-nowrap" role="presentation">
                    <button class="nav-link d-flex align-items-center gap-2" id="sick-tab" data-bs-toggle="pill" data-bs-target="#tab-sick" type="button" role="tab" aria-controls="tab-sick" aria-selected="false">
                        <i class="bx bx-plus-medical font-18"></i>
                        <span>{{ __('Sick Leave Tiers') }}</span>
                    </button>
                </li>
                <li class="nav-item text-nowrap" role="presentation">
                    <button class="nav-link d-flex align-items-center gap-2" id="absence-tab" data-bs-toggle="pill" data-bs-target="#tab-absence" type="button" role="tab" aria-controls="tab-absence" aria-selected="false">
                        <i class="bx bx-user-x font-18"></i>
                        <span>{{ __('Absence & Penalties') }}</span>
                    </button>
                </li>
                <li class="nav-item text-nowrap" role="presentation">
                    <button class="nav-link d-flex align-items-center gap-2" id="permissions-tab" data-bs-toggle="pill" data-bs-target="#tab-permissions" type="button" role="tab" aria-controls="tab-permissions" aria-selected="false">
                        <i class="bx bx-time-five font-18"></i>
                        <span>{{ __('Permissions & Short Leaves') }}</span>
                    </button>
                </li>
                <li class="nav-item text-nowrap" role="presentation">
                    <button class="nav-link d-flex align-items-center gap-2" id="eos-tab" data-bs-toggle="pill" data-bs-target="#tab-eos" type="button" role="tab" aria-controls="tab-eos" aria-selected="false">
                        <i class="bx bx-award font-18"></i>
                        <span>{{ __('Notice & End of Service') }}</span>
                    </button>
                </li>
                <li class="nav-item text-nowrap" role="presentation">
                    <button class="nav-link d-flex align-items-center gap-2" id="loans-tab" data-bs-toggle="pill" data-bs-target="#tab-loans" type="button" role="tab" aria-controls="tab-loans" aria-selected="false">
                        <i class="bx bx-money-withdraw font-18"></i>
                        <span>{{ __('Loan & Advance Policies') }}</span>
                    </button>
                </li>
            </ul>

            <!-- Tabs Content -->
            <div class="tab-content p-0 border-0 shadow-none" id="policiesTabsContent">

                <!-- ======================================================= -->
                <!-- 1. TAB: LEAVE POLICIES -->
                <!-- ======================================================= -->
                <div class="tab-pane fade show active" id="tab-leaves" role="tabpanel" aria-labelledby="leaves-tab">
                    <div class="card shadow-sm border-0 mb-3">
                        <div class="card-body p-4">
                            <!-- Section: Annual Leave -->
                            <div class="section-title d-flex align-items-center gap-2">
                                <i class="bx bx-sun text-warning font-20"></i>
                                <span>{{ __('Annual Leave Regulations') }}</span>
                            </div>
                            <div class="row g-3 mb-4">
                                <div class="col-12 col-md-6 col-lg-4">
                                    <label class="form-label fw-semibold text-heading d-flex align-items-center justify-content-between mb-1">
                                        <span>{{ __('Annual Leave Balance') }}</span>
                                        <i class="bx bx-info-circle info-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Annual paid leave entitlement per year.') }}"></i>
                                    </label>
                                    <div class="position-relative">
                                        <input type="number" step="1" min="0" max="90" class="form-control input-with-unit" name="policies[annual_leave_days]" value="{{ $policies['leaves']['annual_leave_days'] ?? 21 }}" required>
                                        <span class="unit-badge">{{ __('Days') }}</span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-lg-4">
                                    <label class="form-label fw-semibold text-heading d-flex align-items-center justify-content-between mb-1">
                                        <span>{{ __('First Year Eligibility') }}</span>
                                        <i class="bx bx-info-circle info-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Waiting period before new employee can request leave.') }}"></i>
                                    </label>
                                    <div class="position-relative">
                                        <input type="number" step="1" min="0" max="12" class="form-control input-with-unit" name="policies[first_year_eligibility_months]" value="{{ $policies['leaves']['first_year_eligibility_months'] ?? 3 }}" required>
                                        <span class="unit-badge">{{ __('Months') }}</span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-lg-4">
                                    <label class="form-label fw-semibold text-heading d-flex align-items-center justify-content-between mb-1">
                                        <span>{{ __('Allow Annual Rollover') }}</span>
                                        <i class="bx bx-info-circle info-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Allow balance rollover to next year') }}"></i>
                                    </label>
                                    <div class="d-flex align-items-center gap-2 mt-1">
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input" type="checkbox" role="switch" id="allow_annual_rollover" name="policies[allow_annual_rollover]" value="1" {{ !empty($policies['leaves']['allow_annual_rollover']) ? 'checked' : '' }}>
                                        </div>
                                        <div id="rollover_days_wrapper" class="flex-grow-1 {{ empty($policies['leaves']['allow_annual_rollover']) ? 'd-none' : '' }}">
                                            <div class="position-relative">
                                                <input type="number" step="1" min="0" max="60" class="form-control input-with-unit form-control" name="policies[max_rollover_days]" value="{{ $policies['leaves']['max_rollover_days'] ?? 15 }}" placeholder="{{ __('Max Days') }}">
                                                <span class="unit-badge">{{ __('Days') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section: Casual / Emergency Leave -->
                            <div class="section-title d-flex align-items-center gap-2">
                                <i class="bx bx-bolt-circle text-info font-20"></i>
                                <span>{{ __('Casual & Emergency Leave Regulations') }}</span>
                            </div>
                            <div class="row g-3 mb-4">
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-semibold text-heading d-flex align-items-center justify-content-between mb-1">
                                        <span>{{ __('Enable Casual Leave') }}</span>
                                        <i class="bx bx-info-circle info-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Activate casual leave balance') }}"></i>
                                    </label>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" role="switch" id="enable_casual_leave" name="policies[enable_casual_leave]" value="1" {{ !empty($policies['leaves']['enable_casual_leave']) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-medium" for="enable_casual_leave">{{ __('Active') }}</label>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-semibold text-heading d-flex align-items-center justify-content-between mb-1">
                                        <span>{{ __('Casual Leave Days Per Year') }}</span>
                                        <i class="bx bx-info-circle info-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Deducted or counted within emergency leave allowance.') }}"></i>
                                    </label>
                                    <div class="position-relative">
                                        <input type="number" step="1" min="0" max="30" class="form-control input-with-unit" name="policies[casual_leave_annual_days]" value="{{ $policies['leaves']['casual_leave_annual_days'] ?? 6 }}">
                                        <span class="unit-badge">{{ __('Days') }}</span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-semibold text-heading d-flex align-items-center justify-content-between mb-1">
                                        <span>{{ __('Consecutive Days Allowed') }}</span>
                                        <i class="bx bx-info-circle info-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Allow taking consecutive casual days') }}"></i>
                                    </label>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" role="switch" id="allow_consecutive_casual_days" name="policies[allow_consecutive_casual_days]" value="1" {{ !empty($policies['leaves']['allow_consecutive_casual_days']) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-medium" for="allow_consecutive_casual_days">{{ __('Allow Consecutive') }}</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Section: Hajj, Maternity & Social Leaves -->
                            <div class="section-title d-flex align-items-center gap-2">
                                <i class="bx bx-heart text-danger font-20"></i>
                                <span>{{ __('Maternity, Hajj & Social Leaves') }}</span>
                            </div>
                            <div class="row g-3">
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-semibold text-heading d-flex align-items-center justify-content-between mb-1">
                                        <span>{{ __('Maternity Leave (Female Employees)') }}</span>
                                        <i class="bx bx-info-circle info-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Paid leave for childbirth and maternity.') }}"></i>
                                    </label>
                                    <div class="position-relative">
                                        <input type="number" step="1" min="0" max="120" class="form-control input-with-unit" name="policies[maternity_leave_days]" value="{{ $policies['leaves']['maternity_leave_days'] ?? 70 }}">
                                        <span class="unit-badge">{{ __('Days') }}</span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-semibold text-heading d-flex align-items-center justify-content-between mb-1">
                                        <span>{{ __('Hajj Leave Days') }}</span>
                                        <i class="bx bx-info-circle info-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Paid pilgrimage leave days.') }}"></i>
                                    </label>
                                    <div class="position-relative">
                                        <input type="number" step="1" min="0" max="30" class="form-control input-with-unit" name="policies[hajj_leave_days]" value="{{ $policies['leaves']['hajj_leave_days'] ?? 10 }}">
                                        <span class="unit-badge">{{ __('Days') }}</span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-semibold text-heading d-flex align-items-center justify-content-between mb-1">
                                        <span>{{ __('Hajj Min Service Years') }}</span>
                                        <i class="bx bx-info-circle info-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Minimum completed service required.') }}"></i>
                                    </label>
                                    <div class="position-relative">
                                        <input type="number" step="1" min="0" max="10" class="form-control input-with-unit" name="policies[hajj_min_service_years]" value="{{ $policies['leaves']['hajj_min_service_years'] ?? 2 }}">
                                        <span class="unit-badge">{{ __('Years') }}</span>
                                    </div>
                                </div>

                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-semibold text-heading d-flex align-items-center justify-content-between mb-1">
                                        <span>{{ __('Hajj Lifetime Frequency') }}</span>
                                        <i class="bx bx-info-circle info-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Max times eligible throughout entire service.') }}"></i>
                                    </label>
                                    <div class="position-relative">
                                        <input type="number" step="1" min="1" max="5" class="form-control input-with-unit" name="policies[hajj_max_times]" value="{{ $policies['leaves']['hajj_max_times'] ?? 1 }}">
                                        <span class="unit-badge">{{ __('Times') }}</span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-semibold text-heading d-flex align-items-center justify-content-between mb-1">
                                        <span>{{ __('Hajj Paid Salary Percentage') }}</span>
                                        <i class="bx bx-info-circle info-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Percentage of base salary paid during Hajj.') }}"></i>
                                    </label>
                                    <div class="position-relative">
                                        <input type="number" step="1" min="0" max="100" class="form-control input-with-unit" name="policies[hajj_salary_percentage]" value="{{ $policies['leaves']['hajj_salary_percentage'] ?? 100 }}">
                                        <span class="unit-badge">%</span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-semibold text-heading d-flex align-items-center justify-content-between mb-1">
                                        <span>{{ __('Official Hajj Permit Required') }}</span>
                                        <i class="bx bx-info-circle info-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Require official permit document') }}"></i>
                                    </label>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" role="switch" id="hajj_require_official_permit" name="policies[hajj_require_official_permit]" value="1" {{ !empty($policies['leaves']['hajj_require_official_permit']) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-medium" for="hajj_require_official_permit">{{ __('Mandatory') }}</label>
                                    </div>
                                </div>

                                <!-- Marriage, Bereavement, Paternity -->
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-semibold text-heading">{{ __('Marriage Leave') }}</label>
                                    <div class="position-relative">
                                        <input type="number" step="1" min="0" max="30" class="form-control input-with-unit" name="policies[marriage_leave_days]" value="{{ $policies['leaves']['marriage_leave_days'] ?? 5 }}">
                                        <span class="unit-badge">{{ __('Days') }}</span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-semibold text-heading">{{ __('Bereavement Leave (Death of Relative)') }}</label>
                                    <div class="position-relative">
                                        <input type="number" step="1" min="0" max="30" class="form-control input-with-unit" name="policies[bereavement_leave_days]" value="{{ $policies['leaves']['bereavement_leave_days'] ?? 5 }}">
                                        <span class="unit-badge">{{ __('Days') }}</span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-semibold text-heading">{{ __('Paternity Leave (New Child)') }}</label>
                                    <div class="position-relative">
                                        <input type="number" step="1" min="0" max="30" class="form-control input-with-unit" name="policies[paternity_leave_days]" value="{{ $policies['leaves']['paternity_leave_days'] ?? 3 }}">
                                        <span class="unit-badge">{{ __('Days') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ======================================================= -->
                <!-- 2. TAB: SICK LEAVE TIERS -->
                <!-- ======================================================= -->
                <div class="tab-pane fade" id="tab-sick" role="tabpanel" aria-labelledby="sick-tab">
                    <div class="card shadow-sm border-0 mb-3">
                        <div class="card-header bg-transparent d-flex align-items-center justify-content-between py-3 border-bottom">
                            <div>
                                <h6 class="fw-bold mb-0 text-heading d-flex align-items-center gap-2">
                                    <i class="bx bx-plus-medical text-primary font-18"></i>
                                    <span>{{ __('Sick Leave Tiers & Entitlement') }}</span>
                                    <i class="bx bx-info-circle info-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Define the progressive tiers of sick days and corresponding salary payment percentage.') }}"></i>
                                </h6>
                            </div>
                            <button type="button" id="btnAddSickTier" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1 shadow-sm">
                                <i class="bx bx-plus font-16"></i>
                                <span>{{ __('Add New Tier') }}</span>
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-tiers align-middle mb-0" id="sickTiersTable">
                                    <thead>
                                        <tr>
                                            <th style="width: 4%;">#</th>
                                            <th style="width: 28%;">{{ __('Tier Description [Arabic]') }}</th>
                                            <th style="width: 28%;">{{ __('Tier Description [English]') }}</th>
                                            <th style="width: 18%;">{{ __('Duration (Days)') }}</th>
                                            <th style="width: 14%;">{{ __('Salary Paid (%)') }}</th>
                                            <th style="width: 8%; text-align: center;">{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="sickTiersBody">
                                        @forelse($sickLeaveTiers as $index => $tier)
                                            <tr data-index="{{ $index }}">
                                                <td class="fw-bold text-muted tier-order">{{ $loop->iteration }}</td>
                                                <td>
                                                    <input type="text" class="form-control" name="sick_leave_tiers[{{ $index }}][name_ar]" value="{{ $tier->name_ar }}" placeholder="{{ __('Tier description (e.g. 15 days full pay)') }}" required>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" name="sick_leave_tiers[{{ $index }}][name_en]" value="{{ $tier->name_en }}" placeholder="{{ __('English Description') }}">
                                                </td>
                                                <td>
                                                    <div class="position-relative">
                                                        <input type="number" min="1" max="180" class="form-control input-with-unit" name="sick_leave_tiers[{{ $index }}][days_count]" value="{{ $tier->days_count }}" required>
                                                        <span class="unit-badge">{{ __('Days') }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="position-relative">
                                                        <input type="number" step="0.5" min="0" max="100" class="form-control input-with-unit" name="sick_leave_tiers[{{ $index }}][salary_percentage]" value="{{ $tier->salary_percentage }}" required>
                                                        <span class="unit-badge">%</span>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-icon btn-text-danger rounded-pill btn-remove-row" title="{{ __('Delete Tier') }}">
                                                        <i class="bx bx-trash font-18 text-danger"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ======================================================= -->
                <!-- 3. TAB: ABSENCE & PENALTIES -->
                <!-- ======================================================= -->
                <div class="tab-pane fade" id="tab-absence" role="tabpanel" aria-labelledby="absence-tab">
                    <div class="card shadow-sm border-0 mb-3">
                        <div class="card-body p-4">
                            <div class="section-title d-flex align-items-center gap-2">
                                <i class="bx bx-error text-danger font-20"></i>
                                <span>{{ __('Absence Limits & Termination Criteria') }}</span>
                            </div>
                            <div class="row g-3 mb-4">
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold text-heading d-flex align-items-center justify-content-between mb-1">
                                        <span>{{ __('Max Monthly Deduction (Days)') }}</span>
                                        <i class="bx bx-info-circle info-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Maximum salary deduction days allowed within a single pay period.') }}"></i>
                                    </label>
                                    <div class="position-relative">
                                        <input type="number" step="1" min="1" max="30" class="form-control input-with-unit" name="policies[max_monthly_deduction_days]" value="{{ $policies['absence']['max_monthly_deduction_days'] ?? 5 }}" required>
                                        <span class="unit-badge">{{ __('Days') }}</span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold text-heading d-flex align-items-center justify-content-between mb-1">
                                        <span>{{ __('Unauthorized Absence Multiplier') }}</span>
                                        <i class="bx bx-info-circle info-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Penalty multiplier applied on unexcused absence days.') }}"></i>
                                    </label>
                                    <select class="form-select" name="policies[unauthorized_absence_rate]">
                                        <option value="1.0" {{ ($policies['absence']['unauthorized_absence_rate'] ?? '1.0') == '1.0' ? 'selected' : '' }}>{{ __('1.0x - Deduct 1 day per absent day (Standard)') }}</option>
                                        <option value="1.5" {{ ($policies['absence']['unauthorized_absence_rate'] ?? '1.0') == '1.5' ? 'selected' : '' }}>{{ __('1.5x - Deduct 1.5 days per absent day') }}</option>
                                        <option value="2.0" {{ ($policies['absence']['unauthorized_absence_rate'] ?? '1.0') == '2.0' ? 'selected' : '' }}>{{ __('2.0x - Deduct 2 days per absent day (Double penalty)') }}</option>
                                    </select>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold text-heading d-flex align-items-center justify-content-between mb-1">
                                        <span>{{ __('Continuous Absence for Termination') }}</span>
                                        <i class="bx bx-info-circle info-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Number of consecutive unexcused absence days after which employee is considered resigned.') }}"></i>
                                    </label>
                                    <div class="position-relative">
                                        <input type="number" step="1" min="1" max="60" class="form-control input-with-unit" name="policies[continuous_absence_termination_days]" value="{{ $policies['absence']['continuous_absence_termination_days'] ?? 15 }}" required>
                                        <span class="unit-badge">{{ __('Days') }}</span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold text-heading d-flex align-items-center justify-content-between mb-1">
                                        <span>{{ __('Intermittent Absence for Termination') }}</span>
                                        <i class="bx bx-info-circle info-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Cumulative intermittent unexcused absence days within one work year triggering termination.') }}"></i>
                                    </label>
                                    <div class="position-relative">
                                        <input type="number" step="1" min="1" max="100" class="form-control input-with-unit" name="policies[intermittent_absence_termination_days]" value="{{ $policies['absence']['intermittent_absence_termination_days'] ?? 30 }}" required>
                                        <span class="unit-badge">{{ __('Days') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ======================================================= -->
                <!-- 4. TAB: PERMISSIONS & SHORT LEAVES -->
                <!-- ======================================================= -->
                <div class="tab-pane fade" id="tab-permissions" role="tabpanel" aria-labelledby="permissions-tab">
                    <div class="card shadow-sm border-0 mb-3">
                        <div class="card-body p-4">
                            <div class="section-title d-flex align-items-center gap-2">
                                <i class="bx bx-time-five text-primary font-20"></i>
                                <span>{{ __('Monthly Balance & Permission Limits') }}</span>
                            </div>
                            <div class="row g-3 mb-4">
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-semibold text-heading d-flex align-items-center justify-content-between mb-1">
                                        <span>{{ __('Max Monthly Permission Hours') }}</span>
                                        <i class="bx bx-info-circle info-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Free monthly permission allowance per employee.') }}"></i>
                                    </label>
                                    <div class="position-relative">
                                        <input type="number" step="0.5" min="0" max="40" class="form-control input-with-unit" name="policies[max_monthly_permission_hours]" value="{{ $policies['permissions']['max_monthly_permission_hours'] ?? 4 }}" required>
                                        <span class="unit-badge">{{ __('Hours') }}</span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-semibold text-heading d-flex align-items-center justify-content-between mb-1">
                                        <span>{{ __('Max Monthly Requests Count') }}</span>
                                        <i class="bx bx-info-circle info-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Maximum permission requests allowed per month.') }}"></i>
                                    </label>
                                    <div class="position-relative">
                                        <input type="number" step="1" min="1" max="20" class="form-control input-with-unit" name="policies[max_monthly_permission_count]" value="{{ $policies['permissions']['max_monthly_permission_count'] ?? 3 }}" required>
                                        <span class="unit-badge">{{ __('Times') }}</span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-semibold text-heading d-flex align-items-center justify-content-between mb-1">
                                        <span>{{ __('Max Single Permission Duration') }}</span>
                                        <i class="bx bx-info-circle info-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Maximum duration for an individual permission request.') }}"></i>
                                    </label>
                                    <div class="position-relative">
                                        <input type="number" step="0.5" min="0.5" max="8" class="form-control input-with-unit" name="policies[max_single_permission_hours]" value="{{ $policies['permissions']['max_single_permission_hours'] ?? 2 }}" required>
                                        <span class="unit-badge">{{ __('Hours') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="section-title d-flex align-items-center gap-2">
                                <i class="bx bx-dollar-circle text-success font-20"></i>
                                <span>{{ __('Financial Impact, Grace Periods & Workflows') }}</span>
                            </div>
                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold text-heading mb-1">{{ __('Financial Policy for Exceeded Hours') }}</label>
                                    <select class="form-select" name="policies[permission_financial_policy]">
                                        <option value="paid_with_excess_deduction" {{ ($policies['permissions']['permission_financial_policy'] ?? '') == 'paid_with_excess_deduction' ? 'selected' : '' }}>
                                            {{ __('Free hours allowance, deduct excess from salary (Recommended)') }}
                                        </option>
                                        <option value="always_deducted" {{ ($policies['permissions']['permission_financial_policy'] ?? '') == 'always_deducted' ? 'selected' : '' }}>
                                            {{ __('Always deduct permission hours from salary') }}
                                        </option>
                                        <option value="deduct_from_annual_leaves" {{ ($policies['permissions']['permission_financial_policy'] ?? '') == 'deduct_from_annual_leaves' ? 'selected' : '' }}>
                                            {{ __('Accumulate hours and deduct from annual leave balance') }}
                                        </option>
                                        <option value="always_paid" {{ ($policies['permissions']['permission_financial_policy'] ?? '') == 'always_paid' ? 'selected' : '' }}>
                                            {{ __('Fully paid permissions (No salary deduction)') }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold text-heading d-flex align-items-center justify-content-between mb-1">
                                        <span>{{ __('Morning Grace Period') }}</span>
                                        <i class="bx bx-info-circle info-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Allowed delay window before recording late punch on attendance.') }}"></i>
                                    </label>
                                    <div class="position-relative">
                                        <input type="number" step="1" min="0" max="60" class="form-control input-with-unit" name="policies[morning_grace_period_minutes]" value="{{ $policies['permissions']['morning_grace_period_minutes'] ?? 15 }}" required>
                                        <span class="unit-badge">{{ __('Minutes') }}</span>
                                    </div>
                                </div>

                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-semibold text-heading d-flex align-items-center justify-content-between mb-1">
                                        <span>{{ __('Min Advance Notice') }}</span>
                                        <i class="bx bx-info-circle info-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Required advance hours to submit request.') }}"></i>
                                    </label>
                                    <div class="position-relative">
                                        <input type="number" step="1" min="0" max="72" class="form-control input-with-unit" name="policies[min_advance_notice_hours]" value="{{ $policies['permissions']['min_advance_notice_hours'] ?? 24 }}">
                                        <span class="unit-badge">{{ __('Hours') }}</span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-semibold text-heading mb-1">{{ __('Approval Workflow') }}</label>
                                    <select class="form-select" name="policies[approval_workflow]">
                                        <option value="manager_and_hr" {{ ($policies['permissions']['approval_workflow'] ?? '') == 'manager_and_hr' ? 'selected' : '' }}>
                                            {{ __('Direct Manager + HR Approval') }}
                                        </option>
                                        <option value="manager_only" {{ ($policies['permissions']['approval_workflow'] ?? '') == 'manager_only' ? 'selected' : '' }}>
                                            {{ __('Direct Manager Only') }}
                                        </option>
                                        <option value="hr_only" {{ ($policies['permissions']['approval_workflow'] ?? '') == 'hr_only' ? 'selected' : '' }}>
                                            {{ __('HR Administration Only') }}
                                        </option>
                                        <option value="auto_approve" {{ ($policies['permissions']['approval_workflow'] ?? '') == 'auto_approve' ? 'selected' : '' }}>
                                            {{ __('Auto-Approve within balance') }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-semibold text-heading d-flex align-items-center justify-content-between mb-1">
                                        <span>{{ __('Max Emergency Permissions') }}</span>
                                        <i class="bx bx-info-circle info-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Exempted from advance notice rule.') }}"></i>
                                    </label>
                                    <div class="position-relative">
                                        <input type="number" step="1" min="0" max="5" class="form-control input-with-unit" name="policies[max_monthly_emergency_permissions]" value="{{ $policies['permissions']['max_monthly_emergency_permissions'] ?? 1 }}">
                                        <span class="unit-badge">{{ __('Times') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ======================================================= -->
                <!-- 5. TAB: NOTICE & END OF SERVICE (EOS) -->
                <!-- ======================================================= -->
                <div class="tab-pane fade" id="tab-eos" role="tabpanel" aria-labelledby="eos-tab">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-body p-4">
                            <!-- 1. Notice Period -->
                            <div class="section-title d-flex align-items-center gap-2">
                                <i class="bx bx-bell text-warning font-20"></i>
                                <span>{{ __('Notice Periods Upon Resignation or Termination') }}</span>
                            </div>
                            <div class="row g-3 mb-4">
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold text-heading d-flex align-items-center justify-content-between mb-1">
                                        <span>{{ __('Notice Period (Monthly Salaried Staff)') }}</span>
                                        <i class="bx bx-info-circle info-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Notice delay period for staff on monthly wages.') }}"></i>
                                    </label>
                                    <div class="position-relative">
                                        <input type="number" step="1" min="0" max="6" class="form-control input-with-unit" name="policies[notice_period_monthly_months]" value="{{ $policies['notice_and_eos']['notice_period_monthly_months'] ?? 3 }}" required>
                                        <span class="unit-badge">{{ __('Months') }}</span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold text-heading d-flex align-items-center justify-content-between mb-1">
                                        <span>{{ __('Notice Period (Non-Monthly Workers)') }}</span>
                                        <i class="bx bx-info-circle info-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Notice delay period for daily, piece-rate, or hourly staff.') }}"></i>
                                    </label>
                                    <div class="position-relative">
                                        <input type="number" step="1" min="0" max="6" class="form-control input-with-unit" name="policies[notice_period_non_monthly_months]" value="{{ $policies['notice_and_eos']['notice_period_non_monthly_months'] ?? 1 }}" required>
                                        <span class="unit-badge">{{ __('Months') }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- 2. EOS Days Criteria -->
                            <div class="section-title d-flex align-items-center gap-2">
                                <i class="bx bx-calculator text-primary font-20"></i>
                                <span>{{ __('End of Service Gratuity Criteria (Days Per Year)') }}</span>
                            </div>
                            <div class="row g-3">
                                <div class="col-12 col-md-6 col-lg-3">
                                    <label class="form-label fw-semibold text-heading d-flex align-items-center justify-content-between mb-1">
                                        <span>{{ __('Monthly Staff (First 5 Years)') }}</span>
                                        <i class="bx bx-info-circle info-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Half month (15 days) per year.') }}"></i>
                                    </label>
                                    <div class="position-relative">
                                        <input type="number" step="1" min="0" max="60" class="form-control input-with-unit" name="policies[eos_days_monthly_first_5_years]" value="{{ $policies['notice_and_eos']['eos_days_monthly_first_5_years'] ?? 15 }}" required>
                                        <span class="unit-badge">{{ __('Days/Yr') }}</span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-lg-3">
                                    <label class="form-label fw-semibold text-heading d-flex align-items-center justify-content-between mb-1">
                                        <span>{{ __('Monthly Staff (After 5 Years)') }}</span>
                                        <i class="bx bx-info-circle info-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Full month (30 days) per year.') }}"></i>
                                    </label>
                                    <div class="position-relative">
                                        <input type="number" step="1" min="0" max="60" class="form-control input-with-unit" name="policies[eos_days_monthly_after_5_years]" value="{{ $policies['notice_and_eos']['eos_days_monthly_after_5_years'] ?? 30 }}" required>
                                        <span class="unit-badge">{{ __('Days/Yr') }}</span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-lg-3">
                                    <label class="form-label fw-semibold text-heading d-flex align-items-center justify-content-between mb-1">
                                        <span>{{ __('Non-Monthly (First 5 Years)') }}</span>
                                        <i class="bx bx-info-circle info-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('10 days per year.') }}"></i>
                                    </label>
                                    <div class="position-relative">
                                        <input type="number" step="1" min="0" max="60" class="form-control input-with-unit" name="policies[eos_days_non_monthly_first_5_years]" value="{{ $policies['notice_and_eos']['eos_days_non_monthly_first_5_years'] ?? 10 }}" required>
                                        <span class="unit-badge">{{ __('Days/Yr') }}</span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-lg-3">
                                    <label class="form-label fw-semibold text-heading d-flex align-items-center justify-content-between mb-1">
                                        <span>{{ __('Non-Monthly (After 5 Years)') }}</span>
                                        <i class="bx bx-info-circle info-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('15 days per year.') }}"></i>
                                    </label>
                                    <div class="position-relative">
                                        <input type="number" step="1" min="0" max="60" class="form-control input-with-unit" name="policies[eos_days_non_monthly_after_5_years]" value="{{ $policies['notice_and_eos']['eos_days_non_monthly_after_5_years'] ?? 15 }}" required>
                                        <span class="unit-badge">{{ __('Days/Yr') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Dynamic EOS Entitlement Tiers Table -->
                    <div class="card shadow-sm border-0 mb-3">
                        <div class="card-header bg-transparent d-flex align-items-center justify-content-between py-3 border-bottom">
                            <div>
                                <h6 class="fw-bold mb-0 text-heading d-flex align-items-center gap-2">
                                    <i class="bx bx-table text-primary font-18"></i>
                                    <span>{{ __('EOS Entitlement Tiers by Termination Type & Service Duration') }}</span>
                                    <i class="bx bx-info-circle info-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Define the entitlement percentage according to resignation vs employer termination and years of service.') }}"></i>
                                </h6>
                            </div>
                            <button type="button" id="btnAddEosRule" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1 shadow-sm">
                                <i class="bx bx-plus font-16"></i>
                                <span>{{ __('Add New Rule') }}</span>
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-tiers align-middle mb-0" id="eosRulesTable">
                                    <thead>
                                        <tr>
                                            <th style="width: 4%;">#</th>
                                            <th style="width: 18%;">{{ __('Type') }}</th>
                                            <th style="width: 25%;">{{ __('Rule Description [Arabic]') }}</th>
                                            <th style="width: 25%;">{{ __('Rule Description [English]') }}</th>
                                            <th style="width: 10%;">{{ __('From (Years)') }}</th>
                                            <th style="width: 10%;">{{ __('To (Years)') }}</th>
                                            <th style="width: 10%;">{{ __('Entitlement (%)') }}</th>
                                            <th style="width: 6%; text-align: center;">{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="eosRulesBody">
                                        @forelse($eosRules as $index => $rule)
                                            <tr data-index="{{ $index }}">
                                                <td class="fw-bold text-muted eos-order">{{ $loop->iteration }}</td>
                                                <td>
                                                    <select class="form-select" name="eos_rules[{{ $index }}][type]">
                                                        <option value="resignation" {{ $rule->type == 'resignation' ? 'selected' : '' }}>{{ __('Resignation') }}</option>
                                                        <option value="termination" {{ $rule->type == 'termination' ? 'selected' : '' }}>{{ __('Employer Termination / End of Contract') }}</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" name="eos_rules[{{ $index }}][name_ar]" value="{{ $rule->name_ar }}" placeholder="{{ __('Description') }}">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" name="eos_rules[{{ $index }}][name_en]" value="{{ $rule->name_en }}" placeholder="{{ __('English Description') }}">
                                                </td>
                                                <td>
                                                    <input type="number" step="0.5" min="0" max="60" class="form-control" name="eos_rules[{{ $index }}][from_years]" value="{{ $rule->from_years }}" required>
                                                </td>
                                                <td>
                                                    <input type="number" step="0.5" min="0" max="60" class="form-control" name="eos_rules[{{ $index }}][to_years]" value="{{ $rule->to_years }}" placeholder="{{ __('Open / Unlimited') }}">
                                                </td>
                                                <td>
                                                    <div class="position-relative">
                                                        <input type="number" step="0.01" min="0" max="100" class="form-control input-with-unit" name="eos_rules[{{ $index }}][percentage]" value="{{ $rule->percentage }}" required>
                                                        <span class="unit-badge">%</span>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-icon btn-text-danger rounded-pill btn-remove-row" title="{{ __('Delete') }}">
                                                        <i class="bx bx-trash font-18 text-danger"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ======================================================= -->
                <!-- 6. TAB: LOAN & ADVANCE POLICIES -->
                <!-- ======================================================= -->
                <div class="tab-pane fade" id="tab-loans" role="tabpanel" aria-labelledby="loans-tab">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-body p-4">

                            <!-- Section 1: Eligibility & Tenure Regulations -->
                            <div class="section-title d-flex align-items-center gap-2">
                                <i class="bx bx-check-shield text-primary font-20"></i>
                                <span>{{ __('Loan Eligibility & Tenure Requirements') }}</span>
                            </div>
                            <div class="row g-3 mb-4">
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold text-heading d-flex align-items-center justify-content-between mb-1">
                                        <span>{{ __('Minimum Service Tenure for Loan') }}</span>
                                        <i class="bx bx-info-circle info-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Minimum continuous service months an employee must complete before being eligible to request a loan or advance.') }}"></i>
                                    </label>
                                    <div class="position-relative">
                                        <input type="number" step="1" min="0" max="60" class="form-control input-with-unit" name="policies[min_service_months]" value="{{ $policies['loans']['min_service_months'] ?? 3 }}" required>
                                        <span class="unit-badge">{{ __('Months') }}</span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <label class="form-label fw-semibold text-heading mb-0" for="allow_multiple_loans">
                                            {{ __('Allow Concurrent / Multiple Active Loans') }}
                                        </label>
                                        <i class="bx bx-info-circle info-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Allow employee to submit another loan request while an active loan is unpaid. If disabled, employee must settle existing debt first.') }}"></i>
                                    </div>
                                    <div class="form-check form-switch mt-2">
                                        <input type="hidden" name="policies[allow_multiple_loans]" value="0">
                                        <input class="form-check-input" type="checkbox" role="switch" id="allow_multiple_loans" name="policies[allow_multiple_loans]" value="1" {{ !empty($policies['loans']['allow_multiple_loans']) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-medium" for="allow_multiple_loans">
                                            {{ __('Allow requesting new loan while an active loan is unpaid') }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 2: Financial Caps & Ceiling Limits -->
                            <div class="section-title d-flex align-items-center gap-2">
                                <i class="bx bx-coin-stack text-warning font-20"></i>
                                <span>{{ __('Financial Ceilings & Salary Deduction Limits') }}</span>
                            </div>
                            <div class="row g-3 mb-4">
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-semibold text-heading d-flex align-items-center justify-content-between mb-1">
                                        <span>{{ __('Max Installment Loan Multiplier') }}</span>
                                        <i class="bx bx-info-circle info-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Maximum loan cap as a multiple of basic salary for installment loans (Default: 3.0x basic salary).') }}"></i>
                                    </label>
                                    <div class="position-relative">
                                        <input type="number" step="0.1" min="0.5" max="12" class="form-control input-with-unit" name="policies[max_salary_multiplier]" value="{{ $policies['loans']['max_salary_multiplier'] ?? 3.0 }}" required>
                                        <span class="unit-badge">x {{ __('Salary') }}</span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-semibold text-heading d-flex align-items-center justify-content-between mb-1">
                                        <span>{{ __('Max Salary Advance Multiplier') }}</span>
                                        <i class="bx bx-info-circle info-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Maximum cap for single-month salary advance (Default: 1.0x basic salary).') }}"></i>
                                    </label>
                                    <div class="position-relative">
                                        <input type="number" step="0.1" min="0.1" max="3" class="form-control input-with-unit" name="policies[max_advance_multiplier]" value="{{ $policies['loans']['max_advance_multiplier'] ?? 1.0 }}" required>
                                        <span class="unit-badge">x {{ __('Salary') }}</span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-semibold text-heading d-flex align-items-center justify-content-between mb-1">
                                        <span>{{ __('Max Monthly Installment Deduction Ceiling') }}</span>
                                        <i class="bx bx-info-circle info-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Maximum allowable monthly deduction for installments as percentage of basic salary to safeguard take-home pay (Standard: 33.3%).') }}"></i>
                                    </label>
                                    <div class="position-relative">
                                        <input type="number" step="0.1" min="5" max="75" class="form-control input-with-unit" name="policies[max_monthly_deduction_percentage]" value="{{ $policies['loans']['max_monthly_deduction_percentage'] ?? 33.3 }}" required>
                                        <span class="unit-badge">%</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 3: Repayment Duration & Governance -->
                            <div class="section-title d-flex align-items-center gap-2">
                                <i class="bx bx-calendar-event text-info font-20"></i>
                                <span>{{ __('Repayment Duration, Approvals & Disbursement') }}</span>
                            </div>
                            <div class="row g-3">
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-semibold text-heading d-flex align-items-center justify-content-between mb-1">
                                        <span>{{ __('Max Repayment Duration') }}</span>
                                        <i class="bx bx-info-circle info-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Maximum allowable number of monthly installments to repay the loan (Standard: 24 months).') }}"></i>
                                    </label>
                                    <div class="position-relative">
                                        <input type="number" step="1" min="1" max="60" class="form-control input-with-unit" name="policies[max_installments_months]" value="{{ $policies['loans']['max_installments_months'] ?? 24 }}" required>
                                        <span class="unit-badge">{{ __('Months') }}</span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-semibold text-heading d-flex align-items-center justify-content-between mb-1">
                                        <span>{{ __('Loan Approval Workflow') }}</span>
                                        <i class="bx bx-info-circle info-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Defines the administrative and financial approval cycle required before loan disbursement.') }}"></i>
                                    </label>
                                    <select class="form-select" name="policies[loan_approval_workflow]">
                                        <option value="manager_and_hr_and_finance" {{ ($policies['loans']['loan_approval_workflow'] ?? '') == 'manager_and_hr_and_finance' ? 'selected' : '' }}>
                                            {{ __('Direct Manager + HR + Financial Department (Recommended)') }}
                                        </option>
                                        <option value="hr_and_finance" {{ ($policies['loans']['loan_approval_workflow'] ?? '') == 'hr_and_finance' ? 'selected' : '' }}>
                                            {{ __('HR Administration + Financial Department') }}
                                        </option>
                                        <option value="manager_and_hr" {{ ($policies['loans']['loan_approval_workflow'] ?? '') == 'manager_and_hr' ? 'selected' : '' }}>
                                            {{ __('Direct Manager + HR Administration') }}
                                        </option>
                                        <option value="hr_only" {{ ($policies['loans']['loan_approval_workflow'] ?? '') == 'hr_only' ? 'selected' : '' }}>
                                            {{ __('HR Administration Only') }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-semibold text-heading d-flex align-items-center justify-content-between mb-1">
                                        <span>{{ __('Default Disbursement Method') }}</span>
                                        <i class="bx bx-info-circle info-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Preset payout method selected when finance disburses approved loan funds.') }}"></i>
                                    </label>
                                    <select class="form-select" name="policies[disbursement_method_default]">
                                        <option value="bank_transfer" {{ ($policies['loans']['disbursement_method_default'] ?? '') == 'bank_transfer' ? 'selected' : '' }}>
                                            {{ __('Bank Transfer (Direct Deposit)') }}
                                        </option>
                                        <option value="cash" {{ ($policies['loans']['disbursement_method_default'] ?? '') == 'cash' ? 'selected' : '' }}>
                                            {{ __('Cash Treasury (Safe)') }}
                                        </option>
                                        <option value="cheque" {{ ($policies['loans']['disbursement_method_default'] ?? '') == 'cheque' ? 'selected' : '' }}>
                                            {{ __('Bank Cheque') }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

            <!-- Bottom Floating Save Bar for convenience -->
            <div class="d-flex align-items-center justify-content-end gap-2 mt-4 pt-2 border-top">
                <button type="submit" class="btn btn-primary shadow d-flex align-items-center gap-2 px-4 py-2">
                    <i class="bx bx-check-circle font-20"></i>
                    <span class="fw-bold">{{ __('Save All Policies & Regulations') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 0. Initialize Bootstrap Tooltips
    function initTooltips() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
    initTooltips();

    // 1. Toggle Rollover Days input based on checkbox
    const rolloverCheckbox = document.getElementById('allow_annual_rollover');
    const rolloverWrapper = document.getElementById('rollover_days_wrapper');
    if (rolloverCheckbox && rolloverWrapper) {
        rolloverCheckbox.addEventListener('change', function() {
            if (this.checked) {
                rolloverWrapper.classList.remove('d-none');
            } else {
                rolloverWrapper.classList.add('d-none');
            }
        });
    }

    // 2. Dynamic Add/Remove for Sick Leave Tiers
    let sickIndex = {{ count($sickLeaveTiers ?? []) }};
    const btnAddSickTier = document.getElementById('btnAddSickTier');
    const sickTiersBody  = document.getElementById('sickTiersBody');

    if (btnAddSickTier && sickTiersBody) {
        btnAddSickTier.addEventListener('click', function() {
            sickIndex++;
            const rowCount = sickTiersBody.querySelectorAll('tr').length + 1;
            const tr = document.createElement('tr');
            tr.setAttribute('data-index', sickIndex);
            tr.innerHTML = `
                <td class="fw-bold text-muted tier-order">${rowCount}</td>
                <td>
                    <input type="text" class="form-control" name="sick_leave_tiers[${sickIndex}][name_ar]" placeholder="{{ __('Tier description (e.g. 15 days full pay)') }}" required>
                </td>
                <td>
                    <input type="text" class="form-control" name="sick_leave_tiers[${sickIndex}][name_en]" placeholder="{{ __('English Description') }}">
                </td>
                <td>
                    <div class="position-relative">
                        <input type="number" min="1" max="180" class="form-control input-with-unit" name="sick_leave_tiers[${sickIndex}][days_count]" value="10" required>
                        <span class="unit-badge">{{ __('Days') }}</span>
                    </div>
                </td>
                <td>
                    <div class="position-relative">
                        <input type="number" step="0.5" min="0" max="100" class="form-control input-with-unit" name="sick_leave_tiers[${sickIndex}][salary_percentage]" value="50" required>
                        <span class="unit-badge">%</span>
                    </div>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-icon btn-text-danger rounded-pill btn-remove-row" title="{{ __('Delete Tier') }}">
                        <i class="bx bx-trash font-18 text-danger"></i>
                    </button>
                </td>
            `;
            sickTiersBody.appendChild(tr);
            reindexOrders(sickTiersBody, '.tier-order');
        });
    }

    // 3. Dynamic Add/Remove for EOS Rules
    let eosIndex = {{ count($eosRules ?? []) }};
    const btnAddEosRule = document.getElementById('btnAddEosRule');
    const eosRulesBody  = document.getElementById('eosRulesBody');

    if (btnAddEosRule && eosRulesBody) {
        btnAddEosRule.addEventListener('click', function() {
            eosIndex++;
            const rowCount = eosRulesBody.querySelectorAll('tr').length + 1;
            const tr = document.createElement('tr');
            tr.setAttribute('data-index', eosIndex);
            tr.innerHTML = `
                <td class="fw-bold text-muted eos-order">${rowCount}</td>
                <td>
                    <select class="form-select" name="eos_rules[${eosIndex}][type]">
                        <option value="resignation">{{ __('Resignation') }}</option>
                        <option value="termination">{{ __('Employer Termination / End of Contract') }}</option>
                    </select>
                </td>
                <td>
                    <input type="text" class="form-control" name="eos_rules[${eosIndex}][name_ar]" placeholder="{{ __('Description') }}">
                </td>
                <td>
                    <input type="text" class="form-control" name="eos_rules[${eosIndex}][name_en]" placeholder="{{ __('English Description') }}">
                </td>
                <td>
                    <input type="number" step="0.5" min="0" max="60" class="form-control" name="eos_rules[${eosIndex}][from_years]" value="0" required>
                </td>
                <td>
                    <input type="number" step="0.5" min="0" max="60" class="form-control" name="eos_rules[${eosIndex}][to_years]" placeholder="{{ __('Open / Unlimited') }}">
                </td>
                <td>
                    <div class="position-relative">
                        <input type="number" step="0.01" min="0" max="100" class="form-control input-with-unit" name="eos_rules[${eosIndex}][percentage]" value="100" required>
                        <span class="unit-badge">%</span>
                    </div>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-icon btn-text-danger rounded-pill btn-remove-row" title="{{ __('Delete') }}">
                        <i class="bx bx-trash font-18 text-danger"></i>
                    </button>
                </td>
            `;
            eosRulesBody.appendChild(tr);
            reindexOrders(eosRulesBody, '.eos-order');
        });
    }

    // 4. Delegated Remove Row Handler
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-remove-row');
        if (btn) {
            const tr = btn.closest('tr');
            const tbody = tr.closest('tbody');
            tr.remove();
            if (tbody.id === 'sickTiersBody') {
                reindexOrders(tbody, '.tier-order');
            } else if (tbody.id === 'eosRulesBody') {
                reindexOrders(tbody, '.eos-order');
            }
        }
    });

    function reindexOrders(tbody, selector) {
        const orders = tbody.querySelectorAll(selector);
        orders.forEach((el, i) => {
            el.textContent = i + 1;
        });
    }

    // 5. Reset Defaults Confirmation
    const btnResetDefaults = document.getElementById('btnResetDefaults');
    if (btnResetDefaults) {
        btnResetDefaults.addEventListener('click', function() {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: "{{ __('Reset Policies to Defaults?') }}",
                    text: "{{ __('This will overwrite all customized numbers and restore standard factory settings.') }}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#696cff',
                    cancelButtonColor: '#8592a3',
                    confirmButtonText: "{{ __('Yes, Reset Now') }}",
                    cancelButtonText: "{{ __('Cancel') }}"
                }).then((result) => {
                    if (result.isConfirmed) {
                        performReset();
                    }
                });
            } else {
                if (confirm("{{ __('Reset all policies to factory defaults?') }}")) {
                    performReset();
                }
            }
        });
    }

    function performReset() {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = "{{ route('hr.settings.policies.reset') }}";
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = "{{ csrf_token() }}";
        form.appendChild(csrf);
        document.body.appendChild(form);
        form.submit();
    }
});
</script>
@endsection
