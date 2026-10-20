@extends('layouts/contentNavbarLayout')

@section('title', $requisition->requisition_number . ' - ' . config('variables.templateName'))

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <a href="{{ route('hr.job-requisitions.index') }}" class="btn btn-icon btn-sm btn-outline-secondary rounded-circle" title="{{ __('Back') }}">
                <i class="icon-base bx bx-arrow-back font-16"></i>
            </a>
            <h4 class="fw-bold mb-0 d-flex align-items-center gap-2 flex-wrap">
                <span>{{ $requisition->jobTitle?->translate(app()->getLocale())?->name ?? $requisition->jobTitle?->translate('ar')?->name ?? $requisition->jobTitle?->name }}</span>
                <span class="badge {{ $requisition->status->badgeClass() }} d-inline-flex align-items-center gap-1 font-12">
                    <i class="icon-base {{ $requisition->status->icon() }} font-14"></i>
                    <span>{{ $requisition->status->label() }}</span>
                </span>
                <span class="badge {{ $requisition->priority->badgeClass() }} font-12">{{ $requisition->priority->label() }}</span>
                @if ($requisition->is_budgeted)
                    <span class="badge bg-label-success font-12"><i class="bx bx-check font-12"></i> {{ __('Budgeted') }}</span>
                @else
                    <span class="badge bg-label-warning font-12"><i class="bx bx-error font-12"></i> {{ __('Unbudgeted') }}</span>
                @endif
            </h4>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ route('hr.job-requisitions.index') }}" class="text-muted">{{ __('Job Requisitions') }}</a></li>
                <li class="breadcrumb-item active text-primary">{{ $requisition->requisition_number }}</li>
            </ol>
        </nav>
    </div>

    <!-- Actions Toolbar -->
    <div class="d-flex flex-wrap align-items-center gap-2">
        @if ($requisition->status === \Modules\HR\Enums\JobRequisitionStatus::DRAFT)
            @can('hr.job-requisitions.edit')
            <a href="{{ route('hr.job-requisitions.edit', $requisition->id) }}" class="btn btn-outline-primary d-flex align-items-center gap-1">
                <i class="icon-base bx bx-edit font-16"></i>
                <span>{{ __('Edit') }}</span>
            </a>
            @endcan
            @can('hr.job-requisitions.submit')
            <form action="{{ route('hr.job-requisitions.submit', $requisition->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Submit this job requisition for approval?') }}');">
                @csrf
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm">
                    <i class="icon-base bx bx-send font-16"></i>
                    <span>{{ __('Submit for Approval') }}</span>
                </button>
            </form>
            @endcan
        @elseif ($requisition->status === \Modules\HR\Enums\JobRequisitionStatus::PENDING_APPROVAL)
            @can('hr.job-requisitions.approve')
            <form action="{{ route('hr.job-requisitions.approve', $requisition->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Approve this job requisition?') }}');">
                @csrf
                <button type="submit" class="btn btn-success d-flex align-items-center gap-1 shadow-sm">
                    <i class="icon-base bx bx-check-double font-16"></i>
                    <span>{{ __('Approve Request') }}</span>
                </button>
            </form>
            @endcan
            @can('hr.job-requisitions.reject')
            <button type="button" class="btn btn-outline-danger d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#rejectModal">
                <i class="icon-base bx bx-x-circle font-16"></i>
                <span>{{ __('Reject') }}</span>
            </button>
            @endcan
        @elseif ($requisition->status === \Modules\HR\Enums\JobRequisitionStatus::APPROVED)
            @can('hr.job-openings.create')
            <a href="{{ route('hr.job-openings.create', ['requisition_id' => $requisition->id]) }}" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm">
                <i class="icon-base bx bx-broadcast font-16"></i>
                <span>{{ __('Create Job Opening') }}</span>
            </a>
            @endcan
            @can('hr.job-requisitions.start-recruitment')
            <form action="{{ route('hr.job-requisitions.start-recruitment', $requisition->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Start active candidate recruitment for this role?') }}');">
                @csrf
                <button type="submit" class="btn btn-outline-primary d-flex align-items-center gap-1">
                    <i class="icon-base bx bx-user-voice font-16"></i>
                    <span>{{ __('Start Recruitment') }}</span>
                </button>
            </form>
            @endcan
        @elseif ($requisition->status === \Modules\HR\Enums\JobRequisitionStatus::IN_RECRUITMENT)
            @can('hr.job-openings.create')
            <a href="{{ route('hr.job-openings.create', ['requisition_id' => $requisition->id]) }}" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm">
                <i class="icon-base bx bx-broadcast font-16"></i>
                <span>{{ __('Create Job Opening') }}</span>
            </a>
            @endcan
        @endif

        @if (!in_array($requisition->status, [\Modules\HR\Enums\JobRequisitionStatus::CANCELLED, \Modules\HR\Enums\JobRequisitionStatus::FULFILLED, \Modules\HR\Enums\JobRequisitionStatus::REJECTED]))
            @can('hr.job-requisitions.cancel')
            <form action="{{ route('hr.job-requisitions.cancel', $requisition->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Cancel this requisition?') }}');">
                @csrf
                <button type="submit" class="btn btn-outline-secondary d-flex align-items-center gap-1">
                    <i class="icon-base bx bx-stop-circle font-16"></i>
                    <span>{{ __('Cancel Request') }}</span>
                </button>
            </form>
            @endcan
        @endif
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if ($requisition->status === \Modules\HR\Enums\JobRequisitionStatus::REJECTED && $requisition->rejection_reason)
    <div class="alert alert-danger mb-4" role="alert">
        <h6 class="alert-heading fw-bold mb-1 d-flex align-items-center gap-1">
            <i class="icon-base bx bx-x-circle font-18"></i>
            <span>{{ __('Rejection Reason') }}</span>
        </h6>
        <p class="mb-0 small">{{ $requisition->rejection_reason }}</p>
    </div>
