<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover" />
    <title>{{ __('Login | ALA ORBIT - Employee Portal') }}</title>

    <!-- PWA Primary Meta Tags -->
    <meta name="theme-color" content="#00334B" />
    <meta name="mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="default" />
    <meta name="apple-mobile-web-app-title" content="ALA ORBIT" />
    <meta name="application-name" content="ALA ORBIT" />
    <link rel="manifest" href="{{ route('portal.manifest') }}" />
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('assets/portal/icon-192.png') }}" />
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('assets/portal/icon-512.png') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/portal/apple-touch-icon.png') }}" />
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/portal/icon-192.png') }}" />

    <!-- Google Fonts (Almarai) -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;700;800&display=swap" rel="stylesheet" />

    <!-- Boxicons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" />

    <!-- Unified Portal CSS -->
    <link rel="stylesheet" href="{{ asset('assets/portal/css/portal.css') }}?v={{ file_exists(public_path('assets/portal/css/portal.css')) ? filemtime(public_path('assets/portal/css/portal.css')) : '2.0.1' }}" />
</head>

<body class="portal-login-body">
    <!-- Top Mobile Status Notch Bar preview with language switch -->
    <div class="mobile-top-bar">
        <span>{{ __('ALA ORBIT') }}</span>
        <a href="{{ route('lang.swap', app()->getLocale() === 'ar' ? 'en' : 'ar') }}" class="portal-login-lang-btn" title="{{ __('Change Language') }}" onclick="savePortalLocaleAndRedirect(event, '{{ app()->getLocale() === 'ar' ? 'en' : 'ar' }}', this.href)">
            <i class="bx bx-globe"></i>
            <span>{{ app()->getLocale() === 'ar' ? 'English' : 'عربي' }}</span>
        </a>
    </div>

    <!-- Main Login Card Content -->
    <div class="login-wrapper">
        <!-- Logo with Astronaut & Rocket Badge -->
        <div class="logo-box-container">
            <div class="logo-box">
                <img src="{{ asset('assets/portal/astronaut.png') }}" alt="Astro Leaders" />
            </div>
            <div class="rocket-badge">
                <i class="bx bxs-rocket"></i>
            </div>
        </div>

        <!-- App Title & Subtitle -->
        <h1 class="app-title">{{ __('ALA ORBIT') }}</h1>
        <div class="app-subtitle-badge">
             <i class="bx bxs-badge-check"></i>
            <span>{{ __('Nursery Employee Portal • Kuwait') }}</span>
        </div>

        <!-- Administrative Notice (تنويه إداري معتمد - ليس كارد) -->
        <div class="admin-notice-row">
            <div class="notice-icon">i</div>
            <div class="notice-text-col">
                <div class="notice-text-title">{{ __('Official Administrative Notice') }}</div>
                <div class="notice-text-desc">
                    {{ __('Login credentials (email & password) are issued by HR Administration') }}
                </div>
            </div>
        </div>

        @if(session('error'))
            <div class="alert-box alert-danger">
                <i class="bx bx-error-circle fs-5"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif


        <!-- Login Form Card -->
        <div class="login-card">
            <form action="{{ route('portal.login.submit', [], false) }}" method="POST">
                @csrf

                <!-- Email Input -->
                <div class="form-group">
                    <div class="form-label-row">
                        <span class="form-label-title">{{ __('Email Address') }}</span>
                        <span class="form-label-hint">{{ __('e.g.: user@example.com') }}</span>
                    </div>

                    <div class="custom-input-box">
                        <i class="bx bx-envelope side-icon"></i>
                        <input type="email" 
                               name="email" 
                               id="emailInput" 
                               value="{{ old('email') }}" 
                               required 
                               autocomplete="email" 
                               placeholder="name@astroleaders.com" 
                               dir="ltr" 
                               style="text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};" />
                    </div>
                </div>

                <!-- Password Input -->
                <div class="form-group">
                    <div class="form-label-row">
                        <span class="form-label-title">{{ __('Password') }}</span>
                    </div>

                    <div class="custom-input-box">
                        <i class="bx bx-lock-alt side-icon"></i>
                        <input type="password" 
                               name="password" 
                               id="passwordInput" 
                               required 
                               autocomplete="current-password" 
                               placeholder="••••••••••••" 
                               dir="ltr" 
                               style="text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};" />
                        <i class="bx bx-show toggle-pass" id="btnTogglePass" title="{{ __('Show / Hide Password') }}"></i>
                    </div>
                </div>

                <!-- Remember Device & Forgot Password -->
                <div class="form-extra-row">
                    <label class="remember-checkbox-label">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }} />
                        <span>{{ __('Remember this device') }}</span>
                    </label>

                    <a href="javascript:void(0)" class="forgot-pass-link" onclick="alert('{{ __('Please contact the HR administrator to reset your password') }}')">
                        {{ __('Forgot password?') }}
                    </a>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-submit-login">
                    <span>{{ __('Sign In') }}</span>
                    <i class="bx bx-log-in fs-4"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Footer Help & Version -->
    <div class="login-footer">
        <div class="footer-help-text">{{ __("Didn't receive your account details?") }}</div>
        <a href="mailto:hr@astroleaders.com" class="footer-hr-link">
            {{ __('Contact Nursery HR Administration') }}
        </a>
        <div class="footer-privacy-box mb-2">
            <a href="{{ route('portal.privacy') }}" class="footer-hr-link" style="font-size: 11.5px; opacity: 0.85;">
                <i class="bx bx-shield-quarter me-1"></i>{{ __('Privacy Policy & Terms of Service') }}
            </a>
        </div>
        <div class="footer-version-tag">
            {{ __('Internal Release V2.4 • State of Kuwait') }}
        </div>
    </div>

    <!-- Password visibility toggle script -->
    <script>
        const btnToggle = document.getElementById('btnTogglePass');
        const passInput = document.getElementById('passwordInput');

        if (btnToggle && passInput) {
            btnToggle.addEventListener('click', () => {
                const isPassword = passInput.getAttribute('type') === 'password';
                passInput.setAttribute('type', isPassword ? 'text' : 'password');
                btnToggle.classList.toggle('bx-show', !isPassword);
                btnToggle.classList.toggle('bx-hide', isPassword);
            });
        }

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

    <!-- PWA Install Prompt & Service Worker Component -->
    @include('employeeportal::components.pwa-installer')
</body>

</html>
