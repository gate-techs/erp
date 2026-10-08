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
        $template = NotificationTemplate::with('translations')
            ->where('code', $templateCode)
            ->first();

        if (! $template || ! $template->is_active) {
            Log::warning("Notification template [{$templateCode}] not found or inactive.");

            return ['sent' => 0, 'recipients' => 0];
        }

        // Normalize recipients to a unique collection of active User instances
        $users = $this->resolveRecipients($recipients);
        if ($users->isEmpty()) {
            return ['sent' => 0, 'recipients' => 0];
        }

        $arTranslation = $template->translations->firstWhere('locale', 'ar');
        $enTranslation = $template->translations->firstWhere('locale', 'en');

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

        $titleAr = $replaceVariables($arTranslation?->title ?? $template->name_ar);
        $titleEn = $replaceVariables($enTranslation?->title ?? $template->name_en) ?: $titleAr;
        $messageAr = $replaceVariables($arTranslation?->message ?? '');
        $messageEn = $replaceVariables($enTranslation?->message ?? '') ?: $messageAr;

        $resolvedActionUrl = $actionUrl
            ?: ($arTranslation?->action_url ?? $enTranslation?->action_url ?? null);
        if ($resolvedActionUrl) {
            $resolvedActionUrl = $replaceVariables($resolvedActionUrl);
        }

        $payload = [
            'template_code' => $template->code,
            'template_type' => $template->type,
            'icon'          => $template->icon ?? 'bx-bell',
            'color'         => $template->color ?? 'primary',
            'title_ar'      => $titleAr,
            'title_en'      => $titleEn,
            'message_ar'    => $messageAr,
            'message_en'    => $messageEn,
            'action_url'    => $resolvedActionUrl,
        ];

        DB::transaction(function () use ($users, $payload, $template) {
            foreach ($users as $user) {
                $notificationId = (string) Str::uuid();
                $this->repository->createNotification([
                    'id'              => $notificationId,
                    'type'            => $template->code,
                    'notifiable_type' => \App\Models\User::class,
                    'notifiable_id'   => $user->id,
                    'data'            => $payload,
                ]);

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
            }
        });

        // Trigger Web Push delivery to active recipients
        $this->pushService->sendToUsers($users->pluck('id')->all(), $payload);

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

