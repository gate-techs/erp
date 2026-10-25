@extends('employeeportal::layouts.master')

@section('title', __('Edit Personal Information'))

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

    <!-- Server-Side Error Alert Banner -->
    @if(session('error'))
        <div class="portal-form-alert-banner">
            <div class="alert-icon"><i class="bx bx-error"></i></div>
            <div class="alert-text">{{ session('error') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="portal-form-alert-banner">
            <div class="alert-icon"><i class="bx bx-error-circle"></i></div>
            <div class="alert-text">
                {{ __('Please correct the highlighted errors before saving changes.') }}
            </div>
        </div>
    @endif

    <form action="{{ route('portal.profile.update') }}" method="POST" enctype="multipart/form-data" id="editProfileForm" novalidate onsubmit="return validateProfileForm(event)">
        @csrf

        <!-- 1. Hero Card - Figma Custom Spec -->
        <div class="edit-hero-card">
            <div class="edit-avatar-wrapper">
                <img src="{{ $employee?->avatar_url ?? auth()->user()?->avatar_url ?? asset('assets/portal/male-avatar.png') }}"
                     alt="Avatar"
                     class="edit-avatar-img"
                     id="previewAvatarImg"
                     onerror="this.onerror=null; this.src='{{ asset('assets/portal/teacher-avatar.png') }}';" />
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
            <div class="portal-invalid-feedback text-center mt-2" id="avatarError">
                @error('avatar') {{ $message }} @enderror
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
                    <label class="portal-input-label" for="profileNameArInput">{{ __('Full Name (Civil ID)') }} <span class="text-danger">*</span></label>
                </div>
                <div class="portal-input-box">
                    <input type="text"
                           id="profileNameArInput"
                           name="name_ar"
                           class="portal-form-control @error('name_ar') is-invalid @enderror"
                           value="{{ old('name_ar', $employee?->translate('ar')?->name ?? ($employee?->name ?? auth()->user()?->name)) }}"
                           placeholder="{{ __('Full Arabic Name') }}"
                           required />
                    <i class="bx bx-edit-alt"></i>
                </div>
                <div class="portal-invalid-feedback" id="nameArError">
                    @error('name_ar') {{ $message }} @enderror
                </div>
            </div>

            <!-- Full English Name -->
            <div class="portal-form-group">
                <div class="portal-label-row">
                    <label class="portal-input-label" for="profileNameEnInput">{{ __('Full English Name') }}</label>
                </div>
                <div class="portal-input-box">
                    <input type="text"
                           id="profileNameEnInput"
                           name="name_en"
                           class="portal-form-control @error('name_en') is-invalid @enderror"
                           value="{{ old('name_en', $employee?->translate('en')?->name ?? '') }}"
                           placeholder="Full English Name"
                           dir="ltr" />
                    <i class="bx bx-user"></i>
                </div>
                <div class="portal-invalid-feedback" id="nameEnError">
                    @error('name_en') {{ $message }} @enderror
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
                    <select name="primary_branch_id" class="portal-form-control portal-select @error('primary_branch_id') is-invalid @enderror">
                        <option value="">{{ __('Select Primary Branch...') }}</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ (old('primary_branch_id', $employee?->primary_branch_id) == $branch->id) ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="portal-invalid-feedback" id="branchError">
                    @error('primary_branch_id') {{ $message }} @enderror
                </div>
            </div>

            <!-- Date of Birth & Nationality (2 Columns) -->
            <div class="portal-row-2col">
                <div class="portal-form-group">
                    <label class="portal-input-label mb-1">{{ __('Date of Birth') }}</label>
                    <div class="portal-input-box">
                        <input type="date"
                               name="date_of_birth"
                               class="portal-form-control @error('date_of_birth') is-invalid @enderror"
                               style="padding: 0 10px;"
                               value="{{ old('date_of_birth', $employee?->date_of_birth ? \Carbon\Carbon::parse($employee->date_of_birth)->format('Y-m-d') : '1994-08-15') }}" />
                    </div>
                    <div class="portal-invalid-feedback" id="dobError">
                        @error('date_of_birth') {{ $message }} @enderror
                    </div>
                </div>

                <div class="portal-form-group">
                    <label class="portal-input-label mb-1">{{ __('Nationality') }}</label>
                    <div class="portal-input-box">
                        <select name="nationality" class="portal-form-control portal-select">
                            <option value="Kuwaiti" {{ (old('nationality', $employee?->nationality) == 'Kuwaiti' || old('nationality', $employee?->nationality) == 'كويتية' || !$employee?->nationality) ? 'selected' : '' }}>{{ __('Kuwaiti') }}</option>
                            <option value="Saudi" {{ (old('nationality', $employee?->nationality) == 'Saudi' || old('nationality', $employee?->nationality) == 'سعودية') ? 'selected' : '' }}>{{ __('Saudi') }}</option>
                            <option value="Emirati" {{ (old('nationality', $employee?->nationality) == 'Emirati' || old('nationality', $employee?->nationality) == 'إماراتية') ? 'selected' : '' }}>{{ __('Emirati') }}</option>
                            <option value="Bahraini" {{ (old('nationality', $employee?->nationality) == 'Bahraini' || old('nationality', $employee?->nationality) == 'بحرينية') ? 'selected' : '' }}>{{ __('Bahraini') }}</option>
                            <option value="Qatari" {{ (old('nationality', $employee?->nationality) == 'Qatari' || old('nationality', $employee?->nationality) == 'قطرية') ? 'selected' : '' }}>{{ __('Qatari') }}</option>
                            <option value="Omani" {{ (old('nationality', $employee?->nationality) == 'Omani' || old('nationality', $employee?->nationality) == 'عمانية') ? 'selected' : '' }}>{{ __('Omani') }}</option>
                            <option value="Egyptian" {{ (old('nationality', $employee?->nationality) == 'Egyptian' || old('nationality', $employee?->nationality) == 'مصرية') ? 'selected' : '' }}>{{ __('Egyptian') }}</option>
                            <option value="Jordanian" {{ (old('nationality', $employee?->nationality) == 'Jordanian' || old('nationality', $employee?->nationality) == 'أردنية') ? 'selected' : '' }}>{{ __('Jordanian') }}</option>
                            <option value="Lebanese" {{ (old('nationality', $employee?->nationality) == 'Lebanese' || old('nationality', $employee?->nationality) == 'لبنانية') ? 'selected' : '' }}>{{ __('Lebanese') }}</option>
                            <option value="Other" {{ (old('nationality', $employee?->nationality) == 'Other' || old('nationality', $employee?->nationality) == 'أخرى') ? 'selected' : '' }}>{{ __('Other') }}</option>
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
                        <option value="married" {{ old('marital_status', $currentMarital) === 'married' ? 'selected' : '' }}>{{ __('Married') }}</option>
                        <option value="single" {{ old('marital_status', $currentMarital) === 'single' ? 'selected' : '' }}>{{ __('Single') }}</option>
                        <option value="divorced" {{ old('marital_status', $currentMarital) === 'divorced' ? 'selected' : '' }}>{{ __('Divorced') }}</option>
                        <option value="widowed" {{ old('marital_status', $currentMarital) === 'widowed' ? 'selected' : '' }}>{{ __('Widowed') }}</option>
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
                    <label class="portal-input-label" for="portalPhoneInput">{{ __('Primary Phone Number') }} <span class="text-danger">*</span></label>
                </div>
                <div class="portal-iti-wrapper" dir="ltr">
                    @php
                        $rawPortalPhone = old('phone', $employee?->phone ?? '99112233');
                        $cleanedPortalPhone = preg_replace('/^\+965/', '', $rawPortalPhone);
                    @endphp
                    <input type="tel" id="portalPhoneInput" name="phone" class="portal-phone-input @error('phone') is-invalid @enderror" value="{{ $cleanedPortalPhone }}" dir="ltr" required />
                    <div class="portal-invalid-feedback" id="profilePhoneError">
                        @error('phone') {{ $message }} @enderror
                    </div>
                </div>
            </div>

            <!-- Email -->
            <div class="portal-form-group">
                <div class="portal-label-row">
                    <label class="portal-input-label" for="profileEmailInput">{{ __('Email Address') }} <span class="text-danger">*</span></label>
                </div>
                <div class="portal-input-box">
                    <input type="email"
                           id="profileEmailInput"
                           name="email"
                           class="portal-form-control @error('email') is-invalid @enderror"
                           value="{{ old('email', $employee?->email ?? (auth()->user()?->email ?? 'nour@astroleaders.com')) }}"
                           placeholder="name@example.com"
                           dir="ltr"
                           required />
                    <i class="bx bx-envelope"></i>
                </div>
                <div class="portal-invalid-feedback" id="profileEmailError">
                    @error('email') {{ $message }} @enderror
                </div>
            </div>

            <!-- Emergency Contact -->
            <div class="portal-row-2col">
                <div class="portal-form-group">
                    <label class="portal-input-label mb-1" for="profileEmergNameInput">{{ __('Emergency Contact Name') }}</label>
                    <input type="text"
                           id="profileEmergNameInput"
                           name="emergency_contact_name"
                           class="portal-form-control @error('emergency_contact_name') is-invalid @enderror"
                           style="padding: 0 12px;"
                           value="{{ old('emergency_contact_name', $employee?->emergency_contact_name ?? '') }}"
                           placeholder="{{ __('Name') }}" />
                    <div class="portal-invalid-feedback" id="emergNameError">
                        @error('emergency_contact_name') {{ $message }} @enderror
                    </div>
                </div>
                <div class="portal-form-group">
                    <label class="portal-input-label mb-1" for="profileEmergPhoneInput">{{ __('Emergency Contact Phone') }}</label>
                    <input type="tel"
                           id="profileEmergPhoneInput"
                           name="emergency_contact_phone"
                           class="portal-form-control @error('emergency_contact_phone') is-invalid @enderror"
                           style="padding: 0 12px;"
                           value="{{ old('emergency_contact_phone', $employee?->emergency_contact_phone ?? '') }}"
                           placeholder="+965XXXXXXXX"
                           dir="ltr" />
                    <div class="portal-invalid-feedback" id="emergPhoneError">
                        @error('emergency_contact_phone') {{ $message }} @enderror
                    </div>
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
                    <label class="portal-input-label" for="profilePasswordInput">{{ __('New Password') }}</label>
                </div>
                <div class="portal-input-box">
                    <input type="password"
                           id="profilePasswordInput"
                           name="password"
                           class="portal-form-control @error('password') is-invalid @enderror"
                           placeholder="••••••••"
                           autocomplete="new-password" />
                    <i class="bx bx-lock-alt"></i>
                </div>
                <div class="portal-invalid-feedback" id="passwordError">
                    @error('password') {{ $message }} @enderror
                </div>
                <div class="portal-input-helper">
                    {{ __('Leave blank if you do not wish to change your password. Must be at least 6 characters.') }}
                </div>
            </div>

            <div class="portal-form-group mb-0">
                <div class="portal-label-row">
                    <label class="portal-input-label" for="profilePasswordConfirmInput">{{ __('Confirm New Password') }}</label>
                </div>
                <div class="portal-input-box">
                    <input type="password"
                           id="profilePasswordConfirmInput"
                           name="password_confirmation"
                           class="portal-form-control @error('password_confirmation') is-invalid @enderror"
                           placeholder="••••••••"
                           autocomplete="new-password" />
                    <i class="bx bx-check-shield"></i>
                </div>
                <div class="portal-invalid-feedback" id="passwordConfirmError">
                    @error('password_confirmation') {{ $message }} @enderror
                </div>
            </div>
        </div>

        <!-- 5. Exact Custom Action Buttons -->
        <div class="edit-profile-actions">
            <!-- Save Button -->
            <button type="submit" class="btn-save-custom" id="btnSaveProfile">
                <span>{{ __('Save Changes') }}</span>
                <i class="bx bx-check-circle fs-5"></i>
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
            const itiInstance = window.intlTelInput(phoneInputField, {
                initialCountry: "kw",
                preferredCountries: ["kw", "sa", "ae", "qa", "bh", "om", "eg"],
                utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js",
                separateDialCode: true,
                autoPlaceholder: "polite"
            });

            const adjustPhonePadding = () => {
                const selectedFlag = phoneInputField.closest('.iti')?.querySelector('.iti__selected-flag');
                if (selectedFlag) {
                    const width = selectedFlag.offsetWidth;
                    if (width > 0) {
                        phoneInputField.style.setProperty('padding-left', (width + 8) + 'px', 'important');
                    }
                }
            };

            setTimeout(adjustPhonePadding, 50);
            phoneInputField.addEventListener('countrychange', adjustPhonePadding);

            phoneInputField.addEventListener('input', function() {
                clearFieldError('portalPhoneInput', 'profilePhoneError');
            });
        }

        // Live input error clearing
        const inputsToListen = [
            { id: 'profileNameArInput', err: 'nameArError' },
            { id: 'profileEmailInput', err: 'profileEmailError' },
            { id: 'profilePasswordInput', err: 'passwordError' },
            { id: 'profilePasswordConfirmInput', err: 'passwordConfirmError' }
        ];

        inputsToListen.forEach(item => {
            const el = document.getElementById(item.id);
            if (el) {
                el.addEventListener('input', () => clearFieldError(item.id, item.err));
            }
        });
    });

    function setFieldError(fieldId, errorId, message) {
        const field = document.getElementById(fieldId);
        const errorEl = document.getElementById(errorId);
        if (field) field.classList.add('is-invalid');
        if (errorEl) {
            errorEl.textContent = message;
            errorEl.classList.add('d-block');
        }
    }

    function clearFieldError(fieldId, errorId) {
        const field = document.getElementById(fieldId);
        const errorEl = document.getElementById(errorId);
        if (field) {
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
        }
        if (errorEl) {
            errorEl.textContent = '';
            errorEl.classList.remove('d-block');
        }
    }

    function validateProfileForm(e) {
        let hasError = false;

        const nameAr = document.getElementById('profileNameArInput');
        const phone = document.getElementById('portalPhoneInput');
        const email = document.getElementById('profileEmailInput');
        const password = document.getElementById('profilePasswordInput');
        const passwordConfirm = document.getElementById('profilePasswordConfirmInput');

        // 1. Validate Arabic Name
        if (nameAr && !nameAr.value.trim()) {
            setFieldError('profileNameArInput', 'nameArError', "{{ __('Please enter your full name as in Civil ID.') }}");
            hasError = true;
        } else if (nameAr) {
            clearFieldError('profileNameArInput', 'nameArError');
        }

        // 2. Validate Phone
        if (phone && !phone.value.trim()) {
            setFieldError('portalPhoneInput', 'profilePhoneError', "{{ __('Primary phone number is required.') }}");
            hasError = true;
        } else if (phone) {
            clearFieldError('portalPhoneInput', 'profilePhoneError');
        }

        // 3. Validate Email
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (email && (!email.value.trim() || !emailPattern.test(email.value.trim()))) {
            setFieldError('profileEmailInput', 'profileEmailError', "{{ __('Please enter a valid email address.') }}");
            hasError = true;
        } else if (email) {
            clearFieldError('profileEmailInput', 'profileEmailError');
        }

        // 4. Validate Password & Confirmation (if entered)
        if (password && password.value) {
            if (password.value.length < 6) {
                setFieldError('profilePasswordInput', 'passwordError', "{{ __('Password must be at least 6 characters.') }}");
                hasError = true;
            } else {
                clearFieldError('profilePasswordInput', 'passwordError');
            }

            if (passwordConfirm && password.value !== passwordConfirm.value) {
                setFieldError('profilePasswordConfirmInput', 'passwordConfirmError', "{{ __('Passwords do not match.') }}");
                hasError = true;
            } else if (passwordConfirm) {
                clearFieldError('profilePasswordConfirmInput', 'passwordConfirmError');
            }
        } else {
            if (password) clearFieldError('profilePasswordInput', 'passwordError');
            if (passwordConfirm) clearFieldError('profilePasswordConfirmInput', 'passwordConfirmError');
        }

        if (hasError) {
            e.preventDefault();
            if (typeof showPortalToast === 'function') {
                showPortalToast('error', "{{ __('Validation Error') }}", "{{ __('Please correct the highlighted fields before saving.') }}");
            }
            return false;
        }

        // Disable button to prevent double-submit
        const submitBtn = document.getElementById('btnSaveProfile');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = `<span>{{ __('Saving...') }}</span> <i class="bx bx-loader-alt bx-spin fs-5"></i>`;
        }

        return true;
    }

    function previewSelectedAvatar(input) {
        clearFieldError('previewAvatarImg', 'avatarError');
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const maxBytes = 4 * 1024 * 1024; // 4MB
            if (file.size > maxBytes) {
                setFieldError('previewAvatarImg', 'avatarError', "{{ __('Image size exceeds 4MB limit!') }}");
                input.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('previewAvatarImg');
                if (preview) {
                    preview.src = e.target.result;
                }
            };
            reader.readAsDataURL(file);
        }
    }
</script>
@endpush
@endsection
