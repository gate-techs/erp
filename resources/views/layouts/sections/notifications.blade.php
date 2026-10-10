<!-- Global ERP Notification Toast Container -->
<div id="erpNotifyContainer" class="erp-notify-container" aria-live="polite" aria-atomic="true"></div>

<script>
(function() {
    // Global notification helper
    const icons = {
        success: 'icon-base bx bx-check-circle',
        error: 'icon-base bx bx-x-circle',
        warning: 'icon-base bx bx-error',
        info: 'icon-base bx bx-info-circle'
    };

    const defaultTitles = {
        success: "{{ __('Success') }}",
        error: "{{ __('Error') }}",
        warning: "{{ __('Warning') }}",
        info: "{{ __('Notice') }}"
    };

    function createToast({ type = 'info', title = '', message = '', duration = 4000 }) {
        const container = document.getElementById('erpNotifyContainer');
        if (!container) return;

        const toast = document.createElement('div');
        toast.className = `erp-toast erp-toast-${type}`;
        toast.setAttribute('role', 'alert');

        const toastTitle = title || defaultTitles[type] || '';
        const iconClass = icons[type] || icons.info;

        toast.innerHTML = `
            <i class="erp-toast-icon ${iconClass}"></i>
            <div class="erp-toast-content">
                ${toastTitle ? `<div class="erp-toast-title">${escapeHtml(toastTitle)}</div>` : ''}
                <div class="erp-toast-message">${escapeHtml(message)}</div>
            </div>
            <button type="button" class="erp-toast-close" aria-label="Close">&times;</button>
            <div class="erp-toast-progress">
                <div class="erp-toast-progress-bar"></div>
            </div>
        `;

        container.appendChild(toast);

        // Force reflow and show
        void toast.offsetWidth;
        toast.classList.add('show');

        const progressBar = toast.querySelector('.erp-toast-progress-bar');
        const closeBtn = toast.querySelector('.erp-toast-close');

        let remaining = duration;
        let startTime = Date.now();
        let timeoutId = null;

        function startTimer() {
            if (progressBar) {
                progressBar.style.transition = `width ${remaining}ms linear`;
                progressBar.style.width = '0%';
            }
            timeoutId = setTimeout(dismiss, remaining);
        }

        function pauseTimer() {
            clearTimeout(timeoutId);
            const elapsed = Date.now() - startTime;
            remaining = Math.max(0, remaining - elapsed);
            if (progressBar) {
                const computedWidth = window.getComputedStyle(progressBar).width;
                progressBar.style.transition = 'none';
                progressBar.style.width = computedWidth;
            }
        }

        function resumeTimer() {
            startTime = Date.now();
            startTimer();
        }

        function dismiss() {
            clearTimeout(timeoutId);
            toast.classList.remove('show');
            toast.classList.add('hide');
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 350);
        }

        toast.addEventListener('mouseenter', pauseTimer);
        toast.addEventListener('mouseleave', resumeTimer);
        closeBtn.addEventListener('click', dismiss);

        startTimer();
    }

    function escapeHtml(string) {
        if (!string) return '';
        return String(string)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    window.notify = {
        success: (message, title) => createToast({ type: 'success', message, title }),
        error: (message, title) => createToast({ type: 'error', message, title }),
        warning: (message, title) => createToast({ type: 'warning', message, title }),
        info: (message, title) => createToast({ type: 'info', message, title }),
        show: (options) => createToast(options)
    };
    window.toastr = window.notify;

    // Auto trigger on server sessions
    document.addEventListener('DOMContentLoaded', () => {
        @if (session('success'))
            window.notify.success(@json(session('success')));
        @endif

        @if (session('error'))
            window.notify.error(@json(session('error')));
        @endif

        @if (session('warning'))
            window.notify.warning(@json(session('warning')));
        @endif

        @if (session('info'))
            window.notify.info(@json(session('info')));
        @endif

        @if (session('status'))
            window.notify.info(@json(session('status')));
        @endif
    });
})();
</script>
