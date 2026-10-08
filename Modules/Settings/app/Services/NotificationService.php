<?php

namespace Modules\Settings\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Settings\Enums\NotificationAudience;
use Modules\Settings\Models\NotificationTemplate;
use Modules\Settings\Repositories\Contracts\NotificationRepositoryInterface;
use Modules\Settings\Services\Contracts\NotificationServiceInterface;
use Modules\Settings\Services\Contracts\PushNotificationServiceInterface;
use Illuminate\Support\Facades\Log;

class NotificationService implements NotificationServiceInterface
{
    public function __construct(
        private readonly NotificationRepositoryInterface $repository,
        private readonly PushNotificationServiceInterface $pushService
    ) {}

    public function getUserNotifications(int $userId): LengthAwarePaginator
    {
        return $this->repository->getForUser($userId);
    }

    public function getBroadcastData(array $filters): array
    {
        $result = $this->repository->searchBatches($filters['search'] ?? null, $filters['start'] ?? 0, $filters['length'] ?? 10);

        return [
            'total' => $this->repository->countBatches(),
            'filtered' => $result['filtered'],
            'rows' => $result['rows'],
        ];
    }

    public function sendBroadcast(array $data, int $createdBy): array
    {
        $users = $this->repository->getUsersByAudience($data['audience_type']);
        $scheduled = (bool) ($data['is_scheduled'] ?? false);
        $payload = [
            'title_ar' => $data['title_ar'],
            'title_en' => $data['title_en'] ?: $data['title_ar'],
            'message_ar' => $data['message_ar'],
            'message_en' => $data['message_en'] ?: $data['message_ar'],
            'action_url' => $data['action_url'] ?? null,
        ];

        DB::transaction(function () use ($data, $createdBy, $users, $scheduled, $payload) {
            $batch = $this->repository->createBatch([
                'created_by' => $createdBy,
                'audience_type' => $data['audience_type'],
                'audience_filter' => null,
                'payload' => $payload,
                'status' => $scheduled ? 'scheduled' : 'sent',
                'recipients_count' => $users->count(),
                'scheduled_at' => $scheduled ? $data['scheduled_at'] : null,
                'sent_at' => $scheduled ? null : now(),
            ]);

            if (! $scheduled) {
                foreach ($users as $user) {
                    $notificationId = (string) Str::uuid();
                    $this->repository->createNotification([
                        'id' => $notificationId,
                        'type' => 'general_broadcast',
                        'notifiable_type' => \App\Models\User::class,
                        'notifiable_id' => $user->id,
                        'data' => array_merge($payload, ['batch_id' => $batch->id]),
                    ]);

                    $this->repository->createTranslations([
                        ['notification_id' => $notificationId, 'locale' => 'ar', 'title' => $payload['title_ar'], 'message' => $payload['message_ar'], 'action_url' => $payload['action_url'], 'created_at' => now(), 'updated_at' => now()],
                        ['notification_id' => $notificationId, 'locale' => 'en', 'title' => $payload['title_en'], 'message' => $payload['message_en'], 'action_url' => $payload['action_url'], 'created_at' => now(), 'updated_at' => now()],
                    ]);
                }
            }
        });

        if (! $scheduled) {
            $this->pushService->sendToUsers($users->pluck('id')->all(), $payload);
        }

        return ['scheduled' => $scheduled, 'recipients' => $users->count()];
    }

