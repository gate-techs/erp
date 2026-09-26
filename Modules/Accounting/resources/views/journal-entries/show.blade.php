@extends('layouts/contentNavbarLayout')

@section('title', __('Journal Entry Voucher') . ': ' . $entry->entry_number . ' - ' . config('variables.templateName'))

@section('content')
<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4 d-print-none">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <i class="icon-base bx bx-receipt text-primary font-24"></i>
            <span>{{ __('Journal Entry Voucher') }}: {{ $entry->entry_number }}</span>
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('Accounting') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('accounting.journal-entries.index') }}">{{ __('Journal Entries') }}</a></li>
                <li class="breadcrumb-item active">{{ $entry->entry_number }}</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-label-secondary d-flex align-items-center gap-1 shadow-xs" onclick="window.print();">
            <i class="icon-base bx bx-printer font-18"></i>
            <span>{{ __('Print Voucher') }}</span>
        </button>

        @if($entry->canBeEdited())
            <a href="{{ route('accounting.journal-entries.edit', $entry->id) }}" class="btn btn-primary d-flex align-items-center gap-1 shadow-xs">
                <i class="icon-base bx bx-edit font-18"></i>
                <span>{{ __('Edit Draft') }}</span>
            </a>
            <form action="{{ route('accounting.journal-entries.post', $entry->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to post this journal entry? Once posted, it cannot be modified.') }}');">
                @csrf
                <button type="submit" class="btn btn-success d-flex align-items-center gap-1 shadow-xs">
                    <i class="icon-base bx bx-check-double font-18"></i>
                    <span>{{ __('Post Entry') }}</span>
                </button>
            </form>
        @elseif($entry->canBeReversed())
            <button type="button" class="btn btn-warning d-flex align-items-center gap-1 shadow-xs" data-bs-toggle="modal" data-bs-target="#reverseEntryModal">
                <i class="icon-base bx bx-undo font-18"></i>
                <span>{{ __('Reverse Entry') }}</span>
            </button>
        @endif

        <a href="{{ route('accounting.journal-entries.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-1 shadow-xs">
            <i class="icon-base bx bx-arrow-back font-18"></i>
            <span>{{ __('Back') }}</span>
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible d-flex align-items-center mb-4 d-print-none" role="alert">
        <i class="icon-base bx bx-check-circle me-2 font-20"></i>
        <div>{{ session('success') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($entry->is_reversal && $entry->reversedEntry)
    <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
        <i class="icon-base bx bx-undo me-2 font-20"></i>
        <div>
            {{ __('This is a reversal voucher for entry:') }}
            <a href="{{ route('accounting.journal-entries.show', $entry->reversedEntry->id) }}" class="fw-bold text-dark text-decoration-underline">
                {{ $entry->reversedEntry->entry_number }}
            </a>
        </div>
    </div>
@endif

@if($entry->reversalEntry()->exists())
    @php $rev = $entry->reversalEntry()->first(); @endphp
    <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
        <i class="icon-base bx bx-info-circle me-2 font-20"></i>
        <div>
            {{ __('This journal entry has been reversed by entry:') }}
            <a href="{{ route('accounting.journal-entries.show', $rev->id) }}" class="fw-bold text-dark text-decoration-underline">
                {{ $rev->entry_number }}
            </a>
            ({{ $rev->entry_date->format('Y-m-d') }})
        </div>
    </div>
@endif

<!-- Voucher Document Card -->
<div class="card shadow-sm border-0 mb-4 printable-voucher">
    <div class="card-body p-4 p-md-5">
        <!-- Voucher Header -->
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start gap-4 pb-4 border-bottom mb-4">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="avatar avatar-md bg-primary text-white rounded d-flex align-items-center justify-content-center">
                        <i class="icon-base bx bx-receipt font-24"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold text-heading mb-0">{{ $entry->entity?->translate(app()->getLocale())?->name ?? $entry->entity?->code }}</h4>
                        <span class="text-muted small">{{ __('Accounting Entity Code:') }} {{ $entry->entity?->code }}</span>
                    </div>
                </div>
                <h5 class="fw-bold text-primary mb-0 mt-3">{{ __('Journal Entry Voucher') }} ({{ $entry->entry_number }})</h5>
            </div>

            <div class="text-sm-end">
                <div class="mb-2">
                    <span class="badge {{ $entry->status->badgeClass() }} fs-6 px-3 py-2 fw-bold">
                        {{ $entry->status->label() }}
                    </span>
                </div>
                <div class="text-muted small mb-1"><strong>{{ __('Journal:') }}</strong> {{ $entry->journal?->translate(app()->getLocale())?->name ?? $entry->journal?->code }} ({{ $entry->journal?->code }})</div>
                <div class="text-muted small mb-1"><strong>{{ __('Entry Date:') }}</strong> {{ $entry->entry_date->format('Y-m-d') }}</div>
                @if($entry->reference)
                    <div class="text-muted small mb-1"><strong>{{ __('Reference:') }}</strong> {{ $entry->reference }}</div>
                @endif
                <div class="text-muted small"><strong>{{ __('Currency:') }}</strong> {{ $entry->currency }}</div>
            </div>
        </div>

        <!-- Narration Box -->
        <div class="bg-label-secondary rounded p-3 mb-4">
            <span class="fw-bold text-heading d-block mb-1 small">{{ __('General Description / Narration:') }}</span>
            <p class="mb-0 text-dark">{{ $entry->narration }}</p>
        </div>

        <!-- Lines Table -->
        <div class="table-responsive mb-4">
            <table class="table table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 40px;" class="text-center">#</th>
                        <th>{{ __('Account') }}</th>
                        <th>{{ __('Cost Center') }}</th>
                        <th class="text-end" style="width: 140px;">{{ __('Debit') }}</th>
                        <th class="text-end" style="width: 140px;">{{ __('Credit') }}</th>
                        <th>{{ __('Description') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($entry->lines as $idx => $line)
                        <tr>
                            <td class="text-center text-muted fw-semibold">{{ $idx + 1 }}</td>
                            <td>
                                <span class="fw-bold text-heading">{{ $line->account?->code }} - {{ $line->account?->translate(app()->getLocale())?->name }}</span>
                            </td>
                            <td>
                                @if($line->costCenter)
                                    <span class="badge bg-label-secondary font-11">
                                        {{ $line->costCenter->code }} - {{ $line->costCenter->translate(app()->getLocale())?->name }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end fw-semibold {{ (float) $line->debit > 0 ? 'text-success' : 'text-muted' }}" style="font-variant-numeric: tabular-nums; font-size: 0.875rem;">
                                {{ (float) $line->debit > 0 ? number_format((float) $line->debit, 2) : '-' }}
                            </td>
                            <td class="text-end fw-semibold {{ (float) $line->credit > 0 ? 'text-danger' : 'text-muted' }}" style="font-variant-numeric: tabular-nums; font-size: 0.875rem;">
                                {{ (float) $line->credit > 0 ? number_format((float) $line->credit, 2) : '-' }}
                            </td>
                            <td>{{ $line->description }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <td colspan="3" class="text-end fw-bold text-muted font-13">{{ __('Total') }} ({{ $entry->currency }}):</td>
                        <td class="text-end fw-bold text-success" style="font-variant-numeric: tabular-nums; font-size: 0.875rem;">{{ number_format((float) $entry->total_debit, 2) }}</td>
                        <td class="text-end fw-bold text-danger" style="font-variant-numeric: tabular-nums; font-size: 0.875rem;">{{ number_format((float) $entry->total_credit, 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Audit Trail & Signatures -->
        <div class="row g-4 pt-3 border-top mt-4">
            <div class="col-12 col-md-4">
                <div class="p-3 border rounded">
                    <span class="text-muted small d-block mb-1">{{ __('Created By') }}</span>
                    <strong class="text-heading d-block">{{ $entry->creator?->name ?? '-' }}</strong>
                    <small class="text-muted">{{ $entry->created_at->format('Y-m-d H:i') }}</small>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="p-3 border rounded">
                    <span class="text-muted small d-block mb-1">{{ __('Posted / Approved By') }}</span>
                    <strong class="text-heading d-block">{{ $entry->postedBy?->name ?? __('Not posted yet') }}</strong>
                    <small class="text-muted">{{ $entry->posted_at ? $entry->posted_at->format('Y-m-d H:i') : '-' }}</small>
                </div>
            </div>

            <div class="col-12 col-md-4 text-center d-flex flex-column justify-content-end">
                <div class="border-top pt-2">
                    <span class="text-muted small">{{ __('Accountant Signature & Stamp') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reverse Modal -->
@if($entry->canBeReversed())
<div class="modal fade" id="reverseEntryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('accounting.journal-entries.reverse', $entry->id) }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title fw-bold d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-undo text-warning font-22"></i>
                    <span>{{ __('Reverse Journal Entry') }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <p class="text-muted mb-3">
                    {{ __('You are about to create an automated reversal voucher for entry:') }}
                    <strong class="text-heading fw-bold">{{ $entry->entry_number }}</strong>
                </p>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold" for="modal_reversal_date">{{ __('Reversal Entry Date') }} <span class="text-danger">*</span></label>
                        <input type="date" name="entry_date" id="modal_reversal_date" class="form-control" value="{{ now()->toDateString() }}" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold" for="modal_reversal_reason">{{ __('Reversal Reason / Notes') }}</label>
                        <textarea name="reason" id="modal_reversal_reason" class="form-control" rows="3" placeholder="{{ __('e.g. Correcting incorrect account line') }}"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 pb-4">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-warning">
                    <i class="icon-base bx bx-undo me-1"></i>
                    <span>{{ __('Confirm Reversal') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection
