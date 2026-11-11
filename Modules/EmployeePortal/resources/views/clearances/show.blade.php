@extends('employeeportal::layouts.master')

@section('title', __('Clearance Details') . ' - ' . $clearance->clearance_code)

@section('content')
<div class="loans-history-page-container">
    <!-- Top Navigation Header -->
    <div class="loans-nav-header">
        <a href="{{ route('portal.requests') }}" class="loans-back-btn" title="{{ __('Back to Requests') }}">
            <i class="bx {{ app()->getLocale() === 'ar' ? 'bx-right-arrow-alt' : 'bx-left-arrow-alt' }}"></i>
        </a>
        <h1 class="loans-header-title">{{ $clearance->clearance_code }}</h1>
        <a href="{{ route('portal.clearances.create') }}" class="loans-back-btn" title="{{ __('New Request') }}">
            <i class="bx bx-plus"></i>
        </a>
    </div>

    <!-- Status Overview Banner Card -->
    <div class="loan-hero-card mb-3" style="background: linear-gradient(135deg, #00334B 0%, #004D70 100%);">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <span class="text-white-50 small d-block mb-1">{{ __('Clearance Status') }}</span>
                <span class="badge {{ $clearance->status?->badgeClass() ?? 'bg-label-primary' }} font-13 py-1 px-3">
                    <i class="{{ $clearance->status?->icon() ?? 'bx bx-time' }} me-1"></i>
                    {{ $clearance->status?->label() ?? $clearance->status }}
                </span>
            </div>
            <div class="text-end">
                <span class="text-white-50 small d-block mb-1">{{ __('Last Working Day') }}</span>
                <strong class="text-white font-15">{{ $clearance->last_working_day?->format('Y-m-d') }}</strong>
            </div>
        </div>

        <div class="loan-stats-grid">
            <div class="loan-stat-col">
                <span class="loan-stat-label">{{ __('Leaving Reason') }}</span>
                @php
                    $reasonsMap = [
                        'resignation'     => __('Resignation'),
                        'contract_expiry' => __('Contract Expiry'),
                        'termination'     => __('Employer Termination'),
                        'retirement'      => __('Retirement'),
                        'other'           => __('Other'),
                    ];
                @endphp
                <span class="loan-stat-val text-white font-13">{{ $reasonsMap[$clearance->leaving_reason] ?? __($clearance->leaving_reason) }}</span>
                <span class="loan-stat-sub">{{ __('Official Cause') }}</span>
            </div>

            <div class="loan-stat-col highlighted">
                <span class="loan-stat-label">{{ __('Review Status') }}</span>
                <span class="loan-stat-val gold">{{ __('Under Review') }}</span>
                <span class="loan-stat-sub">{{ __('HR Department') }}</span>
            </div>

            <div class="loan-stat-col">
                <span class="loan-stat-label">{{ __('Notice Date') }}</span>
                <span class="loan-stat-val text-white font-13">{{ $clearance->resignation_date?->format('Y-m-d') ?? '-' }}</span>
                <span class="loan-stat-sub">{{ __('Submitted At') }}</span>
            </div>
        </div>
    </div>

    <!-- Reason Details Note if exists -->
    @if($clearance->reason_details)
        <div class="card border-0 shadow-xs rounded-3 mb-3 p-3">
            <small class="text-muted fw-semibold d-block mb-1">{{ __('Reason Details & Administrative Notes') }}:</small>
            <p class="small text-heading mb-0">{{ $clearance->reason_details }}</p>
        </div>
    @endif

    <!-- Status Notice Card: Under Review -->
    <div class="card border-0 shadow-xs rounded-3 mb-4">
        <div class="card-body p-4 text-center">
            <div class="mb-3">
                <span class="avatar avatar-xl rounded-circle bg-label-warning d-inline-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                    <i class="bx bx-time-five font-34 text-warning"></i>
                </span>
            </div>
            <h5 class="fw-bold text-heading mb-2">{{ __('الطلب في وضع المراجعة') }}</h5>
            <p class="text-muted small mb-3 mx-auto" style="max-width: 520px; line-height: 1.7;">
                {{ __('تم استلام طلب إخلاء الطرف ونهاية الخدمة بنجاح، والطلب حالياً في وضع المراجعة والتدقيق الإداري من قبل إدارة الموارد البشرية. سيتم إشعارك فور اعتماد الطلب أو اتخاذ أي إجراء بشأنه.') }}
            </p>

            <div class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill bg-label-secondary small">
                <i class="bx bx-info-circle text-primary"></i>
                <span>{{ __('رقم مرجع الطلب') }}: <strong class="text-heading">{{ $clearance->clearance_code }}</strong></span>
                <span class="mx-1">•</span>
                <span>{{ __('تاريخ التقديم') }}: <strong class="text-heading">{{ $clearance->created_at?->format('Y-m-d') }}</strong></span>
            </div>
        </div>
    </div>
</div>
@endsection

