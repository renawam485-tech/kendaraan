/**
 * Toast Notification System
 * Usage: showToast('success' | 'error', 'Title', 'Message')
 */

(function () {
    // Create container once
    function getContainer() {
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            document.body.appendChild(container);
        }
        return container;
    }

    const ICONS = {
        success: `<svg class="toast-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd" d="M2.25 12a9.75 9.75 0 1 1 19.5 0 9.75 9.75 0 0 1-19.5 0Zm13.28-4.22a.75.75 0 0 1 0 1.06l-5.25 5.25a.75.75 0 0 1-1.06 0l-2.25-2.25a.75.75 0 1 1 1.06-1.06l1.72 1.72 4.72-4.72a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd"/>
                  </svg>`,
        error:   `<svg class="toast-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm-1.72 6.97a.75.75 0 1 0-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 1 0 1.06 1.06L12 13.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L13.06 12l1.72-1.72a.75.75 0 1 0-1.06-1.06L12 10.94l-1.72-1.72Z" clip-rule="evenodd"/>
                  </svg>`,
    };

    const TITLES = {
        success: 'Berhasil',
        error:   'Error',
    };

    window.showToast = function (type, title, message) {
        const container = getContainer();

        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
            ${ICONS[type] || ''}
            <div class="toast-body">
                <div class="toast-title">${title}</div>
                <div class="toast-message">${message}</div>
            </div>
            <button class="toast-close" aria-label="Tutup">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        `;

        function dismiss() {
            toast.classList.replace('show', 'hide');
            toast.addEventListener('transitionend', () => toast.remove(), { once: true });
        }

        toast.querySelector('.toast-close').addEventListener('click', dismiss);
        toast.addEventListener('click', dismiss);

        container.appendChild(toast);

        // Trigger entrance animation
        requestAnimationFrame(() => {
            requestAnimationFrame(() => toast.classList.add('show'));
        });

        // Auto-dismiss after 4s (matches progress bar)
        setTimeout(dismiss, 4000);
    };

    // Auto-fire toasts injected via data attributes on DOMContentLoaded
    document.addEventListener('DOMContentLoaded', function () {
        const triggers = document.querySelectorAll('[data-toast]');
        triggers.forEach(function (el) {
            const type    = el.dataset.toast;
            const title   = el.dataset.toastTitle   || TITLES[type] || type;
            const message = el.dataset.toastMessage || '';
            if (message) showToast(type, title, message);
        });
    });
})();
