@extends('employeeportal::layouts.master')

@section('title', __('Permission Request'))

@push('styles')
<!-- Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">

<style>
    .perm-page-container {
        padding-bottom: 35px;
    }

    /* Top Page Header */
    .perm-nav-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 14px;
        position: relative;
    }

    .perm-back-btn {
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

    .perm-back-btn:active {
        background: #EEF5F8;
        transform: scale(0.95);
    }

    .perm-header-title {
        font-family: 'Tajawal', sans-serif;
        font-size: 17px;
        font-weight: 800;
        color: #00334B;
        margin: 0;
        text-align: center;
        flex: 1;
    }

    .perm-nav-placeholder {
        width: 38px;
    }

    /* 1. Header Banner Card */
    .perm-hero-card {
        width: 100%;
        border-radius: 16px;
        padding: 18px 16px;
        background: linear-gradient(135deg, #002A44 0%, #00456C 100%);
        box-shadow: 0px 8px 16px -4px rgba(0, 42, 68, 0.2), 0px 18px 24px -6px rgba(0, 42, 68, 0.15);
        display: flex;
        flex-direction: column;
        gap: 14px;
        margin-bottom: 18px;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        box-sizing: border-box;
    }

    .perm-hero-card::after {
        content: '';
        position: absolute;
        top: -30px;
        left: -30px;
        width: 110px;
        height: 110px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(254, 174, 44, 0.2) 0%, rgba(254, 174, 44, 0) 70%);
        pointer-events: none;
    }

    .perm-hero-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .perm-hero-title-group {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .perm-hero-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: rgba(254, 174, 44, 0.18);
        border: 1px solid rgba(254, 174, 44, 0.35);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #FEAE2C;
        font-size: 24px;
        flex-shrink: 0;
    }

    .perm-hero-title {
        font-family: 'Tajawal', sans-serif;
        font-size: 18px;
        font-weight: 800;
        color: #FEAE2C;
        margin: 0;
        line-height: 1.25;
    }

    .perm-hero-subtitle {
        font-size: 11px;
        color: #CBD5E1;
        margin: 0;
        font-weight: 500;
    }

    .perm-history-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.18);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        color: #FFFFFF;
        text-decoration: none;
        transition: all 0.2s;
    }

    .perm-history-pill:active {
        background: rgba(255, 255, 255, 0.22);
    }

    .perm-history-pill .dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #FEAE2C;
    }

    /* Hero Balance 3-Stat Grid */
    .perm-stats-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 8px;
        background: rgba(0, 0, 0, 0.22);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        padding: 10px 8px;
    }

    .perm-stat-col {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        position: relative;
    }

    .perm-stat-col:not(:last-child)::after {
        content: '';
        position: absolute;
        top: 15%;
        bottom: 15%;
        left: 0;
        width: 1px;
        background: rgba(255, 255, 255, 0.12);
    }

    [dir="ltr"] .perm-stat-col:not(:last-child)::after {
        left: auto;
        right: 0;
    }

    .perm-stat-label {
        font-size: 10px;
        color: #94A3B8;
        font-weight: 600;
        margin-bottom: 2px;
    }

    .perm-stat-val {
        font-size: 15px;
        font-weight: 900;
        color: #FEAE2C;
        line-height: 1.2;
    }

    .perm-stat-sub {
        font-size: 9px;
        color: #E2E8F0;
        font-weight: 500;
    }

    /* 2. Section Cards */
    .perm-section-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 16px;
        margin-bottom: 14px;
        box-shadow: 0 3px 12px rgba(0, 51, 75, 0.04);
        border: 1px solid rgba(0, 51, 75, 0.06);
    }

    .perm-section-title {
        font-family: 'Tajawal', sans-serif;
        font-size: 14px;
        font-weight: 800;
        color: #002A44;
        margin: 0 0 12px 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .perm-section-title i {
        font-size: 18px;
        color: #FEAE2C;
    }

    .perm-field-label {
        display: block;
        font-family: 'Tajawal', sans-serif;
        font-size: 12px;
        font-weight: 700;
        color: #002A44;
        margin-bottom: 6px;
    }

    /* Permission Type Grid (4 Options) */
    .perm-types-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-bottom: 8px;
    }

    .perm-type-radio-card {
        position: relative;
        background: #F8FAFC;
        border: 1.5px solid #E2E8F0;
        border-radius: 12px;
        padding: 12px 10px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 96px;
        box-sizing: border-box;
    }

    .perm-type-radio-card:hover {
        border-color: #CBD5E1;
        background: #F1F5F9;
    }

    .perm-type-radio-card.active {
        background: #F0F9FF;
        border-color: #0284C7;
        box-shadow: 0 2px 8px rgba(2, 132, 199, 0.12);
    }

    .perm-type-radio-card.active.is-emergency {
        background: #FFF1F2;
        border-color: #E11D48;
        box-shadow: 0 2px 8px rgba(225, 29, 72, 0.12);
    }

    .perm-type-card-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 6px;
    }

    .perm-type-card-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    .icon-blue {
        background: #EFF6FF;
        color: #0284C7;
    }

    .icon-amber {
        background: #FFFBEB;
        color: #D97706;
    }

    .icon-indigo {
        background: #EEF2FF;
        color: #4F46E5;
    }

    .icon-rose {
        background: #FFF1F2;
        color: #E11D48;
    }

    .perm-type-check-dot {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        border: 2px solid #CBD5E1;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        color: transparent;
        transition: all 0.2s;
    }

    .perm-type-radio-card.active .perm-type-check-dot {
        background: #0284C7;
        border-color: #0284C7;
        color: #ffffff;
    }

    .perm-type-radio-card.active.is-emergency .perm-type-check-dot {
        background: #E11D48;
        border-color: #E11D48;
        color: #ffffff;
    }

    .perm-type-card-title {
        font-family: 'Tajawal', sans-serif;
        font-size: 12.5px;
        font-weight: 800;
        color: #002A44;
        margin: 0 0 2px 0;
        line-height: 1.2;
    }

    .perm-type-card-desc {
        font-size: 10px;
        color: #64748B;
        margin: 0;
        line-height: 1.25;
    }

    /* Date and Quick Chips */
    .date-chips-row {
        display: flex;
        gap: 6px;
        margin-top: 6px;
    }

    .date-chip {
        padding: 3px 10px;
        border-radius: 8px;
        background: #F1F5F9;
        color: #00334B;
        font-size: 10.5px;
        font-weight: 700;
        border: 1px solid #E2E8F0;
        cursor: pointer;
        transition: all 0.15s;
    }

    .date-chip:active {
        background: #00334B;
        color: #ffffff;
        border-color: #00334B;
    }

    /* Time Range Pickers (2 Columns) */
    .time-pickers-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 12px;
    }

    .time-field-group {
        display: flex;
        flex-direction: column;
    }

    .time-input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .time-input-wrapper i {
        position: absolute;
        right: 14px;
        font-size: 18px;
        color: #64748B;
        pointer-events: none;
    }

    [dir="ltr"] .time-input-wrapper i {
        right: auto;
        left: 14px;
    }

    .flatpickr-custom-input {
        width: 100%;
        height: 48px;
        background: #F0F4F8;
        border: 1px solid #E2E8F0;
        border-radius: 12px;
        padding: 0 40px 0 12px;
        font-family: 'Tajawal', sans-serif;
        font-size: 13.5px;
        font-weight: 800;
        color: #002A44;
        outline: none;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    [dir="ltr"] .flatpickr-custom-input {
        padding: 0 12px 0 40px;
    }

    .flatpickr-custom-input:focus {
        background: #ffffff;
        border-color: #002A44;
        box-shadow: none;
    }

    /* Duration Summary Card */
    .duration-summary-box {
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        border-radius: 12px;
        padding: 12px 14px;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .duration-summary-main {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .duration-title-group {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12.5px;
        font-weight: 800;
        color: #002A44;
    }

    .duration-pill {
        background: #002A44;
        color: #FEAE2C;
        font-size: 13px;
        font-weight: 900;
        padding: 4px 12px;
        border-radius: 8px;
        box-shadow: 0 2px 6px rgba(0, 42, 68, 0.15);
    }

    .duration-projection-line {
        font-size: 11px;
        color: #475569;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 6px;
        border-top: 1px dashed #E2E8F0;
    }

    .duration-alert-box {
        background: #FFF1F2;
        border: 1px solid #FECDD3;
        color: #BE123C;
        padding: 8px 12px;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 700;
        display: none;
        align-items: center;
        gap: 6px;
    }

    .duration-alert-box.active {
        display: flex;
    }

    /* Emergency Toggle Switch */
    .perm-toggle-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 6px 0;
    }

    .perm-toggle-text {
        display: flex;
        flex-direction: column;
    }

    .perm-toggle-title {
        font-size: 13px;
        font-weight: 800;
        color: #002A44;
        margin: 0;
    }

    .perm-toggle-sub {
        font-size: 10px;
        color: #64748B;
        margin: 0;
    }

    /* iOS Switch */
    .switch-ios {
        position: relative;
        display: inline-block;
        width: 48px;
        height: 28px;
        flex-shrink: 0;
    }

    .switch-ios input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .switch-ios-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #E2E8F0;
        transition: .3s;
        border-radius: 34px;
    }

    .switch-ios-slider:before {
        position: absolute;
        content: "";
        height: 22px;
        width: 22px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .3s;
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .switch-ios input:checked + .switch-ios-slider {
        background-color: #E11D48;
    }

    .switch-ios input:checked + .switch-ios-slider:before {
        transform: translateX(20px);
    }

    /* Reason Textarea */
    .perm-textarea {
        width: 100%;
        min-height: 80px;
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        border-radius: 12px;
        padding: 12px;
        font-family: 'Tajawal', sans-serif;
        font-size: 12.5px;
        color: #1E293B;
        outline: none;
        resize: none;
        transition: all 0.2s;
        box-sizing: border-box;
    }

    .perm-textarea:focus {
        background: #ffffff;
        border-color: #002A44;
        box-shadow: none;
    }

    /* Upload Area */
    .perm-upload-dropzone {
        background: #F0F4F8;
        border: 1.5px dashed #CBD5E1;
        border-radius: 14px;
        padding: 16px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
    }

    .perm-upload-dropzone:hover {
        background: #E2E8F0;
        border-color: #002A44;
    }

    .upload-cloud-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #E0E7FF;
        color: #3730A3;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin: 0 auto 6px auto;
    }

    .upload-title {
        font-size: 12px;
        font-weight: 800;
        color: #002A44;
        margin-bottom: 2px;
    }

    .upload-sub {
        font-size: 10px;
        color: #64748B;
    }

    /* Submit Button */
    .btn-submit-perm-custom {
        width: 100%;
        height: 54px;
        border-radius: 16px;
        background: #002A44;
        color: #FEAE2C;
        border: none;
        font-family: 'Tajawal', sans-serif;
        font-size: 16px;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        box-shadow: 0 6px 20px rgba(0, 42, 68, 0.22);
        transition: all 0.2s ease;
        text-decoration: none;
        margin-top: 16px;
    }

    .btn-submit-perm-custom:active {
        transform: scale(0.98);
        background: #001f33;
    }

    .btn-submit-perm-custom:disabled {
        opacity: 0.65;
        cursor: not-allowed;
    }

    /* Recent Permissions Accordion / List */
    .perm-history-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 16px;
        margin-top: 20px;
        border: 1px solid rgba(0, 51, 75, 0.06);
        box-shadow: 0 3px 12px rgba(0, 51, 75, 0.04);
    }

    .perm-history-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #F1F5F9;
    }

    .perm-history-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .perm-history-left {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .perm-history-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }

    .perm-history-info {
        display: flex;
        flex-direction: column;
    }

    .perm-history-title {
        font-size: 12.5px;
        font-weight: 800;
        color: #002A44;
        margin: 0;
    }

    .perm-history-meta {
        font-size: 10px;
        color: #64748B;
        margin: 0;
    }

    .perm-history-badge {
        font-size: 10px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 6px;
    }
