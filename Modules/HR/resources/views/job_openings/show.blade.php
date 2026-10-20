@extends('layouts/contentNavbarLayout')

@section('title', $opening->opening_code . ' - ' . $opening->title . ' - ' . config('variables.templateName'))

@section('content')
<!-- Page Header -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
            <!-- Left Side: Code, Status, Titles & Breadcrumbs -->
            <div>
                <!-- Top Pills Row -->
                <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                    <a href="{{ route('hr.job-openings.index') }}" class="btn btn-icon btn-sm btn-outline-secondary rounded-circle" title="{{ __('Back') }}">
                        <i class="icon-base bx bx-arrow-back font-16"></i>
                    </a>
                    <span class="badge bg-label-primary rounded-pill px-2.5 py-1 font-12 fw-semibold">
                        {{ $opening->opening_code }}
                    </span>
                    <span class="badge {{ $opening->status->badgeClass() }} rounded-pill px-2.5 py-1 font-12 fw-semibold d-inline-flex align-items-center gap-1">
                        <i class="icon-base {{ $opening->status->icon() }} font-13"></i>
                        <span>{{ $opening->status->label() }}</span>
                    </span>
                    <span class="badge {{ $opening->workplace_type->badgeClass() }} rounded-pill px-2.5 py-1 font-12 fw-semibold d-inline-flex align-items-center gap-1">
                        <i class="{{ $opening->workplace_type->icon() }} font-13"></i>
                        <span>{{ $opening->workplace_type->label() }}</span>
                    </span>
                    <span class="badge {{ $opening->posting_type->badgeClass() }} rounded-pill px-2.5 py-1 font-12 fw-semibold">
                        {{ $opening->posting_type->label() }}
                    </span>
                </div>

                <!-- Main Job Title -->
                <h3 class="fw-bold text-heading mb-1">
                    {{ $opening->translate(app()->getLocale())?->title ?? $opening->translate('ar')?->title ?? $opening->translate('en')?->title ?? $opening->title }}
                </h3>

                <!-- Secondary Language Title (if present) -->
                @php
                    $otherLocale = app()->getLocale() === 'ar' ? 'en' : 'ar';
                    $otherTitle = $opening->translate($otherLocale)?->title;
                @endphp
                @if($otherTitle && $otherTitle !== ($opening->translate(app()->getLocale())?->title ?? $opening->title))
                    <div class="text-muted small mb-2 d-flex align-items-center gap-1">
                        <i class="bx bx-globe text-primary font-14"></i>
                        <span>{{ $otherTitle }}</span>
                    </div>
                @endif

                <!-- Breadcrumbs -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 small text-muted">
                        <li class="breadcrumb-item"><a href="{{ route('hr.job-openings.index') }}" class="text-muted">{{ __('Job Openings') }}</a></li>
                        <li class="breadcrumb-item active text-primary">{{ $opening->opening_code }}</li>
                    </ol>
                </nav>
            </div>

            <!-- Right Side: Action Toolbar -->
            <div class="d-flex flex-wrap align-items-center gap-2">
                @if ($opening->status !== \Modules\HR\Enums\JobOpeningStatus::CLOSED)
                    @can('hr.job-openings.edit')
                    <a href="{{ route('hr.job-openings.edit', $opening->id) }}" class="btn btn-outline-primary d-flex align-items-center gap-1 rounded-3 px-3 py-2">
                        <i class="icon-base bx bx-edit font-16"></i>
                        <span>{{ __('Edit') }}</span>
                    </a>
                    @endcan
                @endif

                @if ($opening->status === \Modules\HR\Enums\JobOpeningStatus::DRAFT || $opening->status === \Modules\HR\Enums\JobOpeningStatus::ON_HOLD)
                    @can('hr.job-openings.publish')
                    <form action="{{ route('hr.job-openings.publish', $opening->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Publish this job opening now?') }}');">
                        @csrf
                        <button type="submit" class="btn btn-success d-flex align-items-center gap-1 shadow-sm rounded-3 px-3 py-2">
                            <i class="icon-base bx bx-broadcast font-16"></i>
                            <span>{{ __('Publish Opening') }}</span>
                        </button>
                    </form>
                    @endcan
                @endif

                @if ($opening->status === \Modules\HR\Enums\JobOpeningStatus::PUBLISHED)
                    @can('hr.job-openings.close')
                    <form action="{{ route('hr.job-openings.close', $opening->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Close this job opening?') }}');">
                        @csrf
                        <button type="submit" class="btn btn-warning d-flex align-items-center gap-1 shadow-sm rounded-3 px-3 py-2">
                            <i class="icon-base bx bx-x-circle font-16"></i>
                            <span>{{ __('Close Opening') }}</span>
                        </button>
                    </form>
                    @endcan
                @endif

                @if ($opening->status === \Modules\HR\Enums\JobOpeningStatus::DRAFT)
                    @can('hr.job-openings.delete')
                    <form action="{{ route('hr.job-openings.destroy', $opening->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this draft opening?') }}');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger d-flex align-items-center gap-1 rounded-3 px-3 py-2">
                            <i class="icon-base bx bx-trash font-16"></i>
                            <span>{{ __('Delete') }}</span>
                        </button>
                    </form>
                    @endcan
                @endif
            </div>
        </div>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <i class="bx bx-error me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Quick Metrics Row -->
