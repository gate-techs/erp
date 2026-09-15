@extends('layouts/blankLayout')

@section('title', 'Login - Astro Leaders Academy')

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

    /* Left Column: Login Form */
    .auth-col-form {
        flex: 0 0 35%;
        max-width: 35%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 2.5rem 3rem;
        background: #ffffff;
    }

    .auth-form-box {
        width: 100%;
        max-width: 350px;
    }

    .auth-logo-img {
        max-width: 270px;
        height: auto;
        display: block;
        margin: 0 auto 2.5rem auto;
    }

    .auth-label {
        display: block;
        font-size: 0.86rem;
        font-weight: 500;
        color: #566a7f;
        margin-bottom: 0.35rem;
        text-align: right;
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
    }

    .auth-input-field:focus {
        border-color: #0b284e;
        box-shadow: 0 0 0 0.2rem rgba(11, 40, 78, 0.12);
        outline: none;
    }

    .auth-input-field.is-invalid {
        border-color: #ff3e1d;
    }

    .auth-password-container {
        position: relative;
        width: 100%;
    }

    .auth-password-container .auth-input-field {
        padding-left: 2.6rem;
    }

    .auth-toggle-password {
        position: absolute;
        left: 12px;
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
        justify-content: flex-end;
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

    /* Right Column: Illustration */
    .auth-col-illustration {
        flex: 0 0 65%;
        max-width: 65%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1.5rem 2rem 1.5rem 0;
        background: #ffffff;
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
            padding: 3rem 1.5rem;
        }

        .auth-col-illustration {
            display: none !important;
        }
    }
</style>
@endsection

@section('content')
<div class="auth-split-wrapper" dir="ltr">
    <!-- Left Column: Form -->
    <div class="auth-col-form">
        <div class="auth-form-box">
            <!-- Logo -->
            <div class="text-center">
                <a href="{{ url('/') }}">
                    <img src="{{ asset('assets/img/illustrations/astro-logo.png') }}" alt="Astro Leaders Academy" class="auth-logo-img" />
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
                    <label for="email" class="auth-label">Email</label>
                    <input 
                        type="email" 
                        class="auth-input-field @error('email') is-invalid @enderror" 
                        id="email" 
                        name="email" 
                        value="{{ old('email') }}" 
                        required 
                        autofocus 
                        autocomplete="username"
                    />
                </div>

                <!-- Password -->
                <div class="mb-2">
                    <label for="password" class="auth-label">Password</label>
                    <div class="auth-password-container">
                        <input 
                            type="password" 
                            id="password" 
                            class="auth-input-field @error('password') is-invalid @enderror" 
                            name="password" 
                            required 
                            autocomplete="current-password" 
                        />
                        <button type="button" class="auth-toggle-password" id="togglePasswordBtn" onclick="togglePasswordVisibility()" aria-label="Toggle password visibility">
                            <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="auth-remember-wrap">
                    <label for="remember-me">Remember me</label>
                    <input type="checkbox" id="remember-me" name="remember" value="1" {{ old('remember') ? 'checked' : '' }} />
                </div>

                <!-- Submit Button -->
                <button class="auth-btn-login" type="submit">
                    Login
                </button>
            </form>
        </div>
    </div>

    <!-- Right Column: Illustration -->
    <div class="auth-col-illustration">
        <div class="auth-illustration-card">
            <img src="{{ asset('assets/img/illustrations/auth-bg-login.jpeg') }}" alt="Astro Leaders Academy" />
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
