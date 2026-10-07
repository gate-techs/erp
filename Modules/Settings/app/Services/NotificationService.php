<?php

namespace Modules\Settings\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Settings\Enums\NotificationAudience;
use Modules\Settings\Repositories\Contracts\NotificationRepositoryInterface;
use Modules\Settings\Services\Contracts\NotificationServiceInterface;
use Modules\Settings\Services\Contracts\PushNotificationServiceInterface;

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
}
