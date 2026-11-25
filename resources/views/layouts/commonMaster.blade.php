<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="layout-navbar-fixed layout-menu-fixed layout-compact" data-assets-path="{{ asset('/assets') . '/' }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" data-skin="default" data-base-url="{{ url('/') }}" data-framework="laravel" data-bs-theme="light" data-template="vertical-menu-template">

<head>
    <script>
        (function() {
            let theme = localStorage.getItem('astro_theme') || 'light';
            try {
                const saved = localStorage.getItem('vuexy_customizer_state');
                if (saved) {
                    const state = JSON.parse(saved);
                    if (state.skin === 'light' || state.skin === 'dark') theme = state.skin;
                    const locale = (document.documentElement.lang || 'en').split('-')[0].toLowerCase();
                    if (state.directionByLocale && state.directionByLocale[locale]) {
                        const dir = state.directionByLocale[locale];
                        if (dir === 'rtl' || dir === 'ltr') document.documentElement.setAttribute('dir', dir);
                    }
                    if (state.navbarType === 'hidden') {
                        document.documentElement.classList.remove('layout-navbar-fixed');
                    } else {
                        document.documentElement.classList.add('layout-navbar-fixed');
                    }
                    if (state.menuCollapsed) document.documentElement.classList.add('layout-menu-collapsed');
                    if (state.footerType === 'sticky') document.documentElement.classList.add('layout-footer-fixed');
                    else if (state.footerType === 'static' || state.footerType === 'hidden') document.documentElement.classList.remove('layout-footer-fixed');
                } else {
                    document.documentElement.classList.add('layout-navbar-fixed');
                }
            } catch (e) {}
            document.documentElement.setAttribute('data-bs-theme', theme);

            // Apply saved primary color immediately before first paint to prevent FOUC
            try {
                const savedPrimary = localStorage.getItem('astro_primary_color');
                if (savedPrimary) {
                    let hex = savedPrimary.replace('#', '');
                    if (hex.length === 3) hex = hex.split('').map(c => c + c).join('');
                    const bigint = parseInt(hex, 16);
                    if (!isNaN(bigint)) {
                        const rgb = [(bigint >> 16) & 255, (bigint >> 8) & 255, bigint & 255].join(', ');
                        const shade = (h, pct) => {
                            let r = Math.min(255, Math.max(0, Math.round(((bigint >> 16) & 255) * (1 + pct / 100))));
                            let g = Math.min(255, Math.max(0, Math.round(((bigint >> 8) & 255) * (1 + pct / 100))));
                            let b = Math.min(255, Math.max(0, Math.round((bigint & 255) * (1 + pct / 100))));
                            return '#' + [r, g, b].map(x => x.toString(16).padStart(2, '0')).join('');
                        };
                        const hover = shade(hex, -10);
                        const active = shade(hex, -15);
                        const root = document.documentElement;
                        root.style.setProperty('--bs-primary', savedPrimary);
                        root.style.setProperty('--bs-primary-rgb', rgb);
                        root.style.setProperty('--bs-primary-bg-subtle', 'rgba(' + rgb + ', 0.16)');
                        root.style.setProperty('--bs-primary-border-subtle', 'rgba(' + rgb + ', 0.35)');
                        root.style.setProperty('--bs-primary-text-emphasis', savedPrimary);
                        root.style.setProperty('--bs-btn-bg', savedPrimary);
                        root.style.setProperty('--bs-btn-border-color', savedPrimary);
                        root.style.setProperty('--bs-btn-hover-bg', hover);
                        root.style.setProperty('--bs-btn-hover-border-color', shade(hex, -12));
                        root.style.setProperty('--bs-btn-active-bg', active);
                        root.style.setProperty('--bs-link-color', savedPrimary);
                        root.style.setProperty('--bs-link-color-rgb', rgb);
                        root.style.setProperty('--bs-link-hover-color', shade(hex, -20));
                        root.style.setProperty('--bs-primary-color', savedPrimary);
                        root.style.setProperty('--bs-menu-active-bg', savedPrimary);
                        root.style.setProperty('--bs-menu-active-color', '#ffffff');
                        root.style.setProperty('--bs-menu-sub-active-bg', savedPrimary);
                        root.style.setProperty('--bs-menu-sub-active-color', '#ffffff');
                        root.style.setProperty('--bs-focus-ring-color', 'rgba(' + rgb + ', 0.25)');
                        root.style.setProperty('--astro-primary-hover', hover);
                        root.style.setProperty('--astro-primary-active', active);
                    }
                }
            } catch (e) {}
        })();
    </script>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    @php
        // Global System Settings (Singleton cached)
        try {
            $globalSetting = $globalSetting ?? (\Modules\Settings\Models\Setting::instance() ?? null);
        } catch (\Throwable $e) {
            $globalSetting = null;
        }

        $siteName = $globalSetting?->site_name ?? config('variables.templateName', 'GateTech');
        $siteSlogan = $globalSetting?->slogan ?? config('variables.templateDescription', '');
        $siteLogo = $globalSetting?->logo_url ?? asset('assets/img/illustrations/astro-logo.png');
        $siteFavicon = $globalSetting?->favicon_url ?? asset('assets/img/favicon/favicon.ico');
        $siteLoginImage = $globalSetting?->login_image_url ?? asset('assets/img/illustrations/auth-bg-login.jpeg');
        $primaryColor = $globalSetting?->primary_color ?? '#0b284e';
    @endphp

    <title>
        @yield('title') | {{ $siteName }} {{ !empty($siteSlogan) ? '- ' . $siteSlogan : '' }}
    </title>
    <meta name="description" content="{{ $siteSlogan }}" />
    <meta name="keywords" content="{{ config('variables.templateKeyword', '') }}" />
    <meta property="og:title" content="{{ $siteName }}" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:image" content="{{ $siteLogo }}" />
    <meta property="og:description" content="{{ $siteSlogan }}" />
    <meta property="og:site_name" content="{{ $siteName }}" />
    <meta name="robots" content="noindex, nofollow" />
    <!-- laravel CRUD token -->
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <!-- Canonical SEO -->
    <link rel="canonical" href="{{ url()->current() }}" />
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ $siteFavicon }}" />

    <!-- Include Styles -->
    @include('layouts/sections/styles')

    @if (!empty($primaryColor))
    @php
        $hexClean = ltrim($primaryColor, '#');
        if (strlen($hexClean) === 3) {
            $hexClean = $hexClean[0].$hexClean[0].$hexClean[1].$hexClean[1].$hexClean[2].$hexClean[2];
        }
        $primaryRgb = strlen($hexClean) === 6
            ? hexdec(substr($hexClean, 0, 2)) . ', ' . hexdec(substr($hexClean, 2, 2)) . ', ' . hexdec(substr($hexClean, 4, 2))
            : '11, 40, 78';
    @endphp
    <style>
        :root, [data-bs-theme="light"], [data-bs-theme="dark"] {
            --bs-primary: {{ $primaryColor }};
            --bs-primary-rgb: {{ $primaryRgb }};
            --bs-primary-border-subtle: {{ $primaryColor }}33;
            --bs-primary-bg-subtle: {{ $primaryColor }}15;
            --bs-link-color: {{ $primaryColor }};
            --bs-link-hover-color: {{ $primaryColor }};
        }
        .btn-primary {
            background-color: var(--bs-primary, {{ $primaryColor }}) !important;
            border-color: var(--bs-primary, {{ $primaryColor }}) !important;
        }
        .btn-primary:hover, .btn-primary:focus, .btn-primary:active {
            background-color: var(--astro-primary-hover, var(--bs-primary, {{ $primaryColor }})) !important;
            border-color: var(--astro-primary-hover, var(--bs-primary, {{ $primaryColor }})) !important;
        }
        .nav-pills .nav-link.active, .nav-pills .show > .nav-link {
            background-color: var(--bs-primary, {{ $primaryColor }}) !important;
            color: #ffffff !important;
        }
        .text-primary {
            --bs-text-opacity: 1;
            color: rgba(var(--bs-primary-rgb, 11, 40, 78), var(--bs-text-opacity)) !important;
        }
        .bg-primary {
            --bs-bg-opacity: 1;
            background-color: rgba(var(--bs-primary-rgb, 11, 40, 78), var(--bs-bg-opacity)) !important;
        }
        .bg-primary-subtle {
            background-color: rgba(var(--bs-primary-rgb, 11, 40, 78), 0.12) !important;
            color: var(--bs-primary, {{ $primaryColor }}) !important;
        }
        .menu-vertical .menu-item.active > .menu-link:not(.menu-toggle) {
            background-color: var(--bs-primary, {{ $primaryColor }}) !important;
            color: #ffffff !important;
        }
        .menu-vertical .menu-item.active > .menu-link:not(.menu-toggle) .menu-icon,
        .menu-vertical .menu-item.active > .menu-link:not(.menu-toggle) div {
            color: #ffffff !important;
        }
        .menu-vertical .menu-item.open > .menu-link.menu-toggle,
        .menu-vertical .menu-item.active.open > .menu-link.menu-toggle {
            background-color: transparent !important;
        }
        .menu-vertical .menu-item.open > .menu-link.menu-toggle .menu-icon,
        .menu-vertical .menu-item.open > .menu-link.menu-toggle div,
        .menu-vertical .menu-item.active.open > .menu-link.menu-toggle .menu-icon,
        .menu-vertical .menu-item.active.open > .menu-link.menu-toggle div {
            color: #1e293b !important;
        }
        [data-bs-theme="dark"] .menu-vertical .menu-item.open > .menu-link.menu-toggle .menu-icon,
        [data-bs-theme="dark"] .menu-vertical .menu-item.open > .menu-link.menu-toggle div,
        [data-bs-theme="dark"] .menu-vertical .menu-item.active.open > .menu-link.menu-toggle .menu-icon,
        [data-bs-theme="dark"] .menu-vertical .menu-item.active.open > .menu-link.menu-toggle div {
            color: #f1f5f9 !important;
        }
        .form-check-input:checked {
            background-color: var(--bs-primary, {{ $primaryColor }}) !important;
            border-color: var(--bs-primary, {{ $primaryColor }}) !important;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--bs-primary, {{ $primaryColor }}) !important;
            box-shadow: 0 0 0 0.25rem rgba(var(--bs-primary-rgb, 11, 40, 78), 0.15) !important;
        }
        [dir="rtl"] .form-select {
            background-position: left 0.85rem center !important;
            padding-left: 2.5rem !important;
            padding-right: 0.85rem !important;
        }
        [dir="ltr"] .form-select {
            background-position: right 0.85rem center !important;
            padding-right: 2.5rem !important;
            padding-left: 0.85rem !important;
        }
    </style>
    @endif

    <!-- Include Scripts for customizer, helper, analytics, config -->
    @include('layouts/sections/scriptsIncludes')
</head>

<body>
    <!-- Layout Content -->
    @yield('layoutContent')
    <!--/ Layout Content -->

    <!-- Global Notification Toasts -->
    @include('layouts/sections/notifications')

    <!-- Theme Customizer -->
    @include('layouts/sections/customizer')

    <!-- Include Scripts -->
    @include('layouts/sections/scripts')
</body>

</html>