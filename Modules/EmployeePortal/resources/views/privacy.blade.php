@extends('employeeportal::layouts.master')

@section('title', __('Privacy Policy'))

@section('content')
<div class="privacy-page-container">
    <!-- Top Navigation Header -->
    <div class="privacy-nav-header">
        <a href="{{ route('portal.profile') }}" class="privacy-back-btn" title="{{ __('Back') }}">
            <i class="bx {{ app()->getLocale() === 'ar' ? 'bx-chevron-right' : 'bx-chevron-left' }}"></i>
        </a>
        <h1 class="privacy-header-title">{{ __('Privacy Policy') }}</h1>
        @include('employeeportal::components.notification-bell')
    </div>

    <!-- 1. Hero Introduction Card (Clean & Compact) -->
    <div class="privacy-hero-card">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h2 class="privacy-hero-label">{{ __('Privacy Policy & Data Protection') }}</h2>
                <span class="privacy-hero-subtitle">{{ __('Astro Leaders Academy • State of Kuwait') }}</span>
            </div>
            <div class="privacy-hero-icon-box">
                <i class="bx bxs-shield-check"></i>
            </div>
        </div>
    </div>

    <!-- 2. Streamlined Privacy Cards -->
    <div class="privacy-stacked-list">
        <!-- 1. Identity -->
        <div class="privacy-section-card">
            <div class="privacy-card-header">
                <div class="privacy-cat-icon icon-teal">
                    <i class="bx bx-buildings"></i>
                </div>
                <h3 class="privacy-cat-title">{{ __('1. Application Identity & Scope') }}</h3>
            </div>
            <div class="privacy-card-body">
                <p class="privacy-rule-text">
                    {{ __('ALA ORBIT is the official employee self-service and smart human resources management mobile application for Astro Leaders Academy in the State of Kuwait. This policy explains how we collect, handle, and protect personal and employment information.') }}
                </p>
            </div>
        </div>

        <!-- 2. Data We Collect -->
        <div class="privacy-section-card">
            <div class="privacy-card-header">
                <div class="privacy-cat-icon icon-blue">
                    <i class="bx bx-data"></i>
                </div>
                <h3 class="privacy-cat-title">{{ __('2. Information We Collect') }}</h3>
            </div>
            <div class="privacy-card-body">
                <ul class="privacy-bullet-list">
                    <li><strong>{{ __('Identity & Employment Data:') }}</strong> {{ __('Name, official email address, employee ID number, branch assignment, job title, and employment status.') }}</li>
                    <li><strong>{{ __('Precise Geolocation (GPS):') }}</strong> {{ __('Collected strictly during the physical check-in and check-out punch action to verify presence within designated campus geofences. Location is never tracked continuously or in the background.') }}</li>
                    <li><strong>{{ __('Camera & Photo Library:') }}</strong> {{ __('Requested only when the employee chooses to upload profile photos, sick leave medical reports, or official request documents.') }}</li>
                    <li><strong>{{ __('Push Notifications Device Token:') }}</strong> {{ __('Used solely to deliver shift reminders, leave request approvals, and administrative alerts.') }}</li>
                </ul>
            </div>
        </div>

        <!-- 3. Purpose -->
        <div class="privacy-section-card">
            <div class="privacy-card-header">
                <div class="privacy-cat-icon icon-purple">
                    <i class="bx bx-target-lock"></i>
                </div>
                <h3 class="privacy-cat-title">{{ __('3. Purpose of Processing') }}</h3>
            </div>
            <div class="privacy-card-body">
                <p class="privacy-rule-text">
                    {{ __('The data collected is processed strictly for human resources management, payroll computation, attendance verification, and internal communications between the employee and the nursery administration.') }}
                </p>
            </div>
        </div>

        <!-- 4. Security -->
        <div class="privacy-section-card">
            <div class="privacy-card-header">
                <div class="privacy-cat-icon icon-emerald">
                    <i class="bx bx-lock-alt"></i>
                </div>
                <h3 class="privacy-cat-title">{{ __('4. Security & Data Protection') }}</h3>
            </div>
            <div class="privacy-card-body">
                <p class="privacy-rule-text">
                    {{ __('All communications between ALA ORBIT and our servers are encrypted using modern Transport Layer Security (TLS 1.3 / HTTPS). Data is stored in secure database environments with strict access control. We never sell, rent, or share personal information with third-party advertisers.') }}
                </p>
            </div>
        </div>

        <!-- 5. User Rights -->
        <div class="privacy-section-card">
            <div class="privacy-card-header">
                <div class="privacy-cat-icon icon-red">
                    <i class="bx bx-user-x"></i>
                </div>
                <h3 class="privacy-cat-title">{{ __('5. Account Deletion & Data Rights') }}</h3>
            </div>
            <div class="privacy-card-body">
                <p class="privacy-rule-text mb-2">
                    {{ __('Employees have the right to review their personal information, update contact details, or request account deactivation and data purging upon termination of employment.') }}
                </p>
                <a href="{{ route('portal.profile') }}" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1 font-12 fw-bold">
                    <i class="bx bx-trash me-1"></i> {{ __('Request Account Deletion') }}
                </a>
            </div>
        </div>

        </div>
    </div>
