@extends('employeeportal::layouts.master')

@section('title', __('Loans & Advances History'))

@section('content')
<div class="loans-history-page-container">
    <!-- Top Navigation Header -->
    <div class="loans-nav-header">
        <a href="{{ request()->get('from') === 'profile' ? route('portal.profile') : route('portal.requests') }}" class="loans-back-btn" title="{{ __('Back') }}">
            <i class="bx {{ app()->getLocale() === 'ar' ? 'bx-right-arrow-alt' : 'bx-left-arrow-alt' }}"></i>
        </a>
        <h1 class="loans-header-title">{{ __('Loans & Advances') }}</h1>
        <a href="{{ route('portal.loans.create') }}" class="loans-back-btn" title="{{ __('New Request') }}">
            <i class="bx bx-plus"></i>
        </a>
    </div>

    <!-- 1. Loans Summary Banner Card -->
    <div class="loans-summary-card">
        <div class="loans-summary-top">
            <h2 class="loans-summary-title">{{ __('Financial Debt & Repayment') }}</h2>
            <div class="loans-summary-date-pill">
                <span class="dot"></span>
                <span>{{ date('F Y') }}</span>
            </div>
        </div>

        <div class="loans-metrics-row">
            <!-- 1. Active Balance Remaining -->
            <div class="loans-metric-box highlighted">
                <span class="loans-metric-label">{{ __('Remaining Balance') }}</span>
                <span class="loans-metric-val gold">{{ number_format($totalRemainingDebt, 2) }}</span>
                <span class="loans-metric-sub">{{ __('Active Debt') }}</span>
            </div>

            <!-- 2. Next Installment -->
            <div class="loans-metric-box">
                <span class="loans-metric-label">{{ __('Next Installment') }}</span>
                <span class="loans-metric-val">{{ $nextInstallment ? number_format($nextInstallment->amount, 2) : '0.00' }}</span>
                <span class="loans-metric-sub">{{ $nextInstallment ? $nextInstallment->due_date?->format('M Y') : '—' }}</span>
            </div>

            <!-- 3. Total Repaid -->
            <div class="loans-metric-box">
                <span class="loans-metric-label">{{ __('Total Repaid') }}</span>
                <span class="loans-metric-val text-success">{{ number_format($totalRepaid, 2) }}</span>
                <span class="loans-metric-sub">{{ __('Recovered') }}</span>
            </div>
        </div>
    </div>

    <!-- 2. Status Filter Tabs (Horizontal Scroller) -->
    <div class="loans-tabs-scroller">
        <!-- All Requests -->
        <a href="{{ route('portal.loans.index', ['status' => 'all']) }}"
           class="loans-tab-item {{ $currentFilter === 'all' ? 'active' : '' }}">
            <span>{{ __('All') }}</span>
            <span class="tab-count">{{ $statusCounts['all'] }}</span>
        </a>

        <!-- Pending (Under Review) -->
        <a href="{{ route('portal.loans.index', ['status' => 'pending']) }}"
           class="loans-tab-item {{ $currentFilter === 'pending' ? 'active' : '' }}">
            <span>{{ __('Under Review') }}</span>
            <span class="tab-count">{{ $statusCounts['pending'] }}</span>
        </a>

        <!-- Active / Disbursed -->
        <a href="{{ route('portal.loans.index', ['status' => 'disbursed']) }}"
           class="loans-tab-item {{ $currentFilter === 'disbursed' ? 'active' : '' }}">
            <span>{{ __('Active / Disbursed') }}</span>
            <span class="tab-count">{{ $statusCounts['disbursed'] }}</span>
        </a>

        <!-- Approved -->
        <a href="{{ route('portal.loans.index', ['status' => 'approved']) }}"
           class="loans-tab-item {{ $currentFilter === 'approved' ? 'active' : '' }}">
            <span>{{ __('Approved') }}</span>
            <span class="tab-count">{{ $statusCounts['approved'] }}</span>
        </a>

        <!-- Completed / Paid -->
        <a href="{{ route('portal.loans.index', ['status' => 'paid']) }}"
           class="loans-tab-item {{ $currentFilter === 'paid' ? 'active' : '' }}">
            <span>{{ __('Fully Repaid') }}</span>
            <span class="tab-count">{{ $statusCounts['paid'] }}</span>
        </a>

        <!-- Rejected -->
        <a href="{{ route('portal.loans.index', ['status' => 'rejected']) }}"
           class="loans-tab-item {{ $currentFilter === 'rejected' ? 'active' : '' }}">
            <span>{{ __('Rejected') }}</span>
            <span class="tab-count">{{ $statusCounts['rejected'] }}</span>
        </a>
    </div>

    <!-- 3. List of Loans Cards -->
    <div class="loans-list">
        @forelse($loans as $loan)
            @php
                $type = $loan->type;
                $status = $loan->status;
                $progress = $loan->progress_percentage;
                $isSalaryAdvance = ($type === \Modules\HR\Enums\LoanType::SALARY_ADVANCE);
            @endphp
            <a href="{{ route('portal.loans.show', $loan->id) }}" class="loan-card-item">
                <div class="loan-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <div class="loan-badge-type {{ $isSalaryAdvance ? 'type-advance' : 'type-loan' }}">
                            <i class="bx {{ $isSalaryAdvance ? 'bx-credit-card' : 'bx-money-withdraw' }}"></i>
                            <span>{{ $type?->label() ?? __('Advance') }}</span>
                        </div>
                        <span class="loan-ref-code">#{{ $loan->id }}</span>
                    </div>

                    <span class="loan-status-badge badge {{ $status?->badgeClass() ?? 'bg-label-secondary' }}">
                        {{ $status?->label() ?? '-' }}
                    </span>
                </div>

                <div class="loan-card-body">
                    <div class="loan-card-row">
                        <div>
                            <span class="loan-detail-label">{{ __('Total Amount') }}</span>
                            <span class="loan-detail-val">{{ number_format($loan->amount, 2) }} <small>{{ __('KWD') }}</small></span>
                        </div>
                        <div class="text-center">
                            <span class="loan-detail-label">{{ __('Monthly Cut') }}</span>
                            <span class="loan-detail-val text-primary">{{ number_format($loan->monthly_installment, 2) }}</span>
                        </div>
                        <div class="text-end">
                            <span class="loan-detail-label">{{ __('Remaining') }}</span>
                            <span class="loan-detail-val text-danger">{{ number_format($loan->remaining_amount, 2) }}</span>
                        </div>
                    </div>

                    <!-- Repayment Progress -->
                    <div class="loan-card-progress-wrap mt-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="progress-sub-text">
                                {{ __('Repayment:') }} {{ number_format($loan->total_paid, 2) }} / {{ number_format($loan->amount, 2) }}
                            </span>
                            <span class="progress-percentage-val">{{ $progress }}%</span>
                        </div>
                        <div class="progress" style="height: 6px; border-radius: 4px; background: #F1F5F9;">
                            <div class="progress-bar bg-success" style="width: {{ $progress }}%; border-radius: 4px;"></div>
                        </div>
                    </div>
                </div>

                <div class="loan-card-footer">
                    <span class="loan-date-text">
                        <i class="bx bx-calendar"></i>
                        {{ __('First Deduction:') }} {{ $loan->start_date ? $loan->start_date->format('Y-m-d') : '—' }}
                    </span>
                    <span class="loan-view-link">
                        <span>{{ __('Details') }}</span>
                        <i class="bx {{ app()->getLocale() === 'ar' ? 'bx-left-arrow-alt' : 'bx-right-arrow-alt' }}"></i>
                    </span>
                </div>
            </a>
        @empty
            <div class="loan-empty-state">
                <div class="empty-icon-wrap">
                    <i class="bx bx-wallet"></i>
                </div>
                <h4 class="empty-title">{{ __('No records found') }}</h4>
                <p class="empty-desc">{{ __('You currently have no financial advance requests under this category.') }}</p>
                <a href="{{ route('portal.loans.create') }}" class="btn-new-empty">
                    <i class="bx bx-plus"></i>
                    <span>{{ __('Request New Advance') }}</span>
                </a>
            </div>
        @endforelse
    </div>
