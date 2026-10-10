<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Incident Report') }} #{{ $report->id }} - {{ $report->student?->name ?? '' }}</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Cairo', 'Inter', sans-serif;
            background-color: #f8f9fa;
            color: #2c3e50;
            font-size: 13px;
            line-height: 1.5;
            padding: 20px;
        }
        .print-container {
            max-width: 850px;
            margin: 0 auto;
            background: #fff;
            padding: 35px 45px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
            border: 1px solid #e2e8f0;
        }
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }
        .logo-box img {
            max-height: 70px;
            max-width: 150px;
            object-fit: contain;
        }
        .header-title {
            text-align: center;
            flex-grow: 1;
        }
        .header-title h2 {
            font-size: 20px;
            font-weight: 700;
            color: #dc2626;
            margin-bottom: 4px;
        }
        .header-title p {
            font-size: 12px;
            color: #64748b;
        }
        .meta-info {
            text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};
            font-size: 11px;
            color: #64748b;
        }
        .meta-info span {
            display: block;
            margin-bottom: 2px;
        }
        .section-title {
            font-size: 14px;
            font-weight: 700;
            color: #1e293b;
            background-color: #f1f5f9;
            padding: 6px 12px;
            border-radius: 4px;
            border-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}: 4px solid #dc2626;
            margin: 20px 0 12px 0;
        }
        table.info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        table.info-table th, table.info-table td {
            border: 1px solid #cbd5e1;
            padding: 8px 12px;
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
            font-size: 12px;
        }
        table.info-table th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 600;
            width: 22%;
        }
        table.info-table td {
            color: #0f172a;
        }
        .content-box {
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 10px 14px;
            background: #fafafa;
            min-height: 50px;
            font-size: 12px;
            color: #1e293b;
            white-space: pre-line;
            line-height: 1.6;
            margin-bottom: 12px;
        }
        .nurse-box {
            background-color: #f0fdf4;
            border-color: #86efac;
            color: #166534;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            background-color: #fee2e2;
            color: #991b1b;
        }
        .witness-tag {
            display: inline-block;
            background: #e2e8f0;
            padding: 3px 9px;
            border-radius: 12px;
            font-size: 11px;
            margin: 2px 4px 2px 0;
            color: #334155;
        }
        .signatures-section {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px dashed #cbd5e1;
        }
        .signature-box {
            width: 30%;
            text-align: center;
        }
        .signature-title {
            font-weight: 600;
            font-size: 12px;
            margin-bottom: 45px;
            color: #334155;
        }
        .signature-line {
            border-top: 1px solid #94a3b8;
            margin: 0 15px;
            padding-top: 5px;
            font-size: 11px;
            color: #64748b;
        }
        .actions-toolbar {
            max-width: 850px;
            margin: 0 auto 15px auto;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            border: 1px solid transparent;
            text-decoration: none;
        }
        .btn-primary {
            background: #dc2626;
            color: #fff;
        }
        .btn-secondary {
            background: #fff;
            color: #475569;
            border-color: #cbd5e1;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            .actions-toolbar {
                display: none !important;
            }
            .print-container {
                box-shadow: none;
                border: none;
                padding: 0;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

    <!-- Action Buttons (Hidden during print) -->
    <div class="actions-toolbar">
        <button type="button" class="btn btn-primary" onclick="window.print()">
            {{ __('Print Document') }}
        </button>
        <button type="button" class="btn btn-secondary" onclick="window.close()">
            {{ __('Close') }}
        </button>
    </div>

    <div class="print-container">
        <!-- Header -->
        @php
            $inst = $report->institution ?? $report->student?->institution;
            $branch = $report->branch ?? $report->student?->branch;
            $student = $report->student;
            $sName = $student ? ($student->translate(app()->getLocale())?->name ?? $student->translate('ar')?->name ?? $student->name) : '-';
        @endphp
        <div class="header-section">
            <div class="logo-box">
                @if ($inst && $inst->logo)
                    <img src="{{ asset('storage/' . $inst->logo) }}" alt="Logo">
                @else
                    <h3 style="color:#dc2626; font-size:18px;">{{ $inst?->name ?? config('app.name') }}</h3>
                @endif
            </div>
            <div class="header-title">
                <h2>{{ __('Student Incident & Injury Report') }}</h2>
                <p>STUDENT INCIDENT & INJURY REPORT</p>
                <small style="color: #64748b;">{{ $inst?->name }} - {{ $branch?->name }}</small>
            </div>
            <div class="meta-info">
                <span><strong>{{ __('Report No:') }}</strong> #{{ str_pad($report->id, 5, '0', STR_PAD_LEFT) }}</span>
                <span><strong>{{ __('Print Date:') }}</strong> {{ date('Y-m-d H:i') }}</span>
                <span><strong>{{ __('Status:') }}</strong> <span class="badge">{{ $report->incident_type_name }}</span></span>
            </div>
        </div>

        <!-- 1. Student Information -->
        <div class="section-title">{{ __('1. Student Information') }}</div>
        <table class="info-table">
            <tr>
                <th>{{ __('Student Name:') }}</th>
                <td style="font-weight:700;">{{ $sName }}</td>
                <th>{{ __('Student Code / Civil ID:') }}</th>
                <td>{{ $student?->student_code ?? '-' }} / {{ $student?->civil_id ?? '-' }}</td>
            </tr>
            <tr>
                <th>{{ __('Class / Stage:') }}</th>
                <td>
                    {{ $student?->class?->translate(app()->getLocale())?->name ?? $student?->class?->name ?? '-' }}
                    ({{ $student?->stage?->translate(app()->getLocale())?->name ?? $student?->stage?->name ?? '-' }})
                </td>
                <th>{{ __('Branch:') }}</th>
                <td>{{ $branch?->name ?? '-' }}</td>
            </tr>
            <tr>
                <th>{{ __('Guardian:') }}</th>
                <td>{{ $student?->guardian?->translate(app()->getLocale())?->name ?? $student?->guardian?->name ?? '-' }}</td>
                <th>{{ __('Emergency Phone:') }}</th>
                <td style="direction:ltr; text-align:{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}; font-weight:700;">
                    {{ $student?->guardian?->phone ?? '-' }}
                </td>
            </tr>
        </table>

        <!-- 2. Incident Facts & Setting -->
        <div class="section-title">{{ __('2. Incident Details & Timing') }}</div>
        <table class="info-table">
            <tr>
                <th>{{ __('Incident Date:') }}</th>
                <td style="font-weight:700;">{{ $report->date ? $report->date->format('Y-m-d') : '-' }}</td>
                <th>{{ __('Incident Time:') }}</th>
                <td style="font-weight:700;">{{ $report->time ?: '-' }}</td>
            </tr>
            <tr>
                <th>{{ __('Incident Location:') }}</th>
                <td>{{ $report->location ?: '-' }}</td>
                <th>{{ __('Incident / Injury Type:') }}</th>
                <td><span class="badge">{{ $report->incident_type_name }}</span></td>
            </tr>
            <tr>
                <th>{{ __('Responsible / Registered By:') }}</th>
                <td>{{ $report->user?->name ?? '-' }}</td>
                <th>{{ __('Witnesses / Present Staff:') }}</th>
                <td>
                    @php $witnesses = $report->witness_users; @endphp
                    @if ($witnesses->isNotEmpty())
                        @foreach ($witnesses as $w)
                            <span class="witness-tag">{{ $w->name }}</span>
                        @endforeach
                    @else
                        -
                    @endif
                </td>
            </tr>
        </table>

        <!-- 3. Student Statement & Description -->
        <div class="section-title">{{ __('3. Student Narrative & Description') }}</div>
        <div class="content-box">
            {{ $report->student_description ?: __('No description recorded.') }}
        </div>

        <!-- 4. Procedures & Immediate Actions Taken -->
        <div class="section-title">{{ __('4. Procedures & Immediate Actions Taken') }}</div>
        <div class="content-box">
            {{ $report->procedures ?: __('No procedures recorded.') }}
        </div>

        <!-- 5. Nurse & Medical Assessment -->
        <div class="section-title">{{ __('5. Nurse Report & Medical Assessment') }}</div>
        <div class="content-box nurse-box">
            {{ $report->nurse_report ?: __('No nurse report recorded.') }}
        </div>

        <!-- Signatures -->
        <div class="signatures-section">
            <div class="signature-box">
                <div class="signature-title">{{ __('Responsible / Nurse') }}</div>
                <div class="signature-line">{{ $report->user?->name ?? __('Name & Signature') }}</div>
            </div>
            <div class="signature-box">
                <div class="signature-title">{{ __('Branch Manager / Administration') }}</div>
                <div class="signature-line">{{ __('Approval & Signature') }}</div>
            </div>
            <div class="signature-box">
                <div class="signature-title">{{ __('Guardian Acknowledgment') }}</div>
                <div class="signature-line">{{ __('Signature & Date') }}</div>
            </div>
        </div>
    </div>

</body>
</html>
