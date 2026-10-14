@extends('layouts/contentNavbarLayout')

@section('title', __('Activity Log & Financial Audit Trail') . ' - ' . config('variables.templateName'))

@section('content')
<!-- Header -->
<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <i class="icon-base bx bx-history text-primary font-24"></i>
            <span>{{ __('Accounting Audit Trail') }}</span>
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('accounting.dashboard') }}">{{ __('Accounting') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Financial Activity Log') }}</li>
            </ol>
        </nav>
    </div>

    <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-outline-primary d-flex align-items-center gap-1 shadow-xs" id="btnRefreshTable">
            <i class="icon-base bx bx-refresh font-18"></i>
            <span>{{ __('Refresh Log') }}</span>
        </button>
        <a href="{{ route('accounting.guide') }}#tab-lockdates-btn" class="btn btn-label-secondary d-flex align-items-center gap-1 shadow-xs">
            <i class="icon-base bx bx-help-circle font-18"></i>
            <span>{{ __('Audit Guide') }}</span>
        </a>
    </div>
</div>

<!-- Filter Toolbar Card (3 columns per row) -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
        <h6 class="fw-bold mb-0 text-heading d-flex align-items-center gap-2">
            <i class="icon-base bx bx-filter-alt text-primary font-20"></i>
            <span>{{ __('Filter & Search Options') }}</span>
        </h6>
        <button type="button" class="btn btn-sm btn-label-secondary" id="btnResetFilters">
            <i class="bx bx-reset me-1"></i>{{ __('Reset Filters') }}
        </button>
    </div>
    <div class="card-body pt-3 pb-3">
        <!-- Row 1: 3 columns -->
        <div class="row g-3 mb-3">
            <div class="col-12 col-md-4">
                <label class="form-label small fw-semibold">{{ __('Executed By') }}</label>
                <select id="filter_user_id" class="form-select select2" data-placeholder="{{ __('All Users') }}" data-allow-clear="true">
                    <option value="">{{ __('All Users') }}</option>
                    @foreach($lookups['users'] as $u)
                        <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-md-4">
                <label class="form-label small fw-semibold">{{ __('Operation / Event Type') }}</label>
                <select id="filter_event" class="form-select select2" data-placeholder="{{ __('All Operations') }}" data-allow-clear="true">
                    <option value="">{{ __('All Operations') }}</option>
                    @foreach($lookups['events'] as $eventKey => $eventLabel)
                        <option value="{{ $eventKey }}">{{ $eventLabel }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-md-4">
                <label class="form-label small fw-semibold">{{ __('Accounting Section / Entity') }}</label>
                <select id="filter_subject_type" class="form-select select2" data-placeholder="{{ __('All Accounting Records') }}" data-allow-clear="true">
                    <option value="">{{ __('All Accounting Records') }}</option>
                    @foreach($lookups['subjectTypes'] as $classKey => $typeLabel)
                        <option value="{{ $classKey }}">{{ $typeLabel }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Row 2: 3 columns -->
        <div class="row g-3">
            <div class="col-12 col-md-4">
                <label class="form-label small fw-semibold">{{ __('From Date') }}</label>
                <input type="date" id="filter_date_from" class="form-control">
            </div>

            <div class="col-12 col-md-4">
                <label class="form-label small fw-semibold">{{ __('To Date') }}</label>
                <input type="date" id="filter_date_to" class="form-control">
            </div>

            <div class="col-12 col-md-4">
                <label class="form-label small fw-semibold">{{ __('Quick Search in Description') }}</label>
                <div class="input-group">
                    <input type="text" id="filter_search" class="form-control" placeholder="{{ __('Entry #, account code, narration...') }}">
                    <button class="btn btn-primary" type="button" id="btnApplyFilter">
                        <i class="bx bx-search me-1"></i>{{ __('Search') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Table Card -->
<div class="card shadow-sm border-0">
    <div class="card-datatable table-responsive">
        <table class="table table-hover border-top" id="accountingAuditLogsTable">
            <thead>
                <tr>
                    <th class="text-center" style="width: 5%;">#</th>
                    <th>{{ __('Executed By') }}</th>
                    <th>{{ __('Financial Record') }}</th>
                    <th class="text-center">{{ __('Operation Type') }}</th>
                    <th>{{ __('Description & Details') }}</th>
                    <th>{{ __('Date & Time') }}</th>
                    <th class="text-center" style="width: 8%;">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- Diff / Details Modal -->
<div class="modal fade" id="auditDiffModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-transparent border-bottom py-3">
                <div class="d-flex align-items-center gap-2">
                    <span class="avatar avatar-sm bg-label-primary rounded-circle d-flex align-items-center justify-content-center">
                        <i class="bx bx-git-compare font-18 text-primary"></i>
                    </span>
                    <div>
                        <h6 class="modal-title fw-bold mb-0 text-heading" id="diffModalTitle">{{ __('Audit Trail Details') }}</h6>
                        <small class="text-muted" id="diffModalSubtitle"></small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Info Summary Alert -->
                <div class="alert alert-light border d-flex flex-wrap align-items-center justify-content-between gap-2 p-3 mb-3">
                    <div>
                        <span class="small text-muted d-block">{{ __('Executed By:') }}</span>
                        <strong class="text-heading" id="modalCauser"></strong>
                    </div>
                    <div>
                        <span class="small text-muted d-block">{{ __('Event:') }}</span>
                        <span id="modalEventBadge"></span>
                    </div>
                    <div>
                        <span class="small text-muted d-block">{{ __('Timestamp:') }}</span>
                        <span class="small fw-semibold text-heading" id="modalTime"></span>
                    </div>
                </div>

                <!-- Diffs Comparison Table -->
                <h6 class="fw-bold text-heading mb-2">{{ __('Changes Comparison (Previous vs New Values):') }}</h6>
                <div class="table-responsive border rounded mb-3">
                    <table class="table table-sm table-striped mb-0" id="diffsTable">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 30%;">{{ __('Field') }}</th>
                                <th style="width: 35%;">{{ __('Old Value') }}</th>
                                <th style="width: 35%;">{{ __('New Value') }}</th>
                            </tr>
                        </thead>
                        <tbody id="diffsTableBody">
                            <!-- Injected dynamically -->
                        </tbody>
                    </table>
                </div>

                <!-- Empty state if no field changes -->
                <div id="noDiffsAlert" class="alert alert-secondary text-center small mb-0 py-3" style="display: none;">
                    <i class="bx bx-check-circle text-success me-1 font-18"></i>
                    <span>{{ __('No direct field modifications; the action is a system recording or approval.') }}</span>
                </div>
            </div>
            <div class="modal-footer border-top py-2">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const $ = window.jQuery || window.$;
    const dataUrl = "{{ route('accounting.audit-logs.data') }}";
    const showUrlBase = "{{ url('accounting/audit-logs') }}";

    // 1. DataTables Init
    const tableEl = $('#accountingAuditLogsTable');
    const dt = tableEl.DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: dataUrl,
            data: function (d) {
                d.user_id      = $('#filter_user_id').val();
                d.event        = $('#filter_event').val();
                d.subject_type = $('#filter_subject_type').val();
                d.date_from    = $('#filter_date_from').val();
                d.date_to      = $('#filter_date_to').val();
                d.quick_search = $('#filter_search').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'id', orderable: false, searchable: false, className: 'text-center align-middle' },
            { data: 'user', name: 'user', className: 'align-middle' },
            { data: 'subject', name: 'subject', className: 'align-middle' },
            { data: 'event', name: 'event', className: 'text-center align-middle' },
            { data: 'description', name: 'description', className: 'align-middle' },
            { data: 'created_at', name: 'created_at', className: 'align-middle' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center align-middle' }
        ],
        order: [],
        language: {
            emptyTable: "{{ __('No matching records found in audit log') }}",
            zeroRecords: "{{ __('No matching records found') }}",
            loadingRecords: "{{ __('Loading...') }}",
            processing: "{{ __('Processing...') }}",
            search: '',
            searchPlaceholder: "{{ __('Search audit log...') }}",
        }
    });

    // 2. Filter actions
    $('#btnApplyFilter').on('click', function () {
        dt.ajax.reload();
    });

    $('#filter_search').on('keyup', function (e) {
        if (e.key === 'Enter') {
            dt.ajax.reload();
        }
    });

    $('#filter_user_id, #filter_event, #filter_subject_type, #filter_date_from, #filter_date_to').on('change', function () {
        dt.ajax.reload();
    });

    $('#btnResetFilters').on('click', function () {
        $('#filter_user_id').val('').trigger('change.select2');
        $('#filter_event').val('').trigger('change.select2');
        $('#filter_subject_type').val('').trigger('change.select2');
        $('#filter_date_from').val('');
        $('#filter_date_to').val('');
        $('#filter_search').val('');
        dt.ajax.reload();
    });

    $('#btnRefreshTable').on('click', function () {
        dt.ajax.reload(null, false);
    });

    // 3. Diff Modal Handling
    $(document).on('click', '.btn-view-diff', function () {
        const logId = $(this).data('id');
        const modalEl = $('#auditDiffModal');
        const modal = new bootstrap.Modal(modalEl[0]);

        $('#diffModalSubtitle').text('...');
        $('#modalCauser').text('...');
        $('#modalEventBadge').html('...');
        $('#modalTime').text('...');
        $('#diffsTableBody').empty();
        $('#noDiffsAlert').hide();
        $('#diffsTable').show();

        modal.show();

        $.ajax({
            url: showUrlBase + '/' + logId,
            type: 'GET',
            success: function (res) {
                if (res.status && res.data) {
                    const d = res.data;
                    $('#diffModalSubtitle').text(d.description);
                    $('#modalCauser').text(d.causer_name);
                    $('#modalEventBadge').html('<span class="badge ' + d.event_badge_color + ' font-11 px-2 py-1">' + d.event_label + '</span>');
                    $('#modalTime').text(d.created_at);

                    if (d.diffs && d.diffs.length > 0) {
                        let rows = '';
                        d.diffs.forEach(function (diff) {
                            const oldVal = (diff.old !== null && diff.old !== '') ? diff.old : '<span class="text-muted fst-italic">{{ __('Empty') }}</span>';
                            const newVal = (diff.new !== null && diff.new !== '') ? diff.new : '<span class="text-muted fst-italic">{{ __('Empty') }}</span>';

                            rows += '<tr>' +
                                '<td class="fw-semibold text-heading">' + diff.field_label + ' <small class="text-muted d-block" style="font-size: 0.7rem;">' + diff.field + '</small></td>' +
                                '<td class="text-danger bg-danger-subtle py-2 px-3">' + oldVal + '</td>' +
                                '<td class="text-success bg-success-subtle py-2 px-3">' + newVal + '</td>' +
                            '</tr>';
                        });
                        $('#diffsTableBody').html(rows);
                        $('#diffsTable').show();
                        $('#noDiffsAlert').hide();
                    } else {
                        $('#diffsTable').hide();
                        $('#noDiffsAlert').show();
                    }
                }
            },
            error: function () {
                $('#diffModalSubtitle').text('{{ __('Unable to load change details') }}');
            }
        });
    });
});
</script>
@endsection
