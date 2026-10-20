<?php

namespace Modules\EmployeePortal\Services;

use Modules\EmployeePortal\Services\Contracts\PortalPwaServiceInterface;

class PortalPwaService implements PortalPwaServiceInterface
{
    /**
     * Get web app manifest array.
     *
     * @return array
     */
    public function getManifest(): array
    {
        $appName = 'ALA ORBIT';
        $shortName = 'ALA ORBIT';

        return [
            'name'                        => $appName,
            'short_name'                  => $shortName,
            'description'                 => 'منظومة الموارد البشرية وبوابة الخدمة الذاتية - ALA ORBIT',
            'start_url'                   => route('portal.splash', [], false),
            'id'                          => '/portal/',
            'scope'                       => '/portal/',
            'display'                     => 'standalone',
            'display_override'            => ['standalone', 'window-controls-overlay'],
            'orientation'                 => 'portrait',
            'background_color'            => '#00334B',
            'theme_color'                 => '#00334B',
            'prefer_related_applications' => false,
            'icons'                       => [
                [
                    'src'     => '/assets/portal/icon-192.png',
                    'sizes'   => '192x192',
                    'type'    => 'image/png',
                    'purpose' => 'any',
                ],
                [
                    'src'     => '/assets/portal/icon-512.png',
                    'sizes'   => '512x512',
                    'type'    => 'image/png',
                    'purpose' => 'any',
                ],
                [
                    'src'     => '/assets/portal/icon-maskable.png',
                    'sizes'   => '512x512',
                    'type'    => 'image/png',
                    'purpose' => 'maskable',
                ],
            ],
            'categories'                  => ['business', 'productivity', 'education'],
            'lang'                        => 'ar',
            'dir'                         => 'rtl',
        ];
    }

    /**
     * Get service worker javascript string.
     *
     * @return string
     */
    public function getServiceWorkerScript(): string
    {
        return <<<'JS'
const CACHE_NAME = 'astro-portal-v4';
const OFFLINE_URL = '/portal/offline';

const PRECACHE_ASSETS = [
    '/portal/offline',
    '/portal/splash',
    '/portal/login',
    '/assets/portal/icon-192.png',
    '/assets/portal/icon-512.png',
    '/assets/portal/icon-maskable.png',
    '/assets/portal/apple-touch-icon.png',
    '/assets/portal/splash-frame.svg',
    '/assets/portal/animated-splash.svg',
    '/assets/portal/teacher-avatar.png',
    '/assets/portal/male-avatar.png',
    '/assets/portal/female-avatar.png',
    '/assets/portal/header-emblem.png'
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => cache.addAll(PRECACHE_ASSETS)).then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => {
            return Promise.all(
                keys.map((key) => {
                    if (key !== CACHE_NAME) return caches.delete(key);
                })
            );
        }).then(() => self.clients.claim())
    );
});

self.addEventListener('push', (event) => {
    let payload = { title: 'ALA ORBIT', body: 'You have a new notification.' };

    try {
        if (event.data) payload = event.data.json();
    } catch (error) {
        payload.body = event.data ? event.data.text() : payload.body;
    }

    event.waitUntil(
        self.registration.showNotification(payload.title || 'ALA ORBIT', {
            body: payload.body || '',
            icon: '/assets/portal/icon-192.png',
            badge: '/assets/portal/icon-192.png',
            data: { url: payload.action_url || '/portal/dashboard' },
            dir: 'auto',
            lang: 'ar'
        })
    );
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const targetUrl = event.notification.data?.url || '/portal/dashboard';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
            for (const client of clientList) {
                if ('focus' in client) {
                    client.navigate(targetUrl);
                    return client.focus();
                }
            }
            return clients.openWindow(targetUrl);
        })
    );
});

self.addEventListener('fetch', (event) => {
    if (event.request.method !== 'GET') return;

    // 1. Navigation requests: Network First, fallback to offline screen
    if (event.request.mode === 'navigate') {
        event.respondWith(
            fetch(event.request).catch(() => caches.match(OFFLINE_URL))
        );
        return;
    }

    const url = event.request.url;

    // 2. CSS, JS, and Portal assets: Network First with Cache Fallback (Ensures newest styles always render)
    if (
        event.request.destination === 'style' ||
        event.request.destination === 'script' ||
        url.includes('/assets/portal/') ||
        url.match(/\.(css|js)(\?.*)?$/)
    ) {
        event.respondWith(
            fetch(event.request)
                .then((networkRes) => {
                    if (networkRes && networkRes.status === 200) {
                        const clone = networkRes.clone();
                        caches.open(CACHE_NAME).then((cache) => cache.put(event.request, clone));
                    }
                    return networkRes;
                })
                .catch(() => caches.match(event.request))
        );
        return;
    }

    // 3. Static Media (Fonts, Images, Icons): Cache First with background revalidation
    event.respondWith(
        caches.match(event.request).then((cached) => {
            const fetchPromise = fetch(event.request).then((networkRes) => {
                if (networkRes && networkRes.status === 200) {
                    const clone = networkRes.clone();
                    caches.open(CACHE_NAME).then((cache) => cache.put(event.request, clone));
                }
                return networkRes;
            }).catch(() => null);

            return cached || fetchPromise;
        }).catch(() => caches.match(event.request))
    );
});
JS;
    }

}
