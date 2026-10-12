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
    <div class="navbar-nav align-items-center global-navbar-search-wrapper position-relative">
        <div class="nav-item d-flex align-items-center w-100 position-relative">
            <i class="icon-base bx bx-search search-icon"></i>
            <input type="text" id="globalNavbarSearchInput" class="form-control border-0 shadow-none global-navbar-search-input" aria-label="{{ __('Search...') }}" autocomplete="off">
            <div id="globalSearchSpinner" class="spinner-border spinner-border-sm text-primary search-spinner d-none" role="status">
                <span class="visually-hidden">{{ __('Searching...') }}</span>
            </div>
            
        </div>

        <!-- Global Search Dropdown Results -->
        <div id="globalSearchResultsDropdown" class="global-search-dropdown d-none">
            <div id="globalSearchDropdownContent"></div>
            <div class="global-search-footer">
                <span><kbd class="badge bg-label-secondary px-1 py-0 me-1">↑</kbd><kbd class="badge bg-label-secondary px-1 py-0 me-1">↓</kbd> {{ __('to navigate') }}</span>
                <span><kbd class="badge bg-label-secondary px-1 py-0 me-1">Enter</kbd> {{ __('to select') }}</span>
                <span><kbd class="badge bg-label-secondary px-1 py-0 me-1">Esc</kbd> {{ __('to close') }}</span>
            </div>
        </div>
    </div>
    <!-- /Search -->
    <ul class="navbar-nav flex-row align-items-center ms-auto gap-2">
        <!-- Language Switcher (文A) -->
        <li class="nav-item dropdown-language dropdown">
            <a class="nav-link dropdown-toggle hide-arrow btn-nav-circle" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="false" title="{{ __('Change Language') }}">
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
            <a class="nav-link dropdown-toggle hide-arrow btn-nav-circle" href="javascript:void(0);" id="theme-toggle-btn" title="{{ __('Toggle Dark / Light Mode') }}">
                <i class="icon-base bx bx-sun fs-4 theme-icon-sun"></i>
                <i class="icon-base bx bx-moon fs-4 theme-icon-moon d-none"></i>
            </a>
        </li>
        <!--/ Style Switcher -->

        <!-- AI Financial Assistant (المساعد المالي والمرشد الذكي) -->
        @if(request()->routeIs('accounting.*'))
        <li class="nav-item">
            <a class="nav-link hide-arrow btn-nav-circle position-relative" href="{{ route('accounting.ai-assistant.index') }}" title="{{ __('المساعد المالي والمرشد الذكي') }}">
                <i class="icon-base bx bx-bot fs-4 text-primary"></i>
                <span class="position-absolute top-0 start-100 translate-middle p-1 bg-success border border-light rounded-circle" style="width: 8px; height: 8px; margin-top: 6px; margin-inline-start: -6px;"></span>
            </a>
        </li>
        @endif


        <!-- User Profile Avatar -->
        <li class="nav-item navbar-dropdown dropdown-user dropdown ms-1">
            <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);" data-bs-toggle="dropdown">
                <div class="avatar avatar-online">
                    <img src="{{ Auth::check() ? Auth::user()->avatar_url : asset('assets/img/avatars/1.png') }}" alt="User Profile" class="w-px-40 h-auto rounded-circle">
                </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="{{ route('profile.show') }}">
                        <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar avatar-online">
                                    <img src="{{ Auth::check() ? Auth::user()->avatar_url : asset('assets/img/avatars/1.png') }}" alt class="w-px-40 h-auto rounded-circle">
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0">{{ Auth::check() ? Auth::user()->name : __('System Administrator') }}</h6>
                                <small class="text-muted">{{ Auth::check() ? Auth::user()->email : 'Admin' }}</small>
                            </div>
                        </div>
                    </a>
                </li>
                <li>
                    <div class="dropdown-divider my-1"></div>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('profile.show') }}">
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
                            <i class="icon-base bx bx-power-off icon-md me-3"></i><span>{{ __('Log Out') }}</span>
                        </a>
                    </form>
                </li>
            </ul>
        </li>
        <!--/ User -->
    </ul>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('globalNavbarSearchInput');
    const dropdown = document.getElementById('globalSearchResultsDropdown');
    const dropdownContent = document.getElementById('globalSearchDropdownContent');
    const spinner = document.getElementById('globalSearchSpinner');
    const searchUrl = @json(route('global.search'));

    if (!searchInput || !dropdown || !dropdownContent) return;

    let debounceTimer = null;
    let activeIndex = -1;
    let currentItems = [];

    // Global shortcut Ctrl+K / Cmd+K
    document.addEventListener('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
            e.preventDefault();
            searchInput.focus();
            searchInput.select();
            showInitialShortcuts();
        }
    });

    // Handle Input change with debounce
    searchInput.addEventListener('input', function () {
        const query = this.value.trim();
        clearTimeout(debounceTimer);

        if (query.length < 2) {
            hideSpinner();
            showInitialShortcuts();
            return;
        }

        showSpinner();
        debounceTimer = setTimeout(() => {
            fetchResults(query);
        }, 250);
    });

    // Focus input -> show dropdown
    searchInput.addEventListener('focus', function () {
        if (this.value.trim().length >= 2) {
            dropdown.classList.remove('d-none');
        } else {
            showInitialShortcuts();
        }
    });

    // Keyboard navigation (Up, Down, Enter, Esc)
    searchInput.addEventListener('keydown', function (e) {
        if (dropdown.classList.contains('d-none')) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            navigateItems(1);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            navigateItems(-1);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (activeIndex >= 0 && currentItems[activeIndex]) {
                window.location.href = currentItems[activeIndex].url;
            }
        } else if (e.key === 'Escape') {
            e.preventDefault();
            closeDropdown();
        }
    });

    // Click outside to close
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.global-navbar-search-wrapper')) {
            closeDropdown();
        }
    });

    function showSpinner() {
        if (spinner) spinner.classList.remove('d-none');
    }

    function hideSpinner() {
        if (spinner) spinner.classList.add('d-none');
    }

    function closeDropdown() {
        dropdown.classList.add('d-none');
        activeIndex = -1;
    }

    function fetchResults(query) {
        fetch(`${searchUrl}?q=${encodeURIComponent(query)}`)
            .then(res => res.json())
            .then(data => {
                hideSpinner();
                if (data.status && data.data) {
                    renderResults(data.data, query);
                }
            })
            .catch(() => {
                hideSpinner();
            });
    }

    function highlightText(text, query) {
        if (!text || !query) return text || '';
        const escaped = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        const regex = new RegExp(`(${escaped})`, 'gi');
        return text.replace(regex, '<span class="text-primary fw-bold">$1</span>');
    }

