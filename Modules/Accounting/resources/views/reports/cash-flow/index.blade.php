@extends('layouts/contentNavbarLayout')

@section('title', __('Cash Flow Statement') . ' - ' . config('variables.templateName'))

@section('page-style')
<style>
#cashFlowTable {
    font-size: 12.5px;
}
#cashFlowTable th {
    padding: 0.5rem 0.65rem;
    font-size: 12px;
    white-space: nowrap;
    vertical-align: middle;
}
#cashFlowTable td {
    padding: 0.45rem 0.65rem;
    vertical-align: middle;
}
</style>
@endsection

@section('content')
<!-- Header Toolbar -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1">{{ __('Cash Flow Statement') }}</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('Accounting') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Cash Flow Statement') }}</li>
            </ol>
        </nav>
    </div>

    <div class="d-flex align-items-center gap-2">
        @can('accounting.reports.export')
        <a href="{{ route('accounting.reports.cash-flow.export', request()->all()) }}" class="btn btn-label-success">
            <i class="bx bx-export me-1"></i> {{ __('Export Excel') }}
        </a>
        @endcan

        @can('accounting.reports.print')
        <div class="btn-group">
            <a href="{{ route('accounting.reports.cash-flow.print', array_merge(request()->all(), ['orientation' => 'portrait'])) }}" target="_blank" class="btn btn-primary">
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
                           href="{{ route('accounting.reports.cash-flow.print', array_merge(request()->all(), ['template_id' => $tpl->id, 'orientation' => 'portrait'])) }}">
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
        @endcan
    </div>
</div>

<!-- Filters Toolbar (2 Clean Rows) -->
<div class="card shadow-sm border-0 mb-4" id="filtersCard">
    <div class="card-header bg-transparent border-bottom py-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h6 class="fw-bold mb-0 text-heading">{{ __('Filter Cash Flow Statement') }}</h6>
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

    <form method="GET" action="{{ route('accounting.reports.cash-flow') }}" id="filterForm">
        <div class="card-body pt-3 pb-3">
            <div class="row g-3">
                <!-- Row 1: Accounting Entity (col-md-6) -->
                <div class="col-12 col-md-6">
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

                <!-- Row 1: From Date (col-md-3) -->
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label fw-semibold text-heading small mb-1" for="filter_from_date">
                        {{ __('From Date') }}
                    </label>
                    <input type="date" name="from_date" id="filter_from_date" class="form-control" value="{{ $filters['from_date'] }}">
                </div>

                <!-- Row 1: To Date (col-md-3) -->
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label fw-semibold text-heading small mb-1" for="filter_to_date">
                        {{ __('To Date') }}
                    </label>
                    <input type="date" name="to_date" id="filter_to_date" class="form-control" value="{{ $filters['to_date'] }}">
                </div>

                <!-- Row 2: Status Toggle (col-md-6) -->
                <div class="col-12 col-md-6">
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
            </div>
        </div>
    </form>
</div>

