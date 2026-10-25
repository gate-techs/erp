@extends('employeeportal::layouts.master')

@section('title', __('Loan & Advance Details'))

@section('content')
<div class="loan-details-page-container">
    <!-- Top Navigation Header -->
    <div class="loan-nav-header">
        <a href="{{ route('portal.loans.index') }}" class="loan-back-btn" title="{{ __('Back') }}">
            <i class="bx {{ app()->getLocale() === 'ar' ? 'bx-right-arrow-alt' : 'bx-left-arrow-alt' }}"></i>
        </a>
        <h1 class="loan-header-title">{{ __('Advance Details') }}</h1>
        <div style="width: 38px;"></div>
    </div>

    @php
        $type = $loan->type;
        $status = $loan->status;
        $isSalaryAdvance = ($type === \Modules\HR\Enums\LoanType::SALARY_ADVANCE);
    @endphp

    <!-- 1. Loan Overview Hero Card -->
    <div class="loan-details-hero-card">
        <div class="hero-top-row">
            <div class="d-flex align-items-center gap-2">
                <span class="loan-type-tag {{ $isSalaryAdvance ? 'type-advance' : 'type-loan' }}">
                    <i class="bx {{ $isSalaryAdvance ? 'bx-credit-card' : 'bx-money-withdraw' }}"></i>
                    <span>{{ $type?->label() ?? __('Advance') }}</span>
                </span>
                <span class="loan-code-tag">#{{ $loan->id }}</span>
            </div>

            <span class="badge {{ $status?->badgeClass() ?? 'bg-label-secondary' }} px-3 py-1 rounded-pill" style="font-size: 11px;">
                {{ $status?->label() ?? '-' }}
            </span>
        </div>

        <!-- Total Amount Highlight -->
        <div class="hero-amount-center">
            <span class="hero-amount-label">{{ __('Total Advance Amount') }}</span>
            <div class="hero-amount-value">
                <span>{{ number_format($loan->amount, 2) }}</span>
                <small>{{ __('KWD') }}</small>
            </div>
        </div>

        <!-- 3-Metrics Grid -->
        <div class="hero-metrics-grid">
            <div class="hero-metric-item">
                <span class="metric-label">{{ __('Repaid') }}</span>
                <span class="metric-val text-success">{{ number_format($loan->total_paid, 2) }}</span>
            </div>
            <div class="hero-metric-item">
                <span class="metric-label">{{ __('Remaining') }}</span>
                <span class="metric-val text-danger">{{ number_format($loan->remaining_amount, 2) }}</span>
            </div>
            <div class="hero-metric-item">
                <span class="metric-label">{{ __('Monthly Cut') }}</span>
                <span class="metric-val text-warning">{{ number_format($loan->monthly_installment, 2) }}</span>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="hero-progress-section mt-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="progress-sub">{{ __('Repayment Progress') }}</span>
                <span class="progress-pct">{{ $progress }}%</span>
            </div>
            <div class="progress" style="height: 6px; border-radius: 4px; background: rgba(255,255,255,0.15);">
                <div class="progress-bar bg-success" style="width: {{ $progress }}%; border-radius: 4px;"></div>
            </div>
        </div>
    </div>

    <!-- 2. Request Details Card -->
    <div class="loan-section-card">
        <h3 class="loan-section-title">
            <span>{{ __('Request Information') }}</span>
            <i class="bx bx-info-circle"></i>
        </h3>

        <div class="loan-info-list">
            <div class="loan-info-row">
                <span class="info-label">{{ __('Submission Date') }}</span>
                <span class="info-val">{{ $loan->created_at ? $loan->created_at->format('Y-m-d') : '—' }}</span>
            </div>
            <div class="loan-info-row">
                <span class="info-label">{{ __('First Deduction Date') }}</span>
                <span class="info-val font-monospace">{{ $loan->start_date ? $loan->start_date->format('Y-m-d') : '—' }}</span>
            </div>
            <div class="loan-info-row">
                <span class="info-label">{{ __('Installments Count') }}</span>
                <span class="info-val">{{ $loan->installments_count }} {{ __('Months') }}</span>
            </div>
            @if($loan->disbursed_at)
            <div class="loan-info-row">
                <span class="info-label">{{ __('Disbursed At') }}</span>
                <span class="info-val text-success">{{ $loan->disbursed_at->format('Y-m-d') }}</span>
            </div>
            @endif
        </div>

        @if($loan->reason)
        <div class="loan-reason-box mt-3">
            <span class="reason-title"><i class="bx bx-message-detail"></i> {{ __('Request Justification:') }}</span>
            <p class="reason-text">{{ $loan->reason }}</p>
        </div>
        @endif

        @if($loan->rejection_reason)
        <div class="loan-rejection-box mt-3">
            <span class="rejection-title"><i class="bx bx-error-circle"></i> {{ __('Rejection Reason:') }}</span>
            <p class="rejection-text">{{ $loan->rejection_reason }}</p>
        </div>
        @endif

        @if($loan->attachment)
        <div class="loan-attachment-card mt-3">
            <div class="d-flex align-items-center gap-2">
                <i class="bx bxs-file-pdf text-danger fs-4"></i>
                <span class="attachment-name">{{ __('Supporting Document Attached') }}</span>
            </div>
            <a href="{{ asset('storage/' . $loan->attachment) }}" target="_blank" class="attachment-btn">
                <span>{{ __('View') }}</span>
                <i class="bx bx-link-external"></i>
            </a>
        </div>
        @endif
    </div>

    <!-- 3. Installments Breakdown Card -->
    <div class="loan-section-card">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h3 class="loan-section-title mb-0">
                <span>{{ __('Installments Schedule') }}</span>
                <i class="bx bx-calendar-star"></i>
            </h3>
            <span class="schedule-stats-badge">
                {{ $paid_installments_count }} / {{ $loan->installments->count() }} {{ __('Paid') }}
            </span>
        </div>

        <div class="installments-timeline">
            @forelse($loan->installments as $inst)
                @php
                    $isPaid = ($inst->status === \Modules\HR\Enums\InstallmentStatus::PAID);
                    $isPostponed = ($inst->status === \Modules\HR\Enums\InstallmentStatus::POSTPONED);
                @endphp
                <div class="installment-item {{ $isPaid ? 'paid' : ($isPostponed ? 'postponed' : 'pending') }}">
                    <div class="inst-left">
                        <div class="inst-circle">
                            @if($isPaid)
                                <i class="bx bx-check"></i>
                            @elseif($isPostponed)
                                <i class="bx bx-time-five"></i>
                            @else
                                <span>#{{ $inst->installment_number }}</span>
                            @endif
                        </div>
                        <div class="inst-text">
                            <span class="inst-num">{{ __('Installment') }} #{{ $inst->installment_number }}</span>
                            <span class="inst-date">
                                <i class="bx bx-calendar"></i>
                                {{ $inst->due_date ? $inst->due_date->format('Y-m-d') : '-' }}
                            </span>
                        </div>
                    </div>

                    <div class="inst-right">
                        <span class="inst-amount">{{ number_format($inst->amount, 2) }} <small>{{ __('KWD') }}</small></span>
                        <span class="badge {{ $inst->status?->badgeClass() ?? 'bg-label-secondary' }} inst-status-badge">
                            {{ $inst->status?->label() ?? '-' }}
                        </span>
                    </div>
                </div>
            @empty
                <p class="text-muted small text-center py-3">{{ __('Installment schedule will be generated upon approval.') }}</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
