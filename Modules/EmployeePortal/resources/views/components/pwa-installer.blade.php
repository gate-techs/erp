<!-- PWA Floating Install Banner -->
<div id="pwaInstallBanner" class="pwa-install-banner">
    <div class="banner-inner">
        <div class="banner-icon-side">
            <img src="{{ asset('assets/portal/icon-192.png') }}" alt="App Icon" class="pwa-app-icon" />
        </div>
        <div class="banner-content">
            <div class="banner-title">{{ __('Install ALA ORBIT App 📲') }}</div>
            <div class="banner-desc">{{ __('Install the app on your home screen for quick, standalone access') }}</div>
        </div>
        <div class="banner-actions">
            <button type="button" id="btnInstallPwa" class="btn-install-now">{{ __('Install Now') }}</button>
            <button type="button" id="btnDismissPwa" class="btn-dismiss-banner" aria-label="✕">✕</button>
        </div>
    </div>
</div>

<!-- iOS Install Instruction Modal -->
<div id="iosInstallModal" class="ios-install-modal">
    <div class="ios-modal-card">
        <div class="ios-modal-header">
            <img src="{{ asset('assets/portal/icon-192.png') }}" alt="App Icon" class="ios-app-icon" />
            <div>
                <h5>{{ __('Install App on iPhone') }}</h5>
                <p>{{ __('Just 2 steps to add the app to your home screen') }}</p>
            </div>
            <button type="button" class="btn-close-ios" onclick="closeIosInstallModal()">✕</button>
        </div>
        <div class="ios-modal-body">
            <div class="ios-step-item">
                <div class="step-num">1</div>
                <div class="step-text">
                    {{ __('Tap the Share button') }} <strong>(Share <span class="share-icon-symbol">⎋</span>)</strong> {{ __("in Safari's bottom bar.") }}
                </div>
            </div>
            <div class="ios-step-item">
                <div class="step-num">2</div>
                <div class="step-text">
                    {{ __('Scroll down and choose') }} <strong>"{{ __('Add to Home Screen') }}" (➕)</strong>.
                </div>
            </div>
            <div class="ios-step-item">
                <div class="step-num">3</div>
                <div class="step-text">
                    {{ __('Tap') }} <strong>"{{ __('Add') }}"</strong> {{ __('at the top to add the app to your home screen!') }}
                </div>
            </div>
        </div>
        <button type="button" class="btn-confirm-ios" onclick="closeIosInstallModal()">{{ __('Got it 👍') }}</button>
    </div>
</div>

@auth
<div id="pushPermissionBanner" class="push-permission-banner" role="status">
    <div class="push-permission-icon"><i class="bx bxs-bell-ring"></i></div>
    <div class="push-permission-content">
        <div class="push-permission-title">{{ __('Enable notifications') }}</div>
        <div class="push-permission-desc">{{ __('Receive leave requests, tasks and important updates.') }}</div>
    </div>
    <button type="button" id="btnEnablePush" class="btn-enable-push">{{ __('Enable') }}</button>
    <button type="button" id="btnDismissPush" class="btn-dismiss-push" aria-label="{{ __('Close') }}">&times;</button>
</div>
@endauth


