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

@push('styles')
<style>
/* ==============================================================================
 * Mobile Financial Advance / Loan Styling (PWA Optimized)
 * ============================================================================== */
.loan-page-container {
    padding-bottom: 90px;
    max-width: 440px;
    margin: 0 auto;
}

.loan-nav-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 14px;
    padding: 0 4px;
}

.loan-back-btn {
    width: 38px;
    height: 38px;
    border-radius: 12px;
    background: #ffffff;
    border: 1px solid rgba(0, 51, 75, 0.08);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--portal-navy-deep);
    font-size: 20px;
    text-decoration: none;
    box-shadow: 0 2px 8px rgba(0, 51, 75, 0.04);
    transition: all 0.2s ease;
}

.loan-back-btn:active {
    transform: scale(0.95);
    background: #f8fafc;
}

.loan-header-title {
    font-family: 'Tajawal', sans-serif;
    font-size: 17px;
    font-weight: 800;
    color: var(--portal-navy-deep);
    margin: 0;
}

/* 1. Hero Balance Card */
.loan-hero-card {
    background: linear-gradient(135deg, #002A44 0%, #004B6B 100%);
    color: #ffffff;
    border-radius: 18px;
    padding: 16px;
    margin-bottom: 16px;
    box-shadow: 0 8px 24px rgba(0, 42, 68, 0.18);
    position: relative;
    overflow: hidden;
}

.loan-hero-card::after {
    content: '';
    position: absolute;
    top: -30px;
    inset-inline-end: -30px;
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(254, 174, 44, 0.18) 0%, transparent 70%);
    pointer-events: none;
}

.loan-hero-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 14px;
}

.loan-hero-title-group {
    display: flex;
    align-items: center;
    gap: 10px;
}

.loan-hero-icon-box {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    background: rgba(254, 174, 44, 0.15);
    border: 1px solid rgba(254, 174, 44, 0.35);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: var(--portal-accent-gold);
}

.loan-hero-title {
    font-family: 'Tajawal', sans-serif;
    font-size: 15px;
    font-weight: 700;
    margin: 0;
    color: #ffffff;
}

.loan-hero-subtitle {
    font-size: 10.5px;
    color: #CBD5E1;
    margin: 0;
}

.loan-history-pill {
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #ffffff;
    font-size: 11px;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 20px;
    display: flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
}

.loan-history-pill .dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: var(--portal-accent-gold);
    box-shadow: 0 0 6px var(--portal-accent-gold);
}

.loan-stats-grid {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 8px;
    background: rgba(0, 0, 0, 0.18);
    border-radius: 12px;
    padding: 10px 8px;
    border: 1px solid rgba(255, 255, 255, 0.08);
}

.loan-stat-col {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}

.loan-stat-label {
    font-size: 10px;
    color: #94A3B8;
    margin-bottom: 2px;
}

.loan-stat-val {
    font-size: 14px;
    font-weight: 800;
    color: #ffffff;
    font-family: 'Inter', 'Tajawal', monospace;
}

.loan-stat-val.gold {
    color: var(--portal-accent-gold);
}

.loan-stat-sub {
    font-size: 9.5px;
    color: #CBD5E1;
    margin-top: 2px;
}

.loan-existing-debt-banner {
    margin-top: 10px;
    background: rgba(234, 88, 12, 0.15);
    border: 1px solid rgba(234, 88, 12, 0.35);
    border-radius: 10px;
    padding: 6px 10px;
    font-size: 11px;
    color: #FED7AA;
    display: flex;
    align-items: center;
    gap: 6px;
}

/* 2. Section Card */
.loan-section-card {
    background: #ffffff;
    border-radius: 16px;
    padding: 16px;
    margin-bottom: 14px;
    border: 1px solid rgba(0, 51, 75, 0.06);
    box-shadow: 0 4px 14px rgba(0, 51, 75, 0.04);
}

.loan-section-title {
    font-family: 'Tajawal', sans-serif;
    font-size: 14px;
    font-weight: 800;
    color: var(--portal-navy-deep);
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 12px;
}

