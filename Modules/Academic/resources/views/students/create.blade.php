
@extends('layouts/contentNavbarLayout')

@section('title', __('Student Registration') . ' - ' . config('variables.templateName'))

@section('page-style')
<style>
/* Clean wizard tabs matching user screenshot */
.wizard-tabs-wrapper {
    display: flex;
    align-items: center;
    background: #ffffff;
    border-bottom: 1.5px solid #e7eaed;
    position: relative;
    padding: 0 0.25rem;
}
.wizard-tabs-scroll-container {
    flex: 1;
    overflow-x: auto;
    overflow-y: hidden;
    scrollbar-width: none;
    -ms-overflow-style: none;
    scroll-behavior: smooth;
}
.wizard-tabs-scroll-container::-webkit-scrollbar {
    display: none;
}
.wizard-tabs {
    display: flex;
    flex-wrap: nowrap;
    border-bottom: none !important;
    margin-bottom: 0;
    gap: 0.35rem;
    padding: 0 0.25rem;
    list-style: none;
}
.wizard-tabs .nav-item {
    flex-shrink: 0;
}
.wizard-tabs .nav-link {
    white-space: nowrap;
    border: none !important;
    border-bottom: 2.5px solid transparent !important;
    border-radius: 0;
    padding: 0.85rem 1.15rem;
    font-size: 0.95rem;
    font-weight: 500;
    color: #697a8d;
    background: transparent !important;
    transition: all 0.2s ease;
    cursor: pointer;
    box-shadow: none !important;
}
.wizard-tabs .nav-link:hover {
    color: #1b5e7d;
}
.wizard-tabs .nav-link.active {
    color: #1b5e7d !important;
    font-weight: 700;
    border-bottom: 2.5px solid #1b5e7d !important;
    background: transparent !important;
}
.wizard-nav-arrow {
    background: transparent;
    border: none;
    color: #8c98a4;
    padding: 0.5rem 0.35rem;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: color 0.2s ease, transform 0.2s ease;
    flex-shrink: 0;
}
.wizard-nav-arrow:hover {
    color: #1b5e7d;
    transform: scale(1.1);
}

/* Uniform padding across all tab panes */
.student-wizard-card .card-body {
    padding: 0 !important;
}
#studentWizardTabsContent .tab-pane {
    padding: 2.25rem 2.5rem;
}
@media (max-width: 768px) {
    #studentWizardTabsContent .tab-pane {
        padding: 1.25rem 0.85rem;
    }
}
.section-title {
    font-weight: 700;
    color: #566a7f;
    position: relative;
    padding-bottom: 0.5rem;
    margin-bottom: 1.25rem;
    border-bottom: 2px dashed #eceef1;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.question-row {
    background-color: #fafbfe;
    border: 1px solid #edf0f5;
    border-radius: 8px;
    padding: 1rem 1.25rem;
    margin-bottom: 0.85rem;
    transition: border-color 0.2s ease;
}
.question-row:hover {
    border-color: #d2d8e6;
    background-color: #f7f9fd;
}
.sticky-wizard-actions {
    position: sticky;
    bottom: 0;
    z-index: 10;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(8px);
    border-top: 1px solid #e7e7e8;
    padding: 1rem 1.5rem;
    border-radius: 0 0 0.5rem 0.5rem;
}
.terms-capsule-badge {
    display: inline-block;
    border: 1px solid #dcdfe6;
    border-radius: 50px;
    padding: 0.65rem 4rem;
    font-size: 1.25rem;
    font-weight: 600;
    color: #4b5563;
    background-color: #ffffff;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.03);
}
.terms-item {
    margin-bottom: 2.25rem;
}
.terms-item-title {
    font-weight: 700;
    font-size: 1.05rem;
    color: #2f3349;
    margin-bottom: 0.5rem;
}
.terms-item-text {
    font-size: 0.95rem;
    color: #6c757d;
    line-height: 1.85;
    margin-bottom: 0;
}
.terms-checkbox-wrap {
    margin-top: 2.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid #eceef1;
}
/* Registration Form Badge matching screenshot */
.reg-form-badge {
    display: inline-block;
    border: 1px solid #d5d9e2;
    border-radius: 50px;
    padding: 0.65rem 4rem;
    background-color: #ffffff;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.02);
    min-width: 320px;
}
.reg-form-badge .reg-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #495057;
    margin-bottom: 2px;
}
.reg-form-badge .reg-years {
    font-size: 1.05rem;
    font-weight: 600;
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.35rem;
}
.reg-form-badge .year-digit {
    display: inline-block;
    border: 1px solid #ced4da;
    border-radius: 4px;
    padding: 0 6px;
    font-size: 0.95rem;
    background: #fbfbfb;
}

/* Photo upload box matching screenshot */
.student-photo-box {
    width: 140px;
    height: 48px;
    border: 1.5px solid #003366;
    border-radius: 4px;
    background: #ffffff;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
    transition: all 0.2s ease;
}
.student-photo-box:hover {
    box-shadow: 0 2px 8px rgba(0, 51, 102, 0.2);
}
.student-photo-box img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Clean section headers */
.clean-form-heading {
    font-size: 1.1rem;
    font-weight: 700;
    color: #555e68;
    margin-top: 2rem;
    margin-bottom: 1.25rem;
}

/* Health Tab Styles matching user screenshot */
.health-capsule-badge {
    border: 1px solid #d9dee3;
    border-radius: 50px;
    padding: 8px 45px;
    display: inline-block;
    font-weight: 700;
    font-size: 1.15rem;
    color: #4b566b;
    background: #ffffff;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}
.health-subtitle {
    font-size: 0.95rem;
    font-weight: 600;
    color: #697a8d;
    text-align: right;
    margin-bottom: 0.65rem;
}
.health-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 12px 0;
    margin-bottom: 0.5rem;
}
.health-table thead th {
    background-color: #2c3548 !important;
    color: #ffffff !important;
    padding: 12px 18px;
    font-size: 0.95rem;
    font-weight: 700;
    text-align: center;
    border: none;
}
.health-table thead th.col-name {
    text-align: right;
    width: 44%;
}
.health-table thead th.col-no {
    width: 12%;
}
.health-table thead th.col-yes {
    text-align: center;
    width: 44%;
}
.health-table tbody tr td {
    background-color: #f6f7f9;
    border-bottom: 1px solid #ebecee;
    padding: 6px 14px;
    vertical-align: middle;
}
.health-table tbody tr:first-child td {
    border-top: none;
}
.health-table tbody tr:last-child td {
    border-bottom: none;
}
.health-table tbody td.col-name {
    text-align: right;
    font-weight: 600;
    color: #4b566b;
    font-size: 0.92rem;
}
.health-table tbody td.col-no {
    text-align: center;
}
.health-table tbody td.col-yes {
    padding: 6px 12px;
}
.health-check-radio {
    width: 19px;
    height: 19px;
    cursor: pointer;
    margin: 0;
    border: 1.5px solid #a1acb8;
}
.health-check-radio:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}
.health-med-input {
    background-color: #ffffff !important;
    border: 1px solid #d9dee3;
    border-radius: 6px;
    height: 38px;
    font-size: 0.88rem;
    box-shadow: none;
}
.health-med-input:focus {
    border-color: #696cff;
    box-shadow: 0 0 0 0.15rem rgba(105, 108, 255, 0.2);
}
.health-q10-box {
    background-color: #f6f7f9;
    border: 1px solid #e2e5e9;
}

