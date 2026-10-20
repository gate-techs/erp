@extends('employeeportal::layouts.master')

@section('title', __('Notification & Alert Settings'))

@section('content')
<div class="notif-page-container">
    <!-- Top Navigation Header -->
    <div class="notif-nav-header">
        <a href="{{ route('portal.profile') }}" class="notif-back-btn" title="{{ __('Back') }}">
            <i class="bx {{ app()->getLocale() === 'ar' ? 'bx-chevron-right' : 'bx-chevron-left' }}"></i>
        </a>
        <h1 class="notif-header-title">{{ __('Notification & Alert Settings') }}</h1>
        @include('employeeportal::components.notification-bell')
    </div>

    <!-- 1. Master Web Push Notification Hero Card -->
    <div class="notif-hero-card">
        <div class="notif-hero-top">
            <div class="d-flex align-items-center gap-2">
                <div class="notif-hero-icon-box">
                    <i class="bx bx-broadcast"></i>
                </div>
                <div>
                    <span class="notif-hero-label">{{ __('Master Push Notifications') }}</span>
                    <span class="notif-hero-subtitle" id="pushStatusText">
                        {{ $hasActivePushSubscription ? __('Active on this device') : __('Permission Required') }}
                    </span>
                </div>
            </div>

            <div class="form-check form-switch p-0 m-0">
                <input class="form-check-input notif-switch notif-master-switch" 
                       type="checkbox" 
                       id="toggle_push_enabled" 
                       data-key="push_enabled"
                       {{ $preferences->push_enabled ? 'checked' : '' }}
                       onchange="handleTogglePreference(this)">
            </div>
        </div>

        <p class="notif-hero-desc mb-2">
            {{ __('Receive instant alerts on your phone even when the app is closed') }}
        </p>

        <div class="d-flex align-items-center justify-content-between pt-1">
            <span class="notif-status-badge {{ $hasActivePushSubscription ? 'status-active' : 'status-pending' }}" id="pushDeviceBadge">
                <i class="bx {{ $hasActivePushSubscription ? 'bx-check-circle' : 'bx-info-circle' }}"></i>
                <span>{{ $hasActivePushSubscription ? __('Active on this device') : __('Permission Required') }}</span>
            </span>

            <button type="button" class="notif-test-btn" onclick="sendTestNotification()">
                <i class="bx bx-paper-plane"></i>
                <span>{{ __('Send Test Notification') }}</span>
            </button>
        </div>
    </div>

    <!-- 2. Settings Stacked Group Cards -->
    <div class="notif-cards-list">

        <!-- GROUP 1: ATTENDANCE & SHIFT REMINDERS -->
        <div class="notif-section-card">
            <div class="notif-card-header">
                <div class="d-flex align-items-center gap-2">
                    <div class="notif-cat-icon icon-teal">
                        <i class="bx bx-time-five"></i>
                    </div>
                    <div>
                        <h3 class="notif-cat-title">{{ __('Attendance & Shift Alerts') }}</h3>
                        <span class="notif-cat-sub">{{ __('Daily reminders for check-in, check-out and attendance confirmation') }}</span>
                    </div>
                </div>
            </div>

            <div class="notif-card-body">
                <!-- Morning Reminder -->
                <div class="notif-item-row">
                    <div class="notif-item-info">
                        <strong class="notif-item-title">{{ __('Morning Check-In Reminder') }}</strong>
                        <span class="notif-item-desc">{{ __('Remind me before shift start (:time)', ['time' => $shiftStart]) }}</span>

                        <!-- Reminder Minutes Selector -->
                        <div class="notif-mins-chips mt-2" id="minsSelectorBox" style="{{ $preferences->morning_reminder_enabled ? '' : 'opacity: 0.5; pointer-events: none;' }}">
                            @foreach([5, 10, 15, 30] as $mins)
                                <button type="button" 
                                        class="min-chip {{ $preferences->morning_reminder_mins === $mins ? 'active' : '' }}" 
                                        data-mins="{{ $mins }}"
                                        onclick="selectReminderMins({{ $mins }}, this)">
                                    {{ __(':mins mins before', ['mins' => $mins]) }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                    <div class="form-check form-switch p-0 m-0 ms-2">
                        <input class="form-check-input notif-switch" 
                               type="checkbox" 
                               id="toggle_morning_reminder_enabled" 
                               data-key="morning_reminder_enabled"
                               {{ $preferences->morning_reminder_enabled ? 'checked' : '' }}
                               onchange="handleTogglePreference(this, 'minsSelectorBox')">
                    </div>
                </div>

                <!-- Check-Out Reminder -->
                <div class="notif-item-row">
                    <div class="notif-item-info">
                        <strong class="notif-item-title">{{ __('Evening Check-Out Reminder') }}</strong>
                        <span class="notif-item-desc">{{ __('Prompt to punch out at the end of official work hours') }}</span>
                    </div>
                    <div class="form-check form-switch p-0 m-0 ms-2">
                        <input class="form-check-input notif-switch" 
                               type="checkbox" 
                               id="toggle_checkout_reminder_enabled" 
                               data-key="checkout_reminder_enabled"
                               {{ $preferences->checkout_reminder_enabled ? 'checked' : '' }}
                               onchange="handleTogglePreference(this)">
                    </div>
                </div>

                <!-- Instant Punch Confirmation -->
                <div class="notif-item-row">
                    <div class="notif-item-info">
                        <strong class="notif-item-title">{{ __('Instant Punch Confirmation') }}</strong>
                        <span class="notif-item-desc">{{ __('Show a confirmation notification immediately after recording punch') }}</span>
                    </div>
                    <div class="form-check form-switch p-0 m-0 ms-2">
                        <input class="form-check-input notif-switch" 
                               type="checkbox" 
                               id="toggle_punch_success_alert" 
                               data-key="punch_success_alert"
                               {{ $preferences->punch_success_alert ? 'checked' : '' }}
                               onchange="handleTogglePreference(this)">
                    </div>
                </div>
            </div>
        </div>

        <!-- GROUP 2: FINANCIAL & PAYROLL ALERTS -->
        <div class="notif-section-card">
            <div class="notif-card-header">
                <div class="d-flex align-items-center gap-2">
                    <div class="notif-cat-icon icon-emerald">
                        <i class="bx bx-coin-stack"></i>
                    </div>
                    <div>
                        <h3 class="notif-cat-title">{{ __('Payroll & Financial Alerts') }}</h3>
                        <span class="notif-cat-sub">{{ __('Salary slips, loan updates, and incentive announcements') }}</span>
                    </div>
                </div>
            </div>

            <div class="notif-card-body">
                <!-- Payroll Alert -->
                <div class="notif-item-row">
                    <div class="notif-item-info">
                        <strong class="notif-item-title">{{ __('Monthly Payslip Disbursement') }}</strong>
                        <span class="notif-item-desc">{{ __('Notify when the monthly payslip is finalized and deposited') }}</span>
                    </div>
                    <div class="form-check form-switch p-0 m-0 ms-2">
                        <input class="form-check-input notif-switch" 
                               type="checkbox" 
                               id="toggle_payroll_alert_enabled" 
                               data-key="payroll_alert_enabled"
                               {{ $preferences->payroll_alert_enabled ? 'checked' : '' }}
                               onchange="handleTogglePreference(this)">
                    </div>
                </div>

                <!-- Loans Alert -->
                <div class="notif-item-row">
                    <div class="notif-item-info">
                        <strong class="notif-item-title">{{ __('Salary Advance & Loans Status') }}</strong>
                        <span class="notif-item-desc">{{ __('Updates on loan approvals, disbursements and monthly deductions') }}</span>
                    </div>
                    <div class="form-check form-switch p-0 m-0 ms-2">
                        <input class="form-check-input notif-switch" 
                               type="checkbox" 
                               id="toggle_loans_alert_enabled" 
                               data-key="loans_alert_enabled"
                               {{ $preferences->loans_alert_enabled ? 'checked' : '' }}
                               onchange="handleTogglePreference(this)">
                    </div>
                </div>

                <!-- Rewards Alert -->
                <div class="notif-item-row">
                    <div class="notif-item-info">
                        <strong class="notif-item-title">{{ __('Rewards & Appreciation Bonuses') }}</strong>
                        <span class="notif-item-desc">{{ __('Get notified immediately when granted a reward or appreciation certificate') }}</span>
                    </div>
                    <div class="form-check form-switch p-0 m-0 ms-2">
                        <input class="form-check-input notif-switch" 
                               type="checkbox" 
                               id="toggle_rewards_alert_enabled" 
                               data-key="rewards_alert_enabled"
                               {{ $preferences->rewards_alert_enabled ? 'checked' : '' }}
                               onchange="handleTogglePreference(this)">
                    </div>
                </div>
            </div>
        </div>

        <!-- GROUP 3: REQUESTS & WORKFLOW NOTICES -->
        <div class="notif-section-card">
            <div class="notif-card-header">
                <div class="d-flex align-items-center gap-2">
                    <div class="notif-cat-icon icon-sky">
                        <i class="bx bx-git-pull-request"></i>
                    </div>
                    <div>
                        <h3 class="notif-cat-title">{{ __('Requests & Workflow Updates') }}</h3>
                        <span class="notif-cat-sub">{{ __('Decision notices on leave requests and permissions') }}</span>
                    </div>
                </div>
            </div>

            <div class="notif-card-body">
                <!-- Leave Requests -->
                <div class="notif-item-row">
                    <div class="notif-item-info">
                        <strong class="notif-item-title">{{ __('Leave Requests Status') }}</strong>
                        <span class="notif-item-desc">{{ __('Instant notice when leave request is approved, rejected or returned') }}</span>
                    </div>
                    <div class="form-check form-switch p-0 m-0 ms-2">
                        <input class="form-check-input notif-switch" 
                               type="checkbox" 
                               id="toggle_leave_requests_alert" 
                               data-key="leave_requests_alert"
                               {{ $preferences->leave_requests_alert ? 'checked' : '' }}
                               onchange="handleTogglePreference(this)">
                    </div>
                </div>

                <!-- Permission Requests -->
                <div class="notif-item-row">
                    <div class="notif-item-info">
                        <strong class="notif-item-title">{{ __('Permission Requests Status') }}</strong>
                        <span class="notif-item-desc">{{ __('Timely decision alerts on short leave and departure requests') }}</span>
                    </div>
                    <div class="form-check form-switch p-0 m-0 ms-2">
                        <input class="form-check-input notif-switch" 
                               type="checkbox" 
                               id="toggle_permission_requests_alert" 
                               data-key="permission_requests_alert"
                               {{ $preferences->permission_requests_alert ? 'checked' : '' }}
                               onchange="handleTogglePreference(this)">
                    </div>
                </div>
            </div>
        </div>

        <!-- GROUP 4: NURSERY ANNOUNCEMENTS & MEMOS -->
        <div class="notif-section-card">
            <div class="notif-card-header">
                <div class="d-flex align-items-center gap-2">
                    <div class="notif-cat-icon icon-gold">
                        <i class="bx bx-megaphone"></i>
                    </div>
                    <div>
                        <h3 class="notif-cat-title">{{ __('Nursery Announcements & Memos') }}</h3>
                        <span class="notif-cat-sub">{{ __('Administrative notices, emergency closures and bylaws updates') }}</span>
                    </div>
                </div>
            </div>

            <div class="notif-card-body">
                <!-- Admin Memos -->
                <div class="notif-item-row">
                    <div class="notif-item-info">
                        <strong class="notif-item-title">{{ __('Administrative & Urgent Memos') }}</strong>
                        <span class="notif-item-desc">{{ __('Alerts regarding emergency closures, student events, and staff meetings') }}</span>
                    </div>
                    <div class="form-check form-switch p-0 m-0 ms-2">
                        <input class="form-check-input notif-switch" 
                               type="checkbox" 
                               id="toggle_admin_memos_alert" 
                               data-key="admin_memos_alert"
                               {{ $preferences->admin_memos_alert ? 'checked' : '' }}
                               onchange="handleTogglePreference(this)">
                    </div>
                </div>

                <!-- Policy Updates -->
                <div class="notif-item-row">
                    <div class="notif-item-info">
                        <strong class="notif-item-title">{{ __('Policy & Regulation Updates') }}</strong>
                        <span class="notif-item-desc">{{ __('Notifications when nursery internal regulations or bylaws are updated') }}</span>
                    </div>
                    <div class="form-check form-switch p-0 m-0 ms-2">
                        <input class="form-check-input notif-switch" 
                               type="checkbox" 
                               id="toggle_policy_updates_alert" 
                               data-key="policy_updates_alert"
                               {{ $preferences->policy_updates_alert ? 'checked' : '' }}
                               onchange="handleTogglePreference(this)">
                    </div>
                </div>
            </div>
        </div>

        <!-- GROUP 5: QUIET HOURS & CHANNELS -->
        <div class="notif-section-card">
            <div class="notif-card-header">
                <div class="d-flex align-items-center gap-2">
                    <div class="notif-cat-icon icon-indigo">
                        <i class="bx bx-moon"></i>
                    </div>
                    <div>
                        <h3 class="notif-cat-title">{{ __('Quiet Hours & Channels') }}</h3>
                        <span class="notif-cat-sub">{{ __('Mute non-urgent alerts during rest time and weekends') }}</span>
                    </div>
                </div>
            </div>

            <div class="notif-card-body">
                <!-- Do Not Disturb Mode -->
                <div class="notif-item-row">
                    <div class="notif-item-info">
                        <strong class="notif-item-title">{{ __('Do Not Disturb Mode') }}</strong>
                        <span class="notif-item-desc">{{ __('Silence non-critical alerts between selected hours') }}</span>

                        <!-- Time Interval Pickers -->
                        <div class="notif-time-range mt-2" id="quietTimeBox" style="{{ $preferences->quiet_hours_enabled ? '' : 'opacity: 0.5; pointer-events: none;' }}">
                            <div class="d-flex align-items-center gap-2">
                                <div class="time-field-wrap">
                                    <label class="time-field-label">{{ __('From') }}</label>
                                    <input type="time" 
                                           id="quiet_start_input" 
                                           class="time-input-styled" 
                                           value="{{ substr($preferences->quiet_hours_start, 0, 5) }}"
                                           onchange="saveQuietHours()">
                                </div>
                                <span class="time-sep">➔</span>
                                <div class="time-field-wrap">
                                    <label class="time-field-label">{{ __('To') }}</label>
                                    <input type="time" 
                                           id="quiet_end_input" 
                                           class="time-input-styled" 
                                           value="{{ substr($preferences->quiet_hours_end, 0, 5) }}"
                                           onchange="saveQuietHours()">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-check form-switch p-0 m-0 ms-2">
                        <input class="form-check-input notif-switch" 
                               type="checkbox" 
                               id="toggle_quiet_hours_enabled" 
                               data-key="quiet_hours_enabled"
                               {{ $preferences->quiet_hours_enabled ? 'checked' : '' }}
                               onchange="handleTogglePreference(this, 'quietTimeBox')">
                    </div>
                </div>

                <!-- Email Channel -->
                <div class="notif-item-row">
                    <div class="notif-item-info">
                        <strong class="notif-item-title">{{ __('Email Digest') }}</strong>
                        <span class="notif-item-desc">{{ __('Send summaries of requests and payroll to your email') }}</span>
                    </div>
                    <div class="form-check form-switch p-0 m-0 ms-2">
                        <input class="form-check-input notif-switch" 
                               type="checkbox" 
                               id="toggle_email_enabled" 
                               data-key="email_enabled"
                               {{ $preferences->email_enabled ? 'checked' : '' }}
                               onchange="handleTogglePreference(this)">
                    </div>
                </div>

                <!-- WhatsApp Channel -->
                <div class="notif-item-row">
                    <div class="notif-item-info">
                        <strong class="notif-item-title">{{ __('WhatsApp Notifications') }}</strong>
                        <span class="notif-item-desc">{{ __('Receive urgent announcements directly on WhatsApp') }}</span>
                    </div>
                    <div class="form-check form-switch p-0 m-0 ms-2">
                        <input class="form-check-input notif-switch" 
                               type="checkbox" 
                               id="toggle_whatsapp_enabled" 
                               data-key="whatsapp_enabled"
                               {{ $preferences->whatsapp_enabled ? 'checked' : '' }}
                               onchange="handleTogglePreference(this)">
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('styles')
<style>
/* ==============================================================================
 * Mobile Notification & Alert Settings Styling (Clean PWA Stacked Cards)
 * ============================================================================== */
.notif-page-container {
    padding-bottom: 95px;
    max-width: 440px;
    margin: 0 auto;
}

.notif-nav-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 14px;
    padding: 0 4px;
}

