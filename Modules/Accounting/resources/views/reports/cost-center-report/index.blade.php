@extends('layouts/contentNavbarLayout')

@section('title', __('Cost Center Performance Report') . ' - ' . config('variables.templateName'))

@section('page-style')
<style>
#costCenterTable {
    font-size: 12.5px;
}
#costCenterTable th {
    padding: 0.5rem 0.65rem;
    font-size: 12px;
    white-space: nowrap;
    vertical-align: middle;
}
#costCenterTable td {
    padding: 0.45rem 0.65rem;
    vertical-align: middle;
}
</style>
@endsection

@section('content')
<!-- Header Toolbar -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1">{{ __('Cost Center Performance Report') }}</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('Accounting') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Cost Center Performance Report') }}</li>
            </ol>
        </nav>
    </div>

    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('accounting.reports.cost-center-report.export', request()->all()) }}" class="btn btn-label-success">
            <i class="bx bx-export me-1"></i> {{ __('Export Excel') }}
        </a>

        <div class="btn-group">
            <a href="{{ route('accounting.reports.cost-center-report.print', array_merge(request()->all(), ['orientation' => 'portrait'])) }}" target="_blank" class="btn btn-primary">
                <i class="bx bx-printer me-1"></i> {{ __('Print Report') }}
            </a>
            <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="visually-hidden">{{ __('Template Options') }}</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow">
                <li><h6 class="dropdown-header text-uppercase small">{{ __('Select Print Template:') }}</h6></li>
                @forelse($printTemplates as $tpl)
                    <li>
                        <a class="dropdown-item d-flex align-items-center justify-content-between py-2" target="_blank" 
                           href="{{ route('accounting.reports.cost-center-report.print', array_merge(request()->all(), ['template_id' => $tpl->id, 'orientation' => 'portrait'])) }}">
                            <span>
                                <i class="bx bx-file me-2 text-primary"></i> {{ $tpl->display_name ?? $tpl->name }}
                                <small class="text-muted d-block ps-4">({{ $tpl->page_size }} - {{ ucfirst($tpl->orientation) }})</small>
                            </span>
                            @if($tpl->is_default)
                                <span class="badge bg-label-primary small">{{ __('Default') }}</span>
                            @endif
                        </a>
                    </li>
                @empty
                    <li><span class="dropdown-item text-muted">{{ __('No active templates') }}</span></li>
                @endforelse
            </ul>
        </div>
    </div>
</div>

