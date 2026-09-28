@extends('employeeportal::layouts.master')

@section('title', 'الملف الشخصي')

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

    /* Hero Profile Card */
    .profile-hero-card {
        background: linear-gradient(135deg, #00334B 0%, #004B6B 100%);
        border-radius: 24px;
        padding: 24px 20px;
        color: #ffffff;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0, 51, 75, 0.22);
        margin-bottom: 18px;
        position: relative;
        overflow: hidden;
    }

    .profile-hero-card::after {
        content: '';
        position: absolute;
        top: -40px;
        left: -40px;
        width: 140px;
        height: 140px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(253, 187, 34, 0.15) 0%, transparent 70%);
        pointer-events: none;
    }

    .profile-avatar-wrapper {
        position: relative;
        width: 86px;
        height: 86px;
        margin: 0 auto 12px auto;
    }

    .profile-avatar-img {
        width: 86px;
        height: 86px;
        border-radius: 50%;
        object-fit: cover;
        border: 3.5px solid #ffffff;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.25);
    }

    .profile-online-badge {
        position: absolute;
        bottom: 2px;
        left: 4px;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: #10B981;
        border: 2.5px solid #00334B;
    }

    .profile-name {
        font-size: 19px;
        font-weight: 800;
        margin: 0 0 4px 0;
        letter-spacing: -0.2px;
    }

    .profile-job-title {
        font-size: 13px;
        color: #E2E8F0;
        font-weight: 600;
        margin-bottom: 12px;
    }

    .profile-badges-row {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .profile-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.18);
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 700;
        color: #ffffff;
    }

    .profile-pill-gold {
        background: rgba(253, 187, 34, 0.2);
        border-color: rgba(253, 187, 34, 0.4);
        color: #FDBB22;
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
        direction: rtl;
        text-align: right;
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
        <a href="{{ route('portal.dashboard', [], false) }}" class="profile-back-btn" title="الرجوع للرئيسية">
            <i class="bx bx-right-arrow-alt"></i>
        </a>
        <h1 class="profile-header-title">الملف الشخصي</h1>
        <div class="profile-header-action">
            <i class="bx bx-badge-check" style="color: #FDBB22;"></i>
        </div>
    </div>

    <!-- 2. Hero Profile Card -->
    <div class="profile-hero-card">
        <div class="profile-avatar-wrapper">
            <img src="{{ $employee?->avatar_url ?? auth()->user()?->avatar_url ?? asset('assets/portal/teacher-avatar.png') }}" alt="Avatar" class="profile-avatar-img" />
            <span class="profile-online-badge"></span>
        </div>
        <h2 class="profile-name">{{ $employee?->name ?? auth()->user()?->name ?? 'أ. نورة المطيري' }}</h2>
        <div class="profile-job-title">{{ $employee?->jobTitle?->name ?? 'معلمة رياض أطفال - فصل النجوم' }}</div>

        <div class="profile-badges-row">
            <span class="profile-pill profile-pill-gold">
                <i class="bx bx-id-card"></i>
                <span>{{ $employee?->employee_code ?? 'EMP-' . (auth()->id() ?? '101') }}</span>
            </span>
            <span class="profile-pill">
                <i class="bx bx-building-house"></i>
                <span>أسترو ليدرز</span>
            </span>
        </div>
    </div>

    <!-- 3. Personal & Contact Information -->
    <div class="profile-section-card">
        <div class="profile-section-header">
            <i class="bx bx-user-pin"></i>
            <h3 class="profile-section-title">البيانات الوظيفية والتواصل</h3>
        </div>

        <div class="info-list-item">
            <span class="info-label">
                <i class="bx bx-envelope"></i>
                <span>البريد الإلكتروني</span>
            </span>
            <span class="info-value">{{ auth()->user()?->email ?? $employee?->email ?? 'teacher@astroleaders.com' }}</span>
        </div>

        <div class="info-list-item">
            <span class="info-label">
                <i class="bx bx-phone"></i>
                <span>رقم الهاتف</span>
            </span>
            <span class="info-value">{{ $employee?->phone ?? '965-98765432' }}</span>
        </div>

        <div class="info-list-item">
            <span class="info-label">
                <i class="bx bx-calendar"></i>
                <span>تاريخ التعيين</span>
            </span>
            <span class="info-value info-value-rtl">{{ $employee?->joining_date ?? '01-09-2024' }}</span>
        </div>
    </div>

    <!-- 4. Work & Shift Schedule Information -->
    <div class="profile-section-card">
        <div class="profile-section-header">
            <i class="bx bx-time-five"></i>
            <h3 class="profile-section-title">جدول الدوام الرسمي</h3>
        </div>

        <div class="info-list-item">
            <span class="info-label">
                <i class="bx bx-sun"></i>
                <span>الوردية الحالية</span>
            </span>
            <span class="info-value info-value-rtl">{{ $employee?->shift?->name ?? 'الدوام الصباحي المدرسي' }}</span>
        </div>

        <div class="info-list-item">
            <span class="info-label">
                <i class="bx bx-stopwatch"></i>
                <span>فترة السماح</span>
            </span>
            <span class="info-value info-value-rtl">15 دقيقة (حتى 07:45 ص)</span>
        </div>

        <div class="info-list-item">
            <span class="info-label">
                <i class="bx bx-map-pin"></i>
                <span>نطاق البصمة الجغرافي</span>
            </span>
            <span class="info-value info-value-rtl" style="color: #10B981;">
                <i class="bx bx-check-shield"></i> مفعّل (GPS)
            </span>
        </div>
    </div>

    <!-- 5. Logout Action Button (زر تسجيل الخروج كما طلب المستخدم) -->
    <div class="logout-section-card">
        <form action="{{ route('portal.logout', [], false) }}" method="POST" id="portalLogoutForm">
            @csrf
            <button type="submit" class="portal-logout-btn" onclick="return confirm('هل ترغب في تسجيل الخروج من بوابة الدوام؟')">
                <i class="bx bx-log-out"></i>
                <span>تسجيل الخروج من البوابة</span>
            </button>
        </form>
    </div>

    <!-- PWA App Version -->
    <div class="app-version-info">
        حضانة أسترو ليدرز • إصدار التطبيق v1.2 PWA
    </div>
</div>
@endsection
