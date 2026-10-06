@extends('employeeportal::layouts.master')

@section('title', __('Requests'))

@push('styles')
<style>
    .requests-page-container {
        padding-bottom: 28px;
    }

    /* Top Page Header */
    .requests-nav-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 14px;
        position: relative;
    }

    .requests-back-btn {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        background: #ffffff;
        border: 1px solid rgba(0, 51, 75, 0.08);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #00334B;
        font-size: 20px;
        text-decoration: none;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
        transition: all 0.2s ease;
    }

    .requests-back-btn:active {
        background: #EEF5F8;
        transform: scale(0.95);
    }

    .requests-header-title {
        font-size: 17px;
        font-weight: 800;
        color: #00334B;
        margin: 0;
        text-align: center;
        flex: 1;
        font-family: 'Tajawal', sans-serif;
    }

    .requests-nav-placeholder {
        width: 38px;
    }

    /* 1. Header Banner Card - Exactly matching Figma Spec */
    .requests-banner-card {
        width: 100%;
        min-height: 142px;
        border-radius: 12px;
        padding: 24px;
        background: linear-gradient(270deg, #002A44 0%, #004165 50%, #002A44 100%);
        box-shadow: 0px 2px 4px -2px rgba(0, 0, 0, 0.1), 0px 4px 6px -1px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 10px;
        margin-bottom: 18px;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        box-sizing: border-box;
    }

    .requests-banner-badge {
        display: inline-flex;
        align-items: center;
        align-self: flex-start;
        gap: 6px;
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.18);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        color: #E2E8F0;
    }

    .requests-banner-badge .badge-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #FDBB22;
    }

    .requests-banner-title {
        font-family: 'Tajawal', sans-serif;
        font-size: 18px;
        font-weight: 800;
        color: #FEAE2C;
        margin: 0;
        line-height: 1.3;
    }

    .requests-banner-desc {
        font-family: 'Tajawal', sans-serif;
        font-size: 11px;
        color: #CBD5E1;
        font-weight: 500;
        line-height: 1.45;
        margin: 0;
    }

    /* 2. Requests Grid (2 Columns) */
    .requests-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
        margin-bottom: 18px;
    }

    /* Request Card - Exactly matching Figma Spec */
    .request-item-card {
        width: 100%;
        min-height: 165.5px;
        border-radius: 12px;
        padding: 16px;
        background: #ffffff;
        border: 1px solid rgba(0, 51, 75, 0.06);
        box-shadow: 0 4px 15px rgba(0, 51, 75, 0.04);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        text-decoration: none;
        color: inherit;
        position: relative;
        overflow: hidden;
        transition: all 0.22s ease;
        box-sizing: border-box;
    }

    .request-item-card:hover, .request-item-card:active {
        transform: translateY(-2px);
        box-shadow: 0 8px 22px rgba(0, 51, 75, 0.09);
        border-color: rgba(0, 42, 68, 0.2);
    }

    /* Top colored accent line indicator */
    .request-item-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 14px;
        right: 14px;
        height: 3px;
        border-radius: 0 0 4px 4px;
        background: transparent;
    }

    .request-card-orange::before {
        background: #FEAE2C;
    }

    .request-card-navy::before {
        background: #002A44;
    }

    /* Icon Box */
    .request-card-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        margin-bottom: 10px;
        flex-shrink: 0;
    }

    .icon-box-orange {
        background: #FFF7ED;
        color: #EA580C;
        border: 1px solid #FFEDD5;
    }

    .icon-box-blue {
        background: #EFF6FF;
        color: #0284C7;
        border: 1px solid #E0F2FE;
    }

    .icon-box-navy {
        background: #F0F9FF;
        color: #0369A1;
        border: 1px solid #BAE6FD;
    }

    .icon-box-gold {
        background: #FEF3C7;
        color: #D97706;
        border: 1px solid #FDE68A;
    }

    /* Typography inside Card */
    .request-card-title {
        font-family: 'Tajawal', sans-serif;
        font-weight: 700;
        font-size: 16px;
        line-height: 22px;
        letter-spacing: 0px;
        text-align: right;
        color: #002A44;
        margin: 0 0 4px 0;
    }

    [dir="ltr"] .request-card-title {
        text-align: left;
    }

    .request-card-desc {
        font-family: 'Tajawal', sans-serif;
        font-weight: 400;
        font-size: 10px;
        line-height: 13.75px;
        letter-spacing: 0px;
        text-align: right;
        color: #64748B;
        margin: 0 0 10px 0;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    [dir="ltr"] .request-card-desc {
        text-align: left;
    }

    /* Footer / Tag Row */
    .request-card-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 8px;
        border-top: 1px dashed #F1F5F9;
        margin-top: auto;
    }

    .request-tag-text {
        font-family: 'Tajawal', sans-serif;
        font-size: 10.5px;
        font-weight: 700;
        color: #00334B;
    }

    .request-arrow-icon {
        font-size: 16px;
        color: #00334B;
        transition: transform 0.2s ease;
    }

    .request-item-card:hover .request-arrow-icon {
        transform: translateX(-3px);
    }

    [dir="ltr"] .request-item-card:hover .request-arrow-icon {
        transform: translateX(3px);
    }
</style>
@endpush

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
        <a href="javascript:void(0)" onclick="showPortalToast('success', '{{ __('Permission Request') }}', '{{ __('Permission request form will be available soon') }}')" class="request-item-card request-card-navy">
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
        <a href="javascript:void(0)" onclick="showPortalToast('success', '{{ __('Document Update') }}', '{{ __('Document upload form will be available soon') }}')" class="request-item-card request-card-navy">
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
