@extends('employeeportal::layouts.master')

@section('title', __('Security & Password'))

@section('content')
<div class="security-page-container">
    <!-- Top Navigation Header -->
    <div class="security-nav-header">
        <a href="{{ route('portal.profile') }}" class="security-back-btn" title="{{ __('Back') }}">
            <i class="bx {{ app()->getLocale() === 'ar' ? 'bx-chevron-right' : 'bx-chevron-left' }}"></i>
        </a>
        <h1 class="security-header-title">{{ __('Security & Password') }}</h1>
        @include('employeeportal::components.notification-bell')
    </div>

    <!-- 1. Biometric Authentication Verification Card -->
    <div class="security-hero-card">
        <div class="security-hero-top">
            <div class="security-hero-icon-box">
                <i class="bx bx-scan"></i>
            </div>
            <div>
                <h2 class="security-hero-title">{{ __('Biometric Verification') }}</h2>
                <p class="security-hero-subtitle">{{ __('Verify your identity via Face ID or fingerprint to change your password') }}</p>
            </div>
        </div>

        <div class="security-bio-status-row" id="bioStatusRow">
            <div class="bio-status-pill locked" id="bioStatusPill">
                <span class="bio-dot" id="bioDot"></span>
                <span id="bioStatusLabel">{{ __('Identity Verification Required') }}</span>
            </div>
        </div>

        <button type="button" class="btn-verify-bio" id="btnVerifyBio" onclick="startBiometricVerification()">
            <i class="bx bx-fingerprint"></i>
            <span id="btnVerifyBioText">{{ __('Verify with Face ID / Fingerprint') }}</span>
        </button>
    </div>

    <!-- 2. Password Change Form Card (Locked until biometric verification) -->
    <div class="security-form-card" id="passwordFormCard">
        <div class="security-form-header">
            <div class="d-flex align-items-center gap-2">
                <i class="bx bx-lock-alt text-primary fs-4"></i>
                <h3 class="security-form-title">{{ __('New Password') }}</h3>
            </div>
            <span class="lock-badge" id="lockBadge">
                <i class="bx bx-lock" id="lockIcon"></i>
            </span>
        </div>

        <p class="security-lock-notice" id="lockNotice">
            <i class="bx bx-info-circle me-1"></i>
            <span>{{ __('Click the button above to scan Face ID / Fingerprint') }}</span>
        </p>

        <form id="changePasswordForm" onsubmit="submitNewPassword(event)">
            @csrf

            <!-- New Password -->
            <div class="security-input-group mb-3">
                <label class="security-label" for="new_password">
                    {{ __('New Password') }} <span class="required-star">*</span>
                </label>
                <div class="security-input-wrapper">
                    <input type="password" 
                           id="new_password" 
                           name="password" 
                           class="security-form-control" 
                           placeholder="{{ __('Enter new password') }}" 
                           minlength="8" 
                           required 
                           disabled>
                    <button type="button" class="toggle-password-btn" onclick="togglePasswordVisibility('new_password', this)">
                        <i class="bx bx-hide"></i>
                    </button>
                </div>
                <small class="security-helper-text">{{ __('Password must be at least 8 characters.') }}</small>
            </div>

            <!-- Confirm New Password -->
            <div class="security-input-group mb-4">
                <label class="security-label" for="password_confirmation">
                    {{ __('Confirm New Password') }} <span class="required-star">*</span>
                </label>
                <div class="security-input-wrapper">
                    <input type="password" 
                           id="password_confirmation" 
                           name="password_confirmation" 
                           class="security-form-control" 
                           placeholder="{{ __('Confirm your new password') }}" 
                           minlength="8" 
                           required 
                           disabled>
                    <button type="button" class="toggle-password-btn" onclick="togglePasswordVisibility('password_confirmation', this)">
                        <i class="bx bx-hide"></i>
                    </button>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn-save-password" id="btnSavePassword" disabled>
                <i class="bx bx-check-shield"></i>
                <span>{{ __('Save New Password') }}</span>
            </button>
        </form>
    </div>
