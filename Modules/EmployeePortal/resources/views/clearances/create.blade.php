@extends('employeeportal::layouts.master')

@section('title', __('Exit Clearance & End of Service Request'))

@section('content')
<div class="loan-page-container">
    <!-- Top Navigation Header -->
    <div class="loan-nav-header">
        <a href="{{ route('portal.requests') }}" class="loan-back-btn" title="{{ __('Back') }}">
            <i class="bx {{ app()->getLocale() === 'ar' ? 'bx-right-arrow-alt' : 'bx-left-arrow-alt' }}"></i>
        </a>
        <h1 class="loan-header-title">{{ __('Exit Clearance') }}</h1>
        <a href="{{ route('portal.clearances.index') }}" class="loan-back-btn" title="{{ __('Clearance History') }}">
            <i class="bx bx-history"></i>
        </a>
    </div>

    <!-- Active Clearance Warning Banner if exists -->
    @if($pendingClearance)
        <div class="alert alert-warning d-flex align-items-center gap-3 p-3 rounded-3 shadow-xs mb-3">
            <i class="bx bx-error-circle font-24 flex-shrink-0 text-warning"></i>
            <div>
                <h6 class="fw-bold mb-1">{{ __('You Have an Active Clearance Under Review') }}</h6>
                <p class="small text-muted mb-2">
                    {{ __('Your clearance request (:code) has been created on :date and is currently progressing with departments.', ['code' => $pendingClearance->clearance_code, 'date' => $pendingClearance->created_at->format('Y-m-d')]) }}
                </p>
                <a href="{{ route('portal.clearances.show', $pendingClearance->id) }}" class="btn btn-sm btn-primary py-1 px-3">
                    <i class="bx bx-show me-1"></i> {{ __('View Clearance Details & Checklists') }}
                </a>
            </div>
        </div>
    @endif

    <!-- 1. Employee Profile & Financial Overview Card -->
    <div class="loan-hero-card" style="background: linear-gradient(135deg, #00334B 0%, #004D70 100%);">
        <div class="d-flex align-items-center gap-3 mb-3">
            <div class="avatar avatar-md rounded-circle bg-white text-primary d-flex align-items-center justify-content-center font-20 fw-bold" style="width: 48px; height: 48px;">
                {{ mb_substr($employee?->name ?? 'E', 0, 1) }}
            </div>
            <div class="text-white">
                <h3 class="fw-bold mb-0 text-white font-16">{{ $employee?->name ?? '-' }}</h3>
                <span class="opacity-75 small">{{ $employee?->jobTitle?->name ?? '-' }} • {{ $employee?->department?->name ?? '-' }}</span>
            </div>
        </div>

        <div class="loan-stats-grid">
            <div class="loan-stat-col">
                <span class="loan-stat-label">{{ __('Join Date') }}</span>
                <span class="loan-stat-val text-white font-14">{{ $employee?->hire_date?->format('Y-m-d') ?? '-' }}</span>
                <span class="loan-stat-sub">{{ __('Official Start') }}</span>
            </div>

            <div class="loan-stat-col highlighted">
                <span class="loan-stat-label">{{ __('Active Loans Dues') }}</span>
                <span class="loan-stat-val gold">{{ number_format($outstandingLoans, 2) }}</span>
                <span class="loan-stat-sub">{{ __('Pending Settlement') }}</span>
            </div>

            <div class="loan-stat-col">
                <span class="loan-stat-label">{{ __('Leave Balance') }}</span>
                <span class="loan-stat-val text-white">{{ $leaveBalance }} {{ __('Days') }}</span>
                <span class="loan-stat-sub">{{ __('Available') }}</span>
            </div>
        </div>
    </div>

    @if(!$pendingClearance)
    <!-- 2. Clearance Submission Form -->
    <form action="{{ route('portal.clearances.store') }}" method="POST" id="clearanceForm">
        @csrf

        <!-- Reason Selection Card -->
        <div class="loan-section-card">
            <h3 class="loan-section-title">
                <i class="bx bx-help-circle text-primary"></i>
                <span>{{ __('Leaving Reason') }}</span>
            </h3>

            <div class="mb-3">
                <select name="leaving_reason" id="leavingReason" class="form-select loan-form-control" required>
                    @foreach($reasons as $val => $label)
                        <option value="{{ $val }}" {{ old('leaving_reason') === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Dates Card -->
        <div class="loan-section-card">
            <h3 class="loan-section-title">
                <i class="bx bx-calendar text-primary"></i>
                <span>{{ __('Departure Dates') }}</span>
            </h3>

            <div class="mb-3">
                <label class="form-label small fw-semibold text-muted">{{ __('Resignation / Notice Date') }}</label>
                <input type="date" name="resignation_date" class="form-control loan-form-control" value="{{ old('resignation_date', date('Y-m-d')) }}">
            </div>

            <div class="mb-3">
                <label class="form-label small fw-semibold text-danger">{{ __('Last Working Day') }} *</label>
                <input type="date" name="last_working_day" class="form-control loan-form-control border-danger-subtle" value="{{ old('last_working_day', date('Y-m-d', strtotime('+30 days'))) }}" required>
                <small class="text-muted d-block mt-1">{{ __('Specify your final date of active duty at the organization.') }}</small>
            </div>
        </div>

        <!-- Reasons & Background Details Card -->
        <div class="loan-section-card">
            <h3 class="loan-section-title">
                <i class="bx bx-detail text-primary"></i>
                <span>{{ __('Reason Details & Administrative Notes') }}</span>
            </h3>

            <div class="mb-2">
                <textarea name="reason_details" class="form-control loan-form-control loan-textarea" rows="4" placeholder="{{ __('Optional notes, reason for resignation, or handover plan summary...') }}">{{ old('reason_details') }}</textarea>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="mt-4 mb-5">
            <button type="submit" class="loan-submit-btn w-100" id="btnSubmitClearance">
                <span class="btn-text">{{ __('Submit Exit Clearance Request') }}</span>
                <i class="bx {{ app()->getLocale() === 'ar' ? 'bx-left-arrow-alt' : 'bx-right-arrow-alt' }}"></i>
            </button>
        </div>
    </form>
    @endif
</div>
@endsection

