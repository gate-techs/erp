@php
    $counts = $counts ?? [
        'administrations' => \Modules\HR\Models\Department::whereNull('parent_id')->count(),
        'departments'     => \Modules\HR\Models\Department::whereNotNull('parent_id')->count(),
        'job_titles'      => \Modules\HR\Models\JobTitle::count(),
        'allowance_types' => \Modules\HR\Models\HrSetting::where('type', 'allowance-types')->count(),
        'document_types'  => \Modules\HR\Models\HrSetting::where('type', 'document-types')->count(),
        'shifts'          => \Modules\HR\Models\Shift::count(),
        'work_schedules'   => \Modules\HR\Models\WorkSchedule::count(),
        'official_holidays'=> \Modules\HR\Models\OfficialHoliday::count(),
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

            <!-- 4- أنواع البدلات -->
            <a href="{{ route('hr.settings.allowance-types.index') }}" 
               class="nav-link text-nowrap d-flex align-items-center justify-content-between px-3 py-2 rounded {{ request()->routeIs('hr.settings.allowance-types.*') ? 'active bg-primary text-white fw-bold shadow-sm' : 'text-body' }}">
                <div class="d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-wallet {{ request()->routeIs('hr.settings.allowance-types.*') ? 'text-white' : 'text-primary' }} font-18"></i>
                    <span>{{ __('Allowance Types') }}</span>
                </div>
                <span class="badge rounded-pill ms-2 {{ request()->routeIs('hr.settings.allowance-types.*') ? 'bg-white text-primary' : 'bg-label-primary' }}">
                    {{ $counts['allowance_types'] ?? 0 }}
                </span>
            </a>

            <!-- 5- أنواع المستندات -->
            <a href="{{ route('hr.settings.document-types.index') }}" 
               class="nav-link text-nowrap d-flex align-items-center justify-content-between px-3 py-2 rounded {{ request()->routeIs('hr.settings.document-types.*') ? 'active bg-primary text-white fw-bold shadow-sm' : 'text-body' }}">
                <div class="d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-file {{ request()->routeIs('hr.settings.document-types.*') ? 'text-white' : 'text-primary' }} font-18"></i>
                    <span>{{ __('Document Types') }}</span>
                </div>
                <span class="badge rounded-pill ms-2 {{ request()->routeIs('hr.settings.document-types.*') ? 'bg-white text-primary' : 'bg-label-primary' }}">
                    {{ $counts['document_types'] ?? 0 }}
                </span>
            </a>

            <!-- 6- الورديات -->
            <a href="{{ route('hr.settings.shifts.index') }}" 
               class="nav-link text-nowrap d-flex align-items-center justify-content-between px-3 py-2 rounded {{ request()->routeIs('hr.settings.shifts.*') ? 'active bg-primary text-white fw-bold shadow-sm' : 'text-body' }}">
                <div class="d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-time-five {{ request()->routeIs('hr.settings.shifts.*') ? 'text-white' : 'text-primary' }} font-18"></i>
                    <span>{{ __('Shifts') }}</span>
                </div>
                <span class="badge rounded-pill ms-2 {{ request()->routeIs('hr.settings.shifts.*') ? 'bg-white text-primary' : 'bg-label-primary' }}">
                    {{ $counts['shifts'] ?? 0 }}
                </span>
            </a>

            <!-- 7- جداول العمل -->
            <a href="{{ route('hr.settings.work-schedules.index') }}" 
               class="nav-link text-nowrap d-flex align-items-center justify-content-between px-3 py-2 rounded {{ request()->routeIs('hr.settings.work-schedules.*') ? 'active bg-primary text-white fw-bold shadow-sm' : 'text-body' }}">
                <div class="d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-calendar-event {{ request()->routeIs('hr.settings.work-schedules.*') ? 'text-white' : 'text-primary' }} font-18"></i>
                    <span>{{ __('Work Schedules') }}</span>
                </div>
                <span class="badge rounded-pill ms-2 {{ request()->routeIs('hr.settings.work-schedules.*') ? 'bg-white text-primary' : 'bg-label-primary' }}">
                    {{ $counts['work_schedules'] ?? 0 }}
                </span>
            </a>

            <!-- 8- العطلات الرسمية -->
            <a href="{{ route('hr.settings.official-holidays.index') }}" 
               class="nav-link text-nowrap d-flex align-items-center justify-content-between px-3 py-2 rounded {{ request()->routeIs('hr.settings.official-holidays.*') ? 'active bg-primary text-white fw-bold shadow-sm' : 'text-body' }}">
                <div class="d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-sun {{ request()->routeIs('hr.settings.official-holidays.*') ? 'text-white' : 'text-primary' }} font-18"></i>
                    <span>{{ __('Official Holidays') }}</span>
                </div>
                <span class="badge rounded-pill ms-2 {{ request()->routeIs('hr.settings.official-holidays.*') ? 'bg-white text-primary' : 'bg-label-primary' }}">
                    {{ $counts['official_holidays'] ?? 0 }}
                </span>
            </a>
        </div>
    </div>
</div>
