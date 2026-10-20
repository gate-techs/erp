@extends('layouts/contentNavbarLayout')

@section('title', $application->application_code . ' - ' . $application->candidate?->full_name . ' - ' . config('variables.templateName'))

@section('content')
<!-- Modern Header Card -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
            <!-- Left Side -->
            <div>
                <!-- Top Pills Row -->
                <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                    <a href="{{ route('hr.job-applications.index') }}" class="btn btn-icon btn-sm btn-outline-secondary rounded-circle" title="{{ __('Back') }}">
                        <i class="icon-base bx bx-arrow-back font-16"></i>
                    </a>
                    <span class="badge bg-label-primary rounded-pill px-2.5 py-1 font-12 fw-semibold">
                        {{ $application->application_code }}
                    </span>
                    <span class="badge {{ $application->stage->badgeClass() }} rounded-pill px-2.5 py-1 font-12 fw-semibold d-inline-flex align-items-center gap-1" id="badgeStageCurrent">
                        <i class="icon-base {{ $application->stage->icon() }} font-13"></i>
                        <span>{{ $application->stage->label() }}</span>
                    </span>
                    <span class="badge {{ $application->status->badgeClass() }} rounded-pill px-2.5 py-1 font-12 fw-semibold">
                        {{ $application->status->label() }}
                    </span>
                    @if ($application->jobOpening)
                        <span class="badge bg-label-info rounded-pill px-2.5 py-1 font-12 fw-semibold">
                            <i class="bx bx-briefcase font-12 me-1"></i>{{ $application->jobOpening->opening_code }}
                        </span>
                    @endif
                </div>

                <!-- Candidate Name -->
                <h3 class="fw-bold text-heading mb-1 d-flex align-items-center gap-2 flex-wrap">
                    <span>{{ $application->candidate?->full_name }}</span>
                    @if ($application->candidate?->current_job_title)
                        <span class="text-muted fs-6 fw-normal">({{ $application->candidate->current_job_title }})</span>
                    @endif
                </h3>

                @if ($application->jobOpening)
                    @php
                        $opTitle = $application->jobOpening->translate(app()->getLocale())?->title ?? $application->jobOpening->title;
                    @endphp
                    <div class="text-muted small mb-2 d-flex align-items-center gap-1">
                        <i class="bx bx-target-lock text-primary font-14"></i>
                        <span>{{ __('Applying for:') }} <strong>{{ $opTitle }}</strong></span>
                    </div>
                @endif

                <!-- Breadcrumbs -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 small text-muted">
                        <li class="breadcrumb-item"><a href="{{ route('hr.job-applications.index') }}" class="text-muted">{{ __('Candidate Pipeline') }}</a></li>
                        <li class="breadcrumb-item active text-primary">{{ $application->application_code }}</li>
                    </ol>
                </nav>
            </div>

            <!-- Right Side Action Buttons -->
            <div class="d-flex flex-wrap align-items-center gap-2">
                @if ($application->candidate?->resume_path)
                    @can('hr.job-applications.download-resume')
                    <a href="{{ route('hr.job-applications.download-resume', $application->id) }}" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm rounded-3 px-3 py-2">
                        <i class="icon-base bx bx-download font-16"></i>
                        <span>{{ __('Download CV / Resume') }}</span>
                    </a>
                    @endcan
                @endif

                @can('hr.interviews.create')
                <a href="{{ route('hr.interviews.create', ['application_id' => $application->id]) }}" class="btn btn-outline-primary d-flex align-items-center gap-1 rounded-3 px-3 py-2">
                    <i class="icon-base bx bx-calendar-plus font-16"></i>
                    <span>{{ __('Schedule Interview') }}</span>
                </a>
                @endcan

                @can('hr.job-offers.create')
                <a href="{{ route('hr.job-offers.create', ['application_id' => $application->id]) }}" class="btn btn-outline-success d-flex align-items-center gap-1 rounded-3 px-3 py-2">
                    <i class="icon-base bx bx-gift font-16"></i>
                    <span>{{ __('Issue Job Offer') }}</span>
                </a>
                @endcan

                @if ($application->status !== \Modules\HR\Enums\ApplicationStatus::REJECTED)
                    @can('hr.job-applications.reject')
                    <button type="button" class="btn btn-outline-danger d-flex align-items-center gap-1 rounded-3 px-3 py-2" data-bs-toggle="modal" data-bs-target="#rejectCandidateModal">
                        <i class="icon-base bx bx-user-x font-16"></i>
                        <span>{{ __('Disqualify') }}</span>
                    </button>
                    @endcan
                @else
                    @can('hr.job-applications.reopen')
                    <button type="button" class="btn btn-outline-success d-flex align-items-center gap-1 rounded-3 px-3 py-2" id="btnReopen">
                        <i class="icon-base bx bx-undo font-16"></i>
                        <span>{{ __('Reopen Application') }}</span>
                    </button>
                    @endcan
                @endif

                @can('hr.job-applications.delete')
                <form action="{{ route('hr.job-applications.destroy', $application->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this candidate application?') }}');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-secondary d-flex align-items-center gap-1 rounded-3 px-3 py-2" title="{{ __('Delete') }}">
                        <i class="icon-base bx bx-trash font-16"></i>
                    </button>
                </form>
                @endcan
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