.loan-section-title i {
    color: var(--portal-accent);
    font-size: 18px;
}

.loan-section-desc {
    font-size: 11px;
    color: var(--portal-text-muted);
    margin-bottom: 10px;
}

/* 3. Types Selection Grid */
.loan-types-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
}

.loan-type-radio-card {
    border: 2px solid rgba(0, 51, 75, 0.08);
    border-radius: 14px;
    padding: 12px 10px;
    cursor: pointer;
    background: #FAFAFC;
    transition: all 0.2s ease;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.loan-type-radio-card.active {
    border-color: var(--portal-accent);
    background: #FFFDF8;
    box-shadow: 0 4px 12px rgba(254, 174, 44, 0.15);
}

.loan-type-card-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
}

.loan-type-card-icon {
    width: 34px;
    height: 34px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}

.loan-type-card-icon.icon-gold {
    background: #FEF3C7;
    color: #D97706;
}

.loan-type-card-icon.icon-blue {
    background: #EFF6FF;
    color: #0284C7;
}

.loan-type-check-dot {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    border: 2px solid rgba(0, 51, 75, 0.15);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    color: transparent;
    transition: all 0.2s ease;
}

.loan-type-radio-card.active .loan-type-check-dot {
    background: var(--portal-accent);
    border-color: var(--portal-accent);
    color: #ffffff;
}

.loan-type-card-title {
    font-size: 13px;
    font-weight: 700;
    color: var(--portal-navy-deep);
    margin: 0 0 3px 0;
}

.loan-type-card-desc {
    font-size: 10px;
    color: var(--portal-text-muted);
    margin: 0;
    line-height: 1.35;
}

/* 4. Inputs */
.loan-input-group {
    display: flex;
    flex-direction: column;
}

.loan-field-label {
    font-size: 12px;
    font-weight: 700;
    color: var(--portal-navy-deep);
    margin-bottom: 6px;
}

.required-star {
    color: #EF4444;
}

