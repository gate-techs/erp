@extends('layouts/contentNavbarLayout')

@section('title', __('Create Email Template') . ' - ' . config('variables.templateName'))

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <i class="icon-base bx bx-plus-circle text-primary font-24"></i>
            <span>{{ __('Create New Email Template') }}</span>
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('settings.index') }}">{{ __('System Settings') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('settings.email-templates.index') }}">{{ __('Email Templates') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Create') }}</li>
            </ol>
        </nav>
    </div>
    <div>
        <a href="{{ route('settings.email-templates.index') }}" class="btn btn-secondary d-flex align-items-center gap-1">
            <i class="icon-base bx bx-arrow-back font-16"></i>
            <span>{{ __('Back to Templates') }}</span>
        </a>
    </div>
</div>

<form action="{{ route('settings.email-templates.store') }}" method="POST">
    @csrf
    <div class="row g-4">
        <!-- Main Form -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold mb-0 text-heading">{{ __('Template Details') }}</h6>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" checked>
                        <label class="form-check-label font-13 fw-semibold" for="is_active">{{ __('Active') }}</label>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label font-13 fw-semibold">{{ __('Unique Code / Identifier') }} <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control font-monospace" placeholder="e.g. project_milestone_reached" required value="{{ old('code') }}">
                            <div class="form-text font-11 text-muted">{{ __('Used by system triggers and backend events.') }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-13 fw-semibold">{{ __('Associated Module') }} <span class="text-danger">*</span></label>
                            <select name="module" class="form-select" required>
                                @foreach($modules as $mKey => $mName)
                                    <option value="{{ $mKey }}" {{ old('module') === $mKey ? 'selected' : '' }}>{{ $mName }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label font-13 fw-semibold">{{ __('Template Title (Arabic)') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name_ar" class="form-control" placeholder="مثال: إشعار تسليم مرحلة المشروع" required value="{{ old('name_ar') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-13 fw-semibold">{{ __('Template Title (English)') }}</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Project Milestone Notice" value="{{ old('name') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-13 fw-semibold">{{ __('Email Subject (Arabic)') }} <span class="text-danger">*</span></label>
                        <input type="text" name="subject_ar" class="form-control" placeholder="مثال: تم إنجاز مرحلة هامة في مشروع {project_name}" required value="{{ old('subject_ar') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-13 fw-semibold">{{ __('Email Body (Arabic - HTML)') }} <span class="text-danger">*</span></label>
                        <textarea name="body_ar" class="form-control font-13" rows="10" style="font-family: monospace; direction: rtl;" required placeholder="<p>مرحباً <strong>{employee_name}</strong>،</p><p>نود إعلامكم بأنه تم إنجاز المرحلة التالية بنجاح...</p>">{{ old('body_ar') }}</textarea>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label font-13 fw-semibold">{{ __('Action Button Text (Arabic)') }}</label>
                            <input type="text" name="action_button_text_ar" class="form-control" placeholder="مثال: استعراض التفاصيل" value="{{ old('action_button_text_ar') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-13 fw-semibold">{{ __('Action URL Variable') }}</label>
                            <input type="text" name="action_url_variable" class="form-control" placeholder="مثال: {system_url}" value="{{ old('action_url_variable') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-13 fw-semibold">{{ __('Internal Description') }}</label>
                        <textarea name="description" class="form-control form-control-sm" rows="2" placeholder="وصف استخدام هذا القالب...">{{ old('description') }}</textarea>
                    </div>
                </div>

                <div class="card-footer bg-light py-3 border-top d-flex justify-content-end gap-2">
                    <a href="{{ route('settings.email-templates.index') }}" class="btn btn-secondary btn-sm">{{ __('Cancel') }}</a>
                    <button type="submit" class="btn btn-primary btn-sm d-flex align-items-center gap-1 shadow-sm px-4">
                        <i class="icon-base bx bx-check font-18"></i>
                        <span>{{ __('Save & Create Template') }}</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar: Custom Variables Definition -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light py-3 border-bottom">
                    <h6 class="fw-bold mb-0 d-flex align-items-center gap-2">
                        <i class="icon-base bx bx-code-block text-primary font-18"></i>
                        <span>{{ __('Custom Variables Definition') }}</span>
                    </h6>
                </div>
                <div class="card-body p-3">
                    <p class="text-muted font-12 mb-2">
                        {{ __('Enter one variable per line with optional explanation using format: tag: description') }}
                    </p>
                    <textarea name="variables" class="form-control font-monospace font-12" rows="10" placeholder="{employee_name}: اسم الموظف&#10;{project_name}: اسم المشروع&#10;{system_url}: رابط النظام">{{ old('variables', "{employee_name}: اسم الموظف\n{company_name}: اسم المنشأة\n{system_url}: رابط النظام") }}</textarea>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
