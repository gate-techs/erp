@extends('layouts/contentNavbarLayout')

@section('title', __('Request Statistics') . ' - ' . config('variables.templateName'))

@section('vendor-style')
    @vite('resources/assets/vendor/libs/apex-charts/apex-charts.scss')
@endsection

@section('vendor-script')
    @vite('resources/assets/vendor/libs/apex-charts/apexcharts.js')
@endsection

@section('content')
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 d-flex align-items-center gap-2 text-heading"><i class="icon-base bx bx-bar-chart-alt-2 text-primary"></i>{{ __('Request Statistics') }}</h4>
            <p class="text-muted mb-0">{{ __('A complete overview of your potential customers.') }}</p>
        </div>
        @can('crm.leads.view')
        <a href="{{ route('crm.leads.index') }}" class="btn btn-label-primary d-flex align-items-center gap-2"><i class="icon-base bx bx-group"></i>{{ __('Customers') }}</a>
        @endcan
    </div>

    <div id="leadsStatistics">
        <div class="row g-3 mb-3">
            <div class="col-12 col-sm-6 col-xl-3"><div class="card border-0 shadow-sm h-100"><div class="card-body d-flex justify-content-between align-items-center"><div><small class="text-muted d-block mb-2">{{ __('Total Requests') }}</small><h3 id="requestStatTotal" class="mb-0 fw-bold text-heading">—</h3><small class="text-muted">{{ __('Requests and complaints') }}</small></div><span class="avatar rounded bg-label-primary"><i class="icon-base bx bx-layer font-22"></i></span></div></div></div>
            <div class="col-12 col-sm-6 col-xl-3"><div class="card border-0 shadow-sm h-100"><div class="card-body d-flex justify-content-between align-items-center"><div><small class="text-muted d-block mb-2">{{ __('New This Month') }}</small><h3 id="requestStatMonth" class="mb-0 fw-bold text-heading">—</h3><small id="requestStatGrowth" class="text-muted"></small></div><span class="avatar rounded bg-label-success"><i class="icon-base bx bx-trending-up font-22"></i></span></div></div></div>
            <div class="col-12 col-sm-6 col-xl-3"><div class="card border-0 shadow-sm h-100"><div class="card-body d-flex justify-content-between align-items-center"><div><small class="text-muted d-block mb-2">{{ __('Pending Requests') }}</small><h3 id="requestStatPending" class="mb-0 fw-bold text-heading">—</h3><small class="text-muted">{{ __('Need follow-up') }}</small></div><span class="avatar rounded bg-label-warning"><i class="icon-base bx bx-time-five font-22"></i></span></div></div></div>
            <div class="col-12 col-sm-6 col-xl-3"><div class="card border-0 shadow-sm h-100"><div class="card-body d-flex justify-content-between align-items-center"><div><small class="text-muted d-block mb-2">{{ __('Complaints') }}</small><h3 id="requestStatComplaints" class="mb-0 fw-bold text-heading">—</h3><small class="text-muted">{{ __('Registration complaints') }}</small></div><span class="avatar rounded bg-label-danger"><i class="icon-base bx bx-error-alt font-22"></i></span></div></div></div>
        </div>

        <div class="row g-3">
            <div class="col-12 col-xl-8"><div class="card border-0 shadow-sm h-100"><div class="card-header border-bottom"><h6 class="fw-bold text-heading mb-1">{{ __('Request Growth') }}</h6><small class="text-muted">{{ __('New requests over the last 12 months') }}</small></div><div class="card-body"><div id="requestsGrowthChart" style="min-height: 300px"></div></div></div></div>
            <div class="col-12 col-xl-4"><div class="card border-0 shadow-sm h-100"><div class="card-header border-bottom"><h6 class="fw-bold text-heading mb-1">{{ __('Requests by Status') }}</h6><small class="text-muted">{{ __('Current workflow status') }}</small></div><div class="card-body"><div id="requestsStatusChart" style="min-height: 300px"></div></div></div></div>
            <div class="col-12 col-xl-6"><div class="card border-0 shadow-sm"><div class="card-header border-bottom"><h6 class="fw-bold text-heading mb-1">{{ __('Requests by Type') }}</h6><small class="text-muted">{{ __('Requests versus complaints') }}</small></div><div class="card-body"><div id="requestsTypeChart" style="min-height: 280px"></div></div></div></div>
            <div class="col-12 col-xl-6"><div class="card border-0 shadow-sm"><div class="card-header border-bottom"><h6 class="fw-bold text-heading mb-1">{{ __('Requests by Branch') }}</h6><small class="text-muted">{{ __('Highest request volume') }}</small></div><div class="card-body"><div id="requestsBranchChart" style="min-height: 280px"></div></div></div></div>
            <div class="col-12"><div class="card border-0 shadow-sm"><div class="card-header border-bottom"><h6 class="fw-bold text-heading mb-1">{{ __('Requests by Department') }}</h6><small class="text-muted">{{ __('Department workload') }}</small></div><div class="card-body"><div id="requestsDepartmentChart" style="min-height: 280px"></div></div></div></div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        window.addEventListener('load', function () {
            const $ = window.jQuery || window.$;
            if (!$) return;

            const numberFormat = new Intl.NumberFormat(document.documentElement.lang || 'en');
            $.getJSON('{{ route('crm.leads.statistics.data') }}').done(function (response) {
                if (!response.success || typeof ApexCharts === 'undefined') return;

                const stats = response.data;
                $('#requestStatTotal').text(numberFormat.format(stats.total));
                $('#requestStatMonth').text(numberFormat.format(stats.this_month));
                $('#requestStatPending').text(numberFormat.format(stats.status_counts.find(item => item.label === '{{ __('Pending') }}')?.value || 0));
                $('#requestStatComplaints').text(numberFormat.format(stats.type_counts.find(item => item.label === '{{ __('Registration Complaint') }}')?.value || 0));
                $('#requestStatGrowth').text(`${stats.growth >= 0 ? '+' : ''}${stats.growth}% {{ __('vs previous 30 days') }}`)
                    .toggleClass('text-success', stats.growth >= 0).toggleClass('text-danger', stats.growth < 0);

                new ApexCharts(document.querySelector('#requestsGrowthChart'), {
                    chart: { type: 'area', height: 300, toolbar: { show: false }, fontFamily: 'inherit' },
                    series: [{ name: '{{ __('Requests') }}', data: stats.monthly.map(item => item.value) }],
                    xaxis: { categories: stats.monthly.map(item => item.label), labels: { rotate: -35 } },
                    yaxis: { min: 0, forceNiceScale: true }, colors: ['#696cff'], stroke: { curve: 'smooth', width: 3 },
                    fill: { type: 'gradient', gradient: { opacityFrom: 0.35, opacityTo: 0.05 } }, dataLabels: { enabled: false },
                    grid: { borderColor: 'rgba(67, 89, 113, .12)' }, tooltip: { y: { formatter: value => numberFormat.format(value) } }
                }).render();

                new ApexCharts(document.querySelector('#requestsStatusChart'), {
                    chart: { type: 'donut', height: 300, fontFamily: 'inherit' },
                    series: stats.status_counts.map(item => item.value), labels: stats.status_counts.map(item => item.label),
                    colors: ['#ffab00', '#03c3ec', '#71dd37'], legend: { position: 'bottom' }, dataLabels: { enabled: false },
                    plotOptions: { pie: { donut: { size: '68%', labels: { show: true, total: { show: true, label: '{{ __('Requests') }}' } } } } }
                }).render();

                const types = stats.type_counts.length ? stats.type_counts : [{ label: '{{ __('No data') }}', value: 0 }];
                new ApexCharts(document.querySelector('#requestsTypeChart'), {
                    chart: { type: 'donut', height: 280, fontFamily: 'inherit' }, series: types.map(item => item.value), labels: types.map(item => item.label),
                    colors: ['#696cff', '#ea5455'], legend: { position: 'bottom' }, dataLabels: { enabled: true }
                }).render();

                const branches = stats.branch_counts.length ? stats.branch_counts : [{ label: '{{ __('No data') }}', value: 0 }];
                new ApexCharts(document.querySelector('#requestsBranchChart'), {
                    chart: { type: 'bar', height: 280, toolbar: { show: false }, fontFamily: 'inherit' },
                    series: [{ name: '{{ __('Requests') }}', data: branches.map(item => item.value) }], xaxis: { categories: branches.map(item => item.label) }, colors: ['#03c3ec'],
                    plotOptions: { bar: { borderRadius: 5, horizontal: true, barHeight: '55%' } }, dataLabels: { enabled: true },
                    grid: { borderColor: 'rgba(67, 89, 113, .12)' }, tooltip: { y: { formatter: value => numberFormat.format(value) } }
                }).render();

                const departments = stats.department_counts.length ? stats.department_counts : [{ label: '{{ __('No data') }}', value: 0 }];
                new ApexCharts(document.querySelector('#requestsDepartmentChart'), {
                    chart: { type: 'bar', height: 280, toolbar: { show: false }, fontFamily: 'inherit' },
                    series: [{ name: '{{ __('Requests') }}', data: departments.map(item => item.value) }], xaxis: { categories: departments.map(item => item.label) }, colors: ['#71dd37'],
                    plotOptions: { bar: { borderRadius: 5, horizontal: true, barHeight: '55%' } }, dataLabels: { enabled: true },
                    grid: { borderColor: 'rgba(67, 89, 113, .12)' }, tooltip: { y: { formatter: value => numberFormat.format(value) } }
                }).render();
            });
        });
    </script>
@endsection
