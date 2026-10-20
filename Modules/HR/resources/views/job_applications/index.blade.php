@extends('layouts/contentNavbarLayout')

@section('title', __('Candidate Pipeline') . ' - ' . config('variables.templateName'))

@section('page-style')
<style>
/* Kanban Board Container & Scroll */
.kanban-board-wrapper {
    display: flex;
    gap: 1.25rem;
    overflow-x: auto;
    padding-bottom: 2rem;
    min-height: calc(100vh - 340px);
    scroll-behavior: smooth;
}
.kanban-board-wrapper::-webkit-scrollbar {
    height: 8px;
}
.kanban-board-wrapper::-webkit-scrollbar-track {
    background: #f1f3f7;
    border-radius: 4px;
}
.kanban-board-wrapper::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}
.kanban-board-wrapper::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Kanban Column */
.kanban-col {
    flex: 0 0 330px;
    max-width: 330px;
    background: #f8fafc;
    border: 1px solid #e8ecf2;
    border-radius: 1rem;
    display: flex;
    flex-direction: column;
    height: calc(100vh - 320px);
    min-height: 520px;
    transition: all 0.2s ease;
    box-shadow: 0 2px 8px -2px rgba(15, 23, 42, 0.04);
}

.kanban-col-header {
    padding: 1rem 1.15rem;
    background: #ffffff;
    border-top-left-radius: 1rem;
    border-top-right-radius: 1rem;
    border-bottom: 1px solid #edf0f5;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.kanban-col-indicator-bar {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    border-top-left-radius: 1rem;
    border-top-right-radius: 1rem;
}

.kanban-stage-dot {
    width: 9px;
    height: 9px;
    border-radius: 50%;
    display: inline-block;
}

.kanban-stage-badge-count {
    background: #f1f5f9;
    color: #475569;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 0.2rem 0.55rem;
    border-radius: 9999px;
    border: 1px solid #e2e8f0;
}

.kanban-col-body {
    padding: 0.85rem;
    overflow-y: auto;
    flex-grow: 1;
    min-height: 250px;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    transition: background-color 0.2s ease, border-color 0.2s ease;
}
.kanban-col-body::-webkit-scrollbar {
    width: 5px;
}
.kanban-col-body::-webkit-scrollbar-thumb {
    background: #e2e8f0;
    border-radius: 3px;
}
.kanban-col-body.drag-over {
    background-color: rgba(105, 108, 255, 0.05) !important;
    border: 2px dashed #696cff !important;
    border-radius: 0 0 1rem 1rem;
}

/* Jira / Trello Style Candidate Card */
.kanban-card {
    background: #ffffff;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    padding: 11px 13px;
    cursor: grab;
    transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
    margin-bottom: 9px;
    position: relative;
    user-select: none;
}
.kanban-card:active {
    cursor: grabbing;
}
.kanban-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 14px rgba(15, 23, 42, 0.08);
    border-color: #cbd5e1;
}
.kanban-card.is-dragging {
    opacity: 0.45;
    transform: rotate(1.5deg) scale(0.98);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
}

.kanban-card-title {
    font-size: 0.92rem;
    font-weight: 600;
    color: #1e293b;
    text-decoration: none;
    line-height: 1.35;
}
.kanban-card-title:hover {
    color: #696cff;
}

.kanban-card-subtitle {
    font-size: 0.8rem;
    color: #475569;
    margin-top: 4px;
    line-height: 1.3;
}

.kanban-card-meta {
    font-size: 0.75rem;
    color: #64748b;
    margin-top: 6px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.kanban-card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 9px;
    padding-top: 8px;
    border-top: 1px solid #f1f5f9;
}

.kanban-card-avatar {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    font-size: 0.65rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #eef2ff;
    color: #4338ca;
    border: 1px solid #e0e7ff;
}

.kanban-card-code {
    font-size: 0.72rem;
    font-weight: 600;
    color: #64748b;
    font-family: var(--bs-, monospace);
    letter-spacing: 0.3px;
}

