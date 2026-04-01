/**
 * vehicles.js — Admin Kelola Unit
 * Digunakan oleh: resources/views/admin/vehicles/index.blade.php
 */

/* ──────────────────────────────────────────
   MODAL HELPERS
────────────────────────────────────────── */

/**
 * Buka modal berdasarkan ID-nya.
 * @param {string} id  — ID elemen modal
 */
function openModal(id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    // Kunci scroll body supaya konten di belakang tidak ikut scroll
    document.body.style.overflow = 'hidden';
}

/**
 * Tutup modal berdasarkan ID-nya.
 * @param {string} id  — ID elemen modal
 */
function closeModal(id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    // Kembalikan scroll body
    document.body.style.overflow = '';
}

/**
 * Buka modal edit dan isi field-nya.
 * @param {number} id
 * @param {string} name
 * @param {string} licensePlate
 * @param {string} type
 * @param {string} assetStatus
 * @param {string} notes
 */
function openEditModal(id, name, licensePlate, type, assetStatus, notes) {
    const form = document.getElementById('form-edit');
    if (!form) return;

    form.action = '/admin/vehicles/' + id;
    document.getElementById('edit-name').value          = name;
    document.getElementById('edit-license-plate').value = licensePlate;
    document.getElementById('edit-notes').value         = notes;

    // Set pilihan select Tipe
    const typeSelect = document.getElementById('edit-type');
    for (let opt of typeSelect.options) {
        opt.selected = (opt.value === type);
    }

    // Set pilihan select Status Aset
    const statusSelect = document.getElementById('edit-asset-status');
    for (let opt of statusSelect.options) {
        opt.selected = (opt.value === assetStatus);
    }

    openModal('modal-edit');
}

/* ──────────────────────────────────────────
   INISIALISASI SETELAH DOM SIAP
────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', function () {

    /* --- Tutup modal saat klik backdrop (area di luar kotak) --- */
    document.querySelectorAll('[id^="modal-"]').forEach(function (modal) {
        modal.addEventListener('click', function (e) {
            if (e.target === this) closeModal(this.id);
        });
    });

    /* --- Tutup modal dengan tombol ESC --- */
    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Escape') return;
        document.querySelectorAll('[id^="modal-"]').forEach(function (modal) {
            if (!modal.classList.contains('hidden')) {
                closeModal(modal.id);
            }
        });
    });

    /* --- Buka modal otomatis jika ada validation error --- */
    const hasErrors   = window.VEHICLE_HAS_ERRORS  === true;
    const oldMethod   = window.VEHICLE_OLD_METHOD   || '';

    if (hasErrors) {
        if (oldMethod === 'PUT') {
            openModal('modal-edit');
        } else {
            openModal('modal-tambah');
        }
    }
});
