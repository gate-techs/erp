@extends('settings::layouts.printable')

@section('title', __('الملف الشخصي') . ' - ' . $user->name)
@section('report_title', __('بطاقة بيانات المستخدم والحساب (User Profile Card)'))

@section('report_meta')
    <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 14px; font-size: 11px; margin-top: 4px;">
        <span><strong>{{ __('المعرف:') }}</strong> #{{ $user->id }}</span>
        <span>•</span>
        <span><strong>{{ __('اسم المستخدم:') }}</strong> {{ $user->username ?: '-' }}</span>
        <span>•</span>
        <span><strong>{{ __('الحالة:') }}</strong> {{ $user->is_active ? __('نشط') : __('معطل') }}</span>
        <span>•</span>
        <span><strong>{{ __('تاريخ الطباعة:') }}</strong> {{ now()->format('Y-m-d H:i') }}</span>
    </div>
@endsection

@section('content')
<div style="font-family: inherit; color: #1e293b;">

    <!-- User Header Card -->
    <div style="border: 1px solid #cbd5e1; border-radius: 8px; padding: 14px 16px; margin-bottom: 16px; background: #f8fafc; display: flex; align-items: center; justify-content: space-between; gap: 16px;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 72px; height: 72px; border-radius: 50%; overflow: hidden; border: 2px solid #6366f1; flex-shrink: 0; background: #fff;">
                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            <div>
                <h3 style="margin: 0 0 4px 0; font-size: 16px; font-weight: 800; color: #0f172a;">{{ $user->name }}</h3>
                <div style="font-size: 12px; color: #475569;">
                    <span><i class="bx bx-envelope"></i> {{ $user->email }}</span>
                    @if($user->phone)
                        <span style="margin: 0 6px;">•</span>
                        <span style="direction: ltr; display: inline-block;"><i class="bx bx-phone"></i> {{ $user->phone }}</span>
                    @endif
                </div>
            </div>
        </div>

        <div style="text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};">
            <span style="display: inline-block; background: #e0e7ff; color: #3730a3; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: bold;">
                {{ $user->roles->pluck('display_name')->first() ?: ($user->roles->pluck('name')->first() ?: __('مستخدم')) }}
            </span>
            <div style="font-size: 11px; color: #64748b; margin-top: 4px;">
                {{ __('تاريخ التسجيل:') }} {{ $user->created_at ? $user->created_at->format('Y-m-d') : '-' }}
            </div>
        </div>
    </div>

    <!-- User Information Table -->
    <div style="border: 1px solid #cbd5e1; border-radius: 6px; margin-bottom: 16px; overflow: hidden;">
        <div style="background: #0f172a; color: #ffffff; padding: 6px 12px; font-weight: bold; font-size: 12px;">
            <i class="bx bx-user"></i> {{ __('تفاصيل الحساب والمعلومات الأساسية') }}
        </div>
        <table style="width: 100%; border-collapse: collapse; font-size: 11.5px;">
            <tr style="border-bottom: 1px solid #f1f5f9;">
                <td style="padding: 8px 12px; width: 22%; background: #f8fafc; font-weight: bold;">{{ __('الاسم بالكامل:') }}</td>
                <td style="padding: 8px 12px; width: 28%;">{{ $user->name }}</td>
                <td style="padding: 8px 12px; width: 22%; background: #f8fafc; font-weight: bold;">{{ __('اسم المستخدم:') }}</td>
                <td style="padding: 8px 12px; width: 28%;">{{ $user->username ?: '-' }}</td>
            </tr>
            <tr style="border-bottom: 1px solid #f1f5f9;">
                <td style="padding: 8px 12px; background: #f8fafc; font-weight: bold;">{{ __('البريد الإلكتروني:') }}</td>
                <td style="padding: 8px 12px;">{{ $user->email }}</td>
                <td style="padding: 8px 12px; background: #f8fafc; font-weight: bold;">{{ __('رقم الهاتف:') }}</td>
                <td style="padding: 8px 12px; direction: ltr; text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};">{{ $user->phone ?: '-' }}</td>
            </tr>
            <tr style="border-bottom: 1px solid #f1f5f9;">
                <td style="padding: 8px 12px; background: #f8fafc; font-weight: bold;">{{ __('نوع الحساب:') }}</td>
                <td style="padding: 8px 12px;">{{ is_object($user->user_type) ? $user->user_type->name : ($user->user_type ?: __('مستخدم')) }}</td>
                <td style="padding: 8px 12px; background: #f8fafc; font-weight: bold;">{{ __('حالة الحساب:') }}</td>
                <td style="padding: 8px 12px;">
                    <span style="font-weight: bold; color: {{ $user->is_active ? '#16a34a' : '#dc2626' }};">
                        {{ $user->is_active ? __('نشط ومفعل') : __('معطل') }}
                    </span>
                </td>
            </tr>
            <tr>
                <td style="padding: 8px 12px; background: #f8fafc; font-weight: bold;">{{ __('الأدوار الممنوحة:') }}</td>
                <td style="padding: 8px 12px;" colspan="3">
                    @foreach($user->roles as $role)
                        <span style="display: inline-block; background: #f1f5f9; padding: 2px 8px; border-radius: 4px; font-size: 11px; margin-inline-end: 6px;">
                            {{ $role->display_name ?? $role->name }}
                        </span>
                    @endforeach
                </td>
            </tr>
        </table>
    </div>

</div>
@endsection
