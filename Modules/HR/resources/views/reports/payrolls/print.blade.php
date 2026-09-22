@extends('settings::layouts.printable')

@php
    $reportTitle = match($reportType ?? 'payroll_sheet') {
        'loans'             => __('كشف تقرير السلف والقروض والأقساط الشهرية'),
        'rewards_penalties' => __('كشف تقرير المكافآت والحوافز والجزاءات الإدارية'),
        'eos'               => __('كشف تقرير التزامات ومخصصات نهاية الخدمة التراكمية (EOS)'),
        default             => __('كشف مسير الرواتب الشهري المجمع المعتمد'),
    };
@endphp

@section('title', $reportTitle)
@section('report_title', $reportTitle)

@section('report_meta')
    <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 14px; font-size: 11px; margin-top: 4px;">
        @if($reportType === 'payroll_sheet')
            @if(request('payroll_month'))
                <span><strong>{{ __('شهر المسير:') }}</strong> {{ request('payroll_month') }}</span>
                <span>•</span>
            @endif
            <span><strong>{{ __('إجمالي الموظفين:') }}</strong> {{ $kpis['total_employees'] ?? $records->count() }}</span>
            <span>•</span>
            <span><strong>{{ __('إجمالي المستحقات (Gross):') }}</strong> {{ number_format($kpis['total_gross_salary'] ?? 0, 2) }}</span>
            <span>•</span>
            <span><strong>{{ __('إجمالي الخصومات:') }}</strong> {{ number_format($kpis['total_deductions'] ?? 0, 2) }}</span>
            <span>•</span>
            <span style="color: #166534;"><strong>{{ __('صافي الرواتب (Net):') }}</strong> {{ number_format($kpis['total_net_salary'] ?? 0, 2) }}</span>
        @elseif($reportType === 'loans')
            <span><strong>{{ __('إجمالي عدد السلف:') }}</strong> {{ $kpis['total_loans_count'] ?? $records->count() }}</span>
            <span>•</span>
            <span><strong>{{ __('إجمالي مبالغ السلف:') }}</strong> {{ number_format($kpis['total_amount'] ?? 0, 2) }}</span>
            <span>•</span>
            <span style="color: #166534;"><strong>{{ __('إجمالي المسدد:') }}</strong> {{ number_format($kpis['total_paid'] ?? 0, 2) }}</span>
            <span>•</span>
            <span style="color: #991b1b;"><strong>{{ __('إجمالي المتبقي:') }}</strong> {{ number_format($kpis['total_remaining'] ?? 0, 2) }}</span>
        @elseif($reportType === 'rewards_penalties')
            @if(request('start_date') || request('end_date'))
                <span><strong>{{ __('الفترة:') }}</strong> {{ request('start_date', '-') }} {{ __('إلى') }} {{ request('end_date', '-') }}</span>
                <span>•</span>
            @endif
            <span><strong>{{ __('إجمالي البنود:') }}</strong> {{ $kpis['total_items_count'] ?? $records->count() }}</span>
            <span>•</span>
            <span style="color: #166534;"><strong>{{ __('إجمالي المكافآت:') }}</strong> +{{ number_format($kpis['total_rewards_amount'] ?? 0, 2) }}</span>
            <span>•</span>
            <span style="color: #991b1b;"><strong>{{ __('إجمالي الجزاءات:') }}</strong> -{{ number_format($kpis['total_penalties_amount'] ?? 0, 2) }}</span>
            <span>•</span>
            <span><strong>{{ __('صافي الأثر المالي:') }}</strong> {{ number_format($kpis['net_impact'] ?? 0, 2) }}</span>
        @elseif($reportType === 'eos')
            <span><strong>{{ __('حتى تاريخ:') }}</strong> {{ request('as_of_date', now()->format('Y-m-d')) }}</span>
            <span>•</span>
            <span><strong>{{ __('إجمالي الموظفين:') }}</strong> {{ $kpis['total_employees'] ?? $records->count() }}</span>
            <span>•</span>
            <span style="color: #1e40af;"><strong>{{ __('إجمالي مخصص نهاية الخدمة التراكمي:') }}</strong> {{ number_format($kpis['total_cumulative_provisions'] ?? 0, 2) }}</span>
        @endif
    </div>
@endsection

