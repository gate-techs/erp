@extends('layouts/contentNavbarLayout')

@section('title', __('Dashboard') . ' - ' . config('variables.templateName'))

@section('vendor-style')
    @vite('resources/assets/vendor/libs/apex-charts/apex-charts.scss')
@endsection

@section('vendor-script')
    @vite('resources/assets/vendor/libs/apex-charts/apexcharts.js')
@endsection

@section('content')
<!-- Header & Quick Actions -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted small">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item text-muted small">{{ __('Inventory') }}</li>
                <li class="breadcrumb-item active fw-bold small text-primary">{{ __('Dashboard') }}</li>
            </ol>
        </nav>
        <h4 class="fw-bold mb-0 text-heading d-flex align-items-center gap-2">
            <i class="icon-base bx bx-store text-primary font-24"></i>
            <span>{{ __('Dashboard') }}</span>
        </h4>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('inventory.items.index') }}" class="btn btn-primary btn-sm">
            <i class="bx bx-box me-1"></i>{{ __('Items') }}
        </a>
        <a href="{{ route('inventory.index') }}" class="btn btn-label-primary btn-sm">
            <i class="bx bx-store-alt me-1"></i>{{ __('Warehouses') }}
        </a>
        <a href="{{ route('inventory.categories.index') }}" class="btn btn-label-secondary btn-sm">
            <i class="bx bx-category me-1"></i>{{ __('Item Categories') }}
        </a>
        <a href="{{ route('inventory.units.index') }}" class="btn btn-label-secondary btn-sm">
            <i class="bx bx-ruler me-1"></i>{{ __('Units') }}
        </a>
    </div>
</div>

<!-- ========================================================
     SIMPLE & CLEAN STATISTIC CARDS (3 CARDS PER ROW)
     ======================================================== -->