</div>
@endsection

@push('styles')
<style>
.loans-history-page-container {
    padding-bottom: 95px;
    max-width: 440px;
    margin: 0 auto;
}

.loans-nav-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 14px;
    padding: 0 4px;
}

.loans-back-btn {
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

.loans-header-title {
    font-family: 'Tajawal', sans-serif;
    font-size: 17px;
    font-weight: 800;
    color: var(--portal-navy-deep);
    margin: 0;
}

/* 1. Summary Card */
.loans-summary-card {
    background: linear-gradient(135deg, #002A44 0%, #004B6B 100%);
    color: #ffffff;
    border-radius: 18px;
    padding: 16px;
    margin-bottom: 14px;
    box-shadow: 0 8px 24px rgba(0, 42, 68, 0.16);
}

.loans-summary-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 12px;
}

.loans-summary-title {
    font-size: 14px;
    font-weight: 700;
    color: #ffffff;
    margin: 0;
}

.loans-summary-date-pill {
    background: rgba(255, 255, 255, 0.12);
    font-size: 11px;
    font-weight: 600;
    padding: 3px 8px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.loans-summary-date-pill .dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: var(--portal-accent-gold);
}

.loans-metrics-row {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 8px;
    background: rgba(0, 0, 0, 0.18);
    border-radius: 12px;
    padding: 10px 6px;
}

.loans-metric-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}

