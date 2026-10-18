<?php

namespace Modules\EmployeePortal\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Modules\EmployeePortal\Services\Contracts\PortalNotificationServiceInterface;

class PortalNotificationController extends Controller
{
    public function __construct(
        protected PortalNotificationServiceInterface $notificationService
    ) {}

    /**
     * Display the mobile notification center screen.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $tab = $request->query('tab', 'all');

        $context = $this->notificationService->getNotificationsContext($user, $tab);

        return view('employeeportal::notifications.index', $context);
    }

    /**
     * Mark a single notification as read via AJAX.
     */
    public function markAsRead(Request $request, string $id): JsonResponse
    {
        $user = Auth::user();
        $success = $this->notificationService->markAsRead($user, $id);
        $unreadCount = $this->notificationService->getUnreadCount($user);

        return response()->json([
            'success' => $success,
            'id' => $id,
            'unread_count' => $unreadCount,
            'message' => __('Notification marked as read.'),
        ]);
    }

    /**
     * Mark all notifications as read via AJAX.
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $user = Auth::user();
        $count = $this->notificationService->markAllAsRead($user);

        return response()->json([
            'success' => true,
            'affected' => $count,
            'unread_count' => 0,
            'message' => __('All notifications have been marked as read.'),
        ]);
    }

    /**
     * Delete a single notification.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $user = Auth::user();
        $success = $this->notificationService->deleteNotification($user, $id);
        $unreadCount = $this->notificationService->getUnreadCount($user);

        return response()->json([
            'success' => $success,
            'id' => $id,
            'unread_count' => $unreadCount,
            'message' => __('Notification removed successfully.'),
        ]);
    }

    /**
     * Clear all notifications for the current user.
     */
    public function clearAll(Request $request): JsonResponse
    {
        $user = Auth::user();
        $count = $this->notificationService->clearAll($user);

        return response()->json([
            'success' => true,
            'affected' => $count,
            'unread_count' => 0,
            'message' => __('All notifications cleared.'),
        ]);
    }
}
