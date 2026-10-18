@extends('employeeportal::layouts.master')

@section('title', __('Awards & Honorary Incentives Log'))

@section('content')
<div class="rewards-page-container">
    <!-- Top Navigation Header -->
    <div class="rewards-nav-header">
        <a href="{{ route('portal.profile') }}" class="rewards-back-btn" title="{{ __('Back') }}">
            <i class="bx {{ app()->getLocale() === 'ar' ? 'bx-chevron-right' : 'bx-chevron-left' }}"></i>
        </a>
        <h1 class="rewards-header-title">{{ __('Awards & Honorary Incentives Log') }}</h1>
        @include('employeeportal::components.notification-bell')
    </div>

    <!-- 1. Hero Overview Trophy Card -->
    <div class="rewards-hero-card">
        <div class="rewards-hero-top">
            <div class="d-flex align-items-center gap-2">
                <div class="rewards-trophy-icon-box">
                    <i class="bx bx-trophy"></i>
                </div>
                <div>
                    <span class="rewards-hero-label">{{ __('Excellence & Administrative Recognition Board') }}</span>
                    <span class="rewards-hero-subtitle">{{ __('Astro Leaders Nursery Excellence') }}</span>
                </div>
            </div>

            <span class="rewards-star-badge">
                <i class="bx bxs-star"></i>
                <span>{{ __('Honors & Appreciation') }}</span>
            </span>
        </div>

        <!-- Big KPI Metric -->
        <div class="rewards-hero-count-center">
            <span class="rewards-count-number">{{ $totalCount }}</span>
            <span class="rewards-count-label">{{ __('Total Awards & Recognitions') }}</span>
        </div>

        <!-- 3-Metrics Grid -->
        <div class="rewards-stats-grid">
            <div class="rewards-stat-col">
                <span class="stat-label">{{ __('Financial Bonuses') }}</span>
                <span class="stat-val font-monospace text-emerald">{{ number_format($totalFinancialAmount, 2) }}</span>
                <span class="stat-sub">{{ __('KWD Approved') }}</span>
            </div>

            <div class="rewards-stat-col highlighted">
                <span class="stat-label">{{ __('Certificates & Shields') }}</span>
                <span class="stat-val font-monospace text-gold">{{ $honoraryCount }}</span>
                <span class="stat-sub">{{ __('Honorary Recognitions') }}</span>
            </div>

            <div class="rewards-stat-col">
                <span class="stat-label">{{ __('Direct Grants') }}</span>
                <span class="stat-val font-monospace">{{ $financialCount }}</span>
                <span class="stat-sub">{{ __('Immediate Grants') }}</span>
            </div>
        </div>
    </div>

    <!-- 2. Filter Tabs (All, Financial, Honorary) -->
    <div class="rewards-filter-tabs">
        <a href="{{ route('portal.profile.rewards', ['filter' => 'all']) }}" 
           class="rewards-tab-btn {{ $currentFilter === 'all' ? 'active' : '' }}">
            <span>{{ __('All Awards') }}</span>
            <span class="tab-count">{{ $totalCount }}</span>
        </a>
        <a href="{{ route('portal.profile.rewards', ['filter' => 'financial']) }}" 
           class="rewards-tab-btn {{ $currentFilter === 'financial' ? 'active' : '' }}">
            <span>{{ __('Financial Bonuses') }}</span>
            <span class="tab-count">{{ $financialCount }}</span>
        </a>
        <a href="{{ route('portal.profile.rewards', ['filter' => 'honorary']) }}" 
           class="rewards-tab-btn {{ $currentFilter === 'honorary' ? 'active' : '' }}">
            <span>{{ __('Certificates & Shields') }}</span>
            <span class="tab-count">{{ $honoraryCount }}</span>
        </a>
    </div>

    <!-- 3. Rewards Cards List -->
    <div class="rewards-list-wrapper">
        @forelse($rewards as $reward)
            <div class="reward-item-card {{ $reward->is_financial ? 'financial-card' : 'honorary-card' }}">
                <!-- Header of Card -->
                <div class="reward-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <div class="reward-type-icon {{ $reward->is_financial ? 'icon-emerald-subtle' : 'icon-gold-subtle' }}">
                            <i class="bx {{ $reward->is_financial ? 'bx-coin-stack' : 'bx-medal' }}"></i>
                        </div>
                        <div>
                            <h3 class="reward-title">{{ $reward->type_title }}</h3>
                            <span class="reward-ref font-monospace">{{ $reward->reward_number }}</span>
                        </div>
                    </div>

                    <!-- Status Pill -->
                    @if($reward->status)
                        <span class="badge {{ $reward->status->badgeClass() }} px-2 py-1 rounded-pill font-11">
                            <i class="{{ $reward->status->icon() }} me-1"></i>
                            {{ $reward->status->label() }}
                        </span>
                    @else
                        <span class="badge bg-label-success px-2 py-1 rounded-pill font-11">
                            {{ __('Approved') }}
                        </span>
                    @endif
                </div>

                <!-- Reward Main Benefit / Value -->
                <div class="reward-body-box mt-3">
                    @if($reward->is_financial)
                        <div class="reward-amount-row">
                            <div>
                                <span class="reward-amount-label">{{ __('Reward Amount') }}</span>
                                <div class="d-flex align-items-baseline gap-1 mt-1">
                                    <span class="reward-amount-val text-emerald font-monospace">+{{ number_format($reward->amount, 2) }}</span>
                                    <small class="reward-amount-curr">{{ __('KWD') }}</small>
                                </div>
                            </div>
                            <div class="reward-disbursement-box">
                                <span class="disb-title">{{ __('Disbursement Method') }}</span>
                                <span class="disb-val">
                                    <i class="bx bx-check-double text-emerald"></i>
                                    {{ $reward->payment_method?->label() ?? __('With Monthly Payroll') }}
                                </span>
                            </div>
                        </div>
                    @else
                        <div class="honorary-badge-row">
                            <div class="honorary-ribbon">
                                <i class="bx bxs-award"></i>
                                <span>{{ __('Appreciation Shield & Certificate of Honor') }}</span>
                            </div>
                            <span class="honorary-distinction">{{ __('Honorary Distinction') }}</span>
                        </div>
                    @endif

                    <!-- Reason / Recognition Statement -->
                    @if(!empty($reward->reason))
                        <div class="reward-reason-quote mt-3">
                            <i class="bx bxs-quote-right quote-mark"></i>
                            <p class="quote-text">{{ $reward->reason }}</p>
                        </div>
                    @endif

                    <!-- Notes if any -->
                    @if(!empty($reward->notes))
                        <div class="reward-notes-text mt-2">
                            <i class="bx bx-info-circle text-primary"></i>
                            <span>{{ $reward->notes }}</span>
                        </div>
                    @endif
                </div>

                <!-- Footer Strip: Date & Certificate Action -->
                <div class="reward-card-footer mt-3">
                    <div class="d-flex align-items-center gap-1 text-muted font-12">
                        <i class="bx bx-calendar"></i>
                        <span>{{ __('Award Date:') }}</span>
                        <strong class="text-dark">{{ $reward->formatted_date }}</strong>
                    </div>

                    @if(!empty($reward->attachment))
                        <a href="{{ asset('storage/' . $reward->attachment) }}" target="_blank" class="reward-cert-btn" title="{{ __('View Appreciation Certificate') }}">
                            <i class="bx bx-download"></i>
                            <span>{{ __('View Appreciation Certificate') }}</span>
                        </a>
                    @else
                        <span class="reward-cert-badge">
                            <i class="bx bx-check-circle text-success"></i>
                            <span>{{ __('Official Approved Record') }}</span>
                        </span>
                    @endif
                </div>
            </div>
        @empty
            <div class="rewards-empty-state">
                <div class="empty-icon-circle">
                    <i class="bx bx-trophy"></i>
                </div>
                <h4 class="empty-title">{{ __('No rewards recorded in this category yet') }}</h4>
                <p class="empty-subtitle">
                    {{ __('Keep up the outstanding dedication! Awards and certificates of appreciation are recorded and notified immediately upon official HR approval.') }}
                </p>
                <a href="{{ route('portal.profile.rewards', ['filter' => 'all']) }}" class="empty-reset-btn">
                    <i class="bx bx-reset"></i>
                    <span>{{ __('View All Records') }}</span>
                </a>
            </div>
        @endforelse
    </div>

    <!-- Motivational Footer Note -->
    <div class="rewards-quote-card mt-3">
        <div class="d-flex align-items-center gap-2 mb-1">
            <i class="bx bxs-heart text-danger fs-5"></i>
            <h4 class="quote-card-title">{{ __('Our Commitment to Teaching Staff Excellence') }}</h4>
        </div>
        <p class="quote-card-desc mb-0">
            {{ __('At Astro Leaders Nursery, we take pride in every exceptional achievement and effort made by our educational team in nurturing and caring for our children.') }}
        </p>
    </div>
