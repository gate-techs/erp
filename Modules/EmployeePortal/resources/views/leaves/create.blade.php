@extends('employeeportal::layouts.master')

@section('title', __('Leave Request'))


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
                {{ __('Please correct the errors indicated below before submitting.') }}
            </div>
        </div>
    @endif

    <!-- Leave Submission Form -->
    <form action="{{ route('portal.leaves.store') }}" method="POST" enctype="multipart/form-data" id="leaveSubmitForm" novalidate onsubmit="return validateLeaveForm(event)">
        @csrf

        <!-- 2. Leave Type Card -->
        <div class="leave-section-card">
            <h3 class="leave-section-title">{{ __('Requested Leave Type') }} <span class="text-danger">*</span></h3>
            <select name="leave_type" id="portalLeaveTypeSelect" class="leave-select-control @error('leave_type') is-invalid @enderror" required onchange="recalculateLeaveDays()">
                @foreach($leaveTypes as $type)
                    <option value="{{ $type->value }}" {{ (old('leave_type', 'annual') === $type->value) ? 'selected' : '' }}>
                        {{ $type->label() }}
                    </option>
                @endforeach
            </select>
            <div class="portal-invalid-feedback" id="leaveTypeError">
                @error('leave_type') {{ $message }} @enderror
            </div>
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
                    <label class="leave-field-label" for="leaveStartDate">
                        {{ __('Start Date') }} <span class="text-danger">*</span>
                    </label>
                    <div class="date-input-wrapper">
                        <input type="text"
                               name="start_date"
                               id="leaveStartDate"
                               class="js-flatpickr-date flatpickr-input flatpickr-custom-input @error('start_date') is-invalid @enderror"
                               value="{{ old('start_date', date('Y-m-d')) }}"
                               placeholder="YYYY-MM-DD"
                               required />
                        <i class="bx bx-calendar"></i>
                    </div>
                    <div class="portal-invalid-feedback" id="startDateError">
                        @error('start_date') {{ $message }} @enderror
                    </div>
                </div>

                <!-- End Date -->
                <div class="date-field-group">
                    <label class="leave-field-label" for="leaveEndDate">
                        {{ __('End Date') }} <span class="text-danger">*</span>
                    </label>
                    <div class="date-input-wrapper">
                        <input type="text"
                               name="end_date"
                               id="leaveEndDate"
                               class="js-flatpickr-date flatpickr-input flatpickr-custom-input @error('end_date') is-invalid @enderror"
                               value="{{ old('end_date', date('Y-m-d', strtotime('+4 days'))) }}"
                               placeholder="YYYY-MM-DD"
                               required />
                        <i class="bx bx-calendar"></i>
                    </div>
                    <div class="portal-invalid-feedback" id="endDateError">
                        @error('end_date') {{ $message }} @enderror
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
            <div class="portal-invalid-feedback" id="balanceExceededError" style="margin-top: 8px;"></div>
        </div>

        <!-- 4. Emergency Phone Number During Leave with intl-tel-input -->
        <div class="leave-section-card">
            <h3 class="leave-section-title">
                <span>{{ __('Emergency Phone During Leave') }}</span>
                <i class="bx bx-phone-call"></i>
            </h3>
            <div class="portal-iti-wrapper">
                @php
                    $rawEmergencyPhone = old('emergency_contact_phone', $employee?->emergency_contact_phone ?? ($employee?->phone ?? '99112233'));
                    $cleanedEmergencyPhone = preg_replace('/^\+965/', '', $rawEmergencyPhone);
                @endphp
                <input type="tel"
                       id="leaveEmergencyPhoneInput"
                       name="emergency_contact_phone"
                       class="emergency-phone-input @error('emergency_contact_phone') is-invalid @enderror"
                       value="{{ $cleanedEmergencyPhone }}"
                       dir="ltr" />
                <div class="portal-invalid-feedback" id="phoneError">
                    @error('emergency_contact_phone') {{ $message }} @enderror
                </div>
            </div>
        </div>

        <!-- 5. Reason & Notes Card -->
        <div class="leave-section-card">
            <h3 class="leave-section-title">
                <span>{{ __('Leave Reason & Additional Notes') }}</span>
                <i class="bx bx-edit-alt"></i>
            </h3>
            <textarea name="reason"
                      id="leaveReasonInput"
                      class="leave-textarea @error('reason') is-invalid @enderror"
                      placeholder="{{ __('Please briefly describe the reason for your leave request or any special instructions for the substitute lesson plan...') }}">{{ old('reason') }}</textarea>
            <div class="portal-invalid-feedback" id="reasonError">
                @error('reason') {{ $message }} @enderror
            </div>
        </div>

        <!-- 6. Attachment Card -->
        <div class="leave-section-card" id="attachmentSectionCard">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <h3 class="leave-section-title mb-0" style="font-size: 14px;">
                    <span>{{ __('Attach Medical Report or Excuse') }}</span>
                    <i class="bx bx-paperclip"></i>
                </h3>
                <span class="badge bg-light text-muted font-11 rounded-pill" id="attachmentOptionalBadge" style="font-size: 10px;">{{ __('Optional') }}</span>
            </div>

            <div class="leave-upload-dropzone @error('attachment') is-invalid @enderror" id="leaveDropzoneBox" onclick="document.getElementById('leaveAttachmentInput').click()">
                <div class="upload-cloud-icon">
                    <i class="bx bx-cloud-upload"></i>
                </div>
                <div class="upload-title" id="uploadFilenameText">{{ __('Click here to upload a file or take a photo') }}</div>
                <div class="upload-sub">{{ __('Max size 5MB (JPG, PNG or PDF images)') }}</div>
                <input type="file" name="attachment" id="leaveAttachmentInput" accept=".pdf,.png,.jpg,.jpeg" style="display: none;" onchange="handleAttachmentSelected(this)" />
            </div>
            <div class="portal-invalid-feedback" id="attachmentError">
                @error('attachment') {{ $message }} @enderror
            </div>
        </div>

        <!-- 7. Submit Button (Figma Spec) -->
        <button type="submit" class="btn-submit-leave-custom" id="btnSubmitLeave">
            <span>{{ __('Confirm and Submit Request') }}</span>
            <i class="bx bx-check-circle fs-5"></i>
        </button>
    </form>
