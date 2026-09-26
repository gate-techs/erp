@extends('settings::layouts.printable')

@section('title', __('Detailed Account Statement'))
@section('report_title', __('Detailed Account Statement'))

@section('report_meta')
    <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 14px; font-size: 11px; margin-top: 4px;">
        @if($account)
            <span><strong>{{ __('Account:') }}</strong> [{{ $account->code }}] {{ $account->name }}</span>
            <span>•</span>
        @endif
        <span><strong>{{ __('Period:') }}</strong> {{ $filters['from_date'] }} {{ __('to') }} {{ $filters['to_date'] }}</span>
        <span>•</span>
        <span><strong>{{ __('Status:') }}</strong> {{ $filters['status'] === 'all' ? __('All Entries') : __('Posted Only') }}</span>
        <span>•</span>
        <span><strong>{{ __('Printed At:') }}</strong> {{ $printedAt->format('Y-m-d H:i') }}</span>
    </div>
@endsection

@section('content')
    @if($account)
        {{-- Account Header Details --}}
        <div style="margin-bottom: 12px; padding: 8px 12px; background-color: #f8fafc; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 11px;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                <div>
                    <strong>{{ __('Account Code:') }}</strong> <span>{{ $account->code }}</span> &nbsp;|&nbsp;
                    <strong>{{ __('Account Name:') }}</strong> <span>{{ $account->name }}</span> &nbsp;|&nbsp;
                    <strong>{{ __('Type:') }}</strong> <span>{{ $account->account_type?->label() ?? '—' }}</span>
                </div>
                <div>
                    <strong>{{ __('Opening Balance:') }}</strong> 
                    <span style="font-weight: 700; color: #1e293b;">
                        {{ number_format(abs($summary['initial_balance']), 2) }}
                        <span style="font-size: 9.5px; font-weight: normal;">
                            {{ $summary['initial_balance'] >= 0 ? ($account->is_debit_normal ? __('Debit') : __('Credit')) : ($account->is_debit_normal ? __('Credit') : __('Debit')) }}
                        </span>
                    </span>
                </div>
            </div>
        </div>
    @endif

    {{-- Movements Table --}}
    <table class="report-table" style="width: 100%; border-collapse: collapse; font-size: 10.5px;">
        <thead>
            <tr>
                <th style="width: 75px; text-align: center;">{{ __('Date') }}</th>
                <th style="width: 80px; text-align: center;">{{ __('Entry #') }}</th>
                <th style="width: 90px;">{{ __('Journal') }}</th>
                <th>{{ __('Description / Narration') }}</th>
                <th style="width: 80px; text-align: center;">{{ __('Reference') }}</th>
                <th style="width: 85px; text-align: end;">{{ __('Debit') }}</th>
                <th style="width: 85px; text-align: end;">{{ __('Credit') }}</th>
                <th style="width: 100px; text-align: end;">{{ __('Running Balance') }}</th>
            </tr>
        </thead>
        <tbody>
            {{-- Initial Balance Row --}}
            <tr style="background-color: #f1f5f9; font-weight: 700; border-top: 1px solid #cbd5e1; border-bottom: 1px solid #cbd5e1;">
                <td colspan="5" style="padding: 6px 8px; color: #1e293b;">
                    {{ __('Initial balance before') }} {{ $filters['from_date'] }}
                </td>
                <td style="text-align: end; color: #64748b;">—</td>
                <td style="text-align: end; color: #64748b;">—</td>
                <td style="text-align: end; padding: 6px 8px;">
                    {{ number_format($summary['initial_balance'], 2) }}
                </td>
            </tr>

            {{-- Movement Lines --}}
            @forelse($lines as $line)
                <tr>
                    <td style="text-align: center;">{{ $line['date'] }}</td>
                    <td style="text-align: center; font-weight: 600;">
                        {{ $line['entry_number'] }}
                    </td>
                    <td>{{ $line['journal_name'] }}</td>
                    <td>
                        {{ $line['description'] }}
                        @if(!empty($line['partner_name']))
                            <span style="color: #64748b; font-size: 9px;">({{ $line['partner_name'] }})</span>
                        @endif
                    </td>
                    <td style="text-align: center; color: #64748b;">{{ $line['reference'] ?: '—' }}</td>
                    <td style="text-align: end;">
                        {{ $line['debit'] > 0 ? number_format($line['debit'], 2) : '—' }}
                    </td>
                    <td style="text-align: end;">
                        {{ $line['credit'] > 0 ? number_format($line['credit'], 2) : '—' }}
                    </td>
                    <td style="text-align: end; font-weight: 600;">
                        {{ number_format($line['running_balance'], 2) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; color: #64748b; font-style: italic; padding: 12px;">
                        {{ __('No recorded transactions for this account during the selected period.') }}
                    </td>
                </tr>
            @endforelse
        </tbody>

        <tfoot>
            {{-- Period Totals --}}
            <tr style="background-color: #f8fafc; font-weight: 700; border-top: 2px solid #cbd5e1; border-bottom: 1px solid #cbd5e1;">
                <td colspan="5" style="text-align: end; padding: 6px 8px; color: #334155;">
                    {{ __('Total Period Movements:') }}
                </td>
                <td style="text-align: end; padding: 6px 8px;">
                    {{ number_format($summary['total_debit'], 2) }}
                </td>
                <td style="text-align: end; padding: 6px 8px;">
                    {{ number_format($summary['total_credit'], 2) }}
                </td>
                <td style="text-align: end; padding: 6px 8px; color: #64748b;">
                    {{ number_format($summary['total_debit'] - $summary['total_credit'], 2) }}
                </td>
            </tr>

            {{-- Ending Balance --}}
            <tr style="background-color: #f1f5f9; font-weight: 800; font-size: 11px; border-bottom: 3px double #334155;">
                <td colspan="5" style="text-align: end; padding: 7px 8px; color: #1e293b;">
                    {{ __('Ending Balance at') }} {{ $filters['to_date'] }}:
                </td>
                <td colspan="2" style="text-align: center; color: #64748b; font-weight: normal; font-size: 9.5px;">
                    @if($account)
                        {{ $summary['ending_balance'] >= 0 ? ($account->is_debit_normal ? __('Debit Balance') : __('Credit Balance')) : ($account->is_debit_normal ? __('Credit Balance') : __('Debit Balance')) }}
                    @endif
                </td>
                <td style="text-align: end; padding: 7px 8px; color: #1e293b;">
                    {{ number_format(abs($summary['ending_balance']), 2) }}
                </td>
            </tr>
        </tfoot>
    </table>

    {{-- Signatures Footer Block --}}
    <div style="margin-top: 30px; display: flex; justify-content: space-between; font-size: 11px; padding: 0 20px;">
        <div style="text-align: center;">
            <p style="margin-bottom: 40px; font-weight: 600;">{{ __('Prepared By') }}</p>
            <p>..................................</p>
        </div>
        <div style="text-align: center;">
            <p style="margin-bottom: 40px; font-weight: 600;">{{ __('Audited By') }}</p>
            <p>..................................</p>
        </div>
        <div style="text-align: center;">
            <p style="margin-bottom: 40px; font-weight: 600;">{{ __('Financial Approval') }}</p>
            <p>..................................</p>
        </div>
    </div>
@endsection
