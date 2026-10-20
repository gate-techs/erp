<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover" />
    <title>{{ __('Privacy Policy') }} | {{ __('ALA ORBIT') }}</title>

    <meta name="theme-color" content="#00334B" />
    <meta name="mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />

    <!-- Boxicons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" />

    <!-- Bootstrap 5 CSS -->
    @if(app()->getLocale() === 'ar')
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet" />
    @else
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    @endif

    <style>
        :root {
            --primary-navy: #00334B;
            --primary-dark: #002233;
            --accent-yellow: #FEAE2C;
            --surface-bg: #F8FAFC;
            --card-border: rgba(0, 51, 75, 0.08);
            --text-dark: #0F172A;
            --text-muted: #64748B;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            -webkit-tap-highlight-color: transparent;
            font-family: {{ app()->getLocale() === 'ar' ? "'Tajawal', sans-serif" : "'Inter', 'Tajawal', sans-serif" }};
        }

        body {
            background-color: var(--surface-bg);
            color: var(--text-dark);
            min-height: 100vh;
            padding-bottom: 40px;
        }

        .privacy-header-bar {
            background: linear-gradient(135deg, #002233 0%, #00334B 100%);
            color: #ffffff;
            padding: calc(env(safe-area-inset-top, 16px) + 12px) 16px 24px 16px;
            border-bottom-left-radius: 24px;
            border-bottom-right-radius: 24px;
            box-shadow: 0 10px 25px rgba(0, 34, 51, 0.15);
        }

        .privacy-nav-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .privacy-back-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.12);
            color: #ffffff;
            text-decoration: none;
            backdrop-filter: blur(8px);
            font-size: 20px;
            transition: all 0.2s ease;
        }

        .privacy-back-btn:active {
            transform: scale(0.95);
            background: rgba(255, 255, 255, 0.2);
        }

        .privacy-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 20px;
            background: rgba(254, 174, 44, 0.18);
            color: var(--accent-yellow);
            font-size: 11.5px;
            font-weight: 700;
            border: 1px solid rgba(254, 174, 44, 0.35);
        }

        .privacy-hero-title {
            font-size: 22px;
            font-weight: 800;
            margin-bottom: 6px;
        }

        .privacy-hero-sub {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.75);
            line-height: 1.5;
        }

        .privacy-content-wrapper {
            max-width: 680px;
            margin: -12px auto 0 auto;
            padding: 0 16px;
        }

        .privacy-card {
            background: #ffffff;
            border-radius: 18px;
            padding: 20px;
            border: 1px solid var(--card-border);
            box-shadow: 0 4px 18px rgba(0, 51, 75, 0.05);
            margin-bottom: 14px;
        }

        .privacy-card-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 16px;
            font-weight: 800;
            color: var(--primary-navy);
            margin-bottom: 12px;
        }

        .privacy-card-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: rgba(0, 51, 75, 0.06);
            color: var(--primary-navy);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .privacy-card-text {
            font-size: 13.5px;
            color: #475569;
            line-height: 1.7;
        }

        .privacy-list {
            padding-inline-start: 18px;
            margin-top: 8px;
            font-size: 13px;
            color: #475569;
            line-height: 1.8;
        }

        .privacy-list li {
            margin-bottom: 6px;
        }

        .privacy-footer-note {
            text-align: center;
            padding: 16px;
            font-size: 12px;
            color: var(--text-muted);
        }
    </style>
</head>

