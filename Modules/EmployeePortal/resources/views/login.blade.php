<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover" />
    <title>تسجيل الدخول | أسترو ليدرز - بوابة الموظفين</title>

    <!-- PWA Primary Meta Tags -->
    <meta name="theme-color" content="#00334B" />
    <meta name="mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="default" />
    <meta name="apple-mobile-web-app-title" content="أسترو ليدرز" />
    <meta name="application-name" content="أسترو ليدرز" />
    <link rel="manifest" href="{{ route('portal.manifest') }}" />
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('assets/portal/icon-192.png') }}" />
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('assets/portal/icon-512.png') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/portal/apple-touch-icon.png') }}" />
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/portal/icon-192.png') }}" />

    <!-- Google Fonts (Cairo) -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet" />

    <!-- Boxicons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" />

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            font-family: 'Cairo', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: linear-gradient(180deg, #F0F6FA 0%, #FFFFFF 35%, #F4F8FA 100%);
            color: #1E293B;
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px calc(20px + env(safe-area-inset-bottom, 0px)) 20px;
            user-select: none;
        }

        /* Top Mobile Status Notch Bar preview */
        .mobile-top-bar {
            width: 100%;
            max-width: 390px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 6px 8px 12px 8px;
            font-size: 13px;
            font-weight: 700;
            color: #00334B;
        }

        .mobile-top-bar .icons-side {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 14px;
        }

        /* Main Container */
        .login-wrapper {
            width: 100%;
            max-width: 390px;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: auto 0;
        }

        /* Header Logo & Badges */
        .logo-box-container {
            position: relative;
            margin-bottom: 12px;
        }

        .logo-box {
            width: 90px;
            height: 90px;
            border-radius: 26px;
            background: #ffffff;
            box-shadow: 0 10px 30px rgba(0, 51, 75, 0.08);
            border: 1px solid rgba(0, 51, 75, 0.06);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            padding: 8px;
        }

        .logo-box img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        /* Rocket Badge on Logo */
        .rocket-badge {
            position: absolute;
            bottom: -4px;
            right: -4px;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #00334B;
            border: 2px solid #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #FDBB22;
            font-size: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }

        /* Title & Subtitle */
        .app-title {
            font-size: 26px;
            font-weight: 800;
            color: #00334B;
            letter-spacing: -0.3px;
            margin-bottom: 2px;
        }

        .app-subtitle-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #5A6A85;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 16px;
        }

        .app-subtitle-badge i {
            color: #B8860B;
            font-size: 17px;
        }

        /* Administrative Notice (تنويه إداري معتمد - ليس كارد) */
        .admin-notice-row {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            text-align: right;
            background: transparent;
            border: none;
            box-shadow: none;
            padding: 0 4px;
        }

        .admin-notice-row .notice-icon {
            width: 36px;
            height: 36px;
            border-radius: 12px;
            background: #D9CEB5;
            color: #6D4C13;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-family: serif;
            font-size: 18px;
            flex-shrink: 0;
        }

        .admin-notice-row .notice-text-col {
            display: flex;
            flex-direction: column;
        }

        .admin-notice-row .notice-text-title {
            font-size: 14px;
            font-weight: 700;
            color: #004B6B;
            margin-bottom: 2px;
        }

        .admin-notice-row .notice-text-desc {
            font-size: 11px;
            font-weight: 500;
            color: #64748B;
            line-height: 1.4;
        }

        /* Form Card Container */
        .login-card {
            width: 100%;
            background: #ffffff;
            border-radius: 22px;
            padding: 22px 20px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(0, 0, 0, 0.04);
            margin-bottom: 16px;
        }

        /* Input Groups */
        .form-group {
            margin-bottom: 18px;
            text-align: right;
        }

        .form-label-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 6px;
        }

        .form-label-title {
            font-size: 13px;
            font-weight: 700;
            color: #00334B;
            white-space: nowrap;
        }

        .form-label-hint {
            font-size: 11px;
            color: #94A3B8;
            white-space: nowrap;
        }

        .custom-input-box {
            position: relative;
            background: #F8FAFC;
            border: 1.5px solid #E2E8F0;
            border-radius: 14px;
            height: 52px;
            display: flex;
            align-items: center;
            padding: 0 16px;
            transition: all 0.2s ease;
        }

        .custom-input-box:focus-within {
            border-color: #004B6B;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(0, 75, 107, 0.1);
        }

        .custom-input-box input {
            width: 100%;
            border: none;
            outline: none;
            background: transparent;
            font-family: 'Cairo', sans-serif;
            font-size: 14px;
            font-weight: 600;
            color: #1E293B;
            text-align: right;
            padding: 0 8px;
        }

        .custom-input-box input::placeholder {
            color: #CBD5E1;
            font-weight: 400;
            letter-spacing: 0.5px;
        }

        .custom-input-box .side-icon {
            font-size: 20px;
            color: #004B6B;
            flex-shrink: 0;
        }

        .custom-input-box .toggle-pass {
            font-size: 20px;
            color: #94A3B8;
            cursor: pointer;
            flex-shrink: 0;
            transition: color 0.2s ease;
        }

        .custom-input-box .toggle-pass:hover {
            color: #004B6B;
        }

        /* Checkbox & Forgot Password Row */
        .form-extra-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 22px;
            font-size: 12px;
        }

        .remember-checkbox-label {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #475569;
            font-weight: 600;
            cursor: pointer;
            white-space: nowrap;
        }

        .remember-checkbox-label input[type="checkbox"] {
            width: 18px;
            height: 18px;
            border-radius: 5px;
            accent-color: #FDBB22;
            cursor: pointer;
        }

        .forgot-pass-link {
            color: #B8860B;
            font-weight: 700;
            text-decoration: none;
            transition: color 0.2s ease;
            white-space: nowrap;
        }

        .forgot-pass-link:hover {
            color: #8C6207;
            text-decoration: underline;
        }

        /* Primary Yellow Submit Button */
        .btn-submit-login {
            width: 100%;
            height: 52px;
            background: linear-gradient(180deg, #FFC107 0%, #F59E0B 100%);
            border: none;
            border-radius: 14px;
            color: #00334B;
            font-family: 'Cairo', sans-serif;
            font-size: 16px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 6px 20px rgba(245, 158, 11, 0.35);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-submit-login:hover {
            filter: brightness(1.05);
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(245, 158, 11, 0.45);
        }

        .btn-submit-login:active {
            transform: scale(0.98);
        }

        /* Alert error / success */
        .alert-box {
            width: 100%;
            padding: 10px 14px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 14px;
            text-align: right;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .alert-box.alert-danger {
            background: #FEE2E2;
            color: #DC2626;
            border: 1px solid #FCA5A5;
        }

        .alert-box.alert-success {
            background: #DCFCE7;
            color: #16A34A;
            border: 1px solid #86EFAC;
        }

        /* Footer */
        .login-footer {
            width: 100%;
            max-width: 390px;
            text-align: center;
            margin-top: 8px;
        }

        .footer-help-text {
            font-size: 12px;
            color: #64748B;
            font-weight: 500;
            margin-bottom: 4px;
        }

        .footer-hr-link {
            font-size: 13px;
            font-weight: 700;
            color: #004B6B;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 8px;
        }

        .footer-hr-link:hover {
            text-decoration: underline;
        }

        .footer-version-tag {
            font-size: 11px;
            font-weight: 500;
            color: #94A3B8;
        }
    </style>
</head>

<body>


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
        <h1 class="app-title">أسترو ليدرز</h1>
        <div class="app-subtitle-badge">
             <i class="bx bxs-badge-check"></i>
            <span>بوابة موظفي الحضانة • الكويت</span>
           
        </div>

        <!-- Administrative Notice (تنويه إداري معتمد - ليس كارد) -->
        <div class="admin-notice-row">
            <div class="notice-icon">i</div>
            <div class="notice-text-col">
                <div class="notice-text-title">تنويه إداري معتمد</div>
                <div class="notice-text-desc">
                    يتم تسليم بيانات الدخول (البريد الإلكتروني وكلمة المرور)
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

                <!-- Email Input (بدل الرقم الوظيفي هيبقي الemail كما طلب المستخدم) -->
                <div class="form-group">
                    <div class="form-label-row">
                        <span class="form-label-title">البريد الإلكتروني</span>
                        <span class="form-label-hint">مثال: user@example.com</span>
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
                               style="text-align: right;" />
                    </div>
                </div>

                <!-- Password Input -->
                <div class="form-group">
                    <div class="form-label-row">
                        <span class="form-label-title">كلمة المرور</span>
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
                               style="text-align: right;" />
                        <i class="bx bx-show toggle-pass" id="btnTogglePass" title="إظهار / إخفاء كلمة المرور"></i>
                    </div>
                </div>

                <!-- Remember Device & Forgot Password -->
                <div class="form-extra-row">
                    <label class="remember-checkbox-label">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }} />
                        <span>تذكّر هذا الجهاز</span>
                    </label>

                    <a href="javascript:void(0)" class="forgot-pass-link" onclick="alert('يرجى التواصل مع مسؤول الموارد البشرية لإعادة تعيين كلمة المرور')">
                        نسيت كلمة المرور؟
                    </a>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-submit-login">
                    <span>تسجيل الدخول</span>
                    <i class="bx bx-log-in fs-4"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Footer Help & Version -->
    <div class="login-footer">
        <div class="footer-help-text">لم تستلم بيانات حسابك؟</div>
        <a href="mailto:hr@astroleaders.com" class="footer-hr-link">
            تواصل مع إدارة الموارد البشرية بالحضانة
        </a>
        <div class="footer-version-tag">
            الإصدار الداخلي V2.4 • دولة الكويت
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
    </script>

    <!-- PWA Install Prompt & Service Worker Component -->
    @include('employeeportal::components.pwa-installer')
</body>

</html>