.kanban-empty {
    border: 2px dashed #e2e8f0;
    border-radius: 0.75rem;
    padding: 2.2rem 1rem;
    text-align: center;
    background: rgba(255, 255, 255, 0.6);
}

.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <i class="icon-base bx bx-user-check text-primary font-24"></i>
            <span>{{ __('Candidate Pipeline') }}</span>
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hr.job-openings.index') }}" class="text-muted">{{ __('Recruitment') }}</a></li>
                <li class="breadcrumb-item active text-primary">{{ __('Candidates & Applications') }}</li>
            </ol>
        </nav>
    </div>

    <div class="d-flex flex-wrap align-items-center gap-2">
        <!-- View Toggle Buttons -->
        <div class="btn-group shadow-sm bg-white p-1 rounded-3 border" role="group" aria-label="View Switcher">
            <button type="button" class="btn btn-sm btn-primary active d-flex align-items-center gap-1.5 rounded-2 px-3 py-1.5" id="btnViewKanban">
                <i class="bx bx-grid-alt font-18"></i>
                <span class="d-none d-sm-inline fw-semibold">{{ __('Kanban Board') }}</span>
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1.5 rounded-2 px-3 py-1.5 border-0" id="btnViewTable">
                <i class="bx bx-list-ul font-18"></i>
                <span class="d-none d-sm-inline fw-semibold">{{ __('List Table') }}</span>
            </button>
        </div>

        @can('hr.job-applications.create')
        <a href="{{ route('hr.job-applications.create', $selectedOpeningId ? ['job_opening_id' => $selectedOpeningId] : []) }}" class="btn btn-primary d-flex align-items-center gap-1.5 shadow-sm px-3 py-2 rounded-3">
            <i class="icon-base bx bx-user-plus font-18"></i>
            <span class="fw-semibold">{{ __('New Candidate') }}</span>
        </a>
        @endcan
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm rounded-3" role="alert">
        <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm rounded-3" role="alert">
        <i class="bx bx-error me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Quick Metrics KPI Row -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card border-0 shadow-sm h-100 rounded-3" style="border: 1px solid #edf0f6 !important;">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1 fw-medium">{{ __('Total Pool') }}</span>
                    <h4 class="fw-bold mb-0 text-primary">{{ $kpis['total'] }}</h4>
                </div>
                <div class="avatar avatar-md bg-label-primary rounded-3 p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-group font-20"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card border-0 shadow-sm h-100 rounded-3" style="border: 1px solid #edf0f6 !important;">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1 fw-medium">{{ __('Applied / New') }}</span>
                    <h4 class="fw-bold mb-0 text-primary">{{ $kpis['new'] }}</h4>
                </div>
                <div class="avatar avatar-md bg-label-primary rounded-3 p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-envelope font-20"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card border-0 shadow-sm h-100 rounded-3" style="border: 1px solid #edf0f6 !important;">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1 fw-medium">{{ __('Screening') }}</span>
                    <h4 class="fw-bold mb-0 text-info">{{ $kpis['screening'] }}</h4>
                </div>
                <div class="avatar avatar-md bg-label-info rounded-3 p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-filter-alt font-20"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card border-0 shadow-sm h-100 rounded-3" style="border: 1px solid #edf0f6 !important;">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1 fw-medium">{{ __('Interviews') }}</span>
                    <h4 class="fw-bold mb-0 text-warning">{{ $kpis['interview'] }}</h4>
                </div>
                <div class="avatar avatar-md bg-label-warning rounded-3 p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-calendar font-20"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card border-0 shadow-sm h-100 rounded-3" style="border: 1px solid #edf0f6 !important;">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1 fw-medium">{{ __('Hired') }}</span>
                    <h4 class="fw-bold mb-0 text-success">{{ $kpis['hired'] }}</h4>
                </div>
                <div class="avatar avatar-md bg-label-success rounded-3 p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-check-double font-20"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card border-0 shadow-sm h-100 rounded-3" style="border: 1px solid #edf0f6 !important;">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1 fw-medium">{{ __('Disqualified') }}</span>
                    <h4 class="fw-bold mb-0 text-danger">{{ $kpis['rejected'] }}</h4>
                </div>
                <div class="avatar avatar-md bg-label-danger rounded-3 p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-user-x font-20"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters Bar -->
