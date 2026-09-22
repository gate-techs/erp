@extends('settings::layouts.printable')

@php
    $reportTitle = match($reportType ?? 'master') {
        'expiring_docs' => __('تقرير انتهاء وثائق الموظفين'),
        'demographics'  => __('تقرير توزيع الموظفين والتكلفة'),
        default         => __('دليل الموظفين الشامل'),
    };
@endphp

@section('title', $reportTitle)
@section('report_title', $reportTitle)

@section('report_meta')
    <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 14px; font-size: 11px; margin-top: 4px;">
        @if($reportType === 'demographics')
            <span><strong>{{ __('إجمالي القوى العاملة:') }}</strong> {{ $demographics['totalHeadcount'] ?? 0 }} {{ __('موظف') }}</span>
            <span>•</span>
            <span><strong>{{ __('إجمالي الرواتب الشهرية:') }}</strong> {{ number_format($demographics['totalBasicSalary'] ?? 0, 2) }} {{ $globalSetting?->currency_symbol ?: ($globalSetting?->currency ?? 'د.ك') }}</span>
            <span>•</span>
            <span><strong>{{ __('متوسط الراتب الشهري:') }}</strong> {{ number_format($demographics['avgSalary'] ?? 0, 2) }} {{ $globalSetting?->currency_symbol ?: ($globalSetting?->currency ?? 'د.ك') }}</span>
        @else
            <span><strong>{{ __('إجمالي السجلات:') }}</strong> {{ is_countable($records) ? count($records) : 0 }}</span>
            @if($reportType === 'expiring_docs')
                <span>•</span>
                <span><strong>{{ __('المنتهية:') }}</strong> <span style="color: #dc2626; font-weight: bold;">{{ $kpis['expired_count'] ?? 0 }}</span></span>
                <span>•</span>
                <span><strong>{{ __('خلال 30 يوم:') }}</strong> <span style="color: #ea580c; font-weight: bold;">{{ $kpis['expiring_30_days'] ?? 0 }}</span></span>
                <span>•</span>
                <span><strong>{{ __('خلال 60 يوم:') }}</strong> <span style="color: #d97706;">{{ $kpis['expiring_60_days'] ?? 0 }}</span></span>
                <span>•</span>
                <span><strong>{{ __('خلال 90 يوم:') }}</strong> <span style="color: #0284c7;">{{ $kpis['expiring_90_days'] ?? 0 }}</span></span>
            @endif
        @endif
        <span>•</span>
        <span><strong>{{ __('تاريخ الطباعة:') }}</strong> {{ now()->format('Y-m-d H:i') }}</span>
    </div>
@endsection

