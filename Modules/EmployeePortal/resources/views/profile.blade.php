@extends('employeeportal::layouts.master')

@section('title', __('Profile'))

@push('styles')
<style>
    /* Profile Page Mobile Styles */
    .profile-page-container {
        padding-bottom: 24px;
    }

    /* Top Page Header */
    .profile-nav-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
    }

    .profile-back-btn {
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

    .profile-back-btn:active {
        background: #EEF5F8;
        transform: scale(0.95);
    }

    .profile-header-title {
        font-size: 17px;
        font-weight: 800;
        color: #00334B;
        margin: 0;
    }

    .profile-header-action {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        background: #ffffff;
        border: 1px solid rgba(0, 51, 75, 0.08);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #64748B;
        font-size: 18px;
    }

    /* Hero Profile Card - Custom Figma Spec */
    .profile-hero-card {
        width: 100%;
        margin: 0 0 16px 0;
        background: linear-gradient(135deg, #053146 0%, #00334B 50%, #0C4663 100%);
        border-radius: 24px;
        padding: 20px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 14px;
        box-shadow: 0px 2px 6px -1px rgba(0, 51, 75, 0.03), 0px 4px 20px -2px rgba(0, 51, 75, 0.06);
        color: #ffffff;
        position: relative;
        overflow: hidden;
        box-sizing: border-box;
    }

    .profile-hero-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        direction: rtl;
    }

    [dir="ltr"] .profile-hero-top {
        direction: ltr;
    }

    .profile-hero-info {
        flex: 1;
        min-width: 0;
        text-align: right;
    }

    [dir="ltr"] .profile-hero-info {
        text-align: left;
    }

    .profile-name {
        font-size: 16.5px;
        font-weight: 800;
        color: #FDBB22;
        margin: 0 0 4px 0;
        line-height: 1.3;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .profile-job-title {
        font-size: 12px;
        color: #E2E8F0;
        font-weight: 600;
        margin-bottom: 4px;
        line-height: 1.35;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .profile-location-sub {
        font-size: 11px;
        color: #94A3B8;
        font-weight: 500;
        line-height: 1.3;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .profile-avatar-wrapper {
        position: relative;
        width: 76px;
        height: 76px;
        flex-shrink: 0;
    }

    .profile-avatar-img {
        width: 76px;
        height: 76px;
        border-radius: 18px;
        object-fit: cover;
        border: 2.5px solid #FDBB22;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
        display: block;
    }

    .profile-camera-btn {
        position: absolute;
        bottom: -3px;
        left: -3px;
        width: 26px;
        height: 26px;
        border-radius: 9px;
        background: #FDBB22;
        color: #00334B;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        border: 2px solid #00334B;
        cursor: pointer;
        transition: transform 0.2s;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }

    [dir="ltr"] .profile-camera-btn {
        left: auto;
        right: -3px;
    }

    .profile-camera-btn:hover {
        transform: scale(1.08);
    }

    /* 3 Compact Metric Pills */
    .profile-hero-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
        direction: rtl;
    }

    [dir="ltr"] .profile-hero-stats {
        direction: ltr;
    }

    .profile-stat-box {
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.09);
        border-radius: 14px;
        padding: 8px 4px;
        text-align: center;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 2px;
        min-height: 48px;
    }

    .profile-stat-label {
        font-size: 9.5px;
        color: #94A3B8;
        font-weight: 500;
        white-space: nowrap;
    }

    .profile-stat-val {
        font-size: 11.5px;
        color: #FFFFFF;
        font-weight: 700;
        white-space: nowrap;
    }

    /* Bottom Edit Action Button */
    .profile-hero-edit-btn {
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.18);
        border-radius: 14px;
        padding: 9px 12px;
        color: #E2E8F0;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .profile-hero-edit-btn:hover {
        background: rgba(255, 255, 255, 0.14);
        border-color: rgba(253, 187, 34, 0.5);
        color: #FFFFFF;
    }

    .profile-hero-edit-btn i {
        color: #FDBB22;
        font-size: 15px;
    }

    /* Section Card */
    .profile-section-card {
        background: #ffffff;
        border-radius: 20px;
        padding: 18px;
        margin-bottom: 14px;
        box-shadow: 0 3px 15px rgba(0, 0, 0, 0.03);
        border: 1px solid rgba(0, 0, 0, 0.04);
    }

    .profile-section-header {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 14px;
        padding-bottom: 10px;
        border-bottom: 1px solid #F1F5F9;
    }

    .profile-section-header i {
        font-size: 20px;
        color: #004B6B;
    }

    .profile-section-title {
        font-size: 14px;
        font-weight: 800;
        color: #00334B;
        margin: 0;
    }

    .info-list-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 9px 0;
        border-bottom: 1px dashed #F1F5F9;
    }

    .info-list-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .info-label {
        font-size: 12.5px;
        font-weight: 600;
        color: #64748B;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .info-label i {
        font-size: 16px;
        color: #94A3B8;
    }

    .info-value {
        font-size: 13px;
        font-weight: 700;
        color: #0F172A;
        text-align: left;
        direction: ltr;
    }

    .info-value-rtl {
        direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};
        text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
    }

    .lang-switch-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 12px;
        font-size: 12.5px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .lang-switch-active {
        background: rgba(0, 51, 75, 0.08);
        color: #00334B;
        border: 1px solid rgba(0, 51, 75, 0.15);
    }

    .lang-switch-btn {
        background: #FDBB22;
        color: #00334B;
        border: 1px solid #F59E0B;
        box-shadow: 0 2px 8px rgba(253, 187, 34, 0.35);
    }

    .lang-switch-btn:hover {
        background: #F59E0B;
        color: #00334B;
    }

    /* Logout Action Card */
    .logout-section-card {
        margin-top: 22px;
        margin-bottom: 10px;
    }

    .portal-logout-btn {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        background: #FEE2E2;
        color: #DC2626;
        border: 1.5px solid #FCA5A5;
        border-radius: 16px;
        padding: 15px 20px;
        font-size: 15px;
        font-weight: 800;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.08);
        transition: all 0.2s ease;
    }

    .portal-logout-btn:active {
        background: #FCA5A5;
        color: #991B1B;
        transform: scale(0.98);
    }

    .portal-logout-btn i {
        font-size: 20px;
    }

    .app-version-info {
        text-align: center;
        font-size: 11px;
        font-weight: 600;
        color: #94A3B8;
        margin-top: 14px;
    }
