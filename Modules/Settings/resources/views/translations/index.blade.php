@extends('layouts/contentNavbarLayout')

@section('title', __('Language & Translation Manager') . ' - ' . config('variables.templateName'))

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header & Controls -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
                <i class="icon-base bx bx-globe text-primary font-22"></i>
                {{ __('Language & Translation Manager') }}
            </h4>
            <p class="text-muted mb-0">{{ __('Manage localization and system phrases per module') }}</p>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
            <!-- Module Switcher Dropdown -->
            <div class="dropdown">
                <button class="btn btn-outline-primary dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="icon-base bx bx-layer"></i>
                    <span>{{ __('Current Scope:') }}</span>
                    <strong class="text-primary">
                        @foreach ($modules as $m)
                            @if ($m['id'] === $activeModule)
                                {{ __($m['name']) }}
                            @endif
                        @endforeach
                    </strong>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                    @foreach ($modules as $mod)
                        <li>
                            <a class="dropdown-item d-flex align-items-center justify-content-between {{ $mod['id'] === $activeModule ? 'active fw-bold' : '' }}" href="{{ route('settings.translations.index', ['module' => $mod['id']]) }}">
                                <span>{{ __($mod['name']) }}</span>
                                @if ($mod['id'] === $activeModule)
                                    <i class="icon-base bx bx-check"></i>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Add Key Button -->
            <button type="button" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#addKeyModal">
                <i class="icon-base bx bx-plus font-18"></i>
                <span>{{ __('Add New Key') }}</span>
            </button>
        </div>
    </div>

    @if (isset($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissible mb-4" role="alert">
            <h6 class="alert-heading fw-bold mb-1">{{ __('Please review the following errors:') }}</h6>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Main Card -->
    <div class="card shadow-none border mb-4">
        <!-- Search & Filter Bar -->
        <div class="card-header border-bottom py-3">
            <div class="row g-3 align-items-center">
                <div class="col-12 col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="icon-base bx bx-search text-muted"></i></span>
                        <input type="text" id="searchInput" class="form-control border-start-0" placeholder="{{ __('Search in keys or translations...') }}" onkeyup="filterTranslations()">
                    </div>
                </div>
                <div class="col-12 col-md-6 d-flex align-items-center justify-content-md-end gap-2">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-outline-secondary active" id="btnFilterAll" onclick="setFilter('all')">
                            {{ __('All') }} ({{ count($translations) }})
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-warning" id="btnFilterMissing" onclick="setFilter('missing')">
                            <i class="icon-base bx bx-error-circle me-1"></i>
                            {{ __('Incomplete') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Translations Form & Table -->
        <form id="translationsForm" action="{{ route('settings.translations.update') }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="module" value="{{ $activeModule }}">

            <div class="table-responsive">
                <table class="table table-hover table-translations mb-0" id="translationsTable">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 4%;">#</th>
                            <th style="width: 28%;">{{ __('Translation Key') }}</th>
                            <th style="width: 32%;">{{ __('Arabic (AR)') }}</th>
                            <th style="width: 32%;">{{ __('English (EN)') }}</th>
                            <th style="width: 4%; text-align: center;">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($translations as $index => $item)
                            <tr class="translation-row" data-key="{{ strtolower($item['key']) }}" data-ar="{{ strtolower($item['ar']) }}" data-en="{{ strtolower($item['en']) }}" data-is-missing="{{ empty($item['ar']) || empty($item['en']) ? 'true' : 'false' }}">
                                <td class="text-muted small pt-3">{{ $index + 1 }}</td>
                                <td>
                                    <div class="translation-key-badge">
                                        <span>{{ $item['key'] }}</span>
                                        <button type="button" class="btn-copy-key" title="{{ __('Copy Key') }}" onclick="copyKey(this, '{{ addslashes($item['key']) }}')">
                                            <i class="icon-base bx bx-copy"></i>
                                        </button>
                                    </div>
                                </td>
                                <td>
                                    <input type="hidden" name="translations[{{ $index }}][key]" value="{{ $item['key'] }}">
                                    <textarea name="translations[{{ $index }}][ar]" 
                                              class="form-control translation-input translation-textarea" 
                                              placeholder="{{ __('Arabic Translation...') }}"
                                              rows="1"
                                              dir="rtl"
                                              oninput="autoExpand(this)">{{ $item['ar'] }}</textarea>
                                </td>
                                <td>
                                    <textarea name="translations[{{ $index }}][en]" 
                                              class="form-control translation-input translation-textarea" 
                                              placeholder="{{ __('English Translation...') }}"
                                              rows="1"
                                              dir="ltr"
                                              oninput="autoExpand(this)">{{ $item['en'] }}</textarea>
                                </td>
                                <td class="text-center pt-2">
                                    <button type="button" class="btn btn-sm btn-icon btn-outline-danger" title="{{ __('Delete') }}" onclick="confirmDelete('{{ addslashes($item['key']) }}')">
                                        <i class="icon-base bx bx-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="icon-base bx bx-folder-open font-40 mb-2 d-block text-secondary"></i>
                                    {{ __('No translations found for this module.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Bottom Save Bar -->
            @if (count($translations) > 0)
                <div class="card-footer border-top bg-lighter py-3">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <span class="text-muted small">
                            <i class="icon-base bx bx-info-circle text-primary me-1"></i>
                            {{ __('Any modifications saved will be written immediately into the module JSON files.') }}
                        </span>
                        <button type="submit" class="btn btn-primary d-flex align-items-center gap-2 px-4 shadow-sm">
                            <i class="icon-base bx bx-save font-18"></i>
                            <span>{{ __('Save Changes') }}</span>
                        </button>
                    </div>
                </div>
            @endif
        </form>
    </div>
</div>

<!-- Add Key Modal -->
<div class="modal fade" id="addKeyModal" tabindex="-1" aria-labelledby="addKeyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" action="{{ route('settings.translations.store') }}" method="POST">
            @csrf
            <input type="hidden" name="module" value="{{ $activeModule }}">

            <div class="modal-header border-bottom">
                <h5 class="modal-title d-flex align-items-center gap-2" id="addKeyModalLabel">
                    <i class="icon-base bx bx-plus-circle text-primary"></i>
                    {{ __('Add New Translation Key') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-4">
                <div class="mb-3">
                    <label class="form-label fw-semibold">{{ __('Target Module') }}</label>
                    <input type="text" class="form-control bg-light" value="{{ $activeModule }}" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">{{ __('Translation Key (English / Identifier)') }} <span class="text-danger">*</span></label>
                    <input type="text" name="key" class="form-control" placeholder="e.g. Employees Directory" required dir="ltr">
                    <small class="text-muted">{{ __('Used in code with __("Key")') }}</small>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">{{ __('Arabic Translation') }}</label>
                    <textarea name="ar" class="form-control" rows="2" placeholder="مثال: دليل وملفات الموظفين" dir="rtl"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">{{ __('English Translation') }}</label>
                    <textarea name="en" class="form-control" rows="2" placeholder="e.g. Employees Directory" dir="ltr"></textarea>
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-check font-18"></i>
                    <span>{{ __('Add & Save') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Floating Toast Notification for Copy -->
<div id="copyToast" class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999; display: none;">
    <div class="toast align-items-center text-bg-dark border-0 show shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex align-items-center">
            <div class="toast-body d-flex align-items-center gap-2 py-2 px-3">
                <i class="icon-base bx bx-check-circle text-success font-20"></i>
                <span id="copyToastMessage">{{ __('Key copied to clipboard!') }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Delete Form -->
<form id="deleteKeyForm" action="{{ route('settings.translations.destroy') }}" method="POST" class="d-none">
    @csrf
    @method('DELETE')
    <input type="hidden" name="module" value="{{ $activeModule }}">
    <input type="hidden" name="key" id="deleteKeyInput">
</form>

<script>
    let currentFilter = 'all';

    function autoExpand(el) {
        el.style.height = 'auto';
        el.style.height = Math.max(38, el.scrollHeight) + 'px';
    }

    // Auto expand all textareas on load
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.translation-textarea').forEach(el => autoExpand(el));
    });

    function filterTranslations() {
        const query = document.getElementById('searchInput').value.toLowerCase().trim();
        const rows = document.querySelectorAll('.translation-row');

        rows.forEach(row => {
            const key = row.getAttribute('data-key');
            const ar = row.getAttribute('data-ar');
            const en = row.getAttribute('data-en');
            const isMissing = row.getAttribute('data-is-missing') === 'true';

            const matchesSearch = !query || key.includes(query) || ar.includes(query) || en.includes(query);
            const matchesFilter = (currentFilter === 'all') || (currentFilter === 'missing' && isMissing);

            if (matchesSearch && matchesFilter) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    function setFilter(filter) {
        currentFilter = filter;
        document.getElementById('btnFilterAll').classList.toggle('active', filter === 'all');
        document.getElementById('btnFilterMissing').classList.toggle('active', filter === 'missing');
        filterTranslations();
    }

    function copyKey(btn, keyText) {
        navigator.clipboard.writeText(keyText).then(() => {
            // Subtle button icon feedback only
            const icon = btn.querySelector('i');
            if (icon) {
                const originalClass = icon.className;
                icon.className = 'icon-base bx bx-check text-success';
                setTimeout(() => {
                    icon.className = originalClass;
                }, 1500);
            }
        });
    }

    function confirmDelete(keyText) {
        if (confirm("{{ __('Are you sure you want to delete the translation key:') }} " + keyText)) {
            document.getElementById('deleteKeyInput').value = keyText;
            document.getElementById('deleteKeyForm').submit();
        }
    }
</script>
@endsection
