@extends('layouts/contentNavbarLayout')

@section('title', __('Dashboard') . ' - ' . config('variables.templateName'))

@section('vendor-style')
    @vite('resources/assets/vendor/libs/apex-charts/apex-charts.scss')
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
    @vite('resources/assets/vendor/libs/apex-charts/apexcharts.js')
@endsection

@section('content')
<!-- ========================================================
     1. WELCOME BANNER & TOP EXECUTIVE SUMMARY
     ======================================================== -->
<div class="row g-3 mb-4">
    <!-- Welcome Card -->
    <div class="col-12 col-xxl-8">
        <div class="card h-100 border-0 shadow-sm overflow-hidden">
            <div class="d-flex align-items-center row h-100 g-0">
                <div class="col-12 col-sm-7">
                    <div class="card-body p-4">
                        <span class="badge bg-label-primary rounded-pill mb-2 px-3 py-1">{{ __('System Overview') }}</span>
                        <h4 class="card-title fw-bold text-primary mb-2">
                            {{ __('Welcome back') }}, {{ $user->name }}! 👋
                        </h4>
                        <p class="text-muted mb-3" style="line-height: 1.6;">
                            {{ __('A comprehensive dashboard for managing academic, financial, administrative, and inventory operations.') }}
                        </p>
                        <div class="d-flex flex-wrap align-items-center gap-3 pt-1">
                            <span class="text-muted small">
                                <i class="bx bx-buildings me-1 text-primary"></i><strong>{{ $stats['branches'] }}</strong> {{ __('Branches') }}
                            </span>
                            <span class="text-muted small">
                                <i class="bx bx-layer me-1 text-success"></i><strong>{{ $stats['stages'] }}</strong> {{ __('Academic Stages') }}
                            </span>
                            <span class="text-muted small">
                                <i class="bx bx-chalkboard me-1 text-info"></i><strong>{{ $stats['classes'] }}</strong> {{ __('Classes') }}
                            </span>
                            <span class="text-muted small">
                                <i class="bx bx-shield-quarter me-1 text-warning"></i><strong>{{ $stats['activity_logs'] }}</strong> {{ __('Audit Logs') }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-5 text-center text-sm-end d-none d-sm-block">
                    <div class="card-body pb-0 px-4">
                        <img src="{{ asset('assets/img/illustrations/man-with-laptop.png') }}" height="160" alt="Dashboard Illustration" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top 2 Quick Executive Metrics -->
    <div class="col-12 col-xxl-4">
        <div class="row g-3 h-100">
            <!-- Staff & Employees -->
            <div class="col-12 col-sm-6 col-xxl-12">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center justify-content-between p-3 p-md-4">
                        <div>
                            <span class="text-muted small fw-medium d-block mb-1">{{ __('Staff & Employees') }}</span>
                            <h3 class="mb-1 fw-bold text-heading">{{ number_format($stats['employees']) }}</h3>
                            <small class="text-primary fw-semibold">{{ $stats['departments'] }} {{ __('Departments') }} • {{ number_format($stats['attendances']) }} {{ __('Attendances') }}</small>
                        </div>
                        <span class="badge bg-label-primary rounded p-3">
                            <i class="bx bx-user-pin fs-3"></i>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Financial Accounts & Ledger -->
            <div class="col-12 col-sm-6 col-xxl-12">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center justify-content-between p-3 p-md-4">
                        <div>
                            <span class="text-muted small fw-medium d-block mb-1">{{ __('Chart of Accounts') }}</span>
                            <h3 class="mb-1 fw-bold text-success">{{ number_format($stats['accounts']) }}</h3>
                            <small class="text-success fw-semibold">{{ $stats['cash_accounts'] }} {{ __('Cash Accounts') }} • {{ $stats['journals'] }} {{ __('Journals') }}</small>
                        </div>
                        <span class="badge bg-label-success rounded p-3">
                            <i class="bx bx-book-bookmark fs-3"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================
     2. FOUR MASTER PILLARS / SECTORS OF THE ERP
     ======================================================== -->
<div class="row g-3 mb-4">
    <!-- 1. Financial & Accounting Sector -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-label-success rounded p-2">
                            <i class="bx bx-dollar-circle fs-4"></i>
                        </span>
                        <span class="fw-bold text-heading">{{ __('Financial Management') }}</span>
                    </div>
                    @if(Route::has('accounting.dashboard'))
                        <a href="{{ route('accounting.dashboard') }}" class="text-muted">
                            <i class="bx bx-chevron-left align-middle fs-4"></i>
                        </a>
                    @endif
                </div>
                <div class="mb-3">
                    <span class="text-muted small d-block">{{ __('Total Collected Fees') }}</span>
                    <h4 class="mb-0 fw-bold text-success">{{ number_format($stats['collected_fees'], 2) }}</h4>
                    <div class="progress mt-2" style="height: 6px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ min(100, $stats['collection_rate']) }}%"></div>
                    </div>
                    <div class="d-flex justify-content-between text-muted small mt-1">
                        <span>{{ number_format($stats['collection_rate'], 1) }}% {{ __('Collected') }}</span>
                        <span>{{ number_format($stats['total_fees'], 0) }} {{ __('Total') }}</span>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2 pt-2 border-top">
                    <span class="badge bg-label-secondary small">{{ $stats['accounts'] }} {{ __('Accounts') }}</span>
                    <span class="badge bg-label-secondary small">{{ $stats['cash_accounts'] }} {{ __('Cash') }}</span>
                    <span class="badge bg-label-secondary small">{{ $stats['cost_centers'] }} {{ __('Cost Centers') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. HR & Workforce Sector -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-label-primary rounded p-2">
                            <i class="bx bx-group fs-4"></i>
                        </span>
                        <span class="fw-bold text-heading">{{ __('Human Resources') }}</span>
                    </div>
                    @if(Route::has('hr.dashboard'))
                        <a href="{{ route('hr.dashboard') }}" class="text-muted">
                            <i class="bx bx-chevron-left align-middle fs-4"></i>
                        </a>
                    @endif
                </div>
                <div class="mb-3">
                    <span class="text-muted small d-block">{{ __('Total Employees') }}</span>
                    <h4 class="mb-0 fw-bold text-primary">{{ number_format($stats['employees']) }}</h4>
                    <div class="d-flex align-items-center justify-content-between text-muted small mt-2">
                        <span><i class="bx bx-check-circle text-success me-1"></i>{{ number_format($stats['attendances']) }} {{ __('Attendances') }}</span>
                        <span><i class="bx bx-time text-warning me-1"></i>{{ $stats['leave_requests'] }} {{ __('Leaves') }}</span>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2 pt-2 border-top">
                    <span class="badge bg-label-secondary small">{{ $stats['departments'] }} {{ __('Departments') }}</span>
                    <span class="badge bg-label-secondary small">{{ $stats['job_titles'] }} {{ __('Job Titles') }}</span>
                    <span class="badge bg-label-secondary small">{{ $stats['loans'] }} {{ __('Loans') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Academic & Educational Sector -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-label-info rounded p-2">
                            <i class="bx bxs-graduation fs-4"></i>
                        </span>
                        <span class="fw-bold text-heading">{{ __('Academic Sector') }}</span>
                    </div>
                    <a href="{{ route('academic.dashboard') }}" class="text-muted">
                        <i class="bx bx-chevron-left align-middle fs-4"></i>
                    </a>
                </div>
                <div class="mb-3">
                    <span class="text-muted small d-block">{{ __('Classes & Stages') }}</span>
                    <h4 class="mb-0 fw-bold text-info">{{ $stats['classes'] }} <small class="text-muted fs-6">{{ __('Classes') }}</small></h4>
                    <div class="d-flex align-items-center justify-content-between text-muted small mt-2">
                        <span>{{ $stats['stages'] }} {{ __('Academic Stages') }}</span>
                        <span>{{ $stats['students'] }} {{ __('Students') }}</span>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2 pt-2 border-top">
                    <span class="badge bg-label-secondary small">{{ $stats['subjects'] }} {{ __('Subjects') }}</span>
                    <span class="badge bg-label-secondary small">{{ $stats['lessons'] }} {{ __('Lessons') }}</span>
                    <span class="badge bg-label-secondary small">{{ $stats['monthly_plans'] + $stats['weekly_plans'] }} {{ __('Plans') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Inventory, Facilities & System -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-label-warning rounded p-2">
                            <i class="bx bx-cube fs-4"></i>
                        </span>
                        <span class="fw-bold text-heading">{{ __('Inventory & Logistics') }}</span>
                    </div>
                    <a href="{{ route('inventory.dashboard') }}" class="text-muted">
                        <i class="bx bx-chevron-left align-middle fs-4"></i>
                    </a>
                </div>
                <div class="mb-3">
                    <span class="text-muted small d-block">{{ __('Warehouses & Stock') }}</span>
                    <h4 class="mb-0 fw-bold text-warning">{{ $stats['warehouses'] }} <small class="text-muted fs-6">{{ __('Warehouses') }}</small></h4>
                    <div class="d-flex align-items-center justify-content-between text-muted small mt-2">
                        <span>{{ $stats['categories'] }} {{ __('Categories') }}</span>
                        <span>{{ $stats['units'] }} {{ __('Units') }}</span>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2 pt-2 border-top">
                    <span class="badge bg-label-secondary small">{{ $stats['branches'] }} {{ __('Branches') }}</span>
                    <span class="badge bg-label-secondary small">{{ $stats['users'] }} {{ __('Users') }}</span>
                    <span class="badge bg-label-secondary small">{{ $stats['roles'] }} {{ __('Roles') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================
     3. VISUAL ANALYTICS (APEXCHARTS ROW 1)
     ======================================================== -->
<div class="row g-3 mb-4">
    <!-- Employees by Department Bar Chart -->
    <div class="col-12 col-lg-7">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bx bx-buildings text-primary fs-5"></i>
                    <h5 class="mb-0 fw-bold text-heading">{{ __('Employees by Department') }}</h5>
                </div>
                <span class="badge bg-label-primary rounded-pill">{{ count($departmentsList) }} {{ __('Departments') }}</span>
            </div>
            <div class="card-body">
                <div id="departmentsBarChart" style="min-height: 290px;"></div>
            </div>
        </div>
    </div>

    <!-- Classes by Academic Stage Donut Chart -->
    <div class="col-12 col-lg-5">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bx bx-pie-chart-alt text-info fs-5"></i>
                    <h5 class="mb-0 fw-bold text-heading">{{ __('Classes by Academic Stage') }}</h5>
                </div>
                <span class="badge bg-label-info rounded-pill">{{ $stats['classes'] }} {{ __('Classes') }}</span>
            </div>
            <div class="card-body d-flex flex-column justify-content-center">
                <div id="stagesDonutChart" style="min-height: 250px;"></div>
                <div class="row text-center mt-3 pt-2 border-top g-2">
                    <div class="col-6">
                        <span class="text-muted small d-block">{{ __('Academic Stages') }}</span>
                        <strong class="text-primary small">{{ $stats['stages'] }}</strong>
                    </div>
                    <div class="col-6">
                        <span class="text-muted small d-block">{{ __('Total Classes') }}</span>
                        <strong class="text-info small">{{ $stats['classes'] }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================
     4. VISUAL ANALYTICS (APEXCHARTS ROW 2)
     ======================================================== -->
<div class="row g-3 mb-4">
    <!-- Fee Structure by Fee Type -->
    <div class="col-12 col-lg-6">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bx bx-receipt text-success fs-5"></i>
                    <h5 class="mb-0 fw-bold text-heading">{{ __('Fee Structure by Category') }}</h5>
                </div>
                <a href="{{ route('academic.fee-statistics.index') }}" class="btn btn-sm btn-label-success">
                    {{ __('Details') }}
                </a>
            </div>
            <div class="card-body">
                <div id="feeTypesBarChart" style="min-height: 270px;"></div>
            </div>
        </div>
    </div>

    <!-- System Operational Distribution Donut Chart -->
    <div class="col-12 col-lg-6">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bx bx-doughnut-chart text-warning fs-5"></i>
                    <h5 class="mb-0 fw-bold text-heading">{{ __('System Record Distribution') }}</h5>
                </div>
                <span class="badge bg-label-warning rounded-pill">{{ __('Live Core Records') }}</span>
            </div>
            <div class="card-body d-flex flex-column justify-content-center">
                <div id="systemDistributionDonutChart" style="min-height: 270px;"></div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================
     5. RECENT SYSTEM AUDIT & OPERATIONS LOG
     ======================================================== -->
@if(!empty($recentActivities) && count($recentActivities) > 0)
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
        <div class="d-flex align-items-center gap-2">
            <i class="bx bx-history text-primary fs-5"></i>
            <h5 class="mb-0 fw-bold text-heading">{{ __('Recent System Activity & Audit Trail') }}</h5>
        </div>
        <span class="badge bg-label-primary">{{ number_format($stats['activity_logs']) }} {{ __('Total Activities') }}</span>
    </div>
    <div class="table-responsive text-nowrap">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>{{ __('User') }}</th>
                    <th>{{ __('Action / Description') }}</th>
                    <th>{{ __('Entity') }}</th>
                    <th class="text-end">{{ __('Date & Time') }}</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @foreach($recentActivities as $log)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-label-primary rounded-circle p-2">
                                    <i class="bx bx-user fs-6"></i>
                                </span>
                                <div>
                                    <span class="fw-semibold text-heading">{{ optional($log->causer)->name ?? __('System') }}</span>
                                    <small class="text-muted d-block">{{ optional($log->causer)->email ?? '' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-label-{{ $log->event == 'created' ? 'success' : ($log->event == 'deleted' ? 'danger' : 'info') }} me-1">
                                {{ ucfirst($log->event ?? 'action') }}
                            </span>
                            <span class="text-heading">{{ $log->description ?? __('Performed action') }}</span>
                        </td>
                        <td>
                            <span class="badge bg-label-secondary font-monospace">
                                {{ class_basename($log->subject_type ?? 'Record') }}
                                @if(!empty($log->subject_id)) #{{ $log->subject_id }} @endif
                            </span>
                        </td>
                        <td class="text-end text-muted small">
                            <i class="bx bx-time-five me-1 align-middle"></i>{{ optional($log->created_at)->diffForHumans() ?? '' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<!-- ========================================================
     6. SYSTEM MODULE LAUNCHERS (QUICK ACCESS)
     ======================================================== -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent py-3">
        <h5 class="mb-0 fw-bold text-heading">{{ __('System Modules') }}</h5>
    </div>
    <div class="card-body pt-0">
        <div class="row g-3">
            <!-- Academic -->
            <div class="col-12 col-sm-6 col-lg-4 col-xl-2">
                <a href="{{ route('academic.dashboard') }}" class="card border shadow-none p-3 text-decoration-none h-100 transition-all hover-elevate">
                    <div class="d-flex flex-column align-items-center text-center gap-2">
                        <span class="badge bg-label-primary rounded p-3">
                            <i class="bx bxs-graduation fs-2"></i>
                        </span>
                        <h6 class="mb-0 fw-bold text-heading">{{ __('Academic Management') }}</h6>
                        <small class="text-muted">{{ $stats['classes'] }} {{ __('Classes') }} • {{ $stats['stages'] }} {{ __('Stages') }}</small>
                    </div>
                </a>
            </div>

            <!-- Financial / Fees -->
            <div class="col-12 col-sm-6 col-lg-4 col-xl-2">
                <a href="{{ route('academic.fee-statistics.index') }}" class="card border shadow-none p-3 text-decoration-none h-100 transition-all hover-elevate">
                    <div class="d-flex flex-column align-items-center text-center gap-2">
                        <span class="badge bg-label-success rounded p-3">
                            <i class="bx bx-receipt fs-2"></i>
                        </span>
                        <h6 class="mb-0 fw-bold text-heading">{{ __('Fees & Collections') }}</h6>
                        <small class="text-muted">{{ number_format($stats['collected_fees'], 2) }} {{ __('Collected') }}</small>
                    </div>
                </a>
            </div>

            <!-- Accounting -->
            <div class="col-12 col-sm-6 col-lg-4 col-xl-2">
                @php $accRoute = Route::has('accounting.dashboard') ? route('accounting.dashboard') : (Route::has('accounting.index') ? route('accounting.index') : '#'); @endphp
                <a href="{{ $accRoute }}" class="card border shadow-none p-3 text-decoration-none h-100 transition-all hover-elevate">
                    <div class="d-flex flex-column align-items-center text-center gap-2">
                        <span class="badge bg-label-info rounded p-3">
                            <i class="bx bx-wallet fs-2"></i>
                        </span>
                        <h6 class="mb-0 fw-bold text-heading">{{ __('Accounting & Ledger') }}</h6>
                        <small class="text-muted">{{ $stats['accounts'] }} {{ __('Accounts') }} • {{ $stats['journals'] }} {{ __('Journals') }}</small>
                    </div>
                </a>
            </div>

            <!-- HR -->
            <div class="col-12 col-sm-6 col-lg-4 col-xl-2">
                @php $hrRoute = Route::has('hr.dashboard') ? route('hr.dashboard') : '#'; @endphp
                <a href="{{ $hrRoute }}" class="card border shadow-none p-3 text-decoration-none h-100 transition-all hover-elevate">
                    <div class="d-flex flex-column align-items-center text-center gap-2">
                        <span class="badge bg-label-danger rounded p-3">
                            <i class="bx bx-user-pin fs-2"></i>
                        </span>
                        <h6 class="mb-0 fw-bold text-heading">{{ __('Human Resources') }}</h6>
                        <small class="text-muted">{{ $stats['employees'] }} {{ __('Employees') }} • {{ $stats['departments'] }} {{ __('Depts') }}</small>
                    </div>
                </a>
            </div>

            <!-- Inventory -->
            <div class="col-12 col-sm-6 col-lg-4 col-xl-2">
                <a href="{{ route('inventory.dashboard') }}" class="card border shadow-none p-3 text-decoration-none h-100 transition-all hover-elevate">
                    <div class="d-flex flex-column align-items-center text-center gap-2">
                        <span class="badge bg-label-warning rounded p-3">
                            <i class="bx bx-store fs-2"></i>
                        </span>
                        <h6 class="mb-0 fw-bold text-heading">{{ __('Warehouses & Inventory') }}</h6>
                        <small class="text-muted">{{ $stats['warehouses'] }} {{ __('Warehouses') }}</small>
                    </div>
                </a>
            </div>

            <!-- Nurseries & Branches -->
            <div class="col-12 col-sm-6 col-lg-4 col-xl-2">
                <a href="{{ route('academic.academic-years.index') }}" class="card border shadow-none p-3 text-decoration-none h-100 transition-all hover-elevate">
                    <div class="d-flex flex-column align-items-center text-center gap-2">
                        <span class="badge bg-label-secondary rounded p-3">
                            <i class="bx bx-buildings fs-2"></i>
                        </span>
                        <h6 class="mb-0 fw-bold text-heading">{{ __('Nurseries & Branches') }}</h6>
                        <small class="text-muted">{{ $stats['branches'] }} {{ __('Branches') }}</small>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
(function () {
    const chartData = @json($chartData);

    function initAllDashboardCharts() {
        if (typeof window.ApexCharts === 'undefined') {
            setTimeout(initAllDashboardCharts, 60);
            return;
        }

        // 1. Departments Bar Chart
        const deptsEl = document.querySelector("#departmentsBarChart");
        if (deptsEl) {
            const deptsSeries = chartData.departments_chart?.series || [];
            const deptsLabels = chartData.departments_chart?.labels || [];
            const deptsOptions = {
                chart: {
                    type: 'bar',
                    height: 290,
                    toolbar: { show: false },
                    fontFamily: 'inherit'
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '40%',
                        borderRadius: 6
                    }
                },
                dataLabels: { enabled: false },
                colors: ['#696cff'],
                series: [{
                    name: "{{ __('Staff & Employees') }}",
                    data: deptsSeries
                }],
                xaxis: {
                    categories: deptsLabels,
                    labels: {
                        style: { colors: '#6c757d', fontSize: '12px' }
                    }
                },
                yaxis: {
                    labels: {
                        formatter: val => Math.round(val),
                        style: { colors: '#6c757d', fontSize: '12px' }
                    }
                },
                grid: {
                    borderColor: '#f1ecf0',
                    strokeDashArray: 4
                }
            };
            new ApexCharts(deptsEl, deptsOptions).render();
        }

        // 2. Stages Donut Chart
        const stagesEl = document.querySelector("#stagesDonutChart");
        if (stagesEl) {
            const stagesSeries = chartData.stages_chart?.series || [];
            const hasStagesData = stagesSeries.some(v => v > 0);

            const stagesOptions = {
                chart: {
                    type: 'donut',
                    height: 250,
                    fontFamily: 'inherit'
                },
                series: hasStagesData ? stagesSeries : [1],
                labels: hasStagesData ? (chartData.stages_chart?.labels || []) : ["{{ __('No data found') }}"],
                colors: hasStagesData ? ['#696cff', '#03c3ec', '#71dd37', '#ffab00', '#ff3e1d', '#8592a3'] : ['#e0e0e0'],
                plotOptions: {
                    pie: {
                        donut: {
                            size: '65%',
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: "{{ __('Classes') }}",
                                    formatter: function (w) {
                                        if (!hasStagesData) return '0';
                                        return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                    }
                                }
                            }
                        }
                    }
                },
                dataLabels: { enabled: false },
                legend: {
                    position: 'bottom',
                    fontSize: '12px'
                }
            };
            new ApexCharts(stagesEl, stagesOptions).render();
        }

        // 3. Fee Structure Bar Chart
        const feeTypesEl = document.querySelector("#feeTypesBarChart");
        if (feeTypesEl) {
            const feeSeries = chartData.fee_types_chart?.series || [];
            const feeLabels = chartData.fee_types_chart?.labels || [];
            const feeOptions = {
                chart: {
                    type: 'bar',
                    height: 270,
                    toolbar: { show: false },
                    fontFamily: 'inherit'
                },
                plotOptions: {
                    bar: {
                        horizontal: true,
                        barHeight: '50%',
                        borderRadius: 5
                    }
                },
                dataLabels: { enabled: false },
                colors: ['#71dd37'],
                series: [{
                    name: "{{ __('Defined Plans') }}",
                    data: feeSeries
                }],
                xaxis: {
                    categories: feeLabels,
                    labels: {
                        formatter: val => Math.round(val),
                        style: { colors: '#6c757d', fontSize: '12px' }
                    }
                },
                yaxis: {
                    labels: {
                        style: { colors: '#6c757d', fontSize: '12px' }
                    }
                },
                grid: {
                    borderColor: '#f1ecf0',
                    strokeDashArray: 4
                }
            };
            new ApexCharts(feeTypesEl, feeOptions).render();
        }

        // 4. System Record Distribution Donut Chart
        const sysDistEl = document.querySelector("#systemDistributionDonutChart");
        if (sysDistEl) {
            const sysSeries = chartData.system_distribution_chart?.series || [];
            const sysLabels = chartData.system_distribution_chart?.labels || [];
            const hasSysData = sysSeries.some(v => v > 0);

            const sysOptions = {
                chart: {
                    type: 'donut',
                    height: 270,
                    fontFamily: 'inherit'
                },
                series: hasSysData ? sysSeries : [1],
                labels: hasSysData ? sysLabels : ["{{ __('No data found') }}"],
                colors: hasSysData ? ['#71dd37', '#696cff', '#03c3ec', '#ffab00', '#20c997', '#8592a3'] : ['#e0e0e0'],
                plotOptions: {
                    pie: {
                        donut: {
                            size: '65%',
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: "{{ __('Records') }}",
                                    formatter: function (w) {
                                        if (!hasSysData) return '0';
                                        return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                    }
                                }
                            }
                        }
                    }
                },
                dataLabels: { enabled: false },
                legend: {
                    position: 'bottom',
                    fontSize: '12px'
                }
            };
            new ApexCharts(sysDistEl, sysOptions).render();
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAllDashboardCharts);
    } else {
        initAllDashboardCharts();
    }
})();
</script>
@endsection
