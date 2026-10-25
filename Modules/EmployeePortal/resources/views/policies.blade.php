@extends('employeeportal::layouts.master')

@section('title', __('Nursery Policies & Regulations'))

@section('content')
<div class="policies-page-container">
    <!-- Top Navigation Header -->
    <div class="policies-nav-header">
        <a href="{{ route('portal.profile') }}" class="policies-back-btn" title="{{ __('Back') }}">
            <i class="bx {{ app()->getLocale() === 'ar' ? 'bx-chevron-right' : 'bx-chevron-left' }}"></i>
        </a>
        <h1 class="policies-header-title">{{ __('Nursery Policies & Regulations') }}</h1>
        @include('employeeportal::components.notification-bell')
    </div>


    <!-- 1. Hero Introduction Card -->
    <div class="policies-hero-card">
        <div class="policies-hero-top">
            <div class="d-flex align-items-center gap-2">
                <div class="policies-hero-icon-box">
                    <i class="bx bx-building-house"></i>
                </div>
                <div>
                    <span class="policies-hero-label">{{ __('Approved Policies & Regulations Handbook') }}</span>
                    <span class="policies-hero-subtitle">{{ __('Astro Leaders Nursery • State of Kuwait') }}</span>
                </div>
            </div>

            <span class="policies-verified-badge">
                <i class="bx bx-check-shield"></i>
                <span>{{ __('Approved & Automatically Updated Regulations ✓') }}</span>
            </span>
        </div>

        <p class="policies-hero-desc">
            {{ __('This regulation governs official working hours, leave entitlements, permissions, financial facilities, and child protection code in accordance with nursery bylaws and Kuwait Labor Law.') }}
        </p>

        <!-- Quick Dynamic Metric Tags -->
        <div class="policies-hero-tags">
            <span class="policy-tag">
                <i class="bx bx-time"></i> 
                {{ __('Grace Period: :minutes mins', ['minutes' => $graceMins]) }}
            </span>
            <span class="policy-tag">
                <i class="bx bx-calendar"></i> 
                {{ __('Annual Leave: :days days', ['days' => $annualDays]) }}
            </span>
            <span class="policy-tag">
                <i class="bx bx-coin-stack"></i> 
                {{ __('Loan Cap: :multiplier x Basic Salary', ['multiplier' => $loanMaxMulti]) }}
            </span>
        </div>
    </div>

    <!-- 2. Stacked Policy Sections List (Vertical Layout - No Tabs) -->
    <div class="policies-stacked-list">
        
        <!-- SECTION 1: ATTENDANCE & PUNCH (DYNAMIC) -->
        <div class="policy-section-card expanded" id="policyAttendance">
            <div class="policy-card-header" onclick="togglePolicyCard(this)">
                <div class="d-flex align-items-center gap-2">
                    <div class="policy-cat-icon icon-teal">
                        <i class="bx bx-fingerprint"></i>
                    </div>
                    <div>
                        <h3 class="policy-cat-title">{{ __('1. Official Working Hours & Attendance Punch Regulations') }}</h3>
                        <span class="policy-cat-sub">{{ __('Working hours, morning grace period, and campus geofence') }}</span>
                    </div>
                </div>
                <i class="bx bx-chevron-down policy-arrow"></i>
            </div>

            <div class="policy-card-body">
                <!-- Assigned Shift Dynamic Banner -->
                <div class="dynamic-config-banner mb-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="d-block text-muted font-11">{{ __('Assigned Employee Shift:') }}</span>
                            <strong class="text-navy font-13">{{ $shiftTitle }}</strong>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-label-primary font-11">{{ $shiftStart }} - {{ $shiftEnd }}</span>
                            <small class="d-block text-muted font-10 font-monospace">{{ __(':hours working hours daily', ['hours' => $shiftHours]) }}</small>
                        </div>
                    </div>
                </div>

                <div class="policy-rule-item">
                    <div class="rule-icon"><i class="bx bx-check-circle text-teal"></i></div>
                    <div class="rule-content">
                        <strong class="rule-title">
                            {{ __('Morning Grace Period:') }} 
                            <span class="badge bg-label-teal ms-1 font-11">{{ __(':minutes minutes', ['minutes' => $graceMins]) }}</span>
                        </strong>
                        <p class="rule-text">
                            {{ __('The nursery provides a :minutes-minute morning grace period after official start time. Any punch recorded after the grace period is automatically treated as morning tardiness.', ['minutes' => $graceMins]) }}
                        </p>
                    </div>
                </div>

                <div class="policy-rule-item">
                    <div class="rule-icon"><i class="bx bx-check-circle text-teal"></i></div>
                    <div class="rule-content">
                        <strong class="rule-title">{{ __('Mandatory Dual Punch (Check-In & Check-Out):') }}</strong>
                        <p class="rule-text">
                            {{ __('Staff must register check-in upon arrival at the nursery campus and check-out before departing at the end of the shift. Leaving without a checkout punch or approved permission is considered incomplete attendance.') }}
                        </p>
                    </div>
                </div>

                <div class="policy-rule-item">
                    <div class="rule-icon"><i class="bx bx-check-circle text-teal"></i></div>
                    <div class="rule-content">
                        <strong class="rule-title">{{ __('Approved Geofence for Punch (Campus Location):') }}</strong>
                        <p class="rule-text">
                            {{ __('Punches are only accepted within the authorized geofence of the nursery campus (:branch). Location spoofing is strictly prohibited and punches from outside the zone are invalidated.', ['branch' => $branchName]) }}
                        </p>
                    </div>
                </div>

                <div class="policy-rule-item">
                    <div class="rule-icon"><i class="bx bx-check-circle text-teal"></i></div>
                    <div class="rule-content">
                        <strong class="rule-title">{{ __('Cumulative Absence & Tardiness Rules:') }}</strong>
                        <p class="rule-text">
                            {{ __('The maximum monthly deduction for absence is :max days salary. Unexcused absence for :continuous continuous days or :intermittent intermittent days entails legal procedures under Kuwait Labor Law.', ['max' => $maxAbsenceDays, 'continuous' => $continuousDays, 'intermittent' => $intermittentDays]) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 2: LEAVES & VACATIONS (DYNAMIC) -->
        <div class="policy-section-card expanded" id="policyLeaves">
            <div class="policy-card-header" onclick="togglePolicyCard(this)">
                <div class="d-flex align-items-center gap-2">
                    <div class="policy-cat-icon icon-emerald">
                        <i class="bx bx-calendar-star"></i>
                    </div>
                    <div>
                        <h3 class="policy-cat-title">{{ __('2. Official Leave & Absence Regulations') }}</h3>
                        <span class="policy-cat-sub">{{ __('Annual leave balance, rollover, casual emergency leave, and maternity') }}</span>
                    </div>
                </div>
                <i class="bx bx-chevron-down policy-arrow"></i>
            </div>

            <div class="policy-card-body">
                <div class="policy-rule-item">
                    <div class="rule-icon"><i class="bx bx-check-circle text-emerald"></i></div>
                    <div class="rule-content">
                        <strong class="rule-title">
                            {{ __('Annual Leave Entitlement:') }} 
                            <span class="badge bg-label-success ms-1 font-11">{{ __(':days days / yearly', ['days' => $annualDays]) }}</span>
                        </strong>
                        <p class="rule-text">
                            {{ __('Employees are entitled to :days paid annual leave days per year, with eligibility starting after completing :months continuous months of service.', ['days' => $annualDays, 'months' => $eligMonths]) }}
                        </p>
                    </div>
                </div>

                <div class="policy-rule-item">
                    <div class="rule-icon"><i class="bx bx-check-circle text-emerald"></i></div>
                    <div class="rule-content">
                        <strong class="rule-title">
                            {{ __('Annual Leave Balance Rollover:') }} 
                            <span class="badge bg-label-info ms-1 font-11">{{ $allowRollover ? __('Max :days days', ['days' => $rolloverDays]) : __('Not Allowed') }}</span>
                        </strong>
                        <p class="rule-text">
                            @if($allowRollover)
                                {{ __('Employees may roll over up to :days days of unused annual leave to the following year upon general management approval.', ['days' => $rolloverDays]) }}
                            @else
                                {{ __('Annual leave balance cannot be carried over and must be utilized within the contractual year.') }}
                            @endif
                        </p>
                    </div>
                </div>

                <div class="policy-rule-item">
                    <div class="rule-icon"><i class="bx bx-check-circle text-emerald"></i></div>
                    <div class="rule-content">
                        <strong class="rule-title">
                            {{ __('Casual / Emergency Leave:') }} 
                            <span class="badge bg-label-warning ms-1 font-11">{{ __(':days days yearly', ['days' => $casualDays]) }}</span>
                        </strong>
                        <p class="rule-text">
                            {{ __('Employees receive :days casual leave days annually for sudden emergencies, with a maximum of 1 day per occasion and mandatory immediate notice.', ['days' => $casualDays]) }}
                        </p>
                    </div>
                </div>

                <div class="policy-rule-item">
                    <div class="rule-icon"><i class="bx bx-check-circle text-emerald"></i></div>
                    <div class="rule-content">
                        <strong class="rule-title">
                            {{ __('Maternity & Childcare Leave:') }} 
                            <span class="badge bg-label-danger ms-1 font-11">{{ __(':days days paid', ['days' => $maternityDays]) }}</span>
                        </strong>
                        <p class="rule-text">
                            {{ __('Female staff are entitled to :days fully paid calendar days for maternity leave upon childbirth, in addition to statutory nursing hours under Kuwait Labor Law.', ['days' => $maternityDays]) }}
                        </p>
                    </div>
                </div>

                <div class="policy-rule-item">
                    <div class="rule-icon"><i class="bx bx-check-circle text-emerald"></i></div>
                    <div class="rule-content">
                        <strong class="rule-title">{{ __('Special Occasion Leaves:') }}</strong>
                        <div class="dynamic-chips-grid mt-1">
                            <span class="chip-item">{{ __('💍 Marriage: :days days', ['days' => $marriageDays]) }}</span>
                            <span class="chip-item">{{ __('🕊️ Bereavement: :days days', ['days' => $bereaveDays]) }}</span>
                            <span class="chip-item">{{ __('👶 Paternity: :days days', ['days' => $paternityDays]) }}</span>
                            <span class="chip-item">{{ __('🕋 Hajj: :days days (:years yrs service)', ['days' => $hajjDays, 'years' => $hajjYears]) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Dynamic Sick Leave Tiers Table from DB -->
                @if(isset($sickLeaveTiers) && $sickLeaveTiers->isNotEmpty())
                    <div class="dynamic-tiers-card mt-3">
                        <h4 class="tiers-title">
                            <i class="bx bx-plus-medical text-danger"></i>
                            <span>{{ __('Sick Leave Compensation Scale (Kuwait Labor Law):') }}</span>
                        </h4>
                        <div class="tiers-table">
                            @foreach($sickLeaveTiers as $tier)
                                <div class="tier-row">
                                    <span class="tier-name">{{ $tier->display_name }}</span>
                                    <span class="tier-badge {{ $tier->salary_percentage == 100 ? 'tier-full' : 'tier-partial' }}">
                                        {{ __(':percentage% Salary', ['percentage' => $tier->salary_percentage]) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- SECTION 3: PERMISSIONS (DYNAMIC) -->
        <div class="policy-section-card expanded" id="policyPermissions">
            <div class="policy-card-header" onclick="togglePolicyCard(this)">
                <div class="d-flex align-items-center gap-2">
                    <div class="policy-cat-icon icon-sky">
                        <i class="bx bx-time-five"></i>
                    </div>
                    <div>
                        <h3 class="policy-cat-title">{{ __('3. Permission & Short Leave Regulations') }}</h3>
                        <span class="policy-cat-sub">{{ __('Monthly hours balance, single permission cap, and advance notice') }}</span>
                    </div>
                </div>
                <i class="bx bx-chevron-down policy-arrow"></i>
            </div>

            <div class="policy-card-body">
                <div class="policy-rule-item">
                    <div class="rule-icon"><i class="bx bx-check-circle text-sky"></i></div>
                    <div class="rule-content">
                        <strong class="rule-title">
                            {{ __('Allowed Monthly Balance:') }} 
                            <span class="badge bg-label-info ms-1 font-11">{{ __(':hours hours monthly', ['hours' => $permMonthlyHours]) }}</span>
                        </strong>
                        <p class="rule-text">
                            {{ __('The maximum permission allowance is :hours hours per calendar month, distributed over no more than :count requests to maintain classroom care and continuity.', ['hours' => $permMonthlyHours, 'count' => $permMonthlyCount]) }}
                        </p>
                    </div>
                </div>

                <div class="policy-rule-item">
                    <div class="rule-icon"><i class="bx bx-check-circle text-sky"></i></div>
                    <div class="rule-content">
                        <strong class="rule-title">
                            {{ __('Single Permission Limit:') }} 
                            <span class="badge bg-label-primary ms-1 font-11">{{ __('Max :hours hours', ['hours' => $permSingleHours]) }}</span>
                        </strong>
                        <p class="rule-text">
                            {{ __('A single permission cannot exceed :hours hours during one shift (late arrival or early departure).', ['hours' => $permSingleHours]) }}
                        </p>
                    </div>
                </div>

                <div class="policy-rule-item">
                    <div class="rule-icon"><i class="bx bx-check-circle text-sky"></i></div>
                    <div class="rule-content">
                        <strong class="rule-title">
                            {{ __('Advance Notice Deadline:') }} 
                            <span class="badge bg-label-warning ms-1 font-11">{{ __(':hours hours in advance', ['hours' => $permNoticeHours]) }}</span>
                        </strong>
                        <p class="rule-text">
                            {{ __('Permissions must be submitted through the portal at least :hours hours in advance for classroom coverage arrangements, excluding documented medical emergencies (up to :count emergency permission monthly).', ['hours' => $permNoticeHours, 'count' => $permEmergency]) }}
                        </p>
                    </div>
                </div>

                <div class="policy-rule-item">
                    <div class="rule-icon"><i class="bx bx-check-circle text-sky"></i></div>
                    <div class="rule-content">
                        <strong class="rule-title">{{ __('Official Approval Workflow:') }}</strong>
                        <p class="rule-text">
                            {{ __('Requires sequential approval from the direct educational supervisor and nursery HR management before leaving campus.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 4: LOANS & ADVANCES (DYNAMIC) -->
        <div class="policy-section-card expanded" id="policyLoans">
            <div class="policy-card-header" onclick="togglePolicyCard(this)">
                <div class="d-flex align-items-center gap-2">
                    <div class="policy-cat-icon icon-gold">
                        <i class="bx bx-coin-stack"></i>
                    </div>
                    <div>
                        <h3 class="policy-cat-title">{{ __('4. Salary Advances & Financial Facilities Regulations') }}</h3>
                        <span class="policy-cat-sub">{{ __('Eligibility conditions, monthly deduction ceiling, and installment tenure') }}</span>
                    </div>
                </div>
                <i class="bx bx-chevron-down policy-arrow"></i>
            </div>

            <div class="policy-card-body">
                <div class="policy-rule-item">
                    <div class="rule-icon"><i class="bx bx-check-circle text-warning"></i></div>
                    <div class="rule-content">
                        <strong class="rule-title">
                            {{ __('Service Seniority for Eligibility:') }} 
                            <span class="badge bg-label-warning ms-1 font-11">{{ __(':months months active service', ['months' => $loanMinMonths]) }}</span>
                        </strong>
                        <p class="rule-text">
                            {{ __('Staff confirmed in service after :months continuous months of employment are eligible to apply for advances and financial facilities.', ['months' => $loanMinMonths]) }}
                        </p>
                    </div>
                </div>

                <div class="policy-rule-item">
                    <div class="rule-icon"><i class="bx bx-check-circle text-warning"></i></div>
                    <div class="rule-content">
                        <strong class="rule-title">
                            {{ __('Urgent Salary Advance:') }} 
                            <span class="badge bg-label-success ms-1 font-11">{{ __('Up to :multiplier x Basic Salary', ['multiplier' => $advanceMaxMulti]) }}</span>
                        </strong>
                        <p class="rule-text">
                            {{ __('An urgent advance up to :multiplier times the basic salary, deducted in full in one installment from the immediately following payroll.', ['multiplier' => $advanceMaxMulti]) }}
                        </p>
                    </div>
                </div>

                <div class="policy-rule-item">
                    <div class="rule-icon"><i class="bx bx-check-circle text-warning"></i></div>
                    <div class="rule-content">
                        <strong class="rule-title">
                            {{ __('Monthly Installment Loan:') }} 
                            <span class="badge bg-label-primary ms-1 font-11">{{ __('Up to :multiplier x Basic Salary • Up to :months months', ['multiplier' => $loanMaxMulti, 'months' => $loanMaxMonths]) }}</span>
                        </strong>
                        <p class="rule-text">
                            {{ __('Financial facilities up to :multiplier times the basic salary, repayable over flexible monthly installments up to :months months.', ['multiplier' => $loanMaxMulti, 'months' => $loanMaxMonths]) }}
                        </p>
                    </div>
                </div>

                <div class="policy-rule-item">
                    <div class="rule-icon"><i class="bx bx-check-circle text-warning"></i></div>
                    <div class="rule-content">
                        <strong class="rule-title">
                            {{ __('Monthly Deduction Safety Cap:') }} 
                            <span class="badge bg-label-danger ms-1 font-11">{{ __(':percentage% of Net Salary', ['percentage' => $loanMaxDeduct]) }}</span>
                        </strong>
                        <p class="rule-text">
                            {{ __('The monthly deduction must not exceed :percentage% of net salary to safeguard the financial stability of staff.', ['percentage' => $loanMaxDeduct]) }}
                        </p>
                    </div>
                </div>

                <div class="policy-rule-item">
                    <div class="rule-icon"><i class="bx bx-check-circle text-warning"></i></div>
                    <div class="rule-content">
                        <strong class="rule-title">{{ __('Multiple Loans Policy:') }}</strong>
                        <p class="rule-text">
                            @if($allowMultiLoans)
                                {{ __('Multiple concurrent loans are permitted as long as total installments remain within the :percentage% net salary ceiling.', ['percentage' => $loanMaxDeduct]) }}
                            @else
                                {{ __('A new loan cannot be requested until the outstanding balance of the active loan is completely settled.') }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 5: NOTICE & END OF SERVICE (DYNAMIC EOS) -->
        <div class="policy-section-card expanded" id="policyEos">
            <div class="policy-card-header" onclick="togglePolicyCard(this)">
                <div class="d-flex align-items-center gap-2">
                    <div class="policy-cat-icon icon-indigo">
                        <i class="bx bx-award"></i>
                    </div>
                    <div>
                        <h3 class="policy-cat-title">{{ __('5. Notice Period & End of Service Gratuity') }}</h3>
                        <span class="policy-cat-sub">{{ __('Statutory notice duration and end of service entitlement scale') }}</span>
                    </div>
                </div>
                <i class="bx bx-chevron-down policy-arrow"></i>
            </div>

            <div class="policy-card-body">
                <div class="policy-rule-item">
                    <div class="rule-icon"><i class="bx bx-check-circle text-primary"></i></div>
                    <div class="rule-content">
                        <strong class="rule-title">
                            {{ __('Official Notice Period:') }} 
                            <span class="badge bg-label-primary ms-1 font-11">{{ __(':months months', ['months' => $noticeMonthly]) }}</span>
                        </strong>
                        <p class="rule-text">
                            {{ __('For monthly paid employees, statutory notice period is :months months prior to termination or resignation.', ['months' => $noticeMonthly]) }}
                        </p>
                    </div>
                </div>

                <div class="policy-rule-item">
                    <div class="rule-icon"><i class="bx bx-check-circle text-primary"></i></div>
                    <div class="rule-content">
                        <strong class="rule-title">{{ __('Approved Gratuity Accrual Rates:') }}</strong>
                        <p class="rule-text">
                            {{ __('Employees accrue :first5 days pay for each of the first 5 years of service, and :after5 days pay for each subsequent year under Kuwait Labor Law.', ['first5' => $eosFirst5, 'after5' => $eosAfter5]) }}
                        </p>
                    </div>
                </div>

                <!-- Dynamic EOS Rules Scale from DB -->
                @if(isset($eosRules) && $eosRules->isNotEmpty())
                    <div class="dynamic-tiers-card mt-3">
                        <h4 class="tiers-title">
                            <i class="bx bx-scale text-primary"></i>
                            <span>{{ __('End of Service Entitlement Scale upon Resignation (by Service Years):') }}</span>
                        </h4>
                        <div class="tiers-table">
                            @foreach($eosRules as $rule)
                                <div class="tier-row">
                                    <span class="tier-name">{{ $rule->display_name }}</span>
                                    <span class="tier-badge {{ $rule->percentage >= 100 ? 'tier-full' : ($rule->percentage > 0 ? 'tier-partial' : 'tier-zero') }}">
                                        {{ __(':percentage% Entitlement', ['percentage' => $rule->percentage]) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- SECTION 6: CHILD PROTECTION & CONDUCT CODE -->
        <div class="policy-section-card expanded" id="policyConduct">
            <div class="policy-card-header" onclick="togglePolicyCard(this)">
                <div class="d-flex align-items-center gap-2">
                    <div class="policy-cat-icon icon-rose">
                        <i class="bx bx-shield-quarter"></i>
                    </div>
                    <div>
                        <h3 class="policy-cat-title">{{ __('6. Child Protection & Professional Conduct Code') }}</h3>
                        <span class="policy-cat-sub">{{ __('Child safety priority, privacy, positive discipline, and professional attire') }}</span>
                    </div>
                </div>
                <i class="bx bx-chevron-down policy-arrow"></i>
            </div>

            <div class="policy-card-body">
                <div class="policy-rule-item">
                    <div class="rule-icon"><i class="bx bx-check-circle text-danger"></i></div>
                    <div class="rule-content">
                        <strong class="rule-title">{{ __('Strict Prohibition of Corporal or Verbal Abuse:') }}</strong>
                        <p class="rule-text">
                            {{ __('Absolute zero tolerance for physical punishment, verbal aggression, shouting, or neglect. Interactions are solely based on positive reinforcement, emotional nurturing, and developmental guidance.') }}
                        </p>
                    </div>
                </div>

                <div class="policy-rule-item">
                    <div class="rule-icon"><i class="bx bx-check-circle text-danger"></i></div>
                    <div class="rule-content">
                        <strong class="rule-title">{{ __('Child Privacy, Data Confidentiality & Photography Ban:') }}</strong>
                        <p class="rule-text">
                            {{ __('Strictly prohibited to photograph children or record videos on personal phones, or post nursery-related media on personal social accounts, to protect family and child privacy.') }}
                        </p>
                    </div>
                </div>

                <div class="policy-rule-item">
                    <div class="rule-icon"><i class="bx bx-check-circle text-danger"></i></div>
                    <div class="rule-content">
                        <strong class="rule-title">{{ __('Continuous Supervision & Custody:') }}</strong>
                        <p class="rule-text">
                            {{ __('Children must never be left unattended in classrooms, restrooms, or playground areas, with strict adherence to authorized parent pickup protocols.') }}
                        </p>
                    </div>
                </div>

                <div class="policy-rule-item">
                    <div class="rule-icon"><i class="bx bx-check-circle text-danger"></i></div>
                    <div class="rule-content">
                        <strong class="rule-title">{{ __('Professional Appearance & Identification Badge:') }}</strong>
                        <p class="rule-text">
                            {{ __('Commitment to modest, comfortable attire suited for childcare environments, with mandatory wearing of nursery ID badge during work hours.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- 3. HR Support & Inquiry Action Card -->
    <div class="policies-contact-card mt-3">
        <div class="d-flex align-items-center gap-3">
            <div class="contact-icon-box">
                <i class="bx bx-headphone"></i>
            </div>
            <div>
                <h4 class="contact-card-title">{{ __('Have Questions About Policies & Regulations?') }}</h4>
                <p class="contact-card-desc mb-0">
                    {{ __('HR department is always available to support you and clarify any professional inquiries.') }}
                </p>
            </div>
        </div>

        <a href="{{ route('portal.requests') }}" class="contact-action-btn mt-3">
            <i class="bx bx-send"></i>
            <span>{{ __('Submit an Inquiry or Request via Portal') }}</span>
        </a>
    </div>

</div>
@endsection


@push('scripts')
<script>
function togglePolicyCard(headerEl) {
    const card = headerEl.closest('.policy-section-card');
    if (card) {
        card.classList.toggle('expanded');
    }
}
</script>
@endpush