.notif-back-btn {
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

.notif-back-btn:active {
    transform: scale(0.95);
    background: #f8fafc;
}

.notif-header-title {
    font-family: 'Tajawal', sans-serif;
    font-size: 16.5px;
    font-weight: 800;
    color: var(--portal-navy-deep);
    margin: 0;
}

.notif-nav-badge {
    width: 38px;
    height: 38px;
    border-radius: 12px;
    background: rgba(2, 132, 199, 0.1);
    color: #0284c7;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

/* 1. Hero Master Push Card */
.notif-hero-card {
    background: linear-gradient(135deg, #002A44 0%, #004B6B 100%);
    color: #ffffff;
    border-radius: 18px;
    padding: 16px;
    margin-bottom: 14px;
    box-shadow: 0 8px 24px rgba(0, 42, 68, 0.18);
    position: relative;
    overflow: hidden;
}

.notif-hero-card::after {
    content: '';
    position: absolute;
    top: -25px;
    inset-inline-end: -25px;
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(2, 132, 199, 0.3) 0%, transparent 70%);
    pointer-events: none;
}

.notif-hero-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
}

.notif-hero-icon-box {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(8px);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    color: #ffffff;
}

.notif-hero-label {
    display: block;
    font-size: 14px;
    font-weight: 800;
    color: #ffffff;
    line-height: 1.25;
}

.notif-hero-subtitle {
    display: block;
    font-size: 11px;
    color: #cbd5e1;
}

.notif-hero-desc {
    font-size: 11.5px;
    color: #e2e8f0;
    line-height: 1.5;
}

.notif-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 700;
}