@endif

<!-- Headcount Progress & Overview Row -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card kpi-card shadow-sm border-0 h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1">{{ __('Headcount Needed') }}</span>
                    <h3 class="fw-bold mb-0 text-primary">{{ $requisition->headcount_needed }}</h3>
                </div>
                <div class="avatar avatar-md bg-label-primary rounded p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-group font-22"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card kpi-card shadow-sm border-0 h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1">{{ __('Hired So Far') }}</span>
                    <h3 class="fw-bold mb-0 text-success">{{ $requisition->hired_count }}</h3>
                </div>
                <div class="avatar avatar-md bg-label-success rounded p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-user-check font-22"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card kpi-card shadow-sm border-0 h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1">{{ __('Remaining Open') }}</span>
                    <h3 class="fw-bold mb-0 text-warning">{{ max(0, $requisition->headcount_needed - $requisition->hired_count) }}</h3>
                </div>
                <div class="avatar avatar-md bg-label-warning rounded p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-hourglass font-22"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card kpi-card shadow-sm border-0 h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1">{{ __('Salary Range (KWD)') }}</span>
                    <h4 class="fw-bold mb-0 text-dark">
                        @if ($requisition->min_salary || $requisition->max_salary)
                            {{ number_format($requisition->min_salary ?? 0, 0) }} - {{ number_format($requisition->max_salary ?? 0, 0) }}
                        @else
                            —
                        @endif
                    </h4>
                </div>
                <div class="avatar avatar-md bg-label-info rounded p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-money font-22"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Requisition Detailed Info Card -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-transparent border-bottom py-3">
        <h5 class="card-title mb-0 d-flex align-items-center gap-2 text-primary">
            <i class="icon-base bx bx-info-circle font-20"></i>
            <span>{{ __('Requisition Specifications & Metadata') }}</span>
        </h5>
    </div>
    <div class="card-body pt-4">
        <div class="row g-4">
            <div class="col-6 col-md-3">
                <span class="text-muted small d-block">{{ __('Requisition Number') }}</span>
                <span class="fw-bold text-heading">{{ $requisition->requisition_number }}</span>
            </div>
            <div class="col-6 col-md-3">
                <span class="text-muted small d-block">{{ __('Department') }}</span>
                <span class="fw-semibold text-heading">
                    {{ $requisition->department?->translate(app()->getLocale())?->name ?? $requisition->department?->name ?? '-' }}
                </span>
            </div>
            <div class="col-6 col-md-3">
                <span class="text-muted small d-block">{{ __('Branch') }}</span>
                <span class="fw-semibold text-heading">
                    {{ $requisition->branch?->translate(app()->getLocale())?->name ?? $requisition->branch?->name ?? __('All Branches / Headquarter') }}
                </span>
            </div>
            <div class="col-6 col-md-3">
                <span class="text-muted small d-block">{{ __('Employment Type') }}</span>
                <span class="badge bg-label-secondary font-12">{{ $requisition->employment_type?->label() }}</span>
            </div>

            <div class="col-6 col-md-3">
                <span class="text-muted small d-block">{{ __('Requisition Type') }}</span>
                <span class="badge {{ $requisition->requisition_type?->badgeClass() }} font-12">{{ $requisition->requisition_type?->label() }}</span>
                @if ($requisition->replacementEmployee)
                    <small class="d-block text-muted mt-1">
                        {{ __('Replacing:') }} <strong>{{ $requisition->replacementEmployee->name }}</strong>
                    </small>
                @endif
            </div>
            <div class="col-6 col-md-3">
                <span class="text-muted small d-block">{{ __('Linked Workforce Plan') }}</span>
                @if ($requisition->planItem)
                    <a href="{{ route('hr.workforce-plans.show', $requisition->planItem->workforce_plan_id) }}" class="fw-bold text-decoration-none">
                        <i class="bx bx-line-chart font-14"></i> {{ $requisition->planItem->plan?->plan_code }}
                    </a>
                @else
                    <span class="text-muted">{{ __('Outside Plan / Emergency') }}</span>
                @endif
            </div>
            <div class="col-6 col-md-3">
                <span class="text-muted small d-block">{{ __('Expected Start Date') }}</span>
                <span class="fw-semibold text-heading">
                    {{ $requisition->expected_start_date ? $requisition->expected_start_date->format('Y-m-d') : '—' }}
                </span>
            </div>
            <div class="col-6 col-md-3">
                <span class="text-muted small d-block">{{ __('Requested By') }}</span>
                <span class="fw-semibold text-heading">{{ $requisition->requester?->name ?? '-' }}</span>
                <small class="text-muted d-block">{{ $requisition->created_at->format('Y-m-d H:i') }}</small>
            </div>

            @if ($requisition->approved_at)
            <div class="col-12 border-top pt-3">
                <span class="text-muted small d-block">{{ __('Approval Details') }}</span>
                <span class="text-success small">
                    <i class="bx bx-check-circle font-14"></i> {{ __('Approved by') }} <strong>{{ $requisition->approver?->name ?? '-' }}</strong> {{ __('on') }} {{ $requisition->approved_at->format('Y-m-d H:i') }}
                </span>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Details & Requirements Cards -->
