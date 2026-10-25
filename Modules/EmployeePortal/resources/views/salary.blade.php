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
