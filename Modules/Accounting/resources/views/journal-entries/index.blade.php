@extends('layouts/contentNavbarLayout')

@section('title', __('Journal Entries') . ' - ' . config('variables.templateName'))

@section('content')
<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <i class="icon-base bx bx-receipt text-primary font-24"></i>
            <span>{{ __('Journal Entries') }}</span>
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('Accounting') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Journal Entries') }}</li>
            </ol>
        </nav>
    </div>
    @can('accounting.journal-entries.create')
    <a href="{{ route('accounting.journal-entries.create') }}" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm">
        <i class="icon-base bx bx-plus font-18"></i><span>{{ __('New Journal Entry') }}</span>
    </a>
    @endcan
</div>

<div id="alertContainer">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible d-flex align-items-center mb-4" role="alert">
            <i class="icon-base bx bx-check-circle me-2 font-20"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible d-flex align-items-center mb-4" role="alert">
            <i class="icon-base bx bx-error-circle me-2 font-20"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
</div>

<!-- Advanced Filters Toolbar -->
<div class="card shadow-sm border-0 mb-4" id="filtersCard">
    <div class="card-header bg-transparent border-bottom py-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h6 class="fw-bold mb-0 text-heading">{{ __('Filter Journal Entries') }}</h6>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
            <!-- Quick Date Presets -->
            <div class="btn-group btn-group-sm" role="group" id="quickDateFilters" aria-label="Quick Date Ranges">
                <button type="button" class="btn btn-outline-secondary active" data-range="all">{{ __('All') }}</button>
                <button type="button" class="btn btn-outline-secondary" data-range="today">{{ __('Today') }}</button>
                <button type="button" class="btn btn-outline-secondary" data-range="month">{{ __('This Month') }}</button>
                <button type="button" class="btn btn-outline-secondary" data-range="quarter">{{ __('This Quarter') }}</button>
                <button type="button" class="btn btn-outline-secondary" data-range="year">{{ __('This Year') }}</button>
            </div>

            <button type="button" id="btnResetFilters" class="btn btn-label-secondary btn-sm shadow-xs" title="{{ __('Reset all filters') }}">
                <span class="fw-semibold">{{ __('Reset') }}</span>
            </button>
        </div>
    </div>

    <div class="card-body pt-3 pb-3">
        <div class="row g-3">
            <!-- 1. Accounting Entity -->
            <div class="col-12 col-sm-6 col-lg-2">
                <label class="form-label fw-semibold text-heading small mb-1" for="filter_entity_id">
                    {{ __('Accounting Entity') }}
                </label>
                <select id="filter_entity_id" class="form-select select2" data-control="select2" data-placeholder="{{ __('All Entities') }}" data-allow-clear="true">
                    <option value="">{{ __('All Entities') }}</option>
                    @foreach($entities as $entity)
                        <option value="{{ $entity->id }}" @selected($selectedEntityId == $entity->id)>
                            {{ $entity->translate(app()->getLocale())?->name ?? $entity->translate('ar')?->name ?? $entity->code }} ({{ $entity->code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- 2. Journal -->
            <div class="col-12 col-sm-6 col-lg-2">
                <label class="form-label fw-semibold text-heading small mb-1" for="filter_journal_id">
                    {{ __('Journal') }}
                </label>
                <select id="filter_journal_id" class="form-select select2" data-control="select2" data-placeholder="{{ __('All Journals') }}" data-allow-clear="true">
                    <option value="">{{ __('All Journals') }}</option>
                    @foreach($journals as $journal)
                        <option value="{{ $journal->id }}" data-entity="{{ $journal->entity_id }}">
                            {{ $journal->code }} - {{ $journal->translate(app()->getLocale())?->name ?? $journal->translate('ar')?->name ?? $journal->code }} ({{ $journal->type->label() }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- 3. Status -->
            <div class="col-12 col-sm-6 col-lg-2">
                <label class="form-label fw-semibold text-heading small mb-1" for="filter_status">
                    {{ __('Entry Status') }}
                </label>
                <select id="filter_status" class="form-select select2" data-control="select2" data-placeholder="{{ __('All Statuses') }}" data-allow-clear="true">
                    <option value="">{{ __('All Statuses') }}</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status->value }}">{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 4. Entry Type -->
            <div class="col-12 col-sm-6 col-lg-2">
                <label class="form-label fw-semibold text-heading small mb-1" for="filter_entry_type">
                    {{ __('Entry Type') }}
                </label>
                <select id="filter_entry_type" class="form-select select2" data-control="select2" data-placeholder="{{ __('All Types') }}" data-allow-clear="true">
                    <option value="">{{ __('All Types') }}</option>
                    @foreach($types as $type)
                        <option value="{{ $type->value }}">{{ $type->label() }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 5. From Date -->
            <div class="col-12 col-sm-6 col-lg-2">
                <label class="form-label fw-semibold text-heading small mb-1" for="filter_from_date">
                    {{ __('From Date') }}
                </label>
                <input type="date" id="filter_from_date" class="form-control">
            </div>

            <!-- 6. To Date -->
            <div class="col-12 col-sm-6 col-lg-2">
                <label class="form-label fw-semibold text-heading small mb-1" for="filter_to_date">
                    {{ __('To Date') }}
                </label>
                <input type="date" id="filter_to_date" class="form-control">
            </div>
        </div>
    </div>
</div>

<!-- Journal Entries Table -->
<div class="card shadow-sm border-0">
    <div class="card-datatable table-responsive">
        <table id="journalEntriesTable" class="table table-hover border-top w-100 text-nowrap">
            <thead>
                <tr>
                    <th class="text-center" style="width:40px">#</th>
                    <th style="min-width: 140px;">{{ __('Entry Number') }}</th>
                    <th style="min-width: 240px;">{{ __('Narration') }}</th>
                    <th>{{ __('Entity') }}</th>
                    <th>{{ __('Journal') }}</th>
                    <th>{{ __('Date') }}</th>
                    <th class="text-end">{{ __('Total Debit') }}</th>
                    <th class="text-end">{{ __('Total Credit') }}</th>
                    <th class="text-center">{{ __('Status') }}</th>
                    <th class="text-center" style="width:110px">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- Reverse Entry Modal -->
<div class="modal fade" id="reverseEntryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="reverseEntryForm" class="modal-content">
            @csrf
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title fw-bold d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-undo text-warning font-22"></i>
                    <span>{{ __('Reverse Journal Entry') }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <p class="text-muted mb-3">
                    {{ __('You are about to create an automated reversal voucher for entry:') }}
                    <strong id="reverse_target_number" class="text-heading fw-bold"></strong>
                </p>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold" for="reversal_date">{{ __('Reversal Entry Date') }} <span class="text-danger">*</span></label>
                        <input type="date" name="entry_date" id="reversal_date" class="form-control" value="{{ now()->toDateString() }}" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold" for="reversal_reason">{{ __('Reversal Reason / Notes') }}</label>
                        <textarea name="reason" id="reversal_reason" class="form-control" rows="3" placeholder="{{ __('e.g. Correcting error in line item amount') }}"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 pb-4">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-warning" id="btnConfirmReverse">
                    <i class="icon-base bx bx-undo me-1"></i>
                    <span>{{ __('Confirm Reversal') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Draft Modal -->
<div class="modal fade" id="deleteEntryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <form id="deleteEntryForm" class="modal-content">
            @csrf
            @method('DELETE')
            <div class="modal-body text-center py-4">
                <div class="avatar avatar-xl bg-label-danger mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-trash font-32 text-danger"></i>
                </div>
                <h5 class="fw-bold mb-2">{{ __('Confirm Delete') }}</h5>
                <p class="text-muted mb-0">
                    {{ __('Are you sure you want to delete draft entry:') }}
                    <br>
                    <strong id="delete_target_number" class="text-danger"></strong>؟
                </p>
            </div>
            <div class="modal-footer justify-content-center border-0 pt-0 pb-4">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-danger" id="btnConfirmDelete">{{ __('Yes, Delete') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const $ = window.jQuery || window.$;
    const dataUrl = "{{ route('accounting.journal-entries.data') }}";
    const baseUrl = "{{ url('accounting/journal-entries') }}";

    function alertBox(type, message) {
        $('#alertContainer').html(`
            <div class="alert alert-${type} alert-dismissible d-flex align-items-center mb-4" role="alert">
                <i class="icon-base bx ${type === 'success' ? 'bx-check-circle' : 'bx-error-circle'} me-2 font-20"></i>
                <div>${message}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `);
        $('html, body').animate({ scrollTop: 0 }, 200);
    }

    const table = $('#journalEntriesTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 10,
        ajax: {
            url: dataUrl,
            data: function (d) {
                d.entity_id = $('#filter_entity_id').val();
                d.journal_id = $('#filter_journal_id').val();
                d.status = $('#filter_status').val();
                d.entry_type = $('#filter_entry_type').val();
                d.from_date = $('#filter_from_date').val();
                d.to_date = $('#filter_to_date').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'id', orderable: false, searchable: false, className: 'text-center align-middle' },
            { data: 'entry_number_formatted', name: 'entry_number', className: 'align-middle' },
            { data: 'narration_formatted', name: 'narration', className: 'align-middle' },
            { data: 'entity_info', name: 'entity_id', orderable: false, searchable: false, className: 'align-middle' },
            { data: 'journal_info', name: 'journal_id', orderable: false, searchable: false, className: 'align-middle' },
            { data: 'entry_date_formatted', name: 'entry_date', className: 'align-middle' },
            { data: 'total_debit_formatted', name: 'total_debit', className: 'text-end align-middle' },
            { data: 'total_credit_formatted', name: 'total_credit', className: 'text-end align-middle' },
            { data: 'status_badge', name: 'status', className: 'text-center align-middle' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center align-middle' }
        ],
        order: [[5, 'desc']],
        language: {
            search: '',
            searchPlaceholder: "{{ __('Search entries, references...') }}",
            paginate: {
                previous: '<i class="bx bx-chevron-left"></i>',
                next: '<i class="bx bx-chevron-right"></i>'
            }
        }
    });

    function formatDate(date) {
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const d = String(date.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    }

    function filterJournalsByEntity() {
        const selectedEntity = $('#filter_entity_id').val();
        const journalSelect = $('#filter_journal_id');
        const currentJournal = journalSelect.val();
        let isCurrentValid = false;

        journalSelect.find('option').each(function () {
            const jEntity = $(this).data('entity');
            if (!$(this).val() || !selectedEntity || String(jEntity) === String(selectedEntity)) {
                $(this).prop('disabled', false).show();
                if ($(this).val() && $(this).val() === currentJournal) {
                    isCurrentValid = true;
                }
            } else {
                $(this).prop('disabled', true).hide();
            }
        });

        if (currentJournal && !isCurrentValid) {
            journalSelect.val('').trigger('change.select2');
        } else {
            journalSelect.trigger('change.select2');
        }
    }

    // Quick Date Presets
    $('#quickDateFilters button').on('click', function () {
        $('#quickDateFilters button').removeClass('active btn-primary').addClass('btn-outline-secondary');
        $(this).addClass('active btn-primary').removeClass('btn-outline-secondary');

        const range = $(this).data('range');
        const now = new Date();

        if (range === 'all') {
            $('#filter_from_date').val('');
            $('#filter_to_date').val('');
        } else if (range === 'today') {
            const str = formatDate(now);
            $('#filter_from_date').val(str);
            $('#filter_to_date').val(str);
        } else if (range === 'month') {
            const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
            const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);
            $('#filter_from_date').val(formatDate(firstDay));
            $('#filter_to_date').val(formatDate(lastDay));
        } else if (range === 'quarter') {
            const currentQuarter = Math.floor(now.getMonth() / 3);
            const firstDay = new Date(now.getFullYear(), currentQuarter * 3, 1);
            const lastDay = new Date(now.getFullYear(), (currentQuarter + 1) * 3, 0);
            $('#filter_from_date').val(formatDate(firstDay));
            $('#filter_to_date').val(formatDate(lastDay));
        } else if (range === 'year') {
            const firstDay = new Date(now.getFullYear(), 0, 1);
            const lastDay = new Date(now.getFullYear(), 11, 31);
            $('#filter_from_date').val(formatDate(firstDay));
            $('#filter_to_date').val(formatDate(lastDay));
        }

        table.ajax.reload();
    });

    $('#filter_from_date, #filter_to_date').on('change', function () {
        $('#quickDateFilters button').removeClass('active btn-primary').addClass('btn-outline-secondary');
        table.ajax.reload();
    });

    $('#filter_entity_id').on('change', function () {
        filterJournalsByEntity();
        table.ajax.reload();
    });

    $('#filter_journal_id, #filter_status, #filter_entry_type').on('change', function () {
        table.ajax.reload();
    });

    $('#btnResetFilters').on('click', function () {
        $('#filter_entity_id, #filter_journal_id, #filter_status, #filter_entry_type').val('').trigger('change.select2');
        $('#filter_from_date, #filter_to_date').val('');
        $('#quickDateFilters button').removeClass('active btn-primary').addClass('btn-outline-secondary');
        $('#quickDateFilters button[data-range="all"]').addClass('active btn-primary').removeClass('btn-outline-secondary');
        filterJournalsByEntity();
        table.ajax.reload();
    });

    // Initial check
    filterJournalsByEntity();

    // Post Action
    $(document).on('click', '.btn-post-entry', function () {
        const id = $(this).data('id');
        if (!confirm("{{ __('Are you sure you want to post this journal entry? Once posted, it cannot be modified.') }}")) return;

        $.ajax({
            url: baseUrl + '/' + id + '/post',
            method: 'POST',
            data: { _token: "{{ csrf_token() }}" },
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function (res) {
                table.ajax.reload();
                alertBox('success', res.message || "{{ __('Journal entry posted successfully.') }}");
            },
            error: function (xhr) {
                let msg = "{{ __('Failed to post journal entry.') }}";
                if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                alertBox('danger', msg);
            }
        });
    });

    // Reverse Modal
    const reverseModalEl = document.getElementById('reverseEntryModal');
    const reverseModal = reverseModalEl ? bootstrap.Modal.getOrCreateInstance(reverseModalEl) : null;
    const reverseForm = $('#reverseEntryForm');

    $(document).on('click', '.btn-reverse-entry', function () {
        const id = $(this).data('id');
        const number = $(this).data('number');

        reverseForm.attr('action', baseUrl + '/' + id + '/reverse');
        $('#reverse_target_number').text(number);

        if (reverseModal) reverseModal.show();
    });

    reverseForm.on('submit', function (e) {
        e.preventDefault();
        const submitBtn = $('#btnConfirmReverse');
        submitBtn.prop('disabled', true);

        $.ajax({
            url: reverseForm.attr('action'),
            method: 'POST',
            data: reverseForm.serialize(),
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function (res) {
                if (reverseModal) reverseModal.hide();
                table.ajax.reload();
                alertBox('success', res.message);
                if (res.redirect_url) {
                    window.location.href = res.redirect_url;
                }
            },
            error: function (xhr) {
                let msg = "{{ __('Failed to reverse journal entry.') }}";
                if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                alertBox('danger', msg);
            },
            complete: function () {
                submitBtn.prop('disabled', false);
            }
        });
    });

    // Delete Draft Modal
    const deleteModalEl = document.getElementById('deleteEntryModal');
    const deleteModal = deleteModalEl ? bootstrap.Modal.getOrCreateInstance(deleteModalEl) : null;
    const deleteForm = $('#deleteEntryForm');

    $(document).on('click', '.btn-delete-entry', function () {
        const id = $(this).data('id');
        const number = $(this).data('number');

        deleteForm.attr('action', baseUrl + '/' + id);
        $('#delete_target_number').text(number);

        if (deleteModal) deleteModal.show();
    });

    deleteForm.on('submit', function (e) {
        e.preventDefault();
        const submitBtn = $('#btnConfirmDelete');
        submitBtn.prop('disabled', true);

        $.ajax({
            url: deleteForm.attr('action'),
            method: 'POST',
            data: deleteForm.serialize(),
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function (res) {
                if (deleteModal) deleteModal.hide();
                table.ajax.reload();
                alertBox('success', res.message || "{{ __('Journal entry deleted successfully.') }}");
            },
            error: function (xhr) {
                let msg = "{{ __('Failed to delete entry.') }}";
                if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                alertBox('danger', msg);
            },
            complete: function () {
                submitBtn.prop('disabled', false);
            }
        });
    });
});
</script>
@endsection
