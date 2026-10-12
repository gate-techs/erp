@extends('layouts/contentNavbarLayout')

@section('title', __('Dashboard') . ' - ' . config('variables.templateName'))

@section('vendor-style')
    @vite('resources/assets/vendor/libs/apex-charts/apex-charts.scss')
@endsection

@section('vendor-script')
    @vite('resources/assets/vendor/libs/apex-charts/apexcharts.js')
@endsection

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted small">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item text-muted small">{{ __('Academic Management') }}</li>
                <li class="breadcrumb-item text-muted small">{{ __('Fees & Packages') }}</li>
                <li class="breadcrumb-item active fw-bold small text-primary">{{ __('Dashboard') }}</li>
            </ol>
        </nav>
        <h4 class="fw-bold mb-0 text-heading">{{ __('Dashboard') }}</h4>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('academic.fee-types.index') }}" class="btn btn-primary btn-sm">
            <i class="bx bx-category me-1"></i>{{ __('Fee Types') }}
        </a>
        <a href="{{ route('academic.fees.index') }}" class="btn btn-label-primary btn-sm">
            <i class="bx bx-layer me-1"></i>{{ __('Manage Fees') }}
        </a>
        <a href="{{ route('academic.student-fees.index') }}" class="btn btn-label-secondary btn-sm">
            <i class="bx bx-group me-1"></i>{{ __('Student Fees') }}
        </a>
    </div>
</div>

<!-- ========================================================
     SIMPLE & CLEAN STATISTIC CARDS (3 CARDS PER ROW)
     ======================================================== -->
