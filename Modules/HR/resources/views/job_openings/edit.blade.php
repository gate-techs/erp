@extends('layouts/contentNavbarLayout')

@section('title', __('Edit Job Opening') . ' - ' . $opening->opening_code . ' - ' . config('variables.templateName'))

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
            <a href="{{ route('hr.job-openings.show', $opening->id) }}" class="btn btn-icon btn-sm btn-outline-secondary rounded-circle" title="{{ __('Back') }}">
                <i class="icon-base bx bx-arrow-back font-16"></i>
            </a>
            <h4 class="fw-bold mb-0 d-flex align-items-center gap-2 flex-wrap">
                <i class="icon-base bx bx-broadcast text-primary font-24"></i>
                <span>{{ __('Edit Job Opening') }}</span>
                <span class="badge bg-label-primary rounded-pill px-2.5 py-1 font-12 fw-semibold">{{ $opening->opening_code }}</span>
                <span class="badge {{ $opening->status->badgeClass() }} rounded-pill px-2.5 py-1 font-12 fw-semibold">{{ $opening->status->label() }}</span>
            </h4>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ route('hr.job-openings.index') }}" class="text-muted">{{ __('Job Openings') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hr.job-openings.show', $opening->id) }}" class="text-muted">{{ $opening->opening_code }}</a></li>
                <li class="breadcrumb-item active text-primary">{{ __('Edit') }}</li>
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

@php
    $transAr = $opening->translate('ar');
    $transEn = $opening->translate('en');
@endphp

