@extends('layouts/contentNavbarLayout')

@section('title', __('Notification Templates'))

@section('content')
<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="icon-base bx bx-bell text-primary me-2"></i>{{ __('قوالب الإشعارات (Notification Templates)') }}</h4>
        <p class="text-muted mb-0 font-13">{{ __('إدارة وتخصيص نماذج الإشعارات الداخلية والتنبيهات لكافة موديولات النظام.') }}</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('settings.notification-templates.index') }}" class="btn btn-sm {{ $selectedModule === 'all' ? 'btn-primary' : 'btn-outline-secondary' }}">
            <i class="icon-base bx bx-layer me-1 font-16"></i>{{ __('الكل') }}
        </a>
        <a href="{{ route('settings.notification-templates.index', ['module' => 'hr']) }}" class="btn btn-sm {{ $selectedModule === 'hr' ? 'btn-primary' : 'btn-outline-secondary' }}">
            <i class="icon-base bx bx-user-pin me-1 font-16"></i>{{ __('الموارد البشرية') }}
        </a>
        <a href="{{ route('settings.notification-templates.index', ['module' => 'accounting']) }}" class="btn btn-sm {{ $selectedModule === 'accounting' ? 'btn-primary' : 'btn-outline-secondary' }}">
            <i class="icon-base bx bx-calculator me-1 font-16"></i>{{ __('المحاسبة والمالية') }}
        </a>
        <a href="{{ route('settings.notification-templates.index', ['module' => 'crm']) }}" class="btn btn-sm {{ $selectedModule === 'crm' ? 'btn-primary' : 'btn-outline-secondary' }}">
            <i class="icon-base bx bx-phone-call me-1 font-16"></i>{{ __('CRM') }}
        </a>
        <a href="{{ route('settings.notification-templates.index', ['module' => 'tasks']) }}" class="btn btn-sm {{ $selectedModule === 'tasks' ? 'btn-primary' : 'btn-outline-secondary' }}">
            <i class="icon-base bx bx-task me-1 font-16"></i>{{ __('المهام والمشاريع') }}
        </a>
        <a href="{{ route('settings.notification-templates.index', ['module' => 'inventory']) }}" class="btn btn-sm {{ $selectedModule === 'inventory' ? 'btn-primary' : 'btn-outline-secondary' }}">
            <i class="icon-base bx bx-package me-1 font-16"></i>{{ __('المخزون') }}
        </a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="table-responsive text-nowrap">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 50%;">
                        <div class="d-flex align-items-center gap-1">
                            <span>{{ __('اسم القالب') }}</span>
                        </div>
                    </th>
                    <th class="text-center" style="width: 20%;">
                        <span>{{ __('الوحدة') }}</span>
                    </th>
                    <th class="text-center" style="width: 15%;">
                        <span>{{ __('الحالة') }}</span>
                    </th>
                    <th class="text-center" style="width: 15%;">
                        <span>{{ __('الإجراءات') }}</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse($templates as $template)
                    @php
                        $moduleBadges = [
                            'hr' => 'bg-label-success',
                            'tasks' => 'bg-label-primary',
                            'accounting' => 'bg-label-warning',
                            'crm' => 'bg-label-info',
                        ];
                        $badgeClass = $moduleBadges[$template->module] ?? 'bg-label-secondary';
                    @endphp
                    <tr>
                        <td>
                            <div class="d-flex flex-column">
                                <a href="{{ route('settings.notification-templates.edit', $template->id) }}" class="fw-semibold text-heading font-14 mb-0">
                                    {{ app()->getLocale() === 'ar' ? $template->name_ar : ($template->name_en ?: $template->name_ar) }}
                                </a>
                                <small class="text-muted font-11"><code>{{ $template->code }}</code></small>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $badgeClass }} px-3 py-1 font-12 fw-medium rounded-pill">
                                {{ strtoupper($template->module ?: 'General') }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-label-{{ $template->is_active ? 'success' : 'secondary' }} font-11">
                                {{ $template->is_active ? __('مفعل') : __('معطل') }}
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('settings.notification-templates.edit', $template->id) }}" class="btn btn-icon btn-sm btn-label-primary" title="{{ __('تعديل') }}">
                                <i class="icon-base bx bx-edit-alt font-16"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">
                            <i class="icon-base bx bx-bell font-40 text-muted mb-2 d-block"></i>
                            <span>{{ __('لا توجد قوالب إشعارات.') }}</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('page-script')
<script>
document.querySelectorAll('.template-toggle').forEach(function (el) {
    el.addEventListener('change', function () {});
});
</script>
@endsection