<body>
    <!-- Top Header Banner -->
    <header class="privacy-header-bar">
        <div class="privacy-content-wrapper" style="margin: 0 auto; padding: 0;">
            <div class="privacy-nav-row">
                <a href="{{ url()->previous() ?: route('portal.login') }}" class="privacy-back-btn" title="{{ __('Back') }}">
                    <i class="bx {{ app()->getLocale() === 'ar' ? 'bx-chevron-right' : 'bx-chevron-left' }}"></i>
                </a>
                <span class="privacy-badge">
                    <i class="bx bxs-shield-check"></i>
                    {{ __('Official Privacy Policy') }}
                </span>
            </div>
            <h1 class="privacy-hero-title">{{ __('ALA ORBIT • Privacy Policy') }}</h1>
            <p class="privacy-sub">
                {{ __('Smart HR & Employee Self-Service Mobile Application') }} • {{ __('State of Kuwait') }}
            </p>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="privacy-content-wrapper">
        <!-- 1. Identity & Scope -->
        <article class="privacy-card">
            <h2 class="privacy-card-title">
                <span class="privacy-card-icon"><i class="bx bx-buildings"></i></span>
                {{ __('1. Application Identity & Scope') }}
            </h2>
            <p class="privacy-card-text">
                {{ __('ALA ORBIT is the official employee self-service and smart human resources management mobile application for Astro Leaders Academy in the State of Kuwait. This policy explains how we collect, handle, and protect personal and employment information.') }}
            </p>
        </article>

        <!-- 2. Data We Collect -->
        <article class="privacy-card">
            <h2 class="privacy-card-title">
                <span class="privacy-card-icon"><i class="bx bx-data"></i></span>
                {{ __('2. Information We Collect') }}
            </h2>
            <p class="privacy-card-text">
                {{ __('To provide reliable HR services and verify attendance, the application collects only necessary data:') }}
            </p>
            <ul class="privacy-list">
                <li><strong>{{ __('Identity & Employment Data:') }}</strong> {{ __('Name, official email address, employee ID number, branch assignment, job title, and employment status.') }}</li>
                <li><strong>{{ __('Precise Geolocation (GPS):') }}</strong> {{ __('Collected strictly during the physical check-in and check-out punch action to verify presence within designated campus geofences. Location is never tracked continuously or in the background.') }}</li>
                <li><strong>{{ __('Camera & Photo Library:') }}</strong> {{ __('Requested only when the employee chooses to upload profile photos, sick leave medical reports, or official request documents.') }}</li>
                <li><strong>{{ __('Push Notifications Device Token:') }}</strong> {{ __('Used solely to deliver shift reminders, leave request approvals, and administrative alerts.') }}</li>
            </ul>
        </article>

        <!-- 3. Purpose of Use -->
        <article class="privacy-card">
            <h2 class="privacy-card-title">
                <span class="privacy-card-icon"><i class="bx bx-target-lock"></i></span>
                {{ __('3. Purpose of Processing') }}
            </h2>
            <p class="privacy-card-text">
                {{ __('The data collected is processed strictly for human resources management, payroll computation, attendance verification, and internal communications between the employee and the nursery administration.') }}
            </p>
        </article>

        <!-- 4. Data Security -->
        <article class="privacy-card">
            <h2 class="privacy-card-title">
                <span class="privacy-card-icon"><i class="bx bx-lock-alt"></i></span>
                {{ __('4. Security & Data Protection') }}
            </h2>
            <p class="privacy-card-text">
                {{ __('All communications between ALA ORBIT and our servers are encrypted using modern Transport Layer Security (TLS 1.3 / HTTPS). Data is stored in secure database environments with strict access control. We never sell, rent, or share personal information with third-party advertisers.') }}
            </p>
        </article>

        <!-- 5. User Rights & Account Deletion (Apple Guideline 5.1.1) -->
        <article class="privacy-card">
            <h2 class="privacy-card-title">
                <span class="privacy-card-icon"><i class="bx bx-user-x"></i></span>
                {{ __('5. Account Deletion & Data Rights') }}
            </h2>
            <p class="privacy-card-text">
                {{ __('Employees have the right to review their personal information, update contact details, or request account deactivation and data purging upon termination of employment. A dedicated "Request Account Deletion" action is available directly within the Security Settings section of the app, or by contacting HR administration directly.') }}
            </p>
        </article>

        <!-- 6. Contact Information -->
        <article class="privacy-card">
            <h2 class="privacy-card-title">
                <span class="privacy-card-icon"><i class="bx bx-envelope"></i></span>
                {{ __('6. Contact HR Administration') }}
            </h2>
            <p class="privacy-card-text">
                {{ __('For privacy inquiries, data requests, or support, please reach out to:') }}<br />
                <strong>{{ __('Organization:') }}</strong> Astro Leaders Academy • State of Kuwait<br />
                <strong>{{ __('Email:') }}</strong> hr@astroleaderskw.com • info@astroleaderskw.com<br />
                <strong>{{ __('Version:') }}</strong> ALA ORBIT v1.0.0
            </p>
        </article>

        <div class="privacy-footer-note">
            © {{ date('Y') }} ALA ORBIT • {{ __('Astro Leaders Academy. All rights reserved.') }}
        </div>
    </main>
</body>

</html>