</div>

@push('scripts')
<script>
    let fpStart = null;
    let fpEnd = null;
    let currentWorkingDays = 0;
    const initialAnnualBalance = Number("{{ (float)$annualBalance }}") || 0;

    document.addEventListener("DOMContentLoaded", function () {
        const isAr = "{{ app()->getLocale() }}" === "ar";

        // 1. Initialize intl-tel-input for Emergency Phone
        const emergencyPhoneField = document.querySelector("#leaveEmergencyPhoneInput");
        if (emergencyPhoneField) {
            const itiInstance = window.intlTelInput(emergencyPhoneField, {
                initialCountry: "kw",
                preferredCountries: ["kw", "sa", "ae", "qa", "bh", "om", "eg"],
                utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js",
                separateDialCode: true,
                autoPlaceholder: "polite"
            });

            const adjustPhonePadding = () => {
                const selectedFlag = emergencyPhoneField.closest('.iti')?.querySelector('.iti__selected-flag');
                if (selectedFlag) {
                    const width = selectedFlag.offsetWidth;
                    if (width > 0) {
                        emergencyPhoneField.style.setProperty('padding-left', (width + 8) + 'px', 'important');
                    }
                }
            };

            setTimeout(adjustPhonePadding, 50);
            emergencyPhoneField.addEventListener('countrychange', adjustPhonePadding);

            emergencyPhoneField.addEventListener('input', function() {
                clearFieldError('leaveEmergencyPhoneInput', 'phoneError');
            });
        }

        // 2. Initialize Flatpickr for Start Date
        fpStart = flatpickr("#leaveStartDate", {
            locale: isAr ? flatpickr.l10ns.ar : "default",
            dateFormat: "Y-m-d",
            altInput: false,
            defaultDate: "{{ old('start_date', date('Y-m-d')) }}",
            onChange: function(selectedDates, dateStr) {
                clearFieldError('leaveStartDate', 'startDateError');
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
            defaultDate: "{{ old('end_date', date('Y-m-d', strtotime('+4 days'))) }}",
            minDate: "{{ old('start_date', date('Y-m-d')) }}",
            onChange: function() {
                clearFieldError('leaveEndDate', 'endDateError');
                recalculateLeaveDays();
            }
        });

        // 4. Initial calculation on load
        recalculateLeaveDays();

        // 5. If server returned errors, reveal feedback
        @if($errors->has('leave_type'))
            setFieldError('portalLeaveTypeSelect', 'leaveTypeError', "{{ $errors->first('leave_type') }}");
        @endif
        @if($errors->has('start_date'))
            setFieldError('leaveStartDate', 'startDateError', "{{ $errors->first('start_date') }}");
        @endif
        @if($errors->has('end_date'))
            setFieldError('leaveEndDate', 'endDateError', "{{ $errors->first('end_date') }}");
        @endif
        @if($errors->has('emergency_contact_phone'))
            setFieldError('leaveEmergencyPhoneInput', 'phoneError', "{{ $errors->first('emergency_contact_phone') }}");
        @endif
        @if($errors->has('reason'))
            setFieldError('leaveReasonInput', 'reasonError', "{{ $errors->first('reason') }}");
        @endif
        @if($errors->has('attachment'))
            setFieldError('leaveDropzoneBox', 'attachmentError', "{{ $errors->first('attachment') }}");
        @endif
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

    function recalculateLeaveDays() {
        const leaveTypeSelect = document.getElementById('portalLeaveTypeSelect');
        const startDateInput = document.getElementById('leaveStartDate');
        const endDateInput = document.getElementById('leaveEndDate');
        const totalDaysPill = document.getElementById('totalDaysPill');
        const balanceExceededEl = document.getElementById('balanceExceededError');

        if (!leaveTypeSelect || !startDateInput || !endDateInput) return;

        const leaveType = leaveTypeSelect.value;
        const startDate = startDateInput.value;
        const endDate = endDateInput.value;

        // Toggle attachment required for Sick Leave
        const optionalBadge = document.getElementById('attachmentOptionalBadge');
        if (optionalBadge) {
            if (leaveType === 'sick') {
                optionalBadge.className = 'badge bg-danger text-white font-11 rounded-pill';
                optionalBadge.textContent = "{{ __('Required') }}";
            } else {
                optionalBadge.className = 'badge bg-light text-muted font-11 rounded-pill';
                optionalBadge.textContent = "{{ __('Optional') }}";
            }
        }

        if (!startDate || !endDate) return;

        // Client-side quick working days calculation
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
            currentWorkingDays = workingCount;
            if (totalDaysPill) {
                totalDaysPill.textContent = `${workingCount} {{ __('Working Days') }}`;
            }

            // Check if exceeds balance for annual leave
            if (leaveType === 'annual' && workingCount > initialAnnualBalance) {
                if (balanceExceededEl) {
                    balanceExceededEl.textContent = `{{ __('Requested days (:days) exceed your available annual balance (:bal days)!') }}`.replace(':days', workingCount).replace(':bal', initialAnnualBalance);
                    balanceExceededEl.classList.add('d-block');
                }
            } else {
                if (balanceExceededEl) {
                    balanceExceededEl.textContent = '';
                    balanceExceededEl.classList.remove('d-block');
                }
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
                    currentWorkingDays = data.working_days;
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
        clearFieldError('leaveDropzoneBox', 'attachmentError');
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const maxBytes = 5 * 1024 * 1024; // 5MB
            if (file.size > maxBytes) {
                setFieldError('leaveDropzoneBox', 'attachmentError', "{{ __('File size exceeds 5MB limit!') }}");
                input.value = '';
                document.getElementById('uploadFilenameText').textContent = "{{ __('Click here to upload a file or take a photo') }}";
                return;
            }
            document.getElementById('uploadFilenameText').innerHTML = `<strong style="color: #0284C7;"><i class="bx bx-check-circle"></i> ${file.name}</strong>`;
        }
    }

    // Client-Side Validation on Submit
    function validateLeaveForm(e) {
        let hasError = false;

        const leaveTypeSelect = document.getElementById('portalLeaveTypeSelect');
        const startDateInput = document.getElementById('leaveStartDate');
        const endDateInput = document.getElementById('leaveEndDate');
        const attachmentInput = document.getElementById('leaveAttachmentInput');
        const reasonInput = document.getElementById('leaveReasonInput');

        // 1. Validate Leave Type
        if (!leaveTypeSelect.value) {
            setFieldError('portalLeaveTypeSelect', 'leaveTypeError', "{{ __('Please select a leave type.') }}");
            hasError = true;
        } else {
            clearFieldError('portalLeaveTypeSelect', 'leaveTypeError');
        }

        // 2. Validate Start Date
        if (!startDateInput.value) {
            setFieldError('leaveStartDate', 'startDateError', "{{ __('Start date is required.') }}");
            hasError = true;
        } else {
            clearFieldError('leaveStartDate', 'startDateError');
        }

        // 3. Validate End Date
        if (!endDateInput.value) {
            setFieldError('leaveEndDate', 'endDateError', "{{ __('End date is required.') }}");
            hasError = true;
        } else {
            const d1 = new Date(startDateInput.value);
            const d2 = new Date(endDateInput.value);
            if (d2 < d1) {
                setFieldError('leaveEndDate', 'endDateError', "{{ __('End date cannot be earlier than start date.') }}");
                hasError = true;
            } else {
                clearFieldError('leaveEndDate', 'endDateError');
            }
        }

        // 4. Validate Sick Leave Attachment
        if (leaveTypeSelect.value === 'sick' && (!attachmentInput.files || attachmentInput.files.length === 0)) {
            setFieldError('leaveDropzoneBox', 'attachmentError', "{{ __('Please attach a medical report for sick leave.') }}");
            hasError = true;
        }

        // 5. Validate Annual Balance Limit
        if (leaveTypeSelect.value === 'annual' && currentWorkingDays > initialAnnualBalance) {
            setFieldError('leaveEndDate', 'endDateError', "{{ __('Requested leave days exceed your available annual balance.') }}");
            hasError = true;
        }

        if (hasError) {
            e.preventDefault();
            if (typeof showPortalToast === 'function') {
                showPortalToast('error', "{{ __('Please check the required fields') }}", "{{ __('Some fields contain invalid data or exceed limits.') }}");
            }
            return false;
        }

        // Disable button to prevent double-submit
        const submitBtn = document.getElementById('btnSubmitLeave');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = `<span>{{ __('Submitting...') }}</span> <i class="bx bx-loader-alt bx-spin fs-5"></i>`;
        }

        return true;
    }
</script>
@endpush
@endsection


