@extends('layouts/blankLayout')

@section('title', __('Complete Your Employee Profile') . ' - ' . config('app.name'))

@section('vendor-style')
    @vite([
        'resources/assets/vendor/libs/select2/select2.scss'
    ])
@endsection

@section('vendor-script')
    @vite([
        'resources/assets/vendor/libs/select2/select2.js'
    ])
@endsection

@section('content')
@php
    $currentLocale = session('locale', 'en');
    $isRtl = $currentLocale === 'ar';
    $isPublicForm = !empty($isPublic) || empty($employee->id);
    $formAction = $isPublicForm ? route('hr.onboarding.portal.public.store') : route('hr.onboarding.portal.update', $employee->onboarding_token);
@endphp

<div class="container-xxl py-4 py-md-5" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-9">

            <!-- Top Bar with Language Switcher -->
            <div class="d-flex align-items-center justify-content-between mb-3 px-1">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-label-primary px-3 py-2 fw-semibold rounded-pill">
                        <i class="bx bx-building me-1"></i> {{ config('app.name', 'ERP Portal') }}
                    </span>
                </div>
                <!-- Language Toggle Button -->
                <div class="dropdown">
                    <a href="{{ route('lang.swap', $isRtl ? 'en' : 'ar') }}" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-2 rounded-pill px-3 shadow-xs">
                        <i class="bx bx-globe font-16"></i>
                        <span class="fw-bold">{{ $isRtl ? 'English' : 'العربية' }}</span>
                    </a>
                </div>
            </div>

            <!-- Welcome Header Card -->
            <div class="card shadow-sm border-0 mb-4 overflow-hidden rounded-4" style="background: linear-gradient(135deg, #696cff 0%, #3f3da3 100%);">
                <div class="card-body p-4 text-white">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar avatar-lg rounded-circle bg-white text-primary d-flex align-items-center justify-content-center shadow" style="width: 60px; height: 60px; font-size: 28px;">
                                <i class="bx bx-user-pin"></i>
                            </div>
                            <div>
                                <h4 class="text-white fw-bold mb-1">
                                    {{ __('Welcome') }}, {{ $employee->translate($currentLocale)?->name ?? $employee->translate('ar')?->name ?? $employee->translate('en')?->name ?? __('New Employee') }}!
                                </h4>
                                <p class="text-white-50 mb-0 small">
                                    {{ __('Please complete and verify your personal, contact, and banking information below.') }}
                                </p>
                            </div>
                        </div>
                        <div class="text-end">
                            @if(!empty($employee->employee_code))
                                <span class="badge bg-white text-primary px-3 py-2 fw-bold fs-6 shadow-xs rounded-pill">
                                    <i class="bx bx-id-card me-1"></i> {{ $employee->employee_code }}
                                </span>
                            @else
                                <span class="badge bg-white text-primary px-3 py-2 fw-bold fs-6 shadow-xs rounded-pill">
                                    <i class="bx bx-user-plus me-1"></i> {{ __('New Employee Registration') }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success d-flex align-items-center gap-2 p-3 shadow-sm rounded-3 mb-4" role="alert">
                    <i class="bx bx-check-circle font-24 text-success"></i>
                    <div>
                        <h6 class="alert-heading fw-bold mb-1">{{ __('Profile Updated Successfully!') }}</h6>
                        <span class="small">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger p-3 shadow-sm rounded-3 mb-4" role="alert">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="bx bx-error-circle font-22"></i>
                        <h6 class="alert-heading fw-bold mb-0">{{ __('Please correct the errors below:') }}</h6>
                    </div>
                    <ul class="mb-0 small ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form -->
            <form action="{{ $formAction }}" method="POST" enctype="multipart/form-data" id="onboardingPortalForm">
                @csrf
                @if(!$isPublicForm)
                    @method('PUT')
                @endif

                <!-- 1. Personal & Civil Info -->
                <div class="card shadow-sm border-0 mb-4 rounded-4">
                    <div class="card-header bg-light py-3 border-bottom rounded-top-4">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bx bx-user text-primary fs-4"></i>
                            <h6 class="fw-bold mb-0 text-heading">{{ __('1. Personal & Civil Information') }}</h6>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold">{{ __('Full Name (Arabic)') }} <span class="text-danger">*</span></label>
                                <input type="text" name="name_ar" class="form-control" value="{{ old('name_ar', $employee->translate('ar')?->name) }}" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold">{{ __('Full Name (English)') }}</label>
                                <input type="text" name="name_en" class="form-control" value="{{ old('name_en', $employee->translate('en')?->name) }}" dir="ltr">
                            </div>

                            <!-- Gender Select -->
                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-semibold">{{ __('Gender') }} <span class="text-danger">*</span></label>
                                <select name="gender" class="form-select select2" data-control="select2" required>
                                    <option value="male" {{ old('gender', is_object($employee->gender) ? $employee->gender->value : $employee->gender) == 'male' ? 'selected' : '' }}>{{ __('Male') }}</option>
                                    <option value="female" {{ old('gender', is_object($employee->gender) ? $employee->gender->value : $employee->gender) == 'female' ? 'selected' : '' }}>{{ __('Female') }}</option>
                                </select>
                            </div>

                            <!-- Marital Status Select -->
                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-semibold">{{ __('Marital Status') }}</label>
                                <select name="marital_status" class="form-select select2" data-control="select2" data-placeholder="{{ __('Select...') }}">
                                    <option value=""></option>
                                    @if(isset($maritalStatuses) && $maritalStatuses->isNotEmpty())
                                        @foreach($maritalStatuses as $ms)
                                            @php
                                                $msName = $ms->translate($currentLocale)?->name ?? $ms->translate('ar')?->name ?? $ms->translate('en')?->name ?? $ms->name ?? $ms->value;
                                                $msVal = $ms->value ?? $msName;
                                            @endphp
                                            <option value="{{ $msVal }}" {{ old('marital_status', $employee->marital_status) == $msVal || old('marital_status', $employee->marital_status) == $msName ? 'selected' : '' }}>
                                                {{ $msName }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="single" {{ old('marital_status', $employee->marital_status) == 'single' ? 'selected' : '' }}>{{ __('Single') }}</option>
                                        <option value="married" {{ old('marital_status', $employee->marital_status) == 'married' ? 'selected' : '' }}>{{ __('Married') }}</option>
                                        <option value="divorced" {{ old('marital_status', $employee->marital_status) == 'divorced' ? 'selected' : '' }}>{{ __('Divorced') }}</option>
                                        <option value="widowed" {{ old('marital_status', $employee->marital_status) == 'widowed' ? 'selected' : '' }}>{{ __('Widowed') }}</option>
                                    @endif
                                </select>
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-semibold">{{ __('Date of Birth') }}</label>
                                <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth', optional($employee->date_of_birth)->format('Y-m-d')) }}">
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-semibold">{{ __('National ID / Civil ID') }} <span class="text-danger">*</span></label>
                                <input type="text" name="national_id" class="form-control" value="{{ old('national_id', $employee->national_id) }}" required dir="ltr">
                            </div>

                            <!-- Nationality Select -->
                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-semibold">{{ __('Nationality') }}</label>
                                <select name="nationality" class="form-select select2" data-control="select2" data-placeholder="{{ __('Select Nationality...') }}">
                                    <option value=""></option>
                                    @if(isset($nationalities) && $nationalities->isNotEmpty())
                                        @foreach($nationalities as $nat)
                                            @php
                                                $natName = $nat->translate($currentLocale)?->name ?? $nat->translate('ar')?->name ?? $nat->translate('en')?->name ?? $nat->name ?? $nat->value;
                                                $natVal = $nat->value ?? $natName;
                                            @endphp
                                            <option value="{{ $natVal }}" {{ old('nationality', $employee->nationality) == $natVal || old('nationality', $employee->nationality) == $natName ? 'selected' : '' }}>
                                                {{ $natName }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="Kuwaiti" {{ old('nationality', $employee->nationality) == 'Kuwaiti' ? 'selected' : '' }}>{{ __('Kuwaiti') }}</option>
                                        <option value="Saudi" {{ old('nationality', $employee->nationality) == 'Saudi' ? 'selected' : '' }}>{{ __('Saudi') }}</option>
                                        <option value="Egyptian" {{ old('nationality', $employee->nationality) == 'Egyptian' ? 'selected' : '' }}>{{ __('Egyptian') }}</option>
                                    @endif
                                </select>
                            </div>

                            <!-- Blood Type Select -->
                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-semibold">{{ __('Blood Type') }}</label>
                                <select name="blood_type" class="form-select select2" data-control="select2" data-placeholder="{{ __('Select...') }}" dir="ltr">
                                    <option value=""></option>
                                    @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bt)
                                        <option value="{{ $bt }}" {{ old('blood_type', $employee->blood_type) == $bt ? 'selected' : '' }}>{{ $bt }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Contact & Address Details -->
                <div class="card shadow-sm border-0 mb-4 rounded-4">
                    <div class="card-header bg-light py-3 border-bottom rounded-top-4">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bx bx-phone-call text-primary fs-4"></i>
                            <h6 class="fw-bold mb-0 text-heading">{{ __('2. Contact & Address Details') }}</h6>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold">{{ __('Phone Number') }} <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $employee->phone) }}" required dir="ltr">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold">{{ __('Alternate Phone') }}</label>
                                <input type="text" name="alternate_phone" class="form-control" value="{{ old('alternate_phone', $employee->alternate_phone) }}" dir="ltr">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold">{{ __('Personal Email') }} <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $employee->email) }}" required dir="ltr">
                            </div>

                            <!-- Governorate / City Select -->
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold">{{ __('Governorate') }}</label>
                                <select name="governorate_id" id="governorate_select" class="form-select select2" data-control="select2" data-placeholder="{{ __('Select Governorate...') }}">
                                    <option value=""></option>
                                    @if(isset($governorates))
                                        @foreach ($governorates as $gov)
                                            <option value="{{ $gov->id }}" {{ old('governorate_id', $employee->governorate_id) == $gov->id ? 'selected' : '' }}>
                                                {{ $gov->translate($currentLocale)?->name ?? $gov->translate('ar')?->name ?? $gov->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold">{{ __('City') }}</label>
                                <input type="text" name="city" class="form-control" value="{{ old('city', $employee->city) }}" placeholder="{{ __('City / Area') }}">
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold">{{ __('Detailed Address') }}</label>
                                <input type="text" name="address_1_ar" class="form-control" placeholder="{{ __('Street, Building, Apartment...') }}" value="{{ old('address_1_ar', $employee->translate('ar')?->address_1) }}">
                            </div>

                            <!-- Emergency Contact Person Section -->
                            <div class="col-12 mt-4">
                                <h6 class="fw-bold text-muted small border-bottom pb-2">
                                    <i class="bx bx-shield-plus me-1 text-danger"></i> {{ __('Emergency Contact Person') }}
                                </h6>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-semibold">{{ __('Emergency Contact Name') }}</label>
                                <input type="text" name="emergency_contact_name" class="form-control" value="{{ old('emergency_contact_name', $employee->emergency_contact_name) }}">
                            </div>

                            <!-- Relationship Select -->
                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-semibold">{{ __('Relationship') }}</label>
                                <select name="emergency_contact_relation" class="form-select select2" data-control="select2" data-placeholder="{{ __('Select Relation...') }}">
                                    <option value=""></option>
                                    @if(isset($relationships) && $relationships->isNotEmpty())
                                        @foreach ($relationships as $rel)
                                            @php
                                                $relName = $rel->translate($currentLocale)?->name ?? $rel->translate('ar')?->name ?? $rel->translate('en')?->name ?? $rel->name ?? $rel->value;
                                                $relVal = $rel->value ?? $relName;
                                            @endphp
                                            <option value="{{ $relVal }}" {{ old('emergency_contact_relation', $employee->emergency_contact_relation) == $relVal ? 'selected' : '' }}>
                                                {{ $relName }}
                                            </option>
                                        @endforeach
                                    @else
                                        @foreach(['Father', 'Mother', 'Brother', 'Sister', 'Spouse', 'Relative'] as $r)
                                            <option value="{{ $r }}" {{ old('emergency_contact_relation', $employee->emergency_contact_relation) == $r ? 'selected' : '' }}>{{ __($r) }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-semibold">{{ __('Emergency Contact Phone') }}</label>
                                <input type="text" name="emergency_contact_phone" class="form-control" value="{{ old('emergency_contact_phone', $employee->emergency_contact_phone) }}" dir="ltr">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Bank & Financial Information -->
                <div class="card shadow-sm border-0 mb-4 rounded-4">
                    <div class="card-header bg-light py-3 border-bottom rounded-top-4">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bx bx-credit-card text-primary fs-4"></i>
                            <h6 class="fw-bold mb-0 text-heading">{{ __('3. Bank & Payment Information') }}</h6>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <!-- Bank Select -->
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold">{{ __('Bank') }}</label>
                                <select name="bank_id" class="form-select select2" data-control="select2" data-placeholder="{{ __('Select Bank...') }}">
                                    <option value=""></option>
                                    @if(isset($banks))
                                        @foreach ($banks as $b)
                                            <option value="{{ $b->id }}" {{ old('bank_id', $employee->bank_id) == $b->id ? 'selected' : '' }}>
                                                {{ $b->translate($currentLocale)?->name ?? $b->translate('ar')?->name ?? $b->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold">{{ __('Account Holder Name') }}</label>
                                <input type="text" name="account_holder_name" class="form-control" value="{{ old('account_holder_name', $employee->account_holder_name) }}" placeholder="{{ __('As shown on bank card/statement') }}">
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold">{{ __('Account Number') }}</label>
                                <input type="text" name="account_number" class="form-control" value="{{ old('account_number', $employee->account_number) }}" dir="ltr">
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold">{{ __('IBAN Number') }}</label>
                                <div class="input-group" dir="ltr">
                                    <span class="input-group-text"><i class="bx bx-wallet"></i></span>
                                    <input type="text" name="iban" class="form-control" value="{{ old('iban', $employee->iban) }}" placeholder="EG... / SA... / KW..." style="text-transform: uppercase;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Required Attachments -->
                <div class="card shadow-sm border-0 mb-4 rounded-4">
                    <div class="card-header bg-light py-3 border-bottom rounded-top-4">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bx bx-file text-primary fs-4"></i>
                            <h6 class="fw-bold mb-0 text-heading">{{ __('4. Document Uploads') }}</h6>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold">{{ __('Personal Photo') }} (Avatar)</label>
                                <input type="file" name="avatar" class="form-control" accept="image/jpeg,image/png,image/jpg,image/webp">
                                <span class="text-muted font-11">{{ __('Accepted: JPG, PNG, WEBP (Max 2MB)') }}</span>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold">{{ __('National ID / Passport Copy') }}</label>
                                <input type="file" name="doc_national_id" class="form-control" accept=".pdf,image/jpeg,image/png">
                                <span class="text-muted font-11">{{ __('Accepted: PDF, JPG, PNG (Max 5MB)') }}</span>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold">{{ __('Graduation Certificate / Degree') }}</label>
                                <input type="file" name="doc_certificate" class="form-control" accept=".pdf,image/jpeg,image/png">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold">{{ __('Resume / CV or Bank Document') }}</label>
                                <input type="file" name="doc_other" class="form-control" accept=".pdf,image/jpeg,image/png">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="card shadow-sm border-0 mb-5 rounded-4">
                    <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-2 text-muted small">
                            <i class="bx bx-shield-quarter text-success fs-4"></i>
                            <span>{{ __('Your data is securely stored and processed in our company system.') }}</span>
                        </div>
                        <button type="submit" class="btn btn-primary px-5 py-3 fw-bold fs-6 shadow rounded-pill">
                            <i class="bx bx-check-double me-1 font-20"></i>
                            {{ __('Submit & Save My Details') }}
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
    window.addEventListener('load', function () {
        const $ = window.jQuery || window.$;
        if (!$ || !$.fn.select2) return;

        const isRtl = $('html').attr('dir') === 'rtl';

        $('.select2, [data-control="select2"]').each(function () {
            $(this).select2({
                theme: 'bootstrap-5',
                placeholder: $(this).data('placeholder') || '',
                allowClear: true,
                dir: isRtl ? 'rtl' : 'ltr',
                width: '100%'
            });
        });
    });
</script>
@endsection
