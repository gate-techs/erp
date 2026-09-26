@extends('settings::layouts.printable')

@section('title', __('Income Statement'))
@section('report_title', __('Income Statement'))

@section('report_meta')
    <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 14px; font-size: 11px; margin-top: 4px;">
        <span><strong>{{ __('Period:') }}</strong> {{ $filters['from_date'] }} {{ __('to') }} {{ $filters['to_date'] }}</span>
        <span>•</span>
        <span><strong>{{ __('Status:') }}</strong> {{ $filters['status'] === 'all' ? __('All Entries') : __('Posted Only') }}</span>
        <span>•</span>
        <span><strong>{{ __('Display Scope:') }}</strong> {{ $filters['hide_zero'] ? __('Active Accounts Only') : __('All Accounts (Including Zero)') }}</span>
        <span>•</span>
        <span><strong>{{ __('Printed At:') }}</strong> {{ $printedAt->format('Y-m-d H:i') }}</span>
    </div>
@endsection

@section('content')
    <table class="report-table" style="width: 100%; border-collapse: collapse; font-size: 11px;">
        <thead>
            <tr style="background-color: #f1f5f9; text-align: center; border-bottom: 2px solid #cbd5e1;">
                <th style="text-align: start; padding: 6px 8px; min-width: 220px;">{{ __('Account') }}</th>
                <th style="width: 100px; text-align: end; padding: 6px 8px;">{{ __('Debit') }}</th>
                <th style="width: 100px; text-align: end; padding: 6px 8px;">{{ __('Credit') }}</th>
                <th style="width: 120px; text-align: end; padding: 6px 8px;">{{ __('Net Movement') }}</th>
                <th style="width: 80px; text-align: end; padding: 6px 8px;">{{ __('Percentage %') }}</th>
            </tr>
        </thead>
        <tbody>
            <!-- 1. Revenues Section -->
            <tr style="background-color: #f8fafc; font-weight: bold; border-top: 1px solid #cbd5e1; border-bottom: 1px solid #e2e8f0;">
                <td colspan="5" style="padding: 6px 8px;">
                    {{ __('Revenues') }}
                </td>
            </tr>

            @forelse($revenues as $row)
                @php
                    $acc = $row['account'];
                @endphp
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="text-align: start; padding: 5px 8px; padding-inline-start: 18px;">
                        <span style="font-weight: bold; margin-inline-end: 6px;">[{{ $acc->code }}]</span>
                        <span>{{ $acc->name }}</span>
                    </td>
                    <td style="text-align: end; padding: 5px 8px; color: #64748b;">
                        {{ $row['debit'] > 0 ? number_format($row['debit'], 2) : '—' }}
                    </td>
                    <td style="text-align: end; padding: 5px 8px; color: #64748b;">
                        {{ $row['credit'] > 0 ? number_format($row['credit'], 2) : '—' }}
                    </td>
                    <td style="text-align: end; padding: 5px 8px; font-weight: 600;">
                        {{ number_format($row['amount'], 2) }}
                    </td>
                    <td style="text-align: end; padding: 5px 8px; color: #64748b;">
                        {{ $row['percentage'] }}%
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 12px; color: #64748b;">
                        {{ __('No revenue transactions recorded during this period.') }}
                    </td>
                </tr>
            @endforelse

            <!-- Revenues Subtotal -->
            <tr style="background-color: #f1f5f9; font-weight: bold; border-top: 1px solid #cbd5e1; border-bottom: 2px solid #cbd5e1;">
                <td style="padding: 6px 8px; padding-inline-start: 18px;">{{ __('Total Revenues') }}</td>
                <td style="text-align: end; padding: 6px 8px; color: #64748b;">—</td>
                <td style="text-align: end; padding: 6px 8px; color: #64748b;">—</td>
                <td style="text-align: end; padding: 6px 8px;">{{ number_format($summary['total_revenues'], 2) }}</td>
                <td style="text-align: end; padding: 6px 8px;">100.00%</td>
            </tr>

            <!-- 2. Expenses Section -->
            <tr style="background-color: #f8fafc; font-weight: bold; border-top: 2px solid #cbd5e1; border-bottom: 1px solid #e2e8f0;">
                <td colspan="5" style="padding: 6px 8px;">
                    {{ __('Expenses') }}
                </td>
            </tr>

            @forelse($expenses as $row)
                @php
                    $acc = $row['account'];
                @endphp
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="text-align: start; padding: 5px 8px; padding-inline-start: 18px;">
                        <span style="font-weight: bold; margin-inline-end: 6px;">[{{ $acc->code }}]</span>
                        <span>{{ $acc->name }}</span>
                    </td>
                    <td style="text-align: end; padding: 5px 8px; color: #64748b;">
                        {{ $row['debit'] > 0 ? number_format($row['debit'], 2) : '—' }}
                    </td>
                    <td style="text-align: end; padding: 5px 8px; color: #64748b;">
                        {{ $row['credit'] > 0 ? number_format($row['credit'], 2) : '—' }}
                    </td>
                    <td style="text-align: end; padding: 5px 8px; font-weight: 600;">
                        {{ number_format($row['amount'], 2) }}
                    </td>
                    <td style="text-align: end; padding: 5px 8px; color: #64748b;">
                        {{ $row['percentage'] }}%
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 12px; color: #64748b;">
                        {{ __('No expense transactions recorded during this period.') }}
                    </td>
                </tr>
            @endforelse

            <!-- Expenses Subtotal -->
            <tr style="background-color: #f1f5f9; font-weight: bold; border-top: 1px solid #cbd5e1; border-bottom: 2px solid #cbd5e1;">
                <td style="padding: 6px 8px; padding-inline-start: 18px;">{{ __('Total Expenses') }}</td>
                <td style="text-align: end; padding: 6px 8px; color: #64748b;">—</td>
                <td style="text-align: end; padding: 6px 8px; color: #64748b;">—</td>
                <td style="text-align: end; padding: 6px 8px;">{{ number_format($summary['total_expenses'], 2) }}</td>
                <td style="text-align: end; padding: 6px 8px;">
                    @if($summary['total_revenues'] > 0)
                        {{ round(($summary['total_expenses'] / $summary['total_revenues']) * 100, 2) }}%
                    @else
                        —
                    @endif
                </td>
            </tr>

            <!-- Grand Result Row -->
            <tr style="background-color: #f1f5f9; font-weight: 800; font-size: 11.5px; border-top: 2px solid #334155; border-bottom: 3px double #334155;">
                <td style="padding: 8px 10px; color: #0f172a;">
                    {{ $summary['is_profitable'] ? __('Net Profit for the Period') : __('Net Loss for the Period') }}
                </td>
                <td style="text-align: end; padding: 8px 10px; color: #64748b;">—</td>
                <td style="text-align: end; padding: 8px 10px; color: #64748b;">—</td>
                <td style="text-align: end; padding: 8px 10px; color: #0f172a;">
                    {{ number_format($summary['net_profit'], 2) }}
                </td>
                <td style="text-align: end; padding: 8px 10px;">
                    {{ $summary['profit_margin'] }}%
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Signatures Block -->
    <div style="display: flex; justify-content: space-between; margin-top: 40px; padding: 0 30px; font-size: 11px;">
        <div style="text-align: center;">
            <div style="font-weight: bold; margin-bottom: 35px;">{{ __('Responsible Accountant') }}</div>
            <div style="border-top: 1px dashed #94a3b8; width: 140px; margin: 0 auto; padding-top: 4px; color: #64748b;">{{ __('Signature') }}</div>
        </div>
        <div style="text-align: center;">
            <div style="font-weight: bold; margin-bottom: 35px;">{{ __('Internal Auditor') }}</div>
            <div style="border-top: 1px dashed #94a3b8; width: 140px; margin: 0 auto; padding-top: 4px; color: #64748b;">{{ __('Signature') }}</div>
        </div>
        <div style="text-align: center;">
            <div style="font-weight: bold; margin-bottom: 35px;">{{ __('Financial Manager') }}</div>
            <div style="border-top: 1px dashed #94a3b8; width: 140px; margin: 0 auto; padding-top: 4px; color: #64748b;">{{ __('Approval') }}</div>
        </div>
    </div>
@endsection
