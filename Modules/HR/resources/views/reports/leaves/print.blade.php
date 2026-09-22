@extends('settings::layouts.printable')

@php
    $reportTitle = match($reportType ?? 'balances') {
        'leaves_log'   => __('كشف وسجل طلبات الإجازات التفصيلي'),
        'permissions'  => __('كشف تقرير طلبات وساعات الاستئذانات والأذونات'),
        default        => __('كشف تقرير أرصدة الإجازات المستحقة والمستهلكة والمتبقية'),
    };
@endphp

@section('title', $reportTitle)
@section('report_title', $reportTitle)

@section('report_meta')
    <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 14px; font-size: 11px; margin-top: 4px;">
        @if($reportType !== 'balances')
            <span><strong>{{ __('الفترة:') }}</strong> {{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }} {{ __('إلى') }} {{ request('end_date', now()->format('Y-m-d')) }}</span>
            <span>•</span>
        @endif
        <span><strong>{{ __('إجمالي السجلات:') }}</strong> {{ $records->count() }}</span>
        <span>•</span>
        <span><strong>{{ __('تاريخ الاستخراج:') }}</strong> {{ now()->format('Y-m-d H:i') }}</span>
    </div>
@endsection

@section('content')
<table class="report-table">
    <thead>
        <tr>
            @foreach($availableColumns as $colKey => $colDef)
                @if(!empty($selectedColumns[$colKey]))
                    <th class="{{ $colDef['class'] ?? '' }}" style="width: {{ $colDef['width'] ?? 'auto' }};">{{ $colDef['label'] }}</th>
                @endif
            @endforeach
        </tr>
    </thead>
    <tbody>
        @if($reportType === 'leaves_log')
            {{-- Leaves Requests Log Rows --}}
            @forelse($records as $index => $row)
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
                    @if(!empty($selectedColumns['leave_type']))
                        <td style="text-align: center;">
                            {{ $row->leave_type?->label() ?? '-' }}
                        </td>
                    @endif
                    @if(!empty($selectedColumns['start_date']))
                        <td style="text-align: center;">{{ $row->start_date ? \Carbon\Carbon::parse($row->start_date)->format('Y-m-d') : '-' }}</td>
                    @endif
                    @if(!empty($selectedColumns['end_date']))
                        <td style="text-align: center;">{{ $row->end_date ? \Carbon\Carbon::parse($row->end_date)->format('Y-m-d') : '-' }}</td>
                    @endif
                    @if(!empty($selectedColumns['days_count']))
                        <td style="text-align: center; font-weight: bold; color: #1e40af;">{{ $row->days_count }}</td>
                    @endif
                    @if(!empty($selectedColumns['replacement']))
                        <td>{{ $row->replacementEmployee->name ?? '-' }}</td>
                    @endif
                    @if(!empty($selectedColumns['status']))
                        <td style="text-align: center;">
                            @php
                                $badgeClass = match($row->status?->value) {
                                    'approved'         => 'badge-print-success',
                                    'rejected'         => 'badge-print-danger',
                                    'pending'          => 'badge-print-warning',
                                    'manager_approved' => 'badge-print-info',
                                    default            => 'badge-print-secondary',
                                };
                            @endphp
                            <span class="badge-print {{ $badgeClass }}">{{ $row->status?->label() ?? '-' }}</span>
                        </td>
                    @endif
                    @if(!empty($selectedColumns['reason']))
                        <td style="font-size: 10.5px; color: #475569;">{{ $row->reason ?: '-' }}</td>
                    @endif
                    @if(!empty($selectedColumns['created_at']))
                        <td style="text-align: center; font-size: 10px; color: #64748b;">{{ $row->created_at ? $row->created_at->format('Y-m-d') : '-' }}</td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="20" style="text-align: center; padding: 25px; color: #64748b;">
                        {{ __('لا توجد طلبات إجازات تطابق معايير الفلترة المحددة.') }}
                    </td>
                </tr>
            @endforelse

        @elseif($reportType === 'permissions')
            {{-- Permissions Requests Log Rows --}}
            @forelse($records as $index => $row)
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
                        <td style="text-align: center;">{{ $row->date ? \Carbon\Carbon::parse($row->date)->format('Y-m-d') : '-' }}</td>
                    @endif
                    @if(!empty($selectedColumns['permission_type']))
                        <td style="text-align: center;">{{ $row->type?->label() ?? '-' }}</td>
                    @endif
                    @if(!empty($selectedColumns['start_time']))
                        <td style="text-align: center;">{{ $row->start_time ?? '-' }}</td>
                    @endif
                    @if(!empty($selectedColumns['end_time']))
                        <td style="text-align: center;">{{ $row->end_time ?? '-' }}</td>
                    @endif
                    @if(!empty($selectedColumns['minutes']))
                        <td style="text-align: center; font-weight: bold; color: #0369a1;">{{ $row->duration_minutes }} د</td>
                    @endif
                    @if(!empty($selectedColumns['hours']))
                        <td style="text-align: center;">{{ round(($row->duration_minutes ?? 0) / 60, 2) }} س</td>
                    @endif
                    @if(!empty($selectedColumns['status']))
                        <td style="text-align: center;">
                            @php
                                $badgeClass = match($row->status?->value) {
                                    'approved'         => 'badge-print-success',
                                    'rejected'         => 'badge-print-danger',
                                    'pending'          => 'badge-print-warning',
                                    'manager_approved' => 'badge-print-info',
                                    default            => 'badge-print-secondary',
                                };
                            @endphp
                            <span class="badge-print {{ $badgeClass }}">{{ $row->status?->label() ?? '-' }}</span>
                        </td>
                    @endif
                    @if(!empty($selectedColumns['reason']))
                        <td style="font-size: 10.5px; color: #475569;">{{ $row->reason ?: '-' }}</td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="20" style="text-align: center; padding: 25px; color: #64748b;">
                        {{ __('لا توجد سجلات استئذانات تطابق معايير الفلترة المحددة.') }}
                    </td>
                </tr>
            @endforelse

        @else
            {{-- Leave Balances Report Rows --}}
            @forelse($records as $index => $row)
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
                    @if(!empty($selectedColumns['hire_date']))
                        <td style="text-align: center; font-size: 10px;">{{ $row->hire_date ? \Carbon\Carbon::parse($row->hire_date)->format('Y-m-d') : '-' }}</td>
                    @endif
                    @if(!empty($selectedColumns['annual_entitled']))
                        <td style="text-align: center; font-weight: bold;">{{ $row->annual_entitled }}</td>
                    @endif
                    @if(!empty($selectedColumns['annual_consumed']))
                        <td style="text-align: center; {{ $row->annual_consumed > 0 ? 'color: #b45309; font-weight: bold;' : '' }}">
                            {{ $row->annual_consumed }}
                        </td>
                    @endif
                    @if(!empty($selectedColumns['annual_pending']))
                        <td style="text-align: center; {{ $row->annual_pending > 0 ? 'color: #0284c7; font-weight: bold;' : '' }}">
                            {{ $row->annual_pending }}
                        </td>
                    @endif
                    @if(!empty($selectedColumns['annual_remaining']))
                        <td style="text-align: center; font-weight: bold; color: #166534; font-size: 12px;">
                            {{ $row->annual_remaining }}
                        </td>
                    @endif
                    @if(!empty($selectedColumns['casual_consumed']))
                        <td style="text-align: center;">{{ $row->casual_consumed }} / {{ $row->casual_entitled }}</td>
                    @endif
                    @if(!empty($selectedColumns['sick_consumed']))
                        <td style="text-align: center;">{{ $row->sick_consumed }} / {{ $row->sick_entitled }}</td>
                    @endif
                    @if(!empty($selectedColumns['total_entitled']))
                        <td style="text-align: center; font-weight: bold;">{{ $row->total_entitled }}</td>
                    @endif
                    @if(!empty($selectedColumns['total_consumed']))
                        <td style="text-align: center; font-weight: bold; color: #b45309;">{{ $row->total_consumed }}</td>
                    @endif
                    @if(!empty($selectedColumns['total_remaining']))
                        <td style="text-align: center; font-weight: bold; color: #166534;">{{ $row->total_remaining }}</td>
                    @endif
                    @if(!empty($selectedColumns['consumption_rate']))
                        <td style="text-align: center; font-weight: bold;">
                            {{ $row->consumption_rate }}%
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="20" style="text-align: center; padding: 25px; color: #64748b;">
                        {{ __('لا توجد سجلات أرصدة موظفين تطابق معايير الفلترة المحددة.') }}
                    </td>
                </tr>
            @endforelse
        @endif
    </tbody>
</table>

<!-- Summary Box under the table on print -->
<div style="margin-top: 14px; padding: 8px 12px; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 4px; display: flex; justify-content: space-between; font-size: 10.5px;">
    <span><strong>{{ __('إجمالي الموظفين المدرجين في الكشف:') }}</strong> 
        @if($reportType === 'balances')
            {{ $records->count() }} {{ __('موظف') }}
        @else
            {{ $records->pluck('employee_id')->unique()->count() }} {{ __('موظف') }}
        @endif
    </span>
    <span><strong>{{ __('إجمالي السجلات:') }}</strong> {{ $records->count() }}</span>
</div>
@endsection
