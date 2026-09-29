@extends('layouts/contentNavbarLayout')

@section('title', ($employee->translate(app()->getLocale())?->name ?? $employee->translate('ar')?->name ?? $employee->translate('en')?->name ?? __('Employee Profile')) . ' - ' . config('variables.templateName'))

@section('page-style')
<style>
/* ===== CORE VARIABLES ===== */
:root {
    --p-slate:      #1e293b;
    --p-slate2:     #334155;
    --p-muted:      #64748b;
    --p-border:     #e2e8f0;
    --p-bg:         #f8fafc;
    --p-card:       #ffffff;
    --p-emerald:    #10b981;
    --p-amber:      #f59e0b;
    --p-rose:       #f43f5e;
    --p-teal:       #0d9488;
    --p-violet:     #7c3aed;
    --p-radius:     .875rem;
    --p-shadow:     0 1px 3px rgba(0,0,0,.07), 0 4px 12px rgba(0,0,0,.05);
    --p-shadow-md:  0 4px 16px rgba(0,0,0,.10);
}

/* ===== PAGE BG ===== */
body { background: var(--p-bg) !important; }

/* ===== PROFILE HERO ===== */
.profile-hero {
    background: linear-gradient(120deg, var(--p-slate) 0%, var(--p-slate2) 100%);
    border-radius: var(--p-radius);
    padding: 2rem;
    color: #fff;
    position: relative;
    overflow: hidden;
    box-shadow: var(--p-shadow-md);
}
.profile-hero::before {
    content: '';
    position: absolute; inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Ccircle cx='30' cy='30' r='20'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") repeat;
    pointer-events: none;
}
.profile-avatar-wrap {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.profile-avatar-wrap img {
    width: 88px; height: 88px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid rgba(255,255,255,.35);
    box-shadow: 0 4px 20px rgba(0,0,0,.3);
}
.ring-svg {
    position: absolute; top: -7px; left: -7px;
    width: 102px; height: 102px;
    transform: rotate(-90deg);
    pointer-events: none;
}
.status-badge-dot {
    position: absolute; bottom: 4px; right: 4px;
    width: 16px; height: 16px;
    border: 2.5px solid var(--p-slate);
    border-radius: 50%;
}
.hero-name { font-size: 1.45rem; font-weight: 700; letter-spacing: -.01em; margin-bottom: .2rem; }
.hero-sub  { font-size: .88rem; opacity: .7; }
.hero-chip {
    display: inline-flex; align-items: center; gap: .3rem;
    background: rgba(255,255,255,.12); backdrop-filter: blur(6px);
    border: 1px solid rgba(255,255,255,.18);
    border-radius: 20px; padding: .25rem .75rem;
    font-size: .78rem; font-weight: 600;
    color: #fff;
}
.completion-circle {
    text-align: center; min-width: 90px;
}
.completion-circle .pct { font-size: 1.6rem; font-weight: 800; line-height: 1; }
.completion-circle small { font-size: .72rem; opacity: .7; }

/* ===== STATS SUMMARY BAR (CLEAN & ELEGANT) ===== */
.stats-summary-card {
    background: #ffffff;
    border: 1px solid var(--p-border);
    border-radius: var(--p-radius);
    margin: 1.15rem 0;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    overflow: hidden;
}
.stats-summary-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
}
.stat-summary-item {
    padding: 1.15rem 1.4rem;
    display: flex;
    flex-direction: column;
    gap: .25rem;
    position: relative;
    background: #ffffff;
    transition: background-color .15s ease;
}
.stat-summary-item:not(:last-child) {
    border-inline-end: 1px solid var(--p-border);
}
.stat-summary-item:hover {
    background-color: #fafbfc;
}
.stat-summary-header {
    display: flex;
    align-items: center;
    gap: .45rem;
}
.stat-summary-icon {
    font-size: 1.15rem;
    line-height: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.stat-summary-label {
    font-size: .78rem;
    font-weight: 600;
    color: var(--p-muted);
}
.stat-summary-val-wrap {
    display: flex;
    align-items: baseline;
    gap: .35rem;
    margin-top: .15rem;
}
.stat-summary-val {
    font-size: 1.4rem;
    font-weight: 700;
    color: var(--p-slate);
    line-height: 1.15;
    letter-spacing: -.01em;
    font-variant-numeric: tabular-nums;
}
.stat-summary-unit {
    font-size: .78rem;
    font-weight: 600;
    color: var(--p-muted);
}
.stat-summary-sub {
    font-size: .72rem;
    color: #94a3b8;
}

@media (max-width: 991px) {
    .stats-summary-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    .stat-summary-item:nth-child(2) {
        border-inline-end: none;
    }
    .stat-summary-item:nth-child(-n+2) {
        border-bottom: 1px solid var(--p-border);
    }
}
@media (max-width: 576px) {
    .stats-summary-grid {
        grid-template-columns: 1fr;
    }
    .stat-summary-item {
        border-inline-end: none !important;
        border-bottom: 1px solid var(--p-border);
    }
    .stat-summary-item:last-child {
        border-bottom: none;
    }
}

/* ===== MAIN LAYOUT ===== */
.profile-layout { display: grid; grid-template-columns: 280px 1fr; gap: 1.1rem; align-items: start; }
@media (max-width: 991px) { .profile-layout { grid-template-columns: 1fr; } }

/* ===== SIDEBAR CARD ===== */
.sidebar-card {
    background: var(--p-card);
    border-radius: var(--p-radius);
    box-shadow: var(--p-shadow);
    overflow: hidden;
    position: sticky; top: 80px;
}
.sidebar-section { padding: 1.1rem 1.25rem; border-bottom: 1px solid var(--p-border); }
.sidebar-section:last-child { border-bottom: none; }
.sidebar-section-title {
    font-size: .68rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .07em; color: var(--p-muted); margin-bottom: .75rem;
    display: flex; align-items: center; gap: .4rem;
}
.sb-row { display: flex; align-items: flex-start; gap: .6rem; padding: .35rem 0; }
.sb-icon {
    width: 28px; height: 28px; border-radius: .4rem;
    display: flex; align-items: center; justify-content: center;
    font-size: .9rem; flex-shrink: 0; margin-top: .1rem;
}
.sb-label { font-size: .7rem; color: var(--p-muted); font-weight: 600; line-height: 1.2; }
.sb-value { font-size: .83rem; color: var(--p-slate); font-weight: 500; line-height: 1.3; word-break: break-word; }

/* ===== TABS CARD ===== */
.tabs-card {
    background: var(--p-card);
    border-radius: var(--p-radius);
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    border: 1px solid var(--p-border);
    overflow: hidden;
}
.profile-nav {
    display: flex;
    overflow-x: auto;
    border-bottom: 1px solid var(--p-border);
    padding: 0 1.25rem;
    scrollbar-width: none;
    background: #ffffff;
}
.profile-nav::-webkit-scrollbar { display: none; }
.profile-nav .pnav-item {
    display: flex;
    align-items: center;
    gap: .5rem;
    padding: 1rem 1.15rem;
    font-size: .84rem;
    font-weight: 600;
    color: var(--p-muted);
    white-space: nowrap;
    border-bottom: 2.5px solid transparent;
    cursor: pointer;
    margin-bottom: -1px;
    transition: all .15s ease;
    text-decoration: none;
    background: none;
    border-top: none;
    border-left: none;
    border-right: none;
}
.profile-nav .pnav-item:hover {
    color: var(--p-slate);
}
.profile-nav .pnav-item.active {
    color: var(--p-slate);
    border-bottom-color: var(--p-slate);
    font-weight: 700;
}
.profile-nav .pnav-item i {
    font-size: 1.05rem;
}
.tabs-body {
    padding: 1.5rem;
    background: #fafbfc;
}

/* ===== SUBCARDS (CLEAN & SPACIOUS) ===== */
.profile-subcard {
    background: #ffffff;
    border: 1px solid var(--p-border);
    border-radius: .75rem;
    overflow: hidden;
    margin-bottom: 1.25rem;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
}
.profile-subcard:last-child {
    margin-bottom: 0;
}
.profile-subcard-header {
    padding: .85rem 1.25rem;
    background: #ffffff;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.profile-subcard-title {
    font-size: .86rem;
    font-weight: 700;
    color: var(--p-slate);
    display: flex;
    align-items: center;
    gap: .5rem;
    margin: 0;
}
.profile-subcard-title i {
    font-size: 1.1rem;
    color: #64748b;
}
.profile-subcard-body {
    padding: 1.25rem;
}

/* ===== DESCRIPTION LIST GRID ===== */
.dl-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.2rem 1.5rem;
}
.dl-grid.cols-3 {
    grid-template-columns: repeat(3, 1fr);
}
.dl-grid.cols-2 {
    grid-template-columns: repeat(2, 1fr);
}
@media (max-width: 1199px) {
    .dl-grid { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 768px) {
    .dl-grid, .dl-grid.cols-3, .dl-grid.cols-2 { grid-template-columns: repeat(2, 1fr); gap: 1rem; }
}
@media (max-width: 480px) {
    .dl-grid, .dl-grid.cols-3, .dl-grid.cols-2 { grid-template-columns: 1fr; }
}

.dl-item {
    display: flex;
    flex-direction: column;
    gap: .25rem;
}
.dl-label {
    font-size: .74rem;
    font-weight: 600;
    color: var(--p-muted);
    line-height: 1.3;
}
.dl-value {
    font-size: .88rem;
    font-weight: 600;
    color: var(--p-slate);
    line-height: 1.35;
    word-break: break-word;
}
.dl-value.mono {
    font-family: var(--bs-, monospace);
    font-weight: 500;
}
.dl-value.empty {
    color: #94a3b8;
    font-weight: 400;
}

/* ===== ALLOWANCE ITEM ===== */
.allowance-list-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: .85rem 1rem;
    border-radius: .5rem;
    background: #f8fafc;
    border: 1px solid #f1f5f9;
    margin-bottom: .5rem;
    transition: background-color .15s ease;
}
.allowance-list-item:hover {
    background: #f1f5f9;
}
.allowance-list-item:last-child {
    margin-bottom: 0;
}

