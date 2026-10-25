

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
