@extends('layouts/contentNavbarLayout')

@section('title', __('Income Statement') . ' - ' . config('variables.templateName'))

@section('page-style')
<style>
#incomeStatementTable {
    font-size: 12.5px;
}
#incomeStatementTable th {
    padding: 0.5rem 0.65rem;
    font-size: 12px;
    white-space: nowrap;
    vertical-align: middle;
}
#incomeStatementTable td {
    padding: 0.45rem 0.65rem;
    vertical-align: middle;
}
.account-link:hover {
    text-decoration: underline !important;
}
</style>
@endsection

@section('content')
<!-- Header Toolbar -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1">{{ __('Income Statement') }}</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('Accounting') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Income Statement') }}</li>
            </ol>
        </nav>
    </div>

    <div class="d-flex align-items-center gap-2">
        <!-- Export CSV / Excel Button -->
        @can('accounting.reports.export')
        <a href="{{ route('accounting.reports.income-statement.export', request()->all()) }}" class="btn btn-label-success">
            <i class="bx bx-export me-1"></i> {{ __('Export Excel') }}
        </a>
        @endcan

        <!-- Smart Split Print Button -->
        @can('accounting.reports.print')
        <div class="btn-group">
            <a href="{{ route('accounting.reports.income-statement.print', array_merge(request()->all(), ['orientation' => 'portrait'])) }}" target="_blank" class="btn btn-primary">
                <i class="bx bx-printer me-1"></i> {{ __('Print Report') }}
            </a>
            <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="visually-hidden">{{ __('Template Options') }}</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow">
                <li><h6 class="dropdown-header text-uppercase small">{{ __('Choose Print Template:') }}</h6></li>
                @forelse($printTemplates as $tpl)
                    <li>
                        <a class="dropdown-item d-flex align-items-center justify-content-between py-2" target="_blank" 
                           href="{{ route('accounting.reports.income-statement.print', array_merge(request()->all(), ['template_id' => $tpl->id, 'orientation' => 'portrait'])) }}">
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

