@extends('layouts/contentNavbarLayout')

@section('title', __('Interviews & Evaluations') . ' - ' . config('variables.templateName'))

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <i class="icon-base bx bx-calendar-check text-primary font-24"></i>
            <span>{{ __('Interviews & Candidate Evaluation') }}</span>
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hr.job-openings.index') }}" class="text-muted">{{ __('Recruitment') }}</a></li>
                <li class="breadcrumb-item active text-primary">{{ __('Interviews') }}</li>
            </ol>
        </nav>
    </div>

    <div class="d-flex flex-wrap align-items-center gap-2">
        <a href="{{ route('hr.job-applications.index') }}" class="btn btn-outline-primary d-flex align-items-center gap-1 shadow-sm px-3 py-2 rounded-3">
            <i class="bx bx-user-check font-18"></i>
            <span class="fw-semibold">{{ __('Candidate Pipeline') }}</span>
        </a>

        <a href="{{ route('hr.interviews.create') }}" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm px-3 py-2 rounded-3">
            <i class="icon-base bx bx-plus font-18"></i>
            <span class="fw-semibold">{{ __('Schedule Interview') }}</span>
        </a>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm rounded-3" role="alert">
        <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Quick Metrics KPI Row -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100 rounded-3" style="border: 1px solid #edf0f6 !important;">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1 fw-medium">{{ __('Total Sessions') }}</span>
                    <h4 class="fw-bold mb-0 text-primary">{{ $kpis['total'] }}</h4>
                </div>
                <div class="avatar avatar-md bg-label-primary rounded-3 p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-calendar-event font-20"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100 rounded-3" style="border: 1px solid #edf0f6 !important;">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1 fw-medium">{{ __('Upcoming / Scheduled') }}</span>
                    <h4 class="fw-bold mb-0 text-info">{{ $kpis['scheduled'] }}</h4>
                </div>
                <div class="avatar avatar-md bg-label-info rounded-3 p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-time-five font-20"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100 rounded-3" style="border: 1px solid #edf0f6 !important;">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1 fw-medium">{{ __("Today's Interviews") }}</span>
                    <h4 class="fw-bold mb-0 text-warning">{{ $kpis['today'] }}</h4>
                </div>
                <div class="avatar avatar-md bg-label-warning rounded-3 p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-bell font-20"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100 rounded-3" style="border: 1px solid #edf0f6 !important;">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1 fw-medium">{{ __('Completed & Evaluated') }}</span>
                    <h4 class="fw-bold mb-0 text-success">{{ $kpis['completed'] }}</h4>
                </div>
                <div class="avatar avatar-md bg-label-success rounded-3 p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-check-double font-20"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters Bar -->
