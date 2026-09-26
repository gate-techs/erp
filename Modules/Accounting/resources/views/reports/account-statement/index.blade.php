@extends('layouts/contentNavbarLayout')

@section('title', __('Detailed Account Statement') . ' - ' . config('variables.templateName'))

@section('page-style')
<style>
#statementTable {
    font-size: 12.5px;
}
#statementTable th {
    padding: 0.5rem 0.65rem;
    font-size: 12px;
    white-space: nowrap;
    vertical-align: middle;
}
#statementTable td {
    padding: 0.45rem 0.65rem;
    vertical-align: middle;
}
</style>
@endsection

@section('content')
<!-- Header Toolbar -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1">{{ __('Detailed Account Statement') }}</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('Accounting') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Detailed Account Statement') }}</li>
            </ol>
        </nav>
    </div>

    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('accounting.reports.account-statement.export', request()->all()) }}" class="btn btn-label-success">
            <i class="bx bx-export me-1"></i> {{ __('Export Excel') }}
        </a>

        <div class="btn-group">
            <a href="{{ route('accounting.reports.account-statement.print', array_merge(request()->all(), ['orientation' => 'portrait'])) }}" target="_blank" class="btn btn-primary">
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
                           href="{{ route('accounting.reports.account-statement.print', array_merge(request()->all(), ['template_id' => $tpl->id, 'orientation' => 'portrait'])) }}">
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
            <h6 class="fw-bold mb-0 text-heading">{{ __('Filter Account Statement') }}</h6>
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

    <form method="GET" action="{{ route('accounting.reports.account-statement') }}" id="filterForm">
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

                <!-- Row 1: Account (col-md-4) -->
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold text-heading small mb-1" for="filter_account_id">
                        {{ __('Financial Account') }}
                    </label>
                    <select id="filter_account_id" name="account_id" class="form-select select2" data-control="select2" data-placeholder="{{ __('Select Account') }}">
                        @foreach($filterOptions['accounts'] as $acc)
                            <option value="{{ $acc->id }}" @selected((string) ($filters['account_id'] ?? '') === (string) $acc->id)>
                                [{{ $acc->code }}] {{ $acc->name }}
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

                <!-- Row 2: Cost Center (col-md-3) -->
                <div class="col-12 col-sm-6 col-md-3">
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
            </div>
        </div>
    </form>
</div>

<!-- Statement Table -->
<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="statementTable">
            <thead>
                <tr class="table-light text-muted border-bottom">
                    <th style="width: 100px;">{{ __('Date') }}</th>
                    <th style="width: 110px;">{{ __('Entry #') }}</th>
                    <th style="width: 120px;">{{ __('Journal') }}</th>
                    <th style="min-width: 250px;">{{ __('Description / Narration') }}</th>
                    <th style="width: 110px;">{{ __('Reference') }}</th>
                    <th style="width: 115px;" class="text-end">{{ __('Debit') }}</th>
                    <th style="width: 115px;" class="text-end">{{ __('Credit') }}</th>
                    <th style="width: 130px;" class="text-end">{{ __('Running Balance') }}</th>
                </tr>
            </thead>
            <tbody>
                <!-- Initial Balance Row -->
                <tr class="table-light fw-bold">
                    <td colspan="5" class="py-2 text-dark ps-3">
                        <i class="bx bx-calendar-check me-1 text-primary"></i>
                        <span>{{ __('Initial balance before') }} {{ $filters['from_date'] }}</span>
                    </td>
                    <td class="text-end text-muted">—</td>
                    <td class="text-end text-muted">—</td>
                    <td class="text-end text-dark py-2">
                        {{ number_format($summary['initial_balance'], 2) }}
                    </td>
                </tr>

                @forelse($lines as $line)
                    <tr>
                        <td class="text-muted">{{ $line['date'] }}</td>
                        <td>
                            <a href="{{ route('accounting.journal-entries.show', $line['entry_id']) }}" class="badge bg-label-primary text-decoration-none fw-semibold" target="_blank">
                                {{ $line['entry_number'] }}
                            </a>
                        </td>
                        <td class="text-muted">{{ $line['journal_name'] }}</td>
                        <td>
                            <span>{{ $line['description'] }}</span>
                            @if(!empty($line['partner_name']))
                                <small class="text-muted d-block">({{ $line['partner_name'] }})</small>
                            @endif
                        </td>
                        <td class="text-muted small">{{ $line['reference'] ?: '—' }}</td>
                        <td class="text-end">
                            {{ $line['debit'] > 0 ? number_format($line['debit'], 2) : '—' }}
                        </td>
                        <td class="text-end">
                            {{ $line['credit'] > 0 ? number_format($line['credit'], 2) : '—' }}
                        </td>
                        <td class="text-end fw-semibold">
                            {{ number_format($line['running_balance'], 2) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            {{ __('No recorded transactions for this account during the selected period.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>

            <tfoot>
                <tr class="table-light fw-bold border-top border-dark border-2">
                    <td colspan="5" class="ps-3 py-3 text-dark">
                        {{ __('Period Movements & Ending Balance Total') }}
                    </td>
                    <td class="text-end py-3 text-dark">
                        {{ number_format($summary['total_debit'], 2) }}
                    </td>
                    <td class="text-end py-3 text-dark">
                        {{ number_format($summary['total_credit'], 2) }}
                    </td>
                    <td class="text-end py-3 text-dark fs-6">
                        {{ number_format($summary['ending_balance'], 2) }}
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

    // 2. Auto-submit on change
    $('#filter_entity_id, #filter_account_id, #filter_journal_id, #filter_cost_center_id, #filter_status').on('change', function () {
        $('#filterForm').submit();
    });

    $('#filter_from_date, #filter_to_date').on('change', function () {
        $('#filterForm').submit();
    });

    // 3. Reset Filters
    $('#btnResetFilters').on('click', function () {
        $('#filter_entity_id, #filter_journal_id, #filter_cost_center_id').val('').trigger('change.select2');
        $('#filter_status').prop('checked', true);
        $('#filter_from_date, #filter_to_date').val('');
        window.location.href = "{{ route('accounting.reports.account-statement') }}";
    });
});
</script>
@endsection
