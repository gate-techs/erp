@extends('settings::layouts.printable')

@section('title', __('قسيمة الراتب الفردية') . ' - ' . ($item->employee->name ?? ''))
@section('report_title', __('قسيمة مفردات الراتب الشهري (Employee Payslip)'))

@section('report_meta')
    <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 14px; font-size: 11px; margin-top: 4px;">
        <span><strong>{{ __('رقم المسير:') }}</strong> {{ $item->payroll->payroll_number ?? '-' }}</span>
        <span>•</span>
        <span><strong>{{ __('شهر الراتب:') }}</strong> {{ $item->payroll->payroll_month ?? '-' }} ({{ \Carbon\Carbon::parse(($item->payroll->payroll_month ?? now()->format('Y-m')) . '-01')->translatedFormat('F Y') }})</span>
        <span>•</span>
        <span><strong>{{ __('حالة الصرف:') }}</strong> {{ $item->payment_status?->label() ?? __('معتمد') }}</span>
    </div>
@endsection

@section('content')
<div style="font-family: inherit; color: #1e293b;">

    <!-- Employee Information Box -->
    <div style="border: 1px solid #cbd5e1; border-radius: 6px; padding: 12px; margin-bottom: 16px; background-color: #f8fafc;">
        <table style="width: 100%; border-collapse: collapse; font-size: 11.5px;">
            <tr>
                <td style="padding: 4px 8px; width: 18%;"><strong>{{ __('اسم الموظف:') }}</strong></td>
                <td style="padding: 4px 8px; width: 32%; font-weight: bold; color: #0f172a;">{{ $item->employee->name ?? '-' }}</td>
                <td style="padding: 4px 8px; width: 18%;"><strong>{{ __('الرقم الوظيفي:') }}</strong></td>
                <td style="padding: 4px 8px; width: 32%; font-weight: bold;">{{ $item->employee->employee_code ?? '-' }}</td>
            </tr>
            <tr>
                <td style="padding: 4px 8px;"><strong>{{ __('القسم / الإدارة:') }}</strong></td>
                <td style="padding: 4px 8px;">{{ $item->employee->department->name ?? '-' }}</td>
                <td style="padding: 4px 8px;"><strong>{{ __('المسمى الوظيفي:') }}</strong></td>
                <td style="padding: 4px 8px;">{{ $item->employee->jobTitle->name ?? '-' }}</td>
            </tr>
            <tr>
                <td style="padding: 4px 8px;"><strong>{{ __('الفرع:') }}</strong></td>
                <td style="padding: 4px 8px;">{{ $item->employee->primaryBranch->name ?? '-' }}</td>
                <td style="padding: 4px 8px;"><strong>{{ __('رقم الهوية / الإقامة:') }}</strong></td>
                <td style="padding: 4px 8px;">{{ $item->employee->national_id ?? '-' }}</td>
            </tr>
            <tr>
                <td style="padding: 4px 8px;"><strong>{{ __('البنك المصرفي:') }}</strong></td>
                <td style="padding: 4px 8px;">{{ $item->bank_name ?: ($item->employee->bank->name ?? '-') }}</td>
                <td style="padding: 4px 8px;"><strong>{{ __('رقم الحساب / IBAN:') }}</strong></td>
                <td style="padding: 4px 8px; font-family: monospace;">{{ $item->iban ?: ($item->employee->iban ?? ($item->account_number ?? '-')) }}</td>
            </tr>
        </table>
    </div>

    <!-- Earnings & Deductions Tables (Two Columns) -->
    <div style="display: flex; gap: 16px; margin-bottom: 16px;">

        <!-- 1. Earnings (الاستحقاقات) -->
        <div style="flex: 1; border: 1px solid #cbd5e1; border-radius: 6px; overflow: hidden;">
            <div style="background: #1e293b; color: #ffffff; padding: 6px 12px; font-weight: bold; font-size: 12px; text-align: center;">
                <i class="bx bx-plus-circle"></i> {{ __('الاستحقاقات والمزايا (Earnings)') }}
            </div>
            <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
                <tr style="border-bottom: 1px solid #e2e8f0;">
                    <td style="padding: 6px 10px;">{{ __('الراتب الأساسي') }}</td>
                    <td style="padding: 6px 10px; text-align: left; font-weight: bold;">{{ number_format($item->basic_salary, 2) }}</td>
                </tr>

                @if(!empty($item->allowances_breakdown) && is_array($item->allowances_breakdown))
                    @foreach($item->allowances_breakdown as $allowance)
                        <tr style="border-bottom: 1px solid #e2e8f0; color: #475569;">
                            <td style="padding: 5px 10px; padding-right: 18px;">• {{ $allowance['name'] ?? __('بدل') }}</td>
                            <td style="padding: 5px 10px; text-align: left;">{{ number_format($allowance['amount'] ?? 0, 2) }}</td>
                        </tr>
                    @endforeach
                @elseif($item->allowances_amount > 0)
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 6px 10px;">{{ __('إجمالي البدلات') }}</td>
                        <td style="padding: 6px 10px; text-align: left;">{{ number_format($item->allowances_amount, 2) }}</td>
                    </tr>
                @endif

                @if($item->overtime_amount > 0)
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 6px 10px;">{{ __('أجر ساعات إضافية') }} @if($item->overtime_hours > 0) <small>({{ $item->overtime_hours }} س)</small> @endif</td>
                        <td style="padding: 6px 10px; text-align: left;">{{ number_format($item->overtime_amount, 2) }}</td>
                    </tr>
                @endif

                @if($item->rewards_amount > 0)
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 6px 10px;">{{ __('مكافآت وحوافز تشجيعية') }}</td>
                        <td style="padding: 6px 10px; text-align: left; color: #166534;">+{{ number_format($item->rewards_amount, 2) }}</td>
                    </tr>
                @endif

                @if($item->other_earnings > 0)
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 6px 10px;">{{ __('مستحقات أخرى') }}</td>
                        <td style="padding: 6px 10px; text-align: left;">{{ number_format($item->other_earnings, 2) }}</td>
                    </tr>
                @endif

                <tr style="background: #f1f5f9; font-weight: bold; border-top: 2px solid #cbd5e1;">
                    <td style="padding: 8px 10px;">{{ __('إجمالي الراتب المستحق (Gross)') }}</td>
                    <td style="padding: 8px 10px; text-align: left; font-size: 12px; color: #0f172a;">{{ number_format($item->gross_salary, 2) }}</td>
                </tr>
            </table>
        </div>

        <!-- 2. Deductions (الاستقطاعات) -->
        <div style="flex: 1; border: 1px solid #cbd5e1; border-radius: 6px; overflow: hidden;">
            <div style="background: #991b1b; color: #ffffff; padding: 6px 12px; font-weight: bold; font-size: 12px; text-align: center;">
                <i class="bx bx-minus-circle"></i> {{ __('الاستقطاعات والخصومات (Deductions)') }}
            </div>
            <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
                @if($item->late_deduction > 0)
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 6px 10px;">{{ __('خصم التأخير') }} @if($item->late_minutes > 0) <small>({{ $item->late_minutes }} د)</small> @endif</td>
                        <td style="padding: 6px 10px; text-align: left; color: #991b1b;">-{{ number_format($item->late_deduction, 2) }}</td>
                    </tr>
                @endif

                @if($item->absence_deduction > 0)
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 6px 10px;">{{ __('خصم أيام الغياب') }} @if($item->absence_days > 0) <small>({{ $item->absence_days }} يوم)</small> @endif</td>
                        <td style="padding: 6px 10px; text-align: left; color: #991b1b;">-{{ number_format($item->absence_deduction, 2) }}</td>
                    </tr>
                @endif

                @if($item->loans_deduction > 0)
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 6px 10px;">{{ __('قسط السلفة الشهرية') }}</td>
                        <td style="padding: 6px 10px; text-align: left; color: #991b1b;">-{{ number_format($item->loans_deduction, 2) }}</td>
                    </tr>
                @endif

                @if($item->penalties_deduction > 0)
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 6px 10px;">{{ __('جزاءات ومخالفات إدارية') }}</td>
                        <td style="padding: 6px 10px; text-align: left; color: #991b1b;">-{{ number_format($item->penalties_deduction, 2) }}</td>
                    </tr>
                @endif

                @if($item->insurance_deduction > 0)
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 6px 10px;">{{ __('التأمينات الاجتماعية (حصة الموظف)') }}</td>
                        <td style="padding: 6px 10px; text-align: left; color: #991b1b;">-{{ number_format($item->insurance_deduction, 2) }}</td>
                    </tr>
                @endif

                @if($item->other_deductions > 0)
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 6px 10px;">{{ __('خصومات أخرى') }}</td>
                        <td style="padding: 6px 10px; text-align: left; color: #991b1b;">-{{ number_format($item->other_deductions, 2) }}</td>
                    </tr>
                @endif

                @if($item->total_deductions == 0)
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 12px 10px; text-align: center; color: #64748b;" colspan="2">{{ __('لا توجد خصومات أو استقطاعات لهذا الشهر') }}</td>
                    </tr>
                @endif

                <tr style="background: #fef2f2; font-weight: bold; border-top: 2px solid #fecaca;">
                    <td style="padding: 8px 10px;">{{ __('إجمالي الخصومات (Total Deductions)') }}</td>
                    <td style="padding: 8px 10px; text-align: left; font-size: 12px; color: #991b1b;">-{{ number_format($item->total_deductions, 2) }}</td>
                </tr>
            </table>
        </div>

    </div>

    <!-- Net Salary Highlight Callout Box -->
    <div style="background: #f0fdf4; border: 2px solid #22c55e; border-radius: 6px; padding: 14px 20px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <div style="font-size: 13px; font-weight: bold; color: #15803d; margin-bottom: 3px;">
                {{ __('صافي الراتب المستحق للصرف (Net Salary Payable)') }}
            </div>
            <div style="font-size: 10.5px; color: #475569;">
                {{ __('فقط لا غير: الراتب المستحق عن شهر') }} {{ $item->payroll->payroll_month ?? '-' }}
            </div>
        </div>
        <div style="text-align: left;">
            <span style="font-size: 22px; font-weight: 800; color: #166534; font-family: monospace;">
                {{ number_format($item->net_salary, 2) }}
            </span>
            <span style="font-size: 12px; font-weight: bold; color: #15803d; margin-right: 4px;">
                {{ $globalSetting?->currency_symbol ?: ($globalSetting?->currency ?? 'د.ك') }}
            </span>
        </div>
    </div>

    @if(!empty($item->notes))
        <div style="font-size: 10.5px; color: #64748b; padding: 6px 10px; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 4px; margin-bottom: 16px;">
            <strong>{{ __('ملاحظات:') }}</strong> {{ $item->notes }}
        </div>
    @endif

</div>
@endsection
