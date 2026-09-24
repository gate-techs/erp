@php
    $errors = $errors ?? session('errors', new \Illuminate\Support\ViewErrorBag());
    $isEdit = isset($employee) && $employee !== null;
    $assignedBranchIds = $isEdit ? $employee->branches->pluck('id')->toArray() : [];
    $currentDept = $isEdit && $employee->department ? $employee->department : null;
    $currentAdminId = $currentDept ? $currentDept->parent_id : null;
@endphp

<!-- Top Overview & Account Setup Card -->
<div class="card employee-section-card mb-4">
    <div class="card-header">
        <div class="d-flex align-items-center gap-3">
            <div class="avatar avatar-sm">
                <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-cog font-20"></i></span>
            </div>
            <div>
                <h6 class="fw-bold mb-0 text-heading">{{ __('Overview & Account Setup') }}</h6>
                <small class="text-muted d-block mt-1">{{ __('Core identity and system access') }}</small>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row g-4 align-items-stretch">
            <!-- Avatar & Profile Widget -->
            <div class="col-12 col-lg-3">
                <div class="h-100 p-3 p-xl-4 employee-form-surface border rounded-3 d-flex flex-column align-items-center justify-content-center text-center">
                    @php
                        $rawGender = $isEdit ? ($employee->gender instanceof \BackedEnum ? $employee->gender->value : ($employee->gender ?? 'female')) : 'female';
                        $genderVal = strtolower((string)$rawGender);
                        $defaultAvatar = ($genderVal === 'male' || $genderVal === 'ذكر') 
                            ? asset('assets/img/avatars/male-avatar.png') 
                            : asset('assets/img/avatars/female-avatar.png');
                        $hasCustomAvatar = ($isEdit && $employee->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($employee->avatar)) ? '1' : '0';
                        $currentAvatarSrc = $hasCustomAvatar === '1' ? $employee->avatar_url : $defaultAvatar;

                        $completion = $isEdit ? $employee->profile_completion : [
                            'percentage' => 20,
                            'color' => '#ff3e1d',
                            'class' => 'bg-danger',
                            'label' => __('Incomplete')
                        ];
                        $formCircumference = 389.56;
                        $formDashOffset = round($formCircumference - ($formCircumference * ($completion['percentage'] / 100)), 2);
                    @endphp
                    <div class="position-relative d-inline-flex align-items-center justify-content-center mb-3" style="width: 130px; height: 130px;" data-bs-toggle="tooltip" title="{{ __('Profile Completion') }}: {{ $completion['percentage'] }}% ({{ $completion['label'] }})">
                        <!-- SVG Circular Progress Ring -->
                        <svg class="position-absolute top-0 start-0" width="130" height="130" viewBox="0 0 140 140" style="transform: rotate(-90deg);">
                            <circle cx="70" cy="70" r="62" stroke="#eceef1" stroke-width="5" fill="none" />
                            <circle id="profileCompletionCircle" cx="70" cy="70" r="62" stroke="{{ $completion['color'] }}" stroke-width="5" 
                                    stroke-dasharray="{{ $formCircumference }}" stroke-dashoffset="{{ $formDashOffset }}" 
                                    stroke-linecap="round" fill="none" style="transition: stroke-dashoffset 0.6s ease, stroke 0.4s ease;" />
                        </svg>
                        
                        <img id="avatarPreview" 
                             src="{{ $currentAvatarSrc }}" 
                             alt="Avatar" 
                             data-female-avatar="{{ asset('assets/img/avatars/female-avatar.png') }}"
                             data-male-avatar="{{ asset('assets/img/avatars/male-avatar.png') }}"
                             data-has-custom="{{ $hasCustomAvatar }}"
                             class="rounded-circle shadow-xs" 
                             style="width: 104px; height: 104px; object-fit: cover; z-index: 1;">
                             
                        <label for="avatarInput" class="btn btn-icon btn-sm btn-primary rounded-circle position-absolute shadow-sm" title="{{ __('Upload Avatar') }}" style="bottom: 4px; right: 4px; z-index: 4; cursor: pointer;">
                            <i class="icon-base bx bx-camera font-16"></i>
                        </label>
                        <input type="file" id="avatarInput" name="avatar" class="d-none" accept="image/png, image/jpeg, image/jpg, image/webp">
                        
                        <span id="profileCompletionBadge" class="position-absolute badge {{ $completion['class'] }} rounded-pill text-white fw-bold shadow-sm" style="bottom: -8px; font-size: 11px; padding: 2px 8px; z-index: 3;">
                            {{ $completion['percentage'] }}%
                        </span>
                    </div>
                    <h6 class="fw-bold mb-1 text-heading text-truncate w-100" id="cardEmployeeName">
                        {{ $isEdit ? ($employee->translate('ar')?->name ?? $employee->translate('en')?->name) : __('New Employee') }}
                    </h6>
                    <p class="text-muted small mb-0 text-truncate w-100">
                        {{ $isEdit && $employee->jobTitle ? ($employee->jobTitle->translate('ar')?->name ?? $employee->jobTitle->name) : __('Workforce Profile') }}
                    </p>
                </div>
            </div>

            <!-- Core Setup & System Account -->
            <div class="col-12 col-lg-9 d-flex flex-column justify-content-between">
                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-4">
                        <label class="form-label text-muted small fw-semibold mb-1">{{ __('Employee Code') }}</label>
                        <div class="input-group">
                            <span class="input-group-text employee-form-surface"><i class="bx bx-barcode"></i></span>
                            <input type="text" name="employee_code" class="form-control @error('employee_code') is-invalid @enderror" 
                                   value="{{ old('employee_code', $isEdit ? $employee->employee_code : $nextCode) }}" placeholder="{{ __('e.g. EMP-0001') }}">
                        </div>
                        @error('employee_code') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label text-muted small fw-semibold mb-1" for="employeeNameAr">{{ __('Full Name [Arabic]') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name_ar" id="employeeNameAr" class="form-control @error('name_ar') is-invalid @enderror" 
                               value="{{ old('name_ar', $isEdit ? $employee->translate('ar')?->name : '') }}" 
                               placeholder="{{ __('e.g. Ahmed Mohamed') }}" required dir="rtl">
                        @error('name_ar') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label text-muted small fw-semibold mb-1 d-flex justify-content-between align-items-center" for="employeeNameEn">
                            <span>{{ __('Full Name [English]') }}</span>
                            <span class="badge bg-label-primary small cursor-pointer" id="btnBadgeTranslateName" title="{{ __('Auto-transliterate with AI') }}" style="cursor: pointer;">
                                <i class="bx bx-brain me-1" id="translateNameIcon"></i>{{ __('AI Translate') }}
                            </span>
                        </label>
                        <input type="text" name="name_en" id="employeeNameEn" class="form-control @error('name_en') is-invalid @enderror" 
                               value="{{ old('name_en', $isEdit ? $employee->translate('en')?->name : '') }}" 
                               placeholder="{{ __('e.g. Ahmed Mohamed Ali') }}" dir="ltr">
                        <div id="aiTranslateFeedback" class="small text-danger mt-1 d-none"></div>
                        @error('name_en') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                </div>

                <!-- Academic Teacher Switch Option -->
                <div class="p-3 mb-3 employee-form-surface rounded-3 border d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar avatar-sm bg-label-primary rounded p-2 d-flex align-items-center justify-content-center">
                            <i class="bx bx-book-reader font-20"></i>
                        </div>
                        <div>
                            <label class="form-check-label fw-semibold text-heading small mb-0 cursor-pointer" for="isTeacherSwitch">
                                {{ __('Academic Teacher') }}
                            </label>
                            <span class="text-muted x-small d-block">{{ __('Enable this option if this employee is an academic teacher (assigned to subjects, classrooms, and timetables).') }}</span>
                        </div>
                    </div>
                    <div class="form-check form-switch m-0">
                        <input class="form-check-input" type="checkbox" id="isTeacherSwitch" name="is_teacher" value="1"
                               {{ old('is_teacher', $isEdit && $employee->is_teacher ? '1' : '0') == '1' ? 'checked' : '' }} style="cursor: pointer;">
                    </div>
                </div>

                <!-- System User Account Box (Inline) -->
                @php
                    $currentRole = old('role', ($isEdit && $employee->user) ? $employee->user->roles->first()?->name : 'employee');
                    $rolesList = $roles ?? collect();
                @endphp
                <div class="p-3 employee-form-surface rounded-3 border">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <i class="icon-base bx bx-user-circle text-primary font-22"></i>
                            <div>
                                <span class="fw-semibold text-heading small">{{ __('System User Account') }}</span>
                                <span class="text-muted x-small d-block">{{ __('Allow this employee to log into the ERP system with their email and role.') }}</span>
                            </div>
                        </div>
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" id="createUserAccountSwitch" name="create_user_account" value="1"
                                   {{ old('create_user_account', $isEdit && $employee->user_id ? '1' : '0') == '1' ? 'checked' : '' }}>
                        </div>
                    </div>

                    <div id="userAccountFields" class="{{ old('create_user_account', $isEdit && $employee->user_id ? '1' : '0') == '1' ? '' : 'd-none' }} pt-3 mt-2 border-top">
                        <div class="row align-items-center g-3">
                            @if ($isEdit && $employee->user)
                                <div class="col-12">
                                    <div class="alert alert-light-primary py-2 px-3 small mb-0 d-flex align-items-center gap-2 rounded-2">
                                        <i class="bx bx-check-shield text-success font-18"></i>
                                        <span class="text-truncate">{{ __('Linked User:') }} <strong>{{ $employee->user->email }}</strong> ({{ $employee->user->roles->pluck('name')->join(', ') ?: __('No Role') }})</span>
                                    </div>
                                </div>
                            @endif

                            <!-- Email -->
                            <div class="col-12 col-md-4">
                                <label class="form-label text-muted small fw-semibold mb-1">
                                    {{ __('Email') }} <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bx bx-envelope"></i></span>
                                    <input type="email" id="userAccountEmail" name="email" class="form-control bg-white @error('email') is-invalid @enderror" 
                                           value="{{ old('email', $isEdit ? $employee->email : '') }}" placeholder="employee@domain.com" dir="ltr">
                                </div>
                                @error('email') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <!-- Password -->
                            <div class="col-12 col-md-4">
                                <label class="form-label text-muted small fw-semibold mb-1">
                                    {{ $isEdit && $employee->user_id ? __('Change Password') : __('Password') }}
                                    @if (!$isEdit) <span class="text-muted font-11">({{ __('Default:') }} 12345678)</span> @endif
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bx bx-lock-alt"></i></span>
                                    <input type="password" name="user_password" class="form-control bg-white" placeholder="{{ $isEdit ? __('Leave blank to keep current') : '••••••••' }}">
                                </div>
                            </div>

                            <!-- Role -->
                            <div class="col-12 col-md-4">
                                <label class="form-label text-muted small fw-semibold mb-1">
                                    {{ __('Role & Permissions') }} <span class="text-danger">*</span>
                                </label>
                                <select name="role" class="form-select select2" data-control="select2" data-placeholder="{{ __('Select Role...') }}">
                                    <option value=""></option>
                                    @foreach ($rolesList as $r)
                                        <option value="{{ $r->name }}" {{ $currentRole === $r->name ? 'selected' : '' }}>
                                            {{ $r->display_name ?? __($r->name) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

        <!-- 1. Personal & Civil Information -->
        <div class="card employee-section-card mb-4">
            <div class="card-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar avatar-sm">
                        <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-user font-20"></i></span>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-heading">{{ __('Personal Information') }}</h6>
                        <small class="text-muted d-block mt-1">{{ __('Basic civil and identification details') }}</small>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">

                    <!-- Gender (Select2) -->
                    <div class="col-12 col-md-3">
                        <label class="form-label small fw-semibold mb-1">{{ __('Gender') }} <span class="text-danger">*</span></label>
                        <select id="genderSelect" name="gender" class="form-select select2" data-control="select2" data-placeholder="{{ __('Select Gender...') }}" required>
                            <option value=""></option>
                            @foreach ($genders as $gender)
                                @php
                                    if ($gender instanceof \Modules\Settings\Models\GeneralSetting) {
                                        $gVal = strtolower($gender->value);
                                        $gLabel = $gender->translate('ar')?->name ?? $gender->translate('en')?->name ?? $gender->name ?? $gender->value;
                                    } elseif (is_object($gender) && method_exists($gender, 'label')) {
                                        $gVal = $gender->value;
                                        $gLabel = $gender->label();
                                    } else {
                                        $gVal = is_object($gender) ? $gender->value : $gender;
                                        $gLabel = __($gender);
                                    }
                                    $currentGender = strtolower($isEdit ? ($employee->gender instanceof \BackedEnum ? $employee->gender->value : ($employee->gender ?? 'female')) : 'female');
                                @endphp
                                <option value="{{ $gVal }}" {{ old('gender', $currentGender) === $gVal ? 'selected' : '' }}>
                                    {{ $gLabel }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Marital Status (From Settings Select2) -->
                    <div class="col-12 col-md-3">
                        <label class="form-label small fw-semibold mb-1">{{ __('Marital Status') }}</label>
                        <select name="marital_status" class="form-select select2" data-control="select2" data-placeholder="{{ __('Select Marital Status...') }}">
                            <option value=""></option>
                            @if ($maritalStatuses->isNotEmpty())
                                @foreach ($maritalStatuses as $ms)
                                    @php
                                        $msName = $ms->translate(app()->getLocale())?->name ?? $ms->translate('ar')?->name ?? $ms->translate('en')?->name ?? $ms->name ?? $ms->value;
                                        $msVal = $ms->value ?? $msName;
                                    @endphp
                                    <option value="{{ $msVal }}" {{ old('marital_status', $isEdit ? $employee->marital_status : '') == $msVal ? 'selected' : '' }}>
                                        {{ $msName }}
                                    </option>
                                @endforeach
                            @else
                                <option value="single" {{ old('marital_status', $isEdit ? $employee->marital_status : '') == 'single' ? 'selected' : '' }}>{{ __('Single') }}</option>
                                <option value="married" {{ old('marital_status', $isEdit ? $employee->marital_status : '') == 'married' ? 'selected' : '' }}>{{ __('Married') }}</option>
                                <option value="divorced" {{ old('marital_status', $isEdit ? $employee->marital_status : '') == 'divorced' ? 'selected' : '' }}>{{ __('Divorced') }}</option>
                                <option value="widowed" {{ old('marital_status', $isEdit ? $employee->marital_status : '') == 'widowed' ? 'selected' : '' }}>{{ __('Widowed') }}</option>
                            @endif
                        </select>
                    </div>

                    <!-- Nationality (From Settings Select2) -->
                    <div class="col-12 col-md-3">
                        <label class="form-label small fw-semibold mb-1">{{ __('Nationality') }}</label>
                        <select name="nationality" class="form-select select2" data-control="select2" data-placeholder="{{ __('Select Nationality...') }}">
                            <option value=""></option>
                            @if ($nationalities->isNotEmpty())
                                @foreach ($nationalities as $nat)
                                    @php
                                        $natName = $nat->translate(app()->getLocale())?->name ?? $nat->translate('ar')?->name ?? $nat->translate('en')?->name ?? $nat->name ?? $nat->value;
                                        $natVal = $nat->value ?? $natName;
                                    @endphp
                                    <option value="{{ $natVal }}" {{ old('nationality', $isEdit ? $employee->nationality : 'KWT') == $natVal || old('nationality', $isEdit ? $employee->nationality : '') == $natName ? 'selected' : '' }}>
                                        {{ $natName }}
                                    </option>
                                @endforeach
                            @else
                                <option value="كويتي" {{ old('nationality', $isEdit ? $employee->nationality : 'كويتي') == 'كويتي' ? 'selected' : '' }}>{{ __('Kuwaiti') }}</option>
                                <option value="سعودي" {{ old('nationality', $isEdit ? $employee->nationality : '') == 'سعودي' ? 'selected' : '' }}>{{ __('Saudi') }}</option>
                                <option value="مصري" {{ old('nationality', $isEdit ? $employee->nationality : '') == 'مصري' ? 'selected' : '' }}>{{ __('Egyptian') }}</option>
                            @endif
                        </select>
                    </div>

                    <!-- Religion (From Settings Select2) -->
                    <div class="col-12 col-md-3">
                        <label class="form-label small fw-semibold mb-1">{{ __('Religion') }}</label>
                        <select name="religion" class="form-select select2" data-control="select2" data-placeholder="{{ __('Select Religion...') }}">
                            <option value=""></option>
                            @if ($religions->isNotEmpty())
                                @foreach ($religions as $rel)
                                    @php
                                        $relName = $rel->translate(app()->getLocale())?->name ?? $rel->translate('ar')?->name ?? $rel->translate('en')?->name ?? $rel->name ?? $rel->value;
                                        $relVal = $rel->value ?? $relName;
                                    @endphp
                                    <option value="{{ $relVal }}" {{ old('religion', $isEdit ? $employee->religion : 'ISLAM') == $relVal || old('religion', $isEdit ? $employee->religion : '') == $relName ? 'selected' : '' }}>
                                        {{ $relName }}
                                    </option>
                                @endforeach
                            @else
                                <option value="مسلم" selected>{{ __('Muslim') }}</option>
                                <option value="مسيحي">{{ __('Christian') }}</option>
                                <option value="أخرى">{{ __('Other') }}</option>
                            @endif
                        </select>
                    </div>

                    <!-- Date of Birth -->
                    <div class="col-12 col-md-4">
                        <label class="form-label small mb-1">{{ __('Date of Birth') }}</label>
                        <input type="date" name="date_of_birth" class="form-control js-flatpickr-date"
                               value="{{ old('date_of_birth', $isEdit && $employee->date_of_birth ? $employee->date_of_birth->format('Y-m-d') : '') }}">
                    </div>

                    <!-- National ID -->
                    <div class="col-12 col-md-4">
                        <label class="form-label small mb-1">{{ __('National ID') }}</label>
                        <input type="text" name="national_id" class="form-control @error('national_id') is-invalid @enderror" 
                               value="{{ old('national_id', $isEdit ? $employee->national_id : '') }}" placeholder="2900101...">
                        @error('national_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Blood Type (From Settings Select2) -->
                    <div class="col-12 col-md-4">
                        <label class="form-label small mb-1">{{ __('Blood Type') }}</label>
                        <select name="blood_type" class="form-select select2" data-control="select2" data-placeholder="{{ __('Select Blood Type...') }}">
                            <option value=""></option>
                            @if ($bloodTypes->isNotEmpty())
                                @foreach ($bloodTypes as $bt)
                                    @php
                                        $btName = $bt->translate(app()->getLocale())?->name ?? $bt->translate('ar')?->name ?? $bt->translate('en')?->name ?? $bt->name ?? $bt->value;
                                        $btVal = $bt->value ?? $btName;
                                    @endphp
                                    <option value="{{ $btVal }}" {{ old('blood_type', $isEdit ? $employee->blood_type : '') == $btVal ? 'selected' : '' }}>
                                        {{ $btName }}
                                    </option>
                                @endforeach
                            @else
                                @foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bt)
                                    <option value="{{ $bt }}" {{ old('blood_type', $isEdit ? $employee->blood_type : '') === $bt ? 'selected' : '' }}>{{ $bt }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <!-- Passport & Visa Sub-Section -->
                    <div class="col-12 mt-3">
                        <div class="p-4 employee-form-surface rounded-3 border">
                            <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                                <i class="bx bx-id-card text-primary font-20"></i>
                                <h6 class="fw-semibold mb-0 text-heading">{{ __('Passport & Visa Information') }}</h6>
                            </div>
                            <div class="row g-3">
                                <div class="col-12 col-md-4">
                                    <label class="form-label text-muted small mb-1">{{ __('Passport Number') }}</label>
                                    <input type="text" name="passport_number" class="form-control" 
                                           value="{{ old('passport_number', $isEdit ? $employee->passport_number : '') }}" placeholder="A12345678">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label text-muted small mb-1">{{ __('Passport Issue Date') }}</label>
                                    <input type="date" name="passport_issue_date" class="form-control js-flatpickr-date"
                                           value="{{ old('passport_issue_date', $isEdit && $employee->passport_issue_date ? $employee->passport_issue_date->format('Y-m-d') : '') }}">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label text-muted small mb-1">{{ __('Passport Expiry Date') }}</label>
                                    <input type="date" name="passport_expiry_date" class="form-control js-flatpickr-date"
                                           value="{{ old('passport_expiry_date', $isEdit && $employee->passport_expiry_date ? $employee->passport_expiry_date->format('Y-m-d') : '') }}">
                                </div>

                                <div class="col-12 col-md-6 mt-2">
                                    <label class="form-label text-muted small mb-1">{{ __('Visa Type') }}</label>
                                    <select name="visa_type_id" class="form-select select2" data-control="select2" data-placeholder="{{ __('Select Visa Type...') }}">
                                        <option value=""></option>
                                        @foreach ($visaTypes as $vt)
                                            <option value="{{ $vt->id }}" {{ old('visa_type_id', $isEdit ? $employee->visa_type_id : '') == $vt->id ? 'selected' : '' }}>
                                                {{ $vt->translate(app()->getLocale())?->name ?? $vt->translate('ar')?->name ?? $vt->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-md-6 mt-2">
                                    <label class="form-label text-muted small mb-1">{{ __('Visa Expiry Date') }}</label>
                                    <input type="date" name="visa_expiry_date" class="form-control js-flatpickr-date"
                                           value="{{ old('visa_expiry_date', $isEdit && $employee->visa_expiry_date ? $employee->visa_expiry_date->format('Y-m-d') : '') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Job & Placement Information -->
        <div class="card employee-section-card mb-4">
            <div class="card-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar avatar-sm">
                        <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-briefcase font-20"></i></span>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-heading">{{ __('Job & Department Assignment') }}</h6>
                        <small class="text-muted d-block mt-1">{{ __('Employment terms, roles and department') }}</small>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <!-- Primary Branch -->
                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-semibold mb-1">{{ __('Primary Branch') }} <span class="text-danger">*</span></label>
                        <select name="primary_branch_id" id="primary_branch_id" class="form-select select2" data-control="select2" data-placeholder="{{ __('Select Primary Branch...') }}" required>
                            <option value=""></option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}" {{ old('primary_branch_id', $isEdit ? $employee->primary_branch_id : '') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->translate(app()->getLocale())?->name ?? $branch->translate('ar')?->name ?? $branch->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('primary_branch_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <!-- Assigned Additional Branches -->
                    <div class="col-12 col-md-6">
                        <label class="form-label small mb-1">{{ __('Assigned Branches (Additional)') }}</label>
                        <select name="branches[]" class="form-select select2" data-control="select2" multiple data-placeholder="{{ __('Select additional branches...') }}">
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}" {{ in_array($branch->id, old('branches', $assignedBranchIds)) ? 'selected' : '' }}>
                                    {{ $branch->translate(app()->getLocale())?->name ?? $branch->translate('ar')?->name ?? $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Administration (الإدارة) -->
                    <div class="col-12 col-md-4">
                        <label class="form-label small fw-semibold mb-1">{{ __('Administration') }}</label>
                        <select id="administration_select" class="form-select select2" data-control="select2" data-placeholder="{{ __('Select Administration...') }}">
                            <option value=""></option>
                            @foreach ($administrations as $admin)
                                <option value="{{ $admin->id }}" {{ old('administration_id', $currentAdminId) == $admin->id ? 'selected' : '' }}>
                                    {{ $admin->translate(app()->getLocale())?->name ?? $admin->translate('ar')?->name ?? $admin->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Department (القسم) -->
                    <div class="col-12 col-md-4">
                        <label class="form-label small fw-semibold mb-1">{{ __('Department') }}</label>
                        <select name="department_id" id="department_select" class="form-select select2" data-control="select2" data-placeholder="{{ __('Select Department...') }}">
                            <option value=""></option>
                            @foreach ($allDepartments as $dept)
                                <option value="{{ $dept->id }}" 
                                        data-parent-id="{{ $dept->parent_id ?? '' }}"
                                        {{ old('department_id', $isEdit ? $employee->department_id : '') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->translate(app()->getLocale())?->name ?? $dept->translate('ar')?->name ?? $dept->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Job Title -->
                    <div class="col-12 col-md-4">
                        <label class="form-label small fw-semibold mb-1">{{ __('Job Title') }}</label>
                        <select name="job_title_id" class="form-select select2" data-control="select2" data-placeholder="{{ __('Select Job Title...') }}">
                            <option value=""></option>
                            @foreach ($jobTitles as $title)
                                <option value="{{ $title->id }}" {{ old('job_title_id', $isEdit ? $employee->job_title_id : '') == $title->id ? 'selected' : '' }}>
                                    {{ $title->translate(app()->getLocale())?->name ?? $title->translate('ar')?->name ?? $title->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Direct Manager -->
                    <div class="col-12 col-md-4">
                        <label class="form-label small mb-1">{{ __('Direct Manager') }}</label>
                        <select name="manager_id" class="form-select select2" data-control="select2" data-placeholder="{{ __('No Direct Manager') }}">
                            <option value=""></option>
                            @foreach ($managers as $mgr)
                                @if (!$isEdit || $mgr->id != $employee->id)
                                    <option value="{{ $mgr->id }}" {{ old('manager_id', $isEdit ? $employee->manager_id : '') == $mgr->id ? 'selected' : '' }}>
                                        {{ $mgr->translate('ar')?->name ?? $mgr->translate('en')?->name }} ({{ $mgr->employee_code }})
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <!-- Employment Type -->
                    <div class="col-12 col-md-4">
                        <label class="form-label small fw-semibold mb-1">{{ __('Employment Type') }} <span class="text-danger">*</span></label>
                        <select name="employment_type" class="form-select select2" data-control="select2" data-placeholder="{{ __('Select Employment Type...') }}" required>
                            <option value=""></option>
                            @foreach ($employmentTypes as $type)
                                <option value="{{ $type->value }}" {{ old('employment_type', $isEdit ? $employee->employment_type?->value : 'full_time') === $type->value ? 'selected' : '' }}>
                                    {{ $type->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('employment_type') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <!-- Status -->
                    <div class="col-12 col-md-4">
                        <label class="form-label small fw-semibold mb-1">{{ __('Status') }} <span class="text-danger">*</span></label>
                        <select name="status" class="form-select select2" data-control="select2" data-placeholder="{{ __('Select Status...') }}" required>
                            <option value=""></option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status->value }}" {{ old('status', $isEdit ? $employee->status?->value : 'active') === $status->value ? 'selected' : '' }}>
                                    {{ $status->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('status') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <!-- Work Schedule -->
                    <div class="col-12 col-md-4">
                        <label class="form-label small mb-1">{{ __('Work Schedule') }}</label>
                        <select name="work_schedule_id" class="form-select select2" data-control="select2" data-placeholder="{{ __('Default / Central Schedule') }}">
                            <option value=""></option>
                            @foreach ($workSchedules as $schedule)
                                <option value="{{ $schedule->id }}" {{ old('work_schedule_id', $isEdit ? $employee->work_schedule_id : '') == $schedule->id ? 'selected' : '' }}>
                                    {{ $schedule->translate(app()->getLocale())?->name ?? $schedule->translate('ar')?->name ?? $schedule->name }} ({{ $schedule->working_days_count }} {{ __('days/week') }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Shift -->
                    <div class="col-12 col-md-4">
                        <label class="form-label small mb-1">{{ __('Shift') }}</label>
                        <select name="shift_id" class="form-select select2" data-control="select2" data-placeholder="{{ __('Select Shift...') }}">
                            <option value=""></option>
                            @foreach ($shifts as $shift)
                                <option value="{{ $shift->id }}" {{ old('shift_id', $isEdit ? $employee->shift_id : '') == $shift->id ? 'selected' : '' }}>
                                    {{ $shift->translate(app()->getLocale())?->name ?? $shift->translate('ar')?->name ?? $shift->name }} ({{ $shift->start_time }} - {{ $shift->end_time }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Hire Date -->
                    <div class="col-12 col-md-4">
                        <label class="form-label small fw-semibold mb-1">{{ __('Hire Date') }} <span class="text-danger">*</span></label>
                        <input type="date" name="hire_date" class="form-control js-flatpickr-date @error('hire_date') is-invalid @enderror"
                               value="{{ old('hire_date', $isEdit && $employee->hire_date ? $employee->hire_date->format('Y-m-d') : now()->toDateString()) }}" required>
                        @error('hire_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Probation End Date -->
                    <div class="col-12 col-md-6">
                        <label class="form-label small mb-1">{{ __('Probation End Date') }}</label>
                        <input type="date" name="probation_end_date" class="form-control js-flatpickr-date"
                               value="{{ old('probation_end_date', $isEdit && $employee->probation_end_date ? $employee->probation_end_date->format('Y-m-d') : '') }}">
                    </div>

                    <!-- Contract End Date -->
                    <div class="col-12 col-md-6">
                        <label class="form-label small mb-1">{{ __('Contract End Date') }}</label>
                        <input type="date" name="contract_end_date" class="form-control js-flatpickr-date"
                               value="{{ old('contract_end_date', $isEdit && $employee->contract_end_date ? $employee->contract_end_date->format('Y-m-d') : '') }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Contact & Location Information -->
        <div class="card employee-section-card mb-4">
            <div class="card-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar avatar-sm">
                        <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-phone-call font-20"></i></span>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-heading">{{ __('Contact & Location') }}</h6>
                        <small class="text-muted d-block mt-1">{{ __('Contact details and emergency information') }}</small>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12 col-md-4">
                          <label class="form-label small fw-semibold mb-1">{{ __('Phone Number') }} <span class="text-danger">*</span></label>
                          <input type="text" name="phone" class="form-control js-phone-input @error('phone') is-invalid @enderror"
                                 value="{{ old('phone', $isEdit ? $employee->phone : '') }}" placeholder="01012345678" required dir="ltr">
                          @error('phone') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label small mb-1">{{ __('Alternate Phone') }}</label>
                        <input type="text" name="alternate_phone" class="form-control js-phone-input"
                               value="{{ old('alternate_phone', $isEdit ? $employee->alternate_phone : '') }}" placeholder="011..." dir="ltr">
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label small mb-1">{{ __('Email') }}</label>
                        <div class="input-group">
                            <span class="input-group-text employee-form-surface"><i class="bx bx-envelope"></i></span>
                            <input type="email" name="email" id="contactEmail" class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $isEdit ? $employee->email : '') }}" placeholder="employee@domain.com" dir="ltr">
                        </div>
                        @error('email') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <!-- Governorate (From Settings Select2) -->
                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-semibold mb-1">{{ __('Governorate') }}</label>
                        <select name="governorate_id" id="governorate_select" class="form-select select2" data-control="select2" data-placeholder="{{ __('Select Governorate...') }}">
                            <option value=""></option>
                            @foreach ($governorates as $gov)
                                <option value="{{ $gov->id }}" {{ old('governorate_id', $isEdit ? $employee->governorate_id : '') == $gov->id ? 'selected' : '' }}>
                                    {{ $gov->translate(app()->getLocale())?->name ?? $gov->translate('ar')?->name ?? $gov->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- City (From Settings Select2 with parent filtering) -->
                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-semibold mb-1">{{ __('City') }}</label>
                        <select name="city" id="city_select" class="form-select select2" data-control="select2" data-placeholder="{{ __('Select City...') }}">
                            <option value=""></option>
                            @foreach ($cities as $city)
                                @php
                                    $cityName = $city->translate(app()->getLocale())?->name ?? $city->translate('ar')?->name ?? $city->translate('en')?->name ?? $city->name ?? $city->value;
                                @endphp
                                <option value="{{ $cityName }}" 
                                        data-parent-id="{{ $city->parent_id ?? '' }}"
                                        {{ old('city', $isEdit ? $employee->city : '') == $cityName ? 'selected' : '' }}>
                                    {{ $cityName }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Address 1 (Arabic & English) -->
                    <div class="col-12 col-md-6">
                        <label class="form-label small mb-1">{{ __('Address 1 [Arabic]') }}</label>
                        <input type="text" name="address_1_ar" class="form-control" 
                               value="{{ old('address_1_ar', $isEdit ? $employee->translate('ar')?->address_1 : '') }}" placeholder="{{ __('Street / Building No.') }}" dir="rtl">
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label small mb-1">{{ __('Address 1 [English]') }}</label>
                        <input type="text" name="address_1_en" class="form-control" 
                               value="{{ old('address_1_en', $isEdit ? $employee->translate('en')?->address_1 : '') }}" placeholder="{{ __('Street / Building No.') }}" dir="ltr">
                    </div>

                    <!-- Address 2 (Arabic & English) -->
                    <div class="col-12 col-md-6">
                        <label class="form-label small mb-1">{{ __('Address 2 [Arabic]') }}</label>
                        <input type="text" name="address_2_ar" class="form-control" 
                               value="{{ old('address_2_ar', $isEdit ? $employee->translate('ar')?->address_2 : '') }}" placeholder="{{ __('Landmark / Additional details') }}" dir="rtl">
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label small mb-1">{{ __('Address 2 [English]') }}</label>
                        <input type="text" name="address_2_en" class="form-control" 
                               value="{{ old('address_2_en', $isEdit ? $employee->translate('en')?->address_2 : '') }}" placeholder="{{ __('Landmark / Additional details') }}" dir="ltr">
                    </div>

                    <!-- Emergency Contact Sub-Box -->
                    <div class="col-12 mt-3">
                        <div class="p-4 employee-form-surface rounded-3 border">
                            <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                                <i class="bx bx-error-circle text-primary font-20"></i>
                                <h6 class="fw-semibold mb-0 text-heading">{{ __('Emergency Contact') }}</h6>
                            </div>
                            <div class="row g-3">
                                <div class="col-12 col-md-4">
                                    <label class="form-label text-muted small mb-1">{{ __('Emergency Contact Name') }}</label>
                                    <input type="text" name="emergency_contact_name" class="form-control" 
                                           value="{{ old('emergency_contact_name', $isEdit ? $employee->emergency_contact_name : '') }}" placeholder="{{ __('e.g. Ahmed Mohamed') }}">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label text-muted small mb-1">{{ __('Emergency Contact Relation') }}</label>
                                    <select name="emergency_contact_relation" class="form-select select2" data-control="select2" data-placeholder="{{ __('Select Relation...') }}">
                                        <option value=""></option>
                                        @if ($relationships->isNotEmpty())
                                            @foreach ($relationships as $rel)
                                                @php
                                                    $relName = $rel->translate(app()->getLocale())?->name ?? $rel->translate('ar')?->name ?? $rel->translate('en')?->name ?? $rel->name ?? $rel->value;
                                                    $relVal = $rel->value ?? $relName;
                                                @endphp
                                                <option value="{{ $relVal }}" {{ old('emergency_contact_relation', $isEdit ? $employee->emergency_contact_relation : '') == $relVal ? 'selected' : '' }}>
                                                    {{ $relName }}
                                                </option>
                                            @endforeach
                                        @else
                                            <option value="أب" {{ old('emergency_contact_relation', $isEdit ? $employee->emergency_contact_relation : '') == 'أب' ? 'selected' : '' }}>{{ __('Father') }}</option>
                                            <option value="أم" {{ old('emergency_contact_relation', $isEdit ? $employee->emergency_contact_relation : '') == 'أم' ? 'selected' : '' }}>{{ __('Mother') }}</option>
                                            <option value="أخ" {{ old('emergency_contact_relation', $isEdit ? $employee->emergency_contact_relation : '') == 'أخ' ? 'selected' : '' }}>{{ __('Brother') }}</option>
                                            <option value="زوجة" {{ old('emergency_contact_relation', $isEdit ? $employee->emergency_contact_relation : '') == 'زوجة' ? 'selected' : '' }}>{{ __('Spouse') }}</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label text-muted small mb-1">{{ __('Emergency Contact Phone') }}</label>
                                    <input type="text" name="emergency_contact_phone" class="form-control js-phone-input"
                                           value="{{ old('emergency_contact_phone', $isEdit ? $employee->emergency_contact_phone : '') }}" placeholder="010..." dir="ltr">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Payroll & Financial Setup -->
        <div class="card employee-section-card mb-4">
            <div class="card-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar avatar-sm">
                        <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-wallet font-20"></i></span>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-heading">{{ __('Payroll & Financial Setup') }}</h6>
                        <small class="text-muted d-block mt-1">{{ __('Base salary and payroll configuration') }}</small>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <label class="form-label small fw-semibold mb-1">{{ __('Basic Salary') }} <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0" name="basic_salary" class="form-control @error('basic_salary') is-invalid @enderror" 
                                   value="{{ old('basic_salary', $isEdit ? $employee->basic_salary : '0.00') }}" required>
                            <span class="input-group-text employee-form-surface">{{ __('KWD') }}</span>
                        </div>
                        @error('basic_salary') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label small mb-1">{{ __('Hourly Rate') }}</label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0" name="hourly_rate" class="form-control" 
                                   value="{{ old('hourly_rate', $isEdit ? $employee->hourly_rate : '') }}" placeholder="0.00">
                            <span class="input-group-text employee-form-surface">{{ __('KWD') }}</span>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label small mb-1">{{ __('Daily Working Hours') }}</label>
                        <input type="number" step="0.5" min="1" max="24" name="daily_working_hours" class="form-control" 
                               value="{{ old('daily_working_hours', $isEdit ? $employee->daily_working_hours : '8.0') }}">
                    </div>

                    <!-- Bank (Select2) -->
                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-semibold mb-1">{{ __('Bank') }}</label>
                        <select name="bank_id" id="bank_select" class="form-select select2" data-control="select2" data-placeholder="{{ __('Select Bank...') }}">
                            <option value=""></option>
                            @foreach ($banks as $b)
                                <option value="{{ $b->id }}" {{ old('bank_id', $isEdit ? $employee->bank_id : '') == $b->id ? 'selected' : '' }}>
                                    {{ $b->translate(app()->getLocale())?->name ?? $b->translate('ar')?->name ?? $b->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Bank Branch (Select2 with parent filtering) -->
                    <div class="col-12 col-md-6">
                        <label class="form-label small mb-1">{{ __('Bank Branch') }}</label>
                        <select name="bank_branch_id" id="bank_branch_select" class="form-select select2" data-control="select2" data-placeholder="{{ __('Select Bank Branch...') }}">
                            <option value=""></option>
                            @foreach ($bankBranches as $bb)
                                <option value="{{ $bb->id }}" 
                                        data-parent-id="{{ $bb->parent_id ?? '' }}"
                                        {{ old('bank_branch_id', $isEdit ? $employee->bank_branch_id : '') == $bb->id ? 'selected' : '' }}>
                                    {{ $bb->translate(app()->getLocale())?->name ?? $bb->translate('ar')?->name ?? $bb->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label small mb-1">{{ __('Account Holder Name') }}</label>
                        <input type="text" name="account_holder_name" class="form-control" 
                               value="{{ old('account_holder_name', $isEdit ? $employee->account_holder_name : '') }}">
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label small mb-1">{{ __('Account Number') }}</label>
                        <input type="text" name="account_number" class="form-control" 
                               value="{{ old('account_number', $isEdit ? $employee->account_number : '') }}" placeholder="1234567890">
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label small mb-1">{{ __('IBAN') }}</label>
                        <input type="text" name="iban" class="form-control" 
                               value="{{ old('iban', $isEdit ? $employee->iban : '') }}" placeholder="KW00..." dir="ltr">
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label small mb-1">{{ __('Social Insurance Number') }}</label>
                        <input type="text" name="social_insurance_number" class="form-control" 
                               value="{{ old('social_insurance_number', $isEdit ? $employee->social_insurance_number : '') }}">
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label small mb-1">{{ __('Social Insurance Percentage (%)') }}</label>
                        <div class="input-group">
                            <input type="number" step="0.1" min="0" max="100" name="social_insurance_percentage" class="form-control" 
                                   value="{{ old('social_insurance_percentage', $isEdit ? $employee->social_insurance_percentage : '0') }}">
                            <span class="input-group-text employee-form-surface">%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. Salary Allowances (بدلات الراتب) -->
        <div class="card employee-section-card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar avatar-sm">
                        <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-calculator font-20"></i></span>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-heading">{{ __('Salary Allowances') }}</h6>
                        <small class="text-muted d-block mt-1">{{ __('Additional recurring and variable allowances') }}</small>
                    </div>
                </div>
                <button type="button" class="btn btn-outline-primary employee-action-btn d-flex align-items-center gap-1" id="btnAddAllowance">
                    <i class="bx bx-plus font-16"></i>
                    <span>{{ __('Add Allowance') }}</span>
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm align-middle table-hover mb-0" id="allowancesTable">
                        <thead class="employee-form-surface-table">
                            <tr class="text-muted small">
                                <th style="min-width: 180px;">{{ __('Allowance Type') }} <span class="text-danger">*</span></th>
                                <th style="min-width: 130px;">{{ __('Amount / Value') }} <span class="text-danger">*</span></th>
                                <th style="min-width: 140px;">{{ __('Calculation Type') }}</th>
                                <th style="min-width: 130px;">{{ __('Frequency') }}</th>
                                <th style="min-width: 140px;">{{ __('Effective Date') }}</th>
                                <th style="min-width: 160px;">{{ __('Notes') }}</th>
                                <th class="text-center" style="width: 50px;">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody id="allowancesTableBody">
                            @php
                                $existingAllowances = old('allowances', $isEdit && $employee->allowances ? $employee->allowances->toArray() : []);
                            @endphp
                            @if (!empty($existingAllowances))
                                @foreach ($existingAllowances as $aIdx => $allowance)
                                    <tr class="allowance-row" data-index="{{ $aIdx }}">
                                        <td>
                                            <select name="allowances[{{ $aIdx }}][allowance_type_id]" class="form-select select2" data-control="select2" data-placeholder="{{ __('Select Allowance Type...') }}" required>
                                                <option value=""></option>
                                                @foreach ($allowanceTypes as $at)
                                                    <option value="{{ $at->id }}" {{ (isset($allowance['allowance_type_id']) && $allowance['allowance_type_id'] == $at->id) ? 'selected' : '' }}>
                                                        {{ $at->translate(app()->getLocale())?->name ?? $at->translate('ar')?->name ?? $at->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" min="0" name="allowances[{{ $aIdx }}][amount]" class="form-control" value="{{ $allowance['amount'] ?? '0.00' }}" placeholder="0.00" required>
                                        </td>
                                        <td>
                                            <select name="allowances[{{ $aIdx }}][is_percentage]" class="form-select select2" data-control="select2">
                                                <option value="0" {{ empty($allowance['is_percentage']) ? 'selected' : '' }}>{{ __('Fixed Amount') }} ({{ __('KWD') }})</option>
                                                <option value="1" {{ !empty($allowance['is_percentage']) ? 'selected' : '' }}>{{ __('Percentage') }} (%)</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select name="allowances[{{ $aIdx }}][is_recurring]" class="form-select select2" data-control="select2">
                                                <option value="1" {{ !isset($allowance['is_recurring']) || !empty($allowance['is_recurring']) ? 'selected' : '' }}>{{ __('Recurring Monthly') }}</option>
                                                <option value="0" {{ isset($allowance['is_recurring']) && empty($allowance['is_recurring']) ? 'selected' : '' }}>{{ __('One-time / Variable') }}</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="date" name="allowances[{{ $aIdx }}][effective_date]" class="form-control js-flatpickr-date" value="{{ !empty($allowance['effective_date']) ? (is_string($allowance['effective_date']) ? substr($allowance['effective_date'], 0, 10) : $allowance['effective_date']->format('Y-m-d')) : now()->toDateString() }}">
                                        </td>
                                        <td>
                                            <input type="text" name="allowances[{{ $aIdx }}][notes]" class="form-control" value="{{ $allowance['notes'] ?? '' }}" placeholder="{{ __('Notes...') }}">
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-icon btn-outline-danger employee-icon-btn btn-remove-allowance" title="{{ __('Remove') }}">
                                                <i class="bx bx-trash font-16"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                <div id="noAllowancesAlert" class="employee-empty-state text-center py-4 mb-0 {{ !empty($existingAllowances) ? 'd-none' : '' }}">
                    <div class="mb-2"><i class="bx bx-wallet text-muted opacity-50" style="font-size: 2rem;"></i></div>
                    <span class="text-muted small fw-medium">{{ __('No allowances added yet. Click "+ Add Allowance" to add one.') }}</span>
                </div>
            </div>
        </div>

        <!-- 6. Official Documents & Files (الملفات والمستندات) -->
        <div class="card employee-section-card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar avatar-sm">
                        <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-file font-20"></i></span>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-heading">{{ __('Official Documents & Attachments') }}</h6>
                        <small class="text-muted d-block mt-1">{{ __('Passports, IDs, and employee files') }}</small>
                    </div>
                </div>
                <button type="button" class="btn btn-outline-primary employee-action-btn d-flex align-items-center gap-1" id="btnAddDocument">
                    <i class="bx bx-plus font-16"></i>
                    <span>{{ __('Add Document') }}</span>
                </button>
            </div>
            <div class="card-body">
                <div id="documentsContainer" class="d-flex flex-column gap-3">
                    @php
                        $existingDocuments = old('documents', $isEdit && $employee->documents ? $employee->documents->toArray() : []);
                    @endphp
                    @if (!empty($existingDocuments))
                        @foreach ($existingDocuments as $dIdx => $doc)
                            <div class="document-item card border shadow-none employee-form-surface p-3 position-relative" data-index="{{ $dIdx }}">
                                <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-3">
                                    <span class="fw-semibold small text-primary d-flex align-items-center gap-1">
                                        <i class="bx bx-paperclip font-16"></i>
                                        {{ __('Document') }} #<span class="doc-number-label">{{ $dIdx + 1 }}</span>
                                    </span>
                                    <button type="button" class="btn btn-icon btn-outline-danger employee-icon-btn btn-remove-document" title="{{ __('Remove') }}">
                                        <i class="bx bx-trash font-16"></i>
                                    </button>
                                </div>
                                <div class="row g-3">
                                    <div class="col-12 col-md-3">
                                        <label class="form-label text-muted small mb-1">{{ __('Document Type') }} <span class="text-danger">*</span></label>
                                        <select name="documents[{{ $dIdx }}][document_type_id]" class="form-select select2" data-control="select2" data-placeholder="{{ __('Select Document Type...') }}" required>
                                            <option value=""></option>
                                            @foreach ($documentTypes as $dt)
                                                <option value="{{ $dt->id }}" {{ (isset($doc['document_type_id']) && $doc['document_type_id'] == $dt->id) ? 'selected' : '' }}>
                                                    {{ $dt->translate(app()->getLocale())?->name ?? $dt->translate('ar')?->name ?? $dt->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-12 col-md-3">
                                        <label class="form-label text-muted small mb-1">{{ __('Document / ID Number') }}</label>
                                        <input type="text" name="documents[{{ $dIdx }}][document_number]" class="form-control" value="{{ $doc['document_number'] ?? '' }}" placeholder="{{ __('e.g. DOC-12345') }}">
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label class="form-label text-muted small mb-1">{{ __('Upload File') }}</label>
                                        <div class="employee-file-upload d-flex align-items-center gap-2">
                                            <label class="btn btn-outline-secondary employee-action-btn mb-0 d-flex align-items-center gap-1 cursor-pointer">
                                                <i class="bx bx-upload"></i> <span>{{ __('Choose file') }}</span>
                                                <input type="file" name="documents[{{ $dIdx }}][file]" class="d-none employee-file-input" accept=".pdf,.png,.jpg,.jpeg,.doc,.docx">
                                            </label>
                                            <span class="employee-file-name text-muted small text-truncate" style="max-width: 150px;">{{ __('No file chosen') }}</span>
                                        </div>
                                        @if (!empty($doc['file_path']))
                                            <input type="hidden" name="documents[{{ $dIdx }}][file_path]" value="{{ $doc['file_path'] }}">
                                            <input type="hidden" name="documents[{{ $dIdx }}][file_name]" value="{{ $doc['file_name'] ?? '' }}">
                                            <div class="mt-1 d-flex align-items-center gap-2">
                                                <a href="{{ asset('storage/' . $doc['file_path']) }}" target="_blank" class="employee-current-file d-inline-flex align-items-center gap-2 text-decoration-none">
                                                      <div class="file-icon"><i class="bx bx-file"></i></div>
                                                      <div class="d-flex flex-column lh-1">
                                                          <span class="text-heading small fw-medium text-truncate" style="max-width: 150px;">{{ $doc['file_name'] ?? __('View Document') }}</span>
                                                          <span class="text-muted x-small mt-1">{{ __('Current File') }}</span>
                                                      </div>
                                                  </a>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="col-12 col-md-3">
                                        <label class="form-label text-muted small mb-1">{{ __('Issue Date') }}</label>
                                        <input type="date" name="documents[{{ $dIdx }}][issue_date]" class="form-control js-flatpickr-date" value="{{ !empty($doc['issue_date']) ? (is_string($doc['issue_date']) ? substr($doc['issue_date'], 0, 10) : $doc['issue_date']->format('Y-m-d')) : '' }}">
                                    </div>

                                    <div class="col-12 col-md-3">
                                        <label class="form-label text-muted small mb-1">{{ __('Expiry Date') }}</label>
                                        <input type="date" name="documents[{{ $dIdx }}][expiry_date]" class="form-control js-flatpickr-date" value="{{ !empty($doc['expiry_date']) ? (is_string($doc['expiry_date']) ? substr($doc['expiry_date'], 0, 10) : $doc['expiry_date']->format('Y-m-d')) : '' }}">
                                    </div>

                                    <div class="col-12 col-md-3">
                                        <label class="form-label text-muted small mb-1">{{ __('Notification Before Expiry (Days)') }}</label>
                                        <div class="input-group">
                                            <input type="number" name="documents[{{ $dIdx }}][notify_before_days]" class="form-control" min="1" max="365" value="{{ $doc['notify_before_days'] ?? 30 }}">
                                            <span class="input-group-text bg-white">{{ __('days') }}</span>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-3">
                                        <label class="form-label text-muted small mb-1">{{ __('Notes') }}</label>
                                        <input type="text" name="documents[{{ $dIdx }}][notes]" class="form-control" value="{{ $doc['notes'] ?? '' }}" placeholder="{{ __('Notes...') }}">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
                <div id="noDocumentsAlert" class="employee-empty-state text-center py-4 mb-0 {{ !empty($existingDocuments) ? 'd-none' : '' }}">
                    <div class="mb-2"><i class="bx bx-file text-muted opacity-50" style="font-size: 2rem;"></i></div>
                    <span class="text-muted small fw-medium">{{ __('No documents attached yet. Click "+ Add Document" to attach one.') }}</span>
                </div>
            </div>
        </div>

        <!-- 7. Administrative Notes -->
        <div class="card employee-section-card mb-4">
            <div class="card-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar avatar-sm">
                        <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-note font-20"></i></span>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-heading">{{ __('Administrative Notes') }}</h6>
                        <small class="text-muted d-block mt-1">{{ __('Internal HR notes and remarks') }}</small>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label small mb-1">{{ __('Notes [Arabic]') }}</label>
                        <textarea name="notes_ar" rows="3" class="form-control" placeholder="{{ __('Any administrative notes in Arabic...') }}" dir="rtl">{{ old('notes_ar', $isEdit ? $employee->translate('ar')?->notes : '') }}</textarea>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label small mb-1">{{ __('Notes [English]') }}</label>
                        <textarea name="notes_en" rows="3" class="form-control" placeholder="{{ __('Any administrative notes in English...') }}" dir="ltr">{{ old('notes_en', $isEdit ? $employee->translate('en')?->notes : '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

<!-- Allowance Row Template for Dynamic JS Addition -->
<template id="allowanceRowTemplate">
    <tr class="allowance-row" data-index="__INDEX__">
        <td>
            <select name="allowances[__INDEX__][allowance_type_id]" class="form-select select2" data-control="select2" data-placeholder="{{ __('Select Allowance Type...') }}" required>
                <option value=""></option>
                @foreach ($allowanceTypes as $at)
                    <option value="{{ $at->id }}">{{ $at->translate(app()->getLocale())?->name ?? $at->translate('ar')?->name ?? $at->name }}</option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="number" step="0.01" min="0" name="allowances[__INDEX__][amount]" class="form-control" placeholder="0.00" required>
        </td>
        <td>
            <select name="allowances[__INDEX__][is_percentage]" class="form-select select2" data-control="select2">
                <option value="0">{{ __('Fixed Amount') }} ({{ __('KWD') }})</option>
                <option value="1">{{ __('Percentage') }} (%)</option>
            </select>
        </td>
        <td>
            <select name="allowances[__INDEX__][is_recurring]" class="form-select select2" data-control="select2">
                <option value="1">{{ __('Recurring Monthly') }}</option>
                <option value="0">{{ __('One-time / Variable') }}</option>
            </select>
        </td>
        <td>
            <input type="date" name="allowances[__INDEX__][effective_date]" class="form-control js-flatpickr-date" value="{{ now()->toDateString() }}">
        </td>
        <td>
            <input type="text" name="allowances[__INDEX__][notes]" class="form-control" placeholder="{{ __('Notes...') }}">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-icon btn-outline-danger employee-icon-btn btn-remove-allowance" title="{{ __('Remove') }}">
                <i class="bx bx-trash font-16"></i>
            </button>
        </td>
    </tr>
</template>

<!-- Document Card Template for Dynamic JS Addition -->
<template id="documentItemTemplate">
    <div class="document-item card border shadow-none employee-form-surface p-3 position-relative" data-index="__INDEX__">
        <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-3">
            <span class="fw-semibold small text-primary d-flex align-items-center gap-1">
                <i class="bx bx-paperclip font-16"></i>
                {{ __('Document') }} #<span class="doc-number-label">__DOC_NUM__</span>
            </span>
            <button type="button" class="btn btn-icon btn-outline-danger employee-icon-btn btn-remove-document" title="{{ __('Remove') }}">
                <i class="bx bx-trash font-16"></i>
            </button>
        </div>
        <div class="row g-3">
            <div class="col-12 col-md-3">
                <label class="form-label text-muted small mb-1">{{ __('Document Type') }} <span class="text-danger">*</span></label>
                <select name="documents[__INDEX__][document_type_id]" class="form-select select2" data-control="select2" data-placeholder="{{ __('Select Document Type...') }}" required>
                    <option value=""></option>
                    @foreach ($documentTypes as $dt)
                        <option value="{{ $dt->id }}">{{ $dt->translate(app()->getLocale())?->name ?? $dt->translate('ar')?->name ?? $dt->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-md-3">
                <label class="form-label text-muted small mb-1">{{ __('Document / ID Number') }}</label>
                <input type="text" name="documents[__INDEX__][document_number]" class="form-control" placeholder="{{ __('e.g. DOC-12345') }}">
            </div>

            <div class="col-12 col-md-6">
                <label class="form-label text-muted small mb-1">{{ __('Upload File') }} <span class="text-danger">*</span></label>
                <div class="employee-file-upload d-flex align-items-center gap-2">
                                            <label class="btn btn-outline-secondary employee-action-btn mb-0 d-flex align-items-center gap-1 cursor-pointer">
                                                <i class="bx bx-upload"></i> <span>{{ __('Choose file') }}</span>
                                                <input type="file" name="documents[__INDEX__][file]" class="d-none employee-file-input" accept=".pdf,.png,.jpg,.jpeg,.doc,.docx" required>
                                            </label>
                                            <span class="employee-file-name text-muted small text-truncate" style="max-width: 150px;">{{ __('No file chosen') }}</span>
                                        </div>
            </div>

            <div class="col-12 col-md-3">
                <label class="form-label text-muted small mb-1">{{ __('Issue Date') }}</label>
                <input type="date" name="documents[__INDEX__][issue_date]" class="form-control js-flatpickr-date">
            </div>

            <div class="col-12 col-md-3">
                <label class="form-label text-muted small mb-1">{{ __('Expiry Date') }}</label>
                <input type="date" name="documents[__INDEX__][expiry_date]" class="form-control js-flatpickr-date">
            </div>

            <div class="col-12 col-md-3">
                <label class="form-label text-muted small mb-1">{{ __('Notification Before Expiry (Days)') }}</label>
                <div class="input-group">
                    <input type="number" name="documents[__INDEX__][notify_before_days]" class="form-control" min="1" max="365" value="30">
                    <span class="input-group-text bg-white">{{ __('days') }}</span>
                </div>
            </div>

            <div class="col-12 col-md-3">
                <label class="form-label text-muted small mb-1">{{ __('Notes') }}</label>
                <input type="text" name="documents[__INDEX__][notes]" class="form-control" placeholder="{{ __('Notes...') }}">
            </div>
        </div>
    </div>
</template>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const nameArInput = document.getElementById('employeeNameAr');
    const nameEnInput = document.getElementById('employeeNameEn');
    const btnTranslateAction = document.getElementById('btnTranslateNameAction');
    const btnBadgeTranslate = document.getElementById('btnBadgeTranslateName');
    const translateIcon = document.getElementById('translateNameIcon');
    const cardEmployeeName = document.getElementById('cardEmployeeName');

    if (!nameArInput || !nameEnInput) return;

    let debounceTimer = null;
    let isTranslating = false;

    // Track if user manually typed in English name
    nameEnInput.addEventListener('input', function () {
        this.dataset.manuallyEdited = 'true';
    });

    function performTranslation(force = false) {
        const arabicText = nameArInput.value.trim();
        if (!arabicText) return;

        // Don't overwrite if user manually typed unless forced by clicking the button
        if (!force && nameEnInput.value.trim() && nameEnInput.dataset.manuallyEdited === 'true') {
            return;
        }

        const feedbackEl = document.getElementById('aiTranslateFeedback');
        if (feedbackEl) {
            feedbackEl.classList.add('d-none');
            feedbackEl.textContent = '';
        }

        if (isTranslating) return;
        isTranslating = true;

        if (translateIcon) {
            translateIcon.className = 'bx bx-loader-alt bx-spin text-primary';
        }

        fetch('{{ route("ai.translate-name") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ name_ar: arabicText })
        })
        .then(async response => {
            const data = await response.json().catch(() => ({}));
            if (!response.ok || !data.success) {
                const errMsg = data.message || data.error || '{{ __("Failed to generate English name via AI.") }}';
                throw new Error(errMsg);
            }
            return data;
        })
        .then(data => {
            if (data.name_en) {
                nameEnInput.value = data.name_en;
                nameEnInput.dataset.autoFilled = 'true';

                // Update preview card if present
                if (cardEmployeeName) {
                    cardEmployeeName.textContent = arabicText;
                }
            }
        })
        .catch(err => {
            console.error('AI translation error:', err);
            if (feedbackEl) {
                feedbackEl.textContent = err.message;
                feedbackEl.classList.remove('d-none');
            }
        })
        .finally(() => {
            isTranslating = false;
            if (translateIcon) {
                translateIcon.className = 'bx bx-brain';
            }
        });
    }

    // Auto translate when typing stops or on blur
    nameArInput.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        const val = this.value.trim();
        if (cardEmployeeName && val) {
            cardEmployeeName.textContent = val;
        }
        if (val.length >= 3) {
            debounceTimer = setTimeout(() => {
                performTranslation(false);
            }, 750);
        }
    });

    nameArInput.addEventListener('blur', function () {
        clearTimeout(debounceTimer);
        performTranslation(false);
    });

    // Manual click on Translate buttons
    if (btnTranslateAction) {
        btnTranslateAction.addEventListener('click', function () {
            performTranslation(true);
        });
    }

    if (btnBadgeTranslate) {
        btnBadgeTranslate.addEventListener('click', function () {
            performTranslation(true);
        });
    }
});
</script>

