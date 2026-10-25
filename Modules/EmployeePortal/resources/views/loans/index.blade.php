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
