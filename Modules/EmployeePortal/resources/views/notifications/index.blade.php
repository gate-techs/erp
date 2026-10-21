@extends('employeeportal::layouts.master')

@section('title', __('Notifications Center'))

@section('content')
<div class="portal-notif-container">
    <!-- 0. Top Navigation Header -->
    <div class="portal-notif-header">
        <a href="{{ route('portal.dashboard') }}" class="portal-notif-nav-btn" aria-label="{{ __('Back to Home') }}">
            <i class="bx {{ app()->getLocale() === 'ar' ? 'bx-chevron-right' : 'bx-chevron-left' }}"></i>
        </a>
        <h1 class="portal-notif-header-title">{{ __('Notifications Center') }}</h1>
        <a href="{{ route('portal.profile.notifications') }}" class="portal-notif-nav-btn" aria-label="{{ __('Notification Settings') }}" title="{{ __('Notification Settings') }}">
            <i class="bx bx-cog"></i>
        </a>
    </div>



    <!-- 2. Notifications Feed List -->
    <div class="portal-notif-feed-list" id="portalNotifList">
        @forelse($notifications as $item)
            <div class="notif-card-item" id="notif-card-{{ $item->id }}" data-id="{{ $item->id }}" onclick="handleNotificationClick('{{ $item->id }}', '{{ $item->action_url ?? '' }}')">
                <!-- Icon Box -->
                <div class="notif-card-icon-col">
                    <div class="notif-card-icon-box notif-theme-{{ $item->category }}">
                        <i class="bx {{ $item->icon }}"></i>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="notif-card-body-col">
                    <div class="notif-card-meta-row">
                        <div class="notif-card-tags-wrap">
                            <span class="notif-cat-chip cat-{{ $item->category }}">{{ $item->category_label }}</span>
                        </div>
                        <div class="notif-card-time-wrap">
                            <i class="bx bx-time-five"></i>
                            <span>{{ $item->relative_time }}</span>
                        </div>
                    </div>

                    <h3 class="notif-card-title-text">{{ $item->title }}</h3>
                    <p class="notif-card-message-text">{{ $item->message }}</p>

                    @if($item->action_url)
                        <div class="notif-card-cta-row">
                            <span class="notif-cta-btn">
                                <span>{{ __('View Details') }}</span>
                                <i class="bx {{ app()->getLocale() === 'ar' ? 'bx-left-arrow-alt' : 'bx-right-arrow-alt' }}"></i>
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="notif-empty-card">
                <div class="notif-empty-icon-wrap">
                    <i class="bx bx-bell-off"></i>
                </div>
                <h3 class="notif-empty-heading">{{ __('No notifications here') }}</h3>
                <p class="notif-empty-subtext">
                    {{ __('There are no notifications in this section at the moment.') }}
                </p>
                <div class="notif-empty-actions">
                    @if($active_tab !== 'all')
                        <a href="{{ route('portal.notifications.index', ['tab' => 'all']) }}" class="notif-empty-btn">
                            <i class="bx bx-layer"></i>
                            <span>{{ __('View All Notifications') }}</span>
                        </a>
                    @else
                        <a href="{{ route('portal.dashboard') }}" class="notif-empty-btn">
                            <i class="bx bx-home-alt"></i>
                            <span>{{ __('Back to Home') }}</span>
                        </a>
                    @endif
                </div>
            </div>
        @endforelse
    </div>
</div>

<style>
/* ==============================================================================
 * Modern Notifications Center (Ultra Premium Mobile Experience)
 * ============================================================================== */
.portal-notif-container {
    max-width: 500px;
    margin: 0 auto;
    padding: 0 4px 90px 4px;
}

/* 0. Top Navigation Header */
.portal-notif-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 14px;
    padding: 0 2px;
}

