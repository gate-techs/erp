@extends('settings::layouts.printable')

@section('title', __('Bank Reconciliation Statement') . ' - ' . $reconciliation->reconciliation_number)
@section('report_title', __('Bank Reconciliation Statement') . ' - ' . ($reconciliation->cashAccount?->translate(app()->getLocale())?->name ?? $reconciliation->cashAccount?->code))

@section('report_meta')
    <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 14px; font-size: 11px; margin-top: 4px;">
        <span><strong>{{ __('Reconciliation #:') }}</strong> {{ $reconciliation->reconciliation_number }}</span>
        <span>•</span>
        <span><strong>{{ __('Statement Date:') }}</strong> {{ $reconciliation->statement_date->format('Y-m-d') }}</span>
        <span>•</span>
        <span><strong>{{ __('Bank:') }}</strong> {{ $reconciliation->cashAccount?->bank_name ?? '-' }} ({{ $reconciliation->cashAccount?->account_number ?? '-' }})</span>
        <span>•</span>
        <span><strong>{{ __('Status:') }}</strong> {{ $reconciliation->status->label() }}</span>
        <span>•</span>
        <span><strong>{{ __('Currency:') }}</strong> {{ $reconciliation->cashAccount?->currency ?? 'SAR' }}</span>
        <span>•</span>
        <span><strong>{{ __('Printed At:') }}</strong> {{ now()->format('Y-m-d H:i') }}</span>
    </div>
@endsection

@section('content')
    <!-- Summary Comparison Box -->
    <div style="margin: 15px 0; border: 1px solid #cbd5e1; border-radius: 6px; padding: 12px; background-color: #f8fafc; font-size: 12px;">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 4px; width: 25%;"><strong>{{ __('Statement Balance (Bank):') }}</strong></td>
                <td style="padding: 4px; width: 25%; font-family: monospace; font-size: 13px; font-weight: bold; color: #1e40af;">{{ number_format($reconciliation->statement_balance, 2) }}</td>
                <td style="padding: 4px; width: 25%;"><strong>{{ __('Book Balance (GL):') }}</strong></td>
                <td style="padding: 4px; width: 25%; font-family: monospace; font-size: 13px; font-weight: bold;">{{ number_format($reconciliation->book_balance, 2) }}</td>
            </tr>
            <tr>
                <td style="padding: 4px;"><strong>{{ __('Reconciled Cleared Total:') }}</strong></td>
                <td style="padding: 4px; font-family: monospace; font-size: 13px; font-weight: bold; color: #15803d;">{{ number_format($reconciliation->reconciled_balance, 2) }}</td>
                <td style="padding: 4px;"><strong>{{ __('Difference:') }}</strong></td>
                <td style="padding: 4px; font-family: monospace; font-size: 13px; font-weight: bold; color: {{ abs((float)$reconciliation->difference) < 0.0001 ? '#15803d' : '#b91c1c' }};">
                    {{ number_format($reconciliation->difference, 2) }}
                </td>
            </tr>
        </table>
    </div>

    <h4 style="font-size: 13px; margin: 15px 0 8px 0; border-bottom: 1px solid #cbd5e1; padding-bottom: 4px;">
        {{ __('Cleared & Reconciled Transactions') }}
    </h4>

    <table class="report-table" style="width: 100%; border-collapse: collapse; font-size: 11px; margin-top: 10px;">
        <thead>
            <tr style="background-color: #f8fafc; border-bottom: 2px solid #cbd5e1;">
                <th style="width: 40px; text-align: center; padding: 6px;">#</th>
                <th style="width: 85px; padding: 6px;">{{ __('Date') }}</th>
                <th style="width: 100px; padding: 6px;">{{ __('Entry #') }}</th>
                <th style="padding: 6px;">{{ __('Description / Reference') }}</th>
                <th style="width: 110px; text-align: end; padding: 6px;">{{ __('Deposits (Debit)') }}</th>
                <th style="width: 110px; text-align: end; padding: 6px;">{{ __('Payments (Credit)') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reconciliation->items as $index => $item)
                <tr style="border-bottom: 1px solid #e2e8f0;">
                    <td style="text-align: center; padding: 6px;">{{ $index + 1 }}</td>
                    <td style="padding: 6px;">{{ $item->transaction_date->format('Y-m-d') }}</td>
                    <td style="font-family: monospace; font-weight: bold; padding: 6px;">
                        {{ $item->journalEntryLine?->entry?->entry_number ?? '-' }}
                    </td>
                    <td style="padding: 6px;">
                        {{ $item->notes ?: ($item->journalEntryLine?->description ?: $item->statement_reference ?: '-') }}
                    </td>
                    <td style="text-align: end; font-family: monospace; font-weight: bold; padding: 6px; color: #15803d;">
                        {{ $item->debit > 0 ? number_format($item->debit, 2) : '-' }}
                    </td>
                    <td style="text-align: end; font-family: monospace; font-weight: bold; padding: 6px; color: #b91c1c;">
                        {{ $item->credit > 0 ? number_format($item->credit, 2) : '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 15px; color: #64748b;">{{ __('No transactions found in this statement.') }}</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #f1f5f9; border-top: 2px solid #94a3b8; font-weight: bold;">
                <td colspan="4" style="text-align: end; padding: 8px;">{{ __('Total Cleared:') }}</td>
                <td style="text-align: end; font-family: monospace; padding: 8px; color: #15803d;">{{ number_format($reconciliation->items->sum('debit'), 2) }}</td>
                <td style="text-align: end; font-family: monospace; padding: 8px; color: #b91c1c;">{{ number_format($reconciliation->items->sum('credit'), 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 30px; display: flex; justify-content: space-between; font-size: 11px; padding: 0 20px;">
        <div style="text-align: center;">
            <p style="margin-bottom: 40px;"><strong>{{ __('Prepared By:') }}</strong></p>
            <p>____________________</p>
        </div>
        <div style="text-align: center;">
            <p style="margin-bottom: 40px;"><strong>{{ __('Reviewed & Approved By:') }}</strong></p>
            <p>____________________</p>
        </div>
    </div>
@endsection