.loan-currency-input-wrapper,
.loan-date-input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.loan-form-control {
    width: 100%;
    border: 1.5px solid rgba(0, 51, 75, 0.12);
    border-radius: 12px;
    padding: 10px 14px;
    font-size: 14px;
    font-family: inherit;
    color: var(--portal-text-main);
    background: #ffffff;
    outline: none;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.loan-form-control:focus {
    border-color: var(--portal-navy-deep);
    box-shadow: 0 0 0 3px rgba(0, 42, 68, 0.08);
}

.loan-amount-input {
    font-size: 18px;
    font-weight: 800;
    font-family: 'Inter', monospace;
    padding-inline-end: 55px;
}

.loan-currency-addon {
    position: absolute;
    inset-inline-end: 14px;
    font-size: 13px;
    font-weight: 700;
    color: var(--portal-text-muted);
    pointer-events: none;
}

.loan-date-input-wrapper i {
    position: absolute;
    inset-inline-end: 14px;
    font-size: 18px;
    color: var(--portal-text-muted);
    pointer-events: none;
}

.loan-field-helper {
    font-size: 10.5px;
    color: var(--portal-text-muted);
    margin-top: 4px;
}

/* Stepper for Installments */
.loan-stepper-wrapper {
    display: flex;
    align-items: center;
    gap: 8px;
}

.stepper-btn {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    border: 1.5px solid rgba(0, 51, 75, 0.12);
    background: #F8FAFC;
    color: var(--portal-navy-deep);
    font-size: 20px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.15s ease;
}

.stepper-btn:active {
    background: #E2E8F0;
    transform: scale(0.95);
}

/* Monthly Preview Box */
.loan-monthly-preview-box {
    background: linear-gradient(135deg, #F0FDF4 0%, #DCFCE7 100%);
    border: 1px solid #BBF7D0;
    border-radius: 14px;
    padding: 12px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.monthly-label {
    font-size: 12px;
    font-weight: 700;
    color: #166534;
    display: block;
}

.monthly-sub {
    font-size: 10px;
    color: #15803D;
    display: block;
}

.monthly-preview-val {
    font-size: 18px;
    font-weight: 800;
    color: #15803D;
    font-family: 'Inter', monospace;
    display: flex;
    align-items: baseline;
    gap: 4px;
}

.monthly-preview-val small {
    font-size: 11px;
}

/* Live Alert */
.loan-feedback-alert {
    margin-top: 10px;
    padding: 8px 12px;
    border-radius: 10px;
    font-size: 11px;
    line-height: 1.4;
}

.loan-feedback-alert.alert-danger {
    background: #FEF2F2;
    border: 1px solid #FECACA;
    color: #991B1B;
}

.loan-feedback-alert.alert-success {
    background: #F0FDF4;
    border: 1px solid #BBF7D0;
    color: #166534;
}

/* Accordion */
.loan-schedule-accordion {
    border: 1px solid rgba(0, 51, 75, 0.08);
    border-radius: 12px;
    overflow: hidden;
}

.schedule-accordion-header {
    background: #F8FAFC;
    padding: 10px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    cursor: pointer;
    font-size: 12px;
}

.schedule-count-pill {
    background: #E2E8F0;
    color: var(--portal-navy-deep);
    font-size: 10.5px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 12px;
}

.accordion-arrow {
    font-size: 18px;
    color: var(--portal-text-muted);
    transition: transform 0.2s ease;
}

.accordion-arrow.rotated {
    transform: rotate(180deg);
}

.schedule-accordion-body {
    padding: 10px 12px;
    background: #ffffff;
}

.schedule-mini-table {
    width: 100%;
    font-size: 11.5px;
    border-collapse: collapse;
}

.schedule-mini-table th {
    color: var(--portal-text-muted);
    font-weight: 600;
    padding-bottom: 6px;
    border-bottom: 1px solid #F1F5F9;
}

.schedule-mini-table td {
    padding: 6px 0;
    border-bottom: 1px solid #F8FAFC;
    color: var(--portal-navy-deep);
}

/* Textarea & File */
.loan-textarea {
    resize: none;
    border-radius: 12px;
}

.loan-file-upload-zone {
    border: 2px dashed rgba(0, 51, 75, 0.15);
    border-radius: 14px;
    padding: 16px;
    display: flex;
    align-items: center;
    gap: 12px;
    background: #F8FAFC;
    cursor: pointer;
    transition: all 0.2s ease;
}

.loan-file-upload-zone:hover,
.loan-file-upload-zone:active {
    border-color: var(--portal-accent);
    background: #FFFDF8;
}

.upload-icon-wrapper {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    background: #EFF6FF;
    color: #0284C7;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    flex-shrink: 0;
}

.upload-icon-wrapper.file-ready {
    background: #DCFCE7;
    color: #15803D;
}

.upload-text-group {
    display: flex;
    flex-direction: column;
}

.upload-main-text {
    font-size: 12px;
    font-weight: 700;
    color: var(--portal-navy-deep);
}

.upload-sub-text {
    font-size: 10px;
    color: var(--portal-text-muted);
}

/* Submit Button */
.loan-submit-section {
    position: sticky;
    bottom: 82px;
    z-index: 10;
    padding-top: 4px;
}

.loan-submit-btn {
    width: 100%;
    background: linear-gradient(135deg, #FEAE2C 0%, #EA580C 100%);
    color: #ffffff;
    border: none;
    border-radius: 14px;
    padding: 14px 20px;
    font-family: 'Tajawal', sans-serif;
    font-size: 15px;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    box-shadow: 0 8px 22px rgba(234, 88, 12, 0.28);
    cursor: pointer;
    transition: all 0.2s ease;
}

.loan-submit-btn:active {
    transform: scale(0.98);
    box-shadow: 0 4px 12px rgba(234, 88, 12, 0.2);
}

.loan-submit-btn:disabled {
    opacity: 0.6;
    pointer-events: none;
}
</style>
@endpush

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
