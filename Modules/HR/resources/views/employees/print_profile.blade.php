@extends('settings::layouts.printable')

@php
    $locale = app()->getLocale();
    $nameAr = $employee->translate('ar')?->name ?? $employee->name;
    $nameEn = $employee->translate('en')?->name ?? $employee->name_en ?? '';
    $dispName = $locale === 'ar' ? $nameAr : ($nameEn ?: $nameAr);

    $jobTitleName = $employee->jobTitle?->translate($locale)?->name 
        ?? $employee->jobTitle?->translate('ar')?->name 
        ?? $employee->jobTitle?->name 
        ?? '-';

    $deptName = $employee->department?->translate($locale)?->name 
        ?? $employee->department?->translate('ar')?->name 
        ?? $employee->department?->name 
        ?? '-';

    $branchName = $employee->primaryBranch?->name ?? '-';
    $managerName = $employee->manager?->translate($locale)?->name ?? $employee->manager?->name ?? '-';
    $nationalityName = $employee->nationalityRelation?->name ?? (is_string($employee->nationality) ? $employee->nationality : '-');

    $activeAllowances = $employee->allowances->where('is_active', true);
    $allowTotal = $activeAllowances->sum('amount');
    $currency = $globalSetting?->currency_symbol ?: ($globalSetting?->currency ?? 'د.ك');

    $avatarUrl = $employee->avatar_url;
@endphp

@section('title', __('الملف الوظيفي') . ' - ' . $dispName)
@section('report_title', __('بطاقة الملف الوظيفي وبيانات الموظف'))

@section('report_meta')
    <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 14px; font-size: 11px; margin-top: 4px;">
        <span><strong>{{ __('الرقم الوظيفي:') }}</strong> {{ $employee->employee_code ?: '-' }}</span>
        <span>•</span>
        <span><strong>{{ __('القسم:') }}</strong> {{ $deptName }}</span>
        <span>•</span>
        <span><strong>{{ __('المسمى:') }}</strong> {{ $jobTitleName }}</span>
        <span>•</span>
        <span><strong>{{ __('حالة العمل:') }}</strong> {{ $employee->status?->label() ?? __('نشط') }}</span>
        <span>•</span>
        <span><strong>{{ __('تاريخ الطباعة:') }}</strong> {{ now()->format('Y-m-d H:i') }}</span>
    </div>
@endsection

