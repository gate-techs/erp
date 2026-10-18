<?php

namespace Modules\EmployeePortal\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\EmployeePortal\Services\Contracts\PortalNotificationServiceInterface;

class PortalNotificationService implements PortalNotificationServiceInterface
{
    /**
     * Retrieve formatted notifications context for the portal notifications center.
     */
    public function getNotificationsContext(?User $user, ?string $tab = 'all', int $perPage = 30): array
    {
        if (!$user) {
            return [
                'active_tab' => $tab ?? 'all',
                'counts' => [
                    'all' => 0,
                    'unread' => 0,
                    'financial' => 0,
                    'workflows' => 0,
                    'announcements' => 0,
                ],
                'notifications' => collect([]),
                'total_unread' => 0,
            ];
        }

        // Query all notifications for this user
        $rawNotifications = DB::table('notifications')
            ->where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        // If user has zero notifications in DB, seed a few helpful onboarding welcome notices
        if ($rawNotifications->isEmpty()) {
            $this->seedInitialNotifications($user);
            $rawNotifications = DB::table('notifications')
                ->where('notifiable_type', User::class)
                ->where('notifiable_id', $user->id)
                ->orderByDesc('created_at')
                ->get();
        }

        $isArabic = app()->getLocale() === 'ar';

        // Transform raw notifications into formatted presentation DTOs
        $formatted = $rawNotifications->map(function ($row) use ($isArabic) {
            $data = is_string($row->data) ? json_decode($row->data, true) : (array) $row->data;
            if (!is_array($data)) {
                $data = [];
            }

            $category = $this->determineCategory($row->type, $data);
            $isRead = !is_null($row->read_at);

            $title = $this->extractLocalizedText($data, 'title', $isArabic, __('System Notification'));
            $message = $this->extractLocalizedText($data, 'message', $isArabic, '');
            if (empty($message)) {
                $message = $this->extractLocalizedText($data, 'body', $isArabic, __('No description provided.'));
            }

            $createdAt = Carbon::parse($row->created_at)->setTimezone('Asia/Kuwait');
            $relativeTime = $createdAt->locale(app()->getLocale())->diffForHumans();
            $formattedDate = $createdAt->locale(app()->getLocale())->translatedFormat('d M Y - h:i A');

            $meta = $this->getCategoryMeta($category);

            return (object) [
                'id' => (string) $row->id,
                'type' => (string) $row->type,
                'category' => $category,
                'category_label' => $meta['label'],
                'icon' => $meta['icon'],
                'color_class' => $meta['color_class'],
                'title' => $title,
                'message' => $message,
                'action_url' => $data['action_url'] ?? null,
                'is_read' => $isRead,
                'created_at' => $createdAt,
                'relative_time' => $relativeTime,
                'formatted_date' => $formattedDate,
            ];
        });

        // Compute counts
        $counts = [
            'all' => $formatted->count(),
            'unread' => $formatted->where('is_read', false)->count(),
            'financial' => $formatted->where('category', 'financial')->count(),
            'workflows' => $formatted->where('category', 'workflows')->count(),
            'announcements' => $formatted->where('category', 'announcements')->count(),
        ];

        // Filter by tab
        $activeTab = in_array($tab, ['all', 'unread', 'financial', 'workflows', 'announcements'], true) ? $tab : 'all';

        $filtered = match ($activeTab) {
            'unread' => $formatted->where('is_read', false)->values(),
            'financial' => $formatted->where('category', 'financial')->values(),
            'workflows' => $formatted->where('category', 'workflows')->values(),
            'announcements' => $formatted->where('category', 'announcements')->values(),
            default => $formatted->values(),
        };

        return [
            'active_tab' => $activeTab,
            'counts' => $counts,
            'notifications' => $filtered,
            'total_unread' => $counts['unread'],
        ];
    }

