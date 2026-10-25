@extends('layouts/contentNavbarLayout')

@section('title', __('تحرير قالب الإشعار') . ' : ' . ($template->name_ar ?: $template->name_en))

@section('page-style')
<style>
    .variable-simple-row {
        cursor: pointer;
        padding: 5px 8px;
        border-radius: 4px;
        transition: background-color 0.15s ease;
        line-height: 1.8;
    }
    .variable-simple-row:hover {
        background-color: #f0fdf4;
    }
    .variable-tag-text {
        color: #0d9488;
        font-family: inherit;
        font-weight: 600;
        direction: ltr;
        display: inline-block;
    }
    .variable-desc-text {
        color: #1e293b;
        font-weight: 500;
    }
</style>
@endsection

@section('content')
@php
    $ar = $template->translations->firstWhere('locale', 'ar');
    $en = $template->translations->firstWhere('locale', 'en');
@endphp

<!-- Header -->
<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1">
            <span>{{ __('تحرير قالب الإشعار') }} : <span class="text-primary">{{ $template->name_ar ?: $template->name_en }}</span></span>
        </h4>
        <p class="text-muted mb-0 font-13">{{ __('قم بتعديل موضوع الإشعار ومحتواه وإعدادات اللغة لهذا النموذج.') }}</p>
    </div>
    <a href="{{ route('settings.notification-templates.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-1">
        <i class="icon-base bx bx-arrow-back font-16"></i>
        <span>{{ __('إلى الخلف') }}</span>
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="icon-base bx bx-check-circle me-1"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<form method="POST" action="{{ route('settings.notification-templates.update', $template->id) }}" id="notificationTemplateForm">
    @csrf
    @method('PUT')

    <input type="hidden" name="name_ar" value="{{ $template->name_ar }}">
    <input type="hidden" name="name_en" value="{{ $template->name_en }}">
    <input type="hidden" name="icon" value="{{ $template->icon ?: 'bx-bell' }}">
    <input type="hidden" name="color" value="{{ $template->color ?: '#696cff' }}">
    <input type="hidden" name="description" value="{{ $template->description }}">

    <div class="row g-4">
        <!-- Main Form Column (Left / Center) -->
        <div class="col-xl-8 col-lg-7">
            <!-- Language Selector Card -->
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <ul class="nav nav-pills" id="notifLangTabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active px-3 py-1 font-13" id="tab-ar-btn" data-bs-toggle="pill" data-bs-target="#tab-ar" type="button" role="tab">
                                <span>🇸🇦 العربية (Arabic)</span>
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link px-3 py-1 font-13" id="tab-en-btn" data-bs-toggle="pill" data-bs-target="#tab-en" type="button" role="tab">
                                <span>🇬🇧 English</span>
                            </button>
                        </li>
                    </ul>

                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" name="is_active" id="notif_is_active" value="1" {{ $template->is_active ? 'checked' : '' }}>
                        <label class="form-check-label font-13 fw-semibold" for="notif_is_active">{{ __('تفعيل الإشعار') }}</label>
                    </div>
                </div>
            </div>

            <!-- Subject & Content Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <div class="tab-content p-0" id="notifTabsContent">
                        <!-- Arabic Tab -->
                        <div class="tab-pane fade show active" id="tab-ar" role="tabpanel">
                            <div class="mb-4">
                                <label class="form-label font-14 fw-bold mb-2">{{ __('الموضوع') }} <span class="text-danger">*</span></label>
                                <input type="text" name="title_ar" id="title_ar" class="form-control" value="{{ old('title_ar', $ar?->title) }}" required placeholder="موضوع الإشعار...">
                            </div>

                            <div class="mb-4">
                                <label class="form-label font-14 fw-bold mb-2">{{ __('رسالة الإخطار') }} <span class="text-danger">*</span></label>
                                <textarea name="message_ar" id="message_ar" class="form-control font-14" rows="6" required placeholder="محتوى نص الإشعار...">{{ old('message_ar', $ar?->message) }}</textarea>
                            </div>

                            <div class="mb-2">
                                <label class="form-label font-13 text-muted mb-1">{{ __('رابط الإجراء (اختياري)') }}</label>
                                <input type="text" name="action_url_ar" class="form-control form-control-sm" value="{{ old('action_url_ar', $ar?->action_url) }}" placeholder="/tasks أو {action_url}">
                            </div>
                        </div>

                        <!-- English Tab -->
                        <div class="tab-pane fade" id="tab-en" role="tabpanel">
                            <div class="mb-4">
                                <label class="form-label font-14 fw-bold mb-2">{{ __('الموضوع') }}</label>
                                <input type="text" name="title_en" id="title_en" class="form-control" value="{{ old('title_en', $en?->title) }}" placeholder="Notification subject...">
                            </div>

                            <div class="mb-4">
                                <label class="form-label font-14 fw-bold mb-2">{{ __('رسالة الإخطار') }}</label>
                                <textarea name="message_en" id="message_en" class="form-control font-14" rows="6" placeholder="Notification message...">{{ old('message_en', $en?->message) }}</textarea>
                            </div>

                            <div class="mb-2">
                                <label class="form-label font-13 text-muted mb-1">{{ __('Action URL (Optional)') }}</label>
                                <input type="text" name="action_url_en" class="form-control form-control-sm" value="{{ old('action_url_en', $en?->action_url) }}" placeholder="/tasks or {action_url}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-light py-3 border-top d-flex align-items-center justify-content-between">
                    <button type="submit" class="btn btn-success d-flex align-items-center gap-1 shadow-sm px-4">
                        <i class="icon-base bx bx-save font-18"></i>
                        <span>{{ __('احفظ التغييرات') }}</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Right Column: Simple Clean Variables & Details (Like Screenshot) -->
        <div class="col-xl-4 col-lg-5">
            <!-- Variables List -->
            <div class="card shadow-sm border-0 mb-4 sticky-top" style="top: 20px; z-index: 10;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4 text-end" style="color: #0f172a;">
                        {{ __('المتغيرات') }}
                    </h5>

                    <div class="d-flex flex-column gap-2 text-end" style="max-height: 480px; overflow-y: auto;">
                        @forelse($template->variables ?? [] as $variable)
                            @php
                                $tag = str_starts_with($variable, '{') ? $variable : '{' . $variable . '}';
                                $nameClean = trim($variable, '{}');
                                $humanLabel = ucwords(str_replace('_', ' ', $nameClean));
                            @endphp
                            <div class="variable-simple-row d-flex align-items-center justify-content-end gap-1" onclick="copyNotifVariable('{{ $tag }}', this)" title="{{ __('اضغط للإدراج في نص الإشعار أو النسخ') }}">
                                <span class="variable-tag-text font-14">{{ $tag }}</span>
                                <span class="text-muted font-14">:</span>
                                <span class="variable-desc-text font-14">{{ $humanLabel }}</span>
                            </div>
                        @empty
                            <div class="text-muted font-12 text-center py-4">
                                {{ __('لا توجد متغيرات محددة') }}
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Template Read-only Details (Matching Screenshot) -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3 text-end" style="color: #0f172a;">
                        {{ __('تفاصيل القالب') }}
                    </h6>

                    <div class="mb-3 text-end">
                        <label class="form-label font-13 text-muted mb-1">{{ __('الاسم') }}</label>
                        <input type="text" class="form-control form-control-sm bg-light text-end" value="{{ $template->name_ar ?: $template->name_en }}" readonly>
                    </div>

                    <div class="mb-2 text-end">
                        <label class="form-label font-13 text-muted mb-1">{{ __('الكود البرمجي') }}</label>
                        <input type="text" class="form-control form-control-sm bg-light text-end font-monospace" value="{{ $template->code }}" readonly>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@section('page-script')
