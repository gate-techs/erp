@extends('layouts/contentNavbarLayout')

@section('title', __('AI Settings') . ' - ' . config('variables.templateName'))

@section('content')
    <!-- Page Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
                <i class="bx bx-brain text-primary fs-3"></i>
                <span>{{ __('AI Settings') }}</span>
            </h4>
            <p class="text-muted mb-0 small">{{ __('Configure providers and select the provider used by each AI task.') }}</p>
        </div>

        <button type="button" class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#providerModal">
            <i class="bx bx-plus font-18"></i>
            <span>{{ __('Add Provider') }}</span>
        </button>
    </div>

    <!-- Alerts -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible d-flex align-items-center mb-4" role="alert">
            <i class="bx bx-check-circle fs-4 me-2"></i>
            <div class="flex-grow-1">{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible d-flex align-items-center mb-4" role="alert">
            <i class="bx bx-error-circle fs-4 me-2"></i>
            <div class="flex-grow-1">{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (isset($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissible mb-4" role="alert">
            <h6 class="alert-heading fw-bold mb-1">{{ __('Please review the following errors:') }}</h6>
            <ul class="mb-0 ps-3 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Clean Tab Navigation -->
    <div class="nav-align-top mb-4">
        <ul class="nav nav-pills nav-tabs-grid mb-3 gap-2" role="tablist">
            <li class="nav-item">
                <button type="button" class="nav-link active d-flex align-items-center gap-2 py-2 px-3" role="tab" data-bs-toggle="tab" data-bs-target="#tab-providers">
                    <i class="bx bx-server font-18"></i>
                    <span>{{ __('AI Providers') }}</span>
                    <span class="badge rounded-pill bg-label-primary ms-1">{{ $providers->count() }}</span>
                </button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link d-flex align-items-center gap-2 py-2 px-3" role="tab" data-bs-toggle="tab" data-bs-target="#tab-routing">
                    <i class="bx bx-git-repo-forked font-18"></i>
                    <span>{{ __('AI Task Routing') }}</span>
                    <span class="badge rounded-pill bg-label-secondary ms-1">{{ $tasks->count() }}</span>
                </button>
            </li>
            @if (config('ai.log_usage', false))
            <li class="nav-item">
                <button type="button" class="nav-link d-flex align-items-center gap-2 py-2 px-3" role="tab" data-bs-toggle="tab" data-bs-target="#tab-usage">
                    <i class="bx bx-bar-chart-alt-2 font-18"></i>
                    <span>{{ __('Usage & Statistics') }}</span>
                    @if ($totalRequestsAll > 0)
                        <span class="badge rounded-pill bg-label-info ms-1">{{ number_format($totalTokensAll) }} {{ __('Tokens') }}</span>
                    @endif
                </button>
            </li>
            @endif
        </ul>

        <div class="tab-content p-0 bg-transparent shadow-none border-0">
            <!-- TAB 1: AI PROVIDERS -->
            <div class="tab-pane fade show active" id="tab-providers" role="tabpanel">
                <div class="card border shadow-none">
                    <div class="card-header border-bottom py-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">{{ __('AI Providers') }}</h5>
                            <small class="text-muted">{{ __('Manage your AI provider connections and API keys.') }}</small>
                        </div>
                    </div>

                    @if ($providers->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 text-nowrap">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 25%;">{{ __('Name') }}</th>
                                        <th style="width: 20%;">{{ __('Driver') }}</th>
                                        <th style="width: 20%;">{{ __('Model') }}</th>
                                        <th style="width: 15%;">{{ __('Status') }}</th>
                                        <th style="width: 20%;" class="text-end">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($providers as $provider)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="avatar avatar-xs bg-label-{{ $provider->driver_color }} rounded d-flex align-items-center justify-content-center">
                                                        <i class="{{ $provider->driver_icon }}"></i>
                                                    </div>
                                                    <div>
                                                        <strong class="text-body d-block">{{ $provider->name }}</strong>
                                                        <code class="small text-muted" dir="ltr">{{ $provider->code }}</code>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-label-{{ $provider->driver_color }}">
                                                    {{ $provider->driver_label }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-body border font-monospace" dir="ltr">
                                                    {{ $provider->model ?: __('Provider default') }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge {{ $provider->is_active ? 'bg-label-success' : 'bg-label-secondary' }}">
                                                    {{ $provider->is_active ? __('Active') : __('Inactive') }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                @php
                                                    $providerData = $provider->only(['id', 'code', 'name', 'driver', 'model', 'base_url', 'is_active']);
                                                @endphp
                                                <div class="d-flex justify-content-end gap-1">
                                                    <button type="button" class="btn btn-sm btn-icon btn-outline-primary js-edit-provider" data-provider="{{ e(json_encode($providerData)) }}" data-bs-toggle="modal" data-bs-target="#providerModal" title="{{ __('Edit') }}">
                                                        <i class="bx bx-edit-alt"></i>
                                                    </button>
                                                    <form method="POST" action="{{ route('settings.ai.providers.destroy', $provider) }}" onsubmit="return confirm('{{ __('Delete this provider?') }}')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-icon btn-outline-danger" title="{{ __('Delete') }}">
                                                            <i class="bx bx-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="card-body text-center py-5">
                            <div class="avatar avatar-lg bg-label-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                                <i class="bx bx-chip fs-2 text-primary"></i>
                            </div>
                            <h6 class="fw-bold mb-1">{{ __('No AI providers configured yet.') }}</h6>
                            <p class="text-muted small mb-3">{{ __('Add your first AI provider like Google Gemini or OpenAI to start using AI features.') }}</p>
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#providerModal">
                                <i class="bx bx-plus me-1"></i>{{ __('Add Provider') }}
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- TAB 2: TASK ROUTING -->
            <div class="tab-pane fade" id="tab-routing" role="tabpanel">
                <div class="card border shadow-none">
                    <div class="card-header border-bottom py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div>
                            <h5 class="card-title mb-0">{{ __('AI Task Routing') }}</h5>
                            <small class="text-muted">{{ __('Select which provider executes each system task.') }}</small>
                        </div>
                        <small class="text-muted text-nowrap">
                            <i class="bx bx-info-circle me-1"></i>{{ __('System Fallback') }}: <strong class="text-primary font-monospace" dir="ltr">{{ ucfirst(config('ai.default_provider', 'gemini')) }}</strong>
                        </small>
                    </div>

                    <div class="table-responsive">
                        {{-- min-width: scrolls horizontally on phones instead of squeezing the description into a narrow column --}}
                        <table class="table table-hover align-middle mb-0" style="min-width: 820px;">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 25%;">{{ __('Task') }}</th>
                                    <th style="width: 35%;">{{ __('Description') }}</th>
                                    <th style="width: 20%;">{{ __('Current Provider') }}</th>
                                    <th style="width: 20%;" class="text-end">{{ __('Change Provider') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tasks as $task)
                                    @php
                                        $assigned = $task->providers->first();
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="avatar avatar-xs bg-label-{{ $task->color }} rounded d-flex align-items-center justify-content-center">
                                                    <i class="{{ $task->icon }}"></i>
                                                </div>
                                                <strong class="text-body text-nowrap">{{ __($task->name) }}</strong>
                                            </div>
                                        </td>
                                        <td style="min-width: 280px;">
                                            <span class="text-muted small">{{ __($task->description) }}</span>
                                        </td>
                                        <td>
                                            @if ($assigned)
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge bg-label-{{ $assigned->driver_color }} d-inline-flex align-items-center gap-1">
                                                        <i class="{{ $assigned->driver_icon }}"></i>
                                                        {{ $assigned->name }}
                                                    </span>
                                                    <form method="POST" action="{{ route('settings.ai.tasks.providers.remove', [$task, $assigned]) }}" onsubmit="return confirm('{{ __('Reset to Default') }}?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-link text-danger p-0 ms-1" title="{{ __('Reset to Default') }}">
                                                            <i class="bx bx-x font-16"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            @else
                                                <span class="badge bg-label-secondary">
                                                    {{ __('System Default') }} ({{ ucfirst(config('ai.default_provider', 'gemini')) }})
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @if ($providers->where('is_active', true)->isNotEmpty())
                                                <form method="POST" action="{{ route('settings.ai.tasks.providers.assign', $task) }}" class="d-inline-flex align-items-center gap-1">
                                                    @csrf
                                                    <input type="hidden" name="priority" value="1">
                                                    <input type="hidden" name="is_default" value="1">

                                                    <select name="provider_id" class="form-select form-select-sm" style="min-width: 140px;" required>
                                                        <option value="">{{ __('Select provider') }}</option>
                                                        @foreach ($providers->where('is_active', true) as $p)
                                                            <option value="{{ $p->id }}" {{ $assigned && $assigned->id === $p->id ? 'selected' : '' }}>
                                                                {{ $p->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <button type="submit" class="btn btn-sm btn-primary py-1 px-2" title="{{ __('Save') }}">
                                                        <i class="bx bx-check"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <small class="text-muted">{{ __('Add a provider first') }}</small>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if (config('ai.log_usage', false))
            <!-- TAB 3: USAGE & STATISTICS -->
            <div class="tab-pane fade" id="tab-usage" role="tabpanel">
                <div class="card border shadow-none mb-4">
                    <div class="card-header border-bottom py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div>
                            <h5 class="card-title mb-0">{{ __('All Models Usage Breakdown') }}</h5>
                            <small class="text-muted">{{ __('Tokens and requests consumption tracked per model.') }}</small>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <span class="badge bg-label-primary px-3 py-2">
                                <i class="bx bx-transfer me-1"></i>{{ __('Total Requests') }}: <strong>{{ number_format($totalRequestsAll) }}</strong>
                            </span>
                            <span class="badge bg-label-info px-3 py-2">
                                <i class="bx bx-coin me-1"></i>{{ __('Total Tokens') }}: <strong>{{ number_format($totalTokensAll) }}</strong>
                            </span>
                        </div>
                    </div>

                    @if ($usageByModel->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 25%;">{{ __('Model') }}</th>
                                        <th style="width: 15%;">{{ __('Driver') }}</th>
                                        <th style="width: 15%;">{{ __('Total Requests') }}</th>
                                        <th style="width: 15%;">{{ __('Input Tokens') }}</th>
                                        <th style="width: 15%;">{{ __('Output Tokens') }}</th>
                                        <th style="width: 15%;">{{ __('Total Tokens') }}</th>
                                        <th style="width: 15%;" class="text-end">{{ __('Last Used') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($usageByModel as $usage)
                                        <tr>
                                            <td>
                                                <span class="fw-bold font-monospace text-body" dir="ltr">
                                                    {{ $usage->model ?: __('Provider default') }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-label-primary">
                                                    {{ ucfirst($usage->driver) }}
                                                </span>
                                            </td>
                                            <td>
                                                <strong>{{ number_format($usage->total_requests) }}</strong>
                                            </td>
                                            <td>
                                                <span class="text-muted font-monospace">{{ number_format($usage->total_prompt_tokens) }}</span>
                                            </td>
                                            <td>
                                                <span class="text-muted font-monospace">{{ number_format($usage->total_completion_tokens) }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-label-info font-monospace fs-6">
                                                    {{ number_format($usage->total_tokens) }}
                                                </span>
                                                @if ($usage->total_characters > 0)
                                                    <small class="text-muted d-block font-monospace">({{ number_format($usage->total_characters) }} {{ __('Characters Translated') }})</small>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <small class="text-muted">
                                                    {{ $usage->last_used_at ? \Carbon\Carbon::parse($usage->last_used_at)->diffForHumans() : '-' }}
                                                </small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Recent Activity Log Table -->
                        @if ($recentLogs->isNotEmpty())
                            <div class="card-header border-top border-bottom py-3 bg-light">
                                <h6 class="mb-0 fw-bold d-flex align-items-center gap-2">
                                    <i class="bx bx-history text-muted"></i>
                                    <span>{{ __('Recent Activity') }}</span>
                                </h6>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{ __('Date') }}</th>
                                            <th>{{ __('Task') }}</th>
                                            <th>{{ __('Model') }}</th>
                                            <th>{{ __('Tokens') }}</th>
                                            <th>{{ __('User') }}</th>
                                            <th class="text-end">{{ __('Status') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recentLogs as $log)
                                            <tr>
                                                <td class="small text-muted font-monospace">
                                                    {{ $log->created_at->format('Y-m-d H:i') }}
                                                </td>
                                                <td>
                                                    <span class="badge bg-label-secondary small">
                                                        {{ $log->task_code ? __($log->task_code) : '-' }}
                                                    </span>
                                                </td>
                                                <td class="font-monospace small" dir="ltr">
                                                    {{ $log->model ?: $log->driver }}
                                                </td>
                                                <td class="font-monospace small">
                                                    {{ number_format($log->total_tokens) }}
                                                    @if ($log->characters_count > 0)
                                                        <small class="text-muted">({{ number_format($log->characters_count) }} {{ __('chars') }})</small>
                                                    @endif
                                                </td>
                                                <td class="small text-muted">
                                                    {{ $log->user?->name ?: __('System') }}
                                                </td>
                                                <td class="text-end">
                                                    @if ($log->status === 'success')
                                                        <span class="badge bg-label-success small">{{ __('Success') }}</span>
                                                    @else
                                                        <span class="badge bg-label-danger small" title="{{ $log->error_message }}">{{ __('Failed') }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    @else
                        <div class="card-body text-center py-5">
                            <div class="avatar avatar-lg bg-label-info rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                                <i class="bx bx-line-chart fs-2 text-info"></i>
                            </div>
                            <h6 class="fw-bold mb-1">{{ __('No AI usage recorded yet.') }}</h6>
                            <p class="text-muted small mb-0">{{ __('Usage metrics and token counts will appear here automatically when AI features are used.') }}</p>
                        </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Provider Modal (Add / Edit) -->
    <div class="modal fade" id="providerModal" tabindex="-1" aria-labelledby="providerModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" action="{{ route('settings.ai.providers.store') }}" class="modal-content border-0 shadow" id="providerForm">
                @csrf
                <div id="providerMethod"></div>

                <div class="modal-header border-bottom py-3">
                    <h5 class="modal-title d-flex align-items-center gap-2" id="providerModalTitle">
                        <i class="bx bx-brain text-primary fs-4" id="providerModalIcon"></i>
                        <span id="providerModalHeading">{{ __('Add Provider') }}</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body py-3">
                    <!-- Main Fields -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="providerName">{{ __('Name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="providerName" class="form-control" placeholder="{{ __('Display name e.g. Google Gemini') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="providerDriver">{{ __('Driver') }} <span class="text-danger">*</span></label>
                        <select name="driver" id="providerDriver" class="form-select" required>
                            @foreach ($drivers as $driver)
                                @php
                                    $driverLabels = [
                                        'gemini' => 'Google Gemini',
                                        'openai' => 'OpenAI (ChatGPT)',
                                        'deepl' => 'DeepL Translator',
                                    ];
                                    $dLabel = $driverLabels[$driver] ?? ucfirst($driver);
                                @endphp
                                <option value="{{ $driver }}">{{ $dLabel }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="providerApiKey">{{ __('API Key') }}</label>
                        <div class="input-group">
                            <input type="password" name="api_key" id="providerApiKey" class="form-control font-monospace" dir="ltr" autocomplete="new-password" placeholder="{{ __('Enter API Key') }}">
                            <button class="btn btn-outline-secondary" type="button" id="toggleApiKeyBtn">
                                <i class="bx bx-show" id="toggleApiKeyIcon"></i>
                            </button>
                        </div>
                        <small class="form-text text-muted" id="apiKeyHelp">{{ __('API keys are encrypted before being stored.') }}</small>
                    </div>

                    <!-- Collapsible Advanced Settings (Keeps UI clean!) -->
                    <div class="mb-3">
                        <button type="button" class="btn btn-sm btn-link text-decoration-none p-0 d-flex align-items-center gap-1" data-bs-toggle="collapse" data-bs-target="#advancedSettingsArea" aria-expanded="false">
                            <i class="bx bx-cog font-16"></i>
                            <span>{{ __('Advanced Settings') }}</span>
                            <i class="bx bx-chevron-down font-16"></i>
                        </button>

                        <div class="collapse mt-3 p-3 bg-light rounded border" id="advancedSettingsArea">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold" for="providerCode">{{ __('Code') }} <span class="text-danger">*</span></label>
                                <input type="text" name="code" id="providerCode" class="form-control form-control-sm font-monospace" dir="ltr" placeholder="gemini" required>
                                <small class="form-text text-muted small">{{ __('Unique identifier e.g. gemini, openai') }}</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold" for="providerModel">{{ __('Model') }}</label>
                                <input type="text" name="model" id="providerModel" class="form-control form-control-sm font-monospace" dir="ltr" placeholder="gemini-3.6-flash">
                            </div>

                            <div class="mb-0">
                                <label class="form-label small fw-semibold" for="providerBaseUrl">{{ __('Base URL') }}</label>
                                <input type="url" name="base_url" id="providerBaseUrl" class="form-control form-control-sm font-monospace" dir="ltr" placeholder="https://...">
                            </div>
                        </div>
                    </div>

                    <!-- Active Switch -->
                    <div class="form-check form-switch pt-2 border-top">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" class="form-check-input" id="providerActive" checked>
                        <label class="form-check-label fw-semibold text-body" for="providerActive">{{ __('Active') }}</label>
                    </div>
                </div>

                <div class="modal-footer border-top py-2">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const providerForm = document.getElementById('providerForm');
    const providerModalHeading = document.getElementById('providerModalHeading');
    const providerModalIcon = document.getElementById('providerModalIcon');
    const providerMethod = document.getElementById('providerMethod');
    const providerCode = document.getElementById('providerCode');
    const providerName = document.getElementById('providerName');
    const providerDriver = document.getElementById('providerDriver');
    const providerModel = document.getElementById('providerModel');
    const providerBaseUrl = document.getElementById('providerBaseUrl');
    const providerApiKey = document.getElementById('providerApiKey');
    const providerActive = document.getElementById('providerActive');
    const apiKeyHelp = document.getElementById('apiKeyHelp');
    const toggleApiKeyBtn = document.getElementById('toggleApiKeyBtn');
    const toggleApiKeyIcon = document.getElementById('toggleApiKeyIcon');

    const defaultModels = {
        'gemini': 'gemini-3.6-flash',
        'openai': 'gpt-4o-mini',
        'deepl': ''
    };

    // Toggle API Key eye visibility
    if (toggleApiKeyBtn && providerApiKey && toggleApiKeyIcon) {
        toggleApiKeyBtn.addEventListener('click', function () {
            if (providerApiKey.type === 'password') {
                providerApiKey.type = 'text';
                toggleApiKeyIcon.classList.remove('bx-show');
                toggleApiKeyIcon.classList.add('bx-hide');
            } else {
                providerApiKey.type = 'password';
                toggleApiKeyIcon.classList.remove('bx-hide');
                toggleApiKeyIcon.classList.add('bx-show');
            }
        });
    }

    // Auto set model when driver changes
    if (providerDriver && providerModel) {
        providerDriver.addEventListener('change', function () {
            if (!providerModel.value || Object.values(defaultModels).includes(providerModel.value)) {
                providerModel.value = defaultModels[this.value] || '';
            }
            if (!providerMethod.innerHTML && !providerCode.dataset.manuallyEdited) {
                providerCode.value = this.value;
            }
        });
    }

    // Auto generate code from name
    if (providerName && providerCode) {
        providerName.addEventListener('input', function () {
            if (!providerMethod.innerHTML && !providerCode.dataset.manuallyEdited) {
                const slug = providerName.value.toLowerCase().replace(/[^a-z0-9_-]/g, '_').replace(/_+/g, '_');
                providerCode.value = slug || (providerDriver ? providerDriver.value : '');
            }
        });

        providerCode.addEventListener('input', function () {
            providerCode.dataset.manuallyEdited = 'true';
        });
    }

    // Edit Provider Buttons
    document.querySelectorAll('.js-edit-provider').forEach(function (button) {
        button.addEventListener('click', function () {
            try {
                const provider = JSON.parse(button.dataset.provider);
                providerModalHeading.textContent = @json(__('Edit Provider'));
                providerModalIcon.className = 'bx bx-edit text-primary fs-4';
                providerForm.action = `{{ url('settings/ai/providers') }}/${provider.id}`;
                providerMethod.innerHTML = '<input type="hidden" name="_method" value="PUT">';
                providerCode.value = provider.code || '';
                providerCode.dataset.manuallyEdited = 'true';
                providerName.value = provider.name || '';
                providerDriver.value = provider.driver || '';
                providerModel.value = provider.model || '';
                providerBaseUrl.value = provider.base_url || '';
                providerApiKey.value = '';
                providerApiKey.placeholder = '••••••••••••••••';
                providerActive.checked = !!provider.is_active;
                apiKeyHelp.textContent = @json(__('Leave blank when editing to keep the current key.'));
            } catch (e) {
                console.error('Failed to parse provider data:', e);
            }
        });
    });

    // Reset when opening "Add Provider"
    const addBtn = document.querySelector('[data-bs-target="#providerModal"]:not(.js-edit-provider)');
    if (addBtn) {
        addBtn.addEventListener('click', function () {
            providerModalHeading.textContent = @json(__('Add Provider'));
            providerModalIcon.className = 'bx bx-brain text-primary fs-4';
            providerForm.action = @json(route('settings.ai.providers.store'));
            providerMethod.innerHTML = '';
            providerForm.reset();
            providerCode.dataset.manuallyEdited = '';
            providerActive.checked = true;
            providerApiKey.placeholder = @json(__('Enter API Key'));
            apiKeyHelp.textContent = @json(__('API keys are encrypted before being stored.'));
            if (providerDriver) {
                providerCode.value = providerDriver.value;
                providerModel.value = defaultModels[providerDriver.value] || '';
            }
        });
    }
});
</script>
@endpush
