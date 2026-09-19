@extends('layouts/contentNavbarLayout')

@section('title', $plan->title . ' - ' . config('variables.templateName'))

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <a href="{{ route('hr.workforce-plans.index') }}" class="btn btn-icon btn-sm btn-outline-secondary rounded-circle" title="{{ __('Back to Plans') }}">
                <i class="icon-base bx bx-arrow-back font-16"></i>
            </a>
            <h4 class="fw-bold mb-0 d-flex align-items-center gap-2">
                <span>{{ $plan->title }}</span>
                <span class="badge {{ $plan->status->badgeClass() }} d-inline-flex align-items-center gap-1 font-12">
                    <i class="icon-base {{ $plan->status->icon() }} font-14"></i>
                    <span>{{ $plan->status->label() }}</span>
                </span>
            </h4>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ route('hr.workforce-plans.index') }}" class="text-muted">{{ __('Workforce Plans') }}</a></li>
                <li class="breadcrumb-item active text-primary">{{ $plan->plan_code }}</li>
            </ol>
        </nav>
    </div>

    <!-- Actions Toolbar -->
    <div class="d-flex flex-wrap align-items-center gap-2">
        @if ($plan->status === \Modules\HR\Enums\WorkforcePlanStatus::DRAFT)
            <a href="{{ route('hr.workforce-plans.edit', $plan->id) }}" class="btn btn-outline-primary d-flex align-items-center gap-1">
                <i class="icon-base bx bx-edit font-16"></i>
                <span>{{ __('Edit Plan') }}</span>
            </a>
            <form action="{{ route('hr.workforce-plans.submit', $plan->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Submit this plan for management approval?') }}');">
                @csrf
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm">
                    <i class="icon-base bx bx-send font-16"></i>
                    <span>{{ __('Submit for Approval') }}</span>
                </button>
            </form>
        @elseif ($plan->status === \Modules\HR\Enums\WorkforcePlanStatus::SUBMITTED)
            <form action="{{ route('hr.workforce-plans.approve', $plan->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Approve this workforce plan?') }}');">
                @csrf
                <button type="submit" class="btn btn-success d-flex align-items-center gap-1 shadow-sm">
                    <i class="icon-base bx bx-check-double font-16"></i>
                    <span>{{ __('Approve Plan') }}</span>
                </button>
            </form>
        @elseif ($plan->status === \Modules\HR\Enums\WorkforcePlanStatus::APPROVED)
            <form action="{{ route('hr.workforce-plans.activate', $plan->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Activate this plan for departmental hiring?') }}');">
                @csrf
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm">
                    <i class="icon-base bx bx-play-circle font-16"></i>
                    <span>{{ __('Activate Plan') }}</span>
                </button>
            </form>
        @elseif ($plan->status === \Modules\HR\Enums\WorkforcePlanStatus::ACTIVE)
            <button type="button" id="btn-recalculate-live" class="btn btn-outline-info d-flex align-items-center gap-1">
                <i class="icon-base bx bx-refresh font-16"></i>
                <span>{{ __('Recalculate Live Headcount') }}</span>
            </button>
            <form action="{{ route('hr.workforce-plans.close', $plan->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Close this workforce plan?') }}');">
                @csrf
                <button type="submit" class="btn btn-outline-secondary d-flex align-items-center gap-1">
                    <i class="icon-base bx bx-lock-alt font-16"></i>
                    <span>{{ __('Close Plan') }}</span>
                </button>
            </form>
        @endif
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- KPI Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card kpi-card shadow-sm border-0 h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1">{{ __('Target Headcount') }}</span>
                    <h3 class="fw-bold mb-0 text-primary">{{ number_format($plan->total_target_headcount) }}</h3>
                </div>
                <div class="avatar avatar-md bg-label-primary rounded p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-user-plus font-22"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card kpi-card shadow-sm border-0 h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1">{{ __('Current Headcount') }}</span>
                    <h3 class="fw-bold mb-0 text-dark">{{ number_format($plan->total_current_headcount) }}</h3>
                </div>
                <div class="avatar avatar-md bg-label-secondary rounded p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-group font-22"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card kpi-card shadow-sm border-0 h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1">{{ __('Total Budgeted Cost') }}</span>
                    <h3 class="fw-bold mb-0 text-success font-monospace">{{ number_format($plan->total_budgeted_cost, 3) }} <span class="fs-6 fw-normal text-muted">{{ __('KWD') }}</span></h3>
                </div>
                <div class="avatar avatar-md bg-label-success rounded p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-wallet font-22"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card kpi-card shadow-sm border-0 h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1">{{ __('Fulfillment Progress') }}</span>
                    <h3 class="fw-bold mb-0 text-warning">{{ $plan->fulfillment_percentage }}%</h3>
                </div>
                <div class="avatar avatar-md bg-label-warning rounded p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-pie-chart-alt-2 font-22"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Plan Meta Info -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body py-3">
        <div class="row g-3">
            <div class="col-6 col-md-3">
                <span class="text-muted small d-block">{{ __('Plan Code') }}</span>
                <span class="fw-bold font-monospace text-heading">{{ $plan->plan_code }}</span>
            </div>
            <div class="col-6 col-md-3">
                <span class="text-muted small d-block">{{ __('Plan Year') }}</span>
                <span class="fw-bold text-heading">{{ $plan->year }}</span>
            </div>
            <div class="col-6 col-md-3">
                <span class="text-muted small d-block">{{ __('Validity Period') }}</span>
                <span class="font-monospace small text-heading">
                    {{ $plan->start_date ? $plan->start_date->format('Y-m-d') : '-' }} &rarr; {{ $plan->end_date ? $plan->end_date->format('Y-m-d') : '-' }}
                </span>
            </div>
            <div class="col-6 col-md-3">
                <span class="text-muted small d-block">{{ __('Created By') }}</span>
                <span class="fw-semibold text-heading">{{ $plan->creator?->name ?? '-' }}</span>
            </div>
            @if ($plan->approved_at)
            <div class="col-12 col-md-6 border-top pt-2">
                <span class="text-muted small d-block">{{ __('Approval Info') }}</span>
                <span class="small text-success">
                    <i class="bx bx-check font-14"></i> {{ __('Approved by') }} <strong>{{ $plan->approver?->name ?? '-' }}</strong> {{ __('on') }} {{ $plan->approved_at->format('Y-m-d H:i') }}
                </span>
            </div>
            @endif
            @if ($plan->description)
            <div class="col-12 border-top pt-2">
                <span class="text-muted small d-block mb-1">{{ __('Strategic Objectives & Description') }}</span>
                <p class="mb-0 text-muted small">{{ $plan->description }}</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Plan Positions Table -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
        <h5 class="card-title mb-0 d-flex align-items-center gap-2 text-primary">
            <i class="icon-base bx bx-list-check font-20"></i>
            <span>{{ __('Planned Positions & Hiring Breakdown') }}</span>
            <span class="badge bg-label-primary rounded-pill font-11">{{ $plan->items->count() }} {{ __('Roles') }}</span>
        </h5>
    </div>
    <div class="table-responsive text-nowrap">
        <table class="table table-hover align-middle mb-0" style="min-width: 1300px;">
            <thead class="table-light">
                <tr>
                    <th style="min-width: 220px;">{{ __('Department / Branch') }}</th>
                    <th style="min-width: 220px;">{{ __('Job Title') }}</th>
                    <th style="min-width: 170px;">{{ __('Type & Quarter') }}</th>
                    <th style="min-width: 140px;">{{ __('Priority') }}</th>
                    <th style="min-width: 90px;" class="text-center">{{ __('Current') }}</th>
                    <th style="min-width: 90px;" class="text-center">{{ __('Target') }}</th>
                    <th style="min-width: 90px;" class="text-center">{{ __('Vacancies') }}</th>
                    <th style="min-width: 90px;" class="text-center">{{ __('Hired') }}</th>
                    <th style="min-width: 140px;">{{ __('Fulfillment') }}</th>
                    <th style="min-width: 150px;">{{ __('Est. Salary') }}</th>
                    <th style="min-width: 90px;" class="text-center">{{ __('Action') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($plan->items as $item)
                @php
                    $deptName = $item->department?->translate(app()->getLocale())?->name ?? $item->department?->translate('ar')?->name ?? $item->department?->name ?? '-';
                    $branchName = $item->branch?->translate(app()->getLocale())?->name ?? $item->branch?->translate('ar')?->name ?? $item->branch?->name ?? __('All Branches');
                    $jobName = $item->jobTitle?->translate(app()->getLocale())?->name ?? $item->jobTitle?->translate('ar')?->name ?? $item->jobTitle?->name ?? '-';
                    $pct = $item->target_headcount > 0 ? round(($item->current_headcount / $item->target_headcount) * 100) : 0;
                    $color = $pct >= 80 ? 'bg-success' : ($pct >= 40 ? 'bg-primary' : 'bg-warning');
                @endphp
                <tr>
                    <td>
                        <div class="d-flex flex-column">
                            <span class="fw-bold text-heading">{{ $deptName }}</span>
                            <small class="text-muted"><i class="bx bx-git-branch font-12"></i> {{ $branchName }}</small>
                        </div>
                    </td>
                    <td>
                        <span class="fw-semibold text-primary">{{ $jobName }}</span>
                    </td>
                    <td>
                        <div class="d-flex flex-column">
                            <span class="small">{{ $item->employment_type?->label() }}</span>
                            <small class="text-muted">{{ $item->target_quarter?->label() }}</small>
                        </div>
                    </td>
                    <td>
                        <span class="badge {{ $item->priority?->badgeClass() }}">{{ $item->priority?->label() }}</span>
                    </td>
                    <td class="text-center">
                        <span class="fw-semibold">{{ $item->current_headcount }}</span>
                    </td>
                    <td class="text-center">
                        <span class="fw-bold text-dark">{{ $item->target_headcount }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-label-warning fw-bold">{{ $item->vacancies_count }}</span>
                    </td>
                    <td class="text-center">
                        <span class="fw-semibold text-success">{{ $item->hired_count }}</span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress w-100" style="height: 6px;">
                                <div class="progress-bar {{ $color }}" style="width: {{ min(100, $pct) }}%;"></div>
                            </div>
                            <small class="fw-bold">{{ $pct }}%</small>
                        </div>
                    </td>
                    <td>
                        <span class="font-monospace small text-muted">{{ number_format($item->estimated_monthly_salary, 3) }} KWD</span>
                    </td>
                    <td class="text-center">
                        @if (in_array($plan->status, [\Modules\HR\Enums\WorkforcePlanStatus::APPROVED, \Modules\HR\Enums\WorkforcePlanStatus::ACTIVE]))
                            <a href="{{ route('hr.job-requisitions.create', ['plan_item_id' => $item->id]) }}" class="btn btn-sm btn-icon btn-outline-success" title="{{ __('Request Job Requisition for this role') }}">
                                <i class="bx bx-user-plus font-16"></i>
                            </a>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" class="text-center py-4 text-muted">{{ __('No position items defined for this plan.') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('page-script')
<script>
$(function() {
    $('#btn-recalculate-live').on('click', function() {
        const btn = $(this);
        btn.prop('disabled', true);

        $.ajax({
            url: "{{ route('hr.workforce-plans.recalculate', $plan->id) }}",
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(res) {
                btn.prop('disabled', false);
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: '{{ __("Updated") }}',
                        text: res.message,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => location.reload());
                } else {
                    alert(res.message);
                    location.reload();
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false);
                const msg = xhr.responseJSON?.message || '{{ __("Error recalculating headcount.") }}';
                if (typeof Swal !== 'undefined') {
                    Swal.fire('{{ __("Error") }}', msg, 'error');
                } else {
                    alert(msg);
                }
            }
        });
    });
});
</script>
@endsection