<div class="card border-0 shadow-sm mb-4 rounded-3" style="border: 1px solid #edf0f6 !important;">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('hr.interviews.index') }}" id="filterForm" class="row g-3 align-items-end">
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold text-heading small">{{ __('Filter by Job Opening') }}</label>
                <select name="job_opening_id" id="filter_job_opening_id" class="select2 form-select" onchange="document.getElementById('filterForm').submit();">
                    <option value="">{{ __('-- All Job Openings --') }}</option>
                    @foreach ($jobOpenings as $op)
                        @php
                            $opTitle = $op->translate(app()->getLocale())?->title ?? $op->title;
                        @endphp
                        <option value="{{ $op->id }}" {{ (string)$selectedOpeningId === (string)$op->id ? 'selected' : '' }}>
                            {{ $op->opening_code }} &bull; {{ $opTitle }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-md-3">
                <label class="form-label fw-semibold text-heading small">{{ __('Filter by Status') }}</label>
                <select name="status" id="filter_status" class="form-select" onchange="document.getElementById('filterForm').submit();">
                    <option value="">{{ __('-- All Statuses --') }}</option>
                    @foreach ($statuses as $st)
                        <option value="{{ $st->value }}" {{ $selectedStatus === $st->value ? 'selected' : '' }}>{{ $st->label() }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-md-3">
                <label class="form-label fw-semibold text-heading small">{{ __('Filter by Date') }}</label>
                <input type="date" name="date" id="filter_date" class="form-control js-flatpickr-date" value="{{ $selectedDate }}" onchange="document.getElementById('filterForm').submit();">
            </div>

            <div class="col-12 col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-outline-primary px-3 rounded-3 w-100">
                    <i class="bx bx-filter-alt me-1"></i> {{ __('Filter') }}
                </button>
                @if ($selectedOpeningId || $selectedStatus || $selectedDate)
                    <a href="{{ route('hr.interviews.index') }}" class="btn btn-outline-secondary px-3 rounded-3" title="{{ __('Reset All') }}">
                        <i class="bx bx-reset"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Table View -->
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-datatable table-responsive">
        <table class="table border-top table-hover text-nowrap" id="interviewsTable">
            <thead>
                <tr>
                    <th class="text-center" style="width: 40px;">#</th>
                    <th>{{ __('Candidate') }}</th>
                    <th>{{ __('Job Opening') }}</th>
                    <th>{{ __('Type & Mode') }}</th>
                    <th>{{ __('Schedule & Time') }}</th>
                    <th>{{ __('Interviewers') }}</th>
                    <th>{{ __('Score & Rating') }}</th>
                    <th class="text-center">{{ __('Status') }}</th>
                    <th class="text-center" style="width: 110px;">{{ __('Actions') }}</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Modal: Cancel Interview -->
<div class="modal fade" id="cancelInterviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title text-danger d-flex align-items-center gap-2">
                    <i class="bx bx-x-circle font-20"></i>
                    <span>{{ __('Cancel Interview Session') }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="cancelInterviewForm">
                @csrf
                <input type="hidden" name="interview_id" id="cancel_interview_id">
                <div class="modal-body py-4">
                    <p class="text-muted small mb-3">
                        {{ __('Are you sure you want to cancel this scheduled interview? This action will mark the session as cancelled.') }}
                    </p>
                    <div class="mb-0">
                        <label class="form-label fw-semibold text-heading small">{{ __('Reason for Cancellation') }}</label>
                        <textarea name="cancel_reason" id="cancel_reason" rows="3" class="form-control" placeholder="{{ __('Provide internal reason...') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-danger" id="btnConfirmCancel">
                        <i class="bx bx-check me-1"></i> {{ __('Confirm Cancellation') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const $ = window.jQuery || window.$;

    // 1. Initialize Select2
    if (typeof window.initSelect2 === 'function') {
        window.initSelect2();
    } else if ($.fn.select2) {
        $('.select2').select2({ width: '100%' });
    }

    // 2. Initialize DataTables
    const dtTable = $('#interviewsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('hr.interviews.data') }}",
            data: function (d) {
                d.job_opening_id = $('#filter_job_opening_id').val();
                d.status         = $('#filter_status').val();
                d.date           = $('#filter_date').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center align-middle' },
            { data: 'candidate_info', name: 'candidate.full_name', className: 'align-middle' },
            { data: 'opening_info', name: 'jobOpening.title', className: 'align-middle' },
            { data: 'interview_type_mode', name: 'type', className: 'align-middle' },
            { data: 'schedule_info', name: 'scheduled_at', className: 'align-middle' },
            { data: 'interviewers_list', name: 'interviewers.first_name', orderable: false, className: 'align-middle' },
            { data: 'score_rating', name: 'overall_score', className: 'align-middle' },
            { data: 'status_badge', name: 'status', className: 'text-center align-middle' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center align-middle' }
        ],
        order: [[4, 'desc']]
    });

    // 3. Cancel Modal Handler
    const cancelModal = new bootstrap.Modal(document.getElementById('cancelInterviewModal'));

    $(document).on('click', '.btn-cancel-interview', function () {
        const id = $(this).data('id');
        $('#cancel_interview_id').val(id);
        $('#cancel_reason').val('');
        cancelModal.show();
    });

    $('#cancelInterviewForm').on('submit', function (e) {
        e.preventDefault();
        const id = $('#cancel_interview_id').val();
        const reason = $('#cancel_reason').val();

        $('#btnConfirmCancel').prop('disabled', true);

        fetch(`{{ url('hr/interviews') }}/${id}/cancel`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ reason: reason })
        })
        .then(res => res.json())
        .then(data => {
            $('#btnConfirmCancel').prop('disabled', false);
            if (data.success) {
                cancelModal.hide();
                dtTable.ajax.reload(null, false);
            } else {
                alert(data.message || '{{ __('Failed to cancel interview.') }}');
            }
        })
        .catch(err => {
            $('#btnConfirmCancel').prop('disabled', false);
            alert('{{ __('Server communication error.') }}');
        });
    });
});
</script>
@endsection
