@extends('employeeportal::layouts.master')

@section('title', __('Permissions History'))

@section('content')
<div class="perm-history-page-container">
    <!-- Top Navigation Header -->
    <div class="perm-nav-header">
        <a href="{{ route('portal.requests') }}" class="perm-back-btn" title="{{ __('Back') }}">
            <i class="bx {{ app()->getLocale() === 'ar' ? 'bx-right-arrow-alt' : 'bx-left-arrow-alt' }}"></i>
        </a>
        <h1 class="perm-header-title">{{ __('Permissions History') }}</h1>
        <a href="{{ route('portal.permissions.create') }}" class="perm-back-btn" title="{{ __('Request New Permission') }}">
            <i class="bx bx-plus"></i>
        </a>
    </div>

    <!-- 1. Monthly Balance Banner Card with Progress Bar -->
    <div class="perm-history-balance-card">
        <div class="perm-balance-top-row">
            <h2 class="perm-balance-title">{{ __('Monthly Permission Balance') }}</h2>
            <div class="perm-balance-date-pill">
                <span class="dot"></span>
                <span>{{ $balance['today_formatted'] ?? date('j F Y') }}</span>
            </div>
        </div>

        <div class="perm-balance-metrics-row">
            <!-- 1. Monthly Limit -->
            <div class="perm-metric-box highlighted">
                <span class="perm-metric-label">{{ __('Monthly Limit') }}</span>
                <span class="perm-metric-val">{{ $balance['max_monthly_hours'] }} {{ __('hrs') }}</span>
            </div>

            <!-- 2. Consumed Hours -->
            <div class="perm-metric-box">
                <span class="perm-metric-label">{{ __('Consumed') }}</span>
                <span class="perm-metric-val">{{ $balance['consumed_hours'] }} {{ __('hrs') }}</span>
            </div>

            <!-- 3. Remaining Hours -->
            <div class="perm-metric-box">
                <span class="perm-metric-label">{{ __('Remaining') }}</span>
                <span class="perm-metric-val gold">{{ $balance['remaining_hours'] }} {{ __('hrs') }}</span>
            </div>
        </div>

        <!-- Cumulative Consumption Rate Progress Bar -->
        <div class="perm-progress-section">
            <div class="perm-progress-info">
                <span class="perm-progress-label">{{ __('Cumulative Consumption Rate') }}</span>
                <span class="perm-progress-percentage">{{ $balance['consumption_percentage'] }}%</span>
            </div>
            <div class="perm-progress-track">
                <div class="perm-progress-bar" style="width: {{ $balance['consumption_percentage'] }}%;"></div>
            </div>
        </div>
    </div>

    <!-- 2. Status Filter Tabs (Horizontal Scroller) -->
    <div class="perm-tabs-scroller">
        <!-- All Requests -->
        <a href="{{ route('portal.permissions.index', ['status' => 'all']) }}"
           class="perm-tab-item {{ $currentFilter === 'all' ? 'active' : '' }}">
            <span>{{ __('All Requests') }}</span>
            <span class="tab-count">{{ $statusCounts['all'] }}</span>
        </a>

        <!-- Under Review -->
        <a href="{{ route('portal.permissions.index', ['status' => 'pending']) }}"
           class="perm-tab-item {{ $currentFilter === 'pending' ? 'active' : '' }}">
            <span>{{ __('Under Review') }}</span>
            <span class="tab-count">{{ $statusCounts['pending'] }}</span>
        </a>

        <!-- Approved -->
        <a href="{{ route('portal.permissions.index', ['status' => 'approved']) }}"
           class="perm-tab-item {{ $currentFilter === 'approved' ? 'active' : '' }}">
            <span>{{ __('Approved Requests') }}</span>
            <span class="tab-count">{{ $statusCounts['approved'] }}</span>
        </a>

        <!-- Rejected -->
        <a href="{{ route('portal.permissions.index', ['status' => 'rejected']) }}"
           class="perm-tab-item {{ $currentFilter === 'rejected' ? 'active' : '' }}">
            <span>{{ __('Rejected Requests') }}</span>
            <span class="tab-count">{{ $statusCounts['rejected'] }}</span>
        </a>

        <!-- Emergency -->
        <a href="{{ route('portal.permissions.index', ['status' => 'emergency']) }}"
           class="perm-tab-item {{ $currentFilter === 'emergency' ? 'active' : '' }}">
            <span>{{ __('Emergency Requests') }}</span>
            <span class="tab-count">{{ $statusCounts['emergency'] }}</span>
        </a>
    </div>

    <!-- 3. Informative Review Notice Banner -->
    <div class="perm-notice-banner">
        <i class="bx bx-info-circle"></i>
        <span>{{ __('Pending requests usually take 24 to 48 business hours for final approval.') }}</span>
    </div>

    <!-- 4. Permission Records List -->
    <div class="perm-records-list">
        @forelse($permissions as $perm)
            <a href="{{ route('portal.permissions.show', $perm->id) }}" class="perm-record-card text-decoration-none">
                <!-- Top Header: Title, Code, Icon & Status Badge -->
                <div class="perm-record-top">
                    <!-- Right: Icon and Title Group -->
                    <div class="perm-record-type-col">
                        <div class="perm-record-icon-circle {{ $perm->icon_class }}">
                            <i class="bx {{ $perm->icon_bx }}"></i>
                        </div>
                        <div class="perm-record-titles">
                            <h3 class="perm-record-main-title">{{ $perm->display_title }}</h3>
                            <span class="perm-record-code">{{ __('Request No. :code', ['code' => $perm->request_code]) }}</span>
                        </div>
                    </div>

                    <!-- Left: Status Badge -->
                    <div>
                        @if($perm->is_pending_status)
                            <span class="perm-badge-pending">{{ __('Under Review') }}</span>
                        @elseif($perm->is_approved_status)
                            <span class="perm-badge-approved">{{ __('Approved') }}</span>
                        @elseif($perm->is_rejected_status)
                            <span class="perm-badge-rejected">
                                <i class="bx bx-x-circle"></i>
                                <span>{{ __('Request Rejected') }}</span>
                            </span>
                        @else
                            <span class="perm-badge-pending">{{ $perm->status->label() }}</span>
                        @endif
                    </div>
                </div>

                <!-- Bottom Bar: Date & Time Range & View Details -->
                <div class="perm-record-time-bar">
                    <div>
                        <span>{{ $perm->formatted_date }}</span>
                        <span class="perm-record-time-slot">({{ $perm->formatted_from }} - {{ $perm->formatted_to }})</span>
                    </div>
                    <div class="perm-view-detail-hint">
                        <span>{{ __('Details') }}</span>
                        <i class="bx {{ app()->getLocale() === 'ar' ? 'bx-chevron-left' : 'bx-chevron-right' }}"></i>
                    </div>
                </div>

                <!-- Optional Rejection Reason -->
                @if($perm->is_rejected_status && !empty($perm->rejection_reason))
                    <div class="perm-rejection-box">
                        <div class="perm-rejection-title">
                            <i class="bx bx-info-circle"></i>
                            <span>{{ __('Rejection Reason:') }}</span>
                        </div>
                        <p class="perm-rejection-text">{{ $perm->rejection_reason }}</p>
                    </div>
                @endif
            </a>
        @empty
            <div class="text-center py-5 bg-white rounded-4 border shadow-sm p-4 mt-2">
                <div class="mb-3">
                    <i class="bx bx-receipt text-muted" style="font-size: 48px;"></i>
                </div>
                <h4 class="fw-bold mb-1" style="font-size: 15px; color: #002A44;">{{ __('No permission requests found in this section.') }}</h4>
                <p class="text-muted small mb-4">{{ __('You can submit a new permission request easily anytime.') }}</p>
                <a href="{{ route('portal.permissions.create') }}" class="btn-submit-perm-custom mx-auto" style="max-width: 260px; height: 46px; font-size: 14px;">
                    <i class="bx bx-plus"></i>
                    <span>{{ __('Request New Permission') }}</span>
                </a>
            </div>
        @endforelse

        @if($permissions->hasPages())
            <div class="d-flex justify-content-center mt-3">
                {{ $permissions->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const activeTab = document.querySelector('.perm-tab-item.active');
        const scroller = document.querySelector('.perm-tabs-scroller');
        if (activeTab && scroller) {
            const tabLeft = activeTab.offsetLeft;
            const tabWidth = activeTab.offsetWidth;
            const scrollerWidth = scroller.offsetWidth;
            scroller.scrollTo({
                left: tabLeft - (scrollerWidth / 2) + (tabWidth / 2),
                behavior: 'smooth'
            });
        }
    });
</script>
@endpush
