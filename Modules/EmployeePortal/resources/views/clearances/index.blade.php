@extends('employeeportal::layouts.master')

@section('title', __('Clearance Requests History'))

@section('content')
<div class="loans-history-page-container">
    <!-- Top Navigation Header -->
    <div class="loans-nav-header">
        <a href="{{ route('portal.requests') }}" class="loans-back-btn" title="{{ __('Back') }}">
            <i class="bx {{ app()->getLocale() === 'ar' ? 'bx-right-arrow-alt' : 'bx-left-arrow-alt' }}"></i>
        </a>
        <h1 class="loans-header-title">{{ __('Exit Clearances') }}</h1>
        <a href="{{ route('portal.clearances.create') }}" class="loans-back-btn" title="{{ __('New Request') }}">
            <i class="bx bx-plus"></i>
        </a>
    </div>

    <!-- 1. Status Filter Tabs (Horizontal Scroller) -->
    <div class="loans-tabs-scroller mb-3">
        <!-- All Requests -->
        <a href="{{ route('portal.clearances.index', ['status' => 'all']) }}"
           class="loans-tab-item {{ $filter === 'all' ? 'active' : '' }}">
            <span>{{ __('All') }}</span>
            <span class="tab-count">{{ $statusCounts['all'] }}</span>
        </a>

        <!-- Pending (Under Review) -->
        <a href="{{ route('portal.clearances.index', ['status' => 'pending']) }}"
           class="loans-tab-item {{ $filter === 'pending' ? 'active' : '' }}">
            <span>{{ __('Pending Review') }}</span>
            <span class="tab-count">{{ $statusCounts['pending'] }}</span>
        </a>

        <!-- In Progress -->
        <a href="{{ route('portal.clearances.index', ['status' => 'in_progress']) }}"
           class="loans-tab-item {{ $filter === 'in_progress' ? 'active' : '' }}">
            <span>{{ __('In Progress') }}</span>
            <span class="tab-count">{{ $statusCounts['in_progress'] }}</span>
        </a>

        <!-- Cleared -->
        <a href="{{ route('portal.clearances.index', ['status' => 'cleared']) }}"
           class="loans-tab-item {{ $filter === 'cleared' ? 'active' : '' }}">
            <span>{{ __('Fully Cleared') }}</span>
            <span class="tab-count">{{ $statusCounts['cleared'] }}</span>
        </a>
    </div>

    <!-- 2. Clearance Requests List -->
    @forelse($clearances as $item)
        <a href="{{ route('portal.clearances.show', $item->id) }}" class="card border-0 shadow-xs rounded-3 mb-3 text-decoration-none">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="fw-bold text-primary font-15">{{ $item->clearance_code }}</span>
                        <span class="badge bg-label-secondary font-11">{{ __(ucfirst($item->leaving_reason)) }}</span>
                    </div>
                    <span class="badge {{ $item->status?->badgeClass() ?? 'bg-label-primary' }}">
                        {{ $item->status?->label() ?? $item->status }}
                    </span>
                </div>

                <div class="row g-2 small text-muted mb-2">
                    <div class="col-6">
                        <span class="d-block">{{ __('Last Working Day') }}:</span>
                        <strong class="text-danger">{{ $item->last_working_day?->format('Y-m-d') }}</strong>
                    </div>
                    <div class="col-6">
                        <span class="d-block">{{ __('Notice Date') }}:</span>
                        <span class="text-heading">{{ $item->resignation_date?->format('Y-m-d') ?? '-' }}</span>
                    </div>
                </div>

                <!-- Department approvals progress -->
                @php
                    $total = $item->approvals->count();
                    $cleared = $item->approvals->where('status', \Modules\HR\Enums\ClearanceApprovalStatus::CLEARED)->count();
                    $pct = $total > 0 ? round(($cleared / $total) * 100) : 0;
                @endphp
                <div class="d-flex align-items-center justify-content-between pt-2 border-top small">
                    <span class="text-muted">{{ __('Department Sign-offs') }}: <strong>{{ $cleared }}/{{ $total }}</strong></span>
                    <span class="text-primary fw-semibold d-inline-flex align-items-center gap-1">
                        {{ __('View Details') }}
                        <i class="bx {{ app()->getLocale() === 'ar' ? 'bx-left-arrow-alt' : 'bx-right-arrow-alt' }}"></i>
                    </span>
                </div>
            </div>
        </a>
    @empty
        <div class="text-center py-5">
            <div class="avatar avatar-xl bg-label-secondary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                <i class="bx bx-user-x font-32 text-secondary"></i>
            </div>
            <h6 class="fw-bold mb-1">{{ __('No clearance records found') }}</h6>
            <p class="text-muted small mb-3">{{ __('You have not submitted any exit clearance requests yet.') }}</p>
            <a href="{{ route('portal.clearances.create') }}" class="btn btn-sm btn-primary">
                <i class="bx bx-plus me-1"></i> {{ __('New Clearance Request') }}
            </a>
        </div>
    @endforelse

    <div class="mt-3">
        {{ $clearances->links() }}
    </div>
</div>
@endsection

