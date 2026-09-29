<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover" />
    <title>{{ __('Astro Leaders') }} | {{ __('Smart HR & Employee Self-Service System') }}</title>

    <!-- PWA Primary Meta Tags -->
    <meta name="theme-color" content="#012436" />
    <meta name="mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
    <meta name="apple-mobile-web-app-title" content="AstroLeaders" />
    <meta name="application-name" content="AstroLeaders" />
    <link rel="manifest" href="{{ route('portal.manifest') }}" />
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('assets/portal/icon-192.png') }}" />
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('assets/portal/icon-512.png') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/portal/apple-touch-icon.png') }}" />
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/portal/icon-192.png') }}" />

    <!-- Google Fonts (Cairo & Inter) -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet" />

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            -webkit-tap-highlight-color: transparent;
            user-select: none;
        }

        html {
            background-color: #012436 !important;
            background: #012436 !important;
            height: 100%;
            width: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }

        body {
            background: #012436 linear-gradient(180deg, #012030 0%, #00334B 35%, #054563 70%, #012436 100%) no-repeat fixed !important;
            background-color: #012436 !important;
            min-height: 100vh;
            min-height: 100dvh;
            height: 100%;
            width: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden;
            font-family: {{ app()->getLocale() === 'ar' ? "'Cairo', sans-serif" : "'Inter', sans-serif" }};
            color: #ffffff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            position: relative;
        }

        /* Ambient Space Background & Stars */
        .space-container {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            overflow: hidden;
            z-index: 1;
        }

        /* Scattered Space Stars / Particles matching design image */
        .star {
            position: absolute;
            border-radius: 50%;
            animation: starTwinkle 3s ease-in-out infinite alternate;
        }

        .star-yellow-1 {
            width: 5px;
            height: 5px;
            background: #FDBB22;
            top: 7%;
            left: 35%;
            box-shadow: 0 0 8px #FDBB22;
            animation-duration: 2.5s;
        }

        .pill-pink {
            width: 15px;
            height: 5px;
            background: #FF3B69;
            border-radius: 4px;
            top: 7.5%;
            right: 20%;
            transform: rotate(-10deg);
            box-shadow: 0 0 6px rgba(255, 59, 105, 0.6);
            animation: floatPill 4s ease-in-out infinite alternate;
        }

        .star-orange-1 {
            width: 14px;
            height: 14px;
            background: #FFA726;
            top: 19%;
            right: 28%;
            box-shadow: 0 0 12px rgba(255, 167, 38, 0.7);
            animation-duration: 3.2s;
        }

        .star-blue-small {
            width: 4px;
            height: 4px;
            background: #C7E7FF;
            top: 11%;
            left: 15%;
            opacity: 0.6;
            animation-duration: 2.8s;
        }

        .star-white-1 {
            width: 3px;
            height: 3px;
            background: #ffffff;
            top: 16%;
            right: 17%;
            opacity: 0.7;
        }

        .star-cyan-mid {
            width: 10px;
            height: 10px;
            background: #97CDF3;
            top: 45%;
            left: 25%;
            box-shadow: 0 0 8px rgba(151, 205, 243, 0.6);
            animation-duration: 3.5s;
        }

        .star-yellow-tiny {
            width: 4px;
            height: 4px;
            background: #FDBB22;
            top: 27%;
            right: 28%;
            opacity: 0.8;
        }

        .star-small-right {
            width: 3px;
            height: 3px;
            background: #C7E7FF;
            top: 33%;
            right: 12%;
            opacity: 0.5;
        }

        .star-cyan-lower {
            width: 5px;
            height: 5px;
            background: #C7E7FF;
            bottom: 33%;
            right: 12%;
            opacity: 0.8;
            box-shadow: 0 0 6px #C7E7FF;
        }

        .star-yellow-bottom {
            width: 6px;
            height: 6px;
            background: #FDBB22;
            bottom: 18%;
            left: 13%;
            box-shadow: 0 0 8px rgba(253, 187, 34, 0.8);
            animation-duration: 2.6s;
        }

        .star-tiny-footer {
            width: 4px;
            height: 4px;
            background: #C7E7FF;
            bottom: 9%;
            right: 25%;
            opacity: 0.7;
        }

        /* Large Ambient Curved Orbit in Background */
        .ambient-orbit {
            position: absolute;
            top: 18%;
            left: -15%;
            width: 130%;
            height: 360px;
            border: 1px dashed rgba(199, 231, 255, 0.12);
            border-radius: 50%;
            transform: rotate(-16deg);
            pointer-events: none;
        }

        .ambient-orbit-bottom {
            position: absolute;
            bottom: 12%;
            left: -20%;
            width: 140%;
            height: 280px;
            border: 1px dashed rgba(199, 231, 255, 0.08);
            border-radius: 50%;
            transform: rotate(8deg);
            pointer-events: none;
        }

        @keyframes starTwinkle {
            0% { opacity: 0.3; transform: scale(0.85); }
            100% { opacity: 1; transform: scale(1.15); }
        }

        @keyframes floatPill {
            0% { transform: translateY(0) rotate(-10deg); }
            100% { transform: translateY(-4px) rotate(-8deg); }
        }

        /* Main Center Hero Wrapper */
        .splash-main {
            position: relative;
            z-index: 10;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100%;
            max-width: 400px;
            padding: 0 20px;
            margin: auto 0;
            text-align: center;
            cursor: pointer;
        }

        /* Central Glow Orb & Orbit Frame */
        .splash-hero-container {
            position: relative;
            width: 280px;
            height: 280px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        /* Glowing Ambient Orb behind Astronaut */
        .splash-orb-glow {
            position: absolute;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(16, 92, 128, 0.8) 0%, rgba(7, 56, 80, 0.45) 58%, rgba(1, 36, 54, 0) 75%);
            box-shadow: 0 0 60px rgba(16, 92, 128, 0.45);
            pointer-events: none;
        }

        /* Rotating Orbit Ring */
        .splash-orbit-ring {
            position: absolute;
            width: 260px;
            height: 260px;
            border-radius: 50%;
            border: 1.5px dashed rgba(199, 231, 255, 0.32);
            transform: rotate(-14deg);
            animation: rotateOrbit 16s linear infinite;
            pointer-events: none;
        }

        @keyframes rotateOrbit {
            from { transform: rotate(-14deg); }
            to { transform: rotate(346deg); }
        }

        .orbit-planet {
            position: absolute;
            border-radius: 50%;
        }

        .planet-yellow {
            top: 26px;
            right: 32px;
            width: 15px;
            height: 15px;
            background: #FDBB22;
            box-shadow: 0 0 12px rgba(253, 187, 34, 0.9);
        }

        .planet-blue {
            bottom: 30px;
            left: 36px;
            width: 11px;
            height: 11px;
            background: #97CDF3;
            box-shadow: 0 0 10px rgba(151, 205, 243, 0.8);
        }

        /* Astronaut Image Wrapper */
        .splash-astronaut-wrapper {
            position: relative;
            z-index: 5;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: astronautFloat 3.6s ease-in-out infinite;
        }

        .splash-astronaut-img {
            width: 185px;
            height: auto;
            max-height: 205px;
            object-fit: contain;
            filter: drop-shadow(0 12px 24px rgba(0, 0, 0, 0.45));
        }

        @keyframes astronautFloat {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-8px);
            }
        }

        /* Crisp Brand Typography matching design */
        .splash-text-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
        }

        .splash-title {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, 'Cairo', sans-serif;
            font-size: 24px;
            font-weight: 800;
            color: #ffffff;
            margin: 0;
            letter-spacing: 0.2px;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.5);
        }

        .splash-subtitle {
            font-family: 'Cairo', sans-serif;
            font-size: 13.5px;
            font-weight: 600;
            color: #88BED9;
            margin: 0;
            line-height: 1.4;
            text-shadow: 0 1px 4px rgba(0, 0, 0, 0.4);
        }

        /* Bottom Loading & Footer Section */
        .splash-footer {
            position: relative;
            z-index: 10;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            padding-bottom: max(24px, env(safe-area-inset-bottom, 24px));
            width: 100%;
            text-align: center;
            background: transparent !important;
        }

        /* 3 Animated Dots: White/Cyan, Sky Blue, Golden Yellow */
        .loading-dots {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 2px;
        }

        .loading-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            transition: all 0.3s ease;
            animation: dotWave 1.4s ease-in-out infinite;
        }

        .loading-dot.dot-white {
            background-color: #C7E7FF;
            animation-delay: 0s;
        }

        .loading-dot.dot-blue {
            background-color: #97CDF3;
            animation-delay: 0.2s;
        }

        .loading-dot.dot-yellow {
            background-color: #FDBB22;
            box-shadow: 0 0 8px rgba(253, 187, 34, 0.7);
            animation-delay: 0.4s;
        }

        @keyframes dotWave {
            0%, 80%, 100% {
                transform: scale(0.75);
                opacity: 0.4;
            }
            40% {
                transform: scale(1.25);
                opacity: 1;
            }
        }

        /* Footer Text */
        .footer-version {
            font-size: 13px;
            font-weight: 500;
            color: rgba(199, 231, 255, 0.75);
            letter-spacing: 0.2px;
        }

        .footer-copyright {
            font-size: 11px;
            font-weight: 400;
            color: rgba(199, 231, 255, 0.55);
            margin-top: -6px;
        }

        /* Tap to continue indicator */
        .tap-hint {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.4);
            margin-top: -2px;
            transition: opacity 0.3s ease;
        }

        /* Smooth Screen Fade Out on Redirect */
        body.fade-out {
            opacity: 0;
            transition: opacity 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>
</head>

<body onclick="goToApp()">
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
            <h1 class="splash-title">Astro Leaders </h1>
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
                    .then(reg => console.log('Portal SW active:', reg.scope))
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
