@extends('layouts/blankLayout')

@section('title', __('Login'))

@section('page-style')
<style>
    body, html {
        height: 100%;
        margin: 0;
        padding: 0;
        background: #ffffff !important;
        overflow-x: hidden;
    }

    .auth-split-wrapper {
        min-height: 100vh;
        display: flex;
        flex-direction: row;
        width: 100vw;
        background: #ffffff;
    }

    /* Form Column */
    .auth-col-form {
        flex: 0 0 38%;
        max-width: 38%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        align-items: center;
        padding: 1.75rem 3rem;
        background: #ffffff;
        min-height: 100vh;
        box-sizing: border-box;
    }

    /* Top Actions (Language Switcher) */
    .auth-col-header {
        width: 100%;
        display: flex;
        justify-content: flex-end;
        align-items: center;
    }

    .auth-lang-btn {
        background-color: #ffffff;
        border: 1px solid #d9dee3;
        color: #566a7f;
        border-radius: 50rem;
        padding: 0.42rem 1rem;
        font-size: 0.86rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease-in-out;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        cursor: pointer;
        text-decoration: none;
    }

    .auth-lang-btn:hover, .auth-lang-btn:focus, .auth-lang-btn.show {
        background-color: #f8f9fa;
        border-color: #0b284e;
        color: #0b284e;
        box-shadow: 0 4px 8px rgba(11, 40, 78, 0.08);
    }

    .auth-lang-menu {
        border-radius: 12px;
        min-width: 160px;
        padding: 0.4rem;
        margin-top: 0.5rem !important;
        border: 1px solid rgba(0, 0, 0, 0.08) !important;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
    }

    .auth-lang-menu .dropdown-item {
        border-radius: 8px;
        font-size: 0.88rem;
        padding: 0.5rem 0.85rem;
        color: #566a7f;
        transition: all 0.15s ease;
    }

    .auth-lang-menu .dropdown-item:hover {
        background-color: #f5f5f9;
        color: #0b284e;
    }

    .auth-lang-menu .dropdown-item.active {
        background-color: rgba(11, 40, 78, 0.08) !important;
        color: #0b284e !important;
        font-weight: 600;
    }

    .auth-form-box {
        width: 100%;
        max-width: 360px;
        margin-top: auto;
        margin-bottom: auto;
        padding: 1.5rem 0;
    }

    .auth-col-footer {
        width: 100%;
        text-align: center;
        padding-top: 0.75rem;
    }

    .auth-logo-img {
        max-width: 260px;
        height: auto;
        display: block;
        margin: 0 auto 2.2rem auto;
    }

    .auth-label {
        display: block;
        font-size: 0.86rem;
        font-weight: 500;
        color: #566a7f;
        margin-bottom: 0.35rem;
        text-align: start;
    }

    .auth-input-field {
        width: 100%;
        height: 44px;
        padding: 0.5rem 0.85rem;
        font-size: 0.92rem;
        border: 1px solid #d9dee3;
        border-radius: 8px !important;
        background-color: #ffffff;
        color: #566a7f;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        text-align: start;
    }

    .auth-input-field:focus {
        border-color: var(--bs-primary);
        box-shadow: none;
        outline: none;
    }

    .auth-input-field.is-invalid {
        border-color: #ff3e1d;
    }

    .auth-password-container {
        position: relative;
        width: 100%;
    }

    [dir="ltr"] .auth-password-container .auth-input-field {
        padding-right: 2.6rem;
        padding-left: 0.85rem;
    }

    [dir="ltr"] .auth-toggle-password {
        right: 12px;
        left: auto;
    }

    [dir="rtl"] .auth-password-container .auth-input-field {
        padding-left: 2.6rem;
        padding-right: 0.85rem;
    }

    [dir="rtl"] .auth-toggle-password {
        left: 12px;
        right: auto;
    }

    .auth-toggle-password {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        border: none;
        background: transparent;
        color: #8592a3;
        cursor: pointer;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        outline: none;
    }

    .auth-toggle-password:hover {
        color: #566a7f;
    }

    .auth-remember-wrap {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: 0.55rem;
        margin-top: 0.85rem;
        margin-bottom: 1.8rem;
    }

    .auth-remember-wrap label {
        font-size: 0.85rem;
        color: #566a7f;
        cursor: pointer;
        user-select: none;
        margin: 0;
    }

    .auth-remember-wrap input[type="checkbox"] {
        width: 17px;
        height: 17px;
        border-radius: 4px;
        border: 1px solid #c4c9cf;
        cursor: pointer;
        accent-color: #0b284e;
    }

    .auth-btn-login {
        width: 100%;
        height: 44px;
        background-color: #0b284e !important;
        border: 1px solid #0b284e !important;
        color: #ffffff !important;
        font-weight: 600;
        font-size: 0.95rem;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background-color 0.2s ease, border-color 0.2s ease;
    }

    .auth-btn-login:hover {
        background-color: #071c38 !important;
        border-color: #071c38 !important;
    }

    /* Illustration Column */
    .auth-col-illustration {
        flex: 0 0 62%;
        max-width: 62%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #ffffff;
    }

    [dir="ltr"] .auth-col-illustration {
        padding: 1.5rem 2rem 1.5rem 0;
    }

    [dir="rtl"] .auth-col-illustration {
        padding: 1.5rem 0 1.5rem 2rem;
    }

    .auth-illustration-card {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 20px;
        overflow: hidden;
    }

    .auth-illustration-card img {
        width: 100%;
        max-height: 92vh;
        object-fit: contain;
        border-radius: 18px;
    }

    /* Responsive */
    @media (max-width: 991.98px) {
        .auth-col-form {
            flex: 0 0 100%;
            max-width: 100%;
            padding: 1.5rem 1.5rem;
        }

        .auth-col-illustration {
            display: none !important;
        }
    }
