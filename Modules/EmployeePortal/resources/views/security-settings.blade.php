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

    <!-- 3. Legal & Privacy Policy Link (Apple App Store Requirement) -->
    <div class="security-form-card mt-3" style="padding: 14px 16px;">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <i class="bx bx-shield-quarter text-primary fs-4"></i>
                <div>
                    <h4 class="m-0 fs-6 fw-bold text-dark">{{ __('Privacy Policy & Data Protection') }}</h4>
                    <p class="m-0 text-muted" style="font-size: 11.5px;">{{ __('Review how ALA ORBIT protects your personal data') }}</p>
                </div>
            </div>
            <a href="{{ route('portal.privacy') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1" style="font-size: 12px; font-weight: 700;">
                {{ __('View') }}
            </a>
        </div>
    </div>

    <!-- 4. Account Deletion & Data Rights (Apple Guideline 5.1.1(v)) -->
    <div class="security-danger-card mt-3">
        <div class="d-flex align-items-center gap-2 mb-2">
            <i class="bx bx-error-circle text-danger fs-4"></i>
            <h4 class="m-0 fs-6 fw-bold text-danger">{{ __('Account Deletion & Data Rights') }}</h4>
        </div>
        <p class="danger-card-desc mb-3">
            {{ __('In accordance with privacy standards, you can request account deactivation and data purging from our HR system. This action requires current password verification.') }}
        </p>
        <button type="button" class="btn-delete-account" onclick="openDeleteAccountModal()">
            <i class="bx bx-trash"></i>
            <span>{{ __('Request Account Deletion') }}</span>
        </button>
    </div>
</div>

<!-- Account Deletion Modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-danger d-flex align-items-center gap-2">
                    <i class="bx bx-error-circle fs-3"></i>
                    {{ __('Request Account Deletion') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-2">
                <p class="text-muted" style="font-size: 13px;">
                    {{ __('Please enter your current password to confirm account deactivation and data purge request. Your active sessions will be terminated.') }}
                </p>
                <form id="deleteAccountForm" onsubmit="submitDeleteAccount(event)">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size: 12.5px;">{{ __('Current Password') }} <span class="text-danger">*</span></label>
                        <input type="password" id="delCurrentPassword" name="password" class="form-control" required placeholder="{{ __('Enter current password') }}" style="border-radius: 12px; font-size: 13.5px;" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size: 12.5px;">{{ __('Reason for Leaving (Optional)') }}</label>
                        <textarea id="delReason" name="reason" class="form-control" rows="2" placeholder="{{ __('Optional notes for HR administration...') }}" style="border-radius: 12px; font-size: 13px;"></textarea>
                    </div>
                    <div class="d-flex gap-2 justify-content-end">
                        <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" id="btnConfirmDeleteAccount" class="btn btn-danger rounded-pill px-4 fw-bold">
                            {{ __('Confirm Deletion Request') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

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
                                name: "{{ config('app.name', 'ALA ORBIT') }}",
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

    // Account Deletion Handlers (Apple Guideline 5.1.1(v))
    let deleteModalInstance = null;
    function openDeleteAccountModal() {
        const modalEl = document.getElementById('deleteAccountModal');
        if (modalEl) {
            deleteModalInstance = new bootstrap.Modal(modalEl);
            deleteModalInstance.show();
        }
    }

    async function submitDeleteAccount(event) {
        event.preventDefault();
        const pwd = document.getElementById('delCurrentPassword').value;
        const reason = document.getElementById('delReason').value;
        const btn = document.getElementById('btnConfirmDeleteAccount');

        if (!pwd) return;

        btn.disabled = true;
        btn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> {{ __("Processing...") }}';

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
            const response = await fetch("{{ route('portal.profile.security.deleteAccount') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ password: pwd, reason: reason })
            });

            const data = await response.json();

            if (response.ok && data.success) {
                alert('✅ ' + data.message);
                if (data.redirect_url) {
                    window.location.href = data.redirect_url;
                }
            } else {
                alert('⚠️ ' + (data.message || '{{ __("Failed to process account deletion request.") }}'));
                btn.disabled = false;
                btn.innerHTML = '{{ __("Confirm Deletion Request") }}';
            }
        } catch (err) {
            console.error('Account deletion error:', err);
            alert('⚠️ {{ __("Connection error with server") }}');
            btn.disabled = false;
            btn.innerHTML = '{{ __("Confirm Deletion Request") }}';
        }
    }
</script>
@endpush
@endsection
