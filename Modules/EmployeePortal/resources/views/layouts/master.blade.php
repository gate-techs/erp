<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', __('Attendance Portal')) | {{ __('Astro Leaders') }}</title>

    <!-- PWA Primary Meta Tags -->
    <meta name="theme-color" content="#00334B" />
    <meta name="mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
    <meta name="apple-mobile-web-app-title" content="{{ __('Astro Leaders') }}" />
    <meta name="application-name" content="{{ __('Astro Leaders') }}" />
    <link rel="manifest" href="{{ route('portal.manifest') }}" />
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('assets/portal/icon-192.png') }}" />
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('assets/portal/icon-512.png') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/portal/apple-touch-icon.png') }}" />
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/portal/icon-192.png') }}" />

    <!-- Google Fonts (Cairo & Inter) -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet" />

    <!-- Boxicons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" />

    <!-- Bootstrap 5 CSS (RTL / LTR) -->
    @if(app()->getLocale() === 'ar')
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet" />
    @else
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    @endif

    <style>
        :root {
            --portal-primary: #00334B;
            --portal-primary-dark: #002233;
            --portal-primary-light: #004B6B;
            --portal-accent: #FDBB22;
            --portal-bg: #F4F6FA;
            --portal-card-bg: #ffffff;
            --portal-bottom-nav-height: 64px;
            --portal-safe-area-bottom: env(safe-area-inset-bottom, 0px);
        }

        * {
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: {{ app()->getLocale() === 'ar' ? "'Cairo', sans-serif" : "'Inter', 'Cairo', sans-serif" }};
            background-color: var(--portal-bg);
            color: #1F2937;
            margin: 0;
            padding: 0;
            padding-bottom: 78px;
            user-select: none;
            -webkit-font-smoothing: antialiased;
        }

        .portal-app-wrapper {
            max-width: 440px;
            margin: 0 auto;
            position: relative;
            min-height: 100vh;
            min-height: 100dvh;
            padding-top: calc(env(safe-area-inset-top, 0px) + 8px);
        }

        /* Top Mobile App Header matching user's design image */
        .portal-header {
            background: linear-gradient(180deg, #073850 0%, #0A4A69 100%);
            color: #ffffff;
            padding: 13px 16px 17px 16px;
            border-radius: 20px;
            margin: 0 12px 14px 12px;
            box-shadow: 0 6px 20px rgba(0, 40, 60, 0.22);
        }


        .header-top-badges {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        /* Right: Kuwait Hijri Date Badge */
        .badge-date-pill {
            background: rgba(0, 0, 0, 0.28);
            color: #C6DFEA;
            font-size: 11px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .badge-date-pill i {
            color: #FDBB22;
            font-size: 13px;
        }

        /* Left: Status Pill */
        .badge-status-pill {
            background: #2B5759;
            color: #8FD8C7;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 11px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .badge-status-pill .status-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #FDBB22;
            display: inline-block;
        }

        /* User Greeting Row */
        .header-user-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .user-info-side {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar-ring {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            border: 2px solid #FDBB22;
            padding: 1px;
            background: #00334B;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .user-avatar-ring img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }

        .greeting-text-col {
            display: flex;
            flex-direction: column;
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
        }

        .greeting-name {
            font-size: 16.5px;
            font-weight: 800;
            color: #FDBB22;
            line-height: 1.25;
            margin-bottom: 2px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .greeting-name .wave-hand {
            font-size: 17px;
        }

        .greeting-role {
            font-size: 11.5px;
            font-weight: 600;
            color: #8FB2C5;
            line-height: 1.2;
        }

        /* Language Switcher Pill Button */
        .portal-lang-btn {
            height: 38px;
            padding: 0 10px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.16);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.22);
            display: inline-flex;
            align-items: center;
            gap: 4px;
            color: #ffffff;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s ease;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            flex-shrink: 0;
        }

        .portal-lang-btn:hover, .portal-lang-btn:active {
            background: rgba(253, 187, 34, 0.25);
            border-color: #FDBB22;
            color: #FDBB22;
        }

        .portal-lang-btn i {
            font-size: 15px;
            color: #FDBB22;
        }

        /* Left: Notification Bell */
        .notification-bell-btn {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #DCE5EB;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #00334B;
            font-size: 21px;
            position: relative;
            text-decoration: none;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .notification-bell-btn .bell-badge-dot {
            position: absolute;
            top: 8px;
            right: 10px;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #FDBB22;
            box-shadow: 0 0 6px rgba(253, 187, 34, 0.8);
        }

        /* 4-Tab Bottom Navigation Bar matching user image perfectly */
        .portal-bottom-nav {
            position: fixed;
            bottom: 10px;
            left: 50%;

            transform: translateX(-50%);
            width: calc(100% - 24px);
            max-width: 416px;
            height: 80px;
            background: #ffffff;
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: space-around;
            z-index: 1040;
            box-shadow: 0 4px 20px rgba(0, 51, 75, 0.08);
            border: 1px solid rgba(0, 51, 75, 0.05);
            padding: 3px 6px;
        }

        .portal-nav-tab {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #64748B;
            text-decoration: none;
            position: relative;
            height: 100%;
            flex: 1;
            padding: 2px 0;
            gap: 2px;
            transition: all 0.2s ease;
        }

        .portal-nav-tab span {
            font-size: 11px;
            font-weight: 600;
            line-height: 1;
            color: #64748B;
        }

        .portal-nav-tab .tab-icon-wrapper {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 46px;
            height: 32px;
            border-radius: 12px;
            transition: all 0.2s ease;
        }

        .portal-nav-tab i {
            font-size: 22px;
            color: #64748B;
            line-height: 1;
            transition: all 0.2s ease;
        }

        .portal-nav-tab.active {
            color: #00334B;
        }

        .portal-nav-tab.active span {
            color: #00334B;
            font-weight: 800;
        }

        .portal-nav-tab.active .tab-icon-wrapper {
            background: #EEF4F8;
        }

        .portal-nav-tab.active i {
            color: #00334B;
        }

        /* Top yellow arch indicator protruding above active tab matching user image */
        .portal-nav-tab.active::before {
            content: '';
            position: absolute;
            top: -4px;
            width: 40px;
            height: 4px;
            background: #F59E0B;
            border-radius: 4px 4px 0 0;
            box-shadow: 0 -1px 3px rgba(245, 158, 11, 0.4);
        }

        .portal-nav-tab .dot-badge {
            position: absolute;
            top: 2px;
            right: 8px;
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #F59E0B;
            border: 1.5px solid #ffffff;
            box-shadow: 0 0 3px rgba(245, 158, 11, 0.6);
        }
    </style>

    @stack('styles')
</head>

<body>
    <div class="portal-app-wrapper">
        @php
            $portalCurrentDate = '';
            if (app()->getLocale() === 'ar') {
                try {
                    if (class_exists('IntlDateFormatter')) {
                        $hijriFormatter = new \IntlDateFormatter('ar_SA@calendar=islamic-umalqura', \IntlDateFormatter::FULL, \IntlDateFormatter::NONE, 'Asia/Kuwait', \IntlDateFormatter::TRADITIONAL, 'EEEE، d MMMM y هـ');
                        $portalCurrentDate = $hijriFormatter->format(now('Asia/Kuwait')) . ' • الكويت';
                    } else {
                        \Carbon\Carbon::setLocale('ar');
                        $portalCurrentDate = now('Asia/Kuwait')->translatedFormat('l، d F Y') . ' • الكويت';
                    }
                } catch (\Throwable $e) {
                    \Carbon\Carbon::setLocale('ar');
                    $portalCurrentDate = now('Asia/Kuwait')->translatedFormat('l، d F Y') . ' • الكويت';
                }
            } else {
                \Carbon\Carbon::setLocale('en');
                $portalCurrentDate = now('Asia/Kuwait')->translatedFormat('l, d F Y') . ' • Kuwait';
            }

            $currentHour = now('Asia/Kuwait')->hour;
            $greetingTime = $currentHour < 12 ? __('Good Morning') : __('Good Evening');
        @endphp

        <!-- Top Mobile App Header matching design -->
        <header class="portal-header">
            <!-- Date Badge & Status Badge Row -->
            <div class="header-top-badges">
                <!-- 1. Date Badge (Kuwait) -->
                <div class="badge-date-pill">
                    <i class="bx bxs-calendar"></i>
                    <span>{{ $portalCurrentDate }}</span>
                </div>

                <!-- 2. Status Pill with dot -->
                <div class="badge-status-pill">
                    @if(isset($todayAttendance) && !empty($todayAttendance->check_out))
                        <span class="status-dot" style="background: #10B981; box-shadow: 0 0 6px #10B981;"></span>
                        <span>{{ __("Today's shift completed ✓") }}</span>
                    @elseif(isset($todayAttendance) && !empty($todayAttendance->check_in))
                        <span class="status-dot" style="background: #10B981; box-shadow: 0 0 6px #10B981;"></span>
                        <span>{{ __('Checked in') }} {{ \Carbon\Carbon::parse($todayAttendance->check_in)->format('h:i A') }}</span>
                    @else
                        <span class="status-dot"></span>
                        <span>{{ __('Awaiting Punch') }}</span>
                    @endif
                </div>
            </div>

            <!-- User Greeting & Avatar & Notification Bell & Lang Switcher -->
            <div class="header-user-row">
                <!-- Avatar ring + Teacher greeting -->
                <a href="{{ route('portal.profile', [], false) }}" class="user-info-side"
                    style="text-decoration: none; color: inherit;">
                    <div class="user-avatar-ring">
                        <img src="{{ auth()->user()?->avatar_url ?? asset('assets/portal/header-emblem.png') }}" alt="Avatar" />
                    </div>

                    <div class="greeting-text-col">
                        <div class="greeting-name">
                            <span>{{ $greetingTime }}, {{ auth()->user()?->employee?->name ?? (auth()->user()?->name ?? __('Employee')) }}</span>
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

                <!-- Left / Right: Notification Bell -->
                <a href="javascript:void(0)" class="notification-bell-btn">
                    <i class="bx bxs-bell"></i>
                    <span class="bell-badge-dot"></span>
                </a>
            </div>
        </header>

        <!-- Main Content Container -->
        <main id="portalMainContent" class="container-fluid px-3 pt-1">

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show rounded-4 py-2 px-3 fs-7" role="alert">
                    <i class="bx bx-error me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-4 py-2 px-3 fs-7" role="alert">
                    <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </main>

        <!-- 4-Tab Bottom Navigation Bar matching design -->
        <nav class="portal-bottom-nav">
            <!-- 1. الرئيسية (Home) -->
            <a href="{{ route('portal.dashboard', [], false) }}"
                class="portal-nav-tab {{ request()->routeIs('portal.dashboard') ? 'active' : '' }}">
                <div class="tab-icon-wrapper">
                    <i class="bx {{ request()->routeIs('portal.dashboard') ? 'bxs-home' : 'bx-home' }}"></i>
                </div>
                <span>{{ __('Home') }}</span>
            </a>

            <!-- 2. الطلبات (Requests) with yellow dot badge -->
            <a href="javascript:void(0)" class="portal-nav-tab" onclick="alert('{{ __('Requests screen is under development') }}')">
                <div class="tab-icon-wrapper">
                    <i class="bx bx-receipt"></i>
                    <span class="dot-badge"></span>
                </div>
                <span>{{ __('Requests') }}</span>
            </a>

            <!-- 3. الحضور (Attendance) -->
            <a href="{{ route('portal.dashboard', [], false) }}" class="portal-nav-tab">
                <div class="tab-icon-wrapper">
                    <i class="bx bx-time-five"></i>
                </div>
                <span>{{ __('Attendance') }}</span>
            </a>

            <!-- 4. الملف الشخصي (Profile) -->
            <a href="{{ route('portal.profile', [], false) }}"
                class="portal-nav-tab {{ request()->routeIs('portal.profile') ? 'active' : '' }}">
                <div class="tab-icon-wrapper">
                    <i class="bx {{ request()->routeIs('portal.profile') ? 'bxs-user' : 'bx-user' }}"></i>
                </div>
                <span>{{ __('Profile') }}</span>
            </a>
        </nav>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- PWA Install Prompt & Service Worker Component -->
    @include('employeeportal::components.pwa-installer')

    @stack('scripts')
</body>

</html>