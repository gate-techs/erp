@extends('employeeportal::layouts.master')

@section('title', __('Requests'))

@section('content')
<div class="requests-page-container">
    <!-- Top Navigation Header -->
    <div class="requests-nav-header">
        <a href="{{ route('portal.dashboard', [], false) }}" class="requests-back-btn" title="{{ __('Back') }}">
            <i class="bx {{ app()->getLocale() === 'ar' ? 'bx-right-arrow-alt' : 'bx-left-arrow-alt' }}"></i>
        </a>
        <h1 class="requests-header-title">{{ __('Requests') }}</h1>
        <div class="requests-nav-placeholder"></div>
    </div>

    <!-- 1. Header Banner Card (تقديم طلب جديد) -->
    <div class="requests-banner-card">
        <div class="requests-banner-badge">
            <span class="badge-dot"></span>
            <span>{{ __('Teachers & Staff Portal') }}</span>
        </div>

        <h2 class="requests-banner-title">{{ __('Submit New Request') }}</h2>
        <p class="requests-banner-desc">
            {{ __('Choose transaction type for documentation and approval by Academic Management & HR.') }}
        </p>
    </div>

    <!-- 2. Grid Cards (2x2 matching exact image) -->
    <div class="requests-grid">
        <!-- 1. طلب إجازة (Leave Request) -->
        <a href="{{ route('portal.leaves.create') }}" class="request-item-card request-card-orange">
            <div>
                <div class="request-card-icon-box icon-box-orange">
                    <i class="bx bx-calendar-event"></i>
                </div>
                <h3 class="request-card-title">{{ __('Leave Request') }}</h3>
                <p class="request-card-desc">
                    {{ __('Annual, sick, or emergency leave for educators') }}
                </p>
            </div>
            <div class="request-card-footer">
                <span class="request-tag-text">{{ __('Direct to Admin') }}</span>
                <i class="bx {{ app()->getLocale() === 'ar' ? 'bx-left-arrow-alt' : 'bx-right-arrow-alt' }} request-arrow-icon"></i>
            </div>
        </a>

        <!-- 2. استئذان ومغادرة (Permission / Early Leave) -->
        <a href="{{ route('portal.permissions.create') }}" class="request-item-card request-card-navy">
            <div>
                <div class="request-card-icon-box icon-box-blue">
                    <i class="bx bx-time-five"></i>
                </div>
                <h3 class="request-card-title">{{ __('Permission Request') }}</h3>
                <p class="request-card-desc">
                    {{ __('Temporary exit or early departure permit during classes') }}
                </p>
            </div>
            <div class="request-card-footer">
                <span class="request-tag-text">{{ __('Instant Approval') }}</span>
                <i class="bx {{ app()->getLocale() === 'ar' ? 'bx-left-arrow-alt' : 'bx-right-arrow-alt' }} request-arrow-icon"></i>
            </div>
        </a>

        <!-- 3. تحديث مستند (Document Update) -->
        <a href="{{ route('portal.documents.index') }}" class="request-item-card request-card-navy">
            <div>
                <div class="request-card-icon-box icon-box-navy">
                    <i class="bx bx-file"></i>
                </div>
                <h3 class="request-card-title">{{ __('Document Update') }}</h3>
                <p class="request-card-desc">
                    {{ __('Civil ID, driving license, or certificate renewals') }}
                </p>
            </div>
            <div class="request-card-footer">
                <span class="request-tag-text">{{ __('Human Resources') }}</span>
                <i class="bx {{ app()->getLocale() === 'ar' ? 'bx-left-arrow-alt' : 'bx-right-arrow-alt' }} request-arrow-icon"></i>
            </div>
        </a>

        <!-- 4. سلفة مالية (Financial Loan) -->
        <a href="javascript:void(0)" onclick="showPortalToast('success', '{{ __('Financial Advance') }}', '{{ __('Financial advance form will be available soon') }}')" class="request-item-card request-card-orange">
            <div>
                <div class="request-card-icon-box icon-box-gold">
                    <i class="bx bx-wallet"></i>
                </div>
                <h3 class="request-card-title">{{ __('Financial Advance') }}</h3>
                <p class="request-card-desc">
                    {{ __('Request salary advance or salary entitlement installments') }}
                </p>
            </div>
            <div class="request-card-footer">
                <span class="request-tag-text">{{ __('Accounting Dept') }}</span>
                <i class="bx {{ app()->getLocale() === 'ar' ? 'bx-left-arrow-alt' : 'bx-right-arrow-alt' }} request-arrow-icon"></i>
            </div>
        </a>
    </div>
</div>
@endsection