.portal-notif-nav-btn {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    background: #ffffff;
    border: 1px solid rgba(0, 51, 75, 0.08);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 8px rgba(0, 51, 75, 0.04);
    color: #00334B;
    font-size: 22px;
    text-decoration: none;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    flex-shrink: 0;
}

.portal-notif-nav-btn:hover {
    transform: translateY(-1px);
    background: #f8fafc;
    box-shadow: 0 4px 12px rgba(0, 51, 75, 0.08);
    border-color: rgba(0, 51, 75, 0.15);
    color: #00334B;
}

.portal-notif-nav-btn:active {
    transform: scale(0.94);
}

.portal-notif-header-title {
    font-size: 18px;
    font-weight: 800;
    color: #00334B;
    margin: 0;
    text-align: center;
    flex-grow: 1;
}

/* 1. Segmented Filter Tabs */
.portal-notif-filter-scroller {
    overflow-x: auto;
    scrollbar-width: none;
    -ms-overflow-style: none;
    margin: 0 -4px 14px -4px;
    padding: 2px 4px;
}

.portal-notif-filter-scroller::-webkit-scrollbar {
    display: none;
}

.portal-notif-filter-track {
    display: inline-flex;
    gap: 7px;
    min-width: 100%;
}

.notif-filter-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #ffffff;
    border: 1px solid #E2E8F0;
    border-radius: 14px;
    padding: 8px 13px;
    font-size: 12.5px;
    font-weight: 700;
    color: #64748B;
    text-decoration: none;
    white-space: nowrap;
    transition: all 0.2s ease;
    box-shadow: 0 2px 6px rgba(15, 41, 66, 0.02);
}

.notif-filter-pill i {
    font-size: 15px;
}

.notif-filter-pill:hover {
    color: #00334B;
    background: #f8fafc;
    border-color: #CBD5E1;
}

.notif-filter-pill.active {
    background: #00334B;
    border-color: #00334B;
    color: #ffffff;
    box-shadow: 0 4px 14px rgba(0, 51, 75, 0.18);
}

.notif-pill-badge {
    background: #F1F5F9;
    color: #475569;
    border-radius: 9px;
    padding: 1px 7px;
    font-size: 11px;
    font-weight: 800;
}

.notif-filter-pill.active .notif-pill-badge {
    background: rgba(255, 255, 255, 0.22);
    color: #ffffff;
}

/* 2. Notifications Feed List */
.portal-notif-feed-list {
    display: flex;
    flex-direction: column;
    gap: 11px;
}

.notif-card-item {
    background: #ffffff;
    border-radius: 18px;
    border: 1px solid #E2E8F0;
    box-shadow: 0 2px 8px rgba(15, 41, 66, 0.03);
    position: relative;
    overflow: hidden;
    padding: 15px 14px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    cursor: pointer;
    text-decoration: none;
    color: inherit;
    transition: all 0.2s ease;
}

.notif-card-item:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(15, 41, 66, 0.08);
    border-color: #CBD5E1;
}

/* Icon Box Themes */
.notif-card-icon-col {
    flex-shrink: 0;
}

.notif-card-icon-box {
    width: 44px;
    height: 44px;
    border-radius: 13px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
}

.notif-theme-financial {
    background: #ECFDF5;
    color: #059669;
    border: 1px solid #A7F3D0;
}

.notif-theme-workflows {
    background: #EFF6FF;
    color: #2563EB;
    border: 1px solid #BFDBFE;
}

.notif-theme-announcements {
    background: #FEF3C7;
    color: #D97706;
    border: 1px solid #FDE68A;
}

.notif-theme-system {
    background: #F3E8FF;
    color: #7C3AED;
    border: 1px solid #E9D5FF;
}

/* Card Body */
.notif-card-body-col {
    flex-grow: 1;
    min-width: 0;
}

.notif-card-meta-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    margin-bottom: 5px;
}

