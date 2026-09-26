@extends('layouts/contentNavbarLayout')

@section('title', __('Accounting Dashboard') . ' - ' . config('variables.templateName'))

@section('vendor-style')
@vite('resources/assets/vendor/libs/apex-charts/apex-charts.scss')
<style>
    .financial-num {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
        font-weight: 700;
        letter-spacing: 0.3px;
        font-variant-numeric: tabular-nums;
    }
</style>
@endsection

@section('vendor-script')
@vite('resources/assets/vendor/libs/apex-charts/apexcharts.js')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@endsection

@section('content')
<!-- Standard ERP Header -->
<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <i class="icon-base bx bx-calculator text-primary font-24"></i>
            <span>{{ __('Accounting Dashboard') }}</span>
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('Accounting') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Dashboard') }}</li>
            </ol>
        </nav>
    </div>

    <div class="d-flex align-items-center gap-2">
        <form action="{{ route('accounting.dashboard') }}" method="GET" class="d-inline-block">
            <select name="entity_id" id="dashboard_entity_id" class="form-select select2" data-control="select2" onchange="this.form.submit()" style="min-width: 220px;">
                <option value="">{{ __('All Accounting Entities') }}</option>
                @foreach($entities as $ent)
                    <option value="{{ $ent->id }}" @selected($selectedEntityId == $ent->id)>
                        {{ $ent->translate(app()->getLocale())?->name ?? $ent->translate('ar')?->name ?? $ent->code }} ({{ $ent->code }})
                    </option>
                @endforeach
            </select>
        </form>

        <button type="button" class="btn btn-outline-info d-flex align-items-center gap-1 shadow-xs text-nowrap" data-bs-toggle="offcanvas" data-bs-target="#aiFinancialAssistantOffcanvas">
            <i class="icon-base bx bx-bot font-18"></i>
            <span>{{ __('Smart Assistant') }}</span>
        </button>

        <a href="{{ route('accounting.guide') }}" class="btn btn-outline-primary d-flex align-items-center gap-1 shadow-xs text-nowrap">
            <i class="icon-base bx bx-book-open font-18"></i>
            <span>{{ __('System Guide') }}</span>
        </a>

        <a href="{{ route('accounting.journal-entries.create') }}" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm text-nowrap">
            <i class="icon-base bx bx-plus font-18"></i>
            <span>{{ __('New Entry') }}</span>
        </a>
    </div>
</div>

<!-- 1. KPI Cards Row -->
<div class="row g-4 mb-4">
    <!-- Revenue -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted fw-semibold small">{{ __('Total Revenues') }}</span>
                    <div class="avatar avatar-sm bg-label-success rounded d-flex align-items-center justify-content-center">
                        <i class="icon-base bx bx-trending-up font-18 text-success"></i>
                    </div>
                </div>
                <h4 class="fw-bold text-heading mb-1 financial-num">{{ number_format($kpis['revenue'], 2) }}</h4>
                <small class="text-muted font-11">{{ $currencySymbol }} &bull; {{ __('Income Accounts') }}</small>
            </div>
        </div>
    </div>

    <!-- Expenses -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted fw-semibold small">{{ __('Total Expenses') }}</span>
                    <div class="avatar avatar-sm bg-label-danger rounded d-flex align-items-center justify-content-center">
                        <i class="icon-base bx bx-trending-down font-18 text-danger"></i>
                    </div>
                </div>
                <h4 class="fw-bold text-heading mb-1 financial-num">{{ number_format($kpis['expenses'], 2) }}</h4>
                <small class="text-muted font-11">{{ $currencySymbol }} &bull; {{ __('Operating Expenses') }}</small>
            </div>
        </div>
    </div>

    <!-- Net Profit -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted fw-semibold small">{{ __('Net Profit / (Loss)') }}</span>
                    <div class="avatar avatar-sm {{ $kpis['net_profit'] >= 0 ? 'bg-label-primary' : 'bg-label-danger' }} rounded d-flex align-items-center justify-content-center">
                        <i class="icon-base bx {{ $kpis['net_profit'] >= 0 ? 'bx-coin-stack text-primary' : 'bx-error-circle text-danger' }} font-18"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h4 class="fw-bold mb-0 financial-num {{ $kpis['net_profit'] >= 0 ? 'text-primary' : 'text-danger' }}">
                        {{ number_format($kpis['net_profit'], 2) }}
                    </h4>
                    <span class="badge {{ $kpis['net_profit'] >= 0 ? 'bg-label-success' : 'bg-label-danger' }} font-11">
                        {{ $kpis['profit_margin'] }}%
                    </span>
                </div>
                <small class="text-muted font-11">{{ $currencySymbol }} &bull; {{ __('Net Operations') }}</small>
            </div>
        </div>
    </div>

    <!-- Liquidity -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted fw-semibold small">{{ __('Cash & Bank Liquidity') }}</span>
                    <div class="avatar avatar-sm bg-label-info rounded d-flex align-items-center justify-content-center">
                        <i class="icon-base bx bx-wallet font-18 text-info"></i>
                    </div>
                </div>
                <h4 class="fw-bold text-heading mb-1 financial-num">{{ number_format($kpis['total_liquidity'], 2) }}</h4>
                <small class="text-muted font-11">{{ $currencySymbol }} &bull; {{ __('Total Available Cash') }}</small>
            </div>
        </div>
    </div>