@php
$searchI18n = [
    'frequentlyUsed' => __('Frequently Used Screens'),
    'shortcuts' => __('Shortcuts'),
    'noResults' => __('No results found for :query'),
    'trySearching' => __('Try searching by student name, guardian, employee, or screen title'),
    'screen' => __('Screen'),
];

$defaultSearchPages = [
    [
        'title' => __('Student Affairs'),
        'subtitle' => __('List and records of registered students'),
        'url' => Route::has('academic.students.index') ? route('academic.students.index') : '#',
        'icon' => 'bx bx-user',
        'badge' => __('Screen'),
        'badge_class' => 'bg-label-info',
    ],
    [
        'title' => __('Guardians'),
        'subtitle' => __('Guardian contacts and information'),
        'url' => Route::has('academic.guardians.index') ? route('academic.guardians.index') : '#',
        'icon' => 'bx bx-group',
        'badge' => __('Screen'),
        'badge_class' => 'bg-label-success',
    ],
    [
        'title' => __('Interviews Calendar'),
        'subtitle' => __('Guardian interviews schedule'),
        'url' => Route::has('academic.interviews_calender.index') ? route('academic.interviews_calender.index') : '#',
        'icon' => 'bx bx-calendar-event',
        'badge' => __('Screen'),
        'badge_class' => 'bg-label-primary',
    ],
    [
        'title' => __('Events'),
        'subtitle' => __('School activities and events management'),
        'url' => Route::has('academic.events.index') ? route('academic.events.index') : '#',
        'icon' => 'bx bx-party',
        'badge' => __('Screen'),
        'badge_class' => 'bg-label-warning',
    ],
    [
        'title' => __('Incident Reports'),
        'subtitle' => __('Incident and injury records'),
        'url' => Route::has('academic.incident-reports.index') ? route('academic.incident-reports.index') : '#',
        'icon' => 'bx bx-error-circle',
        'badge' => __('Screen'),
        'badge_class' => 'bg-label-danger',
    ],
];
@endphp

    const i18n = {!! json_encode($searchI18n) !!};
    const defaultPages = {!! json_encode($defaultSearchPages) !!};

    function showInitialShortcuts() {

        let html = `
            <div class="global-search-category-header">
                <span><i class="bx bx-bolt-circle me-1 text-primary"></i> ${i18n.frequentlyUsed}</span>
                <span class="badge bg-label-secondary">${i18n.shortcuts}</span>
            </div>
        `;

        currentItems = defaultPages;
        activeIndex = -1;

        defaultPages.forEach((item, idx) => {
            html += `
                <a href="${item.url}" class="global-search-item" data-index="${idx}">
                    <div class="search-item-icon-box bg-label-primary text-primary">
                        <i class="${item.icon}"></i>
                    </div>
                    <div class="search-item-info">
                        <div class="search-item-title">${item.title}</div>
                        <div class="search-item-subtitle">${item.subtitle}</div>
                    </div>
                    <span class="search-item-badge ${item.badge_class}">${item.badge}</span>
                </a>
            `;
        });

        dropdownContent.innerHTML = html;
        dropdown.classList.remove('d-none');
    }

    function renderResults(resultData, query) {
        currentItems = [];
        activeIndex = -1;

        if (resultData.total === 0 || !resultData.categories || resultData.categories.length === 0) {
            const emptyMsg = i18n.noResults.replace(':query', query);
            dropdownContent.innerHTML = `
                <div class="global-search-empty">
                    <i class="bx bx-search-alt text-muted"></i>
                    <h6 class="mb-1 text-muted">${emptyMsg}</h6>
                    <small class="text-secondary">${i18n.trySearching}</small>
                </div>
            `;
            dropdown.classList.remove('d-none');
            return;
        }

        let html = '';
        let globalIndex = 0;

        resultData.categories.forEach(cat => {
            html += `
                <div class="global-search-category-header">
                    <span><i class="${cat.icon} me-1 text-${cat.color}"></i> ${cat.title}</span>
                    <span class="badge bg-label-${cat.color}">${cat.count}</span>
                </div>
            `;

            cat.items.forEach(item => {
                currentItems.push(item);
                const itemIndex = globalIndex++;

                html += `
                    <a href="${item.url}" class="global-search-item" data-index="${itemIndex}">
                        <div class="search-item-icon-box bg-label-${cat.color} text-${cat.color}">
                            <i class="${item.icon}"></i>
                        </div>
                        <div class="search-item-info">
                            <div class="search-item-title">${highlightText(item.title, query)}</div>
                            <div class="search-item-subtitle">${item.subtitle}</div>
                        </div>
                        <span class="search-item-badge ${item.badge_class}">${item.badge}</span>
                    </a>
                `;
            });
        });

        dropdownContent.innerHTML = html;
        dropdown.classList.remove('d-none');
    }

    function navigateItems(direction) {
        const itemElements = dropdownContent.querySelectorAll('.global-search-item');
        if (itemElements.length === 0) return;

        if (activeIndex >= 0 && itemElements[activeIndex]) {
            itemElements[activeIndex].classList.remove('active');
        }

        activeIndex += direction;
        if (activeIndex >= itemElements.length) activeIndex = 0;
        if (activeIndex < 0) activeIndex = itemElements.length - 1;

        const currentEl = itemElements[activeIndex];
        if (currentEl) {
            currentEl.classList.add('active');
            currentEl.scrollIntoView({ block: 'nearest' });
        }
    }
});
</script>
