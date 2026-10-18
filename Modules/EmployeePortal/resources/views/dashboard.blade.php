@extends('employeeportal::layouts.master')

@section('title', __('Attendance Punch'))

@push('styles')
<style>
    /* Main Punch Center Card */
    .punch-hero-card {
        background: #ffffff;
        border-radius: 24px;
        padding: 22px 16px 26px 16px;
        box-shadow: 0 4px 25px rgba(0, 0, 0, 0.04);
        border: 1px solid rgba(0, 0, 0, 0.04);
        text-align: center;
        margin-bottom: 12px;
    }

    .punch-time-badge {
        background: #EEF5F8;
        color: #475569;
        font-size: 12px;
        font-weight: 700;
        padding: 5px 16px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 8px;
    }

    .digital-live-clock {
        font-size: 34px;
        font-weight: 900;
        color: #00334B;
        letter-spacing: 1px;
        margin-bottom: 2px;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    .clock-timezone-label {
        font-size: 12px;
        color: #64748B;
        font-weight: 600;
        margin-bottom: 20px;
    }

    /* Concentric Fingerprint Button matching design */
    .fingerprint-outer-halo {
        width: 186px;
        height: 186px;
        border-radius: 50%;
        background: radial-gradient(circle, #FDEED8 0%, #FFF9F0 65%, #FBF1E2 100%);
        box-shadow: 0 0 0 10px rgba(253, 238, 216, 0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 6px auto 20px auto;
        position: relative;
        transition: transform 0.2s ease;
    }

    .fingerprint-inner-btn {
        width: 146px;
        height: 146px;
        border-radius: 50%;
        background: #00334B;
        box-shadow: 0 12px 30px rgba(0, 51, 75, 0.35);
        border: 3px solid rgba(255, 255, 255, 0.1);
        outline: 1.5px solid rgba(253, 187, 34, 0.3);
        outline-offset: -8px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
    }

    .fingerprint-inner-btn:active {
        transform: scale(0.95);
        box-shadow: 0 4px 15px rgba(0, 51, 75, 0.4);
    }

    .fingerprint-icon-svg {
        width: 52px;
        height: 52px;
        color: #FDBB22;
        margin-bottom: 4px;
        filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
    }

    .fingerprint-label-text {
        font-size: 12px;
        font-weight: 700;
        color: #ffffff;
        letter-spacing: 0.2px;
    }

    .punch-prompt-title {
        font-size: 18px;
        font-weight: 800;
        color: #00334B;
        margin-bottom: 6px;
    }

    .punch-prompt-desc {
        font-size: 13px;
        color: #64748B;
        font-weight: 500;
        line-height: 1.5;
        max-width: 290px;
        margin: 0 auto 16px auto;
    }

    /* Facial Recognition Pill Button */
    .btn-face-id {
        background: #ECEFF3;
        border-radius: 20px;
        padding: 8px 24px;
        color: #475569;
        font-size: 13px;
        font-weight: 700;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-face-id:hover {
        background: #E2E8F0;
    }

    .btn-face-id .face-badge-icon {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: #E2E8F0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
    }


    /* Teacher Profile Card matching design */
    .teacher-status-card {
        background: #ffffff;
        border-radius: 18px;
        padding: 14px 16px;
        box-shadow: 0 2px 14px rgba(0, 0, 0, 0.03);
        border: 1px solid rgba(0, 0, 0, 0.04);
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    .teacher-status-card .teacher-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .teacher-status-card .teacher-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #E2E8F0;
        flex-shrink: 0;
    }

    .teacher-status-card .teacher-name {
        font-size: 15px;
        font-weight: 800;
        color: #00334B;
        line-height: 1.2;
        margin-bottom: 2px;
    }

    .teacher-status-card .teacher-dept {
        font-size: 11.5px;
        font-weight: 600;
        color: #004B6B;
        line-height: 1.2;
    }

    .teacher-status-card .status-side {
        text-align: left;
    }

    .teacher-status-card .status-label {
        font-size: 11px;
        color: #94A3B8;
        font-weight: 600;
        display: block;
        margin-bottom: 2px;
    }

    .teacher-status-card .status-value {
        font-size: 12px;
        font-weight: 800;
        color: #00334B;
    }

    /* GPS Coordinates hidden */
    #punch-gps-status {
        font-size: 11px;
        color: #94A3B8;
        margin-top: 8px;
    }

    /* Modal Dialog Popup for Successful Punch (Centered in middle of screen) */
    .punch-success-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 20, 32, 0.78);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        z-index: 99999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease;
    }

    .punch-success-overlay.show {
        opacity: 1;
        pointer-events: auto;
    }

    .punch-success-sheet {
        background: linear-gradient(180deg, #013149 0%, #001f30 100%);
        border-radius: 28px;
        width: 100%;
        max-width: 350px;
        padding: 26px 20px 22px 20px;
        box-shadow: 0 16px 45px rgba(0, 0, 0, 0.55);
        border: 1px solid rgba(255, 255, 255, 0.15);
        text-align: center;
        transform: scale(0.85);
        opacity: 0;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
    }

    .punch-success-overlay.show .punch-success-sheet {
        transform: scale(1);
        opacity: 1;
    }

    /* Badge Emblem matching image */
    .punch-badge-wrapper {
        position: relative;
        width: 90px;
        height: 90px;
        margin: 0 auto 16px auto;
    }

    .punch-badge-white-ring {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        background: #ffffff;
        padding: 7px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
    }

    .punch-badge-inner-dark {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background: #00334B;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 40px;
    }

    .punch-badge-mini-gold {
        position: absolute;
        bottom: 0;
        right: 0;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: #FDBB22;
        color: #002233;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        border: 2.5px solid #013149;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
    }

    /* Pill Badge with dot */
    .punch-pill-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.12);
        padding: 5px 16px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        color: #C0D6E4;
        margin-bottom: 12px;
    }

    .punch-pill-badge .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #FDBB22;
        box-shadow: 0 0 10px #FDBB22;
    }

    .punch-modal-title {
        color: #FDBB22;
        font-size: 21px;
        font-weight: 800;
        margin: 0 0 6px 0;
        letter-spacing: -0.2px;
    }

    .punch-modal-subtitle {
        color: #CFE0EC;
        font-size: 13.5px;
        font-weight: 500;
        margin: 0 0 20px 0;
    }

    /* 2-Column Stats Box matching image */
    .punch-stats-box {
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 16px;
        padding: 14px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .stat-col {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
    }

    .stat-label {
        color: #8CAABF;
        font-size: 11.5px;
        font-weight: 600;
    }

    .stat-val-time {
        color: #FFFFFF;
        font-size: 18px;
        font-weight: 800;
        font-family: 'Tajawal', sans-serif;
    }

    .stat-val-status {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        color: #FFFFFF;
        font-size: 13px;
        font-weight: 700;
    }

    .stat-val-status i {
        color: #10B981;
        font-size: 17px;
    }

    .stat-divider {
        width: 1px;
        height: 38px;
        background: rgba(255, 255, 255, 0.12);
    }

    /* Auto-redirect button */
    .punch-redirect-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        padding: 13px 18px;
        background: rgba(253, 187, 34, 0.15);
        border: 1px solid rgba(253, 187, 34, 0.45);
        border-radius: 14px;
        color: #FDBB22;
        font-size: 14px;
        font-weight: 800;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .punch-redirect-btn:active {
        transform: scale(0.98);
        background: rgba(253, 187, 34, 0.25);
    }

    /* 0. Dashboard Top Navigation Header matching all other portal screens */
    .portal-dashboard-container {
        max-width: 500px;
        margin: 0 auto;
        padding: 0 0 90px 0;
    }

    .portal-dashboard-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
        padding: 0 4px;
    }

    .portal-dashboard-nav-btn {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: #ffffff;
        border: 1px solid rgba(0, 51, 75, 0.08);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(0, 51, 75, 0.04);
        color: #00334B;
        font-size: 24px;
        text-decoration: none;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        flex-shrink: 0;
    }

    .portal-dashboard-nav-btn:hover {
        transform: translateY(-1px);
        background: #f8fafc;
        box-shadow: 0 4px 12px rgba(0, 51, 75, 0.08);
        border-color: rgba(0, 51, 75, 0.15);
        color: #00334B;
    }

    .portal-dashboard-header-title {
        font-size: 18px;
        font-weight: 800;
        color: #00334B;
        margin: 0;
        text-align: center;
        flex-grow: 1;
    }

    .portal-dashboard-container .portal-header {
        margin: 0 0 14px 0;
    }
