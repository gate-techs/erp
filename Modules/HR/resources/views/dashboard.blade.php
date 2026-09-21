@extends('layouts/contentNavbarLayout')

@section('title', __('Dashboard') . ' - ' . config('variables.templateName'))

@section('vendor-style')
@vite('resources/assets/vendor/libs/apex-charts/apex-charts.scss')
<style>
    :root {
        --odoo-purple: #714B67;
        --odoo-teal: #00A09D;
        --odoo-dark: #1e293b;
        --odoo-border: #e2e8f0;
    }
    .hr-header-box {
        background: #ffffff;
        border: 1px solid var(--odoo-border) !important;
        border-radius: 12px;
    }
    .hr-stat-card {
        background: #ffffff;
        border: 1px solid var(--odoo-border) !important;
        border-radius: 12px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        position: relative;
        overflow: hidden;
    }
    .hr-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05) !important;
    }
    .border-top-accent-amber  { border-top: 3px solid #f59e0b !important; }
    .border-top-accent-teal   { border-top: 3px solid #00A09D !important; }
    .border-top-accent-purple { border-top: 3px solid #714B67 !important; }
    .border-top-accent-green  { border-top: 3px solid #10b981 !important; }
    .border-top-accent-rose   { border-top: 3px solid #ef4444 !important; }

    .stat-icon-wrapper {
        width: 46px;
        height: 46px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .hr-odoo-card {
        background: #ffffff;
        border: 1px solid var(--odoo-border) !important;
        border-radius: 12px;
    }
    .table-clean th {
        font-size: 11.5px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        background-color: #f8fafc !important;
        border-bottom: 1px solid var(--odoo-border) !important;
        padding: 11px 14px !important;
    }
    .table-clean td {
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9 !important;
        padding: 11px 14px !important;
    }
    .nav-tabs .nav-link {
        font-weight: 600;
        color: #64748b;
        border-radius: 8px 8px 0 0;
        padding: 10px 18px;
    }
    .nav-tabs .nav-link.active {
        color: var(--odoo-purple) !important;
        border-color: var(--odoo-border) var(--odoo-border) #fff !important;
        background-color: #fff;
    }
    .btn-odoo-primary {
        background-color: #714B67 !important;
        border-color: #714B67 !important;
        color: #ffffff !important;
    }
    .btn-odoo-primary:hover {
        background-color: #5c3c53 !important;
        border-color: #5c3c53 !important;
        color: #ffffff !important;
    }
</style>
@endsection

@section('vendor-script')
@vite('resources/assets/vendor/libs/apex-charts/apexcharts.js')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@endsection

@section('content')
<!-- 1. Header Action Bar (Strictly One Single Line) -->
<div class="hr-header-box px-4 py-3 shadow-sm mb-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap flex-lg-nowrap gap-3">
        <!-- Title & Subtitle on One Line -->
        <div class="d-flex align-items-center gap-3">
            <div class="avatar avatar-sm rounded-circle d-flex align-items-center justify-content-center" style="background-color: #f5eff3; color: #714B67;">
                <i class="bx bx-home-circle font-22"></i>
            </div>
            <div>
                <div class="d-flex align-items-center gap-2">
                    <h4 class="fw-bold text-heading mb-0">{{ __('Dashboard') }}</h4>
                    <span class="badge px-2 py-1 rounded-pill font-11" style="background-color: #f5eff3; color: #714B67; border: 1px solid #ebdbe5;">
                        {{ __('HR Operations') }}
                    </span>
                </div>
                <small class="text-muted d-block font-12 mt-1">
                    <i class="bx bx-calendar me-1"></i> {{ now()->translatedFormat('l، d F Y') }}
                </small>
            </div>
        </div>

        <!-- 4 Action Buttons strictly in one line (flex-nowrap) -->
        <div class="d-flex align-items-center flex-nowrap gap-2" style="white-space: nowrap;">
            <a href="{{ route('hr.attendances.index') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1 px-3 py-2 rounded-2">
                <i class="bx bx-time-five font-16"></i>
                <span class="font-13">{{ __('Record Attendance') }}</span>
            </a>
            <a href="{{ route('hr.leaves.index') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1 px-3 py-2 rounded-2">
                <i class="bx bx-calendar-event font-16"></i>
                <span class="font-13">{{ __('Leave Requests') }}</span>
            </a>
            <a href="{{ route('hr.job-requisitions.create') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1 px-3 py-2 rounded-2">
                <i class="bx bx-briefcase font-16"></i>
                <span class="font-13">{{ __('New Requisition') }}</span>
            </a>
            <a href="{{ route('hr.employees.create') }}" class="btn btn-sm btn-odoo-primary d-inline-flex align-items-center gap-1 px-3 py-2 rounded-2 shadow-sm">
                <i class="bx bx-user-plus font-16"></i>
                <span class="font-13 fw-semibold">{{ __('Add Employee') }}</span>
            </a>
        </div>
    </div>
</div>

<!-- 2. Primary KPI Executive Scorecards (Wider with Odoo Palette) -->
<div class="row g-3 mb-4">
    <!-- Today's Attendance -->
    <div class="col-12 col-sm-6 col-xl">
        <div class="card hr-stat-card border-top-accent-amber shadow-sm h-100 p-3">
            <div class="d-flex align-items-start justify-content-between mb-2">
                <div>
                    <span class="text-muted small fw-semibold d-block mb-1">{{ __('Today Attendance') }}</span>
                    <h3 class="fw-bold mb-0" style="color: #b45309;">{{ $attendanceRate }}%</h3>
                </div>
                <div class="stat-icon-wrapper" style="background-color: #fef8ee; color: #f59e0b;">
                    <i class="bx bx-time font-22"></i>
                </div>
            </div>
            <div class="pt-2 border-top d-flex align-items-center justify-content-between small text-muted">
                <span>{{ __('Present') }}: <strong class="text-heading">{{ $attendance['today_present'] }}</strong></span>
                <span>{{ __('Late') }}: <strong class="text-heading">{{ $attendance['today_late'] }}</strong></span>
                <span>{{ __('Absent') }}: <strong class="text-danger">{{ $attendance['today_absent'] }}</strong></span>
            </div>
        </div>
    </div>

    <!-- Leaves & Absences Today -->
    <div class="col-12 col-sm-6 col-xl">
        <div class="card hr-stat-card border-top-accent-teal shadow-sm h-100 p-3">
            <div class="d-flex align-items-start justify-content-between mb-2">
                <div>
                    <span class="text-muted small fw-semibold d-block mb-1">{{ __('Leaves & Absences') }}</span>
                    <h3 class="fw-bold mb-0" style="color: #008784;">{{ $attendance['on_leave_today_count'] }}</h3>
                </div>
                <div class="stat-icon-wrapper" style="background-color: #e6f6f6; color: #00A09D;">
                    <i class="bx bx-calendar-minus font-22"></i>
                </div>
            </div>
            <div class="pt-2 border-top d-flex align-items-center justify-content-between small text-muted">
                <span>{{ __('On Leave Today') }}: <strong class="text-heading">{{ $attendance['on_leave_today_count'] }}</strong></span>
                <span>{{ __('Pending Requests') }}: <strong style="color: #00A09D;">{{ $pendingActions['leaves']['count'] }}</strong></span>
            </div>
        </div>
    </div>

    <!-- Job Requisitions -->
    <div class="col-12 col-sm-6 col-xl">
        <div class="card hr-stat-card border-top-accent-purple shadow-sm h-100 p-3">
            <div class="d-flex align-items-start justify-content-between mb-2">
                <div>
                    <span class="text-muted small fw-semibold d-block mb-1">{{ __('Job Requisitions') }}</span>
                    <h3 class="fw-bold mb-0" style="color: #714B67;">{{ $requisitionMetrics['total'] }}</h3>
                </div>
                <div class="stat-icon-wrapper" style="background-color: #f5eff3; color: #714B67;">
                    <i class="bx bx-briefcase font-22"></i>
                </div>
            </div>
            <div class="pt-2 border-top d-flex align-items-center justify-content-between small text-muted">
                <span>{{ __('Pending Approval') }}: <strong class="text-warning">{{ $requisitionMetrics['pending_approval'] }}</strong></span>
                <span>{{ __('In Recruitment') }}: <strong style="color: #714B67;">{{ $requisitionMetrics['in_recruitment'] }}</strong></span>
            </div>
        </div>
    </div>

    <!-- Total Workforce -->
    <div class="col-12 col-sm-6 col-xl">
        <div class="card hr-stat-card border-top-accent-green shadow-sm h-100 p-3">
            <div class="d-flex align-items-start justify-content-between mb-2">
                <div>
                    <span class="text-muted small fw-semibold d-block mb-1">{{ __('Total Workforce') }}</span>
                    <h3 class="fw-bold mb-0 text-success">{{ $workforce['total_employees'] }}</h3>
                </div>
                <div class="stat-icon-wrapper" style="background-color: #ecfdf5; color: #10b981;">
                    <i class="bx bx-group font-22"></i>
                </div>
            </div>
            <div class="pt-2 border-top d-flex align-items-center justify-content-between small text-muted">
                <span>{{ __('Active') }}: <strong class="text-heading">{{ $workforce['active_employees'] }}</strong></span>
                <span>{{ __('Probation') }}: <strong class="text-heading">{{ $workforce['probation_employees'] }}</strong></span>
                <span>{{ __('Depts') }}: <strong class="text-heading">{{ $workforce['departments_count'] }}</strong></span>
            </div>
        </div>
    </div>

    <!-- Monthly Payroll & Commitments (Dynamic Setting Currency د.ك) -->
    <div class="col-12 col-sm-6 col-xl">
        <div class="card hr-stat-card border-top-accent-rose shadow-sm h-100 p-3">
            <div class="d-flex align-items-start justify-content-between mb-2">
                <div>
                    <span class="text-muted small fw-semibold d-block mb-1">{{ __('Monthly Payroll') }}</span>
                    <h4 class="fw-bold mb-0 text-danger">
                        {{ number_format($financials['monthly_payroll_net'], 2) }}
                        <small class="font-12 text-muted fw-normal">{{ $settingCurrency }}</small>
                    </h4>
                </div>
                <div class="stat-icon-wrapper" style="background-color: #fef2f2; color: #ef4444;">
                    <i class="bx bx-wallet font-22"></i>
                </div>
            </div>
            <div class="pt-2 border-top d-flex align-items-center justify-content-between small text-muted">
                <span>{{ __('Active Loans') }}: <strong class="text-heading">{{ number_format($financials['active_loans_balance'], 2) }} {{ $settingCurrency }}</strong></span>
                <a href="{{ route('hr.payrolls.index') }}" class="text-danger fw-semibold text-decoration-none font-12">{{ __('Payrolls') }} &rarr;</a>
            </div>
        </div>
    </div>
</div>

<!-- 3. Dedicated Job Requisitions Section (Directly below Statistics) -->
<div class="card hr-odoo-card border-0 shadow-sm mb-4 rounded-3">
    <div class="card-header bg-transparent border-bottom py-3 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-2">
            <div class="stat-icon-wrapper" style="background-color: #f5eff3; color: #714B67;">
                <i class="bx bx-briefcase font-20"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-bold">{{ __('Job Requisitions & Staffing Requests') }}</h5>
                <small class="text-muted">{{ __('Departmental headcount needs and recruiting progress') }}</small>
            </div>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
            <div class="d-flex align-items-center gap-1 border rounded-3 px-3 py-1 bg-light small">
                <span class="text-muted">{{ __('Needed Headcount') }}:</span>
                <strong style="color: #714B67;">{{ $requisitionMetrics['total_headcount'] }}</strong>
                <span class="text-muted mx-1">|</span>
                <span class="text-muted">{{ __('Hired') }}:</span>
                <strong class="text-success">{{ $requisitionMetrics['total_hired'] }}</strong>
            </div>

            <a href="{{ route('hr.job-requisitions.create') }}" class="btn btn-sm btn-odoo-primary rounded-2 d-flex align-items-center gap-1 shadow-sm">
                <i class="bx bx-plus font-16"></i>
                <span>{{ __('New Requisition') }}</span>
            </a>

            <a href="{{ route('hr.job-requisitions.index') }}" class="btn btn-sm btn-outline-secondary rounded-2">
                {{ __('View All') }} &rarr;
            </a>
        </div>
    </div>

    <!-- Status Badges Bar -->
    <div class="p-3 bg-light border-bottom d-flex flex-wrap align-items-center gap-2 font-13">
        <span class="text-muted fw-semibold me-2">{{ __('Requisition Status') }}:</span>
        <span class="badge px-3 py-2 rounded-pill d-flex align-items-center gap-1" style="background-color: #fef8ee; color: #b45309; border: 1px solid #fde68a;">
            <i class="bx bx-time-five font-14"></i>
            <span>{{ __('Pending Approval') }}: {{ $requisitionMetrics['pending_approval'] }}</span>
        </span>
        <span class="badge px-3 py-2 rounded-pill d-flex align-items-center gap-1" style="background-color: #e6f6f6; color: #008784; border: 1px solid #bfe8e7;">
            <i class="bx bx-check font-14"></i>
            <span>{{ __('Approved') }}: {{ $requisitionMetrics['approved'] }}</span>
        </span>
        <span class="badge px-3 py-2 rounded-pill d-flex align-items-center gap-1" style="background-color: #f5eff3; color: #714B67; border: 1px solid #ebdbe5;">
            <i class="bx bx-user-voice font-14"></i>
            <span>{{ __('In Recruitment') }}: {{ $requisitionMetrics['in_recruitment'] }}</span>
        </span>
        <span class="badge px-3 py-2 rounded-pill d-flex align-items-center gap-1" style="background-color: #ecfdf5; color: #047857; border: 1px solid #a7f3d0;">
            <i class="bx bx-check-double font-14"></i>
            <span>{{ __('Fulfilled') }}: {{ $requisitionMetrics['fulfilled'] }}</span>
        </span>
    </div>

    <!-- Requisitions Table -->
    <div class="table-responsive">
        <table class="table table-clean table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>{{ __('Requisition Code') }}</th>
                    <th>{{ __('Department & Position') }}</th>
                    <th class="text-center">{{ __('Headcount') }}</th>
                    <th>{{ __('Priority') }}</th>
                    <th>{{ __('Requested By') }}</th>
                    <th>{{ __('Target Start Date') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th class="text-center">{{ __('Action') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recentRequisitions as $req)
                    <tr>
                        <td>
                            <a href="{{ route('hr.job-requisitions.show', $req->id) }}" class="fw-bold font-13 text-decoration-none" style="color: #714B67;">
                                <i class="bx bx-file-blank me-1"></i>{{ $req->requisition_number }}
                            </a>
                        </td>
                        <td>
                            <span class="fw-semibold text-heading d-block font-13">{{ $req->jobTitle?->name ?? '-' }}</span>
                            <small class="text-muted font-11">{{ $req->department?->name ?? '-' }}</small>
                        </td>
                        <td class="text-center">
                            <span class="badge font-12 fw-bold" style="background-color: #f5eff3; color: #714B67; border: 1px solid #ebdbe5;">
                                {{ $req->hired_count }} / {{ $req->headcount_needed }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $req->priority?->badgeClass() ?? 'bg-label-secondary' }} font-11">
                                {{ $req->priority?->label() ?? '-' }}
                            </span>
                        </td>
                        <td>
                            <span class="font-12 text-heading d-block">{{ $req->requester?->name ?? '-' }}</span>
                            <small class="text-muted font-11">{{ $req->created_at?->format('Y-m-d') }}</small>
                        </td>
                        <td>
                            <span class="font-12 text-muted">
                                <i class="bx bx-calendar font-11 me-1"></i>{{ $req->expected_start_date?->format('Y-m-d') ?? '-' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $req->status?->badgeClass() ?? 'bg-label-primary' }} font-11">
                                {{ $req->status?->label() ?? 'Pending' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('hr.job-requisitions.show', $req->id) }}" class="btn btn-xs btn-outline-secondary rounded-pill">
                                {{ __('View') }}
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4 small">
                            <i class="bx bx-info-circle me-1"></i> {{ __('No job requisitions found') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- 4. Deep Focus: Attendance, Absence & Discipline (Full-Width Card - بالعرض) -->
<div class="card hr-odoo-card border-0 shadow-sm mb-4 rounded-3">
    <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <div class="stat-icon-wrapper" style="background-color: #fef8ee; color: #f59e0b;">
                <i class="bx bx-time font-22"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-bold">{{ __('Today Attendance & Discipline') }}</h5>
                <small class="text-muted">{{ __('Live status of employee check-ins, punctuality, and presence') }}</small>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge px-3 py-2 rounded-pill font-13" style="background-color: #fef8ee; color: #b45309; border: 1px solid #fde68a;">
                {{ __('Attendance Rate') }}: <strong>{{ $attendanceRate }}%</strong>
            </span>
            <a href="{{ route('hr.attendances.index') }}" class="btn btn-sm btn-outline-secondary rounded-2">
                {{ __('Attendance Matrix') }} &rarr;
            </a>
        </div>
    </div>
    <div class="card-body p-4">
        <div class="row g-4 align-items-center">
            <!-- Col 1: Radial Gauge -->
            <div class="col-12 col-md-3 text-center border-end">
                <div id="attendanceGaugeChart" style="min-height: 180px;"></div>
                <div class="fw-bold font-14 text-heading mt-1">{{ __('Today Attendance Rate') }}</div>
                <small class="text-muted font-11">{{ $attendance['today_present'] }} / {{ $workforce['total_employees'] }} {{ __('Staff Present') }}</small>
            </div>

            <!-- Col 2: 4 Wide Odoo Pastel Stat Tiles -->
            <div class="col-12 col-md-4 border-end">
                <div class="d-flex flex-column gap-2">
                    <!-- Present on time -->
                    <div class="d-flex align-items-center justify-content-between p-2 rounded-3" style="background-color: #ecfdf5; border: 1px solid #d1fae5;">
                        <span class="d-flex align-items-center gap-2 font-13 fw-semibold text-success">
                            <i class="bx bx-check-circle font-18"></i> {{ __('Present on Time') }}
                        </span>
                        <span class="badge bg-success font-13 px-2 py-1 rounded-pill">{{ max(0, $attendance['today_present'] - $attendance['today_late']) }}</span>
                    </div>
                    <!-- Late -->
                    <div class="d-flex align-items-center justify-content-between p-2 rounded-3" style="background-color: #fef8ee; border: 1px solid #fde68a;">
                        <span class="d-flex align-items-center gap-2 font-13 fw-semibold" style="color: #b45309;">
                            <i class="bx bx-alarm font-18"></i> {{ __('Late Check-in') }}
                        </span>
                        <span class="badge bg-warning font-13 px-2 py-1 rounded-pill">{{ $attendance['today_late'] }}</span>
                    </div>
                    <!-- Absent -->
                    <div class="d-flex align-items-center justify-content-between p-2 rounded-3" style="background-color: #fef2f2; border: 1px solid #fecaca;">
                        <span class="d-flex align-items-center gap-2 font-13 fw-semibold text-danger">
                            <i class="bx bx-x-circle font-18"></i> {{ __('Absent without Leave') }}
                        </span>
                        <span class="badge bg-danger font-13 px-2 py-1 rounded-pill">{{ $attendance['today_absent'] }}</span>
                    </div>
                    <!-- On Leave -->
                    <div class="d-flex align-items-center justify-content-between p-2 rounded-3" style="background-color: #e6f6f6; border: 1px solid #bfe8e7;">
                        <span class="d-flex align-items-center gap-2 font-13 fw-semibold" style="color: #008784;">
                            <i class="bx bx-calendar-minus font-18"></i> {{ __('On Approved Leave') }}
                        </span>
                        <span class="badge font-13 px-2 py-1 rounded-pill text-white" style="background-color: #00A09D;">{{ $attendance['on_leave_today_count'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Col 3: Live Check-in Logs Table -->
            <div class="col-12 col-md-5">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="font-12 fw-bold text-muted text-uppercase">{{ __('Recent Check-ins Today') }}</span>
                    <a href="{{ route('hr.attendances.index') }}" class="font-12 text-primary text-decoration-none">{{ __('View All') }} &rarr;</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-clean align-middle mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('Employee') }}</th>
                                <th>{{ __('Check-in') }}</th>
                                <th>{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentAttendanceLogs as $log)
                                <tr>
                                    <td>
                                        <span class="fw-semibold font-12 text-heading d-block">{{ $log->employee?->name }}</span>
                                        <small class="text-muted font-10">{{ $log->employee?->department?->name ?? '-' }}</small>
                                    </td>
                                    <td>
                                        <span class="font-12">{{ $log->check_in ?? '-' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $log->status?->badgeColor() ?? 'bg-label-primary' }} font-10">
                                            {{ $log->status?->label() ?? 'Recorded' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-3 small">
                                        <i class="bx bx-info-circle me-1"></i> {{ __('No check-in records logged yet for today') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 5. Employees on Leave Today (Full-Width Card - بالعرض) -->
<div class="card hr-odoo-card border-0 shadow-sm mb-4 rounded-3">
    <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <div class="stat-icon-wrapper" style="background-color: #e6f6f6; color: #00A09D;">
                <i class="bx bx-calendar-check font-22"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-bold">{{ __('Employees on Leave Today') }}</h5>
                <small class="text-muted">{{ __('Approved active leaves and away staff') }}</small>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge px-3 py-2 rounded-pill font-12" style="background-color: #e6f6f6; color: #008784; border: 1px solid #bfe8e7;">
                {{ $attendance['on_leave_today_count'] }} {{ __('Staff on Leave') }}
            </span>
            <a href="{{ route('hr.leaves.index') }}" class="btn btn-sm btn-outline-secondary rounded-2">
                {{ __('Leave Requests') }} &rarr;
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-clean table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>{{ __('Employee') }}</th>
                        <th>{{ __('Department & Position') }}</th>
                        <th>{{ __('Leave Type') }}</th>
                        <th>{{ __('Duration') }}</th>
                        <th>{{ __('Days Count') }}</th>
                        <th>{{ __('Expected Return') }}</th>
                        <th>{{ __('Status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($employeesOnLeaveToday as $leave)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar avatar-xs rounded-circle d-flex align-items-center justify-content-center" style="background-color: #e6f6f6; color: #008784;">
                                        <span class="font-10 fw-bold">{{ substr($leave->employee?->name ?? 'E', 0, 2) }}</span>
                                    </div>
                                    <div>
                                        <span class="fw-semibold font-13 d-block text-heading">{{ $leave->employee?->name }}</span>
                                        <small class="text-muted font-11">{{ $leave->employee?->employee_code ?? '-' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="font-13 text-heading d-block">{{ $leave->employee?->jobTitle?->name ?? '-' }}</span>
                                <small class="text-muted font-11">{{ $leave->employee?->department?->name ?? '-' }}</small>
                            </td>
                            <td>
                                <span class="badge font-11" style="background-color: #f5eff3; color: #714B67; border: 1px solid #ebdbe5;">
                                    {{ $leave->leave_type?->label() ?? __('Leave') }}
                                </span>
                            </td>
                            <td>
                                <span class="font-12">{{ $leave->start_date?->format('Y-m-d') }} &rarr; {{ $leave->end_date?->format('Y-m-d') }}</span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark font-12 fw-bold border">
                                    {{ $leave->days_count }} {{ __('Days') }}
                                </span>
                            </td>
                            <td>
                                <span class="badge font-12 fw-semibold" style="background-color: #ecfdf5; color: #047857; border: 1px solid #a7f3d0;">
                                    <i class="bx bx-calendar-check me-1"></i>{{ $leave->end_date?->addDay()->format('Y-m-d') }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-label-success font-11">
                                    {{ __('Active Leave') }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4 small">
                                <i class="bx bx-check-double text-success font-22 d-block mb-1"></i>
                                {{ __('All staff are present. No approved leaves active today.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- 6. Employee Requests Management Hub (Pending Approvals) -->
<div class="card hr-odoo-card border-0 shadow-sm mb-4 rounded-3">
    <div class="card-header bg-transparent border-bottom py-3 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-2">
            <div class="stat-icon-wrapper" style="background-color: #fef2f2; color: #ef4444;">
                <i class="bx bx-bell font-20"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-bold">{{ __('Employee Requests & Pending Decisions') }}</h5>
                <small class="text-muted">{{ __('Applications awaiting HR review and manager approval') }}</small>
            </div>
        </div>

        <!-- Tabs Nav -->
        <ul class="nav nav-tabs card-header-tabs" id="requestsTab" role="tablist">
            <li class="nav-item">
                <button class="nav-link active d-flex align-items-center gap-1" id="leaves-tab" data-bs-toggle="tab" data-bs-target="#tabLeaves" type="button" role="tab">
                    <i class="bx bx-calendar-exclamation font-16"></i>
                    <span>{{ __('Leave Requests') }}</span>
                    <span class="badge rounded-pill font-11 ms-1 text-white" style="background-color: #714B67;">{{ $pendingActions['leaves']['count'] }}</span>
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link d-flex align-items-center gap-1" id="permissions-tab" data-bs-toggle="tab" data-bs-target="#tabPermissions" type="button" role="tab">
                    <i class="bx bx-time-five font-16"></i>
                    <span>{{ __('Permission Requests') }}</span>
                    <span class="badge bg-label-secondary rounded-pill font-11 ms-1">{{ $pendingActions['permissions']['count'] }}</span>
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link d-flex align-items-center gap-1" id="loans-tab" data-bs-toggle="tab" data-bs-target="#tabLoans" type="button" role="tab">
                    <i class="bx bx-wallet font-16"></i>
                    <span>{{ __('Loan Requests') }}</span>
                    <span class="badge rounded-pill font-11 ms-1 text-white" style="background-color: #00A09D;">{{ $pendingActions['loans']['count'] }}</span>
                </button>
            </li>
        </ul>
    </div>

    <div class="tab-content p-0">
        <!-- 1. Leaves Tab -->
        <div class="tab-pane fade show active" id="tabLeaves" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-clean table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('Employee') }}</th>
                            <th>{{ __('Leave Type') }}</th>
                            <th>{{ __('Duration') }}</th>
                            <th>{{ __('Days Count') }}</th>
                            <th>{{ __('Reason') }}</th>
                            <th class="text-center">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pendingLeaves as $pl)
                            <tr>
                                <td>
                                    <span class="fw-semibold font-13 text-heading d-block">{{ $pl->employee?->name }}</span>
                                    <small class="text-muted font-11">{{ $pl->employee?->department?->name ?? '-' }}</small>
                                </td>
                                <td>
                                    <span class="badge font-11" style="background-color: #f5eff3; color: #714B67; border: 1px solid #ebdbe5;">{{ $pl->leave_type?->label() ?? 'Leave' }}</span>
                                </td>
                                <td>
                                    <span class="font-12">{{ $pl->start_date?->format('Y-m-d') }} &rarr; {{ $pl->end_date?->format('Y-m-d') }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark font-12 fw-bold border">{{ $pl->days_count }} {{ __('Days') }}</span>
                                </td>
                                <td>
                                    <span class="font-12 text-muted text-truncate d-inline-block" style="max-width: 220px;">{{ $pl->reason ?? '-' }}</span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('hr.leaves.index') }}" class="btn btn-xs btn-odoo-primary rounded-pill">
                                        {{ __('Review') }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4 small">
                                    <i class="bx bx-check-circle text-success me-1"></i> {{ __('No pending leave requests awaiting approval') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 2. Permissions Tab -->
        <div class="tab-pane fade" id="tabPermissions" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-clean table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('Employee') }}</th>
                            <th>{{ __('Permission Type') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Hours') }}</th>
                            <th>{{ __('Reason') }}</th>
                            <th class="text-center">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pendingPermissions as $pp)
                            <tr>
                                <td>
                                    <span class="fw-semibold font-13 text-heading d-block">{{ $pp->employee?->name }}</span>
                                    <small class="text-muted font-11">{{ $pp->employee?->department?->name ?? '-' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-label-secondary font-11">{{ $pp->type?->label() ?? 'Permission' }}</span>
                                </td>
                                <td>
                                    <span class="font-12">{{ $pp->date?->format('Y-m-d') }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-label-info font-11">{{ $pp->hours }} {{ __('Hours') }}</span>
                                </td>
                                <td>
                                    <span class="font-12 text-muted text-truncate d-inline-block" style="max-width: 220px;">{{ $pp->reason ?? '-' }}</span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('hr.permissions.index') }}" class="btn btn-xs btn-odoo-primary rounded-pill">
                                        {{ __('Review') }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4 small">
                                    <i class="bx bx-check-circle text-success me-1"></i> {{ __('No pending permission requests awaiting approval') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 3. Loans Tab -->
        <div class="tab-pane fade" id="tabLoans" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-clean table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('Employee') }}</th>
                            <th>{{ __('Amount') }}</th>
                            <th>{{ __('Installments') }}</th>
                            <th>{{ __('Monthly') }}</th>
                            <th>{{ __('Start Date') }}</th>
                            <th class="text-center">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pendingLoans as $ploan)
                            <tr>
                                <td>
                                    <span class="fw-semibold font-13 text-heading d-block">{{ $ploan->employee?->name }}</span>
                                    <small class="text-muted font-11">{{ $ploan->employee?->department?->name ?? '-' }}</small>
                                </td>
                                <td>
                                    <strong class="text-success font-13">{{ number_format($ploan->amount, 2) }} {{ $settingCurrency }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-label-secondary font-11">{{ $ploan->installments_count }} {{ __('Months') }}</span>
                                </td>
                                <td>
                                    <span class="font-12">{{ number_format($ploan->monthly_installment, 2) }} {{ $settingCurrency }}</span>
                                </td>
                                <td>
                                    <span class="font-12">{{ $ploan->start_date?->format('Y-m-d') }}</span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('hr.loans.index') }}" class="btn btn-xs btn-odoo-primary rounded-pill">
                                        {{ __('Review') }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4 small">
                                    <i class="bx bx-check-circle text-success me-1"></i> {{ __('No pending loan requests awaiting approval') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- 7. Recruitment Conversion Funnel & Department Distribution -->
<div class="row g-4 mb-4">
    <!-- Recruitment Conversion Funnel -->
    <div class="col-12 col-lg-7">
        <div class="card hr-odoo-card border-0 shadow-sm h-100 rounded-3">
            <div class="card-header bg-transparent border-bottom d-flex align-items-center justify-content-between py-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="stat-icon-wrapper" style="background-color: #f5eff3; color: #714B67;">
                        <i class="bx bx-filter font-20"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">{{ __('Recruitment Conversion Funnel') }}</h6>
                        <small class="text-muted">{{ __('Candidate progression stages from application to hire') }}</small>
                    </div>
                </div>
                <a href="{{ route('hr.job-applications.index') }}" class="btn btn-xs btn-outline-secondary rounded-pill">
                    {{ __('Open Kanban') }}
                </a>
            </div>
            <div class="card-body p-4">
                <div id="recruitmentFunnelChart" style="min-height: 270px;"></div>
            </div>
        </div>
    </div>

    <!-- Department Distribution Donut -->
    <div class="col-12 col-lg-5">
        <div class="card hr-odoo-card border-0 shadow-sm h-100 rounded-3">
            <div class="card-header bg-transparent border-bottom d-flex align-items-center justify-content-between py-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="stat-icon-wrapper" style="background-color: #e6f6f6; color: #00A09D;">
                        <i class="bx bx-pie-chart-alt font-20"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">{{ __('Department Distribution') }}</h6>
                        <small class="text-muted">{{ __('Staff allocation by department') }}</small>
                    </div>
                </div>
                <span class="badge font-11 rounded-pill" style="background-color: #f5eff3; color: #714B67; border: 1px solid #ebdbe5;">{{ $workforce['departments_count'] }} {{ __('Departments') }}</span>
            </div>
            <div class="card-body p-4 d-flex flex-column align-items-center justify-content-center">
                <div id="departmentDonutChart" style="width: 100%; min-height: 240px;"></div>
                
                <div class="w-100 mt-3 pt-3 border-top">
                    <div class="d-flex justify-content-between small mb-1">
                        <span><i class="bx bx-male" style="color: #714B67;"></i> {{ __('Male') }}: <strong>{{ $workforce['male_employees'] }}</strong> ({{ $maleRatio }}%)</span>
                        <span><i class="bx bx-female" style="color: #00A09D;"></i> {{ __('Female') }}: <strong>{{ $workforce['female_employees'] }}</strong> ({{ $femaleRatio }}%)</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar" role="progressbar" style="width: {{ $maleRatio }}%; background-color: #714B67;"></div>
                        <div class="progress-bar" role="progressbar" style="width: {{ $femaleRatio }}%; background-color: #00A09D;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 8. Active Interviews & Onboarding Journeys Grid -->
<div class="row g-4">
    <!-- Scheduled Interviews -->
    <div class="col-12 col-lg-6">
        <div class="card hr-odoo-card border-0 shadow-sm h-100 rounded-3">
            <div class="card-header bg-transparent border-bottom d-flex align-items-center justify-content-between py-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bx bx-calendar-check font-20" style="color: #714B67;"></i>
                    <h6 class="mb-0 fw-bold">{{ __('Upcoming Scheduled Interviews') }}</h6>
                </div>
                <a href="{{ route('hr.interviews.index') }}" class="font-12 fw-semibold text-decoration-none" style="color: #714B67;">
                    {{ __('View All') }} &rarr;
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-clean table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('Candidate') }}</th>
                            <th>{{ __('Position') }}</th>
                            <th>{{ __('Date & Time') }}</th>
                            <th class="text-center">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($upcomingInterviews as $interview)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar avatar-xs rounded-circle d-flex align-items-center justify-content-center" style="background-color: #f5eff3; color: #714B67;">
                                            <span class="font-10 fw-bold">{{ $interview->candidate?->initials ?? 'C' }}</span>
                                        </div>
                                        <div>
                                            <span class="fw-semibold font-13 d-block text-heading">{{ $interview->candidate?->full_name ?? '-' }}</span>
                                            <small class="text-muted font-11">{{ $interview->type->label() ?? '' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="font-12 text-heading">{{ $interview->jobOpening?->title ?? '-' }}</span>
                                </td>
                                <td>
                                    <span class="font-12 d-block"><i class="bx bx-calendar font-11 text-muted me-1"></i>{{ $interview->scheduled_at?->format('Y-m-d') }}</span>
                                    <small class="text-muted font-11"><i class="bx bx-time font-11 me-1"></i>{{ $interview->scheduled_at?->format('H:i') }}</small>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('hr.interviews.evaluate', $interview->id) }}" class="btn btn-xs btn-outline-secondary rounded-pill">
                                        {{ __('Evaluate') }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4 small">
                                    <i class="bx bx-info-circle me-1"></i> {{ __('No scheduled interviews found') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Active Onboarding Journeys -->
    <div class="col-12 col-lg-6">
        <div class="card hr-odoo-card border-0 shadow-sm h-100 rounded-3">
            <div class="card-header bg-transparent border-bottom d-flex align-items-center justify-content-between py-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bx bx-rocket font-20" style="color: #00A09D;"></i>
                    <h6 class="mb-0 fw-bold">{{ __('Active Onboarding Journeys') }}</h6>
                </div>
                <a href="{{ route('hr.onboardings.index') }}" class="font-12 fw-semibold text-decoration-none" style="color: #00A09D;">
                    {{ __('View All') }} &rarr;
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-clean table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('Candidate') }}</th>
                            <th>{{ __('Position') }}</th>
                            <th>{{ __('Progress') }}</th>
                            <th class="text-center">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentOnboardings as $onb)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar avatar-xs rounded-circle d-flex align-items-center justify-content-center" style="background-color: #e6f6f6; color: #00A09D;">
                                            <span class="font-10 fw-bold">{{ $onb->candidate?->initials ?? 'C' }}</span>
                                        </div>
                                        <div>
                                            <a href="{{ route('hr.onboardings.show', $onb->id) }}" class="fw-semibold font-13 text-heading text-decoration-none">
                                                {{ $onb->candidate?->full_name ?? '-' }}
                                            </a>
                                            <small class="text-muted d-block font-11">{{ $onb->onboarding_code }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="font-12 text-heading">{{ $onb->jobTitle?->name ?? '-' }}</span>
                                </td>
                                <td style="min-width: 140px;">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 6px;">
                                            <div class="progress-bar" role="progressbar" style="width: {{ $onb->progress_percentage }}%; background-color: #00A09D;"></div>
                                        </div>
                                        <span class="font-12 fw-bold text-heading">{{ $onb->progress_percentage }}%</span>
                                    </div>
                                    <small class="text-muted font-10">{{ $onb->completed_tasks_count }}/{{ $onb->total_tasks_count }} {{ __('Tasks') }}</small>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('hr.onboardings.show', $onb->id) }}" class="btn btn-xs btn-outline-secondary rounded-pill">
                                        {{ __('Journey') }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4 small">
                                    <i class="bx bx-info-circle me-1"></i> {{ __('No active onboarding journeys') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Attendance Gauge / Donut Chart (Odoo Amber)
    const attendanceEl = document.querySelector('#attendanceGaugeChart');
    if (attendanceEl && typeof ApexCharts !== 'undefined') {
        const attendanceOptions = {
            chart: {
                type: 'radialBar',
                height: 200,
                sparkline: { enabled: true }
            },
            plotOptions: {
                radialBar: {
                    startAngle: -90,
                    endAngle: 90,
                    hollow: { size: '65%' },
                    track: {
                        background: '#f1f5f9',
                        strokeWidth: '97%',
                        margin: 5
                    },
                    dataLabels: {
                        name: { show: false },
                        value: {
                            offsetY: -10,
                            fontSize: '22px',
                            fontWeight: 700,
                            color: '#b45309',
                            formatter: function (val) {
                                return val + '%';
                            }
                        }
                    }
                }
            },
            colors: ['#f59e0b'],
            series: [{{ (float) $attendanceRate }}]
        };
        const attendanceChart = new ApexCharts(attendanceEl, attendanceOptions);
        attendanceChart.render();
    }

    // 2. Recruitment Funnel Horizontal Bar Chart (Odoo-inspired palette)
    const funnelEl = document.querySelector('#recruitmentFunnelChart');
    if (funnelEl && typeof ApexCharts !== 'undefined') {
        const funnelOptions = {
            chart: {
                type: 'bar',
                height: 270,
                toolbar: { show: false },
                fontFamily: 'inherit'
            },
            plotOptions: {
                bar: {
                    borderRadius: 6,
                    horizontal: true,
                    distributed: true,
                    barHeight: '55%',
                    dataLabels: { position: 'bottom' }
                }
            },
            colors: ['#714B67', '#00A09D', '#f59e0b', '#10b981', '#2563eb'],
            series: [{
                name: "{{ __('Candidates') }}",
                data: @json($funnelData['series'])
            }],
            xaxis: {
                categories: @json($funnelData['labels']),
                labels: {
                    style: { fontSize: '12px' }
                }
            },
            yaxis: {
                labels: {
                    style: { fontSize: '12px', fontWeight: 600 }
                }
            },
            tooltip: {
                theme: 'dark',
                y: {
                    formatter: function (val) {
                        return val + " {{ __('Candidates') }}";
                    }
                }
            },
            legend: { show: false },
            grid: {
                borderColor: '#f1f5f9',
                strokeDashArray: 4
            }
        };
        const funnelChart = new ApexCharts(funnelEl, funnelOptions);
        funnelChart.render();
    }

    // 3. Department Distribution Donut Chart (Odoo palette)
    const donutEl = document.querySelector('#departmentDonutChart');
    if (donutEl && typeof ApexCharts !== 'undefined') {
        const donutOptions = {
            chart: {
                type: 'donut',
                height: 240,
                fontFamily: 'inherit'
            },
            labels: @json($deptDistribution['labels']),
            series: @json($deptDistribution['series']),
            colors: ['#714B67', '#00A09D', '#f59e0b', '#10b981', '#ef4444', '#64748b', '#0284c7', '#8b5cf6'],
            stroke: { width: 2, colors: ['#ffffff'] },
            dataLabels: { enabled: false },
            legend: {
                position: 'bottom',
                fontSize: '11px',
                markers: { radius: 12 }
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '72%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: "{{ __('Total Staff') }}",
                                fontSize: '12px',
                                fontWeight: 600,
                                formatter: function (w) {
                                    return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                }
                            }
                        }
                    }
                }
            },
            tooltip: {
                theme: 'dark',
                y: {
                    formatter: function (val) {
                        return val + " {{ __('Employees') }}";
                    }
                }
            }
        };
        const donutChart = new ApexCharts(donutEl, donutOptions);
        donutChart.render();
    }
});
</script>
@endsection
