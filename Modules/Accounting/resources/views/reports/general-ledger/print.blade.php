@extends('settings::layouts.printable')

@section('title', __('General Ledger'))
@section('report_title', __('General Ledger'))

@section('report_meta')
    <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 14px; font-size: 11px; margin-top: 4px;">
        <span><strong>{{ __('Period:') }}</strong> {{ $filters['from_date'] }} {{ __('to') }} {{ $filters['to_date'] }}</span>
        <span>•</span>
        <span><strong>{{ __('Accounts Count:') }}</strong> {{ count($accounts) }}</span>
        <span>•</span>
        <span><strong>{{ __('Status:') }}</strong> {{ $filters['status'] === 'all' ? __('All Entries') : __('Posted Only') }}</span>
        <span>•</span>
        <span><strong>{{ __('Printed At:') }}</strong> {{ $printedAt->format('Y-m-d H:i') }}</span>
    </div>
@endsection

@section('content')
    {{-- Classic Standard Accounting Ledger Table --}}
    <table class="report-table" style="width: 100%; border-collapse: collapse; font-size: 10.5px;">
        <thead>
            <tr>
                <th style="width: 75px; text-align: center;">{{ __('Date') }}</th>
                <th style="width: 80px; text-align: center;">{{ __('Entry #') }}</th>
                <th style="width: 90px;">{{ __('Journal') }}</th>
                <th style="width: 90px;">{{ __('Cost Center') }}</th>
                <th>{{ __('Description / Narration') }}</th>
                <th style="width: 85px; text-align: end;">{{ __('Debit') }}</th>
                <th style="width: 85px; text-align: end;">{{ __('Credit') }}</th>
                <th style="width: 95px; text-align: end;">{{ __('Balance') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($accounts as $accData)
                @php
                    $acc = $accData['account'];
                @endphp
                {{-- Account Header Row --}}
                <tr style="background-color: #f1f5f9; font-weight: 700; border-top: 1px solid #cbd5e1; border-bottom: 1px solid #cbd5e1;">
                    <td colspan="5" style="padding: 6px 8px; font-size: 11px; color: #1e293b;">
                        <span style="font-family: monospace; font-weight: 800; margin-inline-end: 6px;">
                            [{{ $acc->code }}]
                        </span>
                        <span>{{ $acc->name }}</span>
                        @if($acc->account_type)
                            <span style="font-size: 9.5px; font-weight: normal; color: #64748b; margin-inline-start: 6px;">
                                ({{ $acc->account_type->label() }})
                            </span>
                        @endif
                    </td>
                    <td colspan="3" style="text-align: end; padding: 6px 8px; font-size: 10.5px; color: #1e293b;">
                        <span style="color: #64748b; font-weight: normal;">{{ __('Initial Balance:') }}</span>
                        <strong>
                            {{ number_format(abs($accData['initial_net']), 2) }}
                            <span style="font-size: 9px; font-weight: normal;">{{ $accData['initial_net'] >= 0 ? ($accData['is_debit_normal'] ? __('Debit') : __('Credit')) : ($accData['is_debit_normal'] ? __('Credit') : __('Debit')) }}</span>
                        </strong>
                    </td>
                </tr>

                {{-- Movement Lines --}}
                @forelse($accData['movements'] as $move)
                    <tr>
                        <td style="text-align: center; font-family: monospace;">{{ $move->journalEntry?->entry_date?->format('Y-m-d') }}</td>
                        <td style="text-align: center; font-family: monospace; font-weight: 600;">
                            {{ $move->journalEntry?->entry_number }}
                        </td>
                        <td>{{ $move->journalEntry?->journal?->name ?? '—' }}</td>
                        <td>{{ $move->costCenter?->name ?? '—' }}</td>
                        <td>
                            {{ $move->description ?: ($move->journalEntry?->description ?? '—') }}
                        </td>
                        <td style="text-align: end; font-family: monospace;">
                            {{ $move->debit > 0 ? number_format($move->debit, 2) : '—' }}
                        </td>
                        <td style="text-align: end; font-family: monospace;">
                            {{ $move->credit > 0 ? number_format($move->credit, 2) : '—' }}
                        </td>
                        <td style="text-align: end; font-family: monospace; font-weight: 600;">
                            {{ number_format(abs($move->running_balance), 2) }}
                            <span style="font-size: 8.5px; font-weight: normal; color: #64748b;">
                                {{ $move->running_balance >= 0 ? ($accData['is_debit_normal'] ? __('Dr') : __('Cr')) : ($accData['is_debit_normal'] ? __('Cr') : __('Dr')) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; color: #64748b; font-style: italic; padding: 6px;">
                            {{ __('No recorded transactions during the selected period.') }}
                        </td>
                    </tr>
                @endforelse

                {{-- Account Ending Summary Line --}}
                <tr style="background-color: #f8fafc; font-weight: bold; border-top: 1px solid #cbd5e1; border-bottom: 1px solid #cbd5e1;">
                    <td colspan="5" style="text-align: end; color: #334155; padding: 5px 8px;">
                        {{ __('Account Period Total / Ending Balance:') }}
                    </td>
                    <td style="text-align: end; font-family: monospace;">
                        {{ number_format($accData['period_debit'], 2) }}
                    </td>
                    <td style="text-align: end; font-family: monospace;">
                        {{ number_format($accData['period_credit'], 2) }}
                    </td>
                    <td style="text-align: end; font-family: monospace;">
                        {{ number_format(abs($accData['ending_net']), 2) }}
                        <span style="font-size: 8.5px; font-weight: normal;">
                            {{ $accData['ending_net'] >= 0 ? ($accData['is_debit_normal'] ? __('Debit') : __('Credit')) : ($accData['is_debit_normal'] ? __('Credit') : __('Debit')) }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 24px; color: #64748b;">
                        {{ __('No accounts or transactions matching current filter criteria.') }}
                    </td>
                </tr>
            @endforelse
        </tbody>

        @if(!empty($accounts))
            <tfoot>
                <tr style="background-color: #f1f5f9; font-weight: 800; font-size: 11px; border-top: 2px solid #334155; border-bottom: 3px double #334155;">
                    <td colspan="5" style="text-align: center; padding: 7px 8px; color: #1e293b;">
                        {{ __('Grand Total for All Accounts') }}
                    </td>
                    <td style="text-align: end; font-family: monospace; padding: 7px 8px; color: #1e293b;">
                        {{ number_format($summary['period_debit'], 2) }}
                    </td>
                    <td style="text-align: end; font-family: monospace; padding: 7px 8px; color: #1e293b;">
                        {{ number_format($summary['period_credit'], 2) }}
                    </td>
                    <td style="text-align: end; font-family: monospace; padding: 7px 8px; color: #1e293b;">
                        @if($summary['is_balanced'])
                            {{ __('Balanced') }}
                        @else
                            {{ __('Difference:') }} {{ number_format(abs($summary['period_debit'] - $summary['period_credit']), 2) }}
                        @endif
                    </td>
                </tr>
            </tfoot>
        @endif
    </table>
@endsection
