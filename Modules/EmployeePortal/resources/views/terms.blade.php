@extends('employeeportal::layouts.master')

@section('title', __('Terms & Conditions'))

@section('content')
<div class="terms-page-container">
    <!-- Top Navigation Header -->
    <div class="terms-nav-header">
        <a href="{{ route('portal.profile') }}" class="terms-back-btn" title="{{ __('Back') }}">
            <i class="bx {{ app()->getLocale() === 'ar' ? 'bx-chevron-right' : 'bx-chevron-left' }}"></i>
        </a>
        <h1 class="terms-header-title">{{ __('Terms & Conditions') }}</h1>
        @include('employeeportal::components.notification-bell')
    </div>

    <!-- 1. Hero Introduction Card (Clean & Compact) -->
    <div class="terms-hero-card">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h2 class="terms-hero-label">{{ __('Terms of Service') }}</h2>
                <span class="terms-hero-subtitle">{{ __('Astro Leaders Academy • State of Kuwait') }}</span>
            </div>
            <div class="terms-hero-icon-box">
                <i class="bx bxs-file-doc"></i>
            </div>
        </div>
    </div>

    <!-- 2. Streamlined Terms Cards -->
    <div class="terms-stacked-list">
        <!-- 1. Acceptance -->
        <div class="terms-section-card">
            <div class="terms-card-header">
                <div class="terms-cat-icon icon-teal">
                    <i class="bx bx-check-double"></i>
                </div>
                <h3 class="terms-cat-title">{{ __('1. Acceptance & Authorization') }}</h3>
            </div>
            <div class="terms-card-body">
                <p class="terms-rule-text">
                    {{ __('By logging in and utilizing ALA ORBIT, you acknowledge and agree that this application is designated exclusively for staff and contractors of Astro Leaders Academy in the State of Kuwait. Continued usage constitutes binding acceptance of all nursery bylaws and directives.') }}
                </p>
            </div>
        </div>

        <!-- 2. Attendance & Biometric -->
        <div class="terms-section-card">
            <div class="terms-card-header">
                <div class="terms-cat-icon icon-blue">
                    <i class="bx bx-fingerprint"></i>
                </div>
                <h3 class="terms-cat-title">{{ __('2. Biometric & Geofence Punch Compliance') }}</h3>
            </div>
            <div class="terms-card-body">
                <ul class="terms-bullet-list">
                    <li><strong>{{ __('Physical Presence:') }}</strong> {{ __('Punches must be recorded by the employee in person inside the approved branch radius.') }}</li>
                    <li><strong>{{ __('No Location Manipulation:') }}</strong> {{ __('The use of GPS spoofing applications, mock locations, or third-party emulators is considered fraudulent conduct subject to disciplinary penalties under Kuwait Labor Law.') }}</li>
                    <li><strong>{{ __('Shift Punctuality:') }}</strong> {{ __('Working hours and grace allowances are automatically calculated per official nursery shift schedules.') }}</li>
                </ul>
            </div>
        </div>

        <!-- 3. Security -->
        <div class="terms-section-card">
            <div class="terms-card-header">
                <div class="terms-cat-icon icon-purple">
                    <i class="bx bx-shield-quarter"></i>
                </div>
                <h3 class="terms-cat-title">{{ __('3. Account Security & Confidentiality') }}</h3>
            </div>
            <div class="terms-card-body">
                <ul class="terms-bullet-list">
                    <li>{{ __('Sharing accounts, passwords, or temporary verification tokens with colleagues is strictly prohibited.') }}</li>
                    <li>{{ __('Any transaction, leave request, or document submission executed from your account is legally attributed to you.') }}</li>
                    <li>{{ __('You must immediately notify HR Administration if you suspect any unauthorized access to your account.') }}</li>
                </ul>
            </div>
        </div>

        <!-- 4. Integrity of Documents -->
        <div class="terms-section-card">
            <div class="terms-card-header">
                <div class="terms-cat-icon icon-emerald">
                    <i class="bx bx-file"></i>
                </div>
                <h3 class="terms-cat-title">{{ __('4. Integrity of Submitted Records & Requests') }}</h3>
            </div>
            <div class="terms-card-body">
                <p class="terms-rule-text">
                    {{ __('All submitted leave requests, permission slips, medical sick leave certificates, and personal identity documents must be genuine and accurate. Forgery or misrepresentation of medical or official records will result in immediate escalation to legal and management authorities.') }}
                </p>
            </div>
        </div>

        <!-- 5. Intellectual Property -->
        <div class="terms-section-card">
            <div class="terms-card-header">
                <div class="terms-cat-icon icon-gold">
                    <i class="bx bx-planet"></i>
                </div>
                <h3 class="terms-cat-title">{{ __('5. Intellectual Property & Brand Identity') }}</h3>
            </div>
            <div class="terms-card-body">
                <p class="terms-rule-text">
                    {{ __('The ALA ORBIT name, logo, galaxy UI identity, software algorithms, and educational assets are exclusive intellectual property of Astro Leaders Academy. Unauthorized reproduction or reverse engineering is prohibited.') }}
                </p>
            </div>
        </div>

        </div>
    </div>
