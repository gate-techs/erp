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