<div class="card border-0 shadow-sm mb-4 rounded-3" style="border: 1px solid #edf0f6 !important;">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('hr.job-applications.index') }}" id="filterForm" class="row g-3 align-items-end">
            <div class="col-12 col-md-6 col-lg-5">
                <label class="form-label fw-semibold text-heading small">{{ __('Filter by Job Opening') }}</label>
                <select name="job_opening_id" id="filter_job_opening_id" class="select2 form-select" onchange="document.getElementById('filterForm').submit();">
                    <option value="">{{ __('-- All Job Openings (All Positions) --') }}</option>
                    @foreach ($jobOpenings as $op)
                        @php
                            $opTitle = $op->translate(app()->getLocale())?->title ?? $op->title;
                        @endphp
                        <option value="{{ $op->id }}" {{ (string)$selectedOpeningId === (string)$op->id ? 'selected' : '' }}>
                            {{ $op->opening_code }} &bull; {{ $opTitle }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-md-4 col-lg-4">
                <label class="form-label fw-semibold text-heading small">{{ __('Search Candidate') }}</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bx bx-search text-muted"></i></span>
                    <input type="text" name="search" id="filter_search" class="form-control border-start-0" placeholder="{{ __('Search name, email, phone, title...') }}" value="{{ $search }}">
                    @if ($search)
                        <a href="{{ route('hr.job-applications.index', $selectedOpeningId ? ['job_opening_id' => $selectedOpeningId] : []) }}" class="btn btn-outline-secondary">
                            <i class="bx bx-x"></i>
                        </a>
                    @endif
                </div>
            </div>

            <div class="col-12 col-md-2 col-lg-3 d-flex gap-2">
                <button type="submit" class="btn btn-outline-primary px-3 rounded-3">
                    <i class="bx bx-filter-alt me-1"></i> {{ __('Filter') }}
                </button>
                @if ($selectedOpeningId || $search)
                    <a href="{{ route('hr.job-applications.index') }}" class="btn btn-outline-secondary px-3 rounded-3" title="{{ __('Reset All') }}">
                        <i class="bx bx-reset me-1"></i> {{ __('Reset') }}
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- SECTION 1: KANBAN BOARD VIEW -->
<div id="kanbanViewContainer">
    <div class="kanban-board-wrapper">
        @php
            $avatarGradients = [
                'linear-gradient(135deg, #696cff 0%, #3f51b5 100%)',
                'linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%)',
                'linear-gradient(135deg, #10b981 0%, #059669 100%)',
                'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)',
                'linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%)',
                'linear-gradient(135deg, #ec4899 0%, #be185d 100%)',
            ];
        @endphp
        @foreach ($stages as $stg)
            @php
                $colData = $kanbanData[$stg->value] ?? ['items' => collect(), 'count' => 0];
                $cards = $colData['items'];
                $stageColor = $stg->colorHex();
            @endphp
            <div class="kanban-col" data-stage="{{ $stg->value }}">
                <!-- Column Header -->
                <div class="kanban-col-header">
                    <div class="kanban-col-indicator-bar" style="background: linear-gradient(90deg, {{ $stageColor }}, {{ $stageColor }}88);"></div>
                    
                    <div class="d-flex align-items-center gap-2">
                        <span class="kanban-stage-dot" style="background-color: {{ $stageColor }}; box-shadow: 0 0 0 3px {{ $stageColor }}25;"></span>
                        <span class="fw-bold text-heading font-14">{{ $stg->label() }}</span>
                    </div>

                    <span class="kanban-stage-badge-count kanban-stage-count">
                        {{ $cards->count() }}
                    </span>
                </div>

                <!-- Column Cards Container -->
                <div class="kanban-col-body" data-stage="{{ $stg->value }}">
                    @php
                        $palettes = ['bg-label-primary', 'bg-label-info', 'bg-label-success', 'bg-label-warning', 'bg-label-danger', 'bg-label-secondary'];
                    @endphp
                    @forelse ($cards as $item)
                        @php
                            $c = $item->candidate;
                            $op = $item->jobOpening;
                            $opTitle = $op?->translate(app()->getLocale())?->title ?? $op?->title;
                            $avatarPalette = $palettes[($item->id ?? 0) % count($palettes)];
                        @endphp
                        <div class="kanban-card" draggable="true" data-id="{{ $item->id }}">
                            <!-- Top Row: Candidate Name & Quick Actions / CV -->
                            <div class="d-flex align-items-start justify-content-between gap-2">
                                <a href="{{ route('hr.job-applications.show', $item->id) }}" class="kanban-card-title text-truncate" title="{{ $c?->full_name }}">
                                    {{ $c?->full_name }}
                                </a>

                                <div class="d-flex align-items-center gap-1 flex-shrink-0">
                                    @if ($c?->resume_path)
                                        <i class="bx bx-paperclip text-muted font-14" title="{{ __('CV Attached') }}"></i>
                                    @endif
                                    @if ($item->status?->value === 'rejected')
                                        <span class="badge bg-label-danger rounded-pill font-10 px-1.5 py-0.5">{{ __('Disqualified') }}</span>
                                    @endif
                                    @php
                                        $canViewApp = auth()->user()->can('hr.job-applications.view');
                                        $canRejectApp = auth()->user()->can('hr.job-applications.reject');
                                    @endphp
                                    @if ($canViewApp || ($canRejectApp && $item->status?->value !== 'rejected'))
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-xs btn-icon btn-text-secondary rounded-circle" data-bs-toggle="dropdown" aria-expanded="false" title="{{ __('Actions') }}">
                                            <i class="bx bx-dots-horizontal-rounded font-16"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                            @if ($canViewApp)
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('hr.job-applications.show', $item->id) }}">
                                                    <i class="bx bx-show font-14 text-primary"></i>
                                                    <span>{{ __('View Profile') }}</span>
                                                </a>
                                            </li>
                                            @endif
                                            @if ($canRejectApp && $item->status?->value !== 'rejected')
                                                <li>
                                                    <button type="button" class="dropdown-item d-flex align-items-center gap-2 text-danger btn-reject-candidate" data-id="{{ $item->id }}" data-name="{{ $c?->full_name }}">
                                                        <i class="bx bx-user-x font-14"></i>
                                                        <span>{{ __('Disqualify') }}</span>
                                                    </button>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Subtitle: Job Opening Applied For -->
                            @if ($op)
                                <div class="kanban-card-subtitle text-truncate">
                                    <i class="bx bx-briefcase font-12 me-1 text-muted"></i>{{ $opTitle }}
                                </div>
                            @endif

                            <!-- Simple Inline Text Meta (Experience & Rating) -->
                            <div class="kanban-card-meta">
                                @if ($c?->total_experience_years)
                                    <span><i class="bx bx-time-five font-12 me-0.5"></i>{{ $c->total_experience_years }} {{ __('yrs') }}</span>
                                @endif
                                @if ($item->rating)
                                    <span class="text-warning fw-semibold"><i class="bx bxs-star font-12 me-0.5"></i>{{ $item->rating }}/5</span>
                                @endif
                            </div>

                            <!-- Bottom Row: Avatar + Salary on Left, Code on Right -->
                            <div class="kanban-card-footer">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="kanban-card-avatar" title="{{ $c?->full_name }}">
                                        {{ $c?->initials ?? 'CA' }}
                                    </div>
                                    @if ($item->expected_salary)
                                        <span class="fw-semibold text-success font-12">
                                            {{ number_format($item->expected_salary) }} {{ $item->currency ?? 'KWD' }}
                                        </span>
                                    @endif
                                </div>

                                <span class="kanban-card-code">
                                    #{{ $item->application_code }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="kanban-empty">
                            <i class="bx bx-folder-open font-28 text-muted mb-1 d-block opacity-50"></i>
                            <span class="small text-muted d-block">{{ __('No candidates') }}</span>
                            <span class="font-10 text-muted d-block opacity-75">{{ __('Drag candidates here') }}</span>
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- SECTION 2: DATATABLE VIEW (Hidden by default) -->
<div id="tableViewContainer" class="d-none">
    <div class="card border-0 shadow-sm">
        <div class="card-datatable table-responsive">
            <table class="table border-top table-hover text-nowrap" id="jobApplicationsTable">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 40px;">#</th>
                        <th>{{ __('Candidate Information') }}</th>
                        <th>{{ __('Applied Job Opening') }}</th>
                        <th class="text-center">{{ __('Pipeline Stage') }}</th>
                        <th class="text-center">{{ __('Status') }}</th>
                        <th>{{ __('Salary / Rating') }}</th>
                        <th>{{ __('Applied At') }}</th>
                        <th class="text-center" style="width: 100px;">{{ __('Actions') }}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Disqualify / Reject Candidate -->
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
            <form id="rejectCandidateForm">
                @csrf
                <input type="hidden" name="application_id" id="reject_application_id">
                <div class="modal-body py-4">
                    <div class="alert alert-warning mb-3 small d-flex align-items-center gap-2">
                        <i class="bx bx-info-circle font-18"></i>
                        <span>{{ __('Are you sure you want to disqualify:') }} <strong id="reject_candidate_name">-</strong>?</span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-heading small required">{{ __('Reason for Disqualification') }}</label>
                        <select name="rejection_reason" id="rejection_reason" class="form-select" required>
                            <option value="">{{ __('-- Select Reason --') }}</option>
                            @foreach ($rejectionReasons as $reason)
                                <option value="{{ $reason->value }}">{{ $reason->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold text-heading small">{{ __('Evaluation Notes / Feedback') }}</label>
                        <textarea name="rejection_notes" id="rejection_notes" rows="3" class="form-control" placeholder="{{ __('Provide internal feedback or rationale...') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-danger d-flex align-items-center gap-1" id="btnConfirmReject">
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
    let dtTable = null;

    // 1. Initialize Select2
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

    // 2. DataTable Initialization Function
    function initDataTable() {
        if ($.fn.DataTable.isDataTable('#jobApplicationsTable')) {
            return;
        }
        dtTable = $('#jobApplicationsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('hr.job-applications.data') }}",
                data: function (d) {
                    d.job_opening_id = $('#filter_job_opening_id').val();
                    d.search = $('#filter_search').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center align-middle' },
                { data: 'candidate_info', name: 'candidate.full_name', className: 'align-middle' },
                { data: 'opening_info', name: 'jobOpening.title', className: 'align-middle' },
                { data: 'stage_badge', name: 'stage', className: 'text-center align-middle' },
                { data: 'status_badge', name: 'status', className: 'text-center align-middle' },
                { data: 'salary_rating', name: 'expected_salary', className: 'align-middle' },
                { data: 'applied_at', name: 'created_at', className: 'align-middle' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center align-middle' }
            ],
            order: [[6, 'desc']]
        });
    }

    // 3. View Toggle (Kanban vs Table)
    const btnKanban = document.getElementById('btnViewKanban');
    const btnTable  = document.getElementById('btnViewTable');
    const kanbanContainer = document.getElementById('kanbanViewContainer');
    const tableContainer  = document.getElementById('tableViewContainer');

    function switchToKanban() {
        if (btnKanban) {
            btnKanban.classList.add('active', 'btn-primary');
            btnKanban.classList.remove('btn-outline-secondary');
        }
        if (btnTable) {
            btnTable.classList.remove('active', 'btn-primary');
            btnTable.classList.add('btn-outline-secondary');
        }
        if (kanbanContainer) kanbanContainer.classList.remove('d-none');
        if (tableContainer) tableContainer.classList.add('d-none');
        localStorage.setItem('candidate_pipeline_view', 'kanban');
    }

    function switchToTable() {
        if (btnTable) {
            btnTable.classList.add('active', 'btn-primary');
            btnTable.classList.remove('btn-outline-secondary');
        }
        if (btnKanban) {
            btnKanban.classList.remove('active', 'btn-primary');
            btnKanban.classList.add('btn-outline-secondary');
        }
        if (tableContainer) tableContainer.classList.remove('d-none');
        if (kanbanContainer) kanbanContainer.classList.add('d-none');
        localStorage.setItem('candidate_pipeline_view', 'table');
        initDataTable();
    }

    if (btnKanban) btnKanban.addEventListener('click', switchToKanban);
    if (btnTable) btnTable.addEventListener('click', switchToTable);

    // Initial View from localStorage
    const activeView = localStorage.getItem('candidate_pipeline_view') || 'kanban';
    if (activeView === 'table') {
        switchToTable();
    } else {
        switchToKanban();
    }

    // 4. HTML5 Drag & Drop for Kanban Cards
    let draggedCard = null;

    document.querySelectorAll('.kanban-card').forEach(card => {
        card.addEventListener('dragstart', function (e) {
            draggedCard = this;
            this.classList.add('is-dragging');
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', this.dataset.id);
        });

        card.addEventListener('dragend', function () {
            this.classList.remove('is-dragging');
            draggedCard = null;
            document.querySelectorAll('.kanban-col-body').forEach(col => col.classList.remove('drag-over'));
        });
    });

    document.querySelectorAll('.kanban-col-body').forEach(colBody => {
        colBody.addEventListener('dragover', function (e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            this.classList.add('drag-over');
        });

        colBody.addEventListener('dragleave', function () {
            this.classList.remove('drag-over');
        });

        colBody.addEventListener('drop', function (e) {
            e.preventDefault();
            this.classList.remove('drag-over');

            const appId = e.dataTransfer.getData('text/plain') || (draggedCard ? draggedCard.dataset.id : null);
            const targetStage = this.dataset.stage;

            if (!appId || !targetStage) return;

            const currentStage = draggedCard ? draggedCard.closest('.kanban-col-body').dataset.stage : null;
            if (currentStage === targetStage) return;

            // Optimistic DOM update
            const emptyNotice = this.querySelector('.kanban-empty');
            if (emptyNotice) emptyNotice.remove();
            this.prepend(draggedCard);

            updateCounters();

            // Send AJAX
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
                    if (window.toastr) toastr.success(data.message);
                } else {
                    alert(data.message || '{{ __('Failed to update stage') }}');
                    location.reload();
                }
            })
            .catch(err => {
                console.error(err);
                alert('{{ __('Server communication error.') }}');
                location.reload();
            });
        });
    });

    function updateCounters() {
        document.querySelectorAll('.kanban-col').forEach(col => {
            const countBadge = col.querySelector('.kanban-stage-count');
            const cardsCount = col.querySelectorAll('.kanban-card').length;
            if (countBadge) countBadge.textContent = cardsCount;
        });
    }

    // 5. Rejection Modal Handlers
    const rejectModal = new bootstrap.Modal(document.getElementById('rejectCandidateModal'));

    $(document).on('click', '.btn-reject-candidate', function () {
        const appId = $(this).data('id');
        const appName = $(this).data('name') || '';

        $('#reject_application_id').val(appId);
        $('#reject_candidate_name').text(appName);
        $('#rejection_reason').val('');
        $('#rejection_notes').val('');

        rejectModal.show();
    });

    $('#rejectCandidateForm').on('submit', function (e) {
        e.preventDefault();
        const appId = $('#reject_application_id').val();
        const reason = $('#rejection_reason').val();
        const notes = $('#rejection_notes').val();

        if (!reason) {
            alert('{{ __('Please select a rejection reason.') }}');
            return;
        }

        $('#btnConfirmReject').prop('disabled', true);

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
            $('#btnConfirmReject').prop('disabled', false);
            if (data.success) {
                rejectModal.hide();
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(err => {
            $('#btnConfirmReject').prop('disabled', false);
            alert('{{ __('Server communication error.') }}');
        });
    });

    // 6. Reopen Application Handler
    $(document).on('click', '.btn-reopen-candidate', function () {
        const appId = $(this).data('id');
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
