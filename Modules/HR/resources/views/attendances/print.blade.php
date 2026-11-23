@extends('settings::layouts.printable')

@php
    $pageTitle = __('كشف الحضور والإنصراف الشهري') . ' - ' . ($matrixData['month_name'] ?? '') . ' ' . ($matrixData['year'] ?? '');
@endphp

@section('title', $pageTitle)
@section('report_title', $pageTitle)

@section('report_meta')
    <div style="display: flex; flex-wrap: wrap; justify-content: center; align-items: center; gap: 14px; font-size: 11px; margin-top: 4px; color: #475569;">
        <span><strong>{{ __('الفترة الشهرية:') }}</strong> {{ $matrixData['month_name'] ?? '' }} {{ $matrixData['year'] ?? '' }}</span>
        <span>•</span>
        <span><strong>{{ __('عدد الموظفين:') }}</strong> {{ count($matrixData['matrix_rows'] ?? []) }}</span>
        @if(!empty($branchModel))
            <span>•</span>
            <span><strong>{{ __('الفرع:') }}</strong> {{ $branchModel->name }}</span>
        @endif
        @if(!empty($departmentModel))
            <span>•</span>
            <span><strong>{{ __('القسم / الإدارة:') }}</strong> {{ $departmentModel->name }}</span>
        @endif
        @if(!empty($employeeModel))
            <span>•</span>
            <span><strong>{{ __('الموظف:') }}</strong> {{ $employeeModel->name }} ({{ $employeeModel->employee_code }})</span>
        @endif
    </div>
@endsection