</style>
@endpush

@section('content')
@php
    $hasCheckIn = $hasCheckIn ?? !empty($todayAttendance?->check_in);
    $hasCheckOut = $hasCheckOut ?? !empty($todayAttendance?->check_out);
    $checkInTimeFormatted = $checkInTimeFormatted ?? ($hasCheckIn ? \Carbon\Carbon::parse($todayAttendance->check_in)->format('h:i A') : null);
    $checkOutTimeFormatted = $checkOutTimeFormatted ?? ($hasCheckOut ? \Carbon\Carbon::parse($todayAttendance->check_out)->format('h:i A') : null);
@endphp
<div class="portal-dashboard-container">
    <!-- 0. Top Navigation Header matching all other portal screens -->
    <div class="portal-dashboard-header">
        <a href="{{ route('portal.profile', [], false) }}" class="portal-dashboard-nav-btn" aria-label="{{ __('Profile') }}">
            <i class="bx {{ app()->getLocale() === 'ar' ? 'bx-chevron-right' : 'bx-chevron-left' }}"></i>
        </a>
        <h1 class="portal-dashboard-header-title">{{ __('Home') }}</h1>
        @include('employeeportal::components.notification-bell')
    </div>

    <!-- 1. Original Header Card -->
    <header class="portal-header">
        <!-- Date Badge & Status Badge Row -->
        <div class="header-top-badges">
            <!-- 1. Date Badge (Kuwait) -->
            <div class="badge-date-pill">
                <i class="bx bxs-calendar"></i>
                <span>{{ $portalCurrentDate ?? (now('Asia/Kuwait')->translatedFormat('l, d F Y') . ' • ' . __('Kuwait')) }}</span>
            </div>

            <!-- 2. Status Pill with dot -->
            <div class="badge-status-pill">
                @if($hasCheckIn && $hasCheckOut)
                    <span class="status-dot" style="background: #10B981; box-shadow: 0 0 6px #10B981;"></span>
                    <span>{{ __("Today's shift completed ✓ (Out: :time)", ['time' => $checkOutTimeFormatted]) }}</span>
                @elseif($hasCheckIn)
                    <span class="status-dot" style="background: #10B981; box-shadow: 0 0 6px #10B981;"></span>
                    <span>{{ __('Checked in') }} {{ $checkInTimeFormatted }}</span>
                @else
                    <span class="status-dot"></span>
                    <span>{{ __('Awaiting Punch') }}</span>
                @endif
            </div>
        </div>

        <!-- User Greeting & Avatar Row -->
        <div class="header-user-row">
            <a href="{{ route('portal.profile', [], false) }}" class="user-info-side" style="text-decoration: none; color: inherit;">
                <div class="user-avatar-ring">
                    <img src="{{ $avatarUrl ?? (auth()->user()?->avatar_url ?? asset('assets/portal/teacher-avatar.png')) }}" alt="Avatar" onerror="this.onerror=null; this.src='{{ asset('assets/portal/teacher-avatar.png') }}';" />
                </div>

                <div class="greeting-text-col">
                    <div class="greeting-name">
                        <span>{{ $greetingTime ?? (now('Asia/Kuwait')->hour < 12 ? __('Good Morning') : __('Good Evening')) }}, {{ auth()->user()?->employee?->name ?? (auth()->user()?->name ?? __('Employee')) }}</span>
                        <span class="wave-hand">👋</span>
                    </div>
                    <div class="greeting-role">
                        {{ auth()->user()?->employee?->jobTitle?->name ?? __('Staff Member') }}
                        @if(auth()->user()?->employee?->primaryBranch)
                            • {{ auth()->user()?->employee?->primaryBranch?->name }}
                        @endif
                    </div>
                </div>
            </a>
        </div>
    </header>

    <!-- 1. Central Punch Center Card -->
    <div class="punch-hero-card">
        <!-- Top Badge -->
        <div class="punch-time-badge">
            <i class="bx bx-time-five" style="color: #D97706;"></i>
            <span>{{ __('Official Attendance Time') }}</span>
        </div>

        <!-- Digital Live Clock -->
        <div class="digital-live-clock" id="portalLiveClock">
            {{ now('Asia/Kuwait')->format('H:i:s') }}
        </div>
        <div class="clock-timezone-label">
            {{ now('Asia/Kuwait')->hour < 12 ? __('AM') : __('PM') }} {{ __('Kuwait Local Time') }}
        </div>

        <!-- Big Concentric Fingerprint Button -->
        <div class="fingerprint-outer-halo" style="{{ $hasCheckIn && !$hasCheckOut ? 'background: radial-gradient(circle, #FEE2E2 0%, #FFF5F5 65%, #FDE8E8 100%); box-shadow: 0 0 0 10px rgba(254, 226, 226, 0.4);' : ($hasCheckIn && $hasCheckOut ? 'background: radial-gradient(circle, #D1FAE5 0%, #ECFDF5 65%, #E6FBF0 100%); box-shadow: 0 0 0 10px rgba(209, 250, 229, 0.4);' : '') }}">
            <button type="button" class="fingerprint-inner-btn" id="btnFingerprintPunch"
                {{ $hasCheckIn && $hasCheckOut ? 'disabled' : '' }}
                title="{{ $hasCheckIn && !$hasCheckOut ? __('Touch to record check-out') : ($hasCheckIn && $hasCheckOut ? __('Shift Completed') : __('Touch to Punch')) }}"
                style="{{ $hasCheckIn && !$hasCheckOut ? 'background: #991B1B; box-shadow: 0 12px 30px rgba(153, 27, 27, 0.35);' : ($hasCheckIn && $hasCheckOut ? 'background: #065F46; box-shadow: 0 12px 30px rgba(6, 95, 70, 0.35); opacity: 0.85;' : '') }}">
                <!-- Golden Fingerprint Icon -->
                <svg class="fingerprint-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M2 12C2 6.5 6.5 2 12 2a10 10 0 0 1 8 4"/>
                    <path d="M5 19.5C5.5 18 6 15 6 12c0-.7.12-1.37.34-2"/>
                    <path d="M17.29 21.02c.12-.6.18-1.2.18-1.82 0-3-1-5.5-2.5-7.5"/>
                    <path d="M8.65 22c.21-.66.45-1.32.75-2 1.2-2.7 2.6-4.5 4.6-6.2"/>
                    <path d="M14 4.4a8 8 0 0 1 5.3 5.3"/>
                    <path d="M10 2c-.6.2-1.2.5-1.7.9"/>
                    <path d="M11 6a6 6 0 0 1 5.7 5.7"/>
                    <path d="M12 10a2 2 0 0 1 2 2c0 2-1 3.5-2 5"/>
                </svg>
                <span class="fingerprint-label-text">
                    @if($hasCheckIn && $hasCheckOut)
                        {{ __('Shift Completed') }}
                    @elseif($hasCheckIn)
                        {{ __('Check-Out Punch') }}
                    @else
                        {{ __('Touch to Punch') }}
                    @endif
                </span>
            </button>
        </div>

        <!-- Punch Title & Subtitle -->
        @if($hasCheckIn && $hasCheckOut)
            <h4 class="punch-prompt-title" style="color: #059669;">{{ __("Today's shift completed successfully") }}</h4>
            <p class="punch-prompt-desc">
                {{ __('Checked in at (:in) and checked out at (:out)', ['in' => $checkInTimeFormatted, 'out' => $checkOutTimeFormatted]) }}
            </p>
        @elseif($hasCheckIn)
            <h4 class="punch-prompt-title" style="color: #DC2626;">{{ __('Tap to record check-out') }}</h4>
            <p class="punch-prompt-desc">
                {{ __('Your check-in was recorded at (:time). Please place your thumb on the screen to end your work shift.', ['time' => $checkInTimeFormatted]) }}
            </p>
        @else
            <h4 class="punch-prompt-title">{{ __('Tap to record punch') }}</h4>
            <p class="punch-prompt-desc">
                {{ __('Please place your thumb on the screen to start your work shift') }}
            </p>
        @endif

        @if(!$hasCheckIn || !$hasCheckOut)
            <!-- Face ID Button -->
            <button type="button" class="btn-face-id" id="btnFaceId">
                <span>{{ __('Face ID') }}</span>
                <i class="bx bx-scan" style="font-size: 16px; color: #00334B;"></i>
            </button>
        @endif

        <div id="punch-gps-status"></div>
        <input type="hidden" id="geoLat" />
        <input type="hidden" id="geoLng" />
    </div>

    <!-- 3. Teacher Profile Card -->
    <div class="teacher-status-card">
        <div class="teacher-info">
            <img src="{{ auth()->user()?->avatar_url ?? asset('assets/portal/male-avatar.png') }}" alt="Avatar" class="teacher-avatar" />
            <div>
                <div class="teacher-name">
                    {{ auth()->user()?->employee?->name ?? (auth()->user()?->name ?? __('Employee')) }}
                </div>
                <div class="teacher-dept">
                    {{ auth()->user()?->employee?->jobTitle?->name ?? __('Staff Member') }}
                </div>
            </div>
        </div>

        <div class="status-side">
            <span class="status-label">{{ __("Today's Status") }}</span>
            <span class="status-value" style="{{ $hasCheckIn ? 'color: #059669;' : '' }}">
                @if($hasCheckIn && $hasCheckOut)
                    {{ __("Shift Completed (Out: :time)", ['time' => $checkOutTimeFormatted]) }}
                @elseif($hasCheckIn)
                    {{ __('Checked in') }}: {{ $checkInTimeFormatted }}
                @else
                    {{ __('Awaiting Punch') }}
                @endif
            </span>
        </div>
    </div>

    <!-- 4. Success Modal / Bottom Sheet -->
    <div class="punch-success-overlay" id="punchSuccessModal">
        <div class="punch-success-sheet">

            <!-- Central Badge Emblem -->
            <div class="punch-badge-wrapper">
                <div class="punch-badge-white-ring">
                    <div class="punch-badge-inner-dark">
                        <i class="bx bx-check"></i>
                    </div>
                </div>
                <div class="punch-badge-mini-gold">
                    <i class="bx bxs-award"></i>
                </div>
            </div>

            <!-- Pill Badge with Yellow Dot -->
            <div class="punch-pill-badge">
                <span class="dot"></span>
                <span>{{ __('Digital Punch Approved') }}</span>
            </div>

            <!-- Title -->
            <h2 class="punch-modal-title" id="punchModalTitle">{{ __('Your check-in was successfully recorded!') }}</h2>

            <!-- Subtitle Greeting -->
            <p class="punch-modal-subtitle" id="punchModalSubtitle">
                {{ __('Hello :name, have a great day!', ['name' => $employee->first_name ?? $employee->name ?? __('Teacher')]) }}
            </p>

            <!-- 2-Column Stats Box -->
            <div class="punch-stats-box">
                <!-- Actual Punch Time -->
                <div class="stat-col">
                    <span class="stat-label">{{ __('Actual Punch Time') }}</span>
                    <span class="stat-val-time" id="punchModalTime">
                        {{ now('Asia/Kuwait')->format('h:i') }} {{ now('Asia/Kuwait')->format('A') === 'AM' ? __('AM') : __('PM') }}
                    </span>
                </div>

                <div class="stat-divider"></div>

                <!-- Attendance Status -->
                <div class="stat-col">
                    <span class="stat-label">{{ __('Attendance Status') }}</span>
                    <div class="stat-val-status">
                        <i class="bx bx-check-circle"></i>
                        <span id="punchModalStatus">{{ __('Early & On-Time Check-In') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Live Digital Clock (HH:MM:SS)
    setInterval(() => {
        const now = new Date();
        const clockEl = document.getElementById('portalLiveClock');
        if (clockEl) {
            const h = String(now.getHours()).padStart(2, '0');
            const m = String(now.getMinutes()).padStart(2, '0');
            const s = String(now.getSeconds()).padStart(2, '0');
            clockEl.textContent = `${h}:${m}:${s}`;
        }
    }, 1000);

    // Geolocation Acquisition
    if ('geolocation' in navigator) {
        navigator.geolocation.getCurrentPosition(
            (pos) => {
                document.getElementById('geoLat').value = pos.coords.latitude;
                document.getElementById('geoLng').value = pos.coords.longitude;
            },
            () => {}
        );
    }

    // Fingerprint Button Click Action
    const punchBtn = document.getElementById('btnFingerprintPunch');
    if (punchBtn) {
        punchBtn.addEventListener('click', async () => {
            const lat = document.getElementById('geoLat').value;
            const lng = document.getElementById('geoLng').value;

            punchBtn.disabled = true;
            punchBtn.style.opacity = '0.7';

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                const punchUrl = "{{ route('portal.attendance.punch', [], false) }}";

                const response = await fetch(punchUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        punch_type: '{{ $hasCheckIn && !$hasCheckOut ? "check_out" : "check_in" }}',
                        latitude: lat ? parseFloat(lat) : null,
                        longitude: lng ? parseFloat(lng) : null
                    })
                });

                const data = await response.json().catch(() => null);

                if (response.ok && data && data.success) {
                    // Fill modal details
                    if (data.data?.title) {
                        document.getElementById('punchModalTitle').textContent = data.data.title;
                    }
                    if (data.data?.greeting) {
                        document.getElementById('punchModalSubtitle').textContent = data.data.greeting;
                    }
                    if (data.data?.punch_time_formatted) {
                        document.getElementById('punchModalTime').textContent = data.data.punch_time_formatted;
                    }
                    if (data.data?.status_label) {
                        document.getElementById('punchModalStatus').textContent = data.data.status_label;
                    }

                    // Show Centered Success Modal
                    const modal = document.getElementById('punchSuccessModal');
                    if (modal) {
                        modal.classList.add('show');
                    }

                    // Vibrate if supported
                    if ('vibrate' in navigator) {
                        navigator.vibrate([100, 50, 100]);
                    }

                    // Automatically transition to Profile after 2 seconds
                    setTimeout(() => {
                        window.location.href = "{{ route('portal.profile', [], false) }}";
                    }, 2000);
                } else {
                    const errorMsg = data?.message || ('{{ __("Punch failed") }} (' + response.status + ')');
                    alert('⚠️ ' + errorMsg);
                    punchBtn.disabled = false;
                    punchBtn.style.opacity = '1';
                }
            } catch (err) {
                console.error('Punch request failed:', err);
                alert('⚠️ {{ __("Connection error with server") }}: ' + (err.message || '{{ __("Please check your network connection") }}'));
                punchBtn.disabled = false;
                punchBtn.style.opacity = '1';
            }
        });
    }

    // Face ID Button Simulation
    const faceBtn = document.getElementById('btnFaceId');
    if (faceBtn) {
        faceBtn.addEventListener('click', () => {
            alert('📸 {{ __("Activating camera for Face ID scan...") }}');
            if (punchBtn) punchBtn.click();
        });
    }
</script>
@endpush