.status-active  { background: rgba(16, 185, 129, 0.2); border: 1px solid rgba(16, 185, 129, 0.4); color: #a7f3d0; }
.status-pending { background: rgba(245, 158, 11, 0.2); border: 1px solid rgba(245, 158, 11, 0.4); color: #fde68a; }

.notif-test-btn {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: rgba(255, 255, 255, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.25);
    color: #ffffff;
    padding: 4px 10px;
    border-radius: 10px;
    font-size: 11px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s ease;
}

.notif-test-btn:active {
    transform: scale(0.96);
    background: rgba(255, 255, 255, 0.25);
}

/* 2. Group Cards */
.notif-cards-list {
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.notif-section-card {
    background: #ffffff;
    border-radius: 18px;
    border: 1px solid rgba(0, 51, 75, 0.08);
    box-shadow: 0 4px 16px rgba(0, 51, 75, 0.04);
    overflow: hidden;
}

.notif-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 16px;
    background: #ffffff;
    border-bottom: 1px solid rgba(0, 51, 75, 0.06);
}

.notif-cat-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}

.icon-teal    { background: rgba(13, 148, 136, 0.12); color: #0d9488; }
.icon-emerald { background: rgba(16, 185, 129, 0.12); color: #10b981; }
.icon-sky     { background: rgba(2, 132, 199, 0.12); color: #0284c7; }
.icon-gold    { background: rgba(217, 119, 6, 0.12); color: #d97706; }
.icon-indigo  { background: rgba(79, 70, 229, 0.12); color: #4f46e5; }

.notif-cat-title {
    font-size: 13.5px;
    font-weight: 800;
    color: var(--portal-navy-deep);
    margin: 0 0 2px 0;
    line-height: 1.3;
}

.notif-cat-sub {
    font-size: 10.5px;
    color: #64748b;
    display: block;
    line-height: 1.25;
}

.notif-card-body {
    padding: 8px 16px;
    display: flex;
    flex-direction: column;
}

.notif-item-row {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    padding: 12px 0;
}

.notif-item-row:not(:last-child) {
    border-bottom: 1px solid rgba(0, 51, 75, 0.05);
}

.notif-item-info {
    flex: 1;
}

.notif-item-title {
    display: block;
    font-size: 13px;
    font-weight: 700;
    color: #1e293b;
    line-height: 1.3;
}

.notif-item-desc {
    display: block;
    font-size: 11px;
    color: #64748b;
    margin-top: 2px;
    line-height: 1.4;
}

/* Switches */
.notif-switch {
    width: 44px !important;
    height: 24px !important;
    cursor: pointer;
    background-color: #cbd5e1;
    border-color: #cbd5e1;
    transition: all 0.25s ease;
}

.notif-switch:checked {
    background-color: #0d9488 !important;
    border-color: #0d9488 !important;
}

.notif-master-switch:checked {
    background-color: #0284c7 !important;
    border-color: #0284c7 !important;
}

/* Minutes Chips */
.notif-mins-chips {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
    transition: opacity 0.2s ease;
}

.min-chip {
    background: #f1f5f9;
    color: #475569;
    border: 1px solid rgba(0, 51, 75, 0.08);
    border-radius: 8px;
    padding: 3px 8px;
    font-size: 10.5px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s ease;
}

.min-chip.active {
    background: var(--portal-navy-deep);
    color: #ffffff;
    border-color: var(--portal-navy-deep);
}

/* Quiet Hours Range */
.notif-time-range {
    transition: opacity 0.2s ease;
}

.time-field-wrap {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.time-field-label {
    font-size: 10px;
    color: #94a3b8;
    font-weight: 700;
}

.time-input-styled {
    background: #f8fafc;
    border: 1px solid rgba(0, 51, 75, 0.12);
    border-radius: 8px;
    padding: 4px 8px;
    font-size: 11.5px;
    font-weight: 700;
    color: var(--portal-navy-deep);
    font-family: monospace;
}

.time-sep {
    color: #94a3b8;
    font-size: 12px;
    margin-top: 14px;
}
</style>
@endpush

@push('scripts')
<script>
const CSRF_TOKEN = '{{ csrf_token() }}';
const TOGGLE_URL = '{{ route("portal.profile.notifications.toggle") }}';
const UPDATE_URL = '{{ route("portal.profile.notifications.update") }}';
const VAPID_KEY  = '{{ $vapidPublicKey }}';

/**
 * Handle fast async preference toggle
 */
function handleTogglePreference(switchEl, dependentBoxId = null) {
    const key = switchEl.getAttribute('data-key');
    const isChecked = switchEl.checked;

    if (dependentBoxId) {
        const depEl = document.getElementById(dependentBoxId);
        if (depEl) {
            depEl.style.opacity = isChecked ? '1' : '0.5';
            depEl.style.pointerEvents = isChecked ? 'auto' : 'none';
        }
    }

    if (key === 'push_enabled') {
        if (isChecked) {
            requestPushPermission();
        }
    }

    if (navigator.vibrate) {
        navigator.vibrate([25]);
    }

    fetch(TOGGLE_URL, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN,
        },
        body: JSON.stringify({
            key: key,
            value: isChecked,
        }),
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            if (typeof showPortalToast === 'function') {
                showPortalToast('success', '{{ __("Saved ✓") }}', data.message || '{{ __("Setting updated successfully.") }}');
            }
        }
    })
    .catch(err => {
        console.error('Error toggling preference:', err);
    });
}

/**
 * Select reminder minutes chip
 */
function selectReminderMins(mins, chipBtn) {
    document.querySelectorAll('.min-chip').forEach(c => c.classList.remove('active'));
    chipBtn.classList.add('active');

    fetch(UPDATE_URL, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN,
        },
        body: JSON.stringify({
            morning_reminder_mins: mins,
        }),
    })
    .then(res => res.json())
    .then(data => {
        if (data.success && typeof showPortalToast === 'function') {
            showPortalToast('success', '{{ __("Saved ✓") }}', data.message || '{{ __("Notification settings saved successfully.") }}');
        }
    });
}

/**
 * Auto-save quiet hours time range
 */
function saveQuietHours() {
    const startVal = document.getElementById('quiet_start_input')?.value;
    const endVal   = document.getElementById('quiet_end_input')?.value;

    if (!startVal || !endVal) return;

    fetch(UPDATE_URL, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN,
        },
        body: JSON.stringify({
            quiet_hours_start: startVal + ':00',
            quiet_hours_end: endVal + ':00',
        }),
    })
    .then(res => res.json())
    .then(data => {
        if (data.success && typeof showPortalToast === 'function') {
            showPortalToast('success', '{{ __("Saved ✓") }}', data.message || '{{ __("Notification settings saved successfully.") }}');
        }
    });
}