@section('content')
<div style="font-family: inherit; color: #1e293b;">

    <!-- 1. Header / Profile Hero Card -->
    <div style="border: 1px solid #cbd5e1; border-radius: 8px; padding: 14px 16px; margin-bottom: 14px; background: #f8fafc; display: flex; align-items: center; justify-content: space-between; gap: 16px;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 72px; height: 72px; border-radius: 50%; overflow: hidden; border: 2px solid #6366f1; flex-shrink: 0; background: #fff;">
                <img src="{{ $avatarUrl }}" alt="{{ $dispName }}" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            <div>
                <h3 style="margin: 0 0 4px 0; font-size: 16px; font-weight: 800; color: #0f172a;">
                    {{ $dispName }}
                    @if($nameEn && $nameEn !== $dispName)
                        <span style="font-size: 13px; font-weight: 400; color: #64748b; margin-inline-start: 6px;">({{ $nameEn }})</span>
                    @endif
                </h3>
                <div style="font-size: 12px; color: #475569; display: flex; flex-wrap: wrap; gap: 10px;">
                    <span><i class="bx bx-briefcase"></i> <strong>{{ $jobTitleName }}</strong></span>
                    <span>•</span>
                    <span><i class="bx bx-buildings"></i> {{ $deptName }}</span>
                    <span>•</span>
                    <span><i class="bx bx-git-branch"></i> {{ $branchName }}</span>
                </div>
            </div>
        </div>

        <div style="text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};">
            <span style="display: inline-block; background: #e0e7ff; color: #3730a3; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; margin-bottom: 4px;">
                {{ $employee->employee_code }}
            </span>
            <div style="font-size: 11px; color: #64748b;">
                {{ __('تاريخ التعيين:') }} {{ $employee->hire_date ? $employee->hire_date->format('Y-m-d') : '-' }}
            </div>
        </div>
    </div>

    <!-- 2. Personal & Identity Information -->
    <div style="border: 1px solid #cbd5e1; border-radius: 6px; margin-bottom: 14px; overflow: hidden;">
        <div style="background: #0f172a; color: #ffffff; padding: 6px 12px; font-weight: bold; font-size: 12px; display: flex; align-items: center; gap: 6px;">
            <i class="bx bx-id-card"></i> <span>{{ __('البيانات الشخصية والتعريفية (Personal & Identity Information)') }}</span>
        </div>
        <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
            <tr style="border-bottom: 1px solid #f1f5f9;">
                <td style="padding: 6px 10px; width: 18%; background: #f8fafc; font-weight: bold;">{{ __('الرقم المدني / الإقامة:') }}</td>
                <td style="padding: 6px 10px; width: 32%;">{{ $employee->national_id ?: '-' }}</td>
                <td style="padding: 6px 10px; width: 18%; background: #f8fafc; font-weight: bold;">{{ __('الجنسية:') }}</td>
                <td style="padding: 6px 10px; width: 32%;">{{ $nationalityName }}</td>
            </tr>
            <tr style="border-bottom: 1px solid #f1f5f9;">
                <td style="padding: 6px 10px; background: #f8fafc; font-weight: bold;">{{ __('تاريخ الميلاد:') }}</td>
                <td style="padding: 6px 10px;">
                    {{ $employee->date_of_birth ? $employee->date_of_birth->format('Y-m-d') : '-' }}
                    @if($employee->date_of_birth)
                        <span style="color: #64748b;">({{ (int) now()->diffInYears($employee->date_of_birth) }} {{ __('سنة') }})</span>
                    @endif
                </td>
                <td style="padding: 6px 10px; background: #f8fafc; font-weight: bold;">{{ __('النوع (الجنس):') }}</td>
                <td style="padding: 6px 10px;">{{ $employee->gender?->label() ?? '-' }}</td>
            </tr>
            <tr style="border-bottom: 1px solid #f1f5f9;">
                <td style="padding: 6px 10px; background: #f8fafc; font-weight: bold;">{{ __('الحالة الاجتماعية:') }}</td>
                <td style="padding: 6px 10px;">{{ $employee->maritalStatus?->name ?? (is_string($employee->marital_status) ? $employee->marital_status : '-') }}</td>
                <td style="padding: 6px 10px; background: #f8fafc; font-weight: bold;">{{ __('الديانة:') }}</td>
                <td style="padding: 6px 10px;">{{ $employee->religionModel?->name ?? (is_string($employee->religion) ? $employee->religion : '-') }}</td>
            </tr>
            <tr style="border-bottom: 1px solid #f1f5f9;">
                <td style="padding: 6px 10px; background: #f8fafc; font-weight: bold;">{{ __('رقم الهاتف:') }}</td>
                <td style="padding: 6px 10px; direction: ltr; text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};">{{ $employee->phone ?: '-' }}</td>
                <td style="padding: 6px 10px; background: #f8fafc; font-weight: bold;">{{ __('البريد الإلكتروني:') }}</td>
                <td style="padding: 6px 10px;">{{ $employee->email ?: '-' }}</td>
            </tr>
            <tr>
                <td style="padding: 6px 10px; background: #f8fafc; font-weight: bold;">{{ __('جهة اتصال الطوارئ:') }}</td>
                <td style="padding: 6px 10px;" colspan="3">
                    @if($employee->emergency_contact_name)
                        <strong>{{ $employee->emergency_contact_name }}</strong>
                        @if($employee->emergency_contact_relation) ({{ $employee->emergency_contact_relation }}) @endif
                        - <span style="direction: ltr; display: inline-block;">{{ $employee->emergency_contact_phone }}</span>
                    @else
                        -
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <!-- 3. Employment & Organizational Details -->
    <div style="border: 1px solid #cbd5e1; border-radius: 6px; margin-bottom: 14px; overflow: hidden;">
        <div style="background: #0f172a; color: #ffffff; padding: 6px 12px; font-weight: bold; font-size: 12px; display: flex; align-items: center; gap: 6px;">
            <i class="bx bx-briefcase-alt"></i> <span>{{ __('البيانات الوظيفية والتنظيمية (Employment & Job Details)') }}</span>
        </div>
        <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
            <tr style="border-bottom: 1px solid #f1f5f9;">
                <td style="padding: 6px 10px; width: 18%; background: #f8fafc; font-weight: bold;">{{ __('الإدارة / القسم:') }}</td>
                <td style="padding: 6px 10px; width: 32%;">{{ $deptName }}</td>
                <td style="padding: 6px 10px; width: 18%; background: #f8fafc; font-weight: bold;">{{ __('المسمى الوظيفي:') }}</td>
                <td style="padding: 6px 10px; width: 32%;">{{ $jobTitleName }}</td>
            </tr>
            <tr style="border-bottom: 1px solid #f1f5f9;">
                <td style="padding: 6px 10px; background: #f8fafc; font-weight: bold;">{{ __('الفرع الأساسي:') }}</td>
                <td style="padding: 6px 10px;">{{ $branchName }}</td>
                <td style="padding: 6px 10px; background: #f8fafc; font-weight: bold;">{{ __('المدير المباشر:') }}</td>
                <td style="padding: 6px 10px;">{{ $managerName }}</td>
            </tr>
            <tr style="border-bottom: 1px solid #f1f5f9;">
                <td style="padding: 6px 10px; background: #f8fafc; font-weight: bold;">{{ __('نوع التوظيف:') }}</td>
                <td style="padding: 6px 10px;">{{ $employee->employment_type ? $employee->employment_type->label() : '-' }}</td>
                <td style="padding: 6px 10px; background: #f8fafc; font-weight: bold;">{{ __('حالة الموظف:') }}</td>
                <td style="padding: 6px 10px;"><strong>{{ $employee->status?->label() ?? '-' }}</strong></td>
            </tr>
            <tr style="border-bottom: 1px solid #f1f5f9;">
                <td style="padding: 6px 10px; background: #f8fafc; font-weight: bold;">{{ __('جدول الدوام:') }}</td>
                <td style="padding: 6px 10px;">{{ $employee->workSchedule?->translate($locale)?->name ?? $employee->workSchedule?->name ?? '-' }}</td>
                <td style="padding: 6px 10px; background: #f8fafc; font-weight: bold;">{{ __('الوردية (Shift):') }}</td>
                <td style="padding: 6px 10px;">{{ $employee->shift?->translate($locale)?->name ?? $employee->shift?->name ?? '-' }}</td>
            </tr>
            <tr>
                <td style="padding: 6px 10px; background: #f8fafc; font-weight: bold;">{{ __('تاريخ التعيين:') }}</td>
                <td style="padding: 6px 10px;">
                    {{ $employee->hire_date ? $employee->hire_date->format('Y-m-d') : '-' }}
                    @if($kpis['service_years'] !== null)
                        <span style="color: #64748b;">({{ $kpis['service_years'] }} {{ __('سنة') }} {{ $kpis['service_months'] ? 'و ' . $kpis['service_months'] . ' ' . __('شهر') : '' }})</span>
                    @endif
                </td>
                <td style="padding: 6px 10px; background: #f8fafc; font-weight: bold;">{{ __('الرقم التأميني:') }}</td>
                <td style="padding: 6px 10px;">{{ $employee->social_insurance_number ?: '-' }}</td>
            </tr>
        </table>
    </div>

    <!-- 4. Financial & Compensation Details (Two columns: Salary & Bank) -->
    <div style="display: flex; gap: 14px; margin-bottom: 14px;">
        <div style="flex: 1; border: 1px solid #cbd5e1; border-radius: 6px; overflow: hidden;">
            <div style="background: #1e293b; color: #ffffff; padding: 6px 12px; font-weight: bold; font-size: 12px; display: flex; align-items: center; gap: 6px;">
                <i class="bx bx-money"></i> <span>{{ __('البيانات المالية والتعاقدية') }}</span>
            </div>
            <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 6px 10px; width: 45%; background: #f8fafc; font-weight: bold;">{{ __('الراتب الأساسي:') }}</td>
                    <td style="padding: 6px 10px; font-weight: 800; color: #15803d; font-size: 12px;">
                        {{ number_format((float)($employee->basic_salary ?? 0), 2) }} {{ $currency }}
                    </td>
                </tr>
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 6px 10px; background: #f8fafc; font-weight: bold;">{{ __('إجمالي البدلات:') }}</td>
                    <td style="padding: 6px 10px; font-weight: 700; color: #0369a1;">
                        {{ number_format($allowTotal, 2) }} {{ $currency }}
                    </td>
                </tr>
                @if($activeAllowances->isNotEmpty())
                    @foreach($activeAllowances as $alw)
                        <tr style="border-bottom: 1px solid #f1f5f9; color: #64748b;">
                            <td style="padding: 4px 10px; padding-inline-start: 20px;">• {{ $alw->allowanceType?->name ?? __('بدل') }}</td>
                            <td style="padding: 4px 10px;">{{ number_format((float)$alw->amount, 2) }} {{ $currency }}</td>
                        </tr>
                    @endforeach
                @endif
                <tr style="background: #f1f5f9;">
                    <td style="padding: 6px 10px; font-weight: 800; color: #0f172a;">{{ __('إجمالي الراتب التعاقدي:') }}</td>
                    <td style="padding: 6px 10px; font-weight: 900; color: #0f172a; font-size: 12.5px;">
                        {{ number_format((float)($employee->basic_salary ?? 0) + $allowTotal, 2) }} {{ $currency }}
                    </td>
                </tr>
            </table>
        </div>

        <div style="flex: 1; border: 1px solid #cbd5e1; border-radius: 6px; overflow: hidden;">
            <div style="background: #1e293b; color: #ffffff; padding: 6px 12px; font-weight: bold; font-size: 12px; display: flex; align-items: center; gap: 6px;">
                <i class="bx bx-credit-card"></i> <span>{{ __('البيانات المصرفية والحساب') }}</span>
            </div>
            <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 6px 10px; width: 40%; background: #f8fafc; font-weight: bold;">{{ __('اسم البنك:') }}</td>
                    <td style="padding: 6px 10px;">{{ $employee->bank?->name ?: '-' }}</td>
                </tr>
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 6px 10px; background: #f8fafc; font-weight: bold;">{{ __('فرع البنك:') }}</td>
                    <td style="padding: 6px 10px;">{{ $employee->bankBranch?->name ?: '-' }}</td>
                </tr>
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 6px 10px; background: #f8fafc; font-weight: bold;">{{ __('اسم صاحب الحساب:') }}</td>
                    <td style="padding: 6px 10px;">{{ $employee->account_holder_name ?: $dispName }}</td>
                </tr>
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 6px 10px; background: #f8fafc; font-weight: bold;">{{ __('رقم الحساب:') }}</td>
                    <td style="padding: 6px 10px; font-family: monospace;">{{ $employee->account_number ?: '-' }}</td>
                </tr>
                <tr>
                    <td style="padding: 6px 10px; background: #f8fafc; font-weight: bold;">{{ __('رقم IBAN:') }}</td>
                    <td style="padding: 6px 10px; font-family: monospace; font-weight: bold; color: #0f172a;">{{ $employee->iban ?: '-' }}</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- 5. Official Documents Table -->
    @if($employee->documents->isNotEmpty())
        <div style="border: 1px solid #cbd5e1; border-radius: 6px; margin-bottom: 14px; overflow: hidden;">
            <div style="background: #0f172a; color: #ffffff; padding: 6px 12px; font-weight: bold; font-size: 12px; display: flex; align-items: center; gap: 6px;">
                <i class="bx bx-file"></i> <span>{{ __('الوثائق والمستندات الثبوتية المسجلة') }}</span>
            </div>
            <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
                <thead>
                    <tr style="background: #f1f5f9; border-bottom: 1px solid #cbd5e1; font-weight: bold;">
                        <th style="padding: 6px 10px; text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};">{{ __('نوع الوثيقة') }}</th>
                        <th style="padding: 6px 10px; text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};">{{ __('رقم الوثيقة') }}</th>
                        <th style="padding: 6px 10px; text-align: center;">{{ __('تاريخ الإصدار') }}</th>
                        <th style="padding: 6px 10px; text-align: center;">{{ __('تاريخ الانتهاء') }}</th>
                        <th style="padding: 6px 10px; text-align: center;">{{ __('الحالة') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($employee->documents as $doc)
                        @php
                            $isExpired = $doc->expiry_date && $doc->expiry_date->isPast();
                            $isExpiringSoon = $doc->expiry_date && ! $isExpired && $doc->expiry_date->diffInDays(now()) <= 60;
                        @endphp
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 5px 10px; font-weight: 600;">{{ $doc->documentType?->name ?? $doc->document_type ?? '-' }}</td>
                            <td style="padding: 5px 10px; font-family: monospace;">{{ $doc->document_number ?: '-' }}</td>
                            <td style="padding: 5px 10px; text-align: center;">{{ $doc->issue_date ? $doc->issue_date->format('Y-m-d') : '-' }}</td>
                            <td style="padding: 5px 10px; text-align: center; font-weight: 600;">{{ $doc->expiry_date ? $doc->expiry_date->format('Y-m-d') : '-' }}</td>
                            <td style="padding: 5px 10px; text-align: center;">
                                @if($isExpired)
                                    <span style="color: #dc2626; font-weight: bold;">{{ __('منتهية') }}</span>
                                @elseif($isExpiringSoon)
                                    <span style="color: #ea580c; font-weight: bold;">{{ __('قاربت على الانتهاء') }}</span>
                                @else
                                    <span style="color: #16a34a; font-weight: bold;">{{ __('سارية') }}</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

</div>
@endsection
