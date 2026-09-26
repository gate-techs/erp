@extends('layouts/contentNavbarLayout')

@section('title', __('General Ledger') . ' - ' . config('variables.templateName'))

@section('page-style')
<style>
.cursor-pointer {
    cursor: pointer;
}
.transition-transform {
    transition: transform 0.15s ease-in-out;
}
.account-row {
    background-color: #f8fafc;
    border-top: 1px solid #e2e8f0;
    border-bottom: 1px solid #e2e8f0;
}
.account-row:hover {
    background-color: #f1f5f9;
}
.account-row.is-open .toggle-arrow {
    transform: rotate(90deg);
}
[dir="rtl"] .account-row.is-open .toggle-arrow {
    transform: rotate(-90deg);
}
#ledgerTable {
    font-size: 12.5px;
}
#ledgerTable th {
    padding: 0.55rem 0.65rem;
    font-weight: 600;
    font-size: 12px;
    white-space: nowrap;
}
#ledgerTable td {
    padding: 0.45rem 0.65rem;
    vertical-align: middle;
}
.movement-row td {
    background-color: #ffffff;
    border-bottom: 1px solid #f1f5f9;
}
.movement-row:hover td {
    background-color: #f8fafc !important;
}
.line-desc {
    max-width: 320px;
    word-break: break-word;
    white-space: normal;
}
</style>
@endsection

@section('content')
<!-- Header Toolbar -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1">{{ __('General Ledger') }}</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('Accounting') }}</a></li>
                <li class="breadcrumb-item active">{{ __('General Ledger') }}</li>
            </ol>
        </nav>
    </div>

    <div class="d-flex align-items-center gap-2">
        <!-- Toggle All Accounts Button -->
        <button type="button" id="btnToggleAll" class="btn btn-outline-secondary" data-state="closed">
            <i class="bx bx-expand-vertical me-1"></i> <span id="toggleAllText">{{ __('Expand All') }}</span>
        </button>

        <!-- Export CSV / Excel Button -->
        <a href="{{ route('accounting.reports.general-ledger.export', request()->all()) }}" class="btn btn-label-success">
            <i class="bx bx-export me-1"></i> {{ __('Export Excel') }}
        </a>

        <!-- Smart Split Print Button -->
        <div class="btn-group">
            <a href="{{ route('accounting.reports.general-ledger.print', array_merge(request()->all(), ['orientation' => 'landscape'])) }}" target="_blank" class="btn btn-primary">
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
                           href="{{ route('accounting.reports.general-ledger.print', array_merge(request()->all(), ['template_id' => $tpl->id, 'orientation' => 'landscape'])) }}">
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

<!-- Filters Toolbar (Single Clean Row, 6 Inputs) -->
<div class="card shadow-sm border-0 mb-4" id="filtersCard">
    <div class="card-header bg-transparent border-bottom py-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h6 class="fw-bold mb-0 text-heading">{{ __('Filter General Ledger') }}</h6>
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

    <form method="GET" action="{{ route('accounting.reports.general-ledger') }}" id="filterForm">
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
                        {{ __('Account') }}
                    </label>
                    <select id="filter_account_id" name="account_id" class="form-select select2" data-control="select2" data-placeholder="{{ __('All Accounts') }}" data-allow-clear="true">
                        <option value="">{{ __('All Accounts') }}</option>
                        @foreach($filterOptions['accounts'] as $acc)
                            <option value="{{ $acc->id }}" data-entity="{{ $acc->entity_id }}" @selected((string) request('account_id') === (string) $acc->id)>
                                {{ $acc->code }} - {{ $acc->name }}
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
                            <option value="{{ $journal->id }}" data-entity="{{ $journal->entity_id }}" @selected((string) request('journal_id') === (string) $journal->id)>
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
                            <option value="{{ $cc->id }}" data-entity="{{ $cc->entity_id }}" @selected((string) request('cost_center_id') === (string) $cc->id)>
                                {{ $cc->code }} - {{ $cc->name }}
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