<div class="row g-4">
    @if ($requisition->justification)
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-transparent border-bottom py-3">
                <h6 class="card-title mb-0 fw-bold text-heading">
                    <i class="icon-base bx bx-comment-detail text-primary me-1"></i>
                    <span>{{ __('Business Need & Justification') }}</span>
                </h6>
            </div>
            <div class="card-body pt-3">
                <p class="mb-0 text-muted">{{ $requisition->justification }}</p>
            </div>
        </div>
    </div>
    @endif

    @if ($requisition->job_description)
    <div class="col-12 col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-transparent border-bottom py-3">
                <h6 class="card-title mb-0 fw-bold text-heading">
                    <i class="icon-base bx bx-task text-primary me-1"></i>
                    <span>{{ __('Key Responsibilities & Duties') }}</span>
                </h6>
            </div>
            <div class="card-body pt-3">
                <div class="text-muted" style="white-space: pre-line;">{{ $requisition->job_description }}</div>
            </div>
        </div>
    </div>
    @endif

    @if ($requisition->requirements)
    <div class="col-12 col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-transparent border-bottom py-3">
                <h6 class="card-title mb-0 fw-bold text-heading">
                    <i class="icon-base bx bx-certification text-primary me-1"></i>
                    <span>{{ __('Required Qualifications & Skills') }}</span>
                </h6>
            </div>
            <div class="card-body pt-3">
                <div class="text-muted" style="white-space: pre-line;">{{ $requisition->requirements }}</div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Linked Job Openings -->
@if ($requisition->jobOpenings && $requisition->jobOpenings->count() > 0)
<div class="card shadow-sm border-0 mt-4">
    <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
        <h6 class="card-title mb-0 fw-bold text-heading d-flex align-items-center gap-2">
            <i class="icon-base bx bx-broadcast text-primary"></i>
            <span>{{ __('Published Job Openings') }}</span>
            <span class="badge bg-label-primary font-11">{{ $requisition->jobOpenings->count() }}</span>
        </h6>
        <a href="{{ route('hr.job-openings.create', ['requisition_id' => $requisition->id]) }}" class="btn btn-sm btn-outline-primary">
            <i class="bx bx-plus me-1"></i> {{ __('Add Opening') }}
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>{{ __('Opening Code') }}</th>
                    <th>{{ __('Title') }}</th>
                    <th class="text-center">{{ __('Workplace') }}</th>
                    <th class="text-center">{{ __('Positions') }}</th>
                    <th class="text-center">{{ __('Status') }}</th>
                    <th class="text-center">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($requisition->jobOpenings as $op)
                    <tr>
                        <td>
                            <a href="{{ route('hr.job-openings.show', $op->id) }}" class="fw-semibold text-primary">
                                {{ $op->opening_code }}
                            </a>
                        </td>
                        <td>{{ $op->title }}</td>
                        <td class="text-center">
                            <span class="badge {{ $op->workplace_type->badgeClass() }} font-11">
                                {{ $op->workplace_type->label() }}
                            </span>
                        </td>
                        <td class="text-center">
                            <strong>{{ $op->filled_positions }} / {{ $op->open_positions }}</strong>
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $op->status->badgeClass() }} font-11">
                                {{ $op->status->label() }}
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('hr.job-openings.show', $op->id) }}" class="btn btn-sm btn-icon btn-outline-secondary">
                                <i class="bx bx-show"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<!-- Rejection Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('hr.job-requisitions.reject', $requisition->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title d-flex align-items-center gap-2 text-danger">
                        <i class="bx bx-x-circle font-20"></i>
                        <span>{{ __('Reject Job Requisition') }}</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small">{{ __('Please provide the rationale for rejecting this departmental hiring request.') }}</p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-heading small required">{{ __('Rejection Reason') }}</label>
                        <textarea name="rejection_reason" class="form-control" rows="3" placeholder="{{ __('e.g. Headcount freeze, lack of budget, or role merged...') }}" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('Confirm Rejection') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
