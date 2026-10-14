@extends('employeeportal::layouts.master')

@section('title', __('Edit Personal Information'))

@push('styles')

<style>
    .edit-profile-container {
        padding-bottom: 28px;
    }

    /* Top Page Header with Back Button */
    .edit-profile-nav {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 14px;
        position: relative;
    }

    .edit-profile-back-btn {
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

    .edit-profile-back-btn:active {
        background: #EEF5F8;
        transform: scale(0.95);
    }

    .edit-profile-title {
        font-size: 17px;
        font-weight: 800;
        color: #00334B;
        margin: 0;
        text-align: center;
        flex: 1;
    }

    .edit-profile-nav-placeholder {
        width: 38px;
    }

    /* 1. Top Hero Card - Exactly matching Figma Spec */
    .edit-hero-card {
        width: 100%;
        min-height: 229px;
        margin: 0 0 16px 0;
        background: linear-gradient(135deg, #00334B 0%, #004B6B 50%, #00334B 100%);
        border-radius: 16px;
        padding: 24px 24px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: #ffffff;
        position: relative;
        box-shadow: 0 8px 24px rgba(0, 51, 75, 0.15);
        box-sizing: border-box;
    }

    .edit-avatar-wrapper {
        position: relative;
        width: 88px;
        height: 88px;
        margin-bottom: 12px;
    }

    .edit-avatar-img {
        width: 88px;
        height: 88px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #FDBB22;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25);
        display: block;
    }

    .edit-camera-btn {
        position: absolute;
        bottom: 0px;
        left: 0px;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: #FDBB22;
        color: #00334B;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        border: 2.5px solid #00334B;
        cursor: pointer;
        transition: transform 0.2s;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.25);
    }

    [dir="ltr"] .edit-camera-btn {
        left: auto;
        right: 0px;
    }

    .edit-camera-btn:hover {
        transform: scale(1.1);
    }

    .edit-hero-name-row {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        margin-bottom: 8px;
    }

    .edit-hero-name {
        font-size: 19px;
        font-weight: 800;
        color: #FFFFFF;
        margin: 0;
        letter-spacing: -0.2px;
    }

    .edit-hero-badge-check {
        color: #FDBB22;
        font-size: 18px;
    }

    .edit-hero-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.16);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        padding: 5px 14px;
        border-radius: 20px;
        font-size: 11.5px;
        color: #E2E8F0;
        font-weight: 600;
    }

    .edit-hero-pill .pill-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #FDBB22;
    }

    /* 2. Form Section Card */
    .edit-section-card {
        background: #ffffff;
        border-radius: 20px;
        padding: 20px 18px;
        margin-bottom: 16px;
        box-shadow: 0 3px 15px rgba(0, 0, 0, 0.03);
        border: 1px solid rgba(0, 51, 75, 0.05);
    }

    .edit-section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 18px;
        padding-bottom: 10px;
        border-bottom: 1px solid #F1F5F9;
    }

    .edit-section-title {
        font-size: 15px;
        font-weight: 800;
        color: #00334B;
        margin: 0;
    }

    .edit-section-tag {
        font-size: 11px;
        color: #94A3B8;
        font-weight: 600;
    }

    .portal-form-group {
        margin-bottom: 16px;
    }

    .portal-label-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 6px;
    }

    .portal-input-label {
        font-size: 12px;
        font-weight: 700;
        color: #00334B;
        margin: 0;
    }

    .portal-verified-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: #FFF7ED;
        color: #C2410C;
        border: 1px solid #FFEDD5;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 10.5px;
        font-weight: 700;
    }

    .portal-input-box {
        position: relative;
        display: flex;
        align-items: center;
    }

    .portal-input-box i {
        position: absolute;
        right: 14px;
        font-size: 18px;
        color: #94A3B8;
        pointer-events: none;
    }

    [dir="ltr"] .portal-input-box i {
        right: auto;
        left: 14px;
    }

    .portal-form-control {
        width: 100%;
        height: 48px;
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        border-radius: 12px;
        padding: 0 42px 0 14px;
        font-size: 13px;
        font-weight: 600;
        color: #1E293B;
        font-family: inherit;
        transition: all 0.2s ease;
        outline: none;
    }

    [dir="ltr"] .portal-form-control {
        padding: 0 14px 0 42px;
    }

    .portal-form-control:focus {
        background: #ffffff;
        border-color: #00334B;
        box-shadow: none;
    }

    .portal-form-control:disabled, .portal-form-control[readonly] {
        background: #F1F5F9;
        color: #64748B;
        cursor: not-allowed;
    }

    .portal-input-helper {
        font-size: 10.5px;
        color: #94A3B8;
        margin-top: 5px;
        line-height: 1.4;
    }

    .portal-row-2col {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .portal-select {
        appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='%2364748B' viewBox='0 0 24 24'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: left 12px center;
        background-size: 18px;
    }

    [dir="ltr"] .portal-select {
        background-position: right 12px center;
    }

    /* intl-tel-input Custom Overrides */
    .iti {
        width: 100%;
        position: relative;
        display: block;
    }

    .iti__country-list {
        z-index: 1060;
        font-family: inherit;
        font-size: 12px;
        border-radius: 12px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    }

    .iti--separate-dial-code .iti__selected-flag {
        background-color: #F1F5F9;
        border-radius: 12px 0 0 12px;
        padding: 0 10px 0 12px;
        border-right: 1px solid #E2E8F0;
    }

    .iti--separate-dial-code input[type="tel"] {
        padding-left: 118px !important;
        padding-right: 14px !important;
        direction: ltr !important;
        text-align: left !important;
    }

    /* 3. Bottom Action Buttons matching exact user dimensions */
    .edit-profile-actions {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-top: 20px;
    }

    /* زر الحفظ */
    .btn-save-custom {
        width: 100%;
        height: 56px;
        border-radius: 16px;
        background: #002A44;
        color: #FEAE2C;
        border: none;
        font-size: 15.5px;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        box-shadow: 0 4px 16px rgba(0, 42, 68, 0.25);
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .btn-save-custom:active {
        transform: scale(0.98);
        background: #001f33;
    }

    /* زر الإلغاء */
    .btn-cancel-custom {
        width: 100%;
        height: 56px;
        border-radius: 16px;
        background: #FEAE2C;
        color: #002A44;
        border: none;
        font-size: 15.5px;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        box-shadow: 0 4px 16px rgba(254, 174, 44, 0.25);
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .btn-cancel-custom:active {
        transform: scale(0.98);
        opacity: 0.92;
    }
</style>
@endpush

@section('content')
<div class="edit-profile-container">
    <!-- Top Navigation Header -->
    <div class="edit-profile-nav">
        <a href="{{ route('portal.profile') }}" class="edit-profile-back-btn" title="{{ __('Back') }}">
            <i class="bx {{ app()->getLocale() === 'ar' ? 'bx-right-arrow-alt' : 'bx-left-arrow-alt' }}"></i>
        </a>
        <h1 class="edit-profile-title">{{ __('Edit Personal Information') }}</h1>
        <div class="edit-profile-nav-placeholder"></div>
    </div>

    <form action="{{ route('portal.profile.update') }}" method="POST" enctype="multipart/form-data" id="editProfileForm">
        @csrf

        <!-- 1. Hero Card - Figma Custom Spec -->
        <div class="edit-hero-card">
            <div class="edit-avatar-wrapper">
                <img src="{{ $employee?->avatar_url ?? auth()->user()?->avatar_url ?? asset('assets/portal/male-avatar.png') }}"
                     alt="Avatar"
                     class="edit-avatar-img"
                     id="previewAvatarImg" />
                <button type="button" class="edit-camera-btn" title="{{ __('Change Photo') }}" onclick="document.getElementById('editAvatarInput').click()">
                    <i class="bx bxs-camera"></i>
                </button>
                <input type="file" name="avatar" id="editAvatarInput" accept="image/*" style="display: none;" onchange="previewSelectedAvatar(this)" />
            </div>

            <div class="edit-hero-name-row">
                <h2 class="edit-hero-name">{{ $employee?->name ?? auth()->user()?->name ?? 'Nourah Al-Kandari' }}</h2>
                <i class="bx bxs-badge-check edit-hero-badge-check"></i>
            </div>

            <div class="edit-hero-pill">
                <span>{{ __('Employee Code') }}: #{{ $employee?->employee_code ?? (auth()->id() ?? '1003') }}</span>
                <span class="pill-dot"></span>
                <span><i class="bx bx-map-pin"></i> {{ $employee?->primaryBranch?->name ?? __('Kaifan Branch') }}</span>
            </div>
        </div>

        <!-- 2. Personal & Basic Information Card -->
        <div class="edit-section-card">
            <div class="edit-section-header">
                <h3 class="edit-section-title">{{ __('Personal & Basic Information') }}</h3>
                <span class="edit-section-tag">{{ __('Required') }}</span>
            </div>

            <!-- Full Arabic Name -->
            <div class="portal-form-group">
                <div class="portal-label-row">
                    <label class="portal-input-label">{{ __('Full Name (Civil ID)') }}</label>
                </div>
                <div class="portal-input-box">
                    <input type="text" name="name_ar" class="portal-form-control" value="{{ $employee?->translate('ar')?->name ?? ($employee?->name ?? auth()->user()?->name) }}" placeholder="{{ __('Full Arabic Name') }}" />
                    <i class="bx bx-edit-alt"></i>
                </div>
            </div>

            <!-- Full English Name -->
            <div class="portal-form-group">
                <div class="portal-label-row">
                    <label class="portal-input-label">{{ __('Full English Name') }}</label>
                </div>
                <div class="portal-input-box">
                    <input type="text" name="name_en" class="portal-form-control" value="{{ $employee?->translate('en')?->name ?? '' }}" placeholder="Full English Name" dir="ltr" />
                    <i class="bx bx-user"></i>
                </div>
            </div>

            <!-- Civil ID (Readonly / Verified) -->
            <div class="portal-form-group">
                <div class="portal-label-row">
                    <label class="portal-input-label">{{ __('Kuwaiti Civil ID') }}</label>
                    <span class="portal-verified-badge"><i class="bx bxs-lock-alt"></i> {{ __('Verified Data') }}</span>
                </div>
                <div class="portal-input-box">
                    <input type="text" class="portal-form-control" value="{{ $employee?->national_id ?? '294081203948' }}" readonly />
                    <i class="bx bx-shield-quarter"></i>
                </div>
                <div class="portal-input-helper">
                    {{ __('To request changing Civil ID or correcting records, please submit an official request to HR.') }}
                </div>
            </div>

            <!-- Primary Branch Selection -->
            <div class="portal-form-group">
                <div class="portal-label-row">
                    <label class="portal-input-label">{{ __('Primary Branch') }}</label>
                </div>
                <div class="portal-input-box">
                    <select name="primary_branch_id" class="portal-form-control portal-select">
                        <option value="">{{ __('Select Primary Branch...') }}</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ ($employee?->primary_branch_id == $branch->id) ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Date of Birth & Nationality (2 Columns) -->
            <div class="portal-row-2col">
                <div class="portal-form-group">
                    <label class="portal-input-label mb-1">{{ __('Date of Birth') }}</label>
                    <div class="portal-input-box">
                        <input type="date" name="date_of_birth" class="portal-form-control" style="padding: 0 10px;" value="{{ $employee?->date_of_birth ? \Carbon\Carbon::parse($employee->date_of_birth)->format('Y-m-d') : '1994-08-15' }}" />
                    </div>
                </div>

                <div class="portal-form-group">
                    <label class="portal-input-label mb-1">{{ __('Nationality') }}</label>
                    <div class="portal-input-box">
                        <select name="nationality" class="portal-form-control portal-select">
                            <option value="Kuwaiti" {{ ($employee?->nationality == 'Kuwaiti' || $employee?->nationality == 'كويتية' || !$employee?->nationality) ? 'selected' : '' }}>{{ __('Kuwaiti') }}</option>
                            <option value="Saudi" {{ ($employee?->nationality == 'Saudi' || $employee?->nationality == 'سعودية') ? 'selected' : '' }}>{{ __('Saudi') }}</option>
                            <option value="Emirati" {{ ($employee?->nationality == 'Emirati' || $employee?->nationality == 'إماراتية') ? 'selected' : '' }}>{{ __('Emirati') }}</option>
                            <option value="Bahraini" {{ ($employee?->nationality == 'Bahraini' || $employee?->nationality == 'بحرينية') ? 'selected' : '' }}>{{ __('Bahraini') }}</option>
                            <option value="Qatari" {{ ($employee?->nationality == 'Qatari' || $employee?->nationality == 'قطرية') ? 'selected' : '' }}>{{ __('Qatari') }}</option>
                            <option value="Omani" {{ ($employee?->nationality == 'Omani' || $employee?->nationality == 'عمانية') ? 'selected' : '' }}>{{ __('Omani') }}</option>
                            <option value="Egyptian" {{ ($employee?->nationality == 'Egyptian' || $employee?->nationality == 'مصرية') ? 'selected' : '' }}>{{ __('Egyptian') }}</option>
                            <option value="Jordanian" {{ ($employee?->nationality == 'Jordanian' || $employee?->nationality == 'أردنية') ? 'selected' : '' }}>{{ __('Jordanian') }}</option>
                            <option value="Lebanese" {{ ($employee?->nationality == 'Lebanese' || $employee?->nationality == 'لبنانية') ? 'selected' : '' }}>{{ __('Lebanese') }}</option>
                            <option value="Other" {{ ($employee?->nationality == 'Other' || $employee?->nationality == 'أخرى') ? 'selected' : '' }}>{{ __('Other') }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Marital Status -->
            <div class="portal-form-group">
                <label class="portal-input-label mb-1">{{ __('Marital Status') }}</label>
                <div class="portal-input-box">
                    @php
                        $currentMarital = is_object($employee?->marital_status) ? ($employee->marital_status->value ?? '') : (string)($employee?->marital_status ?? 'married');
                    @endphp
                    <select name="marital_status" class="portal-form-control portal-select">
                        <option value="married" {{ $currentMarital === 'married' ? 'selected' : '' }}>{{ __('Married') }}</option>
                        <option value="single" {{ $currentMarital === 'single' ? 'selected' : '' }}>{{ __('Single') }}</option>
                        <option value="divorced" {{ $currentMarital === 'divorced' ? 'selected' : '' }}>{{ __('Divorced') }}</option>
                        <option value="widowed" {{ $currentMarital === 'widowed' ? 'selected' : '' }}>{{ __('Widowed') }}</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- 3. Contact Details Card with intl-tel-input -->
        <div class="edit-section-card">
            <div class="edit-section-header">
                <h3 class="edit-section-title">{{ __('Contact & Emergency Details') }}</h3>
            </div>

            <!-- Phone with International Country Picker -->
            <div class="portal-form-group">
                <div class="portal-label-row">
                    <label class="portal-input-label">{{ __('Primary Phone Number') }}</label>
                </div>
                <div>
                    @php
                        $rawPortalPhone = $employee?->phone ?? '99112233';
                        $cleanedPortalPhone = preg_replace('/^\+965/', '', $rawPortalPhone);
                    @endphp
                    <input type="tel" id="portalPhoneInput" name="phone" class="portal-form-control" value="{{ $cleanedPortalPhone }}" dir="ltr" />
                </div>
            </div>

            <!-- Email -->
            <div class="portal-form-group">
                <div class="portal-label-row">
                    <label class="portal-input-label">{{ __('Email Address') }}</label>
                </div>
                <div class="portal-input-box">
                    <input type="email" name="email" class="portal-form-control" value="{{ $employee?->email ?? (auth()->user()?->email ?? 'nour@astroleaders.com') }}" placeholder="name@example.com" dir="ltr" />
                    <i class="bx bx-envelope"></i>
                </div>
            </div>

            <!-- Emergency Contact -->
            <div class="portal-row-2col">
                <div class="portal-form-group">
                    <label class="portal-input-label mb-1">{{ __('Emergency Contact Name') }}</label>
                    <input type="text" name="emergency_contact_name" class="portal-form-control" style="padding: 0 12px;" value="{{ $employee?->emergency_contact_name ?? '' }}" placeholder="{{ __('Name') }}" />
                </div>
                <div class="portal-form-group">
                    <label class="portal-input-label mb-1">{{ __('Emergency Contact Phone') }}</label>
                    <input type="tel" name="emergency_contact_phone" class="portal-form-control" style="padding: 0 12px;" value="{{ $employee?->emergency_contact_phone ?? '' }}" placeholder="+965XXXXXXXX" dir="ltr" />
                </div>
            </div>
        </div>

        <!-- 4. Password Change Card -->
        <div class="edit-section-card">
            <div class="edit-section-header">
                <h3 class="edit-section-title">{{ __('Change Password') }}</h3>
                <span class="edit-section-tag">{{ __('Optional') }}</span>
            </div>

            <div class="portal-form-group">
                <div class="portal-label-row">
                    <label class="portal-input-label">{{ __('New Password') }}</label>
                </div>
                <div class="portal-input-box">
                    <input type="password" name="password" class="portal-form-control" placeholder="••••••••" autocomplete="new-password" />
                    <i class="bx bx-lock-alt"></i>
                </div>
                <div class="portal-input-helper">
                    {{ __('Leave blank if you do not wish to change your password.') }}
                </div>
            </div>

            <div class="portal-form-group mb-0">
                <div class="portal-label-row">
                    <label class="portal-input-label">{{ __('Confirm New Password') }}</label>
                </div>
                <div class="portal-input-box">
                    <input type="password" name="password_confirmation" class="portal-form-control" placeholder="••••••••" autocomplete="new-password" />
                    <i class="bx bx-check-shield"></i>
                </div>
            </div>
        </div>

        <!-- 5. Exact Custom Action Buttons -->
        <div class="edit-profile-actions">
            <!-- Save Button -->
            <button type="submit" class="btn-save-custom">
                <span>{{ __('Save Changes') }}</span>
            </button>

            <!-- Cancel Button -->
            <a href="{{ route('portal.profile') }}" class="btn-cancel-custom">
                <span>{{ __('Cancel') }}</span>
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Initialize intl-tel-input for Phone
    document.addEventListener("DOMContentLoaded", function () {
        const phoneInputField = document.querySelector("#portalPhoneInput");
        if (phoneInputField) {
            window.intlTelInput(phoneInputField, {
                initialCountry: "kw",
                preferredCountries: ["kw", "sa", "ae", "qa", "bh", "om", "eg"],
                utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js",
                separateDialCode: true
            });
        }
    });

    function previewSelectedAvatar(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('previewAvatarImg');
                if (preview) {
                    preview.src = e.target.result;
                }
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
@endsection