/**
 * Request Web Push Permission & Subscribe
 */
function requestPushPermission() {
    if (!('Notification' in window)) {
        return;
    }

    Notification.requestPermission().then(permission => {
        const statusText = document.getElementById('pushStatusText');
        const statusBadge = document.getElementById('pushDeviceBadge');

        if (permission === 'granted') {
            if (statusText) statusText.innerText = '{{ __("Active on this device") }}';
            if (statusBadge) {
                statusBadge.className = 'notif-status-badge status-active';
                statusBadge.innerHTML = '<i class="bx bx-check-circle"></i> <span>{{ __("Active on this device") }}</span>';
            }
        } else {
            if (statusText) statusText.innerText = '{{ __("Permission Required") }}';
            if (statusBadge) {
                statusBadge.className = 'notif-status-badge status-pending';
                statusBadge.innerHTML = '<i class="bx bx-info-circle"></i> <span>{{ __("Permission Required") }}</span>';
            }
        }
    });
}

/**
 * Send local test notification to device
 */
function sendTestNotification() {
    if (!('Notification' in window)) {
        if (typeof showPortalToast === 'function') {
            showPortalToast('info', '{{ __("Notification & Alert Settings") }}', '{{ __("Test Notification Sent") }}');
        }
        return;
    }

    if (Notification.permission === 'granted') {
        try {
            new Notification('{{ __("ALA ORBIT") }}', {
                body: '{{ __("Test Notification Sent") }} 🔔',
                icon: '{{ asset("assets/portal/icon-192.png") }}',
            });
        } catch (e) {
            if (navigator.serviceWorker && navigator.serviceWorker.ready) {
                navigator.serviceWorker.ready.then(reg => {
                    reg.showNotification('{{ __("ALA ORBIT") }}', {
                        body: '{{ __("Test Notification Sent") }} 🔔',
                        icon: '{{ asset("assets/portal/icon-192.png") }}',
                    });
                });
            }
        }

        if (typeof showPortalToast === 'function') {
            showPortalToast('success', '{{ __("Saved ✓") }}', '{{ __("Test Notification Sent") }}');
        }
    } else {
        requestPushPermission();
    }
}
</script>
@endpush
