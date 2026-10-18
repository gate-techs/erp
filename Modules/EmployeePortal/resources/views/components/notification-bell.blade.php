<a href="{{ route('portal.notifications.index') }}" class="portal-global-bell-btn {{ ($portalUnreadNotificationsCount ?? 0) > 0 ? 'has-unread' : '' }}" aria-label="{{ __('Notifications') }}">
    <i class="bx {{ ($portalUnreadNotificationsCount ?? 0) > 0 ? 'bxs-bell' : 'bx-bell' }}"></i>
    @if(($portalUnreadNotificationsCount ?? 0) > 0)
        <span class="portal-global-bell-badge" id="portalGlobalBellCount">{{ $portalUnreadNotificationsCount > 99 ? '99+' : $portalUnreadNotificationsCount }}</span>
    @endif
</a>

<style>
.portal-global-bell-btn {
    position: relative;
    width: 40px;
    height: 40px;
    border-radius: 12px;
    background: #ffffff;
    border: 1px solid rgba(0, 51, 75, 0.08);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: var(--portal-primary, #00334B);
    font-size: 20px;
    text-decoration: none;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 2px 8px rgba(0, 51, 75, 0.04);
    flex-shrink: 0;
}

.portal-global-bell-btn:hover,
.portal-global-bell-btn:focus {
    color: var(--portal-primary, #00334B);
    background: #f8fafc;
    border-color: rgba(0, 51, 75, 0.15);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 51, 75, 0.08);
}

.portal-global-bell-btn:active {
    transform: scale(0.94);
}

.portal-global-bell-btn.has-unread {
    color: var(--portal-primary, #00334B);
}

.portal-global-bell-badge {
    position: absolute;
    top: -4px;
    right: -4px;
    min-width: 18px;
    height: 18px;
    padding: 0 4px;
    border-radius: 9px;
    background: linear-gradient(135deg, #EF4444, #DC2626);
    color: #ffffff;
    font-size: 10px;
    font-weight: 700;
    line-height: 18px;
    text-align: center;
    box-shadow: 0 0 8px rgba(239, 68, 68, 0.5);
    border: 2px solid #ffffff;
    pointer-events: none;
    animation: portalBellPulse 2s infinite ease-in-out;
}

[dir="rtl"] .portal-global-bell-badge {
    right: auto;
    left: -4px;
}

@keyframes portalBellPulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.1);
    }
}
</style>