<!-- Filters Toolbar (2 Clean Rows) -->
<div class="card shadow-sm border-0 mb-4" id="filtersCard">
    <div class="card-header bg-transparent border-bottom py-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h6 class="fw-bold mb-0 text-heading">{{ __('Filter Cost Centers') }}</h6>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
            <div class="btn-group btn-group-sm" role="group" id="quickDateFilters" aria-label="الفترات الزمنية">
                <button type="button" class="btn btn-outline-secondary" data-range="all">{{ __('All') }}</button>
                <button type="button" class="btn btn-outline-secondary" data-range="today">{{ __('Today') }}</button>
                <button type="button" class="btn btn-outline-secondary" data-range="month">{{ __('This Month') }}</button>
                <button type="button" class="btn btn-outline-secondary" data-range="quarter">{{ __('This Quarter') }}</button>
                <button type="button" class="btn btn-outline-secondary" data-range="year">{{ __('This Year') }}</button>
            </div>

            <button type="button" id="btnResetFilters" class="btn btn-label-secondary btn-sm shadow-xs">
                <span>{{ __('Reset') }}</span>
            </button>
        </div>
    </div>

    <form method="GET" action="{{ route('accounting.reports.cost-center-report') }}" id="filterForm">
        <div class="card-body pt-3 pb-3">
            <div class="row g-3">
                <!-- Row 1: Accounting Entity (col-md-4) -->
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold text-heading small mb-1" for="filter_entity_id">
                        {{ __('Accounting Entity') }}
                    </label>
                    <select id="filter_entity_id" name="entity_id" class="form-select select2" data-control="select2" data-placeholder="{{ __('All Entities') }}" data-allow-clear="true">
                        <option value="">{{ __('All Entities') }}</option>
                        @foreach($filterOptions['entities'] as $entity)
                            <option value="{{ $entity->id }}" @selected((string) request('entity_id') === (string) $entity->id)>
                                {{ $entity->name }} ({{ $entity->code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Row 1: Cost Center (col-md-4) -->
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold text-heading small mb-1" for="filter_cost_center_id">
                        {{ __('Cost Center') }}
                    </label>
                    <select id="filter_cost_center_id" name="cost_center_id" class="form-select select2" data-control="select2" data-placeholder="{{ __('All Centers') }}" data-allow-clear="true">
                        <option value="">{{ __('All Centers') }}</option>
                        @foreach($filterOptions['costCenters'] as $cc)
                            <option value="{{ $cc->id }}" @selected((string) request('cost_center_id') === (string) $cc->id)>
                                [{{ $cc->code }}] {{ $cc->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Row 1: Status Toggle (col-md-4) -->
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold text-heading small mb-2 d-block">
                        {{ __('Entry Status') }}
                    </label>
                    <div class="form-check form-switch pt-1">
                        <input type="hidden" name="status" value="all">
                        <input class="form-check-input cursor-pointer" type="checkbox" name="status" id="filter_status" value="posted" @checked(request('status', 'posted') === 'posted')>
                        <label class="form-check-label fw-semibold text-heading small cursor-pointer" for="filter_status">
                            {{ __('Posted Only (Approved)') }}
                        </label>
                    </div>
                </div>

                <!-- Row 2: From Date (col-md-4) -->
                <div class="col-12 col-sm-6 col-md-4">
                    <label class="form-label fw-semibold text-heading small mb-1" for="filter_from_date">
                        {{ __('From Date') }}
                    </label>
                    <input type="date" name="from_date" id="filter_from_date" class="form-control" value="{{ $filters['from_date'] }}">
                </div>

                <!-- Row 2: To Date (col-md-4) -->
                <div class="col-12 col-sm-6 col-md-4">
                    <label class="form-label fw-semibold text-heading small mb-1" for="filter_to_date">
                        {{ __('To Date') }}
                    </label>
                    <input type="date" name="to_date" id="filter_to_date" class="form-control" value="{{ $filters['to_date'] }}">
                </div>

                <!-- Row 2: Hide Zero Centers Toggle (col-md-4) -->
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold text-heading small mb-2 d-block">
                        {{ __('Centers Scope') }}
                    </label>
                    <div class="form-check form-switch pt-1">
                        <input type="hidden" name="hide_zero" value="0">
                        <input class="form-check-input cursor-pointer" type="checkbox" name="hide_zero" id="filter_hide_zero" value="1" @checked($filters['hide_zero'])>
                        <label class="form-check-label fw-semibold text-heading small cursor-pointer" for="filter_hide_zero">
                            {{ __('Hide Zero Centers') }}
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Cost Centers Performance Table -->
<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="costCenterTable">
            <thead>
                <tr class="table-light text-muted border-bottom">
                    <th style="width: 110px;">{{ __('Center Code') }}</th>
                    <th style="min-width: 380px;">{{ __('Cost Center Name') }}</th>
                    <th style="width: 140px;" class="text-end">{{ __('Total Revenues') }}</th>
                    <th style="width: 140px;" class="text-end">{{ __('Total Expenses') }}</th>
                    <th style="width: 160px;" class="text-end">{{ __('Net Profit / Loss') }}</th>
                    <th style="width: 110px;" class="text-center">{{ __('Profit Margin %') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $r)
                    @php
                        $cc = $r['cost_center'];
                    @endphp
                    <tr>
                        <td class="text-muted">
                            {{ $cc ? $cc->code : '—' }}
                        </td>
                        <td class="text-nowrap" style="min-width: 380px;">
                            @if($cc)
                                <span class="fw-semibold text-dark">{{ $cc->name }}</span>
                                @if($cc->parent)
                                    <small class="text-muted ms-2">({{ $cc->parent->name }})</small>
                                @endif
                            @else
                                <span class="fst-italic text-muted">{{ __('Without Cost Center (Unassigned)') }}</span>
                            @endif
                        </td>
                        <td class="text-end">
                            {{ $r['revenues'] > 0 ? number_format($r['revenues'], 2) : '—' }}
                        </td>
                        <td class="text-end">
                            {{ $r['expenses'] > 0 ? number_format($r['expenses'], 2) : '—' }}
                        </td>
                        <td class="text-end fw-semibold">
                            {{ number_format($r['net_profit'], 2) }}
                        </td>
                        <td class="text-center">
                            {{ $r['profit_margin'] }}%
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            {{ __('No transactions or cost centers match the search') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if(!empty($rows))
                <tfoot>
                    <tr class="table-light fw-bold border-top border-bottom">
                        <td colspan="2" class="ps-3 text-dark">
                            {{ __('Grand Total for All Centers') }}
                        </td>
                        <td class="text-end">
                            {{ number_format($summary['total_revenues'], 2) }}
                        </td>
                        <td class="text-end">
                            {{ number_format($summary['total_expenses'], 2) }}
                        </td>
                        <td class="text-end text-dark">
                            {{ number_format($summary['net_profit'], 2) }}
                        </td>
                        <td class="text-center">
                            {{ $summary['profit_margin'] }}%
                        </td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('filterForm');

    // Auto submit on select change
    form.querySelectorAll('select').forEach(select => {
        select.addEventListener('change', () => form.submit());
    });

    // Auto submit on toggle switch change
    document.querySelectorAll('.form-check-input').forEach(checkbox => {
        checkbox.addEventListener('change', () => form.submit());
    });

    // Date Range Helpers
    const fromInput = document.getElementById('filter_from_date');
    const toInput   = document.getElementById('filter_to_date');

    document.querySelectorAll('#quickDateFilters [data-range]').forEach(btn => {
        btn.addEventListener('click', function () {
            const range = this.dataset.range;
            const now = new Date();
            let from = '', to = '';

            const formatDate = (d) => {
                const year = d.getFullYear();
                const month = String(d.getMonth() + 1).padStart(2, '0');
                const day = String(d.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            };

            if (range === 'today') {
                from = to = formatDate(now);
            } else if (range === 'month') {
                from = formatDate(new Date(now.getFullYear(), now.getMonth(), 1));
                to   = formatDate(new Date(now.getFullYear(), now.getMonth() + 1, 0));
            } else if (range === 'quarter') {
                const qStartMonth = Math.floor(now.getMonth() / 3) * 3;
                from = formatDate(new Date(now.getFullYear(), qStartMonth, 1));
                to   = formatDate(new Date(now.getFullYear(), qStartMonth + 3, 0));
            } else if (range === 'year') {
                from = formatDate(new Date(now.getFullYear(), 0, 1));
                to   = formatDate(new Date(now.getFullYear(), 11, 31));
            } else if (range === 'all') {
                from = '';
                to   = '';
            }

            fromInput.value = from;
            toInput.value = to;
            form.submit();
        });
    });

    // Reset Filters Button
    const btnReset = document.getElementById('btnResetFilters');
    if (btnReset) {
        btnReset.addEventListener('click', function () {
            window.location.href = "{{ route('accounting.reports.cost-center-report') }}";
        });
    }
});
</script>
@endsection
