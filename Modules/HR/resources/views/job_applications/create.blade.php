@extends('layouts/contentNavbarLayout')

@section('title', __('New Candidate Application') . ' - ' . config('variables.templateName'))

@section('vendor-style')
    @vite([
        'resources/assets/vendor/libs/select2/select2.scss'
    ])
@endsection

@section('vendor-script')
    @vite([
        'resources/assets/vendor/libs/select2/select2.js'
    ])
@endsection

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <a href="{{ route('hr.job-applications.index') }}" class="btn btn-icon btn-sm btn-outline-secondary rounded-circle" title="{{ __('Back') }}">
                <i class="icon-base bx bx-arrow-back font-16"></i>
            </a>
            <h4 class="fw-bold mb-0 d-flex align-items-center gap-2">
                <i class="icon-base bx bx-user-plus text-primary font-24"></i>
                <span>{{ __('New Candidate Application') }}</span>
            </h4>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ route('hr.job-applications.index') }}" class="text-muted">{{ __('Candidate Pipeline') }}</a></li>
                <li class="breadcrumb-item active text-primary">{{ __('Add Candidate') }}</li>
            </ol>
        </nav>
    </div>
</div>

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <i class="bx bx-error me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (isset($errors) && $errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <div class="d-flex gap-2">
            <i class="bx bx-error-circle font-20 mt-1"></i>
            <div>
                <strong class="d-block mb-1">{{ __('Please correct the following errors:') }}</strong>
                <ul class="mb-0 ps-3 small">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<form id="createCandidateForm" action="{{ route('hr.job-applications.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <!-- Card 1: Job Opening Selection -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-transparent border-bottom py-3">
            <h5 class="card-title mb-0 d-flex align-items-center gap-2 text-primary">
                <i class="icon-base bx bx-briefcase font-20"></i>
                <span>{{ __('Target Job Opening') }}</span>
                <span class="badge bg-label-danger font-11 fw-normal ms-1">{{ __('Required') }}</span>
            </h5>
        </div>
        <div class="card-body pt-4">
            <div class="row g-3">
                <div class="col-12 col-md-8">
                    <label class="form-label fw-semibold text-heading small required">{{ __('Select Published Job Opening') }}</label>
                    <select name="job_opening_id" id="job_opening_id" class="select2 form-select @error('job_opening_id') is-invalid @enderror" required>
                        <option value="">{{ __('-- Select Target Opening --') }}</option>
                        @foreach ($jobOpenings as $op)
                            @php
                                $opTitle = $op->translate(app()->getLocale())?->title ?? $op->title;
                                $isSelected = (string)old('job_opening_id', $selectedOpeningId) === (string)$op->id;
                            @endphp
                            <option value="{{ $op->id }}" {{ $isSelected ? 'selected' : '' }}>
                                {{ $op->opening_code }} &bull; {{ $opTitle }}
                            </option>
                        @endforeach
                    </select>
                    @error('job_opening_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold text-heading small">{{ __('Initial Pipeline Stage') }}</label>
                    <select name="stage" id="stage" class="form-select @error('stage') is-invalid @enderror">
                        @foreach ($stages as $stg)
                            <option value="{{ $stg->value }}" {{ old('stage', 'new') === $stg->value ? 'selected' : '' }}>
                                {{ $stg->label() }}
                            </option>
                        @endforeach
                    </select>
                    @error('stage') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Card 2: Personal & Contact Information -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-transparent border-bottom py-3">
            <h5 class="card-title mb-0 d-flex align-items-center gap-2 text-primary">
                <i class="icon-base bx bx-user font-20"></i>
                <span>{{ __('Candidate Personal Details') }}</span>
            </h5>
        </div>
        <div class="card-body pt-4">
            <div class="row g-3">
                <!-- First Name -->
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-heading small required">{{ __('First Name') }}</label>
                    <input type="text" name="first_name" id="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}" placeholder="{{ __('e.g. سارة') }}" required>
                    @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Last Name -->
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-heading small">{{ __('Last Name / Family') }}</label>
                    <input type="text" name="last_name" id="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}" placeholder="{{ __('e.g. أحمد') }}">
                    @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Email -->
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-heading small required">{{ __('Email Address') }}</label>
                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="candidate@example.com" required>
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Phone -->
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-heading small">{{ __('Phone Number') }}</label>
                    <input type="text" name="phone" id="phone" class="form-control js-phone-input @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="+965 xxxxxxxx" dir="ltr">
                    @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Nationality -->
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-heading small">{{ __('Nationality') }}</label>
                    <input type="text" name="nationality" id="nationality" class="form-control @error('nationality') is-invalid @enderror" value="{{ old('nationality') }}" placeholder="{{ __('e.g. كويتي / مصري / أردني') }}">
                    @error('nationality') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- City -->
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-heading small">{{ __('City / Residence') }}</label>
                    <input type="text" name="city" id="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city') }}" placeholder="{{ __('e.g. مدينة الكويت') }}">
                    @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Card 3: Professional Experience & Qualifications -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-transparent border-bottom py-3">
            <h5 class="card-title mb-0 d-flex align-items-center gap-2 text-primary">
                <i class="icon-base bx bx-award font-20"></i>
                <span>{{ __('Professional Background & Expectations') }}</span>
            </h5>
        </div>
        <div class="card-body pt-4">
            <div class="row g-3">
                <!-- Current Title -->
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-heading small">{{ __('Current Job Title') }}</label>
                    <input type="text" name="current_job_title" id="current_job_title" class="form-control @error('current_job_title') is-invalid @enderror" value="{{ old('current_job_title') }}" placeholder="{{ __('e.g. معلمة لغة إنجليزية') }}">
                    @error('current_job_title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Current Company -->
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-heading small">{{ __('Current / Previous Employer') }}</label>
                    <input type="text" name="current_company" id="current_company" class="form-control @error('current_company') is-invalid @enderror" value="{{ old('current_company') }}" placeholder="{{ __('e.g. مدرسة الأمل الدولية') }}">
                    @error('current_company') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Total Experience Years -->
                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold text-heading small">{{ __('Total Experience (Years)') }}</label>
                    <input type="number" step="0.5" name="total_experience_years" id="total_experience_years" class="form-control @error('total_experience_years') is-invalid @enderror" value="{{ old('total_experience_years') }}" placeholder="3.5">
                    @error('total_experience_years') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Expected Salary -->
                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold text-heading small">{{ __('Expected Monthly Salary') }}</label>
                    <input type="number" step="0.01" name="expected_salary" id="expected_salary" class="form-control @error('expected_salary') is-invalid @enderror" value="{{ old('expected_salary') }}" placeholder="600.00">
                    @error('expected_salary') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Currency (Fixed to KWD) -->
                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold text-heading small">{{ __('Currency') }}</label>
                    <input type="text" name="currency" id="currency" class="form-control" value="KWD" readonly>
                    <div class="form-text text-muted small">{{ __('دينار كويتي (KWD)') }}</div>
                </div>

                <!-- Notice Period -->
                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold text-heading small">{{ __('Notice Period (Days)') }}</label>
                    <input type="number" name="notice_period_days" id="notice_period_days" class="form-control @error('notice_period_days') is-invalid @enderror" value="{{ old('notice_period_days', 30) }}" placeholder="30">
                    @error('notice_period_days') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Sourcing Channel -->
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold text-heading small">{{ __('Application Source') }}</label>
                    <select name="source" id="source" class="form-select">
                        <option value="LinkedIn" {{ old('source') === 'LinkedIn' ? 'selected' : '' }}>LinkedIn</option>
                        <option value="Direct" {{ old('source', 'Direct') === 'Direct' ? 'selected' : '' }}>{{ __('Direct Application') }}</option>
                        <option value="Career Portal" {{ old('source') === 'Career Portal' ? 'selected' : '' }}>{{ __('Career Portal') }}</option>
                        <option value="Referral" {{ old('source') === 'Referral' ? 'selected' : '' }}>{{ __('Employee Referral') }}</option>
                        <option value="Recruitment Agency" {{ old('source') === 'Recruitment Agency' ? 'selected' : '' }}>{{ __('Agency') }}</option>
                    </select>
                </div>

                <!-- LinkedIn URL -->
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold text-heading small">{{ __('LinkedIn Profile URL') }}</label>
                    <input type="url" name="linkedin_url" id="linkedin_url" class="form-control" value="{{ old('linkedin_url') }}" placeholder="https://linkedin.com/in/...">
                </div>

                <!-- Portfolio / Website -->
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold text-heading small">{{ __('Portfolio / Website') }}</label>
                    <input type="url" name="portfolio_url" id="portfolio_url" class="form-control" value="{{ old('portfolio_url') }}" placeholder="https://...">
                </div>

                <!-- CV / Resume Upload -->
                <div class="col-12">
                    <label class="form-label fw-semibold text-heading small">{{ __('Upload CV / Resume (PDF / Word, Max 10MB)') }}</label>
                    <input type="file" name="resume" id="resume" class="form-control @error('resume') is-invalid @enderror" accept=".pdf,.doc,.docx">
                    @error('resume') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Card 4: Cover Letter & Notes -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-transparent border-bottom py-3">
            <h5 class="card-title mb-0 d-flex align-items-center gap-2 text-primary">
                <i class="icon-base bx bx-file font-20"></i>
                <span>{{ __('Cover Letter & Internal Notes') }}</span>
            </h5>
        </div>
        <div class="card-body pt-4">
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-semibold text-heading small">{{ __('Cover Letter') }}</label>
                    <textarea name="cover_letter" id="cover_letter" rows="4" class="form-control" placeholder="{{ __('Candidate intro letter or summary notes...') }}">{{ old('cover_letter') }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold text-heading small">{{ __('Internal Notes') }}</label>
                    <textarea name="notes" id="notes" rows="3" class="form-control" placeholder="{{ __('Confidential recruiter notes...') }}">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Action Bar (Save Button at the Bottom) -->
    <div class="card shadow-sm border-0 mb-5">
        <div class="card-body p-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <a href="{{ route('hr.job-applications.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-3">
                <i class="bx bx-x me-1 font-18"></i>
                {{ __('Cancel') }}
            </a>
            <button type="submit" class="btn btn-primary d-flex align-items-center gap-2 px-5 py-2 shadow-sm rounded-3 fw-semibold">
                <i class="icon-base bx bx-save font-18"></i>
                <span>{{ __('Save Candidate Application') }}</span>
            </button>
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
        $('.select2').each(function () {
            if (!$(this).hasClass('select2-hidden-accessible')) {
                $(this).select2({
                    placeholder: $(this).data('placeholder') || '',
                    allowClear: false,
                    width: '100%'
                });
            }
        });
    }
});
</script>
@endsection