</div>

@push('styles')
<style>
.privacy-page-container {
    padding-bottom: 95px;
    max-width: 440px;
    margin: 0 auto;
}

/* 1. Hero Introduction Card */
.privacy-hero-card {
    background: linear-gradient(135deg, #002A44 0%, #004B6B 100%);
    color: #ffffff;
    border-radius: 18px;
    padding: 16px 18px;
    margin-bottom: 14px;
    box-shadow: 0 4px 18px rgba(0, 42, 68, 0.12);
}

.privacy-hero-icon-box {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.14);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    color: #10B981;
    flex-shrink: 0;
}

.privacy-hero-label {
    font-size: 16px;
    font-weight: 800;
    color: #ffffff;
    margin: 0 0 2px 0;
}

.privacy-hero-subtitle {
    font-size: 11.5px;
    color: rgba(255, 255, 255, 0.75);
}

/* 2. Stacked Sections List */
.privacy-stacked-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.privacy-section-card {
    background: #ffffff;
    border-radius: 16px;
    border: 1px solid rgba(0, 51, 75, 0.06);
    box-shadow: 0 2px 10px rgba(0, 51, 75, 0.03);
    overflow: hidden;
}

.privacy-card-header {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    background: #ffffff;
    border-bottom: 1px solid rgba(0, 51, 75, 0.05);
}

.privacy-cat-icon {
    width: 32px;
    height: 32px;
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 17px;
    flex-shrink: 0;
}

.privacy-cat-icon.icon-teal { background: #E6FFFA; color: #0D9488; }
.privacy-cat-icon.icon-blue { background: #EFF6FF; color: #2563EB; }
.privacy-cat-icon.icon-purple { background: #FAF5FF; color: #9333EA; }
.privacy-cat-icon.icon-emerald { background: #ECFDF5; color: #059669; }
.privacy-cat-icon.icon-red { background: #FEF2F2; color: #DC2626; }
.privacy-cat-icon.icon-slate { background: #F8FAFC; color: #475569; }

.privacy-cat-title {
    font-size: 13px;
    font-weight: 800;
    color: #00334B;
    margin: 0;
    line-height: 1.3;
}

.privacy-card-body {
    padding: 12px 16px;
}

.privacy-rule-text {
    font-size: 12px;
    color: #475569;
    line-height: 1.6;
    margin: 0;
}

.privacy-bullet-list {
    margin: 0;
    padding-inline-start: 18px;
    font-size: 12px;
    color: #475569;
    line-height: 1.6;
}

.privacy-bullet-list li {
    margin-bottom: 6px;
}

.privacy-bullet-list li:last-child {
    margin-bottom: 0;
}
</style>
@endpush
@endsection