<!-- Cash Flow Statement Table -->
<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="cashFlowTable">
            <thead>
                <tr class="table-light text-muted border-bottom">
                    <th style="min-width: 420px;" class="ps-3">{{ __('Description / Activities') }}</th>
                    <th style="width: 170px;" class="text-end">{{ __('Partial') }}</th>
                    <th style="width: 170px;" class="text-end pe-3">{{ __('Total') }}</th>
                </tr>
            </thead>
            <tbody>
                <!-- 1. Operating Activities -->
                <tr class="table-light fw-bold">
                    <td colspan="3" class="ps-3 text-dark py-2">
                        <i class="bx bx-briefcase me-1 text-primary"></i>
                        <span>{{ __('1. Cash Flows from Operating Activities') }}</span>
                    </td>
                </tr>
                <tr>
                    <td class="ps-4">
                        <span>{{ __('Net Income from Operations (Profit / Loss)') }}</span>
                    </td>
                    <td class="text-end">
                        {{ number_format($operating['net_income'], 2) }}
                    </td>
                    <td class="text-end text-muted pe-3">—</td>
                </tr>
                <tr>
                    <td class="ps-4">
                        <span>{{ __('(Increase) Decrease in Accounts Receivable and Customers') }}</span>
                        <small class="text-muted d-block ps-3">{{ __('Current and Trade Receivables') }}</small>
                    </td>
                    <td class="text-end">
                        {{ number_format($operating['change_ar'], 2) }}
                    </td>
                    <td class="text-end text-muted pe-3">—</td>
                </tr>
                <tr>
                    <td class="ps-4">
                        <span>{{ __('Increase (Decrease) in Accounts Payable and Suppliers') }}</span>
                        <small class="text-muted d-block ps-3">{{ __('Current and Trade Payables') }}</small>
                    </td>
                    <td class="text-end">
                        {{ number_format($operating['change_ap'], 2) }}
                    </td>
                    <td class="text-end text-muted pe-3">—</td>
                </tr>
                <tr class="table-light fw-semibold">
                    <td class="ps-4 text-heading">
                        <span>{{ __('Net Cash Flow from (Used in) Operating Activities') }}</span>
                    </td>
                    <td class="text-end text-muted">—</td>
                    <td class="text-end pe-3 fw-bold">
                        {{ number_format($operating['subtotal'], 2) }}
                    </td>
                </tr>

                <!-- 2. Investing Activities -->
                <tr class="table-light fw-bold">
                    <td colspan="3" class="ps-3 text-dark py-2">
                        <i class="bx bx-buildings me-1 text-primary"></i>
                        <span>{{ __('2. Cash Flows from Investing Activities') }}</span>
                    </td>
                </tr>
                <tr>
                    <td class="ps-4">
                        <span>{{ __('Change in Non-Current Assets (Fixed Assets and Projects)') }}</span>
                    </td>
                    <td class="text-end">
                        {{ number_format($investing['fixed_assets_change'], 2) }}
                    </td>
                    <td class="text-end text-muted pe-3">—</td>
                </tr>
                <tr class="table-light fw-semibold">
                    <td class="ps-4 text-heading">
                        <span>{{ __('Net Cash Flow from (Used in) Investing Activities') }}</span>
                    </td>
                    <td class="text-end text-muted">—</td>
                    <td class="text-end pe-3 fw-bold">
                        {{ number_format($investing['subtotal'], 2) }}
                    </td>
                </tr>

                <!-- 3. Financing Activities -->
                <tr class="table-light fw-bold">
                    <td colspan="3" class="ps-3 text-dark py-2">
                        <i class="bx bx-coin me-1 text-primary"></i>
                        <span>{{ __('3. Cash Flows from Financing Activities') }}</span>
                    </td>
                </tr>
                <tr>
                    <td class="ps-4">
                        <span>{{ __('Change in Equity and Long-Term Liabilities (Loans and Financing)') }}</span>
                    </td>
                    <td class="text-end">
                        {{ number_format($financing['equity_loans_change'], 2) }}
                    </td>
                    <td class="text-end text-muted pe-3">—</td>
                </tr>
                <tr class="table-light fw-semibold">
                    <td class="ps-4 text-heading">
                        <span>{{ __('Net Cash Flow from (Used in) Financing Activities') }}</span>
                    </td>
                    <td class="text-end text-muted">—</td>
                    <td class="text-end pe-3 fw-bold">
                        {{ number_format($financing['subtotal'], 2) }}
                    </td>
                </tr>

                <!-- Summary / Net Change in Cash -->
                <tr class="table-secondary fw-bold border-top border-bottom">
                    <td class="ps-3 text-dark py-2">
                        <i class="bx bx-trending-up me-1 text-primary"></i>
                        <span>{{ __('Net Change in Cash and Cash Equivalents during the Period') }}</span>
                    </td>
                    <td class="text-end text-muted">—</td>
                    <td class="text-end pe-3 py-2">
                        {{ number_format($summary['net_cash_change'], 2) }}
                    </td>
                </tr>
                <tr>
                    <td class="ps-4 fw-semibold text-heading">
                        <span>{{ __('Cash and Cash Equivalents at Beginning of Period') }} ({{ $filters['from_date'] }})</span>
                    </td>
                    <td class="text-end text-muted">—</td>
                    <td class="text-end pe-3">
                        {{ number_format($summary['beginning_cash'], 2) }}
                    </td>
                </tr>
                <tr class="table-light fw-bold border-bottom">
                    <td class="ps-4 text-dark py-2">
                        <span>{{ __('Cash and Cash Equivalents at End of Period') }} ({{ $filters['to_date'] }})</span>
                    </td>
                    <td class="text-end text-muted">—</td>
                    <td class="text-end pe-3 py-2">
                        {{ number_format($summary['ending_cash'], 2) }}
                    </td>
                </tr>
            </tbody>
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
            window.location.href = "{{ route('accounting.reports.cash-flow') }}";
        });
    }
});
</script>
@endsection
