<!-- PWA Floating Install Banner -->
<div id="pwaInstallBanner" class="pwa-install-banner">
    <div class="banner-inner">
        <div class="banner-icon-side">
            <img src="{{ asset('assets/portal/icon-192.png') }}" alt="App Icon" class="pwa-app-icon" />
        </div>
        <div class="banner-content">
            <div class="banner-title">{{ __('Install Astro Leaders App 📲') }}</div>
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

<style>
    /* PWA Install Banner Styles */
    .pwa-install-banner {
        position: fixed;
        bottom: 74px;
        left: 12px;
        right: 12px;
        max-width: 420px;
        margin: 0 auto;
        background: rgba(0, 51, 75, 0.95);
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        border: 1.5px solid rgba(253, 187, 34, 0.35);
        box-shadow: 0 10px 30px rgba(0, 30, 45, 0.45);
        border-radius: 18px;
        padding: 12px 14px;
        z-index: 9999;
        display: none;
        animation: pwaSlideUp 0.35s ease forwards;
        color: #ffffff;
        direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};
    }

    .pwa-install-banner.show {
        display: block !important;
    }

    @keyframes pwaSlideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .banner-inner {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .banner-icon-side .pwa-app-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        border: 1.5px solid #FDBB22;
        box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        flex-shrink: 0;
    }

    .banner-content {
        flex: 1;
        min-width: 0;
    }

    .banner-title {
        font-size: 13.5px;
        font-weight: 800;
        color: #FDBB22;
        line-height: 1.25;
        margin-bottom: 2px;
    }

    .banner-desc {
        font-size: 11px;
        color: #C2D7E5;
        font-weight: 500;
        line-height: 1.3;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .banner-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-shrink: 0;
    }

    .btn-install-now {
        background: linear-gradient(135deg, #FDBB22 0%, #F59E0B 100%);
        color: #00334B;
        border: none;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 800;
        padding: 8px 14px;
        cursor: pointer;
        transition: transform 0.15s ease;
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.35);
        white-space: nowrap;
    }

    .btn-install-now:active {
        transform: scale(0.95);
    }

    .btn-dismiss-banner {
        background: rgba(255, 255, 255, 0.15);
        color: #ffffff;
        border: none;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        font-size: 12px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* iOS Modal Styles */
    .ios-install-modal {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        z-index: 10000;
        display: none;
        align-items: flex-end;
        justify-content: center;
        padding: 16px;
        direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};
    }

    .ios-install-modal.show {
        display: flex !important;
    }

    .ios-modal-card {
        background: #ffffff;
        border-radius: 24px;
        max-width: 420px;
        width: 100%;
        padding: 20px 18px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
        animation: pwaSlideUp 0.3s ease;
    }

    .ios-modal-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
        border-bottom: 1px solid #E2E8F0;
        padding-bottom: 12px;
        position: relative;
    }

    .ios-app-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        border: 2px solid #FDBB22;
    }

    .ios-modal-header h5 {
        font-size: 16px;
        font-weight: 800;
        color: #00334B;
        margin: 0 0 2px 0;
    }

    .ios-modal-header p {
        font-size: 12px;
        color: #64748B;
        margin: 0;
    }

    .btn-close-ios {
        position: absolute;
        top: 0;
        left: 0;
        background: #F1F5F9;
        border: none;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        font-size: 14px;
        color: #64748B;
        cursor: pointer;
    }

    .ios-step-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 14px;
    }

    .step-num {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: #00334B;
        color: #FDBB22;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        flex-shrink: 0;
    }

    .step-text {
        font-size: 13px;
        color: #334155;
        line-height: 1.5;
    }

    .share-icon-symbol {
        display: inline-block;
        font-size: 16px;
        color: #007AFF;
        vertical-align: middle;
    }

    .btn-confirm-ios {
        width: 100%;
        background: #00334B;
        color: #ffffff;
        border: none;
        border-radius: 14px;
        padding: 12px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        margin-top: 8px;
    }
</style>

<script>
    (function () {
        // 1. Service Worker Registration
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('{{ route("portal.sw") }}', { scope: '/portal/' })
                    .then((reg) => {
                        console.log('✅ Astro PWA ServiceWorker registered with scope:', reg.scope);
                    })
                    .catch((err) => {
                        console.warn('⚠️ Astro PWA ServiceWorker registration failed:', err);
                    });
            });
        }

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
                    alert('لتثبيت التطبيق على جهازك:\nافتح قائمة المتصفح (⋮) ثم اضغط "تثبيت التطبيق" أو "إضافة إلى الشاشة الرئيسية".');
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
