@extends('layouts/contentNavbarLayout')

@section('title', __('Edit Notification Template'))

@section('content')
@php
    $ar = $template->translations->firstWhere('locale', 'ar');
    $en = $template->translations->firstWhere('locale', 'en');
    $icons = ['bx-bell', 'bx-calendar-check', 'bx-calendar-plus', 'bx-calendar-x', 'bx-task', 'bx-message-rounded-detail', 'bx-info-circle', 'bx-error-circle', 'bx-check-circle', 'bx-user', 'bx-wallet', 'bx-time-five'];
    $colorMap = ['primary' => '#696cff', 'info' => '#03c3ec', 'success' => '#71dd37', 'warning' => '#ffab00', 'danger' => '#ff3e1d', 'secondary' => '#8592a3', 'dark' => '#233446'];
    $storedColor = old('color', $template->color ?: 'primary');
    $selectedColor = $colorMap[$storedColor] ?? (preg_match('/^#[0-9a-f]{6}$/i', $storedColor) ? $storedColor : '#696cff');
@endphp

<div class="d-flex align-items-center justify-content-between mb-3">
    <div><h4 class="fw-bold mb-1"><i class="bx bx-bell text-primary me-1"></i>{{ __('Notification Template') }}</h4></div>
    <a href="{{ route('settings.notification-templates.index') }}" class="btn btn-outline-secondary btn-sm">{{ __('Back') }}</a>
</div>

<form method="POST" action="{{ route('settings.notification-templates.update', $template->id) }}">
    @csrf @method('PUT')
    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-3"><div class="card-header py-2 d-flex justify-content-between align-items-center"><strong>{{ __('Basic Settings') }}</strong><label class="form-check form-switch mb-0"><input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $template->is_active ? 'checked' : '' }}><span class="form-check-label small">{{ __('Active') }}</span></label></div><div class="card-body py-3"><div class="row g-2"><div class="col-md-6"><label class="form-label small">{{ __('Name (Arabic)') }}</label><input name="name_ar" class="form-control form-control-sm" required value="{{ old('name_ar', $template->name_ar) }}"></div><div class="col-md-6"><label class="form-label small">{{ __('Name (English)') }}</label><input name="name_en" class="form-control form-control-sm" value="{{ old('name_en', $template->name_en) }}"></div><div class="col-md-8"><label class="form-label small d-block">{{ __('Icon') }}</label><input type="hidden" name="icon" id="notificationIcon" value="{{ old('icon', $template->icon ?: 'bx-bell') }}"><div class="icon-picker">@foreach($icons as $icon)<button type="button" class="icon-picker-item {{ old('icon', $template->icon ?: 'bx-bell') === $icon ? 'selected' : '' }}" data-icon="{{ $icon }}" title="{{ $icon }}"><i class="bx {{ $icon }}"></i></button>@endforeach</div></div><div class="col-md-4"><label class="form-label small">{{ __('Color') }}</label><input type="color" name="color" id="notificationColor" class="form-control form-control-color w-100" value="{{ $selectedColor }}" title="{{ __('Choose color') }}"></div></div></div></div>

            <div class="card border-0 shadow-sm mb-3"><div class="card-header py-2"><strong>{{ __('Arabic Translation') }}</strong></div><div class="card-body py-3"><input name="title_ar" class="form-control form-control-sm mb-2" required placeholder="{{ __('Notification title') }}" value="{{ old('title_ar', $ar?->title) }}"><textarea name="message_ar" class="form-control form-control-sm mb-2" rows="2" required placeholder="{{ __('Notification message') }}">{{ old('message_ar', $ar?->message) }}</textarea><input name="action_url_ar" class="form-control form-control-sm" placeholder="{{ __('Action URL') }}" value="{{ old('action_url_ar', $ar?->action_url) }}"></div></div>
            <div class="card border-0 shadow-sm mb-3"><div class="card-header py-2"><strong>{{ __('English Translation') }}</strong></div><div class="card-body py-3"><input name="title_en" class="form-control form-control-sm mb-2" placeholder="{{ __('Notification title') }}" value="{{ old('title_en', $en?->title) }}"><textarea name="message_en" class="form-control form-control-sm mb-2" rows="2" placeholder="{{ __('Notification message') }}">{{ old('message_en', $en?->message) }}</textarea><input name="action_url_en" class="form-control form-control-sm" placeholder="{{ __('Action URL') }}" value="{{ old('action_url_en', $en?->action_url) }}"></div></div>
            <button class="btn btn-primary btn-sm px-4"><i class="bx bx-save me-1"></i>{{ __('Save Changes') }}</button>
        </div>

        <div class="col-lg-4"><div class="card border-0 shadow-sm mb-3"><div class="card-header py-2"><strong>{{ __('Preview') }}</strong><div class="btn-group btn-group-sm float-end"><button type="button" class="btn btn-outline-primary active" id="previewAr">AR</button><button type="button" class="btn btn-outline-primary" id="previewEn">EN</button></div></div><div class="card-body bg-light p-3"><div id="notificationPreview" class="notification-preview-card"><div class="notification-preview-icon"><i id="previewIcon" class="bx {{ $template->icon ?: 'bx-bell' }}"></i></div><div class="min-w-0"><div id="previewTitle" class="fw-bold text-dark"></div><div id="previewMessage" class="small text-muted mt-1"></div></div></div></div></div><div class="card border-0 shadow-sm"><div class="card-header py-2"><strong>{{ __('Variables') }}</strong></div><div class="card-body p-3">@forelse($template->variables ?? [] as $variable)<code class="d-block mb-1">{{ str_starts_with($variable, '{') ? $variable : '{'.$variable.'}' }}</code>@empty<span class="text-muted small">{{ __('No variables configured.') }}</span>@endforelse</div></div></div>
    </div>
