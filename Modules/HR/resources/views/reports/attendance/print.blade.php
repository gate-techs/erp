@extends('settings::layouts.printable')

@php
    $reportTitle = match($reportType ?? 'daily') {
        'monthly_summary' => __('كشف ملخص الحضور الشهري ومسير الدوام (Timesheet)'),
        'lateness'        => __('كشف تقرير التأخير والانصراف المبكر'),
        'absenteeism'     => __('كشف تقرير الغياب بدون إذن والإجراءات الإدارية'),
        default           => __('كشف تقرير الحضور والانصراف والدوام التفصيلي'),
    };
@endphp

@section('title', $reportTitle)
@section('report_title', $reportTitle)

@section('report_meta')
    <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 14px; font-size: 11px; margin-top: 4px;">
        <span><strong>{{ __('الفترة:') }}</strong> {{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }} {{ __('إلى') }} {{ request('end_date', now()->format('Y-m-d')) }}</span>
        <span>•</span>
        <span><strong>{{ __('إجمالي السجلات:') }}</strong> {{ $kpis['total_records'] }}</span>
        <span>•</span>
        <span><strong>{{ __('نسبة الحضور:') }}</strong> {{ $kpis['attendance_rate'] }}% ({{ $kpis['present_count'] }} موظف)</span>
        @if($kpis['total_late_minutes'] > 0)
            <span>•</span>
            <span style="color: #b45309;"><strong>{{ __('إجمالي التأخير:') }}</strong> {{ $kpis['total_late_minutes'] }} دقيقة</span>
        @endif
        @if($kpis['total_overtime_minutes'] > 0)
            <span>•</span>
            <span style="color: #0369a1;"><strong>{{ __('إجمالي الإضافي:') }}</strong> {{ $kpis['total_overtime_minutes'] }} دقيقة</span>
        @endif
    </div>
@endsection

