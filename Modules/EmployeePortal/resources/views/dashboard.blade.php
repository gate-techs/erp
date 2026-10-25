@extends('employeeportal::layouts.master')

@section('title', __('Attendance Punch'))

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
                <span>{{ $portalCurrentDate ?? (now('Asia/Kuwait')->translatedFormat('d M Y') . ' • ' . __('Kuwait')) }}</span>
            </div>

            <!-- 2. Status Pill with dot -->
            <div class="badge-status-pill">
                @if($hasCheckIn && $hasCheckOut)
                    <span class="status-dot" style="background: #10B981; box-shadow: 0 0 6px #10B981;"></span>
                    <span>{{ __('Check-out') }} {{ $checkOutTimeFormatted }}</span>
                @elseif($hasCheckIn)
                    <span class="status-dot" style="background: #10B981; box-shadow: 0 0 6px #10B981;"></span>
                    <span>{{ __('Check-in') }} {{ $checkInTimeFormatted }}</span>
                @else
                    <span class="status-dot"></span>
                    <span>{{ __('Not Started') }}</span>
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
            <h4 class="punch-prompt-title" style="color: #059669; font-size: 15px; margin-bottom: 2px;">{{ __("Today's Shift Completed ✓") }}</h4>
        @elseif($hasCheckIn)
            <h4 class="punch-prompt-title" style="color: #DC2626; font-size: 15px; margin-bottom: 2px;">{{ __('Check-Out Punch') }}</h4>
        @else
            <h4 class="punch-prompt-title" style="font-size: 15px; margin-bottom: 2px;">{{ __('Check-In Punch') }}</h4>
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

    <!-- ========================================================================= -->
    <!-- 2. Daily Attendance Timeline Tracker (تحت البصمة بتصميم عمودي بسيط واحترافي) -->
    <!-- ========================================================================= -->
    @php
        $tData = $dayTimeline ?? [];
        $tState = $tData['progress_state'] ?? ($hasCheckIn && $hasCheckOut ? 'completed' : ($hasCheckIn ? 'in_progress' : 'not_started'));
        $tInTime = $tData['check_in_formatted'] ?? $checkInTimeFormatted;
        $tOutTime = $tData['check_out_formatted'] ?? $checkOutTimeFormatted;
        $tEmp = $tData['employee'] ?? [];
        $tShiftStart = $tEmp['shift_start'] ?? null;
        $tShiftEnd = $tEmp['shift_end'] ?? null;
        $tWorkingHours = $tData['total_working_hours'] ?? ($todayAttendance?->total_working_hours ?? 0);
    @endphp

    <div class="portal-tracker-card" id="portalAttendanceTracker">
        <!-- Tracker Header -->
        <div class="portal-tracker-header">
            <div>
                @if($tState === 'completed')
                    <span class="portal-tracker-status-tag tag-completed">
                        <i class="bx bx-check-circle"></i>
                        <span>{{ __('Completed ✓') }}</span>
                    </span>
                @elseif($tState === 'in_progress')
                    <span class="portal-tracker-status-tag tag-in_progress">
                        <i class="bx bx-loader-alt bx-spin font-13"></i>
                        <span>{{ __('Shift in Progress') }}</span>
                    </span>
                @else
                    <span class="portal-tracker-status-tag tag-not_started">
                        <i class="bx bx-time"></i>
                        <span>{{ __('Not Started Yet') }}</span>
                    </span>
                @endif
            </div>

            <div class="portal-tracker-title-wrap">
                <h5 class="portal-tracker-title">{{ __('Daily Attendance Tracker') }}</h5>
                <span class="portal-tracker-icon">
                    <i class="bx bx-run"></i>
                </span>
            </div>
        </div>

        <!-- Clean Vertical Pipeline (خطوات متتالية فوق بعضها بانسيابية) -->
        <div class="portal-vtrack-list">
            <!-- Step 1: Check-In (الحضور) -->
            <div class="portal-vtrack-item {{ $hasCheckIn ? 'is-done' : '' }}">
                <div class="portal-vtrack-icon-box {{ $hasCheckIn ? 'box-done' : 'box-wait' }}">
                    <i class="bx bx-check"></i>
                </div>
                <div class="portal-vtrack-content">
                    <div class="portal-vtrack-label-col">
                        <span class="portal-vtrack-label">{{ __('Check-In Punch') }}</span>
                        <span class="portal-vtrack-desc">
                            @if($tShiftStart)
                                {{ __('Shift Start') }}: {{ $tShiftStart }}
                            @else
                                {{ __('Scheduled Shift Start') }}
                            @endif
                        </span>
                    </div>
                    <div class="portal-vtrack-time-col">
                        <span class="portal-vtrack-time">{{ $hasCheckIn ? $tInTime : '--:--' }}</span>
                        <span class="portal-vtrack-badge {{ $hasCheckIn ? 'b-green' : 'b-gray' }}">
                            {{ $hasCheckIn ? __('Recorded') : __('Pending') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Step 2: During Shift (الدوام جاري حالياً) -->
            <div class="portal-vtrack-item {{ $hasCheckIn && $hasCheckOut ? 'is-done' : ($hasCheckIn ? 'is-live' : '') }}">
                <div class="portal-vtrack-icon-box {{ $hasCheckIn && $hasCheckOut ? 'box-done' : ($hasCheckIn ? 'box-live' : 'box-wait') }}">
                    @if($hasCheckIn && $hasCheckOut)
                        <i class="bx bx-check-double font-16"></i>
                    @elseif($hasCheckIn)
                        <i class="bx bx-time-five font-16"></i>
                    @else
                        <i class="bx bx-circle font-13"></i>
                    @endif
                </div>
                <div class="portal-vtrack-content">
                    <div class="portal-vtrack-label-col">
                        <span class="portal-vtrack-label">{{ __('Shift in Progress') }}</span>
                        <span class="portal-vtrack-desc">{{ __('Logged Working Hours') }}</span>
                    </div>
                    <div class="portal-vtrack-time-col">
                        <span class="portal-vtrack-time">{{ $tWorkingHours }} {{ __('hrs') }}</span>
                        @if($hasCheckIn && $hasCheckOut)
                            <span class="portal-vtrack-badge b-green">{{ __('Completed') }}</span>
                        @elseif($hasCheckIn)
                            <span class="portal-vtrack-badge b-blue">{{ __('Working') }}</span>
                        @else
                            <span class="portal-vtrack-badge b-gray">-</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Step 3: Check-Out (الانصراف) -->
            <div class="portal-vtrack-item {{ $hasCheckIn && $hasCheckOut ? 'is-done' : '' }}">
                <div class="portal-vtrack-icon-box {{ $hasCheckIn && $hasCheckOut ? 'box-done' : 'box-wait' }}">
                    <i class="bx bx-log-out-circle"></i>
                </div>
                <div class="portal-vtrack-content">
                    <div class="portal-vtrack-label-col">
                        <span class="portal-vtrack-label">{{ __('Check-Out Punch') }}</span>
                        <span class="portal-vtrack-desc">
                            @if($tShiftEnd)
                                {{ __('Shift End') }}: {{ $tShiftEnd }}
                            @else
                                {{ __('Official Shift End Time') }}
                            @endif
                        </span>
                    </div>
                    <div class="portal-vtrack-time-col">
                        <span class="portal-vtrack-time">{{ $hasCheckOut ? $tOutTime : '--:--' }}</span>
                        <span class="portal-vtrack-badge {{ $hasCheckOut ? 'b-green' : 'b-gray' }}">
                            {{ $hasCheckOut ? __('Checked Out') : __('Awaiting Punch') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tracker Footer -->
        <div class="portal-tracker-footer">
            <div>
                <span>{{ __('Total Hours') }}:</span>
                <strong id="portalFooterHours">{{ $tWorkingHours }} {{ __('Hours') }}</strong>
            </div>
            <div>
                <span>{{ __('Shift') }}:</span>
                <strong>{{ $tEmp['shift_name'] ?? __('Primary Shift') }}</strong>
            </div>
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