<!-- Responsive Odoo-Style General Ledger Table with Complete Fields -->
<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table align-middle mb-0" id="ledgerTable">
            <thead>
                <tr class="table-light text-muted border-bottom">
                    <th style="width: 100px;">{{ __('Date') }}</th>
                    <th style="width: 110px;">{{ __('Entry #') }}</th>
                    <th style="width: 120px;">{{ __('Journal') }}</th>
                    <th style="width: 120px;">{{ __('Cost Center') }}</th>
                    <th>{{ __('Description / Narration') }}</th>
                    <th class="text-end" style="width: 110px;">{{ __('Debit') }}</th>
                    <th class="text-end" style="width: 110px;">{{ __('Credit') }}</th>
                    <th class="text-end" style="width: 120px;">{{ __('Balance') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($accounts as $accData)
                    @php
                        $acc = $accData['account'];
                        $accKey = 'acc_' . $acc->id;
                    @endphp

                    {{-- 1. Account Summary Header Row --}}
                    <tr class="account-row cursor-pointer" data-target="{{ $accKey }}">
                        <td colspan="5" class="py-2">
                            <div class="d-flex align-items-center justify-content-between pe-3">
                                <div>
                                    <span class="d-inline-block transition-transform toggle-arrow me-2 font-12 text-secondary">
                                        <i class="bx bx-chevron-left font-18"></i>
                                    </span>
                                    <span class="fw-bold text-dark me-1">[{{ $acc->code }}]</span>
                                    <span class="fw-semibold text-dark">{{ $acc->name }}</span>
                                    <small class="text-muted ms-2">({{ $accData['movements_count'] }} {{ __('movements') }})</small>
                                </div>
                                <div class="text-muted font-11">
                                    <span>{{ __('Initial Balance:') }}</span>
                                    <strong class="text-dark ms-1">
                                        {{ number_format(abs($accData['initial_net']), 2) }}
                                        <small>{{ $accData['initial_net'] >= 0 ? ($accData['is_debit_normal'] ? __('Dr') : __('Cr')) : ($accData['is_debit_normal'] ? __('Cr') : __('Dr')) }}</small>
                                    </strong>
                                </div>
                            </div>
                        </td>
                        <td class="text-end py-2 fw-bold text-dark">
                            {{ $accData['period_debit'] > 0 ? number_format($accData['period_debit'], 2) : '—' }}
                        </td>
                        <td class="text-end py-2 fw-bold text-dark">
                            {{ $accData['period_credit'] > 0 ? number_format($accData['period_credit'], 2) : '—' }}
                        </td>
                        <td class="text-end py-2 fw-bold text-dark">
                            {{ number_format(abs($accData['ending_net']), 2) }}
                            <small class="text-muted font-10">
                                {{ $accData['ending_net'] >= 0 ? ($accData['is_debit_normal'] ? __('Dr') : __('Cr')) : ($accData['is_debit_normal'] ? __('Cr') : __('Dr')) }}
                            </small>
                        </td>
                    </tr>

                    {{-- 2. Movement Rows (Hidden by default, shown when row expands) --}}
                    @foreach($accData['movements'] as $move)
                        <tr class="movement-row {{ $accKey }} d-none">
                            <td class="text-secondary text-nowrap">
                                {{ $move->journalEntry?->entry_date?->format('Y-m-d') }}
                            </td>
                            <td class="text-nowrap">
                                <a href="{{ route('accounting.journal-entries.show', $move->journal_entry_id) }}" target="_blank" class="fw-semibold text-primary text-decoration-none">
                                    {{ $move->journalEntry?->entry_number }}
                                </a>
                            </td>
                            <td class="text-secondary text-nowrap">
                                {{ $move->journalEntry?->journal?->name ?? '—' }}
                            </td>
                            <td class="text-secondary text-nowrap">
                                {{ $move->costCenter?->name ?? '—' }}
                            </td>
                            <td class="line-desc">
                                {{ $move->description ?: ($move->journalEntry?->description ?? '—') }}
                            </td>
                            <td class="text-end text-nowrap">
                                {{ $move->debit > 0 ? number_format($move->debit, 2) : '—' }}
                            </td>
                            <td class="text-end text-nowrap">
                                {{ $move->credit > 0 ? number_format($move->credit, 2) : '—' }}
                            </td>
                            <td class="text-end fw-semibold text-dark text-nowrap">
                                {{ number_format(abs($move->running_balance), 2) }}
                                <small class="text-muted font-10">
                                    {{ $move->running_balance >= 0 ? ($accData['is_debit_normal'] ? __('Dr') : __('Cr')) : ($accData['is_debit_normal'] ? __('Cr') : __('Dr')) }}
                                </small>
                            </td>
                        </tr>
                    @endforeach

                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            {{ __('No accounting transactions matching the filter criteria.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>

            @if(!empty($accounts))
                <tfoot>
                    <tr class="table-light fw-bold border-top border-dark border-2">
                        <td colspan="5" class="text-center py-3 text-dark">
                            {{ __('Grand Total') }}
                        </td>
                        <td class="text-end py-3 text-dark text-nowrap">
                            {{ number_format($summary['period_debit'], 2) }}
                        </td>
                        <td class="text-end py-3 text-dark text-nowrap">
                            {{ number_format($summary['period_credit'], 2) }}
                        </td>
                        <td class="text-end py-3 text-dark text-nowrap">
                            @if($summary['is_balanced'])
                                <span class="text-success font-11">✔ {{ __('Balanced') }}</span>
                            @else
                                <span class="text-danger font-11">{{ number_format(abs($summary['period_debit'] - $summary['period_credit']), 2) }}</span>
                            @endif
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
    const $ = window.jQuery || window.$;

    // 1. Toggle single account row
    $(document).on('click', '.account-row', function () {
        const targetClass = $(this).data('target');
        const rows = $('.' + targetClass);
        $(this).toggleClass('is-open');
        rows.toggleClass('d-none');
    });

    // 2. Toggle All Accounts
    $('#btnToggleAll').on('click', function () {
        const state = $(this).attr('data-state');
        if (state === 'closed') {
            $('.account-row').addClass('is-open');
            $('.movement-row').removeClass('d-none');
            $(this).attr('data-state', 'open');
            $('#toggleAllText').text("{{ __('Collapse All') }}");
            $(this).find('i').removeClass('bx-expand-vertical').addClass('bx-collapse-vertical');
        } else {
            $('.account-row').removeClass('is-open');
            $('.movement-row').addClass('d-none');
            $(this).attr('data-state', 'closed');
            $('#toggleAllText').text("{{ __('Expand All') }}");
            $(this).find('i').removeClass('bx-collapse-vertical').addClass('bx-expand-vertical');
        }
    });

    // 3. Quick Date Presets
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

    // 4. Auto-submit on filter select change
    $('#filter_entity_id, #filter_account_id, #filter_journal_id, #filter_cost_center_id, #filter_status').on('change', function () {
        $('#filterForm').submit();
    });

    $('#filter_from_date, #filter_to_date').on('change', function () {
        $('#filterForm').submit();
    });

    // 5. Reset Filters
    $('#btnResetFilters').on('click', function () {
        $('#filter_entity_id, #filter_account_id, #filter_journal_id, #filter_cost_center_id').val('').trigger('change.select2');
        $('#filter_status').prop('checked', true);
        $('#filter_from_date, #filter_to_date').val('');
        window.location.href = "{{ route('accounting.reports.general-ledger') }}";
    });
});
</script>
@endsection
