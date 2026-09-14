@php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
@endphp

<!--  Brand demo (display only for navbar-full and hide on below xl) -->
@if(isset($navbarFull))
<div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-4">
    <a href="{{url('/')}}" class="app-brand-link gap-2">
        <span class="app-brand-logo demo">@include('_partials.macros')</span>
        <span class="app-brand-text demo menu-text fw-bold text-heading">{{config('variables.templateName')}}</span>
    </a>
</div>
@endif

<!-- ! Not required for layout-without-menu -->
@if(!isset($navbarHideToggle))
<div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0 {{ isset($contentNavbar) ?' d-xl-none ' : '' }}">
    <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
        <i class="icon-base bx bx-menu icon-md"></i>
    </a>
</div>
@endif

<div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
    <!-- Search -->
    <div class="navbar-nav align-items-center">
        <div class="nav-item d-flex align-items-center">
            <i class="icon-base bx bx-search icon-md"></i>
            <input type="text" class="form-control border-0 shadow-none ps-1 ps-sm-2" placeholder="{{ __('Search...') }}" aria-label="{{ __('Search...') }}">
        </div>
    </div>
    <!-- /Search -->
    <ul class="navbar-nav flex-row align-items-center ms-auto gap-2">
        <!-- Language Switcher (文A) -->
        <li class="nav-item dropdown-language dropdown">
            <a class="nav-link dropdown-toggle hide-arrow btn-nav-circle" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="false" title="تغيير اللغة">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="nav-icon-svg">
                    <path d="M4 5h7M9 3v2c0 3.5-1.5 6.5-4 8.5M7 9c1.5 2 3.5 3.5 6 4.5"/>
                    <path d="M14 20l4-9 4 9M15.5 17h5"/>
                </svg>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item {{ app()->getLocale() === 'ar' ? 'active' : '' }}" href="{{ route('lang.swap', 'ar') }}">
                        <span class="d-flex align-items-center">
                            <span class="me-2">🇸🇦</span>
                            <span class="flex-grow-1">العربية</span>
                            @if(app()->getLocale() === 'ar')
                            <i class="icon-base bx bx-check ms-2 text-primary"></i>
                            @endif
                        </span>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item {{ app()->getLocale() === 'en' ? 'active' : '' }}" href="{{ route('lang.swap', 'en') }}">
                        <span class="d-flex align-items-center">
                            <span class="me-2">🇺🇸</span>
                            <span class="flex-grow-1">English</span>
                            @if(app()->getLocale() === 'en')
                            <i class="icon-base bx bx-check ms-2 text-primary"></i>
                            @endif
                        </span>
                    </a>
                </li>
            </ul>
        </li>
        <!--/ Language Switcher -->

        <!-- Style / Theme Switcher (Sun / Moon) -->
        <li class="nav-item dropdown-style-switcher dropdown">
            <a class="nav-link dropdown-toggle hide-arrow btn-nav-circle" href="javascript:void(0);" id="theme-toggle-btn" title="الوضع الليلي / النهاري">
                <i class="icon-base bx bx-sun fs-4 theme-icon-sun"></i>
                <i class="icon-base bx bx-moon fs-4 theme-icon-moon d-none"></i>
            </a>
        </li>
        <!--/ Style Switcher -->

        <!-- User Profile Avatar -->
        <li class="nav-item navbar-dropdown dropdown-user dropdown ms-1">
            <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);" data-bs-toggle="dropdown">
                <div class="avatar avatar-online">
                    <img src="{{ asset('assets/img/avatars/1.png') }}" alt="User Profile" class="w-px-40 h-auto rounded-circle">
                </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="javascript:void(0);">
                        <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar avatar-online">
                                    <img src="{{ asset('assets/img/avatars/1.png') }}" alt class="w-px-40 h-auto rounded-circle">
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0">{{ Auth::check() ? Auth::user()->name : 'مسؤول النظام' }}</h6>
                                <small class="text-muted">{{ Auth::check() ? Auth::user()->email : 'Admin' }}</small>
                            </div>
                        </div>
                    </a>
                </li>
                <li>
                    <div class="dropdown-divider my-1"></div>
                </li>
                <li>
                    <a class="dropdown-item" href="javascript:void(0);">
                        <i class="icon-base bx bx-user icon-md me-3"></i><span>{{ __('My Profile') }}</span>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="javascript:void(0);">
                        <i class="icon-base bx bx-cog icon-md me-3"></i><span>{{ __('Settings') }}</span>
                    </a>
                </li>
                <li>
                    <div class="dropdown-divider my-1"></div>
                </li>
                <li>
                    <form method="POST" action="{{ route('logout') }}" id="logout-form">
                        @csrf
                        <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="document.getElementById('logout-form').submit();">
                            <i class="icon-base bx bx-power-off icon-md me-3"></i><span>تسجيل الخروج</span>
                        </a>
                    </form>
                </li>
            </ul>
        </li>
        <!--/ User -->
    </ul>
</div>