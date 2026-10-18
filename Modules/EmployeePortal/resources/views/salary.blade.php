@extends('employeeportal::layouts.master')

@section('title', __('Salary Details & Allowances'))

@section('content')
<div class="salary-page-container">
    <!-- Top Navigation Header -->
    <div class="salary-nav-header">
        <a href="{{ route('portal.profile') }}" class="salary-back-btn" title="{{ __('Back') }}">
            <i class="bx {{ app()->getLocale() === 'ar' ? 'bx-chevron-right' : 'bx-chevron-left' }}"></i>
        </a>
        <h1 class="salary-header-title">{{ __('Salary Details & Allowances') }}</h1>
        <a href="{{ route('portal.loans.index', ['from' => 'profile']) }}" class="salary-back-btn" title="{{ __('Loans & Advances') }}">
            <i class="bx bx-coin-stack"></i>
        </a>
    </div>

    <!-- 1. Hero Salary Overview Card -->
    <div class="salary-hero-card">
        <div class="salary-hero-top">
            <div class="d-flex align-items-center gap-2">
                <div class="salary-hero-icon-box">
                    <i class="bx bx-wallet"></i>
                </div>
                <div>
                    <span class="salary-hero-label">{{ __('Estimated Net Monthly Salary') }}</span>
                    <span class="salary-hero-month">{{ date('F Y') }}</span>
                </div>
            </div>

            <span class="salary-status-pill">
                <span class="dot"></span>
                <span>{{ __('Active Base') }}</span>
            </span>
        </div>

        <!-- Big Net Salary Value -->
        <div class="salary-hero-amount-center">
            <span class="salary-currency">{{ __('KWD') }}</span>
            <span class="salary-amount-number">{{ number_format($netEstimated, 2) }}</span>
        </div>

        <!-- 3-Metrics Grid -->
        <div class="salary-stats-grid">
            <div class="salary-stat-col">
                <span class="stat-label">{{ __('Basic Salary') }}</span>
                <span class="stat-val font-monospace">{{ number_format($basicSalary, 2) }}</span>
                <span class="stat-sub">{{ __('Monthly Base') }}</span>
            </div>

            <div class="salary-stat-col highlighted">
                <span class="stat-label">{{ __('Allowances') }}</span>
                <span class="stat-val text-emerald font-monospace">+{{ number_format($totalAllowances, 2) }}</span>
                <span class="stat-sub">{{ $allowances->count() }} {{ __('Items') }}</span>
            </div>

            <div class="salary-stat-col">
                <span class="stat-label">{{ __('Deductions') }}</span>
                <span class="stat-val text-rose font-monospace">-{{ number_format($insuranceAmount + $monthlyLoansCut, 2) }}</span>
                <span class="stat-sub">{{ __('Insurance & Loans') }}</span>
            </div>
        </div>
    </div>

    <!-- 2. Salary Structure & Allowances Card -->
    <div class="salary-section-card">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h3 class="salary-section-title mb-0">
                <span>{{ __('Salary & Allowance Structure') }}</span>
                <i class="bx bx-layer"></i>
            </h3>
            <span class="badge bg-label-primary px-2 py-1 rounded-pill font-11">
                {{ __('Gross:') }} {{ number_format($grossSalary, 2) }} {{ __('KWD') }}
            </span>
        </div>

        <!-- Basic Salary Item -->
        <div class="allowance-item-card basic-item">
            <div class="d-flex align-items-center gap-3">
                <div class="allowance-icon-box icon-emerald">
                    <i class="bx bx-check-shield"></i>
                </div>
                <div>
                    <h4 class="allowance-title">{{ __('Basic Monthly Salary') }}</h4>
                    <span class="allowance-sub">{{ __('Contractual Core Base') }}</span>
                </div>
            </div>
            <div class="text-end">
                <span class="allowance-amount">{{ number_format($basicSalary, 2) }}</span>
                <small class="allowance-curr">{{ __('KWD') }}</small>
            </div>
        </div>

        <!-- Active Allowances List -->
        @if($allowances->isNotEmpty())
            <div class="mt-2 d-flex flex-column gap-2">
                @foreach($allowances as $al)
                    @php
                        $alTypeName = $al->allowanceType?->translate(app()->getLocale())?->name
                                    ?? $al->allowanceType?->translate('ar')?->name
                                    ?? $al->allowanceType?->name
                                    ?? __('Allowance');
                    @endphp
                    <div class="allowance-item-card">
                        <div class="d-flex align-items-center gap-3">
                            <div class="allowance-icon-box icon-blue">
                                <i class="bx bx-plus-circle"></i>
                            </div>
                            <div>
                                <h4 class="allowance-title">{{ $alTypeName }}</h4>
                                <span class="allowance-sub">
                                    {{ $al->is_recurring ? __('Recurring Monthly') : __('One-time Allowance') }}
                                    @if($al->effective_date)
                                        • {{ $al->effective_date->format('Y-m') }}
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="allowance-amount text-primary">+{{ number_format($al->amount, 2) }}</span>
                            <small class="allowance-curr">{{ __('KWD') }}</small>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="salary-empty-hint mt-2">
                <i class="bx bx-info-circle"></i>
                <span>{{ __('No extra allowances currently assigned to this account.') }}</span>
            </div>
        @endif

        <!-- Gross Summary Footer -->
        <div class="salary-calc-footer mt-3">
            <span class="calc-label">{{ __('Total Gross Monthly Salary:') }}</span>
            <span class="calc-val">{{ number_format($grossSalary, 2) }} {{ __('KWD') }}</span>
        </div>
    </div>

    <!-- 3. Monthly Deductions & Commitments Card -->
    <div class="salary-section-card">
        <h3 class="salary-section-title">
            <span>{{ __('Monthly Deductions & Commitments') }}</span>
            <i class="bx bx-minus-circle"></i>
        </h3>

        <div class="d-flex flex-column gap-2">
            <!-- 3.1 التأمينات الاجتماعية -->
            <div class="deduction-item-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="deduction-icon-box icon-amber">
                        <i class="bx bx-shield-quarter"></i>
                    </div>
                    <div>
                        <h4 class="deduction-title">{{ __('Social Insurance') }}</h4>
                        <span class="deduction-sub">{{ __('Official Pension Rate:') }} {{ $insurancePct }}%</span>
                    </div>
                </div>
                <div class="text-end">
                    <span class="deduction-amount text-danger">-{{ number_format($insuranceAmount, 2) }}</span>
                    <small class="deduction-curr">{{ __('KWD') }}</small>
                </div>
            </div>

            <!-- 3.2 أقساط السلف القائمة -->
            @if($monthlyLoansCut > 0)
                <div class="deduction-item-card">
                    <div class="d-flex align-items-center gap-3">
                        <div class="deduction-icon-box icon-orange">
                            <i class="bx bx-coin-stack"></i>
                        </div>
                        <div>
                            <h4 class="deduction-title">{{ __('Active Loan Installment') }}</h4>
                            <span class="deduction-sub">{{ __('Remaining Debt:') }} {{ number_format($activeLoansDebt, 2) }} {{ __('KWD') }}</span>
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="deduction-amount text-danger">-{{ number_format($monthlyLoansCut, 2) }}</span>
                        <small class="deduction-curr">{{ __('KWD') }}</small>
                    </div>
                </div>
            @endif
        </div>

        @if($monthlyLoansCut > 0)
            <div class="mt-3 text-end">
                <a href="{{ route('portal.loans.index', ['from' => 'profile']) }}" class="salary-link-pill">
                    <span>{{ __('View Loans Schedule & Details') }}</span>
                    <i class="bx {{ app()->getLocale() === 'ar' ? 'bx-left-arrow-alt' : 'bx-right-arrow-alt' }}"></i>
                </a>
            </div>
        @endif
    </div>

    <!-- 4. Bank Account & Direct Deposit Information -->
    <div class="salary-section-card">
        <h3 class="salary-section-title">
            <span>{{ __('Bank Account & Direct Deposit') }}</span>
            <i class="bx bxs-bank"></i>
        </h3>

        <div class="bank-details-card">
            <div class="bank-header-row">
                <div class="d-flex align-items-center gap-2">
                    <i class="bx bxs-institution text-primary fs-4"></i>
                    <span class="bank-name-text">{{ $bankInfo['bank_name'] }}</span>
                </div>
                <span class="bank-verified-badge">
                    <i class="bx bx-check-circle"></i>
                    <span>{{ __('Verified') }}</span>
                </span>
            </div>

            <div class="bank-field-item mt-3">
                <span class="field-label">{{ __('Account Holder Name') }}</span>
                <span class="field-val">{{ $bankInfo['account_name'] }}</span>
            </div>

            @if(!empty($bankInfo['account_number']) && $bankInfo['account_number'] !== '—')
            <div class="bank-field-item mt-2">
                <span class="field-label">{{ __('Account Number') }}</span>
                <span class="field-val font-monospace">{{ $bankInfo['account_number'] }}</span>
            </div>
            @endif

            @if(!empty($bankInfo['iban']) && $bankInfo['iban'] !== '—')
            <div class="bank-iban-box mt-3">
                <div>
                    <span class="iban-label">{{ __('IBAN (International Bank Account Number)') }}</span>
                    <span class="iban-val font-monospace" id="ibanCodeText">{{ $bankInfo['iban'] }}</span>
                </div>
                <button type="button" class="copy-iban-btn" onclick="copyIbanToClipboard()" title="{{ __('Copy IBAN') }}">
                    <i class="bx bx-copy" id="copyIbanIcon"></i>
                </button>
            </div>
            @endif
        </div>
    </div>

    <!-- 5. Monthly Payslips History (سجل مسيرات الرواتب) -->
    <div class="salary-section-card">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h3 class="salary-section-title mb-0">
                <span>{{ __('Monthly Payslips History') }}</span>
                <i class="bx bx-receipt"></i>
            </h3>
            <span class="badge bg-label-info px-2 py-1 rounded-pill font-11">
                {{ $payrollHistory->count() }} {{ __('Payslips') }}
            </span>
        </div>

        <div class="payslips-list d-flex flex-column gap-3">
            @forelse($payrollHistory as $pi)
                @php
                    $payroll = $pi->payroll;
                    $paymentStatus = $pi->payment_status;
                    $isPaid = ($paymentStatus === \Modules\HR\Enums\PayrollPaymentStatus::PAID);
                    $monthLabel = $payroll?->payroll_month ?? ($payroll?->created_at ? $payroll->created_at->format('Y-m') : __('Payroll'));
                @endphp
                <div class="payslip-card-item">
                    <div class="payslip-top-row">
                        <div>
                            <span class="payslip-month-title">{{ __('Payslip') }} • {{ $monthLabel }}</span>
                            <span class="payslip-ref-no">{{ $payroll?->payroll_number ?? '#'.$pi->payroll_id }}</span>
                        </div>
                        <span class="badge {{ $paymentStatus?->badgeClass() ?? 'bg-label-secondary' }} px-2 py-1 rounded-pill font-11">
                            {{ $paymentStatus?->label() ?? '-' }}
                        </span>
                    </div>

                    <!-- Net Salary -->
                    <div class="payslip-net-row mt-2">
                        <div class="d-flex flex-column">
                            <span class="net-title">{{ __('Net Transferred') }}</span>
                            <span class="net-val font-monospace text-emerald">{{ number_format($pi->net_salary, 2) }} <small>{{ __('KWD') }}</small></span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn-toggle-slip" onclick="toggleSlipDetails('slipDetails{{ $pi->id }}', this)">
                                <span>{{ __('Breakdown') }}</span>
                                <i class="bx bx-chevron-down"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Expandable Slip Breakdown -->
                    <div class="slip-details-accordion mt-2" id="slipDetails{{ $pi->id }}" style="display: none;">
                        <div class="slip-breakdown-grid">
                            <div class="slip-sub-row">
                                <span class="slip-sub-label">{{ __('Basic Salary:') }}</span>
                                <span class="slip-sub-val font-monospace">{{ number_format($pi->basic_salary, 2) }}</span>
                            </div>
                            <div class="slip-sub-row">
                                <span class="slip-sub-label">{{ __('Allowances:') }}</span>
                                <span class="slip-sub-val text-emerald font-monospace">+{{ number_format($pi->allowances_amount, 2) }}</span>
                            </div>
                            @if($pi->overtime_amount > 0)
                            <div class="slip-sub-row">
                                <span class="slip-sub-label">{{ __('Overtime:') }}</span>
                                <span class="slip-sub-val text-emerald font-monospace">+{{ number_format($pi->overtime_amount, 2) }}</span>
                            </div>
                            @endif
                            @if($pi->rewards_amount > 0)
                            <div class="slip-sub-row">
                                <span class="slip-sub-label">{{ __('Rewards & Bonuses:') }}</span>
                                <span class="slip-sub-val text-emerald font-monospace">+{{ number_format($pi->rewards_amount, 2) }}</span>
                            </div>
                            @endif
                            <div class="slip-sub-row border-top pt-1 mt-1">
                                <span class="slip-sub-label fw-bold">{{ __('Total Gross:') }}</span>
                                <span class="slip-sub-val font-monospace fw-bold">{{ number_format($pi->gross_salary, 2) }}</span>
                            </div>

                            <!-- Deductions -->
                            @if($pi->absence_deduction > 0)
                            <div class="slip-sub-row">
                                <span class="slip-sub-label text-rose">{{ __('Absence Deduction:') }}</span>
                                <span class="slip-sub-val text-rose font-monospace">-{{ number_format($pi->absence_deduction, 2) }}</span>
                            </div>
                            @endif
                            @if($pi->late_deduction > 0)
                            <div class="slip-sub-row">
                                <span class="slip-sub-label text-rose">{{ __('Late Deduction:') }}</span>
                                <span class="slip-sub-val text-rose font-monospace">-{{ number_format($pi->late_deduction, 2) }}</span>
                            </div>
                            @endif
                            @if($pi->loans_deduction > 0)
                            <div class="slip-sub-row">
                                <span class="slip-sub-label text-rose">{{ __('Loan Installment Cut:') }}</span>
                                <span class="slip-sub-val text-rose font-monospace">-{{ number_format($pi->loans_deduction, 2) }}</span>
                            </div>
                            @endif
                            @if($pi->insurance_deduction > 0)
                            <div class="slip-sub-row">
                                <span class="slip-sub-label text-rose">{{ __('Social Insurance Cut:') }}</span>
                                <span class="slip-sub-val text-rose font-monospace">-{{ number_format($pi->insurance_deduction, 2) }}</span>
                            </div>
                            @endif
                            <div class="slip-sub-row border-top pt-1 mt-1">
                                <span class="slip-sub-label fw-bold text-rose">{{ __('Total Deductions:') }}</span>
                                <span class="slip-sub-val font-monospace fw-bold text-rose">-{{ number_format($pi->total_deductions, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="salary-empty-hint text-center py-4">
                    <i class="bx bx-receipt fs-1 text-muted d-block mb-2 opacity-50"></i>
                    <p class="mb-0 text-muted small">{{ __('No payroll history records registered yet.') }}</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* ==============================================================================
 * Mobile Salary Details & Allowances Styling (PWA Optimized)
 * ============================================================================== */
.salary-page-container {
    padding-bottom: 95px;
    max-width: 440px;
    margin: 0 auto;
}

.salary-nav-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 14px;
    padding: 0 4px;
}

.salary-back-btn {
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

.salary-back-btn:active {
    transform: scale(0.95);
    background: #f8fafc;
}

.salary-header-title {
    font-family: 'Tajawal', sans-serif;
    font-size: 16.5px;
    font-weight: 800;
    color: var(--portal-navy-deep);
    margin: 0;
}

/* 1. Hero Card */
.salary-hero-card {
    background: linear-gradient(135deg, #002A44 0%, #004B6B 100%);
    color: #ffffff;
    border-radius: 18px;
    padding: 18px 16px;
    margin-bottom: 14px;
    box-shadow: 0 8px 24px rgba(0, 42, 68, 0.18);
    position: relative;
    overflow: hidden;
}

.salary-hero-card::after {
    content: '';
    position: absolute;
    top: -30px;
    inset-inline-end: -30px;
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(16, 185, 129, 0.2) 0%, transparent 70%);
    pointer-events: none;
}

.salary-hero-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 14px;
}

.salary-hero-icon-box {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: rgba(16, 185, 129, 0.18);
    border: 1px solid rgba(16, 185, 129, 0.35);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #34D399;
    font-size: 20px;
}

.salary-hero-label {
    font-size: 11px;
    color: #CBD5E1;
    display: block;
}

.salary-hero-month {
    font-size: 13px;
    font-weight: 700;
    color: #ffffff;
    display: block;
}

.salary-status-pill {
    background: rgba(16, 185, 129, 0.18);
    border: 1px solid rgba(16, 185, 129, 0.35);
    color: #A7F3D0;
    font-size: 11px;
    font-weight: 700;
    padding: 3px 9px;
    border-radius: 20px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.salary-status-pill .dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #34D399;
    box-shadow: 0 0 6px #34D399;
}

.salary-hero-amount-center {
    text-align: center;
    margin-bottom: 16px;
    display: flex;
    align-items: baseline;
    justify-content: center;
    gap: 6px;
}

.salary-currency {
    font-size: 14px;
    font-weight: 700;
    color: var(--portal-accent-gold);
}

.salary-amount-number {
    font-size: 32px;
    font-weight: 900;
    font-family: 'Inter', monospace;
    letter-spacing: -0.5px;
    color: #ffffff;
}

.salary-stats-grid {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 8px;
    background: rgba(0, 0, 0, 0.2);
    border-radius: 12px;
    padding: 10px 6px;
    border: 1px solid rgba(255, 255, 255, 0.08);
}

.salary-stat-col {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}

.stat-label {
    font-size: 9.5px;
    color: #94A3B8;
    margin-bottom: 2px;
}

.stat-val {
    font-size: 13.5px;
    font-weight: 800;
    color: #ffffff;
}

.text-emerald {
    color: #34D399 !important;
}

.text-rose {
    color: #F87171 !important;
}

.stat-sub {
    font-size: 9px;
    color: #CBD5E1;
    margin-top: 2px;
}

/* 2. Section Cards */
.salary-section-card {
    background: #ffffff;
    border-radius: 16px;
    padding: 16px;
    margin-bottom: 14px;
    border: 1px solid rgba(0, 51, 75, 0.06);
    box-shadow: 0 4px 14px rgba(0, 51, 75, 0.04);
}

.salary-section-title {
    font-family: 'Tajawal', sans-serif;
    font-size: 14px;
    font-weight: 800;
    color: var(--portal-navy-deep);
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 12px;
}

.salary-section-title i {
    color: var(--portal-accent);
    font-size: 18px;
}

/* Allowances Items */
.allowance-item-card {
    background: #F8FAFC;
    border: 1px solid #F1F5F9;
    border-radius: 12px;
    padding: 10px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.allowance-item-card.basic-item {
    background: #F0FDF4;
    border-color: #DCFCE7;
}

.allowance-icon-box {
    width: 34px;
    height: 34px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 17px;
    flex-shrink: 0;
}

.icon-emerald {
    background: #DCFCE7;
    color: #166534;
}

.icon-blue {
    background: #EFF6FF;
    color: #0284C7;
}

.icon-amber {
    background: #FEF3C7;
    color: #D97706;
}

.icon-orange {
    background: #FFEDD5;
    color: #EA580C;
}

.allowance-title {
    font-size: 12.5px;
    font-weight: 700;
    color: var(--portal-navy-deep);
    margin: 0;
}

.allowance-sub {
    font-size: 10px;
    color: var(--portal-text-muted);
}

.allowance-amount {
    font-size: 14px;
    font-weight: 800;
    font-family: 'Inter', monospace;
    color: var(--portal-navy-deep);
}

.allowance-curr {
    font-size: 10px;
    font-weight: 600;
    color: var(--portal-text-muted);
}

.salary-calc-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 12px;
    background: #F1F5F9;
    border-radius: 10px;
    font-size: 12px;
}

.calc-label {
    font-weight: 700;
    color: var(--portal-navy-deep);
}

.calc-val {
    font-family: 'Inter', monospace;
    font-weight: 800;
    color: var(--portal-navy-deep);
}

/* Deduction items */
.deduction-item-card {
    background: #FFFDFD;
    border: 1px solid #FEE2E2;
    border-radius: 12px;
    padding: 10px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.deduction-icon-box {
    width: 34px;
    height: 34px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 17px;
    flex-shrink: 0;
}

.deduction-title {
    font-size: 12.5px;
    font-weight: 700;
    color: var(--portal-navy-deep);
    margin: 0;
}

.deduction-sub {
    font-size: 10px;
    color: var(--portal-text-muted);
}

.deduction-amount {
    font-size: 14px;
    font-weight: 800;
    font-family: 'Inter', monospace;
}

.deduction-curr {
    font-size: 10px;
    font-weight: 600;
    color: var(--portal-text-muted);
}

.salary-link-pill {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 11px;
    font-weight: 700;
    color: var(--portal-accent-orange);
    text-decoration: none;
}

/* Bank Details */
.bank-details-card {
    background: #F8FAFC;
    border: 1px solid #E2E8F0;
    border-radius: 14px;
    padding: 14px;
}

.bank-header-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-bottom: 8px;
    border-bottom: 1px solid #E2E8F0;
}

.bank-name-text {
    font-size: 13.5px;
    font-weight: 800;
    color: var(--portal-navy-deep);
}

.bank-verified-badge {
    background: #DCFCE7;
    color: #166534;
    font-size: 10.5px;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    gap: 3px;
}

.bank-field-item {
    display: flex;
    flex-direction: column;
}

.field-label {
    font-size: 10.5px;
    color: var(--portal-text-muted);
}

.field-val {
    font-size: 12.5px;
    font-weight: 700;
    color: var(--portal-navy-deep);
}

.bank-iban-box {
    background: #ffffff;
    border: 1.5px dashed #CBD5E1;
    border-radius: 10px;
    padding: 8px 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.iban-label {
    font-size: 9.5px;
    color: var(--portal-text-muted);
    display: block;
}

.iban-val {
    font-size: 12px;
    font-weight: 800;
    color: var(--portal-navy-deep);
    word-break: break-all;
}

.copy-iban-btn {
    background: #EFF6FF;
    border: 1px solid #BFDBFE;
    color: #0284C7;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    cursor: pointer;
    flex-shrink: 0;
    transition: all 0.15s ease;
}

.copy-iban-btn:active {
    transform: scale(0.95);
    background: #DBEAFE;
}

/* Payslips List */
.payslip-card-item {
    background: #F8FAFC;
    border: 1px solid #F1F5F9;
    border-radius: 14px;
    padding: 12px;
    transition: all 0.2s ease;
}

.payslip-top-row {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
}

.payslip-month-title {
    font-size: 13px;
    font-weight: 800;
    color: var(--portal-navy-deep);
    display: block;
}

.payslip-ref-no {
    font-size: 10px;
    color: var(--portal-text-muted);
    font-family: monospace;
}

.payslip-net-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.net-title {
    font-size: 10px;
    color: var(--portal-text-muted);
}

.net-val {
    font-size: 16px;
    font-weight: 800;
    color: #10B981;
}

.net-val small {
    font-size: 11px;
}

.btn-toggle-slip {
    background: #ffffff;
    border: 1px solid rgba(0, 51, 75, 0.12);
    border-radius: 8px;
    padding: 4px 10px;
    font-size: 11px;
    font-weight: 700;
    color: var(--portal-navy-deep);
    display: flex;
    align-items: center;
    gap: 4px;
    cursor: pointer;
    transition: all 0.15s ease;
}

.btn-toggle-slip:active {
    background: #F1F5F9;
}

.slip-breakdown-grid {
    background: #ffffff;
    border: 1px solid #E2E8F0;
    border-radius: 10px;
    padding: 10px;
    font-size: 11px;
}

.slip-sub-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 2px 0;
}

.slip-sub-label {
    color: var(--portal-text-muted);
}

.slip-sub-val {
    font-weight: 700;
    color: var(--portal-navy-deep);
}

.salary-empty-hint {
    background: #F8FAFC;
    border-radius: 10px;
    padding: 10px 12px;
    font-size: 11px;
    color: var(--portal-text-muted);
    display: flex;
    align-items: center;
    gap: 6px;
}
</style>
@endpush

@push('scripts')
<script>
function copyIbanToClipboard() {
    const ibanText = document.getElementById('ibanCodeText').innerText.trim();
    if (navigator.clipboard) {
        navigator.clipboard.writeText(ibanText).then(() => {
            const icon = document.getElementById('copyIbanIcon');
            icon.className = 'bx bx-check text-success';
            showPortalToast('success', "{{ __('IBAN Copied') }}", "{{ __('IBAN copied to clipboard successfully.') }}");
            setTimeout(() => {
                icon.className = 'bx bx-copy';
            }, 2000);
        });
    }
}

function toggleSlipDetails(id, btn) {
    const el = document.getElementById(id);
    const icon = btn.querySelector('i');
    if (el.style.display === 'none') {
        el.style.display = 'block';
        icon.className = 'bx bx-chevron-up';
    } else {
        el.style.display = 'none';
        icon.className = 'bx bx-chevron-down';
    }
}
</script>
@endpush