/* ===== RECORD TABLE ===== */
.rec-table {
    width: 100%;
    border-collapse: collapse;
    font-size: .83rem;
}
.rec-table thead th {
    font-size: .72rem;
    font-weight: 700;
    color: var(--p-muted);
    padding: .75rem 1rem;
    border-bottom: 1px solid var(--p-border);
    background: #f8fafc;
    text-align: start;
    white-space: nowrap;
}
.rec-table tbody td {
    padding: .8rem 1rem;
    border-bottom: 1px solid #f1f5f9;
    color: var(--p-slate2);
    vertical-align: middle;
}
.rec-table tbody tr:last-child td {
    border-bottom: none;
}
.rec-table tbody tr:hover td {
    background: #f8fafc;
}

/* ===== DOC CARD ===== */
.doc-card {
    background: #ffffff;
    border: 1px solid var(--p-border);
    border-radius: .65rem;
    padding: 1rem 1.1rem;
    display: flex;
    align-items: flex-start;
    gap: .85rem;
    transition: all .15s ease;
}
.doc-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    border-color: #cbd5e1;
}
.doc-icon {
    width: 42px;
    height: 42px;
    border-radius: .55rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}

/* ===== EMPTY STATE ===== */
.empty-state {
    text-align: center;
    padding: 2.5rem 1.5rem;
    color: var(--p-muted);
}
.empty-state i {
    font-size: 2.5rem;
    display: block;
    opacity: .3;
    margin-bottom: .6rem;
}
.empty-state p {
    font-size: .85rem;
    margin: 0;
}

/* ===== EXPIRY BADGES ===== */
.expiry-danger { color: var(--p-rose); font-weight: 600; }
.expiry-warn   { color: var(--p-amber); font-weight: 600; }

/* ===== PRINT ===== */
@media print {
    .no-print, .sidebar-wrapper, nav, footer, header { display: none !important; }
    .profile-layout { grid-template-columns: 1fr !important; }
    .sidebar-card { position: static !important; }
    .tabs-card .profile-nav { display: none; }
    .tabs-card .tab-pane { display: block !important; opacity: 1 !important; }
}
</style>
@endsection

@php
    $locale   = app()->getLocale();
    $nameAr   = $employee->translate('ar')?->name ?? '';
    $nameEn   = $employee->translate('en')?->name ?? '';
    $dispName = $locale === 'ar' ? ($nameAr ?: $nameEn) : ($nameEn ?: $nameAr);

    $jobTitle   = $employee->jobTitle?->translate($locale)?->name ?? $employee->jobTitle?->translate('ar')?->name ?? '-';
    $department = $employee->department?->translate($locale)?->name ?? $employee->department?->translate('ar')?->name ?? '-';
    $branch     = $employee->primaryBranch?->translate($locale)?->name ?? $employee->primaryBranch?->translate('ar')?->name ?? '-';
    $manager    = $employee->manager?->translate($locale)?->name ?? $employee->manager?->translate('ar')?->name ?? null;

    $statusEnum  = $employee->status;
    $statusColor = match($statusEnum?->value) {
        'active'     => '#10b981',
        'on_leave'   => '#0d9488',
        'probation'  => '#f59e0b',
        'suspended'  => '#f43f5e',
        'resigned'   => '#94a3b8',
        'terminated' => '#475569',
        default      => '#94a3b8',
    };
    $statusBg = match($statusEnum?->value) {
        'active'     => '#ecfdf5',
        'on_leave'   => '#f0fdfa',
        'probation'  => '#fffbeb',
        'suspended'  => '#fff1f2',
        'resigned'   => '#f8fafc',
        'terminated' => '#f1f5f9',
        default      => '#f8fafc',
    };

    $genderVal  = strtolower($employee->gender instanceof \BackedEnum ? $employee->gender->value : ($employee->gender ?? ''));
    $isMale     = $genderVal === 'male';

    $completion  = $employee->profile_completion;
    $percent     = (int) $completion['percentage'];
    $ringColor   = $completion['color'];
    $circ        = 277.0;
    $dashOff     = round($circ - ($circ * ($percent / 100)), 2);

    $activeAllowances = $employee->allowances->where('is_active', true);
    $allowTotal       = $activeAllowances->sum('amount');
@endphp

@section('content')

