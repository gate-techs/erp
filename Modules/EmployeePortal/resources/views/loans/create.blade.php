@extends('employeeportal::layouts.master')

@section('title', __('Financial Advance Request'))

@section('content')
<div class="loan-page-container">
    <!-- Top Navigation Header -->
    <div class="loan-nav-header">
        <a href="{{ route('portal.requests') }}" class="loan-back-btn" title="{{ __('Back') }}">
            <i class="bx {{ app()->getLocale() === 'ar' ? 'bx-right-arrow-alt' : 'bx-left-arrow-alt' }}"></i>
        </a>
        <h1 class="loan-header-title">{{ __('Financial Advance') }}</h1>
        <a href="{{ route('portal.loans.index') }}" class="loan-back-btn" title="{{ __('Loan History') }}">
            <i class="bx bx-history"></i>
        </a>
    </div>

    <!-- 1. Financial Eligibility Hero Card -->
    <div class="loan-hero-card">


        @php
            $empData = $eligibility['employee'] ?? [];
            $basicSalary = (float)($empData['basic_salary'] ?? 0);
            $maxLoan = (float)($empData['max_allowed_loan'] ?? 0);
            $maxDeduction = (float)($empData['max_monthly_deduction'] ?? 0);
            $activeDebt = (float)($empData['remaining_debt'] ?? 0);
            $isEligible = $eligibility['is_eligible'] ?? true;
        @endphp

        <!-- 3-Metrics Grid -->
        <div class="loan-stats-grid">
            <div class="loan-stat-col">
                <span class="loan-stat-label">{{ __('Basic Salary') }}</span>
                <span class="loan-stat-val">{{ number_format($basicSalary, 2) }}</span>
                <span class="loan-stat-sub">{{ __('Monthly Base') }}</span>
            </div>

            <div class="loan-stat-col highlighted">
                <span class="loan-stat-label">{{ __('Max Advance Cap') }}</span>
                <span class="loan-stat-val gold" id="heroMaxLoan">{{ number_format($maxLoan, 2) }}</span>
                <span class="loan-stat-sub">{{ __('Approved Ceiling') }}</span>
            </div>

            <div class="loan-stat-col">
                <span class="loan-stat-label">{{ __('Max Monthly Cut') }}</span>
                <span class="loan-stat-val text-warning">{{ number_format($maxDeduction, 2) }}</span>
                <span class="loan-stat-sub">{{ __('Per Payroll') }}</span>
            </div>
        </div>

        @if($activeDebt > 0)
        <div class="loan-existing-debt-banner">
            <i class="bx bx-info-circle"></i>
            <span>{{ __('Active balance on existing advances:') }} <strong>{{ number_format($activeDebt, 2) }}</strong></span>
        </div>
        @endif
    </div>

    <!-- Loan Submission Form -->
    <form action="{{ route('portal.loans.store') }}" method="POST" enctype="multipart/form-data" id="loanSubmitForm">
        @csrf

        <!-- 2. Advance Type Selection Card -->
        <div class="loan-section-card">
            <h3 class="loan-section-title">
                <span>{{ __('Advance Type') }}</span>
                <i class="bx bx-category-alt"></i>
            </h3>

            <!-- Hidden input for type -->
            <input type="hidden" name="type" id="selectedLoanType" value="salary_advance">

            <div class="loan-types-grid">
                <!-- 1. Salary Advance -->
                <div class="loan-type-radio-card active" data-type="salary_advance" onclick="selectLoanType('salary_advance', this)">
                    <div class="loan-type-card-top">
                        <div class="loan-type-card-icon icon-gold">
                            <i class="bx bx-credit-card"></i>
                        </div>
                        <div class="loan-type-check-dot">
                            <i class="bx bx-check"></i>
                        </div>
                    </div>
                    <div>
                        <h4 class="loan-type-card-title">{{ __('Salary Advance') }}</h4>
                        <p class="loan-type-card-desc">{{ __('1 month advance deducted in full from next payroll') }}</p>
                    </div>
                </div>

                <!-- 2. Installment Loan -->
                <div class="loan-type-radio-card" data-type="installment_loan" onclick="selectLoanType('installment_loan', this)">
                    <div class="loan-type-card-top">
                        <div class="loan-type-card-icon icon-blue">
                            <i class="bx bx-money-withdraw"></i>
                        </div>
                        <div class="loan-type-check-dot">
                            <i class="bx bx-check"></i>
                        </div>
                    </div>
                    <div>
                        <h4 class="loan-type-card-title">{{ __('Installment Loan') }}</h4>
                        <p class="loan-type-card-desc">{{ __('Repaid over flexible monthly installments') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Financial Amount & Repayment Duration -->
        <div class="loan-section-card">
            <h3 class="loan-section-title">
                <span>{{ __('Financial Request Details') }}</span>
                <i class="bx bx-calculator"></i>
            </h3>

            <!-- Amount Input -->
            <div class="loan-input-group mb-3">
                <label class="loan-field-label" for="loanAmount">
                    {{ __('Requested Amount') }} <span class="required-star">*</span>
                </label>
                <div class="loan-currency-input-wrapper">
                    <input type="number"
                           step="0.01"
                           min="1"
                           max="{{ $maxLoan > 0 ? $maxLoan : '' }}"
                           name="amount"
                           id="loanAmount"
                           class="loan-form-control loan-amount-input"
                           placeholder="0.00"
                           required
                           oninput="triggerRealtimeCalculation()" />
                    <span class="loan-currency-addon">{{ __('KWD') }}</span>
                </div>
                <small class="loan-field-helper" id="amountHelperText">
                    {{ __('Allowed limit up to:') }} <strong id="allowedCapText">{{ number_format($maxLoan, 2) }}</strong>
                </small>
            </div>

            <!-- Installments Count (Hidden or locked for salary_advance) -->
            <div class="loan-input-group mb-3" id="installmentsCountWrapper" style="display: none;">
                <label class="loan-field-label" for="loanInstallmentsCount">
                    {{ __('Repayment Installments (Months)') }} <span class="required-star">*</span>
                </label>
                <div class="loan-stepper-wrapper">
                    <button type="button" class="stepper-btn" onclick="stepInstallment(-1)">-</button>
                    <input type="number"
                           name="installments_count"
                           id="loanInstallmentsCount"
                           class="loan-form-control text-center font-monospace"
                           value="1"
                           min="1"
                           max="24"
                           required
                           oninput="triggerRealtimeCalculation()" />
                    <button type="button" class="stepper-btn" onclick="stepInstallment(1)">+</button>
                </div>
                <small class="loan-field-helper">{{ __('Monthly repayment schedule up to 24 months.') }}</small>
            </div>

            <!-- First Deduction Date -->
            <div class="loan-input-group mb-3">
                <label class="loan-field-label" for="loanStartDate">
                    {{ __('First Deduction Month / Date') }} <span class="required-star">*</span>
                </label>
                <div class="loan-date-input-wrapper">
                    <input type="text"
                           name="start_date"
                           id="loanStartDate"
                           class="js-flatpickr-date flatpickr-input loan-form-control"
                           value="{{ date('Y-m-01', strtotime('+1 month')) }}"
                           placeholder="YYYY-MM-DD"
                           required
                           onchange="triggerRealtimeCalculation()" />
                    <i class="bx bx-calendar"></i>
                </div>
            </div>

            <!-- Calculated Monthly Installment Badge -->
            <div class="loan-monthly-preview-box">
                <div class="monthly-preview-info">
                    <span class="monthly-label">{{ __('Estimated Monthly Deduction:') }}</span>
                    <span class="monthly-sub" id="monthlySubText">{{ __('From upcoming payroll') }}</span>
                </div>
                <div class="monthly-preview-val">
                    <span id="calculatedMonthlyValue">0.00</span>
                    <small>{{ __('KWD') }}</small>
                </div>
            </div>

            <!-- Live Eligibility Feedback Banner -->
            <div id="liveEligibilityAlert" class="loan-feedback-alert d-none"></div>

            <!-- Installment Schedule Preview Accordion -->
            <div class="loan-schedule-accordion mt-3" id="scheduleAccordionSection" style="display: none;">
                <div class="schedule-accordion-header" onclick="toggleScheduleAccordion()">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bx bx-calendar-star text-primary"></i>
                        <span class="fw-bold">{{ __('Installment Schedule Preview') }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="schedule-count-pill" id="scheduleTotalMonthsPill">1 {{ __('Month') }}</span>
                        <i class="bx bx-chevron-down accordion-arrow" id="accordionArrow"></i>
                    </div>
                </div>
                <div class="schedule-accordion-body" id="scheduleAccordionBody" style="display: none;">
                    <div class="table-responsive">
                        <table class="schedule-mini-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('Due Date') }}</th>
                                    <th class="text-end">{{ __('Amount') }}</th>
                                </tr>
                            </thead>
                            <tbody id="scheduleTableBody">
                                <!-- Filled by JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Justification & Reason -->
        <div class="loan-section-card">
            <h3 class="loan-section-title">
                <span>{{ __('Request Justification') }}</span>
                <i class="bx bx-edit-alt"></i>
            </h3>
            <div class="loan-input-group mb-0">
                <textarea name="reason"
                          id="loanReason"
                          class="loan-form-control loan-textarea"
                          rows="3"
                          placeholder="{{ __('Explain reason for financial advance (e.g. emergency expense, family support, personal requirement)...') }}"></textarea>
            </div>
        </div>

        <!-- 5. Supporting Attachment (Optional) -->
        <div class="loan-section-card">
            <h3 class="loan-section-title">
                <span>{{ __('Supporting Attachment') }}</span>
                <i class="bx bx-paperclip"></i>
            </h3>
            <p class="loan-section-desc">{{ __('Attach any supporting document or financial notice if available (PDF, JPG, PNG).') }}</p>

            <div class="loan-file-upload-zone" onclick="document.getElementById('loanAttachmentInput').click()">
                <input type="file"
                       name="attachment"
                       id="loanAttachmentInput"
                       class="d-none"
                       accept=".pdf,.jpg,.jpeg,.png"
                       onchange="handleAttachmentSelected(this)" />
                <div class="upload-icon-wrapper" id="uploadIconWrapper">
                    <i class="bx bx-cloud-upload"></i>
                </div>
                <div class="upload-text-group">
                    <span class="upload-main-text" id="uploadMainText">{{ __('Click to upload attachment') }}</span>
                    <span class="upload-sub-text" id="uploadSubText">{{ __('Max file size: 5MB') }}</span>
                </div>
            </div>
        </div>

        <!-- 6. Submit Action Button -->
        <div class="loan-submit-section">
            <button type="submit" class="loan-submit-btn" id="loanSubmitBtn">
                <span class="btn-text">{{ __('Submit Advance Request') }}</span>
                <i class="bx {{ app()->getLocale() === 'ar' ? 'bx-left-arrow-alt' : 'bx-right-arrow-alt' }}"></i>
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
let currentLoanType = 'salary_advance';
let calcDebounceTimer = null;

function selectLoanType(type, cardEl) {
    currentLoanType = type;
    document.getElementById('selectedLoanType').value = type;

    document.querySelectorAll('.loan-type-radio-card').forEach(el => el.classList.remove('active'));
    if (cardEl) cardEl.classList.add('active');

    const installmentsWrapper = document.getElementById('installmentsCountWrapper');
    const installmentsInput = document.getElementById('loanInstallmentsCount');
    const scheduleSection = document.getElementById('scheduleAccordionSection');

    if (type === 'salary_advance') {
        installmentsWrapper.style.display = 'none';
        installmentsInput.value = 1;
        scheduleSection.style.display = 'none';
        document.getElementById('monthlySubText').innerText = "{{ __('Deducted in full from next payroll') }}";
    } else {
        installmentsWrapper.style.display = 'block';
        if (parseInt(installmentsInput.value) <= 1) {
            installmentsInput.value = 6;
        }
        scheduleSection.style.display = 'block';
        document.getElementById('monthlySubText').innerText = "{{ __('Monthly installment per payroll') }}";
    }

    triggerRealtimeCalculation();
}

function stepInstallment(delta) {
    const input = document.getElementById('loanInstallmentsCount');
    let val = parseInt(input.value) || 1;
    val = Math.max(1, Math.min(24, val + delta));
    input.value = val;
    triggerRealtimeCalculation();
}

function toggleScheduleAccordion() {
    const body = document.getElementById('scheduleAccordionBody');
    const arrow = document.getElementById('accordionArrow');
    if (body.style.display === 'none') {
        body.style.display = 'block';
        arrow.classList.add('rotated');
    } else {
        body.style.display = 'none';
        arrow.classList.remove('rotated');
    }
}

function triggerRealtimeCalculation() {
    clearTimeout(calcDebounceTimer);
    calcDebounceTimer = setTimeout(performCalculation, 280);
}

function performCalculation() {
    const amount = parseFloat(document.getElementById('loanAmount').value) || 0;
    const installments = currentLoanType === 'salary_advance' ? 1 : (parseInt(document.getElementById('loanInstallmentsCount').value) || 1);
    const startDate = document.getElementById('loanStartDate').value;
    const alertBox = document.getElementById('liveEligibilityAlert');
    const monthlyDisplay = document.getElementById('calculatedMonthlyValue');

    if (amount <= 0) {
        monthlyDisplay.innerText = '0.00';
        alertBox.classList.add('d-none');
        return;
    }

    const estimatedMonthly = (amount / installments).toFixed(2);
    monthlyDisplay.innerText = estimatedMonthly;

    // AJAX verification against policy
    fetch("{{ route('portal.loans.calculate') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            amount: amount,
            type: currentLoanType,
            installments_count: installments,
            start_date: startDate
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            monthlyDisplay.innerText = parseFloat(data.monthly_installment).toFixed(2);

            // Update schedule table
            if (data.schedule && data.schedule.length > 0) {
                document.getElementById('scheduleTotalMonthsPill').innerText = data.schedule.length + " {{ __('Months') }}";
                let rowsHtml = '';
                data.schedule.forEach(item => {
                    rowsHtml += `
                        <tr>
                            <td class="font-monospace text-muted py-1">#${item.installment_number}</td>
                            <td class="py-1">${item.due_date} <small class="text-muted">(${item.due_date_formatted})</small></td>
                            <td class="text-end fw-bold text-success py-1">${parseFloat(item.amount).toFixed(2)}</td>
                        </tr>
                    `;
                });
                document.getElementById('scheduleTableBody').innerHTML = rowsHtml;
            }

            if (!data.is_eligible) {
                alertBox.className = 'loan-feedback-alert alert-danger';
                alertBox.innerHTML = `<i class="bx bx-error-circle me-1"></i> ${data.ineligibility_reason}`;
                alertBox.classList.remove('d-none');
                document.getElementById('loanSubmitBtn').disabled = true;
            } else {
                alertBox.className = 'loan-feedback-alert alert-success';
                alertBox.innerHTML = `<i class="bx bx-check-circle me-1"></i> {{ __('Amount and installment schedule comply with financial eligibility.') }}`;
                alertBox.classList.remove('d-none');
                document.getElementById('loanSubmitBtn').disabled = false;
            }
        }
    })
    .catch(err => {
        console.error('Calculation error:', err);
    });
}

function handleAttachmentSelected(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        document.getElementById('uploadMainText').innerText = file.name;
        document.getElementById('uploadSubText').innerText = (file.size / 1024 / 1024).toFixed(2) + ' MB';
        document.getElementById('uploadIconWrapper').classList.add('file-ready');
        document.getElementById('uploadIconWrapper').innerHTML = '<i class="bx bx-check"></i>';
    }
}

// Flatpickr initialization
document.addEventListener('DOMContentLoaded', function() {
    if (typeof flatpickr !== 'undefined') {
        flatpickr("#loanStartDate", {
            dateFormat: "Y-m-d",
            defaultDate: "{{ date('Y-m-01', strtotime('+1 month')) }}",
            minDate: "today",
            locale: "{{ app()->getLocale() === 'ar' ? 'ar' : 'default' }}"
        });
    }

    // Intercept form submission for smooth UX
    const form = document.getElementById('loanSubmitForm');
    form.addEventListener('submit', function() {
        const btn = document.getElementById('loanSubmitBtn');
        btn.disabled = true;
        btn.querySelector('.btn-text').innerText = "{{ __('Submitting Request...') }}";
    });
});
</script>
@endpush