<div class="row g-3 mb-4">
    <!-- 1. Total Items -->
    <div class="col-12 col-sm-6 col-lg-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted fw-medium">{{ __('Total Items') }}</span>
                    <span class="badge bg-label-primary rounded p-2">
                        <i class="bx bx-box fs-4"></i>
                    </span>
                </div>
                <h3 class="mb-1 fw-bold text-heading">{{ number_format($stats['items']) }}</h3>
                <div class="d-flex align-items-center justify-content-between text-muted small mt-2">
                    <span>{{ $stats['categories'] }} {{ __('Item Categories') }}</span>
                    <a href="{{ route('inventory.items.index') }}" class="text-primary text-decoration-none fw-semibold">
                        {{ __('View All') }} <i class="bx bx-chevron-left align-middle"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Total Stock -->
    <div class="col-12 col-sm-6 col-lg-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted fw-medium">{{ __('Total Stock') }}</span>
                    <span class="badge bg-label-success rounded p-2">
                        <i class="bx bx-archive fs-4"></i>
                    </span>
                </div>
                <h3 class="mb-1 fw-bold text-success">{{ number_format($stats['stock']) }}</h3>
                <div class="d-flex align-items-center justify-content-between text-muted small mt-2">
                    <span>{{ __('Current Stock') }}</span>
                    <span>{{ $stats['warehouses'] }} {{ __('Warehouses') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Active Warehouses -->
    <div class="col-12 col-sm-6 col-lg-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted fw-medium">{{ __('Active Warehouses') }}</span>
                    <span class="badge bg-label-info rounded p-2">
                        <i class="bx bx-store fs-4"></i>
                    </span>
                </div>
                <h3 class="mb-1 fw-bold text-info">{{ number_format($stats['warehouses']) }}</h3>
                <div class="d-flex align-items-center justify-content-between text-muted small mt-2">
                    <span>{{ $stats['branches'] }} {{ __('Branches') }}</span>
                    <a href="{{ route('inventory.index') }}" class="text-info text-decoration-none fw-semibold">
                        {{ __('View All') }} <i class="bx bx-chevron-left align-middle"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Item Categories -->
    <div class="col-12 col-sm-6 col-lg-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted fw-medium">{{ __('Item Categories') }}</span>
                    <span class="badge bg-label-warning rounded p-2">
                        <i class="bx bx-category fs-4"></i>
                    </span>
                </div>
                <h3 class="mb-1 fw-bold text-warning">{{ number_format($stats['categories']) }}</h3>
                <div class="d-flex align-items-center justify-content-between text-muted small mt-2">
                    <span>{{ __('Category') }}</span>
                    <a href="{{ route('inventory.categories.index') }}" class="text-warning text-decoration-none fw-semibold">
                        {{ __('View All') }} <i class="bx bx-chevron-left align-middle"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 5. Units of Measure -->
    <div class="col-12 col-sm-6 col-lg-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted fw-medium">{{ __('Units of Measure') }}</span>
                    <span class="badge bg-label-secondary rounded p-2">
                        <i class="bx bx-ruler fs-4"></i>
                    </span>
                </div>
                <h3 class="mb-1 fw-bold text-heading">{{ number_format($stats['units']) }}</h3>
                <div class="d-flex align-items-center justify-content-between text-muted small mt-2">
                    <span>{{ __('Unit') }}</span>
                    <a href="{{ route('inventory.units.index') }}" class="text-secondary text-decoration-none fw-semibold">
                        {{ __('View All') }} <i class="bx bx-chevron-left align-middle"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 6. Low Stock Alerts -->
    <div class="col-12 col-sm-6 col-lg-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted fw-medium">{{ __('Low Stock Alerts') }}</span>
                    <span class="badge bg-label-danger rounded p-2">
                        <i class="bx bx-error-circle fs-4"></i>
                    </span>
                </div>
                <h3 class="mb-1 fw-bold text-danger">{{ number_format($stats['low_stock']) }}</h3>
                <div class="d-flex align-items-center justify-content-between text-muted small mt-2">
                    <span class="text-danger fw-semibold">{{ __('Low Stock Items') }}</span>
                    <span>&le; 5</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================
     CHARTS SECTION
     ======================================================== -->
<div class="row g-3 mb-4">
    <!-- Categories Breakdown Chart -->
    <div class="col-12 col-lg-7">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0 fw-bold text-heading">{{ __('Items by Category') }}</h5>
                <span class="badge bg-label-primary rounded-pill">{{ count($categories) }} {{ __('Item Categories') }}</span>
            </div>
            <div class="card-body">
                <div id="categoriesBarChart" style="min-height: 290px;"></div>
            </div>
        </div>
    </div>

    <!-- Stock by Branch Chart -->
    <div class="col-12 col-lg-5">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0 fw-bold text-heading">{{ __('Stock by Branch') }}</h5>
                <span class="badge bg-label-success rounded-pill">{{ count($branches) }} {{ __('Branches') }}</span>
            </div>
            <div class="card-body d-flex flex-column justify-content-center">
                <div id="branchStockChart" style="min-height: 260px;"></div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================
     OPERATIONAL TABLES
     ======================================================== -->
<div class="row g-3">
    <!-- Active Warehouses Table -->
    <div class="col-12 col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0 fw-bold text-heading">{{ __('Active Warehouses') }}</h5>
                <a href="{{ route('inventory.index') }}" class="btn btn-sm btn-label-primary">
                    <i class="bx bx-store me-1"></i>{{ __('Manage') }}
                </a>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('Warehouse Name') }}</th>
                            <th>{{ __('Warehouse Code') }}</th>
                            <th>{{ __('Branch') }}</th>
                            <th class="text-center">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($warehouses as $wh)
                            <tr>
                                <td>
                                    <span class="fw-semibold text-heading">{{ $wh->name }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-label-secondary">{{ $wh->code }}</span>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $wh->branch?->name ?? __('Headquarters') }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-label-success rounded-pill">{{ __('Active') }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">{{ __('No data found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Low Stock Items Table -->
    <div class="col-12 col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bx bx-error-circle text-danger fs-5"></i>
                    <h5 class="mb-0 fw-bold text-heading">{{ __('Low Stock Alerts') }}</h5>
                </div>
                <span class="badge bg-label-danger">{{ count($lowStockItems) }}</span>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('Item') }}</th>
                            <th>{{ __('Category') }}</th>
                            <th class="text-end">{{ __('Quantity') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lowStockItems as $item)
                            <tr>
                                <td>
                                    <span class="fw-semibold text-heading small">{{ $item->name }}</span>
                                    @if(!empty($item->code))
                                        <small class="text-muted d-block">{{ $item->code }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-label-secondary py-0 px-2 small">{{ $item->category?->name ?? '-' }}</span>
                                </td>
                                <td class="text-end text-danger fw-bold">
                                    {{ (float) ($item->total_quantity ?? 0) }} {{ $item->unit?->name ?? '' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">
                                    <i class="bx bx-check-circle text-success fs-3 d-block mb-1"></i>
                                    {{ __('No low stock items found') }}
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
(function () {
    const chartData = @json($chartData);
    if (!window.ApexCharts) return;

    // 1. Categories Bar Chart
    const categoriesOptions = {
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
        colors: ['#714B67'],
        series: [{
            name: "{{ __('Items') }}",
            data: chartData.categories_chart.series || []
        }],
        xaxis: {
            categories: chartData.categories_chart.labels || [],
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
    new ApexCharts(document.querySelector("#categoriesBarChart"), categoriesOptions).render();

    // 2. Branch Stock Chart
    const branchStockSeries = chartData.branches_chart.series || [];
    const hasBranchData = branchStockSeries.some(v => v > 0);

    const branchOptions = {
        chart: {
            type: 'donut',
            height: 260,
            fontFamily: 'inherit'
        },
        series: hasBranchData ? branchStockSeries : [1],
        labels: hasBranchData ? (chartData.branches_chart.labels || []) : ["{{ __('No data found') }}"],
        colors: hasBranchData ? ['#017E84', '#28A745', '#e59866', '#5A56DF', '#e46a76', '#714B67'] : ['#e0e0e0'],
        plotOptions: {
            pie: {
                donut: {
                    size: '65%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: "{{ __('Total Stock') }}",
                            formatter: function (w) {
                                if (!hasBranchData) return '0';
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
    new ApexCharts(document.querySelector("#branchStockChart"), branchOptions).render();
})();
</script>
@endsection
