@extends('layouts/contentNavbarLayout')

@section('title', __('Edit Job Offer') . ' - ' . config('variables.templateName'))

@section('content')
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <i class="bx bx-edit text-primary font-24"></i>
            <span>{{ __('Edit Job Offer') }}: {{ $offer->offer_code }}</span>
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hr.job-offers.index') }}" class="text-muted">{{ __('Job Offers') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hr.job-offers.show', $offer->id) }}" class="text-muted">{{ $offer->offer_code }}</a></li>
                <li class="breadcrumb-item active text-primary">{{ __('Edit') }}</li>
            </ol>
        </nav>
    </div>

    <div>
        <a href="{{ route('hr.job-offers.show', $offer->id) }}" class="btn btn-outline-secondary d-flex align-items-center gap-1 shadow-sm px-3 py-2 rounded-3">
            <i class="bx bx-arrow-back font-18"></i>
            <span class="fw-semibold">{{ __('Back to Offer') }}</span>
        </a>
    </div>
</div>

@if (isset($errors) && $errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm rounded-3" role="alert">
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<form action="{{ route('hr.job-offers.update', $offer->id) }}" method="POST" id="jobOfferEditForm">
    @csrf
    @method('PUT')

    <div class="row g-4">
        <!-- Left Column: Primary Details & Compensation -->
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h5 class="card-title mb-0 fw-bold d-flex align-items-center gap-2">
                        <i class="bx bx-user-pin text-primary font-20"></i>
                        <span>{{ __('Candidate & Position Details') }}</span>
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="p-3 bg-light rounded-3 mb-3 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-md bg-label-primary rounded-circle me-3 d-flex align-items-center justify-content-center">
                                <span class="fw-bold font-14">{{ $offer->candidate?->initials }}</span>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold text-heading">{{ $offer->candidate?->full_name }}</h6>
                                <small class="text-muted">{{ $offer->candidate?->email }} | {{ $offer->candidate?->phone }}</small>
                            </div>
                        </div>
                        <span class="badge {{ $offer->status->badgeClass() }} rounded-pill px-3 py-1.5 font-12">
                            <i class="{{ $offer->status->icon() }} me-1"></i>{{ $offer->status->label() }}
                        </span>
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold" for="department_id">{{ __('Target Department') }}</label>
                            <select name="department_id" id="department_id" class="select2 form-select">
                                <option value="">{{ __('-- Select department --') }}</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ old('department_id', $offer->department_id) == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->translate(app()->getLocale())?->name ?? $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold" for="job_title_id">{{ __('Target Job Title') }}</label>
                            <select name="job_title_id" id="job_title_id" class="select2 form-select">
                                <option value="">{{ __('-- Select job title --') }}</option>
                                @foreach($jobTitles as $title)
                                    <option value="{{ $title->id }}" {{ old('job_title_id', $offer->job_title_id) == $title->id ? 'selected' : '' }}>
                                        {{ $title->translate(app()->getLocale())?->title ?? $title->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold" for="employment_type">{{ __('Employment Type') }} <span class="text-danger">*</span></label>
                            <select name="employment_type" id="employment_type" class="form-select" required>
                                @foreach($employmentTypes as $type)
                                    <option value="{{ $type->value }}" {{ old('employment_type', $offer->employment_type) == $type->value ? 'selected' : '' }}>
                                        {{ $type->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold" for="currency">{{ __('Currency') }} <span class="text-danger">*</span></label>
                            <input type="text" name="currency" id="currency" class="form-control fw-bold" value="{{ old('currency', $offer->currency ?: $systemCurrency) }}" readonly required>
                            <small class="text-muted">{{ __('System standard currency') }} ({{ $systemCurrencySymbol }})</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial Package Card -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h5 class="card-title mb-0 fw-bold d-flex align-items-center gap-2">
                        <i class="bx bx-dollar-circle text-success font-20"></i>
                        <span>{{ __('Financial Compensation Breakdown') }}</span>
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold" for="basic_salary">{{ __('Basic Salary') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" step="0.01" name="basic_salary" id="basic_salary" class="form-control salary-input fw-bold font-15" value="{{ old('basic_salary', $offer->basic_salary) }}" required>
                                <span class="input-group-text curr-label">{{ $offer->currency ?: $systemCurrency }}</span>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold" for="housing_allowance">{{ __('Housing Allowance') }}</label>
                            <div class="input-group">
                                <input type="number" step="0.01" name="housing_allowance" id="housing_allowance" class="form-control salary-input" value="{{ old('housing_allowance', $offer->housing_allowance) }}">
                                <span class="input-group-text curr-label">{{ $offer->currency ?: $systemCurrency }}</span>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold" for="transportation_allowance">{{ __('Transportation Allowance') }}</label>
                            <div class="input-group">
                                <input type="number" step="0.01" name="transportation_allowance" id="transportation_allowance" class="form-control salary-input" value="{{ old('transportation_allowance', $offer->transportation_allowance) }}">
                                <span class="input-group-text curr-label">{{ $offer->currency ?: $systemCurrency }}</span>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold" for="other_allowances">{{ __('Other Allowances') }}</label>
                            <div class="input-group">
                                <input type="number" step="0.01" name="other_allowances" id="other_allowances" class="form-control salary-input" value="{{ old('other_allowances', $offer->other_allowances) }}">
                                <span class="input-group-text curr-label">{{ $offer->currency ?: $systemCurrency }}</span>
                            </div>
                        </div>

                        <!-- Live Total Card -->
                        <div class="col-12">
                            <div class="p-3 rounded-3 bg-label-success border border-success border-opacity-25 d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted small fw-bold d-block">{{ __('Calculated Monthly Gross Total') }}</span>
                                    <h3 class="fw-bolder text-success mb-0" id="displayTotalSalary">{{ number_format($offer->total_salary, 2) }} {{ $offer->currency ?: $systemCurrency }}</h3>
                                </div>
                                <div class="avatar avatar-md bg-success text-white rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bx bx-check font-22"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Terms and Notes Card -->
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h5 class="card-title mb-0 fw-bold d-flex align-items-center gap-2">
                        <i class="bx bx-detail text-primary font-20"></i>
                        <span>{{ __('Terms, Conditions & Offer Notes') }}</span>
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="terms_and_conditions">{{ __('Special Terms & Conditions (Appears on Offer Letter)') }}</label>
                        <textarea name="terms_and_conditions" id="terms_and_conditions" rows="4" class="form-control">{{ old('terms_and_conditions', $offer->terms_and_conditions) }}</textarea>
                    </div>

                    <div>
                        <label class="form-label fw-semibold" for="notes">{{ __('Internal HR Notes (Private)') }}</label>
                        <textarea name="notes" id="notes" rows="2" class="form-control">{{ old('notes', $offer->notes) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Dates & Policy Settings -->
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h5 class="card-title mb-0 fw-bold d-flex align-items-center gap-2">
                        <i class="bx bx-calendar text-primary font-20"></i>
                        <span>{{ __('Key Dates & Validity') }}</span>
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="offer_date">{{ __('Offer Issue Date') }} <span class="text-danger">*</span></label>
                        <input type="date" name="offer_date" id="offer_date" class="form-control js-flatpickr-date" value="{{ old('offer_date', $offer->offer_date->format('Y-m-d')) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="expiry_date">{{ __('Offer Expiry Date') }}</label>
                        <input type="date" name="expiry_date" id="expiry_date" class="form-control js-flatpickr-date" value="{{ old('expiry_date', $offer->expiry_date?->format('Y-m-d')) }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="joining_date">{{ __('Expected Joining Date') }}</label>
                        <input type="date" name="joining_date" id="joining_date" class="form-control js-flatpickr-date" value="{{ old('joining_date', $offer->joining_date?->format('Y-m-d')) }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="probation_months">{{ __('Probation Period (Months)') }}</label>
                        <input type="number" name="probation_months" id="probation_months" class="form-control" value="{{ old('probation_months', $offer->probation_months) }}" min="0" max="12">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="contract_duration_months">{{ __('Contract Duration (Months)') }}</label>
                        <input type="number" name="contract_duration_months" id="contract_duration_months" class="form-control" value="{{ old('contract_duration_months', $offer->contract_duration_months) }}">
                    </div>
                </div>
            </div>

            <!-- Update Action Card -->
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <button type="submit" class="btn btn-primary w-100 py-2.5 font-15 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                        <i class="bx bx-save font-18"></i>
                        <span>{{ __('Update Job Offer') }}</span>
                    </button>
                    <a href="{{ route('hr.job-offers.show', $offer->id) }}" class="btn btn-label-secondary w-100 mt-2 py-2">
                        {{ __('Cancel') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const $ = window.jQuery || window.$;

    if (typeof window.initSelect2 === 'function') {
        window.initSelect2();
    } else if ($.fn.select2) {
        $('.select2').select2({ width: '100%' });
    }

    function calculateTotal() {
        const basic     = parseFloat($('#basic_salary').val()) || 0;
        const housing   = parseFloat($('#housing_allowance').val()) || 0;
        const transport = parseFloat($('#transportation_allowance').val()) || 0;
        const other     = parseFloat($('#other_allowances').val()) || 0;
        const curr      = $('#currency').val() || '{{ $systemCurrency }}';

        const total = basic + housing + transport + other;
        $('#displayTotalSalary').text(total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ' + curr);
        $('.curr-label').text(curr);
    }

    $('.salary-input, #currency').on('input change', calculateTotal);
});
</script>
@endsection