<!-- Pipeline Visual Stepper -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="fw-semibold text-heading small">{{ __('Candidate Pipeline Progression') }}</span>
            <span class="badge bg-label-info font-11">{{ __('Click any stage below to transition candidate') }}</span>
        </div>
        <div class="row g-2">
            @foreach ($stages as $stg)
                @php
                    $isCurrent = $application->stage === $stg;
                @endphp
                <div class="col">
                    <button type="button" class="btn w-100 py-2 d-flex flex-column align-items-center gap-1 rounded-3 btn-switch-stage {{ $isCurrent ? 'btn-primary shadow-sm' : 'btn-outline-secondary' }}" data-stage="{{ $stg->value }}">
                        <i class="{{ $stg->icon() }} font-18"></i>
                        <span class="font-12 fw-semibold">{{ $stg->label() }}</span>
                    </button>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Quick Metrics Row -->
<div class="row g-3 mb-4">
    <!-- Experience -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1">{{ __('Total Experience') }}</span>
                    <h5 class="fw-bold mb-0 text-primary">
                        {{ $application->candidate?->total_experience_years ? $application->candidate->total_experience_years . ' ' . __('Years') : __('Not Specified') }}
                    </h5>
                    <small class="text-muted">{{ $application->candidate?->current_company ?: '-' }}</small>
                </div>
                <div class="avatar avatar-md bg-label-primary rounded-3 p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-award font-22"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Expected Salary -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1">{{ __('Expected Monthly Salary') }}</span>
                    @if ($application->expected_salary)
                        <h5 class="fw-bold mb-0 text-primary">{{ number_format($application->expected_salary) }} {{ $application->currency ?? 'KWD' }}</h5>
                    @else
                        <h5 class="fw-bold mb-0 text-muted">{{ __('Negotiable') }}</h5>
                    @endif
                    <small class="text-muted">{{ __('Notice:') }} {{ $application->notice_period_days ? $application->notice_period_days . ' ' . __('Days') : __('Immediate') }}</small>
                </div>
                <div class="avatar avatar-md bg-label-success rounded-3 p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-dollar font-22"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Sourcing Source -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1">{{ __('Application Channel') }}</span>
                    <h5 class="fw-bold mb-0 text-info">{{ $application->source ?: 'Direct' }}</h5>
                    <small class="text-muted">{{ $application->created_at->format('Y-m-d H:i') }}</small>
                </div>
                <div class="avatar avatar-md bg-label-info rounded-3 p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-share-alt font-22"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Evaluation Rating -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1">{{ __('Candidate Rating') }}</span>
                    <div class="d-flex align-items-center gap-1" id="interactiveRatingStars">
                        @for ($i = 1; $i <= 5; $i++)
                            <i class="bx {{ $i <= ($application->rating ?? 0) ? 'bxs-star text-warning' : 'bx-star text-muted' }} font-22 cursor-pointer star-rate" data-score="{{ $i }}" title="{{ $i }} {{ __('Stars') }}"></i>
                        @endfor
                    </div>
                    <small class="text-muted d-block mt-1" id="ratingFeedback">{{ $application->rating ? $application->rating . ' / 5 ' . __('Score') : __('Click to rate') }}</small>
                </div>
                <div class="avatar avatar-md bg-label-warning rounded-3 p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-star font-22"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Details Section -->
