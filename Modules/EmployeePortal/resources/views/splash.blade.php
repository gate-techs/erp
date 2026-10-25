<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover" />
    <title>{{ __('ALA ORBIT') }} | {{ __('Smart HR & Employee Self-Service System') }}</title>

    <!-- PWA Primary Meta Tags (Android & Chrome) -->
    <meta name="theme-color" content="#012436" />
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
    <meta name="msapplication-TileColor" content="#012436" />
    <meta name="msapplication-TileImage" content="{{ asset('assets/portal/icon-512.png') }}" />
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('assets/portal/icon-192.png') }}" />
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('assets/portal/icon-512.png') }}" />
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/portal/icon-192.png') }}" />

    <!-- Google Fonts (Almarai) -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;700;800&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('assets/portal/css/portal.css') }}?v={{ file_exists(public_path('assets/portal/css/portal.css')) ? filemtime(public_path('assets/portal/css/portal.css')) : time() }}" />
</head>

<body class="splash-body" onclick="goToApp()">
    <!-- Ambient Space Background with Particles & Curved Orbits -->
    <div class="space-container">
        <!-- Dashed Orbit Lines in Background -->
        <div class="ambient-orbit"></div>
        <div class="ambient-orbit-bottom"></div>

        <!-- Scattered Stars -->
        <div class="star star-yellow-1"></div>
        <div class="pill-pink"></div>
        <div class="star star-orange-1"></div>
        <div class="star star-blue-small"></div>
        <div class="star star-white-1"></div>
        <div class="star star-cyan-mid"></div>
        <div class="star star-yellow-tiny"></div>
        <div class="star star-small-right"></div>
        <div class="star star-cyan-lower"></div>
        <div class="star star-yellow-bottom"></div>
        <div class="star star-tiny-footer"></div>
    </div>

    <!-- Center Hero Element with Rotating Orbit & Typography -->
    <main class="splash-main">
        <div class="splash-hero-container">
            <!-- Glowing Orb -->
            <div class="splash-orb-glow"></div>

            <!-- Rotating Orbit with Planet Dots -->
            <div class="splash-orbit-ring">
                <span class="orbit-planet planet-yellow"></span>
                <span class="orbit-planet planet-blue"></span>
            </div>

            <!-- Crisp Sharp Astronaut -->
            <div class="splash-astronaut-wrapper">
                <img src="{{ asset('assets/portal/astronaut.png') }}" alt="Astro Leaders Astronaut" class="splash-astronaut-img" />
            </div>
        </div>

        <!-- Brand Typography -->
        <div class="splash-text-container">
            <h1 class="splash-title">ALA ORBIT</h1>
            <p class="splash-subtitle">{{ __('Smart HR & Employee Self-Service System') }}</p>
        </div>
    </main>

    <!-- Bottom Loading Dots & Footer -->
    <footer class="splash-footer">
        <!-- 3 Loading Dots -->
        <div class="loading-dots">
            <span class="loading-dot dot-white"></span>
            <span class="loading-dot dot-blue"></span>
            <span class="loading-dot dot-yellow"></span>
        </div>

       
        <!-- Copyright -->
        <div class="footer-copyright">
            @GTABS
        </div>

        <div class="tap-hint">
            {{ __('Tap anywhere to enter') }}
        </div>
    </footer>

    <!-- Auto-transition & Service Worker Registration Script -->
    <script>
        // Service Worker registration
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('{{ route("portal.sw") }}')
                    .then(reg => {
                        reg.update();
                        console.log('Portal SW active:', reg.scope);
                    })
                    .catch(err => console.warn('SW err:', err));
            });
        }

        // Navigate after 3 seconds
        let navigated = false;
        function goToApp() {
            if (navigated) return;
            navigated = true;
            document.body.classList.add('fade-out');
            setTimeout(() => {
                @auth
                    window.location.href = "{{ route('portal.dashboard', [], false) }}";
                @else
                    window.location.href = "{{ route('portal.login', [], false) }}";
                @endauth
            }, 350);
        }

        // Auto timer to enter the app
        setTimeout(goToApp, 3200);
    </script>
</body>

</html>