</style>
@endsection

@section('content')
<div class="auth-split-wrapper" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <!-- Column: Form -->
    <div class="auth-col-form">
        <!-- Top Header: Language Switcher -->
        <div class="auth-col-header">
            <div class="dropdown">
                <button class="btn auth-lang-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="{{ __('Change Language') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="2" y1="12" x2="22" y2="12"></line>
                        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                    </svg>
                    <span>{{ app()->getLocale() === 'ar' ? 'العربية' : 'English' }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end auth-lang-menu shadow-lg">
                    <li>
                        <a class="dropdown-item d-flex align-items-center justify-content-between py-2 {{ app()->getLocale() === 'ar' ? 'active' : '' }}" href="{{ route('lang.swap', 'ar') }}">
                            <span class="d-flex align-items-center gap-2">
                                <span class="fs-5">🇸🇦</span>
                                <span>العربية</span>
                            </span>
                            @if(app()->getLocale() === 'ar')
                                <i class="bx bx-check text-primary fs-5 ms-2"></i>
                            @endif
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center justify-content-between py-2 {{ app()->getLocale() === 'en' ? 'active' : '' }}" href="{{ route('lang.swap', 'en') }}">
                            <span class="d-flex align-items-center gap-2">
                                <span class="fs-5">🇺🇸</span>
                                <span>English</span>
                            </span>
                            @if(app()->getLocale() === 'en')
                                <i class="bx bx-check text-primary fs-5 ms-2"></i>
                            @endif
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Center: Form Box -->
        <div class="auth-form-box">
            <!-- Logo -->
            <div class="text-center">
                <a href="{{ url('/') }}">
                    <img src="{{ $siteLogo ?? asset('assets/img/illustrations/astro-logo.png') }}" alt="{{ $siteName ?? 'Astro Leaders Academy' }}" class="auth-logo-img" />
                </a>
            </div>

            <!-- Session Status / Alert -->
            @if (session('status'))
                <div class="alert alert-success mb-3 py-2 text-center" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Validation Errors -->
            @if ($errors->any())
                <div class="alert alert-danger mb-3 py-2" role="alert">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="formAuthentication" action="{{ route('login') }}" method="POST">
                @csrf

                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="auth-label">{{ __('Email') }}</label>
                    <input 
                        type="email" 
                        class="auth-input-field @error('email') is-invalid @enderror" 
                        id="email" 
                        name="email" 
                        value="{{ old('email') }}" 
                        placeholder="{{ __('Enter your email') }}"
                        required 
                        autofocus 
                        autocomplete="username"
                    />
                </div>

                <!-- Password -->
                <div class="mb-2">
                    <label for="password" class="auth-label">{{ __('Password') }}</label>
                    <div class="auth-password-container">
                        <input 
                            type="password" 
                            id="password" 
                            class="auth-input-field @error('password') is-invalid @enderror" 
                            name="password" 
                            placeholder="············"
                            required 
                            autocomplete="current-password" 
                        />
                        <button type="button" class="auth-toggle-password" id="togglePasswordBtn" onclick="togglePasswordVisibility()" aria-label="{{ __('Toggle password visibility') }}">
                            <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="auth-remember-wrap">
                    <input type="checkbox" id="remember-me" name="remember" value="1" {{ old('remember') ? 'checked' : '' }} />
                    <label for="remember-me">{{ __('Remember Me') }}</label>
                </div>

                <!-- Submit Button -->
                <button class="auth-btn-login" type="submit">
                    {{ __('Login') }}
                </button>
            </form>
        </div>

        <!-- Footer -->
        <div class="auth-col-footer">
            <small class="text-muted">© {{ date('Y') }} {{ $siteName ?? 'Astro Leaders Academy' }}</small>
        </div>
    </div>

    <!-- Column: Illustration -->
    <div class="auth-col-illustration">
        <div class="auth-illustration-card">
            <img src="{{ $siteLoginImage ?? asset('assets/img/illustrations/auth-bg-login.jpeg') }}" alt="{{ $siteName ?? 'Astro Leaders Academy' }}" />
        </div>
    </div>
</div>

<script>
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
        } else {
            passwordInput.type = 'password';
            eyeIcon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
        }
    }
</script>
@endsection
