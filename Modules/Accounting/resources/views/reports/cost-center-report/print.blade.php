@extends('settings::layouts.printable')

@section('title', __('Cost Center Performance Report'))
@section('report_title', __('Cost Center Performance Report'))

@section('report_meta')
    <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 14px; font-size: 11px; margin-top: 4px;">
        <span><strong>{{ __('Period:') }}</strong> {{ $filters['from_date'] }} {{ __('to') }} {{ $filters['to_date'] }}</span>
        <span>•</span>
        <span><strong>{{ __('Centers Count:') }}</strong> {{ count($rows) }}</span>
        <span>•</span>
        <span><strong>{{ __('Status:') }}</strong> {{ $filters['status'] === 'all' ? __('All Entries') : __('Posted Entries Only') }}</span>
        <span>•</span>
        <span><strong>{{ __('Printed At:') }}</strong> {{ $printedAt->format('Y-m-d H:i') }}</span>
    </div>
@endsection

@section('content')
    <table class="report-table" style="width: 100%; border-collapse: collapse; font-size: 10.5px;">
        <thead>
            <tr>
                <th style="width: 80px; text-align: center; padding: 7px 8px;">{{ __('Center Code') }}</th>
                <th style="text-align: start; padding: 7px 10px;">{{ __('Cost Center Name') }}</th>
                <th style="width: 110px; text-align: end; padding: 7px 8px;">{{ __('Total Revenues') }}</th>
                <th style="width: 110px; text-align: end; padding: 7px 8px;">{{ __('Total Expenses') }}</th>
                <th style="width: 120px; text-align: end; padding: 7px 8px;">{{ __('Net Profit / Loss') }}</th>
                <th style="width: 80px; text-align: center; padding: 7px 8px;">{{ __('Profit Margin %') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $r)
                @php
                    $cc = $r['cost_center'];
                @endphp
                <tr>
                    <td style="text-align: center; color: #475569;">
                        {{ $cc ? $cc->code : '—' }}
                    </td>
                    <td style="padding: 6px 10px;">
                        @if($cc)
                            <span style="font-weight: 600; color: #1e293b;">{{ $cc->name }}</span>
                            @if($cc->parent)
                                <span style="font-size: 9.5px; color: #64748b; margin-inline-start: 6px;">({{ $cc->parent->name }})</span>
                            @endif
                        @else
                            <span style="font-style: italic; color: #64748b;">{{ __('Without Cost Center (Unassigned)') }}</span>
                        @endif
                    </td>
                    <td style="text-align: end; padding: 6px 8px;">
                        {{ $r['revenues'] > 0 ? number_format($r['revenues'], 2) : '—' }}
                    </td>
                    <td style="text-align: end; padding: 6px 8px;">
                        {{ $r['expenses'] > 0 ? number_format($r['expenses'], 2) : '—' }}
                    </td>
                    <td style="text-align: end; padding: 6px 8px; font-weight: 600;">
                        {{ number_format($r['net_profit'], 2) }}
                    </td>
                    <td style="text-align: center; padding: 6px 8px;">
                        {{ $r['profit_margin'] }}%
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #64748b; font-style: italic; padding: 16px;">
                        {{ __('No transactions or cost centers match the search') }}
                    </td>
                </tr>
            @endforelse
        </tbody>

        @if(!empty($rows))
            <tfoot>
                <tr style="background-color: #f1f5f9; font-weight: 800; font-size: 11px; border-top: 2px solid #334155; border-bottom: 3px double #334155;">
                    <td colspan="2" style="text-align: center; padding: 7px 10px; color: #1e293b;">
                        {{ __('Grand Total for All Centers') }}
                    </td>
                    <td style="text-align: end; padding: 7px 8px; color: #1e293b;">
                        {{ number_format($summary['total_revenues'], 2) }}
                    </td>
                    <td style="text-align: end; padding: 7px 8px; color: #1e293b;">
                        {{ number_format($summary['total_expenses'], 2) }}
                    </td>
                    <td style="text-align: end; padding: 7px 8px; color: #1e293b;">
                        {{ number_format($summary['net_profit'], 2) }}
                    </td>
                    <td style="text-align: center; padding: 7px 8px; color: #1e293b;">
                        {{ $summary['profit_margin'] }}%
                    </td>
                </tr>
            </tfoot>
        @endif
    </table>

    {{-- Signatures Block --}}
    <div style="margin-top: 35px; display: flex; justify-content: space-between; font-size: 11px; padding: 0 20px;">
        <div style="text-align: center;">
            <p style="margin-bottom: 40px; font-weight: 600;">{{ __('Accountant Preparation') }}</p>
            <p>..................................</p>
        </div>
        <div style="text-align: center;">
            <p style="margin-bottom: 40px; font-weight: 600;">{{ __('Financial Auditor') }}</p>
            <p>..................................</p>
        </div>
        <div style="text-align: center;">
            <p style="margin-bottom: 40px; font-weight: 600;">{{ __('Financial Management Approval') }}</p>
            <p>..................................</p>
        </div>
    </div>
@endsection
