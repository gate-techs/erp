@extends('settings::layouts.printable')

@section('title', __('Cash Flow Statement'))
@section('report_title', __('Cash Flow Statement'))

@section('report_meta')
    <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 14px; font-size: 11px; margin-top: 4px;">
        <span><strong>{{ __('Period:') }}</strong> {{ $filters['from_date'] }} {{ __('to') }} {{ $filters['to_date'] }}</span>
        <span>•</span>
        <span><strong>{{ __('Status:') }}</strong> {{ $filters['status'] === 'all' ? __('All Entries') : __('Posted Entries Only') }}</span>
        <span>•</span>
        <span><strong>{{ __('Printed At:') }}</strong> {{ $printedAt->format('Y-m-d H:i') }}</span>
    </div>
@endsection

@section('content')
    <table class="report-table" style="width: 100%; border-collapse: collapse; font-size: 11px;">
        <thead>
            <tr>
                <th style="text-align: start; padding: 7px 10px;">{{ __('Description / Activities') }}</th>
                <th style="width: 130px; text-align: end; padding: 7px 10px;">{{ __('Partial') }}</th>
                <th style="width: 130px; text-align: end; padding: 7px 10px;">{{ __('Total') }}</th>
            </tr>
        </thead>
        <tbody>
            {{-- 1. Operating Activities --}}
            <tr style="background-color: #f1f5f9; font-weight: 700; border-top: 1px solid #cbd5e1; border-bottom: 1px solid #cbd5e1;">
                <td colspan="3" style="padding: 6px 10px; color: #1e293b;">
                    {{ __('1. Cash Flows from Operating Activities') }}
                </td>
            </tr>
            <tr>
                <td style="padding: 5px 16px;">{{ __('Net Income from Operations (Profit / Loss)') }}</td>
                <td style="text-align: end; padding: 5px 10px;">{{ number_format($operating['net_income'], 2) }}</td>
                <td style="text-align: end; color: #64748b; padding: 5px 10px;">—</td>
            </tr>
            <tr>
                <td style="padding: 5px 16px;">{{ __('(Increase) Decrease in Accounts Receivable and Customers') }}</td>
                <td style="text-align: end; padding: 5px 10px;">{{ number_format($operating['change_ar'], 2) }}</td>
                <td style="text-align: end; color: #64748b; padding: 5px 10px;">—</td>
            </tr>
            <tr>
                <td style="padding: 5px 16px;">{{ __('Increase (Decrease) in Accounts Payable and Suppliers') }}</td>
                <td style="text-align: end; padding: 5px 10px;">{{ number_format($operating['change_ap'], 2) }}</td>
                <td style="text-align: end; color: #64748b; padding: 5px 10px;">—</td>
            </tr>
            <tr style="background-color: #f8fafc; font-weight: 700; border-top: 1px solid #e2e8f0; border-bottom: 1px solid #cbd5e1;">
                <td style="padding: 6px 16px; color: #334155;">{{ __('Net Cash Flow from Operating Activities') }}</td>
                <td style="text-align: end; color: #64748b; padding: 6px 10px;">—</td>
                <td style="text-align: end; padding: 6px 10px; color: #1e293b;">{{ number_format($operating['subtotal'], 2) }}</td>
            </tr>

            {{-- 2. Investing Activities --}}
            <tr style="background-color: #f1f5f9; font-weight: 700; border-top: 1px solid #cbd5e1; border-bottom: 1px solid #cbd5e1;">
                <td colspan="3" style="padding: 6px 10px; color: #1e293b;">
                    {{ __('2. Cash Flows from Investing Activities') }}
                </td>
            </tr>
            <tr>
                <td style="padding: 5px 16px;">{{ __('Change in Non-Current Assets (Fixed Assets and Projects)') }}</td>
                <td style="text-align: end; padding: 5px 10px;">{{ number_format($investing['fixed_assets_change'], 2) }}</td>
                <td style="text-align: end; color: #64748b; padding: 5px 10px;">—</td>
            </tr>
            <tr style="background-color: #f8fafc; font-weight: 700; border-top: 1px solid #e2e8f0; border-bottom: 1px solid #cbd5e1;">
                <td style="padding: 6px 16px; color: #334155;">{{ __('Net Cash Flow from Investing Activities') }}</td>
                <td style="text-align: end; color: #64748b; padding: 6px 10px;">—</td>
                <td style="text-align: end; padding: 6px 10px; color: #1e293b;">{{ number_format($investing['subtotal'], 2) }}</td>
            </tr>

            {{-- 3. Financing Activities --}}
            <tr style="background-color: #f1f5f9; font-weight: 700; border-top: 1px solid #cbd5e1; border-bottom: 1px solid #cbd5e1;">
                <td colspan="3" style="padding: 6px 10px; color: #1e293b;">
                    {{ __('3. Cash Flows from Financing Activities') }}
                </td>
            </tr>
            <tr>
                <td style="padding: 5px 16px;">{{ __('Change in Equity and Long-Term Liabilities') }}</td>
                <td style="text-align: end; padding: 5px 10px;">{{ number_format($financing['equity_loans_change'], 2) }}</td>
                <td style="text-align: end; color: #64748b; padding: 5px 10px;">—</td>
            </tr>
            <tr style="background-color: #f8fafc; font-weight: 700; border-top: 1px solid #e2e8f0; border-bottom: 1px solid #cbd5e1;">
                <td style="padding: 6px 16px; color: #334155;">{{ __('Net Cash Flow from Financing Activities') }}</td>
                <td style="text-align: end; color: #64748b; padding: 6px 10px;">—</td>
                <td style="text-align: end; padding: 6px 10px; color: #1e293b;">{{ number_format($financing['subtotal'], 2) }}</td>
            </tr>

            {{-- Summary Rows --}}
            <tr style="background-color: #e2e8f0; font-weight: 800; border-top: 2px solid #334155; border-bottom: 1px solid #cbd5e1;">
                <td style="padding: 7px 10px; color: #0f172a;">{{ __('Net Change in Cash and Cash Equivalents during the Period') }}</td>
                <td style="text-align: end; color: #64748b; padding: 7px 10px;">—</td>
                <td style="text-align: end; padding: 7px 10px; color: #0f172a;">{{ number_format($summary['net_cash_change'], 2) }}</td>
            </tr>
            <tr>
                <td style="padding: 6px 10px; font-weight: 600;">{{ __('Cash and Cash Equivalents at Beginning of Period') }} ({{ $filters['from_date'] }})</td>
                <td style="text-align: end; color: #64748b; padding: 6px 10px;">—</td>
                <td style="text-align: end; padding: 6px 10px; font-weight: 600;">{{ number_format($summary['beginning_cash'], 2) }}</td>
            </tr>
            <tr style="background-color: #f1f5f9; font-weight: 800; font-size: 11.5px; border-bottom: 3px double #334155;">
                <td style="padding: 8px 10px; color: #0f172a;">{{ __('Cash and Cash Equivalents at End of Period') }} ({{ $filters['to_date'] }})</td>
                <td style="text-align: end; color: #64748b; padding: 8px 10px;">—</td>
                <td style="text-align: end; padding: 8px 10px; color: #0f172a;">{{ number_format($summary['ending_cash'], 2) }}</td>
            </tr>
        </tbody>
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