<!-- Filters Toolbar (Single Clean Row, 6 Inputs across 2 rows) -->
<div class="card shadow-sm border-0 mb-4" id="filtersCard">
    <div class="card-header bg-transparent border-bottom py-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h6 class="fw-bold mb-0 text-heading">{{ __('Filter Income Statement') }}</h6>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
            <div class="btn-group btn-group-sm" role="group" id="quickDateFilters" aria-label="{{ __('Time Periods') }}">
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

    <form method="GET" action="{{ route('accounting.reports.income-statement') }}" id="filterForm">
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

                <!-- Row 1: Journal (col-md-4) -->
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold text-heading small mb-1" for="filter_journal_id">
                        {{ __('Journal') }}
                    </label>
                    <select id="filter_journal_id" name="journal_id" class="form-select select2" data-control="select2" data-placeholder="{{ __('All Journals') }}" data-allow-clear="true">
                        <option value="">{{ __('All Journals') }}</option>
                        @foreach($filterOptions['journals'] as $journal)
                            <option value="{{ $journal->id }}" @selected((string) request('journal_id') === (string) $journal->id)>
                                {{ $journal->code }} - {{ $journal->name }}
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

                <!-- Row 2: From Date (col-md-3) -->
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label fw-semibold text-heading small mb-1" for="filter_from_date">
                        {{ __('From Date') }}
                    </label>
                    <input type="date" name="from_date" id="filter_from_date" class="form-control" value="{{ $filters['from_date'] }}">
                </div>

                <!-- Row 2: To Date (col-md-3) -->
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label fw-semibold text-heading small mb-1" for="filter_to_date">
                        {{ __('To Date') }}
                    </label>
                    <input type="date" name="to_date" id="filter_to_date" class="form-control" value="{{ $filters['to_date'] }}">
                </div>

                <!-- Row 2: Status Toggle (col-md-3) -->
                <div class="col-12 col-sm-6 col-md-3">
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

                <!-- Row 2: Accounts Scope Toggle (col-md-3) -->
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label fw-semibold text-heading small mb-2 d-block">
                        {{ __('Accounts Scope') }}
                    </label>
                    <div class="form-check form-switch pt-1">
                        <input type="hidden" name="hide_zero" value="0">
                        <input class="form-check-input cursor-pointer" type="checkbox" name="hide_zero" id="filter_hide_zero" value="1" @checked($filters['hide_zero'])>
                        <label class="form-check-label fw-semibold text-heading small cursor-pointer" for="filter_hide_zero">
                            {{ __('Hide Zero Balances') }}
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Responsive Table -->
<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="incomeStatementTable">
            <thead>
                <tr class="table-light text-muted border-bottom">
                    <th style="min-width: 380px;">{{ __('Account') }}</th>
                    <th style="width: 120px;" class="text-end">{{ __('Debit') }}</th>
                    <th style="width: 120px;" class="text-end">{{ __('Credit') }}</th>
                    <th style="width: 140px;" class="text-end">{{ __('Net Movement') }}</th>
                    <th style="width: 100px;" class="text-end">{{ __('Percentage %') }}</th>
                </tr>
            </thead>
            <tbody>
                <!-- 1. Revenues Section -->
                <tr class="table-light fw-bold">
                    <td colspan="5" class="py-2 text-dark">
                        {{ __('Revenues') }}
                    </td>
                </tr>

                @forelse($revenues as $row)
                    @php
                        $acc = $row['account'];
                        $glUrl = route('accounting.reports.general-ledger', [
                            'account_id' => $acc->id,
                            'from_date'  => $filters['from_date'],
                            'to_date'    => $filters['to_date'],
                            'entity_id'  => $filters['entity_id'],
                            'status'     => $filters['status'],
                        ]);
                    @endphp
                    <tr>
                        <td class="ps-4 text-nowrap">
                            <a href="{{ $glUrl }}" class="account-link text-dark fw-semibold text-decoration-none" title="{{ __('View General Ledger for Account') }}">
                                <span class="fw-bold text-primary me-1">[{{ $acc->code }}]</span>
                                <span>{{ $acc->name }}</span>
                            </a>
                            @if($acc->parent)
                                <small class="text-muted ms-1">({{ $acc->parent->name }})</small>
                            @endif
                        </td>
                        <td class="text-end text-muted">
                            {{ $row['debit'] > 0 ? number_format($row['debit'], 2) : '—' }}
                        </td>
                        <td class="text-end text-muted">
                            {{ $row['credit'] > 0 ? number_format($row['credit'], 2) : '—' }}
                        </td>
                        <td class="text-end fw-semibold">
                            {{ number_format($row['amount'], 2) }}
                        </td>
                        <td class="text-end text-muted">
                            {{ $row['percentage'] }}%
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-3 text-muted ps-4">
                            {{ __('No revenue transactions recorded during this period.') }}
                        </td>
                    </tr>
                @endforelse

                <!-- Revenues Subtotal -->
                <tr class="table-light fw-bold border-bottom">
                    <td class="ps-4">{{ __('Total Revenues') }}</td>
                    <td class="text-end text-muted">—</td>
                    <td class="text-end text-muted">—</td>
                    <td class="text-end">{{ number_format($summary['total_revenues'], 2) }}</td>
                    <td class="text-end">100.00%</td>
                </tr>

                <!-- 2. Expenses Section -->
                <tr class="table-light fw-bold">
                    <td colspan="5" class="py-2 text-dark">
                        {{ __('Expenses') }}
                    </td>
                </tr>

                @forelse($expenses as $row)
                    @php
                        $acc = $row['account'];
                        $glUrl = route('accounting.reports.general-ledger', [
                            'account_id' => $acc->id,
                            'from_date'  => $filters['from_date'],
                            'to_date'    => $filters['to_date'],
                            'entity_id'  => $filters['entity_id'],
                            'status'     => $filters['status'],
                        ]);
                    @endphp
                    <tr>
                        <td class="ps-4 text-nowrap">
                            <a href="{{ $glUrl }}" class="account-link text-dark fw-semibold text-decoration-none" title="{{ __('View General Ledger for Account') }}">
                                <span class="fw-bold text-primary me-1">[{{ $acc->code }}]</span>
                                <span>{{ $acc->name }}</span>
                            </a>
                            @if($acc->parent)
                                <small class="text-muted ms-1">({{ $acc->parent->name }})</small>
                            @endif
                        </td>
                        <td class="text-end text-muted">
                            {{ $row['debit'] > 0 ? number_format($row['debit'], 2) : '—' }}
                        </td>
                        <td class="text-end text-muted">
                            {{ $row['credit'] > 0 ? number_format($row['credit'], 2) : '—' }}
                        </td>
                        <td class="text-end fw-semibold">
                            {{ number_format($row['amount'], 2) }}
                        </td>
                        <td class="text-end text-muted">
                            {{ $row['percentage'] }}%
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-3 text-muted ps-4">
                            {{ __('No expense transactions recorded during this period.') }}
                        </td>
                    </tr>
                @endforelse

                <!-- Expenses Subtotal -->
                <tr class="table-light fw-bold border-bottom">
                    <td class="ps-4">{{ __('Total Expenses') }}</td>
                    <td class="text-end text-muted">—</td>
                    <td class="text-end text-muted">—</td>
                    <td class="text-end">{{ number_format($summary['total_expenses'], 2) }}</td>
                    <td class="text-end">
                        @if($summary['total_revenues'] > 0)
                            {{ round(($summary['total_expenses'] / $summary['total_revenues']) * 100, 2) }}%
                        @else
                            —
                        @endif
                    </td>
                </tr>
            </tbody>

            <tfoot>
                <tr class="table-light fw-bold border-top border-dark border-2">
                    <td class="ps-4 py-3 text-dark">
                        {{ $summary['is_profitable'] ? __('Net Profit for the Period') : __('Net Loss for the Period') }}
                    </td>
                    <td class="text-end py-3 text-muted">—</td>
                    <td class="text-end py-3 text-muted">—</td>
                    <td class="text-end py-3 text-dark">
                        {{ number_format($summary['net_profit'], 2) }}
                    </td>
                    <td class="text-end py-3 text-dark">
                        {{ $summary['profit_margin'] }}%
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const $ = window.jQuery || window.$;

    // 1. Quick Date Presets
    $('#quickDateFilters button').on('click', function () {
        $('#quickDateFilters button').removeClass('active');
        $(this).addClass('active');

        const range = $(this).data('range');
        const today = new Date();
        const formatDate = d => {
            const y = d.getFullYear();
            const m = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');
            return `${y}-${m}-${day}`;
        };

        if (range === 'all') {
            $('#filter_from_date').val('');
            $('#filter_to_date').val('');
        } else if (range === 'today') {
            $('#filter_from_date').val(formatDate(today));
            $('#filter_to_date').val(formatDate(today));
        } else if (range === 'month') {
            const first = new Date(today.getFullYear(), today.getMonth(), 1);
            $('#filter_from_date').val(formatDate(first));
            $('#filter_to_date').val(formatDate(today));
        } else if (range === 'quarter') {
            const qMonth = Math.floor(today.getMonth() / 3) * 3;
            const first = new Date(today.getFullYear(), qMonth, 1);
            $('#filter_from_date').val(formatDate(first));
            $('#filter_to_date').val(formatDate(today));
        } else if (range === 'year') {
            const first = new Date(today.getFullYear(), 0, 1);
            $('#filter_from_date').val(formatDate(first));
            $('#filter_to_date').val(formatDate(today));
        }

        $('#filterForm').submit();
    });

    // 2. Auto-submit on filter select or switch change
    $('#filter_entity_id, #filter_cost_center_id, #filter_journal_id, #filter_status, #filter_hide_zero').on('change', function () {
        $('#filterForm').submit();
    });

    $('#filter_from_date, #filter_to_date').on('change', function () {
        $('#filterForm').submit();
    });

    // 3. Reset Filters
    $('#btnResetFilters').on('click', function () {
        $('#filter_entity_id, #filter_cost_center_id, #filter_journal_id').val('').trigger('change.select2');
        $('#filter_status').prop('checked', true);
        $('#filter_hide_zero').prop('checked', true);
        $('#filter_from_date, #filter_to_date').val('');
        window.location.href = "{{ route('accounting.reports.income-statement') }}";
    });
});
</script>
@endsection
