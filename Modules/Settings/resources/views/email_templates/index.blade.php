@extends('layouts/contentNavbarLayout')

@section('title', __('Email Templates Settings') . ' - ' . config('variables.templateName'))

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <i class="icon-base bx bx-envelope text-primary font-24"></i>
            <span>{{ __('Email Templates Settings') }}</span>
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('settings.index') }}">{{ __('System Settings') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Email Templates') }}</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('settings.email-templates.create') }}" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm">
            <i class="icon-base bx bx-plus font-18"></i>
            <span>{{ __('Create Template') }}</span>
        </a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-sm-6 col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted font-12 d-block mb-1">{{ __('Total Templates') }}</span>
                    <h4 class="fw-bold mb-0 text-heading">{{ $stats['total'] ?? 0 }}</h4>
                </div>
                <div class="avatar bg-label-primary rounded p-2">
                    <i class="icon-base bx bx-envelope-open font-22 text-primary"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted font-12 d-block mb-1">{{ __('Active Templates') }}</span>
                    <h4 class="fw-bold mb-0 text-success">{{ $stats['active'] ?? 0 }}</h4>
                </div>
                <div class="avatar bg-label-success rounded p-2">
                    <i class="icon-base bx bx-check-circle font-22 text-success"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted font-12 d-block mb-1">{{ __('Inactive Templates') }}</span>
                    <h4 class="fw-bold mb-0 text-secondary">{{ $stats['inactive'] ?? 0 }}</h4>
                </div>
                <div class="avatar bg-label-secondary rounded p-2">
                    <i class="icon-base bx bx-pause-circle font-22 text-secondary"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted font-12 d-block mb-1">{{ __('Modules Connected') }}</span>
                    <h4 class="fw-bold mb-0 text-info">{{ count($stats['modules'] ?? []) }}</h4>
                </div>
                <div class="avatar bg-label-info rounded p-2">
                    <i class="icon-base bx bx-grid-alt font-22 text-info"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Module Filter Tabs & Search -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-3">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <!-- Filter Pills -->
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('settings.email-templates.index') }}" class="btn btn-sm {{ $selectedModule === 'all' ? 'btn-primary' : 'btn-outline-secondary' }}">
                    <i class="icon-base bx bx-layer me-1 font-16"></i>
                    {{ __('All Modules') }} ({{ $stats['total'] ?? 0 }})
                </a>
                <a href="{{ route('settings.email-templates.index', ['module' => 'tasks']) }}" class="btn btn-sm {{ $selectedModule === 'tasks' ? 'btn-primary' : 'btn-outline-secondary' }}">
                    <i class="icon-base bx bx-task me-1 font-16"></i>
                    {{ __('Tasks & Projects') }} ({{ $stats['modules']['tasks'] ?? 0 }})
                </a>
                <a href="{{ route('settings.email-templates.index', ['module' => 'hr']) }}" class="btn btn-sm {{ $selectedModule === 'hr' ? 'btn-primary' : 'btn-outline-secondary' }}">
                    <i class="icon-base bx bx-user-pin me-1 font-16"></i>
                    {{ __('Human Resources') }} ({{ $stats['modules']['hr'] ?? 0 }})
                </a>
                <a href="{{ route('settings.email-templates.index', ['module' => 'accounting']) }}" class="btn btn-sm {{ $selectedModule === 'accounting' ? 'btn-primary' : 'btn-outline-secondary' }}">
                    <i class="icon-base bx bx-calculator me-1 font-16"></i>
                    {{ __('Accounting') }} ({{ $stats['modules']['accounting'] ?? 0 }})
                </a>
            </div>

            <!-- Search Filter Field -->
            <div style="min-width: 260px;">
                <div class="input-group input-group-merge input-group-sm">
                    <span class="input-group-text"><i class="icon-base bx bx-search font-16"></i></span>
                    <input type="text" id="templateSearchInput" class="form-control" placeholder="{{ __('Search templates...') }}">
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Templates Cards Grid -->
<div class="row g-4" id="templatesContainer">
    @forelse($templates as $tpl)
        @php
            $moduleStyles = [
                'tasks'      => ['bg' => 'bg-label-primary', 'text' => 'text-primary', 'badge' => 'bg-label-primary', 'icon' => 'bx-task', 'title' => __('Tasks')],
                'hr'         => ['bg' => 'bg-label-success', 'text' => 'text-success', 'badge' => 'bg-label-success', 'icon' => 'bx-user-pin', 'title' => __('HR')],
                'accounting' => ['bg' => 'bg-label-warning', 'text' => 'text-warning', 'badge' => 'bg-label-warning', 'icon' => 'bx-dollar-circle', 'title' => __('Accounting')],
                'general'    => ['bg' => 'bg-label-info',    'text' => 'text-info',    'badge' => 'bg-label-info',    'icon' => 'bx-cog', 'title' => __('General')],
            ];
            $style = $moduleStyles[$tpl->module] ?? $moduleStyles['general'];
        @endphp
        <div class="col-xl-4 col-md-6 col-12 template-item" data-name="{{ strtolower($tpl->name_ar . ' ' . $tpl->name . ' ' . $tpl->code) }}">
            <div class="card h-100 shadow-sm border-0 position-relative transition-all">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <!-- Header & Active Switch -->
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar avatar-md {{ $style['bg'] }} rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="icon-base bx {{ $style['icon'] }} font-22 {{ $style['text'] }}"></i>
                                </div>
                                <div>
                                    <span class="badge {{ $style['badge'] }} font-11">{{ $style['title'] }}</span>
                                    <code class="d-block font-11 text-muted mt-1">{{ $tpl->code }}</code>
                                </div>
                            </div>
                            <div class="form-check form-switch mb-0" title="{{ __('Toggle Template Status') }}">
                                <input class="form-check-input toggle-email-status" type="checkbox" data-id="{{ $tpl->id }}" {{ $tpl->is_active ? 'checked' : '' }}>
                            </div>
                        </div>

                        <!-- Title & Description -->
                        <h5 class="fw-bold mb-1 text-heading">{{ $tpl->display_name }}</h5>
                        <p class="text-muted font-13 mb-3" style="min-height: 38px;">
                            {{ $tpl->description ?: __('Standard dynamic notification email template.') }}
                        </p>

                        <!-- Subject preview -->
                        <div class="bg-light p-2 rounded mb-3 border">
                            <span class="text-muted font-11 d-block fw-semibold mb-1">
                                <i class="icon-base bx bx-comment-detail me-1"></i>{{ __('Subject Preview') }}:
                            </span>
                            <div class="font-12 text-truncate text-dark fw-medium" title="{{ $tpl->subject_ar }}">
                                {{ $tpl->subject_ar }}
                            </div>
                        </div>

                        <!-- Variables Pills Preview -->
                        <div class="d-flex flex-wrap gap-1 mb-4">
                            @php 
                                $normVars = $tpl->normalized_variables;
                                $varCount = 0; 
                            @endphp
                            @foreach($normVars as $tag => $label)
                                @if($varCount < 4)
                                    <span class="badge bg-label-secondary font-11" title="{{ is_string($label) ? $label : $tag }}">{{ is_string($tag) ? $tag : '' }}</span>
                                    @php $varCount++; @endphp
                                @endif
                            @endforeach
                            @if(count($normVars) > 4)
                                <span class="badge bg-label-secondary font-11">+{{ count($normVars) - 4 }}</span>
                            @endif
                        </div>
                    </div>

                    <!-- Action Buttons Footer -->
                    <div class="d-flex gap-2 pt-3 border-top">
                        <button type="button" class="btn btn-outline-secondary btn-sm flex-grow-1 d-flex align-items-center justify-content-center gap-1 btn-preview-template" data-id="{{ $tpl->id }}" data-name="{{ $tpl->display_name }}">
                            <i class="icon-base bx bx-show font-16"></i>
                            <span>{{ __('Preview') }}</span>
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm d-flex align-items-center justify-content-center btn-test-modal" data-id="{{ $tpl->id }}" data-name="{{ $tpl->display_name }}" title="{{ __('Send Test Email') }}">
                            <i class="icon-base bx bx-paper-plane font-16"></i>
                        </button>
                        <a href="{{ route('settings.email-templates.edit', $tpl->id) }}" class="btn btn-primary btn-sm flex-grow-1 d-flex align-items-center justify-content-center gap-1 shadow-sm">
                            <i class="icon-base bx bx-edit-alt font-16"></i>
                            <span>{{ __('Customize') }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card shadow-sm border-0 text-center py-5">
                <div class="card-body">
                    <i class="icon-base bx bx-envelope-open font-48 text-muted mb-3 d-block"></i>
                    <h5 class="fw-bold">{{ __('No email templates found') }}</h5>
                    <p class="text-muted font-13">{{ __('No templates available for the selected module filter.') }}</p>
                    <a href="{{ route('settings.email-templates.index') }}" class="btn btn-primary btn-sm">
                        {{ __('View All Templates') }}
                    </a>
                </div>
            </div>
        </div>
    @endforelse
</div>

<!-- Modal: Interactive Live Preview -->
<div class="modal fade" id="previewTemplateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-light py-3 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-envelope-open text-primary font-22"></i>
                    <h5 class="modal-title fw-bold" id="previewModalTitle">{{ __('Email Preview') }}</h5>
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
                <!-- Subject Header Strip -->
                <div class="p-3 bg-white border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted font-12 fw-bold text-nowrap">{{ __('Subject') }}:</span>
                        <span class="fw-semibold text-dark font-13" id="previewSubjectText">-</span>
                    </div>
                </div>
                <!-- Email Render Container -->
                <div id="previewHtmlContainer" style="background: #eef2f6; padding: 20px; min-height: 380px; max-height: 520px; overflow-y: auto;">
                    <div class="text-center py-5 text-muted">
                        <div class="spinner-border text-primary spinner-border-sm me-2" role="status"></div>
                        <span>{{ __('Loading preview...') }}</span>
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
<div class="modal fade" id="sendTestModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <form id="sendTestForm">
                @csrf
                <input type="hidden" id="testTemplateId" name="template_id">
                <div class="modal-header bg-light py-3 border-bottom">
                    <h5 class="modal-title fw-bold d-flex align-items-center gap-2">
                        <i class="icon-base bx bx-paper-plane text-primary font-20"></i>
                        <span>{{ __('Send Test Email') }}</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted font-13 mb-3">
                        {{ __('Send a test email with simulated placeholder data to verify how it appears in the recipient inbox.') }}
                    </p>
                    <div class="mb-3">
                        <label class="form-label font-13 fw-semibold">{{ __('Template Name') }}</label>
                        <input type="text" id="testTemplateName" class="form-control form-control-sm bg-light" readonly>
                    </div>
                    <div>
                        <label class="form-label font-13 fw-semibold">{{ __('Recipient Email Address') }} <span class="text-danger">*</span></label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="icon-base bx bx-envelope"></i></span>
                            <input type="email" id="testRecipientEmail" name="email" class="form-control" placeholder="user@domain.com" required value="{{ auth()->user()->email ?? '' }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-2 bg-light border-top">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary btn-sm d-flex align-items-center gap-1" id="btnSubmitTest">
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
document.addEventListener('DOMContentLoaded', function() {
    let currentPreviewId = null;
    let currentPreviewLocale = 'ar';

    // Search Filtering
    const searchInput = document.getElementById('templateSearchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            document.querySelectorAll('.template-item').forEach(item => {
                const text = item.getAttribute('data-name');
                if (text.includes(query)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }

    // Toggle Active Status
    document.querySelectorAll('.toggle-email-status').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const id = this.getAttribute('data-id');
            const switchEl = this;

            fetch(`{{ url('settings/email-templates') }}/${id}/toggle-status`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (typeof toastr !== 'undefined') {
                        toastr.success(data.message);
                    }
                } else {
                    switchEl.checked = !switchEl.checked;
                }
            })
            .catch(() => {
                switchEl.checked = !switchEl.checked;
            });
        });
    });

    // Preview Modal Logic
    const previewModalEl = document.getElementById('previewTemplateModal');
    const previewModal = new bootstrap.Modal(previewModalEl);

    function loadPreview(id, locale) {
        const container = document.getElementById('previewHtmlContainer');
        const subjectEl = document.getElementById('previewSubjectText');
        
        container.innerHTML = `
            <div class="text-center py-5 text-muted">
                <div class="spinner-border text-primary spinner-border-sm me-2" role="status"></div>
                <span>{{ __('Rendering template...') }}</span>
            </div>
        `;

        fetch(`{{ url('settings/email-templates') }}/${id}/preview?locale=${locale}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
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

    document.querySelectorAll('.btn-preview-template').forEach(btn => {
        btn.addEventListener('click', function() {
            currentPreviewId = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            document.getElementById('previewModalTitle').textContent = `{{ __('Preview') }}: ${name}`;
            currentPreviewLocale = 'ar';
            document.getElementById('btnPreviewAr').classList.add('active');
            document.getElementById('btnPreviewEn').classList.remove('active');
            previewModal.show();
            loadPreview(currentPreviewId, currentPreviewLocale);
        });
    });

    document.getElementById('btnPreviewAr').addEventListener('click', function() {
        if (currentPreviewLocale !== 'ar' && currentPreviewId) {
            currentPreviewLocale = 'ar';
            this.classList.add('active');
            document.getElementById('btnPreviewEn').classList.remove('active');
            loadPreview(currentPreviewId, 'ar');
        }
    });

    document.getElementById('btnPreviewEn').addEventListener('click', function() {
        if (currentPreviewLocale !== 'en' && currentPreviewId) {
            currentPreviewLocale = 'en';
            this.classList.add('active');
            document.getElementById('btnPreviewAr').classList.remove('active');
            loadPreview(currentPreviewId, 'en');
        }
    });

    // Send Test Email Modal
    const testModalEl = document.getElementById('sendTestModal');
    const testModal = new bootstrap.Modal(testModalEl);

    document.querySelectorAll('.btn-test-modal').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            document.getElementById('testTemplateId').value = id;
            document.getElementById('testTemplateName').value = name;
            testModal.show();
        });
    });

    document.getElementById('sendTestForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('testTemplateId').value;
        const email = document.getElementById('testRecipientEmail').value;
        const submitBtn = document.getElementById('btnSubmitTest');

        submitBtn.disabled = true;
        submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> {{ __('Sending...') }}`;

        fetch(`{{ url('settings/email-templates') }}/${id}/test`, {
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
            alert('{{ __('Failed to send test email. Please check server logs.') }}');
        });
    });
});
</script>
@endsection