</style>
@endpush

@section('content')
<div class="profile-page-container">
    <!-- 1. Header with back button -->
    <div class="profile-nav-header">
        <a href="{{ route('portal.dashboard', [], false) }}" class="profile-back-btn" title="{{ __('Back to Home') }}">
            <i class="bx {{ app()->getLocale() === 'ar' ? 'bx-right-arrow-alt' : 'bx-left-arrow-alt' }}"></i>
        </a>
        <h1 class="profile-header-title">{{ __('Profile') }}</h1>
        <div class="profile-header-action">
            <i class="bx bx-badge-check" style="color: #FDBB22;"></i>
        </div>
    </div>

    <!-- 2. Hero Profile Card -->
    <div class="profile-hero-card">
        <!-- Top Section: Squircle Avatar (Right) & Info (Left) in RTL -->
        <div class="profile-hero-top">
            <div class="profile-avatar-wrapper">
                <img src="{{ $employee?->avatar_url ?? auth()->user()?->avatar_url ?? asset('assets/portal/male-avatar.png') }}" alt="Avatar" class="profile-avatar-img" />
                <button type="button" class="profile-camera-btn" title="{{ __('Change Photo') }}" onclick="document.getElementById('avatar-upload-input')?.click()">
                    <i class="bx bxs-camera"></i>
                </button>
            </div>

            <div class="profile-hero-info">
                <h2 class="profile-name">{{ $employee?->name ?? auth()->user()?->name ?? 'Nourah Al-Kandari' }}</h2>
                <div class="profile-job-title">
                    {{ $employee?->jobTitle?->name ?? __('Kindergarten Teacher') }}
                    • {{ __('Astronauts Class') }}
                </div>
                <div class="profile-location-sub">
                    {{ __('Hawally Main Branch') }} • {{ __('State of Kuwait') }}
                </div>
            </div>
        </div>

        <!-- Middle Section: 3 Stats Cards -->
        

        <!-- Bottom Action: Edit Personal & Contact Data -->
        <a href="{{ route('portal.profile.edit') }}" class="profile-hero-edit-btn">
            <i class="bx bx-edit-alt"></i>
            <span>{{ __('Edit Personal & Contact Data') }}</span>
        </a>
    </div>

    <!-- 3. Personal & Contact Information -->
    <div class="profile-section-card">
        <div class="profile-section-header">
            <i class="bx bx-user-pin"></i>
            <h3 class="profile-section-title">{{ __('Job & Contact Information') }}</h3>
        </div>

        <div class="info-list-item">
            <span class="info-label">
                <i class="bx bx-envelope"></i>
                <span>{{ __('Email Address') }}</span>
            </span>
            <span class="info-value">{{ auth()->user()?->email ?? $employee?->email ?? 'teacher@astroleaders.com' }}</span>
        </div>

        <div class="info-list-item">
            <span class="info-label">
                <i class="bx bx-phone"></i>
                <span>{{ __('Phone Number') }}</span>
            </span>
            <span class="info-value">{{ $employee?->phone ?? '965-98765432' }}</span>
        </div>

        <div class="info-list-item">
            <span class="info-label">
                <i class="bx bx-calendar"></i>
                <span>{{ __('Hire Date') }}</span>
            </span>
            <span class="info-value info-value-rtl">{{ $employee?->joining_date ?? '01-09-2024' }}</span>
        </div>
    </div>

    <!-- 4. Work & Shift Schedule Information -->
    <div class="profile-section-card">
        <div class="profile-section-header">
            <i class="bx bx-time-five"></i>
            <h3 class="profile-section-title">{{ __('Official Work Schedule') }}</h3>
        </div>

        <div class="info-list-item">
            <span class="info-label">
                <i class="bx bx-sun"></i>
                <span>{{ __('Current Shift') }}</span>
            </span>
            <span class="info-value info-value-rtl">{{ $employee?->shift?->name ?? __('Morning Duty (06:45 AM - 01:45 PM)') }}</span>
        </div>

        <div class="info-list-item">
            <span class="info-label">
                <i class="bx bx-stopwatch"></i>
                <span>{{ __('Grace Period') }}</span>
            </span>
            <span class="info-value info-value-rtl">{{ __('15 minutes (until 07:00 AM)') }}</span>
        </div>

        <div class="info-list-item">
            <span class="info-label">
                <i class="bx bx-map-pin"></i>
                <span>{{ __('Geofence Range') }}</span>
            </span>
            <span class="info-value info-value-rtl" style="color: #10B981;">
                <i class="bx bx-check-shield"></i> {{ __('Active (GPS)') }}
            </span>
        </div>
    </div>

    <!-- 5. App Language Preferences -->
    <div class="profile-section-card">
        <div class="profile-section-header">
            <i class="bx bx-globe"></i>
            <h3 class="profile-section-title">{{ __('App Language') }}</h3>
        </div>

        <div class="info-list-item">
            <span class="info-label">
                <i class="bx bx-transfer-alt"></i>
                <span>{{ app()->getLocale() === 'ar' ? __('Current Language: Arabic') : __('Current Language: English') }}</span>
            </span>
            @if(app()->getLocale() === 'ar')
                <a href="{{ route('lang.swap', 'en') }}" class="lang-switch-badge lang-switch-btn">
                    <span>English</span>
                    <i class="bx bx-right-arrow-alt"></i>
                </a>
            @else
                <a href="{{ route('lang.swap', 'ar') }}" class="lang-switch-badge lang-switch-btn">
                    <span>العربية</span>
                    <i class="bx bx-left-arrow-alt"></i>
                </a>
            @endif
        </div>
    </div>

    <!-- 6. Logout Action Button -->
    <div class="logout-section-card">
        <form action="{{ route('portal.logout', [], false) }}" method="POST" id="portalLogoutForm">
            @csrf
            <button type="submit" class="portal-logout-btn" onclick="return confirm('{{ __('Are you sure you want to log out from the portal?') }}')">
                <i class="bx bx-log-out"></i>
                <span>{{ __('Log Out from Portal') }}</span>
            </button>
        </form>
    </div>

    <!-- PWA App Version -->
    <div class="app-version-info">
        {{ __('Astro Leaders Nursery • App Version v1.2 PWA') }}
    </div>
</div>
@endsection
