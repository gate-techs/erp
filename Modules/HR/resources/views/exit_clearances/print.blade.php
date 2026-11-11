<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('Exit Clearance Form') }} - {{ $clearance->clearance_code }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            margin: 20px;
            font-size: 13px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }
        .header p {
            margin: 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 8px 10px;
            text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }};
        }
        th {
            background-color: #f7f7f7;
        }
        .section-title {
            font-weight: bold;
            font-size: 14px;
            margin: 15px 0 8px 0;
            border-bottom: 1px solid #ddd;
            padding-bottom: 4px;
        }
        .signatures {
            margin-top: 30px;
        }
        .signatures td {
            height: 60px;
            vertical-align: bottom;
        }
        @media print {
            .no-print { display: none; }
            body { margin: 0; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 16px; font-weight: bold; cursor: pointer;">
            🖨️ {{ __('Print Form') }}
        </button>
    </div>

    <div class="header">
        <h2>{{ __('Employee Exit Clearance Form') }}</h2>
        <p>{{ __('Form Reference') }}: <strong>{{ $clearance->clearance_code }}</strong> | {{ __('Date') }}: {{ date('Y-m-d') }}</p>
    </div>

    <!-- Employee Details -->
    <div class="section-title">{{ __('1. Employee Information') }}</div>
    <table>
        <tr>
            <th width="20%">{{ __('Employee Name') }}</th>
            <td width="30%"><strong>{{ $clearance->employee?->name }}</strong></td>
            <th width="20%">{{ __('Employee Code') }}</th>
            <td width="30%">{{ $clearance->employee?->employee_code }}</td>
        </tr>
        <tr>
            <th>{{ __('Department') }}</th>
            <td>{{ $clearance->department?->name }}</td>
            <th>{{ __('Job Title') }}</th>
            <td>{{ $clearance->jobTitle?->name }}</td>
        </tr>
        <tr>
            <th>{{ __('Hire Date') }}</th>
            <td>{{ $clearance->employee?->hire_date?->format('Y-m-d') ?? '-' }}</td>
            <th>{{ __('Last Working Day') }}</th>
            <td><strong>{{ $clearance->last_working_day?->format('Y-m-d') }}</strong></td>
        </tr>
        <tr>
            <th>{{ __('Leaving Reason') }}</th>
            @php
                $reasonsMap = [
                    'resignation'     => __('Resignation'),
                    'contract_expiry' => __('Contract Expiry'),
                    'termination'     => __('Employer Termination'),
                    'retirement'      => __('Retirement'),
                    'other'           => __('Other'),
                ];
                $reasonLabel = $reasonsMap[$clearance->leaving_reason] ?? __($clearance->leaving_reason);
            @endphp
            <td colspan="3">{{ $reasonLabel }} ({{ $clearance->reason_details ?? '-' }})</td>
        </tr>
    </table>

    <!-- Financial Settlement -->
    <div class="section-title">{{ __('2. Financial Settlements & End of Service') }}</div>
    <table>
        <tr>
            <th width="30%">{{ __('End of Service Pay') }}</th>
            <td width="20%">{{ number_format($clearance->end_of_service_amount, 2) }}</td>
            <th width="30%">{{ __('Outstanding Loans & Deductions') }}</th>
            <td width="20%" style="color: red;">-{{ number_format($clearance->outstanding_loans_amount, 2) }}</td>
        </tr>
        <tr>
            <th>{{ __('Leave Balance (Days)') }}</th>
            <td>{{ $clearance->leave_balance_days }} {{ __('Days') }}</td>
            <th>{{ __('Net Final Settlement') }}</th>
            <td><strong>{{ number_format($clearance->net_settlement_amount, 2) }}</strong></td>
        </tr>
    </table>

    <!-- Signatures -->
    <div class="section-title">{{ __('3. Final Approvals & Acknowledgement') }}</div>
    <table class="signatures">
        <tr>
            <td width="33%">
                <strong>{{ __('Employee Signature') }}:</strong><br><br>
                _______________________
            </td>
            <td width="33%">
                <strong>{{ __('HR Department') }}:</strong><br><br>
                _______________________
            </td>
            <td width="33%">
                <strong>{{ __('Managing Director / CEO') }}:</strong><br><br>
                _______________________
            </td>
        </tr>
    </table>
</body>
</html>

