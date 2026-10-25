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
@endsection