{{-- =================== BREADCRUMB =================== --}}
<div class="d-flex align-items-center justify-content-between mb-3 no-print">
    <div class="d-flex align-items-center gap-2">
        @if(isset($isOwnProfile) && $isOwnProfile)
            <a href="{{ url('/') }}" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1 rounded-pill px-3">
                <i class="icon-base bx bx-home font-15"></i>
                <span>{{ __('Dashboard') }}</span>
            </a>
            <span class="text-muted small">/</span>
            <span class="fw-medium small text-primary">{{ __('My Profile') }}</span>
        @else
            <a href="{{ route('hr.employees.index') }}" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1 rounded-pill px-3">
                <i class="icon-base bx bx-arrow-back font-15"></i>
                <span>{{ __('Employees') }}</span>
            </a>
            <span class="text-muted small">/</span>
            <span class="text-muted small">{{ $dispName }}</span>
        @endif
    </div>
    <div class="d-flex gap-2">
        @php
            $printUrl = isset($isOwnProfile) && $isOwnProfile ? route('profile.print') : route('hr.employees.print-profile', $employee->id);
            $activeTpls = $printTemplates ?? \Modules\Settings\Models\PrintTemplate::where('is_active', true)->get();
        @endphp
        <div class="btn-group">
            <a href="{{ $printUrl }}" target="_blank" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1 shadow-xs">
                <i class="icon-base bx bx-printer font-15"></i>
                <span class="d-none d-sm-inline">{{ __('Print') }}</span>
            </a>
            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false" title="{{ __('Print Templates') }}">
                <span class="visually-hidden">{{ __('Print Templates') }}</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow">
                <li><h6 class="dropdown-header text-uppercase small">{{ __('اختر قالب الطباعة:') }}</h6></li>
                @forelse($activeTpls as $tpl)
                    <li>
                        <a class="dropdown-item d-flex align-items-center justify-content-between py-2" target="_blank" 
                           href="{{ $printUrl }}?template_id={{ $tpl->id }}">
                            <span>
                                <i class="bx bx-file me-2 text-primary"></i> {{ $tpl->display_name }}
                                <small class="text-muted d-block ps-4">({{ $tpl->page_size }} - {{ ucfirst($tpl->orientation) }})</small>
                            </span>
                            @if($tpl->is_default)
                                <span class="badge bg-label-primary small">{{ __('افتراضي') }}</span>
                            @endif
                        </a>
                    </li>
                @empty
                    <li><span class="dropdown-item text-muted">{{ __('لا توجد قوالب نشطة') }}</span></li>
                @endforelse
            </ul>
        </div>
        @if(auth()->user() && auth()->user()->can('employees.edit'))
            <a href="{{ route('hr.employees.edit', $employee->id) }}" class="btn btn-sm btn-dark d-flex align-items-center gap-1 px-3">
                <i class="icon-base bx bx-edit font-15"></i>
                <span>{{ __('Edit Profile') }}</span>
            </a>
        @endif
    </div>
</div>

{{-- =================== HERO CARD =================== --}}
<div class="profile-hero mb-1-25">
    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center gap-3">

        {{-- Avatar + Ring --}}
        <div class="profile-avatar-wrap">
            <svg class="ring-svg" viewBox="0 0 102 102">
                <circle cx="51" cy="51" r="44" stroke="rgba(255,255,255,.15)" stroke-width="5" fill="none"/>
                <circle cx="51" cy="51" r="44"
                    stroke="{{ $ringColor }}"
                    stroke-width="5"
                    stroke-dasharray="{{ $circ }}"
                    stroke-dashoffset="{{ $dashOff }}"
                    stroke-linecap="round" fill="none"
                    style="transition:stroke-dashoffset 1.2s ease;"/>
            </svg>
            <img src="{{ $employee->avatar_url }}" alt="{{ $dispName }}">
            <span class="status-badge-dot" style="background:{{ $statusColor }};"></span>
        </div>

        {{-- Info --}}
        <div class="flex-grow-1">
            <div class="hero-name">{{ $dispName }}</div>
            @if($nameAr && $nameEn && $nameAr !== $nameEn)
                <div class="hero-sub mb-1">{{ $locale === 'ar' ? $nameEn : $nameAr }}</div>
            @endif
            <div class="d-flex flex-wrap gap-2 mt-2">
                <span class="hero-chip">
                    <i class="bx bx-briefcase font-13"></i>{{ $jobTitle }}
                </span>
                <span class="hero-chip">
                    <i class="bx bx-sitemap font-13"></i>{{ $department }}
                </span>
                <span class="hero-chip">
                    <i class="bx bx-building font-13"></i>{{ $branch }}
                </span>
                <span class="hero-chip" style="background:{{ $statusBg }}; color:{{ $statusColor }}; border-color:{{ $statusColor }}40;">
                    <span style="width:7px;height:7px;border-radius:50%;background:{{ $statusColor }};display:inline-block;"></span>
                    {{ $statusEnum?->label() ?? '-' }}
                </span>
            </div>
            @if($employee->hire_date || $kpis['service_years'] !== null)
            <div class="mt-2 d-flex flex-wrap gap-3" style="font-size:.78rem;opacity:.65;">
                @if($employee->hire_date)
                    <span><i class="bx bx-calendar me-1"></i>{{ __('Hired') }}: {{ $employee->hire_date->format('Y-m-d') }}</span>
                @endif
                @if($kpis['service_years'] !== null)
                    <span><i class="bx bx-time-five me-1"></i>{{ $kpis['service_years'] }} {{ __('yrs') }}{{ $kpis['service_months'] ? ' '.$kpis['service_months'].' '.__('mos') : '' }}</span>
                @endif
                <span class=""><i class="bx bx-hash me-1"></i>{{ $employee->employee_code }}</span>
            </div>
            @endif
        </div>

        {{-- Completion Circle --}}
        <div class="completion-circle text-center no-print">
            <div class="pct" style="color:{{ $ringColor }};">{{ $percent }}%</div>
            <small>{{ __('Profile Completion') }}</small>
        </div>
    </div>
</div>

{{-- =================== STATS SUMMARY BAR =================== --}}
<div class="stats-summary-card">
    <div class="stats-summary-grid">
        {{-- Stat 1: Basic Salary --}}
        <div class="stat-summary-item">
            <div class="stat-summary-header">
                <span class="stat-summary-icon text-success"><i class="bx bx-wallet"></i></span>
                <span class="stat-summary-label">{{ __('Basic Salary') }}</span>
            </div>
            <div class="stat-summary-val-wrap">
                <span class="stat-summary-val">{{ number_format($kpis['basic_salary'], 3) }}</span>
                <span class="stat-summary-unit">{{ __('KWD') }}</span>
            </div>
            <div class="stat-summary-sub">{{ __('Monthly Base') }}</div>
        </div>

        {{-- Stat 2: Active Allowances --}}
        <div class="stat-summary-item">
            <div class="stat-summary-header">
                <span class="stat-summary-icon text-warning"><i class="bx bx-layer-plus"></i></span>
                <span class="stat-summary-label">{{ __('Active Allowances') }}</span>
            </div>
            <div class="stat-summary-val-wrap">
                <span class="stat-summary-val">{{ number_format($allowTotal, 3) }}</span>
                <span class="stat-summary-unit">{{ __('KWD') }}</span>
            </div>
            <div class="stat-summary-sub">{{ $activeAllowances->count() }} {{ __('items') }}</div>
        </div>

        {{-- Stat 3: Attendance This Month --}}
        <div class="stat-summary-item">
            <div class="stat-summary-header">
                <span class="stat-summary-icon text-info"><i class="bx bx-calendar-check"></i></span>
                <span class="stat-summary-label">{{ __('Attendance This Month') }}</span>
            </div>
            <div class="stat-summary-val-wrap">
                <span class="stat-summary-val">{{ $kpis['attendance_days'] }}</span>
                <span class="stat-summary-unit">{{ __('Days') }}</span>
            </div>
            <div class="stat-summary-sub">{{ __('This Month') }}</div>
        </div>

        {{-- Stat 4: Rewards YTD --}}
        <div class="stat-summary-item">
            <div class="stat-summary-header">
                <span class="stat-summary-icon text-primary"><i class="bx bx-trophy"></i></span>
                <span class="stat-summary-label">{{ __('Rewards YTD') }}</span>
            </div>
            <div class="stat-summary-val-wrap">
                <span class="stat-summary-val">{{ number_format($kpis['total_rewards_ytd'], 3) }}</span>
                <span class="stat-summary-unit">{{ __('KWD') }}</span>
            </div>
            <div class="stat-summary-sub">{{ __('This Year') }}</div>
        </div>
    </div>
