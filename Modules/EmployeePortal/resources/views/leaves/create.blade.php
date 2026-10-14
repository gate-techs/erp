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