<div class="row g-3 mb-4">
    <!-- Open vs Filled Positions -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small">{{ __('Hiring Progress') }}</span>
                    <span class="badge bg-label-primary rounded-pill px-2.5 py-1 font-12 fw-semibold">{{ $opening->filled_positions }} / {{ $opening->open_positions }}</span>
                </div>
                @php
                    $pct = $opening->open_positions > 0 ? round(($opening->filled_positions / $opening->open_positions) * 100) : 0;
                @endphp
                <div class="progress mb-2" style="height: 8px;">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $pct }}%" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <small class="text-muted d-block">
                    <strong>{{ $opening->remaining_positions }}</strong> {{ __('positions remaining') }}
                </small>
            </div>
        </div>
    </div>

    <!-- Salary Range -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1">{{ __('Offered Salary Range') }}</span>
                    @if ($opening->hide_salary)
                        <h5 class="fw-bold mb-0 text-muted fst-italic">{{ __('Undisclosed') }}</h5>
                        <small class="text-warning">{{ __('Hidden from candidates') }}</small>
                    @elseif ($opening->min_salary && $opening->max_salary)
                        <h5 class="fw-bold mb-0 text-primary">{{ number_format($opening->min_salary) }} - {{ number_format($opening->max_salary) }}</h5>
                        <small class="text-muted">{{ $opening->currency ?? 'KWD' }}</small>
                    @elseif ($opening->min_salary)
                        <h5 class="fw-bold mb-0 text-primary">{{ __('From') }} {{ number_format($opening->min_salary) }}</h5>
                        <small class="text-muted">{{ $opening->currency ?? 'KWD' }}</small>
                    @else
                        <h5 class="fw-bold mb-0 text-muted">-</h5>
                    @endif
                </div>
                <div class="avatar avatar-md bg-label-success rounded-3 p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-dollar font-22"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Experience Required -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1">{{ __('Required Experience') }}</span>
                    @if ($opening->min_experience_years !== null && $opening->max_experience_years !== null)
                        <h5 class="fw-bold mb-0 text-primary">{{ $opening->min_experience_years }} - {{ $opening->max_experience_years }} {{ __('Years') }}</h5>
                    @elseif ($opening->min_experience_years !== null)
                        <h5 class="fw-bold mb-0 text-primary">{{ $opening->min_experience_years }}+ {{ __('Years') }}</h5>
                    @else
                        <h5 class="fw-bold mb-0 text-muted">{{ __('Not Specified') }}</h5>
                    @endif
                    <small class="text-muted">{{ $opening->translate(app()->getLocale())?->education_level ?? $opening->translate('ar')?->education_level ?? $opening->education_level ?? __('Any Education') }}</small>
                </div>
                <div class="avatar avatar-md bg-label-info rounded p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-award font-22"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Dates / Timeline -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1">{{ __('Timeline') }}</span>
                    <div class="small fw-semibold text-heading">
                        <i class="bx bx-calendar-check text-success me-1"></i>
                        {{ $opening->publish_date ? $opening->publish_date->format('Y-m-d') : __('Not Published') }}
                    </div>
                    <small class="text-muted d-block mt-1">
                        <i class="bx bx-timer text-warning me-1"></i>
                        {{ __('Closes:') }} {{ $opening->closing_date ? $opening->closing_date->format('Y-m-d') : __('Open-ended') }}
                    </small>
                </div>
                <div class="avatar avatar-md bg-label-secondary rounded p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-calendar font-22"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Details Section -->