</div>

<!-- 2. Chart 1: Revenue vs Expense Trend (Full Width بالعرض كامل) -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="fw-bold mb-0 text-heading">{{ __('Revenue vs Expense Trend (Last 6 Months)') }}</h6>
                    <small class="text-muted">{{ __('Monthly comparison of realized revenue against recorded expenses') }}</small>
                </div>
                <span class="badge bg-label-primary font-11">{{ $currencySymbol }}</span>
            </div>
            <div class="card-body pt-3">
                <div id="revenueExpenseChart" style="min-height: 320px;"></div>
            </div>
        </div>
    </div>
</div>

<!-- 3. Additional Analytics Charts Row (3 Charts Underneath) -->
<div class="row g-4 mb-4">
    <!-- Chart 2: Expense Breakdown -->
    <div class="col-12 col-lg-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-transparent border-bottom py-3">
                <h6 class="fw-bold mb-0 text-heading">{{ __('Expense Breakdown') }}</h6>
                <small class="text-muted">{{ __('Breakdown of top expense accounts') }}</small>
            </div>
            <div class="card-body pt-3 d-flex flex-column justify-content-center">
                <div id="expenseBreakdownChart" style="min-height: 290px;"></div>
            </div>
        </div>
    </div>

    <!-- Chart 3: Treasury Liquidity Breakdown -->
    <div class="col-12 col-lg-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-transparent border-bottom py-3">
                <h6 class="fw-bold mb-0 text-heading">{{ __('Cash Liquidity Distribution') }}</h6>
                <small class="text-muted">{{ __('Proportion of available balances in cash vaults and bank accounts') }}</small>
            </div>
            <div class="card-body pt-3 d-flex flex-column justify-content-center">
                <div id="liquidityDistributionChart" style="min-height: 290px;"></div>
            </div>
        </div>
    </div>

    <!-- Chart 4: Journal Operations Breakdown -->
    <div class="col-12 col-lg-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-transparent border-bottom py-3">
                <h6 class="fw-bold mb-0 text-heading">{{ __('Operations Volume by Journal') }}</h6>
                <small class="text-muted">{{ __('Number of posted entries per journal') }}</small>
            </div>
            <div class="card-body pt-3 d-flex flex-column justify-content-center">
                <div id="journalActivityChart" style="min-height: 290px;"></div>
            </div>
        </div>
    </div>
</div>

<!-- 4. Chart 5: Cash Inflow vs Outflow Trend (Full Width) -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="fw-bold mb-0 text-heading">{{ __('Cash Inflows & Outflows (Monthly Cash Flow)') }}</h6>
                    <small class="text-muted">{{ __('Total inflows and outflows from bank accounts and cash vaults') }}</small>
                </div>
                <span class="badge bg-label-info font-11">{{ $currencySymbol }}</span>
            </div>
            <div class="card-body pt-3">
                <div id="cashFlowChart" style="min-height: 290px;"></div>
            </div>
        </div>
    </div>
</div>