</div>
@endsection

@push('styles')
<style>
/* ==============================================================================
 * Mobile Awards & Incentives Log Styling (PWA Optimized)
 * ============================================================================== */
.rewards-page-container {
    padding-bottom: 95px;
    max-width: 440px;
    margin: 0 auto;
}

.rewards-nav-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 14px;
    padding: 0 4px;
}

.rewards-back-btn {
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
    transition: all 0.2s ease;
}

.rewards-back-btn:active {
    transform: scale(0.95);
    background: #f8fafc;
}

.rewards-header-title {
    font-family: 'Tajawal', sans-serif;
    font-size: 16.5px;
    font-weight: 800;
    color: var(--portal-navy-deep);
    margin: 0;
}

.rewards-nav-badge {
    width: 38px;
    height: 38px;
    border-radius: 12px;
    background: rgba(217, 119, 6, 0.1);
    color: #d97706;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

/* 1. Hero Overview Trophy Card */
.rewards-hero-card {
    background: linear-gradient(135deg, #002A44 0%, #004B6B 100%);
    color: #ffffff;
    border-radius: 20px;
    padding: 18px 16px;
    margin-bottom: 16px;
    box-shadow: 0 8px 24px rgba(0, 42, 68, 0.2);
    position: relative;
    overflow: hidden;
}

.rewards-hero-card::after {
    content: '';
    position: absolute;
    top: -30px;
    inset-inline-end: -30px;
    width: 140px;
    height: 140px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(253, 187, 34, 0.25) 0%, transparent 70%);
    pointer-events: none;
}

.rewards-hero-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 14px;
}

