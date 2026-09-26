@extends('settings::layouts.printable')

@section('title', __('Opening Balance Sheet'))
@section('report_title', __('Opening Balance Statement') . ' - ' . ($entity->translate(app()->getLocale())?->name ?? $entity->code))

@section('report_meta')
    <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 14px; font-size: 11px; margin-top: 4px;">
        <span><strong>{{ __('Fiscal Year:') }}</strong> {{ $fiscalYear->translate(app()->getLocale())?->name ?? $fiscalYear->name }} ({{ $fiscalYear->start_date->format('Y-m-d') }})</span>
        <span>•</span>
        <span><strong>{{ __('Entry Number:') }}</strong> {{ $openingEntry?->entry_number ?? __('Draft') }}</span>
        <span>•</span>
        <span><strong>{{ __('Status:') }}</strong> {{ $totals['is_posted'] ? __('Posted & Approved') : __('Draft') }}</span>
        <span>•</span>
        <span><strong>{{ __('Currency:') }}</strong> {{ $entity->currency ?? 'SAR' }}</span>
        <span>•</span>
        <span><strong>{{ __('Printed At:') }}</strong> {{ now()->format('Y-m-d H:i') }}</span>
    </div>
@endsection

@section('content')
    <table class="report-table" style="width: 100%; border-collapse: collapse; font-size: 11px; margin-top: 10px;">
        <thead>
            <tr style="background-color: #f8fafc; border-bottom: 2px solid #cbd5e1;">
                <th style="width: 60px; text-align: center; padding: 8px;">#</th>
                <th style="width: 120px; padding: 8px;">{{ __('Account Code') }}</th>
                <th style="padding: 8px;">{{ __('Account Name') }}</th>
                <th style="width: 110px; text-align: center; padding: 8px;">{{ __('Account Type') }}</th>
                <th style="width: 120px; text-align: end; padding: 8px;">{{ __('Debit Amount') }}</th>
                <th style="width: 120px; text-align: end; padding: 8px;">{{ __('Credit Amount') }}</th>
                <th style="width: 160px; padding: 8px;">{{ __('Notes') }}</th>
            </tr>
        </thead>
        <tbody>
            @php
                $index = 1;
                $totalDebit = 0;
                $totalCredit = 0;
            @endphp
            @forelse($accounts as $acc)
                @php
                    $line = $linesByAccount[$acc->id] ?? null;
                    $debit = (float) ($line['debit'] ?? 0);
                    $credit = (float) ($line['credit'] ?? 0);
                    $notes = $line['notes'] ?? '';

                    if ($debit == 0 && $credit == 0) continue;

                    $totalDebit += $debit;
                    $totalCredit += $credit;
                @endphp
                <tr style="border-bottom: 1px solid #e2e8f0;">
                    <td style="text-align: center; padding: 6px;">{{ $index++ }}</td>
                    <td style="font-family: monospace; font-weight: bold; padding: 6px;">{{ $acc->code }}</td>
                    <td style="padding: 6px;">{{ $acc->translate(app()->getLocale())?->name ?? $acc->name }}</td>
                    <td style="text-align: center; padding: 6px; font-size: 10px; color: #64748b;">{{ $acc->account_type?->label() }}</td>
                    <td style="text-align: end; font-family: monospace; font-weight: bold; padding: 6px;">
                        {{ $debit > 0 ? number_format($debit, 4) : '-' }}
                    </td>
                    <td style="text-align: end; font-family: monospace; font-weight: bold; padding: 6px;">
                        {{ $credit > 0 ? number_format($credit, 4) : '-' }}
                    </td>
                    <td style="padding: 6px; color: #64748b; font-size: 10px;">{{ $notes }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px; color: #94a3b8;">
                        {{ __('No opening balance lines recorded.') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #f1f5f9; font-weight: bold; border-top: 2px solid #94a3b8; border-bottom: 2px solid #94a3b8;">
                <td colspan="4" style="padding: 8px; text-align: end;">{{ __('Total Opening Balances:') }}</td>
                <td style="text-align: end; font-family: monospace; font-size: 12px; color: #1e40af; padding: 8px;">
                    {{ number_format($totalDebit, 4) }}
                </td>
                <td style="text-align: end; font-family: monospace; font-size: 12px; color: #0284c7; padding: 8px;">
                    {{ number_format($totalCredit, 4) }}
                </td>
                <td style="padding: 8px; text-align: center;">
                    @if(abs($totalDebit - $totalCredit) < 0.0001 && $totalDebit > 0)
                        <span style="color: #16a34a;">✓ {{ __('Balanced') }}</span>
                    @else
                        <span style="color: #dc2626;">✗ {{ __('Unbalanced') }}</span>
                    @endif
                </td>
            </tr>
        </tfoot>
    </table>
@endsection
