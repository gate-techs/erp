@extends('employeeportal::layouts.master')

@section('title', __('Permission Request'))


@section('content')
<div class="perm-page-container">
    <!-- Top Navigation Header -->
    <div class="perm-nav-header">
        <a href="{{ route('portal.requests') }}" class="perm-back-btn" title="{{ __('Back') }}">
            <i class="bx {{ app()->getLocale() === 'ar' ? 'bx-right-arrow-alt' : 'bx-left-arrow-alt' }}"></i>
        </a>
        <h1 class="perm-header-title">{{ __('Permission Request') }}</h1>
        <div class="perm-nav-placeholder"></div>
    </div>

    <!-- 1. Header Hero Card with Live Policy Balance -->
    <div class="perm-hero-card">
        <div class="perm-hero-top">
            <div class="perm-hero-title-group">
                <div class="perm-hero-icon-box">
                    <i class="bx bx-time-five"></i>
                </div>
                <div>
                    <h2 class="perm-hero-title">{{ __('New Permission Request') }}</h2>
                    <p class="perm-hero-subtitle">{{ __('Monthly Permission Balance') }}</p>
                </div>
            </div>

            <a href="#recentPermissionsSection" class="perm-history-pill">
                <span class="dot"></span>
                <span>{{ __('Requests History') }}</span>
            </a>
        </div>

        <!-- 3-Metrics Grid -->
        <div class="perm-stats-grid">
            <div class="perm-stat-col">
                <span class="perm-stat-label">{{ __('Remaining Hours') }}</span>
                <span class="perm-stat-val" id="heroRemainingHours">{{ $balance['remaining_hours'] }} {{ __('hrs') }}</span>
                <span class="perm-stat-sub">{{ __('Total:') }} {{ $balance['max_monthly_hours'] }} {{ __('hrs') }}</span>
            </div>

            <div class="perm-stat-col">
                <span class="perm-stat-label">{{ __('Available Requests') }}</span>
                <span class="perm-stat-val" id="heroRemainingCount">{{ $balance['remaining_count'] }}</span>
                <span class="perm-stat-sub">{{ __('of') }} {{ $balance['max_monthly_count'] }} {{ __('times') }}</span>
            </div>

            <div class="perm-stat-col">
                <span class="perm-stat-label">{{ __('Max Single Limit') }}</span>
                <span class="perm-stat-val">{{ $balance['max_single_hours'] }} {{ __('hrs') }}</span>
                <span class="perm-stat-sub">{{ __('Maximum') }}</span>
            </div>
        </div>
    </div>

    <!-- Main Submission Form -->
    <form action="{{ route('portal.permissions.store') }}" method="POST" enctype="multipart/form-data" id="permissionSubmitForm">
        @csrf

        <!-- 2. Permission Type Selection Card -->
        <div class="perm-section-card">
            <h3 class="perm-section-title">
                <span>{{ __('Requested Permission Type') }}</span>
                <i class="bx bx-category-alt"></i>
            </h3>

            <!-- Hidden input for type -->
            <input type="hidden" name="type" id="selectedPermissionType" value="{{ \Modules\HR\Enums\PermissionType::DURING_SHIFT->value }}">

            <div class="perm-types-grid">
                <!-- 1. During Shift (خروج أثناء الدوام) -->
                <div class="perm-type-radio-card active" data-type="during_shift" onclick="selectPermissionType('during_shift', this)">
                    <div class="perm-type-card-top">
                        <div class="perm-type-card-icon icon-blue">
                            <i class="bx bx-transfer"></i>
                        </div>
                        <div class="perm-type-check-dot">
                            <i class="bx bx-check"></i>
                        </div>
                    </div>
                    <div>
                        <h4 class="perm-type-card-title">{{ __('During Shift') }}</h4>
                        <p class="perm-type-card-desc">{{ __('Temporary exit and return during classes') }}</p>
                    </div>
                </div>

                <!-- 2. Late Arrival (حضور متأخر) -->
                <div class="perm-type-radio-card" data-type="late_arrival" onclick="selectPermissionType('late_arrival', this)">
                    <div class="perm-type-card-top">
                        <div class="perm-type-card-icon icon-amber">
                            <i class="bx bx-time"></i>
                        </div>
                        <div class="perm-type-check-dot">
                            <i class="bx bx-check"></i>
                        </div>
                    </div>
                    <div>
                        <h4 class="perm-type-card-title">{{ __('Late Arrival') }}</h4>
                        <p class="perm-type-card-desc">{{ __('Arrival after morning official shift starts') }}</p>
                    </div>
                </div>

                <!-- 3. Early Departure (انصراف مبكر) -->
                <div class="perm-type-radio-card" data-type="early_departure" onclick="selectPermissionType('early_departure', this)">
                    <div class="perm-type-card-top">
                        <div class="perm-type-card-icon icon-indigo">
                            <i class="bx bx-log-out-circle"></i>
                        </div>
                        <div class="perm-type-check-dot">
                            <i class="bx bx-check"></i>
                        </div>
                    </div>
                    <div>
                        <h4 class="perm-type-card-title">{{ __('Early Departure') }}</h4>
                        <p class="perm-type-card-desc">{{ __('Leaving before official shift ends') }}</p>
                    </div>
                </div>

                <!-- 4. Emergency (استئذان طارئ) -->
                <div class="perm-type-radio-card is-emergency" data-type="emergency" onclick="selectPermissionType('emergency', this)">
                    <div class="perm-type-card-top">
                        <div class="perm-type-card-icon icon-rose">
                            <i class="bx bx-alarm-exclamation"></i>
                        </div>
                        <div class="perm-type-check-dot">
                            <i class="bx bx-check"></i>
                        </div>
                    </div>
                    <div>
                        <h4 class="perm-type-card-title">{{ __('Emergency Permission') }}</h4>
                        <p class="perm-type-card-desc">{{ __('Urgent condition bypassing advance notice') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Date & Time Period Card -->
        <div class="perm-section-card">
            <h3 class="perm-section-title">
                <span>{{ __('Date & Time Period') }}</span>
                <i class="bx bx-calendar-event"></i>
            </h3>

            <!-- Date Picker with Quick Chips -->
            <div class="mb-3">
                <label class="perm-field-label" for="permissionDate">{{ __('Permission Date') }}</label>
                <div class="time-input-wrapper">
                    <input type="text"
                           name="date"
                           id="permissionDate"
                           class="js-flatpickr-date flatpickr-custom-input"
                           value="{{ date('Y-m-d') }}"
                           placeholder="YYYY-MM-DD"
                           required />
                    <i class="bx bx-calendar"></i>
                </div>
                <div class="date-chips-row">
                    <button type="button" class="date-chip" onclick="setDateQuickly('{{ date('Y-m-d') }}')">{{ __('Today') }}</button>
                    <button type="button" class="date-chip" onclick="setDateQuickly('{{ date('Y-m-d', strtotime('+1 day')) }}')">{{ __('Tomorrow') }}</button>
                </div>
            </div>

            <!-- Time Pickers Grid (From Time & To Time) -->
            <div class="time-pickers-grid">
                <!-- From Time -->
                <div class="time-field-group">
                    <label class="perm-field-label" for="permissionFromTime">{{ __('From Time') }}</label>
                    <div class="time-input-wrapper">
                        <input type="text"
                               name="from_time"
                               id="permissionFromTime"
                               class="flatpickr-custom-input"
                               value="08:00"
                               placeholder="HH:MM"
                               required />
                        <i class="bx bx-time"></i>
                    </div>
                </div>

                <!-- To Time -->
                <div class="time-field-group">
                    <label class="perm-field-label" for="permissionToTime">{{ __('To Time') }}</label>
                    <div class="time-input-wrapper">
                        <input type="text"
                               name="to_time"
                               id="permissionToTime"
                               class="flatpickr-custom-input"
                               value="10:00"
                               placeholder="HH:MM"
                               required />
                        <i class="bx bx-time"></i>
                    </div>
                </div>
            </div>

            <!-- Live Duration & Policy Projection Summary -->
            <div class="duration-summary-box">
                <div class="duration-summary-main">
                    <div class="duration-title-group">
                        <i class="bx bx-timer text-warning"></i>
                        <span>{{ __('Calculated Duration:') }}</span>
                    </div>
                    <span class="duration-pill" id="calculatedDurationPill">2.0 {{ __('hrs') }}</span>
                </div>

                <div class="duration-projection-line">
                    <span>{{ __('Remaining balance after this request:') }}</span>
                    <strong id="projectedBalanceVal" style="color: #0284C7;">{{ max(0, $balance['remaining_hours'] - 2) }} {{ __('hrs') }}</strong>
                </div>

                <!-- Real-time Warning Box -->
                <div class="duration-alert-box" id="permValidationAlert">
                    <i class="bx bx-error-circle font-18 flex-shrink-0"></i>
                    <span id="permValidationAlertText"></span>
                </div>
            </div>
        </div>

        <!-- 4. Emergency Switch Toggle -->
        <div class="perm-section-card">
            <div class="perm-toggle-row">
                <div class="perm-toggle-text">
                    <h4 class="perm-toggle-title">{{ __('Emergency Request') }}</h4>
                    <p class="perm-toggle-sub">{{ __('Enable this option for force majeure situations where advance notice could not be provided.') }}</p>
                </div>
                <label class="switch-ios">
                    <input type="checkbox" name="is_emergency" id="isEmergencyCheckbox" value="1" onchange="handleEmergencyToggle()">
                    <span class="switch-ios-slider"></span>
                </label>
            </div>
        </div>

        <!-- 5. Reason & Notes Card -->
        <div class="perm-section-card">
            <h3 class="perm-section-title">
                <span>{{ __('Reason') }} <span class="text-danger">*</span></span>
                <i class="bx bx-edit-alt"></i>
            </h3>
            <textarea name="reason"
                      id="permissionReason"
                      class="perm-textarea"
                      placeholder="{{ __('State the reason for this permission request...') }}"
                      required></textarea>
        </div>

        <!-- 6. Supporting Attachment (Optional) -->
        <div class="perm-section-card">
            <h3 class="perm-section-title">
                <span>{{ __('Supporting Attachment (Optional)') }}</span>
                <i class="bx bx-paperclip"></i>
            </h3>

            <label class="perm-upload-dropzone" for="permissionAttachmentInput">
                <div class="upload-cloud-icon">
                    <i class="bx bx-cloud-upload"></i>
                </div>
                <div class="upload-title">{{ __('Click to upload medical report or document') }}</div>
                <div class="upload-sub" id="uploadFilenameText">{{ __('Allowed formats: PDF, PNG, JPG (up to 5MB)') }}</div>
                <input type="file"
                       name="attachment"
                       id="permissionAttachmentInput"
                       accept=".pdf,.png,.jpg,.jpeg"
                       style="display: none;"
                       onchange="handleAttachmentSelected(this)" />
            </label>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn-submit-perm-custom" id="btnSubmitPermission">
            <i class="bx bx-send"></i>
            <span>{{ __('Submit Permission Request') }}</span>
        </button>
    </form>

    <!-- 7. Recent Permissions History Section -->
    <div class="perm-history-card" id="recentPermissionsSection">
        <h3 class="perm-section-title mb-2">
            <span>{{ __('Recent Permissions History') }}</span>
            <i class="bx bx-history"></i>
        </h3>

        @if($recentPermissions->isNotEmpty())
            @foreach($recentPermissions as $perm)
                <div class="perm-history-item">
                    <div class="perm-history-left">
                        <div class="perm-history-icon {{ $perm->type === \Modules\HR\Enums\PermissionType::EMERGENCY ? 'icon-rose' : 'icon-blue' }}">
                            <i class="{{ $perm->type?->icon() ?? 'bx bx-time' }}"></i>
                        </div>
                        <div class="perm-history-info">
                            <span class="perm-history-title">{{ $perm->type?->label() ?? __('Permission') }}</span>
                            <span class="perm-history-meta">
                                {{ $perm->date?->format('Y-m-d') }} • {{ \Carbon\Carbon::createFromTimeString($perm->from_time)->format('h:i A') }} - {{ \Carbon\Carbon::createFromTimeString($perm->to_time)->format('h:i A') }} ({{ $perm->hours_count }} {{ __('hrs') }})
                            </span>
                        </div>
                    </div>
                    <div>
                        <span class="perm-history-badge {{ $perm->status?->badgeClass() ?? 'bg-secondary text-white' }}">
                            {{ $perm->status?->label() ?? '-' }}
                        </span>
                    </div>
                </div>
            @endforeach
        @else
            <div class="text-center py-3 text-muted" style="font-size: 11.5px;">
                <i class="bx bx-info-circle font-20 mb-1 d-block text-secondary"></i>
                <span>{{ __('No permission requests recorded for you this month.') }}</span>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    let fpDateInstance = null;
    let fpFromTimeInstance = null;
    let fpToTimeInstance = null;

    document.addEventListener("DOMContentLoaded", function () {
        const isAr = "{{ app()->getLocale() }}" === "ar";

        // 1. Initialize Date Picker
        fpDateInstance = flatpickr("#permissionDate", {
            locale: isAr ? flatpickr.l10ns.ar : "default",
            dateFormat: "Y-m-d",
            altInput: false,
            defaultDate: "{{ date('Y-m-d') }}",
            onChange: function() {
                recalculatePermission();
            }
        });

        // 2. Initialize From Time Picker (24hr H:i for exact server validation)
        fpFromTimeInstance = flatpickr("#permissionFromTime", {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: true,
            defaultDate: "08:00",
            minuteIncrement: 15,
            onChange: function(selectedDates, dateStr) {
                // Ensure To Time is after From Time
                if (fpToTimeInstance && dateStr) {
                    const fromParts = dateStr.split(':').map(Number);
                    const toVal = document.getElementById('permissionToTime').value;
                    if (toVal) {
                        const toParts = toVal.split(':').map(Number);
                        const fromMinutes = fromParts[0] * 60 + fromParts[1];
                        const toMinutes = toParts[0] * 60 + toParts[1];
                        if (toMinutes <= fromMinutes) {
                            // Automatically adjust to 1 hour after
                            const newToMinutes = fromMinutes + 60;
                            const newH = String(Math.floor(newToMinutes / 60) % 24).padStart(2, '0');
                            const newM = String(newToMinutes % 60).padStart(2, '0');
                            fpToTimeInstance.setDate(`${newH}:${newM}`, false);
                        }
                    }
                }
                recalculatePermission();
            }
        });

        // 3. Initialize To Time Picker
        fpToTimeInstance = flatpickr("#permissionToTime", {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: true,
            defaultDate: "10:00",
            minuteIncrement: 15,
            onChange: function() {
                recalculatePermission();
            }
        });

        // 4. Initial calculation on page load
        recalculatePermission();

        // 5. Form submission protection
        const form = document.getElementById('permissionSubmitForm');
        if (form) {
            form.addEventListener('submit', function() {
                const btn = document.getElementById('btnSubmitPermission');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> {{ __('Submitting request...') }}`;
                }
            });
        }
    });

    /**
     * Select permission type card
     */
    function selectPermissionType(typeVal, cardEl) {
        document.querySelectorAll('.perm-type-radio-card').forEach(el => el.classList.remove('active'));
        if (cardEl) {
            cardEl.classList.add('active');
        }

        const inputHidden = document.getElementById('selectedPermissionType');
        if (inputHidden) {
            inputHidden.value = typeVal;
        }

        const emergencyCheckbox = document.getElementById('isEmergencyCheckbox');
        if (typeVal === 'emergency') {
            if (emergencyCheckbox) emergencyCheckbox.checked = true;
        } else {
            // Keep switch as is unless user turns it off
        }

        recalculatePermission();
    }

    /**
     * Quick date button setter (اليوم / غداً)
     */
    function setDateQuickly(dateStr) {
        if (fpDateInstance) {
            fpDateInstance.setDate(dateStr, true);
        }
    }

    /**
     * Toggle emergency switch handler
     */
    function handleEmergencyToggle() {
        const emergencyCheckbox = document.getElementById('isEmergencyCheckbox');
        const emergencyCard = document.querySelector('.perm-type-radio-card[data-type="emergency"]');

        if (emergencyCheckbox && emergencyCheckbox.checked) {
            // If emergency switch turned on and emergency type not selected, keep current or highlight
        }
        recalculatePermission();
    }

    /**
     * Recalculate duration & check balance via AJAX
     */
    function recalculatePermission() {
        const dateInput = document.getElementById('permissionDate');
        const fromTimeInput = document.getElementById('permissionFromTime');
        const toTimeInput = document.getElementById('permissionToTime');
        const typeInput = document.getElementById('selectedPermissionType');
        const emergencyCheckbox = document.getElementById('isEmergencyCheckbox');

        if (!fromTimeInput || !toTimeInput || !dateInput) return;

        const date = dateInput.value;
        const fromTime = fromTimeInput.value;
        const toTime = toTimeInput.value;
        const type = typeInput ? typeInput.value : 'during_shift';
        const isEmergency = emergencyCheckbox ? (emergencyCheckbox.checked ? 1 : 0) : 0;

        if (!fromTime || !toTime || !date) return;

        // Client-side quick calculation
        const fromParts = fromTime.split(':').map(Number);
        const toParts = toTime.split(':').map(Number);
        const fromMinutes = fromParts[0] * 60 + fromParts[1];
        const toMinutes = toParts[0] * 60 + toParts[1];

        const durationPill = document.getElementById('calculatedDurationPill');
        const alertBox = document.getElementById('permValidationAlert');
        const alertText = document.getElementById('permValidationAlertText');
        const projectedBalance = document.getElementById('projectedBalanceVal');

        if (toMinutes <= fromMinutes) {
            if (alertBox && alertText) {
                alertBox.classList.add('active');
                alertText.textContent = "{{ __('End time must be after start time.') }}";
            }
            if (durationPill) durationPill.textContent = "0 {{ __('hrs') }}";
            return;
        }

        const diffMinutes = toMinutes - fromMinutes;
        const diffHours = (diffMinutes / 60).toFixed(2);
        if (durationPill) {
            durationPill.textContent = `${diffHours} {{ __('hrs') }}`;
        }

        // Fetch live server calculation and policy verification
        fetch("{{ route('portal.permissions.calculate') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                date: date,
                from_time: fromTime,
                to_time: toTime,
                type: type,
                is_emergency: isEmergency
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (durationPill && data.duration_formatted) {
                    durationPill.textContent = `${data.duration_formatted} (${data.hours_count} {{ __('hrs') }})`;
                }
                if (projectedBalance && data.projected_remaining_hours !== undefined) {
                    projectedBalance.textContent = `${data.projected_remaining_hours} {{ __('hrs') }}`;
                }

                if (data.warning) {
                    if (alertBox && alertText) {
                        alertBox.classList.add('active');
                        alertText.textContent = data.warning;
                    }
                } else {
                    if (alertBox) {
                        alertBox.classList.remove('active');
                    }
                }
            } else if (data.message) {
                if (alertBox && alertText) {
                    alertBox.classList.add('active');
                    alertText.textContent = data.message;
                }
            }
        })
        .catch(err => {
            console.error('Permission calculation error:', err);
        });
    }

    /**
     * Display selected file name in upload dropzone
     */
    function handleAttachmentSelected(input) {
        const textEl = document.getElementById('uploadFilenameText');
        if (input.files && input.files[0] && textEl) {
            const fileName = input.files[0].name;
            const fileSize = (input.files[0].size / 1024 / 1024).toFixed(2);
            textEl.innerHTML = `<strong style="color: #0284C7;"><i class="bx bx-check-circle"></i> ${fileName} (${fileSize} MB)</strong>`;
        }
    }
</script>
@endpush