.rewards-trophy-icon-box {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    background: rgba(253, 187, 34, 0.18);
    border: 1px solid rgba(253, 187, 34, 0.35);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: #FDBB22;
}

.rewards-hero-label {
    display: block;
    font-size: 14px;
    font-weight: 800;
    color: #ffffff;
    line-height: 1.25;
}

.rewards-hero-subtitle {
    display: block;
    font-size: 11px;
    color: #cbd5e1;
}

.rewards-star-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: rgba(253, 187, 34, 0.2);
    border: 1px solid rgba(253, 187, 34, 0.4);
    color: #fde047;
    padding: 3px 8px;
    border-radius: 14px;
    font-size: 11px;
    font-weight: 700;
}

.rewards-hero-count-center {
    text-align: center;
    margin-bottom: 16px;
}

.rewards-count-number {
    display: block;
    font-size: 38px;
    font-weight: 900;
    color: #FDBB22;
    line-height: 1;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
}

.rewards-count-label {
    display: block;
    font-size: 12px;
    color: #e2e8f0;
    font-weight: 600;
    margin-top: 3px;
}

.rewards-stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 14px;
    padding: 10px 8px;
    text-align: center;
}

.rewards-stat-col {
    padding: 0 4px;
}

.rewards-stat-col:not(:last-child) {
    border-inline-end: 1px solid rgba(255, 255, 255, 0.12);
}

.rewards-stat-col.highlighted {
    background: rgba(253, 187, 34, 0.08);
    border-radius: 8px;
}

.stat-label {
    display: block;
    font-size: 10.5px;
    color: #cbd5e1;
    margin-bottom: 2px;
}

.stat-val {
    display: block;
    font-size: 16px;
    font-weight: 800;
    color: #ffffff;
    line-height: 1.2;
}

.stat-sub {
    display: block;
    font-size: 9.5px;
    color: #94a3b8;
}

