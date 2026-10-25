<a href="{{ route('portal.notifications.index') }}" class="portal-global-bell-btn {{ ($portalUnreadNotificationsCount ?? 0) > 0 ? 'has-unread' : '' }}" aria-label="{{ __('Notifications') }}">
    <i class="bx {{ ($portalUnreadNotificationsCount ?? 0) > 0 ? 'bxs-bell' : 'bx-bell' }}"></i>
    @if(($portalUnreadNotificationsCount ?? 0) > 0)
        <span class="portal-global-bell-badge" id="portalGlobalBellCount">{{ $portalUnreadNotificationsCount > 99 ? '99+' : $portalUnreadNotificationsCount }}</span>
    @endif
</a>