    /**
     * Send notification using a pre-configured notification template code.
     *
     * @param string $templateCode The code matching settings_notification_templates.code
     * @param array $variables Key-value pairs to replace in template placeholders
     * @param mixed $recipients User instance, User ID, collection, or array of users/IDs
     * @param string|null $actionUrl Optional action URL override
     * @return array ['sent' => int, 'recipients' => int]
     */
    public function sendTemplateNotification(
        string $templateCode,
        array $variables = [],
        mixed $recipients = [],
        ?string $actionUrl = null
    ): array {
        // Built-in system template defaults so notifications always succeed even if not seeded yet in DB
        $defaultTemplates = [
            'leave_submitted' => [
                'name_ar'    => 'تقديم طلب إجازة',
                'name_en'    => 'Leave Request Submitted',
                'title_ar'   => 'تم تقديم طلب الإجازة',
                'title_en'   => 'Leave Request Submitted',
                'message_ar' => 'تم تقديم طلب إجازة جديد من {employee_name} من {start_date} إلى {end_date}.',
                'message_en' => 'A new leave request was submitted by {employee_name} from {start_date} to {end_date}.',
                'icon'       => 'bx-calendar-plus',
                'color'      => 'info',
                'action_url' => '/hr/leaves',
            ],
            'leave_approved' => [
                'name_ar'    => 'قبول طلب إجازة',
                'name_en'    => 'Leave Request Approved',
                'title_ar'   => 'تم قبول طلب الإجازة',
                'title_en'   => 'Leave Request Approved',
                'message_ar' => 'تمت الموافقة على طلب إجازتك من {start_date} إلى {end_date}.',
                'message_en' => 'Your leave request from {start_date} to {end_date} has been approved.',
                'icon'       => 'bx-calendar-check',
                'color'      => 'success',
                'action_url' => '/portal/requests',
            ],
            'leave_rejected' => [
                'name_ar'    => 'رفض طلب إجازة',
                'name_en'    => 'Leave Request Rejected',
                'title_ar'   => 'تم رفض طلب الإجازة',
                'title_en'   => 'Leave Request Rejected',
                'message_ar' => 'تم رفض طلب الإجازة الخاص بك. السبب: {rejection_reason}',
                'message_en' => 'Your leave request was rejected. Reason: {rejection_reason}',
                'icon'       => 'bx-calendar-x',
                'color'      => 'danger',
                'action_url' => '/portal/requests',
            ],
        ];

        $template = null;
        try {
            $template = NotificationTemplate::with('translations')
                ->where('code', $templateCode)
                ->first();
        } catch (\Throwable $e) {
            Log::warning("Could not query NotificationTemplate: " . $e->getMessage());
        }

        // Auto-seed template in DB if missing but defined in system defaults
        if (! $template && isset($defaultTemplates[$templateCode])) {
            try {
                $def = $defaultTemplates[$templateCode];
                $template = NotificationTemplate::create([
                    'code'        => $templateCode,
                    'type'        => $templateCode,
                    'module'      => 'hr',
                    'name_ar'     => $def['name_ar'],
                    'name_en'     => $def['name_en'],
                    'description' => $def['name_ar'],
                    'icon'        => $def['icon'],
                    'color'       => $def['color'],
                    'variables'   => array_keys($variables),
                    'is_active'   => true,
                    'is_system'   => true,
                ]);

                $template->translations()->createMany([
                    ['locale' => 'ar', 'title' => $def['title_ar'], 'message' => $def['message_ar'], 'action_url' => $def['action_url']],
                    ['locale' => 'en', 'title' => $def['title_en'], 'message' => $def['message_en'], 'action_url' => $def['action_url']],
                ]);

                $template->load('translations');
            } catch (\Throwable $e) {
                Log::warning("Could not auto-create NotificationTemplate: " . $e->getMessage());
            }
        }

        // Normalize recipients to a unique collection of active User instances
        $users = $this->resolveRecipients($recipients);
        if ($users->isEmpty()) {
            Log::warning("sendTemplateNotification: No active recipients found for [{$templateCode}].");

            return ['sent' => 0, 'recipients' => 0];
        }

        $def = $defaultTemplates[$templateCode] ?? [];
        $arTranslation = $template?->translations?->firstWhere('locale', 'ar');
        $enTranslation = $template?->translations?->firstWhere('locale', 'en');

        $replaceVariables = function (?string $text) use ($variables): string {
            if ($text === null || $text === '') {
                return '';
            }
            foreach ($variables as $key => $value) {
                $cleanKey = trim($key, '{}:');
                $strVal = (string) ($value ?? '');
                $text = str_replace(
                    ["{{$cleanKey}}", ":{$cleanKey}", "{{{$cleanKey}}}"],
                    $strVal,
                    $text
                );
            }

            return $text;
        };

        $titleAr = $replaceVariables($arTranslation?->title ?? ($template?->name_ar ?? ($def['title_ar'] ?? $templateCode)));
        $titleEn = $replaceVariables($enTranslation?->title ?? ($template?->name_en ?? ($def['title_en'] ?? $titleAr))) ?: $titleAr;
        $messageAr = $replaceVariables($arTranslation?->message ?? ($def['message_ar'] ?? ''));
        $messageEn = $replaceVariables($enTranslation?->message ?? ($def['message_en'] ?? $messageAr)) ?: $messageAr;

        $resolvedActionUrl = $actionUrl
            ?: ($arTranslation?->action_url ?? $enTranslation?->action_url ?? ($def['action_url'] ?? '/hr/leaves'));
        if ($resolvedActionUrl) {
            $resolvedActionUrl = $replaceVariables($resolvedActionUrl);
        }

        $payload = [
            'template_code' => $template?->code ?? $templateCode,
            'template_type' => $template?->type ?? $templateCode,
            'icon'          => $template?->icon ?? ($def['icon'] ?? 'bx-bell'),
            'color'         => $template?->color ?? ($def['color'] ?? 'primary'),
            'title_ar'      => $titleAr,
            'title_en'      => $titleEn,
            'message_ar'    => $messageAr,
            'message_en'    => $messageEn,
            'action_url'    => $resolvedActionUrl,
        ];

        DB::transaction(function () use ($users, $payload, $templateCode) {
            foreach ($users as $user) {
                $notificationId = (string) Str::uuid();
                $this->repository->createNotification([
                    'id'              => $notificationId,
                    'type'            => $templateCode,
                    'notifiable_type' => \App\Models\User::class,
                    'notifiable_id'   => $user->id,
                    'data'            => $payload,
                ]);

                try {
                    $this->repository->createTranslations([
                        [
                            'notification_id' => $notificationId,
                            'locale'          => 'ar',
                            'title'           => $payload['title_ar'],
                            'message'         => $payload['message_ar'],
                            'action_url'      => $payload['action_url'],
                            'created_at'      => now(),
                            'updated_at'      => now(),
                        ],
                        [
                            'notification_id' => $notificationId,
                            'locale'          => 'en',
                            'title'           => $payload['title_en'],
                            'message'         => $payload['message_en'],
                            'action_url'      => $payload['action_url'],
                            'created_at'      => now(),
                            'updated_at'      => now(),
                        ],
                    ]);
                } catch (\Throwable $e) {
                    Log::warning("Could not create notification translations: " . $e->getMessage());
                }
            }
        });

        // Trigger Web Push delivery to active recipients
        try {
            $this->pushService->sendToUsers($users->pluck('id')->all(), $payload);
        } catch (\Throwable $e) {
            Log::warning("Web Push delivery failed: " . $e->getMessage());
        }

        Log::info("sendTemplateNotification: successfully delivered [{$templateCode}] to {$users->count()} recipients.");

        return ['sent' => $users->count(), 'recipients' => $users->count()];
    }


    /**
     * Normalize various recipient inputs (User, User ID, collections, arrays) into a Collection of Users.
     */
    protected function resolveRecipients(mixed $recipients): \Illuminate\Support\Collection
    {
        if ($recipients instanceof \Illuminate\Support\Collection) {
            $items = $recipients;
        } elseif (is_array($recipients)) {
            $items = collect($recipients);
        } elseif ($recipients) {
            $items = collect([$recipients]);
        } else {
            return collect();
        }

        $userIds = [];
        $users = collect();

        foreach ($items as $item) {
            if ($item instanceof \App\Models\User) {
                $users->push($item);
            } elseif (is_numeric($item)) {
                $userIds[] = (int) $item;
            }
        }

        if (! empty($userIds)) {
            $fetched = \App\Models\User::whereIn('id', $userIds)->where('is_active', true)->get();
            $users = $users->merge($fetched);
        }

        return $users->unique('id')->filter(fn ($u) => (bool) $u->is_active);
    }
}

