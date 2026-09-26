@extends('settings::layouts.printable')

@section('title', __('Trial Balance'))
@section('report_title', __('Trial Balance'))

@section('report_meta')
    <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 14px; font-size: 11px; margin-top: 4px;">
        <span><strong>{{ __('Period:') }}</strong> {{ $filters['from_date'] }} {{ __('to') }} {{ $filters['to_date'] }}</span>
        <span>•</span>
        <span><strong>{{ __('Accounts Count:') }}</strong> {{ count($rows) }}</span>
        <span>•</span>
        <span><strong>{{ __('Status:') }}</strong> {{ $filters['status'] === 'all' ? __('All Entries') : __('Posted Only') }}</span>
        <span>•</span>
        <span><strong>{{ __('Display Scope:') }}</strong> {{ $filters['hide_zero'] ? __('Active Accounts Only') : __('All Accounts (Including Zero)') }}</span>
        <span>•</span>
        <span><strong>{{ __('Printed At:') }}</strong> {{ $printedAt->format('Y-m-d H:i') }}</span>
    </div>
@endsection

@section('content')
    <table class="report-table" style="width: 100%; border-collapse: collapse; font-size: 10.5px;">
        <thead>
            <tr style="text-align: center; background-color: #f1f5f9;">
                <th rowspan="2" style="text-align: start; min-width: 280px;">{{ __('Account') }}</th>
                <th colspan="2" style="border-inline: 1px solid #cbd5e1;">{{ __('Opening Balance') }}</th>
                <th colspan="2" style="border-inline: 1px solid #cbd5e1;">{{ __('Period Movements') }}</th>
                <th colspan="2">{{ __('Ending Balance') }}</th>
            </tr>
            <tr style="text-align: center; background-color: #f8fafc;">
                <th style="width: 85px; text-align: end;">{{ __('Debit') }}</th>
                <th style="width: 85px; text-align: end; border-inline-end: 1px solid #cbd5e1;">{{ __('Credit') }}</th>
                <th style="width: 85px; text-align: end;">{{ __('Debit') }}</th>
                <th style="width: 85px; text-align: end; border-inline-end: 1px solid #cbd5e1;">{{ __('Credit') }}</th>
                <th style="width: 85px; text-align: end;">{{ __('Debit') }}</th>
                <th style="width: 85px; text-align: end;">{{ __('Credit') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
                @php
                    $acc = $row['account'];
                @endphp
                <tr>
                    <td style="text-align: start; white-space: nowrap;">
                        <span style="font-weight: bold; margin-inline-end: 6px;">[{{ $acc->code }}]</span>
                        <span>{{ $acc->name }}</span>
                        @if($acc->account_type)
                            <span style="font-size: 9px; color: #64748b; margin-inline-start: 4px;">({{ $acc->account_type->label() }})</span>
                        @endif
                    </td>
                    <td style="text-align: end;">
                        {{ $row['initial_debit'] > 0 ? number_format($row['initial_debit'], 2) : '—' }}
                    </td>
                    <td style="text-align: end; border-inline-end: 1px solid #e2e8f0;">
                        {{ $row['initial_credit'] > 0 ? number_format($row['initial_credit'], 2) : '—' }}
                    </td>
                    <td style="text-align: end;">
                        {{ $row['period_debit'] > 0 ? number_format($row['period_debit'], 2) : '—' }}
                    </td>
                    <td style="text-align: end; border-inline-end: 1px solid #e2e8f0;">
                        {{ $row['period_credit'] > 0 ? number_format($row['period_credit'], 2) : '—' }}
                    </td>
                    <td style="text-align: end; font-weight: 600;">
                        {{ $row['ending_debit'] > 0 ? number_format($row['ending_debit'], 2) : '—' }}
                    </td>
                    <td style="text-align: end; font-weight: 600;">
                        {{ $row['ending_credit'] > 0 ? number_format($row['ending_credit'], 2) : '—' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 24px; color: #64748b;">
                        {{ __('No accounting transactions matching the filter criteria.') }}
                    </td>
                </tr>
            @endforelse
        </tbody>

        @if(!empty($rows))
            <tfoot>
                <tr style="background-color: #f1f5f9; font-weight: 800; font-size: 11px; border-top: 2px solid #334155; border-bottom: 3px double #334155;">
                    <td style="padding: 7px 8px; color: #1e293b; white-space: nowrap;">
                        {{ __('Grand Total') }}
                        @if($summary['is_balanced'])
                            <span style="color: #166534; font-size: 9.5px; margin-inline-start: 6px;">(✔ {{ __('Balanced') }})</span>
                        @else
                            <span style="color: #991b1b; font-size: 9.5px; margin-inline-start: 6px;">(⚠ {{ __('Difference:') }} {{ number_format(abs($summary['ending_debit'] - $summary['ending_credit']), 2) }})</span>
                        @endif
                    </td>
                    <td style="text-align: end; padding: 7px 8px; color: #1e293b;">
                        {{ number_format($summary['initial_debit'], 2) }}
                    </td>
                    <td style="text-align: end; padding: 7px 8px; color: #1e293b; border-inline-end: 1px solid #cbd5e1;">
                        {{ number_format($summary['initial_credit'], 2) }}
                    </td>
                    <td style="text-align: end; padding: 7px 8px; color: #1e293b;">
                        {{ number_format($summary['period_debit'], 2) }}
                    </td>
                    <td style="text-align: end; padding: 7px 8px; color: #1e293b; border-inline-end: 1px solid #cbd5e1;">
                        {{ number_format($summary['period_credit'], 2) }}
                    </td>
                    <td style="text-align: end; padding: 7px 8px; color: #1e293b;">
                        {{ number_format($summary['ending_debit'], 2) }}
                    </td>
                    <td style="text-align: end; padding: 7px 8px; color: #1e293b;">
                        {{ number_format($summary['ending_credit'], 2) }}
                    </td>
                </tr>
            </tfoot>
        @endif
    </table>
@endsection
