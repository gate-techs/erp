<?php

namespace Modules\Settings\Services;

use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;
use Modules\Settings\Repositories\Contracts\PushSubscriptionRepositoryInterface;
use Modules\Settings\Services\Contracts\PushNotificationServiceInterface;

class PushNotificationService implements PushNotificationServiceInterface
{
    public function __construct(
        private readonly PushSubscriptionRepositoryInterface $repository
    ) {}

    public function sendToUsers(array $userIds, array $payload): array
    {
        $subscriptions = $this->repository->forUsers($userIds);
        $publicKey = (string) config('services.vapid.public_key');
        $privateKey = (string) config('services.vapid.private_key');
        $subject = (string) config('services.vapid.subject');

        if ($subscriptions->isEmpty()) {
            return ['sent' => 0, 'failed' => 0, 'skipped' => count($userIds)];
        }

        if ($publicKey === '' || $privateKey === '' || $subject === '') {
            Log::warning('Web Push skipped because VAPID configuration is incomplete.');

            return ['sent' => 0, 'failed' => 0, 'skipped' => $subscriptions->count()];
        }

        try {
            $webPush = new WebPush([
                'VAPID' => [
                    'subject' => $subject,
                    'publicKey' => $publicKey,
                    'privateKey' => $privateKey,
                ],
            ]);

            $message = json_encode([
                'title' => $payload['title_' . (app()->getLocale() === 'en' ? 'en' : 'ar')] ?? $payload['title_ar'],
                'body' => $payload['message_' . (app()->getLocale() === 'en' ? 'en' : 'ar')] ?? $payload['message_ar'],
                'action_url' => $payload['action_url'] ?? '/portal/dashboard',
            ], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

            foreach ($subscriptions as $subscription) {
                $webPush->queueNotification(
                    Subscription::create([
                        'endpoint' => $subscription->endpoint,
                        'keys' => [
                            'p256dh' => $subscription->public_key,
                            'auth' => $subscription->auth_token,
                        ],
                        'contentEncoding' => $subscription->content_encoding ?: 'aes128gcm',
                    ]),
                    $message,
                    ['TTL' => 3600]
                );
            }

            $sent = 0;
            $failed = 0;
            foreach ($webPush->flush() as $report) {
                if ($report->isSuccess()) {
                    $sent++;
                } else {
                    $failed++;
                    if ($report->isSubscriptionExpired()) {
                        $this->repository->deleteByEndpointHash(hash('sha256', $report->getEndpoint()));
                    }

                    Log::warning('Web Push delivery failed.', [
                        'endpoint' => $report->getEndpoint(),
                        'reason' => $report->getReason(),
                    ]);
                }
            }

            return ['sent' => $sent, 'failed' => $failed, 'skipped' => 0];
        } catch (\Throwable $exception) {
            Log::error('Web Push delivery exception.', [
                'message' => $exception->getMessage(),
                'user_ids' => $userIds,
            ]);

            return ['sent' => 0, 'failed' => $subscriptions->count(), 'skipped' => 0];
        }
    }
}