<div class="row g-4">
    <!-- Left Column: Dossier, Cover Letter & Notes -->
    <div class="col-12 col-lg-8">
        @if ($application->status === \Modules\HR\Enums\ApplicationStatus::REJECTED)
            <div class="alert alert-danger d-flex align-items-start gap-2 mb-4" role="alert">
                <i class="bx bx-user-x font-22 mt-1"></i>
                <div>
                    <h6 class="alert-heading fw-bold mb-1">{{ __('Candidate Disqualified') }}</h6>
                    <p class="mb-1"><strong>{{ __('Reason:') }}</strong> {{ $application->rejection_reason?->label() ?? '-' }}</p>
                    @if ($application->rejection_notes)
                        <p class="mb-0 small text-muted">{{ $application->rejection_notes }}</p>
                    @endif
                    <small class="text-muted d-block mt-1">{{ __('Disqualified on:') }} {{ $application->rejected_at?->format('Y-m-d H:i') }}</small>
                </div>
            </div>
        @endif

        <!-- Candidate Contact & Profile Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-bottom py-3">
                <h5 class="card-title mb-0 d-flex align-items-center gap-2 text-primary">
                    <i class="icon-base bx bx-user font-20"></i>
                    <span>{{ __('Contact & Personal Information') }}</span>
                </h5>
            </div>
            <div class="card-body pt-3">
                <div class="row g-3">
                    <div class="col-12 col-sm-6">
                        <span class="text-muted small d-block">{{ __('Email Address') }}</span>
                        <a href="mailto:{{ $application->candidate?->email }}" class="fw-semibold text-heading">
                            <i class="bx bx-envelope font-14 me-1"></i>{{ $application->candidate?->email }}
                        </a>
                    </div>
                    <div class="col-12 col-sm-6">
                        <span class="text-muted small d-block">{{ __('Phone Number') }}</span>
                        <a href="tel:{{ $application->candidate?->phone }}" class="fw-semibold text-heading">
                            <i class="bx bx-phone font-14 me-1"></i>{{ $application->candidate?->phone ?: '-' }}
                        </a>
                    </div>
                    <div class="col-12 col-sm-6">
                        <span class="text-muted small d-block">{{ __('Nationality') }}</span>
                        <span class="fw-semibold text-heading">{{ $application->candidate?->nationality ?: '-' }}</span>
                    </div>
                    <div class="col-12 col-sm-6">
                        <span class="text-muted small d-block">{{ __('City / Residence') }}</span>
                        <span class="fw-semibold text-heading">{{ $application->candidate?->city ?: '-' }}</span>
                    </div>
                    @if ($application->candidate?->linkedin_url)
                        <div class="col-12 col-sm-6">
                            <span class="text-muted small d-block">{{ __('LinkedIn Profile') }}</span>
                            <a href="{{ $application->candidate->linkedin_url }}" target="_blank" class="fw-semibold text-primary">
                                <i class="bx bxl-linkedin-square font-16 me-1"></i>{{ __('View LinkedIn Profile') }}
                            </a>
                        </div>
                    @endif
                    @if ($application->candidate?->portfolio_url)
                        <div class="col-12 col-sm-6">
                            <span class="text-muted small d-block">{{ __('Portfolio / Website') }}</span>
                            <a href="{{ $application->candidate->portfolio_url }}" target="_blank" class="fw-semibold text-primary">
                                <i class="bx bx-link-external font-14 me-1"></i>{{ __('View Portfolio') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Cover Letter Card -->
        @if ($application->cover_letter)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h5 class="card-title mb-0 d-flex align-items-center gap-2 text-primary">
                        <i class="icon-base bx bx-envelope-open font-20"></i>
                        <span>{{ __('Candidate Cover Letter') }}</span>
                    </h5>
                </div>
                <div class="card-body pt-3">
                    <p class="mb-0 text-body" style="white-space: pre-line;">{{ $application->cover_letter }}</p>
                </div>
            </div>
        @endif

        <!-- Internal Recruiter Notes -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-bottom py-3">
                <h5 class="card-title mb-0 d-flex align-items-center gap-2 text-primary">
                    <i class="icon-base bx bx-notepad font-20"></i>
                    <span>{{ __('Internal Notes & Evaluation') }}</span>
                </h5>
            </div>
            <div class="card-body pt-3">
                @if ($application->candidate?->notes)
                    <div class="text-body mb-0" style="white-space: pre-line;">{{ $application->candidate->notes }}</div>
                @else
                    <p class="text-muted fst-italic mb-0">{{ __('No internal recruiter notes added yet.') }}</p>
                @endif
            </div>
        </div>

        <!-- Candidate Interviews & Evaluations Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h5 class="card-title mb-0 d-flex align-items-center gap-2 text-primary">
                    <i class="icon-base bx bx-conversation font-20"></i>
                    <span>{{ __('Interviews & Evaluations') }}</span>
                    <span class="badge bg-label-primary rounded-pill font-11">{{ $application->interviews->count() }}</span>
                </h5>
                <a href="{{ route('hr.interviews.create', ['application_id' => $application->id]) }}" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1 rounded-3">
                    <i class="bx bx-calendar-plus font-16"></i>
                    <span>{{ __('Schedule Interview') }}</span>
                </a>
            </div>
            <div class="card-body pt-3">
                @if ($application->interviews->isEmpty())
                    <div class="text-center py-4">
                        <div class="avatar avatar-md bg-label-secondary mx-auto mb-2 rounded-circle p-2 d-flex align-items-center justify-content-center">
                            <i class="bx bx-calendar-x font-22"></i>
                        </div>
                        <h6 class="fw-semibold text-muted mb-1">{{ __('No Interviews Scheduled Yet') }}</h6>
                        <p class="text-muted small mb-3">{{ __('Schedule an interview with candidate to evaluate skills, culture fit, and track scorecards.') }}</p>
                        <a href="{{ route('hr.interviews.create', ['application_id' => $application->id]) }}" class="btn btn-sm btn-outline-primary rounded-3">
                            <i class="bx bx-calendar-plus me-1"></i>{{ __('Schedule First Interview') }}
                        </a>
                    </div>
                @else
                    <div class="d-flex flex-column gap-3">
                        @foreach ($application->interviews->sortByDesc('scheduled_at') as $interview)
                            <div class="p-3 border rounded-3 bg-lighter">
                                <div class="d-flex align-items-start justify-content-between flex-wrap gap-2 mb-2">
                                    <div>
                                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                            <a href="{{ route('hr.interviews.show', $interview->id) }}" class="fw-bold text-heading">
                                                {{ $interview->interview_code }}
                                            </a>
                                            <span class="badge {{ $interview->type->badgeClass() }} rounded-pill font-11">
                                                {{ $interview->type->label() }}
                                            </span>
                                            <span class="badge bg-label-secondary rounded-pill font-11">
                                                <i class="bx {{ $interview->mode->icon() }} me-1"></i>{{ $interview->mode->label() }}
                                            </span>
                                            <span class="badge {{ $interview->status->badgeClass() }} rounded-pill font-11">
                                                {{ $interview->status->label() }}
                                            </span>
                                        </div>
                                        <div class="text-muted small d-flex align-items-center gap-3 flex-wrap">
                                            <span>
                                                <i class="bx bx-calendar me-1"></i>{{ $interview->scheduled_at->format('Y-m-d H:i') }}
                                                ({{ $interview->duration_minutes }} {{ __('min') }})
                                            </span>
                                            @if ($interview->location)
                                                <span><i class="bx bx-map-pin me-1"></i>{{ $interview->location }}</span>
                                            @endif
                                            @if ($interview->meeting_link)
                                                <a href="{{ $interview->meeting_link }}" target="_blank" class="text-primary fw-medium">
                                                    <i class="bx bx-video me-1"></i>{{ __('Join Meeting') }}
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        @if ($interview->status === \Modules\HR\Enums\InterviewStatus::SCHEDULED)
                                            <a href="{{ route('hr.interviews.evaluate', $interview->id) }}" class="btn btn-sm btn-outline-warning rounded-3">
                                                <i class="bx bx-edit me-1"></i>{{ __('Evaluate') }}
                                            </a>
                                        @endif
                                        <a href="{{ route('hr.interviews.show', $interview->id) }}" class="btn btn-sm btn-outline-primary rounded-3">
                                            <i class="bx bx-show me-1"></i>{{ __('Details') }}
                                        </a>
                                    </div>
                                </div>

                                <!-- Interviewers & Evaluation Row -->
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pt-2 border-top">
                                    <div class="d-flex align-items-center gap-1 small">
                                        <span class="text-muted me-1">{{ __('Interviewers:') }}</span>
                                        @forelse ($interview->interviewers as $interviewer)
                                            <span class="badge bg-white border text-heading font-11">
                                                {{ $interviewer->translate(app()->getLocale())?->name ?? $interviewer->name ?? 'Employee #' . $interviewer->id }}
                                            </span>
                                        @empty
                                            <span class="text-muted font-11">{{ __('Not assigned') }}</span>
                                        @endforelse
                                    </div>

                                    @if ($interview->evaluations->isNotEmpty())
                                        @php
                                            $avgScore = round($interview->evaluations->avg('overall_score'), 1);
                                        @endphp
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-label-warning font-12 fw-bold">
                                                <i class="bx bxs-star text-warning me-1"></i>{{ $avgScore }} / 5
                                            </span>
                                            <span class="text-muted font-11">({{ $interview->evaluations->count() }} {{ __('Evaluations') }})</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Right Column: Sidebar Target Job & Audit -->
    <div class="col-12 col-lg-4">
        <!-- Target Job Opening Card -->
        @if ($application->jobOpening)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h5 class="card-title mb-0 d-flex align-items-center gap-2 text-primary">
                        <i class="icon-base bx bx-briefcase font-20"></i>
                        <span>{{ __('Target Job Opening') }}</span>
                    </h5>
                </div>
                <div class="card-body pt-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <a href="{{ route('hr.job-openings.show', $application->jobOpening->id) }}" class="fw-bold text-heading">
                            {{ $application->jobOpening->opening_code }}
                        </a>
                        <span class="badge {{ $application->jobOpening->status->badgeClass() }} rounded-pill px-2.5 py-1 font-11 fw-semibold">
                            {{ $application->jobOpening->status->label() }}
                        </span>
                    </div>

                    <h6 class="fw-semibold text-primary mb-1">
                        {{ $application->jobOpening->translate(app()->getLocale())?->title ?? $application->jobOpening->title }}
                    </h6>

                    <small class="text-muted d-block mb-3">
                        {{ $application->jobOpening->department?->translate(app()->getLocale())?->name ?? $application->jobOpening->department?->name }}
                        &bull; {{ $application->jobOpening->filled_positions }} / {{ $application->jobOpening->open_positions }} {{ __('Hired') }}
                    </small>

                    <a href="{{ route('hr.job-openings.show', $application->jobOpening->id) }}" class="btn btn-outline-primary btn-sm w-100">
                        <i class="bx bx-show me-1"></i> {{ __('View Full Job Opening') }}
                    </a>
                </div>
            </div>
        @endif

        <!-- Audit Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-bottom py-3">
                <h5 class="card-title mb-0 d-flex align-items-center gap-2 text-muted">
                    <i class="icon-base bx bx-history font-20"></i>
                    <span>{{ __('Audit & Tracking') }}</span>
                </h5>
            </div>
            <div class="card-body pt-3">
                <ul class="list-unstyled mb-0 d-flex flex-column gap-2 small">
                    <li class="d-flex justify-content-between">
                        <span class="text-muted">{{ __('Applied Date') }}:</span>
                        <span class="fw-semibold">{{ $application->created_at->format('Y-m-d H:i') }}</span>
                    </li>
                    <li class="d-flex justify-content-between">
                        <span class="text-muted">{{ __('Registered By') }}:</span>
                        <span class="fw-semibold">{{ $application->creator?->name ?? __('Online Portal') }}</span>
                    </li>
                    @if ($application->hired_at)
                        <li class="d-flex justify-content-between">
                            <span class="text-muted">{{ __('Hired Date') }}:</span>
                            <span class="fw-semibold text-success">{{ $application->hired_at->format('Y-m-d H:i') }}</span>
                        </li>
                    @endif
                    @if ($application->rejected_at)
                        <li class="d-flex justify-content-between">
                            <span class="text-muted">{{ __('Disqualified Date') }}:</span>
                            <span class="fw-semibold text-danger">{{ $application->rejected_at->format('Y-m-d H:i') }}</span>
                        </li>
                    @endif
                    <li class="d-flex justify-content-between">
                        <span class="text-muted">{{ __('Last Activity') }}:</span>
                        <span>{{ $application->updated_at->format('Y-m-d H:i') }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Rejection Modal -->
<div class="modal fade" id="rejectCandidateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title text-danger d-flex align-items-center gap-2">
                    <i class="bx bx-user-x font-20"></i>
                    <span>{{ __('Disqualify Candidate') }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="rejectForm">
                @csrf
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-heading small required">{{ __('Reason for Disqualification') }}</label>
                        <select name="rejection_reason" id="modal_rejection_reason" class="form-select" required>
                            <option value="">{{ __('-- Select Reason --') }}</option>
                            @foreach ($rejectionReasons as $reason)
                                <option value="{{ $reason->value }}">{{ $reason->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold text-heading small">{{ __('Evaluation Notes / Feedback') }}</label>
                        <textarea name="rejection_notes" id="modal_rejection_notes" rows="3" class="form-control" placeholder="{{ __('Provide internal feedback or rationale...') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-danger d-flex align-items-center gap-1" id="btnSubmitReject">
                        <i class="bx bx-check me-1"></i>
                        <span>{{ __('Confirm Rejection') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const $ = window.jQuery || window.$;
    const appId = {{ $application->id }};

    // 1. Interactive Pipeline Stepper Buttons
    $('.btn-switch-stage').on('click', function () {
        const targetStage = $(this).data('stage');
        const btn = $(this);

        fetch(`{{ url('hr/job-applications') }}/${appId}/stage`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ stage: targetStage })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(err => {
            alert('{{ __('Server communication error.') }}');
        });
    });

    // 2. Interactive 5-Star Rating
    $('.star-rate').on('click', function () {
        const rating = $(this).data('score');

        fetch(`{{ url('hr/job-applications') }}/${appId}/rating`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ rating: rating })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Update stars visual
                $('.star-rate').each(function () {
                    const s = $(this).data('score');
                    if (s <= data.rating) {
                        $(this).removeClass('bx-star text-muted').addClass('bxs-star text-warning');
                    } else {
                        $(this).removeClass('bxs-star text-warning').addClass('bx-star text-muted');
                    }
                });
                $('#ratingFeedback').text(data.rating + ' / 5 ' + '{{ __('Score') }}');
            }
        });
    });

    // 3. Rejection Submission
    $('#rejectForm').on('submit', function (e) {
        e.preventDefault();
        const reason = $('#modal_rejection_reason').val();
        const notes = $('#modal_rejection_notes').val();

        if (!reason) {
            alert('{{ __('Please select a reason.') }}');
            return;
        }

        $('#btnSubmitReject').prop('disabled', true);

        fetch(`{{ url('hr/job-applications') }}/${appId}/reject`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ rejection_reason: reason, rejection_notes: notes })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message);
                $('#btnSubmitReject').prop('disabled', false);
            }
        });
    });

    // 4. Reopen Application
    $('#btnReopen').on('click', function () {
        if (!confirm('{{ __('Reopen this candidate application and move to active pool?') }}')) return;

        fetch(`{{ url('hr/job-applications') }}/${appId}/reopen`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message);
            }
        });
    });
});
</script>
@endsection