@section('content')
<table class="report-table">
    <thead>
        <tr>
            @foreach($availableColumns as $colKey => $colDef)
                @if(!empty($selectedColumns[$colKey]))
                    <th class="{{ $colDef['class'] }}" style="width: {{ $colDef['width'] }};">{{ $colDef['label'] }}</th>
                @endif
            @endforeach
        </tr>
    </thead>
    <tbody>
        @if($reportType === 'monthly_summary')
            {{-- Monthly Summary (Timesheet) Rows --}}
            @forelse($attendances as $index => $row)
                <tr>
                    @if(!empty($selectedColumns['index']))
                        <td style="text-align: center; color: #64748b;">{{ $index + 1 }}</td>
                    @endif
                    @if(!empty($selectedColumns['code']))
                        <td style="font-weight: 700;">{{ $row->employee->employee_code ?? '-' }}</td>
                    @endif
                    @if(!empty($selectedColumns['employee']))
                        <td><strong>{{ $row->employee->name ?? '-' }}</strong></td>
                    @endif
                    @if(!empty($selectedColumns['department']))
                        <td>{{ $row->employee->department->name ?? '-' }}</td>
                    @endif
                    @if(!empty($selectedColumns['branch']))
                        <td>{{ $row->employee->primaryBranch->name ?? '-' }}</td>
                    @endif
                    @if(!empty($selectedColumns['scheduled_days']))
                        <td style="text-align: center; font-weight: bold;">{{ $row->scheduled_days }}</td>
                    @endif
                    @if(!empty($selectedColumns['present_days']))
                        <td style="text-align: center; color: #166534; font-weight: bold;">{{ $row->present_days }}</td>
                    @endif
                    @if(!empty($selectedColumns['absent_days']))
                        <td style="text-align: center; color: #991b1b; font-weight: bold;">{{ $row->absent_days }}</td>
                    @endif
                    @if(!empty($selectedColumns['leave_days']))
                        <td style="text-align: center; color: #1e40af;">{{ $row->leave_days }}</td>
                    @endif
                    @if(!empty($selectedColumns['rest_days']))
                        <td style="text-align: center; color: #64748b;">{{ $row->rest_days }}</td>
                    @endif
                    @if(!empty($selectedColumns['late_minutes']))
                        <td style="text-align: center; {{ $row->late_minutes > 0 ? 'color: #b45309; font-weight: bold;' : '' }}">
                            {{ $row->late_minutes > 0 ? $row->late_minutes . ' د' : '-' }}
                        </td>
                    @endif
                    @if(!empty($selectedColumns['early_leave']))
                        <td style="text-align: center; {{ $row->early_leave_minutes > 0 ? 'color: #b91c1c; font-weight: bold;' : '' }}">
                            {{ $row->early_leave_minutes > 0 ? $row->early_leave_minutes . ' د' : '-' }}
                        </td>
                    @endif
                    @if(!empty($selectedColumns['overtime']))
                        <td style="text-align: center; {{ $row->overtime_minutes > 0 ? 'color: #0369a1; font-weight: bold;' : '' }}">
                            {{ $row->overtime_minutes > 0 ? '+' . $row->overtime_minutes . ' د' : '-' }}
                        </td>
                    @endif
                    @if(!empty($selectedColumns['total_working_hours']))
                        <td style="text-align: center; font-weight: bold;">{{ $row->total_working_hours }} س</td>
                    @endif
                    @if(!empty($selectedColumns['attendance_rate']))
                        <td style="text-align: center; font-weight: bold;">
                            {{ $row->attendance_rate }}%
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="20" style="text-align: center; padding: 25px; color: #64748b;">
                        {{ __('لا توجد سجلات حضور تطابق معايير الفلترة المحددة.') }}
                    </td>
                </tr>
            @endforelse
        @else
            {{-- Standard / Lateness / Absenteeism Rows --}}
            @forelse($attendances as $index => $row)
                <tr>
                    @if(!empty($selectedColumns['index']))
                        <td style="text-align: center; color: #64748b;">{{ $index + 1 }}</td>
                    @endif

                    @if(!empty($selectedColumns['code']))
                        <td style="font-weight: 700;">{{ $row->employee->employee_code ?? '-' }}</td>
                    @endif

                    @if(!empty($selectedColumns['employee']))
                        <td><strong>{{ $row->employee->name ?? '-' }}</strong></td>
                    @endif

                    @if(!empty($selectedColumns['department']))
                        <td>{{ $row->employee->department->name ?? '-' }}</td>
                    @endif

                    @if(!empty($selectedColumns['branch']))
                        <td>{{ $row->employee->primaryBranch->name ?? '-' }}</td>
                    @endif

                    @if(!empty($selectedColumns['date']))
                        <td style="text-align: center;">{{ $row->date ? $row->date->format('Y-m-d') : '-' }}</td>
                    @endif

                    @if(!empty($selectedColumns['day']))
                        <td style="text-align: center; color: #64748b;">{{ $row->date ? $row->date->translatedFormat('l') : '-' }}</td>
                    @endif

                    @if(!empty($selectedColumns['shift']))
                        <td>{{ $row->shift->name ?? '-' }}</td>
                    @endif

                    @if(!empty($selectedColumns['check_in']))
                        <td style="text-align: center; font-weight: 600;">
                            {{ $row->check_in ? \Carbon\Carbon::parse($row->check_in)->format('H:i') : '-' }}
                        </td>
                    @endif

                    @if(!empty($selectedColumns['late_minutes']))
                        <td style="text-align: center; {{ $row->late_minutes > 0 ? 'color: #b45309; font-weight: bold;' : '' }}">
                            {{ $row->late_minutes > 0 ? $row->late_minutes : '-' }}
                        </td>
                    @endif

                    @if(!empty($selectedColumns['check_out']))
                        <td style="text-align: center; font-weight: 600;">
                            {{ $row->check_out ? \Carbon\Carbon::parse($row->check_out)->format('H:i') : '-' }}
                        </td>
                    @endif

                    @if(!empty($selectedColumns['early_leave']))
                        <td style="text-align: center; {{ $row->early_leave_minutes > 0 ? 'color: #b91c1c; font-weight: bold;' : '' }}">
                            {{ $row->early_leave_minutes > 0 ? $row->early_leave_minutes : '-' }}
                        </td>
                    @endif

                    @if(!empty($selectedColumns['work_hours']))
                        <td style="text-align: center;">
                            {{ $row->total_working_hours ?: '0.00' }}
                        </td>
                    @endif

                    @if(!empty($selectedColumns['overtime']))
                        <td style="text-align: center; {{ $row->overtime_minutes > 0 ? 'color: #0369a1; font-weight: bold;' : '' }}">
                            {{ $row->overtime_minutes > 0 ? '+' . $row->overtime_minutes : '-' }}
                        </td>
                    @endif

                    @if(!empty($selectedColumns['status']))
                        <td style="text-align: center;">
                            @if($reportType === 'absenteeism')
                                <span class="badge-print badge-print-danger">{{ __('غياب بدون إذن') }}</span>
                            @elseif($row->status)
                                @php
                                    $badgeClass = match($row->status->value) {
                                        'present'   => 'badge-print-success',
                                        'absent'    => 'badge-print-danger',
                                        'late'      => 'badge-print-warning',
                                        'half_day', 'on_leave' => 'badge-print-info',
                                        default     => 'badge-print-secondary',
                                    };
                                @endphp
                                <span class="badge-print {{ $badgeClass }}">{{ $row->status->label() }}</span>
                            @else
                                -
                            @endif
                        </td>
                    @endif

                    @if(!empty($selectedColumns['reason']))
                        <td style="color: #b91c1c; font-size: 10px;">
                            {{ $row->adjustment_reason ?: __('غياب غير مبرر') }}
                        </td>
                    @endif

                    @if(!empty($selectedColumns['source']))
                        <td style="text-align: center; font-size: 10px;">
                            {{ $row->is_manual_adjusted ? __('يدوي') : __('بصمة') }}
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="20" style="text-align: center; padding: 25px; color: #64748b;">
                        {{ __('لا توجد سجلات حضور تطابق معايير الفلترة المحددة.') }}
                    </td>
                </tr>
            @endforelse
        @endif
    </tbody>
</table>

<!-- Summary Box under the table on print -->
<div style="margin-top: 14px; padding: 8px 12px; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 4px; display: flex; justify-content: space-between; font-size: 10.5px;">
    <span><strong>{{ __('إجمالي الموظفين المدرجين في الكشف:') }}</strong> 
        @if($reportType === 'monthly_summary')
            {{ $attendances->count() }} {{ __('موظف') }}
        @else
            {{ $attendances->pluck('employee_id')->unique()->count() }} {{ __('موظف') }}
        @endif
    </span>
    <span><strong>{{ __('إجمالي ساعات العمل:') }}</strong> {{ $kpis['total_working_hours'] }} {{ __('ساعة') }}</span>
    <span><strong>{{ __('حالات الغياب:') }}</strong> {{ $kpis['absent_count'] }}</span>
    <span><strong>{{ __('حالات التأخير:') }}</strong> {{ $kpis['late_count'] }}</span>
</div>
@endsection
