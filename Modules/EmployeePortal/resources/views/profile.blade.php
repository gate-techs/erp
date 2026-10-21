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
        @include('employeeportal::components.notification-bell')
    </div>

    <!-- 1. Hero Profile Card -->
    <div class="portal-profile-hero-card">
        <div class="portal-profile-hero-top">
            <div class="portal-profile-hero-avatar-box">
                <img src="{{ $avatar_url }}" alt="{{ $name }}" class="portal-profile-hero-avatar" onerror="this.onerror=null; this.src='{{ asset('assets/portal/teacher-avatar.png') }}';" />
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

    <!-- 2. Language Switch Strip -->
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

    <!-- 3. Section: Profile & Job Details -->
    <h3 class="portal-profile-section-title">{{ __('Profile & Job Details') }}</h3>
    <div class="portal-profile-group-card">
        <!-- 3.1 Job & Contractual Details -->
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

        <!-- 3.2 Salary Details & Allowances -->
        <a href="{{ route('portal.profile.salary') }}" class="portal-profile-item">
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

        <!-- 3.3 Advances & Financial Entitlements -->
        <a href="{{ route('portal.loans.index', ['from' => 'profile']) }}" class="portal-profile-item">
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

    <!-- 4. Section: Documents, Certificates & Training -->
    <h3 class="portal-profile-section-title">{{ __('Documents, Certificates & Training') }}</h3>
    <div class="portal-profile-group-card">
        <!-- 4.1 Archive of Official Documents & Records -->
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

        <!-- 4.2 Awards & Honorary Incentives Log -->
        <a href="{{ route('portal.profile.rewards') }}" class="portal-profile-item">
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

    <!-- 5. Section: Policies & Account Settings -->
    <h3 class="portal-profile-section-title">{{ __('Policies & Account Settings') }}</h3>
    <div class="portal-profile-group-card">
        <!-- 5.1 Nursery Policies & Regulations -->
        <a href="{{ route('portal.profile.policies') }}" class="portal-profile-item">
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

        <!-- 5.2 Notification & Alert Settings -->
        <a href="{{ route('portal.profile.notifications') }}" class="portal-profile-item">
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

        <!-- 5.3 Security & Face ID -->
        <a href="{{ route('portal.profile.security') }}" class="portal-profile-item">
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

        <!-- 5.4 Privacy Policy -->
        <a href="{{ route('portal.privacy') }}" class="portal-profile-item">
            <div class="portal-profile-item-right">
                <div class="portal-profile-item-icon icon-indigo">
                    <i class="bx bx-shield-quarter"></i>
                </div>
                <div>
                    <h4 class="portal-profile-item-title">{{ __('Privacy Policy') }}</h4>
                    <p class="portal-profile-item-subtitle">{{ __('Review how ALA ORBIT protects your personal data') }}</p>
                </div>
            </div>
            <i class="bx {{ app()->getLocale() === 'ar' ? 'bx-chevron-left' : 'bx-chevron-right' }} portal-profile-item-chevron"></i>
        </a>

        <!-- 5.5 Terms & Conditions -->
        <a href="{{ route('portal.terms') }}" class="portal-profile-item">
            <div class="portal-profile-item-right">
                <div class="portal-profile-item-icon icon-purple">
                    <i class="bx bx-file"></i>
                </div>
                <div>
                    <h4 class="portal-profile-item-title">{{ __('Terms & Conditions') }}</h4>
                    <p class="portal-profile-item-subtitle">{{ __('System usage agreement and attendance regulations') }}</p>
                </div>
            </div>
            <i class="bx {{ app()->getLocale() === 'ar' ? 'bx-chevron-left' : 'bx-chevron-right' }} portal-profile-item-chevron"></i>
        </a>

        <!-- 5.6 Request Account Deletion -->
        <a href="javascript:void(0)" onclick="openProfileDeleteAccountModal()" class="portal-profile-item" style="background: rgba(239, 68, 68, 0.02);">
            <div class="portal-profile-item-right">
                <div class="portal-profile-item-icon" style="background: #FEE2E2; color: #DC2626;">
                    <i class="bx bx-user-x"></i>
                </div>
                <div>
                    <h4 class="portal-profile-item-title" style="color: #DC2626;">{{ __('Request Account Deletion') }}</h4>
                    <p class="portal-profile-item-subtitle">{{ __('Account deactivation and data purging from HR system') }}</p>
                </div>
            </div>
            <i class="bx {{ app()->getLocale() === 'ar' ? 'bx-chevron-left' : 'bx-chevron-right' }} portal-profile-item-chevron" style="color: #DC2626;"></i>
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

<!-- Account Deletion Modal -->
<div class="modal fade" id="profileDeleteAccountModal" tabindex="-1" aria-hidden="true">
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
                <form id="profileDeleteAccountForm" onsubmit="submitProfileDeleteAccount(event)">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size: 12.5px;">{{ __('Current Password') }} <span class="text-danger">*</span></label>
                        <input type="password" class="form-control rounded-3" id="profileDelCurrentPassword" required placeholder="••••••••" style="font-size: 13px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size: 12.5px;">{{ __('Reason for Leaving (Optional)') }}</label>
                        <textarea class="form-control rounded-3" id="profileDelReason" rows="2" placeholder="{{ __('Optional notes for HR administration...') }}" style="font-size: 13px;"></textarea>
                    </div>
                    <div class="d-flex gap-2 justify-content-end mt-4">
                        <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal" style="font-size: 13px; font-weight: 700;">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-danger rounded-pill px-4" id="btnConfirmProfileDeleteAccount" style="font-size: 13px; font-weight: 700;">{{ __('Confirm Deletion Request') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let profileDeleteModalInstance = null;
    function openProfileDeleteAccountModal() {
        const modalEl = document.getElementById('profileDeleteAccountModal');
        if (modalEl) {
            profileDeleteModalInstance = new bootstrap.Modal(modalEl);
            profileDeleteModalInstance.show();
        }
    }

    async function submitProfileDeleteAccount(event) {
        event.preventDefault();
        const pwd = document.getElementById('profileDelCurrentPassword').value;
        const reason = document.getElementById('profileDelReason').value;
        const btn = document.getElementById('btnConfirmProfileDeleteAccount');

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