</style>
@endpush

@section('content')
<div class="perm-page-container">
    <!-- Top Navigation Header -->
    <div class="perm-nav-header">
        <a href="{{ route('portal.requests') }}" class="perm-back-btn" title="{{ __('Back') }}">
            <i class="bx {{ app()->getLocale() === 'ar' ? 'bx-right-arrow-alt' : 'bx-left-arrow-alt' }}"></i>
        </a>
        <h1 class="perm-header-title">{{ __('Permission Request') }}</h1>
        <div class="perm-nav-placeholder"></div>
    </div>

    <!-- 1. Header Hero Card with Live Policy Balance -->
    <div class="perm-hero-card">
        <div class="perm-hero-top">
            <div class="perm-hero-title-group">
                <div class="perm-hero-icon-box">
                    <i class="bx bx-time-five"></i>
                </div>
                <div>
                    <h2 class="perm-hero-title">{{ __('New Permission Request') }}</h2>
                    <p class="perm-hero-subtitle">{{ __('Monthly Permission Balance') }}</p>
                </div>
            </div>

            <a href="#recentPermissionsSection" class="perm-history-pill">
                <span class="dot"></span>
                <span>{{ __('Requests History') }}</span>
            </a>
        </div>

        <!-- 3-Metrics Grid -->
        <div class="perm-stats-grid">
            <div class="perm-stat-col">
                <span class="perm-stat-label">{{ __('Remaining Hours') }}</span>
                <span class="perm-stat-val" id="heroRemainingHours">{{ $balance['remaining_hours'] }} {{ __('hrs') }}</span>
                <span class="perm-stat-sub">{{ __('Total:') }} {{ $balance['max_monthly_hours'] }} {{ __('hrs') }}</span>
            </div>

            <div class="perm-stat-col">
                <span class="perm-stat-label">{{ __('Available Requests') }}</span>
                <span class="perm-stat-val" id="heroRemainingCount">{{ $balance['remaining_count'] }}</span>
                <span class="perm-stat-sub">{{ __('of') }} {{ $balance['max_monthly_count'] }} {{ __('times') }}</span>
            </div>

            <div class="perm-stat-col">
                <span class="perm-stat-label">{{ __('Max Single Limit') }}</span>
                <span class="perm-stat-val">{{ $balance['max_single_hours'] }} {{ __('hrs') }}</span>
                <span class="perm-stat-sub">{{ __('Maximum') }}</span>
            </div>
        </div>
    </div>

    <!-- Main Submission Form -->
    <form action="{{ route('portal.permissions.store') }}" method="POST" enctype="multipart/form-data" id="permissionSubmitForm">
        @csrf

        <!-- 2. Permission Type Selection Card -->
        <div class="perm-section-card">
            <h3 class="perm-section-title">
                <span>{{ __('Requested Permission Type') }}</span>
                <i class="bx bx-category-alt"></i>
            </h3>

            <!-- Hidden input for type -->
            <input type="hidden" name="type" id="selectedPermissionType" value="{{ \Modules\HR\Enums\PermissionType::DURING_SHIFT->value }}">

            <div class="perm-types-grid">
                <!-- 1. During Shift (خروج أثناء الدوام) -->
                <div class="perm-type-radio-card active" data-type="during_shift" onclick="selectPermissionType('during_shift', this)">
                    <div class="perm-type-card-top">
                        <div class="perm-type-card-icon icon-blue">
                            <i class="bx bx-transfer"></i>
                        </div>
                        <div class="perm-type-check-dot">
                            <i class="bx bx-check"></i>
                        </div>
                    </div>
                    <div>
                        <h4 class="perm-type-card-title">{{ __('During Shift') }}</h4>
                        <p class="perm-type-card-desc">{{ __('Temporary exit and return during classes') }}</p>
                    </div>
                </div>

                <!-- 2. Late Arrival (حضور متأخر) -->
                <div class="perm-type-radio-card" data-type="late_arrival" onclick="selectPermissionType('late_arrival', this)">
                    <div class="perm-type-card-top">
                        <div class="perm-type-card-icon icon-amber">
                            <i class="bx bx-time"></i>
                        </div>
                        <div class="perm-type-check-dot">
                            <i class="bx bx-check"></i>
                        </div>
                    </div>
                    <div>
                        <h4 class="perm-type-card-title">{{ __('Late Arrival') }}</h4>
                        <p class="perm-type-card-desc">{{ __('Arrival after morning official shift starts') }}</p>
                    </div>
                </div>

                <!-- 3. Early Departure (انصراف مبكر) -->
                <div class="perm-type-radio-card" data-type="early_departure" onclick="selectPermissionType('early_departure', this)">
                    <div class="perm-type-card-top">
                        <div class="perm-type-card-icon icon-indigo">
                            <i class="bx bx-log-out-circle"></i>
                        </div>
                        <div class="perm-type-check-dot">
                            <i class="bx bx-check"></i>
                        </div>
                    </div>
                    <div>
                        <h4 class="perm-type-card-title">{{ __('Early Departure') }}</h4>
                        <p class="perm-type-card-desc">{{ __('Leaving before official shift ends') }}</p>
                    </div>
                </div>

                <!-- 4. Emergency (استئذان طارئ) -->
                <div class="perm-type-radio-card is-emergency" data-type="emergency" onclick="selectPermissionType('emergency', this)">
                    <div class="perm-type-card-top">
                        <div class="perm-type-card-icon icon-rose">
                            <i class="bx bx-alarm-exclamation"></i>
                        </div>
                        <div class="perm-type-check-dot">
                            <i class="bx bx-check"></i>
                        </div>
                    </div>
                    <div>
                        <h4 class="perm-type-card-title">{{ __('Emergency Permission') }}</h4>
                        <p class="perm-type-card-desc">{{ __('Urgent condition bypassing advance notice') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Date & Time Period Card -->
        <div class="perm-section-card">
            <h3 class="perm-section-title">
                <span>{{ __('Date & Time Period') }}</span>
                <i class="bx bx-calendar-event"></i>
            </h3>

            <!-- Date Picker with Quick Chips -->
            <div class="mb-3">
                <label class="perm-field-label" for="permissionDate">{{ __('Permission Date') }}</label>
                <div class="time-input-wrapper">
                    <input type="text"
                           name="date"
                           id="permissionDate"
                           class="js-flatpickr-date flatpickr-custom-input"
                           value="{{ date('Y-m-d') }}"
                           placeholder="YYYY-MM-DD"
                           required />
                    <i class="bx bx-calendar"></i>
                </div>
                <div class="date-chips-row">
                    <button type="button" class="date-chip" onclick="setDateQuickly('{{ date('Y-m-d') }}')">{{ __('Today') }}</button>
                    <button type="button" class="date-chip" onclick="setDateQuickly('{{ date('Y-m-d', strtotime('+1 day')) }}')">{{ __('Tomorrow') }}</button>
                </div>
            </div>

            <!-- Time Pickers Grid (From Time & To Time) -->
            <div class="time-pickers-grid">
                <!-- From Time -->
                <div class="time-field-group">
                    <label class="perm-field-label" for="permissionFromTime">{{ __('From Time') }}</label>
                    <div class="time-input-wrapper">
                        <input type="text"
                               name="from_time"
                               id="permissionFromTime"
                               class="flatpickr-custom-input"
                               value="08:00"
                               placeholder="HH:MM"
                               required />
                        <i class="bx bx-time"></i>
                    </div>
                </div>

                <!-- To Time -->
                <div class="time-field-group">
                    <label class="perm-field-label" for="permissionToTime">{{ __('To Time') }}</label>
                    <div class="time-input-wrapper">
                        <input type="text"
                               name="to_time"
                               id="permissionToTime"
                               class="flatpickr-custom-input"
                               value="10:00"
                               placeholder="HH:MM"
                               required />
                        <i class="bx bx-time"></i>
                    </div>
                </div>
            </div>

            <!-- Live Duration & Policy Projection Summary -->
            <div class="duration-summary-box">
                <div class="duration-summary-main">
                    <div class="duration-title-group">
                        <i class="bx bx-timer text-warning"></i>
                        <span>{{ __('Calculated Duration:') }}</span>
                    </div>
                    <span class="duration-pill" id="calculatedDurationPill">2.0 {{ __('hrs') }}</span>
                </div>

                <div class="duration-projection-line">
                    <span>{{ __('Remaining balance after this request:') }}</span>
                    <strong id="projectedBalanceVal" style="color: #0284C7;">{{ max(0, $balance['remaining_hours'] - 2) }} {{ __('hrs') }}</strong>
                </div>

                <!-- Real-time Warning Box -->
                <div class="duration-alert-box" id="permValidationAlert">
                    <i class="bx bx-error-circle font-18 flex-shrink-0"></i>
                    <span id="permValidationAlertText"></span>
                </div>
            </div>
        </div>

        <!-- 4. Emergency Switch Toggle -->
        <div class="perm-section-card">
            <div class="perm-toggle-row">
                <div class="perm-toggle-text">
                    <h4 class="perm-toggle-title">{{ __('Emergency Request') }}</h4>
                    <p class="perm-toggle-sub">{{ __('Enable this option for force majeure situations where advance notice could not be provided.') }}</p>
                </div>
                <label class="switch-ios">
                    <input type="checkbox" name="is_emergency" id="isEmergencyCheckbox" value="1" onchange="handleEmergencyToggle()">
                    <span class="switch-ios-slider"></span>
                </label>
            </div>
        </div>

        <!-- 5. Reason & Notes Card -->
        <div class="perm-section-card">
            <h3 class="perm-section-title">
                <span>{{ __('Reason') }} <span class="text-danger">*</span></span>
                <i class="bx bx-edit-alt"></i>
            </h3>
            <textarea name="reason"
                      id="permissionReason"
                      class="perm-textarea"
                      placeholder="{{ __('State the reason for this permission request...') }}"
                      required></textarea>
        </div>

        <!-- 6. Supporting Attachment (Optional) -->
        <div class="perm-section-card">
            <h3 class="perm-section-title">
                <span>{{ __('Supporting Attachment (Optional)') }}</span>
                <i class="bx bx-paperclip"></i>
            </h3>

            <label class="perm-upload-dropzone" for="permissionAttachmentInput">
                <div class="upload-cloud-icon">
                    <i class="bx bx-cloud-upload"></i>
                </div>
                <div class="upload-title">{{ __('Click to upload medical report or document') }}</div>
                <div class="upload-sub" id="uploadFilenameText">{{ __('Allowed formats: PDF, PNG, JPG (up to 5MB)') }}</div>
                <input type="file"
                       name="attachment"
                       id="permissionAttachmentInput"
                       accept=".pdf,.png,.jpg,.jpeg"
                       style="display: none;"
                       onchange="handleAttachmentSelected(this)" />
            </label>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn-submit-perm-custom" id="btnSubmitPermission">
            <i class="bx bx-send"></i>
            <span>{{ __('Submit Permission Request') }}</span>
        </button>
    </form>

    <!-- 7. Recent Permissions History Section -->
    <div class="perm-history-card" id="recentPermissionsSection">
        <h3 class="perm-section-title mb-2">
            <span>{{ __('Recent Permissions History') }}</span>
            <i class="bx bx-history"></i>
        </h3>

        @if($recentPermissions->isNotEmpty())
            @foreach($recentPermissions as $perm)
                <div class="perm-history-item">
                    <div class="perm-history-left">
                        <div class="perm-history-icon {{ $perm->type === \Modules\HR\Enums\PermissionType::EMERGENCY ? 'icon-rose' : 'icon-blue' }}">
                            <i class="{{ $perm->type?->icon() ?? 'bx bx-time' }}"></i>
                        </div>
                        <div class="perm-history-info">
                            <span class="perm-history-title">{{ $perm->type?->label() ?? __('Permission') }}</span>
                            <span class="perm-history-meta">
                                {{ $perm->date?->format('Y-m-d') }} • {{ \Carbon\Carbon::createFromTimeString($perm->from_time)->format('h:i A') }} - {{ \Carbon\Carbon::createFromTimeString($perm->to_time)->format('h:i A') }} ({{ $perm->hours_count }} {{ __('hrs') }})
                            </span>
                        </div>
                    </div>
                    <div>
                        <span class="perm-history-badge {{ $perm->status?->badgeClass() ?? 'bg-secondary text-white' }}">
                            {{ $perm->status?->label() ?? '-' }}
                        </span>
                    </div>
                </div>
            @endforeach
        @else
            <div class="text-center py-3 text-muted" style="font-size: 11.5px;">
                <i class="bx bx-info-circle font-20 mb-1 d-block text-secondary"></i>
                <span>{{ __('No permission requests recorded for you this month.') }}</span>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ar.js"></script>

<script>
    let fpDateInstance = null;
    let fpFromTimeInstance = null;
    let fpToTimeInstance = null;

    document.addEventListener("DOMContentLoaded", function () {
        const isAr = "{{ app()->getLocale() }}" === "ar";

        // 1. Initialize Date Picker
        fpDateInstance = flatpickr("#permissionDate", {
            locale: isAr ? flatpickr.l10ns.ar : "default",
            dateFormat: "Y-m-d",
            altInput: false,
            defaultDate: "{{ date('Y-m-d') }}",
            onChange: function() {
                recalculatePermission();
            }
        });

        // 2. Initialize From Time Picker (24hr H:i for exact server validation)
        fpFromTimeInstance = flatpickr("#permissionFromTime", {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: true,
            defaultDate: "08:00",
            minuteIncrement: 15,
            onChange: function(selectedDates, dateStr) {
                // Ensure To Time is after From Time
                if (fpToTimeInstance && dateStr) {
                    const fromParts = dateStr.split(':').map(Number);
                    const toVal = document.getElementById('permissionToTime').value;
                    if (toVal) {
                        const toParts = toVal.split(':').map(Number);
                        const fromMinutes = fromParts[0] * 60 + fromParts[1];
                        const toMinutes = toParts[0] * 60 + toParts[1];
                        if (toMinutes <= fromMinutes) {
                            // Automatically adjust to 1 hour after
                            const newToMinutes = fromMinutes + 60;
                            const newH = String(Math.floor(newToMinutes / 60) % 24).padStart(2, '0');
                            const newM = String(newToMinutes % 60).padStart(2, '0');
                            fpToTimeInstance.setDate(`${newH}:${newM}`, false);
                        }
                    }
                }
                recalculatePermission();
            }
        });

        // 3. Initialize To Time Picker
        fpToTimeInstance = flatpickr("#permissionToTime", {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: true,
            defaultDate: "10:00",
            minuteIncrement: 15,
            onChange: function() {
                recalculatePermission();
            }
        });

        // 4. Initial calculation on page load
        recalculatePermission();

        // 5. Form submission protection
        const form = document.getElementById('permissionSubmitForm');
        if (form) {
            form.addEventListener('submit', function() {
                const btn = document.getElementById('btnSubmitPermission');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> {{ __('Submitting request...') }}`;
                }
            });
        }
    });

    /**
     * Select permission type card
     */
    function selectPermissionType(typeVal, cardEl) {
        document.querySelectorAll('.perm-type-radio-card').forEach(el => el.classList.remove('active'));
        if (cardEl) {
            cardEl.classList.add('active');
        }

        const inputHidden = document.getElementById('selectedPermissionType');
        if (inputHidden) {
            inputHidden.value = typeVal;
        }

        const emergencyCheckbox = document.getElementById('isEmergencyCheckbox');
        if (typeVal === 'emergency') {
            if (emergencyCheckbox) emergencyCheckbox.checked = true;
        } else {
            // Keep switch as is unless user turns it off
        }

        recalculatePermission();
    }

    /**
     * Quick date button setter (اليوم / غداً)
     */
    function setDateQuickly(dateStr) {
        if (fpDateInstance) {
            fpDateInstance.setDate(dateStr, true);
        }
    }

    /**
     * Toggle emergency switch handler
     */
    function handleEmergencyToggle() {
        const emergencyCheckbox = document.getElementById('isEmergencyCheckbox');
        const emergencyCard = document.querySelector('.perm-type-radio-card[data-type="emergency"]');

        if (emergencyCheckbox && emergencyCheckbox.checked) {
            // If emergency switch turned on and emergency type not selected, keep current or highlight
        }
        recalculatePermission();
    }

    /**
     * Recalculate duration & check balance via AJAX
     */
    function recalculatePermission() {
        const dateInput = document.getElementById('permissionDate');
        const fromTimeInput = document.getElementById('permissionFromTime');
        const toTimeInput = document.getElementById('permissionToTime');
        const typeInput = document.getElementById('selectedPermissionType');
        const emergencyCheckbox = document.getElementById('isEmergencyCheckbox');

        if (!fromTimeInput || !toTimeInput || !dateInput) return;

        const date = dateInput.value;
        const fromTime = fromTimeInput.value;
        const toTime = toTimeInput.value;
        const type = typeInput ? typeInput.value : 'during_shift';
        const isEmergency = emergencyCheckbox ? (emergencyCheckbox.checked ? 1 : 0) : 0;

        if (!fromTime || !toTime || !date) return;

        // Client-side quick calculation
        const fromParts = fromTime.split(':').map(Number);
        const toParts = toTime.split(':').map(Number);
        const fromMinutes = fromParts[0] * 60 + fromParts[1];
        const toMinutes = toParts[0] * 60 + toParts[1];

        const durationPill = document.getElementById('calculatedDurationPill');
        const alertBox = document.getElementById('permValidationAlert');
        const alertText = document.getElementById('permValidationAlertText');
        const projectedBalance = document.getElementById('projectedBalanceVal');

        if (toMinutes <= fromMinutes) {
            if (alertBox && alertText) {
                alertBox.classList.add('active');
                alertText.textContent = "{{ __('End time must be after start time.') }}";
            }
            if (durationPill) durationPill.textContent = "0 {{ __('hrs') }}";
            return;
        }

        const diffMinutes = toMinutes - fromMinutes;
        const diffHours = (diffMinutes / 60).toFixed(2);
        if (durationPill) {
            durationPill.textContent = `${diffHours} {{ __('hrs') }}`;
        }

        // Fetch live server calculation and policy verification
        fetch("{{ route('portal.permissions.calculate') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                date: date,
                from_time: fromTime,
                to_time: toTime,
                type: type,
                is_emergency: isEmergency
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (durationPill && data.duration_formatted) {
                    durationPill.textContent = `${data.duration_formatted} (${data.hours_count} {{ __('hrs') }})`;
                }
                if (projectedBalance && data.projected_remaining_hours !== undefined) {
                    projectedBalance.textContent = `${data.projected_remaining_hours} {{ __('hrs') }}`;
                }

                if (data.warning) {
                    if (alertBox && alertText) {
                        alertBox.classList.add('active');
                        alertText.textContent = data.warning;
                    }
                } else {
                    if (alertBox) {
                        alertBox.classList.remove('active');
                    }
                }
            } else if (data.message) {
                if (alertBox && alertText) {
                    alertBox.classList.add('active');
                    alertText.textContent = data.message;
                }
            }
        })
        .catch(err => {
            console.error('Permission calculation error:', err);
        });
    }

    /**
     * Display selected file name in upload dropzone
     */
    function handleAttachmentSelected(input) {
        const textEl = document.getElementById('uploadFilenameText');
        if (input.files && input.files[0] && textEl) {
            const fileName = input.files[0].name;
            const fileSize = (input.files[0].size / 1024 / 1024).toFixed(2);
            textEl.innerHTML = `<strong style="color: #0284C7;"><i class="bx bx-check-circle"></i> ${fileName} (${fileSize} MB)</strong>`;
        }
    }
</script>
@endpush
