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

