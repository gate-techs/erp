@extends('layouts/contentNavbarLayout')

@section('title', __('Bank Reconciliation') . ' - ' . $reconciliation->reconciliation_number)

@section('content')
<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <i class="icon-base bx bx-check-shield text-primary font-24"></i>
            <span>{{ __('Bank Reconciliation Statement') }}</span>
            <span class="text-muted fw-normal">({{ $reconciliation->reconciliation_number }})</span>
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('accounting.bank-reconciliations.index') }}">{{ __('Bank Reconciliations') }}</a></li>
                <li class="breadcrumb-item active">{{ $reconciliation->reconciliation_number }}</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex align-items-center gap-2">
        @can('accounting.bank-reconciliations.print')
        <a href="{{ route('accounting.bank-reconciliations.print', $reconciliation->id) }}" target="_blank" class="btn btn-outline-primary d-flex align-items-center gap-1 shadow-xs">
            <i class="icon-base bx bx-printer font-18"></i>
            <span>{{ __('Print Statement') }}</span>
        </a>
        @endcan
        <a href="{{ route('accounting.bank-reconciliations.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-1 shadow-xs">
            <i class="icon-base bx bx-arrow-back font-18"></i>
            <span>{{ __('Back') }}</span>
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Meta Info Card -->
    <div class="col-12 col-lg-4">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header border-bottom py-3 d-flex align-items-center justify-content-between">
                <h6 class="card-title fw-bold mb-0">{{ __('Reconciliation Details') }}</h6>
                <span class="badge {{ $reconciliation->status->badgeClass() }} d-inline-flex align-items-center gap-1">
                    {{ $reconciliation->status->label() }}
                </span>
            </div>
            <div class="card-body py-3">
                <div class="mb-3">
                    <span class="text-muted small d-block">{{ __('Reconciliation Number') }}</span>
                    <span class="fw-bold text-heading fs-6">{{ $reconciliation->reconciliation_number }}</span>
                </div>

                <div class="mb-3">
                    <span class="text-muted small d-block">{{ __('Statement Date') }}</span>
                    <span class="fw-semibold text-heading">{{ $reconciliation->statement_date->format('Y-m-d') }}</span>
                </div>

                <div class="mb-3">
                    <span class="text-muted small d-block">{{ __('Bank Account') }}</span>
                    <span class="fw-bold text-primary">{{ $reconciliation->cashAccount?->translate(app()->getLocale())?->name ?? $reconciliation->cashAccount?->code }}</span>
                    <span class="d-block small text-muted">{{ $reconciliation->cashAccount?->bank_name }} &bull; {{ $reconciliation->cashAccount?->account_number }}</span>
                </div>

                <div class="mb-3">
                    <span class="text-muted small d-block">{{ __('Accounting Entity') }}</span>
                    <span class="fw-semibold">{{ $reconciliation->entity?->translate(app()->getLocale())?->name ?? $reconciliation->entity?->code }} ({{ $reconciliation->cashAccount?->currency }})</span>
                </div>

                @if($reconciliation->reconciled_at)
                    <div class="mb-3">
                        <span class="text-muted small d-block">{{ __('Reconciled At & By') }}</span>
                        <span class="small">{{ $reconciliation->reconciled_at->format('Y-m-d H:i') }} &bull; {{ $reconciliation->reconciledBy?->name ?? '-' }}</span>
                    </div>
                @endif

                @if($reconciliation->notes)
                    <div class="mt-3 pt-3 border-top">
                        <span class="text-muted small d-block mb-1">{{ __('Notes') }}</span>
                        <p class="small text-secondary mb-0">{{ $reconciliation->notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Summary Figures Card -->
        <div class="card shadow-sm border-0">
            <div class="card-header border-bottom py-3">
                <h6 class="card-title fw-bold mb-0">{{ __('Balance Comparison') }}</h6>
            </div>
            <div class="card-body py-3">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                    <span class="text-muted">{{ __('Statement Ending Balance') }}</span>
                    <span class="fw-bold text-primary fs-6">{{ number_format($reconciliation->statement_balance, 2) }}</span>
                </div>

                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                    <span class="text-muted">{{ __('Book Balance (GL)') }}</span>
                    <span class="fw-semibold">{{ number_format($reconciliation->book_balance, 2) }}</span>
                </div>

                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                    <span class="text-muted">{{ __('Reconciled Cleared Total') }}</span>
                    <span class="fw-bold text-success fs-6">{{ number_format($reconciliation->reconciled_balance, 2) }}</span>
                </div>

                <div class="d-flex align-items-center justify-content-between">
                    <span class="fw-bold text-heading">{{ __('Reconciliation Difference') }}</span>
                    @if(abs((float)$reconciliation->difference) < 0.0001)
                        <span class="badge bg-label-success fs-6"><i class="icon-base bx bx-check-circle me-1"></i> 0.00</span>
                    @else
                        <span class="badge bg-label-danger fs-6"><i class="icon-base bx bx-error me-1"></i> {{ number_format($reconciliation->difference, 2) }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Reconciled Items Table -->
    <div class="col-12 col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header border-bottom py-3 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="card-title fw-bold mb-0">{{ __('Cleared & Reconciled Transactions') }}</h6>
                    <span class="text-muted small">{{ count($reconciliation->items) }} {{ __('transactions recorded in this statement') }}</span>
                </div>
            </div>

            <div class="table-responsive text-nowrap">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">#</th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Entry #') }}</th>
                            <th>{{ __('Description / Reference') }}</th>
                            <th class="text-end text-success">{{ __('Debit') }}</th>
                            <th class="text-end text-danger">{{ __('Credit') }}</th>
                            <th width="8%" class="text-center">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reconciliation->items as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->transaction_date->format('Y-m-d') }}</td>
                                <td>
                                    @if($item->journalEntryLine?->entry)
                                        <a href="{{ route('accounting.journal-entries.show', $item->journalEntryLine->entry->id) }}" class="fw-bold text-primary">
                                            {{ $item->journalEntryLine->entry->entry_number }}
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-truncate d-inline-block" style="max-width: 250px;">
                                        {{ $item->notes ?: ($item->journalEntryLine?->description ?: $item->statement_reference ?: '-') }}
                                    </span>
                                </td>
                                <td class="text-end fw-semibold text-success">
                                    {{ $item->debit > 0 ? number_format($item->debit, 2) : '-' }}
                                </td>
                                <td class="text-end fw-semibold text-danger">
                                    {{ $item->credit > 0 ? number_format($item->credit, 2) : '-' }}
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-label-success">{{ __('Cleared') }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">{{ __('No transactions in this reconciliation.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="4" class="text-end">{{ __('Total Cleared') }}:</td>
                            <td class="text-end text-success">{{ number_format($reconciliation->items->sum('debit'), 2) }}</td>
                            <td class="text-end text-danger">{{ number_format($reconciliation->items->sum('credit'), 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
