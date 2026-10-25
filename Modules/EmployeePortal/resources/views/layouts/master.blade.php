<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', __('Attendance Portal')) | {{ __('ALA ORBIT') }}</title>

    <!-- PWA Primary Meta Tags (Android & Chrome) -->
    <meta name="theme-color" content="#00334B" />
    <meta name="mobile-web-app-capable" content="yes" />
    <meta name="application-name" content="ALA ORBIT" />
    <link rel="manifest" href="{{ route('portal.manifest') }}" />

    <!-- iOS Safari & Web App Meta Tags -->
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
    <meta name="apple-mobile-web-app-title" content="ALA ORBIT" />
    <link rel="apple-touch-icon" href="{{ asset('assets/portal/apple-touch-icon.png') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/portal/apple-touch-icon.png') }}" />
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('assets/portal/icon-192.png') }}" />
    <link rel="apple-touch-icon" sizes="192x192" href="{{ asset('assets/portal/icon-192.png') }}" />

    <!-- Microsoft Tiles & Standard Favicons -->
    <meta name="msapplication-TileColor" content="#00334B" />
    <meta name="msapplication-TileImage" content="{{ asset('assets/portal/icon-512.png') }}" />
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('assets/portal/icon-192.png') }}" />
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('assets/portal/icon-512.png') }}" />
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/portal/icon-192.png') }}" />

    <!-- Google Fonts (Almarai) -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;700;800&display=swap" rel="stylesheet" />

    <!-- Boxicons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" />

    <!-- Bootstrap 5 CSS (RTL / LTR) -->
    @if(app()->getLocale() === 'ar')
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet" />
    @else
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    @endif

    <!-- intl-tel-input CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/css/intlTelInput.min.css" />

    <!-- Unified Portal CSS (Dynamic cache-busting on file change) -->
    <link rel="stylesheet" href="{{ asset('assets/portal/css/portal.css') }}?v={{ file_exists(public_path('assets/portal/css/portal.css')) ? filemtime(public_path('assets/portal/css/portal.css')) : '2.0.0' }}" />

    <!-- Flatpickr Date & Time Picker CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css" />

@stack('styles')
</head>

<body>
    <div class="portal-app-wrapper">


        <!-- Main Content Container -->
        <main id="portalMainContent" class="container-fluid px-3 pt-1">
            @yield('content')
        </main>

        <!-- 4-Tab Bottom Navigation Bar matching design -->
        <nav class="portal-bottom-nav">
            <!-- 1. Home -->
            <a href="{{ route('portal.dashboard', [], false) }}"
                class="portal-nav-tab {{ request()->routeIs('portal.dashboard') ? 'active' : '' }}">
                <div class="tab-icon-wrapper">
                    <i class="bx {{ request()->routeIs('portal.dashboard') ? 'bxs-home' : 'bx-home' }}"></i>
                </div>
                <span>{{ __('Home') }}</span>
            </a>

            <!-- 2. Requests with yellow dot badge -->
            <a href="{{ route('portal.requests', [], false) }}"
                class="portal-nav-tab {{ request()->routeIs('portal.requests') ? 'active' : '' }}">
                <div class="tab-icon-wrapper">
                    <i class="bx {{ request()->routeIs('portal.requests') ? 'bxs-receipt' : 'bx-receipt' }}"></i>
                    <span class="dot-badge"></span>
                </div>
                <span>{{ __('Requests') }}</span>
            </a>

            <!-- 3. Attendance -->
            <a href="{{ route('portal.dashboard', [], false) }}" class="portal-nav-tab">
                <div class="tab-icon-wrapper">
                    <i class="bx bx-time-five"></i>
                </div>
                <span>{{ __('Attendance') }}</span>
            </a>

            <!-- 4. Profile -->
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

    <!-- Flatpickr JS & Arabic Locale -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ar.js"></script>

    <!-- intl-tel-input JS -->
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/intlTelInput.min.js"></script>

    <!-- PWA Install Prompt & Service Worker Component -->
    @include('employeeportal::components.pwa-installer')

    <!-- Animated Mobile Toast (Success / Error) Component -->
    @include('employeeportal::components.portal-toast')

    <!-- Portal Language Storage Handler -->
    <script>
        (function () {
            try {
                const currentLocale = "{{ app()->getLocale() }}";
                localStorage.setItem('astro_portal_locale', currentLocale);
                localStorage.setItem('app_locale', currentLocale);
            } catch (e) {}
        })();

        function savePortalLocaleAndRedirect(event, targetLocale, url) {
            if (event) event.preventDefault();
            try {
                localStorage.setItem('astro_portal_locale', targetLocale);
                localStorage.setItem('app_locale', targetLocale);
            } catch (e) {
                console.error('Failed to save to localStorage:', e);
            }
            window.location.href = url;
        }
    </script>

    @stack('scripts')
</body>

</html>