<!-- 5. Operational Tables (Stacked cleanly) -->
<!-- Table 1: Cash & Bank Accounts (Full Width) -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
        <div>
            <h6 class="fw-bold mb-0 text-heading">{{ __('Cash & Bank Account Balances') }}</h6>
            <small class="text-muted">{{ __('General Ledger recorded balances for cash accounts') }}</small>
        </div>
        <a href="{{ route('accounting.cash-accounts.index') }}" class="btn btn-sm btn-label-secondary">{{ __('View All') }}</a>
    </div>
    <div class="table-responsive" style="max-height: 360px; overflow-y: auto;">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light sticky-top bg-light">
                <tr>
                    <th style="width: 140px;">{{ __('Account Code') }}</th>
                    <th>{{ __('Account Name') }}</th>
                    <th>{{ __('Bank / Branch') }}</th>
                    <th class="text-center" style="width: 120px;">{{ __('Type') }}</th>
                    <th class="text-end" style="width: 180px;">{{ __('Book Balance') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cashAccounts as $ca)
                    <tr>
                        <td><span class="fw-bold font-monospace text-primary font-12">{{ $ca['code'] }}</span></td>
                        <td><span class="fw-semibold text-heading">{{ $ca['name'] }}</span></td>
                        <td><span class="text-muted">{{ $ca['bank_name'] ?: '-' }}</span></td>
                        <td class="text-center">
                            @if($ca['type'] === 'bank')
                                <span class="badge bg-label-info font-11">{{ __('Bank') }}</span>
                            @else
                                <span class="badge bg-label-warning font-11">{{ __('Cash Safe') }}</span>
                            @endif
                        </td>
                        <td class="text-end fw-bold financial-num {{ $ca['balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format($ca['balance'], 2) }} {{ $currencySymbol }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">{{ __('No accounts recorded.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Table 2: Recent Journal Entries (Full Width) -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
        <div>
            <h6 class="fw-bold mb-0 text-heading">{{ __('Recent Journal Entries') }}</h6>
            <small class="text-muted">{{ __('Latest transactions and daily entries recorded in the system') }}</small>
        </div>
        <a href="{{ route('accounting.journal-entries.index') }}" class="btn btn-sm btn-label-secondary">{{ __('View All') }}</a>
    </div>
    <div class="table-responsive" style="max-height: 360px; overflow-y: auto;">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light sticky-top bg-light">
                <tr>
                    <th style="width: 160px;">{{ __('Entry #') }}</th>
                    <th style="width: 180px;">{{ __('Journal') }}</th>
                    <th style="width: 120px;">{{ __('Date') }}</th>
                    <th>{{ __('Description / Narration') }}</th>
                    <th class="text-end" style="width: 160px;">{{ __('Total Amount') }}</th>
                    <th class="text-center" style="width: 120px;">{{ __('Status') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentEntries as $entry)
                    <tr>
                        <td>
                            <a href="{{ route('accounting.journal-entries.show', $entry->id) }}" class="fw-bold text-primary font-monospace">
                                {{ $entry->entry_number }}
                            </a>
                        </td>
                        <td>
                            <span class="badge bg-label-secondary font-11">
                                {{ $entry->journal?->translate(app()->getLocale())?->name ?? $entry->journal?->code }}
                            </span>
                        </td>
                        <td><span class="font-monospace text-heading font-12">{{ $entry->entry_date->format('Y-m-d') }}</span></td>
                        <td><span class="text-truncate d-inline-block font-12" style="max-width: 320px;">{{ $entry->narration ?: '-' }}</span></td>
                        <td class="text-end fw-bold text-heading financial-num">{{ number_format($entry->total_debit, 2) }} {{ $currencySymbol }}</td>
                        <td class="text-center">
                            <span class="badge {{ $entry->status->badgeClass() }} font-11">{{ $entry->status->label() }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">{{ __('No recent journal entries recorded.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const isRtl = $('html').attr('dir') === 'rtl' || $('html').attr('lang') === 'ar';

    // 1. Revenue & Expense Trend Chart (Full Width)
    const trendLabels = @json($trend['labels']);
    const revenueData = @json($trend['revenues']);
    const expenseData = @json($trend['expenses']);

    const trendChartEl = document.querySelector('#revenueExpenseChart');
    if (trendChartEl) {
        const trendOptions = {
            chart: {
                height: 320,
                type: 'area',
                toolbar: { show: false },
                fontFamily: 'inherit'
            },
            dataLabels: { enabled: false },
            stroke: {
                curve: 'smooth',
                width: 2.5
            },
            series: [
                {
                    name: '{{ __("Revenues") }}',
                    data: revenueData
                },
                {
                    name: '{{ __("Expenses") }}',
                    data: expenseData
                }
            ],
            colors: ['#10b981', '#ef4444'],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.35,
                    opacityTo: 0.05,
                    stops: [0, 95, 100]
                }
            },
            xaxis: {
                categories: trendLabels,
                labels: {
                    style: { colors: '#64748b', fontSize: '12px' }
                },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: {
                    formatter: val => val.toLocaleString(),
                    style: { colors: '#64748b', fontSize: '12px' }
                }
            },
            grid: {
                borderColor: '#e2e8f0',
                strokeDashArray: 4
            },
            tooltip: {
                y: {
                    formatter: val => val.toLocaleString() + ' {{ $currencySymbol }}'
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: isRtl ? 'right' : 'left',
                labels: { colors: '#64748b' }
            }
        };

        const trendChart = new ApexCharts(trendChartEl, trendOptions);
        trendChart.render();
    }

    // 2. Expense Breakdown Donut Chart
    const expenseLabels = @json($expenseBreakdown['labels']);
    const expenseSeries = @json($expenseBreakdown['series']);

    const expenseChartEl = document.querySelector('#expenseBreakdownChart');
    if (expenseChartEl) {
        if (expenseSeries.length === 0 || expenseSeries.every(v => v === 0)) {
            expenseChartEl.innerHTML = '<div class="text-center py-5 text-muted"><i class="icon-base bx bx-pie-chart text-secondary font-32 mb-2 d-block"></i><span>{{ __("No expenses recorded for this period.") }}</span></div>';
        } else {
            const expenseOptions = {
                chart: {
                    height: 290,
                    type: 'donut',
                    fontFamily: 'inherit'
                },
                series: expenseSeries,
                labels: expenseLabels,
                colors: ['#696cff', '#8592a3', '#71dd37', '#ff3e1d', '#ffab00', '#03c3ec'],
                stroke: { width: 0 },
                dataLabels: { enabled: false },
                legend: {
                    position: 'bottom',
                    labels: { colors: '#64748b' }
                },
                tooltip: {
                    y: {
                        formatter: val => val.toLocaleString() + ' {{ $currencySymbol }}'
                    }
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '68%',
                            labels: {
                                show: true,
                                value: {
                                    fontSize: '18px',
                                    fontWeight: 700,
                                    formatter: val => parseFloat(val).toLocaleString()
                                },
                                total: {
                                    show: true,
                                    label: '{{ __("Total") }}',
                                    fontSize: '13px',
                                    formatter: w => {
                                        const sum = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                        return sum.toLocaleString();
                                    }
                                }
                            }
                        }
                    }
                }
            };

            const expenseChart = new ApexCharts(expenseChartEl, expenseOptions);
            expenseChart.render();
        }
    }

    // 3. Liquidity Distribution Donut Chart
    const liqLabels = @json($liquidityDistribution['labels']);
    const liqSeries = @json($liquidityDistribution['series']);

    const liqChartEl = document.querySelector('#liquidityDistributionChart');
    if (liqChartEl) {
        if (liqSeries.length === 0 || liqSeries.every(v => v === 0)) {
            liqChartEl.innerHTML = '<div class="text-center py-5 text-muted"><i class="icon-base bx bx-wallet text-secondary font-32 mb-2 d-block"></i><span>{{ __("No balances currently available.") }}</span></div>';
        } else {
            const liqOptions = {
                chart: {
                    height: 290,
                    type: 'donut',
                    fontFamily: 'inherit'
                },
                series: liqSeries,
                labels: liqLabels,
                colors: ['#00A09D', '#714B67', '#03c3ec', '#696cff', '#10b981', '#f59e0b'],
                stroke: { width: 0 },
                dataLabels: { enabled: false },
                legend: {
                    position: 'bottom',
                    labels: { colors: '#64748b' }
                },
                tooltip: {
                    y: {
                        formatter: val => val.toLocaleString() + ' {{ $currencySymbol }}'
                    }
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '68%',
                            labels: {
                                show: true,
                                value: {
                                    fontSize: '18px',
                                    fontWeight: 700,
                                    formatter: val => parseFloat(val).toLocaleString()
                                },
                                total: {
                                    show: true,
                                    label: '{{ __("Total Liquidity") }}',
                                    fontSize: '12px',
                                    formatter: w => {
                                        const sum = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                        return sum.toLocaleString();
                                    }
                                }
                            }
                        }
                    }
                }
            };

            const liqChart = new ApexCharts(liqChartEl, liqOptions);
            liqChart.render();
        }
    }

    // 4. Journal Operations Volume Bar Chart
    const journalLabels = @json($journalActivity['labels']);
    const journalSeries = @json($journalActivity['series']);

    const journalChartEl = document.querySelector('#journalActivityChart');
    if (journalChartEl) {
        if (journalSeries.length === 0) {
            journalChartEl.innerHTML = '<div class="text-center py-5 text-muted"><i class="icon-base bx bx-book-open text-secondary font-32 mb-2 d-block"></i><span>{{ __("No operations recorded in journals.") }}</span></div>';
        } else {
            const journalOptions = {
                chart: {
                    height: 290,
                    type: 'bar',
                    toolbar: { show: false },
                    fontFamily: 'inherit'
                },
                plotOptions: {
                    bar: {
                        borderRadius: 6,
                        columnWidth: '45%',
                        distributed: true
                    }
                },
                dataLabels: { enabled: false },
                series: [{
                    name: '{{ __("Number of Entries") }}',
                    data: journalSeries
                }],
                colors: ['#696cff', '#03c3ec', '#71dd37', '#ffab00', '#8592a3', '#ff3e1d'],
                xaxis: {
                    categories: journalLabels,
                    labels: {
                        style: { colors: '#64748b', fontSize: '11px' }
                    },
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: {
                    labels: {
                        formatter: val => Math.round(val),
                        style: { colors: '#64748b', fontSize: '11px' }
                    }
                },
                grid: {
                    borderColor: '#e2e8f0',
                    strokeDashArray: 4
                },
                legend: { show: false }
            };

            const journalChart = new ApexCharts(journalChartEl, journalOptions);
            journalChart.render();
        }
    }

    // 5. Cash Flow Inflow vs Outflow Trend Chart
    const cfLabels = @json($cashFlowTrend['labels']);
    const cfInflows = @json($cashFlowTrend['inflows']);
    const cfOutflows = @json($cashFlowTrend['outflows']);

    const cfChartEl = document.querySelector('#cashFlowChart');
    if (cfChartEl) {
        const cfOptions = {
            chart: {
                height: 290,
                type: 'bar',
                toolbar: { show: false },
                fontFamily: 'inherit'
            },
            plotOptions: {
                bar: {
                    borderRadius: 5,
                    columnWidth: '45%'
                }
            },
            dataLabels: { enabled: false },
            series: [
                {
                    name: '{{ __("Cash & Bank Inflows (In)") }}',
                    data: cfInflows
                },
                {
                    name: '{{ __("Cash & Bank Outflows (Out)") }}',
                    data: cfOutflows
                }
            ],
            colors: ['#28c76f', '#ea5455'],
            xaxis: {
                categories: cfLabels,
                labels: {
                    style: { colors: '#64748b', fontSize: '12px' }
                },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: {
                    formatter: val => val.toLocaleString(),
                    style: { colors: '#64748b', fontSize: '11px' }
                }
            },
            grid: {
                borderColor: '#e2e8f0',
                strokeDashArray: 4
            },
            tooltip: {
                y: {
                    formatter: val => val.toLocaleString() + ' {{ $currencySymbol }}'
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: isRtl ? 'right' : 'left',
                labels: { colors: '#64748b' }
            }
        };

        const cfChart = new ApexCharts(cfChartEl, cfOptions);
        cfChart.render();
    }
});
</script>
@endsection
