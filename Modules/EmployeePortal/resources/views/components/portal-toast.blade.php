<!-- Mobile Animated Notification Toast/Modal (Success & Error) matching user design -->
<style>
    .portal-toast-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(15, 23, 42, 0.45);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        z-index: 99999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.28s ease, visibility 0.28s ease;
    }

    .portal-toast-backdrop.show {
        opacity: 1;
        visibility: visible;
    }

    .portal-toast-card {
        background: #FFFFFF;
        border-radius: 28px;
        width: 100%;
        max-width: 320px;
        padding: 30px 24px 26px 24px;
        text-align: center;
        box-shadow: 0 20px 45px rgba(0, 51, 75, 0.18), 0 4px 12px rgba(0, 0, 0, 0.05);
        transform: scale(0.85) translateY(15px);
        transition: transform 0.32s cubic-bezier(0.34, 1.56, 0.64, 1);
        position: relative;
        overflow: hidden;
    }

    .portal-toast-backdrop.show .portal-toast-card {
        transform: scale(1) translateY(0);
    }

    /* Ambient background confetti dots */
    .portal-toast-card::before {
        content: '';
        position: absolute;
        top: 14px;
        left: 28px;
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #93C5FD;
        opacity: 0.8;
    }

    .portal-toast-card::after {
        content: '';
        position: absolute;
        bottom: 45px;
        right: 32px;
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #FCD34D;
        opacity: 0.8;
    }

    /* Target / Badge container */
    .toast-icon-container {
        position: relative;
        width: 86px;
        height: 86px;
        margin: 0 auto 18px auto;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Outer glowing pulse ripple ring */
    .toast-pulse-ring {
        position: absolute;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        animation: toastRingPulse 2s infinite;
    }

    /* SUCCESS ICON STYLES */
    .toast-success .toast-pulse-ring {
        border: 4px solid rgba(56, 189, 248, 0.25);
        box-shadow: 0 0 20px rgba(56, 189, 248, 0.15);
    }

    .toast-success .toast-target-outer {
        width: 76px;
        height: 76px;
        border-radius: 50%;
        border: 4px solid #38BDF8;
        background: #FFFFFF;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        box-shadow: 0 4px 14px rgba(0, 51, 75, 0.08);
    }

    .toast-success .toast-target-inner {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        background: #00334B;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #FFFFFF;
        font-size: 24px;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.2);
        animation: toastCheckPop 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }

    .toast-success .toast-sub-badge {
        position: absolute;
        bottom: 0px;
        left: -2px;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: #FDBB22;
        border: 2.5px solid #FFFFFF;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #00334B;
        font-size: 15px;
        box-shadow: 0 2px 8px rgba(253, 187, 34, 0.45);
        animation: toastBadgeSpin 0.6s ease forwards;
    }

    /* ERROR ICON STYLES */
    .toast-error .toast-pulse-ring {
        border: 4px solid rgba(239, 68, 68, 0.2);
        background: rgba(254, 226, 226, 0.4);
        box-shadow: 0 0 20px rgba(239, 68, 68, 0.15);
    }

    .toast-error .toast-target-outer {
        width: 76px;
        height: 76px;
        border-radius: 50%;
        background: #FEE2E2;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .toast-error .toast-target-inner {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: #B91C1C;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #FFFFFF;
        font-size: 26px;
        font-weight: 900;
        box-shadow: 0 4px 12px rgba(185, 28, 28, 0.35);
        animation: toastErrorShake 0.5s ease forwards;
    }

    .toast-error .toast-sub-badge {
        position: absolute;
        bottom: 0px;
        left: -2px;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: #FDE68A;
        border: 2.5px solid #FFFFFF;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #92400E;
        font-size: 14px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    /* Text message */
    .toast-message-title {
        font-size: 16.5px;
        font-weight: 800;
        color: #00334B;
        line-height: 1.4;
        margin: 0;
        letter-spacing: -0.2px;
    }

    .toast-message-desc {
        font-size: 12px;
        color: #64748B;
        margin-top: 5px;
        font-weight: 500;
        line-height: 1.35;
    }

    /* Animations */
    @keyframes toastRingPulse {
        0%, 100% {
            transform: scale(1);
            opacity: 0.8;
        }
        50% {
            transform: scale(1.1);
            opacity: 0.4;
        }
    }

    @keyframes toastCheckPop {
        0% {
            transform: scale(0.3);
            opacity: 0;
        }
        70% {
            transform: scale(1.15);
        }
        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    @keyframes toastBadgeSpin {
        0% {
            transform: scale(0) rotate(-45deg);
        }
        100% {
            transform: scale(1) rotate(0deg);
        }
    }

    @keyframes toastErrorShake {
        0%, 100% { transform: translateX(0); }
        20%, 60% { transform: translateX(-4px); }
        40%, 80% { transform: translateX(4px); }
    }
</style>

<div id="portalMobileToast" class="portal-toast-backdrop" onclick="closePortalToast()">
    <div class="portal-toast-card" onclick="event.stopPropagation()">
        <!-- Icon Container (Dynamic: Success / Error) -->
        <div id="portalToastIconContainer" class="toast-icon-container toast-success">
            <div class="toast-pulse-ring"></div>
            <div class="toast-target-outer">
                <div class="toast-target-inner" id="portalToastInnerIcon">
                    <i class="bx bx-check"></i>
                </div>
            </div>
            <div class="toast-sub-badge" id="portalToastSubBadge">
                <i class="bx bxs-sparkles"></i>
            </div>
        </div>

        <!-- Title Message -->
        <h3 id="portalToastTitle" class="toast-message-title">
            {{ __('Operation completed successfully!') }}
        </h3>
        <p id="portalToastDesc" class="toast-message-desc" style="display: none;"></p>
    </div>
</div>

<script>
    let toastTimeout = null;

    /**
     * Show Global Mobile Toast Modal
     * @param {'success'|'error'} type 
     * @param {string} title 
     * @param {string|null} desc 
     * @param {number} durationMs (Default: 2200ms)
     */
    function showPortalToast(type = 'success', title = '', desc = null, durationMs = 2200) {
        const backdrop = document.getElementById('portalMobileToast');
        const iconContainer = document.getElementById('portalToastIconContainer');
        const innerIcon = document.getElementById('portalToastInnerIcon');
        const subBadge = document.getElementById('portalToastSubBadge');
        const titleEl = document.getElementById('portalToastTitle');
        const descEl = document.getElementById('portalToastDesc');

        if (!backdrop) return;

        // Clear existing timer
        if (toastTimeout) {
            clearTimeout(toastTimeout);
        }

        // Set type and icons
        if (type === 'error') {
            iconContainer.className = 'toast-icon-container toast-error';
            innerIcon.innerHTML = '!';
            subBadge.innerHTML = '<i class="bx bxs-star"></i>';
            titleEl.textContent = title || "{{ __('Operation could not be completed!') }}";
        } else {
            iconContainer.className = 'toast-icon-container toast-success';
            innerIcon.innerHTML = '<i class="bx bx-check"></i>';
            subBadge.innerHTML = '<i class="bx bxs-sparkles"></i>';
            titleEl.textContent = title || "{{ __('Operation completed successfully!') }}";
        }

        if (desc) {
            descEl.textContent = desc;
            descEl.style.display = 'block';
        } else {
            descEl.style.display = 'none';
        }

        // Show Modal
        backdrop.classList.add('show');

        // Auto hide after duration
        toastTimeout = setTimeout(() => {
            closePortalToast();
        }, durationMs);
    }

    function closePortalToast() {
        const backdrop = document.getElementById('portalMobileToast');
        if (backdrop) {
            backdrop.classList.remove('show');
        }
        if (toastTimeout) {
            clearTimeout(toastTimeout);
            toastTimeout = null;
        }
    }

    // Auto trigger on Laravel Session Messages
    document.addEventListener("DOMContentLoaded", function () {
        @if(session('success'))
            showPortalToast('success', "{{ session('success') }}");
        @elseif(session('error'))
            showPortalToast('error', "{{ session('error') }}");
        @elseif(isset($errors) && $errors->any())
            showPortalToast('error', "{{ $errors->first() }}");
        @endif
    });
</script>