.loans-metric-label {
    font-size: 9.5px;
    color: #94A3B8;
    margin-bottom: 2px;
}

.loans-metric-val {
    font-size: 14px;
    font-weight: 800;
    color: #ffffff;
    font-family: 'Inter', monospace;
}

.loans-metric-val.gold {
    color: var(--portal-accent-gold);
}

.loans-metric-sub {
    font-size: 9.5px;
    color: #CBD5E1;
    margin-top: 2px;
}

/* 2. Tabs Scroller */
.loans-tabs-scroller {
    display: flex;
    gap: 8px;
    overflow-x: auto;
    padding-bottom: 8px;
    margin-bottom: 14px;
    scrollbar-width: none;
}

.loans-tabs-scroller::-webkit-scrollbar {
    display: none;
}

.loans-tab-item {
    background: #ffffff;
    border: 1px solid rgba(0, 51, 75, 0.08);
    border-radius: 20px;
    padding: 6px 14px;
    font-size: 12px;
    font-weight: 700;
    color: var(--portal-text-muted);
    text-decoration: none;
    white-space: nowrap;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s ease;
}

.loans-tab-item.active {
    background: var(--portal-navy-deep);
    border-color: var(--portal-navy-deep);
    color: #ffffff;
}

.loans-tab-item .tab-count {
    background: rgba(0, 51, 75, 0.08);
    color: var(--portal-navy-deep);
    font-size: 10px;
    padding: 1px 6px;
    border-radius: 10px;
}

.loans-tab-item.active .tab-count {
    background: rgba(255, 255, 255, 0.2);
    color: #ffffff;
}

/* 3. Cards List */
.loans-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.loan-card-item {
    background: #ffffff;
    border-radius: 16px;
    padding: 14px;
    border: 1px solid rgba(0, 51, 75, 0.06);
    box-shadow: 0 4px 14px rgba(0, 51, 75, 0.04);
    text-decoration: none;
    color: inherit;
    display: block;
    transition: all 0.2s ease;
}

.loan-card-item:active {
    transform: scale(0.98);
}

.loan-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 12px;
    padding-bottom: 8px;
    border-bottom: 1px solid #F1F5F9;
}

.loan-badge-type {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 11px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 8px;
}

.loan-badge-type.type-advance {
    background: #FEF3C7;
    color: #D97706;
}

.loan-badge-type.type-loan {
    background: #EFF6FF;
    color: #0284C7;
}

.loan-ref-code {
    font-size: 11px;
    color: var(--portal-text-muted);
    font-family: monospace;
}

.loan-status-badge {
    font-size: 10.5px;
    font-weight: 700;
}

.loan-card-row {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 8px;
}

.loan-detail-label {
    font-size: 10px;
    color: var(--portal-text-muted);
    display: block;
    margin-bottom: 2px;
}

.loan-detail-val {
    font-size: 13.5px;
    font-weight: 800;
    color: var(--portal-navy-deep);
    font-family: 'Inter', monospace;
    display: block;
}

.loan-detail-val small {
    font-size: 10px;
    font-weight: 600;
}

.progress-sub-text {
    font-size: 10px;
    color: var(--portal-text-muted);
}

.progress-percentage-val {
    font-size: 11px;
    font-weight: 800;
    color: #166534;
}

.loan-card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 12px;
    padding-top: 8px;
    border-top: 1px solid #F8FAFC;
    font-size: 11px;
}

.loan-date-text {
    color: var(--portal-text-muted);
    display: flex;
    align-items: center;
    gap: 4px;
}

.loan-view-link {
    color: var(--portal-accent-orange);
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 2px;
}

/* Empty State */
.loan-empty-state {
    text-align: center;
    padding: 40px 16px;
    background: #ffffff;
    border-radius: 16px;
    border: 1px dashed rgba(0, 51, 75, 0.15);
}

.empty-icon-wrap {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: #FEF3C7;
    color: #D97706;
    font-size: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 14px auto;
}

.empty-title {
    font-size: 15px;
    font-weight: 800;
    color: var(--portal-navy-deep);
    margin-bottom: 6px;
}

.empty-desc {
    font-size: 11.5px;
    color: var(--portal-text-muted);
    margin-bottom: 16px;
}

.btn-new-empty {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: var(--portal-accent);
    color: #ffffff;
    font-size: 12px;
    font-weight: 700;
    padding: 8px 18px;
    border-radius: 20px;
    text-decoration: none;
}
</style>
@endpush
