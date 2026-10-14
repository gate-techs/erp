@extends('employeeportal::layouts.master')

@section('title', __('Profile'))

@section('content')
<div class="portal-profile-container">
    <!-- 0. Top Navigation Header -->
    <div class="portal-profile-header">
        <a href="{{ route('portal.dashboard', [], false) }}" class="portal-profile-back-btn" aria-label="{{ __('Back to Home') }}">
            <i class="bx {{ app()->getLocale() === 'ar' ? 'bx-chevron-right' : 'bx-chevron-left' }}"></i>
        </a>
        <h1 class="portal-profile-header-title">{{ __('Profile') }}</h1>
        <div class="portal-profile-header-spacer"></div>
    </div>

    <!-- 1. Hero Profile Card -->
    <div class="portal-profile-hero-card">
        <div class="portal-profile-hero-top">
            <div class="portal-profile-hero-avatar-box">
                <img src="{{ $avatar_url }}" alt="{{ $name }}" class="portal-profile-hero-avatar" />
                <a href="{{ route('portal.profile.edit') }}" class="portal-profile-hero-camera-badge" aria-label="{{ __('Change Photo') }}">
                    <i class="bx bxs-camera"></i>
                </a>
            </div>

            <div class="portal-profile-hero-info">
                <h2 class="portal-profile-hero-name">{{ $name }}</h2>
                <p class="portal-profile-hero-role">{{ $role_summary }}</p>
                <p class="portal-profile-hero-branch">{{ $branch_name }}</p>
            </div>
        </div>

        <a href="{{ route('portal.profile.edit') }}" class="portal-profile-hero-edit-btn">
            <i class="bx bx-edit"></i>
            <span>{{ __('Edit Personal & Contact Information') }}</span>
        </a>
    </div>

    <!-- 2. Language Switch Strip (تغيير لغة التطبيق) -->
    <a href="{{ $lang_switch_url }}" class="portal-profile-lang-strip" title="{{ __('Change Language') }}" onclick="savePortalLocaleAndRedirect(event, '{{ $target_locale }}', this.href)">
        <div class="portal-profile-lang-right">
            <div class="portal-profile-lang-icon">
                <i class="bx bx-globe"></i>
            </div>
            <div>
                <h3 class="portal-profile-lang-title">{{ __('App Language') }}</h3>
                <p class="portal-profile-lang-desc">{{ $lang_subtitle }}</p>
            </div>
        </div>
        <div class="portal-profile-lang-pill">
            <i class="bx bx-transfer-alt"></i>
            <span>{{ $target_locale_label }}</span>
        </div>
    </a>

    <!-- 3. Section: Profile & Job Details (الملف والبيانات الوظيفية) -->
    <h3 class="portal-profile-section-title">{{ __('Profile & Job Details') }}</h3>
    <div class="portal-profile-group-card">
        <!-- 3.1 بياناتي الوظيفية والتعاقدية -->
        <a href="{{ route('portal.profile.edit') }}" class="portal-profile-item">
            <div class="portal-profile-item-right">
                <div class="portal-profile-item-icon icon-blue">
                    <i class="bx bx-id-card"></i>
                </div>
                <div>
                    <h4 class="portal-profile-item-title">{{ __('My Job & Contractual Details') }}</h4>
                    <p class="portal-profile-item-subtitle">{{ $job_subtitle }}</p>
                </div>
            </div>
            <i class="bx {{ app()->getLocale() === 'ar' ? 'bx-chevron-left' : 'bx-chevron-right' }} portal-profile-item-chevron"></i>
        </a>

        <!-- 3.2 تفاصيل الراتب والبدلات -->
        <a href="#" class="portal-profile-item" onclick="showPortalToast('success', '{{ __('Salary Details & Allowances') }}', '{{ __('Detailed payslip, bank account and transfers') }}'); return false;">
            <div class="portal-profile-item-right">
                <div class="portal-profile-item-icon icon-green">
                    <i class="bx bx-credit-card"></i>
                </div>
                <div>
                    <h4 class="portal-profile-item-title">{{ __('Salary Details & Allowances') }}</h4>
                    <p class="portal-profile-item-subtitle">{{ __('Detailed payslip, bank account and transfers') }}</p>
                </div>
            </div>
            <i class="bx {{ app()->getLocale() === 'ar' ? 'bx-chevron-left' : 'bx-chevron-right' }} portal-profile-item-chevron"></i>
        </a>

        <!-- 3.3 السلف والمستحقات المالية -->
        <a href="#" class="portal-profile-item" onclick="showPortalToast('success', '{{ __('Advances & Financial Entitlements') }}', '{{ __('Request new advance, estimated end of service gratuity') }}'); return false;">
            <div class="portal-profile-item-right">
                <div class="portal-profile-item-icon icon-orange">
                    <i class="bx bx-coin-stack"></i>
                </div>
                <div>
                    <h4 class="portal-profile-item-title">{{ __('Advances & Financial Entitlements') }}</h4>
                    <p class="portal-profile-item-subtitle">{{ __('Request new advance, estimated end of service gratuity') }}</p>
                </div>
            </div>
            <i class="bx {{ app()->getLocale() === 'ar' ? 'bx-chevron-left' : 'bx-chevron-right' }} portal-profile-item-chevron"></i>
        </a>
    </div>

    <!-- 4. Section: Documents, Certificates & Training (المستندات والشهادات والتدريب) -->
    <h3 class="portal-profile-section-title">{{ __('Documents, Certificates & Training') }}</h3>
    <div class="portal-profile-group-card">
        <!-- 4.1 أرشيف المستندات والوثائق الرسمية -->
        <a href="{{ route('portal.documents.index') }}" class="portal-profile-item">
            <div class="portal-profile-item-right">
                <div class="portal-profile-item-icon icon-indigo">
                    <i class="bx bx-folder"></i>
                </div>
                <div>
                    <h4 class="portal-profile-item-title">{{ __('Archive of Official Documents & Records') }}</h4>
                    <p class="portal-profile-item-subtitle">{{ __('Civil ID, work contract, university degree') }}</p>
                </div>
            </div>
            <i class="bx {{ app()->getLocale() === 'ar' ? 'bx-chevron-left' : 'bx-chevron-right' }} portal-profile-item-chevron"></i>
        </a>

        <!-- 4.2 سجل الدورات والتدريب المهني -->
        <a href="#" class="portal-profile-item" onclick="showPortalToast('success', '{{ __('Courses & Professional Training Log') }}', '{{ __('Early childhood care workshops and educational development') }}'); return false;">
            <div class="portal-profile-item-right">
                <div class="portal-profile-item-icon icon-purple">
                    <i class="bx bxs-graduation"></i>
                </div>
                <div>
                    <h4 class="portal-profile-item-title">{{ __('Courses & Professional Training Log') }}</h4>
                    <p class="portal-profile-item-subtitle">{{ __('Early childhood care workshops and educational development') }}</p>
                </div>
            </div>
            <i class="bx {{ app()->getLocale() === 'ar' ? 'bx-chevron-left' : 'bx-chevron-right' }} portal-profile-item-chevron"></i>
        </a>

        <!-- 4.3 سجل المكافآت والحوافز التقديرية -->
        <a href="#" class="portal-profile-item" onclick="showPortalToast('success', '{{ __('Awards & Honorary Incentives Log') }}', '{{ __('Distinguished teacher certificates, term achievement bonuses') }}'); return false;">
            <div class="portal-profile-item-right">
                <div class="portal-profile-item-icon icon-gold">
                    <i class="bx bx-award"></i>
                </div>
                <div>
                    <h4 class="portal-profile-item-title">{{ __('Awards & Honorary Incentives Log') }}</h4>
                    <p class="portal-profile-item-subtitle">{{ __('Distinguished teacher certificates, term achievement bonuses') }}</p>
                </div>
            </div>
            <i class="bx {{ app()->getLocale() === 'ar' ? 'bx-chevron-left' : 'bx-chevron-right' }} portal-profile-item-chevron"></i>
        </a>
    </div>

    <!-- 5. Section: Policies & Account Settings (السياسات وإعدادات الحساب) -->
    <h3 class="portal-profile-section-title">{{ __('Policies & Account Settings') }}</h3>
    <div class="portal-profile-group-card">
        <!-- 5.1 سياسات ولوائح الحضانة -->
        <a href="#" class="portal-profile-item" onclick="showPortalToast('success', '{{ __('Nursery Policies & Regulations') }}', '{{ __('Leave regulations, permission rules and work conduct') }}'); return false;">
            <div class="portal-profile-item-right">
                <div class="portal-profile-item-icon icon-teal">
                    <i class="bx bx-book-bookmark"></i>
                </div>
                <div>
                    <h4 class="portal-profile-item-title">{{ __('Nursery Policies & Regulations') }}</h4>
                    <p class="portal-profile-item-subtitle">{{ __('Leave regulations, permission rules and work conduct') }}</p>
                </div>
            </div>
            <i class="bx {{ app()->getLocale() === 'ar' ? 'bx-chevron-left' : 'bx-chevron-right' }} portal-profile-item-chevron"></i>
        </a>

        <!-- 5.2 إعدادات الإشعارات والتنبيهات -->
        <a href="#" class="portal-profile-item" onclick="showPortalToast('success', '{{ __('Notification & Alert Settings') }}', '{{ __('Punch reminder, management decisions and payroll alerts') }}'); return false;">
            <div class="portal-profile-item-right">
                <div class="portal-profile-item-icon icon-sky">
                    <i class="bx bx-bell"></i>
                </div>
                <div>
                    <h4 class="portal-profile-item-title">{{ __('Notification & Alert Settings') }}</h4>
                    <p class="portal-profile-item-subtitle">{{ __('Punch reminder, management decisions and payroll alerts') }}</p>
                </div>
            </div>
            <i class="bx {{ app()->getLocale() === 'ar' ? 'bx-chevron-left' : 'bx-chevron-right' }} portal-profile-item-chevron"></i>
        </a>

        <!-- 5.3 الأمان وبصمة الوجه (Face ID) -->
        <a href="#" class="portal-profile-item" onclick="showPortalToast('success', '{{ __('Security & Face ID') }}', '{{ __('Change password and enable biometric login') }}'); return false;">
            <div class="portal-profile-item-right">
                <div class="portal-profile-item-icon icon-red">
                    <i class="bx bx-shield-quarter"></i>
                </div>
                <div>
                    <h4 class="portal-profile-item-title">{{ __('Security & Face ID') }}</h4>
                    <p class="portal-profile-item-subtitle">{{ __('Change password and enable biometric login') }}</p>
                </div>
            </div>
            <i class="bx {{ app()->getLocale() === 'ar' ? 'bx-chevron-left' : 'bx-chevron-right' }} portal-profile-item-chevron"></i>
        </a>

        <!-- 5.4 مركز المساعدة والدعم الفني -->
        <a href="#" class="portal-profile-item" onclick="showPortalToast('success', '{{ __('Help & Technical Support Center') }}', '{{ __('Contact HR and support desk') }}'); return false;">
            <div class="portal-profile-item-right">
                <div class="portal-profile-item-icon icon-slate">
                    <i class="bx bx-headphone"></i>
                </div>
                <div>
                    <h4 class="portal-profile-item-title">{{ __('Help & Technical Support Center') }}</h4>
                    <p class="portal-profile-item-subtitle">{{ __('Contact HR and support desk') }}</p>
                </div>
            </div>
            <i class="bx {{ app()->getLocale() === 'ar' ? 'bx-chevron-left' : 'bx-chevron-right' }} portal-profile-item-chevron"></i>
        </a>
    </div>

    <!-- 6. Logout Action Button -->
    <form action="{{ route('portal.logout', [], false) }}" method="POST" id="portalProfileLogoutForm" class="mt-4 mb-2">
        @csrf
        <button type="submit" class="portal-profile-logout-btn" onclick="return confirm('{{ __('Are you sure you want to log out from the portal?') }}')">
            <i class="bx bx-log-out"></i>
            <span>{{ __('Log Out from Account') }}</span>
        </button>
    </form>
</div>
@endsection