.text-emerald { color: #34d399 !important; }
.text-gold    { color: #FDBB22 !important; }

/* 2. Filter Tabs */
.rewards-filter-tabs {
    display: flex;
    gap: 8px;
    margin-bottom: 14px;
    background: #ffffff;
    padding: 4px;
    border-radius: 14px;
    border: 1px solid rgba(0, 51, 75, 0.06);
    box-shadow: 0 2px 8px rgba(0, 51, 75, 0.03);
}

.rewards-tab-btn {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 8px 4px;
    border-radius: 10px;
    font-size: 12px;
    font-weight: 700;
    color: #64748b;
    text-decoration: none;
    transition: all 0.2s ease;
}

.rewards-tab-btn.active {
    background: var(--portal-navy-deep);
    color: #ffffff;
    box-shadow: 0 2px 8px rgba(0, 51, 75, 0.18);
}

.tab-count {
    display: inline-block;
    padding: 1px 6px;
    border-radius: 10px;
    font-size: 10px;
    font-family: monospace;
    background: rgba(0, 51, 75, 0.08);
    color: var(--portal-navy-deep);
}

.rewards-tab-btn.active .tab-count {
    background: rgba(255, 255, 255, 0.2);
    color: #ffffff;
}

/* 3. Reward Item Card */
.rewards-list-wrapper {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.reward-item-card {
    background: #ffffff;
    border-radius: 18px;
    border: 1px solid rgba(0, 51, 75, 0.08);
    box-shadow: 0 4px 16px rgba(0, 51, 75, 0.04);
    padding: 16px;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.reward-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.reward-type-icon {
    width: 38px;
    height: 38px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}

.icon-emerald-subtle { background: rgba(16, 185, 129, 0.12); color: #059669; }
.icon-gold-subtle    { background: rgba(217, 119, 6, 0.12); color: #d97706; }

.reward-title {
    font-size: 13.5px;
    font-weight: 800;
    color: var(--portal-navy-deep);
    margin: 0;
    line-height: 1.25;
}

.reward-ref {
    font-size: 10.5px;
    color: #94a3b8;
}

.reward-body-box {
    background: #f8fafc;
    border-radius: 12px;
    padding: 12px;
    border: 1px solid rgba(0, 51, 75, 0.04);
}

.reward-amount-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.reward-amount-label {
    font-size: 11px;
    color: #64748b;
    font-weight: 600;
}

.reward-amount-val {
    font-size: 22px;
    font-weight: 800;
    line-height: 1;
}

.reward-amount-curr {
    font-size: 12px;
    font-weight: 700;
    color: #475569;
}

.reward-disbursement-box {
    text-align: end;
}

.disb-title {
    display: block;
    font-size: 10px;
    color: #94a3b8;
}

.disb-val {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 11.5px;
    font-weight: 700;
    color: #334155;
    margin-top: 2px;
}

.honorary-badge-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.honorary-ribbon {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%);
    color: #92400E;
    border: 1px solid #FCD34D;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 800;
}

.honorary-distinction {
    font-size: 11px;
    color: #d97706;
    font-weight: 700;
}

.reward-reason-quote {
    position: relative;
    background: #ffffff;
    border-radius: 10px;
    padding: 10px 12px 10px 28px;
    border: 1px solid rgba(0, 51, 75, 0.06);
}

[dir="rtl"] .reward-reason-quote {
    padding: 10px 28px 10px 12px;
}

.quote-mark {
    position: absolute;
    top: 8px;
    inset-inline-start: 8px;
    font-size: 16px;
    color: #FDBB22;
}

.quote-text {
    font-size: 11.5px;
    color: #334155;
    line-height: 1.5;
    margin: 0;
    font-style: italic;
}

.reward-notes-text {
    font-size: 11px;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 4px;
}

.reward-card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 4px;
}

.reward-cert-btn {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: var(--portal-navy-deep);
    color: #ffffff;
    font-size: 11px;
    font-weight: 700;
    padding: 5px 10px;
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.2s ease;
}

.reward-cert-btn:active {
    transform: scale(0.96);
    color: #ffffff;
}

.reward-cert-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 11px;
    color: #64748b;
    font-weight: 600;
}

/* Empty State */
.rewards-empty-state {
    background: #ffffff;
    border-radius: 20px;
    border: 1px solid rgba(0, 51, 75, 0.08);
    padding: 36px 20px;
    text-align: center;
    box-shadow: 0 4px 16px rgba(0, 51, 75, 0.03);
}

.empty-icon-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: rgba(217, 119, 6, 0.1);
    color: #d97706;
    font-size: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 14px auto;
}

.empty-title {
    font-size: 14.5px;
    font-weight: 800;
    color: var(--portal-navy-deep);
    margin-bottom: 6px;
}

.empty-subtitle {
    font-size: 12px;
    color: #64748b;
    line-height: 1.5;
    max-width: 320px;
    margin: 0 auto 16px auto;
}

.empty-reset-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: var(--portal-navy-deep);
    color: #ffffff;
    padding: 8px 16px;
    border-radius: 10px;
    font-size: 12px;
    font-weight: 700;
    text-decoration: none;
}

.empty-reset-btn:active {
    color: #ffffff;
    transform: scale(0.96);
}

/* Motivational Card */
.rewards-quote-card {
    background: #ffffff;
    border-radius: 16px;
    border: 1px dashed rgba(0, 51, 75, 0.15);
    padding: 14px 16px;
}

.quote-card-title {
    font-size: 12.5px;
    font-weight: 800;
    color: var(--portal-navy-deep);
    margin: 0;
}

.quote-card-desc {
    font-size: 11.5px;
    color: #64748b;
    line-height: 1.5;
}

.font-11 { font-size: 11px; }
.font-12 { font-size: 12px; }
</style>
@endpush
