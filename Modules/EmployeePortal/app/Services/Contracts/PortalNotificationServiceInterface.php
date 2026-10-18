<?php

namespace Modules\EmployeePortal\Services\Contracts;

use App\Models\User;

interface PortalNotificationServiceInterface
{
    /**
     * Retrieve formatted notifications context for the portal notifications center.
     */
    public function getNotificationsContext(?User $user, ?string $tab = 'all', int $perPage = 30): array;

    /**
     * Get count of unread notifications for a user.
     */
    public function getUnreadCount(?User $user): int;

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(?User $user, string $notificationId): bool;

    /**
     * Mark all notifications as read for a user.
     */
    public function markAllAsRead(?User $user): int;

    /**
     * Delete a specific notification.
     */
    public function deleteNotification(?User $user, string $notificationId): bool;

    /**
     * Clear all notifications for a user.
     */
    public function clearAll(?User $user): int;
}