<form id="editJobOpeningForm" action="{{ route('hr.job-openings.update', $opening->id) }}" method="POST">
    @csrf
    @method('PUT')

    <!-- Card 1: Link with Job Requisition -->
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
                    <label class="form-label fw-semibold text-heading small">{{ __('Linked Job Requisition') }}</label>
                    <select name="job_requisition_id" id="job_requisition_id" class="select2 form-select @error('job_requisition_id') is-invalid @enderror">
                        <option value="">{{ __('-- None / Direct Opening --') }}</option>
                        @foreach ($requisitions as $req)
                            @php
                                $roleName = $req->jobTitle?->translate(app()->getLocale())?->name ?? $req->jobTitle?->translate('ar')?->name ?? $req->jobTitle?->name ?? '-';
                                $deptName = $req->department?->translate(app()->getLocale())?->name ?? $req->department?->translate('ar')?->name ?? $req->department?->name ?? '-';
                                $isSelected = old('job_requisition_id', $opening->job_requisition_id) == $req->id;
                            @endphp
                            <option value="{{ $req->id }}" {{ $isSelected ? 'selected' : '' }}>
                                {{ $req->requisition_number }} &bull; {{ $roleName }} ({{ $deptName }})
                            </option>
                        @endforeach
                    </select>
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
                    <input type="text" name="title_ar" id="title_ar" class="form-control @error('title_ar') is-invalid @enderror" value="{{ old('title_ar', $transAr?->title ?? $opening->title) }}" dir="rtl" required>
                    @error('title_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Title [English] -->
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-heading small">{{ __('Public Job Title [English]') }}</label>
                    <input type="text" name="title_en" id="title_en" class="form-control @error('title_en') is-invalid @enderror" value="{{ old('title_en', $transEn?->title ?? '') }}" dir="ltr">
                    @error('title_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Job Title / Role -->
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-heading small required">{{ __('Job Role') }}</label>
                    <select name="job_title_id" id="job_title_id" class="select2 form-select @error('job_title_id') is-invalid @enderror" required>
                        <option value="">{{ __('-- Select Job Role --') }}</option>
                        @foreach ($jobTitles as $titleItem)
                            <option value="{{ $titleItem->id }}" {{ old('job_title_id', $opening->job_title_id) == $titleItem->id ? 'selected' : '' }}>
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
                            <option value="{{ $dept->id }}" {{ old('department_id', $opening->department_id) == $dept->id ? 'selected' : '' }}>
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
                            <option value="{{ $br->id }}" {{ old('branch_id', $opening->branch_id) == $br->id ? 'selected' : '' }}>
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
                            @php
                                $val = is_object($opening->employment_type) ? $opening->employment_type->value : $opening->employment_type;
                            @endphp
                            <option value="{{ $et->value }}" {{ old('employment_type', $val) == $et->value ? 'selected' : '' }}>
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
                            @php
                                $val = is_object($opening->workplace_type) ? $opening->workplace_type->value : $opening->workplace_type;
                            @endphp
                            <option value="{{ $wt->value }}" {{ old('workplace_type', $val) == $wt->value ? 'selected' : '' }}>
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
                            @php
                                $val = is_object($opening->posting_type) ? $opening->posting_type->value : $opening->posting_type;
                            @endphp
                            <option value="{{ $pt->value }}" {{ old('posting_type', $val) == $pt->value ? 'selected' : '' }}>
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
                        <input type="number" name="open_positions" id="open_positions" class="form-control @error('open_positions') is-invalid @enderror" min="1" value="{{ old('open_positions', $opening->open_positions) }}" required>
                    </div>
                    @error('open_positions') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Experience Min -->
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold text-heading small">{{ __('Min Experience (Years)') }}</label>
                    <input type="number" name="min_experience_years" id="min_experience_years" class="form-control @error('min_experience_years') is-invalid @enderror" min="0" value="{{ old('min_experience_years', $opening->min_experience_years) }}">
                    @error('min_experience_years') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Experience Max -->
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold text-heading small">{{ __('Max Experience (Years)') }}</label>
                    <input type="number" name="max_experience_years" id="max_experience_years" class="form-control @error('max_experience_years') is-invalid @enderror" min="0" value="{{ old('max_experience_years', $opening->max_experience_years) }}">
                    @error('max_experience_years') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Education Level [Arabic] -->
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-heading small">{{ __('Education Level [Arabic]') }}</label>
                    <input type="text" name="education_level_ar" id="education_level_ar" class="form-control @error('education_level_ar') is-invalid @enderror" value="{{ old('education_level_ar', $transAr?->education_level ?? $opening->education_level) }}" dir="rtl">
                    @error('education_level_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Education Level [English] -->
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-heading small">{{ __('Education Level [English]') }}</label>
                    <input type="text" name="education_level_en" id="education_level_en" class="form-control @error('education_level_en') is-invalid @enderror" value="{{ old('education_level_en', $transEn?->education_level ?? '') }}" dir="ltr">
                    @error('education_level_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Min Salary -->
                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold text-heading small">{{ __('Min Salary') }}</label>
                    <input type="number" step="0.01" name="min_salary" id="min_salary" class="form-control @error('min_salary') is-invalid @enderror" value="{{ old('min_salary', $opening->min_salary) }}">
                    @error('min_salary') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Max Salary -->
                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold text-heading small">{{ __('Max Salary') }}</label>
                    <input type="number" step="0.01" name="max_salary" id="max_salary" class="form-control @error('max_salary') is-invalid @enderror" value="{{ old('max_salary', $opening->max_salary) }}">
                    @error('max_salary') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Currency -->
                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold text-heading small">{{ __('Currency') }}</label>
                    <input type="text" name="currency" id="currency" class="form-control @error('currency') is-invalid @enderror" value="{{ old('currency', $opening->currency ?? 'KWD') }}" maxlength="10">
                    @error('currency') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Hide Salary Checkbox -->
                <div class="col-12 col-md-3 d-flex align-items-center pt-md-4">
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" name="hide_salary" id="hide_salary" value="1" {{ old('hide_salary', $opening->hide_salary) ? 'checked' : '' }}>
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
                    <input type="date" name="publish_date" id="publish_date" class="form-control @error('publish_date') is-invalid @enderror" value="{{ old('publish_date', $opening->publish_date?->format('Y-m-d')) }}">
                    @error('publish_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Closing Date -->
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold text-heading small">{{ __('Application Deadline (Optional)') }}</label>
                    <input type="date" name="closing_date" id="closing_date" class="form-control @error('closing_date') is-invalid @enderror" value="{{ old('closing_date', $opening->closing_date?->format('Y-m-d')) }}">
                    <div class="form-text text-muted small">{{ __('Leave blank for open-ended posting.') }}</div>
                    @error('closing_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Status -->
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold text-heading small">{{ __('Status') }}</label>
                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                        @foreach (\Modules\HR\Enums\JobOpeningStatus::cases() as $st)
                            @php
                                $stVal = is_object($opening->status) ? $opening->status->value : $opening->status;
                            @endphp
                            <option value="{{ $st->value }}" {{ old('status', $stVal) == $st->value ? 'selected' : '' }}>
                                {{ $st->label() }}
                            </option>
                        @endforeach
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
                            <textarea name="job_summary_ar" id="job_summary_ar" rows="3" class="form-control" dir="rtl">{{ old('job_summary_ar', $transAr?->job_summary ?? $opening->job_summary) }}</textarea>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-heading small">{{ __('Key Responsibilities [Arabic]') }}</label>
                            <textarea name="responsibilities_ar" id="responsibilities_ar" rows="5" class="form-control" dir="rtl">{{ old('responsibilities_ar', $transAr?->responsibilities ?? $opening->responsibilities) }}</textarea>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-heading small">{{ __('Requirements & Qualifications [Arabic]') }}</label>
                            <textarea name="requirements_ar" id="requirements_ar" rows="5" class="form-control" dir="rtl">{{ old('requirements_ar', $transAr?->requirements ?? $opening->requirements) }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-heading small">{{ __('Benefits & Perks [Arabic]') }}</label>
                            <textarea name="benefits_ar" id="benefits_ar" rows="3" class="form-control" dir="rtl">{{ old('benefits_ar', $transAr?->benefits ?? $opening->benefits) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Tab English -->
                <div class="tab-pane fade" id="tab-lang-en" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold text-heading small">{{ __('Job Summary / Overview [English]') }}</label>
                            <textarea name="job_summary_en" id="job_summary_en" rows="3" class="form-control" dir="ltr">{{ old('job_summary_en', $transEn?->job_summary ?? '') }}</textarea>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-heading small">{{ __('Key Responsibilities [English]') }}</label>
                            <textarea name="responsibilities_en" id="responsibilities_en" rows="5" class="form-control" dir="ltr">{{ old('responsibilities_en', $transEn?->responsibilities ?? '') }}</textarea>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-heading small">{{ __('Requirements & Qualifications [English]') }}</label>
                            <textarea name="requirements_en" id="requirements_en" rows="5" class="form-control" dir="ltr">{{ old('requirements_en', $transEn?->requirements ?? '') }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-heading small">{{ __('Benefits & Perks [English]') }}</label>
                            <textarea name="benefits_en" id="benefits_en" rows="3" class="form-control" dir="ltr">{{ old('benefits_en', $transEn?->benefits ?? '') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Action Bar -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <a href="{{ route('hr.job-openings.show', $opening->id) }}" class="btn btn-outline-secondary px-4 py-2 rounded-3">
                <i class="bx bx-x me-1 font-18"></i>
                {{ __('Cancel') }}
            </a>
            <button type="submit" class="btn btn-primary d-flex align-items-center gap-2 px-5 py-2 shadow-sm rounded-3 fw-semibold">
                <i class="icon-base bx bx-save font-18"></i>
                <span>{{ __('Update Job Opening') }}</span>
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
});
</script>
@endsection