    /**
     * Get count of unread notifications for a user.
     */
    public function getUnreadCount(?User $user): int
    {
        if (!$user) {
            return 0;
        }

        return DB::table('notifications')
            ->where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->whereNull('read_at')
            ->count();
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(?User $user, string $notificationId): bool
    {
        if (!$user) {
            return false;
        }

        $affected = DB::table('notifications')
            ->where('id', $notificationId)
            ->where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->whereNull('read_at')
            ->update([
                'read_at' => Carbon::now('Asia/Kuwait'),
                'updated_at' => Carbon::now('Asia/Kuwait'),
            ]);

        return $affected > 0;
    }

    /**
     * Mark all notifications as read for a user.
     */
    public function markAllAsRead(?User $user): int
    {
        if (!$user) {
            return 0;
        }

        return DB::table('notifications')
            ->where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->whereNull('read_at')
            ->update([
                'read_at' => Carbon::now('Asia/Kuwait'),
                'updated_at' => Carbon::now('Asia/Kuwait'),
            ]);
    }

    /**
     * Delete a specific notification.
     */
    public function deleteNotification(?User $user, string $notificationId): bool
    {
        if (!$user) {
            return false;
        }

        $deleted = DB::table('notifications')
            ->where('id', $notificationId)
            ->where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->delete();

        return $deleted > 0;
    }

    /**
     * Clear all notifications for a user.
     */
    public function clearAll(?User $user): int
    {
        if (!$user) {
            return 0;
        }

        return DB::table('notifications')
            ->where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->delete();
    }

    /**
     * Determine category based on type and content data.
     */
    protected function determineCategory(string $type, array $data): string
    {
        $haystack = strtolower($type . ' ' . json_encode($data));

        if (
            str_contains($haystack, 'payroll') ||
            str_contains($haystack, 'salary') ||
            str_contains($haystack, 'payslip') ||
            str_contains($haystack, 'loan') ||
            str_contains($haystack, 'advance') ||
            str_contains($haystack, 'reward') ||
            str_contains($haystack, 'bonus') ||
            str_contains($haystack, 'deduction')
        ) {
            return 'financial';
        }

        if (
            str_contains($haystack, 'leave') ||
            str_contains($haystack, 'permission') ||
            str_contains($haystack, 'attendance') ||
            str_contains($haystack, 'punch') ||
            str_contains($haystack, 'shift') ||
            str_contains($haystack, 'request') ||
            str_contains($haystack, 'workflow')
        ) {
            return 'workflows';
        }

        if (
            str_contains($haystack, 'memo') ||
            str_contains($haystack, 'circular') ||
            str_contains($haystack, 'policy') ||
            str_contains($haystack, 'announcement') ||
            str_contains($haystack, 'broadcast') ||
            str_contains($haystack, 'admin')
        ) {
            return 'announcements';
        }

        return 'system';
    }

    /**
     * Category visual and semantic attributes.
     */
    protected function getCategoryMeta(string $category): array
    {
        return match ($category) {
            'financial' => [
                'label' => __('Financial'),
                'icon' => 'bx-credit-card',
                'color_class' => 'icon-financial',
            ],
            'workflows' => [
                'label' => __('Workflows'),
                'icon' => 'bx-calendar-check',
                'color_class' => 'icon-workflow',
            ],
            'announcements' => [
                'label' => __('Announcements'),
                'icon' => 'bx-megaphone',
                'color_class' => 'icon-announcement',
            ],
            default => [
                'label' => __('System Alert'),
                'icon' => 'bx-bell',
                'color_class' => 'icon-system',
            ],
        };
    }

    /**
     * Extract localized text from notification data payload.
     */
    protected function extractLocalizedText(array $data, string $key, bool $isArabic, string $fallback): string
    {
        if ($isArabic) {
            if (!empty($data[$key . '_ar'])) {
                return (string) $data[$key . '_ar'];
            }
        } else {
            if (!empty($data[$key . '_en'])) {
                return (string) $data[$key . '_en'];
            }
        }

        if (!empty($data[$key])) {
            return (string) $data[$key];
        }

        if (!empty($data['title_ar']) && $key === 'title') {
            return (string) $data['title_ar'];
        }

        if (!empty($data['message_ar']) && $key === 'message') {
            return (string) $data['message_ar'];
        }

        return $fallback;
    }

    /**
     * Seed initial dynamic notifications if user has zero records.
     */
    protected function seedInitialNotifications(User $user): void
    {
        $now = Carbon::now('Asia/Kuwait');

        $initialItems = [
            [
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'type' => 'announcement_policy',
                'data' => [
                    'title' => 'Nursery Internal Regulations Updated',
                    'message' => 'Leave regulations and nursery policies have been updated. Please review them.',
                    'action_url' => route('portal.profile.policies'),
                ],
                'created_at' => $now->copy()->subHours(2),
                'read_at' => null,
            ],
            [
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'type' => 'payroll_summary',
                'data' => [
                    'title' => 'Monthly Payslip Notification',
                    'message' => 'Your payslip and allowances for the current month are now available.',
                    'action_url' => route('portal.profile.salary'),
                ],
                'created_at' => $now->copy()->subDay(),
                'read_at' => null,
            ],
            [
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'type' => 'attendance_punch',
                'data' => [
                    'title' => 'Daily Attendance Punch Reminder',
                    'message' => 'Reminder: Morning shift starts soon. Please record your punch via the portal.',
                    'action_url' => route('portal.dashboard'),
                ],
                'created_at' => $now->copy()->subDays(2),
                'read_at' => $now->copy()->subDays(2)->addMinutes(15),
            ],
            [
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'type' => 'reward_granted',
                'data' => [
                    'title' => 'New Excellence Reward Granted',
                    'message' => 'A certificate of appreciation and excellence award have been logged in your profile.',
                    'action_url' => route('portal.profile.rewards'),
                ],
                'created_at' => $now->copy()->subDays(4),
                'read_at' => $now->copy()->subDays(3),
            ],
        ];

        foreach ($initialItems as $item) {
            DB::table('notifications')->insert([
                'id' => $item['id'],
                'type' => $item['type'],
                'notifiable_type' => User::class,
                'notifiable_id' => $user->id,
                'data' => json_encode($item['data'], JSON_UNESCAPED_UNICODE),
                'read_at' => $item['read_at'],
                'created_at' => $item['created_at'],
                'updated_at' => $item['created_at'],
            ]);
        }
    }
}