<div class="row g-3 mb-4">
    <!-- 1. Total Fees -->
    <div class="col-12 col-sm-6 col-lg-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted fw-medium">{{ __('Total Fees') }}</span>
                    <span class="badge bg-label-primary rounded p-2">
                        <i class="bx bx-receipt fs-4"></i>
                    </span>
                </div>
                <h3 class="mb-1 fw-bold text-heading">{{ number_format($stats['total_fees'], 2) }}</h3>
                <div class="d-flex align-items-center justify-content-between text-muted small mt-2">
                    <span>{{ $stats['fee_plans'] }} {{ __('Plans') }}</span>
                    <span>{{ $stats['fee_types'] }} {{ __('Fee Types') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Collected Fees -->
    <div class="col-12 col-sm-6 col-lg-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted fw-medium">{{ __('Collected Fees') }}</span>
                    <span class="badge bg-label-success rounded p-2">
                        <i class="bx bx-check-shield fs-4"></i>
                    </span>
                </div>
                <h3 class="mb-1 fw-bold text-success">{{ number_format($stats['paid_fees'], 2) }}</h3>
                <div class="d-flex align-items-center justify-content-between text-muted small mt-2">
                    <span class="text-success fw-semibold">{{ number_format($stats['collection_rate'], 1) }}% {{ __('Collection Rate') }}</span>
                    <span>{{ $stats['paid_payments'] }} {{ __('Paid Transactions') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Outstanding Balance -->
    <div class="col-12 col-sm-6 col-lg-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted fw-medium">{{ __('Outstanding Balance') }}</span>
                    <span class="badge bg-label-warning rounded p-2">
                        <i class="bx bx-wallet fs-4"></i>
                    </span>
                </div>
                <h3 class="mb-1 fw-bold text-warning">{{ number_format($stats['remaining_fees'], 2) }}</h3>
                <div class="d-flex align-items-center justify-content-between text-muted small mt-2">
                    <span>{{ __('Remaining Balance') }}</span>
                    <span>{{ $stats['unpaid_installments'] }} {{ __('Pending Installments') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Overdue Amount -->
    <div class="col-12 col-sm-6 col-lg-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted fw-medium">{{ __('Overdue Amount') }}</span>
                    <span class="badge bg-label-danger rounded p-2">
                        <i class="bx bx-alarm-exclamation fs-4"></i>
                    </span>
                </div>
                <h3 class="mb-1 fw-bold text-danger">{{ number_format($stats['overdue_amount'], 2) }}</h3>
                <div class="d-flex align-items-center justify-content-between text-muted small mt-2">
                    <span class="text-danger fw-semibold">{{ $stats['overdue_installments'] }} {{ __('Overdue Installments') }}</span>
                    <span>{{ __('Need follow-up') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 5. Pending Installments -->
    <div class="col-12 col-sm-6 col-lg-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted fw-medium">{{ __('Pending Installments') }}</span>
                    <span class="badge bg-label-info rounded p-2">
                        <i class="bx bx-time-five fs-4"></i>
                    </span>
                </div>
                <h3 class="mb-1 fw-bold text-info">{{ number_format($stats['unpaid_installments']) }}</h3>
                <div class="d-flex align-items-center justify-content-between text-muted small mt-2">
                    <span>{{ __('Installment') }}</span>
                    <span>{{ __('In payment schedule') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 6. Assigned Students -->
    <div class="col-12 col-sm-6 col-lg-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted fw-medium">{{ __('Assigned Students') }}</span>
                    <span class="badge bg-label-secondary rounded p-2">
                        <i class="bx bx-user-check fs-4"></i>
                    </span>
                </div>
                <h3 class="mb-1 fw-bold text-heading">{{ number_format($stats['assigned_students']) }}</h3>
                <div class="d-flex align-items-center justify-content-between text-muted small mt-2">
                    <span>{{ $stats['completed_assignments'] }} {{ __('Completed Plans') }}</span>
                    <span>{{ __('Students') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================
     CHARTS SECTION
     ======================================================== -->
<div class="row g-3 mb-4">
    <!-- Fee Types Financial Distribution Bar Chart -->
    <div class="col-12 col-lg-7">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0 fw-bold text-heading">{{ __('Fee Types Financial Distribution') }}</h5>
                <span class="badge bg-label-primary rounded-pill">{{ count($feeTypesBreakdown) }} {{ __('Fee Types') }}</span>
            </div>
            <div class="card-body">
                <div id="feeTypesBarChart" style="min-height: 290px;"></div>
            </div>
        </div>
    </div>

    <!-- Financial Collection Status Donut Chart -->
    <div class="col-12 col-lg-5">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0 fw-bold text-heading">{{ __('Financial Collection Status') }}</h5>
                <span class="badge bg-label-success rounded-pill">{{ number_format($stats['collection_rate'], 1) }}%</span>
            </div>
            <div class="card-body d-flex flex-column justify-content-center">
                <div id="collectionDonutChart" style="min-height: 240px;"></div>
                <div class="row text-center mt-3 pt-2 border-top g-2">
                    <div class="col-4">
                        <span class="text-muted small d-block">{{ __('Collected') }}</span>
                        <strong class="text-success small">{{ number_format($stats['paid_fees'], 2) }}</strong>
                    </div>
                    <div class="col-4">
                        <span class="text-muted small d-block">{{ __('Pending') }}</span>
                        <strong class="text-warning small">{{ number_format(max(0, $stats['remaining_fees'] - $stats['overdue_amount']), 2) }}</strong>
                    </div>
                    <div class="col-4">
                        <span class="text-muted small d-block">{{ __('Overdue') }}</span>
                        <strong class="text-danger small">{{ number_format($stats['overdue_amount'], 2) }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================
     PERFORMANCE BY FEE TYPE TABLE
     ======================================================== -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 fw-bold text-heading">{{ __('Performance by Fee Type') }}</h5>
        <a href="{{ route('academic.fee-types.index') }}" class="btn btn-sm btn-label-primary">
            <i class="bx bx-cog me-1"></i>{{ __('Manage Fee Types') }}
        </a>
    </div>
    <div class="table-responsive text-nowrap">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>{{ __('Fee Type') }}</th>
                    <th class="text-center">{{ __('Plans') }}</th>
                    <th class="text-center">{{ __('Students') }}</th>
                    <th class="text-end">{{ __('Total Amount') }}</th>
                    <th class="text-end">{{ __('Paid Amount') }}</th>
                    <th class="text-center" style="width: 160px;">{{ __('Collection Rate') }}</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse($feeTypesBreakdown as $type)
                    <tr>
                        <td>
                            <span class="fw-semibold text-heading">{{ $type['name'] }}</span>
                            @if(!empty($type['code']))
                                <span class="badge bg-label-secondary ms-1">{{ $type['code'] }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge bg-label-primary rounded-pill">{{ $type['plans_count'] }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-label-info rounded-pill">{{ $type['students_count'] }}</span>
                        </td>
                        <td class="text-end fw-semibold">{{ number_format($type['total_amount'], 2) }}</td>
                        <td class="text-end text-success fw-semibold">{{ number_format($type['paid_amount'], 2) }}</td>
                        <td class="text-center">
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1" style="height: 6px;">
                                    <div class="progress-bar bg-success" style="width: {{ min(100, $type['collection_rate']) }}%"></div>
                                </div>
                                <small class="fw-semibold text-muted">{{ number_format($type['collection_rate'], 1) }}%</small>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">{{ __('No data found') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- ========================================================
     OVERDUE INSTALLMENTS TABLE (IF ANY)
     ======================================================== -->
@if(!empty($overdueList) && count($overdueList) > 0)
<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
        <div class="d-flex align-items-center gap-2">
            <i class="bx bx-alarm-exclamation text-danger fs-5"></i>
            <h5 class="mb-0 fw-bold text-heading">{{ __('Overdue & Due Installments') }}</h5>
        </div>
        <span class="badge bg-label-danger">{{ count($overdueList) }}</span>
    </div>
    <div class="table-responsive text-nowrap">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>{{ __('Student') }}</th>
                    <th>{{ __('Fee Type') }}</th>
                    <th>{{ __('Due Date') }}</th>
                    <th class="text-end">{{ __('Remaining') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($overdueList as $item)
                    <tr>
                        <td>
                            <span class="fw-semibold text-heading">{{ $item['student_name'] }}</span>
                            @if(!empty($item['student_code']))
                                <small class="text-muted d-block">#{{ $item['student_code'] }}</small>
                            @endif
                        </td>
                        <td>
                            <span>{{ $item['fee_type_name'] }}</span>
                            <small class="text-muted d-block">{{ __('Installment') }} #{{ $item['installment_number'] }}</small>
                        </td>
                        <td>
                            <span class="badge bg-label-danger">{{ $item['due_date'] }}</span>
                        </td>
                        <td class="text-end text-danger fw-bold">
                            {{ number_format($item['remaining'], 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection

@section('page-script')
<script>
(function () {
    const chartData = @json($chartData);
    if (!window.ApexCharts) return;

    const formatAmount = v => Number(v || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    // 1. Fee Types Bar Chart
    const barOptions = {
        chart: {
            type: 'bar',
            height: 290,
            toolbar: { show: false },
            fontFamily: 'inherit'
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '45%',
                borderRadius: 6
            }
        },
        dataLabels: { enabled: false },
        stroke: { show: true, width: 2, colors: ['transparent'] },
        colors: ['#714B67', '#28A745'],
        series: [
            {
                name: "{{ __('Total Amount') }}",
                data: chartData.fee_types_chart.total_series || []
            },
            {
                name: "{{ __('Paid Amount') }}",
                data: chartData.fee_types_chart.paid_series || []
            }
        ],
        xaxis: {
            categories: chartData.fee_types_chart.categories || [],
            labels: {
                style: { colors: '#6c757d', fontSize: '12px' }
            }
        },
        yaxis: {
            labels: {
                formatter: val => formatAmount(val),
                style: { colors: '#6c757d', fontSize: '12px' }
            }
        },
        fill: { opacity: 1 },
        tooltip: {
            y: {
                formatter: val => formatAmount(val)
            }
        },
        legend: {
            position: 'top',
            horizontalAlign: 'right'
        },
        grid: {
            borderColor: '#f1ecf0',
            strokeDashArray: 4
        }
    };
    new ApexCharts(document.querySelector("#feeTypesBarChart"), barOptions).render();

    // 2. Collection Donut Chart
    const donutOptions = {
        chart: {
            type: 'donut',
            height: 240,
            fontFamily: 'inherit'
        },
        series: chartData.collection_chart.series || [0, 0, 0],
        labels: [
            "{{ __('Collected') }}",
            "{{ __('Pending / Upcoming') }}",
            "{{ __('Overdue') }}"
        ],
        colors: ['#28A745', '#e59866', '#e46a76'],
        plotOptions: {
            pie: {
                donut: {
                    size: '68%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: "{{ __('Collected') }}",
                            formatter: function (w) {
                                const total = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                const paid = w.globals.seriesTotals[0] || 0;
                                return (total > 0 ? ((paid / total) * 100).toFixed(1) : 0) + '%';
                            }
                        }
                    }
                }
            }
        },
        dataLabels: { enabled: false },
        legend: {
            position: 'bottom',
            horizontalAlign: 'center',
            fontSize: '13px'
        },
        tooltip: {
            y: {
                formatter: val => formatAmount(val)
            }
        }
    };
    new ApexCharts(document.querySelector("#collectionDonutChart"), donutOptions).render();
})();
</script>
@endsection
