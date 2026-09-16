@php
    $counts = $counts ?? [
        'administrations' => \Modules\HR\Models\Department::whereNull('parent_id')->count(),
        'departments'     => \Modules\HR\Models\Department::whereNotNull('parent_id')->count(),
        'job_titles'      => \Modules\HR\Models\JobTitle::count(),
    ];
@endphp

<div class="card shadow-sm border-0 mb-3 mb-md-0">
    <div class="card-header bg-transparent border-bottom py-2 py-md-3 d-none d-md-block">
        <h6 class="card-title mb-0 d-flex align-items-center gap-2 fw-bold text-primary">
            <i class="icon-base bx bx-slider-alt font-18"></i>
            <span>{{ __('HR Settings List') }}</span>
        </h6>
    </div>
    <div class="card-body p-2">
        <div class="nav nav-pills flex-row flex-md-column flex-nowrap overflow-auto gap-1" role="tablist" style="scrollbar-width: none;">
            <!-- 1- الإدارات -->
            <a href="{{ route('hr.settings.administrations.index') }}" 
               class="nav-link text-nowrap d-flex align-items-center justify-content-between px-3 py-2 rounded {{ request()->routeIs('hr.settings.administrations.*') ? 'active bg-primary text-white fw-bold shadow-sm' : 'text-body' }}">
                <div class="d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-buildings {{ request()->routeIs('hr.settings.administrations.*') ? 'text-white' : 'text-primary' }} font-18"></i>
                    <span>{{ __('Administrations') }}</span>
                </div>
                <span class="badge rounded-pill ms-2 {{ request()->routeIs('hr.settings.administrations.*') ? 'bg-white text-primary' : 'bg-label-primary' }}">
                    {{ $counts['administrations'] ?? 0 }}
                </span>
            </a>

            <!-- 2- الأقسام -->
            <a href="{{ route('hr.settings.departments.index') }}" 
               class="nav-link text-nowrap d-flex align-items-center justify-content-between px-3 py-2 rounded {{ request()->routeIs('hr.settings.departments.*') ? 'active bg-primary text-white fw-bold shadow-sm' : 'text-body' }}">
                <div class="d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-sitemap {{ request()->routeIs('hr.settings.departments.*') ? 'text-white' : 'text-primary' }} font-18"></i>
                    <span>{{ __('Departments') }}</span>
                </div>
                <span class="badge rounded-pill ms-2 {{ request()->routeIs('hr.settings.departments.*') ? 'bg-white text-primary' : 'bg-label-primary' }}">
                    {{ $counts['departments'] ?? 0 }}
                </span>
            </a>

            <!-- 3- المسميات الوظيفية -->
            <a href="{{ route('hr.settings.job-titles.index') }}" 
               class="nav-link text-nowrap d-flex align-items-center justify-content-between px-3 py-2 rounded {{ request()->routeIs('hr.settings.job-titles.*') ? 'active bg-primary text-white fw-bold shadow-sm' : 'text-body' }}">
                <div class="d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-id-card {{ request()->routeIs('hr.settings.job-titles.*') ? 'text-white' : 'text-primary' }} font-18"></i>
                    <span>{{ __('Job Titles') }}</span>
                </div>
                <span class="badge rounded-pill ms-2 {{ request()->routeIs('hr.settings.job-titles.*') ? 'bg-white text-primary' : 'bg-label-primary' }}">
                    {{ $counts['job_titles'] ?? 0 }}
                </span>
            </a>
        </div>
    </div>
</div>
