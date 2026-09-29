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
        $appName = 'AstroLeaders';
        $shortName = 'AstroLeaders';

        return [
            'name'                        => $appName,
            'short_name'                  => $shortName,
            'description'                 => 'منظومة الموارد البشرية وبوابة الخدمة الذاتية لحضانة أسترو ليدرز',
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
const CACHE_NAME = 'astro-portal-v3';
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

self.addEventListener('fetch', (event) => {
    if (event.request.method !== 'GET') return;

    if (event.request.mode === 'navigate') {
        event.respondWith(
            fetch(event.request).catch(() => caches.match(OFFLINE_URL))
        );
        return;
    }

    event.respondWith(
        caches.match(event.request).then((cached) => {
            if (cached) return cached;
            return fetch(event.request).then((res) => {
                if (res && res.status === 200 && event.request.url.match(/\.(css|js|svg|png|jpg|ico|woff2)$/)) {
                    const clone = res.clone();
                    caches.open(CACHE_NAME).then((cache) => cache.put(event.request, clone));
                }
                return res;
            });
        }).catch(() => caches.match(event.request))
    );
});
JS;
    }

}
