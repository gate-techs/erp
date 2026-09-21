@extends('layouts/contentNavbarLayout')

@section('title', __('New Job Opening') . ' - ' . config('variables.templateName'))

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
            <a href="{{ route('hr.job-openings.index') }}" class="btn btn-icon btn-sm btn-outline-secondary rounded-circle" title="{{ __('Back') }}">
                <i class="icon-base bx bx-arrow-back font-16"></i>
            </a>
            <h4 class="fw-bold mb-0 d-flex align-items-center gap-2">
                <i class="icon-base bx bx-broadcast text-primary font-24"></i>
                <span>{{ __('New Job Opening') }}</span>
            </h4>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ route('hr.job-openings.index') }}" class="text-muted">{{ __('Job Openings') }}</a></li>
                <li class="breadcrumb-item active text-primary">{{ __('Create Job Opening') }}</li>
            </ol>
        </nav>
    </div>
</div>

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (isset($errors) && $errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <div class="d-flex align-items-start gap-2">
            <i class="icon-base bx bx-error-circle font-22 flex-shrink-0 mt-1"></i>
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

<form id="createJobOpeningForm" action="{{ route('hr.job-openings.store') }}" method="POST">
    @csrf

    <!-- Card 1: Link with Approved Job Requisition -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-transparent border-bottom py-3">
            <h5 class="card-title mb-0 d-flex align-items-center gap-2 text-primary">
                <i class="icon-base bx bx-link font-20"></i>
                <span>{{ __('Job Requisition Link') }}</span>
                <span class="badge bg-label-secondary font-11 fw-normal ms-1">{{ __('Optional') }}</span>
            </h5>
        </div>
        <div class="card-body pt-4">
            <div class="row g-3">
                <div class="col-12 col-md-8">
                    <label class="form-label fw-semibold text-heading small">{{ __('Select Approved Job Requisition') }}</label>
                    <select name="job_requisition_id" id="job_requisition_id" class="select2 form-select @error('job_requisition_id') is-invalid @enderror" data-placeholder="{{ __('-- Direct Opening without Requisition --') }}">
                        <option value="">{{ __('-- Direct Opening without Requisition --') }}</option>
                        @foreach ($requisitions as $req)
                            @php
                                $roleName = $req->jobTitle?->translate(app()->getLocale())?->name ?? $req->jobTitle?->translate('ar')?->name ?? $req->jobTitle?->name ?? '-';
                                $deptName = $req->department?->translate(app()->getLocale())?->name ?? $req->department?->translate('ar')?->name ?? $req->department?->name ?? '-';
                                $isSelected = old('job_requisition_id', $selectedRequisitionId) == $req->id;
                            @endphp
                            <option value="{{ $req->id }}" {{ $isSelected ? 'selected' : '' }}>
                                {{ $req->requisition_number }} &bull; {{ $roleName }} ({{ $deptName }}) - [{{ __('Headcount') }}: {{ $req->headcount_needed }}]
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text text-muted small">
                        <i class="bx bx-info-circle me-1"></i>
                        {{ __('Linking with an approved requisition automatically prefills the position data, requirements, and advances the requisition workflow.') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 2: Core Job Information -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-transparent border-bottom py-3">
            <h5 class="card-title mb-0 d-flex align-items-center gap-2 text-primary">
                <i class="icon-base bx bx-briefcase font-20"></i>
                <span>{{ __('Core Job Information') }}</span>
            </h5>
        </div>
        <div class="card-body pt-4">
            <div class="row g-3">
                <!-- Title [Arabic] -->
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-heading small required">{{ __('Public Job Title [Arabic]') }}</label>
                    <input type="text" name="title_ar" id="title_ar" class="form-control @error('title_ar') is-invalid @enderror" value="{{ old('title_ar', $prefillData['title_ar'] ?? ($prefillData['title'] ?? '')) }}" placeholder="{{ __('e.g. مطور برمجيات أول') }}" dir="rtl" required>
                    @error('title_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Title [English] -->
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-heading small">{{ __('Public Job Title [English]') }}</label>
                    <input type="text" name="title_en" id="title_en" class="form-control @error('title_en') is-invalid @enderror" value="{{ old('title_en', $prefillData['title_en'] ?? '') }}" placeholder="{{ __('e.g. Senior Software Engineer') }}" dir="ltr">
                    @error('title_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Job Title / Role -->
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-heading small required">{{ __('Job Role') }}</label>
                    <select name="job_title_id" id="job_title_id" class="select2 form-select @error('job_title_id') is-invalid @enderror" required>
                        <option value="">{{ __('-- Select Job Role --') }}</option>
                        @foreach ($jobTitles as $titleItem)
                            <option value="{{ $titleItem->id }}" {{ old('job_title_id', $prefillData['job_title_id'] ?? '') == $titleItem->id ? 'selected' : '' }}>
                                {{ $titleItem->translate(app()->getLocale())?->name ?? $titleItem->translate('ar')?->name ?? $titleItem->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('job_title_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Department -->
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-heading small required">{{ __('Department') }}</label>
                    <select name="department_id" id="department_id" class="select2 form-select @error('department_id') is-invalid @enderror" required>
                        <option value="">{{ __('-- Select Department --') }}</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id', $prefillData['department_id'] ?? '') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->translate(app()->getLocale())?->name ?? $dept->translate('ar')?->name ?? $dept->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('department_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Branch -->
                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold text-heading small">{{ __('Branch / Location') }}</label>
                    <select name="branch_id" id="branch_id" class="select2 form-select @error('branch_id') is-invalid @enderror">
                        <option value="">{{ __('-- All / Any Branch --') }}</option>
                        @foreach ($branches as $br)
                            <option value="{{ $br->id }}" {{ old('branch_id', $prefillData['branch_id'] ?? '') == $br->id ? 'selected' : '' }}>
                                {{ $br->translate(app()->getLocale())?->name ?? $br->translate('ar')?->name ?? $br->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('branch_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Employment Type -->
                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold text-heading small required">{{ __('Employment Type') }}</label>
                    <select name="employment_type" id="employment_type" class="form-select @error('employment_type') is-invalid @enderror" required>
                        @foreach ($empTypes as $et)
                            <option value="{{ $et->value }}" {{ old('employment_type', $prefillData['employment_type'] ?? '') == $et->value ? 'selected' : '' }}>
                                {{ $et->label() }}
                            </option>
                        @endforeach
                    </select>
                    @error('employment_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Workplace Type -->
                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold text-heading small required">{{ __('Workplace Type') }}</label>
                    <select name="workplace_type" id="workplace_type" class="form-select @error('workplace_type') is-invalid @enderror" required>
                        @foreach ($workplaceTypes as $wt)
                            <option value="{{ $wt->value }}" {{ old('workplace_type', 'on_site') == $wt->value ? 'selected' : '' }}>
                                {{ $wt->label() }}
                            </option>
                        @endforeach
                    </select>
                    @error('workplace_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Posting Type -->
                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold text-heading small required">{{ __('Posting Type') }}</label>
                    <select name="posting_type" id="posting_type" class="form-select @error('posting_type') is-invalid @enderror" required>
                        @foreach ($postingTypes as $pt)
                            <option value="{{ $pt->value }}" {{ old('posting_type', 'both') == $pt->value ? 'selected' : '' }}>
                                {{ $pt->label() }}
                            </option>
                        @endforeach
                    </select>
                    @error('posting_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Card 3: Requirements & Compensation -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-transparent border-bottom py-3">
            <h5 class="card-title mb-0 d-flex align-items-center gap-2 text-primary">
                <i class="icon-base bx bx-dollar-circle font-20"></i>
                <span>{{ __('Compensation & Qualifications') }}</span>
            </h5>
        </div>
        <div class="card-body pt-4">
            <div class="row g-3">
                <!-- Open Positions -->
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold text-heading small required">{{ __('Target Open Positions') }}</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bx bx-group"></i></span>
                        <input type="number" name="open_positions" id="open_positions" class="form-control @error('open_positions') is-invalid @enderror" min="1" value="{{ old('open_positions', $prefillData['open_positions'] ?? 1) }}" required>
                    </div>
                    @error('open_positions') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Experience Min -->
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold text-heading small">{{ __('Min Experience (Years)') }}</label>
                    <input type="number" name="min_experience_years" id="min_experience_years" class="form-control @error('min_experience_years') is-invalid @enderror" min="0" value="{{ old('min_experience_years', $prefillData['min_experience_years'] ?? '') }}" placeholder="0">
                    @error('min_experience_years') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Experience Max -->
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold text-heading small">{{ __('Max Experience (Years)') }}</label>
                    <input type="number" name="max_experience_years" id="max_experience_years" class="form-control @error('max_experience_years') is-invalid @enderror" min="0" value="{{ old('max_experience_years', $prefillData['max_experience_years'] ?? '') }}" placeholder="5">
                    @error('max_experience_years') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Education Level [Arabic] -->
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-heading small">{{ __('Education Level [Arabic]') }}</label>
                    <input type="text" name="education_level_ar" id="education_level_ar" class="form-control @error('education_level_ar') is-invalid @enderror" value="{{ old('education_level_ar', $prefillData['education_level_ar'] ?? ($prefillData['education_level'] ?? '')) }}" placeholder="{{ __('e.g. بكالوريوس في علوم الحاسب') }}" dir="rtl">
                    @error('education_level_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Education Level [English] -->
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-heading small">{{ __('Education Level [English]') }}</label>
                    <input type="text" name="education_level_en" id="education_level_en" class="form-control @error('education_level_en') is-invalid @enderror" value="{{ old('education_level_en', $prefillData['education_level_en'] ?? '') }}" placeholder="{{ __('e.g. Bachelor Degree in Computer Science') }}" dir="ltr">
                    @error('education_level_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Min Salary -->
                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold text-heading small">{{ __('Min Salary') }}</label>
                    <input type="number" step="0.01" name="min_salary" id="min_salary" class="form-control @error('min_salary') is-invalid @enderror" value="{{ old('min_salary', $prefillData['min_salary'] ?? '') }}" placeholder="0.00">
                    @error('min_salary') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Max Salary -->
                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold text-heading small">{{ __('Max Salary') }}</label>
                    <input type="number" step="0.01" name="max_salary" id="max_salary" class="form-control @error('max_salary') is-invalid @enderror" value="{{ old('max_salary', $prefillData['max_salary'] ?? '') }}" placeholder="0.00">
                    @error('max_salary') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Currency -->
                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold text-heading small">{{ __('Currency') }}</label>
                    <input type="text" name="currency" id="currency" class="form-control @error('currency') is-invalid @enderror" value="{{ old('currency', $prefillData['currency'] ?? 'KWD') }}" maxlength="10">
                    @error('currency') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Hide Salary Checkbox -->
                <div class="col-12 col-md-3 d-flex align-items-center pt-md-4">
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" name="hide_salary" id="hide_salary" value="1" {{ old('hide_salary') ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold text-heading small" for="hide_salary">
                            {{ __('Hide Salary from Applicants') }}
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 4: Publication Schedule & Status -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-transparent border-bottom py-3">
            <h5 class="card-title mb-0 d-flex align-items-center gap-2 text-primary">
                <i class="icon-base bx bx-calendar-event font-20"></i>
                <span>{{ __('Publishing Schedule & Status') }}</span>
            </h5>
        </div>
        <div class="card-body pt-4">
            <div class="row g-3">
                <!-- Publish Date -->
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold text-heading small">{{ __('Publish Date') }}</label>
                    <input type="date" name="publish_date" id="publish_date" class="form-control js-flatpickr-date @error('publish_date') is-invalid @enderror" value="{{ old('publish_date', date('Y-m-d')) }}">
                    @error('publish_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Closing Date -->
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold text-heading small">{{ __('Application Deadline (Optional)') }}</label>
                    <input type="date" name="closing_date" id="closing_date" class="form-control js-flatpickr-date @error('closing_date') is-invalid @enderror" value="{{ old('closing_date') }}">
                    <div class="form-text text-muted small">{{ __('Leave blank for open-ended posting.') }}</div>
                    @error('closing_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Initial Status -->
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold text-heading small">{{ __('Initial Status') }}</label>
                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                        <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>{{ __('Draft (Save privately)') }}</option>
                        <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>{{ __('Published (Make active now)') }}</option>
                    </select>
                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Card 5: Job Description & Details (Bilingual) -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-transparent border-bottom py-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
            <h5 class="card-title mb-0 d-flex align-items-center gap-2 text-primary">
                <i class="icon-base bx bx-detail font-20"></i>
                <span>{{ __('Job Details & Description') }}</span>
            </h5>
            <ul class="nav nav-pills card-header-pills" role="tablist">
                <li class="nav-item">
                    <button type="button" class="nav-link active py-1 px-3" role="tab" data-bs-toggle="tab" data-bs-target="#tab-lang-ar">
                        <i class="bx bx-globe me-1"></i> {{ __('Arabic') }}
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link py-1 px-3" role="tab" data-bs-toggle="tab" data-bs-target="#tab-lang-en">
                        <i class="bx bx-globe me-1"></i> {{ __('English') }}
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body pt-4">
            <div class="tab-content p-0">
                <!-- Tab Arabic -->
                <div class="tab-pane fade show active" id="tab-lang-ar" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold text-heading small">{{ __('Job Summary / Overview [Arabic]') }}</label>
                            <textarea name="job_summary_ar" id="job_summary_ar" rows="3" class="form-control" dir="rtl" placeholder="{{ __('نبذة عامة وموجزة عن الوظيفة وأهدافها...') }}">{{ old('job_summary_ar', $prefillData['job_summary_ar'] ?? ($prefillData['job_summary'] ?? '')) }}</textarea>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-heading small">{{ __('Key Responsibilities [Arabic]') }}</label>
                            <textarea name="responsibilities_ar" id="responsibilities_ar" rows="5" class="form-control" dir="rtl" placeholder="{{ __('قائمة المسؤوليات والمهام اليومية المطلوبة...') }}">{{ old('responsibilities_ar', $prefillData['responsibilities_ar'] ?? ($prefillData['responsibilities'] ?? '')) }}</textarea>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-heading small">{{ __('Requirements & Qualifications [Arabic]') }}</label>
                            <textarea name="requirements_ar" id="requirements_ar" rows="5" class="form-control" dir="rtl" placeholder="{{ __('قائمة المهارات والشهادات والخبرات المطلوبة...') }}">{{ old('requirements_ar', $prefillData['requirements_ar'] ?? ($prefillData['requirements'] ?? '')) }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-heading small">{{ __('Benefits & Perks [Arabic]') }}</label>
                            <textarea name="benefits_ar" id="benefits_ar" rows="3" class="form-control" dir="rtl" placeholder="{{ __('المزايا، التأمين الصحي، بدلات إضافية...') }}">{{ old('benefits_ar', $prefillData['benefits_ar'] ?? ($prefillData['benefits'] ?? '')) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Tab English -->
                <div class="tab-pane fade" id="tab-lang-en" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold text-heading small">{{ __('Job Summary / Overview [English]') }}</label>
                            <textarea name="job_summary_en" id="job_summary_en" rows="3" class="form-control" dir="ltr" placeholder="{{ __('Short overview of the role and key objectives...') }}">{{ old('job_summary_en', $prefillData['job_summary_en'] ?? '') }}</textarea>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-heading small">{{ __('Key Responsibilities [English]') }}</label>
                            <textarea name="responsibilities_en" id="responsibilities_en" rows="5" class="form-control" dir="ltr" placeholder="{{ __('List primary duties and day-to-day responsibilities...') }}">{{ old('responsibilities_en', $prefillData['responsibilities_en'] ?? '') }}</textarea>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-heading small">{{ __('Requirements & Qualifications [English]') }}</label>
                            <textarea name="requirements_en" id="requirements_en" rows="5" class="form-control" dir="ltr" placeholder="{{ __('Required skills, education, certifications, and experience...') }}">{{ old('requirements_en', $prefillData['requirements_en'] ?? '') }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-heading small">{{ __('Benefits & Perks [English]') }}</label>
                            <textarea name="benefits_en" id="benefits_en" rows="3" class="form-control" dir="ltr" placeholder="{{ __('Health insurance, bonuses, flexible working...') }}">{{ old('benefits_en', $prefillData['benefits_en'] ?? '') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Action Bar -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <a href="{{ route('hr.job-openings.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-3">
                <i class="bx bx-x me-1 font-18"></i>
                {{ __('Cancel') }}
            </a>
            <button type="submit" class="btn btn-primary d-flex align-items-center gap-2 px-5 py-2 shadow-sm rounded-3 fw-semibold">
                <i class="icon-base bx bx-save font-18"></i>
                <span>{{ __('Save Job Opening') }}</span>
            </button>
        </div>
    </div>
</form>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const $ = window.jQuery || window.$;

    // 1. Initialize Select2
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

    // 2. Auto-fill from selected Job Requisition via AJAX
    $('#job_requisition_id').on('change', function () {
        const reqId = $(this).val();
        if (!reqId) return;

        const detailsUrl = "{{ route('hr.job-openings.requisition-details') }}";

        $.ajax({
            url: detailsUrl,
            type: 'GET',
            data: { id: reqId },
            success: function (res) {
                if (res.success && res.data) {
                    const d = res.data;
                    if (d.title && !$('#title_ar').val()) $('#title_ar').val(d.title);
                    if (d.job_title_id) $('#job_title_id').val(d.job_title_id).trigger('change');
                    if (d.department_id) $('#department_id').val(d.department_id).trigger('change');
                    if (d.branch_id) $('#branch_id').val(d.branch_id).trigger('change');
                    if (d.employment_type) $('#employment_type').val(d.employment_type);
                    if (d.open_positions) $('#open_positions').val(d.open_positions);
                    if (d.min_experience_years !== null) $('#min_experience_years').val(d.min_experience_years);
                    if (d.max_experience_years !== null) $('#max_experience_years').val(d.max_experience_years);
                    if (d.education_level && !$('#education_level_ar').val()) $('#education_level_ar').val(d.education_level);
                    if (d.min_salary !== null) $('#min_salary').val(d.min_salary);
                    if (d.max_salary !== null) $('#max_salary').val(d.max_salary);
                    if (d.currency) $('#currency').val(d.currency);
                    if (d.responsibilities && !$('#responsibilities_ar').val()) $('#responsibilities_ar').val(d.responsibilities);
                    if (d.requirements && !$('#requirements_ar').val()) $('#requirements_ar').val(d.requirements);
                    if (d.job_summary && !$('#job_summary_ar').val()) $('#job_summary_ar').val(d.job_summary);
                }
            }
        });
    });
});
</script>
@endsection