@section('content')
<style>
    /* Print Attendance Matrix Custom Styles */
    .print-attendance-wrapper {
        width: 100%;
        margin-top: 10px;
    }

    /* Legend Box in Print */
    .print-legend-box {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 6px 12px;
        margin-bottom: 12px;
        font-size: 10px;
    }
    .print-legend-items {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: center;
    }
    .print-legend-item {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-weight: 600;
        color: #334155;
    }

    /* Print Matrix Table */
    table.print-matrix-table {
        width: 100% !important;
        border-collapse: collapse !important;
        table-layout: fixed !important;
        font-size: 9px !important;
        background-color: #ffffff !important;
    }
    table.print-matrix-table th,
    table.print-matrix-table td {
        border: 1px solid #94a3b8 !important;
        text-align: center !important;
        vertical-align: middle !important;
        padding: 3px 1px !important;
        box-sizing: border-box !important;
    }
    table.print-matrix-table thead tr:first-child th {
        background-color: #0b284e !important;
        color: #ffffff !important;
        font-weight: 700 !important;
        font-size: 9.5px !important;
        height: 22px !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    table.print-matrix-table thead tr:nth-child(2) th {
        background-color: #f1f5f9 !important;
        color: #1e293b !important;
        font-weight: 600 !important;
        font-size: 8px !important;
        height: 18px !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    table.print-matrix-table th.col-emp-name,
    table.print-matrix-table td.col-emp-name {
        width: 140px !important;
        max-width: 140px !important;
        text-align: right !important;
        padding: 4px 6px !important;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }
    table.print-matrix-table th.col-day-cell,
    table.print-matrix-table td.col-day-cell {
        width: auto !important;
        min-width: 16px !important;
        height: 24px !important;
    }

    /* Symbols & Colors in Print */
    .print-sym-present {
        color: #15803d !important;
        font-weight: 900 !important;
        font-size: 11px !important;
    }
    .print-sym-absent {
        color: #b91c1c !important;
        font-weight: 900 !important;
        font-size: 11px !important;
    }
    .print-sym-rest {
        color: #64748b !important;
        font-size: 8px !important;
        line-height: 1 !important;
    }
    .print-sym-holiday {
        color: #b45309 !important;
        font-size: 9px !important;
    }
    .print-sym-leave {
        color: #dc2626 !important;
        font-size: 9px !important;
    }
    .print-sym-half {
        color: #c2410c !important;
        font-weight: 800 !important;
        font-size: 9px !important;
    }
    .print-sym-late {
        color: #ea580c !important;
        font-size: 9px !important;
    }
    .print-sym-future {
        color: #cbd5e1 !important;
        font-size: 9px !important;
    }

    /* Weekend / Rest day column background */
    .bg-print-weekend {
        background-color: #f8fafc !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    .emp-print-title {
        font-weight: 700;
        color: #0f172a;
        font-size: 9.5px;
        line-height: 1.2;
    }
    .emp-print-sub {
        font-size: 7.5px;
        color: #64748b;
        margin-top: 1px;
    }

    @media print {
        @page {
            size: A4 landscape;
            margin: 8mm 6mm;
        }
        body {
            background: #fff !important;
        }
        .page-sheet {
            width: 100% !important;
            min-height: auto !important;
            padding: 0 !important;
            margin: 0 !important;
            box-shadow: none !important;
        }
        table.print-matrix-table {
            page-break-inside: auto;
        }
        table.print-matrix-table tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        table.print-matrix-table thead {
            display: table-header-group;
        }
    }
</style>

<div class="print-attendance-wrapper">
    <!-- Legend Box -->
    <div class="print-legend-box">
        <span style="font-weight: 700; color: #0b284e;">{{ __('دليل رموز الحضور:') }}</span>
        <div class="print-legend-items">
            <span class="print-legend-item"><span class="print-sym-present">✓</span> {{ __('حاضر') }}</span>
            <span class="print-legend-item"><span class="print-sym-absent">✕</span> {{ __('غائب') }}</span>
            <span class="print-legend-item"><span class="print-sym-half">½</span> {{ __('نصف يوم') }}</span>
            <span class="print-legend-item"><span class="print-sym-leave">🚩</span> {{ __('إجازة') }}</span>
            <span class="print-legend-item"><span class="print-sym-holiday">⭐</span> {{ __('عطلة') }}</span>
            <span class="print-legend-item"><span class="print-sym-rest">⃠ عطلة</span></span>
            <span class="print-legend-item"><span class="print-sym-late">🕒</span> {{ __('متأخر') }}</span>
            <span class="print-legend-item"><span class="print-sym-future">-</span> {{ __('المستقبل') }}</span>
        </div>
    </div>

    <!-- Attendance Matrix Table -->
    <table class="print-matrix-table">
        <thead>
            <!-- Row 1: Day numbers -->
            <tr>
                <th class="col-emp-name">{{ __('الموظف') }}</th>
                @foreach ($matrixData['days'] as $d)
                    <th class="col-day-cell">{{ $d['day'] }}</th>
                @endforeach
            </tr>
            <!-- Row 2: Day names -->
            <tr>
                <th class="col-emp-name" style="font-size: 7.5px !important; color: #475569 !important;">
                    {{ __('الكود / القسم') }}
                </th>
                @foreach ($matrixData['days'] as $d)
                    <th class="col-day-cell">
                        {{ mb_substr(app()->getLocale() === 'ar' ? $d['day_name_ar'] : $d['day_name_en'], 0, 3) }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse ($matrixData['matrix_rows'] as $row)
                <tr>
                    <td class="col-emp-name">
                        <div class="emp-print-title" title="{{ $row['employee_name'] }}">
                            {{ $row['employee_name'] }}
                        </div>
                        <div class="emp-print-sub">
                            {{ $row['employee_code'] }} @if(!empty($row['department'])) • {{ $row['department'] }} @endif
                        </div>
                    </td>

                    @foreach ($matrixData['days'] as $d)
                        @php
                            $cell = $row['days'][$d['day']] ?? null;
                            $status = $cell['status'] ?? 'future';
                        @endphp
                        <td class="col-day-cell {{ $status === 'rest_day' ? 'bg-print-weekend' : '' }}">
                            @if ($status === 'present')
                                <span class="print-sym-present">✓</span>
                            @elseif ($status === 'absent')
                                <span class="print-sym-absent">✕</span>
                            @elseif ($status === 'rest_day')
                                <span class="print-sym-rest">⃠</span>
                            @elseif ($status === 'holiday')
                                <span class="print-sym-holiday">⭐</span>
                            @elseif ($status === 'late')
                                <span class="print-sym-late">🕒</span>
                            @elseif ($status === 'half_day')
                                <span class="print-sym-half">½</span>
                            @elseif ($status === 'on_leave')
                                <span class="print-sym-leave">🚩</span>
                            @elseif ($status === 'future')
                                <span class="print-sym-future">-</span>
                            @else
                                <span class="print-sym-future">-</span>
                            @endif
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($matrixData['days']) + 1 }}" style="padding: 20px; text-align: center; color: #64748b;">
                        {{ __('لا توجد سجلات مطابقة للفلاتر المحددة.') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
