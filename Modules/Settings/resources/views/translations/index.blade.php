@extends('layouts/contentNavbarLayout')

@section('title', __('Language & Translation Manager') . ' - ' . config('variables.templateName'))

@section('content')
<!-- Header & Controls -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
                <i class="icon-base bx bx-globe text-primary font-22"></i>
                {{ __('Language & Translation Manager') }}
            </h4>
            <p class="text-muted mb-0">{{ __('Manage localization and system phrases per module') }}</p>
        </div>

        <div class="translations-header-actions d-flex flex-wrap align-items-center gap-2">
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
                        <input type="text" id="searchInput" class="form-control border-start-0" placeholder="{{ __('Search in keys or translations...') }}">
                    </div>
                </div>
                <div class="col-12 col-md-6 d-flex align-items-center justify-content-md-end gap-2">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-outline-secondary active" id="btnFilterAll">
                            {{ __('All') }}
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-warning" id="btnFilterMissing">
                            <i class="icon-base bx bx-error-circle me-1"></i>
                            {{ __('Incomplete') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Translations Form & Table -->
        <div id="translationsForm">
            <div class="table-responsive">
                <table class="table table-hover table-translations mb-0" id="translationsTable">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 28%;">{{ __('Translation Key') }}</th>
                            <th style="width: 32%;">{{ __('Arabic (AR)') }}</th>
                            <th style="width: 32%;">{{ __('English (EN)') }}</th>
                            <th style="width: 4%; text-align: center;">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div class="card-footer border-top bg-lighter py-3">
                <span class="text-muted small"><i class="icon-base bx bx-info-circle text-primary me-1"></i>{{ __('Each row is saved independently so pagination does not lose changes.') }}</span>
            </div>
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
                    <input type="text" name="key" class="form-control" placeholder="{{ __('e.g. Employees Directory') }}" required dir="ltr">
                    <small class="text-muted">{{ __('Used in code with __("Key")') }}</small>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">{{ __('Arabic Translation') }}</label>
                    <textarea name="ar" class="form-control" rows="2" placeholder="{{ __('e.g. Employees Directory') }}" dir="rtl"></textarea>
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

@section('page-script')
<script>
window.addEventListener('load', function () {
    const $ = window.jQuery || window.$;
    if (!$ || !$.fn.DataTable) return;

    let currentFilter = 'all';
    const table = $('#translationsTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 25,
        lengthMenu: [[25, 50, 100], [25, 50, 100]],
        pagingType: 'simple_numbers',
        ajax: {
            url: "{{ route('settings.translations.data') }}",
            data: function (data) {
                data.module = @json($activeModule);
                data.missing = currentFilter === 'missing' ? 1 : 0;
                data.search.value = $('#searchInput').val();
            }
        },
        // Same footer as the global DataTables layout (info + paging, centered on mobile); page has its own search bar
        dom: "rt<'row dt-footer-row mx-0 px-4 py-3 border-top align-items-center justify-content-between'<'col-auto'i><'col-auto'p>>",
        // Wide editable rows: scroll horizontally instead of collapsing columns
        responsive: false,
        columns: [
            { data: 'key', render: (data) => `<div class="translation-key-badge"><span>${escapeHtml(data)}</span><button type="button" class="btn-copy-key" title="{{ __('Copy Key') }}" data-key="${escapeHtml(data)}"><i class="icon-base bx bx-copy"></i></button></div>` },
            { data: 'ar', orderable: false, render: (data) => `<textarea class="form-control translation-input translation-textarea" rows="1" dir="rtl">${escapeHtml(data || '')}</textarea>` },
            { data: 'en', orderable: false, render: (data) => `<textarea class="form-control translation-input translation-textarea" rows="1" dir="ltr">${escapeHtml(data || '')}</textarea>` },
            { data: null, orderable: false, searchable: false, className: 'text-center', render: () => `<div class="d-inline-flex gap-1"><button type="button" class="btn btn-sm btn-icon btn-outline-success btn-save-translation" title="{{ __('Save Changes') }}"><i class="icon-base bx bx-save"></i></button><button type="button" class="btn btn-sm btn-icon btn-outline-danger btn-delete-translation" title="{{ __('Delete') }}"><i class="icon-base bx bx-trash"></i></button></div>` }
        ],
        order: [[0, 'asc']]
    });

    function escapeHtml(value) {
        return $('<div>').text(value == null ? '' : value).html();
    }

    function autoExpand(el) {
        el.style.height = 'auto';
        el.style.height = Math.max(38, el.scrollHeight) + 'px';
    }

    $('#searchInput').on('input', function () { table.ajax.reload(); });
    $('#btnFilterAll').on('click', function () { setFilter('all'); });
    $('#btnFilterMissing').on('click', function () { setFilter('missing'); });

    function setFilter(filter) {
        currentFilter = filter;
        $('#btnFilterAll').toggleClass('active', filter === 'all');
        $('#btnFilterMissing').toggleClass('active', filter === 'missing');
        table.ajax.reload();
    }

    $('#translationsTable').on('draw.dt', function () {
        document.querySelectorAll('.translation-textarea').forEach(autoExpand);
    });

    $('#translationsTable').on('click', '.btn-copy-key', function () {
        const button = this;
        navigator.clipboard.writeText($(button).data('key')).then(() => {
            const icon = button.querySelector('i');
            const original = icon.className;
            icon.className = 'icon-base bx bx-check text-success';
            setTimeout(() => icon.className = original, 1500);
        });
    });

    $('#translationsTable').on('click', '.btn-save-translation', function () {
        const row = table.row($(this).closest('tr'));
        const data = row.data();
        const button = this;
        $.ajax({
            url: "{{ route('settings.translations.update-key') }}",
            method: 'PUT',
            data: { _token: "{{ csrf_token() }}", module: @json($activeModule), key: data.key, ar: $(row.node()).find('textarea').eq(0).val(), en: $(row.node()).find('textarea').eq(1).val() }
        }).done(function () {
            $(button).removeClass('btn-outline-success').addClass('btn-success');
            setTimeout(() => $(button).removeClass('btn-success').addClass('btn-outline-success'), 1200);
        }).fail(function (xhr) { alert(xhr.responseJSON?.message || '{{ __('Unable to save translation.') }}'); });
    });

    $('#translationsTable').on('click', '.btn-delete-translation', function () {
        const data = table.row($(this).closest('tr')).data();
        if (confirm("{{ __('Are you sure you want to delete the translation key:') }} " + data.key)) {
            $('#deleteKeyInput').val(data.key);
            $('#deleteKeyForm').submit();
        }
    });
});
</script>
@endsection
@endsection