/* Medical Declaration Tab matching user screenshot */
.medical-declaration-body {
    max-width: 1000px;
    margin: 0 auto;
}
.medical-declaration-text {
    font-size: 0.98rem;
    color: #566a7f;
    line-height: 2.2;
    text-align: right;
    margin-bottom: 1.5rem;
}
.medical-declaration-checkbox-wrap {
    margin-top: 2.5rem;
    text-align: right;
}

/* Financial Policy Tab matching user screenshot */
.financial-capsule-badge {
    border: 1px solid #d9dee3;
    border-radius: 50px;
    padding: 8px 45px;
    display: inline-block;
    font-weight: 700;
    font-size: 1.15rem;
    color: #4b566b;
    background: #ffffff;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}
.financial-body {
    max-width: 1000px;
    margin: 0 auto;
}
.financial-text {
    font-size: 0.98rem;
    color: #566a7f;
    line-height: 2.2;
    text-align: right;
    margin-bottom: 2rem;
}
.financial-checkbox-wrap {
    margin-top: 2rem;
    text-align: right;
}

/* Emergency & Pickup Tab matching user screenshot */
.emergency-capsule-badge,
.pickup-capsule-badge {
    border: 1px solid #d9dee3;
    border-radius: 50px;
    padding: 8px 45px;
    display: inline-block;
    font-weight: 700;
    font-size: 1.15rem;
    color: #4b566b;
    background: #ffffff;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}

/* Information Forms (نموذج معلومات 1 و 2) matching user screenshot */
.form-info-capsule-badge {
    border: 1px solid #d9dee3;
    border-radius: 50px;
    padding: 8px 50px;
    display: inline-block;
    font-weight: 700;
    font-size: 1.15rem;
    color: #4b566b;
    background: #ffffff;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}
.form-info-item {
    margin-bottom: 2rem;
}
.form-info-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1.5rem;
    margin-bottom: 0.65rem;
}
.form-info-question {
    font-size: 0.95rem;
    font-weight: 600;
    color: #566a7f;
    margin-bottom: 0;
    line-height: 1.6;
}
.form-info-radios {
    display: flex;
    align-items: center;
    gap: 1.75rem;
    flex-shrink: 0;
}
.form-info-radios .form-check-input {
    width: 19px;
    height: 19px;
    cursor: pointer;
    border: 1.5px solid #a1acb8;
}
.form-info-radios .form-check-input:checked {
    background-color: #233446;
    border-color: #233446;
}
.form-info-radios .form-check-label {
    font-size: 0.92rem;
    color: #566a7f;
    font-weight: 500;
    cursor: pointer;
}
.form-info-input {
    width: 100%;
    height: 42px;
    background-color: #ffffff !important;
    border: 1px solid #d9dee3;
    border-radius: 6px;
    font-size: 0.9rem;
    padding: 0.5rem 0.85rem;
    box-shadow: none;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}
.form-info-input:focus {
    border-color: #696cff;
    box-shadow: 0 0 0 0.15rem rgba(105, 108, 255, 0.15);
}

/* Photo Consent Tab matching user screenshot */
.photo-consent-body {
    width: 100%;
}
.photo-consent-intro {
    font-size: 0.98rem;
    color: #566a7f;
    line-height: 2.2;
    text-align: right;
    margin-bottom: 2.5rem;
}
.photo-consent-options {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}
.photo-consent-options .form-check {
    margin-bottom: 0;
    display: flex;
    align-items: center;
    gap: 0.85rem;
    padding: 0;
}
.photo-consent-options .form-check-input {
    width: 20px;
    height: 20px;
    cursor: pointer;
    border: 1.5px solid #a1acb8;
    margin: 0;
    float: none;
    flex-shrink: 0;
}
.photo-consent-options .form-check-input:checked {
    background-color: #233446;
    border-color: #233446;
}
.photo-consent-options .form-check-label {
    font-size: 0.95rem;
    color: #566a7f;
    font-weight: 500;
    cursor: pointer;
    margin-bottom: 0;
    line-height: 1.6;
}

/* Notes Tab matching user screenshot */
.notes-body {
    width: 100%;
}
.notes-textarea {
    width: 100%;
    background-color: #ffffff !important;
    border: 1px solid #d9dee3;
    border-radius: 6px;
    font-size: 0.92rem;
    padding: 0.75rem 1rem;
    box-shadow: none;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}
.notes-textarea:focus {
    border-color: #696cff;
    box-shadow: 0 0 0 0.15rem rgba(105, 108, 255, 0.15);
}
</style>
@endsection

@section('content')
<input type="hidden" name="guardian_context_id" value="{{ $guardianId ?? '' }}" form="formStudentWizard">
<!-- Page Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
    <div class="d-flex flex-column justify-content-center">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted fw-medium small">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('academic.students.index') }}" class="text-muted fw-medium small">{{ __('Students') }}</a></li>
                <li class="breadcrumb-item active text-primary fw-medium small" aria-current="page">{{ __('Student Registration') }}</li>
            </ol>
        </nav>
        <!-- Title -->
        <div class="d-flex align-items-center gap-2 mt-1">
            <i class="icon-base bx bx-user-plus text-primary font-24"></i>
            <h4 class="mb-0 fw-bold text-heading">{{ __('Child and Student Registration Form') }}</h4>
        </div>
    </div>

    <div class="mt-3 mt-md-0 d-flex align-items-center gap-2">
        <a href="{{ route('academic.students.index') }}" class="btn btn-label-secondary btn-sm d-flex align-items-center gap-2">
            <i class="icon-base bx bx-arrow-back font-18"></i>
            <span>{{ __('Back to Students') }}</span>
        </a>
    </div>
</div>

<form id="formStudentWizard" method="POST" action="{{ route('academic.students.store') }}" enctype="multipart/form-data">
    @csrf

    <div class="card shadow-sm border-0 mb-4 student-wizard-card">
        <!-- Wizard Tabs Header matching user screenshot -->
        <div class="wizard-tabs-wrapper position-relative">
            <button type="button" class="wizard-nav-arrow wizard-nav-prev" id="wizardNavPrev" aria-label="Previous">
                <i class="icon-base bx bx-chevron-right font-22"></i>
            </button>
            <div class="wizard-tabs-scroll-container">
                <ul class="nav nav-tabs wizard-tabs" id="studentWizardTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tab-terms-btn" data-bs-toggle="tab" data-bs-target="#tab-terms" type="button" role="tab">
                            {{ __('Terms and Conditions') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-child-btn" data-bs-toggle="tab" data-bs-target="#tab-child" type="button" role="tab">
                            {{ __('Child Registration Form') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-health-btn" data-bs-toggle="tab" data-bs-target="#tab-health" type="button" role="tab">
                            {{ __('Health Information') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-medical-btn" data-bs-toggle="tab" data-bs-target="#tab-medical" type="button" role="tab">
                            {{ __('Medical Declaration') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-financial-btn" data-bs-toggle="tab" data-bs-target="#tab-financial" type="button" role="tab">
                            {{ __('Financial Policy') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-emergency-btn" data-bs-toggle="tab" data-bs-target="#tab-emergency" type="button" role="tab">
                            {{ __('Emergency Contacts') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-pickup-btn" data-bs-toggle="tab" data-bs-target="#tab-pickup" type="button" role="tab">
                            {{ __('Child Pick-up Policy') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-form1-btn" data-bs-toggle="tab" data-bs-target="#tab-form1" type="button" role="tab">
                            {{ __('Information Form 1') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-form2-btn" data-bs-toggle="tab" data-bs-target="#tab-form2" type="button" role="tab">
                            {{ __('Information Form 2') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-photo-btn" data-bs-toggle="tab" data-bs-target="#tab-photo" type="button" role="tab">
                            {{ __('Child Photo Usage Consent') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-notes-btn" data-bs-toggle="tab" data-bs-target="#tab-notes" type="button" role="tab">
                            {{ __('Notes') }}
                        </button>
                    </li>
                </ul>
            </div>
            <button type="button" class="wizard-nav-arrow wizard-nav-next" id="wizardNavNext" aria-label="Next">
                <i class="icon-base bx bx-chevron-left font-22"></i>
            </button>
        </div>

        <!-- Wizard Tabs Content -->
        <div class="card-body p-0">
            <div class="tab-content p-0" id="studentWizardTabsContent">

                <!-- TAB 1: الشروط والأحكام -->
                <div class="tab-pane fade show active" id="tab-terms" role="tabpanel">
                    <!-- Top Pill Header matching screenshot -->
                    <div class="text-center mb-5 mt-3">
                        <div class="terms-capsule-badge">
                            {{ __('Terms and Conditions') }}
                        </div>
                    </div>

                    <!-- Terms Content List -->
                    <div class="terms-body">
                        <!-- 1 -->
                        <div class="terms-item">
                            <h6 class="terms-item-title">{{ __('Acceptance of Terms and Conditions:') }}</h6>
                            <p class="terms-item-text">
                                {{ __('By using the application, you agree to abide by these terms and conditions and all additional policies that may be published from time to time. If you do not agree, please refrain from using the application.') }}
                            </p>
                        </div>

                        <!-- 2 -->
                        <div class="terms-item">
                            <h6 class="terms-item-title">{{ __('Account Registration:') }}</h6>
                            <p class="terms-item-text">
                                {{ __('You must create an account to use the application services. Information provided must be accurate and complete. You are responsible for maintaining confidentiality of your account and password.') }}
                            </p>
                        </div>

                        <!-- 3 -->
                        <div class="terms-item">
                            <h6 class="terms-item-title">{{ __('Use of the Application:') }}</h6>
                            <p class="terms-item-text">
                                {{ __('The application must be used for lawful purposes only. Any activity causing harm to others or the application is prohibited. You are responsible for all activities under your account.') }}
                            </p>
                        </div>

                        <!-- 4 -->
                        <div class="terms-item">
                            <h6 class="terms-item-title">{{ __('Data Privacy:') }}</h6>
                            <p class="terms-item-text">
                                {{ __('We are committed to protecting your privacy. Please review our privacy policy to understand how your data is collected and processed.') }}
                            </p>
                        </div>

                        <!-- 5 -->
                        <div class="terms-item">
                            <h6 class="terms-item-title">{{ __('Intellectual Property Rights:') }}</h6>
                            <p class="terms-item-text">
                                {{ __('All rights relating to the application including logos and trademarks belong to our company. Copying, modifying, or distributing without written permission is prohibited.') }}
                            </p>
                        </div>

                        <!-- Terms Agreement Checkbox -->
                        <div class="terms-checkbox-wrap">
                            <div class="form-check form-check-primary">
                                <input class="form-check-input" type="checkbox" name="terms_accepted" value="1" id="terms_accepted" required>
                                <label class="form-check-label fw-semibold text-heading font-15" for="terms_accepted">
                                    {{ __('I Accept Terms and Conditions') }} <span class="text-danger">*</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 2: إستمارة تسجيل الطفل -->
                <div class="tab-pane fade" id="tab-child" role="tabpanel">

                    <!-- Top Header: Left Photo Box & Center Capsule Badge -->
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                        <!-- Left Photo Box (matching screenshot) -->
                        <div class="d-flex align-items-center gap-2">
                            <div class="student-photo-box" id="student_photo_preview_box" onclick="document.getElementById('student_photo_input').click();" title="{{ __('Select Child Photo') }}">
                                <img id="student_photo_preview" src="" class="d-none">
                                <span id="student_photo_placeholder" class="text-muted small"></span>
                            </div>
                            <i class="icon-base bx bx-camera font-22 text-secondary cursor-pointer" onclick="document.getElementById('student_photo_input').click();"></i>
                            <input type="file" name="photo" id="student_photo_input" class="d-none" accept="image/*">
                        </div>

                        <!-- Center Capsule Badge -->
                        <div class="reg-form-badge text-center mx-auto">
                            <div class="reg-title">{{ __('Child Registration Form') }}</div>
                            <div class="reg-years">
                                <span>202</span><span class="year-digit">6</span>
                                <span class="mx-1">/</span>
                                <span>202</span><span class="year-digit">7</span>
                            </div>
                        </div>

                        <!-- Spacer for balance in flex layout -->
                        <div style="width: 160px;" class="d-none d-md-block"></div>
                    </div>

                    <!-- Hidden or dynamic academic year selector to ensure database saving -->
                    <select name="academic_year_id" id="academic_year_id" class="d-none">
                        @foreach ($years as $year)
                            <option value="{{ $year->id }}" selected>{{ $year->name }}</option>
                        @endforeach
                    </select>

                    <!-- أ. بيانات الطفل -->
                    <div class="clean-form-heading">
                        {{ __('Child Information') }}
                    </div>

                    <!-- Row 1: اسم الطالب (حسب البطاقة المدنية) -->
                    <div class="row">
                        <div class="col-12">
                            <label class="form-label text-muted small fw-semibold">{{ __('Student Name (as in Civil ID)') }}</label>
                            <input type="text" name="name_ar" id="name_ar" class="form-control" placeholder="" required>
                        </div>
                    </div>

                    <!-- Row 2: الحضانه | الفرع | المرحلة الدراسية -->
                    <div class="row">
                        <div class="col-12 col-md-4">
                            <label class="form-label text-muted small fw-semibold">{{ __('Nursery / Institution') }}</label>
                            <select name="institution_id" id="institution_id" class="select2 form-select" required>
                                <option value="">{{ __('Select Nursery...') }}</option>
                                @foreach ($institutions as $inst)
                                    <option value="{{ $inst->id }}">{{ $inst->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label text-muted small fw-semibold">{{ __('Branch') }}</label>
                            <select name="branch_id" id="branch_id" class="select2 form-select">
                                <option value="">{{ __('Select Branch...') }}</option>
                                @foreach ($branches as $br)
                                    <option value="{{ $br->id }}">{{ $br->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label text-muted small fw-semibold">{{ __('Academic Stage') }}</label>
                            <select name="academic_stage_id" id="academic_stage_id" class="select2 form-select">
                                <option value="">{{ __('Select Stage...') }}</option>
                                @foreach ($stages as $stage)
                                    <option value="{{ $stage->id }}">{{ $stage->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Row 3: الفصل | الجنسيه | تاريخ الميلاد -->
                    <div class="row">
                        <div class="col-12 col-md-4">
                            <label class="form-label text-muted small fw-semibold">{{ __('Class') }}</label>
                            <select name="academic_class_id" id="academic_class_id" class="select2 form-select">
                                <option value="">{{ __('Select Class...') }}</option>
                                @foreach ($classes as $cls)
                                    <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label text-muted small fw-semibold">{{ __('Nationality') }}</label>
                            <select name="nationality_id" id="nationality_id" class="select2 form-select">
                                <option value="">{{ __('Select Nationality...') }}</option>
                                @foreach ($nationalities as $nat)
                                    <option value="{{ $nat->id }}">{{ $nat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label text-muted small fw-semibold">{{ __('Date of Birth') }}</label>
                            <input type="date" name="birth_date" id="birth_date" class="form-control" placeholder="mm/dd/yyyy">
                        </div>
                    </div>

                    <!-- Row 4: الرقم المدني | الجنس -->
                    <div class="row align-items-center">
                        <div class="col-12 col-md-6">
                            <label class="form-label text-muted small fw-semibold">{{ __('Civil ID') }}</label>
                            <input type="text" name="civil_id" id="civil_id" class="form-control">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label text-muted small fw-semibold d-block">{{ __('Gender') }}</label>
                            <div class="d-flex align-items-center gap-4 pt-1">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="gender" id="gender_male" value="1" checked required>
                                    <label class="form-check-label text-muted fw-semibold" for="gender_male">{{ __('Male') }}</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="gender" id="gender_female" value="2" required>
                                    <label class="form-check-label text-muted fw-semibold" for="gender_female">{{ __('Female') }}</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ربط ولي الأمر والأسرة -->
                    <div class="clean-form-heading d-flex align-items-center justify-content-between">
                        <span>{{ __('Guardian & Family Link') }}</span>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-muted small fw-semibold d-block mb-2">{{ __('Primary Guardian') }}</label>
                        <div class="d-flex flex-wrap align-items-center gap-4">
                            <div class="form-check">
                                <input class="form-check-input primary-guardian-radio" type="radio" name="primary_guardian_type" id="guardian_type_father" value="father" checked>
                                <label class="form-check-label fw-semibold text-heading" for="guardian_type_father">
                                    <i class="icon-base bx bx-male text-primary me-1"></i> {{ __('Father is the primary guardian') }}
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input primary-guardian-radio" type="radio" name="primary_guardian_type" id="guardian_type_mother" value="mother">
                                <label class="form-check-label fw-semibold text-heading" for="guardian_type_mother">
                                    <i class="icon-base bx bx-female text-danger me-1"></i> {{ __('Mother is the primary guardian') }}
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input primary-guardian-radio" type="radio" name="primary_guardian_type" id="guardian_type_existing" value="existing">
                                <label class="form-check-label fw-semibold text-heading" for="guardian_type_existing">
                                    <i class="icon-base bx bx-user-check text-success me-1"></i> {{ __('Link to an existing registered guardian') }}
                                </label>
                            </div>
                        </div>

                        <!-- اختيار ولي أمر مسجل مسبقاً -->
                        <div id="existing_guardian_section" class="mt-3 pt-2" style="display: none;">
                            <label class="form-label text-muted small fw-semibold">{{ __('Select Existing Guardian') }}</label>
                            <div class="row g-2 align-items-center">
                                <div class="col-12 col-md-9">
                                    <select name="academic_guardian_id" id="academic_guardian_id" class="select2 form-select">
                                        <option value="">{{ __('Select Existing Guardian...') }}</option>
                                        @foreach ($guardians as $g)
                                            <option value="{{ $g->id }}" data-phone="{{ $g->phone }}" data-civil="{{ $g->civil_id }}" {{ (int) ($guardianId ?? 0) === (int) $g->id ? 'selected' : '' }}>
                                                {{ $g->name }} ({{ $g->civil_id ?? $g->phone ?? $g->guardian_code }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-md-3">
                                    <button type="button" class="btn btn-outline-primary w-100" id="btn_fetch_guardian">
                                        <i class="icon-base bx bx-refresh me-1"></i> {{ __('Auto-fill Family Data') }}
                                    </button>
                                </div>
                            </div>
                            <small class="text-muted d-block mt-2">
                                <i class="icon-base bx bx-info-circle me-1"></i> {{ __('Selecting an existing guardian will automatically fill Father, Mother, and Siblings data.') }}
                            </small>
                        </div>
                    </div>

                    <!-- ب. بيانات الأب -->
                    <div class="clean-form-heading">
                        {{ __('Father Information') }}
                    </div>

                    <!-- Row 1: أسم الأب (الأول - الثاني والأخير) -->
                    <div class="row">
                        <div class="col-12">
                            <label class="form-label text-muted small fw-semibold">{{ __('Father Name (First - Middle and Last)') }}</label>
                            <input type="text" name="father[name]" id="father_name" class="form-control">
                        </div>
                    </div>

                    <!-- Row 2: الجنسيه | المهنة -->
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <label class="form-label text-muted small fw-semibold">{{ __('Nationality') }}</label>
                            <select name="father[nationality]" id="father_nationality" class="select2 form-select">
                                <option value="">{{ __('Select Nationality...') }}</option>
                                @foreach ($nationalities as $nat)
                                    <option value="{{ $nat->name }}">{{ $nat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label text-muted small fw-semibold">{{ __('Profession') }}</label>
                            <input type="text" name="father[occupation]" id="father_occupation" class="form-control">
                        </div>
                    </div>

                    <!-- Row 3: جهة العمل | الحالة الإجتماعية -->
                    <div class="row align-items-center">
                        <div class="col-12 col-md-6">
                            <label class="form-label text-muted small fw-semibold d-block">{{ __('Workplace') }}</label>
                            <div class="d-flex align-items-center gap-4 pt-1">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="father[workplace]" id="father_work_gov" value="قطاع حكومي">
                                    <label class="form-check-label text-muted fw-semibold" for="father_work_gov">{{ __('Government Sector') }}</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="father[workplace]" id="father_work_pvt" value="قطاع خاص">
                                    <label class="form-check-label text-muted fw-semibold" for="father_work_pvt">{{ __('Private Sector') }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label text-muted small fw-semibold d-block">{{ __('Marital Status') }}</label>
                            <div class="d-flex align-items-center gap-4 pt-1">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="father[marital_status]" id="father_marital_married" value="2" checked>
                                    <label class="form-check-label text-muted fw-semibold" for="father_marital_married">{{ __('Married') }}</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="father[marital_status]" id="father_marital_divorced" value="3">
                                    <label class="form-check-label text-muted fw-semibold" for="father_marital_divorced">{{ __('Divorced') }}</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="father[marital_status]" id="father_marital_widowed" value="4">
                                    <label class="form-check-label text-muted fw-semibold" for="father_marital_widowed">{{ __('Widowed') }}</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Row 4: الفرع / الشركة -->
                    <div class="row">
                        <div class="col-12">
                            <label class="form-label text-muted small fw-semibold">{{ __('Branch / Company') }}</label>
                            <input type="text" name="father[company_branch]" id="father_company_branch" class="form-control">
                        </div>
                    </div>

                    <!-- Row 5: الرقم المدني -->
                    <div class="row">
                        <div class="col-12">
                            <label class="form-label text-muted small fw-semibold">{{ __('Civil ID') }}</label>
                            <input type="text" name="father[civil_id]" id="father_civil_id" class="form-control">
                        </div>
                    </div>

                    <!-- Row 6: الهاتف | البريد الإلكتروني -->
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <label class="form-label text-muted small fw-semibold">{{ __('Phone') }}</label>
                            <input type="text" name="father[phone]" id="father_phone" class="form-control" dir="ltr" placeholder="965XXXXXXXX">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label text-muted small fw-semibold">{{ __('Email') }}</label>
                            <input type="email" name="father[email]" id="father_email" class="form-control" dir="ltr" placeholder="example@domain.com">
                        </div>
                    </div>

                    <!-- ج. بيانات الأم -->
                    <div class="clean-form-heading">
                        {{ __('Mother Information') }}
                    </div>

                    <!-- Row 1: إسم الأم (الأول - الثاني والأخير) -->
                    <div class="row">
                        <div class="col-12">
                            <label class="form-label text-muted small fw-semibold">{{ __('Mother Name (First - Middle and Last)') }}</label>
                            <input type="text" name="mother[name]" id="mother_name" class="form-control">
                        </div>
                    </div>

                    <!-- Row 2: الجنسيه | المهنة -->
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <label class="form-label text-muted small fw-semibold">{{ __('Nationality') }}</label>
                            <select name="mother[nationality]" id="mother_nationality" class="select2 form-select">
                                <option value="">{{ __('Select Nationality...') }}</option>
                                @foreach ($nationalities as $nat)
                                    <option value="{{ $nat->name }}">{{ $nat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label text-muted small fw-semibold">{{ __('Profession') }}</label>
                            <input type="text" name="mother[occupation]" id="mother_occupation" class="form-control">
                        </div>
                    </div>

                    <!-- Row 3: جهة العمل -->
                    <div class="row align-items-center">
                        <div class="col-12">
                            <label class="form-label text-muted small fw-semibold d-block">{{ __('Workplace') }}</label>
                            <div class="d-flex align-items-center gap-4 pt-1">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="mother[workplace]" id="mother_work_gov" value="قطاع حكومي">
                                    <label class="form-check-label text-muted fw-semibold" for="mother_work_gov">{{ __('Government Sector') }}</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="mother[workplace]" id="mother_work_pvt" value="قطاع خاص">
                                    <label class="form-check-label text-muted fw-semibold" for="mother_work_pvt">{{ __('Private Sector') }}</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Row 4: الفرع / الشركة -->
                    <div class="row">
                        <div class="col-12">
                            <label class="form-label text-muted small fw-semibold">{{ __('Branch / Company') }}</label>
                            <input type="text" name="mother[company_branch]" id="mother_company_branch" class="form-control">
                        </div>
                    </div>

                    <!-- Row 5: الرقم المدني -->
                    <div class="row">
                        <div class="col-12">
                            <label class="form-label text-muted small fw-semibold">{{ __('Civil ID') }}</label>
                            <input type="text" name="mother[civil_id]" id="mother_civil_id" class="form-control">
                        </div>
                    </div>

                    <!-- Row 6: الهاتف | البريد الإلكتروني -->
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <label class="form-label text-muted small fw-semibold">{{ __('Phone') }}</label>
                            <input type="text" name="mother[phone]" id="mother_phone" class="form-control" dir="ltr" placeholder="965XXXXXXXX">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label text-muted small fw-semibold">{{ __('Email') }}</label>
                            <input type="email" name="mother[email]" id="mother_email" class="form-control" dir="ltr" placeholder="example@domain.com">
                        </div>
                    </div>

                    <!-- د. الأشقاء في حضانة أسترو ليدرز ( إن وجد ) -->
                    <div class="clean-form-heading">
                        {{ __('Siblings in Astro Leaders Nursery (if any)') }}
                    </div>

                    <!-- Sibling 1 -->
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <label class="form-label text-muted small fw-semibold">{{ __('Name') }}</label>
                            <input type="text" name="siblings[0][sibling_name]" id="sibling_0_name" class="form-control">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label text-muted small fw-semibold">{{ __('Class') }}</label>
                            <select name="siblings[0][academic_class_id]" id="sibling_0_class" class="select2 form-select">
                                <option value="">{{ __('Select Class...') }}</option>
                                @foreach ($classes as $cls)
                                    <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Sibling 2 -->
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <label class="form-label text-muted small fw-semibold">{{ __('Name') }}</label>
                            <input type="text" name="siblings[1][sibling_name]" id="sibling_1_name" class="form-control">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label text-muted small fw-semibold">{{ __('Class') }}</label>
                            <select name="siblings[1][academic_class_id]" id="sibling_1_class" class="select2 form-select">
                                <option value="">{{ __('Select Class...') }}</option>
                                @foreach ($classes as $cls)
                                    <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                </div>

                <!-- TAB 3: البيانات الصحية -->
                <div class="tab-pane fade" id="tab-health" role="tabpanel">
                    <!-- Top Capsule Badge matching screenshot -->
                    <div class="text-center mb-4 mt-2">
                        <div class="health-capsule-badge">
                            {{ __('Health Information') }}
                        </div>
                    </div>

                    <!-- Subtitle 1 -->
                    <div class="health-subtitle">
                        {{ __('Does your child suffer from any of the following diseases?') }}
                    </div>

                    <!-- Table 1: الأمراض المزمنة -->
                    <div class="table-responsive mb-4">
                        <table class="health-table">
                            <thead>
                                <tr>
                                    <th class="col-name">{{ __('Disease Type') }}</th>
                                    <th class="col-no">{{ __('No') }}</th>
                                    <th class="col-yes">{{ __('If yes, please specify medication') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($diseaseOptions as $disease)
                                    <tr>
                                        <td class="col-name">{{ $disease['label'] }}</td>
                                        <td class="col-no">
                                            <input class="form-check-input health-check-radio health-check-no" type="radio"
                                                name="diseases[{{ $disease['code'] }}][has_disease]"
                                                id="dis_no_{{ $disease['code'] }}" value="0" checked>
                                        </td>
                                        <td class="col-yes">
                                            <div class="d-flex align-items-center gap-2">
                                                <input class="form-check-input health-check-radio health-check-yes" type="radio"
                                                    name="diseases[{{ $disease['code'] }}][has_disease]"
                                                    id="dis_yes_{{ $disease['code'] }}" value="1">
                                                <input type="text" name="diseases[{{ $disease['code'] }}][medication_name]"
                                                    id="dis_med_{{ $disease['code'] }}" class="form-control health-med-input"
                                                    placeholder="">
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Subtitle 2 -->
                    <div class="health-subtitle mt-4">
                        {{ __('If your answer is yes, please provide details') }}
                    </div>

                    <!-- Table 2: الأسئلة الطبية 1 إلى 9 -->
                    <div class="table-responsive mb-4">
                        <table class="health-table">
                            <thead>
                                <tr>
                                    <th class="col-name">&nbsp;</th>
                                    <th class="col-no">{{ __('No') }}</th>
                                    <th class="col-yes">{{ __('If yes, please specify medication') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach (array_slice($medicalQuestions, 0, 9) as $q)
                                    <tr>
                                        <td class="col-name">{{ $q['question'] }}</td>
                                        <td class="col-no">
                                            <input class="form-check-input health-check-radio health-check-no" type="radio"
                                                name="medical_responses[{{ $q['code'] }}][answer]"
                                                id="med_no_{{ $q['code'] }}" value="0" checked>
                                        </td>
                                        <td class="col-yes">
                                            <div class="d-flex align-items-center gap-2">
                                                <input class="form-check-input health-check-radio health-check-yes" type="radio"
                                                    name="medical_responses[{{ $q['code'] }}][answer]"
                                                    id="med_yes_{{ $q['code'] }}" value="1">
                                                <input type="text" name="medical_responses[{{ $q['code'] }}][details]"
                                                    id="med_det_{{ $q['code'] }}" class="form-control health-med-input"
                                                    placeholder="">
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Question 10 (العلاج المستمر / التقرير الطبي) matching screenshot -->
                    @php $q10 = $medicalQuestions[9] ?? null; @endphp
                    @if ($q10)
                        <div class="health-q10-box p-3 rounded mb-4">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-2">
                                <label class="form-label fw-semibold text-heading mb-0">
                                    {{ __('Does your child follow a specific ongoing treatment or regularly take medicine? If yes, please bring a medical report with prescription and meet the school nurse.') }}
                                </label>
                                <div class="d-flex align-items-center gap-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="medical_responses[{{ $q10['code'] }}][answer]" id="med_q10_yes" value="1">
                                        <label class="form-check-label fw-semibold" for="med_q10_yes">{{ __('Yes') }}</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="medical_responses[{{ $q10['code'] }}][answer]" id="med_q10_no" value="0" checked>
                                        <label class="form-check-label fw-semibold" for="med_q10_no">{{ __('No') }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2">
                                <label class="form-label text-muted small fw-semibold">{{ __('Condition / Details') }}</label>
                                <textarea name="medical_responses[{{ $q10['code'] }}][details]" rows="3" class="form-control bg-white" placeholder=""></textarea>
                            </div>
                        </div>
                    @endif

                </div>

                <!-- TAB 4: إقرار طبي -->
                <div class="tab-pane fade" id="tab-medical" role="tabpanel">
                    <!-- Top Capsule Badge matching screenshot -->
                    <div class="text-center mb-5 mt-3">
                        <div class="health-capsule-badge">
                            {{ __('Medical Declaration') }}
                        </div>
                    </div>

                    <!-- Medical Declaration Body matching screenshot -->
                    <div class="medical-declaration-body">
                        <p class="medical-declaration-text">
                            {{ __('I permit the school nurse to administer medicine and treatment to my child if needed, perform first aid, and transport them to hospital in emergencies.') }}
                        </p>
                        <p class="medical-declaration-text">
                            {{ __('I declare that the health information provided is accurate and that I have not withheld any medical conditions concerning my child from the school.') }}
                        </p>
                        <p class="medical-declaration-text">
                            {{ __('I absolve the school of all responsibility regarding any health condition of my child related to a chronic illness or condition not declared by me.') }}
                        </p>

                        <!-- Declaration Checkbox matching screenshot -->
                        <div class="medical-declaration-checkbox-wrap">
                            <div class="form-check form-check-primary d-inline-block">
                                <input class="form-check-input" type="checkbox" name="medical_declaration_accepted" value="1" id="medical_declaration_accepted" checked required>
                                <label class="form-check-label fw-semibold text-heading font-15" for="medical_declaration_accepted">
                                    {{ __('I Agree to Medical Declaration') }} <span class="text-danger">*</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 5: السياسة المالية -->
                <div class="tab-pane fade" id="tab-financial" role="tabpanel">
                    <!-- Top Pill Header matching screenshot -->
                    <div class="text-center mb-5 mt-3">
                        <div class="financial-capsule-badge">
                            {{ __('Financial Policy') }}
                        </div>
                    </div>

                    <!-- Financial Content matching screenshot -->
                    <div class="financial-body">
                        <p class="financial-text">
                            {{ __('I acknowledge that I have reviewed the school\'s financial policy and agree to all its terms, including tuition fees, payment dates, late administrative fees, refund and withdrawal policy if any, and I commit to fulfilling all due financial obligations. I also acknowledge that failure to comply may result in administrative measures by the school.') }}
                        </p>

                        <!-- Financial Agreement Checkbox matching screenshot -->
                        <div class="financial-checkbox-wrap">
                            <div class="form-check form-check-primary d-inline-block">
                                <input class="form-check-input" type="checkbox" name="financial_policy_accepted" value="1" id="financial_policy_accepted" checked required>
                                <label class="form-check-label fw-semibold text-heading font-15" for="financial_policy_accepted">
                                    {{ __('I Agree to Financial Policy') }} <span class="text-danger">*</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 6: حالة الطوارئ -->
                <div class="tab-pane fade" id="tab-emergency" role="tabpanel">
                    <!-- Top Pill Header matching screenshot -->
                    <div class="text-center mb-4 mt-2">
                        <div class="emergency-capsule-badge">
                            {{ __('Emergency Contacts') }}
                        </div>
                    </div>

                    <!-- Heading matching screenshot -->
                    <div class="clean-form-heading mb-3">
                        {{ __('Relationship and Phone Number (if any)') }}
                    </div>

                    <!-- 4 Contact Rows matching screenshot -->
                    @for ($i = 0; $i < 4; $i++)
                        <div class="row">
                            <!-- Right: رقم الموبيل -->
                            <div class="col-12 col-md-6">
                                <label class="form-label text-muted small fw-semibold">{{ __('Mobile Number') }}</label>
                                <input type="text" name="emergency_contacts[{{ $i }}][phone_1]" class="form-control" placeholder="">
                                <input type="hidden" name="emergency_contacts[{{ $i }}][priority]" value="{{ $i + 1 }}">
                            </div>
                            <!-- Left: صله القرابه -->
                            <div class="col-12 col-md-6">
                                <label class="form-label text-muted small fw-semibold">{{ __('Relationship') }}</label>
                                <input type="text" name="emergency_contacts[{{ $i }}][relationship]" class="form-control" placeholder="">
                            </div>
                        </div>
                    @endfor
                </div>

                <!-- TAB 7: سياسة إستلام الطفل -->
                <div class="tab-pane fade" id="tab-pickup" role="tabpanel">
                    <!-- Top Pill Header matching screenshot -->
                    <div class="text-center mb-4 mt-2">
                        <div class="pickup-capsule-badge">
                            {{ __('Child Pick-up Policy') }}
                        </div>
                    </div>

                    <!-- Heading matching screenshot -->
                    <div class="clean-form-heading mb-3">
                        {{ __('Relationship and Phone Number (if any)') }}
                    </div>

                    <!-- 4 Contact Rows matching screenshot -->
                    @for ($i = 0; $i < 4; $i++)
                        <div class="row">
                            <!-- Right: رقم الموبيل -->
                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label text-muted small fw-semibold">{{ __('Mobile Number') }}</label>
                                <input type="text" name="authorized_pickups[{{ $i }}][phone]" value="{{ old("authorized_pickups.{$i}.phone") }}" class="form-control" placeholder="">
                            </div>
                            <!-- Left: صله القرابه -->
                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label text-muted small fw-semibold">{{ __('Relationship') }}</label>
                                <input type="text" name="authorized_pickups[{{ $i }}][relationship]" value="{{ old("authorized_pickups.{$i}.relationship") }}" class="form-control" placeholder="">
                            </div>
                        </div>
                    @endfor

                    <!-- ملاحظات ولي الأمر -->
                    <div class="mt-4 mb-4">
                        <label class="form-label text-muted fw-semibold mb-2">{{ __('Guardian Notes') }}</label>
                        <textarea name="guardian_notes" rows="4" class="form-control notes-textarea guardian-notes-sync" placeholder="">{{ old('guardian_notes') }}</textarea>
                    </div>

                    <!-- ملاحظات الإدارة -->
                    <div class="mb-3">
                        <label class="form-label text-muted fw-semibold mb-2">{{ __('Administration Notes') }}</label>
                        <textarea name="admin_notes" rows="4" class="form-control notes-textarea admin-notes-sync" placeholder="">{{ old('admin_notes') }}</textarea>
                    </div>

                    <input type="hidden" name="pickup_policy_accepted" value="1">
                </div>

                <!-- TAB 8: نموذج معلومات 1 -->
                <div class="tab-pane fade" id="tab-form1" role="tabpanel">
                    <!-- Top Pill Header matching screenshot -->
                    <div class="text-center mb-5 mt-3">
                        <div class="form-info-capsule-badge">
                            {{ __('Information Form 1') }}
                        </div>
                    </div>

                    <div class="form-info-body">
                        @foreach ($form1Questions as $q)
                            <div class="form-info-item">
                                <div class="form-info-header">
                                    <h6 class="form-info-question">{{ $loop->iteration }}. {{ $q['question'] }}</h6>
                                    <div class="form-info-radios">
                                        <div class="form-check m-0">
                                            <input class="form-check-input info-radio-yes" type="radio"
                                                name="information_responses[{{ $q['code'] }}][answer]"
                                                id="info_{{ $q['code'] }}_yes" value="1">
                                            <label class="form-check-label" for="info_{{ $q['code'] }}_yes">{{ __('Yes') }}</label>
                                        </div>
                                        <div class="form-check m-0">
                                            <input class="form-check-input info-radio-no" type="radio"
                                                name="information_responses[{{ $q['code'] }}][answer]"
                                                id="info_{{ $q['code'] }}_no" value="0" checked>
                                            <label class="form-check-label" for="info_{{ $q['code'] }}_no">{{ __('No') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <input type="text" name="information_responses[{{ $q['code'] }}][details]"
                                    class="form-control form-info-input"
                                    placeholder="">
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- TAB 9: نموذج معلومات 2 -->
                <div class="tab-pane fade" id="tab-form2" role="tabpanel">
                    <!-- Top Pill Header matching screenshot -->
                    <div class="text-center mb-5 mt-3">
                        <div class="form-info-capsule-badge">
                            {{ __('Information Form 2') }}
                        </div>
                    </div>

                    <div class="form-info-body">
                        @foreach ($form2Questions as $q)
                            <div class="form-info-item">
                                <div class="form-info-header">
                                    <h6 class="form-info-question">{{ $q['code'] }}. {{ $q['question'] }}</h6>
                                    <div class="form-info-radios">
                                        <div class="form-check m-0">
                                            <input class="form-check-input info-radio-yes" type="radio"
                                                name="information_responses[{{ $q['code'] }}][answer]"
                                                id="info_{{ $q['code'] }}_yes" value="1">
                                            <label class="form-check-label" for="info_{{ $q['code'] }}_yes">{{ __('Yes') }}</label>
                                        </div>
                                        <div class="form-check m-0">
                                            <input class="form-check-input info-radio-no" type="radio"
                                                name="information_responses[{{ $q['code'] }}][answer]"
                                                id="info_{{ $q['code'] }}_no" value="0" checked>
                                            <label class="form-check-label" for="info_{{ $q['code'] }}_no">{{ __('No') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <input type="text" name="information_responses[{{ $q['code'] }}][details]"
                                    class="form-control form-info-input"
                                    placeholder="">
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- TAB 10: إقرار صورة الطفل -->
                <div class="tab-pane fade" id="tab-photo" role="tabpanel">
                    <!-- Top Pill Header matching screenshot -->
                    <div class="text-center mb-5 mt-3">
                        <div class="health-capsule-badge">
                            {{ __('Child Photo Usage Consent') }}
                        </div>
                    </div>

                    <div class="photo-consent-body">
                        <p class="photo-consent-intro">
                            {{ __('Under this declaration, Astro Leaders Nursery has the right to use photos and videos of the child during the school day on nursery website and social media.') }}
                        </p>

                        <div class="photo-consent-options">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="photo_consent" id="consent_yes" value="1">
                                <label class="form-check-label" for="consent_yes">
                                    {{ __('I allow Astro Leaders Nursery to use photos and videos of my child for use on social media.') }}
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="photo_consent" id="consent_no" value="0" checked>
                                <label class="form-check-label" for="consent_no">
                                    {{ __('I do not allow Astro Leaders Nursery to use photos and videos of my child for use on social media.') }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 11: ملاحظات -->
                <div class="tab-pane fade" id="tab-notes" role="tabpanel">
                    <!-- Top Pill Header matching screenshot -->
                    <div class="text-center mb-5 mt-3">
                        <div class="health-capsule-badge">
                            {{ __('Notes') }}
                        </div>
                    </div>

                    <div class="notes-body">
                        <!-- ملاحظات ولي الأمر -->
                        <div class="mb-4">
                            <label class="form-label text-muted fw-semibold mb-2">{{ __('Guardian Notes') }}</label>
                            <textarea name="guardian_notes" rows="6" class="form-control notes-textarea guardian-notes-sync" placeholder="">{{ old('guardian_notes') }}</textarea>
                        </div>

                        <!-- ملاحظات الإدارة -->
                        <div class="mb-3">
                            <label class="form-label text-muted fw-semibold mb-2">{{ __('Administration Notes') }}</label>
                            <textarea name="admin_notes" rows="6" class="form-control notes-textarea admin-notes-sync" placeholder="">{{ old('admin_notes') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            </div>
        </div>

        <!-- Sticky Wizard Actions -->
        <div class="sticky-wizard-actions d-flex justify-content-end align-items-center gap-2">
            <a href="{{ route('academic.students.index') }}" class="btn btn-label-secondary d-flex align-items-center gap-1">
                <i class="icon-base bx bx-x font-18"></i>
                <span>{{ __('Cancel') }}</span>
            </a>
            <button type="submit" id="btn_submit_student" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm">
                <i class="icon-base bx bx-check-circle font-18"></i>
                <span>{{ __('Save Student Data') }}</span>
            </button>
        </div>
    </div>
</form>
@endsection

@section('page-script')
<script>
(function () {
    'use strict';

    function initStudentWizard() {
        const $ = window.jQuery || window.$;
        if (!$) {
            setTimeout(initStudentWizard, 50);
            return;
        }

        // Apply Select2
        if ($.fn.select2) {
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%',
                dir: $('html').attr('dir') === 'rtl' ? 'rtl' : 'ltr'
            });
        }

        // Toast Helper
        function showToast(type, message, title = '') {
            const mappedType = type === 'danger' ? 'error' : type;
            if (window.toastr && typeof window.toastr[mappedType] === 'function') {
                window.toastr[mappedType](message, title);
            } else if (window.notify && typeof window.notify[mappedType] === 'function') {
                window.notify[mappedType](message, title);
            }
        }

        // Photo preview handler
        $('#student_photo_input').on('change', function () {
            const input = this;
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    $('#student_photo_preview').attr('src', e.target.result).removeClass('d-none');
                    $('#student_photo_placeholder').addClass('d-none');
                };
                reader.readAsDataURL(input.files[0]);
            }
        });

        // Tab Navigation Logic
        const tabs = $('#studentWizardTabs button[data-bs-toggle="tab"]');
        let currentTabIndex = 0;

        function updateNavButtons(index) {
            $('#btn_prev_tab').prop('disabled', index === 0);
            if (index === tabs.length - 1) {
                $('#btn_next_tab').addClass('d-none');
            } else {
                $('#btn_next_tab').removeClass('d-none');
            }
        }

        $('#btn_next_tab').on('click', function () {
            if (currentTabIndex < tabs.length - 1) {
                currentTabIndex++;
                new bootstrap.Tab(tabs[currentTabIndex]).show();
                updateNavButtons(currentTabIndex);
            }
        });

        $('#btn_prev_tab').on('click', function () {
            if (currentTabIndex > 0) {
                currentTabIndex--;
                new bootstrap.Tab(tabs[currentTabIndex]).show();
                updateNavButtons(currentTabIndex);
            }
        });

        tabs.on('shown.bs.tab', function (e) {
            currentTabIndex = tabs.index(e.target);
            updateNavButtons(currentTabIndex);
        });

        // Guardian Link & Auto-fill Family
        $('input[name="primary_guardian_type"]').on('change', function () {
            if ($(this).val() === 'existing') {
                $('#existing_guardian_section').slideDown(200);
            } else {
                $('#existing_guardian_section').slideUp(200);
            }
        });

        function populateGuardianFamily(guardianId) {
            if (!guardianId) return;
            const btn = $('#btn_fetch_guardian');
            const origHtml = btn.html();
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> {{ __('Loading...') }}');

            const url = '{{ route("academic.students.ajax.guardian-family", ":id") }}'.replace(':id', guardianId);
            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                headers: { 'Accept': 'application/json' },
                success: function (res) {
                    btn.prop('disabled', false).html(origHtml);
                    if (!res.success) {
                        showToast('error', res.message || '{{ __('Failed to load family data.') }}');
                        return;
                    }

                    // Populate Father
                    if (res.father) {
                        $('#father_name').val(res.father.name || '');
                        $('#father_civil_id').val(res.father.civil_id || '');
                        $('#father_occupation').val(res.father.occupation || '');
                        $('#father_company_branch').val(res.father.company_branch || '');
                        $('#father_phone').val(res.father.phone || '');
                        $('#father_email').val(res.father.email || '');
                        if (res.father.nationality) {
                            $('#father_nationality').val(res.father.nationality).trigger('change');
                        }
                        if (res.father.workplace) {
                            $(`input[name="father[workplace]"][value="${res.father.workplace}"]`).prop('checked', true);
                        }
                        if (res.father.marital_status) {
                            $(`input[name="father[marital_status]"][value="${res.father.marital_status}"]`).prop('checked', true);
                        }
                    }

                    // Populate Mother
                    if (res.mother) {
                        $('#mother_name').val(res.mother.name || '');
                        $('#mother_civil_id').val(res.mother.civil_id || '');
                        $('#mother_occupation').val(res.mother.occupation || '');
                        $('#mother_company_branch').val(res.mother.company_branch || '');
                        $('#mother_phone').val(res.mother.phone || '');
                        $('#mother_email').val(res.mother.email || '');
                        if (res.mother.nationality) {
                            $('#mother_nationality').val(res.mother.nationality).trigger('change');
                        }
                        if (res.mother.workplace) {
                            $(`input[name="mother[workplace]"][value="${res.mother.workplace}"]`).prop('checked', true);
                        }
                    }

                    // Populate Siblings
                    if (res.siblings && res.siblings.length > 0) {
                        if (res.siblings[0]) {
                            $('#sibling_0_name').val(res.siblings[0].sibling_name || '');
                            if (res.siblings[0].academic_class_id) {
                                $('#sibling_0_class').val(res.siblings[0].academic_class_id).trigger('change');
                            }
                        }
                        if (res.siblings[1]) {
                            $('#sibling_1_name').val(res.siblings[1].sibling_name || '');
                            if (res.siblings[1].academic_class_id) {
                                $('#sibling_1_class').val(res.siblings[1].academic_class_id).trigger('change');
                            }
                        }
                    }

                    showToast('success', '{{ __('Family data loaded successfully.') }}');
                },
                error: function () {
                    btn.prop('disabled', false).html(origHtml);
                    showToast('error', '{{ __('Failed to load family data.') }}');
                }
            });
        }

        $('#academic_guardian_id').on('change', function () {
            const gid = $(this).val();
            if (gid) {
                populateGuardianFamily(gid);
            }
        });

        $('#btn_fetch_guardian').on('click', function () {
            const gid = $('#academic_guardian_id').val();
            if (gid) {
                populateGuardianFamily(gid);
            } else {
                showToast('warning', '{{ __('Please select a guardian first.') }}');
            }
        });

        // If opened from a guardian's students list, lock the context and
        // load that guardian's family data immediately.
        const guardianContextId = @json($guardianId ?? null);
        if (guardianContextId) {
            $('#guardian_type_existing').prop('checked', true).trigger('change');
            $('#academic_guardian_id').val(String(guardianContextId)).trigger('change');
        }

        // Dynamic Siblings Repeater
        let siblingIndex = 0;
        function addSiblingRow() {
            siblingIndex++;
            const row = `
                <div class="row g-2 align-items-center mb-2 sibling-row p-2 border rounded bg-white" id="sibling_row_${siblingIndex}">
                    <div class="col-12 col-md-5">
                        <input type="text" name="siblings[${siblingIndex}][sibling_name]" class="form-control form-control-sm" placeholder="{{ __('Sibling Name') }}">
                    </div>
                    <div class="col-12 col-md-5">
                        <input type="text" name="siblings[${siblingIndex}][class_name]" class="form-control form-control-sm" placeholder="{{ __('Classroom in Nursery') }}">
                    </div>
                    <div class="col-12 col-md-2 text-end">
                        <button type="button" class="btn btn-label-danger btn-sm w-100 btn-remove-sibling">
                            <i class="icon-base bx bx-trash font-16"></i> {{ __('Delete') }}
                        </button>
                    </div>
                </div>
            `;
            $('#siblings_container').append(row);
        }

        $('#btn_add_sibling').on('click', addSiblingRow);
        $(document).on('click', '.btn-remove-sibling', function () {
            $(this).closest('.sibling-row').remove();
        });

        // Add 1 default sibling row
        addSiblingRow();

        // Sync Notes Textareas between Tab 7 & Tab 11
        $(document).on('input', '.guardian-notes-sync', function () {
            const val = $(this).val();
            $('.guardian-notes-sync').each(function () {
                if ($(this).val() !== val) {
                    $(this).val(val);
                }
            });
        });

        $(document).on('input', '.admin-notes-sync', function () {
            const val = $(this).val();
            $('.admin-notes-sync').each(function () {
                if ($(this).val() !== val) {
                    $(this).val(val);
                }
            });
        });

        // Horizontal scroll for wizard tabs
        $('#wizardNavNext').on('click', function () {
            const container = document.querySelector('.wizard-tabs-scroll-container');
            if (container) {
                const isRtl = $('html').attr('dir') === 'rtl';
                container.scrollBy({ left: isRtl ? -220 : 220, behavior: 'smooth' });
            }
        });
        $('#wizardNavPrev').on('click', function () {
            const container = document.querySelector('.wizard-tabs-scroll-container');
            if (container) {
                const isRtl = $('html').attr('dir') === 'rtl';
                container.scrollBy({ left: isRtl ? 220 : -220, behavior: 'smooth' });
            }
        });
        $('.wizard-tabs .nav-link').on('shown.bs.tab', function () {
            this.scrollIntoView({ behavior: 'smooth', inline: 'nearest', block: 'nearest' });
        });

        // Auto-check Yes radio when typing medication or details
        $(document).on('input', '.health-med-input', function () {
            if ($(this).val().trim().length > 0) {
                $(this).closest('td').find('.health-check-yes').prop('checked', true);
            }
        });

        // AJAX Form Submit
        $('#formStudentWizard').on('submit', function (e) {
            e.preventDefault();
            const form = $(this);
            const submitBtn = $('#btn_submit_student');
            const originalBtnHtml = submitBtn.html();

            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> {{ __('Saving...') }}');

            const formData = new FormData(this);

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                },
                success: function (res) {
                    submitBtn.prop('disabled', false).html(originalBtnHtml);
                    showToast('success', res.message || '{{ __('Student registered successfully.') }}');
                    if (res.redirect) {
                        setTimeout(() => {
                            window.location.href = res.redirect;
                        }, 800);
                    }
                },
                error: function (xhr) {
                    submitBtn.prop('disabled', false).html(originalBtnHtml);
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        let errorMsg = '';
                        for (const field in errors) {
                            errorMsg += errors[field][0] + '\n';
                        }
                        showToast('error', errorMsg || '{{ __('Please review the required fields.') }}');
                    } else {
                        const msg = xhr.responseJSON?.message || '{{ __('An error occurred while saving registration form.') }}';
                        showToast('error', msg);
                    }
                }
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initStudentWizard);
    } else {
        initStudentWizard();
    }
})();
</script>
@endsection
