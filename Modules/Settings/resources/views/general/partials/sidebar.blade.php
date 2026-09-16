@php
    $counts = $generalSettingsCounts ?? [
        'governments' => \Modules\Settings\Models\GeneralSetting::ofType(\Modules\Settings\Enums\GeneralSettingType::GOVERNMENT)->count(),
        'nationalities' => \Modules\Settings\Models\GeneralSetting::ofType(\Modules\Settings\Enums\GeneralSettingType::NATIONALITY)->count(),
        'religions' => \Modules\Settings\Models\GeneralSetting::ofType(\Modules\Settings\Enums\GeneralSettingType::RELIGION)->count(),
        'maritalStatuses' => \Modules\Settings\Models\GeneralSetting::ofType(\Modules\Settings\Enums\GeneralSettingType::MARITAL_STATUS)->count(),
        'banks' => \Modules\Settings\Models\Bank::mainBanks()->count(),
        'bankBranches' => \Modules\Settings\Models\Bank::bankBranches()->count(),
    ];
@endphp

<div class="card shadow-sm border-0 mb-3 mb-md-0">
    <div class="card-header bg-transparent border-bottom py-2 py-md-3 d-none d-md-block">
        <h6 class="card-title mb-0 d-flex align-items-center gap-2 fw-bold text-primary">
            <i class="icon-base bx bx-slider-alt font-18"></i>
            <span>{{ __('General Settings List') }}</span>
        </h6>
    </div>
    <div class="card-body p-2">
        <div class="nav nav-pills flex-row flex-md-column flex-nowrap overflow-auto gap-1" role="tablist" style="scrollbar-width: none;">
            <!-- 1- المحافظات -->
            <a href="{{ route('settings.general.governments.index') }}" 
               class="nav-link text-nowrap d-flex align-items-center justify-content-between px-3 py-2 rounded {{ request()->routeIs('settings.general.governments.*') ? 'active bg-primary text-white fw-bold shadow-sm' : 'text-body' }}">
                <div class="d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-map-pin {{ request()->routeIs('settings.general.governments.*') ? 'text-white' : 'text-primary' }} font-18"></i>
                    <span>{{ __('Governments') }}</span>
                </div>
                <span class="badge rounded-pill ms-2 {{ request()->routeIs('settings.general.governments.*') ? 'bg-white text-primary' : 'bg-label-primary' }}">
                    {{ $counts['governments'] ?? 0 }}
                </span>
            </a>

            <!-- 2- الجنسيات -->
            <a href="{{ route('settings.general.nationalities.index') }}" 
               class="nav-link text-nowrap d-flex align-items-center justify-content-between px-3 py-2 rounded {{ request()->routeIs('settings.general.nationalities.*') ? 'active bg-primary text-white fw-bold shadow-sm' : 'text-body' }}">
                <div class="d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-flag {{ request()->routeIs('settings.general.nationalities.*') ? 'text-white' : 'text-primary' }} font-18"></i>
                    <span>{{ __('Nationalities') }}</span>
                </div>
                <span class="badge rounded-pill ms-2 {{ request()->routeIs('settings.general.nationalities.*') ? 'bg-white text-primary' : 'bg-label-primary' }}">
                    {{ $counts['nationalities'] ?? 0 }}
                </span>
            </a>

            <!-- 3- الديانات -->
            <a href="{{ route('settings.general.religions.index') }}" 
               class="nav-link text-nowrap d-flex align-items-center justify-content-between px-3 py-2 rounded {{ request()->routeIs('settings.general.religions.*') ? 'active bg-primary text-white fw-bold shadow-sm' : 'text-body' }}">
                <div class="d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-book-open {{ request()->routeIs('settings.general.religions.*') ? 'text-white' : 'text-primary' }} font-18"></i>
                    <span>{{ __('Religions') }}</span>
                </div>
                <span class="badge rounded-pill ms-2 {{ request()->routeIs('settings.general.religions.*') ? 'bg-white text-primary' : 'bg-label-primary' }}">
                    {{ $counts['religions'] ?? 0 }}
                </span>
            </a>

            <!-- 4- الحالة الاجتماعية -->
            <a href="{{ route('settings.general.marital-statuses.index') }}" 
               class="nav-link text-nowrap d-flex align-items-center justify-content-between px-3 py-2 rounded {{ request()->routeIs('settings.general.marital-statuses.*') ? 'active bg-primary text-white fw-bold shadow-sm' : 'text-body' }}">
                <div class="d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-group {{ request()->routeIs('settings.general.marital-statuses.*') ? 'text-white' : 'text-primary' }} font-18"></i>
                    <span>{{ __('Marital Status') }}</span>
                </div>
                <span class="badge rounded-pill ms-2 {{ request()->routeIs('settings.general.marital-statuses.*') ? 'bg-white text-primary' : 'bg-label-primary' }}">
                    {{ $counts['maritalStatuses'] ?? 0 }}
                </span>
            </a>

            <!-- 5- البنوك الرئيسية -->
            <a href="{{ route('settings.general.banks.index') }}" 
               class="nav-link text-nowrap d-flex align-items-center justify-content-between px-3 py-2 rounded {{ request()->routeIs('settings.general.banks.*') ? 'active bg-primary text-white fw-bold shadow-sm' : 'text-body' }}">
                <div class="d-flex align-items-center gap-2">
                    <i class="icon-base bx bxs-bank {{ request()->routeIs('settings.general.banks.*') ? 'text-white' : 'text-primary' }} font-18"></i>
                    <span>{{ __('Banks') }}</span>
                </div>
                <span class="badge rounded-pill ms-2 {{ request()->routeIs('settings.general.banks.*') ? 'bg-white text-primary' : 'bg-label-primary' }}">
                    {{ $counts['banks'] ?? 0 }}
                </span>
            </a>

            <!-- 6- فروع البنوك -->
            <a href="{{ route('settings.general.bank-branches.index') }}" 
               class="nav-link text-nowrap d-flex align-items-center justify-content-between px-3 py-2 rounded {{ request()->routeIs('settings.general.bank-branches.*') ? 'active bg-primary text-white fw-bold shadow-sm' : 'text-body' }}">
                <div class="d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-git-repo-forked {{ request()->routeIs('settings.general.bank-branches.*') ? 'text-white' : 'text-primary' }} font-18"></i>
                    <span>{{ __('Bank Branches') }}</span>
                </div>
                <span class="badge rounded-pill ms-2 {{ request()->routeIs('settings.general.bank-branches.*') ? 'bg-white text-primary' : 'bg-label-primary' }}">
                    {{ $counts['bankBranches'] ?? 0 }}
                </span>
            </a>
        </div>
    </div>
</div>