</div>

@push('styles')
<style>
.terms-page-container {
    padding-bottom: 95px;
    max-width: 440px;
    margin: 0 auto;
}

/* 1. Hero Introduction Card */
.terms-hero-card {
    background: linear-gradient(135deg, #002A44 0%, #004B6B 100%);
    color: #ffffff;
    border-radius: 18px;
    padding: 16px 18px;
    margin-bottom: 14px;
    box-shadow: 0 4px 18px rgba(0, 42, 68, 0.12);
}

.terms-hero-icon-box {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.14);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    color: #FDBB22;
    flex-shrink: 0;
}

.terms-hero-label {
    font-size: 16px;
    font-weight: 800;
    color: #ffffff;
    margin: 0 0 2px 0;
}

.terms-hero-subtitle {
    font-size: 11.5px;
    color: rgba(255, 255, 255, 0.75);
}

/* 2. Stacked Sections List */
.terms-stacked-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.terms-section-card {
    background: #ffffff;
    border-radius: 16px;
    border: 1px solid rgba(0, 51, 75, 0.06);
    box-shadow: 0 2px 10px rgba(0, 51, 75, 0.03);
    overflow: hidden;
}

.terms-card-header {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    background: #ffffff;
    border-bottom: 1px solid rgba(0, 51, 75, 0.05);
}

.terms-cat-icon {
    width: 32px;
    height: 32px;
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 17px;
    flex-shrink: 0;
}

.terms-cat-icon.icon-teal { background: #E6FFFA; color: #0D9488; }
.terms-cat-icon.icon-blue { background: #EFF6FF; color: #2563EB; }
.terms-cat-icon.icon-purple { background: #FAF5FF; color: #9333EA; }
.terms-cat-icon.icon-emerald { background: #ECFDF5; color: #059669; }
.terms-cat-icon.icon-gold { background: #FEF9C3; color: #CA8A04; }
.terms-cat-icon.icon-slate { background: #F8FAFC; color: #475569; }

.terms-cat-title {
    font-size: 13px;
    font-weight: 800;
    color: #00334B;
    margin: 0;
    line-height: 1.3;
}

.terms-card-body {
    padding: 12px 16px;
}

.terms-rule-text {
    font-size: 12px;
    color: #475569;
    line-height: 1.6;
    margin: 0;
}

.terms-bullet-list {
    margin: 0;
    padding-inline-start: 18px;
    font-size: 12px;
    color: #475569;
    line-height: 1.6;
}

.terms-bullet-list li {
    margin-bottom: 6px;
}

.terms-bullet-list li:last-child {
    margin-bottom: 0;
}
</style>
@endpush
@endsection