.notif-card-tags-wrap {
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.notif-cat-chip {
    font-size: 11px;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 6px;
}

.cat-financial {
    background: #D1FAE5;
    color: #047857;
}

.cat-workflows {
    background: #EFF6FF;
    color: #1E40AF;
}

.cat-announcements {
    background: #FDE68A;
    color: #B45309;
}

.cat-system {
    background: #E2E8F0;
    color: #475569;
}

.notif-card-time-wrap {
    font-size: 11px;
    color: #94A3B8;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
    gap: 3px;
}

.notif-card-time-wrap i {
    font-size: 12px;
}

.notif-card-title-text {
    font-size: 14.5px;
    font-weight: 800;
    color: #0F2942;
    margin: 0 0 4px 0;
    line-height: 1.35;
}

.notif-card-message-text {
    font-size: 12.5px;
    font-weight: 500;
    color: #64748B;
    margin: 0;
    line-height: 1.45;
}

.notif-card-cta-row {
    margin-top: 8px;
}

.notif-cta-btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 12px;
    font-weight: 700;
    color: #0284C7;
    background: #F0F9FF;
    border: 1px solid #BAE6FD;
    border-radius: 8px;
    padding: 3px 10px;
    transition: all 0.15s ease;
}

.notif-card-item:hover .notif-cta-btn {
    background: #0284C7;
    color: #ffffff;
    border-color: #0284C7;
}

/* Empty State Card */
.notif-empty-card {
    background: #ffffff;
    border-radius: 20px;
    border: 1px dashed #CBD5E1;
    padding: 42px 20px;
    text-align: center;
    margin-top: 8px;
}

.notif-empty-icon-wrap {
    width: 64px;
    height: 64px;
    border-radius: 20px;
    background: #F8FAFC;
    border: 1px solid #E2E8F0;
    color: #94A3B8;
    font-size: 32px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 14px;
}

.notif-empty-heading {
    font-size: 16px;
    font-weight: 800;
    color: #0F2942;
    margin: 0 0 6px 0;
}

.notif-empty-subtext {
    font-size: 13px;
    font-weight: 500;
    color: #64748B;
    margin: 0 0 20px 0;
    line-height: 1.45;
}

.notif-empty-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #00334B;
    color: #ffffff;
    border-radius: 12px;
    padding: 10px 18px;
    font-size: 13px;
    font-weight: 700;
    text-decoration: none;
    box-shadow: 0 4px 14px rgba(0, 51, 75, 0.15);
    transition: all 0.2s ease;
}

.notif-empty-btn:hover {
    background: #004566;
    color: #ffffff;
    transform: translateY(-1px);
}
</style>

<script>
const NOTIF_DELETE_URL_TEMPLATE = '{{ route("portal.notifications.destroy", ["id" => ":id"]) }}';
const CSRF_TOKEN = '{{ csrf_token() }}';

/**
 * Handle notification card click: follow redirect if action URL exists
 */
function handleNotificationClick(id, actionUrl) {
    if (actionUrl && actionUrl.length > 0) {
        window.location.href = actionUrl;
    }
}

/**
 * Delete a notification item via AJAX
 */
function deleteNotificationItem(id) {
    if (!confirm('{{ __("Are you sure you want to delete this notification?") }}')) {
        return;
    }

    const card = document.getElementById('notif-card-' + id);
    if (card) {
        card.style.opacity = '0.3';
        card.style.transform = 'scale(0.95)';
    }

    const url = NOTIF_DELETE_URL_TEMPLATE.replace(':id', id);
    fetch(url, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        }
    })
    .then(res => res.json())
    .then(data => {
        if (card) card.remove();
        showPortalToast('success', '{{ __("Removed") }}', data.message || '{{ __("Notification removed successfully.") }}');
    })
    .catch(() => {
        if (card) {
            card.style.opacity = '1';
            card.style.transform = 'none';
        }
        showPortalToast('error', '{{ __("Error") }}', '{{ __("Could not delete notification.") }}');
    });
}
</script>
@endsection