<script>
    (function () {
        // 1. Service Worker Registration & Automatic Update Check
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                window.portalServiceWorkerRegistration = navigator.serviceWorker.register('{{ route("portal.sw") }}', { scope: '/portal/' })
                    .then((reg) => {
                        console.log('✅ Astro PWA ServiceWorker registered with scope:', reg.scope);
                        // Auto-check for ServiceWorker script changes on launch
                        reg.update();

                        // Register Background Sync if supported (Android & Desktop Chromium)
                        if ('sync' in reg) {
                            reg.sync.register('sync-portal-data').catch(() => {});
                        }
                        if ('periodicSync' in reg) {
                            reg.periodicSync.register('update-notifications', {
                                minInterval: 24 * 60 * 60 * 1000 // 1 day
                            }).catch(() => {});
                        }

                        return reg;
                    })
                    .catch((err) => {
                        console.warn('⚠️ Astro PWA ServiceWorker registration failed:', err);
                    });
            });

            let refreshing = false;
            navigator.serviceWorker.addEventListener('controllerchange', () => {
                if (!refreshing) {
                    refreshing = true;
                    window.location.reload();
                }
            });
        }

        @auth
        // Push permission is requested only after the user clicks Enable.
        const pushBanner = document.getElementById('pushPermissionBanner');
        const enablePushBtn = document.getElementById('btnEnablePush');
        const dismissPushBtn = document.getElementById('btnDismissPush');
        const pushPublicKey = @json(config('services.vapid.public_key'));

        function urlBase64ToUint8Array(base64String) {
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
            const rawData = window.atob(base64);
            return Uint8Array.from([...rawData].map((character) => character.charCodeAt(0)));
        }

        async function savePushSubscription() {
            if (!('serviceWorker' in navigator) || !('PushManager' in window) || !('Notification' in window)) {
                throw new Error('{{ __('Push notifications are not supported on this device.') }}');
            }

            if (Notification.permission !== 'granted') {
                const permission = await Notification.requestPermission();
                if (permission !== 'granted') return false;
            }

            const registration = await (window.portalServiceWorkerRegistration || navigator.serviceWorker.ready);
            if (!registration || !registration.pushManager) {
                throw new Error('{{ __('The notification service is not ready. Please reload the app and try again.') }}');
            }
            const options = { userVisibleOnly: true };
            if (!pushPublicKey) {
                throw new Error('{{ __('VAPID public key is not configured.') }}');
            }
            options.applicationServerKey = urlBase64ToUint8Array(pushPublicKey);
            const subscription = await registration.pushManager.subscribe(options);
            const json = subscription.toJSON();
            const response = await fetch(@json(route('portal.push.subscribe')), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({ endpoint: json.endpoint, keys: json.keys, content_encoding: 'aes128gcm' })
            });

            if (!response.ok) {
                let details = '';
                try { details = await response.text(); } catch (error) { }
                throw new Error(`{{ __('Could not save notification subscription.') }} (${response.status}) ${details}`);
            }
            localStorage.setItem('astro_push_enabled', '1');
            return true;
        }

        async function initializePushPrompt() {
            if (!pushBanner || !enablePushBtn || !('Notification' in window)) return;
            if (Notification.permission === 'denied' || localStorage.getItem('astro_push_dismissed_at')) return;

            if (Notification.permission === 'granted') {
                try { await savePushSubscription(); pushBanner.remove(); } catch (error) { showPushError(error); }
                return;
            }

            pushBanner.classList.add('show');
        }

        enablePushBtn?.addEventListener('click', async () => {
            enablePushBtn.disabled = true;
            try {
                if (await savePushSubscription()) pushBanner?.remove();
                else enablePushBtn.disabled = false;
            } catch (error) {
                showPushError(error);
                enablePushBtn.disabled = false;
            }
        });

        function showPushError(error) {
            const description = pushBanner?.querySelector('.push-permission-desc');
            if (description) {
                description.textContent = error?.message || '{{ __('Could not enable notifications.') }}';
                description.style.color = '#B91C1C';
            }
            pushBanner?.classList.add('show');
        }

        dismissPushBtn?.addEventListener('click', () => {
            pushBanner?.classList.remove('show');
            localStorage.setItem('astro_push_dismissed_at', Date.now().toString());
        });

        window.addEventListener('load', () => setTimeout(initializePushPrompt, 700));
        @endauth

        // 2. Standalone Check
        const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
        if (isStandalone) {
            console.log('📱 Running in standalone PWA mode');
            return;
        }

        // 3. BeforeInstallPrompt handling (Android / Chrome / Edge)
        let deferredPrompt = null;
        const banner = document.getElementById('pwaInstallBanner');
        const installBtn = document.getElementById('btnInstallPwa');
        const dismissBtn = document.getElementById('btnDismissPwa');

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;

            // Check if dismissed in last 24h
            const lastDismiss = localStorage.getItem('astro_pwa_dismissed_at');
            const now = Date.now();
            if (!lastDismiss || (now - parseInt(lastDismiss)) > 86400000) {
                if (banner) banner.classList.add('show');
            }
        });

        // Click Install Action
        if (installBtn) {
            installBtn.addEventListener('click', async () => {
                if (deferredPrompt) {
                    deferredPrompt.prompt();
                    const { outcome } = await deferredPrompt.userChoice;
                    console.log('PWA install prompt outcome:', outcome);
                    deferredPrompt = null;
                    if (banner) banner.classList.remove('show');
                } else if (isIosSafari()) {
                    showIosInstallModal();
                } else {
                    alert("{{ __('To install the app on your device: open browser menu and click Install App or Add to Home Screen.') }}");
                }
            });
        }

        // Dismiss Action
        if (dismissBtn) {
            dismissBtn.addEventListener('click', () => {
                if (banner) banner.classList.remove('show');
                localStorage.setItem('astro_pwa_dismissed_at', Date.now().toString());
            });
        }

        // App Installed event
        window.addEventListener('appinstalled', () => {
            console.log('🎉 Astro PWA was installed successfully!');
            if (banner) banner.classList.remove('show');
        });

        // 4. iOS Detection
        function isIosSafari() {
            const userAgent = window.navigator.userAgent.toLowerCase();
            const isIos = /iphone|ipad|ipod/.test(userAgent);
            const isSafari = userAgent.includes('safari') && !userAgent.includes('crios') && !userAgent.includes('fxios');
            return isIos && isSafari;
        }

        window.showIosInstallModal = function () {
            const modal = document.getElementById('iosInstallModal');
            if (modal) modal.classList.add('show');
        };

        window.closeIosInstallModal = function () {
            const modal = document.getElementById('iosInstallModal');
            if (modal) modal.classList.remove('show');
        };

        // If iOS and first visit, show after 3 seconds
        if (isIosSafari() && !localStorage.getItem('astro_ios_pwa_shown')) {
            setTimeout(() => {
                if (banner) banner.classList.add('show');
                localStorage.setItem('astro_ios_pwa_shown', 'true');
            }, 3000);
        }
    })();
</script>
