@extends('employeeportal::layouts.master')

@section('title', __('Leave Request'))

@push('styles')
<!-- Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">
<!-- intl-tel-input CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/css/intlTelInput.css" />

<style>
    .leave-page-container {
        padding-bottom: 30px;
    }

    /* Top Page Header */
    .leave-nav-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 14px;
        position: relative;
    }

    .leave-back-btn {
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

    .leave-back-btn:active {
        background: #EEF5F8;
        transform: scale(0.95);
    }

    .leave-header-title {
        font-family: 'Tajawal', sans-serif;
        font-size: 17px;
        font-weight: 800;
        color: #00334B;
        margin: 0;
        text-align: center;
        flex: 1;
    }

    .leave-nav-placeholder {
        width: 38px;
    }

    /* 1. Header Banner Card - Exactly matching Figma Spec */
    .leave-hero-card {
        width: 100%;
        min-height: 158px;
        border-radius: 16px;
        padding: 16px;
        background: #002A44;
        box-shadow: 0px 8px 10px -6px rgba(0, 0, 0, 0.1), 0px 20px 25px -5px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 18px;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        box-sizing: border-box;
    }

    .leave-hero-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .leave-archive-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.16);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        color: #E2E8F0;
        text-decoration: none;
    }

    .leave-archive-pill .dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #FDBB22;
    }

    .leave-hero-title-group {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .leave-hero-title {
        font-family: 'Tajawal', sans-serif;
        font-size: 18px;
        font-weight: 800;
        color: #FEAE2C;
        margin: 0;
    }

    .leave-hero-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: rgba(254, 174, 44, 0.16);
        border: 1px solid rgba(254, 174, 44, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #FEAE2C;
        font-size: 24px;
    }

    .leave-hero-bottom {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        padding: 10px 14px;
    }

    .leave-balance-side {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .leave-balance-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: rgba(254, 174, 44, 0.2);
        color: #FEAE2C;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 19px;
    }

    .leave-balance-text {
        display: flex;
        flex-direction: column;
    }

    .leave-balance-label {
        font-size: 10.5px;
        color: #94A3B8;
        font-weight: 600;
    }

    .leave-balance-val {
        font-size: 16px;
        font-weight: 900;
        color: #FEAE2C;
        line-height: 1.2;
    }

    .leave-year-side {
        text-align: left;
    }

    [dir="ltr"] .leave-year-side {
        text-align: right;
    }

    .leave-year-label {
        font-size: 9.5px;
        color: #94A3B8;
    }

    .leave-year-val {
        font-size: 11px;
        color: #FFFFFF;
        font-weight: 700;
    }

    /* 2. Section Card Components */
    .leave-section-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 18px 16px;
        margin-bottom: 14px;
        box-shadow: 0 3px 15px rgba(0, 0, 0, 0.03);
        border: 1px solid rgba(0, 51, 75, 0.05);
    }

    .leave-section-title {
        font-family: 'Tajawal', sans-serif;
        font-size: 14.5px;
        font-weight: 800;
        color: #002A44;
        margin: 0 0 12px 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .leave-section-title i {
        font-size: 18px;
        color: #FEAE2C;
    }

    .leave-field-label {
        display: block;
        font-family: 'Tajawal', sans-serif;
        font-size: 12px;
        font-weight: 700;
        color: #002A44;
        margin-bottom: 6px;
    }

    .leave-select-control {
        width: 100%;
        height: 48px;
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        border-radius: 12px;
        padding: 0 14px;
        font-family: 'Tajawal', sans-serif;
        font-size: 13.5px;
        font-weight: 700;
        color: #002A44;
        outline: none;
        transition: all 0.2s;
        appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='%2364748B' viewBox='0 0 24 24'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: left 12px center;
        background-size: 18px;
    }

    [dir="ltr"] .leave-select-control {
        background-position: right 12px center;
    }

    .leave-select-control:focus {
        background-color: #ffffff;
        border-color: #002A44;
        box-shadow: 0 0 0 3px rgba(0, 42, 68, 0.08);
    }

    .leave-hint-box {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-top: 8px;
        font-size: 10.5px;
        color: #92400E;
        font-weight: 600;
    }

    .leave-hint-box i {
        font-size: 13px;
    }

    /* Date Period Pickers (2 Columns with Labels Outside) */
    .date-pickers-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 14px;
    }

    .date-field-group {
        display: flex;
        flex-direction: column;
    }

    .date-input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .date-input-wrapper i {
        position: absolute;
        right: 14px;
        font-size: 18px;
        color: #64748B;
        pointer-events: none;
    }

    [dir="ltr"] .date-input-wrapper i {
        right: auto;
        left: 14px;
    }

    .flatpickr-custom-input {
        width: 100%;
        height: 48px;
        background: #F0F4F8;
        border: 1px solid #E2E8F0;
        border-radius: 12px;
        padding: 0 40px 0 12px;
        font-family: 'Tajawal', sans-serif;
        font-size: 13px;
        font-weight: 800;
        color: #002A44;
        outline: none;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    [dir="ltr"] .flatpickr-custom-input {
        padding: 0 12px 0 40px;
    }

    .flatpickr-custom-input:focus {
        background: #ffffff;
        border-color: #002A44;
        box-shadow: 0 0 0 3px rgba(0, 42, 68, 0.08);
    }

    /* Total Working Days Box */
    .total-days-badge-card {
        background: #F0F4F8;
        border-radius: 12px;
        padding: 12px 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .days-count-pill {
        background: #002A44;
        color: #FEAE2C;
        font-size: 13.5px;
        font-weight: 900;
        padding: 5px 14px;
        border-radius: 8px;
        box-shadow: 0 2px 6px rgba(0, 42, 68, 0.2);
        transition: all 0.3s ease;
    }

    .days-summary-title {
        font-size: 12.5px;
        font-weight: 800;
        color: #002A44;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    /* Emergency Phone Field */
    .emergency-phone-input {
        width: 100%;
        height: 48px;
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        border-radius: 12px;
        padding: 0 14px;
        font-family: inherit;
        font-size: 13px;
        font-weight: 600;
        color: #002A44;
        outline: none;
        transition: all 0.2s;
    }

    .emergency-phone-input:focus {
        background: #ffffff;
        border-color: #002A44;
        box-shadow: 0 0 0 3px rgba(0, 42, 68, 0.08);
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

    .iti--separate-dial-code input[type="tel"],
    .emergency-phone-input {
        padding-left: 118px !important;
        padding-right: 14px !important;
        direction: ltr !important;
        text-align: left !important;
    }

    /* Textarea */
    .leave-textarea {
        width: 100%;
        min-height: 75px;
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        border-radius: 12px;
        padding: 12px;
        font-family: 'Tajawal', sans-serif;
        font-size: 12px;
        color: #1E293B;
        outline: none;
        resize: none;
        transition: all 0.2s;
    }

    .leave-textarea:focus {
        background: #ffffff;
        border-color: #002A44;
        box-shadow: 0 0 0 3px rgba(0, 42, 68, 0.08);
    }

    /* Upload Area */
    .leave-upload-dropzone {
        background: #F0F4F8;
        border: 1.5px dashed #CBD5E1;
        border-radius: 14px;
        padding: 18px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
    }

    .leave-upload-dropzone:hover {
        background: #E2E8F0;
        border-color: #002A44;
    }

    .upload-cloud-icon {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: #E0E7FF;
        color: #3730A3;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin: 0 auto 6px auto;
    }

    .upload-title {
        font-size: 12px;
        font-weight: 800;
        color: #002A44;
        margin-bottom: 2px;
    }

    .upload-sub {
        font-size: 10px;
        color: #64748B;
    }

    /* 3. Bottom Submit Button - Exactly matching Figma Spec */
    .btn-submit-leave-custom {
        width: 100%;
        height: 56px;
        border-radius: 16px;
        background: #002A44;
        color: #FEAE2C;
        border: none;
        font-family: 'Tajawal', sans-serif;
        font-size: 16px;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8.01px;
        cursor: pointer;
        box-shadow: 0 6px 20px rgba(0, 42, 68, 0.25);
        transition: all 0.2s ease;
        text-decoration: none;
        margin-top: 20px;
    }

    .btn-submit-leave-custom:active {
        transform: scale(0.98);
        background: #001f33;
    }
</style>
@endpush

@section('content')
<div class="leave-page-container">
    <!-- Top Navigation Header -->
    <div class="leave-nav-header">
        <a href="{{ route('portal.requests') }}" class="leave-back-btn" title="{{ __('Back') }}">
            <i class="bx {{ app()->getLocale() === 'ar' ? 'bx-right-arrow-alt' : 'bx-left-arrow-alt' }}"></i>
        </a>
        <h1 class="leave-header-title">{{ __('Leave Request') }}</h1>
        <div class="leave-nav-placeholder"></div>
    </div>

    <!-- 1. Header Banner Card (Figma Spec) -->
    <div class="leave-hero-card">
        <div class="leave-hero-top">
            <div class="leave-hero-title-group">
                <div class="leave-hero-icon-box">
                    <i class="bx bxs-rocket"></i>
                </div>
                <h2 class="leave-hero-title">{{ __('New Leave Request') }}</h2>
            </div>

            <a href="javascript:void(0)" class="leave-archive-pill">
                <span class="dot"></span>
                <span>{{ __('Leave Archive') }}</span>
            </a>
        </div>

        <div class="leave-hero-bottom">
            <div class="leave-balance-side">
                <div class="leave-balance-icon">
                    <i class="bx bxs-calendar-check"></i>
                </div>
                <div class="leave-balance-text">
                    <span class="leave-balance-label">{{ __('Available Leave Balance') }}</span>
                    <span class="leave-balance-val" id="headerBalanceCount">{{ $annualBalance }} {{ __('Days Remaining') }}</span>
                </div>
            </div>

            <div class="leave-year-side">
                <div class="leave-year-label">{{ __('Fiscal Year') }}</div>
                <div class="leave-year-val">{{ date('Y') }} - {{ date('Y') + 1 }}</div>
            </div>
        </div>
    </div>

    <!-- Leave Submission Form -->
    <form action="{{ route('portal.leaves.store') }}" method="POST" enctype="multipart/form-data" id="leaveSubmitForm">
        @csrf

        <!-- 2. Leave Type Card -->
        <div class="leave-section-card">
            <h3 class="leave-section-title">{{ __('Requested Leave Type') }}</h3>
            <select name="leave_type" id="portalLeaveTypeSelect" class="leave-select-control" required onchange="recalculateLeaveDays()">
                @foreach($leaveTypes as $type)
                    <option value="{{ $type->value }}" {{ $type->value === 'annual' ? 'selected' : '' }}>
                        {{ $type->label() }}
                    </option>
                @endforeach
            </select>
            <div class="leave-hint-box" id="leaveTypeHintBox">
                <i class="bx bx-info-circle"></i>
                <span id="leaveTypeHintText">{{ __('Annual leave is deducted directly from your cumulative annual balance.') }}</span>
            </div>
        </div>

        <!-- 3. Time Period Card -->
        <div class="leave-section-card">
            <h3 class="leave-section-title">
                <span>{{ __('Time Period') }}</span>
                <i class="bx bx-calendar"></i>
            </h3>

            <!-- Date Pickers Grid with Labels Outside Input -->
            <div class="date-pickers-grid">
                <!-- Start Date -->
                <div class="date-field-group">
                    <label class="leave-field-label" for="leaveStartDate">{{ __('Start Date') }}</label>
                    <div class="date-input-wrapper">
                        <input type="text"
                               name="start_date"
                               id="leaveStartDate"
                               class="js-flatpickr-date flatpickr-input flatpickr-custom-input"
                               value="{{ date('Y-m-d') }}"
                               placeholder="YYYY-MM-DD"
                               required />
                        <i class="bx bx-calendar"></i>
                    </div>
                </div>

                <!-- End Date -->
                <div class="date-field-group">
                    <label class="leave-field-label" for="leaveEndDate">{{ __('End Date') }}</label>
                    <div class="date-input-wrapper">
                        <input type="text"
                               name="end_date"
                               id="leaveEndDate"
                               class="js-flatpickr-date flatpickr-input flatpickr-custom-input"
                               value="{{ date('Y-m-d', strtotime('+4 days')) }}"
                               placeholder="YYYY-MM-DD"
                               required />
                        <i class="bx bx-calendar"></i>
                    </div>
                </div>
            </div>

            <!-- Total Working Days (Calculated Automatically) -->
            <div class="total-days-badge-card">
                <div class="days-summary-title">
                    <i class="bx bx-spreadsheet"></i>
                    <span>{{ __('Total Leave Days:') }}</span>
                </div>
                <span class="days-count-pill" id="totalDaysPill">5 {{ __('Working Days') }}</span>
            </div>
        </div>

        <!-- 4. Emergency Phone Number During Leave with intl-tel-input -->
        <div class="leave-section-card">
            <h3 class="leave-section-title">
                <span>{{ __('Emergency Phone During Leave') }}</span>
                <i class="bx bx-phone-call"></i>
            </h3>
            <div>
                @php
                    $rawEmergencyPhone = $employee?->emergency_contact_phone ?? ($employee?->phone ?? '99112233');
                    $cleanedEmergencyPhone = preg_replace('/^\+965/', '', $rawEmergencyPhone);
                @endphp
                <input type="tel"
                       id="leaveEmergencyPhoneInput"
                       name="emergency_contact_phone"
                       class="emergency-phone-input"
                       value="{{ $cleanedEmergencyPhone }}"
                       dir="ltr" />
            </div>
        </div>

        <!-- 5. Reason & Notes Card -->
        <div class="leave-section-card">
            <h3 class="leave-section-title">
                <span>{{ __('Leave Reason & Additional Notes') }}</span>
                <i class="bx bx-edit-alt"></i>
            </h3>
            <textarea name="reason" class="leave-textarea" placeholder="{{ __('Please briefly describe the reason for your leave request or any special instructions for the substitute lesson plan...') }}"></textarea>
        </div>

        <!-- 6. Attachment Card -->
        <div class="leave-section-card">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <h3 class="leave-section-title mb-0" style="font-size: 14px;">
                    <span>{{ __('Attach Medical Report or Excuse') }}</span>
                    <i class="bx bx-paperclip"></i>
                </h3>
                <span class="badge bg-light text-muted font-11 rounded-pill" style="font-size: 10px;">{{ __('Optional') }}</span>
            </div>

            <div class="leave-upload-dropzone" onclick="document.getElementById('leaveAttachmentInput').click()">
                <div class="upload-cloud-icon">
                    <i class="bx bx-cloud-upload"></i>
                </div>
                <div class="upload-title" id="uploadFilenameText">{{ __('Click here to upload a file or take a photo') }}</div>
                <div class="upload-sub">{{ __('Max size 5MB (JPG, PNG or PDF images)') }}</div>
                <input type="file" name="attachment" id="leaveAttachmentInput" accept=".pdf,.png,.jpg,.jpeg" style="display: none;" onchange="handleAttachmentSelected(this)" />
            </div>
        </div>

        <!-- 7. Submit Button (Figma Spec) -->
        <button type="submit" class="btn-submit-leave-custom">
            <span>{{ __('Confirm and Submit Request') }}</span>
        </button>
    </form>
</div>

@push('scripts')
<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ar.js"></script>
<!-- intl-tel-input JS -->
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/intlTelInput.min.js"></script>

<script>
    let fpStart = null;
    let fpEnd = null;

    document.addEventListener("DOMContentLoaded", function () {
        const isAr = "{{ app()->getLocale() }}" === "ar";

        // 1. Initialize intl-tel-input for Emergency Phone
        const emergencyPhoneField = document.querySelector("#leaveEmergencyPhoneInput");
        if (emergencyPhoneField) {
            window.intlTelInput(emergencyPhoneField, {
                initialCountry: "kw",
                preferredCountries: ["kw", "sa", "ae", "qa", "bh", "om", "eg"],
                utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js",
                separateDialCode: true
            });
        }

        // 2. Initialize Flatpickr for Start Date
        fpStart = flatpickr("#leaveStartDate", {
            locale: isAr ? flatpickr.l10ns.ar : "default",
            dateFormat: "Y-m-d",
            altInput: false,
            defaultDate: "{{ date('Y-m-d') }}",
            onChange: function(selectedDates, dateStr) {
                if (fpEnd && selectedDates[0]) {
                    fpEnd.set("minDate", dateStr);
                    if (fpEnd.selectedDates[0] && fpEnd.selectedDates[0] < selectedDates[0]) {
                        fpEnd.setDate(dateStr, false);
                    }
                }
                recalculateLeaveDays();
            }
        });

        // 3. Initialize Flatpickr for End Date
        fpEnd = flatpickr("#leaveEndDate", {
            locale: isAr ? flatpickr.l10ns.ar : "default",
            dateFormat: "Y-m-d",
            altInput: false,
            defaultDate: "{{ date('Y-m-d', strtotime('+4 days')) }}",
            minDate: "{{ date('Y-m-d') }}",
            onChange: function() {
                recalculateLeaveDays();
            }
        });

        // 4. Initial calculation on load
        recalculateLeaveDays();
    });

    function recalculateLeaveDays() {
        const leaveTypeSelect = document.getElementById('portalLeaveTypeSelect');
        const startDateInput = document.getElementById('leaveStartDate');
        const endDateInput = document.getElementById('leaveEndDate');
        const totalDaysPill = document.getElementById('totalDaysPill');

        if (!leaveTypeSelect || !startDateInput || !endDateInput) return;

        const leaveType = leaveTypeSelect.value;
        const startDate = startDateInput.value;
        const endDate = endDateInput.value;

        if (!startDate || !endDate) return;

        // Immediate Client-side preview calculation (excluding Friday=5 and Saturday=6 rest days)
        const d1 = new Date(startDate);
        const d2 = new Date(endDate);
        if (!isNaN(d1) && !isNaN(d2) && d2 >= d1) {
            let workingCount = 0;
            let cur = new Date(d1);
            while (cur <= d2) {
                const day = cur.getDay(); // 0=Sun, 1=Mon, ..., 5=Fri, 6=Sat
                if (day !== 5 && day !== 6) {
                    workingCount++;
                }
                cur.setDate(cur.getDate() + 1);
            }
            if (totalDaysPill) {
                totalDaysPill.textContent = `${workingCount} {{ __('Working Days') }}`;
            }
        }

        // Dynamic hint text updates
        const hintText = document.getElementById('leaveTypeHintText');
        if (hintText) {
            if (leaveType === 'sick') {
                hintText.textContent = "{{ __('Sick leave requires an approved medical report attachment.') }}";
            } else if (leaveType === 'casual') {
                hintText.textContent = "{{ __('Casual leave is deducted from the available emergency/casual leave balance.') }}";
            } else {
                hintText.textContent = "{{ __('Annual leave is deducted directly from your cumulative annual balance.') }}";
            }
        }

        fetch("{{ route('portal.leaves.calculate') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                leave_type: leaveType,
                start_date: startDate,
                end_date: endDate
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (totalDaysPill && data.working_days !== undefined) {
                    totalDaysPill.textContent = `${data.working_days} {{ __('Working Days') }}`;
                }
                if (data.remaining_balance !== undefined) {
                    const balanceCount = document.getElementById('headerBalanceCount');
                    if (balanceCount) {
                        balanceCount.textContent = `${data.remaining_balance} {{ __('Days Remaining') }}`;
                    }
                }
            }
        })
        .catch(err => {
            console.error('Leave calculation error:', err);
        });
    }

    function handleAttachmentSelected(input) {
        if (input.files && input.files[0]) {
            const fileName = input.files[0].name;
            document.getElementById('uploadFilenameText').innerHTML = `<strong style="color: #0284C7;"><i class="bx bx-check-circle"></i> ${fileName}</strong>`;
        }
    }
</script>
@endpush
@endsection