</form>
@endsection

@section('page-style')
<style>
.icon-picker{display:flex;flex-wrap:wrap;gap:5px}.icon-picker-item{width:34px;height:34px;border:1px solid #d9dee3;background:#fff;border-radius:7px;color:#697a8d;font-size:19px;display:inline-flex;align-items:center;justify-content:center;cursor:pointer}.icon-picker-item:hover,.icon-picker-item.selected{border-color:#696cff;background:#e7e7ff;color:#696cff}.notification-preview-card{display:flex;gap:10px;align-items:flex-start;background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:12px;min-height:78px}.notification-preview-icon{width:38px;height:38px;display:flex;align-items:center;justify-content:center;border-radius:10px;flex:0 0 38px;font-size:19px}
</style>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const icon = document.getElementById('notificationIcon');
    const color = document.getElementById('notificationColor');
    const card = document.getElementById('notificationPreview');
    const iconView = document.getElementById('previewIcon');
    const titleView = document.getElementById('previewTitle');
    const messageView = document.getElementById('previewMessage');
    const arTitle = document.querySelector('[name="title_ar"]');
    const arMessage = document.querySelector('[name="message_ar"]');
    const enTitle = document.querySelector('[name="title_en"]');
    const enMessage = document.querySelector('[name="message_en"]');
    let locale = 'ar';

    function refresh() {
        iconView.className = 'bx ' + (icon.value || 'bx-bell');
        iconView.style.color = color.value || '#696cff';
        iconView.parentElement.style.backgroundColor = (color.value || '#696cff') + '22';
        titleView.textContent = (locale === 'ar' ? arTitle.value : (enTitle.value || arTitle.value)) || '{{ __('Notification title') }}';
        messageView.textContent = (locale === 'ar' ? arMessage.value : (enMessage.value || arMessage.value)) || '{{ __('Notification message') }}';
        card.dir = locale === 'ar' ? 'rtl' : 'ltr';
    }

    [icon, color, arTitle, arMessage, enTitle, enMessage].forEach(function (field) {
        field.addEventListener('input', refresh);
        field.addEventListener('change', refresh);
    });
    document.querySelectorAll('.icon-picker-item').forEach(function (button) {
        button.addEventListener('click', function () {
            document.querySelectorAll('.icon-picker-item').forEach(item => item.classList.remove('selected'));
            this.classList.add('selected');
            icon.value = this.dataset.icon;
            refresh();
        });
    });
    document.getElementById('previewAr').onclick = function () { locale = 'ar'; this.classList.add('active'); document.getElementById('previewEn').classList.remove('active'); refresh(); };
    document.getElementById('previewEn').onclick = function () { locale = 'en'; this.classList.add('active'); document.getElementById('previewAr').classList.remove('active'); refresh(); };
    refresh();
});
</script>
@endsection