@section('content')
@if($reportType === 'demographics')
    {{-- Demographics & Cost Breakdown Print Layout --}}
    <div style="display: flex; justify-content: space-between; gap: 10px; margin-bottom: 16px;">
        <div style="flex: 1; padding: 10px; border: 1px solid #e2e8f0; border-radius: 6px; background: #f8fafc; text-align: center;">
            <div style="font-size: 11px; color: #64748b;">{{ __('إجمالي القوى العاملة (Headcount)') }}</div>
            <div style="font-size: 18px; font-weight: bold; color: #1e293b; margin-top: 4px;">{{ $demographics['totalHeadcount'] }} {{ __('موظف') }}</div>
        </div>
        <div style="flex: 1; padding: 10px; border: 1px solid #e2e8f0; border-radius: 6px; background: #f0fdf4; text-align: center;">
            <div style="font-size: 11px; color: #15803d;">{{ __('إجمالي كلفة الرواتب الشهرية') }}</div>
            <div style="font-size: 18px; font-weight: bold; color: #166534; margin-top: 4px;">
                {{ number_format($demographics['totalBasicSalary'], 2) }} {{ $globalSetting?->currency_symbol ?: ($globalSetting?->currency ?? 'د.ك') }}
            </div>
        </div>
        <div style="flex: 1; padding: 10px; border: 1px solid #e2e8f0; border-radius: 6px; background: #f0f9ff; text-align: center;">
            <div style="font-size: 11px; color: #0369a1;">{{ __('متوسط الراتب الشهري للموظف') }}</div>
            <div style="font-size: 18px; font-weight: bold; color: #0284c7; margin-top: 4px;">
                {{ number_format($demographics['avgSalary'], 2) }} {{ $globalSetting?->currency_symbol ?: ($globalSetting?->currency ?? 'د.ك') }}
            </div>
        </div>
    </div>

    <!-- Section 1: Departments Breakdown -->
    <div style="margin-bottom: 16px;">
        <h4 style="font-size: 12px; font-weight: bold; color: #1e3a8a; border-bottom: 2px solid #cbd5e1; padding-bottom: 4px; margin-bottom: 8px;">
            {{ __('1. توزيع الموظفين والتكلفة حسب الأقسام والإدارات') }}
        </h4>
        <table class="report-table">
            <thead>
                <tr>
                    <th>{{ __('القسم / الإدارة') }}</th>
                    <th style="text-align: center; width: 80px;">{{ __('عدد الموظفين') }}</th>
                    <th style="text-align: center; width: 90px;">{{ __('النسبة') }}</th>
                    <th style="text-align: center; width: 130px;">{{ __('إجمالي الرواتب') }} ({{ $globalSetting?->currency_symbol ?: ($globalSetting?->currency ?? 'د.ك') }})</th>
                    <th style="text-align: center; width: 120px;">{{ __('متوسط الراتب') }} ({{ $globalSetting?->currency_symbol ?: ($globalSetting?->currency ?? 'د.ك') }})</th>
                </tr>
            </thead>
            <tbody>
                @forelse($demographics['byDepartment'] as $row)
                    <tr>
                        <td><strong>{{ $row->name }}</strong></td>
                        <td style="text-align: center; font-weight: bold;">{{ $row->count }}</td>
                        <td style="text-align: center; font-weight: bold;">{{ $row->percentage }}%</td>
                        <td style="text-align: center; font-weight: bold; color: #166534;">{{ number_format($row->total_salary, 2) }}</td>
                        <td style="text-align: center; color: #475569;">{{ number_format($row->avg_salary, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="text-align: center; color: #64748b;">{{ __('لا توجد بيانات') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Section 2: Nationalities Breakdown -->
    <div style="margin-bottom: 16px;">
        <h4 style="font-size: 12px; font-weight: bold; color: #1e3a8a; border-bottom: 2px solid #cbd5e1; padding-bottom: 4px; margin-bottom: 8px;">
            {{ __('2. توزيع الموظفين والتكلفة حسب الجنسيات') }}
        </h4>
        <table class="report-table">
            <thead>
                <tr>
                    <th>{{ __('الجنسية') }}</th>
                    <th style="text-align: center; width: 80px;">{{ __('عدد الموظفين') }}</th>
                    <th style="text-align: center; width: 90px;">{{ __('النسبة') }}</th>
                    <th style="text-align: center; width: 130px;">{{ __('إجمالي الرواتب') }} ({{ $globalSetting?->currency_symbol ?: ($globalSetting?->currency ?? 'د.ك') }})</th>
                    <th style="text-align: center; width: 120px;">{{ __('متوسط الراتب') }} ({{ $globalSetting?->currency_symbol ?: ($globalSetting?->currency ?? 'د.ك') }})</th>
                </tr>
            </thead>
            <tbody>
                @forelse($demographics['byNationality'] as $row)
                    <tr>
                        <td><strong>{{ $row->name }}</strong></td>
                        <td style="text-align: center; font-weight: bold;">{{ $row->count }}</td>
                        <td style="text-align: center; font-weight: bold;">{{ $row->percentage }}%</td>
                        <td style="text-align: center; font-weight: bold; color: #166534;">{{ number_format($row->total_salary, 2) }}</td>
                        <td style="text-align: center; color: #475569;">{{ number_format($row->avg_salary, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="text-align: center; color: #64748b;">{{ __('لا توجد بيانات') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Section 3: Branches Breakdown -->
    <div style="margin-bottom: 16px;">
        <h4 style="font-size: 12px; font-weight: bold; color: #1e3a8a; border-bottom: 2px solid #cbd5e1; padding-bottom: 4px; margin-bottom: 8px;">
            {{ __('3. توزيع الموظفين والتكلفة حسب الفروع') }}
        </h4>
        <table class="report-table">
            <thead>
                <tr>
                    <th>{{ __('الفرع') }}</th>
                    <th style="text-align: center; width: 80px;">{{ __('عدد الموظفين') }}</th>
                    <th style="text-align: center; width: 90px;">{{ __('النسبة') }}</th>
                    <th style="text-align: center; width: 130px;">{{ __('إجمالي الرواتب') }} ({{ $globalSetting?->currency_symbol ?: ($globalSetting?->currency ?? 'د.ك') }})</th>
                    <th style="text-align: center; width: 120px;">{{ __('متوسط الراتب') }} ({{ $globalSetting?->currency_symbol ?: ($globalSetting?->currency ?? 'د.ك') }})</th>
                </tr>
            </thead>
            <tbody>
                @forelse($demographics['byBranch'] as $row)
                    <tr>
                        <td><strong>{{ $row->name }}</strong></td>
                        <td style="text-align: center; font-weight: bold;">{{ $row->count }}</td>
                        <td style="text-align: center; font-weight: bold;">{{ $row->percentage }}%</td>
                        <td style="text-align: center; font-weight: bold; color: #166534;">{{ number_format($row->total_salary, 2) }}</td>
                        <td style="text-align: center; color: #475569;">{{ number_format($row->avg_salary, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="text-align: center; color: #64748b;">{{ __('لا توجد بيانات') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Section 4: Job Titles Breakdown -->
    <div>
        <h4 style="font-size: 12px; font-weight: bold; color: #1e3a8a; border-bottom: 2px solid #cbd5e1; padding-bottom: 4px; margin-bottom: 8px;">
            {{ __('4. توزيع الموظفين والتكلفة حسب المسميات الوظيفية') }}
        </h4>
        <table class="report-table">
            <thead>
                <tr>
                    <th>{{ __('المسمى الوظيفي') }}</th>
                    <th style="text-align: center; width: 80px;">{{ __('عدد الموظفين') }}</th>
                    <th style="text-align: center; width: 90px;">{{ __('النسبة') }}</th>
                    <th style="text-align: center; width: 130px;">{{ __('إجمالي الرواتب') }} ({{ $globalSetting?->currency_symbol ?: ($globalSetting?->currency ?? 'د.ك') }})</th>
                    <th style="text-align: center; width: 120px;">{{ __('متوسط الراتب') }} ({{ $globalSetting?->currency_symbol ?: ($globalSetting?->currency ?? 'د.ك') }})</th>
                </tr>
            </thead>
            <tbody>
                @forelse($demographics['byJobTitle'] as $row)
                    <tr>
                        <td><strong>{{ $row->name }}</strong></td>
                        <td style="text-align: center; font-weight: bold;">{{ $row->count }}</td>
                        <td style="text-align: center; font-weight: bold;">{{ $row->percentage }}%</td>
                        <td style="text-align: center; font-weight: bold; color: #166534;">{{ number_format($row->total_salary, 2) }}</td>
                        <td style="text-align: center; color: #475569;">{{ number_format($row->avg_salary, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="text-align: center; color: #64748b;">{{ __('لا توجد بيانات') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

@else
    {{-- Master List & Expiring Documents Data Table --}}
    <table class="report-table">
        <thead>
            <tr>
                @foreach($availableColumns as $colKey => $colDef)
                    @if($colKey !== 'action' && !empty($selectedColumns[$colKey]))
                        <th class="{{ $colDef['class'] ?? '' }}" style="width: {{ $colDef['width'] ?? 'auto' }};">
                            {{ $colDef['label'] }}
                        </th>
                    @endif
                @endforeach
            </tr>
        </thead>
        <tbody>
            @if($reportType === 'expiring_docs')
                {{-- Expiring Documents Table Rows --}}
                @forelse($records as $index => $row)
                    <tr>
                        @if(!empty($selectedColumns['index']))
                            <td style="text-align: center; color: #64748b;">{{ $index + 1 }}</td>
                        @endif
                        @if(!empty($selectedColumns['code']))
                            <td style="font-weight: 700;">{{ $row->employee->employee_code ?? '-' }}</td>
                        @endif
                        @if(!empty($selectedColumns['employee']))
                            <td>
                                <strong>{{ $row->employee->name ?? '-' }}</strong>
                                @if($row->employee->jobTitle)
                                    <div style="font-size: 10px; color: #64748b;">{{ $row->employee->jobTitle->name }}</div>
                                @endif
                            </td>
                        @endif
                        @if(!empty($selectedColumns['department']))
                            <td>{{ $row->employee->department->name ?? '-' }}</td>
                        @endif
                        @if(!empty($selectedColumns['branch']))
                            <td>{{ $row->employee->primaryBranch->name ?? '-' }}</td>
                        @endif
                        @if(!empty($selectedColumns['document_type']))
                            <td style="text-align: center; font-weight: bold;">
                                {{ $row->document_type }}
                            </td>
                        @endif
                        @if(!empty($selectedColumns['document_number']))
                            <td style="text-align: center; font-family: monospace;">{{ $row->document_number }}</td>
                        @endif
                        @if(!empty($selectedColumns['issue_date']))
                            <td style="text-align: center; font-size: 10px;">{{ $row->issue_date }}</td>
                        @endif
                        @if(!empty($selectedColumns['expiry_date']))
                            <td style="text-align: center; font-weight: bold; font-family: monospace;">{{ $row->expiry_date }}</td>
                        @endif
                        @if(!empty($selectedColumns['days_remaining']))
                            <td style="text-align: center;">
                                @if($row->days_remaining < 0)
                                    <span class="badge-print badge-print-danger">{{ __('منتهية منذ') }} {{ abs($row->days_remaining) }} {{ __('يوم') }}</span>
                                @elseif($row->days_remaining <= 30)
                                    <span class="badge-print badge-print-danger">{{ $row->days_remaining }} {{ __('يوم متبقي') }}</span>
                                @elseif($row->days_remaining <= 60)
                                    <span class="badge-print badge-print-warning">{{ $row->days_remaining }} {{ __('يوم متبقي') }}</span>
                                @else
                                    <span class="badge-print badge-print-success">{{ $row->days_remaining }} {{ __('يوم متبقي') }}</span>
                                @endif
                            </td>
                        @endif
                        @if(!empty($selectedColumns['status']))
                            <td style="text-align: center;">
                                @php
                                    $pClass = match($row->status_key ?? '') {
                                        'expired'     => 'badge-print-danger',
                                        'expiring_30' => 'badge-print-danger',
                                        'expiring_60' => 'badge-print-warning',
                                        'expiring_90' => 'badge-print-info',
                                        default       => 'badge-print-success',
                                    };
                                @endphp
                                <span class="badge-print {{ $pClass }}">{{ $row->status_label }}</span>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="20" style="text-align: center; padding: 25px; color: #64748b;">
                            {{ __('لا توجد وثائق منتهية أو قريبة من الانتهاء تطابق معايير الفلترة المحددة.') }}
                        </td>
                    </tr>
                @endforelse

            @else
                {{-- Master List Table Rows --}}
                @forelse($records as $index => $row)
                    <tr>
                        @if(!empty($selectedColumns['index']))
                            <td style="text-align: center; color: #64748b;">{{ $index + 1 }}</td>
                        @endif
                        @if(!empty($selectedColumns['code']))
                            <td style="font-weight: 700;">{{ $row->employee_code ?? '-' }}</td>
                        @endif
                        @if(!empty($selectedColumns['employee']))
                            <td><strong>{{ $row->name ?? '-' }}</strong></td>
                        @endif
                        @if(!empty($selectedColumns['department']))
                            <td>{{ $row->department->name ?? '-' }}</td>
                        @endif
                        @if(!empty($selectedColumns['branch']))
                            <td>{{ $row->primaryBranch->name ?? '-' }}</td>
                        @endif
                        @if(!empty($selectedColumns['job_title']))
                            <td>{{ $row->jobTitle->name ?? '-' }}</td>
                        @endif
                        @if(!empty($selectedColumns['nationality']))
                            <td style="text-align: center;">{{ $row->nationality ?? '-' }}</td>
                        @endif
                        @if(!empty($selectedColumns['national_id']))
                            <td style="text-align: center; font-family: monospace;">{{ $row->national_id ?? '-' }}</td>
                        @endif
                        @if(!empty($selectedColumns['phone']))
                            <td style="text-align: center; font-family: monospace;">{{ $row->phone ?? '-' }}</td>
                        @endif
                        @if(!empty($selectedColumns['email']))
                            <td style="font-size: 10px;">{{ $row->email ?? '-' }}</td>
                        @endif
                        @if(!empty($selectedColumns['employment_type']))
                            <td style="text-align: center;">{{ $row->employment_type?->label() ?? '-' }}</td>
                        @endif
                        @if(!empty($selectedColumns['hire_date']))
                            <td style="text-align: center; font-size: 10px;">{{ $row->hire_date ? \Carbon\Carbon::parse($row->hire_date)->format('Y-m-d') : '-' }}</td>
                        @endif
                        @if(!empty($selectedColumns['basic_salary']))
                            <td style="text-align: center; font-weight: bold; color: #166534;">
                                {{ number_format($row->basic_salary ?? 0, 2) }}
                            </td>
                        @endif
                        @if(!empty($selectedColumns['status']))
                            <td style="text-align: center;">
                                @php
                                    $statusBadge = match($row->status?->value) {
                                        'active'     => 'badge-print-success',
                                        'on_leave'   => 'badge-print-warning',
                                        'suspended'  => 'badge-print-danger',
                                        'resigned', 'terminated' => 'badge-print-secondary',
                                        default      => 'badge-print-secondary',
                                    };
                                @endphp
                                <span class="badge-print {{ $statusBadge }}">{{ $row->status?->label() ?? '-' }}</span>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="20" style="text-align: center; padding: 25px; color: #64748b;">
                            {{ __('لا توجد سجلات موظفين تطابق معايير الفلترة المحددة.') }}
                        </td>
                    </tr>
                @endforelse
            @endif
        </tbody>
    </table>

    <!-- Summary Box under Table -->
    <div style="margin-top: 14px; padding: 8px 12px; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 4px; display: flex; justify-content: space-between; font-size: 10.5px;">
        <span><strong>{{ __('إجمالي الموظفين المدرجين في الكشف:') }}</strong> 
            @if($reportType === 'master')
                {{ count($records) }} {{ __('موظف') }}
            @else
                {{ collect($records)->pluck('employee_id')->unique()->count() }} {{ __('موظف') }}
            @endif
        </span>
        @if($reportType === 'master')
            <span><strong>{{ __('إجمالي الرواتب الأساسية في هذا الكشف:') }}</strong> {{ number_format(collect($records)->sum('basic_salary'), 2) }} {{ $globalSetting?->currency_symbol ?: ($globalSetting?->currency ?? 'د.ك') }}</span>
        @else
            <span><strong>{{ __('إجمالي الوثائق:') }}</strong> {{ count($records) }}</span>
        @endif
    </div>
@endif
@endsection
