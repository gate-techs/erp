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

@push('styles')
<style>
.loan-details-page-container {
    padding-bottom: 95px;
    max-width: 440px;
    margin: 0 auto;
}

.loan-nav-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 14px;
    padding: 0 4px;
}

.loan-back-btn {
    width: 38px;
    height: 38px;
    border-radius: 12px;
    background: #ffffff;
    border: 1px solid rgba(0, 51, 75, 0.08);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--portal-navy-deep);
    font-size: 20px;
    text-decoration: none;
    box-shadow: 0 2px 8px rgba(0, 51, 75, 0.04);
}

.loan-header-title {
    font-family: 'Tajawal', sans-serif;
    font-size: 17px;
    font-weight: 800;
    color: var(--portal-navy-deep);
    margin: 0;
}

/* 1. Hero Card */
.loan-details-hero-card {
    background: linear-gradient(135deg, #002A44 0%, #004B6B 100%);
    color: #ffffff;
    border-radius: 18px;
    padding: 18px 16px;
    margin-bottom: 14px;
    box-shadow: 0 8px 24px rgba(0, 42, 68, 0.16);
}

.hero-top-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
}

.loan-type-tag {
    font-size: 11px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.loan-type-tag.type-advance {
    background: rgba(254, 174, 44, 0.2);
    color: var(--portal-accent-gold);
    border: 1px solid rgba(254, 174, 44, 0.35);
}

.loan-type-tag.type-loan {
    background: rgba(56, 189, 248, 0.2);
    color: #38BDF8;
    border: 1px solid rgba(56, 189, 248, 0.35);
}

.loan-code-tag {
    font-size: 11px;
    color: #CBD5E1;
    font-family: monospace;
}

.hero-amount-center {
    text-align: center;
    margin-bottom: 16px;
}

.hero-amount-label {
    font-size: 11px;
    color: #CBD5E1;
    display: block;
    margin-bottom: 4px;
}

.hero-amount-value {
    font-size: 26px;
    font-weight: 900;
    font-family: 'Inter', monospace;
    color: #ffffff;
    display: flex;
    align-items: baseline;
    justify-content: center;
    gap: 6px;
}

.hero-amount-value small {
    font-size: 13px;
    font-weight: 700;
    color: var(--portal-accent-gold);
}

.hero-metrics-grid {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 8px;
    background: rgba(0, 0, 0, 0.2);
    border-radius: 12px;
    padding: 10px 6px;
    border: 1px solid rgba(255, 255, 255, 0.08);
}

.hero-metric-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}

.metric-label {
    font-size: 10px;
    color: #94A3B8;
    margin-bottom: 2px;
}

.metric-val {
    font-size: 14px;
    font-weight: 800;
    font-family: 'Inter', monospace;
}

.progress-sub {
    font-size: 10px;
    color: #CBD5E1;
}

.progress-pct {
    font-size: 11px;
    font-weight: 800;
    color: #4ADE80;
}

/* 2. Section Card */
.loan-section-card {
    background: #ffffff;
    border-radius: 16px;
    padding: 16px;
    margin-bottom: 14px;
    border: 1px solid rgba(0, 51, 75, 0.06);
    box-shadow: 0 4px 14px rgba(0, 51, 75, 0.04);
}

.loan-section-title {
    font-family: 'Tajawal', sans-serif;
    font-size: 14px;
    font-weight: 800;
    color: var(--portal-navy-deep);
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 12px;
}

.loan-section-title i {
    color: var(--portal-accent);
    font-size: 18px;
}

.schedule-stats-badge {
    background: #EFF6FF;
    color: #0284C7;
    font-size: 11px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 10px;
}

.loan-info-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.loan-info-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 12px;
    padding-bottom: 8px;
    border-bottom: 1px solid #F8FAFC;
}

.info-label {
    color: var(--portal-text-muted);
}

.info-val {
    font-weight: 700;
    color: var(--portal-navy-deep);
}

.loan-reason-box {
    background: #F8FAFC;
    border-radius: 12px;
    padding: 10px 12px;
    border: 1px solid #F1F5F9;
}

.reason-title {
    font-size: 11px;
    font-weight: 700;
    color: var(--portal-navy-deep);
    display: flex;
    align-items: center;
    gap: 4px;
    margin-bottom: 4px;
}

.reason-text {
    font-size: 12px;
    color: var(--portal-text-main);
    margin: 0;
    line-height: 1.4;
}

.loan-rejection-box {
    background: #FEF2F2;
    border: 1px solid #FECACA;
    border-radius: 12px;
    padding: 10px 12px;
}

.rejection-title {
    font-size: 11px;
    font-weight: 700;
    color: #991B1B;
    display: flex;
    align-items: center;
    gap: 4px;
    margin-bottom: 4px;
}

.rejection-text {
    font-size: 12px;
    color: #B91C1C;
    margin: 0;
}

.loan-attachment-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #F8FAFC;
    border: 1px solid #E2E8F0;
    border-radius: 12px;
    padding: 10px 12px;
}

.attachment-name {
    font-size: 11.5px;
    font-weight: 700;
    color: var(--portal-navy-deep);
}

.attachment-btn {
    background: #ffffff;
    border: 1px solid #CBD5E1;
    border-radius: 8px;
    padding: 4px 10px;
    font-size: 11px;
    font-weight: 700;
    color: var(--portal-navy-deep);
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 4px;
}

/* 3. Installments Timeline */
.installments-timeline {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.installment-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 12px;
    border-radius: 12px;
    border: 1px solid #F1F5F9;
    background: #F8FAFC;
    transition: all 0.15s ease;
}

.installment-item.paid {
    background: #F0FDF4;
    border-color: #BBF7D0;
}

.installment-item.postponed {
    background: #FFFBEB;
    border-color: #FDE68A;
}

.inst-left {
    display: flex;
    align-items: center;
    gap: 10px;
}

.inst-circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 800;
    background: #E2E8F0;
    color: var(--portal-navy-deep);
}

.installment-item.paid .inst-circle {
    background: #10B981;
    color: #ffffff;
    font-size: 16px;
}

.installment-item.postponed .inst-circle {
    background: #F59E0B;
    color: #ffffff;
    font-size: 16px;
}

.inst-text {
    display: flex;
    flex-direction: column;
}

.inst-num {
    font-size: 12px;
    font-weight: 700;
    color: var(--portal-navy-deep);
}

.inst-date {
    font-size: 10.5px;
    color: var(--portal-text-muted);
    display: flex;
    align-items: center;
    gap: 3px;
}

.inst-right {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
}

.inst-amount {
    font-size: 13.5px;
    font-weight: 800;
    color: var(--portal-navy-deep);
    font-family: 'Inter', monospace;
}

.inst-amount small {
    font-size: 9.5px;
}

.inst-status-badge {
    font-size: 9.5px;
    padding: 2px 6px;
    border-radius: 6px;
}
</style>
@endpush
