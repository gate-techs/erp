@extends('layouts/contentNavbarLayout')

@section('title', __('Edit Email Template') . ' - ' . $template->display_name)

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <i class="icon-base bx bx-edit-alt text-primary font-24"></i>
            <span>{{ __('Customize Email Template') }}: <span class="text-primary">{{ $template->display_name }}</span></span>
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('settings.index') }}">{{ __('System Settings') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('settings.email-templates.index') }}">{{ __('Email Templates') }}</a></li>
                <li class="breadcrumb-item active">{{ $template->code }}</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-secondary d-flex align-items-center gap-1" id="btnLivePreviewModal">
            <i class="icon-base bx bx-show font-16"></i>
            <span>{{ __('Live Preview') }}</span>
        </button>
        <button type="button" class="btn btn-outline-primary d-flex align-items-center gap-1" id="btnOpenTestModal">
            <i class="icon-base bx bx-paper-plane font-16"></i>
            <span>{{ __('Send Test Email') }}</span>
        </button>
        <a href="{{ route('settings.email-templates.index') }}" class="btn btn-secondary d-flex align-items-center gap-1">
            <i class="icon-base bx bx-arrow-back font-16"></i>
            <span>{{ __('Back') }}</span>
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="icon-base bx bx-check-circle me-1"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row g-4">
    <!-- Left Form Column -->
    <div class="col-xl-8 col-lg-7">
        <form action="{{ route('settings.email-templates.update', $template->id) }}" method="POST" id="templateEditForm">
            @csrf
            @method('PUT')

            <!-- Main Template Editor Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light py-3 border-bottom d-flex align-items-center justify-content-between">
                    <ul class="nav nav-pills card-header-pills" id="langTabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active d-flex align-items-center gap-1" id="tab-ar-btn" data-bs-toggle="pill" data-bs-target="#tab-ar" type="button" role="tab">
                                <span>🇸🇦 العربية (Arabic)</span>
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link d-flex align-items-center gap-1" id="tab-en-btn" data-bs-toggle="pill" data-bs-target="#tab-en" type="button" role="tab">
                                <span>🇬🇧 English (Optional)</span>
                            </button>
                        </li>
                    </ul>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ $template->is_active ? 'checked' : '' }}>
                        <label class="form-check-label font-13 fw-semibold" for="is_active">{{ __('Template Active') }}</label>
                    </div>
                </div>

                <div class="card-body p-4">
                    <div class="tab-content p-0" id="langTabsContent">
                        <!-- Arabic Tab Content -->
                        <div class="tab-pane fade show active" id="tab-ar" role="tabpanel">
                            <div class="mb-3">
                                <label class="form-label font-13 fw-semibold">{{ __('Template Name (Arabic)') }} <span class="text-danger">*</span></label>
                                <input type="text" name="name_ar" class="form-control" value="{{ old('name_ar', $template->name_ar) }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label font-13 fw-semibold">{{ __('Email Subject (Arabic)') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="icon-base bx bx-comment-detail"></i></span>
                                    <input type="text" name="subject_ar" id="subject_ar" class="form-control" value="{{ old('subject_ar', $template->subject_ar) }}" required placeholder="مثال: تم إسناد مهمة جديدة إليك: {task_title}">
                                </div>
                                <div class="form-text font-11 text-muted">{{ __('You can insert dynamic variables inside subject like {task_title} or {employee_name}') }}</div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <label class="form-label font-13 fw-semibold mb-0">{{ __('Email Body (Arabic - HTML Supported)') }} <span class="text-danger">*</span></label>
                                    <span class="badge bg-label-info font-11">{{ __('HTML / Cairo Font') }}</span>
                                </div>
                                <textarea name="body_ar" id="body_ar" class="form-control font-13" rows="12" style="font-family: monospace; direction: rtl;" required>{{ old('body_ar', $template->body_ar) }}</textarea>
                                <div class="form-text font-11 text-muted">{{ __('Basic HTML tags like &lt;p&gt;, &lt;strong&gt;, &lt;br&gt;, &lt;ul&gt;, &lt;li&gt;, &lt;div&gt; are fully supported.') }}</div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label font-13 fw-semibold">{{ __('Call-to-Action Button Text (Arabic)') }}</label>
                                    <input type="text" name="action_button_text_ar" id="action_button_text_ar" class="form-control" value="{{ old('action_button_text_ar', $template->action_button_text_ar) }}" placeholder="مثال: عرض المهمة الآن">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label font-13 fw-semibold">{{ __('Button Target URL Variable') }}</label>
                                    <input type="text" name="action_url_variable" class="form-control" value="{{ old('action_url_variable', $template->action_url_variable) }}" placeholder="مثال: {task_url}">
                                </div>
                            </div>
                        </div>

                        <!-- English Tab Content -->
                        <div class="tab-pane fade" id="tab-en" role="tabpanel">
                            <div class="mb-3">
                                <label class="form-label font-13 fw-semibold">{{ __('Template Name (English)') }}</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $template->name) }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label font-13 fw-semibold">{{ __('Email Subject (English)') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="icon-base bx bx-comment-detail"></i></span>
                                    <input type="text" name="subject_en" id="subject_en" class="form-control" value="{{ old('subject_en', $template->subject_en) }}" placeholder="e.g. New Task Assigned: {task_title}">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label font-13 fw-semibold">{{ __('Email Body (English - HTML Supported)') }}</label>
                                <textarea name="body_en" id="body_en" class="form-control font-13" rows="12" style="font-family: monospace; direction: ltr;">{{ old('body_en', $template->body_en) }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label font-13 fw-semibold">{{ __('Call-to-Action Button Text (English)') }}</label>
                                <input type="text" name="action_button_text_en" id="action_button_text_en" class="form-control" value="{{ old('action_button_text_en', $template->action_button_text_en) }}" placeholder="e.g. View Task Now">
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top">
                        <label class="form-label font-13 fw-semibold">{{ __('Internal Description / Notes') }}</label>
                        <textarea name="description" class="form-control form-control-sm" rows="2">{{ old('description', $template->description) }}</textarea>
                    </div>
                </div>

                <div class="card-footer bg-light py-3 border-top d-flex align-items-center justify-content-between">
                    <span class="text-muted font-12">
                        <i class="icon-base bx bx-info-circle me-1"></i>
                        {{ __('System Template Code') }}: <code>{{ $template->code }}</code>
                    </span>
                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm px-4">
                        <i class="icon-base bx bx-save font-18"></i>
                        <span>{{ __('Save Changes') }}</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Right Sidebar: Variables & Helpers -->
    <div class="col-xl-4 col-lg-5">
        <!-- Dynamic Placeholders Card -->
        <div class="card shadow-sm border-0 mb-4 sticky-top" style="top: 20px; z-index: 10;">
            <div class="card-header bg-light py-3 border-bottom d-flex align-items-center justify-content-between">
                <h6 class="fw-bold mb-0 d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-code-alt text-primary font-18"></i>
                    <span>{{ __('Available Dynamic Variables') }}</span>
                </h6>
                <span class="badge bg-label-primary font-11">{{ count($template->normalized_variables) }} {{ __('Tags') }}</span>
            </div>
            <div class="card-body p-3">
                <p class="text-muted font-12 mb-3">
                    {{ __('Click any variable to copy it to clipboard, then paste it inside the email body or subject.') }}
                </p>

                <div class="d-flex flex-column gap-2" style="max-height: 480px; overflow-y: auto;">
                    @forelse($template->normalized_variables as $tag => $label)
                        <div class="variable-item p-2 border rounded bg-light d-flex align-items-center justify-content-between transition-all" style="cursor: pointer;" onclick="copyVariable('{{ $tag }}', this)" title="{{ __('Click to copy') }}">
                            <div>
                                <code class="fw-bold text-primary font-12">{{ $tag }}</code>
                                <div class="text-muted font-11 mt-1">{{ is_string($label) ? $label : $tag }}</div>
                            </div>
                            <button type="button" class="btn btn-xs btn-outline-secondary copy-btn">
                                <i class="icon-base bx bx-copy font-14"></i>
                            </button>
                        </div>
                    @empty
                        <div class="text-muted font-12 text-center py-3">
                            {{ __('No specific variables configured.') }}
                        </div>
                    @endforelse
                </div>
            </div>
            <div class="card-footer bg-light py-2 border-top">
                <div class="d-flex align-items-center gap-1 text-muted font-11">
                    <i class="icon-base bx bx-bulb text-warning font-14"></i>
                    <span>{{ __('Brand colors and header/footer logos are applied automatically.') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Interactive Live Preview -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-light py-3 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-envelope-open text-primary font-22"></i>
                    <h5 class="modal-title fw-bold">{{ __('Live Email Preview') }}</h5>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-primary active" id="btnPreviewAr">العربية (AR)</button>
                        <button type="button" class="btn btn-outline-primary" id="btnPreviewEn">English (EN)</button>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body p-0">
                <div class="p-3 bg-white border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted font-12 fw-bold text-nowrap">{{ __('Subject') }}:</span>
                        <span class="fw-semibold text-dark font-13" id="liveSubjectText">-</span>
                    </div>
                </div>
                <div id="liveHtmlContainer" style="background: #eef2f6; padding: 20px; min-height: 400px; max-height: 540px; overflow-y: auto;">
                    <div class="text-center py-5 text-muted">
                        <div class="spinner-border text-primary spinner-border-sm me-2" role="status"></div>
                        <span>{{ __('Rendering preview...') }}</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer py-2 bg-light border-top">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Send Test Email -->
<div class="modal fade" id="testEmailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <form id="sendDirectTestForm">
                @csrf
                <div class="modal-header bg-light py-3 border-bottom">
                    <h5 class="modal-title fw-bold d-flex align-items-center gap-2">
                        <i class="icon-base bx bx-paper-plane text-primary font-20"></i>
                        <span>{{ __('Send Test Email') }}</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-info py-2 font-12 d-flex align-items-center gap-2 mb-3">
                        <i class="icon-base bx bx-info-circle font-16"></i>
                        <span>{{ __('Make sure to save your changes first if you updated the body.') }}</span>
                    </div>
                    <div>
                        <label class="form-label font-13 fw-semibold">{{ __('Recipient Email Address') }} <span class="text-danger">*</span></label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="icon-base bx bx-envelope"></i></span>
                            <input type="email" id="directRecipientEmail" name="email" class="form-control" placeholder="user@domain.com" required value="{{ auth()->user()->email ?? '' }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-2 bg-light border-top">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary btn-sm d-flex align-items-center gap-1" id="btnSubmitDirectTest">
                        <i class="icon-base bx bx-send font-16"></i>
                        <span>{{ __('Send Test') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
let currentLocale = 'ar';

function copyVariable(tag, el) {
    navigator.clipboard.writeText(tag).then(() => {
        const btn = el.querySelector('.copy-btn');
        const origHtml = btn.innerHTML;
        btn.innerHTML = '<i class="icon-base bx bx-check text-success font-14"></i>';
        el.classList.add('border-primary');
        setTimeout(() => {
            btn.innerHTML = origHtml;
            el.classList.remove('border-primary');
        }, 1500);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const previewModalEl = document.getElementById('previewModal');
    const previewModal = new bootstrap.Modal(previewModalEl);
    const testModalEl = document.getElementById('testEmailModal');
    const testModal = new bootstrap.Modal(testModalEl);

    function fetchLivePreview(locale) {
        currentLocale = locale;
        const container = document.getElementById('liveHtmlContainer');
        const subjectEl = document.getElementById('liveSubjectText');

        container.innerHTML = `
            <div class="text-center py-5 text-muted">
                <div class="spinner-border text-primary spinner-border-sm me-2" role="status"></div>
                <span>{{ __('Rendering preview...') }}</span>
            </div>
        `;

        const isAr = locale === 'ar';
        const bodyVal = isAr ? document.getElementById('body_ar').value : document.getElementById('body_en').value;
        const subjectVal = isAr ? document.getElementById('subject_ar').value : document.getElementById('subject_en').value;
        const btnVal = isAr ? document.getElementById('action_button_text_ar').value : document.getElementById('action_button_text_en').value;

        fetch(`{{ route('settings.email-templates.preview', $template->id) }}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                locale: locale,
                preview_body: bodyVal,
                preview_subject: subjectVal,
                preview_btn_text: btnVal
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                subjectEl.textContent = data.subject;
                container.innerHTML = data.html;
            } else {
                container.innerHTML = `<div class="alert alert-danger">{{ __('Failed to load preview.') }}</div>`;
            }
        })
        .catch(() => {
            container.innerHTML = `<div class="alert alert-danger">{{ __('Failed to load preview.') }}</div>`;
        });
    }

    document.getElementById('btnLivePreviewModal').addEventListener('click', function() {
        previewModal.show();
        fetchLivePreview(currentLocale);
    });

    document.getElementById('btnPreviewAr').addEventListener('click', function() {
        if (currentLocale !== 'ar') {
            this.classList.add('active');
            document.getElementById('btnPreviewEn').classList.remove('active');
            fetchLivePreview('ar');
        }
    });

    document.getElementById('btnPreviewEn').addEventListener('click', function() {
        if (currentLocale !== 'en') {
            this.classList.add('active');
            document.getElementById('btnPreviewAr').classList.remove('active');
            fetchLivePreview('en');
        }
    });

    document.getElementById('btnOpenTestModal').addEventListener('click', function() {
        testModal.show();
    });

    document.getElementById('sendDirectTestForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const email = document.getElementById('directRecipientEmail').value;
        const submitBtn = document.getElementById('btnSubmitDirectTest');

        submitBtn.disabled = true;
        submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> {{ __('Sending...') }}`;

        fetch(`{{ route('settings.email-templates.send-test', $template->id) }}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ email: email })
        })
        .then(res => res.json())
        .then(data => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = `<i class="icon-base bx bx-send font-16"></i> <span>{{ __('Send Test') }}</span>`;
            if (data.success) {
                testModal.hide();
                if (typeof toastr !== 'undefined') {
                    toastr.success(data.message);
                } else {
                    alert(data.message);
                }
            } else {
                alert(data.message || '{{ __('Failed to send test email') }}');
            }
        })
        .catch(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = `<i class="icon-base bx bx-send font-16"></i> <span>{{ __('Send Test') }}</span>`;
            alert('{{ __('Failed to send test email.') }}');
        });
    });
});
</script>
@endsection