<script>
let currentLocale = 'ar';

function copyNotifVariable(tag, el) {
    // 1. Copy to clipboard
    navigator.clipboard.writeText(tag).then(() => {
        el.style.backgroundColor = '#dcfce7';
        setTimeout(() => {
            el.style.backgroundColor = '';
        }, 500);
    });

    // 2. Also insert directly into active message textarea at cursor
    const activeTextarea = currentLocale === 'ar' ? document.getElementById('message_ar') : document.getElementById('message_en');
    if (activeTextarea) {
        const startPos = activeTextarea.selectionStart;
        const endPos = activeTextarea.selectionEnd;
        const text = activeTextarea.value;
        activeTextarea.value = text.substring(0, startPos) + tag + text.substring(endPos, text.length);
        activeTextarea.focus();
        activeTextarea.selectionStart = startPos + tag.length;
        activeTextarea.selectionEnd = startPos + tag.length;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const tabArBtn = document.getElementById('tab-ar-btn');
    const tabEnBtn = document.getElementById('tab-en-btn');
    if (tabArBtn) {
        tabArBtn.addEventListener('shown.bs.tab', () => { currentLocale = 'ar'; });
    }
    if (tabEnBtn) {
        tabEnBtn.addEventListener('shown.bs.tab', () => { currentLocale = 'en'; });
    }
});
</script>
@endsection
