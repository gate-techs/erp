@extends('layouts/blankLayout')

@section('title', 'تسجيل الدخول - ' . config('variables.templateName'))

@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
@endsection

@section('content')
<div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner">
            <!-- Login Card -->
            <div class="card px-sm-6 px-0 shadow-sm border-0">
                <div class="card-body">
                    <!-- Logo -->
                    <div class="app-brand justify-content-center mb-6">
                        <a href="{{ url('/') }}" class="app-brand-link gap-2">
                            <span class="app-brand-logo demo">@include('_partials.macros')</span>
                            <span class="app-brand-text demo text-heading fw-bold">{{ config('variables.templateName') }}</span>
                        </a>
                    </div>
                    <!-- /Logo -->

                    <h4 class="mb-1 text-center fw-bold">مرحباً بك مجدداً 👋</h4>
                    <p class="mb-6 text-center text-muted">يرجى تسجيل الدخول للوصول إلى لوحة التحكم</p>

                    <!-- Session Status / Alert -->
                    @if (session('status'))
                        <div class="alert alert-success mb-4 text-center" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <!-- Validation Errors Alert -->
                    @if ($errors->any())
                        <div class="alert alert-danger mb-4" role="alert">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="formAuthentication" class="mb-6" action="{{ route('login') }}" method="POST">
                        @csrf

                        <!-- Email -->
                        <div class="mb-6">
                            <label for="email" class="form-label">البريد الإلكتروني</label>
                            <input 
                                type="email" 
                                class="form-control @error('email') is-invalid @enderror" 
                                id="email" 
                                name="email" 
                                value="{{ old('email') }}" 
                                placeholder="name@example.com" 
                                required 
                                autofocus 
                                autocomplete="username"
                            />
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-6 form-password-toggle">
                            <div class="d-flex justify-content-between">
                                <label class="form-label" for="password">كلمة المرور</label>
                            </div>
                            <div class="input-group input-group-merge">
                                <input 
                                    type="password" 
                                    id="password" 
                                    class="form-control @error('password') is-invalid @enderror" 
                                    name="password" 
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" 
                                    required 
                                    autocomplete="current-password" 
                                />
                                <span class="input-group-text cursor-pointer"><i class="icon-base bx bx-hide"></i></span>
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Remember Me -->
                        <div class="mb-8">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="form-check mb-0">
                                    <input class="form-check-input" type="checkbox" id="remember-me" name="remember" value="1" {{ old('remember') ? 'checked' : '' }} />
                                    <label class="form-check-label" for="remember-me">تذكرني على هذا الجهاز</label>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="mb-6">
                            <button class="btn btn-primary d-grid w-100 fw-bold py-2" type="submit">
                                <i class="icon-base bx bx-log-in me-1"></i> تسجيل الدخول
                            </button>
                        </div>
                    </form>

                </div>
            </div>
            <!-- /Login Card -->
        </div>
    </div>
</div>
@endsection
