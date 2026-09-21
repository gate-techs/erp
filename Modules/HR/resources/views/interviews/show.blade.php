@extends('layouts/contentNavbarLayout')

@section('title', ($interview->title ?? __('Interview Session')) . ' - ' . config('variables.templateName'))

@section('content')
<!-- Page Header / Top Action Bar -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <a href="{{ route('hr.interviews.index') }}" class="btn btn-icon btn-sm btn-outline-secondary rounded-circle">
                <i class="bx bx-arrow-back font-18"></i>
            </a>
            <span>{{ $interview->title ?? __('Interview Session') }}</span>
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hr.interviews.index') }}" class="text-muted">{{ __('Interviews') }}</a></li>
                <li class="breadcrumb-item active text-primary">#{{ $interview->id }}</li>
            </ol>
        </nav>
    </div>

    <div class="d-flex flex-wrap align-items-center gap-2">
        <a href="{{ route('hr.interviews.evaluate', $interview->id) }}" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm px-3 py-2 rounded-3">
            <i class="bx bx-check-square font-18"></i>
            <span class="fw-semibold">{{ __('Submit Evaluation / Scorecard') }}</span>
        </a>

        @if ($interview->job_application_id)
            <a href="{{ route('hr.job-offers.create', ['application_id' => $interview->job_application_id]) }}" class="btn btn-outline-success d-flex align-items-center gap-1 shadow-sm px-3 py-2 rounded-3">
                <i class="bx bx-gift font-18"></i>
                <span class="fw-semibold">{{ __('Issue Job Offer') }}</span>
            </a>
        @endif

        @if ($interview->mode === \Modules\HR\Enums\InterviewMode::ONLINE && $interview->meeting_link)
            <a href="{{ $interview->meeting_link }}" target="_blank" class="btn btn-outline-success d-flex align-items-center gap-1 shadow-sm px-3 py-2 rounded-3">
                <i class="bx bx-video font-18"></i>
                <span class="fw-semibold">{{ __('Join Video Meeting') }}</span>
            </a>
        @endif
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm rounded-3" role="alert">
        <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row g-4">
    <!-- Left Column: Interview & Candidate Dossier -->
    <div class="col-12 col-lg-4">
        <!-- Candidate Card -->
        @php
            $c = $interview->candidate;
            $app = $interview->jobApplication;
            $op = $interview->jobOpening;
            $opTitle = $op?->translate(app()->getLocale())?->title ?? $op?->title;
        @endphp
        <div class="card border-0 shadow-sm rounded-3 mb-4" style="border: 1px solid #edf0f6 !important;">
            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                <h6 class="card-title mb-0 fw-bold font-15">{{ __('Candidate Profile') }}</h6>
                <a href="{{ route('hr.job-applications.show', $app?->id) }}" class="small text-primary fw-semibold">
                    {{ __('View Application') }} &rarr;
                </a>
            </div>
            <div class="card-body p-4 text-center">
                <div class="avatar avatar-xl bg-label-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center fw-bold fs-3">
                    {{ $c?->initials ?? 'CA' }}
                </div>
                <h5 class="fw-bold mb-1 text-heading">{{ $c?->full_name }}</h5>
                <p class="text-muted small mb-2">{{ $c?->current_job_title ?? __('Applicant') }}</p>

                @if ($op)
                    <div class="badge bg-label-info rounded-pill px-3 py-1.5 font-11 fw-semibold mb-3">
                        <i class="bx bx-target-lock me-1"></i> {{ $opTitle }}
                    </div>
                @endif

                <div class="text-start border-top pt-3 small">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted">{{ __('Email:') }}</span>
                        <span class="fw-semibold text-heading">{{ $c?->email ?? '-' }}</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted">{{ __('Phone:') }}</span>
                        <span class="fw-semibold text-heading">{{ $c?->phone ?? '-' }}</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted">{{ __('Experience:') }}</span>
                        <span class="fw-semibold text-heading">{{ $c?->total_experience_years ? $c->total_experience_years . ' ' . __('yrs') : '-' }}</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="text-muted">{{ __('Expected Salary:') }}</span>
                        <span class="fw-bold text-success">{{ $app?->expected_salary ? number_format($app->expected_salary) . ' ' . ($app->currency ?? 'KWD') : '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Session Details Box -->
        <div class="card border-0 shadow-sm rounded-3 mb-4" style="border: 1px solid #edf0f6 !important;">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="card-title mb-0 fw-bold font-15">{{ __('Session Overview') }}</h6>
            </div>
            <div class="card-body p-4 small">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted">{{ __('Round:') }}</span>
                    <span class="badge bg-label-dark rounded-pill px-2.5 py-1 fw-bold">Round #{{ $interview->interview_round }}</span>
                </div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted">{{ __('Type:') }}</span>
                    <span class="badge {{ $interview->type->badgeClass() }} rounded-pill px-2.5 py-1 fw-semibold">{{ $interview->type->label() }}</span>
                </div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted">{{ __('Mode:') }}</span>
                    <span class="badge {{ $interview->mode->badgeClass() }} rounded-pill px-2.5 py-1 fw-semibold"><i class="{{ $interview->mode->icon() }} me-1"></i>{{ $interview->mode->label() }}</span>
                </div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted">{{ __('Status:') }}</span>
                    <span class="badge {{ $interview->status->badgeClass() }} rounded-pill px-2.5 py-1 fw-semibold">{{ $interview->status->label() }}</span>
                </div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted">{{ __('Scheduled At:') }}</span>
                    <span class="fw-semibold text-heading">{{ $interview->scheduled_at->format('Y-m-d h:i A') }}</span>
                </div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted">{{ __('Duration:') }}</span>
                    <span class="fw-semibold text-heading">{{ $interview->duration_minutes }} {{ __('Minutes') }}</span>
                </div>

                @if ($interview->location)
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <span class="text-muted">{{ __('Location:') }}</span>
                        <span class="fw-semibold text-heading text-end">{{ $interview->location }}</span>
                    </div>
                @endif

                @if ($interview->notes)
                    <div class="border-top pt-3 mt-3">
                        <span class="text-muted d-block mb-1 fw-semibold">{{ __('Instructions / Notes:') }}</span>
                        <p class="text-muted mb-0 font-12 bg-light p-2 rounded">{{ $interview->notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Committee Box -->
        <div class="card border-0 shadow-sm rounded-3" style="border: 1px solid #edf0f6 !important;">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="card-title mb-0 fw-bold font-15">{{ __('Interview Committee') }}</h6>
            </div>
            <div class="card-body p-3">
                <ul class="list-group list-group-flush">
                    @forelse ($interview->interviewers as $interviewer)
                        @php
                            $name = $interviewer->translate(app()->getLocale())?->name ?? $interviewer->name;
                            $hasEvaluated = $interview->evaluations->where('interviewer_id', $interviewer->id)->isNotEmpty();
                        @endphp
                        <li class="list-group-item d-flex align-items-center justify-content-between px-0 py-2.5">
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar avatar-xs bg-label-secondary rounded-circle d-flex align-items-center justify-content-center font-10 fw-bold">
                                    {{ substr($name, 0, 2) }}
                                </div>
                                <span class="fw-semibold text-heading font-13">{{ $name }}</span>
                            </div>
                            @if ($hasEvaluated)
                                <span class="badge bg-label-success rounded-pill font-10 px-2 py-0.5"><i class="bx bx-check me-1"></i>{{ __('Evaluated') }}</span>
                            @else
                                <span class="badge bg-label-warning rounded-pill font-10 px-2 py-0.5">{{ __('Pending') }}</span>
                            @endif
                        </li>
                    @empty
                        <li class="list-group-item px-0 py-2 text-muted small fst-italic">{{ __('No committee assigned') }}</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <!-- Right Column: Scorecards & Evaluation Results -->
    <div class="col-12 col-lg-8">
        <!-- Score Banner Card -->
        <div class="card border-0 shadow-sm rounded-3 mb-4 bg-primary text-white p-4">
            <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
                <div>
                    <span class="text-white opacity-75 small text-uppercase fw-semibold">{{ __('Overall Interview Score') }}</span>
                    <h2 class="fw-bold text-white mb-0 mt-1">
                        @if ($interview->overall_score)
                            {{ number_format($interview->overall_score, 1) }} <span class="fs-6 opacity-75">/ 5.0</span>
                        @else
                            {{ __('Pending Evaluation') }}
                        @endif
                    </h2>
                </div>

                <div class="text-sm-end">
                    @if ($interview->overall_score)
                        <div class="text-warning fs-5 mb-1">
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="bx {{ $i <= (int)round($interview->overall_score) ? 'bxs-star' : 'bx-star text-white opacity-50' }}"></i>
                            @endfor
                        </div>
                        <span class="badge bg-white text-primary rounded-pill px-3 py-1 font-12 fw-bold">
                            {{ count($interview->evaluations) }} {{ __('Evaluations Submitted') }}
                        </span>
                    @else
                        <a href="{{ route('hr.interviews.evaluate', $interview->id) }}" class="btn btn-light text-primary fw-bold px-3 py-2 rounded-3 shadow-sm">
                            <i class="bx bx-plus me-1"></i> {{ __('Add First Evaluation') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Submitted Scorecards -->
        <div class="card border-0 shadow-sm rounded-3 mb-4" style="border: 1px solid #edf0f6 !important;">
            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 font-16 fw-bold d-flex align-items-center gap-2">
                    <i class="bx bx-list-check text-primary font-20"></i>
                    <span>{{ __('Individual Scorecards & Feedback') }}</span>
                </h5>
                <a href="{{ route('hr.interviews.evaluate', $interview->id) }}" class="btn btn-sm btn-primary rounded-3">
                    <i class="bx bx-plus me-1"></i> {{ __('New Evaluation') }}
                </a>
            </div>
            <div class="card-body p-4">
                @forelse ($interview->evaluations as $eval)
                    @php
                        $interviewerName = $eval->interviewer?->translate(app()->getLocale())?->name ?? $eval->interviewer?->name;
                    @endphp
                    <div class="border rounded-3 p-3 mb-3 bg-white shadow-xs">
                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 border-bottom pb-2 mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar avatar-sm bg-label-primary rounded-circle d-flex align-items-center justify-content-center fw-bold">
                                    {{ substr($interviewerName, 0, 2) }}
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0 text-heading">{{ $interviewerName }}</h6>
                                    <small class="text-muted">{{ $eval->submitted_at?->format('Y-m-d h:i A') }}</small>
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-2">
                                <span class="badge {{ $eval->recommendation->badgeClass() }} rounded-pill px-3 py-1 font-11 fw-semibold">
                                    <i class="{{ $eval->recommendation->icon() }} me-1"></i>{{ $eval->recommendation->label() }}
                                </span>
                                <span class="fw-bold text-primary font-14 bg-label-primary px-2.5 py-1 rounded-pill">
                                    {{ number_format($eval->average_score, 1) }} / 5.0
                                </span>
                            </div>
                        </div>

                        <!-- 5 Rubric Criteria -->
                        <div class="row g-2 mb-3 text-center small">
                            <div class="col-4 col-md">
                                <div class="bg-light p-2 rounded">
                                    <span class="text-muted d-block font-11 mb-1">{{ __('Technical') }}</span>
                                    <span class="fw-bold text-heading">{{ $eval->technical_score ?? '-' }} / 5</span>
                                </div>
                            </div>
                            <div class="col-4 col-md">
                                <div class="bg-light p-2 rounded">
                                    <span class="text-muted d-block font-11 mb-1">{{ __('Communication') }}</span>
                                    <span class="fw-bold text-heading">{{ $eval->communication_score ?? '-' }} / 5</span>
                                </div>
                            </div>
                            <div class="col-4 col-md">
                                <div class="bg-light p-2 rounded">
                                    <span class="text-muted d-block font-11 mb-1">{{ __('Problem Solving') }}</span>
                                    <span class="fw-bold text-heading">{{ $eval->problem_solving_score ?? '-' }} / 5</span>
                                </div>
                            </div>
                            <div class="col-6 col-md">
                                <div class="bg-light p-2 rounded">
                                    <span class="text-muted d-block font-11 mb-1">{{ __('Culture Fit') }}</span>
                                    <span class="fw-bold text-heading">{{ $eval->cultural_fit_score ?? '-' }} / 5</span>
                                </div>
                            </div>
                            <div class="col-6 col-md">
                                <div class="bg-light p-2 rounded">
                                    <span class="text-muted d-block font-11 mb-1">{{ __('Experience') }}</span>
                                    <span class="fw-bold text-heading">{{ $eval->experience_score ?? '-' }} / 5</span>
                                </div>
                            </div>
                        </div>

                        <!-- Feedback Notes -->
                        @if ($eval->strengths)
                            <div class="mb-2 small">
                                <strong class="text-success"><i class="bx bx-check-circle me-1"></i>{{ __('Key Strengths:') }}</strong>
                                <span class="text-muted">{{ $eval->strengths }}</span>
                            </div>
                        @endif

                        @if ($eval->weaknesses)
                            <div class="mb-2 small">
                                <strong class="text-danger"><i class="bx bx-x-circle me-1"></i>{{ __('Areas of Concern / Weaknesses:') }}</strong>
                                <span class="text-muted">{{ $eval->weaknesses }}</span>
                            </div>
                        @endif

                        @if ($eval->notes)
                            <div class="small">
                                <strong class="text-secondary"><i class="bx bx-comment-detail me-1"></i>{{ __('General Notes:') }}</strong>
                                <span class="text-muted">{{ $eval->notes }}</span>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-5 text-muted">
                        <i class="bx bx-file-blank font-32 mb-2 d-block opacity-50"></i>
                        <p class="mb-2">{{ __('No evaluations submitted yet for this session.') }}</p>
                        <a href="{{ route('hr.interviews.evaluate', $interview->id) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                            <i class="bx bx-plus me-1"></i> {{ __('Fill Scorecard Now') }}
                        </a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