@section('content')
<table class="report-table">
    <thead>
        <tr>
            @foreach($availableColumns as $colKey => $colDef)
                @if($colKey !== 'actions' && !empty($selectedColumns[$colKey]))
                    <th class="{{ $colDef['class'] ?? '' }}" style="width: {{ $colDef['width'] ?? 'auto' }};">{{ $colDef['label'] }}</th>
                @endif
            @endforeach
        </tr>
    </thead>
    <tbody>
        @if($reportType === 'loans')
            {{-- Loans Rows --}}
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
                    @if(!empty($selectedColumns['loan_type']))
                        <td style="text-align: center;">{{ $row->type?->label() ?? '-' }}</td>
                    @endif
                    @if(!empty($selectedColumns['start_date']))
                        <td style="text-align: center;">{{ $row->start_date ? \Carbon\Carbon::parse($row->start_date)->format('Y-m-d') : '-' }}</td>
                    @endif
                    @if(!empty($selectedColumns['amount']))
                        <td style="text-align: center; font-weight: bold;">{{ number_format($row->amount, 2) }}</td>
                    @endif
                    @if(!empty($selectedColumns['installments_count']))
                        <td style="text-align: center;">{{ $row->installments_count }}</td>
                    @endif
                    @if(!empty($selectedColumns['monthly_installment']))
                        <td style="text-align: center;">{{ number_format($row->monthly_installment, 2) }}</td>
                    @endif
                    @if(!empty($selectedColumns['total_paid']))
                        <td style="text-align: center; color: #166534; font-weight: bold;">{{ number_format($row->total_paid, 2) }}</td>
                    @endif
                    @if(!empty($selectedColumns['remaining_amount']))
                        <td style="text-align: center; color: #991b1b; font-weight: bold;">{{ number_format($row->remaining_amount, 2) }}</td>
                    @endif
                    @if(!empty($selectedColumns['status']))
                        <td style="text-align: center;">
                            @php
                                $badgeClass = match($row->status?->value) {
                                    'active'    => 'badge-print-success',
                                    'completed' => 'badge-print-info',
                                    'pending'   => 'badge-print-warning',
                                    'rejected'  => 'badge-print-danger',
                                    default     => 'badge-print-secondary',
                                };
                            @endphp
                            <span class="badge-print {{ $badgeClass }}">{{ $row->status?->label() ?? '-' }}</span>
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="20" style="text-align: center; padding: 25px; color: #64748b;">
                        {{ __('لا توجد سجلات سلف تطابق معايير الفلترة المحددة.') }}
                    </td>
                </tr>
            @endforelse

        @elseif($reportType === 'rewards_penalties')
            {{-- Rewards & Penalties Rows --}}
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
                    @if(!empty($selectedColumns['item_kind']))
                        <td style="text-align: center; font-weight: bold; {{ $row->item_kind === 'reward' ? 'color: #166534;' : 'color: #991b1b;' }}">
                            {{ $row->item_kind === 'reward' ? __('مكافأة') : __('جزاء') }}
                        </td>
                    @endif
                    @if(!empty($selectedColumns['date']))
                        <td style="text-align: center;">{{ $row->date ? \Carbon\Carbon::parse($row->date)->format('Y-m-d') : '-' }}</td>
                    @endif
                    @if(!empty($selectedColumns['amount']))
                        <td style="text-align: center; font-weight: bold; {{ $row->item_kind === 'reward' ? 'color: #166534;' : 'color: #991b1b;' }}">
                            {{ $row->item_kind === 'reward' ? '+' : '-' }}{{ number_format($row->amount, 2) }}
                        </td>
                    @endif
                    @if(!empty($selectedColumns['type_name']))
                        <td>{{ $row->type_name }}</td>
                    @endif
                    @if(!empty($selectedColumns['reason']))
                        <td style="font-size: 10.5px; color: #475569;">{{ $row->reason ?: '-' }}</td>
                    @endif
                    @if(!empty($selectedColumns['status']))
                        <td style="text-align: center;">
                            {{ $row->status && method_exists($row->status, 'label') ? $row->status->label() : '-' }}
                        </td>
                    @endif
                    @if(!empty($selectedColumns['approved_by']))
                        <td>{{ $row->approved_by }}</td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="20" style="text-align: center; padding: 25px; color: #64748b;">
                        {{ __('لا توجد سجلات مكافآت أو جزاءات تطابق معايير الفلترة.') }}
                    </td>
                </tr>
            @endforelse

        @elseif($reportType === 'eos')
            {{-- EOS Provisions Rows --}}
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
                        <td style="text-align: center; font-size: 10px;">{{ $row->hire_date }}</td>
                    @endif
                    @if(!empty($selectedColumns['service_duration']))
                        <td style="text-align: center; font-weight: bold;">{{ $row->service_duration_text }}</td>
                    @endif
                    @if(!empty($selectedColumns['monthly_salary']))
                        <td style="text-align: center;">{{ number_format($row->monthly_salary, 2) }}</td>
                    @endif
                    @if(!empty($selectedColumns['tier1_provision']))
                        <td style="text-align: center; color: #64748b;">{{ number_format($row->tier1_provision, 2) }}</td>
                    @endif
                    @if(!empty($selectedColumns['tier2_provision']))
                        <td style="text-align: center; color: #64748b;">{{ number_format($row->tier2_provision, 2) }}</td>
                    @endif
                    @if(!empty($selectedColumns['total_eos_provision']))
                        <td style="text-align: center; font-weight: bold; color: #1e40af; font-size: 12px;">
                            {{ number_format($row->total_eos_provision, 2) }}
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="20" style="text-align: center; padding: 25px; color: #64748b;">
                        {{ __('لا توجد بيانات موظفين لحساب مخصص نهاية الخدمة.') }}
                    </td>
                </tr>
            @endforelse

        @else
            {{-- Monthly Payroll Sheet Rows --}}
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
                    @if(!empty($selectedColumns['basic_salary']))
                        <td style="text-align: center;">{{ number_format($row->basic_salary, 2) }}</td>
                    @endif
                    @if(!empty($selectedColumns['allowances']))
                        <td style="text-align: center; color: #0284c7;">{{ number_format($row->allowances_amount, 2) }}</td>
                    @endif
                    @if(!empty($selectedColumns['overtime']))
                        <td style="text-align: center;">{{ number_format($row->overtime_amount, 2) }}</td>
                    @endif
                    @if(!empty($selectedColumns['rewards']))
                        <td style="text-align: center; color: #166534;">{{ number_format($row->rewards_amount, 2) }}</td>
                    @endif
                    @if(!empty($selectedColumns['gross_salary']))
                        <td style="text-align: center; font-weight: bold;">{{ number_format($row->gross_salary, 2) }}</td>
                    @endif
                    @if(!empty($selectedColumns['late_deduction']))
                        <td style="text-align: center; color: #991b1b;">{{ number_format($row->late_deduction, 2) }}</td>
                    @endif
                    @if(!empty($selectedColumns['absence_deduction']))
                        <td style="text-align: center; color: #991b1b;">{{ number_format($row->absence_deduction, 2) }}</td>
                    @endif
                    @if(!empty($selectedColumns['loans_deduction']))
                        <td style="text-align: center; color: #991b1b;">{{ number_format($row->loans_deduction, 2) }}</td>
                    @endif
                    @if(!empty($selectedColumns['penalties_deduction']))
                        <td style="text-align: center; color: #991b1b;">{{ number_format($row->penalties_deduction, 2) }}</td>
                    @endif
                    @if(!empty($selectedColumns['insurance_deduction']))
                        <td style="text-align: center; color: #991b1b;">{{ number_format($row->insurance_deduction, 2) }}</td>
                    @endif
                    @if(!empty($selectedColumns['total_deductions']))
                        <td style="text-align: center; font-weight: bold; color: #991b1b;">{{ number_format($row->total_deductions, 2) }}</td>
                    @endif
                    @if(!empty($selectedColumns['net_salary']))
                        <td style="text-align: center; font-weight: bold; color: #166534; font-size: 12px;">
                            {{ number_format($row->net_salary, 2) }}
                        </td>
                    @endif
                    @if(!empty($selectedColumns['payment_status']))
                        <td style="text-align: center;">
                            {{ $row->payment_status?->label() ?? '-' }}
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="20" style="text-align: center; padding: 25px; color: #64748b;">
                        {{ __('لا توجد سجلات مسير رواتب تطابق معايير الفلترة المحددة.') }}
                    </td>
                </tr>
            @endforelse
        @endif
    </tbody>
</table>

<!-- Financial Signature & Totals Box -->
<div style="margin-top: 14px; padding: 10px 14px; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 4px; display: flex; justify-content: space-between; font-size: 11px;">
    <span><strong>{{ __('إجمالي السجلات المدرجة:') }}</strong> {{ $records->count() }}</span>
    @if($reportType === 'payroll_sheet')
        <span><strong>{{ __('إجمالي الاستحقاقات:') }}</strong> {{ number_format($kpis['total_gross_salary'] ?? 0, 2) }}</span>
        <span><strong>{{ __('إجمالي الخصومات:') }}</strong> {{ number_format($kpis['total_deductions'] ?? 0, 2) }}</span>
        <span style="color: #166534;"><strong>{{ __('صافي الرواتب المصروفة:') }}</strong> {{ number_format($kpis['total_net_salary'] ?? 0, 2) }}</span>
    @elseif($reportType === 'eos')
        <span style="color: #1e40af;"><strong>{{ __('إجمالي التزامات نهاية الخدمة:') }}</strong> {{ number_format($kpis['total_cumulative_provisions'] ?? 0, 2) }}</span>
    @endif
</div>
@endsection