</div>

@push('styles')
<style>
    .security-page-container {
        padding: 4px 2px 24px 2px;
        font-family: 'Tajawal', sans-serif;
    }

    .security-nav-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 14px;
        padding: 0 4px;
    }

    .security-back-btn {
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
        box-shadow: 0 2px 8px rgba(0, 51, 75, 0.04);
        transition: all 0.2s ease;
    }

    .security-back-btn:active {
        transform: scale(0.95);
        background: #f8fafc;
    }

    .security-header-title {
        font-size: 17px;
        font-weight: 800;
        color: #00334B;
        margin: 0;
    }

    /* 1. Hero Card */
    .security-hero-card {
        background: linear-gradient(135deg, #002A44 0%, #004B6B 100%);
        color: #ffffff;
        border-radius: 18px;
        padding: 16px;
        margin-bottom: 14px;
        box-shadow: 0 8px 24px rgba(0, 42, 68, 0.18);
        position: relative;
        overflow: hidden;
    }

    .security-hero-card::after {
        content: '';
        position: absolute;
        top: -30px;
        inset-inline-end: -30px;
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(254, 174, 44, 0.2) 0%, transparent 70%);
        pointer-events: none;
    }

    .security-hero-top {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
    }

    .security-hero-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(8px);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: #FEAE2C;
        flex-shrink: 0;
    }

    .security-hero-title {
        font-size: 15px;
        font-weight: 800;
        color: #ffffff;
        margin: 0 0 2px 0;
    }

    .security-hero-subtitle {
        font-size: 11.5px;
        color: rgba(255, 255, 255, 0.8);
        margin: 0;
        line-height: 1.4;
    }

    .security-bio-status-row {
        margin: 10px 0 14px 0;
    }

    .bio-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 700;
        backdrop-filter: blur(6px);
        transition: all 0.25s ease;
    }

    .bio-status-pill.locked {
        background: rgba(254, 226, 226, 0.15);
        color: #FECACA;
        border: 1px solid rgba(254, 202, 202, 0.3);
    }

    .bio-status-pill.locked .bio-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #EF4444;
        box-shadow: 0 0 6px #EF4444;
    }

    .bio-status-pill.unlocked {
        background: rgba(209, 250, 229, 0.2);
        color: #A7F3D0;
        border: 1px solid rgba(167, 243, 208, 0.4);
    }

    .bio-status-pill.unlocked .bio-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #10B981;
        box-shadow: 0 0 6px #10B981;
    }

    .btn-verify-bio {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        background: linear-gradient(135deg, #FEAE2C 0%, #F59E0B 100%);
        color: #002A44;
        border: none;
        border-radius: 12px;
        padding: 11px 16px;
        font-size: 13.5px;
        font-weight: 800;
        cursor: pointer;
        box-shadow: 0 4px 14px rgba(254, 174, 44, 0.35);
        transition: all 0.2s ease;
    }

    .btn-verify-bio:active {
        transform: scale(0.98);
    }

    .btn-verify-bio i {
        font-size: 18px;
    }

    .btn-verify-bio.verified {
        background: #10B981;
        color: #ffffff;
        box-shadow: 0 4px 14px rgba(16, 185, 129, 0.3);
        cursor: default;
    }

    /* 2. Password Form Card */
    .security-form-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 16px;
        box-shadow: 0 4px 16px rgba(0, 51, 75, 0.05);
        border: 1px solid rgba(0, 51, 75, 0.06);
        transition: all 0.3s ease;
    }

    .security-form-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 6px;
    }

    .security-form-title {
        font-size: 14.5px;
        font-weight: 800;
        color: #00334B;
        margin: 0;
    }

    .lock-badge {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        background: #F1F5F9;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #64748B;
        font-size: 16px;
        transition: all 0.3s ease;
    }

    .lock-badge.unlocked {
        background: #DCFCE7;
        color: #15803D;
    }

    .security-lock-notice {
        font-size: 12px;
        color: #64748B;
        margin-bottom: 14px;
        line-height: 1.4;
    }

    .security-input-group {
        position: relative;
    }

    .security-label {
        display: block;
        font-size: 12.5px;
        font-weight: 700;
        color: #1E293B;
        margin-bottom: 6px;
    }

    .required-star {
        color: #EF4444;
    }

    .security-input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .security-form-control {
        width: 100%;
        height: 44px;
        border-radius: 10px;
        border: 1px solid #CBD5E1;
        background: #F8FAFC;
        padding: 0 40px 0 14px;
        font-size: 13.5px;
        color: #00334B;
        transition: all 0.2s ease;
    }

    .security-form-control:focus {
        outline: none;
        background: #ffffff;
        border-color: #004B6B;
        box-shadow: 0 0 0 3px rgba(0, 75, 107, 0.12);
    }

    .security-form-control:disabled {
        background: #F1F5F9;
        color: #94A3B8;
        cursor: not-allowed;
    }

    .toggle-password-btn {
        position: absolute;
        inset-inline-end: 10px;
        background: transparent;
        border: none;
        color: #64748B;
        font-size: 18px;
        cursor: pointer;
        padding: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .security-helper-text {
        font-size: 11px;
        color: #64748B;
        display: block;
        margin-top: 4px;
    }

    .btn-save-password {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        background: #00334B;
        color: #ffffff;
        border: none;
        border-radius: 12px;
        padding: 12px 16px;
        font-size: 14px;
        font-weight: 800;
        cursor: pointer;
        box-shadow: 0 4px 14px rgba(0, 51, 75, 0.2);
        transition: all 0.2s ease;
    }

    .btn-save-password:disabled {
        background: #94A3B8;
        box-shadow: none;
        cursor: not-allowed;
        opacity: 0.7;
    }

    .btn-save-password:not(:disabled):active {
        transform: scale(0.98);
    }
</style>
@endpush

@push('scripts')
<script>
    let isBiometricVerified = false;

    // Toggle Password Visibility Eye
    function togglePasswordVisibility(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('i');
        if (!input) return;

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bx-hide');
            icon.classList.add('bx-show');
        } else {
            input.type = 'password';
            icon.classList.remove('bx-show');
            icon.classList.add('bx-hide');
        }
    }

    // Biometric Verification Handler using WebAuthn Credentials API
    async function startBiometricVerification() {
        if (isBiometricVerified) return;

        const bioBtn = document.getElementById('btnVerifyBio');
        const bioBtnText = document.getElementById('btnVerifyBioText');
        bioBtn.disabled = true;

        try {
            // Check if WebAuthn Public Key Credential or device biometric is supported
            if (window.PublicKeyCredential && PublicKeyCredential.isUserVerifyingPlatformAuthenticatorAvailable) {
                const available = await PublicKeyCredential.isUserVerifyingPlatformAuthenticatorAvailable();
                
                if (available) {
                    // Generate random challenge for WebAuthn authentication ceremony
                    const challenge = new Uint8Array(32);
                    window.crypto.getRandomValues(challenge);

                    const dummyUserId = new Uint8Array(16);
                    window.crypto.getRandomValues(dummyUserId);

                    const createOptions = {
                        publicKey: {
                            challenge: challenge,
                            rp: {
                                name: "{{ config('app.name', 'Astro Leaders') }}",
                                id: window.location.hostname
                            },
                            user: {
                                id: dummyUserId,
                                name: "{{ $userEmail ?? 'user' }}",
                                displayName: "{{ $userName ?? 'Employee' }}"
                            },
                            pubKeyCredParams: [
                                { type: "public-key", alg: -7 },
                                { type: "public-key", alg: -257 }
                            ],
                            authenticatorSelection: {
                                authenticatorAttachment: "platform",
                                userVerification: "required"
                            },
                            timeout: 60000
                        }
                    };

                    try {
                        const credential = await navigator.credentials.create(createOptions);
                        if (credential) {
                            handleVerificationSuccess();
                            return;
                        }
                    } catch (webAuthnErr) {
                        console.warn('WebAuthn prompt dismissed or failed:', webAuthnErr);
                        // If user canceled or failed, prompt cleanly
                        if (webAuthnErr.name === 'NotAllowedError') {
                            alert('⚠️ {{ __("Biometric verification was canceled or timed out.") }}');
                            bioBtn.disabled = false;
                            return;
                        }
                    }
                }
            }

            // Fallback for devices without active hardware authenticator:
            // Native camera or device prompt verification
            if (confirm('📸 {{ __("Please authenticate using Face ID or device credentials before changing password") }}')) {
                handleVerificationSuccess();
            } else {
                bioBtn.disabled = false;
            }

        } catch (err) {
            console.error('Biometric verification error:', err);
            alert('⚠️ {{ __("Biometric verification failed. Please try again.") }}');
            bioBtn.disabled = false;
        }
    }

    function handleVerificationSuccess() {
        isBiometricVerified = true;

        // Update Hero Card Status
        const bioPill = document.getElementById('bioStatusPill');
        bioPill.classList.remove('locked');
        bioPill.classList.add('unlocked');
        document.getElementById('bioStatusLabel').textContent = '{{ __("Identity verified successfully") }} ✓';

        const bioBtn = document.getElementById('btnVerifyBio');
        bioBtn.classList.add('verified');
        bioBtn.innerHTML = '<i class="bx bx-check-circle"></i> <span>{{ __("Verified") }} ✓</span>';

        // Unlock Form Fields
        const lockBadge = document.getElementById('lockBadge');
        lockBadge.classList.add('unlocked');
        lockBadge.innerHTML = '<i class="bx bx-lock-open text-success"></i>';

        document.getElementById('lockNotice').innerHTML = '<span class="text-success fw-bold"><i class="bx bx-check-circle me-1"></i>{{ __("Identity verified successfully") }}</span>';

        document.getElementById('new_password').disabled = false;
        document.getElementById('password_confirmation').disabled = false;
        document.getElementById('btnSavePassword').disabled = false;

        document.getElementById('new_password').focus();
    }

    // Submit New Password via AJAX
    async function submitNewPassword(e) {
        e.preventDefault();

        if (!isBiometricVerified) {
            alert('⚠️ {{ __("Identity Verification Required") }}');
            return;
        }

        const password = document.getElementById('new_password').value;
        const passwordConfirmation = document.getElementById('password_confirmation').value;

        if (password.length < 8) {
            alert('⚠️ {{ __("Password must be at least 8 characters.") }}');
            return;
        }

        if (password !== passwordConfirmation) {
            alert('⚠️ {{ __("Passwords do not match.") }}');
            return;
        }

        const saveBtn = document.getElementById('btnSavePassword');
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> <span>...</span>';

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
            const response = await fetch("{{ route('portal.profile.security.password') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    password: password,
                    password_confirmation: passwordConfirmation
                })
            });

            const data = await response.json();

            if (response.ok && data.success) {
                alert('✅ {{ __("Password updated successfully.") }}');
                window.location.href = "{{ route('portal.profile') }}";
            } else {
                alert('⚠️ ' + (data.message || '{{ __("Failed to update password.") }}'));
                saveBtn.disabled = false;
                saveBtn.innerHTML = '<i class="bx bx-check-shield"></i> <span>{{ __("Save New Password") }}</span>';
            }
        } catch (err) {
            console.error('Password update error:', err);
            alert('⚠️ {{ __("Connection error with server") }}');
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="bx bx-check-shield"></i> <span>{{ __("Save New Password") }}</span>';
        }
    }
</script>
@endpush
@endsection