<div class="row g-4">
    <!-- Left Column: Detailed Content with Bilingual Tabs -->
    <div class="col-12 col-lg-8">
        @php
            $ar = $opening->translate('ar');
            $en = $opening->translate('en');
        @endphp

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2 py-3">
                <ul class="nav nav-pills card-header-pills mb-0" id="jobDetailsLocaleTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ app()->getLocale() === 'ar' ? 'active' : '' }} d-flex align-items-center gap-1" id="tab-ar-btn" data-bs-toggle="pill" data-bs-target="#tab-locale-ar" type="button" role="tab">
                            <span>🇸🇦</span>
                            <span class="fw-semibold">{{ __('العربية (Arabic)') }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ app()->getLocale() === 'en' ? 'active' : '' }} d-flex align-items-center gap-1" id="tab-en-btn" data-bs-toggle="pill" data-bs-target="#tab-locale-en" type="button" role="tab">
                            <span>🇬🇧</span>
                            <span class="fw-semibold">{{ __('English (الإنجليزية)') }}</span>
                        </button>
                    </li>
                </ul>
                <span class="badge bg-label-secondary font-12"><i class="bx bx-globe me-1"></i>{{ __('Bilingual Details') }}</span>
            </div>
            <div class="card-body pt-4">
                <div class="tab-content p-0" id="jobDetailsTabContent">
                    <!-- Arabic Tab Pane -->
                    <div class="tab-pane fade {{ app()->getLocale() === 'ar' ? 'show active' : '' }}" id="tab-locale-ar" role="tabpanel" dir="rtl">
                        <div class="mb-4">
                            <h5 class="fw-bold text-primary mb-1">{{ $ar?->title ?? $opening->title }}</h5>
                            @if ($ar?->education_level)
                                <span class="badge bg-label-info font-12"><i class="bx bx-book me-1"></i>{{ $ar->education_level }}</span>
                            @endif
                        </div>

                        <!-- Arabic Job Summary -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-dark d-flex align-items-center gap-2 mb-2">
                                <i class="bx bx-info-circle text-primary"></i>
                                <span>{{ __('Job Summary') }}</span>
                            </h6>
                            <p class="text-body mb-0" style="white-space: pre-line;">{{ $ar?->job_summary ?: ($opening->job_summary ?: __('No summary provided.')) }}</p>
                        </div>

                        <hr class="my-4">

                        <!-- Arabic Responsibilities -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-dark d-flex align-items-center gap-2 mb-2">
                                <i class="bx bx-task text-primary"></i>
                                <span>{{ __('Key Responsibilities') }}</span>
                            </h6>
                            @if ($ar?->responsibilities || $opening->responsibilities)
                                <div class="text-body" style="white-space: pre-line;">{{ $ar?->responsibilities ?: $opening->responsibilities }}</div>
                            @else
                                <p class="text-muted fst-italic mb-0">{{ __('No specific responsibilities listed.') }}</p>
                            @endif
                        </div>

                        <hr class="my-4">

                        <!-- Arabic Requirements -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-dark d-flex align-items-center gap-2 mb-2">
                                <i class="bx bx-check-shield text-primary"></i>
                                <span>{{ __('Requirements & Qualifications') }}</span>
                            </h6>
                            @if ($ar?->requirements || $opening->requirements)
                                <div class="text-body" style="white-space: pre-line;">{{ $ar?->requirements ?: $opening->requirements }}</div>
                            @else
                                <p class="text-muted fst-italic mb-0">{{ __('No specific requirements listed.') }}</p>
                            @endif
                        </div>

                        <!-- Arabic Benefits -->
                        @if ($ar?->benefits || $opening->benefits)
                            <hr class="my-4">
                            <div class="mb-0">
                                <h6 class="fw-bold text-dark d-flex align-items-center gap-2 mb-2">
                                    <i class="bx bx-gift text-primary"></i>
                                    <span>{{ __('Benefits & Perks') }}</span>
                                </h6>
                                <div class="text-body" style="white-space: pre-line;">{{ $ar?->benefits ?: $opening->benefits }}</div>
                            </div>
                        @endif
                    </div>

                    <!-- English Tab Pane -->
                    <div class="tab-pane fade {{ app()->getLocale() === 'en' ? 'show active' : '' }}" id="tab-locale-en" role="tabpanel" dir="ltr">
                        <div class="mb-4">
                            <h5 class="fw-bold text-primary mb-1">{{ $en?->title ?? $opening->title }}</h5>
                            @if ($en?->education_level)
                                <span class="badge bg-label-info font-12"><i class="bx bx-book me-1"></i>{{ $en->education_level }}</span>
                            @endif
                        </div>

                        <!-- English Job Summary -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-dark d-flex align-items-center gap-2 mb-2">
                                <i class="bx bx-info-circle text-primary"></i>
                                <span>Job Summary</span>
                            </h6>
                            <p class="text-body mb-0" style="white-space: pre-line;">{{ $en?->job_summary ?: 'No summary provided in English.' }}</p>
                        </div>

                        <hr class="my-4">

                        <!-- English Responsibilities -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-dark d-flex align-items-center gap-2 mb-2">
                                <i class="bx bx-task text-primary"></i>
                                <span>Key Responsibilities</span>
                            </h6>
                            @if ($en?->responsibilities)
                                <div class="text-body" style="white-space: pre-line;">{{ $en->responsibilities }}</div>
                            @else
                                <p class="text-muted fst-italic mb-0">No specific responsibilities listed in English.</p>
                            @endif
                        </div>

                        <hr class="my-4">

                        <!-- English Requirements -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-dark d-flex align-items-center gap-2 mb-2">
                                <i class="bx bx-check-shield text-primary"></i>
                                <span>Requirements & Qualifications</span>
                            </h6>
                            @if ($en?->requirements)
                                <div class="text-body" style="white-space: pre-line;">{{ $en->requirements }}</div>
                            @else
                                <p class="text-muted fst-italic mb-0">No specific requirements listed in English.</p>
                            @endif
                        </div>

                        <!-- English Benefits -->
                        @if ($en?->benefits)
                            <hr class="my-4">
                            <div class="mb-0">
                                <h6 class="fw-bold text-dark d-flex align-items-center gap-2 mb-2">
                                    <i class="bx bx-gift text-primary"></i>
                                    <span>Benefits & Perks</span>
                                </h6>
                                <div class="text-body" style="white-space: pre-line;">{{ $en->benefits }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Sidebar Metadata -->
    <div class="col-12 col-lg-4">
        <!-- Classification Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-bottom py-3">
                <h5 class="card-title mb-0 d-flex align-items-center gap-2 text-primary">
                    <i class="icon-base bx bx-briefcase-alt font-20"></i>
                    <span>{{ __('Opening Classification') }}</span>
                </h5>
            </div>
            <div class="card-body pt-3">
                <ul class="list-unstyled mb-0 d-flex flex-column gap-3">
                    <li class="d-flex align-items-start justify-content-between">
                        <span class="text-muted small">{{ __('Job Role') }}</span>
                        <span class="fw-semibold text-heading small text-end">
                            {{ $opening->jobTitle?->translate(app()->getLocale())?->name ?? $opening->jobTitle?->translate('ar')?->name ?? $opening->jobTitle?->name }}
                        </span>
                    </li>
                    <li class="d-flex align-items-start justify-content-between">
                        <span class="text-muted small">{{ __('Department') }}</span>
                        <span class="fw-semibold text-heading small text-end">
                            {{ $opening->department?->translate(app()->getLocale())?->name ?? $opening->department?->translate('ar')?->name ?? $opening->department?->name }}
                        </span>
                    </li>
                    <li class="d-flex align-items-start justify-content-between">
                        <span class="text-muted small">{{ __('Branch / Location') }}</span>
                        <span class="fw-semibold text-heading small text-end">
                            {{ $opening->branch?->translate(app()->getLocale())?->name ?? $opening->branch?->translate('ar')?->name ?? $opening->branch?->name ?? __('All Branches') }}
                        </span>
                    </li>
                    <li class="d-flex align-items-start justify-content-between">
                        <span class="text-muted small">{{ __('Employment Type') }}</span>
                        <span class="badge bg-label-info rounded-pill px-2.5 py-1 font-11 fw-semibold">
                            {{ is_object($opening->employment_type) ? $opening->employment_type->label() : $opening->employment_type }}
                        </span>
                    </li>
                    <li class="d-flex align-items-start justify-content-between">
                        <span class="text-muted small">{{ __('Workplace') }}</span>
                        <span class="badge {{ $opening->workplace_type->badgeClass() }} rounded-pill px-2.5 py-1 font-11 fw-semibold">
                            {{ $opening->workplace_type->label() }}
                        </span>
                    </li>
                    <li class="d-flex align-items-start justify-content-between">
                        <span class="text-muted small">{{ __('Posting Type') }}</span>
                        <span class="badge {{ $opening->posting_type->badgeClass() }} rounded-pill px-2.5 py-1 font-11 fw-semibold">
                            {{ $opening->posting_type->label() }}
                        </span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Linked Requisition Card -->
        @if ($opening->requisition)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h5 class="card-title mb-0 d-flex align-items-center gap-2 text-primary">
                        <i class="icon-base bx bx-link font-20"></i>
                        <span>{{ __('Linked Job Requisition') }}</span>
                    </h5>
                </div>
                <div class="card-body pt-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <a href="{{ route('hr.job-requisitions.show', $opening->requisition->id) }}" class="fw-bold text-heading">
                            {{ $opening->requisition->requisition_number }}
                        </a>
                        <span class="badge {{ $opening->requisition->status->badgeClass() }} rounded-pill px-2.5 py-1 font-11 fw-semibold">
                            {{ $opening->requisition->status->label() }}
                        </span>
                    </div>
                    <small class="text-muted d-block mb-3">
                        {{ $opening->requisition->jobTitle?->translate(app()->getLocale())?->name ?? $opening->requisition->jobTitle?->name }}
                        &bull; {{ __('Needed') }}: {{ $opening->requisition->headcount_needed }}
                    </small>
                    <a href="{{ route('hr.job-requisitions.show', $opening->requisition->id) }}" class="btn btn-outline-primary btn-sm w-100">
                        <i class="bx bx-show me-1"></i> {{ __('View Requisition') }}
                    </a>
                </div>
            </div>
        @endif

        <!-- Audit & Tracking Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-bottom py-3">
                <h5 class="card-title mb-0 d-flex align-items-center gap-2 text-muted">
                    <i class="icon-base bx bx-history font-20"></i>
                    <span>{{ __('Audit Trail') }}</span>
                </h5>
            </div>
            <div class="card-body pt-3">
                <ul class="list-unstyled mb-0 d-flex flex-column gap-2 small">
                    <li class="d-flex justify-content-between">
                        <span class="text-muted">{{ __('Created By') }}:</span>
                        <span class="fw-semibold">{{ $opening->creator?->name ?? '-' }}</span>
                    </li>
                    <li class="d-flex justify-content-between">
                        <span class="text-muted">{{ __('Created At') }}:</span>
                        <span>{{ $opening->created_at?->format('Y-m-d H:i') ?? '-' }}</span>
                    </li>
                    @if ($opening->publisher)
                        <li class="d-flex justify-content-between">
                            <span class="text-muted">{{ __('Published By') }}:</span>
                            <span class="fw-semibold">{{ $opening->publisher->name }}</span>
                        </li>
                    @endif
                    <li class="d-flex justify-content-between">
                        <span class="text-muted">{{ __('Last Updated') }}:</span>
                        <span>{{ $opening->updated_at?->format('Y-m-d H:i') ?? '-' }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
