// Dispatcher Form JavaScript

document.addEventListener('DOMContentLoaded', function () {

    // Auto-fade notifikasi success setelah 5 detik
    const successNotif = document.querySelector('[data-notification="success"]');
    if (successNotif) {
        setTimeout(() => {
            successNotif.style.transition = 'opacity 0.4s';
            successNotif.style.opacity = '0';
            setTimeout(() => successNotif.remove(), 400);
        }, 5000);
    }

    // Cegah scroll body saat drawer terbuka
    // Alpine mengirim event ini dari x-data saat isOpen berubah
    document.addEventListener('drawer-opened', () => {
        document.body.style.overflow = 'hidden';
    });

    document.addEventListener('drawer-closed', () => {
        document.body.style.overflow = '';
    });

});