</div>

{{-- =================== MAIN LAYOUT =================== --}}
<div class="profile-layout">

    {{-- ===== LEFT SIDEBAR ===== --}}
    <div class="sidebar-card">

        {{-- Contact --}}
        <div class="sidebar-section">
            <div class="sidebar-section-title"><i class="bx bx-phone font-14"></i>{{ __('Contact') }}</div>
            @if($employee->phone)
            <div class="sb-row">
                <div class="sb-icon" style="background:#ecfdf5;color:#10b981;"><i class="bx bx-phone"></i></div>
                <div>
                    <div class="sb-label">{{ __('Mobile') }}</div>
                    <a href="tel:{{ $employee->phone }}" class="sb-value text-decoration-none" dir="ltr" style="color:var(--p-slate2);">{{ $employee->phone }}</a>
                </div>
            </div>
            @endif
            @if($employee->alternate_phone)
            <div class="sb-row">
                <div class="sb-icon" style="background:#f0fdfa;color:#0d9488;"><i class="bx bx-phone-incoming"></i></div>
                <div>
                    <div class="sb-label">{{ __('Alternate Phone') }}</div>
                    <div class="sb-value" dir="ltr">{{ $employee->alternate_phone }}</div>
                </div>
            </div>
            @endif
            @if($employee->email)
            <div class="sb-row">
                <div class="sb-icon" style="background:#fffbeb;color:#f59e0b;"><i class="bx bx-envelope"></i></div>
                <div>
                    <div class="sb-label">{{ __('Email') }}</div>
                    <a href="mailto:{{ $employee->email }}" class="sb-value text-decoration-none" style="color:var(--p-slate2);">{{ $employee->email }}</a>
                </div>
            </div>
            @endif
        </div>

        {{-- Quick Info --}}
        <div class="sidebar-section">
            <div class="sidebar-section-title"><i class="bx bx-id-card font-14"></i>{{ __('Quick Info') }}</div>
            @if($employee->employment_type)
            <div class="sb-row">
                <div class="sb-icon" style="background:#f5f3ff;color:#7c3aed;"><i class="bx bx-briefcase"></i></div>
                <div>
                    <div class="sb-label">{{ __('Employment Type') }}</div>
                    <div class="sb-value">{{ $employee->employment_type->label() }}</div>
                </div>
            </div>
            @endif
            @if($manager)
            <div class="sb-row">
                <div class="sb-icon" style="background:#f0fdfa;color:#0d9488;"><i class="bx bx-user-check"></i></div>
                <div>
                    <div class="sb-label">{{ __('Manager') }}</div>
                    <div class="sb-value">{{ $manager }}</div>
                </div>
            </div>
            @endif
            @if($employee->workSchedule)
            <div class="sb-row">
                <div class="sb-icon" style="background:#fffbeb;color:#f59e0b;"><i class="bx bx-time"></i></div>
                <div>
                    <div class="sb-label">{{ __('Work Schedule') }}</div>
                    <div class="sb-value">{{ $employee->workSchedule?->translate($locale)?->name ?? $employee->workSchedule?->translate('ar')?->name ?? '-' }}</div>
                </div>
            </div>
            @endif
            @if($employee->shift)
            <div class="sb-row">
                <div class="sb-icon" style="background:#ecfdf5;color:#10b981;"><i class="bx bx-rotate-left"></i></div>
                <div>
                    <div class="sb-label">{{ __('Shift') }}</div>
                    <div class="sb-value">{{ $employee->shift?->translate($locale)?->name ?? $employee->shift?->translate('ar')?->name ?? '-' }}</div>
                </div>
            </div>
            @endif
        </div>

        {{-- Activity Summary --}}
        <div class="sidebar-section">
            <div class="sidebar-section-title"><i class="bx bx-bar-chart-alt-2 font-14"></i>{{ __('Activity Summary') }}</div>
            <div class="d-flex flex-column gap-2">
                <div class="d-flex align-items-center justify-content-between py-1 border-bottom border-light">
                    <span class="d-flex align-items-center gap-2" style="font-size:.82rem;color:var(--p-slate2);">
                        <i class="bx bx-file text-secondary font-15"></i>{{ __('Documents') }}
                    </span>
                    <span class="badge bg-light text-secondary rounded-pill fw-bold" style="font-size:.75rem;">{{ $employee->documents_count ?? 0 }}</span>
                </div>
                <div class="d-flex align-items-center justify-content-between py-1 border-bottom border-light">
                    <span class="d-flex align-items-center gap-2" style="font-size:.82rem;color:var(--p-slate2);">
                        <i class="bx bx-calendar-event text-warning font-15"></i>{{ __('Leave Requests') }}
                    </span>
                    <span class="badge bg-light text-secondary rounded-pill fw-bold" style="font-size:.75rem;">{{ $employee->leaveRequests_count ?? 0 }}</span>
                </div>
                <div class="d-flex align-items-center justify-content-between py-1 border-bottom border-light">
                    <span class="d-flex align-items-center gap-2" style="font-size:.82rem;color:var(--p-slate2);">
                        <i class="bx bx-credit-card text-info font-15"></i>{{ __('Loans') }}
                    </span>
                    <span class="badge bg-light text-secondary rounded-pill fw-bold" style="font-size:.75rem;">{{ $employee->loans_count ?? 0 }}</span>
                </div>
                <div class="d-flex align-items-center justify-content-between py-1 border-bottom border-light">
                    <span class="d-flex align-items-center gap-2" style="font-size:.82rem;color:var(--p-slate2);">
                        <i class="bx bx-trophy text-success font-15"></i>{{ __('Rewards') }}
                    </span>
                    <span class="badge bg-light text-secondary rounded-pill fw-bold" style="font-size:.75rem;">{{ $employee->rewards_count ?? 0 }}</span>
                </div>
                <div class="d-flex align-items-center justify-content-between py-1">
                    <span class="d-flex align-items-center gap-2" style="font-size:.82rem;color:var(--p-slate2);">
                        <i class="bx bx-shield-x text-danger font-15"></i>{{ __('Penalties') }}
                    </span>
                    <span class="badge bg-light text-secondary rounded-pill fw-bold" style="font-size:.75rem;">{{ $employee->penalties_count ?? 0 }}</span>
                </div>
            </div>
        </div>

    </div>

    {{-- ===== MAIN CONTENT TABS ===== --}}
    <div class="tabs-card">
        <nav class="profile-nav" id="profileNav">
            <button class="pnav-item active" data-tab="tab-personal"><i class="bx bx-user"></i>{{ __('Personal') }}</button>
            <button class="pnav-item" data-tab="tab-financial"><i class="bx bx-wallet"></i>{{ __('Financial') }}</button>
            <button class="pnav-item" data-tab="tab-attendance"><i class="bx bx-time-five"></i>{{ __('Attendance & Leaves') }}</button>
            <button class="pnav-item" data-tab="tab-payroll"><i class="bx bx-credit-card"></i>{{ __('Payroll & Loans') }}</button>
            <button class="pnav-item" data-tab="tab-documents"><i class="bx bx-folder"></i>{{ __('Documents') }}@if($employee->documents_count) <span style="background:#1e293b;color:#fff;border-radius:10px;padding:0 6px;font-size:.65rem;font-weight:700;margin-inline-start:.25rem;">{{ $employee->documents_count }}</span>@endif</button>
        </nav>

        <div class="tabs-body">

            {{-- ========== TAB 1: PERSONAL ========== --}}
            <div class="tab-pane-content" id="tab-personal">

                {{-- Subcard 1: Personal Details --}}
                <div class="profile-subcard">
                    <div class="profile-subcard-header">
                        <h6 class="profile-subcard-title"><i class="bx bx-user"></i>{{ __('Personal & Demographic Details') }}</h6>
                    </div>
                    <div class="profile-subcard-body">
                        <div class="dl-grid">
                            <div class="dl-item">
                                <span class="dl-label">{{ __('Full Name (Arabic)') }}</span>
                                <span class="dl-value">{{ $nameAr ?: '—' }}</span>
                            </div>
                            <div class="dl-item">
                                <span class="dl-label">{{ __('Full Name (English)') }}</span>
                                <span class="dl-value">{{ $nameEn ?: '—' }}</span>
                            </div>
                            <div class="dl-item">
                                <span class="dl-label">{{ __('Gender') }}</span>
                                <span class="dl-value">{{ $isMale ? __('Male') : __('Female') }}</span>
                            </div>
                            <div class="dl-item">
                                <span class="dl-label">{{ __('Date of Birth') }}</span>
                                <span class="dl-value">
                                    {{ $employee->date_of_birth ? $employee->date_of_birth->format('Y-m-d') : '—' }}
                                    @if($employee->date_of_birth)
                                        <small class="text-muted fw-normal ms-1">({{ $employee->date_of_birth->age }} {{ __('yrs') }})</small>
                                    @endif
                                </span>
                            </div>
                            <div class="dl-item">
                                <span class="dl-label">{{ __('Nationality') }}</span>
                                <span class="dl-value">{{ $employee->nationality ?: '—' }}</span>
                            </div>
                            <div class="dl-item">
                                <span class="dl-label">{{ __('Marital Status') }}</span>
                                <span class="dl-value">{{ $employee->marital_status ?: '—' }}</span>
                            </div>
                            <div class="dl-item">
                                <span class="dl-label">{{ __('Religion') }}</span>
                                <span class="dl-value">{{ $employee->religion ?: '—' }}</span>
                            </div>
                            <div class="dl-item">
                                <span class="dl-label">{{ __('Blood Type') }}</span>
                                <span class="dl-value mono">{{ $employee->blood_type ?: '—' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Subcard 2: Identity & Official Documents --}}
                <div class="profile-subcard">
                    <div class="profile-subcard-header">
                        <h6 class="profile-subcard-title"><i class="bx bx-id-card"></i>{{ __('Identity & Legal Documents') }}</h6>
                    </div>
                    <div class="profile-subcard-body">
                        <div class="dl-grid">
                            <div class="dl-item">
                                <span class="dl-label">{{ __('Civil ID / National ID') }}</span>
                                <span class="dl-value mono">{{ $employee->national_id ?: '—' }}</span>
                            </div>
                            <div class="dl-item">
                                <span class="dl-label">{{ __('Passport Number') }}</span>
                                <span class="dl-value mono">{{ $employee->passport_number ?: '—' }}</span>
                            </div>
                            <div class="dl-item">
                                <span class="dl-label">{{ __('Passport Issue Date') }}</span>
                                <span class="dl-value">{{ $employee->passport_issue_date ? $employee->passport_issue_date->format('Y-m-d') : '—' }}</span>
                            </div>
                            <div class="dl-item">
                                <span class="dl-label">{{ __('Passport Expiry Date') }}</span>
                                <span class="dl-value {{ $employee->passport_expiry_date?->isPast() ? 'expiry-danger' : ($employee->passport_expiry_date?->diffInDays() < 90 ? 'expiry-warn' : '') }}">
                                    {{ $employee->passport_expiry_date ? $employee->passport_expiry_date->format('Y-m-d') : '—' }}
                                    @if($employee->passport_expiry_date?->isPast())
                                        <span class="badge bg-label-danger ms-1 font-11">{{ __('Expired') }}</span>
                                    @elseif($employee->passport_expiry_date && $employee->passport_expiry_date->diffInDays() < 90)
                                        <span class="badge bg-label-warning ms-1 font-11">{{ __('Soon') }}</span>
                                    @endif
                                </span>
                            </div>
                            <div class="dl-item">
                                <span class="dl-label">{{ __('Visa Type') }}</span>
                                <span class="dl-value">{{ $employee->visaType?->translate($locale)?->name ?? $employee->visaType?->translate('ar')?->name ?? '—' }}</span>
                            </div>
                            <div class="dl-item">
                                <span class="dl-label">{{ __('Visa Expiry Date') }}</span>
                                <span class="dl-value {{ $employee->visa_expiry_date?->isPast() ? 'expiry-danger' : ($employee->visa_expiry_date?->diffInDays() < 60 ? 'expiry-warn' : '') }}">
                                    {{ $employee->visa_expiry_date ? $employee->visa_expiry_date->format('Y-m-d') : '—' }}
                                    @if($employee->visa_expiry_date?->isPast())
                                        <span class="badge bg-label-danger ms-1 font-11">{{ __('Expired') }}</span>
                                    @elseif($employee->visa_expiry_date && $employee->visa_expiry_date->diffInDays() < 60)
                                        <span class="badge bg-label-warning ms-1 font-11">{{ __('Soon') }}</span>
                                    @endif
                                </span>
                            </div>
                            <div class="dl-item">
                                <span class="dl-label">{{ __('Social Insurance No.') }}</span>
                                <span class="dl-value mono">{{ $employee->social_insurance_number ?: '—' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Subcard 3: Address & Emergency Contact --}}
                <div class="profile-subcard">
                    <div class="profile-subcard-header">
                        <h6 class="profile-subcard-title"><i class="bx bx-map-pin"></i>{{ __('Address & Emergency Contact') }}</h6>
                    </div>
                    <div class="profile-subcard-body">
                        <div class="dl-grid cols-3">
                            <div class="dl-item">
                                <span class="dl-label">{{ __('Governorate') }}</span>
                                <span class="dl-value">{{ $employee->governorate?->translate($locale)?->name ?? $employee->governorate?->translate('ar')?->name ?? '—' }}</span>
                            </div>
                            <div class="dl-item">
                                <span class="dl-label">{{ __('City') }}</span>
                                <span class="dl-value">{{ $employee->city ?: '—' }}</span>
                            </div>
                            <div class="dl-item">
                                <span class="dl-label">{{ __('Address') }}</span>
                                <span class="dl-value">{{ $employee->translate($locale)?->address_1 ?? $employee->translate('ar')?->address_1 ?? '—' }}</span>
                            </div>
                            <div class="dl-item">
                                <span class="dl-label">{{ __('Emergency Contact') }}</span>
                                <span class="dl-value">{{ $employee->emergency_contact_name ?: '—' }}</span>
                            </div>
                            <div class="dl-item">
                                <span class="dl-label">{{ __('Relationship') }}</span>
                                <span class="dl-value">{{ $employee->emergency_contact_relation ?: '—' }}</span>
                            </div>
                            <div class="dl-item">
                                <span class="dl-label">{{ __('Phone') }}</span>
                                <span class="dl-value">
                                    @if($employee->emergency_contact_phone)
                                        <a href="tel:{{ $employee->emergency_contact_phone }}" class="text-decoration-none mono" style="color: var(--p-slate);" dir="ltr">{{ $employee->emergency_contact_phone }}</a>
                                    @else
                                        —
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Subcard 4: Contract & Employment Details --}}
                <div class="profile-subcard">
                    <div class="profile-subcard-header">
                        <h6 class="profile-subcard-title"><i class="bx bx-calendar"></i>{{ __('Contract & Employment Details') }}</h6>
                    </div>
                    <div class="profile-subcard-body">
                        <div class="dl-grid cols-3">
                            <div class="dl-item">
                                <span class="dl-label">{{ __('Hire Date') }}</span>
                                <span class="dl-value">{{ $employee->hire_date ? $employee->hire_date->format('Y-m-d') : '—' }}</span>
                            </div>
                            <div class="dl-item">
                                <span class="dl-label">{{ __('Probation End Date') }}</span>
                                <span class="dl-value">{{ $employee->probation_end_date ? $employee->probation_end_date->format('Y-m-d') : '—' }}</span>
                            </div>
                            <div class="dl-item">
                                <span class="dl-label">{{ __('Contract End Date') }}</span>
                                <span class="dl-value">{{ $employee->contract_end_date ? $employee->contract_end_date->format('Y-m-d') : '—' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ========== TAB 2: FINANCIAL ========== --}}
            <div class="tab-pane-content d-none" id="tab-financial">

                {{-- Subcard 1: Salary & Compensation --}}
                <div class="profile-subcard">
                    <div class="profile-subcard-header">
                        <h6 class="profile-subcard-title"><i class="bx bx-wallet"></i>{{ __('Salary & Compensation') }}</h6>
                    </div>
                    <div class="profile-subcard-body">
                        <div class="dl-grid">
                            <div class="dl-item">
                                <span class="dl-label">{{ __('Basic Salary') }}</span>
                                <span class="dl-value" style="font-size: 1.15rem; color: #10b981; font-weight: 800;">
                                    {{ number_format($kpis['basic_salary'], 3) }} <small class="text-muted font-12 fw-normal">{{ __('KWD') }}</small>
                                </span>
                            </div>
                            <div class="dl-item">
                                <span class="dl-label">{{ __('Hourly Rate') }}</span>
                                <span class="dl-value">{{ $employee->hourly_rate ? number_format($employee->hourly_rate, 3) . ' ' . __('KWD') : '—' }}</span>
                            </div>
                            <div class="dl-item">
                                <span class="dl-label">{{ __('Daily Working Hours') }}</span>
                                <span class="dl-value">{{ $employee->daily_working_hours ? $employee->daily_working_hours . ' ' . __('Hours') : '—' }}</span>
                            </div>
                            <div class="dl-item">
                                <span class="dl-label">{{ __('Social Insurance %') }}</span>
                                <span class="dl-value">{{ $employee->social_insurance_percentage ? $employee->social_insurance_percentage . '%' : '—' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Subcard 2: Bank Account Details --}}
                <div class="profile-subcard">
                    <div class="profile-subcard-header">
                        <h6 class="profile-subcard-title"><i class="bx bx-bank"></i>{{ __('Bank Account Details') }}</h6>
                    </div>
                    <div class="profile-subcard-body">
                        <div class="dl-grid">
                            <div class="dl-item">
                                <span class="dl-label">{{ __('Bank') }}</span>
                                <span class="dl-value">{{ $employee->bank?->translate($locale)?->name ?? $employee->bank?->name ?? '—' }}</span>
                            </div>
                            <div class="dl-item">
                                <span class="dl-label">{{ __('Account Holder') }}</span>
                                <span class="dl-value">{{ $employee->account_holder_name ?: '—' }}</span>
                            </div>
                            <div class="dl-item">
                                <span class="dl-label">{{ __('Account Number') }}</span>
                                <span class="dl-value mono">{{ $employee->account_number ?: '—' }}</span>
                            </div>
                            <div class="dl-item">
                                <span class="dl-label">{{ __('IBAN') }}</span>
                                <span class="dl-value mono" dir="ltr">{{ $employee->iban ?: '—' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Subcard 3: Active Allowances --}}
                <div class="profile-subcard">
                    <div class="profile-subcard-header">
                        <h6 class="profile-subcard-title"><i class="bx bx-layer-plus"></i>{{ __('Active Allowances') }}</h6>
                        @if($activeAllowances->isNotEmpty())
                            <span class="badge bg-label-warning fw-semibold font-12">{{ $activeAllowances->count() }} {{ __('items') }}</span>
                        @endif
                    </div>
                    <div class="profile-subcard-body p-0">
                        @if($activeAllowances->isNotEmpty())
                            <div class="table-responsive">
                                <table class="rec-table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Allowance') }}</th>
                                            <th>{{ __('Effective Date') }}</th>
                                            <th class="text-end">{{ __('Monthly Amount') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($activeAllowances as $al)
                                        <tr>
                                            <td class="fw-semibold">{{ $al->allowanceType?->translate($locale)?->name ?? $al->allowanceType?->translate('ar')?->name ?? __('Allowance') }}</td>
                                            <td class="mono text-muted">{{ $al->effective_date ?? '—' }}</td>
                                            <td class="text-end fw-bold" style="color: #10b981;">{{ number_format($al->amount, 3) }} {{ __('KWD') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr style="background: #f8fafc; border-top: 2px solid #e2e8f0;">
                                            <td colspan="2" class="fw-bold">{{ __('Total Active Allowances') }}</td>
                                            <td class="text-end fw-bolder font-15" style="color: #10b981;">{{ number_format($allowTotal, 3) }} {{ __('KWD') }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @else
                            <div class="empty-state">
                                <i class="bx bx-info-circle"></i>
                                <p>{{ __('No active allowances') }}</p>
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            {{-- ========== TAB 3: ATTENDANCE & LEAVES ========== --}}
            <div class="tab-pane-content d-none" id="tab-attendance">

                {{-- Subcard 1: Recent Attendance --}}
                <div class="profile-subcard">
                    <div class="profile-subcard-header">
                        <h6 class="profile-subcard-title"><i class="bx bx-time-five"></i>{{ __('Recent Attendance Log') }}</h6>
                        @if($employee->attendances->isNotEmpty())
                            <span class="badge bg-label-info fw-semibold font-12">{{ $employee->attendances->count() }}</span>
                        @endif
                    </div>
                    <div class="profile-subcard-body p-0">
                        @if($employee->attendances->isNotEmpty())
                            <div class="table-responsive">
                                <table class="rec-table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Date') }}</th>
                                            <th>{{ __('Check In') }}</th>
                                            <th>{{ __('Check Out') }}</th>
                                            <th>{{ __('Hours') }}</th>
                                            <th>{{ __('Late (min)') }}</th>
                                            <th>{{ __('Status') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($employee->attendances as $att)
                                        @php $attS = $att->status; @endphp
                                        <tr>
                                            <td class="mono fw-semibold">{{ $att->date?->format('Y-m-d') ?? '—' }}</td>
                                            <td class="mono">{{ $att->check_in ?? '—' }}</td>
                                            <td class="mono">{{ $att->check_out ?? '—' }}</td>
                                            <td>{{ $att->total_working_hours ? number_format($att->total_working_hours, 1) : '—' }}</td>
                                            <td>{{ $att->late_minutes ?: '—' }}</td>
                                            <td><span class="badge {{ $attS?->badgeClass() ?? 'bg-label-secondary' }}" style="font-size: .72rem;">{{ $attS?->label() ?? '—' }}</span></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="empty-state">
                                <i class="bx bx-calendar-x"></i>
                                <p>{{ __('No attendance records found') }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Subcard 2: Leave Requests History --}}
                <div class="profile-subcard">
                    <div class="profile-subcard-header">
                        <h6 class="profile-subcard-title"><i class="bx bx-door-open"></i>{{ __('Leave Requests History') }}</h6>
                        @if($employee->leaveRequests->isNotEmpty())
                            <span class="badge bg-label-warning fw-semibold font-12">{{ $employee->leaveRequests->count() }}</span>
                        @endif
                    </div>
                    <div class="profile-subcard-body p-0">
                        @if($employee->leaveRequests->isNotEmpty())
                            <div class="table-responsive">
                                <table class="rec-table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Type') }}</th>
                                            <th>{{ __('From') }}</th>
                                            <th>{{ __('Days') }}</th>
                                            <th>{{ __('Status') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($employee->leaveRequests as $leave)
                                        <tr>
                                            <td class="fw-semibold">{{ $leave->leave_type?->label() ?? '—' }}</td>
                                            <td class="mono text-muted">{{ $leave->start_date?->format('Y-m-d') ?? '—' }}</td>
                                            <td class="fw-bold">{{ $leave->days_count }}</td>
                                            <td><span class="badge {{ $leave->status?->badgeClass() ?? 'bg-label-secondary' }}" style="font-size: .72rem;">{{ $leave->status?->label() ?? '—' }}</span></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="empty-state">
                                <i class="bx bx-calendar"></i>
                                <p>{{ __('No leave requests') }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Subcard 3: Permission Requests History --}}
                <div class="profile-subcard">
                    <div class="profile-subcard-header">
                        <h6 class="profile-subcard-title"><i class="bx bx-exit"></i>{{ __('Permission Requests History') }}</h6>
                        @if($employee->permissionRequests->isNotEmpty())
                            <span class="badge bg-label-secondary fw-semibold font-12">{{ $employee->permissionRequests->count() }}</span>
                        @endif
                    </div>
                    <div class="profile-subcard-body p-0">
                        @if($employee->permissionRequests->isNotEmpty())
                            <div class="table-responsive">
                                <table class="rec-table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Type') }}</th>
                                            <th>{{ __('Date') }}</th>
                                            <th>{{ __('Hours') }}</th>
                                            <th>{{ __('Status') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($employee->permissionRequests as $perm)
                                        <tr>
                                            <td class="fw-semibold">{{ $perm->type?->label() ?? '—' }}</td>
                                            <td class="mono text-muted">{{ $perm->date?->format('Y-m-d') ?? '—' }}</td>
                                            <td>{{ $perm->hours_count }}</td>
                                            <td><span class="badge {{ $perm->status?->badgeClass() ?? 'bg-label-secondary' }}" style="font-size: .72rem;">{{ $perm->status?->label() ?? '—' }}</span></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="empty-state">
                                <i class="bx bx-time"></i>
                                <p>{{ __('No permission requests') }}</p>
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            {{-- ========== TAB 4: PAYROLL & LOANS ========== --}}
            <div class="tab-pane-content d-none" id="tab-payroll">

                {{-- Subcard 1: Payroll History --}}
                <div class="profile-subcard">
                    <div class="profile-subcard-header">
                        <h6 class="profile-subcard-title"><i class="bx bx-credit-card"></i>{{ __('Payroll History') }}</h6>
                    </div>
                    <div class="profile-subcard-body p-0">
                        @if($employee->payrollItems->isNotEmpty())
                            <div class="table-responsive">
                                <table class="rec-table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Payroll No.') }}</th>
                                            <th>{{ __('Payroll Month') }}</th>
                                            <th>{{ __('Basic') }}</th>
                                            <th>{{ __('Gross') }}</th>
                                            <th>{{ __('Deductions') }}</th>
                                            <th style="color: #10b981;">{{ __('Net') }}</th>
                                            <th>{{ __('Payment Status') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($employee->payrollItems as $pi)
                                        <tr>
                                            <td><a href="{{ route('hr.payrolls.show', $pi->payroll_id) }}" class="fw-bold mono text-decoration-none" style="color: var(--p-slate);">{{ $pi->payroll?->payroll_number ?? '#'.$pi->payroll_id }}</a></td>
                                            <td class="mono text-muted">{{ $pi->payroll?->payroll_month ?? '—' }}</td>
                                            <td>{{ number_format($pi->basic_salary, 3) }}</td>
                                            <td>{{ number_format($pi->gross_salary, 3) }}</td>
                                            <td style="color: #f43f5e;">{{ number_format($pi->total_deductions, 3) }}</td>
                                            <td style="color: #10b981; font-weight: 800;">{{ number_format($pi->net_salary, 3) }}</td>
                                            <td><span class="fw-semibold {{ $pi->payment_status?->textColor() ?? 'text-muted' }}" style="font-size: .8rem;">{{ $pi->payment_status?->label() ?? '—' }}</span></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="empty-state">
                                <i class="bx bx-credit-card"></i>
                                <p>{{ __('No payroll records found') }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Subcard 2: Loans & Advances --}}
                <div class="profile-subcard">
                    <div class="profile-subcard-header">
                        <h6 class="profile-subcard-title"><i class="bx bx-money-withdraw"></i>{{ __('Loans & Advances') }}</h6>
                    </div>
                    <div class="profile-subcard-body p-0">
                        @if($employee->loans->isNotEmpty())
                            <div class="table-responsive">
                                <table class="rec-table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Type') }}</th>
                                            <th>{{ __('Loan Amount') }}</th>
                                            <th>{{ __('Remaining') }}</th>
                                            <th>{{ __('Status') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($employee->loans as $loan)
                                        <tr>
                                            <td class="fw-semibold">{{ $loan->type?->label() ?? '—' }}</td>
                                            <td class="mono">{{ number_format($loan->amount, 3) }} {{ __('KWD') }}</td>
                                            <td class="mono fw-bold" style="color: #f59e0b;">{{ number_format($loan->remaining_amount, 3) }} {{ __('KWD') }}</td>
                                            <td><span class="badge {{ $loan->status?->badgeClass() ?? 'bg-label-secondary' }}" style="font-size: .72rem;">{{ $loan->status?->label() ?? '—' }}</span></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="empty-state">
                                <i class="bx bx-wallet"></i>
                                <p>{{ __('No loans') }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Subcard 3: Rewards & Penalties --}}
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="profile-subcard h-100">
                            <div class="profile-subcard-header">
                                <h6 class="profile-subcard-title"><i class="bx bx-trophy text-success"></i>{{ __('Rewards & Incentives') }}</h6>
                            </div>
                            <div class="profile-subcard-body">
                                @if($employee->rewards->isNotEmpty())
                                    @foreach($employee->rewards as $rw)
                                    <div class="allowance-list-item">
                                        <div>
                                            <div style="font-size: .84rem; font-weight: 600; color: var(--p-slate);">{{ $rw->rewardType?->translate($locale)?->name ?? $rw->rewardType?->translate('ar')?->name ?? __('Reward') }}</div>
                                            <small class="text-muted font-11 mono">{{ $rw->award_date?->format('Y-m-d') ?? '—' }}</small>
                                        </div>
                                        <div class="text-end">
                                            <div style="font-size: .9rem; font-weight: 800; color: #10b981;">{{ number_format($rw->amount, 3) }} {{ __('KWD') }}</div>
                                            <span class="fw-semibold {{ $rw->status?->textColor() ?? 'text-muted' }}" style="font-size: .74rem;">{{ $rw->status?->label() ?? '—' }}</span>
                                        </div>
                                    </div>
                                    @endforeach
                                @else
                                    <div class="empty-state py-3">
                                        <i class="bx bx-gift"></i>
                                        <p>{{ __('No rewards') }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="profile-subcard h-100">
                            <div class="profile-subcard-header">
                                <h6 class="profile-subcard-title"><i class="bx bx-shield-x text-danger"></i>{{ __('Penalties & Violations') }}</h6>
                            </div>
                            <div class="profile-subcard-body">
                                @if($employee->penalties->isNotEmpty())
                                    @foreach($employee->penalties as $pen)
                                    <div class="allowance-list-item">
                                        <div>
                                            <div style="font-size: .84rem; font-weight: 600; color: var(--p-slate);">{{ $pen->penaltyType?->translate($locale)?->name ?? $pen->penaltyType?->translate('ar')?->name ?? __('Penalty') }}</div>
                                            <small class="text-muted font-11 mono">{{ $pen->penalty_date?->format('Y-m-d') ?? '—' }}</small>
                                        </div>
                                        <div class="text-end">
                                            <div style="font-size: .9rem; font-weight: 800; color: #f43f5e;">{{ number_format($pen->amount, 3) }} {{ __('KWD') }}</div>
                                            <span class="fw-semibold {{ $pen->status?->textColor() ?? 'text-muted' }}" style="font-size: .74rem;">{{ $pen->status?->label() ?? '—' }}</span>
                                        </div>
                                    </div>
                                    @endforeach
                                @else
                                    <div class="empty-state py-3">
                                        <i class="bx bx-shield"></i>
                                        <p>{{ __('No penalties') }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ========== TAB 5: DOCUMENTS ========== --}}
            <div class="tab-pane-content d-none" id="tab-documents">
                <div class="profile-subcard">
                    <div class="profile-subcard-header">
                        <h6 class="profile-subcard-title"><i class="bx bx-folder-open"></i>{{ __('Uploaded Documents') }}</h6>
                        @if($employee->documents->isNotEmpty())
                            <span class="badge bg-label-secondary fw-semibold font-12">{{ $employee->documents->count() }}</span>
                        @endif
                    </div>
                    <div class="profile-subcard-body">
                        @if($employee->documents->isNotEmpty())
                            <div class="row g-3">
                                @foreach($employee->documents as $doc)
                                    @php
                                        $isExpired = $doc->expiry_date && \Carbon\Carbon::parse($doc->expiry_date)->isPast();
                                        $nearExp   = !$isExpired && $doc->expiry_date && \Carbon\Carbon::parse($doc->expiry_date)->diffInDays() < 60;
                                    @endphp
                                    <div class="col-sm-6 col-lg-4">
                                        <div class="doc-card">
                                            <div class="doc-icon" style="background:{{ $isExpired ? '#fff1f2' : '#f8fafc' }}; color:{{ $isExpired ? '#f43f5e' : '#64748b' }}; border: 1px solid {{ $isExpired ? '#fecdd3' : '#e2e8f0' }};">
                                                <i class="bx bx-file"></i>
                                            </div>
                                            <div class="flex-grow-1 overflow-hidden">
                                                <div style="font-size: .86rem; font-weight: 700; color: var(--p-slate); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                    {{ $doc->documentType?->translate($locale)?->name ?? $doc->documentType?->translate('ar')?->name ?? __('Document') }}
                                                </div>
                                                @if($doc->document_number)
                                                    <div class="mono text-muted font-12 mt-1">{{ $doc->document_number }}</div>
                                                @endif
                                                @if($doc->expiry_date)
                                                    <div class="mt-1 {{ $isExpired ? 'expiry-danger' : ($nearExp ? 'expiry-warn' : 'text-muted') }}" style="font-size: .74rem; display: flex; align-items: center; gap: .3rem;">
                                                        <i class="bx {{ $isExpired ? 'bx-error-circle' : 'bx-calendar' }}"></i>
                                                        <span>{{ __('Expires') }}: {{ \Carbon\Carbon::parse($doc->expiry_date)->format('Y-m-d') }}</span>
                                                        @if($isExpired)
                                                            <span class="badge bg-label-danger font-10 px-1 py-0">{{ __('Expired') }}</span>
                                                        @elseif($nearExp)
                                                            <span class="badge bg-label-warning font-10 px-1 py-0">{{ __('Soon') }}</span>
                                                        @endif
                                                    </div>
                                                @endif
                                                @if($doc->file_path)
                                                    <a href="{{ \Illuminate\Support\Facades\Storage::url($doc->file_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary mt-2 d-inline-flex align-items-center gap-1 font-12" style="padding: .25rem .65rem;">
                                                        <i class="bx bx-download font-14"></i>
                                                        <span>{{ __('Download') }}</span>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="empty-state">
                                <i class="bx bx-folder-open"></i>
                                <p>{{ __('No documents uploaded') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>{{-- end tabs-body --}}
    </div>{{-- end tabs-card --}}
</div>{{-- end profile-layout --}}

@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Simple tab switching
    const navBtns  = document.querySelectorAll('#profileNav .pnav-item');
    const tabPanes = document.querySelectorAll('.tab-pane-content');

    navBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            navBtns.forEach(b => b.classList.remove('active'));
            tabPanes.forEach(p => p.classList.add('d-none'));
            this.classList.add('active');
            const target = document.getElementById(this.dataset.tab);
            if (target) target.classList.remove('d-none');
        });
    });
});
</script>
@endsection
